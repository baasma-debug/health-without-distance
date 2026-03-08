<?php
session_start();
// Redirect to login if not authenticated
if (!isset($_SESSION['patient_id'])) {
    header("Location: register1.php");
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

// Fetch patient info
$patient_id = $_SESSION['patient_id'];
$stmt = $conn->prepare("SELECT full_name, email FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If patient not found (shouldn't happen), logout
if (!$patient) {
    session_destroy();
    header("Location: register1.php");
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
        #wrapper{
           max-width: 900px;
           min-height: 500px;
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
            background-color: rgb(122, 179, 243);
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
            background-color:rgb(122, 179, 243);;
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
            border: 0.5px solid black;
        }
        #inner_right_pannel{
            background-color:white;
            flex:2;
            min-height: 430px;
            transition: all 2s ease;
            border: 0.5px solid black;
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
            /* margin: 10px; */
            width: 30%;
           
        }
        .loader_off{
            display: none;
           
        }
        /* chat message bubbles */
        .message.doctor {
            text-align: right;
            background-color: rgb(122, 179, 243);
            margin: 20px;
            height: 50px;
            color: #000;
            padding: 2px;
            padding-right: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            
        }
        .message.patient {
            text-align: left;
            background-color: rgb(122, 179, 243);
            padding: 5px;
            margin: 4px 0;
            width: 70px;
            height: 70px;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div id="left_pannel">
            <div id="user_info" style="padding: 10px;">
                <img id="profile_img" src="../ui/image/user.png"/>
                <br>
                <span id="patient_name"><?php echo htmlspecialchars($patient['full_name']); ?></span>
                <br>
                
                <span id="patient_email" style="font-size: 12px;"><?php echo htmlspecialchars($patient['email']); ?></span>
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
              Patients Consultation
                
            
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
    <!-- Scripts for contact and patient management -->
    <script>
    function loadContact() {
        // Show loader
        const loader = document.getElementById('loader_hoder');
        loader.classList.remove('loader_off');
        loader.classList.add('loader_on');
        
        // Set 30 second timeout
        const timeoutId = setTimeout(() => {
            loader.classList.add('loader_off');
            loader.classList.remove('loader_on');
            document.getElementById('inner_left_pannel').innerHTML = '<p style="padding: 20px; color: red;">Request timeout. Please try again.</p>';
        }, 30000); // 30 seconds
            
        // Fetch doctors list and show only on left panel
        fetch('doctors_list.php')
            .then(r => r.json())
            .then(json => {
                clearTimeout(timeoutId); // Cancel timeout if request completes
                const leftPanel = document.getElementById('inner_left_pannel');
                const rightPanel = document.getElementById('inner_right_pannel');
                
                if (json.success && json.data.length > 0) {
                    let html = '<div style="padding: 10px; overflow-y: auto; height: 100%;"><h3>Doctors</h3>';
                    
                  
                    json.data.forEach(doctor => {
                        html += `
                            <div class="doctor-item" data-doctor-id="${doctor.id}" 
                                 style="padding: 10px; border-bottom: 1px solid #ccc; cursor: pointer; background: #f9f9f9; margin: 5px 0; border-radius: 4px; animation: appear 0.5s ease-out;">
                                <strong>${doctor.full_name}</strong><br>
                            </div>
                        `;
                    });
                    html += '</div>';
                    leftPanel.innerHTML = html;
                    rightPanel.innerHTML = ''; // clear right panel
                    
                    // open chat on click
                    document.querySelectorAll('.doctor-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const doctorId = this.getAttribute('data-doctor-id');
                            const doctorName = this.querySelector('strong').textContent;
                            loadChat(doctorId, doctorName);
                        });
                    });
                } else {
                    leftPanel.innerHTML = '<p style="padding: 20px; color: #999;">don\'t have any doctors</p>';
                    rightPanel.innerHTML = '';
                }
                
                // Hide loader
                loader.classList.add('loader_off');
                loader.classList.remove('loader_on');
            })
            .catch(err => {
                clearTimeout(timeoutId); // Cancel timeout if error occurs
                console.error(err);
                document.getElementById('inner_left_pannel').innerHTML = '<p>Failed to load patients list.</p>';
                document.getElementById('inner_right_pannel').innerHTML = '';
                
                // Hide loader
                loader.classList.add('loader_off');
                loader.classList.remove('loader_on');
            });
    }

    function loadDoctorDetails(doctorId) {
        // Fetch doctor details
        fetch('contact.php?doctor_id=' + doctorId)
            .then(r => r.json())
            .then(json => {
                const rightPanel = document.getElementById('inner_right_pannel');
                if (json.success) {
                    const d = json.data;
                    rightPanel.innerHTML = `
                        <div style="padding: 20px; overflow-y: auto;">
                            <h3>${d.full_name}</h3>
                            <hr>
                            <p><strong>email:</strong> ${d.email}</p>
                            <p><strong>phone:</strong> ${d.phone}</p>
                            <p><strong>date of birth:</strong> ${d.date_of_birth}</p>
                            <p><strong>gender:</strong> ${d.gender}</p>
                            <p><strong>address:</strong> ${d.address || 'not specified'}</p>
                        </div>
                         
                    `;
                } else {
                    rightPanel.innerHTML = `<p style="padding: 20px; color: red;">${json.message}</p>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('inner_right_pannel').innerHTML = '<p style="padding: 20px; color: red;">Error loading patient details.</p>';
            });
    }

    let chatInterval = null;
    function loadChat(doctorId, doctorName) {
        // switch to chat view (radio button may control visibility via CSS)
        start_chat();

        // highlight the selected doctor
        document.querySelectorAll('.doctor-item').forEach(p => p.style.background = '#f9f9f9');
        document.querySelector(`[data-doctor-id="${doctorId}"]`).style.background = '#d4e8f7';

        // clear any previous polling
        if (chatInterval) {
            clearInterval(chatInterval);
            chatInterval = null;
        }

        // Load chat interface in right panel
        const rightPanel = document.getElementById('inner_right_pannel');
        rightPanel.innerHTML = `
            <div style="padding: 20px; overflow-y: auto; height:90%; display: flex; flex-direction: column;">
                <h3>Chat with ${doctorName}</h3>
                <div id="chat_messages" style="border: 1px solid #ccc; padding: 10px; flex: 1; overflow-y: auto; margin-bottom: 10px;">
                <img style="width: 50px; display: block; margin: 20px auto;" src="../ui/icon/delete.png"/>
                    <!-- Chat messages will be loaded here -->
                </div>
                <form id="chatForm" style="display: flex;">
                    <input type="text" id="chatInput" placeholder="Type your message..." style="flex:1; padding: 5px;" required>
                    <button type="submit" style="padding: 5px 10px; margin-left:5px;">Send</button>
                </form>
            </div>
        `;

        // attach submission handler
        const form = document.getElementById('chatForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageEl = document.getElementById('chatInput');
            const msg = messageEl.value.trim();
            if (!msg) return;

            fetch('chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'doctor_id=' + encodeURIComponent(doctorId) + '&message=' + encodeURIComponent(msg)
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    messageEl.value = '';
                    loadMessages(doctorId);
                } else {
                    alert('Error sending message: ' + resp.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to send message');
            });
        });

        // initial load of existing messages
        loadMessages(doctorId);

        // poll for new messages every 5 seconds
        chatInterval = setInterval(() => loadMessages(doctorId), 5000);
    }

    function loadMessages(doctorId) {
        fetch('chat.php?doctor_id=' + doctorId)
            .then(r => r.json())
            .then(json => {
                const container = document.getElementById('chat_messages');
                if (json.success) {
                    let html = '';
                    json.data.forEach(m => {
                        const cls = m.sender === 'doctor' ? 'doctor' : 'patient';
                        html += `<div class=\"message ${cls}\">${m.message}</div>`;
                    });
                    container.innerHTML = html;
                    container.scrollTop = container.scrollHeight;
                } else {
                    container.innerHTML = '<p style="color:red;">' + json.message + '</p>';
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    

    
    function loadSettings() {
        const loader = document.getElementById('loader_hoder');
        loader.classList.remove('loader_off');
        loader.classList.add('loader_on');

        fetch('settings.php')
            .then(r => r.json())
            .then(json => {
                loader.classList.add('loader_off');
                loader.classList.remove('loader_on');
                const rightPanel = document.getElementById('inner_right_pannel');
                if (json.success && json.data_type === 'settings') {
                    rightPanel.innerHTML = json.data;
                    // handle form submission
                    const form = document.getElementById('doctorSettingsForm');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        const submitBtn = form.querySelector('button[type=submit]');
                        submitBtn.disabled = true;
                        loader.classList.remove('loader_off');
                        loader.classList.add('loader_on');
                        fetch('settings.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(r => r.json())
                        .then(resp => {
                            loader.classList.add('loader_off');
                            loader.classList.remove('loader_on');
                            submitBtn.disabled = false;
                            if (resp.success) {
                                alert(resp.message);
                                // update left panel info
                                document.getElementById('patient_name').textContent = formData.get('full_name');
                                document.getElementById('patient_email').textContent = formData.get('email');
                                const specEl = document.getElementById('patient_specialty');
                                if (specEl) specEl.textContent = formData.get('Medical_Specialty');
                            } else {
                                alert('Error: ' + resp.message);
                            }
                        })
                        .catch(err => {
                            loader.classList.add('loader_off');
                            loader.classList.remove('loader_on');
                            submitBtn.disabled = false;
                            console.error(err);
                            alert('Failed to save settings');
                        });
                    });
                    document.getElementById('cancelSettings').addEventListener('click', function() {
                        rightPanel.innerHTML = '';
                    });
                } else {
                    rightPanel.innerHTML = '<p style="padding: 20px; color: red;">Unable to load settings.</p>';
                }
            })
            .catch(err => {
                console.error(err);
                loader.classList.add('loader_off');
                loader.classList.remove('loader_on');
                document.getElementById('inner_right_pannel').innerHTML = '<p style="padding: 20px; color: red;">Error loading settings.</p>';
            });
    }

    function start_chat(){
        var radio_chat = document.getElementById("radio_chat");
        radio_chat.checked = true;
    }
    </script>
</body>
</html>
