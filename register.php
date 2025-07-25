<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';

    if ($role === 'user') {
        $name = trim($_POST['user_name'] ?? '');
        $email = trim($_POST['user_email'] ?? '');
        $password = $_POST['user_password'] ?? '';
        $confirm = $_POST['user_confirm_password'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: index.php");
            exit;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $hashed);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful. Please log in.";
        } else {
            $_SESSION['error'] = "User registration failed: " . $stmt->error;
        }
        $stmt->close();

    } elseif ($role === 'provider') {
        $name = trim($_POST['provider_name'] ?? '');
        $email = trim($_POST['provider_email'] ?? '');
        $password = $_POST['provider_password'] ?? '';
        $confirm = $_POST['provider_confirm_password'] ?? '';
        $service_type = $_POST['service_type'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $experience = $_POST['experience'] ?? '';
        $district = $_POST['district'] ?? '';
        $gn_division = $_POST['gn_division'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: index.php");
            exit;
        }

        // Optional: Validate district and gn_division exist in DB for security
        // e.g. check if district id exists, gn division id exists and belongs to district

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO service_providers (name, email, service_type, phone, experience, district, gn_division, password, created_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("ssssssss", $name, $email, $service_type, $phone, $experience, $district, $gn_division, $hashed);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Provider registered successfully. Await admin approval.";
        } else {
            $_SESSION['error'] = "Provider registration failed: " . $stmt->error;
        }
        $stmt->close();

    } else {
        $_SESSION['error'] = "Invalid role selected.";
    }

    $conn->close();
    header("Location: index.php");
    exit;
}
?>
