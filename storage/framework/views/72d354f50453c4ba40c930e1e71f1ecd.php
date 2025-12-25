<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - EventPro</title>

    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="/" style="color: white; text-decoration: none;">EventPro</a>
        </div>

        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/events/create">Create Event</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">
                Welcome, <?php echo e(session('full_name')); ?>

            </span>

            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?php echo e(route('dashboard')); ?>" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="/events/create"><i class="fas fa-calendar-plus"></i> Create New Event</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Events</h2>
            <p>Here you can view and manage all your event bookings.</p>

            <div class="text-right mb-2">
                <a href="/events/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>

            <?php if($events->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You haven't created any events yet.
                    <a href="/events/create">Create your first event!</a>
                </div>
            <?php else: ?>
                <?php
                    $status_colors = [
                        'pending' => '#ffc107',
                        'approved' => '#28a745',
                        'rejected' => '#dc3545',
                        'completed' => '#007bff',
                    ];
                ?>

                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Guests</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($event->event_name); ?></td>
                                <td>
                                    <?php echo e(\Carbon\Carbon::parse($event->event_date)->format('M d, Y')); ?><br>
                                    <?php echo e(\Carbon\Carbon::parse($event->event_time)->format('h:i A')); ?>

                                </td>
                                <td><?php echo e($event->place_name); ?></td>
                                <td><?php echo e($event->number_of_guests); ?></td>
                                <td>$<?php echo e(number_format((float)$event->total_price, 2)); ?></td>
                                <td>
                                    <?php $color = $status_colors[$event->status] ?? '#6c757d'; ?>
                                    <span style="
                                        background-color: <?php echo e($color); ?>;
                                        color: white;
                                        padding: 0.25rem 0.5rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: bold;
                                    ">
                                        <?php echo e(ucfirst($event->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-primary" style="padding: 0.25rem 0.5rem;"
                                    href="<?php echo e(route('events.show', $event->event_id)); ?>">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="card-grid mt-3">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Events</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #6a11cb;">
                            <?php echo e($totalEvents); ?>

                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Upcoming Events</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #28a745;">
                            <?php echo e($upcomingEvents); ?>

                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Pending Approval</h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #ffc107;">
                            <?php echo e($pendingEvents); ?>

                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <span>EventPro</span>
            </div>
            <div>
                <p>&copy; 2024 EventPro. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/dashboard.blade.php ENDPATH**/ ?>