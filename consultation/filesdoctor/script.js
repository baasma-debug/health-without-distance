

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
            
        // Fetch patients list and show only on left panel
        fetch('patients_list.php')
            .then(r => r.json())
            .then(json => {
                clearTimeout(timeoutId); // Cancel timeout if request completes
                const leftPanel = document.getElementById('inner_left_pannel');
                const rightPanel = document.getElementById('inner_right_pannel');
                
                if (json.success && json.data.length > 0) {
                    let html = '<div style="padding: 10px; overflow-y: auto; height: 100%;"><h3>Patients</h3>';
                    
                  
                    json.data.forEach(patient => {
                        html += `
                            <div class="patient-item" data-patient-id="${patient.id}" 
                                 style="padding: 10px; border-bottom: 1px solid #ccc; cursor: pointer; background: #f9f9f9; margin: 5px 0; border-radius: 4px; animation: appear 0.5s ease-out;">
                                <strong>${patient.full_name}</strong><br>
                            </div>
                        `;
                    });
                    html += '</div>';
                    leftPanel.innerHTML = html;
                    rightPanel.innerHTML = ''; // clear right panel
                    
                    // open chat on click
                    document.querySelectorAll('.patient-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const patientId = this.getAttribute('data-patient-id');
                            const patientName = this.querySelector('strong').textContent;
                            loadChat(patientId, patientName);
                        });
                    });
                } else {
                    leftPanel.innerHTML = '<p style="padding: 20px; color: #999;">don\'t have any patients</p>';
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

    
               

    let chatInterval = null;
    function loadChat(patientId, patientName) {
        // switch to chat view (radio button may control visibility via CSS)
        start_chat();

        // highlight the selected patient
        document.querySelectorAll('.patient-item').forEach(p => p.style.background = '#f9f9f9');
        document.querySelector(`[data-patient-id="${patientId}"]`).style.background = '#d4e8f7';

        // clear any previous polling
        if (chatInterval) {
            clearInterval(chatInterval);
            chatInterval = null;
        }

        // Load chat interface in right panel
        const rightPanel = document.getElementById('inner_right_pannel');
        rightPanel.innerHTML = `
         
            <div style=" border: solid thin #ccc; overflow-y:scroll; height:500px; display: flex; flex-direction: column; padding: 10px;">
                <h3>Chat with ${patientName} <img src="../ui/icon/telephone.png" alt="Call" style="width: 20px; height: 20px; margin-left: 10px;" /><img src="../ui/icon/video.png" alt="Video Call" style="width: 20px; height: 20px; margin-left: 10px;" /></h3>
                <div id="chat_messages" style="border: solid #ccc; padding: 10px; flex: 1; overflow-y: auto; margin-bottom: 10px;">
                <img style="width: 50px; display: block; margin: 20px auto;" src="../ui/icon/delete.png"/>
                   
                
                <!-- Chat messages will be loaded here -->
                </div>
                <form id="chatForm" style="display: flex;">
                    
                   <label for="fileInput"  style="cursor: pointer;">
                   <img src="../ui/icon/file.png" alt="upload file" style="opacity: 0.8;  width: 40px; height: 40px;" />
                   </label>
                   <input type="file" id="fileInput" name="fileInput" style="display: none;" onchange="sendFile(this.files)" />
                    <input type="text" id="chatInput" placeholder="Type your message..." style="flex:1; padding: 5px;" required>
                    <label for="voiceInput">
                        <img src="../ui/icon/microphone.png" alt="Voice input" style="width: 30px; height: 20px;" />
                    </label>
                    <input type="voice" id="voiceInput" style="display: none;">
                    <button type="submit" style="padding: 5px 10px; margin-left:5px;" aria-label="Send message">
                        <img src="../ui/icon/send-message.png" alt="Send message" style="width: 30px; height: 20px;" />
                    </button>
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
                body: 'patient_id=' + encodeURIComponent(patientId) + '&message=' + encodeURIComponent(msg)
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    messageEl.value = '';
                    loadMessages(patientId);
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
        loadMessages(patientId);

        // poll for new messages every 5 seconds
        chatInterval = setInterval(() => loadMessages(patientId), 5000);
    }

    function loadMessages(patientId) {
        fetch('chat.php?patient_id=' + patientId)
            .then(r => r.json())
            .then(json => {
                const container = document.getElementById('chat_messages');
                if (json.success) {
                    let html = '';
                        json.data.forEach((m) => {
                            const cls = m.sender === 'doctor' ? 'doctor' : 'patient';
                            
                            // Check if this is a file message
                            let messageContent = m.message;
                            if (m.files && m.files.trim() !== '') {
                                // This is a file message - create download link or preview
                                const fileName = m.files.split('/').pop();
                                const fileExtension = fileName.split('.').pop().toLowerCase();
                                
                                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                                    // Image file - show preview
                                    messageContent = `${m.message}<br><img src="${m.files}" alt="${fileName}" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;" />`;
                                } else {
                                    // Other file - show download link
                                    messageContent = `${m.message}<br><a href="${m.files}" download="${fileName}" style="color: #007bff; text-decoration: none; padding: 5px 10px; border: 1px solid #007bff; border-radius: 4px; margin-top: 5px; display: inline-block;">📎 Download ${fileName}</a>`;
                                }
                            }
                            
                            html += `<div class="message ${cls}" style="position:relative;">${messageContent}
                        <img src="../ui/icon/delete.png" alt="Delete message" title="Delete message" style="width:18px;height:18px;position:absolute;top:5px;left:5px;cursor:pointer;" onclick="deleteMessage(${m.id}, ${patientId})" />
                    </div>`;
                        // Add deleteMessage function
                        window.deleteMessage = function(messageId, patientId) {
                            if (!confirm('Are you sure you want to delete this message?')) return;
                            fetch('deleted_message.php', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ message_id: messageId, patient_id: patientId })
                            })
                            .then(r => r.json())
                            .then(resp => {
                                if (resp.success) {
                                    loadMessages(patientId);
                                } else {
                                    alert('Error deleting message: ' + resp.message);
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Failed to delete message');
                            });
                        }
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
                    // select the current specialty option
                    const specialty = `<?php echo addslashes($doctor['specialty']); ?>`;
                    if (specialty) {
                        const sel = document.getElementById('specialty');
                        if (sel) sel.value = specialty;
                    }
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
                                document.getElementById('doctor_name').textContent = formData.get('FullName');
                                document.getElementById('doctor_email').textContent = formData.get('EmailAddress');
                                const specEl = document.getElementById('doctor_specialty');
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
    function sendFile(files) {
        // Check if a patient is selected
        const selectedPatient = document.querySelector('.patient-item[style*="background: #d4e8f7"]');
        if (!selectedPatient) {
            alert("Please select a patient first before uploading files.");
            return;
        }
        
        const patientId = selectedPatient.getAttribute('data-patient-id');
        if (!patientId) {
            alert("Unable to identify the selected patient.");
            return;
        }
        
        var file = files[0];
        if (!file) {
            alert("No file selected.");
            return;
        }
        
        // Show loading indicator
        const fileInput = document.getElementById('fileInput');
        fileInput.disabled = true;
        
        var formData = new FormData();
        var xml = new XMLHttpRequest();
        xml.open("POST", "upload_file.php", true);
        
        formData.append("file", file);
        formData.append("patient_id", patientId); // Include patient ID for server-side processing
        
        xml.onload = function() {
            fileInput.disabled = false; // Re-enable file input
            
            if (xml.status == 200) {
                try {
                    var response = JSON.parse(xml.responseText);
                    if (response.success) {
                        alert("File uploaded successfully!");
                        // Reload messages to show the uploaded file
                        loadMessages(response.patient_id);
                        // Clear the file input
                        fileInput.value = '';
                    } else {
                        alert("Upload failed: " + response.message);
                    }
                } catch (e) {
                    alert("Upload completed, but response parsing failed.");
                    console.error("Response parsing error:", e);
                }
            } else {
                alert("Upload failed with status: " + xml.status);
            }
        };
        
        xml.onerror = function() {
            fileInput.disabled = false;
            alert("Upload failed due to network error.");
        };
        
        xml.send(formData);
    }
