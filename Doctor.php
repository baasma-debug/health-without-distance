<?php
session_start();
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login1.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$result = $conn->query("SELECT * FROM doctors WHERE id = $doctor_id");
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health without distance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    

    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h1>Health without distance</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="nav-link active" data-page="home"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#" class="nav-link" data-page="profile"><i class="fas fa-user-md"></i> Profile</a></li>
                    <li><a href="#"onclick="window.location.href='consultation/filesdoctor/index.php'" class="nav-link" data-page="consultation"><i class="fas fa-comments"></i> Consultation</a></li>
                    <li><a href="#"  onclick="window.location.href='avai.html'" class="nav-link" data-page="availability"><i class="fas fa-calendar-alt"></i> Availability</a></li>
                </ul>
            </nav>
            <div class="user-actions" id="userActions">
                <a href="logut.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="login1.php" class="btn btn-primary"><i class="fas fa-user-cog"></i> Login</a>
                <a href="register.php" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Register</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Home Page -->
        <div class="page active" id="home">
            <div class="hero">
                <h1>Comprehensive Healthcare Management Platform</h1>
                <p>Connect with patients, manage appointments, and provide remote consultations with our all-in-one healthcare solution.</p>
            </div>
            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-user-md"></i>
                    <h3>Doctor Profile</h3>
                    <p>Create a professional profile showcasing your credentials, specialties, and experience to attract patients.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-video"></i>
                    <h3>Remote Consultations</h3>
                    <p>Conduct secure video consultations and messaging with patients from anywhere at any time.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Appointment Management</h3>
                    <p>Easily manage your schedule, set availability, and handle bookings with our intuitive calendar system.</p>
                </div>
            </div>
        </div>

        <!-- Doctor Profile Page -->
        <div class="page" id="profile">
            <h1 class="page-title"><i class="fas fa-user-md"></i> Professional Profile</h1>
            <div class="doctor-profile">
                <!-- Sidebar -->
                <div class="profile-sidebar">
                    <div class="profile-image">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="profile-info">
                        <h2 id="doctorName">Dr. <?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <p class="specialty" id="doctorSpecialty"><?php echo htmlspecialchars($user['specialty']); ?></p>
                    </div>
                    
                </div>

                <!-- Details Section -->
                <div class="profile-details">
                    <!-- General Information -->
                    <div class="profile-section">
                        <h3><i class="fas fa-info-circle"></i> General Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Full Name:</strong>
                                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>gender:</strong>
                                <span><?php echo htmlspecialchars($user['gender']); ?></span>
                            </div>
                            
                            <div class="info-item">
                                <strong>Specialty:</strong>
                                <span><?php echo htmlspecialchars($user['specialty']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Phone:</strong>
                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Clinic Address:</strong>
                                <span><?php echo htmlspecialchars($user['clinic_address']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Bio -->
                    <div class="profile-section">
                        <h3><i class="fas fa-file-medical"></i> Professional Bio</h3>
                        <p id="doctorBio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <script src="script1.js"></script>
</body>
</html>