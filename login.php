<?php
// login.php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($remember) {
            setcookie('remember_user', $user['id'], time() + 3600 * 24 * 30, '/');
        }

        header('Location: index.php');
        exit;
    } else {
        $error = 'Sai email hoặc mật khẩu!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-box">
        <h2>Đăng nhập</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
            <label><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
            <button type="submit">Đăng nhập</button>
        </form>
        <p><a href="register.php">Chưa có tài khoản? Đăng ký</a></p>
    </div>
</body>
</html>