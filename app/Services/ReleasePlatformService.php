<?php
namespace App\Services;

use App\Models\{GithubRelease, Release, ReleaseArtifact, ReleaseChannel, ReleaseUpdateNotification, SoftwareProject, User};
use App\Jobs\ProcessReleaseArtifact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ReleasePlatformService
{
    public function __construct(private GithubService $github, private ReleaseStorageService $storage) {}

    public function syncGithubReleases(SoftwareProject $project, bool $importArtifacts = true): int
    {
        $repo = $project->githubRepository()->first() ?: $this->github->sync($project);
        $parts = $this->github->parseRepositoryUrl($project->github_url);
        if (!$parts) throw new RuntimeException('Project has no valid GitHub repository.');
        $response = $this->github->client()->get("https://api.github.com/repos/{$parts['owner']}/{$parts['name']}/releases", ['per_page'=>100]);
        if ($response->failed()) throw new RuntimeException('GitHub releases request failed ('.$response->status().').');
        $count=0;
        foreach (($response->json() ?: []) as $data) {
            if (!isset($data['id'],$data['tag_name'])) continue;
            $gh = GithubRelease::updateOrCreate(['github_repository_id'=>$repo->id,'github_id'=>$data['id']], [
                'tag_name'=>$data['tag_name'],'name'=>$data['name']??null,'body'=>$data['body']??null,'html_url'=>$data['html_url']??null,
                'prerelease'=>(bool)($data['prerelease']??false),'draft'=>(bool)($data['draft']??false),
                'published_at_github'=>$data['published_at']??null,'assets'=>$data['assets']??[],'raw'=>$data,
            ]);
            $count++;
            if ($importArtifacts && !$gh->draft) $this->importGithubAssets($project,$gh,$parts,$data);
        }
        return $count;
    }

    public function importGithubAssets(SoftwareProject $project, GithubRelease $githubRelease, array $parts, array $releaseData): int
    {
        $count=0;
        foreach (($releaseData['assets']??[]) as $asset) {
            $name=(string)($asset['name']??'');
            if ($name==='' || !isset($asset['id'])) continue;
            $type=$this->assetType($name);
            if (!$type || $type==='signature') continue;
            $platform=$this->platformFromName($name);
            $arch=$this->architectureFromName($name);
            $channel=$this->channelFromGithubRelease($releaseData);
            $version=$this->normalizeVersion($githubRelease->tag_name);
            $release=Release::firstOrCreate([
                'software_project_id'=>$project->id,'version'=>$version,'platform'=>$platform,'architecture'=>$arch,'channel'=>$channel,
            ],[
                'source'=>'GITHUB','github_release_id'=>$githubRelease->id,'notes'=>$githubRelease->body,'is_published'=>false,
                'published_at'=>null,'processing_status'=>'QUEUED','signature_status'=>'NOT_CONFIGURED','health_status'=>'UNKNOWN',
            ]);
            $release->update(['source'=>'GITHUB','github_release_id'=>$githubRelease->id]);
            $artifact=$release->artifacts()->where('purpose','INSTALLER')->first();
            if (!$artifact) {
                $artifact=$release->artifacts()->create(['purpose'=>'INSTALLER','package_type'=>$type,'file_path'=>'github/'.$project->slug.'/'.$version.'/'.$name,'file_name'=>$name,'file_size'=>(int)($asset['size']??0),'is_primary'=>true]);
            }
            if (!$artifact->sha256 && !empty($asset['digest']) && Str::startsWith($asset['digest'],'sha256:')) $artifact->update(['sha256'=>Str::after($asset['digest'],'sha256:')]);
            if (!$release->file_path) $release->update(['file_path'=>$artifact->file_path,'file_name'=>$artifact->file_name,'file_size'=>$artifact->file_size,'sha256'=>$artifact->sha256]);
            $release->update(['processing_status'=>'QUEUED']);
            ProcessReleaseArtifact::dispatch($release->id);
            $count++;
        }
        return $count;
    }

    public function processRelease(Release $release): void
    {
        $release->update(['processing_status'=>'PROCESSING','processing_error'=>null]);
        try {
            foreach ($release->artifacts as $artifact) {
                $this->processArtifact($release,$artifact);
            }
            $this->verifyRelease($release);
            $this->healthCheck($release);
            $release->update(['processing_status'=>'READY']);
        } catch (\Throwable $e) {
            $release->update(['processing_status'=>'FAILED','processing_error'=>$e->getMessage(),'health_status'=>'FAILED','health_checked_at'=>now()]);
            throw $e;
        }
    }

    public function processArtifact(Release $release, ReleaseArtifact $artifact): void
    {
        if ($artifact->file_path && Storage::disk('releases')->exists($artifact->file_path)) {
            $artifact->update(['file_size'=>Storage::disk('releases')->size($artifact->file_path),'sha256'=>hash_file('sha256',Storage::disk('releases')->path($artifact->file_path))]);
            return;
        }
        if ($release->source !== 'GITHUB' || !$release->githubRelease) throw new RuntimeException('Artifact is not locally available and has no GitHub source.');
        $raw=$release->githubRelease->raw ?: [];
        $asset=collect($raw['assets']??[])->first(fn($a)=>(string)($a['name']??'')===$artifact->file_name);
        if (!$asset || empty($asset['url'])) throw new RuntimeException('GitHub asset could not be found.');
        $repo=$release->githubRelease->repository;
        $url=$asset['browser_download_url']??null;
        if (!$url) throw new RuntimeException('GitHub asset download URL is missing.');
        $res=$this->github->client()->withHeaders(['Accept'=>'application/octet-stream'])->get($url);
        if ($res->failed()) throw new RuntimeException('GitHub asset download failed ('.$res->status().').');
        $disk=Storage::disk('releases'); $disk->makeDirectory(dirname($artifact->file_path)); $disk->put($artifact->file_path,$res->body());
        $path=$disk->path($artifact->file_path); $sha=hash_file('sha256',$path);
        $artifact->update(['file_size'=>filesize($path),'sha256'=>$sha]);
        if ($artifact->purpose==='INSTALLER') $release->update(['file_path'=>$artifact->file_path,'file_name'=>$artifact->file_name,'file_size'=>$artifact->file_size,'sha256'=>$sha]);
    }

    public function verifyRelease(Release $release): bool
    {
        $artifact=$release->artifacts()->where('purpose','INSTALLER')->first();
        if (!$artifact || !$artifact->file_path || !Storage::disk('releases')->exists($artifact->file_path)) throw new RuntimeException('Installer artifact is missing.');
        $path=Storage::disk('releases')->path($artifact->file_path); $actual=hash_file('sha256',$path);
        if ($artifact->sha256 && !hash_equals(strtolower($artifact->sha256),strtolower($actual))) throw new RuntimeException('SHA-256 verification failed.');
        $status='HASH_VERIFIED'; $algo='SHA-256';
        $publicKey=config('release_platform.signing_public_key');
        if ($publicKey && !$release->signature_path && $release->source === 'GITHUB' && $release->githubRelease) {
            $raw=$release->githubRelease->raw ?: [];
            $signatureAsset=collect($raw['assets']??[])->first(function($a) use ($artifact){
                $n=(string)($a['name']??'');
                return $n !== '' && (str_ends_with(strtolower($n), '.sig') || str_ends_with(strtolower($n), '.asc')) && str_starts_with($n, $artifact->file_name);
            });
            if ($signatureAsset && !empty($signatureAsset['browser_download_url'])) {
                $sigResponse=$this->github->client()->withHeaders(['Accept'=>'application/octet-stream'])->get($signatureAsset['browser_download_url']);
                if ($sigResponse->successful()) {
                    $sigPath='github/'.$release->project->slug.'/'.$release->version.'/signatures/'.basename($signatureAsset['name']);
                    Storage::disk('releases')->makeDirectory(dirname($sigPath));
                    Storage::disk('releases')->put($sigPath,$sigResponse->body());
                    $release->update(['signature_path'=>$sigPath]);
                    $sigPath=Storage::disk('releases')->path($sigPath);
                }
            }
        }
        $sigPath=$release->signature_path ? Storage::disk('releases')->path($release->signature_path) : null;
        if ($publicKey && $sigPath && is_file($sigPath)) {
            $signature=file_get_contents($sigPath); $key=openssl_pkey_get_public($publicKey);
            if (!$key || openssl_verify(hash('sha256',file_get_contents($path),true),$signature,$key,OPENSSL_ALGO_SHA256)!==1) throw new RuntimeException('Digital signature verification failed.');
            $status='SIGNED_AND_VERIFIED'; $algo='RSA-SHA256';
        }
        $release->update(['sha256'=>$actual,'signature_status'=>$status,'signature_algorithm'=>$algo,'verified_at'=>now()]);
        return true;
    }

    public function healthCheck(Release $release): array
    {
        $checks=['artifact'=>false,'hash'=>false,'signature'=>false,'metadata'=>false];
        $artifact=$release->artifacts()->where('purpose','INSTALLER')->first();
        $checks['artifact']=(bool)($artifact?->file_path && Storage::disk('releases')->exists($artifact->file_path));
        $checks['hash']=$checks['artifact'] && (bool)$release->sha256;
        $checks['signature']=in_array($release->signature_status,['HASH_VERIFIED','SIGNED_AND_VERIFIED'],true);
        if (config('release_platform.require_signature') && $release->signature_status !== 'SIGNED_AND_VERIFIED') $checks['signature']=false;
        $checks['metadata']=$release->version!==null && $release->platform!==null && $release->channel!==null;
        $healthy=$checks['artifact']&&$checks['hash']&&$checks['metadata']&&(!config('release_platform.require_signature') || $checks['signature']);
        $status=$healthy?'HEALTHY':'DEGRADED';
        $release->update(['health_status'=>$status,'health_checked_at'=>now()]);
        return ['status'=>$status,'checks'=>$checks];
    }

    public function rollback(Release $release): Release
    {
        return DB::transaction(function() use($release){
            $previous=Release::query()->where('software_project_id',$release->software_project_id)->where('platform',$release->platform)->where('architecture',$release->architecture)->where('channel',$release->channel)->where('id','<>',$release->id)->where('is_published',true)->orderByDesc('published_at')->orderByDesc('id')->first();
            $release->update(['is_published'=>false,'rolled_back_at'=>now(),'health_status'=>'ROLLED_BACK']);
            if($previous){$previous->update(['is_published'=>true,'rolled_back_at'=>null]); $release->update(['rollback_of_release_id'=>$previous->id]);}
            Cache::forget("rozehub:update-releases:{$release->software_project_id}:{$release->platform}:{$release->architecture}:{$release->channel}");
            if($previous) $this->notify($previous,'release_rollback_recovery','A previous release has been restored after a rollback.');
            return $previous ?: $release;
        });
    }

    public function notify(Release $release, string $type='release_available', ?string $message=null): int
    {
        $message ??= "{$release->project?->name} {$release->version} is now available on the {$release->channel} channel.";
        $users=User::query()->pluck('id'); $count=0;
        foreach($users as $id){ ReleaseUpdateNotification::firstOrCreate(['user_id'=>$id,'release_id'=>$release->id,'type'=>$type],['message'=>$message]); $count++; }
        return $count;
    }

    public function ensureChannels(SoftwareProject $project): void
    {
        foreach ([['stable','Stable',true],['beta','Beta',false],['nightly','Nightly',false]] as [$key,$name,$default]) ReleaseChannel::firstOrCreate(['software_project_id'=>$project->id,'key'=>$key],['name'=>$name,'is_default'=>$default,'is_enabled'=>true]);
    }

    private function normalizeVersion(string $tag):string{return ltrim(trim($tag),'vV');}
    private function channelFromGithubRelease(array $data):string{return !empty($data['prerelease'])?'Beta':'Stable';}
    private function assetType(string $name):?string { $e=strtolower(pathinfo($name,PATHINFO_EXTENSION)); return match($e){'msi','exe','dmg','pkg','deb','rpm','appimage','zip','tar','gz','tgz'=>$e,default=>null}; }
    private function platformFromName(string $name):string { $n=strtolower($name); return str_contains($n,'win')||str_ends_with($n,'.msi')||str_ends_with($n,'.exe')?'Windows':(str_contains($n,'mac')||str_contains($n,'darwin')||str_ends_with($n,'.dmg')||str_ends_with($n,'.pkg')?'macOS':'Linux'); }
    private function architectureFromName(string $name):string { $n=strtolower($name); return (str_contains($n,'arm64')||str_contains($n,'aarch64'))?'ARM64':'x64'; }
}
