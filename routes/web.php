<?php

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard',[
        // passing all not logged in users to the dashboard view.
        'users' => User::whereNot('id', auth()->id())->get(),
    ]);
})->middleware(['auth'])->name('dashboard');

// creating the chat with friends route.
Route::get('/chat/{friend}', function(User $friend){
    return view('chat',[
        'friend' => $friend,
    ]);
})->middleware('auth')->name('chat');

// Creating the messages route and getting all the sender and receiver messages to use it later in the ChatComponent as data api.
Route::get('/messages/{friend}', function(User $friend) {
    $messages = ChatMessage::withRelations()
        // The messages sent by the authenticated user to the friend.
        ->where(function($query) use ($friend) {
            $query
                ->where('sender_id', auth()->id())
                ->where('receiver_id', $friend->id);
        })
        // The messages sent by the friend to the authenticated user.
        ->orWhere(function($query) use ($friend) {
            $query
                ->where('sender_id', $friend->id)
                ->where('receiver_id', auth()->id());
        })
        // Sorting the messages by the message id.
        ->orderBy('id', 'asc')
        // Getting the query results.
        ->get();

    // Returning the results as a JSON response
    return response()->json($messages);
})->middleware('auth');

// Creating the send(store) messages route.
Route::post('/messages/{friend}', function(Request $request, User $friend) {
    $validated = $request->validate([
        'message' => 'required',
    ]);

    // Storing the message in the database.
    $message = ChatMessage::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $friend->id,
        'text' => $validated['message'],
    ]);

    // broadcasting(sending in realtime) the message to the receiver after sending it by the sender via the MessageSent Event.
    broadcast(new MessageSent($message));

    return $message;
})->middleware('auth');

require __DIR__.'/auth.php';
