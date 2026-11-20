<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'seller_id',
        'user_id',
        'channel',
        'ip_address',
        'user_agent',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
