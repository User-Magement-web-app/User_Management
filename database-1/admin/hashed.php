<?php
$password = 'adminpassword'; // Replace with your desired password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hash;
?>
