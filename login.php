
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
    $email = $conn->real_escape_string($_POST['EmailAddress']);
    $password = $_POST['password'];

    // Find the user by email
    $sql = "SELECT id, password FROM doctors WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Login success: Store ID in session
            $_SESSION['doctor_id'] = $user['id'];
            header("Location: Doctor.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with that email.";
    }
}
$conn->close();
?>