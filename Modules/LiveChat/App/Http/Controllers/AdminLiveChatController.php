<?php

namespace Modules\LiveChat\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\LiveChat\App\Events\MessageSent;
use Modules\LiveChat\App\Models\Conversation;
use Modules\LiveChat\App\Models\Message;

class AdminLiveChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livechat::admin.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->update(['is_read_by_admin' => true]);
        $messages = $conversation->messages()->with('sender')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        if (! $request->message && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Message or attachment required'], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(8) . '.' . $extension;
            $attachmentPath = $file->storeAs('livechat/attachments', $filename, 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'is_read' => false, // Unread by customer
        ]);

        MessageSent::dispatch($message);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function pollConversations()
    {
        $conversations = Conversation::where('is_read_by_admin', false)->count();

        return response()->json(['unread_count' => $conversations]);
    }
}
