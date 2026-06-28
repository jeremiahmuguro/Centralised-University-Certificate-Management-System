<?php
session_start();
// Security Check: If not logged in or wrong role, kick them back to landing page
// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
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
    <title>Admin Dashboard | CUCMS</title>
    <style>
        /* Base layout to support iframe expansion */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; background: #f4f6f9; }
        
        .sidebar { width: 250px; background: #2c3e50; color: white; padding: 20px; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 1.2rem; border-bottom: 1px solid #555; padding-bottom: 10px; margin-bottom: 10px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px 10px; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover { color: white; background: #34495e; border-radius: 4px; }
        
        .main-content { flex: 1; display: flex; flex-direction: column; }
        
        .header { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        
        /* Iframe container fills all remaining space */
        .view-area { flex: 1; position: relative; width: 100%; }
        #content-frame { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CUCMS Admin</h2>
    <a href="admin_welcome.php" target="content-frame">🏠 System Overview</a>
    <a href="all_analytics.php" target="content-frame">📊 Interactive Reports</a>
    <a href="registered_unis.php" target="content-frame">🏫 Registered Universities</a>
    <a href="manage_user.php" target="content-frame">👥 User Management</a>
    <a href="manage_course.php" target="content-frame">📚 Course Management</a>
    <a href="manage_student.php" target="content-frame">🧑🏽‍🎓 Student Management</a>
    <a href="manage_university.php" target="content-frame">🏫 University Management</a>
    <a href="newUserRegistration.html" target="content-frame">➕ Add New User</a>
    <a href="activity_logs.php" target="content-frame">📜 Activity Logs</a>
    <a href="university_courses.php" target="content-frame">📖 University Catalog</a>
    <a href="visitors.php" target="content-frame">👀 Visitors Reports</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div>
            <span>Welcome, <strong><?php echo $_SESSION['realname']; ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="view-area">
        <iframe src="admin_welcome.php" name="content-frame" id="content-frame"></iframe>
    </div>
</div>

</body>
</html>