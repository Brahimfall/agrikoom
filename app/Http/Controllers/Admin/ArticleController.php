<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $query = Article::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $articles = $query->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Article::query()->select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.articles.index', [
            'articles'   => $articles,
            'categories' => $categories,
            'stats'      => [
                'total'     => Article::count(),
                'published' => Article::where('status', 'published')->count(),
                'draft'     => Article::where('status', 'draft')->count(),
                'views'     => Article::sum('views_count'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.articles.form', [
            'article' => new Article([
                'status'       => 'published',
                'category'     => 'Conseils & Pratiques',
                'author_name'  => $request->user()?->name ?? 'Rédaction AgriKoom',
                'reading_time' => 5,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['user_id'] = $request->user()->id;

        if (empty($data['published_at']) && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $data['image_path'] = $path;
        }

        Article::query()->create($data);

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article de blog créé et publié.');
    }

    public function edit(Request $request, Article $article): View
    {
        $this->authorizeAdmin($request);

        return view('admin.articles.form', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['is_featured'] = $request->boolean('is_featured');

        if ($article->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }

        if ($request->hasFile('image')) {
            if ($article->image_path && ! str_starts_with($article->image_path, 'assets/')) {
                Storage::disk('public')->delete($article->image_path);
            }
            $data['image_path'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article mis à jour avec succès.');
    }

    public function toggle(Request $request, Article $article): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' && ! $article->published_at ? now() : $article->published_at,
        ]);

        $msg = $newStatus === 'published' ? 'Article publié en ligne.' : 'Article basculé en brouillon.';

        return back()->with('status', $msg);
    }

    public function destroy(Request $request, Article $article): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($article->image_path && ! str_starts_with($article->image_path, 'assets/')) {
            Storage::disk('public')->delete($article->image_path);
        }

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article supprimé.');
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
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string', 'max:100'],
            'excerpt'      => ['required', 'string', 'max:1000'],
            'content'      => ['required', 'string'],
            'author_name'  => ['required', 'string', 'max:255'],
            'status'       => ['required', 'in:published,draft,archived'],
            'reading_time' => ['nullable', 'integer', 'min:1', 'max:120'],
            'published_at' => ['nullable', 'date'],
            'image'        => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'article';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Article::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}

