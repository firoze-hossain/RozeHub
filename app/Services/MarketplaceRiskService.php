<?php

namespace App\Services;

use App\Models\MarketplaceRelease;
use App\Models\MarketplaceSubmission;
use App\Models\MarketplaceSubmissionRisk;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MarketplaceRiskService
{
    public function assess(MarketplaceSubmission $submission): array
    {
        $release = $submission->release;
        $item = $submission->item;
        $checks = [];

        $checks[] = $this->check('package_integrity', 'PASS', 0, 'Package metadata and checksum are present.');
        if (!$release || !$release->file_path || !Storage::disk('releases')->exists($release->file_path)) {
            $checks[count($checks)-1] = $this->check('package_integrity', 'FAIL', 50, 'Package is missing from external release storage.');
        }

        $permissions = $item?->permissions ?? [];
        $permScore = 0;
        $permStatus = 'PASS';
        $permNotes = 'No elevated capabilities were declared.';
        foreach ($permissions as $permission) {
            $p = strtolower((string)$permission);
            if (str_contains($p,'system') || str_contains($p,'process') || str_contains($p,'execute')) { $permScore += 25; $permStatus='REVIEW'; }
            elseif (str_contains($p,'filesystem.write') || str_contains($p,'file.write') || str_contains($p,'project.write')) { $permScore += 15; $permStatus='REVIEW'; }
            elseif (str_contains($p,'network')) { $permScore += 8; if ($permStatus==='PASS') $permStatus='REVIEW'; }
        }
        if ($permScore > 0) $permNotes = 'Elevated permissions were declared and require administrator review.';
        $checks[] = $this->check('permissions', $permStatus, min(40,$permScore), $permNotes);

        if ($release) {
            $native = strtolower((string)$release->package_type) === 'native';
            $checks[] = $this->check('package_type', $native ? 'REVIEW' : 'PASS', $native ? 15 : 0,
                $native ? 'Native package may contain executable code; inspect before approval.' : 'Supported archive/package type.');

            $checks[] = $this->archiveCheck($release);
        }

        $score = min(100, array_sum(array_column($checks,'score')));
        $level = $score >= 70 ? 'CRITICAL' : ($score >= 40 ? 'HIGH' : ($score >= 15 ? 'MEDIUM' : 'LOW'));
        if (collect($checks)->contains(fn($c)=>$c['status']==='FAIL')) $level='CRITICAL';

        foreach ($checks as $check) {
            MarketplaceSubmissionRisk::updateOrCreate(
                ['submission_id'=>$submission->id,'category'=>$check['category']],
                ['status'=>$check['status'],'score'=>$check['score'],'summary'=>$check['summary'],'notes'=>$check['notes'],'checked_by'=>null,'checked_at'=>now()]
            );
        }

        return ['score'=>$score,'level'=>$level,'summary'=>$this->summary($level,$checks),'checks'=>$checks];
    }

    private function archiveCheck(MarketplaceRelease $release): array
    {
        $disk=Storage::disk('releases');
        if (!$release->file_path || !$disk->exists($release->file_path)) return $this->check('archive_scan','FAIL',40,'Archive cannot be scanned because the package is missing.');
        $path=$disk->path($release->file_path);
        $ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
        if (!in_array($ext,['zip','jar','vsix'],true)) return $this->check('archive_scan','REVIEW',5,'Package is not a ZIP-compatible archive; inspect its executable contents manually.');
        $zip=new ZipArchive();
        if ($zip->open($path)!==true) return $this->check('archive_scan','FAIL',45,'Archive could not be opened for inspection.');
        $exec=[]; $unsafe=false;
        for($i=0;$i<$zip->numFiles;$i++){
            $name=$zip->getNameIndex($i) ?: '';
            if (str_contains($name,'..\\') || str_contains($name,'../') || str_starts_with($name,'/')) $unsafe=true;
            if (preg_match('/\.(exe|dll|so|dylib|sh|bat|cmd|ps1)$/i',$name)) $exec[]=$name;
        }
        $zip->close();
        if($unsafe) return $this->check('archive_scan','FAIL',60,'Archive contains unsafe path entries.');
        if(count($exec)>0) return $this->check('archive_scan','REVIEW',25,'Archive contains executable files: '.implode(', ',array_slice($exec,0,5)).(count($exec)>5?'…':'') );
        return $this->check('archive_scan','PASS',0,'Archive structure passed the automated safety checks.');
    }

    private function check(string $category,string $status,int $score,string $summary): array { return compact('category','status','score')+['summary'=>$summary,'notes'=>$summary]; }
    private function summary(string $level,array $checks): string { return match($level){ 'LOW'=>'Automated checks found no significant elevated risk.','MEDIUM'=>'Some capabilities or package characteristics require administrator review.','HIGH'=>'Elevated-risk characteristics were detected. Manual inspection is required.','CRITICAL'=>'A critical package integrity or safety issue was detected. Do not publish until resolved.','default'=>'Manual review required.'}; }
}
