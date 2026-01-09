<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT a.*, d.full_name, d.phone FROM appointments a JOIN donors d ON a.donor_id = d.id ORDER BY a.appointment_date DESC");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Manage Appointments</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($appointments)): ?>
            <p style="text-align:center;">No appointments scheduled.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Phone</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['full_name']) ?></td>
                        <td><?= $a['phone'] ?></td>
                        <td><?= date('M d, Y H:i', strtotime($a['appointment_date'])) ?></td>
                        <td><?= htmlspecialchars($a['location']) ?></td>
                        <td style="color:<?= $a['status']=='confirmed'?'green':($a['status']=='cancelled'?'red':'orange') ?>;font-weight:bold;">
                            <?= ucfirst($a['status']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_appointment.php" class="add-new-btn">+ Add New Appointment</a>
    </div>
</body>
</html>