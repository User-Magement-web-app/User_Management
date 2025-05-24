<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user projects that are not deleted along with user information
$stmt = $pdo->prepare("
    SELECT p.project_id, p.title, p.description, p.status, p.created_at, u.username 
    FROM projects p 
    JOIN users u ON p.user_id = u.user_id 
    WHERE p.user_id = ? AND p.is_deleted = 0 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

// Handle Update and Delete
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Handle Delete
        $delete_id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("UPDATE projects SET is_deleted = 1 WHERE project_id = ?");
        if ($stmt->execute([$delete_id])) {
            $success = "Project deleted successfully.";
        } else {
            $error = "Failed to delete project.";
        }
    } else {
        // Handle Update
        $project_id = intval($_POST['project_id']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];

        if ($title === '' || $description === '') {
            $error = "Title and Description cannot be empty.";
        } else {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, status = ? WHERE project_id = ?");
            if ($stmt->execute([$title, $description, $status, $project_id])) {
                $success = "Project updated successfully.";
            } else {
                $error = "Failed to update project.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
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

        h2 {
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
        .p{
            position: fixed; /* Fix the navbar at the top */
            width: 100%; /* Full width */
            top: 50px; /* Align to the top */
            z-index: 100; /* Ensure it stays above other content */
            text-align: center; /* Center the heading */
            color: #2c7a7b;
            margin-top: 60px; /* Space above the heading to avoid overlap with navbar */
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

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Zebra striping for even rows */
        }

        .form-container {
            margin: 20px auto;
            width: 80%; /* Set form width */
            background: white; /* Background for the form */
            padding: 20px; /* Padding inside the form */
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
    </style>
</head>
<body>
<div class="navbar">
    <ul>
    <li><a href="dashboard.php">Home</a></li>
    <li><a href="create_projects.php">Add Project</a></li>
    <li><a href="manage_projects.php">Manage Projects</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<h2>Manage Projects</h2>


<table>

    <thead>
        <tr>
            <th>Project Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= htmlspecialchars($project['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($project['description'])) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td><?= htmlspecialchars($project['username']) ?></td>
                <td>
                    <form method="post" action="" style="display:flex; width:100%;">
                        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
                        <input type="text" name="description" value="<?= htmlspecialchars($project['description']) ?>" required>
                        <select name="status">
                            <option value="In Progress" <?= ($project['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= ($project['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $project['project_id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this project?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
