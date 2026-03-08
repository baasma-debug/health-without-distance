<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = 'localhost';
$user = 'root';
$password = '';
$database = "health_db";
$db = mysqli_connect($server, $user, $password, $database);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    
    header('Content-Type: application/json');
    
    $response = ['success' => false, 'message' => ''];
    
    // Get and sanitize input – use correct field names from form
    $full_name = mysqli_real_escape_string($db, trim($_POST['full_name'] ?? ''));
    $email = mysqli_real_escape_string($db, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($db, trim($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $date_of_birth = mysqli_real_escape_string($db, trim($_POST['date_of_birth'] ?? ''));
    $gender = mysqli_real_escape_string($db, trim($_POST['gender'] ?? ''));
    $address = isset($_POST['address']) ? mysqli_real_escape_string($db, trim($_POST['address'])) : '';
    
    // Server-side validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($date_of_birth) || empty($gender)) {
        $response['message'] = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $response['message'] = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format';
    } else {
        // Check if email already exists using a prepared statement
        $stmt = $db->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response['message'] = 'Email already registered. Please use a different email.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user using prepared statement
            $stmt2 = $db->prepare(
                "INSERT INTO patients (full_name, email, phone, password, date_of_birth, gender, address) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt2->bind_param(
                "sssssss",
                $full_name,
                $email,
                $phone,
                $hashed_password,
                $date_of_birth,
                $gender,
                $address
            );

            if ($stmt2->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful! Welcome aboard.';
            } else {
                $response['message'] = 'Registration failed: ' . $stmt2->error;
            }
            $stmt2->close();
        }
        $stmt->close();
    }
    
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Your existing CSS remains unchanged */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .page-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 20px auto;
            overflow: hidden;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-number {
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .page-title small {
            display: block;
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .section {
            padding: 30px;
        }
        
        .flex-row {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .icon-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .note-sim {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            color: #667eea;
            font-size: 0.9rem;
        }
        
        .note-sim i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .flex-row {
                flex-direction: column;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .icon-circle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- PAGE 1 - REGISTER -->
    <div class="page-card">
        <div class="page-header">
            <span class="page-number">1</span>
            <span class="page-title">Register <small>Create account</small></span>
        </div>
        <div class="section">
            <form id="registerForm">
                <div class="flex-row" style="justify-content: space-between;">
                    <div style="width: 100%; max-width: 600px;">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full_name" placeholder="Enter full name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" placeholder="example@domain.com" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="phone" placeholder="Phone number" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1;">
                                <label><i class="fas fa-lock"></i> Password</label>
                                <input type="password" name="password" id="password" placeholder="Password" required>
                            </div>
                            <div class="form-group" style="flex:1;">
                                <label><i class="fas fa-check-circle"></i> Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1;">
                                <label><i class="fas fa-calendar"></i> Date of Birth</label>
                                <input type="date" name="date_of_birth" required>
                            </div>
                            
                        </div>
                        <div class="form-group" style="flex:1;">
                                <label><i class="fas fa-venus-mars"></i> Gender</label>
                                <select name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-pin"></i> Address (optional)</label>
                            <input type="text" name="address" placeholder="Address (optional)">
                        </div>
                        <input type="hidden" name="register" value="1">
                        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; margin-top: 12px; width: 100%; justify-content: center;">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </div>
                    <div class="icon-circle" style="width:100px; height:100px; border-radius: 30px; align-self: center;">
                        <i class="fas fa-notes-medical fa-3x"></i>
                    </div>
                </div>
            </form>
            <div class="note-sim"><i class="fas fa-check-circle"></i> Order: Full Name → Email → Phone → Password → Confirm → DOB → Gender → Address (optional) → Create Account</div>
        </div>
    </div>

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters long!');
            return;
        }
        
        const formData = new FormData(this);
        
        // explicitly send to this script so browsers won't default to an empty string URL
        fetch('register1.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                this.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    });
    </script>
</body>
</html>