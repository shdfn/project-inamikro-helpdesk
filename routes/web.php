<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

// Mentor Controllers
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\TicketController as MentorTicket;
use App\Http\Controllers\Mentor\NotificationController as MentorNotification;

// IT Support Controllers
use App\Http\Controllers\ITSupport\DashboardController as ITDashboard;
use App\Http\Controllers\ITSupport\TicketController as ITTicket;
use App\Http\Controllers\ITSupport\NotificationController as ITNotification;

// Kepala IT Controllers
use App\Http\Controllers\KepalaIT\DashboardController as KepalaDashboard;
use App\Http\Controllers\KepalaIT\ReportController as KepalaReport;
use App\Http\Controllers\KepalaIT\UserController as KepalaUser;

// ================= ROOT =================
Route::get('/', fn() => redirect('/login'));

// ================= AUTH =================
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ================= LUPA SANDI =================
Route::get('forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ================= MENTOR =================
Route::middleware('auth')->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('dashboard', [MentorDashboard::class, 'index'])->name('dashboard');
    Route::get('notifications', [MentorNotification::class, 'index'])->name('notifications.index');

    Route::get('tickets', [MentorTicket::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [MentorTicket::class, 'create'])->name('tickets.create');
    Route::post('tickets', [MentorTicket::class, 'store'])->name('tickets.store');
    Route::get('tickets/{id}', [MentorTicket::class, 'show'])->name('tickets.show');
    Route::post('tickets/{id}/message', [MentorTicket::class, 'sendMessage'])->name('tickets.message');
    Route::post('tickets/{id}/confirm', [MentorTicket::class, 'confirmResolution'])->name('tickets.confirm');
});

// ================= IT SUPPORT =================
Route::middleware('auth')->prefix('it-support')->name('it.')->group(function () {
    Route::get('dashboard', [ITDashboard::class, 'index'])->name('dashboard');
    Route::get('notifications', [ITNotification::class, 'index'])->name('notifications.index');

    // URUTAN PENTING: spesifik dulu, baru {id}
    Route::get('tickets/incoming', [ITTicket::class, 'incoming'])->name('tickets.incoming');
    Route::get('tickets/my', [ITTicket::class, 'myTickets'])->name('tickets.my');
    Route::get('tickets/history', [ITTicket::class, 'history'])->name('tickets.history');
    Route::get('tickets/export', [ITTicket::class, 'export'])->name('tickets.export');

    Route::get('tickets/{id}', [ITTicket::class, 'show'])->name('tickets.show');
    Route::post('tickets/{id}/assign', [ITTicket::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{id}/message', [ITTicket::class, 'sendMessage'])->name('tickets.message');
    Route::post('tickets/{id}/resolve', [ITTicket::class, 'markResolved'])->name('tickets.resolve');
});

// ================= KEPALA IT =================
Route::middleware('auth')->prefix('kepala-it')->name('kepala.')->group(function () {
    Route::get('dashboard', [KepalaDashboard::class, 'index'])->name('dashboard');

    Route::get('reports/sla', [KepalaReport::class, 'sla'])->name('reports.sla');
    Route::get('reports/performance', [KepalaReport::class, 'performance'])->name('reports.performance');

    // Manajemen User
    Route::get('users', [KepalaUser::class, 'index'])->name('users.index');
    Route::get('users/create', [KepalaUser::class, 'create'])->name('users.create');
    Route::post('users', [KepalaUser::class, 'store'])->name('users.store');
    Route::get('users/{id}/edit', [KepalaUser::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [KepalaUser::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [KepalaUser::class, 'destroy'])->name('users.destroy');
});