<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\VerificationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage-link', function () {
    if (file_exists(public_path('storage'))) {
        return 'The "public/storage" directory already exists.';
    }

    app('files')->link(storage_path('app/public'), public_path('storage'));

    return 'The [public/storage] directory has been linked.';
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/podcasts', [PodcastController::class, 'index'])->name('podcasts.index');
    Route::get('/earnings', [EarningController::class, 'index'])->name('earnings.index');
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/email/verify/success', [VerificationController::class, 'success'])
    ->name('verification.success');

Route::get('/email/verify/error', [VerificationController::class, 'error'])
    ->name('verification.error');

// Password Reset Routes
Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])
    ->name('password.reset')
    ->middleware('guest')
    ->where('token', '.*');

Route::post('reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update')
    ->middleware('guest');

Route::get('/', [LandingPageController::class, 'show'])->name('landing');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/about', 'about')->name('about');
Route::view('/cookie-policy', 'cookie-policy')->name('cookie-policy');
Route::view('/gdpr', 'gdpr')->name('gdpr');

Route::get('/contact-us', [ContactController::class, 'showForm'])->name('contact.show');
Route::post('/contact-us', [ContactController::class, 'submitForm'])->name('contact.submit');
