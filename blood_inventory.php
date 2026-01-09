<?php
// File: blood_inventory.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'] ?? '', ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

// Fetch blood inventory with donor information
$stmt = $pdo->query("
    SELECT 
        bi.*, 
        d.full_name, 
        d.code AS donor_code,
        DATEDIFF(bi.expiry_date, CURDATE()) AS days_left
    FROM blood_inventory bi
    LEFT JOIN donations don ON bi.donation_id = don.id
    LEFT JOIN donors d ON don.donor_id = d.id
    ORDER BY bi.expiry_date ASC
");
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Inventory</title>
    <link rel="stylesheet" href="style.css">

    <style>
        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        th {
            background: #d9534f;
            color: white;
        }
        .warning { background: #fff3cd; color: #856404; }
        .danger  { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .used    { background: #e2e3e5; color: #383d41; }
    </style>
</head>

<body>

<header style="background:#d9534f; color:white; padding:20px; text-align:center; position:relative;">
    <h1>Blood Inventory</h1>
    <a href="index.php"
       style="position:absolute; right:20px; top:25px; color:white; text-decoration:none;">
        Dashboard
    </a>
</header>

<div style="padding:20px; text-align:center;">
    <h2 style="color:#d9534f;">
        Total: <?= count($inventory) ?> blood bags
    </h2>
</div>

<?php if (!empty($inventory)): ?>
<table>
    <thead>
        <tr>
            <th>Bag Code</th>
            <th>Blood Type</th>
            <th>Volume</th>
            <th>Collection Date</th>
            <th>Expiry Date</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inventory as $bag): 
            $days_left = (int)$bag['days_left'];
            $row_class = '';
            $status_text = '';

            // Priority: used bags first
            if ($bag['status'] === 'used') {
                $row_class = 'used';
                $status_text = 'Used';
            } elseif ($days_left < 0) {
                $row_class = 'danger';
                $status_text = '🔴 Expired';
            } elseif ($days_left <= 7) {
                $row_class = 'warning';
                $status_text = '🟡 Expiring Soon';
            } else {
                $row_class = 'success';
                $status_text = '🟢 Valid';
            }
        ?>
        <tr class="<?= $row_class ?>">
            <td><strong><?= htmlspecialchars($bag['blood_bag_code']) ?></strong></td>

            <td style="text-align:center; font-weight:bold; font-size:18px; color:#d9534f;">
                <?= htmlspecialchars($bag['blood_type']) ?>
            </td>

            <td style="text-align:center;">
                <?= (int)$bag['volume_ml'] ?> ml
            </td>

            <td><?= date('d/m/Y', strtotime($bag['collection_date'])) ?></td>
            <td><?= date('d/m/Y', strtotime($bag['expiry_date'])) ?></td>

            <td style="text-align:center; font-weight:bold;">
                <?= $days_left >= 0 ? $days_left . ' days' : 'EXPIRED' ?>
            </td>

            <td style="text-align:center; font-weight:bold;">
                <?= $status_text ?>
            </td>

            <td>
                <?php if ($bag['donor_code']): ?>
                    [<?= htmlspecialchars($bag['donor_code']) ?>]
                    <?= htmlspecialchars($bag['full_name']) ?>
                <?php else: ?>
                    Unknown
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<div style="text-align:center; padding:80px; color:#666; font-size:20px;">
    No blood bags available in the inventory.<br><br>
    Please <a href="add_donation.php">record a donation</a> to add blood to the inventory.
</div>
<?php endif; ?>

</body>
</html>
