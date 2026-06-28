<?php
// Database configuration
$host = "localhost";
$username = "root";      
$password = "root"; 
$dbname = "CUCMS_DB"; 

// Create connection
//$conn = new mysqli($host, $username, $password, $dbname, 3306);
$conn = new mysqli("127.0.0.1", "root", "root", "CUCMS_DB", 3306);

// Check connection
if ($conn->connect_error) {
    // If the connection fails, output an error message and terminate the script
    die("Connection failed: " . $conn->connect_error);
}

echo "Successfully connected to the database!";
?>