<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$action = $_GET['action'] ?? '';
$event_id = $_GET['id'] ?? 0;
$status_filter = $_GET['status'] ?? 'all';

// Handle actions
if ($action == 'approve' && $event_id) {
    $sql = "UPDATE events SET status = 'approved' WHERE event_id = '$event_id'";
    if (mysqli_query($conn, $sql)) {
        $message = 'Event approved successfully!';
    }
} elseif ($action == 'reject' && $event_id) {
    $sql = "UPDATE events SET status = 'rejected' WHERE event_id = '$event_id'";
    if (mysqli_query($conn, $sql)) {
        $message = 'Event rejected!';
    }
} elseif ($action == 'delete' && $event_id) {
    $sql = "DELETE FROM events WHERE event_id = '$event_id'";
    if (mysqli_query($conn, $sql)) {
        $message = 'Event deleted successfully!';
    }
}

// Build query based on filter
$sql = "SELECT e.*, u.full_name, u.email, u.phone, 
               p.place_name, f.food_name, d.design_name 
        FROM events e 
        JOIN users u ON e.user_id = u.user_id 
        JOIN event_places p ON e.place_id = p.place_id 
        JOIN food_items f ON e.food_id = f.food_id 
        JOIN event_designs d ON e.design_id = d.design_id";
        
if ($status_filter != 'all') {
    $sql .= " WHERE e.status = '$status_filter'";
}

