<?php
// add_donor.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $blood_type = $_POST['blood_type'];
    $weight = $_POST['weight'];
    $code = generateCode('HN');

    // Kiểm tra trùng code/phone
    $check = $pdo->prepare("SELECT id FROM donors WHERE code = ? OR phone = ?");
    $check->execute([$code, $phone]);
    if ($check->rowCount() > 0) {
        $error = "Code hoặc số điện thoại đã tồn tại!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO donors (code, full_name, phone, email, date_of_birth, gender, address, blood_type, weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code, $full_name, $phone, $email, $date_of_birth, $gender, $address, $blood_type, $weight]);
        header('Location: donors.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm Người hiến</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Thêm Người hiến</h1><a href="donors.php">Trở về</a></header>
    <?php if ($error): ?><p style="color:red; text-align:center;"><?= $error ?></p><?php endif; ?>
    <form action="add_donor.php" method="POST">
        <label for="full_name">Họ tên</label>
        <input type="text" name="full_name" required>

        <label for="phone">Số điện thoại</label>
        <input type="text" name="phone" required>

        <label for="email">Email</label>
        <input type="email" name="email">

        <label for="date_of_birth">Ngày sinh</label>
        <input type="date" name="date_of_birth" required>

        <label for="gender">Giới tính</label>
        <select name="gender" required>
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
            <option value="other">Khác</option>
        </select>

        <label for="address">Địa chỉ</label>
        <textarea name="address"></textarea>

        <label for="blood_type">Nhóm máu</label>
        <select name="blood_type" required>
            <option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
        </select>

        <label for="weight">Cân nặng (kg)</label>
        <input type="number" name="weight" step="0.1">

        <button type="submit">Thêm</button>
    </form>
</body>
</html>