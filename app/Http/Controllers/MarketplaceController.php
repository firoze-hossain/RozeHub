<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $items = MarketplaceItem::with('project')
            ->where('is_published', true)
            ->when($request->filled('project'), fn ($q) => $q->whereHas('project', fn ($p) => $p->where('slug', $request->string('project'))))
            ->when($request->filled('type'), fn ($q) => $q->where('item_type', $request->string('type')))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($inner) use ($request) {
                $term = '%'.$request->string('q').'%';
                $inner->where('name', 'like', $term)
                    ->orWhere('summary', 'like', $term)
                    ->orWhere('item_id', 'like', $term);
            }))
            ->latest('is_official')->latest('downloads_count')
            ->paginate(18)->withQueryString();

        return view('marketplace.index', [
            'items' => $items,
            'projects' => SoftwareProject::whereHas('releases')->orderBy('name')->get(),
        ]);
    }

    public function item(MarketplaceItem $item)
    {
        abort_unless($item->is_published, 404);

        $item->load([
            'project',
            'releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id'),
        ]);

        return view('marketplace.item', compact('item'));
    }
}
