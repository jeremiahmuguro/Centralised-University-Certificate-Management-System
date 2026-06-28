<?php
// This file handles the logic for saving a new university to the database when the university registration form is submitted. It processes the form data, validates it, and inserts a new record into the University table in the database. After successfully adding the university, it provides feedback to the user and redirects them back to the university registration page or form.


// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the form was submitted using the POST method. This ensures that we only process the university registration when the form is submitted, and not when the page is accessed directly.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names must match the 'name' attributes in the HTML input tags of the university registration form. We will get the university name and university code from the form submission. This data will be used to create a new university record in the database.
    $uName = $_POST['u_name'];
    $uCode = $_POST['u_code'];
    // Matching schema: UniversityName, UniversityCode
    // Prepare a SQL statement to insert a new university record into the University table. This will allow us to add the new university to the database with the provided information. We will use a prepared statement to prevent SQL injection and ensure that the data is inserted safely into the database. The parameters include the university name and university code, which correspond to the columns in the University table.
    $sql = "INSERT INTO University (UniversityName, UniversityCode) VALUES (?, ?)";
    // Create a prepared statement to prevent SQL injection and bind the parameters to the SQL query. This will allow us to safely insert the university data into the database without risking SQL injection attacks. The parameters include the university name and university code, which correspond to the columns in the University table. The prepared statement will help us ensure that the data is inserted correctly and securely into the University table.
    $stmt = $conn->prepare($sql);
    // Bind the parameters to the prepared statement and execute it to create a new university record in the University table. This will allow us to add the new university to the database with the provided information. The parameters include the university name and university code, which correspond to the columns in the University table. The prepared statement will help us ensure that the data is inserted correctly and securely into the University table.
    $stmt->bind_param("ss", $uName, $uCode);
    // Execute the prepared statement and check if the university was registered successfully. If the execution is successful, we will display a success message to the user. If there is an error during the execution, we will display the error message to the user. This will provide feedback on whether the university registration was successful or if there were any issues that need to be addressed. The success message is displayed using a JavaScript alert, and the user is redirected to the UniversityForm.html page after acknowledging the alert. If there is an error, the error message will be displayed directly on the page, allowing the user to see what went wrong during the university registration process.

    if ($stmt->execute()) {
        // If the university was registered successfully, display a success message to the user and redirect them back to the university registration page or form. This will provide feedback that the university was added successfully and allow them to continue registering universities as needed. The success message is displayed using a JavaScript alert, and the user is redirected to the UniversityForm.html page after acknowledging the alert.
        echo "<script>alert('University Registered Successfully!'); window.location='UniversityForm.html';</script>";
    } else {
        // If there was an error during the execution of the prepared statement, display the error message to the user. This will help them understand what went wrong and potentially how to fix it. The error message is retrieved from the statement's error property and displayed using a JavaScript alert, allowing the user to see the specific issue that occurred during the university registration process.
        echo "Error: " . $stmt->error;
    }
    // Close the statement to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the statement after we are done with it helps to clean up resources and allows the database connection to be reused for other operations.
    $stmt->close();
    // Close the database connection to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the connection after we are done with all database operations helps to clean up resources and allows the application to manage connections efficiently.
    $conn->close();
}
?>