<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',     // 'income' ou 'expense'
        'user_id',
    ];

    /**
     * Categoria pertence a um usuário (opcional).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Categoria tem muitas transações.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
