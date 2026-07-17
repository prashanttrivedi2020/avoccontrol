<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'name', 'sort_order', 'is_active'];

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function defaultNames(): array
    {
        return ['Spoilage', 'Expiry / BBD', 'Theft', 'Damage', 'Incident report', 'Other'];
    }

    public static function ensureDefaultsForUser(User $user): array
    {
        $existing = $user->reasons()->orderBy('sort_order')->orderBy('name')->get();

        if ($existing->isNotEmpty()) {
            return $existing->pluck('name')->all();
        }

        $defaults = self::defaultNames();

        foreach ($defaults as $index => $name) {
            // $user->reasons()->create([
            //     'name' => $name,
            //     'sort_order' => $index + 1,
            //     'is_active' => true,
            // ]);
        }

        return $defaults;
    }

    public static function getAllowedNamesForUser(User $user, ?string $reason = null): array
    {

        $names = self::ensureDefaultsForUser($user);

        if ($reason && !in_array($reason, $names, true)) {
            $names[] = $reason;
        }

        return array_values(array_unique($names));
    }
    public static function getAllowedNamesForUsers(array|\Illuminate\Support\Collection $userIds, ?string $reason = null): array
    {
        $names = self::ensureDefaultsForUsers($userIds);

        if ($reason && !in_array($reason, $names, true)) {
            $names[] = $reason;
        }

        return $names;
    }
    public static function ensureDefaultsForUsers(array|\Illuminate\Support\Collection $userIds): array
    {
        $userIds = collect($userIds)->unique()->values();

        $existing = self::whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->unique()
            ->values();

        if ($existing->isNotEmpty()) {
            return $existing->all();
        }

        // Only create defaults for the authenticated user if none exist
        $defaults = self::defaultNames();

        foreach ($defaults as $index => $name) {
            // auth()->user()->reasons()->create([
            //     'name' => $name,
            //     'sort_order' => $index + 1,
            //     'is_active' => true,
            // ]);
        }

        return $defaults;
    }
}
