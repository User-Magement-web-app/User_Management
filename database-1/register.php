<?php
// --- Database connection ---
$conn = new mysqli("localhost", "root", "", "db_it26_project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle form submission ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department_id = intval($_POST['department_id']);
    $role_id = 3; // default to 'user'

    $stmt = $conn->prepare("INSERT INTO users (username, password, role_id, department_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $username, $password, $role_id, $department_id);

    if ($stmt->execute()) {
        $message = "User registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// --- Fetch departments ---
$dept_result = $conn->query("SELECT department_id, department_name FROM departments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        form { width: 300px; margin: auto; }
        label, input, select { display: block; width: 100%; margin-bottom: 10px; }
        .message { text-align: center; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    

    <form method="POST" action="">
    <h2 style="text-align:center;">Register New User</h2>
    <hr>
    
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="department_id">Select Department:</label>
        <select name="department_id" required>
            <option value="">-- Select Department --</option>
            <?php while ($dept = $dept_result->fetch_assoc()): ?>
                <option value="<?= $dept['department_id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <input type="hidden"name="submit"><button type="submit" >Register</button></input>
        

    <p style="text-align:center;">
        Already have an account? <a href="login.php">Login here</a>.
    </p>
    </form>

    

</body>
</html>

<?php $conn->close(); ?>
