<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\LiveChat\App\Models\Conversation;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    // Allow if user is admin or the conversation owner
    return $user->isAdmin() || $user->id === $conversation->user_id;
});

Broadcast::channel('admin-notifications', function ($user) {
    return $user->isAdmin();
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
