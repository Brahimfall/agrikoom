<?php

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/listings', function () {
    return Listing::query()
        ->active()
        ->with(['category:id,name,slug', 'user:id,name,phone,location,phone_verified_at'])
        ->latest()
        ->paginate(20);
});
