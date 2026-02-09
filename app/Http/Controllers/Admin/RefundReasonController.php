<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundReason;
use Illuminate\Http\Request;

class RefundReasonController extends Controller
{
    public function index()
    {
        $reasons = RefundReason::latest()->paginate(10);
        return view('admin.refund_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        RefundReason::create($request->all());

        return back()->with('success', __('common.refund_reason_created_success'));
    }

    public function update(Request $request, RefundReason $refundReason)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $refundReason->update($request->all());

        return back()->with('success', __('common.refund_reason_updated_success'));
    }

    public function destroy(RefundReason $refundReason)
    {
        $refundReason->delete();
        return back()->with('success', __('common.refund_reason_deleted_success'));
    }
}
