<?php
// This page is for administrators to view user activity logs and manage user accounts.

// It checks if the user is logged in and has the admin role before allowing access. It retrieves user account information from the database and displays it in a table format. Only administrators can access this page, and it provides an overview of user accounts including their username, full name, role, and account creation date.

// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Check if user is logged in and is an admin
// If the user is not logged in or does not have the admin role, redirect them to the login page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}
// Fetch user activity logs 
$sql = "SELECT Username, RealName, UserRole, CreatedAt FROM Users ORDER BY CreatedAt DESC";
// Execute the query and get the result
// The result will contain the user accounts ordered by their creation date, with the most recent accounts appearing first
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #2c3e50; color: white; }
    </style>
</head>
<body>
    
    <h2>User Activity & Management</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Account Created</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through the result and display user accounts-->
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Username']; ?></td>
                <td><?php echo $row['RealName']; ?></td>
                <td><?php echo $row['UserRole']; ?></td>
                <td><?php echo $row['CreatedAt']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>