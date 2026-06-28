<?php
// This file serves as the main dashboard for university staff after they log in. It provides a user interface with navigation links to various functionalities such as adding courses, registering students, uploading transcripts, and requesting certificates. The dashboard also includes a welcome message that displays the staff member's real name and a logout button to end the session securely. The content area of the dashboard is designed to load different pages based on the navigation links clicked by the user, allowing for a seamless and interactive experience within the university management system.

// Start the session to access session variables and check if the user is logged in with the UniversityStaff role. If not, redirect them to the login page.
session_start();
// Check if the user is logged in and has the UniversityStaff role. If not, redirect them to the login page.
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'UniversityStaff') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Dashboard | CUCMS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; background: #f4f6f9; }
        .sidebar { width: 250px; background: #34495e; color: white; padding: 20px; }
        .sidebar h2 { font-size: 1.1rem; border-bottom: 1px solid #555; padding-bottom: 10px; margin-bottom: 20px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px 10px; border-radius: 4px; transition: 0.3s; }
        .sidebar a:hover { background: #2c3e50; color: white; }
        
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; }
        .header { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }
        
        /* This is the key part: The Iframe area */
        #content-frame { width: 100%; height: 100%; border: none; background: #f4f6f9; }
        .view-area { flex-grow: 1; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>University Portal</h2>
    <a href="CourseForm.html" target="content-frame">Add Course</a>
    <a href="StudentForm.html" target="content-frame">Register Student</a>
    <a href="TranscriptForm.html" target="content-frame">Upload Transcripts</a>
    <a href="ApplicationForm.html" target="content-frame">Request Certificates</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Institution Management</h1>
        <div>
            <span>Welcome, <strong><?php echo $_SESSION['realname']; ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="view-area">
        <iframe src="dashboard_welcome.html" name="content-frame" id="content-frame"></iframe>
    </div>
</div>

</body>
</html>