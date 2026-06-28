<?php
// This file manages user accounts, allowing Admins to view, update, and delete users.
// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check
// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}

// 2. Handle Delete Logic (Must be BEFORE the SELECT query)
// Check if delete_id is set in the GET parameters
if (isset($_GET['delete_id'])) {
    // Get the user ID to be deleted from the GET parameters
    $id = $_GET['delete_id'];
    
    // Safety: Prevent Admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        // If the user tries to delete their own account, show an error message and prevent deletion
        echo "<script>alert('Error: You cannot delete your own account while logged in!'); window.location='manage_users.php';</script>";
    } else {
        // If the user is not trying to delete themselves, proceed with deletion
        // Prepare a statement to delete the user from the database
        $delete = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
        // Bind the user ID parameter and execute the delete statement
        $delete->bind_param("i", $id);
        // Execute the delete statement and check if it was successful
        if ($delete->execute()) {
            // If the deletion was successful, show a success message and refresh the page
            echo "<script>alert('User removed from system.'); window.location='manage_users.php';</script>";
        } else {
            // If there was an error during deletion, display the error message
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// 3. Now fetch the updated list
// Execute a query to retrieve user records from the database
$result = $conn->query("SELECT UserID, RealName, UserPhone, UserName, UserEmail, UserRole FROM Users");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: white; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 5px; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <h2>User Management Portal</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['UserID']; ?></td>
                <td><?php echo htmlspecialchars($row['RealName']); ?></td>
                <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                <td><?php echo htmlspecialchars($row['UserEmail']); ?></td>
                <td><?php echo $row['UserRole']; ?></td>
                <td>
                    <a href="update_user.php?id=<?php echo $row['UserID']; ?>" class="btn btn-edit">Update</a>
                    <a href="manage_user.php?delete_id=<?php echo $row['UserID']; ?>" 
                       class="btn btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>