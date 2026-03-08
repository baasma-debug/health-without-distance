<?php
header('Content-Type: application/json');

// Start session to get doctor info
session_start();

// Check if user is logged in as doctor
if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Check if patient_id is provided
$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : null;
if (!$patient_id) {
    echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
    exit;
}

$file = $_FILES['file'];

// Validate file size (max 10MB)
$maxFileSize = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 10MB']);
    exit;
}

// Validate file type (allow common file types)
$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'application/pdf',
    'text/plain',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'doctor_' . $doctor_id . '_patient_' . $patient_id . '_' . time() . '_' . uniqid() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Connect to database
    $host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "health_db";
    $conn = new mysqli($host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Insert file message into chat
    $fileMessage = "📎 File uploaded: " . $file['name'];
    $stmt = $conn->prepare("INSERT INTO messages (doctor_id, patient_id, sender, message, files, created_at) VALUES (?, ?, 'doctor', ?, ?, NOW())");
    $stmt->bind_param("iiss", $doctor_id, $patient_id, $fileMessage, $filepath);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        // File uploaded and message saved successfully
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully',
            'filename' => $filename,
            'filepath' => $filepath,
            'patient_id' => $patient_id
        ]);
    } else {
        $stmt->close();
        $conn->close();
        // Delete the uploaded file since database insert failed
        unlink($filepath);
        echo json_encode(['success' => false, 'message' => 'Failed to save file message to database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>
