<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\SoftwareProject;
use App\Models\ReleaseArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\AnalyticsService;

class UpdateController extends Controller
{
    public function check(Request $request, SoftwareProject $project)
    {
        $data = $request->validate([
            'version' => ['required', 'string', 'max:80'],
            'platform' => ['required', 'string', 'max:30'],
            'architecture' => ['required', 'string', 'max:20'],
            'channel' => ['nullable', 'string', 'max:20'],
            'client_id' => ['nullable', 'string', 'max:200'],
        ]);

        $platform = $this->normalizePlatform($data['platform']);
        $architecture = strtoupper($data['architecture']) === 'ARM64' ? 'ARM64' : 'x64';
        $channel = $this->normalizeChannel($data['channel'] ?? 'Stable');
        $current = ltrim(trim($data['version']), 'vV');

        $releases = Cache::remember(
            "rozehub:update-releases:{$project->id}:{$platform}:{$architecture}:{$channel}",
            now()->addSeconds(30),
            fn () => Release::query()
                ->where('software_project_id', $project->id)
                ->where('platform', $platform)
                ->where('architecture', $architecture)
                ->where('channel', $channel)
                ->where('is_published', true)
                ->whereIn('processing_status', ['READY','PROCESSING'])
                ->whereIn('health_status', ['HEALTHY','UNKNOWN'])
                ->whereNotNull('file_path')
                ->whereNotNull('file_name')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get()
        );

        $latest = $releases
            ->filter(fn (Release $release) => $this->eligibleForRollout($release, $data['client_id'] ?? null))
            ->filter(fn (Release $release) => $this->isNewer($release->version, $current))
            ->sort(fn (Release $a, Release $b) => $this->compareVersions($b->version, $a->version))
            ->first();

        $policyRelease = $releases
            ->sort(fn (Release $a, Release $b) => $this->compareVersions($b->version, $a->version))
            ->first();
        $minimumSupported = $policyRelease?->minimum_version;
        $forcedByMinimum = $minimumSupported !== null && $this->compareVersions($current, $minimumSupported) < 0;
        $available = $latest !== null;

        if (!$available && !$forcedByMinimum) {
            return response()->json([
                'project' => $project->slug,
                'currentVersion' => $current,
                'available' => false,
                'mandatory' => false,
                'channel' => $channel,
                'platform' => $platform,
                'architecture' => $architecture,
                'latestVersion' => $releases->first()?->version,
                'minimumSupportedVersion' => $minimumSupported,
                'checkedAt' => now()->toIso8601String(),
            ], 200);
        }

        $release = $latest ?: $releases->sort(fn (Release $a, Release $b) => $this->compareVersions($b->version, $a->version))->first();
        if (!$release) {
            return response()->json([
                'project' => $project->slug,
                'currentVersion' => $current,
                'available' => false,
                'mandatory' => false,
                'channel' => $channel,
                'platform' => $platform,
                'architecture' => $architecture,
                'latestVersion' => null,
                'minimumSupportedVersion' => $minimumSupported,
                'checkedAt' => now()->toIso8601String(),
            ]);
        }

        $installer = $release->artifacts()->where('purpose', 'INSTALLER')->first();
        $updater = $release->artifacts()->where('purpose', 'UPDATER')->first();
        $updateArtifact = $updater ?: $installer;

        return response()->json([
            'project' => $project->slug,
            'currentVersion' => $current,
            'available' => $available || $forcedByMinimum,
            'mandatory' => $forcedByMinimum || (bool) $release->is_mandatory,
            'channel' => $release->channel,
            'platform' => $release->platform,
            'architecture' => $release->architecture,
            'latestVersion' => $release->version,
            'minimumSupportedVersion' => $minimumSupported,
            'release' => [
                'id' => $release->id,
                'version' => $release->version,
                'majorVersion' => $release->major_version,
                'codename' => $release->codename,
                'buildNumber' => $release->build_number,
                'channel' => $release->channel,
                'notes' => $release->notes,
                'minimumVersion' => $release->minimum_version,
                'mandatory' => (bool) $release->is_mandatory,
                'fileName' => $release->file_name,
                'fileSize' => (int) $release->file_size,
                'sha256' => $release->sha256,
                'installerArtifact' => $this->artifactPayload($installer, $release),
                'updateArtifact' => $this->artifactPayload($updateArtifact, $release),
                'publishedAt' => optional($release->published_at)->toIso8601String(),
                'downloadUrl' => route('api.updates.download', $release),
            ],
            'checkedAt' => now()->toIso8601String(),
        ]);
    }

