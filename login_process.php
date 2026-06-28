<?php
// This file processes the login form submission, verifies user credentials, and redirects users based on their roles.
// Start the session to manage user login state
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Check if the form was submitted
// If the request method is POST, it means the login form was submitted and we should process the login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from POST data
    // Get the username and password submitted by the user from the login form
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 1. Fetch the user details by Username only
    $sql = "SELECT * FROM Users WHERE UserName = ?";
    // Prepare the SQL query template
    $stmt = $conn->prepare($sql);
      // Bind parameters
    $stmt->bind_param("s", $user);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();

    // Check if a user with the provided username exists
    if ($result->num_rows == 1) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        
        // 2. Verify the hashed password
        if (password_verify($pass, $row['UserPassword'])) {
            // Success! Store user data in session
            
            //setting session variables for the logged in user
            $_SESSION['username'] = $row['UserName'];
            $_SESSION['realname'] = $row['RealName'];
            $_SESSION['role'] = $row['UserRole'];
            $_SESSION['user_id'] = $row['UserID'];

            // Role-Based Redirection
            if ($row['UserRole'] == 'Admin') {
                header("Location: admin_dashboard.php");
            } elseif ($row['UserRole'] == 'UniversityStaff') {
                header("Location: staff_dashboard.php");
            } elseif ($row['UserRole'] == 'Regulator') {
                header("Location: regulator_dashboard.php");
            }
            exit();
        } else {
            // Password did not match the hash
            echo "<script>alert('Invalid Password'); window.location='index.html';</script>";
        }
    } else {
        // Username not found
        echo "<script>alert('User not found'); window.location='index.html';</script>";
    }
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>