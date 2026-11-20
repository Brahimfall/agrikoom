<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'image_path',
        'author_name',
        'user_id',
        'status',
        'is_featured',
        'reading_time',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'reading_time' => 'integer',
        'views_count'  => 'integer',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest('published_at');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('assets/home/market-hero.png');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'assets/')) {
            return asset($this->image_path);
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function getCategoryBadgeClassesAttribute(): string
    {
        return match (mb_strtolower($this->category)) {
            'conseils', 'conseils & pratiques', 'pratiques' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'machinisme', 'matériel', 'matériel agricole'   => 'bg-amber-50 text-amber-800 border-amber-200',
            'élevage', 'élevage & bétail'                   => 'bg-stone-100 text-stone-800 border-stone-200',
            'marchés', 'marchés & économie', 'économie'    => 'bg-[#7D5A50]/10 text-[#7D5A50] border-[#7D5A50]/20',
            'irrigation', 'eau'                             => 'bg-sky-50 text-sky-800 border-sky-200',
            default                                         => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}

