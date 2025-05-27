<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM allusers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["email"] = $user["email"];

            // Check if profile is completed
            // Save target page in session
if ($user["profile_completed"] == 0) {
    $_SESSION["redirect_target"] = "profile.php";
} else {
    $_SESSION["redirect_target"] = "home.php";
}
header("Location: login_success.php");
exit();

        } else {
            $_SESSION['error_message'] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "No user found with that email.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
