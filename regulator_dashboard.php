<?php
// This file serves as the main dashboard for users with the Regulator role. It provides navigation links to various regulator functions such as registering universities, verifying applications, and viewing issued certificates. The dashboard is protected by a session check to ensure that only authorized users can access it.

// Start the session to access session variables and check if the user is logged in with the Regulator role. If not, redirect them to the login page.
session_start();
// Check if the user is logged in and has the Regulator role. If not, redirect them to the login page.
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Regulator') {
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
    <title>Regulator Dashboard | CUCMS</title>
   <!-- <style>
        /* Ensure the body takes up the full screen */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; background: #f4f6f9; }
        
        .sidebar { width: 250px; background: #1a252f; color: white; padding: 20px; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 1.2rem; border-bottom: 1px solid #34495e; padding-bottom: 15px; margin-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px 10px; transition: 0.3s; border-radius: 4px; }
        .sidebar a:hover { background: #34495e; color: white; }

        /* The main content area must be a flex container to grow */
        .main-content { flex: 1; display: flex; flex-direction: column; }
        
        .header { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }

        /* This container must fill all remaining space below the header */
        .view-area { flex: 1; position: relative; width: 100%; }
        
        /* The iframe must fill its parent completely */
        #content-frame { width: 100%; height: 100%; border: none; } 
    </style>
-->
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
    <h2>Regulator Panel</h2>
    <a href="UniversityForm.html" target="content-frame">Register University</a>
    <a href="view_application.php" target="content-frame">Verify Applications</a>
    <a href="view_issued.php" target="content-frame">View Issued Certificates</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Regulator Dashboard</h1>
        <div>
            <span>Official: <strong><?php echo $_SESSION['realname']; ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="view-area">
        <iframe src="dashboard_welcome.html" name="content-frame" id="content-frame"></iframe>
    </div>
</div>

</body>
</html>