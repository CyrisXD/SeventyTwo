<?php

use Illuminate\Support\Facades\Route;

// Make sure Vue Router is handling routes and 404s
Route::get('/{pathMatch}', function () {
    return view('welcome');
})->where('pathMatch', '.*');
