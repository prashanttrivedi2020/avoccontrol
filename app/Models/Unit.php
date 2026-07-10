<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'name', 'sort_order', 'is_active'];

    public $timestamps = true;

    /**
     * Relationship: Unit belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all active units for a user ordered by sort_order
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Scope to get active units only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
