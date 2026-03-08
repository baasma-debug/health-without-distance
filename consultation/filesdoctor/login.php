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

// Determine input type (form data or JSON)
$input = json_decode(file_get_contents('php://input'), true);
if ($input) {
    // AJAX JSON request
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $is_ajax = true;
} else {
    // Normal form POST
    $email = $_POST['EmailAddress'] ?? '';
    $password = $_POST['password'] ?? '';
    $is_ajax = false;
}

if ($email && $password) {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['doctor_id'] = $user['id'];
            if ($is_ajax) {
                echo json_encode(['success' => true, 'redirect' => 'index.php']);
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
} else {
    $error = "Email and password required.";
}

$stmt->close();
$conn->close();

// Return error response
if ($is_ajax) {
    echo json_encode(['success' => false, 'error' => $error]);
} else {
    // For traditional form, we need to show error on login page
    // You could redirect back with an error message, but for simplicity we'll just echo
    echo $error;
}
?>