<?php
// routes/web.php
use App\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Controllers\DashboardController;
use App\Controllers\EventController;
use App\Controllers\AdminController;
use App\Controllers\AdminEventsController;
use App\Controllers\AdminItemsController;
use App\Controllers\CalendarController;

use App\Controllers\AdminCalendarController;
use App\Controllers\MessageController;
use App\Middleware\LocaleMiddleware;

// Wrap all web routes within the locale middleware to enable language
// switching via the 'lang' query parameter or stored session value. We
// reference the middleware class directly to avoid container binding
// issues with string aliases.
Route::middleware(LocaleMiddleware::class)->group(function () {

// Public route
// Render the home page. When visiting '/', show the home.blade.php view.
Route::get('/', function () {
    return view('home');
})->name('home');

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

    // Guest management routes for events
    Route::get('/events/{eventId}/guests', [\App\Controllers\GuestController::class, 'index'])->name('events.guests');
    Route::post('/events/{eventId}/guests/import', [\App\Controllers\GuestController::class, 'import'])->name('events.guests.import');
    Route::get('/events/{eventId}/guests/export', [\App\Controllers\GuestController::class, 'export'])->name('events.guests.export');

    // Payment routes
    Route::get('/events/{eventId}/payment', [\App\Controllers\PaymentController::class, 'show'])->name('events.payment.show');
    Route::post('/events/{eventId}/payment', [\App\Controllers\PaymentController::class, 'store'])->name('events.payment.process');

    // Cancellation route
    Route::post('/events/{id}/cancel', [EventController::class, 'cancel'])->name('events.cancel');

    // Messaging routes
    Route::get('/messages', [\App\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [\App\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [\App\Controllers\MessageController::class, 'store'])->name('messages.store');

    // Notification routes
    Route::get('/notifications', [\App\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
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

    // Packages management
    Route::get('/packages', [\App\Controllers\AdminPackagesController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [\App\Controllers\AdminPackagesController::class, 'create'])->name('packages.create');
    Route::post('/packages', [\App\Controllers\AdminPackagesController::class, 'store'])->name('packages.store');
    Route::get('/packages/{id}/edit', [\App\Controllers\AdminPackagesController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{id}', [\App\Controllers\AdminPackagesController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}', [\App\Controllers\AdminPackagesController::class, 'destroy'])->name('packages.delete');

    // Admin messaging (admin/event manager -> clients)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

// Calendar routes
Route::middleware('login')->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/day/{date}', [CalendarController::class, 'dayView'])->name('calendar.day');
});


// Admin Calendar Routes
// Use the "login" middleware alias to ensure the user is authenticated,
// and the "admin" alias to ensure they have admin privileges.
Route::middleware(['login', 'admin'])->prefix('admin')->group(function () {
    // Calendar routes
    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('admin.calendar.index');
    Route::get('/calendar/day/{date}', [AdminCalendarController::class, 'showDay'])->name('admin.calendar.day');
});

}); // End locale middleware group