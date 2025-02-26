<?php
session_start();
include 'db.php';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch the user's ID number
    $sql = "SELECT idno FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $id_number = $user['idno'];

        // Deduct one session
        $sql = "UPDATE users SET session = session - 1 WHERE idno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_number);
        $stmt->execute();
    }

    // Destroy the session
    session_destroy();
}

header("Location: login.php");
exit();
?>
