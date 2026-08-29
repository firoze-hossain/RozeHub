<?php
namespace App\Http\Controllers;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceReview;
use Illuminate\Http\Request;
class MarketplaceReviewController extends Controller {
 public function store(Request $r, MarketplaceItem $item){abort_unless($item->is_published,404); $d=$r->validate(['rating'=>'required|integer|min:1|max:5','title'=>'nullable|string|max:180','body'=>'nullable|string|max:3000']); MarketplaceReview::updateOrCreate(['marketplace_item_id'=>$item->id,'user_id'=>auth()->id()],$d+['is_approved'=>true]); return back()->with('success','Your review has been saved.');}
}
