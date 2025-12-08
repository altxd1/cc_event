<?php
require_once 'config.php';
require_admin();
$tab = $_GET['tab'] ?? 'food';
$msg = '';

// Add item actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    if ($type === 'food_add') {
        $name = $_POST['name']; $desc = $_POST['desc']; $price = (float)$_POST['price'];
        $stmt = $mysqli->prepare("INSERT INTO food_items (food_name, description, price_per_person) VALUES (?, ?, ?)");
        $stmt->bind_param('ssd', $name, $desc, $price); $stmt->execute(); $msg = 'Food added';
    } elseif ($type === 'place_add') {
        $name = $_POST['name']; $desc = $_POST['desc']; $capacity = (int)$_POST['capacity']; $price = (float)$_POST['price']; $img = $_POST['image_url'];
        $stmt = $mysqli->prepare("INSERT INTO event_places (place_name, description, capacity, price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssids', $name, $desc, $capacity, $price, $img); $stmt->execute(); $msg = 'Place added';
    } elseif ($type === 'design_add') {
        $name = $_POST['name']; $desc = $_POST['desc']; $price = (float)$_POST['price']; $img = $_POST['image_url'];
        $stmt = $mysqli->prepare("INSERT INTO event_designs (design_name, description, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssds', $name, $desc, $price, $img); $stmt->execute(); $msg = 'Design added';
    }
}

// fetch lists
$foods = $mysqli->query("SELECT * FROM food_items");
$places = $mysqli->query("SELECT * FROM event_places");
$designs = $mysqli->query("SELECT * FROM event_designs");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Items</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>Manage Items</h1><nav><a href="admin.php">Admin Home</a> | <a href="logout.php">Logout</a></nav></header>
<main class="container">
  <?php if ($msg): ?><div class="success"><?= e($msg) ?></div><?php endif; ?>
  <nav class="tabs">
    <a href="?tab=food" class="<?= $tab=='food' ? 'active' : '' ?>">Food Items</a>
    <a href="?tab=places" class="<?= $tab=='places' ? 'active' : '' ?>">Event Places</a>
    <a href="?tab=designs" class="<?= $tab=='designs' ? 'active' : '' ?>">Designs</a>
  </nav>

  <?php if ($tab === 'food'): ?>
    <h2>Food Items</h2>
    <form method="post">
      <input type="hidden" name="type" value="food_add">
      <label>Name<input name="name" required></label>
      <label>Description<textarea name="desc"></textarea></label>
      <label>Price per person<input name="price" type="number" step="0.01" required></label>
      <button type="submit">Add Food</button>
    </form>
    <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Available</th></tr></thead><tbody>
      <?php while ($r=$foods->fetch_assoc()): ?>
      <tr><td><?=e($r['food_id'])?></td><td><?=e($r['food_name'])?></td><td>$<?=number_format($r['price_per_person'],2)?></td><td><?=e($r['is_available'])?></td></tr>
      <?php endwhile; ?>
    </tbody></table>

  <?php elseif ($tab === 'places'): ?>
    <h2>Event Places</h2>
    <form method="post">
      <input type="hidden" name="type" value="place_add">
      <label>Name<input name="name" required></label>
      <label>Description<textarea name="desc"></textarea></label>
      <label>Capacity<input name="capacity" type="number" required></label>
      <label>Price<input name="price" type="number" step="0.01" required></label>
      <label>Image URL<input name="image_url"></label>
      <button type="submit">Add Place</button>
    </form>
    <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Capacity</th><th>Price</th></tr></thead><tbody>
      <?php while ($r=$places->fetch_assoc()): ?>
      <tr><td><?=e($r['place_id'])?></td><td><?=e($r['place_name'])?></td><td><?=e($r['capacity'])?></td><td>$<?=number_format($r['price'],2)?></td></tr>
      <?php endwhile; ?>
    </tbody></table>

  <?php else: ?>
    <h2>Designs</h2>
    <form method="post">
      <input type="hidden" name="type" value="design_add">
      <label>Name<input name="name" required></label>
      <label>Description<textarea name="desc"></textarea></label>
      <label>Price<input name="price" type="number" step="0.01" required></label>
      <label>Image URL<input name="image_url"></label>
      <button type="submit">Add Design</button>
    </form>
    <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Price</th></tr></thead><tbody>
      <?php while ($r=$designs->fetch_assoc()): ?>
      <tr><td><?=e($r['design_id'])?></td><td><?=e($r['design_name'])?></td><td>$<?=number_format($r['price'],2)?></td></tr>
      <?php endwhile; ?>
    </tbody></table>
  <?php endif; ?>
</main>
</body>
</html>
