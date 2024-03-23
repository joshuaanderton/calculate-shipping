<?php

use Illuminate\Support\Facades\Route;
use Ja\Shipping\Actions\Shipments\Store;

Route::middleware(['web', 'auth:sanctum', config('jetstream.auth_session')])->group(function () {
    Route::post('shipping/estimate', Store::class)->name('shipping.estimate');
});
