
<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$stmt = $pdo->query("SELECT don.*, d.full_name, d.blood_type FROM donations don JOIN donors d ON don.donor_id = d.id ORDER BY don.donation_date DESC");
$list = $stmt->fetchAll();
?>
<header><h1>Quản lý Lần hiến máu</h1><a href="index.php">Dashboard</a></header>
<link rel="stylesheet" href="style.css">
<a href="add_donation.php" class="btn">+ Ghi nhận hiến máu mới</a>
<table>
    <tr><th>Mã hiến</th><th>Người hiến</th><th>Ngày</th><th>Thể tích</th><th>Nhóm máu</th><th>Hành động</th></tr>
    <?php foreach($list as $d): ?>
    <tr>
        <td><?= $d['donation_code'] ?></td>
        <td><?= $d['full_name'] ?></td>
        <td><?= date('d/m/Y', strtotime($d['donation_date'])) ?></td>
        <td><?= $d['volume_ml'] ?> ml</td>
        <td><?= $d['blood_type_collected'] ?></td>
        <td><a href="delete_donation.php?id=<?= $d['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a></td>
    </tr>
    <?php endforeach; ?>
</table>