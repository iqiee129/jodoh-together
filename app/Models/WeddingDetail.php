<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeddingDetail extends Model
{
    use HasFactory;

    protected $guarded = []; // Allows us to save data easily
    protected $fillable = [
    'user_id',
    'partner_name',
    'wedding_date',
    'venue',
    'theme',
    'estimated_guests',
    'guest_count',
    'total_budget',
    'photo',
    'google_event_id',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}