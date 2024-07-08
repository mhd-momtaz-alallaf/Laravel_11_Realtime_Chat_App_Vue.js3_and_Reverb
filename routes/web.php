<?php

use App\Models\User;
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

require __DIR__.'/auth.php';
