<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Support\Facades\Storage;

class ReleaseDownloadController extends Controller
{
    public function __invoke(Release $release)
    {
        abort_unless($release->is_published, 404);

        $release->increment('downloads_count');

        abort_unless($release->file_path && Storage::disk('public')->exists($release->file_path), 404, 'The release package has not been uploaded yet.');

        return Storage::disk('public')->download($release->file_path, $release->file_name);
    }
}
