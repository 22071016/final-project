<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $code = $_POST['code'];
    $name = $_POST['name'];
    $blood_type = $_POST['blood_type'];
    $phone_number = $_POST['phone_number'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE donors SET code = ?, name = ?, blood_type = ?, phone_number = ?, status = ? WHERE id = ?");
    $stmt->execute([$code, $name, $blood_type, $phone_number, $status, $id]);

    header("Location: index.php");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donor</title>
</head>
 <link rel="stylesheet" href="style.css">

<body>
    <h1>Edit Donor</h1>
    <form action="edit_donor.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $donor['id']; ?>">

        <label for="code">Code</label>
        <input type="text" name="code" value="<?php echo $donor['code']; ?>" required><br>

        <label for="name">Name</label>
        <input type="text" name="name" value="<?php echo $donor['name']; ?>" required><br>

        <label for="blood_type">Blood Type</label>
        <input type="text" name="blood_type" value="<?php echo $donor['blood_type']; ?>" required><br>

        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" value="<?php echo $donor['phone_number']; ?>" required><br>

        <label for="status">Status</label>
        <input type="text" name="status" value="<?php echo $donor['status']; ?>" required><br>

        <button type="submit">Update Donor</button>
    </form>
</body>
</html>
