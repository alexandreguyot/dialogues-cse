<?php

namespace App\Providers;

use App\Models\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Request::macro('isAdmin', function () {
            return $this->getHost() === adminUrl();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Carbon::setlocale('fr_FR.UTF-8');
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        setlocale(LC_MONETARY, 'fr_FR.UTF-8');

        View::composer('layouts.app', function ($view) {
            $view->with('menuPages', Page::published()->where('in_menu', true)->orderBy('id')->get(['title', 'slug']));
            $view->with('legalPages', Page::published()->whereIn('slug', Page::LEGAL_SLUGS)->orderBy('id')->get(['title', 'slug']));
        });
    }
}
