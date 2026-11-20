<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'company',
        'location',
        'contract_type',
        'deadline',
        'summary',
        'details',
        'profile',
        'apply_email',
        'apply_url',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
