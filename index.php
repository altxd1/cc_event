<?php require_once 'config.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Event Management System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Event Management Co.</h1>
    <nav>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a>
        <?php if ($_SESSION['user_type'] === 'admin'): ?> | <a href="admin.php">Admin</a><?php endif; ?>
      <?php else: ?>
        <a href="login.php">Login</a> | <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <section>
      <h2>Our Services</h2>
      <p>We provide venue booking, catering, and design planning for events big and small.</p>
      <ul>
        <li>Venues: ballrooms, gardens, halls</li>
        <li>Food menus: buffet, plated, custom</li>
        <li>Event designs: floral, modern, themed</li>
      </ul>
      <p><a href="register.php">Create an account</a> to book your event today.</p>
    </section>
  </main>
</body>
</html>
