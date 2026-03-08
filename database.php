<?php
session_start();

$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName  = $conn->real_escape_string(trim($_POST['FullName']));
    $email     = $conn->real_escape_string(trim($_POST['EmailAddress']));
    $pass      = trim($_POST['Password']);
    $confirm   = trim($_POST['Confirm_Password']);
    $specialty = $conn->real_escape_string(trim($_POST['Medical_Specialty']));
    $phone     = $conn->real_escape_string(trim($_POST['Phone_Number']));
    $address   = $conn->real_escape_string(trim($_POST['Clinic_Address']));
    $bio       = $conn->real_escape_string(trim($_POST['Professional_Bio']));
    $gender    = $conn->real_escape_string(trim($_POST['Gender']));
    // Check if passwords match
    if ($pass !== $confirm) {
        die("Error: Passwords do not match.");
    }

    // Check if email already exists
    $checkEmail = $conn->query("SELECT id FROM doctors WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        die("Error: This email is already registered.");
    }

    // Validate required fields
    if (empty($fullName) || empty($email) || empty($pass) || empty($specialty) || empty($phone) || empty($address)) {
        die("Error: Please fill all required fields.");
    }

    // Hash password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO doctors (full_name, email, password, specialty, phone, clinic_address, bio, gender) 
            VALUES ('$fullName', '$email', '$hashed_password', '$specialty', '$phone', '$address', '$bio', '$gender')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['doctor_id'] = $conn->insert_id;
        header("Location: Doctor.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>