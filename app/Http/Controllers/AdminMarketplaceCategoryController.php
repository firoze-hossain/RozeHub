<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMarketplaceCategoryController extends Controller
{
    public function index()
    {
        $projects = SoftwareProject::with(['marketplaceCategories' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('admin.marketplace.categories', compact('projects'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'software_project_id' => 'required|exists:software_projects,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $d['is_active'] = $r->boolean('is_active', true);
        $d['sort_order'] = $d['sort_order'] ?? 0;

        MarketplaceCategory::create(array_merge($d, ['slug' => Str::slug($d['name'])]));

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $r, MarketplaceCategory $category)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $d['is_active'] = $r->boolean('is_active');
        $d['sort_order'] = $d['sort_order'] ?? 0;

        $category->update(array_merge($d, ['slug' => Str::slug($d['name'])]));

        return back()->with('success', "Category '{$category->name}' updated.");
    }

    public function destroy(MarketplaceCategory $category)
    {
        $name = $category->name;
        $category->delete();

        return back()->with('success', "Category '{$name}' removed.");
    }
}
