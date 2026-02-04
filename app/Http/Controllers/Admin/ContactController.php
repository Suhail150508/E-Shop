<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $messages = $query->latest()->paginate(10);

        return view('admin.contact.index', compact('messages'));
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return back()->with('success', 'Message deleted successfully.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update([
            'reply' => $request->reply,
            'status' => 'replied',
        ]);

        // Send email notification to user about the reply
        try {
            Mail::to($message->email)->send(new ContactReplyMail($message));
        } catch (\Exception $e) {
            return back()->with('warning', 'Reply saved but email sending failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Reply sent successfully.');
    }
}
