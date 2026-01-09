<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = $_POST['donor_id'] ?? 0;
    $appointment_date = $_POST['appointment_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($donor_id <= 0 || empty($appointment_date) || empty($location)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (donor_id, appointment_date, location, notes, status, created_by) VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([$donor_id, $appointment_date, $location, $notes, $_SESSION['user_id']]);

        // Gửi thông báo cho người hiến nếu có user_id
        $donor_user = $pdo->prepare("SELECT user_id FROM donors WHERE id = ?");
        $donor_user->execute([$donor_id]);
        $donor_user_id = $donor_user->fetchColumn();

        if ($donor_user_id) {
            sendNotification($donor_user_id, "New Appointment", "You have a blood donation appointment on " . date('M d, Y H:i', strtotime($appointment_date)) . " at $location.");
        }

        $success = "Appointment created successfully!";
    }
}

// Lấy danh sách donor
$donors = $pdo->query("SELECT id, code, full_name FROM donors ORDER BY full_name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Appointment</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 32px;
            color: #c31432;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 18px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #c31432;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #c31432;
            outline: none;
            box-shadow: 0 0 0 4px rgba(195, 20, 50, 0.2);
        }
        .form-group textarea {
            height: 140px;
            resize: vertical;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 20px;
            background: linear-gradient(45deg, #c31432, #d9534f);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 40px;
            transition: all 0.4s;
            box-shadow: 0 12px 35px rgba(195,20,50,0.5);
        }
        .submit-btn:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(195,20,50,0.7);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #c31432;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add New Appointment</h1>
        <a href="appointments.php">Back to List</a>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Schedule Blood Donation Appointment</h2>
            <p class="form-subtitle">Create a new appointment for a registered donor</p>

            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select Donor *</label>
                        <select name="donor_id" required>
                            <option value="">-- Choose a Donor --</option>
                            <?php foreach ($donors as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    [<?= $d['code'] ?>] <?= htmlspecialchars($d['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Appointment Date & Time *</label>
                        <input type="datetime-local" name="appointment_date" required>
                    </div>

                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" placeholder="e.g. National Institute of Hematology" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Notes (optional)</label>
                        <textarea name="notes" placeholder="Any special instructions or reminders..."></textarea>
                    </div>
                </div>

                <p style="text-align:center; color:#666; margin:20px 0;">
                    <strong>Note:</strong> The donor will receive a notification about this appointment (if they have an account).
                </p>

                <button type="submit" class="submit-btn">
                    Create Appointment
                </button>
            </form>

            <a href="appointments.php" class="back-link">← Back to Appointments List</a>
        </div>
    </div>
</body>
</html>