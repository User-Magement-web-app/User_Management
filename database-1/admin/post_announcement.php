<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Handle posting an announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($title === '' || $message === '') {
        $error = "Both title and message are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $message, $_SESSION['user_id']]);
        $success = "Announcement posted successfully.";
    }
}

// Handle Delete Announcement
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "Announcement deleted successfully.";
    } else {
        $error = "Failed to delete announcement.";
    }
}

// Fetch announcements
$stmt = $pdo->query("SELECT a.id, a.title, a.message, a.created_at, u.username FROM announcements a JOIN users u ON a.created_by = u.user_id ORDER BY a.created_at DESC");
$announcements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Announcement</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f9f7;
        }

        .navbar {
            background-color: #2c7a7b;
            padding: 10px;
            position: fixed; /* Fix the navbar at the top */
            width: 100%; /* Full width */
            top: 0; /* Align to the top */
            z-index: 1000; /* Ensure it stays above other content */
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: center; /* Center the navbar items */
        }

        .navbar li {
            display: inline; /* Horizontal layout */
            margin: 0 15px; /* Spacing between items */
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        h3 {
            position: fixed; /* Fix the navbar at the top */
            width: 100%; /* Full width */
            top: 50px; /* Align to the top */
            z-index: 100; /* Ensure it stays above other content */
            text-align: center; /* Center the heading */
            color: #2c7a7b;
            margin-top: 60px; /* Space above the heading to avoid overlap with navbar */
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        table {
            
            margin: 20px ; /* Center the table */
            
            border-collapse: collapse;
            width: 80%; /* Set table width */
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #2c7a7b;
            color: white;
        }
    </style>
</head>
<body>

<div class="navbar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_departments.php">Manage Departments</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h3>Post Announcement</h3>


<?php if ($error): ?>
    <p style="color:red; text-align: center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green; text-align: center;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- Announcement Form -->
<form method="post" action="" style="text-align: center; margin-top: 20px;">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Message:</label><br>
    <textarea name="message" rows="5" cols="40" required></textarea><br><br>

    <button type="submit">Post</button>
</form>

<br>

<!-- Announcements List -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($announcements) === 0): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No announcements found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($announcements as $announcement): ?>
            <tr>
                <td><?= htmlspecialchars($announcement['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($announcement['message'])) ?></td>
                <td><?= htmlspecialchars($announcement['username']) ?></td>
                <td><?= htmlspecialchars($announcement['created_at']) ?></td>
                <td>
                    <a href="?delete_id=<?= $announcement['id'] ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>
