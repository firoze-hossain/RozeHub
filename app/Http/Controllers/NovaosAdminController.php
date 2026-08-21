<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\SoftwareProject;

class NovaosAdminController extends Controller
{
    public function index()
    {
        $project = SoftwareProject::where('slug', 'novaos')->firstOrFail();
        $releases = $project->releases()->latest('created_at')->get();

        return view('admin/novaos/index', [
            'project' => $project,
            'releases' => $releases,
            'publishedCount' => $releases->where('is_published', true)->count(),
            'downloadCount' => $releases->sum('downloads_count'),
            'stableCount' => $releases->where('channel', 'Stable')->where('is_published', true)->count(),
            'latestStable' => $releases->where('channel', 'Stable')->where('is_published', true)->sortByDesc(fn ($r) => $r->published_at?->timestamp ?? 0)->first(),
        ]);
    }
}
