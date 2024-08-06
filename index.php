<?php
// Include the database connection file
require 'config/db.php';

// Variables to store error or success messages
$error = '';
$success = '';

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = strtolower($_POST['username']); // Convert username to lowercase
    $password = $_POST['password'];

    // Query the database to find the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check if the user exists and if the password matches
    if ($user && $password === $user['password']) {
        // Successful login, redirect to the home page
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        header('Location: public/index.php');
        exit;
    } else {
        // Authentication error
        $error = 'Invalid username or password';
    }
}

// Check if the registration form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = strtolower($_POST['registerUsername']); // Convert username to lowercase
    $email = strtolower($_POST['registerEmail']); // Convert email to lowercase
    $password = $_POST['registerPassword']; // Plain text password

    // Validate password strength (min 6 characters, 1 uppercase, 1 special character)
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[\W]/', $password)) {
        $error = 'Password must be at least 6 characters long, contain an uppercase letter, and a special character.';
    } else {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            $success = 'Registration successful!';
        } else {
            $error = 'Failed to register. Please try again!';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NotificaGeral - Login & Register</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Ionicons for icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #001f3f;
            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%),
            radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
        }

        #radius-shape-1 {
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
            background: radial-gradient(#00ff99, #00cc66);
            overflow: hidden;
        }

        #radius-shape-2 {
            border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
            bottom: -60px;
            right: -110px;
            width: 300px;
            height: 300px;
            background: radial-gradient(#00ff99, #00cc66);
            overflow: hidden;
        }

        .bg-glass {
            background-color: hsla(0, 0%, 100%, 0.9) !important;
            backdrop-filter: saturate(200%) blur(25px);
        }

        .form-container {
            max-width: 400px;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-weight: 600;
            color: #001f3f;
        }

        .btn-primary {
            background-color: #001f3f;
            border: none;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #004080;
        }

        .nav-pills .nav-link.active {
            background-color: #001f3f;
        }

        .form-control {
            border-radius: 20px;
        }

        .password-container ion-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }

        .popup-content ion-icon {
            font-size: 40px;
            color: #00cc66;
        }
    </style>
</head>

<body>

    <!-- Background and Logo -->
    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 align-items-center mb-5">
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <img src="images/logo.png" alt="Logo" style="max-width: 500px;">
                    <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                        The best notifier <br />
                        <span style="color: hsl(218, 81%, 75%)">on the market</span>
                    </h1>
                    <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                        With <b>NotificaGeral</b>, you can centralize notifications across email, SMS, 
                        and Web Push, streamlining communication with your customers. Whether for marketing, alerts, or internal messages, 
                        NotificaGeral offers flexibility, speed, and security from a single platform, allowing you to automate and manage notifications effortlessly.
                    </p>
                </div>
                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <!-- Login and Register Forms -->
                            <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-login" data-bs-toggle="pill" href="#pills-login" role="tab" aria-controls="pills-login" aria-selected="true">Login</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-register" data-bs-toggle="pill" href="#pills-register" role="tab" aria-controls="pills-register" aria-selected="false">Register</a>
                                </li>
                            </ul>

                            <!-- Tab content -->
                            <div class="tab-content">
                                <!-- Login Form -->
                                <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                                    <form method="POST">
                                        <h2 class="text-center mb-4">Sign in</h2>
                                        <?php if ($error): ?>
                                        <div class="alert alert-danger text-center"><?= $error ?></div>
                                        <?php endif; ?>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="loginUsername" class="form-control" name="username" required />
                                            <label class="form-label" for="loginUsername">Username</label>
                                        </div>

                                        <div class="form-outline mb-4 password-container">
                                            <input type="password" id="loginPassword" class="form-control" name="password" required />
                                            <label class="form-label" for="loginPassword">Password</label>
                                            <ion-icon name="eye-outline" id="togglePassword"></ion-icon>
                                        </div>

                                        <div class="text-center">
                                            <a href="#" class="forgot-password">Forgot password?</a>
                                        </div>

                                        <div class="captcha-container mb-4">
                                            <input type="checkbox" id="captcha" required>
                                            <label for="captcha">I'm not a robot</label>
                                        </div>

                                        <div class="btn-center">
                                            <button type="submit" name="login" class="btn btn-primary btn-block mb-4">Sign in <ion-icon name="log-in-outline"></ion-icon></button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Register Form -->
                                <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
                                    <form method="POST" onsubmit="return validateCaptcha()">
                                        <h2 class="text-center mb-4">Register</h2>
                                        <?php if ($success): ?>
                                        <div class="alert alert-success text-center"><?= $success ?></div>
                                        <?php endif; ?>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="registerUsername" class="form-control" name="registerUsername" required />
                                            <label class="form-label" for="registerUsername">Username</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="email" id="registerEmail" class="form-control" name="registerEmail" required />
                                            <label class="form-label" for="registerEmail">Email</label>
                                        </div>

                                        <div class="form-outline mb-4 password-container">
                                            <input type="password" id="registerPassword" class="form-control" name="registerPassword" required />
                                            <label class="form-label" for="registerPassword">Password</label>
                                            <ion-icon name="eye-outline" id="toggleRegisterPassword"></ion-icon>
                                        </div>

                                        <div class="progress mb-4">
                                            <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>

                                        <div class="captcha-container mb-4">
                                            <input type="checkbox" id="registerCaptcha" required>
                                            <label for="registerCaptcha">I'm not a robot</label>
                                        </div>

                                        <div class="btn-center">
                                            <button type="submit" name="register" class="btn btn-primary btn-block mb-4">Register</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Success Popup -->
                            <div class="popup" id="successPopup">
                                <div class="popup-content">
                                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                                    <h4>User created successfully!</h4>
                                    <button class="btn btn-primary" onclick="closePopup()">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Password Visibility Toggle -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#loginPassword');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.name = this.name === 'eye-outline' ? 'eye-off-outline' : 'eye-outline';
        });

        const toggleRegisterPassword = document.querySelector('#toggleRegisterPassword');
        const registerPassword = document.querySelector('#registerPassword');

        toggleRegisterPassword.addEventListener('click', function () {
            const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            registerPassword.setAttribute('type', type);
            this.name = this.name === 'eye-outline' ? 'eye-off-outline' : 'eye-outline';
        });

        // Password Strength Validation
        const passwordStrength = document.getElementById('passwordStrength');
        registerPassword.addEventListener('input', function () {
            const strength = calculatePasswordStrength(this.value);
            passwordStrength.style.width = strength + '%';
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength += 30;
            if (/[A-Z]/.test(password)) strength += 30;
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 40;
            return strength;
        }

        // Show Success Popup
        function showPopup() {
            document.getElementById('successPopup').style.display = 'flex';
        }

        // Close Success Popup
        function closePopup() {
            document.getElementById('successPopup').style.display = 'none';
            window.location.href = '#pills-login';
        }

        <?php if ($success): ?>
            showPopup();
        <?php endif; ?>
    </script>
</body>

</html>

