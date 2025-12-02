<?php
session_start();

// ⭐ Nếu chưa có session thì kiểm tra cookie để auto login
if (!isset($_SESSION['user'])) {
    if (isset($_COOKIE['logged_user'])) {
        $_SESSION['user'] = $_COOKIE['logged_user'];
    } else {
        header("Location: login.php");
        exit();
    }
}

require_once 'config.php';

// Lấy danh sách người hiến máu từ bảng donors
$stmt = $pdo->query("SELECT * FROM donors");
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$donors) $donors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Donor Management</h1>

        <button onclick="window.location.href='add_donor.php'">+ Add Donor</button>

        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Code</th>
                <th>Name</th>
                <th>Blood Type</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($donors)): ?>
                <tr><td colspan="7">No donor data available.</td></tr>
            <?php else: ?>
                <?php foreach ($donors as $index => $donor): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($donor['code']) ?></td>
                        <td><?= htmlspecialchars($donor['name']) ?></td>
                        <td><?= htmlspecialchars($donor['blood_type']) ?></td>
                        <td><?= htmlspecialchars($donor['phone_number']) ?></td>
                        <td><?= htmlspecialchars($donor['status']) ?></td>
                        <td>
                            <a href="edit_donor.php?id=<?= $donor['id'] ?>">Edit</a> |
                            <a href="delete_donor.php?id=<?= $donor['id'] ?>" onclick="return confirm('Are you sure to delete this donor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
