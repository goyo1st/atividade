<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'recurrence_id',
        'amount',
        'direction',     // 'income' ou 'expense'
        'description',
        'happened_at',   // data da transação
        'is_recurring',
    ];

    protected $casts = [
        'happened_at' => 'date',
        'is_recurring' => 'boolean',
        'amount' => 'decimal:2',
    ];

    /**
     * Transação pertence a um usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transação pertence a uma categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Transação pode pertencer a uma recorrência.
     */
    public function recurrence()
    {
        return $this->belongsTo(Recurrence::class);
    }
}
