<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use App\Http\Controllers\AdminVendorController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GoogleCalendarSyncController;

Volt::route('/', 'landing')->name('landing');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'login')->name('register');
Volt::route('/admin/login', 'admin.login')->name('admin.login');
Volt::route('/forgot-password', 'forgot-password')
    ->middleware('guest')
    ->name('password.request');
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/my/wedding', 'my-wedding')->name('my-wedding');
    Volt::route('/wedding/setup', 'wedding-setup')->name('wedding.setup');
    Volt::route('/tasks', 'tasks')->name('tasks');
    Volt::route('/budget', 'budget')->name('budget');
    Volt::route('/vendors', 'vendors')->name('vendors');
    Volt::route('/calendar', 'calendar')->name('calendar');
    Volt::route('/profile', 'profile')->name('profile');
    Route::post('/calendar/google/sync', [GoogleCalendarSyncController::class, 'sync'])
    ->name('calendar.google.sync');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/vendors', [AdminVendorController::class, 'index'])->name('vendors');
        Route::post('/vendors', [AdminVendorController::class, 'store'])->name('vendors.store');
        Route::put('/vendors/{vendor}', [AdminVendorController::class, 'update'])->name('vendors.update');
        Route::delete('/vendors/{vendor}', [AdminVendorController::class, 'destroy'])->name('vendors.destroy');
        Route::patch('/vendors/{vendor}/toggle-status', [AdminVendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');
        Route::patch('/settings/theme', [AdminSettingsController::class, 'updateTheme'])->name('settings.theme');
    });
