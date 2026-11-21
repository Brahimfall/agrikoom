<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()->published();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $featuredArticle = null;
        if (! $request->filled('search') && ! $request->filled('category') && ! $request->filled('page')) {
            $featuredArticle = (clone $query)->featured()->first();
        }

        $articles = $query
            ->when($featuredArticle, fn ($q) => $q->where('id', '!=', $featuredArticle->id))
            ->paginate(9)
            ->withQueryString();

        $categories = Article::query()
            ->where('status', 'published')
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        return view('blog.index', [
            'featuredArticle' => $featuredArticle,
            'articles'        => $articles,
            'categories'      => $categories,
            'totalArticles'   => Article::query()->published()->count(),
        ]);
    }

    public function show(Article $article): View
    {
        $isPublic = $article->status === 'published'
            && ($article->published_at === null || $article->published_at <= now());

        abort_if(! $isPublic && ! Auth::user()?->is_admin, 404);

        $article->increment('views_count');

        $relatedArticles = Article::query()->published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->limit(3)
            ->get();

        if ($relatedArticles->isEmpty()) {
            $relatedArticles = Article::query()->published()
                ->where('id', '!=', $article->id)
                ->limit(3)
                ->get();
        }

        return view('blog.show', [
            'article'         => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}

