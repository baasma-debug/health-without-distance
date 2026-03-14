<?php
session_start();

$host    = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['EmailAddress'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic empty check
    if (empty($email) || empty($password)) {
        header("Location: login1.php?error=empty");
        exit();
    }

    // Prepared statement — safe against SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ Login success
            session_regenerate_id(true);
            $_SESSION['doctor_id'] = $user['id'];
            header("Location: Doctor.php");
            exit();
        } else {
            //  Wrong password → back to login with error
            header("Location: login1.php?error=invalid");
            exit();
        }
    } else {
        //  No account found → back to login with error
        header("Location: login1.php?error=invalid");
        exit();
    }

    $stmt->close();

} else {
    // Direct GET access → redirect to login page
    header("Location: login1.php");
    exit();
}

$conn->close();
?>