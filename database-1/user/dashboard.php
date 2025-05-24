<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user projects that are not deleted
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ? AND is_deleted = 0 ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Projects</title>
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

<h2>My Projects</h2>

<?php if (count($projects) === 0): ?>
    <p style="text-align: center;">You have no projects yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Project Title</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($project['description'])) ?></td>
                    <td><?= htmlspecialchars($project['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
