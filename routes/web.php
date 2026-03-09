<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Assistant\DashboardController as AssistantDashboardController;
use App\Http\Controllers\Assistant\SessionController as AssistantSessionController;
use App\Http\Controllers\Auth\RequiredPasswordChangeController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\SessionController as ClientSessionController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\FrontDesk\ClientController;
use App\Http\Controllers\FrontDesk\DashboardController as FrontDeskDashboardController;
use App\Http\Controllers\FrontDesk\SessionController as FrontDeskSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionNoteController;
use App\Http\Controllers\Therapist\DashboardController as TherapistDashboardController;
use App\Http\Controllers\Therapist\SessionController as TherapistSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/password/required', [RequiredPasswordChangeController::class, 'edit'])->name('password.required.edit');
    Route::put('/password/required', [RequiredPasswordChangeController::class, 'update'])->name('password.required.update');
});

Route::middleware(['auth', 'password.changed'])->group(function (): void {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'password.changed', 'role:front_desk'])->prefix('front-desk')->name('front-desk.')->group(function (): void {
    Route::get('/dashboard', [FrontDeskDashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar-events', [FrontDeskSessionController::class, 'calendarEvents'])->name('calendar-events');

    Route::resource('clients', ClientController::class)
        ->parameters(['clients' => 'client'])
        ->except(['show', 'destroy']);

    Route::get('sessions/create', [FrontDeskSessionController::class, 'create'])->name('sessions.create');
    Route::post('sessions', [FrontDeskSessionController::class, 'store'])->name('sessions.store');
    Route::get('sessions/{session}', [FrontDeskSessionController::class, 'show'])->name('sessions.show');
    Route::get('sessions/{session}/edit', [FrontDeskSessionController::class, 'edit'])->name('sessions.edit');
    Route::patch('sessions/{session}', [FrontDeskSessionController::class, 'update'])->name('sessions.update');
});

Route::middleware(['auth', 'password.changed', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('staff', StaffController::class)
        ->parameters(['staff' => 'staff'])
        ->except(['show', 'destroy']);

    Route::get('sessions/create', [AdminSessionController::class, 'create'])->name('sessions.create');
    Route::post('sessions', [AdminSessionController::class, 'store'])->name('sessions.store');
    Route::get('sessions/{session}', [AdminSessionController::class, 'show'])->name('sessions.show');
    Route::get('sessions/{session}/edit', [AdminSessionController::class, 'edit'])->name('sessions.edit');
    Route::patch('sessions/{session}', [AdminSessionController::class, 'update'])->name('sessions.update');
    Route::patch('sessions/{session}/notes', [SessionNoteController::class, 'update'])->name('sessions.notes.update');
});

Route::middleware(['auth', 'password.changed', 'role:therapist'])->prefix('therapist')->name('therapist.')->group(function (): void {
    Route::get('/dashboard', [TherapistDashboardController::class, 'index'])->name('dashboard');

    Route::get('sessions/{session}', [TherapistSessionController::class, 'show'])->name('sessions.show');
    Route::patch('sessions/{session}/details', [TherapistSessionController::class, 'updateDetails'])->name('sessions.details.update');
    Route::patch('sessions/{session}/status', [TherapistSessionController::class, 'updateStatus'])->name('sessions.status.update');
    Route::post('sessions/{session}/tasks', [TherapistSessionController::class, 'storeTask'])->name('sessions.tasks.store');
    Route::patch('sessions/{session}/tasks/{task}', [TherapistSessionController::class, 'updateTask'])->name('sessions.tasks.update');
    Route::delete('sessions/{session}/tasks/{task}', [TherapistSessionController::class, 'destroyTask'])->name('sessions.tasks.destroy');
    Route::patch('sessions/{session}/notes', [SessionNoteController::class, 'update'])->name('sessions.notes.update');
});

Route::middleware(['auth', 'password.changed', 'role:assistant'])->prefix('assistant')->name('assistant.')->group(function (): void {
    Route::get('/dashboard', [AssistantDashboardController::class, 'index'])->name('dashboard');

    Route::get('sessions/{session}', [AssistantSessionController::class, 'show'])->name('sessions.show');
    Route::patch('tasks/{task}/status', [AssistantSessionController::class, 'updateTaskStatus'])->name('tasks.status.update');
});

Route::middleware(['auth', 'password.changed', 'role:client'])->prefix('client')->name('client.')->group(function (): void {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('sessions/{session}', [ClientSessionController::class, 'show'])->name('sessions.show');
});

require __DIR__.'/auth.php';
