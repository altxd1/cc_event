<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

$type = $_GET['type'] ?? 'food'; // food, places, designs
$action = $_GET['action'] ?? 'list'; // list, add, edit, delete
$item_id = $_GET['id'] ?? 0;

$message = '';
$table_name = '';
$title = '';

switch ($type) {
    case 'places':
        $table_name = 'event_places';
        $title = 'Event Places';
        break;
    case 'designs':
        $table_name = 'event_designs';
        $title = 'Event Designs';
        break;
    default:
        $table_name = 'food_items';
        $title = 'Food Items';
        break;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    if ($type == 'food') {
        $price = floatval($_POST['price_per_person']);
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        if ($action == 'add') {
            $sql = "INSERT INTO $table_name (food_name, description, price_per_person, is_available) 
                    VALUES ('$name', '$description', '$price', '$is_available')";
        } else {
            $sql = "UPDATE $table_name SET 
                    food_name = '$name', 
                    description = '$description', 
                    price_per_person = '$price', 
                    is_available = '$is_available' 
                    WHERE food_id = '$item_id'";
        }
    } elseif ($type == 'places') {
        $price = floatval($_POST['price']);
        $capacity = intval($_POST['capacity']);
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        if ($action == 'add') {
            $sql = "INSERT INTO $table_name (place_name, description, capacity, price, is_available) 
                    VALUES ('$name', '$description', '$capacity', '$price', '$is_available')";
        } else {
            $sql = "UPDATE $table_name SET 
                    place_name = '$name', 
                    description = '$description', 
                    capacity = '$capacity', 
                    price = '$price', 
                    is_available = '$is_available' 
                    WHERE place_id = '$item_id'";
        }
    } else { // designs
        $price = floatval($_POST['price']);
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        if ($action == 'add') {
            $sql = "INSERT INTO $table_name (design_name, description, price, is_available) 
                    VALUES ('$name', '$description', '$price', '$is_available')";
        } else {
            $sql = "UPDATE $table_name SET 
                    design_name = '$name', 
                    description = '$description', 
                    price = '$price', 
                    is_available = '$is_available' 
                    WHERE design_id = '$item_id'";
        }
    }
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Item ' . ($action == 'add' ? 'added' : 'updated') . ' successfully!';
        $action = 'list';
    } else {
        $message = 'Error: ' . mysqli_error($conn);
    }
}

// Handle delete
if ($action == 'delete' && $item_id) {
    $sql = "DELETE FROM $table_name WHERE " . ($type == 'food' ? 'food_id' : ($type == 'places' ? 'place_id' : 'design_id')) . " = '$item_id'";
    if (mysqli_query($conn, $sql)) {
        $message = 'Item deleted successfully!';
    }
}

