<?php
session_start();
include 'db.php'; // Ensure db.php correctly connects to the database

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details securely
$sql = "SELECT lastname, firstname, middlename FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle cases where no user is found
if (!$user) {
    die("Error: User not found.");
}

// Ensure all variables are set before using them
$full_name = trim(($user['firstname'] ?? '') . " " . (($user['middlename'] ?? '') ? $user['middlename'] . " " : '') . ($user['lastname'] ?? ''));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_number = $_POST['id_number'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];
    $time_in = $_POST['time_in'];
    $date = $_POST['date'];
    $session = $_POST['session']; // Allow user to input session

    // Prepare SQL statement
    $sql = "INSERT INTO reservations (id_number, full_name, purpose, lab, time_in, date, remaining_session) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing SQL: " . $conn->error); // Debugging line
    }

    $stmt->bind_param("sssssss", $id_number, $full_name, $purpose, $lab, $time_in, $date, $session);

    if ($stmt->execute()) {
        // Store the reserved session number in the user's session data
        $_SESSION['reserved_session'] = $session;
        
        $_SESSION['success_message'] = "Reservation successful!";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            height: 100vh; /* Ensures the body takes the full height of the viewport */
        }
        .header {
            background: #222;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            font-size: 24px;
            font-weight: bold;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header button {
            background: #444;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .header button:hover {
            background: #666;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            top: 100px; /* Adds space from the header */
            margin-top: 50px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background: #444;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <button onclick="window.location.href='dashboard.php'">← Back</button>
       
        <div></div> 
    </div>
    <div class="form-container">
        <h2>Reservation Form</h2>
        <form method="POST" action="">
            <input type="text" name="id_number" placeholder="ID Number" required>
            <input type="text" name="full_name" placeholder="Student Name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>
            <input type="text" name="purpose" placeholder="Purpose" required>
            <input type="text" name="lab" placeholder="Lab" required>
            <input type="time" name="time_in" placeholder="Time In" required>
            <input type="date" name="date" placeholder="Date" required>
            <input type="number" name="session" placeholder="Session Number" required> <!-- Allow user to input session -->
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
