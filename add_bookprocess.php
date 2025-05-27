<?php
session_start();
include 'db.php';

$title = $_POST['title'];
$author = $_POST['author'];
$price = $_POST['price'];
$genre = $_POST['genre'];
$bookDesc = $_POST['bookDesc'];
$authorDesc = $_POST['authorDesc'];
$imageUrl = "";

if (isset($_POST['imageType']) && $_POST['imageType'] == "url") {
    $imageUrl = $_POST['imageUrl'];
} elseif (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = basename($_FILES['imageFile']['name']);
    $targetPath = $uploadDir . uniqid() . "_" . $filename;
    move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetPath);
    $imageUrl = $targetPath;
}

$stmt = $conn->prepare("INSERT INTO books (title, author, price, genre, image_url, book_description, author_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssdssss", $title, $author, $price, $genre, $imageUrl, $bookDesc, $authorDesc);

if ($stmt->execute()) {
    header("Location: add-book.html");
} else {
    echo "error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
