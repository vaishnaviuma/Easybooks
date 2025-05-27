<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$fav_genre = $_POST['fav_genre'];
$phone = $_POST['phone'];

// Check if profile already exists
$stmt = $conn->prepare("SELECT * FROM allusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert new profile
    $stmt = $conn->prepare("INSERT INTO allusers (email, name, age, gender, address, fav_genre, phone_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $email, $name, $age, $gender, $address, $fav_genre, $phone);
} else {
    // Update existing profile
    $stmt = $conn->prepare("UPDATE allusers SET name=?, age=?, gender=?, address=?, fav_genre=?, phone_no=? WHERE email=?");
    $stmt->bind_param("sisssss", $name, $age, $gender, $address, $fav_genre, $phone, $email);
}

if ($stmt->execute()) {
    header("Location: profilecheck.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}
?>
