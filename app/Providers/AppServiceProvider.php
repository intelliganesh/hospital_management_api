<?php

namespace App\Providers;

use App\Services\Patient;
use App\Contracts\PatientContract;
use App\Services\DepartmentService;
use App\Services\FormateDateService;
use App\Services\LogActivityService;
use Illuminate\Support\ServiceProvider;
use App\Services\AutoIdGenerateService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PatientContract::class, Patient::class);
        $this->app->bind('logactivity', function () {
            return new LogActivityService();
        });
        $this->app->bind('formatedate', function () {
            return new FormateDateService();
        });
        $this->app->bind('autoidgenerate', function () {
            return new AutoIdGenerateService();
        });
        $this->app->bind('department', function () {
            return new DepartmentService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin passes every Gate / Spatie check
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return method_exists($user, 'hasRole') && $user->hasRole(config('rbac.super_roles', ['Super Admin']), config('rbac.guard', 'api')) ? true : null;
        });
    }
}
