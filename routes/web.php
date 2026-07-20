<?php

use App\Http\Controllers\UtilsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');
Route::get("testPayment",[ UtilsController::class,'testPayment'])->name('testPayment');
