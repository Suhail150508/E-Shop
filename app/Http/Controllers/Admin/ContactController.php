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

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%');
            });
        }

        $messages = $query->latest()->paginate(10);

        return view('admin.contact.index', compact('messages'));
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return back()->with('success', __('Message deleted successfully.'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update([
            'reply' => $request->reply,
            'status' => 'replied',
        ]);

        try {
            Mail::to($message->email)->send(new ContactReplyMail($message));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Contact reply email failed', ['message_id' => $message->id, 'error' => $e->getMessage()]);
            return back()->with('warning', __('Reply saved but email could not be sent. Please try again later.'));
        }

        return back()->with('success', __('Reply sent successfully.'));
    }
}
