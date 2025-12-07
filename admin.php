<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

// Get statistics
$total_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM events"))['count'];
$pending_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM events WHERE status = 'pending'"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE user_type = 'user'"))['count'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM events WHERE status = 'approved'"))['total'] ?? 0;

// Get recent events
$recent_events = [];
$sql = "SELECT e.*, u.full_name, u.email 
        FROM events e 
        JOIN users u ON e.user_id = u.user_id 
        ORDER BY e.created_at DESC 
        LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result) {
    $recent_events = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventPro</title>
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
                    <li><a href="manage_events.php">Manage Events</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 1rem;">Admin: <?php echo $_SESSION['full_name']; ?></span>
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
                        <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                        <li><a href="manage_items.php?type=food"><i class="fas fa-utensils"></i> Food Items</a></li>
                        <li><a href="manage_items.php?type=places"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                        <li><a href="manage_items.php?type=designs"><i class="fas fa-palette"></i> Event Designs</a></li>
                        <li><a href="#"><i class="fas fa-users"></i> User Management</a></li>
                        <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                        <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <h2>Admin Dashboard</h2>
                <p>Welcome to the EventPro administration panel</p>
                
                <!-- Statistics -->
                <div class="card-grid mt-3">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Total Events</h3>
                            <p style="font-size: 2.5rem; font-weight: bold; color: #6a11cb;">
                                <?php echo $total_events; ?>
                            </p>
                            <p>All events created</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Pending Events</h3>
                            <p style="font-size: 2.5rem; font-weight: bold; color: #ffc107;">
                                <?php echo $pending_events; ?>
                            </p>
                            <p>Awaiting approval</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Total Users</h3>
                            <p style="font-size: 2.5rem; font-weight: bold; color: #28a745;">
                                <?php echo $total_users; ?>
                            </p>
                            <p>Registered users</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Revenue</h3>
                            <p style="font-size: 2.5rem; font-weight: bold; color: #dc3545;">
                                $<?php echo number_format($revenue, 2); ?>
                            </p>
                            <p>Total from approved events</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Events -->
                <div class="mt-3">
                    <h3>Recent Events</h3>
                    <?php if (empty($recent_events)): ?>
                        <div class="alert alert-info">No events found.</div>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Guests</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_events as $event): ?>
                                        <tr>
                                            <td><?php echo $event['event_name']; ?></td>
                                            <td><?php echo $event['full_name']; ?><br>
                                                <small><?php echo $event['email']; ?></small></td>
                                            <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
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
                                                <a href="manage_events.php?action=view&id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-3">
                    <h3>Quick Actions</h3>
                    <div class="card-grid">
                        <div class="card">
                            <div class="card-content">
                                <h3 class="card-title">Approve Events</h3>
                                <p>Review and approve pending event requests</p>
                                <a href="manage_events.php?status=pending" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Go to Pending Events
                                </a>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-content">
                                <h3 class="card-title">Add New Food Item</h3>
                                <p>Add new menu options for events</p>
                                <a href="manage_items.php?type=food&action=add" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Food Item
                                </a>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-content">
                                <h3 class="card-title">Manage Venues</h3>
                                <p>Add or update event venues</p>
                                <a href="manage_items.php?type=places" class="btn btn-primary">
                                    <i class="fas fa-building"></i> Manage Venues
                                </a>
                            </div>
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