<?php

namespace App\Providers;

use App\Models\Loss;
use App\Models\Product;
use App\Models\Reason;
use App\Models\Unit;
use App\Policies\LossPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReasonPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Loss::class, LossPolicy::class);
        Gate::policy(Unit::class, UnitPolicy::class);
        Gate::policy(Reason::class, ReasonPolicy::class);
        Paginator::useBootstrapFive();
    }
}
