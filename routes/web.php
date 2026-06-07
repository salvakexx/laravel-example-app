<?php

use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/', function () {
    return view('welcome');
});
