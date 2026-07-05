<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;

// ─────────────────────────────────────────────
//  PUBLIC ROUTES
// ─────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password
    Route::get('/forgot-password',  [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}',  [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',         [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─────────────────────────────────────────────
//  EMAIL VERIFICATION ROUTES
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Email verified! Welcome to Daiman Sports Complex!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware('throttle:6,1')->name('verification.send');

});

// ─────────────────────────────────────────────
//  USER ROUTES (authenticated + verified)
// ─────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get( '/booking',              [BookingController::class, 'index'])->name('booking.index');
    Route::post('/booking',              [BookingController::class, 'store'])->name('booking.store');
    Route::get( '/booking/history',      [BookingController::class, 'history'])->name('booking.history');
    Route::get( '/booking/{id}',         [BookingController::class, 'show'])->name('booking.show');
    Route::patch('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get( '/booking/{id}/qr',      [BookingController::class, 'downloadQr'])->name('booking.qr');
    Route::get('/booking/slots',         [BookingController::class, 'getSlots'])->name('booking.slots');

    Route::get( '/payment/{id}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment',      [PaymentController::class, 'process'])->name('payment.process');

    Route::get(   '/profile',          [ProfileController::class, 'show'])->name('profile');
    Route::put(   '/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put(   '/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile',          [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─────────────────────────────────────────────
//  ADMIN ROUTES
// ─────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get(   '/bookings',              [AdminController::class, 'bookings'])->name('bookings');
    Route::get(   '/bookings/{id}',         [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::patch( '/bookings/{id}/approve', [AdminController::class, 'approveBooking'])->name('bookings.approve');
    Route::patch( '/bookings/{id}/reject',  [AdminController::class, 'rejectBooking'])->name('bookings.reject');
    Route::delete('/bookings/{id}',         [AdminController::class, 'destroyBooking'])->name('bookings.destroy');

    Route::get(   '/courts',             [AdminController::class, 'courts'])->name('courts');
    Route::get(   '/courts/create',      [AdminController::class, 'createCourt'])->name('courts.create');
    Route::post(  '/courts',             [AdminController::class, 'storeCourt'])->name('courts.store');
    Route::get(   '/courts/{id}/edit',   [AdminController::class, 'editCourt'])->name('courts.edit');
    Route::put(   '/courts/{id}',        [AdminController::class, 'updateCourt'])->name('courts.update');
    Route::patch( '/courts/{id}/toggle', [AdminController::class, 'toggleCourt'])->name('courts.toggle');
    Route::delete('/courts/{id}',        [AdminController::class, 'destroyCourt'])->name('courts.destroy');

    Route::get(   '/users',                  [AdminController::class, 'users'])->name('users');
    Route::get(   '/users/{id}',             [AdminController::class, 'showUser'])->name('users.show');
    Route::patch( '/users/{id}/toggle-role', [AdminController::class, 'toggleUserRole'])->name('users.toggle-role');
    Route::delete('/users/{id}',             [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/reports',  [AdminController::class, 'reports'])->name('reports');

    Route::get('/qr-scanner',        [AdminController::class, 'qrScanner'])->name('qr-scanner');
    Route::get('/qr-verify/{token}', [AdminController::class, 'verifyQr'])->name('qr-verify');
});
