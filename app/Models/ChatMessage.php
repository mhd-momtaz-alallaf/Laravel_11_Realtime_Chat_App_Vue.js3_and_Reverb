<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_id',
        'sender_id',
        'text',
    ];

    // Creating the receiver relation of the ChatMessage model, Each chat message is sent to one specific user (receiver).
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id'); // one chat message => one user (receiver)
    }

    // Creating the sender relation of the ChatMessage model,  Each chat message is sent by one specific user (sender).
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id'); // one chat message => one user (sender)
    }

    // Creating the withRelations method to Eager loading the relationships in the queries, When querying ChatMessage, We might want to eagerly load the receiver and sender to avoid the N+1 query problem..
    public static function withRelations()
    {
        return self::with(['receiver', 'sender']);
    }
}
