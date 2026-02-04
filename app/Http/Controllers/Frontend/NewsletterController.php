<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first('email'),
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        Newsletter::create([
            'email' => $request->email,
            'is_active' => true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('common.subscription_success') ?? 'You have successfully subscribed to our newsletter!',
            ]);
        }

        return back()->with('success', __('common.subscription_success') ?? 'You have successfully subscribed to our newsletter!');
    }
}
