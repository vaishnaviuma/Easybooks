<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'], $_POST['book_id'], $_POST['rating'], $_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $comment);

    if ($stmt->execute()) {
        header("Location: book-detail.php?id=" . $book_id);
        exit();
    } else {
        echo "Error saving review.";
    }
} else {
    echo "Invalid request.";
}
?>
