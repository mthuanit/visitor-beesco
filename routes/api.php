<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckController;

Route::middleware(['api.key', 'throttle:api'])->group(function () {
    Route::get('/visitors', function () {
        return response()->json(App\Models\VisitorSession::orderBy('checkin_time', 'desc')->take(50)->get());
    });
});
