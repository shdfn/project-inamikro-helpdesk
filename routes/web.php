<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD

Route::get('/', function () {
    return view('welcome');
});
=======
use App\Http\Controllers\AuthController;

// Mentor Controllers
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\TicketController as MentorTicket;
use App\Http\Controllers\Mentor\NotificationController as MentorNotification;

// IT Support Controllers
use App\Http\Controllers\ITSupport\DashboardController as ITDashboard;
use App\Http\Controllers\ITSupport\TicketController as ITTicket;
use App\Http\Controllers\ITSupport\NotificationController as ITNotification;

// Redirect root ke login
Route::get('/', fn() => redirect('/login'));

// ================= AUTH =================
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

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

    // Notifikasi
    Route::get('notifications', [ITNotification::class, 'index'])->name('notifications.index');

    // Tiket — URUTAN PENTING: spesifik dulu, baru {id}
    Route::get('tickets/incoming', [ITTicket::class, 'incoming'])->name('tickets.incoming');
    Route::get('tickets/my', [ITTicket::class, 'myTickets'])->name('tickets.my');
    Route::get('tickets/history', [ITTicket::class, 'history'])->name('tickets.history');
    Route::get('tickets/export', [ITTicket::class, 'export'])->name('tickets.export');

    Route::get('tickets/{id}', [ITTicket::class, 'show'])->name('tickets.show');
    Route::post('tickets/{id}/assign', [ITTicket::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{id}/message', [ITTicket::class, 'sendMessage'])->name('tickets.message');
    Route::post('tickets/{id}/resolve', [ITTicket::class, 'markResolved'])->name('tickets.resolve');
});
>>>>>>> d4779e587f5d906a32ef00c31279318cc940a3ee
