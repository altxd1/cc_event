<?php
require_once 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$user || !$password) {
        $errors[] = 'Enter username/email and password.';
    } else {
        $stmt = $mysqli->prepare("SELECT user_id, username, email, password, full_name, user_type FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param('ss', $user, $user);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                // create session
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['user_type'] = $row['user_type'];
                if ($row['user_type'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $errors[] = 'Invalid credentials.';
            }
        } else {
            $errors[] = 'User not found.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Event Management</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Login</h1>
    <?php if (!empty($_GET['registered'])): ?>
      <div class="success">Registration successful — please login.</div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <div class="error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <form method="post">
      <label>Username or Email<input name="user" required value="<?= e($_POST['user'] ?? '') ?>"></label>
      <label>Password<input type="password" name="password" required></label>
      <button type="submit">Login</button>
    </form>
    <p>No account? <a href="register.php">Register</a></p>
  </div>
</body>
</html>
