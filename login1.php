
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Health Without Distance</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Same design system as doctors.html ===== */
        :root {
            --primary: linear-gradient(135deg, #4889ac 0%, #b8dce8 100%);
            --primary-solid: #2b5f70;
            --primary-dark: #2a5b6d;
            --secondary: linear-gradient(135deg, #50a4c0 0%, #4889ac 100%);
            --secondary-solid: #50a4c0;
            --accent: #27AE60;
            --success: #27AE60;
            --error: #ff6b9d;
            --dark: #1A1A2E;
            --gray: #64748B;
            --gray-light: #f7fbfd;
            --white: #ffffff;
            --shadow-md: 0 4px 20px rgba(0,0,0,0.12);
            --shadow-lg: 0 10px 40px rgba(43,95,112,0.25);
            --shadow-xl: 0 20px 60px rgba(43,95,112,0.3);
            --transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Main card */
        .login-container {
            background: var(--white);
            max-width: 450px;
            width: 100%;
            padding: 3rem 2.5rem;
            border-radius: 32px;
            box-shadow: var(--shadow-xl);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header with logo */
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-header i {
            font-size: 3.5rem;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .login-header h1 {
            font-size: 2rem;
            color: var(--dark);
            font-weight: 700;
        }
        .login-header p {
            color: var(--gray);
            margin-top: 0.5rem;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 1.8rem;
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 0.6rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        label i {
            color: var(--primary-solid);
            font-size: 1.1rem;
        }
        input {
            padding: 1rem 1.2rem;
            border: 2px solid var(--gray-light);
            border-radius: 16px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            width: 100%;
        }
        input:focus {
            outline: none;
            border-color: var(--primary-solid);
            box-shadow: 0 0 0 4px rgba(43,95,112,0.1);
            transform: translateY(-2px);
        }

        /* Checkbox row */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0 2rem;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--dark);
        }
        .remember-me input {
            width: auto;
            accent-color: var(--primary-solid);
        }
        .forgot-link {
            color: var(--primary-solid);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .forgot-link:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        /* Buttons */
        .btn {
            padding: 1rem;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%,-50%);
            transition: width 0.6s, height 0.6s;
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

        /* Register link */
        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--gray);
        }
        .register-link a {
            color: var(--primary-solid);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }
        .register-link a:hover {
            text-decoration: underline;
        }

        
        
    </style>
</head>
<body>
    

    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-heartbeat"></i>
            <h1>Welcome Back</h1>
            <p>Sign in to your doctor account</p>
        </div>

        <form  action="login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="EmailAddress" placeholder="doctor@example.com" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="password" required>
            </div>

            

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

</body>
</html>