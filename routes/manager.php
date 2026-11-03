<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// APP_URL peut être écrit avec ou sans schéma ; sans APP_SUBDOMAIN_URL, l'admin reste sur le domaine principal.
$managerHost = env('APP_SUBDOMAIN_URL')
    ? env('APP_SUBDOMAIN_URL') . '.' . preg_replace('#^https?://#', '', rtrim((string) env('APP_URL'), '/'))
    : null;

Route::domain($managerHost)->group(function () use ($managerHost) {
    if ($managerHost) {
        Route::redirect('/', '/login');
    }

    Auth::routes(['register' => false]);

    Route::group(['prefix' => 'two-factor', 'as' => 'two-factor.', 'middleware' => ['auth']], function () {
        Route::get('setup', [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('setup', [TwoFactorController::class, 'confirm'])->middleware('throttle:10,1')->name('confirm');
        Route::get('recovery', [TwoFactorController::class, 'recovery'])->name('recovery');
        Route::get('challenge', [TwoFactorController::class, 'challenge'])->name('challenge');
        Route::post('challenge', [TwoFactorController::class, 'verify'])->middleware('throttle:5,1')->name('verify');
        Route::delete('/', [TwoFactorController::class, 'disable'])->middleware('throttle:5,1')->name('disable');
    });

    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'two-factor']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Pages
        Route::resource('pages', PageController::class, ['except' => ['store', 'update', 'destroy']]);

        // Permissions
        Route::resource('permissions', PermissionController::class, ['except' => ['store', 'update', 'destroy']]);

        // Roles
        Route::resource('roles', RoleController::class, ['except' => ['store', 'update', 'destroy']]);

        // Users
        Route::resource('users', UserController::class, ['except' => ['store', 'update', 'destroy']]);
    });

    Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth', 'two-factor']], function () {
        if (file_exists(app_path('Http/Controllers/Auth/UserProfileController.php'))) {
            Route::get('/', [UserProfileController::class, 'show'])->name('show');
        }
    });
});

