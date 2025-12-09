<?php

// index.php - Dashboard
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['remember_user'])) {
        $userId = $_COOKIE['remember_user'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
        } else {
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
// === THÊM ĐOẠN NÀY: ĐẾM SỐ THÔNG BÁO CHƯA ĐỌC ===
$unread_count = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread_count->execute([$userId]);
$unread = $unread_count->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Dashboard - Quản lý Hiến máu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Quản lý Hiến máu</h1>
        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['name']); ?> (<?php echo $role; ?>)</span>
        <a href="logout.php">Đăng xuất</a>
    </header>
    
    <nav>
        <ul>
            <?php if (hasPermission($role, ['admin', 'staff'])): ?>
                <li><a href="donors.php">Quản lý Người hiến</a></li>
                <li><a href="appointments.php">Quản lý Lịch hẹn</a></li>
                <li><a href="donations.php">Quản lý Lần hiến</a></li>
                <li><a href="health_checks.php">Quản lý Kiểm tra sức khỏe</a></li>
                <li><a href="blood_inventory.php">Quản lý Tồn kho máu</a></li>
            <?php endif; ?>

            <li>
                <a href="notifications.php">
                    Thông báo 
                    <?php if ($unread > 0): ?>
                        <span style="background:#e74c3c; color:white; padding:3px 9px; border-radius:50%; font-size:12px; font-weight:bold;">
                            <?= $unread ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>

            <?php if ($role === 'donor'): ?>
                <li><a href="my_donations.php">Lịch sử hiến máu của tôi</a></li>
                <li><a href="my_appointments.php">Lịch hẹn của tôi</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2>Chào mừng đến với hệ thống!</h2>
        <!-- Thống kê nhanh -->
        <?php if (hasPermission($role, ['admin', 'staff'])): ?>
            <?php
            $totalDonors = $pdo->query("SELECT COUNT(*) FROM donors")->fetchColumn();
            $totalDonations = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
            ?>
            <p>Tổng người hiến: <?php echo $totalDonors; ?></p>
            <p>Tổng lần hiến: <?php echo $totalDonations; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>