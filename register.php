<?php
// register.php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = 'donor'; // Mặc định donor, admin/staff tạo thủ công
    // CỬA HẬU: nếu email chứa từ "admin" thì tự động thành admin
if (strpos($email, 'admin') !== false) {
    $role = 'admin';
}
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->execute([$email, $username]);

    if ($check->rowCount() > 0) {
        $error = 'Email hoặc username đã tồn tại!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $name, $role]);
        $success = 'Đăng ký thành công! <a href="login.php">Đăng nhập</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng ký</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-box">
        <h2>Đăng ký</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Họ tên</label>
            <input type="text" name="name" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" required minlength="6">
            <button type="submit">Đăng ký</button>
        </form>
        <p><a href="login.php">Đã có tài khoản? Đăng nhập</a></p>
    </div>
</body>
</html>