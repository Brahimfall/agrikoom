<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ContactEvent;
use App\Models\ContactRequest;
use App\Models\JobOffer;
use App\Models\Listing;
use App\Models\MarketPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $initialPeriod = $request->get('period', 'month');
        $statsData = $this->calculateStats($initialPeriod);

        // Données annexes statiques / catalogues
        $topCategories = Category::query()
            ->withCount(['listings as active_listings_count' => fn (Builder $q) => $q->active()])
            ->orderByDesc('active_listings_count')
            ->limit(8)
            ->get();

        $topLocations = Listing::query()
            ->active()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->selectRaw('location, count(*) as aggregate')
            ->groupBy('location')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get();

        $recentListings = Listing::query()
            ->with(['category', 'user'])
            ->withCount(['contactEvents', 'contactRequests'])
            ->latest()
            ->limit(8)
            ->get();

        $sellersToVerify = $this->sellerQuery()
            ->withCount(['listings', 'receivedContactEvents'])
            ->whereNull('phone_verified_at')
            ->orderByDesc('listings_count')
            ->limit(6)
            ->get();

        $topContactedListings = Listing::query()
            ->with(['category', 'user'])
            ->withCount(['contactEvents', 'contactRequests'])
            ->orderByDesc('contact_events_count')
            ->limit(6)
            ->get();

        $maxCategoryListings = max(1, (int) $topCategories->max('active_listings_count'));
        $maxLocationListings = max(1, (int) $topLocations->max('aggregate'));

        $metrics = [
            'listings_total' => Listing::query()->count(),
            'listings_active' => Listing::query()->active()->count(),
            'offers_active' => Listing::query()->active()->where('type', 'offer')->count(),
            'requests_active' => Listing::query()->active()->where('type', 'request')->count(),
            'jobs_total' => JobOffer::query()->count(),
            'jobs_active' => JobOffer::query()->active()->count(),
            'categories_total' => Category::query()->count(),
            'categories_active' => Category::query()->where('is_active', true)->count(),
            'market_prices_total' => MarketPrice::query()->count(),
            'market_prices_active' => MarketPrice::query()->where('is_active', true)->count(),
            'articles_total' => Article::query()->count(),
            'articles_published' => Article::query()->where('status', 'published')->count(),
            'articles_draft' => Article::query()->where('status', 'draft')->count(),
        ];

        return view('admin.dashboard', [
            'initialStats' => $statsData,
            'metrics' => $metrics,
            'topCategories' => $topCategories,
            'topLocations' => $topLocations,
            'recentListings' => $recentListings,
            'sellersToVerify' => $sellersToVerify,
            'topContactedListings' => $topContactedListings,
            'maxCategoryListings' => $maxCategoryListings,
            'maxLocationListings' => $maxLocationListings,
        ]);
    }

    /**
     * Endpoint API JSON pour rechargement dynamique sans rafraîchir la page
     */
    public function stats(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $period = $request->get('period', 'month');
        $data = $this->calculateStats($period);

        return response()->json($data);
    }

    private function calculateStats(string $period): array
    {
        $now = now();
        $startDate = match ($period) {
            'today' => $now->copy()->startOfDay(),
            'week'  => $now->copy()->subDays(6)->startOfDay(),
            'year'  => $now->copy()->startOfYear(),
            'all'   => Carbon::create(2020, 1, 1),
            default => $now->copy()->subDays(29)->startOfDay(), // 'month' (30 derniers jours)
        };

        // Requêtes utilisateurs
        $usersBase = User::query();
        $totalUsers = (clone $usersBase)->count();
        $periodUsers = (clone $usersBase)->where('created_at', '>=', $startDate)->count();
        $verifiedUsers = (clone $usersBase)->where('created_at', '>=', $startDate)->whereNotNull('phone_verified_at')->count();
        $sellersPeriod = (clone $this->sellerQuery())->where('created_at', '>=', $startDate)->count();
        $buyersPeriod = (clone $usersBase)->where('created_at', '>=', $startDate)->where('actor_type', 'buyer')->count();

        // Requêtes contacts / clics
        $eventsBase = ContactEvent::query()->where('created_at', '>=', $startDate);
        $totalContacts = (clone $eventsBase)->count();
        $whatsappCount = (clone $eventsBase)->where('channel', 'whatsapp')->count();
        $phoneCount = (clone $eventsBase)->where('channel', 'phone')->count();
        $formCount = (clone $eventsBase)->where('channel', 'form')->count()
            + ContactRequest::query()->where('created_at', '>=', $startDate)->count();

        $uniquePeople = (clone $eventsBase)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address');

        // Pourcentages des canaux
        $whatsappPercent = $totalContacts > 0 ? round(($whatsappCount / $totalContacts) * 100) : 0;
        $phonePercent = $totalContacts > 0 ? round(($phoneCount / $totalContacts) * 100) : 0;
        $formPercent = $totalContacts > 0 ? round(($formCount / $totalContacts) * 100) : 0;

        // Timeline pour graphiques selon la période
        $timeline = $this->buildTimeline($period, $startDate, $now);

        // Flux d'activité récent en direct (Live Stream)
        $recentActivities = $this->buildRecentActivities();

        // Taux de conversion global
        $activeListingsCount = max(1, Listing::query()->active()->count());
        $conversionRate = round(($totalContacts / $activeListingsCount) * 10, 1);

        return [
            'period' => $period,
            'summary' => [
                'users_period' => $periodUsers,
                'users_total' => $totalUsers,
                'users_verified' => $verifiedUsers,
                'sellers' => $sellersPeriod,
                'buyers' => $buyersPeriod,
                'contacts_total' => $totalContacts,
                'unique_people' => $uniquePeople,
                'conversion_rate' => $conversionRate,
            ],
            'channels' => [
                'whatsapp' => [
                    'count' => $whatsappCount,
                    'percent' => $whatsappPercent,
                ],
                'phone' => [
                    'count' => $phoneCount,
                    'percent' => $phonePercent,
                ],
                'form' => [
                    'count' => $formCount,
                    'percent' => $formPercent,
                ],
            ],
            'timeline' => $timeline,
            'recent_activities' => $recentActivities,
            'updated_at' => $now->format('H:i:s'),
        ];
    }

    private function buildTimeline(string $period, Carbon $startDate, Carbon $now): array
    {
        $timeline = [];

        if ($period === 'today') {
            // Heure par heure (00h à 23h)
            for ($h = 0; $h <= 23; $h++) {
                $startH = $now->copy()->startOfDay()->setHour($h);
                $endH = $startH->copy()->endOfHour();

                $contacts = ContactEvent::query()
                    ->whereBetween('created_at', [$startH, $endH])
                    ->count();

                $wa = ContactEvent::query()
                    ->where('channel', 'whatsapp')
                    ->whereBetween('created_at', [$startH, $endH])
                    ->count();

                $users = User::query()
                    ->whereBetween('created_at', [$startH, $endH])
                    ->count();

                $timeline[] = [
                    'label' => sprintf('%02dh', $h),
                    'full_date' => $startH->format('H:i'),
                    'contacts' => $contacts,
                    'whatsapp' => $wa,
                    'users' => $users,
                ];
            }
        } elseif ($period === 'week') {
            // 7 jours (J-6 à aujourd'hui)
            for ($d = 6; $d >= 0; $d--) {
                $day = $now->copy()->subDays($d);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $contacts = ContactEvent::query()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $wa = ContactEvent::query()
                    ->where('channel', 'whatsapp')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $users = User::query()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $timeline[] = [
                    'label' => $day->translatedFormat('D d'),
                    'full_date' => $day->format('d/m/Y'),
                    'contacts' => $contacts,
                    'whatsapp' => $wa,
                    'users' => $users,
                ];
            }
        } elseif ($period === 'year' || $period === 'all') {
            // Mois par mois (12 derniers mois)
            for ($m = 11; $m >= 0; $m--) {
                $monthDate = $now->copy()->subMonths($m);
                $monthStart = $monthDate->copy()->startOfMonth();
                $monthEnd = $monthDate->copy()->endOfMonth();

                $contacts = ContactEvent::query()
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $wa = ContactEvent::query()
                    ->where('channel', 'whatsapp')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $users = User::query()
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $timeline[] = [
                    'label' => $monthDate->translatedFormat('M'),
                    'full_date' => $monthDate->translatedFormat('F Y'),
                    'contacts' => $contacts,
                    'whatsapp' => $wa,
                    'users' => $users,
                ];
            }
        } else {
            // Mois (30 derniers jours groupés par tranches de 2 ou 3 jours ou par jour)
            for ($d = 29; $d >= 0; $d--) {
                $day = $now->copy()->subDays($d);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $contacts = ContactEvent::query()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $wa = ContactEvent::query()
                    ->where('channel', 'whatsapp')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $users = User::query()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $timeline[] = [
                    'label' => $day->format('d/m'),
                    'full_date' => $day->format('d/m/Y'),
                    'contacts' => $contacts,
                    'whatsapp' => $wa,
                    'users' => $users,
                ];
            }
        }

        return $timeline;
    }

    private function buildRecentActivities(): array
    {
        $events = ContactEvent::query()
            ->with(['listing', 'seller'])
            ->latest()
            ->limit(7)
            ->get()
            ->map(fn (ContactEvent $e) => [
                'type' => 'contact',
                'channel' => $e->channel,
                'title' => match ($e->channel) {
                    'whatsapp' => 'Clic WhatsApp vers vendeur',
                    'phone' => 'Appel téléphonique direct',
                    default => 'Formulaire de message envoyé',
                },
                'detail' => $e->listing ? $e->listing->title : 'Annonce agricole',
                'meta' => ($e->seller ? $e->seller->name : 'Vendeur').($e->listing?->location ? ' · '.$e->listing->location : ''),
                'time' => $e->created_at->diffForHumans(),
                'timestamp' => $e->created_at->timestamp,
            ]);

        $users = User::query()
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (User $u) => [
                'type' => 'user',
                'channel' => 'signup',
                'title' => 'Nouvel utilisateur inscrit',
                'detail' => $u->name,
                'meta' => ($u->actor_type ? ucfirst($u->actor_type) : 'Membre').($u->location ? ' · '.$u->location : ''),
                'time' => $u->created_at->diffForHumans(),
                'timestamp' => $u->created_at->timestamp,
            ]);

        return $events->concat($users)
            ->sortByDesc('timestamp')
            ->values()
            ->take(8)
            ->all();
    }

    private function sellerQuery(): Builder
    {
        return User::query()
            ->where(function (Builder $query): void {
                $query->whereIn('actor_type', ['producer', 'seller', 'both'])
                    ->orWhereHas('listings');
            });
    }
}
