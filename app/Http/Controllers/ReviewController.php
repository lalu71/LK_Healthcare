<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\Notify;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $review = Review::where('user_id', $request->user()->id)->first();

        return view('reviews.create', compact('review'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'remark' => 'required|string|max:1000',
        ]);

        // One review per user — editing resets it to pending for re-approval.
        Review::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['rating' => $data['rating'], 'remark' => $data['remark'], 'is_approved' => false]
        );

        Notify::admins('New site review', $request->user()->name.' rated '.$data['rating'].'★', route('admin.reviews.index'), 'chat');

        return redirect()->route('reviews.create')->with('success', 'Thanks for your review! It will appear on the site once approved.');
    }
}
