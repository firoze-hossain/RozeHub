<?php

namespace App\Http\Controllers;

use App\Models\SoftwareProject;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $project = SoftwareProject::where('slug', 'thundercall')->first();
        return view('support.index', compact('project'));
    }

    public function project(Request $request, SoftwareProject $project)
    {
        return view('support.index', compact('project'));
    }
}
