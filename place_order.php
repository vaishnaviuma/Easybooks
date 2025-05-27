<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Get form data from POST
$user_id = $_SESSION['user_id'];
$user_name = $_POST['user_name'] ?? '';
$user_email = $_POST['user_email'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';
$book_id = $_POST['book_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;
$total_amount = $_POST['totalAmount'] ?? 0.0;

// Validate data (optional but recommended)

// Prepare insert statement
$stmt = $conn->prepare("INSERT INTO orders (user_id, user_name, contact_number, shipping_address, book_id, quantity, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssiid", $user_id, $user_name, $contact_number, $shipping_address, $book_id, $quantity, $total_amount);

if ($stmt->execute()) {
    // success, redirect or show message
    header("Location: payment.php?order_id=" . $conn->insert_id);
    exit;
} else {
    echo "Error placing order: " . $stmt->error;
}
