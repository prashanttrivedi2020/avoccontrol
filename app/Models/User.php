<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'store_name',
        'company_name',
        'owner_name',
        'address',
        'tax_number',
        'logo_path',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productImportFailures()
    {
        return $this->hasMany(ProductImportFailure::class);
    }

    public function losses()
    {
        return $this->hasMany(Loss::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function reasons()
    {
        return $this->hasMany(Reason::class);
    }

    public function isDemo(): bool
    {
        return $this->role === 'demo';
    }
}
