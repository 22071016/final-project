<?php
session_start();
require_once 'config.php';

if (isset($_GET['read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['read'], $_SESSION['user_id']]);
    header('Location: notifications.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thông báo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Thông báo</h1><a href="index.php">Trở về</a></header>
    <ul>
        <?php if (empty($notifications)): ?>
            <li style="text-align:center">Chưa có thông báo nào</li>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <li style="background: <?= $notif['is_read'] ? '#f4f4f4' : '#ffdddd' ?>; padding:10px; margin:10px 0; border-radius:5px;">
                    <h3><?= htmlspecialchars($notif['title']) ?></h3>
                    <p><?= htmlspecialchars($notif['message']) ?></p>
                    <small><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                    <?php if (!$notif['is_read']): ?>
                        <a href="?read=<?= $notif['id'] ?>" style="color:red">Đánh dấu đã đọc</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</body>
</html>