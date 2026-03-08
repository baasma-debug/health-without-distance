<?php
session_start();
header('Content-Type: application/json');

// Check authentication
$doctor_id = intval($_SESSION['doctor_id'] ?? 0);
if ($doctor_id === 0) {
    echo json_encode(["success" => false, "message" => "Not authenticated"]);
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


    

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $patient_id = intval($_GET['patient_id'] ?? 0);
    if ($patient_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid patient id"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, sender ,receiver, message, files, created_at FROM messages WHERE doctor_id = ? AND patient_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("ii", $doctor_id, $patient_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
    echo json_encode(["success" => true, "data" => $messages]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if ($patient_id <= 0 || $message === '') {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
        exit;
    }
    

    $stmt = $conn->prepare("INSERT INTO messages (doctor_id, patient_id, sender,receiver, message, files) VALUES (?, ?, 'doctor', 'patient', ?, '')");
    $stmt->bind_param("iis", $doctor_id, $patient_id, $message);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save message"]);
    }
    $stmt->close();
    exit;
}



// unsupported method
echo json_encode(["success" => false, "message" => "Unsupported request method"]);
$conn->close();

