<?php

namespace Modules\LiveChat\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\LiveChat\App\Events\MessageSent;
use Modules\LiveChat\App\Models\Conversation;
use Modules\LiveChat\App\Models\Message;

class AdminLiveChatController extends Controller
{
    // Display a list of conversations
    public function index()
    {
        $conversations = Conversation::with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livechat::admin.index', compact('conversations'));
    }

    // Show a specific conversation with messages
    public function show(Conversation $conversation)
    {
        $conversation->update(['is_read_by_admin' => true]);
        $messages = $conversation->messages()->with('sender')->orderBy('id')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    // Reply to a conversation
    public function reply(\Modules\LiveChat\App\Http\Requests\ReplyMessageRequest $request, Conversation $conversation)
    {
        $validated = $request->validated();

        $messageText = $request->filled('message') ? $validated['message'] : null;

        if (! $messageText && ! $request->hasFile('attachment')) {
            return response()->json([
                'error' => __('livechat::livechat.message_or_attachment_required'),
            ], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(8) . '.' . $extension;
            $attachmentPath = $file->storeAs('livechat/attachments', $filename, 'public');
            if ($attachmentPath === false) {
                Log::warning('LiveChat: attachment store failed', ['conversation_id' => $conversation->id]);

                return response()->json([
                    'error' => __('livechat::livechat.attachment_store_failed'),
                ], 500);
            }
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $messageText,
            'attachment' => $attachmentPath,
            'is_read' => false,
        ]);

        try {
            MessageSent::dispatch($message);
        } catch (\Throwable $e) {
            Log::warning('LiveChat: broadcast failed (message still saved)', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        $message->load('sender');

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    // Poll for new conversations
    public function pollConversations()
    {
        $conversations = Conversation::where('is_read_by_admin', false)->count();

        return response()->json(['unread_count' => $conversations]);
    }
}
