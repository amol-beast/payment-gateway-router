<?php

use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Middleware\HandleApiClientEncryptedRequest;
use App\Http\Middleware\HandleApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::group(['middleware' => [HandleApiClientEncryptedRequest::class]], function () {
    Route::get('/initPayment', [PaymentController::class, 'initiatePayment'])
        ->name('initPayment');
});

Route::group(['middleware' => [HandleApiRequest::class]], function () {
    Route::get('/transaction/{reference_id}', [PaymentController::class, 'getTransactionDetails'])
        ->name('transaction.details');

    Route::get('/transactions', [PaymentController::class, 'getTransactions'])
        ->name('transactions.list');
});

Route::any('/handleResponse/{pgClass}', [PaymentController::class, 'handlePaymentResponse'])->name('handlePaymentResponse');
