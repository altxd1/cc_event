<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo e($cfg['title']); ?> - EventPro Admin</title>

    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
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
        .btn-primary { background-color:#6a11cb !important; color:white !important; border-color:#6a11cb !important; }
        .btn-success { background-color:#28a745 !important; color:white !important; border-color:#28a745 !important; }
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
                <li><a href="<?php echo e(route('admin.events.index')); ?>">Manage Events</a></li>
                <li><a href="<?php echo e(route('admin.items', ['type' => $type])); ?>" class="active">Manage Items</a></li>
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
                    <li><a href="<?php echo e(route('admin.events.index')); ?>"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>

                    <li>
                        <a href="<?php echo e(route('admin.items', ['type' => 'food'])); ?>" class="<?php echo e($type === 'food' ? 'active' : ''); ?>">
                            <i class="fas fa-utensils"></i> Food Items
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.items', ['type' => 'places'])); ?>" class="<?php echo e($type === 'places' ? 'active' : ''); ?>">
                            <i class="fas fa-map-marker-alt"></i> Event Places
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.items', ['type' => 'designs'])); ?>" class="<?php echo e($type === 'designs' ? 'active' : ''); ?>">
                            <i class="fas fa-palette"></i> Event Designs
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Manage <?php echo e($cfg['title']); ?></h2>

            <?php if(session('message')): ?>
                <div class="alert alert-success"><?php echo e(session('message')); ?></div>
            <?php endif; ?>

            <div class="tab-container">
                <a href="<?php echo e(route('admin.items', ['type' => 'food'])); ?>" class="tab-button <?php echo e($type === 'food' ? 'tab-active' : 'tab-inactive'); ?>">
                    <i class="fas fa-utensils"></i> Food Items
                </a>

                <a href="<?php echo e(route('admin.items', ['type' => 'places'])); ?>" class="tab-button <?php echo e($type === 'places' ? 'tab-active' : 'tab-inactive'); ?>">
                    <i class="fas fa-map-marker-alt"></i> Event Places
                </a>

                <a href="<?php echo e(route('admin.items', ['type' => 'designs'])); ?>" class="tab-button <?php echo e($type === 'designs' ? 'tab-active' : 'tab-inactive'); ?>">
                    <i class="fas fa-palette"></i> Event Designs
                </a>

                <a href="<?php echo e(route('admin.items.create', ['type' => $type])); ?>" class="btn btn-success" style="float:right;">
                    <i class="fas fa-plus"></i> Add New
                </a>

                <div style="clear:both;"></div>
            </div>

            <?php if($items->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No items found.
                    <a href="<?php echo e(route('admin.items.create', ['type' => $type])); ?>">Add your first item!</a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <?php if($cfg['has_capacity']): ?>
                                <th>Capacity</th>
                            <?php endif; ?>
                            <th>Price</th>
                            <?php if($hasIsAvailable): ?>
                                <th>Status</th>
                            <?php endif; ?>
                            <?php if($hasCreatedAt): ?>
                                <th>Created</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $id = $item->{$cfg['pk']};
                                $name = $item->{$cfg['name']};
                                $price = $item->{$cfg['price']};
                                $desc = $item->description ?? '';
                                $available = $hasIsAvailable ? (int)($item->is_available ?? 0) : 1;
                            ?>
                            <tr>
                                <td>#<?php echo e($id); ?></td>
                                <td>
                                    <strong><?php echo e($name); ?></strong><br>
                                    <small><?php echo e(\Illuminate\Support\Str::limit($desc, 50)); ?></small>
                                </td>

                                <?php if($cfg['has_capacity']): ?>
                                    <td><?php echo e($item->capacity ?? '-'); ?> guests</td>
                                <?php endif; ?>

                                <td>
                                    $<?php echo e(number_format((float)$price, 2)); ?>

                                    <?php if($type === 'food'): ?>
                                        <br><small>per person</small>
                                    <?php endif; ?>
                                </td>

                                <?php if($hasIsAvailable): ?>
                                    <td>
                                        <span style="
                                            background-color: <?php echo e($available ? '#28a745' : '#dc3545'); ?>;
                                            color: white;
                                            padding: 0.25rem 0.5rem;
                                            border-radius: 20px;
                                            font-size: 0.85rem;
                                        ">
                                            <?php echo e($available ? 'Available' : 'Unavailable'); ?>

                                        </span>
                                    </td>
                                <?php endif; ?>

                                <?php if($hasCreatedAt): ?>
                                    <td><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('M d, Y')); ?></td>
                                <?php endif; ?>

                                <td>
                                    <div style="display:flex; gap:0.25rem;">
                                        <a href="<?php echo e(route('admin.items.edit', ['id' => $id, 'type' => $type])); ?>"
                                           class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="<?php echo e(route('admin.items.delete', ['id' => $id, 'type' => $type])); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-danger" style="padding: 0.25rem 0.5rem;"
                                                    onclick="return confirm('Delete this item?')">
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
</body>
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/admin/items/index.blade.php ENDPATH**/ ?>