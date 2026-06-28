<?php
// This file processes the user registration form submission, validates the input, and inserts a new user record into the database. It also handles password hashing for security and provides feedback on the registration outcome.

// Include the database connection file to interact with the database
include 'db_connect.php'; 
// Check if the form was submitted using the POST method. This ensures that we only process the registration when the form is submitted, and not when the page is accessed directly.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names must match the 'name' attributes in the HTML input tags
    $realName = $_POST['real_name'];
    $phone    = $_POST['phone'];
    $email    = $_POST['email'];
    $user     = $_POST['username'];
    $raw_pass = $_POST['password'];
    $role     = $_POST['role'];

     // Encrypt the password for security
    $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

    // Prepare SQL using the table columns: RealName, UserPhone, UserEmail, UserName, UserPassword, UserRole
    $sql = "INSERT INTO Users (RealName, UserPhone, UserEmail, UserName, UserPassword, UserRole) 
            VALUES (?, ?, ?, ?, ?, ?)";
    // Create a prepared statement to prevent SQL injection and bind the parameters to the SQL query. The 'ssssss' string indicates that all parameters are strings.
    $stmt = $conn->prepare($sql);
    // Bind the user input parameters to the prepared statement. This will safely insert the user data into the database without risking SQL injection attacks. The parameters are bound in the order they appear in the SQL query: RealName, UserPhone, UserEmail, UserName, UserPassword, UserRole.
    $stmt->bind_param("ssssss", $realName, $phone, $email, $user, $hashed_pass, $role);
    // Execute the prepared statement and check if the registration was successful. If the execution is successful, it means the user was added to the database, and we can provide feedback to the user. If there was an error (such as a duplicate username or email), we will display the error message.
    if ($stmt->execute()) {
        // Registration successful, provide feedback to the user
        echo "<h3>Registration Successful!</h3>";
        // Display a message confirming that the user has been added to the system, and provide a link to register another user if needed. The username is sanitized using htmlspecialchars to prevent XSS attacks when displaying the username back to the user.
        echo "User " . htmlspecialchars($user) . " has been added to the Centralised System.";
        // Provide a link to allow the user to register another user if they wish. This link directs back to the registration form.
        echo "<br><a href='newUserRegistration.html'>Add another user</a>";
    } else {
        // This will catch if the Email or Username already exists in CUCMS_DB
        echo "Error: " . $stmt->error;
    }
    // Close the statement and connection to free up resources
    $stmt->close();
    $conn->close();
}
?>