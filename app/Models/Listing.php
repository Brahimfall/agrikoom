<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'product',
        'description',
        'quantity',
        'unit',
        'price',
        'is_negotiable',
        'location',
        'available_from',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'is_negotiable' => 'boolean',
            'available_from' => 'date',
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('sort_order');
    }

    public function contactRequests()
    {
        return $this->hasMany(ContactRequest::class);
    }

    public function contactEvents()
    {
        return $this->hasMany(ContactEvent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function getIsOfferAttribute(): bool
    {
        return $this->type === 'offer';
    }

    public function getIsAvailableImmediatelyAttribute(): bool
    {
        if (empty($this->available_from)) {
            return true;
        }

        try {
            $date = $this->available_from instanceof \DateTimeInterface
                ? $this->available_from
                : \Illuminate\Support\Carbon::parse($this->available_from);

            return $date->lte(today());
        } catch (\Throwable $e) {
            return true;
        }
    }
}
