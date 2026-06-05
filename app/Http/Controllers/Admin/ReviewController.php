<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status; // '', '1' (approved), '0' (pending)

        $reviews = Review::with('user')
            ->when($status !== null && $status !== '', fn($q) => $q->where('is_approved', $status))
            ->latest()
            ->get();

        $pendingCount = Review::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'status', 'pendingCount'));
    }

    public function toggle(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', 'Review '.($review->is_approved ? 'approved' : 'hidden').' successfully!');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
