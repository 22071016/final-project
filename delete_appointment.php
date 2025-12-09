<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'] ?? '', ['admin'])) { exit; }
$id = $_GET['id'] ?? 0;
$pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$id]);
header('Location: appointments.php');