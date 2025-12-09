<?php
session_start();
require_once 'config.php';
if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM donors ORDER BY id DESC");
$donors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Người hiến</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <header>
    <h1>Quản lý Người hiến máu</h1>
    <a href="index.php">Dashboard</a>
</header>

<div class="container">

    <?php if (empty($donors)): ?>
        <div style="text-align:center; padding:80px; color:#999; font-size:20px;">
            Chưa có người hiến máu nào.<br>
            Hãy thêm người hiến đầu tiên ngay bây giờ!
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Họ tên</th>
                    <th>Nhóm máu</th>
                    <th>Số điện thoại</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donors as $donor): ?>
                <tr>
                    <td><?= $donor['id'] ?></td>
                    <td><strong><?= htmlspecialchars($donor['code']) ?></strong></td>
                    <td><?= htmlspecialchars($donor['full_name']) ?></td>
                    <td style="color:#d9534f; font-weight:bold; font-size:18px;">
                        <?= $donor['blood_type'] ?>
                    </td>
                    <td><?= $donor['phone'] ?></td>
                    <td>
                        <a href="edit_donor.php?id=<?= $donor['id'] ?>" style="color:#007bff;">Sửa</a> |
                        <a href="delete_donor.php?id=<?= $donor['id'] ?>" 
                           onclick="return confirm('Xóa người hiến này?')" 
                           style="color:#dc3545;">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="add_donor.php" class="add-new-btn">
        + Thêm người hiến mới
    </a>

</div>
</body>
</html>