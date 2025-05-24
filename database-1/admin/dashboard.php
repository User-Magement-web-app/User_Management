<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Additional styles for the dashboard */
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
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_departments.php">Manage Departments</a></li>
        <li><a href="post_announcement.php">Post Announcement</a></li>
        <li><a href="restore_deleted.php">Restore Deleted Users</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h3>User Summary</h3>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Number of Projects</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch users with their roles and project counts
        $stmt = $pdo->query("
            SELECT u.user_id, u.username, r.role_name, COUNT(p.project_id) AS project_count
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            LEFT JOIN projects p ON u.user_id = p.user_id
            WHERE u.is_deleted = 0
            GROUP BY u.user_id, u.username, r.role_name
            ORDER BY u.user_id ASC
        ");
        $users = $stmt->fetchAll();

        foreach ($users as $user): ?>
        <tr>
            <td><?= $user['user_id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role_name']) ?></td>
            <td><?= $user['project_count'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
