<?php
// File: blood_inventory.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'] ?? '', ['admin','staff'])) {
    header('Location: index.php'); exit;
}

// Lấy dữ liệu từ blood_inventory + join với donation và donor để hiển thị tên người hiến
$stmt = $pdo->query("
    SELECT bi.*, d.full_name, d.code as donor_code,
           DATEDIFF(bi.expiry_date, CURDATE()) as days_left
    FROM blood_inventory bi
    LEFT JOIN donations don ON bi.donation_id = don.id
    LEFT JOIN donors d ON don.donor_id = d.id
    ORDER BY bi.expiry_date ASC
");
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tồn kho máu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .warning { background:#fff3cd !important; color:#856404; }
        .danger { background:#f8d7da !important; color:#721c24; }
        .success { background:#d4edda !important; color:#155724; }
    </style>
</head>
<body>
    <header style="background:#d9534f; color:white; padding:20px; text-align:center; position:relative;">
        <h1>Tồn kho máu</h1>
        <a href="index.php" style="position:absolute; right:20px; top:25px; color:white; text-decoration:none;">Dashboard</a>
    </header>

    <div style="padding:20px; text-align:center;">
        <h2 style="color:#d9534f;">Tổng cộng: <?= count($inventory) ?> túi máu</h2>
    </div>

    <table style="width:95%; margin:auto; border-collapse:collapse; font-size:1px solid #ccc;">
        <thead style="background:#d9534f; color:white;">
            <tr>
                <th>Mã túi</th>
                <th>Nhóm máu</th>
                <th>Thể tích</th>
                <th>Ngày thu</th>
                <th>Hết hạn</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
                <th>Nguồn gốc</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inventory as $bag): 
                $days_left = $bag['days_left'];
                $row_class = '';
                $status_text = '';
                if ($bag['status'] == 'used') {
                    $row_class = 'background:#ddd;';
                    $status_text = 'Đã sử dụng';
                } elseif ($days_left < 0) {
                    $row_class = 'danger';
                    $status_text = 'HẾT HẠN';
                } elseif ($days_left <= 7) {
                    $row_class = 'warning';
                    $status_text = 'Sắp hết hạn';
                } else {
                    $row_class = 'success';
                    $status_text = 'Còn hạn';
                }
            ?>
            <tr class="<?= $row_class ?>">
                <td><strong><?= htmlspecialchars($bag['blood_bag_code']) ?></strong></td>
                <td style="text-align:center; font-weight:bold; font-size:18px; color:#d9534f;">
                    <?= $bag['blood_type'] ?>
                </td>
                <td style="text-align:center;"><?= $bag['volume_ml'] ?> ml</td>
                <td><?= date('d/m/Y', strtotime($bag['collection_date'])) ?></td>
                <td><?= date('d/m/Y', strtotime($bag['expiry_date'])) ?></td>
                <td style="text-align:center; font-weight:bold;">
                    <?= $days_left >= 0 ? "$days_left ngày" : "ĐÃ HẾT" ?>
                </td>
                <td style="text-align:center; font-weight:bold;">
                    <?= $status_text ?>
                </td>
                <td>
                    <?= $bag['donor_code'] ? "[{$bag['donor_code']}] {$bag['full_name']}" : "Chưa rõ" ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if(empty($inventory)): ?>
    <div style="text-align:center; padding:80px; color:#666; font-size:20px;">
        Chưa có túi máu nào trong kho.<br><br>
        Hãy <a href="add_donation.php">ghi nhận hiến máu</a> để tự động thêm vào kho!
    </div>
    <?php endif; ?>
</body>
</html>