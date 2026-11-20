<?php

namespace App\Http\Controllers;

use App\Models\ContactEvent;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackContactClickController extends Controller
{
    public function __invoke(Request $request, Listing $listing): JsonResponse
    {
        $channel = in_array($request->input('channel'), ['whatsapp', 'phone', 'form'], true)
            ? $request->input('channel')
            : 'whatsapp';

        ContactEvent::create([
            'listing_id' => $listing->id,
            'seller_id' => $listing->user_id,
            'user_id' => $request->user()?->id,
            'channel' => $channel,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['status' => 'tracked', 'channel' => $channel]);
    }
}

