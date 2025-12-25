<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - EventPro</title>

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
                <li><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Welcome, <?php echo e(session('full_name')); ?></span>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <h2 class="section-title">Create New Event</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('events.store')); ?>" class="form-container">
        <?php echo csrf_field(); ?>

        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Event Information</h3>

            <div class="form-group">
                <label for="event_name">Event Name *</label>
                <input type="text" id="event_name" name="event_name" class="form-control"
                       value="<?php echo e(old('event_name')); ?>" required>
            </div>

            <div class="event-form-grid">
                <div class="form-group">
                    <label for="event_date">Event Date *</label>
                    <input type="date" id="event_date" name="event_date" class="form-control"
                           value="<?php echo e(old('event_date')); ?>"
                           required
                           min="<?php echo e(now()->format('Y-m-d')); ?>">
                </div>

                <div class="form-group">
                    <label for="event_time">Event Time *</label>
                    <input type="time" id="event_time" name="event_time" class="form-control"
                           value="<?php echo e(old('event_time', '18:00')); ?>" required>
                </div>

                <div class="form-group">
                    <label for="guests">Number of Guests *</label>
                    <input type="number" id="guests" name="guests" class="form-control"
                           value="<?php echo e(old('guests', 50)); ?>" min="10" max="1000" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-map-marker-alt"></i> Select Venue</h3>
            <div class="card-grid">
                <?php $__currentLoopData = $places; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $place): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <div class="card-img"><i class="fas fa-building"></i></div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo e($place->place_name); ?></h3>
                            <p><?php echo e($place->description); ?></p>
                            <p>Capacity: <?php echo e($place->capacity); ?> guests</p>
                            <p class="card-price">$<?php echo e(number_format((float)$place->price, 2)); ?></p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="place_id"
                                       value="<?php echo e($place->place_id); ?>"
                                       data-price="<?php echo e((float)$place->price); ?>"
                                       required
                                       <?php if(old('place_id') == $place->place_id): echo 'checked'; endif; ?>>
                                Select This Venue
                            </label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-utensils"></i> Select Menu</h3>
            <div class="card-grid">
                <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <div class="card-img"><i class="fas fa-hamburger"></i></div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo e($food->food_name); ?></h3>
                            <p><?php echo e($food->description); ?></p>
                            <p class="card-price">$<?php echo e(number_format((float)$food->price_per_person, 2)); ?> per person</p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="food_id"
                                       value="<?php echo e($food->food_id); ?>"
                                       data-price-per-person="<?php echo e((float)$food->price_per_person); ?>"
                                       required
                                       <?php if(old('food_id') == $food->food_id): echo 'checked'; endif; ?>>
                                Select This Menu
                            </label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-palette"></i> Select Design Theme</h3>
            <div class="card-grid">
                <?php $__currentLoopData = $designs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $design): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <div class="card-img"><i class="fas fa-paint-brush"></i></div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo e($design->design_name); ?></h3>
                            <p><?php echo e($design->description); ?></p>
                            <p class="card-price">$<?php echo e(number_format((float)$design->price, 2)); ?></p>

                            <label style="display:block; text-align:center;">
                                <input type="radio"
                                       name="design_id"
                                       value="<?php echo e($design->design_id); ?>"
                                       data-price="<?php echo e((float)$design->price); ?>"
                                       required
                                       <?php if(old('design_id') == $design->design_id): echo 'checked'; endif; ?>>
                                Select This Design
                            </label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-star"></i> Additional Information</h3>
            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" class="form-control" rows="4"><?php echo e(old('special_requests')); ?></textarea>
                <small>Any special requirements or notes for your event</small>
            </div>
        </div>

        <div class="form-section" style="background-color:#e3f2fd;">
            <h3><i class="fas fa-calculator"></i> Price Summary</h3>
            <div id="price-summary">
                <p>Select options above to see pricing details</p>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-success" style="padding: 1rem 3rem; font-size: 1.2rem;">
                <i class="fas fa-check-circle"></i> Submit Event Request
            </button>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary" style="padding: 1rem 3rem; font-size: 1.2rem;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculatePrice() {
        const guests = parseInt(document.getElementById('guests').value) || 50;

        const selectedPlace = document.querySelector('input[name="place_id"]:checked');
        const selectedFood = document.querySelector('input[name="food_id"]:checked');
        const selectedDesign = document.querySelector('input[name="design_id"]:checked');

        if (selectedPlace && selectedFood && selectedDesign) {
            const placePrice = parseFloat(selectedPlace.dataset.price || '0');
            const foodPrice = parseFloat(selectedFood.dataset.pricePerPerson || '0');
            const designPrice = parseFloat(selectedDesign.dataset.price || '0');

            const total = placePrice + (foodPrice * guests) + designPrice;

            document.getElementById('price-summary').innerHTML = `
                <div style="font-size: 1.2rem;">
                    <p>Venue: $${placePrice.toFixed(2)}</p>
                    <p>Food (${guests} guests × $${foodPrice.toFixed(2)}): $${(foodPrice * guests).toFixed(2)}</p>
                    <p>Design: $${designPrice.toFixed(2)}</p>
                    <hr>
                    <p style="font-weight: bold; font-size: 1.5rem; color: #6a11cb;">
                        Total: $${total.toFixed(2)}
                    </p>
                </div>
            `;
        }
    }

    document.getElementById('guests').addEventListener('input', calculatePrice);
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    calculatePrice();
});
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\evnt\myapp\resources\views/events/create.blade.php ENDPATH**/ ?>