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
    // Insert new profile with profile_completed set to 1
    $stmt = $conn->prepare("INSERT INTO allusers (email, name, age, gender, address, fav_genre, phone_no, profile_completed) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssissss", $email, $name, $age, $gender, $address, $fav_genre, $phone);
} else {
    // Update existing profile and set profile_completed to 1
    $stmt = $conn->prepare("UPDATE allusers SET name=?, age=?, gender=?, address=?, fav_genre=?, phone_no=?, profile_completed=1 WHERE email=?");
    $stmt->bind_param("sisssss", $name, $age, $gender, $address, $fav_genre, $phone, $email);
}

if ($stmt->execute()) {
    header("Location: profile.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}
?>
