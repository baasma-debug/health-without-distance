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
       body{
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
       } 
        
        #wrapper{
           max-width: 900px;
           min-height: 500px;
           max-height: 630px;
           display: flex;
           margin: auto;
           color: black;
           font-size: 13px;
        }
        #left_pannel{
           min-height: 500px;
           background-color: hsl(228, 24%, 96%);
           flex: 1;
           text-align: center;
           border: 10px; 
        }
        #profile_img{
            width:80%;
            border:solid thin white;
            border-radius:50%;
            margin: 10px;
        }
        #left_pannel label{
            width: 100%;
            height: 20px;
            display: block;
            background-color: #50a4c0;
            border-bottom: solid thin white;
            cursor: pointer;
            padding: 5px;
            transition:all 1s ease ;
        }
        #left_pannel label:hover{
            background-color: rgb(221, 221, 224);
        }
        #left_pannel label img{
            float: right;
            width: 25px;
        }
        #right_pannel{
            min-height: 500px;
            background-color: blue;
            flex: 4;
        }
        #header{
            background-color:#50a4c0;
            height: 70px;
            font-size: 40px;
            text-align: center;
            font-family: headFont;
            position: relative;
        }
        #inner_left_pannel{
            background-color: rgb(254, 254, 255);
            flex:1;
            min-height: 430px;
            max-height: 550px;
            border: 0.5px solid black;
        }
        #inner_right_pannel{
            background-color:white;
            flex:2;
            min-height: 430px;
            transition: all 2s ease;
            border: 0.5px solid black;
            max-height: 550;
        }
        #radio_contact:checked ~ #inner_right_pannel{
            flex:0;
        }
        /* when chat is selected make sure panels are visible */
        #radio_chat:checked ~ #inner_left_pannel {
            flex:1;
        }
        #radio_chat:checked ~ #inner_right_pannel {
            flex:2;
        }
        #contact{
            width: 150px;
            height: 170px;
            margin: 10px ;
            display: inline-block;
            overflow: hidden;
            vertical-align: top;
             
        }
        #contact img{
            width: 100%;
            height: 100%;
                

        }
        .loader_on{
            position: absolute;
           //margin: 10px;
            width: 30%;
           
        }
        .loader_off{
            display: none;
           
        }
        /* chat message bubbles */
        .message.doctor {
            text-align: right;
            background-color: #50a4c0;
            margin: 10px;
            height: 30px;
            color: #000;
            padding: 2px;
            padding-right: 8px;
           
            
        }
        .message.patient {
            text-align: left;
            background-color: rgb(67, 131, 204);
            padding: 5px;
            margin: 4px 0;
            width: 70px;
            height: 70px;}
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
