<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register · Health Without Distance</title>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Reset & Variables ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: linear-gradient(135deg, #2b5f70, #4889ac);
            --primary-solid: #2b5f70;
            --primary-dark: #1e4755;
            --secondary: linear-gradient(135deg, #50a4c0, #2b5f70);
            --secondary-solid: #50a4c0;
            --accent: #27AE60;
            --success: #27AE60;
            --error: #ff6b9d;
            --dark: #1A1A2E;
            --gray: #64748B;
            --gray-light: #f1f5f9;
            --white: #ffffff;
            --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 30px rgba(43, 95, 112, 0.2);
            --shadow-xl: 0 30px 50px rgba(43, 95, 112, 0.3);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(72, 137, 172, 0.1) 0%, rgba(184, 220, 232, 0.2) 90%),
                        linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* ===== Main Card ===== */
        .register-card {
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 2.5rem;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: cardFloat 0.8s ease-out;
        }

        @keyframes cardFloat {
            0% { opacity: 0; transform: translateY(30px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===== Header ===== */
        .register-header {
            background: var(--primary);
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .register-header i {
            font-size: 4rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .register-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ===== Form Container ===== */
        .register-form {
            padding: 3rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        label i {
            color: var(--primary-solid);
            font-size: 1.1rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--gray-light);
            border-radius: 1rem;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-solid);
            box-shadow: 0 0 0 4px rgba(43, 95, 112, 0.15);
            transform: scale(1.02);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* ===== Button Styles ===== */
        .form-footer {
            display: flex;
            gap: 1.5rem;
            justify-content: flex-end;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid var(--gray-light);
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 1rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
            z-index: -1;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            border-color: var(--primary-solid);
            background: var(--gray-light);
            transform: translateY(-2px);
        }

        /* Cancel button specific */
        .btn-outline.cancel {
            background: var(--gray);
            border: none;
            color: white;
        }

        .btn-outline.cancel:hover {
            background: var(--dark);
        }

        /* ===== Login Link ===== */
        .login-link {
            text-align: center;
            margin-top: 2.5rem;
            color: var(--gray);
            font-size: 1rem;
        }

        .login-link a {
            color: var(--primary-solid);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border-bottom: 2px solid transparent;
        }

        .login-link a:hover {
            border-bottom-color: var(--primary-solid);
        }

       

        

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .register-header h1 {
                font-size: 2rem;
            }

            .register-form {
                padding: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .form-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .register-card {
                border-radius: 1.5rem;
            }

            .register-header {
                padding: 2rem 1rem;
            }

            .register-form {
                padding: 1.5rem;
            }
        }

        /* ===== Optional: Input Icons Inside ===== */
        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .input-icon input {
            padding-left: 3rem;
        }
    </style>
</head>
<body>
    

    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-heartbeat"></i>
            <h1>Create Account</h1>
            <p>Join Health Without Distance and start connecting with patients</p>
        </div>

        <div class="register-form">
            <form action="database.php" method="post" id="doctorRegisterForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName"><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" id="fullName" name="FullName" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" id="email" name="EmailAddress" placeholder="doctor@example.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password *</label>
                        <input type="password" id="password" name="Password" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword"><i class="fas fa-lock"></i> Confirm Password *</label>
                        <input type="password" id="confirmPassword" name="Confirm_Password" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="specialty"><i class="fas fa-stethoscope"></i> Medical Specialty *</label>
                        <select id="specialty" name="Medical_Specialty" required>
                            <option value="" disabled selected>Select your specialty</option>
                            <option value="Pediatrics">Pediatrics</option>
                            <option value="Orthopedics">Orthopedics</option>
                            <option value="General Practitioner">General Practitioner</option>
                            
                        </select>
                    </div>
                        <div class="form-group">
                            <label for="gender"><i class="fas fa-genderless"></i>gender</label>
                            <select id="gender" name="Gender" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                </select>
                        </div>
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone-alt"></i> Phone Number *</label>
                        <input type="tel" id="phone" name="Phone_Number" placeholder="+1 234 567 890" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address"><i class="fas fa-map-marker-alt"></i> Clinic Address *</label>
                    <input type="text" id="address" name="Clinic_Address" placeholder="123 Medical Center, City" required>
                </div>

                <div class="form-group">
                    <label for="bio"><i class="fas fa-file-medical"></i> Professional Bio</label>
                    <textarea id="bio" name="Professional_Bio" rows="4" placeholder="Tell patients about your experience, qualifications, and approach to care..."></textarea>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn btn-outline cancel" id="cancelRegister">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </div>
            </form>

            <div class="login-link">
                Already have an account? <a href="login1.php">Sign in here</a>
            </div>
        </div>
    </div>

    <script>
        // Cancel button redirects to login
        document.getElementById('cancelRegister')?.addEventListener('click', function() {
            window.location.href = 'Doctor.php';
        });

        // Optional: Show notifications if needed (can be triggered from PHP)
        function showNotification(type, message) {
            const notif = document.getElementById(type + 'Notification');
            if (notif) {
                notif.querySelector('.notification-message').textContent = message;
                notif.classList.add('show');
                setTimeout(() => notif.classList.remove('show'), 3000);
            }
        }
    </script>
</body>
</html>