<?php
// setup_admin.php
require_once 'config.php';

$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'admin123'; // change if desired
$admin_fullname = 'Site Admin';
$admin_phone = '';

$stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param('ss', $admin_username, $admin_email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "Admin user already exists. Delete existing admin or change credentials.";
    exit;
}
$stmt->close();

$hash = password_hash($admin_password, PASSWORD_DEFAULT);
$ins = $mysqli->prepare("INSERT INTO users (username, email, password, full_name, phone, user_type) VALUES (?, ?, ?, ?, ?, 'admin')");
$ins->bind_param('sssss', $admin_username, $admin_email, $hash, $admin_fullname, $admin_phone);
if ($ins->execute()) {
    echo "Admin created: username={$admin_username} password={$admin_password} — please delete this file after running.";
} else {
    echo "Error creating admin: " . $mysqli->error;
}
?>
