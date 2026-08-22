<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Support\Facades\Storage;

class ReleaseDownloadController extends Controller
{
    public function __invoke(Release $release)
    {
        abort_unless($release->is_published, 404);
        abort_unless($release->file_path && Storage::disk('releases')->exists($release->file_path), 404, 'The release package has not been uploaded yet.');

        $release->increment('downloads_count');

        return Storage::disk('releases')->download($release->file_path, $release->file_name, [
            'Content-Type' => 'application/octet-stream',
            'X-RozeHub-Version' => $release->version,
            'X-RozeHub-SHA256' => (string) $release->sha256,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
