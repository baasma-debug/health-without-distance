<?php
session_start();
header('Content-Type: application/json');
// Check authentication
$doctor_id = intval($_SESSION['doctor_id'] ?? 0);
if ($doctor_id <= 0) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
// connect database
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$deleted_sender = 0;
$deleted_receiver = 0;
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message_id = intval($input['message_id'] ?? 0);
    $patient_id = intval($input['patient_id'] ?? 0);
    if ($message_id <= 0 || $patient_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
        exit;
    }

    // Only allow doctor to delete their own messages
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND doctor_id = ? AND patient_id = ?");
    $stmt->bind_param("iii", $message_id, $doctor_id, $patient_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete message or not authorized"]);
    }
    $stmt->close();
    exit;
}
?>