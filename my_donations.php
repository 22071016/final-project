<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'donor') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT d.* FROM donations d JOIN donors dn ON d.donor_id = dn.id WHERE dn.user_id = ? ORDER BY d.donation_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Lịch sử hiến máu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Lịch sử hién máu của tôi</h1><a href="index.php">Trở về</a></header>
    <table>
        <thead>
            <tr><th>Mã</th><th>Ngày hién</th><th>Thể tích</th><th>Nhóm máu</th><th>Trạng thái</th></tr>
        </thead>
        <tbody>
            <?php if (empty($donations)): ?>
                <tr><td colspan="5" style="text-align:center">Chưa có lịch sử hién máu</td></tr>
            <?php else: ?>
                <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['donation_code']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['donation_date'])) ?></td>
                        <td><?= $d['volume_ml'] ?> ml</td>
                        <td><?= $d['blood_type_collected'] ?></td>
                        <td><?= ucfirst($d['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>