<?php
// File: add_health_check.php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) { header('Location: index.php'); exit; }

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO health_checks (donor_id, weight, blood_pressure, heart_rate, temperature, hemoglobin, is_normal, notes, staff_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $is_normal = ($_POST['hemoglobin'] >= 12.5 && $_POST['weight'] >= 45 && $_POST['heart_rate'] >= 50 && $_POST['heart_rate'] <= 100) ? 1 : 0;
    $stmt->execute([
        $_POST['donor_id'],
        $_POST['weight'],
        $_POST['blood_pressure'],
        $_POST['heart_rate'],
        $_POST['temperature'],
        $_POST['hemoglobin'],
        $is_normal,
        $_POST['notes'],
        $_SESSION['user_id']
    ]);
    header('Location: health_checks.php?success=1'); exit;
}

$donors = $pdo->query("SELECT id, code, full_name FROM donors WHERE is_active=1")->fetchAll();
?>

<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Nhập kiểm tra sức khỏe</title><link rel="stylesheet" href="style.css"></head><body>
<h1 style="color:#d9534f; text-align:center;">Nhập kết quả kiểm tra sức khỏe</h1>
<a href="health_checks.php">Quay lại danh sách</a><br><br>

<form method="post" style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:10px;">
    <label>Người hiến máu</label>
    <select name="donor_id" required>
        <?php foreach($donors as $d): ?>
        <option value="<?= $d['id'] ?>">[<?= $d['code'] ?>] <?= $d['full_name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Cân nặng (kg)</label><input type="number" step="0.1" name="weight" required><br><br>
    <label>Huyết áp</label><input type="text" name="blood_pressure" placeholder="VD: 120/80" required><br><br>
    <label>Nhịp tim (lần/phút)</label><input type="number" name="heart_rate" required><br><br>
    <label>Nhiệt độ (°C)</label><input type="number" step="0.1" name="temperature" required><br><br>
    <label>Hemoglobin (g/dL) ≥12.5</label><input type="number" step="0.1" name="hemoglobin" required><br><br>
    <label>Ghi chú</label><textarea name="notes"></textarea><br><br>

    <button type="submit" style="background:#d9534f; color:white; padding:12px 30px; border:none; border-radius:5px;">LƯU KẾT QUẢ</button>
</form>
</body></html>