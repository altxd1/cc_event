<?php
require_once 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (!$full_name || !$username || !$email || !$password) {
        $errors[] = 'Please fill required fields.';
    }
    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    }

    if (empty($errors)) {
        // check unique
        $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param('sssss', $username, $email, $hash, $full_name, $phone);
            if ($ins->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Registration failed: ' . $mysqli->error;
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - Event Management</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Register</h1>
    <?php foreach ($errors as $err): ?>
      <div class="error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <form method="post" novalidate>
      <label>Full name<input name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>"></label>
      <label>Username<input name="username" required value="<?= e($_POST['username'] ?? '') ?>"></label>
      <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
      <label>Phone<input name="phone" value="<?= e($_POST['phone'] ?? '') ?>"></label>
      <label>Password<input type="password" name="password" required></label>
      <label>Confirm Password<input type="password" name="password2" required></label>
      <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
