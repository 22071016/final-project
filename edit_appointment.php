<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$id = $_GET['id'];
$item = $pdo->prepare("SELECT * FROM appointments WHERE id = ?")->execute([$id]) ? $pdo->query("SELECT * FROM appointments WHERE id = $id")->fetch() : null;

if ($_POST) {
    $stmt = $pdo->prepare("UPDATE appointments SET donor_id=?, appointment_date=?, location=?, notes=?, status=? WHERE id=?");
    $stmt->execute([$_POST['donor_id'], $_POST['appointment_date'], $_POST['location'], $_POST['notes'], $_POST['status'], $id]);
    header('Location: appointments.php'); exit;
}

$donors = $pdo->query("SELECT id, full_name FROM donors")->fetchAll();
?>

<h1>Sửa lịch hẹn</h1>
<form method="post">
    <label>Người hiến</label><select name="donor_id" required>
        <?php foreach($donors as $d): ?>
        <option value="<?= $d['id'] ?>" <?= $d['id']==$item['donor_id']?'selected':'' ?>><?= $d['full_name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>
    <label>Ngày giờ</label><input type="datetime-local" name="appointment_date" value="<?= str_replace(' ', 'T', $item['appointment_date']) ?>" required><br><br>
    <label>Địa điểm</label><input type="text" name="location" value="<?= $item['location'] ?>" required><br><br>
    <label>Ghi chú</label><textarea name="notes"><?= $item['notes'] ?></textarea><br><br>
    <label>Trạng thái</label>
    <select name="status">
        <option value="pending" <?= $item['status']=='pending'?'selected':'' ?>>Chờ xác nhận</option>
        <option value="confirmed" <?= $item['status']=='confirmed'?'selected':'' ?>>Đã xác nhận</option>
        <option value="cancelled" <?= $item['status']=='cancelled'?'selected':'' ?>>Hủy</option>
    </select><br><br>
    <button type="submit">Cập nhật</button>
</form>