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
Route::post('/test-event', function () {
    \App\Events\OrderCompleted::dispatch(time());
    return ['status' => 'ok'];
});
Route::get('/', function () {
    //Create user and login
    if(!\Illuminate\Support\Facades\Auth::check()) {
        if(!$user=\App\Models\User::query()->first()) {
            $user = new \App\Models\User();
            $user->name = 'Test User';
            $user->email = 'test@test.com';
            $user->password = \Illuminate\Support\Facades\Hash::make('password');
            $user->save();
        }
        \Illuminate\Support\Facades\Auth::login($user);
    }

    return view('welcome');
});
