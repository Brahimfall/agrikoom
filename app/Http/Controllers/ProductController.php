<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::query()
            ->active()
            ->with(['category', 'photos', 'user'])
            ->where('type', 'offer'); // On affiche uniquement les offres (produits à vendre)

        // Filtre par catégorie (supporte l'ID, le slug ou le nom)
        if ($request->filled('category')) {
            $catVal = $request->category;
            if (is_numeric($catVal)) {
                $query->where('category_id', $catVal);
            } else {
                $query->whereHas('category', function ($q) use ($catVal) {
                    $q->where('slug', $catVal)
                      ->orWhere('name', 'like', "%{$catVal}%");
                });
            }
        }

        // Filtre par recherche mot-clé
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par région/localité
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filtre prix min
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }

        // Filtre prix max
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'alpha'      => $query->orderBy('title'),
            default      => $query->latest(), // 'recent'
        };

        $products = $query->paginate(16)->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['listings as active_count' => fn ($q) => $q->active()->where('type', 'offer')])
            ->orderBy('name')
            ->get();

        $totalProducts = Listing::query()->active()->where('type', 'offer')->count();

        $selectedCategory = null;
        if ($request->filled('category')) {
            $catVal = $request->category;
            $selectedCategory = is_numeric($catVal)
                ? $categories->firstWhere('id', (int) $catVal)
                : $categories->first(fn ($c) => $c->slug === $catVal || mb_strtolower($c->name) === mb_strtolower($catVal));
        }

        return view('products.index', compact('products', 'categories', 'totalProducts', 'selectedCategory'));
    }
}
