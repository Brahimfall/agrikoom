<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ContactRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'buyer_id',
        'seller_id',
        'buyer_name',
        'buyer_phone',
        'message',
        'contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
        ];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
