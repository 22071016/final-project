<?php
// File: health_checks.php (phiên bản đẹp + đầy đủ)
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$stmt = $pdo->query("SELECT hc.*, d.full_name, d.code FROM health_checks hc JOIN donors d ON hc.donor_id = d.id ORDER BY hc.check_date DESC");
$checks = $stmt->fetchAll();
?>

<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Kiểm tra sức khỏe</title><link rel="stylesheet" href="style.css"></head><body>
<header style="background:#d9534f; color:white; padding:15px; text-align:center; position:relative;">
    <h1>Kiểm tra sức khỏe trước hiến</h1>
    <a href="index.php" style="position:absolute; right:20px; top:20px; color:white;">Dashboard</a>
</header>

<div style="text-align:center; margin:20px;">
    <a href="add_health_check.php" style="background:#d9534f; color:white; padding:12px 25px; text-decoration:none; border-radius:5px; font-size:18px;">
        + Nhập kiểm tra sức khỏe mới
    </a>
</div>

<table style="width:95%; margin:auto; border-collapse:collapse;">
    <tr style="background:#f2dede;">
        <th>ID</th><th>Mã - Tên người hiến</th><th>Ngày kiểm tra</th><th>Cân nặng</th><th>Huyết áp</th><th>Nhịp tim</th><th>Hemoglobin</th><th>Hợp lệ?</th>
    </tr>
    <?php foreach($checks as $c): ?>
    <tr style="background:<?= $c['is_normal']?'#dff0d8':'#f2dede' ?>;">
        <td><?= $c['id'] ?></td>
        <td><strong>[<?= $c['code'] ?>]</strong> <?= htmlspecialchars($c['full_name']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($c['check_date'])) ?></td>
        <td><?= $c['weight'] ?> kg</td>
        <td><?= $c['blood_pressure'] ?></td>
        <td><?= $c['heart_rate'] ?></td>
        <td><?= $c['hemoglobin'] ?> g/dL</td>
        <td style="font-weight:bold; color:<?= $c['is_normal']?'green':'red' ?>;">
            <?= $c['is_normal'] ? 'HỢP LỆ' : 'KHÔNG ĐỦ ĐIỀU KIỆN' ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if(empty($checks)): ?>
<div style="text-align:center; padding:50px; color:#666;">
    Chưa có dữ liệu kiểm tra sức khỏe nào.<br>
    <a href="add_health_check.php">Nhập lần kiểm tra đầu tiên ngay!</a>
</div>
<?php endif; ?>
</body></html>