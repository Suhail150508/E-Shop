<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = ProductReview::with(['user', 'product'])
            ->latest()
            ->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $review->update([
            'is_approved' => $request->boolean('is_approved'),
        ]);

        return back()->with('success', __('common.review_status_updated'));
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', __('common.review_deleted'));
    }
}
