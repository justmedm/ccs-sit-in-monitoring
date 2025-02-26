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

// Reserved session
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
    background-color: #2a3d4f; /* Beige background */
}

/* Header */
.header {
    background: #F8F1E7; /* Ivory */
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    width: 100%;
    box-sizing: border-box;
    border-bottom: 2px solid #d2b48c; /* Darker beige border */
}

.header-title {
    font-size: 24px;
    font-weight: bold;
}

.nav-menu button {
    background: #d2b48c; /* Darker beige */
    color: black;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    height: 40px;
}

.nav-menu button:hover {
    background: #b59f7a; /* Even darker beige on hover */
}

/* Main content */
.content-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
    max-width: 1200px;
    margin-top: 20px;
    gap: 20px;
}

.student-info, .announcement-section, .rules-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 400px;
    text-align: center; /* Keeps title and image centered */
}

.profile-title {
    text-align: center; /* Keeps Profile Information centered */
}

.profile-details {
    text-align: left; /* Aligns text to the left */
    width: 100%;
    padding: 10px 20px;
}

.student-info .p {
    text-align: left;
}

.student-info img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 3px solid #d2b48c; /* Darker beige border */
}

/* Announcements and Rules Container */
.info-sections {
    display: flex;
    flex-direction: column;
    gap: 20px;
    flex: 1;
}

.announcement-section, .rules-section {
    background: white;
    border-radius: 10px;
    padding: 41px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    max-height: 400px;
    overflow-y: auto;
}

.announcement-item {
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.announcement-item:last-child {
    border-bottom: none;
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
    background: #d2b48c; /* Darker beige */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #b59f7a; /* Even darker beige on hover */
}
</style>

</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">CCS Sit-in Monitoring</div>
        <div class="nav-menu">
            <button onclick="location.href='edit.php'">Edit</button>
            <button onclick="location.href='sitinrules.php'">Sit-in Rules</button>
            <button onclick="location.href='history.php'">History</button>
            <button onclick="location.href='reservation.php'">Reservation</button>
            <button onclick="openLogoutModal()">Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-container">
        <!-- Profile Information -->
        <div class="student-info">
            <h3 class="profile-title">Profile Information</h3>
            <img src="images/<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'cat.jpg'; ?>" alt="Profile Picture">
            <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($course); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($year); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Reserved Session:</strong> <?php echo htmlspecialchars($reserved_session); ?></p>
        </div>
    </div>
        <!-- Announcements -->
            <div class="announcement-section">
                <h2>📢 Announcement</h2>
                <div class="announcement-container">
                    <div class="announcement-item">
                        <strong>CCS Admin | 2025-Feb-25</strong>
                        <p>UC did it again.</p>
                    </div>
                    <div class="announcement-item">
                        <strong>CCS Admin | 2025-Feb-03</strong>
                        <p>The College of Computer Studies will open the registration of students for the Sit-in privilege starting tomorrow. Thank you! <br> <em>Lab Supervisor</em></p>
                    </div>
                    <div class="announcement-item">
                        <strong>CCS Admin | 2024-May-08</strong>
                        <p><strong>Important Announcement:</strong> We are excited to announce the launch of our new website! 🎉 Explore our latest products and services now!</p>
                    </div>
                </div>
            </div>


            <!-- Rules & Regulations -->
            <div class="rules-section">
                <h2>📜 Rules and Regulations</h2>
                <h3>University of Cebu</h3>
                <h4>COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
                <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
                <ul>
                    <li>Maintain silence, decorum, and discipline inside the lab.</li>
                    <li>Games are not allowed inside the lab.</li>
                    <li>Internet surfing is allowed only with the instructor’s permission.</li>
                    <li>Maintain silence, decorum, and discipline inside the lab.</li>
                    <li>Games are not allowed inside the lab.</li>
                    <li>Internet surfing is allowed only with the instructor’s permission.</li>
                    <li>Maintain silence, decorum, and discipline inside the lab.</li>
                    <li>Games are not allowed inside the lab.</li>
                    <li>Internet surfing is allowed only with the instructor’s permission.</li>
                </ul>
            </div>
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
