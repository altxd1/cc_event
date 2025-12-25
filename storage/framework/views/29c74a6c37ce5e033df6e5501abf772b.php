<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - EventPro Admin</title>

    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Emergency override (kept from legacy page) */
        .btn {
            background-color: #e9ecef !important;
            color: #495057 !important;
            border: 2px solid #adb5bd !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin: 5px !important;
            padding: 10px 20px !important;
            display: inline-block !important;
        }
        .btn-primary {
            background-color: #6a11cb !important;
            color: white !important;
            border-color: #6a11cb !important;
        }
    </style>
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="<?php echo e(url('/')); ?>" style="color: white; text-decoration: none;">EventPro</a>
        </div>

        <nav>
            <ul>
                <li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li><a href="<?php echo e(route('admin.events.index')); ?>" class="active">Manage Events</a></li>
                <li><a href="<?php echo e(route('admin.items', ['type' => 'food'])); ?>">Manage Items</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Admin Panel</span>
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
                    <li><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo e(route('admin.events.index')); ?>" class="active"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    <li><a href="<?php echo e(route('admin.items', ['type' => 'food'])); ?>"><i class="fas fa-utensils"></i> Food Items</a></li>
                    <li><a href="<?php echo e(route('admin.items', ['type' => 'places'])); ?>"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                    <li><a href="<?php echo e(route('admin.items', ['type' => 'designs'])); ?>"><i class="fas fa-palette"></i> Event Designs</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Manage Events</h2>

            <?php if(session('message')): ?>
                <div class="alert alert-success"><?php echo e(session('message')); ?></div>
            <?php endif; ?>

            <div class="tab-container">
                <a href="<?php echo e(route('admin.events.index', ['status' => 'all'])); ?>"
                   class="tab-button <?php echo e($status === 'all' ? 'tab-active' : 'tab-inactive'); ?>">
                    All Events (<?php echo e($counts['all']); ?>)
                </a>

                <a href="<?php echo e(route('admin.events.index', ['status' => 'pending'])); ?>"
                   class="tab-button <?php echo e($status === 'pending' ? 'tab-active' : 'tab-inactive'); ?>">
                    Pending (<?php echo e($counts['pending']); ?>)
                </a>

                <a href="<?php echo e(route('admin.events.index', ['status' => 'approved'])); ?>"
                   class="tab-button <?php echo e($status === 'approved' ? 'tab-active' : 'tab-inactive'); ?>">
                    Approved (<?php echo e($counts['approved']); ?>)
                </a>

                <a href="<?php echo e(route('admin.events.index', ['status' => 'rejected'])); ?>"
                   class="tab-button <?php echo e($status === 'rejected' ? 'tab-active' : 'tab-inactive'); ?>">
                    Rejected (<?php echo e($counts['rejected']); ?>)
                </a>
            </div>

            <?php if($events->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No events found for the selected filter.
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
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e(str_pad($event->event_id, 5, '0', STR_PAD_LEFT)); ?></td>
                                <td><?php echo e($event->event_name); ?></td>
                                <td>
                                    <strong><?php echo e($event->full_name); ?></strong><br>
                                    <small><?php echo e($event->email); ?></small><br>
                                    <?php if(!empty($event->phone)): ?>
                                        <small><?php echo e($event->phone); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e(\Carbon\Carbon::parse($event->event_date)->format('M d, Y')); ?><br>
                                    <small><?php echo e(\Carbon\Carbon::parse($event->event_time)->format('h:i A')); ?></small>
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
                                    <div style="display:flex; gap:0.25rem; flex-wrap:wrap;">
                                        <a href="<?php echo e(route('admin.events.show', $event->event_id)); ?>"
                                           class="btn btn-primary" style="padding:0.25rem 0.5rem;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <?php if($event->status === 'pending'): ?>
                                            <form method="POST" action="<?php echo e(route('admin.events.approve', $event->event_id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button class="btn btn-success" style="padding:0.25rem 0.5rem;"
                                                        onclick="return confirm('Approve this event?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <form method="POST" action="<?php echo e(route('admin.events.reject', $event->event_id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button class="btn btn-danger" style="padding:0.25rem 0.5rem;"
                                                        onclick="return confirm('Reject this event?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" action="<?php echo e(route('admin.events.delete', $event->event_id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-danger" style="padding:0.25rem 0.5rem;"
                                                    onclick="return confirm('Delete this event permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

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
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/admin/events/index.blade.php ENDPATH**/ ?>