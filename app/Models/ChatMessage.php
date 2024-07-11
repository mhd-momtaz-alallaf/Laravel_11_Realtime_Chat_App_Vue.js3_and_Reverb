<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_id',
        'sender_id',
        'text',
    ];

    // Encrypting the message text before saving it to the database
    public function setTextAttribute($value)
    {
        $this->attributes['text'] = Crypt::encryptString($value);
    }

   // Decrypt the message text when retrieving it from the database
   public function getTextAttribute($value)
   {
       try {
           return Crypt::decryptString($value);
       } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
           // Handle the error or log it
           return 'Decryption failed';
       }
   }

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
