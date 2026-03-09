<?php
session_start();
// Redirect to login if not authenticated
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login1.php");
    exit;
}

// Database connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "health_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor info
$doctor_id = $_SESSION['doctor_id'];
$stmt = $conn->prepare("SELECT full_name, email, specialty FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If doctor not found (shouldn't happen), logout
if (!$doctor) {
    session_destroy();
    header("Location: login1.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health without distance</title>
    <style type="text/css">
        /* (Keep all original styles exactly as they were) */
       
         
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #222;
        }

        /* ── Outer wrapper ── */
        #wrapper {
            max-width: 960px;
            min-height: 600px;
            display: flex;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(10, 61, 92, 0.18);
        }

        /* ── Left sidebar ── */
        #left_pannel {
            width: 200px;
            flex-shrink: 0;
            background-color: #ffffff;
            color: #1a6fa8;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #d0e8f5;
        }

        #user_info {
            padding: 16px 10px 10px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #profile_img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #d0e8f5;
            object-fit: cover;
            margin-bottom: 8px;
        }

        #doctor_name {
            font-weight: 700;
            font-size: 14px;
            color: #0a3d5c;
            display: block;
            margin-bottom: 2px;
        }

        #doctor_specialty {
            font-size: 12px;
            color: #50a4c0;
            display: block;
            margin-bottom: 2px;
        }

        #doctor_email {
            font-size: 11px;
            color: #888;
            display: block;
            margin-bottom: 16px;
            word-break: break-all;
        }

        /* Nav labels */
        #left_pannel label {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #b8dfeb;
            border-bottom: 1px solid #d0e8f5;
            cursor: pointer;
            padding: 10px 55px;
            color: #1a6fa8;
            font-weight: 600;
            font-size: 13px;
            transition: background-color 0.2s ease;
        }

        #left_pannel label:hover {
            background-color: #e8f4fb;
        }

        #left_pannel label img {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        /* ── Right main area ── */
        #right_pannel {
            flex: 1;
            min-width: 0;
            background-color: #f0f8fd;
            display: flex;
            flex-direction: column;
        }

        /* ── Header bar ── */
        #header {
            background: linear-gradient(135deg, #0a3d5c 0%, #1a7fa8 50%, #0d5578 100%);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border-bottom: 3px solid #5bbcd9;
            box-shadow: 0 4px 18px rgba(10, 61, 92, 0.18);
            position: relative;
            flex-shrink: 0;
        }

        #loader_hoder {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
        }

        #loader_hoder img {
            width: 48px;
            height: 48px;
        }

        .loader_off {
            display: none !important;
        }

        /* ── Inner container (split panels) ── */
        #container {
            display: flex;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        #inner_left_pannel {
            width: 220px;
            flex-shrink: 0;
            background-color: #d0eaf7;
            border-right: 1px solid #b0d4e8;
            overflow-y: auto;
            min-height: 500px;
        }

        #inner_right_pannel {
            flex: 1;
            min-width: 0;
            background-color: #ffffff;
            overflow-y: auto;
            min-height: 500px;
        }

        /* ── Patient list items ── */
        .patient-item {
            padding: 10px 14px;
            border-bottom: 1px solid #c2dff0;
            cursor: pointer;
            background: #f0f8fd;
            transition: background 0.15s ease;
            font-size: 13px;
        }

        .patient-item:hover {
            background: #c5e5f5 !important;
        }

        .patient-item.active {
            background: #d4e8f7 !important;
            font-weight: 600;
        }

        /* ── Chat message bubbles ── */
        .message {
            max-width: 70%;
            padding: 8px 12px;
            border-radius: 14px;
            margin: 6px 10px;
            font-size: 13px;
            line-height: 1.4;
            word-wrap: break-word;
            position: relative;
            clear: both;
        }

        .message.doctor {
            background-color: #50a4c0;
            color: #fff;
            float: right;
            border-bottom-right-radius: 4px;
            text-align: left;
        }

        .message.patient {
            background-color: #e8f4fb;
            color: #1a3a4a;
            float: left;
            border-bottom-left-radius: 4px;
            border: 1px solid #c0ddef;
        }

        /* Delete icon on messages */
        .message .delete-btn {
            position:right;
            top: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message:hover .delete-btn {
            opacity: 0.7;
        }

        /* ── Chat form ── */
        #chatForm {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 10px;
            border-top: 1px solid #d0e8f5;
            background: #f8fcff;
            flex-shrink: 0;
        }

        #chatForm label {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        #chatInput {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #c0ddef;
            border-radius: 20px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        #chatInput:focus {
            border-color: #1a7fa8;
        }

        #chatForm button[type="submit"] {
            background: #1a7fa8;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            padding: 0;
        }

        #chatForm button[type="submit"]:hover {
            background: #0a5f82;
        }

        #chatForm button[type="submit"] img {
            width: 18px;
            height: 18px;
            filter: brightness(0) invert(1);
        }

        /* Chat messages scroll area */
        #chat_messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }

        #chat_messages::after {
            content: '';
            display: table;
            clear: both;
        }

        /* ── Contact / settings panel ── */
        #contact {
            width: 150px;
            height: 170px;
            margin: 10px;
            display: inline-block;
            overflow: hidden;
            vertical-align: top;
        }

        #contact img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        
    </style>
</head>
<body>
    <div id="wrapper">
        <div id="left_pannel">
            <div id="user_info" style="padding: 10px;">
                <img id="profile_img" src="../ui/image/user.png"/>
                <br>
                <span id="doctor_name"><?php echo htmlspecialchars($doctor['full_name']); ?></span>
                <br>
                <span id="doctor_specialty"><?php echo htmlspecialchars($doctor['specialty']); ?></span>
                <br>
                <span id="doctor_email" style="font-size: 12px;"><?php echo htmlspecialchars($doctor['email']); ?></span>
                <br><br><br>
                <div>
                    <label id="label_chat" for="radio_chat">chat<img src="../ui/icon/5832617918309535268_109.jpg"/></label>
                    <label id="label_contact" for="radio_contact" onclick="loadContact()">contact<img src="../ui/icon/contact.png"/></label>
                    <label id="label_setting" for="radio_setting" onclick="loadSettings()">settings<img src="../ui/icon/settings.png"/></label>
                    <label id="label_logout" for="radio_logout" onclick="if(confirm('Are you sure you want to log out?')){ window.location.href='logut.php'; }">Logout<img src="../ui/icon/logout.png"/></label>
                </div>

            </div>
            
        </div>
        <div id="right_pannel">
            <div id="header"> 
                <div  id="loader_hoder" class="loader_on"><img style="width: 70px;" src="../ui/icon/Giphy.gif"/> </div>
                Doctor consultation
                
            
            </div>
            <div id="container" style="display: flex;">
                
                <div id="inner_left_pannel">
                    <!-- left-side content (e.g. list of chats or contacts) could go here -->
                </div>
                <input type="radio" id="radio_chat" name="chat" style="display: none;">
                <input type="radio" id="radio_contact" name="chat" style="display: none;">
                <input type="radio" id="radio_setting" name="chat" style="display: none;">
                <div id="inner_right_pannel"></div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
