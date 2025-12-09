<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = $_POST['donor_id'];
    $volume_ml = $_POST['volume_ml'];
    $notes = trim($_POST['notes']);

    $pdo->beginTransaction();

    // Health check
    $is_normal = (floatval($_POST['hemoglobin']) >= 12.5 && floatval($_POST['weight']) >= 45) ? 1 : 0;
    $hc_stmt = $pdo->prepare("INSERT INTO health_checks (donor_id, weight, blood_pressure, heart_rate, temperature, hemoglobin, is_normal, notes, staff_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $hc_stmt->execute([$donor_id, $_POST['weight'], $_POST['blood_pressure'], $_POST['heart_rate'], $_POST['temperature'], $_POST['hemoglobin'], $is_normal, $notes, $_SESSION['user_id']]);
    $health_check_id = $pdo->lastInsertId();

    // Donation
    $donation_code = generateCode('DON');
    $don_stmt = $pdo->prepare("INSERT INTO donations (donor_id, health_check_id, donation_code, donation_date, volume_ml, blood_type_collected, notes, staff_id, status) VALUES (?, ?, ?, NOW(), ?, (SELECT blood_type FROM donors WHERE id=?), ?, ?, 'completed')");
    $don_stmt->execute([$donor_id, $health_check_id, $donation_code, $volume_ml, $donor_id, $notes, $_SESSION['user_id']]);
    $donation_id = $pdo->lastInsertId();

    // Auto inventory
    $bag_code = generateCode('BAG');
    $expiry_date = date('Y-m-d', strtotime('+35 days'));
    $inv_stmt = $pdo->prepare("INSERT INTO blood_inventory (donation_id, blood_bag_code, blood_type, volume_ml, collection_date, expiry_date, storage_location, status) VALUES (?, ?, (SELECT blood_type FROM donors WHERE id=?), ?, CURDATE(), ?, 'Kho A', 'available')");
    $inv_stmt->execute([$donation_id, $bag_code, $donor_id, $volume_ml, $expiry_date]);

    // Update donor
    $pdo->prepare("UPDATE donors SET total_donations = total_donations + 1, last_donation_date = CURDATE() WHERE id = ?")->execute([$donor_id]);

    $pdo->commit();

    // Send notification
    $donor_user_id = $pdo->prepare("SELECT user_id FROM donors WHERE id = ?")->execute([$donor_id]) ? $pdo->fetchColumn() : null;
    if ($donor_user_id) {
        sendNotification($donor_user_id, "Hiến máu thành công", "Cảm ơn bạn đã hiến $volume_ml ml máu hôm nay! Mã donation: $donation_code");
    }
    sendNotification($_SESSION['user_id'], "Ghi nhận hiến máu", "Hiến máu từ người ID $donor_id đã ghi nhận.");

    header('Location: donations.php');
    exit;
}

$donors = $pdo->query("SELECT id, code, full_name, blood_type FROM donors")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Ghi nhận hiến máu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ghi nhận hiến máu mới</h1>
    <a href="donations.php">Quay lại</a>
    <form method="POST">
        <label>Người hién</label>
        <select name="donor_id" required>
            <option value="">-- Chọn --</option>
            <?php foreach($donors as $d): ?>
                <option value="<?= $d['id'] ?>">[<?= $d['code'] ?>] <?= htmlspecialchars($d['full_name']) ?> - <?= $d['blood_type'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Thể tích (ml)</label>
        <select name="volume_ml" required>
            <option value="250">250</option>
            <option value="350" selected>350</option>
            <option value="450">450</option>
        </select>

        <label>Cân nặng</label>
        <input type="number" name="weight" step="0.1" required>

        <label>Huyết áp</label>
        <input type="text" name="blood_pressure" required>

        <label>Nhịp tim</label>
        <input type="number" name="heart_rate" required>

        <label>Nhiệt độ</label>
        <input type="number" name="temperature" step="0.1" required>

        <label>Hemoglobin</label>
        <input type="number" name="hemoglobin" step="0.1" required>

        <label>Ghi chú</label>
        <textarea name="notes"></textarea>

        <button type="submit">Ghi nhận</button>
    </form>
</body>
</html>