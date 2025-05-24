<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Add new department
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dept_name = trim($_POST['department_name'] ?? '');

    if ($dept_name === '') {
        $error = "Department name cannot be empty.";
    } else {
        // Check duplicate
        $stmt = $pdo->prepare("SELECT * FROM departments WHERE department_name = ? ");
        $stmt->execute([$dept_name]);
        if ($stmt->fetch()) {
            $error = "Department already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO departments (department_name) VALUES (?)");
            $stmt->execute([$dept_name]);
            $success = "Department added successfully.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "Department deleted successfully.";
    } else {
        $error = "Failed to delete department.";
    }
}

// Fetch departments
$stmt = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC");
$departments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
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
        <li><a href="post_announcement.php">Post Announcement</a></li>
        <li><a href="restore_deleted.php">Restore Deleted Users</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h3>Manage Departments</h3>


<?php if ($error): ?>
    <p style="color:red; text-align: center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green; text-align: center;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- Department Form -->
<form method="post" action="" style="text-align: center; margin-top: 20px;">
    <input type="text" name="department_name" placeholder="New Department Name" required>
    <button type="submit">Add Department</button>
</form>

<br>

<!-- Department List -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($departments as $dept): ?>
        <tr>
            <td><?= $dept['department_id'] ?></td>
            <td><?= htmlspecialchars($dept['department_name']) ?></td>
            <td>
                <a href="?delete_id=<?= $dept['department_id'] ?>" onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