    public function releases(Request $request, SoftwareProject $project)
    {
        $data = $request->validate([
            'platform' => ['nullable', 'string', 'max:30'],
            'architecture' => ['nullable', 'string', 'max:20'],
            'channel' => ['nullable', 'string', 'max:20'],
        ]);

        $query = Release::query()
            ->where('software_project_id', $project->id)
            ->where('is_published', true)
            ->whereIn('processing_status', ['READY','PROCESSING'])
            ->whereIn('health_status', ['HEALTHY','UNKNOWN'])
            ->whereNotNull('file_path');

        if (!empty($data['platform'])) {
            $query->where('platform', $this->normalizePlatform($data['platform']));
        }
        if (!empty($data['architecture'])) {
            $query->where('architecture', strtoupper($data['architecture']) === 'ARM64' ? 'ARM64' : 'x64');
        }
        if (!empty($data['channel'])) {
            $query->where('channel', $this->normalizeChannel($data['channel']));
        }

        $releases = $query->orderByDesc('published_at')->orderByDesc('id')->get()->map(fn (Release $release) => [
            'id' => $release->id,
            'version' => $release->version,
            'platform' => $release->platform,
            'architecture' => $release->architecture,
            'channel' => $release->channel,
            'mandatory' => (bool) $release->is_mandatory,
            'minimumVersion' => $release->minimum_version,
            'fileName' => $release->file_name,
            'fileSize' => (int) $release->file_size,
            'sha256' => $release->sha256,
            'installerArtifact' => $this->artifactPayload($release->artifacts()->where('purpose', 'INSTALLER')->first(), $release),
            'updateArtifact' => $this->artifactPayload($release->artifacts()->where('purpose', 'UPDATER')->first(), $release),
            'downloadUrl' => route('api.updates.download', ['release' => $release, 'purpose' => 'installer']),
            'publishedAt' => optional($release->published_at)->toIso8601String(),
        ])->values();

        return response()->json([
            'project' => $project->slug,
            'count' => $releases->count(),
            'releases' => $releases,
        ]);
    }

    public function download(Request $request, Release $release, AnalyticsService $analytics)
    {
        abort_unless($release->is_published, 404);

        $purpose = strtoupper((string) $request->query('purpose', 'INSTALLER')) === 'UPDATER' ? 'UPDATER' : 'INSTALLER';
        $artifact = $release->artifacts()->where('purpose', $purpose)->first();
        if (!$artifact && $purpose === 'UPDATER') {
            $artifact = $release->artifacts()->where('purpose', 'INSTALLER')->first();
        }

        $path = $artifact?->file_path ?: $release->file_path;
        $name = $artifact?->file_name ?: $release->file_name;
        $size = $artifact?->file_size ?? $release->file_size;
        $sha256 = $artifact?->sha256 ?? $release->sha256;

        abort_unless($path && Storage::disk('releases')->exists($path), 404);

        if ($artifact) $artifact->increment('downloads_count');
        else $release->increment('downloads_count');
        $analytics->track('download', $release->software_project_id, $release, ['version'=>$release->version,'platform'=>$release->platform,'architecture'=>$release->architecture,'channel'=>$release->channel,'purpose'=>$purpose,'source'=>'api'], $request);

        return Storage::disk('releases')->download($path, $name, [
            'Content-Type' => 'application/octet-stream',
            'X-RozeHub-Version' => $release->version,
            'X-RozeHub-SHA256' => (string) $sha256,
            'X-RozeHub-Package-Purpose' => $purpose,
            'X-RozeHub-Package-Type' => strtolower((string) ($artifact?->package_type ?: pathinfo((string) $name, PATHINFO_EXTENSION))),
            'X-RozeHub-File-Size' => (string) $size,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function artifactPayload(?ReleaseArtifact $artifact, Release $release): ?array
    {
        if (!$artifact) return null;
        return [
            'id' => $artifact->id,
            'purpose' => $artifact->purpose,
            'packageType' => $artifact->package_type,
            'fileName' => $artifact->file_name,
            'fileSize' => (int) $artifact->file_size,
            'sha256' => $artifact->sha256,
            'downloadUrl' => route('api.updates.download', ['release' => $release, 'purpose' => strtolower($artifact->purpose)]),
        ];
    }

    private function eligibleForRollout(Release $release, ?string $clientId): bool
    {
        $percentage=max(1,min(100,(int)($release->rollout_percentage ?? 100)));
        if($percentage>=100) return true;
        if(!$clientId) return false;
        $bucket=(hexdec(substr(hash('sha256',$release->project?->slug.'|'.$clientId),0,8)) % 100)+1;
        return $bucket <= $percentage;
    }

    private function normalizePlatform(string $platform): string
    {
        return match (strtolower(trim($platform))) {
            'windows', 'win', 'win32', 'win64' => 'Windows',
            'mac', 'macos', 'osx' => 'macOS',
            'linux', 'ubuntu', 'debian' => 'Linux',
            'novaos', 'nova-os' => 'NOVAOS',
            default => trim($platform),
        };
    }

    private function normalizeChannel(string $channel): string
    {
        return match (strtolower(trim($channel))) {
            'stable', 'release', 'production' => 'Stable',
            'beta', 'preview' => 'Beta',
            'nightly', 'dev', 'development' => 'Nightly',
            default => 'Stable',
        };
    }

    private function isNewer(string $candidate, string $current): bool
    {
        return $this->compareVersions($candidate, $current) > 0;
    }

    private function compareVersions(?string $a, ?string $b): int
    {
        $a = ltrim(trim((string) $a), 'vV');
        $b = ltrim(trim((string) $b), 'vV');
        if ($a === $b) return 0;

        $aParts = preg_split('/[.+\-_]/', $a) ?: [];
        $bParts = preg_split('/[.+\-_]/', $b) ?: [];
        $max = max(count($aParts), count($bParts));

        for ($i = 0; $i < $max; $i++) {
            $ap = $aParts[$i] ?? '0';
            $bp = $bParts[$i] ?? '0';
            if (ctype_digit($ap) && ctype_digit($bp)) {
                $cmp = (int) $ap <=> (int) $bp;
            } elseif (ctype_digit($ap)) {
                $cmp = 1;
            } elseif (ctype_digit($bp)) {
                $cmp = -1;
            } else {
                $cmp = strnatcasecmp($ap, $bp);
            }
            if ($cmp !== 0) return $cmp;
        }

        return 0;
    }
}
