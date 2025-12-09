<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = $_POST['donor_id'];
    $appointment_date = $_POST['appointment_date'];
    $location = trim($_POST['location']);
    $notes = trim($_POST['notes']);

    $stmt = $pdo->prepare("INSERT INTO appointments (donor_id, appointment_date, location, notes, status, created_by) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$donor_id, $appointment_date, $location, $notes, $_SESSION['user_id']]);

    // Send notification
    $donor_user_id = $pdo->prepare("SELECT user_id FROM donors WHERE id = ?")->execute([$donor_id]) ? $pdo->fetchColumn() : null;
    if ($donor_user_id) {
        sendNotification($donor_user_id, "Lịch hẹn mới", "Bạn có lịch hién máu vào " . date('d/m/Y H:i', strtotime($appointment_date)) . " tại $location");
    }
    sendNotification($_SESSION['user_id'], "Đặt lịch thành công", "Lịch hẹn cho người hiến ID $donor_id đã tạo.");

    header('Location: appointments.php');
    exit;
}

$donors = $pdo->query("SELECT id, code, full_name FROM donors ORDER BY full_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm lịch hẹn</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Thêm lịch hẹn mới</h1>
    <a href="appointments.php">Quay lại</a>
    <form method="POST">
        <label>Người hiến</label>
        <select name="donor_id" required>
            <option value="">-- Chọn --</option>
            <?php foreach($donors as $d): ?>
                <option value="<?= $d['id'] ?>">[<?= $d['code'] ?>] <?= htmlspecialchars($d['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Ngày giờ hẹn</label>
        <input type="datetime-local" name="appointment_date" required>

        <label>Địa điểm</label>
        <input type="text" name="location" required>

        <label>Ghi chú</label>
        <textarea name="notes"></textarea>

        <button type="submit">Tạo lịch</button>
    </form>
</body>
</html>