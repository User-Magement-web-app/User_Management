<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$error = '';
$success = '';
$user_id = null;
$username = '';
$role_id = 3; // Default to 'user'
$department_id = 0;

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $role_id = intval($_POST['role_id'] ?? 3); // Default to 'user'
    $department_id = intval($_POST['department_id'] ?? 0);

    if ($username === '') {
        $error = "Username cannot be empty.";
    } else {
        if (isset($_POST['user_id']) && $_POST['user_id'] != '') {
            // Update existing user
            $user_id = intval($_POST['user_id']);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role_id = ?, department_id = ? WHERE user_id = ?");
            $stmt->execute([$username, $role_id, $department_id, $user_id]);
            $success = "User  updated successfully.";
        } else {
            // Create new user
            $stmt = $pdo->prepare("INSERT INTO users (username, role_id, department_id) VALUES (?, ?, ?)");
            $stmt->execute([$username, $role_id, $department_id]);
            $success = "User  added successfully.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE user_id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "User  deleted successfully.";
    } else {
        $error = "Failed to delete user.";
    }
}

// Handle Edit
if (isset($_GET['edit_id'])) {
    $user_id = intval($_GET['edit_id']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        $username = $user['username'];
        $role_id = $user['role_id'];
        $department_id = $user['department_id'];
    }
}

// Fetch all users who are not deleted
$stmt = $pdo->query("
    SELECT u.user_id, u.username, r.role_name, d.department_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN departments d ON u.department_id = d.department_id
    WHERE u.is_deleted = 0
    ORDER BY u.user_id ASC
");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
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
        <li><a href="manage_departments.php">Manage Departments</a></li>
        <li><a href="post_announcement.php">Post Announcement</a></li>
        <li><a href="restore_deleted.php">Restore Deleted Users</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h3>Manage Users</h3>

<?php if ($error): ?>
    <p style="color:red; text-align: center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green; text-align: center;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- User Form -->
<form method="post" action="" style="text-align: center; margin-top: 20px;">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id ?? '') ?>">
    <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($username ?? '') ?>">
    <select name="role_id">
        <option value="1" <?= (isset($role_id) && $role_id == 1) ? 'selected' : '' ?>>Admin</option>
        <option value="2" <?= (isset($role_id) && $role_id == 2) ? 'selected' : '' ?>>Manager</option>
        <option value="3" <?= (isset($role_id) && $role_id == 3) ? 'selected' : '' ?>>User </option>
    </select>
    <select name="department_id">
        <option value="0">-- Select Department --</option>
        <?php
        $dept_stmt = $pdo->query("SELECT department_id, department_name FROM departments");
        while ($dept = $dept_stmt->fetch()): ?>
            <option value="<?= $dept['department_id'] ?>" <?= (isset($department_id) && $department_id == $dept['department_id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['department_name']) ?></option>
        <?php endwhile; ?>
    </select>
    <hr>
    <button type="submit">Save User</button>
</form>

<br>

<!-- User List -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?= $user['user_id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role_name']) ?></td>
            <td><?= htmlspecialchars($user['department_name']) ?></td>
            <td>
                <a href="?edit_id=<?= $user['user_id'] ?>">Edit</a> |
                <a href="?delete_id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
