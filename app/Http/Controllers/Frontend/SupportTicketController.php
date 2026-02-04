<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->supportTickets()->with('department')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%'.$request->search.'%')
                    ->orWhere('subject', 'like', '%'.$request->search.'%');
            });
        }

        $tickets = $query->paginate(10);
        $departments = SupportDepartment::where('is_active', true)->get();

        return view('frontend.account.support_ticket.index', compact('tickets', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'department_id' => 'required|exists:support_departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => '#'.strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'department_id' => $request->department_id,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        return back()->with('success', __('Support ticket created successfully.'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $ticket->load(['messages.user', 'department']);

        return view('frontend.account.support_ticket.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        $ticket->update(['status' => 'open']);

        return back()->with('success', __('Reply sent successfully.'));
    }
}
