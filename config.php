<?php
// config.php - Kết nối DB và hàm tiện ích
$host = 'localhost';
$dbname = 'blood_donation';
$username = 'root';
$password = '040903'; // Thay bằng mật khẩu nếu có

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Hàm kiểm tra quyền
function hasPermission($role, $requiredRoles = ['admin']) {
    return in_array($role, $requiredRoles);
}

// Hàm tạo mã ngẫu nhiên (cho code donor/donation)
function generateCode($prefix, $length = 8) {
    return $prefix . '-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes($length/2)));
}
// Hàm gửi thông báo (gọi ở bất kỳ đâu)
function sendNotification($user_id, $title, $message, $type = 'info') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $message, $type]);
}

// Ví dụ: Gửi thông báo khi tạo lịch hẹn thành công
// sendNotification($donor_user_id, "Lịch hẹn mới", "Bạn có lịch hiến máu vào ngày 15/12/2025 lúc 8:00 tại BV Truyền máu", "appointment");
?>





