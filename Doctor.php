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
                    <li><a href="#" class="nav-link" data-page="prescription"><i class="fas fa-file-prescription"></i> Prescription</a></li>
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
                <div class="feature-card" onclick="navigateToPage('prescription')" style="cursor:pointer;">
                    <i class="fas fa-file-prescription"></i>
                    <h3>Electronic Prescription</h3>
                    <p>Issue digital prescriptions instantly, add medications with dosage details, and share them securely with patients.</p>
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

        
        <!-- Prescription Page -->
        <div class="page" id="prescription">
            <h1 class="page-title"><i class="fas fa-file-prescription"></i> Electronic Prescription</h1>
            <div class="prescription-wrapper">
                <!-- Prescription Form -->
                <div class="prescription-form-card">
                    <div class="rx-header">
                        <div class="rx-logo">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health without distance</span>
                        </div>
                        <div class="rx-badge">Rx</div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Patient Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Patient Name</label>
                                <input type="text" id="patientName" placeholder="Full name">
                            </div>
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" id="patientAge" placeholder="Age">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select id="patientGender">
                                    <option value="">Select</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="rxDate">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-stethoscope"></i> Diagnosis</h3>
                        <div class="form-group">
                            <textarea id="diagnosisText" placeholder="Write the diagnosis here..." rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-pills"></i> Medications</h3>
                        <div id="medicationsList"></div>
                        <button class="btn btn-add-med" onclick="addMedication()">
                            <i class="fas fa-plus-circle"></i> Add Medication
                        </button>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-notes-medical"></i> Doctor Notes</h3>
                        <div class="form-group">
                            <textarea id="doctorNotes" placeholder="Additional notes or instructions..." rows="3"></textarea>
                        </div>
                    </div>

                    <div class="rx-actions">
                        <button class="btn btn-primary" onclick="previewPrescription()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button class="btn btn-secondary" onclick="printPrescription()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-outline-dark" onclick="clearPrescription()">
                            <i class="fas fa-trash-alt"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Prescription Preview -->
                <div class="prescription-preview-card" id="rxPreview" style="display:none;">
                    <div id="rxPreviewContent"></div>
                </div>
            </div>
        </div>

    </div>

    <script src="script1.js"></script>
</body>
</html>