<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Admin</title>

    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>">
</head>
<body>
<div class="container" style="margin-top: 2rem;">
    <h2>Event Details - <?php echo e($event->event_name); ?></h2>

    <?php if(session('message')): ?>
        <div class="alert alert-success"><?php echo e(session('message')); ?></div>
    <?php endif; ?>

    <div class="event-form-grid">
        <div class="form-section">
            <h4>Customer Information</h4>
            <p><strong>Name:</strong> <?php echo e($event->full_name); ?></p>
            <p><strong>Email:</strong> <?php echo e($event->email); ?></p>
            <?php if(!empty($event->phone)): ?>
                <p><strong>Phone:</strong> <?php echo e($event->phone); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-section">
            <h4>Event Details</h4>
            <p><strong>Date:</strong> <?php echo e(\Carbon\Carbon::parse($event->event_date)->format('F j, Y')); ?></p>
            <p><strong>Time:</strong> <?php echo e(\Carbon\Carbon::parse($event->event_time)->format('h:i A')); ?></p>
            <p><strong>Guests:</strong> <?php echo e($event->number_of_guests); ?></p>
            <p><strong>Status:</strong> <?php echo e(ucfirst($event->status)); ?></p>
        </div>

        <div class="form-section">
            <h4>Venue</h4>
            <p><strong>Place:</strong> <?php echo e($event->place_name); ?></p>
            <p><strong>Capacity:</strong> <?php echo e($event->place_capacity); ?> guests</p>
            <p><strong>Price:</strong> $<?php echo e(number_format((float)$event->place_price, 2)); ?></p>
        </div>

        <div class="form-section">
            <h4>Food & Design</h4>
            <p><strong>Menu:</strong> <?php echo e($event->food_name); ?></p>
            <p><strong>Food Price/Person:</strong> $<?php echo e(number_format((float)$event->food_price_per_person, 2)); ?></p>
            <p><strong>Design:</strong> <?php echo e($event->design_name); ?></p>
            <p><strong>Design Price:</strong> $<?php echo e(number_format((float)$event->design_price, 2)); ?></p>
            <p><strong>Special Requests:</strong> <?php echo e($event->special_requests ?: 'None'); ?></p>
        </div>
    </div>

    <div class="form-section" style="background-color: #e3f2fd;">
        <h4>Pricing Breakdown</h4>
        <p>Venue: $<?php echo e(number_format((float)$event->place_price, 2)); ?></p>
        <p>
            Food (<?php echo e($event->number_of_guests); ?> guests × $<?php echo e(number_format((float)$event->food_price_per_person, 2)); ?>):
            $<?php echo e(number_format((float)$event->food_price_per_person * (int)$event->number_of_guests, 2)); ?>

        </p>
        <p>Design: $<?php echo e(number_format((float)$event->design_price, 2)); ?></p>
        <hr>
        <p><strong>Total: $<?php echo e(number_format((float)$event->total_price, 2)); ?></strong></p>
    </div>

    <div style="display:flex; gap: 0.5rem; flex-wrap:wrap;">
        <a href="<?php echo e(route('admin.events.index')); ?>" class="btn btn-secondary">Back</a>

        <?php if($event->status === 'pending'): ?>
            <form method="POST" action="<?php echo e(route('admin.events.approve', $event->event_id)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-success" onclick="return confirm('Approve this event?')">Approve</button>
            </form>

            <form method="POST" action="<?php echo e(route('admin.events.reject', $event->event_id)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-danger" onclick="return confirm('Reject this event?')">Reject</button>
            </form>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.events.delete', $event->event_id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="btn btn-danger" onclick="return confirm('Delete this event permanently?')">Delete</button>
        </form>
    </div>
</div>
</body>
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/admin/events/show.blade.php ENDPATH**/ ?>