<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location='login.php';</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #2a3d4f; /* Beige background */
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-content: center;
        background-color: #fffff0; /* Ivory */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .logo-left, .logo-right {
        max-width: 150px;
        height: auto;
    }

    h2 {
        text-align: center;
        flex-grow: 1;
        font-size: 24px;
        margin: 0;
        padding-top: 50px;
        color: black; /* Darker beige */
    }

    .container {
        background: white;
        padding: 50px;
        border-radius: 10px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    input {
        width: 96%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #d2b48c; /* Darker beige */
        border-radius: 5px;
    }

    button {
        width: 100%;
        padding: 10px;
        background: #d2b48c; /* Darker beige */
        color: black;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background: #b59f7a; /* Even darker beige on hover */
    }

    a {
        display: block;
        margin-top: 10px;
        text-decoration: none;
        color:black; /* Darker beige */
    }

    a:hover {
        color: #b59f7a; /* Even darker beige on hover */
    }
</style>

</head>
<body>
<div class="container">
<div class="header">
<img src="images\ccs.png" alt="left" class="logo-left">
<h2>CCS Sit-in Monitoring System</h2>
<img src="images\uc.jpg" alt="right" class="logo-right">
</div>
<form action="login.php" method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<a href="register.php">Don't have an account? Register</a>
</div>
</body>
</html>