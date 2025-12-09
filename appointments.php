<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$stmt = $pdo->query("SELECT a.*, d.full_name, d.phone FROM appointments a JOIN donors d ON a.donor_id = d.id ORDER BY a.appointment_date DESC");
$list = $stmt->fetchAll();
?>

<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Quản lý Lịch hẹn</title><link rel="stylesheet" href="style.css"></head><body>
<header><h1>Quản lý Lịch hẹn</h1><a href="index.php">Dashboard</a></header>
<a href="add_appointment.php" class="btn">+ Thêm lịch hẹn mới</a>
<table>
    <tr><th>ID</th><th>Người hiến</th><th>SĐT</th><th>Ngày hẹn</th><th>Địa điểm</th><th>Trạng thái</th><th>Hành động</th></tr>
    <?php foreach($list as $a): ?>
    <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['full_name']) ?></td>
        <td><?= $a['phone'] ?></td>
        <td><?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?></td>
        <td><?= htmlspecialchars($a['location']) ?></td>
        <td><span style="color:<?= $a['status']=='confirmed'?'green':($a['status']=='cancelled'?'red':'orange') ?>"><?= ucfirst($a['status']) ?></span></td>
        <td>
            <a href="edit_appointment.php?id=<?= $a['id'] ?>">Sửa</a> |
            <a href="delete_appointment.php?id=<?= $a['id'] ?>" onclick="return confirm('Xóa thật?')">Xóa</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body></html>