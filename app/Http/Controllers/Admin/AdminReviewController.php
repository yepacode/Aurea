<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(): View
    {
        $pending = Review::with('product')->where('is_approved', false)->latest()->get();
        $approved = Review::with('product')->where('is_approved', true)->latest()->paginate(20);

        return view('admin.reviews.index', compact('pending', 'approved'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Reseña publicada.');
    }

    public function unapprove(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Reseña ocultada.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
