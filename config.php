<?php
$host = 'localhost';
$dbname = 'blood_donation';
$username = 'root';      // Tên người dùng MySQL
$password = '12345678';          // Laragon mặc định không có mật khẩu

try {
    // Kết nối đến MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Kết nối thành công!";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>
