<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $blood_type = $_POST['blood_type'];
    $phone_number = $_POST['phone_number'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO donors (code, name, blood_type, phone_number, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$code, $name, $blood_type, $phone_number, $status]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donor</title>
   <link rel="stylesheet" href="style.css">


</head>
<body>
    <div class="form-container">
        <h1>Add Donor</h1>
        <form action="add_donor.php" method="POST">
            <label for="code">Code</label>
            <input type="text" name="code" required>

            <label for="name">Name</label>
            <input type="text" name="name" required>

            <label for="blood_type">Blood Type</label>
            <input type="text" name="blood_type" required>

            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" required>

            <label for="status">Status</label>
            <input type="text" name="status" required>

            <button type="submit">Add Donor</button>
        </form>
        <a href="index.php" class="back">← Back to list</a>
    </div>
</body>
</html>

