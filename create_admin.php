<?php
require 'config.php';

$name = 'Piriyanka';
$email = 'piriyanka@gmail.com';
$password = password_hash('zxcv', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo "✅ Admin account created successfully.";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
