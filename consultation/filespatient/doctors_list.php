<?php
session_start();
header('Content-Type: application/json');

// Check if patient is authenticated
$patient_id = intval($_SESSION['patient_id'] ?? 0);
if ($patient_id === 0) {
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
$query = "SELECT id, full_name, email,specialty FROM doctors ORDER BY full_name ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    echo json_encode(["success" => true, "data" => $doctors]);
} else {
    echo json_encode(["success" => false, "message" => "No doctors found"]);
}

$conn->close();
?>
