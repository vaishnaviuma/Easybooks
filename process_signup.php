<?php
session_start();
include 'db.php'; // Make sure this file connects to your ebdub database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    // Server-side validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => "Invalid email format."]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['status' => 'error', 'message' => "Passwords do not match."]);
        exit;
    }

    // Strong password validation
    $strongPasswordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    if (!preg_match($strongPasswordRegex, $password)) {
        echo json_encode(['status' => 'error', 'message' => "Password must be strong."]);
        exit;
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM allusers WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => "Email already registered."]);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO allusers (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
         header("Location: login.php");
         exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method."]);
}
?>
