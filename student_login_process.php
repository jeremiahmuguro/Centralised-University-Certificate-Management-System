<?php
// This file processes the student login form submission. It checks if the submitted email exists in the Student table of the database. If a matching record is found, it redirects the student to the print page. If no matching record is found, it displays an error message indicating that the student was not found and provides a link to try logging in again. The file includes the database connection and handles the logic for validating the student's email against the database records.


// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the form was submitted using the POST method. This ensures that we only process the login when the form is submitted, and not when the page is accessed directly. If the 'student_email' field is not set in the POST data, we will redirect the user back to the student login page to ensure that they are submitting the form correctly. This check helps to prevent errors and ensures that we are only processing valid login attempts from the student login form.
if(!isset($_POST['student_email'])) {
    // If the 'student_email' field is not set in the POST data, redirect the user back to the student login page. This ensures that they are submitting the form correctly and prevents errors from trying to access an undefined index in the POST data. Redirecting back to the login page allows the user to correct their input and try logging in again.
    header("Location: student_login.html");
    // Ensure no further code is executed after the redirect. This is important to prevent any unintended processing of the login logic if the form was not submitted correctly. By calling exit() after the header redirect, we ensure that the script stops executing and the user is properly redirected to the login page without any further processing of the login logic.
    exit();
}
// Get the submitted email from the POST data. This is the email address that the student entered in the login form, and we will use it to check against the Student table in the database to see if a matching record exists. The name 'student_email' must match the 'name' attribute of the input field in the student login form for this to work correctly.
$email = $_POST['student_email'];

// Check if student exists
// Prepare a SQL statement to select the StudentID from the Student table where the StudentEmail matches the submitted email. This will allow us to check if there is a student record in the database with the provided email address. We will use a prepared statement to prevent SQL injection and ensure that the query is executed safely. The parameter is the email address, which corresponds to the StudentEmail column in the Student table. We will also limit the result to 1 record since we only need to check for the existence of a student with that email.
$sql = "SELECT StudentID FROM Student WHERE StudentEmail = ? LIMIT 1";
// Create a prepared statement to prevent SQL injection and bind the email parameter to the SQL query. This will allow us to safely query the database for the student ID based on the provided email address without risking SQL injection attacks. The parameter is the email address, which corresponds to the StudentEmail column in the Student table. The prepared statement will help us ensure that the data is queried correctly and securely from the database.
$stmt = $conn->prepare($sql);
// Bind the email parameter to the prepared statement and execute it to check if a student with the provided email exists. If a student is found, we will proceed to redirect them to the print page. If no student is found, we will display an error message to the user indicating that the student was not found and provide a link to try logging in again. This will provide feedback on whether the login was successful or if there were any issues with the provided email address. The result of the query is checked to see if any rows were returned, indicating that a student with the provided email exists in the database.
$stmt->bind_param("s", $email);
// Execute the prepared statement and get the result to check if a student with the provided email exists. If a student is found, we will proceed to redirect them to the print page. If no student is found, we will display an error message to the user indicating that the student was not found and provide a link to try logging in again. This will provide feedback on whether the login was successful or if there were any issues with the provided email address. The result of the query is checked to see if any rows were returned, indicating that a student with the provided email exists in the database.
$stmt->execute();
// Get the result of the query to check if a student with the provided email exists. If a student is found, we will proceed to redirect them to the print page. If no student is found, we will display an error message to the user indicating that the student was not found and provide a link to try logging in again. This will provide feedback on whether the login was successful or if there were any issues with the provided email address. The result of the query is checked to see if any rows were returned, indicating that a student with the provided email exists in the database.
$result = $stmt->get_result();
// Check if a student with the provided email was found. If no student is found, display an error message and redirect back to the login form. If a student is found, redirect them to the print page. This will ensure that only valid students can access the print page and provide feedback to users who may have entered an incorrect email address.
if($result->num_rows > 0) {
    // Valid student found with that email → redirect to print page
   
    header("Location: print.html");
    // Ensure no further code is executed after the redirect. This is important to prevent any unintended processing of the login logic after we have already redirected the user to the print page. By calling exit() after the header redirect, we ensure that the script stops executing and the user is properly redirected to the print page without any further processing of the login logic.
    exit();
} else {
    
    // Invalid student → show error
    echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'>
            <h1 style='color:#e74c3c;'>✗ STUDENT NOT FOUND</h1>
            <p>No record found for email: <strong>".htmlspecialchars($email)."</strong></p>
            <a href='student_login.html' class='button-link'>Try Again</a>
          </div>";
}























/*session_start(); // Start the session
include 'db_connect.php';

if(!isset($_POST['student_email'])) {
    header("Location: student_login.html");
    exit();
}

$email = $_POST['student_email'];

// Check if student exists
$sql = "SELECT StudentID, StudentName FROM Student WHERE StudentEmail = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // ✅ Set session variables
    $_SESSION['student_email'] = $email;
    $_SESSION['student_id'] = $row['StudentID'];
    $_SESSION['student_name'] = $row['StudentName'];

    // Redirect to print page
    header("Location: print.php");
    exit();
} else {
    // Invalid student → show error
    echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'>
            <h1 style='color:#e74c3c;'>✗ STUDENT NOT FOUND</h1>
            <p>No record found for email: <strong>".htmlspecialchars($email)."</strong></p>
            <a href='student_login.html' class='button-link'>Try Again</a>
          </div>";
}
*/

?>
