<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'claimed_date',
        'day_number',
        'xp_earned',
        'gacha_currency_earned',
    ];

    protected $casts = [
        'claimed_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
