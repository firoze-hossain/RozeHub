<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Support\Facades\Storage;
use App\Services\AnalyticsService;

class ReleaseDownloadController extends Controller
{
    public function __invoke(Release $release, AnalyticsService $analytics)
    {
        abort_unless($release->is_published, 404);
        abort_unless($release->file_path && Storage::disk('releases')->exists($release->file_path), 404, 'The release package has not been uploaded yet.');

        $release->increment('downloads_count');
        $analytics->track('download', $release->software_project_id, $release, ['version'=>$release->version,'platform'=>$release->platform,'architecture'=>$release->architecture,'channel'=>$release->channel,'source'=>'web'], request());

        return Storage::disk('releases')->download($release->file_path, $release->file_name, [
            'Content-Type' => 'application/octet-stream',
            'X-RozeHub-Version' => $release->version,
            'X-RozeHub-SHA256' => (string) $release->sha256,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
