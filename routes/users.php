<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\productsController;

Route::get('/test', function () {
    return view('users.test');
})->name('test');
