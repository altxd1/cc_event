<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get user events
$user_id = $_SESSION['user_id'];
$events = [];

$sql = "SELECT e.*, p.place_name, f.food_name, d.design_name 
        FROM events e 
        JOIN event_places p ON e.place_id = p.place_id 
        JOIN food_items f ON e.food_id = f.food_id 
        JOIN event_designs d ON e.design_id = d.design_id 
        WHERE e.user_id = '$user_id' 
        ORDER BY e.created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - EventPro</title>
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
                    <li><a href="create_event.php">Create Event</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 1rem;">Welcome, <?php echo $_SESSION['full_name']; ?></span>
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
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="create_event.php"><i class="fas fa-calendar-plus"></i> Create New Event</a></li>
                </ul>
            </nav>
        </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <h2>My Events</h2>
                <p>Here you can view and manage all your event bookings.</p>
                
                <div class="text-right mb-2">
                    <a href="create_event.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Event
                    </a>
                </div>
                
                <?php if (empty($events)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You haven't created any events yet. 
                        <a href="create_event.php">Create your first event!</a>
                    </div>
                <?php else: ?>
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
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?php echo $event['event_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?><br>
                                            <?php echo date('h:i A', strtotime($event['event_time'])); ?></td>
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
                                            <button class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <!-- Quick Stats -->
                <div class="card-grid mt-3">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Total Events</h3>
                            <p style="font-size: 2rem; font-weight: bold; color: #6a11cb;">
                                <?php echo count($events); ?>
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Upcoming Events</h3>
                            <p style="font-size: 2rem; font-weight: bold; color: #28a745;">
                                <?php 
                                $upcoming = array_filter($events, function($event) {
                                    return strtotime($event['event_date']) >= time() && $event['status'] == 'approved';
                                });
                                echo count($upcoming);
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Pending Approval</h3>
                            <p style="font-size: 2rem; font-weight: bold; color: #ffc107;">
                                <?php 
                                $pending = array_filter($events, function($event) {
                                    return $event['status'] == 'pending';
                                });
                                echo count($pending);
                                ?>
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
</html>