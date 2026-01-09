<?php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'] ?? '', ['admin'])) {
    exit('Access denied');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    exit('Invalid appointment ID');
}

// Optional: fetch appointment trước khi xóa để check tồn tại
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    exit('Appointment not found');
}

// Xóa appointment
$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

// Optional: thông báo
$_SESSION['message'] = "Appointment ID $id has been deleted.";

header('Location: appointments.php');
exit;
