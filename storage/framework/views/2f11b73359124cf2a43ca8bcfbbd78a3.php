<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($item ? 'Edit' : 'Add'); ?> <?php echo e($cfg['title']); ?> - Admin</title>

    <link rel="stylesheet" href="<?php echo e(asset('style.css')); ?>">
</head>
<body>
<div class="container" style="margin-top: 2rem;">
    <h2><?php echo e($item ? 'Edit' : 'Add New'); ?> <?php echo e($cfg['title']); ?></h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <?php
        $actionUrl = $item
            ? route('admin.items.update', ['id' => $item->{$cfg['pk']}, 'type' => $type])
            : route('admin.items.store', ['type' => $type]);
    ?>

    <form method="POST" action="<?php echo e($actionUrl); ?>" class="form-container">
        <?php echo csrf_field(); ?>
        <?php if($item): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" required
                   value="<?php echo e(old('name', $item ? $item->{$cfg['name']} : '')); ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo e(old('description', $item->description ?? '')); ?></textarea>
        </div>

        <?php if($cfg['has_capacity']): ?>
            <div class="form-group">
                <label>Capacity *</label>
                <input type="number" name="capacity" class="form-control" min="1" required
                       value="<?php echo e(old('capacity', $item->capacity ?? '')); ?>">
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label><?php echo e($cfg['price_label']); ?> *</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required
                   value="<?php echo e(old('price', $item ? $item->{$cfg['price']} : '')); ?>">
        </div>

        <?php if($hasIsAvailable): ?>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_available" value="1"
                        <?php if(old('is_available', $item ? (int)($item->is_available ?? 0) : 1) == 1): echo 'checked'; endif; ?>>
                    Available for selection
                </label>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <?php echo e($item ? 'Update Item' : 'Add Item'); ?>

            </button>

            <a href="<?php echo e(route('admin.items', ['type' => $type])); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/admin/items/form.blade.php ENDPATH**/ ?>