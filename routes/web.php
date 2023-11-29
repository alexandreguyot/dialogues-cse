<?php

use App\Http\Controllers\Site\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/une-identite-visuelle-qui-vous-ressemble', [HomeController::class, 'identite'])->name('identite');
Route::get('/communiquer-au-quotidien', [HomeController::class, 'communiquer'])->name('communiquer');
Route::get('/vous-aider-lors-des-reunions-CSE', [HomeController::class, 'reunions'])->name('reunions');
Route::get('/un-site-web-qui-parle-pour-vous', [HomeController::class, 'site'])->name('site');
Route::get('/demander-un-devis', [HomeController::class, 'devis'])->name('devis');

Route::post('/envoyer-demander-devis', [HomeController::class, 'sendEmail'])->name('sendEmail');
