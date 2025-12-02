<?php
require_once 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Kiểm tra email trùng
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        $error = "Email already exists!";
    } else {
        // Thêm user
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $email, $password]);

        $success = "Registration successful! You can now log in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-box">
    <h2>Register</h2>

    <?php if ($error): ?>
        <p style="color: red; text-align:center;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <div class="input-group">
            <label>Name</label>
            <input type="text" name="name" required autocomplete="off" value="">
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required autocomplete="off" value="">
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required autocomplete="new-password" value="">
        </div>

        <button type="submit">Register</button>

        <p class="swap-link">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </form>
</div>
