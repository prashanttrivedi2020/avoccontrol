<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'name', 'barcode', 'category', 'supplier',
        'purchase_price', 'unit', 'active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function losses()
    {
        return $this->hasMany(Loss::class);
    }

    public function totalLossQuantity()
    {
        return $this->losses()->sum('quantity');
    }

    public function totalLossValue()
    {
        return $this->losses()->selectRaw('SUM(quantity * purchase_price) as total')->value('total') ?? 0;
    }
}
