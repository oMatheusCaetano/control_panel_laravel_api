<?php

use App\Http\Controllers\CreditCardController;
use Illuminate\Support\Facades\Route;

Route::apiResource('credit-cards', CreditCardController::class);
