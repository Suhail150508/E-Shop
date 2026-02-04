<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'department'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%'.$request->search.'%')
                    ->orWhere('subject', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $tickets = $query->paginate(10);

        return view('admin.support_tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['messages.user', 'department', 'user']);

        return view('admin.support_tickets.show', compact('supportTicket'));
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'status' => 'required|in:open,pending,replied,closed',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $supportTicket->update($request->only('status', 'priority'));

        return back()->with('success', __('Ticket updated successfully.'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ticket-' . $ticket->id . '-' . Auth::id() . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999) . '.' . $extension;
            $destinationPath = public_path('uploads/custom-images');
            $file->move($destinationPath, $filename);
            $attachmentPath = 'uploads/custom-images/' . $filename;
        }

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        $ticket->update(['status' => 'replied']);

        return back()->with('success', __('Reply sent successfully.'));
    }
}
