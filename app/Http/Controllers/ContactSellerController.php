<?php

namespace App\Http\Controllers;

use App\Models\ContactEvent;
use App\Models\ContactRequest;
use App\Models\Listing;
use App\Notifications\ListingContacted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSellerController extends Controller
{
    public function __invoke(Request $request, Listing $listing): RedirectResponse
    {
        abort_unless(
            $listing->status === 'active' && ($listing->expires_at === null || $listing->expires_at->isFuture()),
            404
        );

        abort_if($listing->user_id === $request->user()->id, 422, 'Vous ne pouvez pas contacter votre propre annonce.');

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
            'buyer_phone' => ['nullable', 'string', 'max:40'],
        ]);

        $buyerPhone = trim($data['buyer_phone'] ?? '') ?: $request->user()->phone;

        $contact = ContactRequest::create([
            'listing_id' => $listing->id,
            'buyer_id' => $request->user()->id,
            'seller_id' => $listing->user_id,
            'buyer_name' => $request->user()->name,
            'buyer_phone' => $buyerPhone,
            'message' => $data['message'] ?? null,
            'contacted_at' => now(),
        ]);

        ContactEvent::create([
            'listing_id' => $listing->id,
            'seller_id' => $listing->user_id,
            'user_id' => $request->user()->id,
            'channel' => 'form',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $listing->user->notify(new ListingContacted($contact));

        return back()->with('status', 'Votre demande de contact a ete envoyee au vendeur.');
    }
}
