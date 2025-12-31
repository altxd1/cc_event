<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventsController;
use App\Http\Controllers\AdminItemsController;
Route::get('/login', [AuthController::class, 'create'])->name('login.form');
Route::post('/login', [AuthController::class, 'store'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard');
Route::view('/admin', 'admin');

Route::view('/dashboard', 'dashboard')->middleware('login');
Route::view('/admin', 'admin')->middleware('admin');

Route::get('/register', [\App\Http\Controllers\AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'registerStore'])->name('register');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('login')   // uses your RequireLogin middleware alias
    ->name('dashboard');

Route::get('/events/create', [EventController::class, 'create'])
    ->middleware('login')
    ->name('events.create');

Route::post('/events', [EventController::class, 'store'])
    ->middleware('login')
    ->name('events.store');

Route::view('/', 'home')->name('home');

Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');

// Temporary placeholders so your menu links won't 404 (we’ll implement next)
Route::get('/admin/events', fn () => 'Manage Events page coming next')->middleware('admin')->name('admin.events');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/events', [AdminEventsController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [AdminEventsController::class, 'show'])->name('events.show');

    Route::post('/events/{id}/approve', [AdminEventsController::class, 'approve'])->name('events.approve');
    Route::post('/events/{id}/reject', [AdminEventsController::class, 'reject'])->name('events.reject');
    Route::delete('/events/{id}', [AdminEventsController::class, 'destroy'])->name('events.delete');
});

Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register');

Route::get('/events/{id}', [EventController::class, 'show'])
    ->middleware('login')
    ->name('events.show');

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/items', [AdminItemsController::class, 'index'])->name('admin.items');

    Route::get('/items/create', [AdminItemsController::class, 'create'])->name('admin.items.create');
    Route::post('/items', [AdminItemsController::class, 'store'])->name('admin.items.store');

    Route::get('/items/{id}/edit', [AdminItemsController::class, 'edit'])->name('admin.items.edit');
    Route::put('/items/{id}', [AdminItemsController::class, 'update'])->name('admin.items.update');

    Route::delete('/items/{id}', [AdminItemsController::class, 'destroy'])->name('admin.items.delete');
});