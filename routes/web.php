<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\TicketController as MentorTicket;
use App\Http\Controllers\Mentor\NotificationController as MentorNotification;

// Redirect root ke login
Route::get('/', fn() => redirect('/login'));

// ================= AUTH =================
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ================= MENTOR =================
Route::middleware('auth')->prefix('mentor')->name('mentor.')->group(function () {

    // Dashboard
    Route::get('dashboard', [MentorDashboard::class, 'index'])->name('dashboard');

    // Notifikasi (HARUS didaftarkan!)
    Route::get('notifications', [MentorNotification::class, 'index'])->name('notifications.index');

    // ============ TICKET ROUTES ============
    Route::get('tickets', [MentorTicket::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [MentorTicket::class, 'create'])->name('tickets.create');
    Route::post('tickets', [MentorTicket::class, 'store'])->name('tickets.store');

    // Route dinamis {id} di paling bawah
    Route::get('tickets/{id}', [MentorTicket::class, 'show'])->name('tickets.show');
    Route::post('tickets/{id}/message', [MentorTicket::class, 'sendMessage'])->name('tickets.message');
    Route::post('tickets/{id}/confirm', [MentorTicket::class, 'confirmResolution'])->name('tickets.confirm');
});