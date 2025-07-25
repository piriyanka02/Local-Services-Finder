<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: index.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: index.php");
        exit;
    }

    switch ($role) {
        case 'user':
            $table = 'users';
            break;
        case 'admin':
            $table = 'admin';
            break;
        case 'provider':
            $table = 'service_providers';
            break;
        default:
            $_SESSION['error'] = "Invalid role selected.";
            header("Location: index.php");
            exit;
    }

    $stmt = $conn->prepare("SELECT id, name, email, password FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $role;

            $_SESSION['success'] = "Welcome back, " . $user['name'] . "!";

            if ($role === 'admin') {
                header("Location: admin_panel.php");
            } elseif ($role === 'provider') {
                header("Location: provider_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "Account not found.";
    }

    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
