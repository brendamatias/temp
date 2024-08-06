<?php
// Start the session to check if the user is logged in
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to the login page
    header('Location: ../index.php');
    exit;
}

// Retrieve the logged-in username (stored in the session)
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>NotificaGeral - Notification System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- External CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/3aa0xrifmv0wou3gegj41gdnzwjdqn72891xtaxso407rjyb/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Favicon -->
    <link rel="icon" href="http://localhost/notificageral/NotificaGeral/favicon.ico" type="image/x-icon">

    <style>
        /* Custom style for Notification History button */
        .btn-history {
            background-color: #0F3B85; /* Navy Blue */
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            font-size: 18px;
        }

        .btn-history:hover {
            background-color: #D9A424; /* Lime green on hover */
            color: black;
        }
    </style>
</head>
<body>

    <!-- Header with logo and user info -->
    <header>
        <center><img src="../images/logo.png" alt="NotificaGeral Logo" style="max-width: 400px;"></center>
        <p class="lead text-center">Manage your Email, SMS, and Web Push notifications efficiently.</p>

        <!-- User info with avatar, username, settings, and logout -->
        <div class="user-info">
            <img src="../images/avatar.png" alt="User Avatar">
            <span class="username"><?php echo $username; ?></span>
            <ion-icon name="settings-outline" class="settings-icon" onclick="openSettingsPopup()"></ion-icon>
            <a href="logout.php">
                <ion-icon name="log-out-outline" class="logout-icon"></ion-icon>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-5">

        <!-- Button for Notification History -->
        <div class="text-center my-4">
            <a href="status.php" class="btn btn-history">
                <ion-icon name="document-text-outline"></ion-icon> Notification History
            </a>
        </div>

        <!-- Message and file upload area -->
        <div class="message-area">
            <h4>Add Your Message</h4>
            <textarea id="message">Enter your message here...</textarea>

            <div class="upload-area">
                <label for="fileUpload">Upload an image or PDF:</label>
                <input type="file" id="fileUpload" accept=".jpeg,.png,.pdf">
            </div>

            <button class="btn btn-primary mt-3" onclick="saveMessage()">Save</button>

            <div class="uploaded-content">
                <div id="displayMessage"></div>
                <div id="displayFile"></div>
            </div>

            <button class="btn btn-warning edit-btn" onclick="editMessage()">Edit</button>
        </div>

        <!-- Card for "Send Notifications to All Systems" -->
        <div class="generate-all-card">
            <h4>Send Notifications to All Systems</h4>

            <div class="generate-icons">
                <ion-icon name="mail-outline"></ion-icon>
                <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                <ion-icon name="desktop-outline"></ion-icon>
            </div><br><br>
            <center>
                <button class="btn" onclick="generateAllNotifications()">
                    <ion-icon name="flash-outline"></ion-icon> Generate All
                </button>
            </center>
        </div>

        <!-- Existing notification cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <ion-icon name="mail-outline" size="large"></ion-icon>
                        <h5 class="card-title mt-3">Manage Emails</h5>
                        <p class="card-text">Easily configure and send email notifications.</p>
                        <button class="btn btn-config" onclick="openEmailSettingsPopup()">
                            <ion-icon name="settings-outline"></ion-icon> Config
                        </button>
                        <button class="btn btn-generate" onclick="generateNotification('Email')">
                            <ion-icon name="flash-outline"></ion-icon> Generate
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <ion-icon name="chatbubble-ellipses-outline" size="large"></ion-icon>
                        <h5 class="card-title mt-3">Manage SMS</h5>
                        <p class="card-text">Quickly send SMS notifications efficiently.</p>
                        <button class="btn btn-config" onclick="openSmsSettingsPopup()">
                            <ion-icon name="settings-outline"></ion-icon> Config
                        </button>
                        <button class="btn btn-generate" onclick="generateNotification('SMS')">
                            <ion-icon name="flash-outline"></ion-icon> Generate
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <ion-icon name="desktop-outline" size="large"></ion-icon>
                        <h5 class="card-title mt-3">Manage Web Push</h5>
                        <p class="card-text">Easily notify your users via Web Push.</p>
                        <button class="btn btn-config" onclick="openPushSettingsPopup()">
                            <ion-icon name="settings-outline"></ion-icon> Config
                        </button>
                        <button class="btn btn-generate" onclick="generateNotification('Web Push')">
                            <ion-icon name="flash-outline"></ion-icon> Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4">
        <p>NotificaGeral &copy; 2024 - All Rights Reserved</p>
    </footer>

    <!-- Popups for individual and full settings -->
    <div class="popup" id="emailSettingsPopup">
        <div class="popup-content">
            <h4>Email Setup</h4>
            <form action="../config/update_email_config.php" method="POST">
                <!-- Server details section -->
                <div class="section" id="serverDetails">
                    <h5>Technical Server Details <ion-icon name="chevron-down-outline" onclick="toggleSection('serverDetails')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="smtpServer">SMTP Server Name</label>
                        <input type="text" id="smtpServer" name="smtpServer" placeholder="Enter SMTP server">
                        <label for="smtpPort">SMTP Port</label>
                        <input type="number" id="smtpPort" name="smtpPort" placeholder="Enter SMTP port">
                        <br><label for="Login">Login</label><br>
                        <input type="text" id="emailLogin" name="emailLogin" placeholder="Enter login">
                        <label for="emailPassword">Password</label>
                        <input type="password" id="emailPassword" name="emailPassword" placeholder="Enter password">
                    </div>
                </div>

                <!-- Sender info section -->
                <div class="section" id="senderInfo">
                    <h5>Sender Information <ion-icon name="chevron-down-outline" onclick="toggleSection('senderInfo')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="senderName">Sender Name</label>
                                               <label for="senderName">Sender Name</label>
                        <input type="text" id="senderName" name="senderName" placeholder="Enter sender's name">
                        <label for="senderEmail">Sender Email</label>
                        <input type="email" id="senderEmail" name="senderEmail" placeholder="Enter sender's email">
                    </div>
                </div>

                <!-- Template Submission section -->
                <div class="section" id="templateSubmission">
                    <h5>Template Submission <ion-icon name="chevron-down-outline" onclick="toggleSection('templateSubmission')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="templateUpload">Upload Email Templates (.html files)</label>
                        <input type="file" id="templateUpload" name="templateUpload[]" accept=".html" multiple>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Save</button>
                <button class="btn btn-secondary" type="button" onclick="closeEmailSettingsPopup()">Cancel</button>
            </form>
        </div>
    </div>

    <div class="popup" id="smsSettingsPopup">
        <div class="popup-content">
            <h4>SMS Setup</h4>
            <form action="../config/update_sms_config.php" method="POST">
                <div class="section" id="smsProviderDetails">
                    <h5>SMS Provider Details <ion-icon name="chevron-down-outline" onclick="toggleSection('smsProviderDetails')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="smsProvider">SMS Provider</label>
                        <input type="text" id="smsProvider" name="smsProvider" placeholder="Enter SMS provider">
                        <br><label for="smsLogin">Login</label><br>
                        <input type="text" id="smsLogin" name="smsLogin" placeholder="Enter login">
                        <label for="smsPassword">Password</label>
                        <input type="password" id="smsPassword" name="smsPassword" placeholder="Enter password">
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Save</button>
                <button class="btn btn-secondary" type="button" onclick="closeSmsSettingsPopup()">Cancel</button>
            </form>
        </div>
    </div>

    <div class="popup" id="pushSettingsPopup">
        <div class="popup-content">
            <h4>Web Push Setup</h4>
            <form action="../config/update_push_config.php" method="POST">
                <div class="section" id="basicDetails">
                    <h5>Basic Details <ion-icon name="chevron-down-outline" onclick="toggleSection('basicDetails')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="pushSiteName">Website Name</label>
                        <input type="text" id="pushSiteName" name="pushSiteName" placeholder="Enter the website name">
                        <label for="pushSiteUrl">Website URL</label>
                        <input type="url" id="pushSiteUrl" name="pushSiteUrl" placeholder="Enter the website URL">
                        <label for="pushSiteIcon">Site Icon Image</label>
                        <input type="file" id="pushSiteIcon" name="pushSiteIcon" accept="image/*">
                    </div>
                </div>

                <div class="section" id="permissionConfig">
                    <h5>Permission Notification Configuration <ion-icon name="chevron-down-outline" onclick="toggleSection('permissionConfig')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="permissionMessage">Permission Message Text</label>
                        <input type="text" id="permissionMessage" name="permissionMessage" placeholder="Enter the permission message">
                        <label for="permissionAllowText">Allow Button Text</label>
                        <input type="text" id="permissionAllowText" name="permissionAllowText" placeholder="Enter text for 'Allow' button">
                        <label for="permissionDenyText">Deny Button Text</label>
                        <input type="text" id="permissionDenyText" name="permissionDenyText" placeholder="Enter text for 'Deny' button">
                    </div>
                </div>

                <div class="section" id="welcomeConfig">
                    <h5>Welcome Notification Configuration <ion-icon name="chevron-down-outline" onclick="toggleSection('welcomeConfig')"></ion-icon></h5>
                    <div class="section-content hidden">
                        <label for="welcomeTitle">Welcome Notification Title</label>
                        <input type="text" id="welcomeTitle" name="welcomeTitle" placeholder="Enter the welcome notification title">
                        <label for="welcomeMessage">Welcome Message Text</label>
                        <textarea id="welcomeMessage" name="welcomeMessage" placeholder="Enter the welcome message"></textarea>
                        <label for="enableLink">Enable/Disable Destination Link</label>
                        <select id="enableLink" name="enableLink">
                            <option value="enabled">Enable</option>
                            <option value="disabled">Disable</option>
                        </select>
                        <label for="destinationLink">Destination Link URL (if enabled)</label>
                        <input type="url" id="destinationLink" name="destinationLink" placeholder="Enter the destination link URL">
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Save</button>
                <button class="btn btn-secondary" type="button" onclick="closePushSettingsPopup()">Cancel</button>
            </form>
        </div>
    </div>

    <div class="popup" id="fullSettingsPopup">
        <div class="popup-content">
            <h4>General Settings</h4>
            <p>Here you can configure all the settings for your system.</p>
            <button class="btn btn-primary" type="submit">Save</button>
            <button class="btn btn-secondary" type="button" onclick="closeFullSettingsPopup()">Cancel</button>
        </div>
    </div>

    <!-- Overlay and Progress Dialog for "Generate All" -->
    <div class="overlay" id="overlay"></div>
    <div class="progress-dialog" id="progress-dialog">
        <div id="progress-text">Generating Notifications...</div>
        <ul id="progress-list">
            <li id="progress-email">Email Notification&nbsp;&nbsp;&nbsp;<span class="icon"></span></li>
            <li id="progress-sms">SMS Notification&nbsp;&nbsp;&nbsp;<span class="icon"></span></li>
            <li id="progress-push">Web Push Notification&nbsp;&nbsp;&nbsp;<span class="icon"></span></li>
        </ul>
        <div class="progress-bar">
            <span class="progress-bar-fill" id="progress-bar-fill"></span>
        </div>
        <div class="progress-bar-text" id="progress-bar-text"></div>
        <center><button id="progress-close" style="display:none;" onclick="closeProgressDialog()">OK</button></center>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- External JS -->
    <script src="js/script.js"></script>

</body>
</html>


