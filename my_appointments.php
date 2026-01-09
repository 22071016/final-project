<?php
require_once 'config.php';
if (!hasPermission(['donor'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT a.* FROM appointments a JOIN donors dn ON a.donor_id = dn.id WHERE dn.user_id = ? ORDER BY a.appointment_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>My Appointments</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($appointments)): ?>
            <p style="text-align:center;">You have no appointments scheduled.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= date('M d, Y H:i', strtotime($a['appointment_date'])) ?></td>
                        <td><?= htmlspecialchars($a['location']) ?></td>
                        <td><?= ucfirst($a['status']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['notes'] ?? '')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>