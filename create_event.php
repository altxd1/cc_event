<?php
require_once 'config.php';
require_login();

$errors = [];
$places = $mysqli->query("SELECT * FROM event_places WHERE is_available=1");
$foods = $mysqli->query("SELECT * FROM food_items WHERE is_available=1");
$designs = $mysqli->query("SELECT * FROM event_designs WHERE is_available=1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $number_of_guests = (int)($_POST['number_of_guests'] ?? 0);
    $place_id = (int)($_POST['place_id'] ?? 0);
    $food_id = (int)($_POST['food_id'] ?? 0);
    $design_id = (int)($_POST['design_id'] ?? 0);
    $special_requests = trim($_POST['special_requests'] ?? '');

    if (!$event_name || !$event_date || !$event_time || !$number_of_guests || !$place_id || !$food_id || !$design_id) {
        $errors[] = 'Please fill all required fields.';
    }

    // Compute price (place fixed price + food price per person * guests + design price)
    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT price FROM event_places WHERE place_id = ? LIMIT 1");
        $stmt->bind_param('i', $place_id); $stmt->execute(); $res = $stmt->get_result(); $place = $res->fetch_assoc(); $stmt->close();
        $stmt = $mysqli->prepare("SELECT price_per_person FROM food_items WHERE food_id = ? LIMIT 1");
        $stmt->bind_param('i', $food_id); $stmt->execute(); $res = $stmt->get_result(); $food = $res->fetch_assoc(); $stmt->close();
        $stmt = $mysqli->prepare("SELECT price FROM event_designs WHERE design_id = ? LIMIT 1");
        $stmt->bind_param('i', $design_id); $stmt->execute(); $res = $stmt->get_result(); $design = $res->fetch_assoc(); $stmt->close();

        $place_price = $place['price'] ?? 0;
        $food_pp = $food['price_per_person'] ?? 0;
        $design_price = $design['price'] ?? 0;
        $total_price = $place_price + ($food_pp * $number_of_guests) + $design_price;

        $ins = $mysqli->prepare("INSERT INTO events (user_id, event_name, event_date, event_time, place_id, food_id, design_id, number_of_guests, special_requests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->bind_param('isssiiiisd', $_SESSION['user_id'], $event_name, $event_date, $event_time, $place_id, $food_id, $design_id, $number_of_guests, $special_requests, $total_price);
        if ($ins->execute()) {
            header('Location: dashboard.php?created=1');
            exit;
        } else {
            $errors[] = 'Failed to create event: ' . $mysqli->error;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Event</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header><h1>Create Event</h1><nav><a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a></nav></header>
<main class="container">
  <?php foreach ($errors as $err): ?><div class="error"><?= e($err) ?></div><?php endforeach; ?>

  <form method="post">
    <h3>Basic Info</h3>
    <label>Event Name<input name="event_name" value="<?= e($_POST['event_name'] ?? '') ?>" required></label>
    <label>Date<input type="date" name="event_date" value="<?= e($_POST['event_date'] ?? '') ?>" required></label>
    <label>Time<input type="time" name="event_time" value="<?= e($_POST['event_time'] ?? '') ?>" required></label>
    <label>Number of Guests<input type="number" name="number_of_guests" min="1" value="<?= e($_POST['number_of_guests'] ?? 50) ?>" required></label>

    <h3>Select Venue</h3>
    <?php while ($p = $places->fetch_assoc()): ?>
      <label class="card-radio">
        <input type="radio" name="place_id" value="<?= e($p['place_id']) ?>" <?= (isset($_POST['place_id']) && $_POST['place_id']==$p['place_id']) ? 'checked' : '' ?> required>
        <div><strong><?= e($p['place_name']) ?></strong> (Capacity: <?= e($p['capacity']) ?>) - $<?= number_format($p['price'],2) ?><br><?= e($p['description']) ?></div>
      </label>
    <?php endwhile; ?>

    <h3>Select Food</h3>
    <?php while ($f = $foods->fetch_assoc()): ?>
      <label class="card-radio">
        <input type="radio" name="food_id" value="<?= e($f['food_id']) ?>" <?= (isset($_POST['food_id']) && $_POST['food_id']==$f['food_id']) ? 'checked' : '' ?> required>
        <div><strong><?= e($f['food_name']) ?></strong> - $<?= number_format($f['price_per_person'],2) ?> per person<br><?= e($f['description']) ?></div>
      </label>
    <?php endwhile; ?>

    <h3>Select Design</h3>
    <?php while ($d = $designs->fetch_assoc()): ?>
      <label class="card-radio">
        <input type="radio" name="design_id" value="<?= e($d['design_id']) ?>" <?= (isset($_POST['design_id']) && $_POST['design_id']==$d['design_id']) ? 'checked' : '' ?> required>
        <div><strong><?= e($d['design_name']) ?></strong> - $<?= number_format($d['price'],2) ?><br><?= e($d['description']) ?></div>
      </label>
    <?php endwhile; ?>

    <label>Special Requests<textarea name="special_requests"><?= e($_POST['special_requests'] ?? '') ?></textarea></label>

    <button type="submit">Submit Event (will be pending)</button>
  </form>
</main>
</body>
</html>
