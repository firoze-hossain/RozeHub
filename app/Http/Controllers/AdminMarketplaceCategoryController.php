<?php
namespace App\Http\Controllers;
use App\Models\MarketplaceCategory;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class AdminMarketplaceCategoryController extends Controller {
 public function index(){return view('admin/marketplace/categories', ['projects'=>SoftwareProject::with('marketplaceCategories')->orderBy('name')->get()]);}
 public function store(Request $r){$d=$r->validate(['software_project_id'=>'required|exists:software_projects,id','name'=>'required|string|max:100','description'=>'nullable|string|max:500','icon'=>'nullable|string|max:80','sort_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']); MarketplaceCategory::create(array_merge($d,['slug'=>Str::slug($d['name'])])); return back()->with('success','Category created.');}
 public function update(Request $r, MarketplaceCategory $category){$d=$r->validate(['name'=>'required|string|max:100','description'=>'nullable|string|max:500','icon'=>'nullable|string|max:80','sort_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']); $category->update(array_merge($d,['slug'=>Str::slug($d['name'])])); return back()->with('success','Category updated.');}
 public function destroy(MarketplaceCategory $category){$category->delete();return back()->with('success','Category removed.');}
}
