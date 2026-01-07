<?php
// routes/web.php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventsController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\CalendarController;

use App\Http\Controllers\AdminCalendarController;

// Public routes
Route::view('/', 'home')->name('home'); // If you have home.blade.php
Route::get('/', function() { return view('home'); })->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'create'])->name('login.form');
Route::post('/login', [AuthController::class, 'store'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register');

// Dashboard routes (protected)
Route::middleware('login')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Event routes
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    
    // Availability check route
    Route::post('/events/check-availability', [EventController::class, 'checkAvailability'])
        ->name('events.check-availability');
});

// Admin routes (protected)
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Events management
    Route::get('/events', [AdminEventsController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [AdminEventsController::class, 'show'])->name('events.show');
    Route::post('/events/{id}/approve', [AdminEventsController::class, 'approve'])->name('events.approve');
    Route::post('/events/{id}/reject', [AdminEventsController::class, 'reject'])->name('events.reject');
    Route::delete('/events/{id}', [AdminEventsController::class, 'destroy'])->name('events.delete');
    
    // Items management
    Route::get('/items', [AdminItemsController::class, 'index'])->name('items');
    Route::get('/items/create', [AdminItemsController::class, 'create'])->name('items.create');
    Route::post('/items', [AdminItemsController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [AdminItemsController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [AdminItemsController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [AdminItemsController::class, 'destroy'])->name('items.delete');
});

// Calendar routes
Route::middleware('login')->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/day/{date}', [CalendarController::class, 'dayView'])->name('calendar.day');
});


// Admin Calendar Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Calendar routes
    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('admin.calendar.index');
    Route::get('/calendar/day/{date}', [AdminCalendarController::class, 'showDay'])->name('admin.calendar.day');
});