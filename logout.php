<?php
// This file handles user logout by destroying the session and redirecting to the login page.
// Start the session to access session variables
session_start();
// Destroy all session data to log the user out
session_destroy();
// Redirect the user back to the login page after logout
header("Location: index.html");
// Ensure no further code is executed after the redirect
exit();
?>