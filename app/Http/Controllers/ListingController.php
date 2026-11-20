<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Listing;
use App\Models\Category;
use App\Models\JobOffer;
use App\Models\MarketPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(): View
    {
        $activeListings = Listing::query()->active();
        $categoryTiles = Category::query()
            ->where('is_active', true)
            ->withCount(['listings as active_listings_count' => fn ($query) => $query->active()])
            ->orderByRaw("CASE name WHEN 'Cereales' THEN 1 WHEN 'Legumes' THEN 2 WHEN 'Elevage' THEN 3 WHEN 'Materiel agricole' THEN 4 WHEN 'Intrants' THEN 5 WHEN 'Fruits' THEN 6 WHEN 'Tubercules' THEN 7 ELSE 99 END")
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category) => $category->active_listings_count > 0)
            ->values();

        return view('listings.index', [
            'stats' => [
                'active' => (clone $activeListings)->count(),
                'offers' => (clone $activeListings)->where('type', 'offer')->count(),
                'requests' => (clone $activeListings)->where('type', 'request')->count(),
                'categories' => $categoryTiles->count(),
            ],
            'categoryTiles' => $categoryTiles,
            'jobOffers' => JobOffer::query()
                ->active()
                ->orderByDesc('is_featured')
                ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
                ->orderBy('deadline')
                ->limit(4)
                ->get(),
            'jobOfferCount' => JobOffer::query()->active()->count(),
            'marketPrices'  => MarketPrice::query()->active()->get(),
            'marketPricesLastUpdated' => MarketPrice::query()->max('updated_at'),
            'latestArticles' => Article::query()->published()->limit(3)->get(),
        ]);
    }

    public function create(): View
    {
        return view('listings.form', [
            'listing' => null,
        ]);
    }

    public function show(Listing $listing): View
    {
        $listing->load(['category', 'photos', 'user']);

        $isPubliclyVisible = $listing->status === 'active'
            && ($listing->expires_at === null || $listing->expires_at->isFuture());

        abort_if(! $isPubliclyVisible && Auth::id() !== $listing->user_id, 404);

        return view('listings.show', [
            'listing' => $listing,
        ]);
    }

    public function edit(Listing $listing): View
    {
        abort_unless(Auth::id() === $listing->user_id, 403);

        return view('listings.form', [
            'listing' => $listing->load('photos'),
        ]);
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        abort_unless(Auth::id() === $listing->user_id, 403);

        foreach ($listing->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $listing->delete();

        return redirect()->route('listings.mine')->with('status', 'Annonce supprimee.');
    }

    public function mine(): View
    {
        $listings = Auth::user()
            ->listings()
            ->with(['category', 'photos'])
            ->latest()
            ->paginate(12);

        return view('listings.mine', [
            'listings' => $listings,
        ]);
    }

    public function markSold(Listing $listing): RedirectResponse
    {
        abort_unless(Auth::id() === $listing->user_id, 403);

        $listing->update(['status' => 'sold']);

        return back()->with('status', 'Annonce marquee comme vendue.');
    }

    public function renew(Listing $listing): RedirectResponse
    {
        abort_unless(Auth::id() === $listing->user_id, 403);

        $listing->update([
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        return back()->with('status', 'Annonce renouvelee pour 30 jours.');
    }
}
