<?php
session_start();
header('Content-Type: application/json');

// Check if doctor is authenticated
$doctor_id = intval($_SESSION['doctor_id'] ?? 0);
if ($doctor_id === 0) {
    echo json_encode(["success" => false, "message" => "Not authenticated"]);
    exit;
}

// Database connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Fetch all patients
$query = "SELECT id, full_name, email, phone FROM patients ORDER BY full_name ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    echo json_encode(["success" => true, "data" => $patients]);
} else {
    echo json_encode(["success" => false, "message" => "No patients found"]);
}

$conn->close();
?>
