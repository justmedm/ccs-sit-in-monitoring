<?php
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get user details
$sql = "SELECT lastname, firstname, middlename, course, yearlevel, email, address, session, profile_image FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found
if (!$user) {
    die("Error: User not found.");
}

// User info variables
$full_name = trim(($user['firstname'] ?? '') . " " . (($user['middlename'] ?? '') ? $user['middlename'] . " " : '') . ($user['lastname'] ?? ''));
$course = $user['course'] ?? 'Not Available';
$year = $user['yearlevel'] ?? 'Not Available';
$email = $user['email'] ?? 'Not Available';
$address = $user['address'] ?? 'Not Available';
$session = $user['session'] ?? 'Not Available';

// Profile image
$profile_image = !empty($user['profile_image']) && $user['profile_image'] !== 'cat.jpg' 
    ? 'images/' . htmlspecialchars($user['profile_image']) 
    : 'cat.jpg';

// Reserved session (not fully implemented)
$reserved_session = $_SESSION['reserved_session'] ?? 'No session reserved';

// Success message
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

       /* Header */
        .header {
            background: #222;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            width: 100%;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        /* Title Styling */
        .header-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0; /* Remove default margin */
            display: flex;
            align-items: center;
        }

        /* Navigation Menu */
        .nav-menu {
            display: flex;
            align-items: center; /* Ensure buttons align with the title */
        }

        /* Button Styling */
        .nav-menu button {
            background: trasnparent;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            display: flex;
            align-items: center; /* Centers text inside buttons */
            justify-content: center;
            height: 40px; /* Ensures consistent button height */
        }

        /* Button Hover Effect */
        .nav-menu button:hover {
            background: #666;
        }


        /* Main content */
        .content {
            padding: 20px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .student-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            text-align: left;
            width: 100%;
            max-width: 400px;
            margin: 40px auto;
        }

        .profile-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .student-info img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #222;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Logout Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 300px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            background: #444;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">CCS Sit-in Monitoring</div>
        <div class="nav-menu">
            <button onclick="location.href='edit.php'">Edit</button>
            <button onclick="location.href='announcement.php'">View Announcement</button>
            <button onclick="location.href='sitinrules.php'">Sit-in Rules</button>
            <button onclick="location.href='LRR.php'">Lab Rules & Regulations</button>
            <button onclick="location.href='history.php'">History</button>
            <button onclick="location.href='reservation.php'">Reservation</button>
            <button onclick="openLogoutModal()">Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="student-info">
            <h3 class="profile-title">Profile Information</h3>
            <img src="images/<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'cat.jpg'; ?>" alt="Profile Picture">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($course); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($year); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Reserved Session:</strong> <?php echo htmlspecialchars($reserved_session); ?></p>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to log out?</p>
            <button id="proceedLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex'; 
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none'; 
        }

        document.getElementById('proceedLogout').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('cancelLogout').addEventListener('click', closeLogoutModal);
    </script>
</body>
</html>
