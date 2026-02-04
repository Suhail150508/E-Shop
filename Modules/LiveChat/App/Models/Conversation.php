<?php

namespace Modules\LiveChat\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $table = 'live_chat_conversations';

    protected $fillable = [
        'session_id',
        'user_id',
        'customer_name',
        'customer_email',
        'status',
        'is_read_by_admin',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
