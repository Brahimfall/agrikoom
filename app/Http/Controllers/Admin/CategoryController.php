<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $query = Category::query()
            ->withCount([
                'listings',
                'listings as active_listings_count' => fn ($q) => $q->active(),
            ]);

        if ($request->filled('search')) {
            $search = trim($request->string('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->string('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->orderBy('name')->get();

        $allCategories = Category::all();
        $totalListings = Listing::count();

        $stats = [
            'total'          => $allCategories->count(),
            'active'         => $allCategories->where('is_active', true)->count(),
            'inactive'       => $allCategories->where('is_active', false)->count(),
            'total_listings' => $totalListings,
        ];

        return view('admin.categories.index', [
            'categories' => $categories,
            'stats'      => $stats,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.categories.form', [
            'category'     => new Category(['is_active' => true]),
            'presets'      => $this->availablePresets(),
            'otherCategories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:categories,name'],
            'slug'         => ['nullable', 'string', 'max:120', 'unique:categories,slug'],
            'description'  => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
            'preset_image' => ['nullable', 'string', 'max:255'],
            'image_file'   => ['nullable', 'image', 'max:3072'],
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique'   => 'Une catégorie porte déjà ce nom.',
            'slug.unique'   => 'Ce slug est déjà utilisé par une autre catégorie.',
            'image_file.image' => 'Le fichier doit être une image valide.',
            'image_file.max'   => 'L\'image ne doit pas dépasser 3 Mo.',
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Image resolution
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('categories', 'public');
        } elseif (! empty($validated['preset_image'])) {
            $imagePath = $validated['preset_image'];
        }

        Category::create([
            'name'        => trim($validated['name']),
            'slug'        => $slug,
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
            'image'       => $imagePath,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('status', "La catégorie '{$validated['name']}' a été créée avec succès.");
    }

    public function edit(Request $request, Category $category): View
    {
        $this->authorizeAdmin($request);

        return view('admin.categories.form', [
            'category'        => $category,
            'presets'         => $this->availablePresets(),
            'otherCategories' => Category::where('id', '!=', $category->id)->orderBy('name')->get(),
            'listingsCount'   => $category->listings()->count(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'slug'         => ['nullable', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description'  => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
            'preset_image' => ['nullable', 'string', 'max:255'],
            'image_file'   => ['nullable', 'image', 'max:3072'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique'   => 'Une catégorie porte déjà ce nom.',
            'slug.unique'   => 'Ce slug est déjà utilisé par une autre catégorie.',
            'image_file.image' => 'Le fichier doit être une image valide.',
            'image_file.max'   => 'L\'image ne doit pas dépasser 3 Mo.',
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $imagePath = $category->image;

        if ($request->boolean('remove_image')) {
            if ($imagePath && ! str_starts_with($imagePath, 'assets/')) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        } elseif ($request->hasFile('image_file')) {
            if ($imagePath && ! str_starts_with($imagePath, 'assets/')) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_file')->store('categories', 'public');
        } elseif (! empty($validated['preset_image'])) {
            $imagePath = $validated['preset_image'];
        }

        $category->update([
            'name'        => trim($validated['name']),
            'slug'        => $slug,
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
            'image'       => $imagePath,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('status', "La catégorie '{$category->name}' a été mise à jour avec succès.");
    }

    public function toggle(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $category->update(['is_active' => ! $category->is_active]);

        $state = $category->is_active ? 'activée (visible sur le site)' : 'masquée (désactivée)';

        return back()->with('status', "La catégorie '{$category->name}' est désormais {$state}.");
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $listingsCount = $category->listings()->count();

        if ($listingsCount > 0) {
            $reassignToId = $request->input('reassign_to_id');

            if ($reassignToId) {
                $targetCategory = Category::where('id', $reassignToId)
                    ->where('id', '!=', $category->id)
                    ->first();

                if (! $targetCategory) {
                    return back()->withErrors(['error' => 'Catégorie de destination invalide pour le transfert des annonces.']);
                }

                // Transfer all listings to target category
                Listing::where('category_id', $category->id)->update([
                    'category_id' => $targetCategory->id,
                ]);

                $name = $category->name;
                $category->delete();

                return redirect()->route('admin.categories.index')->with(
                    'status',
                    "La catégorie '{$name}' a été supprimée. {$listingsCount} annonce(s) ont été automatiquement transférées vers '{$targetCategory->name}'."
                );
            }

            return back()->withErrors([
                'error' => "Impossible de supprimer la catégorie '{$category->name}' car elle contient {$listingsCount} annonce(s). Veuillez choisir une autre catégorie vers laquelle transférer ces annonces, ou désactivez-la simplement.",
            ]);
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', "La catégorie '{$name}' a été supprimée avec succès.");
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /**
     * @return array<string, string>
     */
    private function availablePresets(): array
    {
        return [
            'assets/home/prod_legumes.jpg'     => 'Légumes & Maraîchage (Étalage coloré)',
            'assets/home/prod_cereales.jpg'    => 'Céréales (Riz, mil, maïs, blé)',
            'assets/home/agri_elevage.jpg'     => 'Élevage & Bétail (Troupeau, caprins)',
            'assets/home/agri_tracteur.jpg'    => 'Matériel Agricole (Tracteur moderne)',
            'assets/home/prod_intrants.jpg'    => 'Intrants & Semences certifiées',
            'assets/home/prod_fruits.jpg'      => 'Fruits (Mangues, agrumes)',
            'assets/home/prod_horticoles.jpg'  => 'Produits horticoles & Pépinières',
            'assets/home/prod_manioc.jpg'      => 'Tubercules (Manioc, patates)',
        ];
    }
}
