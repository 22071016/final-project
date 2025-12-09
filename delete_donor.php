<?php
// delete_donor.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin'])) { // Chỉ admin xóa
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM donors WHERE id = ?");
$stmt->execute([$id]);
header('Location: donors.php');
exit;
?>