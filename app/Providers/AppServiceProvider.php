<?php

namespace App\Providers;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    $abilities = collect(config('permissions.permissions'))->flatten()->unique();

    foreach ($abilities as $ability) {
        Gate::define($ability, fn (User $user) => $user->hasPermission($ability));
    }}
}