// Get items for listing
$items = [];
$sql = "SELECT * FROM $table_name ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get item for editing
$edit_item = null;
if ($action == 'edit' && $item_id) {
    $id_field = $type == 'food' ? 'food_id' : ($type == 'places' ? 'place_id' : 'design_id');
    $sql = "SELECT * FROM $table_name WHERE $id_field = '$item_id'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_item = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo $title; ?> - EventPro Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add this in the <head> section after your CSS links -->
<style>
    /* Emergency override for this page */
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
    
    .btn-success {
        background-color: #28a745 !important;
        color: white !important;
        border-color: #28a745 !important;
    }
</style>
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
                    <li><a href="manage_events.php">Manage Events</a></li>
                    <li><a href="manage_items.php?type=<?php echo $type; ?>" class="active">Manage Items</a></li>
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
                        <li><a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                        <li><a href="manage_items.php?type=food" class="<?php echo $type == 'food' ? 'active' : ''; ?>">
                            <i class="fas fa-utensils"></i> Food Items</a></li>
                        <li><a href="manage_items.php?type=places" class="<?php echo $type == 'places' ? 'active' : ''; ?>">
                            <i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                        <li><a href="manage_items.php?type=designs" class="<?php echo $type == 'designs' ? 'active' : ''; ?>">
                            <i class="fas fa-palette"></i> Event Designs</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <h2>Manage <?php echo $title; ?></h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <!-- Type Tabs -->
               <!-- FIXED TAB NAVIGATION - ALL BUTTONS VISIBLE -->
<div class="tab-container">
    <a href="?type=food" class="tab-button <?php echo $type == 'food' ? 'tab-active' : 'tab-inactive'; ?>">
        <i class="fas fa-utensils"></i> Food Items
    </a>
    
    <a href="?type=places" class="tab-button <?php echo $type == 'places' ? 'tab-active' : 'tab-inactive'; ?>">
        <i class="fas fa-map-marker-alt"></i> Event Places
    </a>
    
    <a href="?type=designs" class="tab-button <?php echo $type == 'designs' ? 'tab-active' : 'tab-inactive'; ?>">
        <i class="fas fa-palette"></i> Event Designs
    </a>
    
    <a href="?type=<?php echo $type; ?>&action=add" class="btn btn-success" style="float: right;">
        <i class="fas fa-plus"></i> Add New
    </a>
    
    <div style="clear: both;"></div>
</div>
                
                <?php if ($action == 'add' || $action == 'edit'): ?>
                    <!-- Add/Edit Form -->
                    <form method="POST" action="" class="form-container">
                        <h3><?php echo $action == 'add' ? 'Add New' : 'Edit'; ?> <?php echo $title; ?></h3>
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo $edit_item ? $edit_item[$type . '_name'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4"><?php 
                                echo $edit_item ? $edit_item['description'] : ''; 
                            ?></textarea>
                        </div>
                        
                        <?php if ($type == 'food'): ?>
                            <div class="form-group">
                                <label for="price_per_person">Price per Person ($) *</label>
                                <input type="number" id="price_per_person" name="price_per_person" class="form-control" 
                                       value="<?php echo $edit_item ? $edit_item['price_per_person'] : ''; ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        <?php elseif ($type == 'places'): ?>
                            <div class="form-group">
                                <label for="capacity">Capacity *</label>
                                <input type="number" id="capacity" name="capacity" class="form-control" 
                                       value="<?php echo $edit_item ? $edit_item['capacity'] : ''; ?>" 
                                       min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price ($) *</label>
                                <input type="number" id="price" name="price" class="form-control" 
                                       value="<?php echo $edit_item ? $edit_item['price'] : ''; ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label for="price">Price ($) *</label>
                                <input type="number" id="price" name="price" class="form-control" 
                                       value="<?php echo $edit_item ? $edit_item['price'] : ''; ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_available" value="1" 
                                    <?php echo ($edit_item && $edit_item['is_available']) || !$edit_item ? 'checked' : ''; ?>>
                                Available for selection
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'Add Item' : 'Update Item'; ?>
                            </button>
                            <a href="?type=<?php echo $type; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Items List -->
                    <?php if (empty($items)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No items found. 
                            <a href="?type=<?php echo $type; ?>&action=add">Add your first item!</a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <?php if ($type == 'places'): ?>
                                            <th>Capacity</th>
                                        <?php endif; ?>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): 
                                        $id_field = $type == 'food' ? 'food_id' : ($type == 'places' ? 'place_id' : 'design_id');
                                        $name_field = $type . '_name';
                                        $price_field = $type == 'food' ? 'price_per_person' : 'price';
                                    ?>
                                        <tr>
                                            <td>#<?php echo $item[$id_field]; ?></td>
                                            <td>
                                                <strong><?php echo $item[$name_field]; ?></strong><br>
                                                <small><?php echo substr($item['description'], 0, 50); ?>...</small>
                                            </td>
                                            <?php if ($type == 'places'): ?>
                                                <td><?php echo $item['capacity']; ?> guests</td>
                                            <?php endif; ?>
                                            <td>
                                                $<?php echo number_format($item[$price_field], 2); ?>
                                                <?php if ($type == 'food'): ?><br><small>per person</small><?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="
                                                    background-color: <?php echo $item['is_available'] ? '#28a745' : '#dc3545'; ?>;
                                                    color: white;
                                                    padding: 0.25rem 0.5rem;
                                                    border-radius: 20px;
                                                    font-size: 0.85rem;
                                                ">
                                                    <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                            <td>
                                                <div style="display: flex; gap: 0.25rem;">
                                                    <a href="?type=<?php echo $type; ?>&action=edit&id=<?php echo $item[$id_field]; ?>" 
                                                       class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?type=<?php echo $type; ?>&action=delete&id=<?php echo $item[$id_field]; ?>" 
                                                       class="btn btn-danger" style="padding: 0.25rem 0.5rem;"
                                                       onclick="return confirm('Delete this item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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