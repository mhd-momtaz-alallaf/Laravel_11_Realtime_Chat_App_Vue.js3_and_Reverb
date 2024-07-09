<?php

use Illuminate\Support\Facades\Broadcast;

// defining the channel broadcast route to use it inside the MessageSent Event.
Broadcast::channel('chat.{id}', function ($user, $id) {
    // if the current logged in user ($user) id is the same of receiver($id) id, then he is going to be authenticated and he will receive the private message via the PrivateChannel that inside the MessageSent Event.
    return (int) $user->id === (int) $id;
});
