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
    dd(app('puller')->pull('test', 'MXgLLBCyKUNTWjUwHKoF15dONK8NVeHkMMTqIXRPT3eBvoVb4VkTC7lufzli3LUg'));
});
