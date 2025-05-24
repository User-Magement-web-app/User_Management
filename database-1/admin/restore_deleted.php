<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Restore user
if (isset($_GET['restore_id'])) {
    $restore_id = intval($_GET['restore_id']);
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 0 WHERE user_id = ?");
    if ($stmt->execute([$restore_id])) {
        $success = "User  restored successfully.";
    } else {
        $error = "Failed to restore user.";
    }
}

// Delete user permanently
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // First, delete related records in user_profiles
    $stmt = $pdo->prepare("DELETE FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$delete_id]);
    // Next, delete related records in projects
    $stmt = $pdo->prepare("DELETE FROM projects WHERE user_id = ?");
    $stmt->execute([$delete_id]);
    // Now delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "User  deleted permanently.";
    } else {
        $error = "Failed to delete user.";
    }
}

// Fetch deleted users
$stmt = $pdo->query("
    SELECT u.user_id, u.username, r.role_name, d.department_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN departments d ON u.department_id = d.department_id
    WHERE u.is_deleted = 1
    ORDER BY u.user_id ASC
");
$deleted_users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restore Deleted Users</title>
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
            margin: 20px auto; /* Center the table */
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
        <li><a href="post_announcement.php">Post Announcement</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h3>Restore Deleted Users</h3>

<?php if ($error): ?>
    <p style="color:red; text-align: center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green; text-align: center;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- Deleted Users List -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Department</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($deleted_users) === 0): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No deleted users found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($deleted_users as $user): ?>
            <tr>
                <td><?= $user['user_id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role_name']) ?></td>
                <td><?= htmlspecialchars($user['department_name']) ?></td>
                <td>
                    <a href="?restore_id=<?= $user['user_id'] ?>" onclick="return confirm('Restore this user?');">Restore</a> |
                    <a href="?delete_id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user permanently?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>
