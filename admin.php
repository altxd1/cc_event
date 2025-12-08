<?php
require_once 'config.php';
require_admin();

// stats
$res = $mysqli->query("SELECT COUNT(*) as total_events, SUM(total_price) as revenue FROM events");
$stats = $res->fetch_assoc();
$pending = $mysqli->query("SELECT COUNT(*) as p FROM events WHERE status='pending'")->fetch_assoc();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin - Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
  <header><h1>Admin Panel</h1><nav><a href="manage_events.php">Manage Events</a> | <a href="manage_items.php">Manage Items</a> | <a href="logout.php">Logout</a></nav></header>
  <main class="container">
    <h2>Overview</h2>
    <div class="cards">
      <div class="card"><strong>Total Events</strong><div><?= e($stats['total_events'] ?? 0) ?></div></div>
      <div class="card"><strong>Pending</strong><div><?= e($pending['p'] ?? 0) ?></div></div>
      <div class="card"><strong>Revenue</strong><div>$<?= number_format($stats['revenue'] ?? 0,2) ?></div></div>
    </div>
  </main>
</body>
</html>
