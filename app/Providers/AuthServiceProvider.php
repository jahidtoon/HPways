<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Application;
use App\Policies\ApplicationPolicy;
use App\Models\Document;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Application::class => ApplicationPolicy::class,
    Document::class => DocumentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Policies automatically registered.
    }
}
