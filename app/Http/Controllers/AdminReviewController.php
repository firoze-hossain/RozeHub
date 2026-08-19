<?php
namespace App\Http\Controllers;
use App\Models\Review;
class AdminReviewController extends Controller {
 public function index(){return view('admin.reviews.index',['reviews'=>Review::with('project')->latest()->paginate(20)]);}
 public function toggle(Review $review){$review->update(['is_approved'=>!$review->is_approved]);return back()->with('success',$review->is_approved?'Review approved.':'Review hidden.');}
 public function destroy(Review $review){$review->delete();return back()->with('success','Review deleted.');}
}