$sql .= " ORDER BY e.created_at DESC";
$result = mysqli_query($conn, $sql);
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - EventPro Admin</title>
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
                    <li><a href="admin.php">Dashboard</a></li>
                    <li><a href="manage_events.php" class="active">Manage Events</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 1rem;">Admin Panel</span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="manage_events.php" class="active"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                        <li><a href="manage_items.php?type=food"><i class="fas fa-utensils"></i> Food Items</a></li>
                        <li><a href="manage_items.php?type=places"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                        <li><a href="manage_items.php?type=designs"><i class="fas fa-palette"></i> Event Designs</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <h2>Manage Events</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <!-- Filter Tabs -->
                <div style="margin-bottom: 2rem;">
                    <a href="?status=all" class="btn <?php echo $status_filter == 'all' ? 'btn-primary' : 'btn-secondary'; ?>">
                        All Events (<?php echo count($events); ?>)
                    </a>
                    <a href="?status=pending" class="btn <?php echo $status_filter == 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Pending (<?php echo count(array_filter($events, fn($e) => $e['status'] == 'pending')); ?>)
                    </a>
                    <a href="?status=approved" class="btn <?php echo $status_filter == 'approved' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Approved (<?php echo count(array_filter($events, fn($e) => $e['status'] == 'approved')); ?>)
                    </a>
                    <a href="?status=rejected" class="btn <?php echo $status_filter == 'rejected' ? 'btn-primary' : 'btn-secondary'; ?>">
                        Rejected (<?php echo count(array_filter($events, fn($e) => $e['status'] == 'rejected')); ?>)
                    </a>
                </div>
                
                <?php if (empty($events)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No events found for the selected filter.
                    </div>
                <?php else: ?>
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
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($event['event_id'], 5, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo $event['event_name']; ?></td>
                                        <td>
                                            <strong><?php echo $event['full_name']; ?></strong><br>
                                            <small><?php echo $event['email']; ?></small><br>
                                            <small><?php echo $event['phone']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($event['event_date'])); ?><br>
                                            <small><?php echo date('h:i A', strtotime($event['event_time'])); ?></small>
                                        </td>
                                        <td><?php echo $event['place_name']; ?></td>
                                        <td><?php echo $event['number_of_guests']; ?></td>
                                        <td>$<?php echo number_format($event['total_price'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $status_colors = [
                                                'pending' => '#ffc107',
                                                'approved' => '#28a745',
                                                'rejected' => '#dc3545',
                                                'completed' => '#007bff'
                                            ];
                                            ?>
                                            <span style="
                                                background-color: <?php echo $status_colors[$event['status']]; ?>;
                                                color: white;
                                                padding: 0.25rem 0.5rem;
                                                border-radius: 20px;
                                                font-size: 0.85rem;
                                                font-weight: bold;
                                            ">
                                                <?php echo ucfirst($event['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                                <a href="?action=view&id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if ($event['status'] == 'pending'): ?>
                                                    <a href="?action=approve&id=<?php echo $event['event_id']; ?>" 
                                                       class="btn btn-success" style="padding: 0.25rem 0.5rem;"
                                                       onclick="return confirm('Approve this event?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="?action=reject&id=<?php echo $event['event_id']; ?>" 
                                                       class="btn btn-danger" style="padding: 0.25rem 0.5rem;"
                                                       onclick="return confirm('Reject this event?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="?action=delete&id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-danger" style="padding: 0.25rem 0.5rem;"
                                                   onclick="return confirm('Delete this event permanently?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Event Details Modal (would be implemented with JavaScript) -->
                    <?php if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])): 
                        $view_id = $_GET['id'];
                        $view_sql = "SELECT e.*, u.*, p.*, f.*, d.* 
                                   FROM events e 
                                   JOIN users u ON e.user_id = u.user_id 
                                   JOIN event_places p ON e.place_id = p.place_id 
                                   JOIN food_items f ON e.food_id = f.food_id 
                                   JOIN event_designs d ON e.design_id = d.design_id 
                                   WHERE e.event_id = '$view_id'";
                        $view_result = mysqli_query($conn, $view_sql);
                        $event_details = mysqli_fetch_assoc($view_result);
                    ?>
                        <div class="modal" style="
                            position: fixed;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            background: rgba(0,0,0,0.5);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            z-index: 1000;
                        ">
                            <div style="
                                background: white;
                                padding: 2rem;
                                border-radius: 10px;
                                max-width: 800px;
                                max-height: 90vh;
                                overflow-y: auto;
                            ">
                                <h3>Event Details - <?php echo $event_details['event_name']; ?></h3>
                                
                                <div class="event-form-grid">
                                    <div class="form-section">
                                        <h4>Customer Information</h4>
                                        <p><strong>Name:</strong> <?php echo $event_details['full_name']; ?></p>
                                        <p><strong>Email:</strong> <?php echo $event_details['email']; ?></p>
                                        <p><strong>Phone:</strong> <?php echo $event_details['phone']; ?></p>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h4>Event Details</h4>
                                        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event_details['event_date'])); ?></p>
                                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($event_details['event_time'])); ?></p>
                                        <p><strong>Guests:</strong> <?php echo $event_details['number_of_guests']; ?></p>
                                        <p><strong>Status:</strong> <?php echo ucfirst($event_details['status']); ?></p>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h4>Venue</h4>
                                        <p><strong>Place:</strong> <?php echo $event_details['place_name']; ?></p>
                                        <p><strong>Capacity:</strong> <?php echo $event_details['capacity']; ?> guests</p>
                                        <p><strong>Price:</strong> $<?php echo number_format($event_details['price'], 2); ?></p>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h4>Food & Design</h4>
                                        <p><strong>Menu:</strong> <?php echo $event_details['food_name']; ?></p>
                                        <p><strong>Design:</strong> <?php echo $event_details['design_name']; ?></p>
                                        <p><strong>Special Requests:</strong> <?php echo $event_details['special_requests'] ?: 'None'; ?></p>
                                    </div>
                                </div>
                                
                                <div class="form-section" style="background-color: #e3f2fd;">
                                    <h4>Pricing Breakdown</h4>
                                    <p>Venue: $<?php echo number_format($event_details['price'], 2); ?></p>
                                    <p>Food (<?php echo $event_details['number_of_guests']; ?> guests × $<?php echo number_format($event_details['price_per_person'], 2); ?>): 
                                       $<?php echo number_format($event_details['price_per_person'] * $event_details['number_of_guests'], 2); ?></p>
                                    <p>Design: $<?php echo number_format($event_details['price'], 2); ?></p>
                                    <hr>
                                    <p><strong>Total: $<?php echo number_format($event_details['total_price'], 2); ?></strong></p>
                                </div>
                                
                                <div class="text-center mt-2">
                                    <a href="manage_events.php" class="btn btn-secondary">Close</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
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
</html>