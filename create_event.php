<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Get available options
$places = mysqli_query($conn, "SELECT * FROM event_places WHERE is_available = 1");
$food_items = mysqli_query($conn, "SELECT * FROM food_items WHERE is_available = 1");
$designs = mysqli_query($conn, "SELECT * FROM event_designs WHERE is_available = 1");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = sanitize($_POST['event_name']);
    $event_date = sanitize($_POST['event_date']);
    $event_time = sanitize($_POST['event_time']);
    $place_id = sanitize($_POST['place_id']);
    $food_id = sanitize($_POST['food_id']);
    $design_id = sanitize($_POST['design_id']);
    $guests = intval($_POST['guests']);
    $requests = sanitize($_POST['special_requests']);
    $user_id = $_SESSION['user_id'];
    
    // Validate date
    if (strtotime($event_date) < strtotime('today')) {
        $error = 'Event date must be in the future!';
    } else {
        // Get prices
        $place_result = mysqli_query($conn, "SELECT price FROM event_places WHERE place_id = '$place_id'");
        $food_result = mysqli_query($conn, "SELECT price_per_person FROM food_items WHERE food_id = '$food_id'");
        $design_result = mysqli_query($conn, "SELECT price FROM event_designs WHERE design_id = '$design_id'");
        
        $place_price = mysqli_fetch_assoc($place_result)['price'];
        $food_price = mysqli_fetch_assoc($food_result)['price_per_person'];
        $design_price = mysqli_fetch_assoc($design_result)['price'];
        
        // Calculate total
        $total_price = $place_price + ($food_price * $guests) + $design_price;
        
        // Insert event
        $sql = "INSERT INTO events (user_id, event_name, event_date, event_time, place_id, food_id, design_id, 
                number_of_guests, special_requests, total_price) 
                VALUES ('$user_id', '$event_name', '$event_date', '$event_time', '$place_id', '$food_id', '$design_id',
                '$guests', '$requests', '$total_price')";
        
        if (mysqli_query($conn, $sql)) {
            $success = 'Event created successfully! It is now pending approval.';
            // Clear form
            $_POST = array();
        } else {
            $error = 'Failed to create event. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - EventPro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <a href="index.php" style="color: white; text-decoration: none;">EventPro</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 1rem;">Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h2 class="section-title">Create New Event</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="form-container">
            <!-- Basic Information -->
            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Event Information</h3>
                <div class="form-group">
                    <label for="event_name">Event Name *</label>
                    <input type="text" id="event_name" name="event_name" class="form-control" 
                           value="<?php echo $_POST['event_name'] ?? ''; ?>" required>
                </div>
                
                <div class="event-form-grid">
                    <div class="form-group">
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" class="form-control" 
                               value="<?php echo $_POST['event_date'] ?? ''; ?>" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">Event Time *</label>
                        <input type="time" id="event_time" name="event_time" class="form-control" 
                               value="<?php echo $_POST['event_time'] ?? '18:00'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="guests">Number of Guests *</label>
                        <input type="number" id="guests" name="guests" class="form-control" 
                               value="<?php echo $_POST['guests'] ?? '50'; ?>" min="10" max="1000" required>
                    </div>
                </div>
            </div>
            
            <!-- Venue Selection -->
            <div class="form-section">
                <h3><i class="fas fa-map-marker-alt"></i> Select Venue</h3>
                <div class="card-grid">
                    <?php while ($place = mysqli_fetch_assoc($places)): ?>
                        <div class="card">
                            <div class="card-img">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $place['place_name']; ?></h3>
                                <p><?php echo $place['description']; ?></p>
                                <p>Capacity: <?php echo $place['capacity']; ?> guests</p>
                                <p class="card-price">$<?php echo number_format($place['price'], 2); ?></p>
                                <label style="display: block; text-align: center;">
                                    <input type="radio" name="place_id" value="<?php echo $place['place_id']; ?>" 
                                           required <?php echo ($_POST['place_id'] ?? '') == $place['place_id'] ? 'checked' : ''; ?>>
                                    Select This Venue
                                </label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Food Selection -->
            <div class="form-section">
                <h3><i class="fas fa-utensils"></i> Select Menu</h3>
                <div class="card-grid">
                    <?php while ($food = mysqli_fetch_assoc($food_items)): ?>
                        <div class="card">
                            <div class="card-img">
                                <i class="fas fa-hamburger"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $food['food_name']; ?></h3>
                                <p><?php echo $food['description']; ?></p>
                                <p class="card-price">$<?php echo number_format($food['price_per_person'], 2); ?> per person</p>
                                <label style="display: block; text-align: center;">
                                    <input type="radio" name="food_id" value="<?php echo $food['food_id']; ?>" 
                                           required <?php echo ($_POST['food_id'] ?? '') == $food['food_id'] ? 'checked' : ''; ?>>
                                    Select This Menu
                                </label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Design Selection -->
            <div class="form-section">
                <h3><i class="fas fa-palette"></i> Select Design Theme</h3>
                <div class="card-grid">
                    <?php while ($design = mysqli_fetch_assoc($designs)): ?>
                        <div class="card">
                            <div class="card-img">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $design['design_name']; ?></h3>
                                <p><?php echo $design['description']; ?></p>
                                <p class="card-price">$<?php echo number_format($design['price'], 2); ?></p>
                                <label style="display: block; text-align: center;">
                                    <input type="radio" name="design_id" value="<?php echo $design['design_id']; ?>" 
                                           required <?php echo ($_POST['design_id'] ?? '') == $design['design_id'] ? 'checked' : ''; ?>>
                                    Select This Design
                                </label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Special Requests -->
            <div class="form-section">
                <h3><i class="fas fa-star"></i> Additional Information</h3>
                <div class="form-group">
                    <label for="special_requests">Special Requests</label>
                    <textarea id="special_requests" name="special_requests" class="form-control" 
                              rows="4"><?php echo $_POST['special_requests'] ?? ''; ?></textarea>
                    <small>Any special requirements or notes for your event</small>
                </div>
            </div>
            
            <!-- Price Summary -->
            <div class="form-section" style="background-color: #e3f2fd;">
                <h3><i class="fas fa-calculator"></i> Price Summary</h3>
                <div id="price-summary">
                    <p>Select options above to see pricing details</p>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-success" style="padding: 1rem 3rem; font-size: 1.2rem;">
                    <i class="fas fa-check-circle"></i> Submit Event Request
                </button>
                <a href="dashboard.php" class="btn btn-secondary" style="padding: 1rem 3rem; font-size: 1.2rem;">
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
        // Price calculation
        document.addEventListener('DOMContentLoaded', function() {
            function calculatePrice() {
                const guests = parseInt(document.getElementById('guests').value) || 50;
                const selectedPlace = document.querySelector('input[name="place_id"]:checked');
                const selectedFood = document.querySelector('input[name="food_id"]:checked');
                const selectedDesign = document.querySelector('input[name="design_id"]:checked');
                
                if (selectedPlace && selectedFood && selectedDesign) {
                    // In a real application, you would fetch prices from server
                    // For now, we'll use placeholder values
                    const placePrice = 1000; // This should come from database
                    const foodPrice = 25; // Per person
                    const designPrice = 500;
                    
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
            
            // Add event listeners
            document.getElementById('guests').addEventListener('input', calculatePrice);
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', calculatePrice);
            });
            
            // Initial calculation
            calculatePrice();
        });
    </script>
</body>
</html>