<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Product\App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'max:2048'], // 2MB max per image
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $extension = $image->getClientOriginalExtension();
                $imageName = 'review-' . Auth::id() . '-' . $product->id . '-' . date('Y-m-d-h-i-s') . '-' . $index . '-' . rand(999, 9999) . '.' . $extension;
                $destinationPath = public_path('uploads/custom-images');
                $image->move($destinationPath, $imageName);
                $imagePaths[] = 'uploads/custom-images/' . $imageName;
            }
        }

        $product->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'images' => ! empty($imagePaths) ? $imagePaths : null,
            'is_approved' => false, // Pending approval
        ]);

        return back()->with('success', __('Thank you for your review! It has been submitted for approval.'));
    }
}
