<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Notifications</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($notifications)): ?>
            <p style="text-align:center;">No notifications yet.</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="card" style="<?= $n['is_read'] ? '' : 'border-left: 5px solid #d9534f;' ?>">
                    <h3><?= htmlspecialchars($n['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                    <small>Received: <?= date('M d, Y H:i', strtotime($n['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>