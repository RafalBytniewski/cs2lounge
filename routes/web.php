<?php

use App\Http\Controllers\TradeOfferController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TradeOfferController::class, 'index']);
Route::post('/offers', [TradeOfferController::class, 'store'])->name('offers.store');
