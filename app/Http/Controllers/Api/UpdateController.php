<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class UpdateController extends Controller
{
    public function check(Request $request, SoftwareProject $project)
    {
        $data = $request->validate([
            'version' => ['required', 'string', 'max:80'],
            'platform' => ['required', 'string', 'max:30'],
            'architecture' => ['required', 'string', 'max:20'],
            'channel' => ['nullable', 'string', 'max:20'],
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
                ->whereNotNull('file_path')
                ->whereNotNull('file_name')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get()
        );

        $latest = $releases
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
            'downloadUrl' => route('api.updates.download', $release),
            'publishedAt' => optional($release->published_at)->toIso8601String(),
        ])->values();

        return response()->json([
            'project' => $project->slug,
            'count' => $releases->count(),
            'releases' => $releases,
        ]);
    }

    public function download(Release $release)
    {
        abort_unless($release->is_published && $release->file_path, 404);
        abort_unless(Storage::disk('releases')->exists($release->file_path), 404);

        $release->increment('downloads_count');

        return Storage::disk('releases')->download($release->file_path, $release->file_name, [
            'Content-Type' => 'application/octet-stream',
            'X-RozeHub-Version' => $release->version,
            'X-RozeHub-SHA256' => (string) $release->sha256,
            'Cache-Control' => 'public, max-age=3600',
        ]);
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
