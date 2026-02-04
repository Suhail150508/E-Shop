<?php

namespace Modules\LiveChat\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\LiveChat\App\Events\MessageSent;
use Modules\LiveChat\App\Models\Conversation;
use Modules\LiveChat\App\Models\Message;

class LiveChatController extends Controller
{
    protected function isPayloadTooLarge(Request $request): bool
    {
        // Check against post_max_size in php.ini
        $contentLength = $request->server('CONTENT_LENGTH') ?: $request->getContentLength();
        $postMax = $this->convertPHPSizeToBytes(ini_get('post_max_size'));

        if ($contentLength && $postMax && $contentLength > $postMax) {
            return true;
        }

        return false;
    }

    protected function convertPHPSizeToBytes($size)
    {
        if (is_numeric($size)) {
            return (int) $size;
        }

        $unit = strtolower(substr($size, -1));
        $bytes = (int) substr($size, 0, -1);

        switch ($unit) {
            case 'g':
                $bytes *= 1024;
                // fallthrough
            case 'm':
                $bytes *= 1024;
                // fallthrough
            case 'k':
                $bytes *= 1024;
        }

        return $bytes;
    }

    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $conversation = Conversation::where('user_id', $userId)->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'customer_name' => Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'status' => 'active',
            ]);
        }

        $messages = $conversation->messages()->with('sender')->get();

        return view('livechat::frontend.index', compact('conversation', 'messages'));
    }

    public function startChat(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();

        $conversation = Conversation::where('user_id', $userId)->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'customer_name' => Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'status' => 'active',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages()->with('sender')->get(),
        ]);
    }

    public function sendMessage(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Early detect oversized payloads (beyond PHP limits) to return JSON error rather than silent failure
        if ($this->isPayloadTooLarge($request)) {
            return response()->json(['error' => 'Uploaded file exceeds the server allowed size (post_max_size).'], 413);
        }

        $request->validate([
            'conversation_id' => 'required|exists:live_chat_conversations,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,webp|max:2048',
        ]);

        if (! $request->message && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Message or attachment required'], 422);
        }

        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'chat-' . Auth::id() . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('livechat/attachments', $filename, 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'customer',
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        $conversation->update(['is_read_by_admin' => false]);

        MessageSent::dispatch($message);

        return response()->json([
            'status' => 'success',
            'message' => $message->load('sender'),
        ]);
    }

    public function getMessages(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['messages' => []]);
        }

        $userId = Auth::id();
        $conversation = Conversation::where('user_id', $userId)->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $conversation->messages()->with('sender')->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function uploadImage(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($this->isPayloadTooLarge($request)) {
            return response()->json(['error' => 'Uploaded file exceeds the server allowed size (post_max_size).'], 413);
        }

        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'chat-' . Auth::id() . '-' . date('Y-m-d-H-i-s') . '-' . rand(999, 9999) . '.' . $extension;
            $destinationPath = public_path('uploads/custom-images');
            $image->move($destinationPath, $imageName);

            return response()->json(['url' => asset('uploads/custom-images/' . $imageName)]);
        }

        return response()->json(['error' => 'Upload failed'], 500);
    }
}
