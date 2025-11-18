<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',        // nome da recorrência (ex: Salário)
        'frequency',    // daily, weekly, monthly, yearly
        'interval',     // 1, 2, ...
        'next_date',
        'amount',
        'category_id',
        'direction',    // income | expense
        'active',
    ];

    protected $casts = [
        'next_date' => 'date',
        'active' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
