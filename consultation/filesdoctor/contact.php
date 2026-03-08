<?php
session_start();
header('Content-Type: application/json');

// Get patient ID from GET parameter or session
$patient_id = intval($_GET['patient_id'] ?? $_SESSION['patients_id'] ?? 0);
if ($patient_id === 0) {
    echo json_encode(["success" => false, "message" => "Not authenticated or missing patient id"]);
    exit;
}

// establish database connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// fetch patient record
$stmt = $conn->prepare(
    "SELECT id, full_name, email, phone, date_of_birth, gender, address
     FROM patients
     WHERE id = ?"
   
);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $data = $result->fetch_assoc();
    // include original data_type field for compatibility
    $data['data_type'] = 'register1.php';
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "No contact found"]);
}

$stmt->close();
$conn->close();

?>
