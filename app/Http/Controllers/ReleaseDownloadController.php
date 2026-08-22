<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Services\ReleaseFileStorage;

class ReleaseDownloadController extends Controller
{
    public function __invoke(Release $release, ReleaseFileStorage $releaseFiles)
    {
        abort_unless($release->is_published, 404);

        $release->increment('downloads_count');

        $absolutePath = $releaseFiles->absolutePath($release->file_path);

        abort_unless($absolutePath, 404, 'The release package has not been uploaded yet.');

        return response()->download($absolutePath, $release->file_name ?: basename($absolutePath));
    }
}
