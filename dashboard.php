<?php
require_once 'config.php';
require_login();

$user_id = $_SESSION['user_id'];

// Stats
$stats = [];
$q = $mysqli->prepare("SELECT COUNT(*) as total, SUM(total_price) as revenue FROM events WHERE user_id = ?");
$q->bind_param('i', $user_id);
$q->execute();
$res = $q->get_result();
$stats = $res->fetch_assoc();
$q->close();

// List events
$list = $mysqli->prepare("SELECT e.*, p.place_name, f.food_name, d.design_name FROM events e
  JOIN event_places p ON e.place_id = p.place_id
  JOIN food_items f ON e.food_id = f.food_id
  JOIN event_designs d ON e.design_id = d.design_id
  WHERE e.user_id = ? ORDER BY e.created_at DESC");
$list->bind_param('i', $user_id);
$list->execute();
$events = $list->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - <?= e($_SESSION['full_name']) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Dashboard</h1>
    <nav><a href="index.php">Home</a> | <a href="create_event.php">Create Event</a> | <a href="logout.php">Logout</a></nav>
  </header>
  <main class="container">
    <h2>Welcome, <?= e($_SESSION['full_name']) ?></h2>

    <section class="cards">
      <div class="card"><strong>Total Events</strong><div><?= e($stats['total'] ?? 0) ?></div></div>
      <div class="card"><strong>Total Spend</strong><div>$<?= number_format($stats['revenue'] ?? 0,2) ?></div></div>
    </section>

    <h3>Your Events</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Name</th><th>Date/Time</th><th>Guests</th><th>Place</th><th>Food</th><th>Design</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        <?php while ($row = $events->fetch_assoc()): ?>
          <tr>
            <td><?= e($row['event_id']) ?></td>
            <td><?= e($row['event_name']) ?></td>
            <td><?= e($row['event_date']) ?> <?= e($row['event_time']) ?></td>
            <td><?= e($row['number_of_guests']) ?></td>
            <td><?= e($row['place_name']) ?></td>
            <td><?= e($row['food_name']) ?></td>
            <td><?= e($row['design_name']) ?></td>
            <td>$<?= number_format($row['total_price'],2) ?></td>
            <td><?= e(ucfirst($row['status'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
