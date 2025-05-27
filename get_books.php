<?php
session_start();
include 'db.php';


$sql = "SELECT * FROM books";
$result = $conn->query($sql);

$books = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $books[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode(["success" => true, "books" => $books]);

$conn->close();
?>
