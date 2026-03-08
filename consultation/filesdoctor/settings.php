
<?php
// settings.php - returns a settings form snippet as JSON and handles updates
session_start();
header('Content-Type: application/json');

// ensure doctor is authenticated
if (empty($_SESSION['doctor_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$doctor_id = intval($_SESSION['doctor_id']);

// open database connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle update request
    $fullName  = $conn->real_escape_string(trim($_POST['FullName'] ?? ''));
    $email     = $conn->real_escape_string(trim($_POST['EmailAddress'] ?? ''));
    $specialty = $conn->real_escape_string(trim($_POST['Medical_Specialty'] ?? ''));
    $phone     = $conn->real_escape_string(trim($_POST['Phone_Number'] ?? ''));
    $password  = trim($_POST['Password'] ?? '');
    $confirm   = trim($_POST['Confirm_Password'] ?? '');

    // validate required
    if (empty($fullName) || empty($email) || empty($specialty) || empty($phone)) {
        echo json_encode(["success" => false, "message" => "Please fill all required fields."]);
        exit;
    }

    // ensure email not used by another doctor
    $checkStmt = $conn->prepare("SELECT id FROM doctors WHERE email = ? AND id != ?");
    $checkStmt->bind_param('si', $email, $doctor_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult && $checkResult->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "This email is already in use by another account."]);
        exit;
    }
    $checkStmt->close();

    // start building update query
    $fields = [];
    $params = [];
    $types  = '';

    $fields[] = "full_name = ?"; $params[] = $fullName; $types .= 's';
    $fields[] = "email = ?"; $params[] = $email; $types .= 's';
    $fields[] = "specialty = ?"; $params[] = $specialty; $types .= 's';
    $fields[] = "phone = ?"; $params[] = $phone; $types .= 's';

    if ($password !== '') {
        if ($password !== $confirm) {
            echo json_encode(["success" => false, "message" => "Passwords do not match."]);
            exit;
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $fields[] = "password = ?"; $params[] = $hashed; $types .= 's';
    }

    $params[] = $doctor_id;
    $types .= 'i';

    $sql = "UPDATE doctors SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Settings saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// GET request: retrieve doctor information to prefill form
$stmt = $conn->prepare("SELECT full_name, email, specialty, phone FROM doctors WHERE id = ?");
$stmt->bind_param('i', $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$doctor) {
    echo json_encode(["success" => false, "message" => "Doctor not found"]);
    exit;
}
// build HTML with current values
$mydata = <<<'HTML'
<style>
    .register-form {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .form-row {
        display: flex;
        gap: 20px;
    }
    .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .form-group input,
    .form-group select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }
    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }
    .btn-outline {
        background-color: transparent;
        border: 2px solid #007bff;
        color: #007bff;
    }
    .btn-outline.cancel {
        border-color: #dc3545;
        color: #dc3545;
    }
    </style>
<div class="register-form">
    <form id="doctorSettingsForm" method="post">
        <div class="form-row">
            <div class="form-group">
                <label for="fullName"><i class="fas fa-user"></i> Full Name *</label>
                <input type="text" id="fullName" name="FullName" value="%FULL%" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                <input type="email" id="email" name="EmailAddress" value="%EMAIL%" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="Password" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label for="confirmPassword"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirmPassword" name="Confirm_Password" placeholder="Confirm new password">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="specialty"><i class="fas fa-stethoscope"></i> Medical Specialty *</label>
                <select id="specialty" name="Medical_Specialty" required>
                    <option value="" disabled>Select your specialty</option>
                    <option value="Pediatrics">Pediatrics</option>
                    <option value="Orthopedics">Orthopedics</option>
                    <option value="General Practitioner">General Practitioner</option>
                </select>
            </div>
            <div class="form-group">
                            <label for="gender"><i class="fas fa-genderless"></i>gender</label>
                            <select id="gender" name="Gender" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                </select>
                        </div>
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone-alt"></i> Phone Number *</label>
                <input type="tel" id="phone" name="Phone_Number" value="%PHONE%" required>
            </div>
        </div>

        <div class="form-footer">
            <button type="button" class="btn btn-outline cancel" id="cancelSettings">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </form>
</div>
HTML;

// substitute values securely
echo json_encode([
    "success" => true,
    "data_type" => "settings",
    "data" => str_replace([
        '%FULL%', '%EMAIL%', '%PHONE%'
    ], [
        htmlspecialchars($doctor['full_name'], ENT_QUOTES),
        htmlspecialchars($doctor['email'], ENT_QUOTES),
        htmlspecialchars($doctor['phone'], ENT_QUOTES)
    ], $mydata)
]);
exit;

