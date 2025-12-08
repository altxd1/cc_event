<?php
require_once 'config.php';
require_admin();

// change status or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && !empty($_POST['event_id'])) {
        $event_id = (int)$_POST['event_id'];
        if ($_POST['action'] === 'approve') {
            $stmt = $mysqli->prepare("UPDATE events SET status='approved' WHERE event_id=?");
            $stmt->bind_param('i',$event_id); $stmt->execute(); $stmt->close();
        } elseif ($_POST['action'] === 'reject') {
            $stmt = $mysqli->prepare("UPDATE events SET status='rejected' WHERE event_id=?");
            $stmt->bind_param('i',$event_id); $stmt->execute(); $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $mysqli->prepare("DELETE FROM events WHERE event_id=?");
            $stmt->bind_param('i',$event_id); $stmt->execute(); $stmt->close();
        }
    }
    header('Location: manage_events.php');
    exit;
}

// filter
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter === 'pending') $where = "WHERE status='pending'";
elseif ($filter === 'approved') $where = "WHERE status='approved'";
elseif ($filter === 'rejected') $where = "WHERE status='rejected'";

$sql = "SELECT e.*, u.full_name, p.place_name, f.food_name, d.design_name FROM events e
JOIN users u ON e.user_id=u.user_id
JOIN event_places p ON e.place_id=p.place_id
JOIN food_items f ON e.food_id=f.food_id
JOIN event_designs d ON e.design_id=d.design_id
{$where} ORDER BY e.created_at DESC";
$rows = $mysqli->query($sql);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Events</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>Manage Events</h1><nav><a href="admin.php">Admin Home</a> | <a href="logout.php">Logout</a></nav></header>
<main class="container">
  <h2>Filter</h2>
  <p><a href="?filter=all">All</a> | <a href="?filter=pending">Pending</a> | <a href="?filter=approved">Approved</a> | <a href="?filter=rejected">Rejected</a></p>

  <table class="table">
    <thead><tr><th>ID</th><th>Customer</th><th>Event</th><th>Date</th><th>Guests</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while ($r = $rows->fetch_assoc()): ?>
        <tr>
          <td><?= e($r['event_id']) ?></td>
          <td><?= e($r['full_name']) ?></td>
          <td><?= e($r['event_name']) ?> (<?= e($r['place_name']) ?>)</td>
          <td><?= e($r['event_date']) ?> <?= e($r['event_time']) ?></td>
          <td><?= e($r['number_of_guests']) ?></td>
          <td>$<?= number_format($r['total_price'],2) ?></td>
          <td><?= e(ucfirst($r['status'])) ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="event_id" value="<?= e($r['event_id']) ?>">
              <?php if ($r['status'] === 'pending'): ?>
                <button name="action" value="approve">Approve</button>
                <button name="action" value="reject">Reject</button>
              <?php endif; ?>
              <button name="action" value="delete" onclick="return confirm('Delete this event?')">Delete</button>
            </form>
            <details>
              <summary>Details</summary>
              <p><strong>Food:</strong> <?= e($r['food_name']) ?> | <strong>Design:</strong> <?= e($r['design_name']) ?></p>
              <p><strong>Special Requests:</strong> <?= e($r['special_requests']) ?></p>
              <p><strong>Created:</strong> <?= e($r['created_at']) ?></p>
            </details>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>
</body>
</html>
