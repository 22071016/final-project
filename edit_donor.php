<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch();

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

    $check = $pdo->prepare("SELECT id FROM donors WHERE (phone = ? OR email = ?) AND id != ?");
    $check->execute([$phone, $email, $id]);
    if ($check->rowCount() > 0) {
        $error = "Số điện thoại hoặc email đã tồn tại!";
    } else {
        $stmt = $pdo->prepare("UPDATE donors SET full_name=?, phone=?, email=?, date_of_birth=?, gender=?, address=?, blood_type=?, weight=? WHERE id=?");
        $stmt->execute([$full_name, $phone, $email, $date_of_birth, $gender, $address, $blood_type, $weight, $id]);
        header('Location: donors.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Sửa Người hién</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Sửa thông tin người hién</h1>
    <a href="donors.php">Quay lại</a>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        <label>Họ tên</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($donor['full_name']) ?>" required>

        <label>Số điện thoại</label>
        <input type="text" name="phone" value="<?= $donor['phone'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $donor['email'] ?>">

        <label>Ngày sinh</label>
        <input type="date" name="date_of_birth" value="<?= $donor['date_of_birth'] ?>" required>

        <label>Giới tính</label>
        <select name="gender" required>
            <option value="male" <?= $donor['gender'] == 'male' ? 'selected' : '' ?>>Nam</option>
            <option value="female" <?= $donor['gender'] == 'female' ? 'selected' : '' ?>>Nữ</option>
            <option value="other" <?= $donor['gender'] == 'other' ? 'selected' : '' ?>>Khác</option>
        </select>

        <label>Địa chỉ</label>
        <textarea name="address"><?= htmlspecialchars($donor['address']) ?></textarea>

        <label>Nhóm máu</label>
        <select name="blood_type" required>
            <option value="A+" <?= $donor['blood_type'] == 'A+' ? 'selected' : '' ?>>A+</option>
            <!-- Thêm các option khác tương tự -->
        </select>

        <label>Cân nặng (kg)</label>
        <input type="number" name="weight" value="<?= $donor['weight'] ?>" step="0.1">

        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>