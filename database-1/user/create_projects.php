<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $description === '') {
        $error = "Title and Description cannot be empty.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO projects (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $description]);
        $success = "Project added successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Project</title>
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
            margin-bottom:60px;
        }

        .message {
            text-align: center; /* Center the message */
            font-weight: bold;
            margin-top: 20px;
        }

        form {
            width: 300px; /* Set a fixed width for the form */
            margin: auto; /* Center the form */
            background: white; /* Background for the form */
            padding: 20px; /* Padding inside the form */
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        input[type="text"], textarea {
            padding: 8px;
            width: 100%; /* Full width */
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px; /* Space between inputs */
        }

        button {
            background-color: #2c7a7b;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%; /* Full width for button */
        }

        button:hover {
            background-color: #285e61;
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

<h2>Create New Project</h2>


<form method="post" action="">
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>
    <label>Project Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" cols="50" required></textarea><br><br>

    <button type="submit">Add Project</button>
</form>
</body>
</html>
