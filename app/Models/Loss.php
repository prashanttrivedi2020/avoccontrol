<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loss extends Model
{
      public const REASONS = [
        'verderb',
        'ablauf',
        'diebstahl',
        'beschaedigung',
        'tathergang',
        'sonstiges',
    ];
    protected $fillable = [
        'user_id', 'product_id', 'loss_date', 'quantity', 'unit',
        'reason', 'supplier', 'purchase_price', 'photo_path', 'notes', 'immutable_hash',
    ];

    protected function casts(): array
    {
        return [
            'loss_date' => 'date',
            'quantity' => 'decimal:3',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function totalValue(): float
    {
        return (float) ($this->quantity * ($this->purchase_price ?? 0));
    }

    /**
     * Return the human-readable, translated label for a reason slug.
     */
    public static function reasonLabel(string $reason): string
    {
        return match($reason) {
            'verderb'       => __('Spoilage'),
            'ablauf'        => __('Expiry / BBD'),
            'diebstahl'     => __('Theft'),
            'beschaedigung' => __('Damage'),
            'tathergang'    => __('Incident report'),
            'sonstiges'     => __('Other'),
            default         => $reason,
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loss) {
            // GoBD-compliant immutable hash
            $loss->immutable_hash = hash('sha256', json_encode([
                'product_id'    => $loss->product_id,
                'user_id'       => $loss->user_id,
                'loss_date'     => $loss->loss_date,
                'quantity'      => $loss->quantity,
                'reason'        => $loss->reason,
                'purchase_price'=> $loss->purchase_price,
                'nonce'         => Str::random(16),
            ]));
        });
    }
}
