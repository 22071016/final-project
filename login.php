<?php
session_start();
require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Lấy user theo email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Kiểm tra user
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['name'];
        setcookie("logged_user", $user['name'], time() + 3600, "/");
        header("Location: index.php");
        exit();
    } else {
        $error = "Incorrect email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-box">
    <h2>Login</h2>

    <?php if ($error): ?>
        <p style="color: red; text-align:center;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>

        <p class="swap-link">
            No account? <a href="register.php">Register here</a>
        </p>
    </form>
</div>

</body>
</html>
