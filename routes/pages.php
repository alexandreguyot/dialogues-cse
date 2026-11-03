<?php

use App\Http\Controllers\Site\PageController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

// Sans le groupe web : pas de session ni de cookie sur des images mises en cache.
Route::get('/media/pages/{file}', [PageController::class, 'media'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('pages.media');

// Chargé en dernier : les routes du site et de l'admin restent prioritaires.
Route::middleware('web')->get('/{slug}', [PageController::class, 'show'])
    ->where('slug', Page::SLUG_PATTERN)
    ->name('pages.show');
