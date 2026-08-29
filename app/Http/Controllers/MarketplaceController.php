<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;

class MarketplaceController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $selectedProject = $request->filled('project') ? SoftwareProject::with('ecosystemProfile')->where('slug', $request->string('project'))->first() : null;

        $analytics->track('marketplace_view', $selectedProject?->id, null, ['type'=>$request->string('type')->toString(),'category'=>$request->string('category')->toString()], $request);

        $items = MarketplaceItem::with('project.ecosystemProfile')
            ->where('is_published', true)
            ->when($request->filled('project'), fn ($q) => $q->whereHas('project', fn ($p) => $p->where('slug', $request->string('project'))))
            ->when($request->filled('type'), fn ($q) => $q->where('item_type', $request->string('type')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('capability'), fn ($q) => $q->whereJsonContains('capabilities', $request->string('capability')))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($inner) use ($request) {
                $term = '%'.$request->string('q').'%';
                $inner->where('name', 'like', $term)->orWhere('summary', 'like', $term)->orWhere('item_id', 'like', $term)->orWhere('vendor', 'like', $term);
            }))
            ->latest('is_official')->latest('downloads_count')->paginate(18)->withQueryString();

        $projects = SoftwareProject::with(['ecosystemProfile','marketplaceCategories'])
            ->whereHas('ecosystemProfile', fn ($q) => $q->where('marketplace_enabled', true))
            ->orderBy('name')->get();

        $categories = $selectedProject?->marketplaceCategories?->where('is_active',true)->sortBy('sort_order')->values() ?? collect();
        $types = MarketplaceItem::where('is_published', true)->when($selectedProject, fn($q)=>$q->where('software_project_id',$selectedProject->id))->distinct()->orderBy('item_type')->pluck('item_type');
        $capabilities = MarketplaceItem::where('is_published', true)->when($selectedProject, fn($q)=>$q->where('software_project_id',$selectedProject->id))->get(['capabilities'])->pluck('capabilities')->flatten()->filter()->unique()->sort()->values();
        $ecosystem = $selectedProject?->ecosystemProfile;

        return view('marketplace.index', compact('items', 'projects', 'types', 'capabilities', 'ecosystem', 'categories'));
    }

    public function item(MarketplaceItem $item, AnalyticsService $analytics)
    {
        abort_unless($item->is_published, 404);
        $analytics->track('marketplace_item_view', $item->software_project_id, $item, ['item_slug'=>$item->slug], request());
        $item->load(['project.ecosystemProfile','owner.publisherProfile','marketplaceReviews' => fn($q)=>$q->where('is_approved',true)->with('user')->latest(),'releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id')]);
        return view('marketplace.item', ['item'=>$item,'rating'=>app(\App\Services\MarketplaceService::class)->ratingSummary($item)]);
    }
}
