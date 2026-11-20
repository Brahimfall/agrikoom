<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketPriceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $query = MarketPrice::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('price_range', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->string('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $marketPrices = $query->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $categories = MarketPrice::query()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('admin.mercuriale.index', [
            'marketPrices' => $marketPrices,
            'categories'   => $categories,
            'stats'        => [
                'total'    => MarketPrice::count(),
                'active'   => MarketPrice::where('is_active', true)->count(),
                'inactive' => MarketPrice::where('is_active', false)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.mercuriale.form', [
            'marketPrice' => new MarketPrice([
                'is_active'   => true,
                'trend'       => 'stable',
                'trend_label' => 'Stable',
                'source'      => 'Marchés régionaux & coopératives',
                'sort_order'  => (MarketPrice::max('sort_order') ?? 0) + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['change_percent'] = $request->filled('change_percent') ? (float) $request->input('change_percent') : 0.0;

        MarketPrice::query()->create($data);

        return redirect()
            ->route('admin.mercuriale.index')
            ->with('status', 'Cours de marché ajouté avec succès.');
    }

    public function edit(Request $request, MarketPrice $marketPrice): View
    {
        $this->authorizeAdmin($request);

        return view('admin.mercuriale.form', [
            'marketPrice' => $marketPrice,
        ]);
    }

    public function update(Request $request, MarketPrice $marketPrice): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['change_percent'] = $request->filled('change_percent') ? (float) $request->input('change_percent') : 0.0;

        $marketPrice->update($data);

        return redirect()
            ->route('admin.mercuriale.index')
            ->with('status', 'Cours de marché mis à jour avec succès.');
    }

    public function toggle(Request $request, MarketPrice $marketPrice): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $marketPrice->update(['is_active' => ! $marketPrice->is_active]);

        $message = $marketPrice->is_active ? 'Cours activé et visible sur le site.' : 'Cours désactivé.';

        return back()->with('status', $message);
    }

    public function destroy(Request $request, MarketPrice $marketPrice): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $marketPrice->delete();

        return redirect()
            ->route('admin.mercuriale.index')
            ->with('status', 'Cours supprimé.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'product_name'   => ['required', 'string', 'max:255'],
            'ticker_code'    => ['nullable', 'string', 'max:20'],
            'category'       => ['required', 'string', 'max:100'],
            'price_range'    => ['required', 'string', 'max:255'],
            'previous_price' => ['nullable', 'string', 'max:255'],
            'change_percent' => ['nullable', 'numeric', 'between:-100,1000'],
            'unit'           => ['nullable', 'string', 'max:100'],
            'location'       => ['nullable', 'string', 'max:255'],
            'trend'          => ['required', 'in:stable,up,down,active,available,variable'],
            'trend_label'    => ['required', 'string', 'max:100'],
            'source'         => ['nullable', 'string', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'sort_order'     => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}

