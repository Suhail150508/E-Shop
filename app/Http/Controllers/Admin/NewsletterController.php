<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::query();

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->input('search'));
            $query->where('email', 'like', '%'.$search.'%');
        }

        $subscribers = $query->latest()->paginate(10);

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $subscriber->delete();

        return back()->with('success', __('Subscriber deleted successfully.'));
    }
}
