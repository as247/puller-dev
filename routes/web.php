<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('chat');
})->name('chat');
Route::post('/send-message', function () {
    $message=request()->get('message');
    $name=request()->get('name');
    $name=strip_tags($name);
    $message=strip_tags($message);
    $name=\Illuminate\Support\Str::limit($name,50);
    $message=\Illuminate\Support\Str::limit($message,500);
    \App\Events\MessageEvent::dispatch($message,$name);
    return ['status' => 'ok'];
})->name('send-message');

