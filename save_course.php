<?php
// This file handles the logic for saving a new course to the database when the course creation form is submitted. It processes the form data, validates it, and inserts a new record into the Course table in the database. After successfully adding the course, it provides feedback to the user and redirects them back to the course management page or form.

// Include the database connection file to interact with the database
include 'db_connect.php';

                        /* Security Check: Only Admins can manage courses
                        // Check if the user is logged in and has the Admin role
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied. Only administrators can manage courses.");
}
*/

// Check if the form was submitted using the POST method
/// If the request method is POST, it means the course creation form was submitted and we should process the form data to save the new course to the database. If the request method is not POST, it means the page was accessed directly or through a GET request, and we should not attempt to process any form data.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data from POST request. These names must match the 'name' attributes in the HTML input tags of the course creation form. We will get the course name, course code, level of the program, university ID, and duration from the form submission. This data will be used to create a new course record in the database.
    $cName = $_POST['c_name'];
    $cCode = $_POST['c_code'];
    $level = $_POST['level'];
    $uID = $_POST['u_id'];
    $duration = $_POST['duration'];

    // Matching schema: CourseName, CourseCode, LevelOfProgram, UniversityID, Duration
    // Prepare a SQL statement to insert a new course record into the Course table. This will allow us to add the new course to the database with the provided information. We will use a prepared statement to prevent SQL injection and ensure that the data is inserted safely into the database. The parameters include the course name, course code, level of the program, university ID, and duration, which correspond to the columns in the Course table.
    $sql = "INSERT INTO Course (CourseName, CourseCode, LevelOfProgram, UniversityID, Duration) VALUES (?, ?, ?, ?, ?)";
    // Create a prepared statement to prevent SQL injection and bind the parameters to the SQL query. This will allow us to safely insert the course data into the database without risking SQL injection attacks. The parameters include the course name, course code, level of the program, university ID, and duration, which correspond to the columns in the Course table. The prepared statement will help us ensure that the data is inserted correctly and securely into the Course table.
    $stmt = $conn->prepare($sql);
    // Bind the parameters to the prepared statement and execute it to create a new course record in the Course table. This will allow us to add the new course to the database with the provided information. The parameters include the course name, course code, level of the program, university ID, and duration, which correspond to the columns in the Course table. The prepared statement will help us ensure that the data is inserted correctly and securely into the Course table.
    $stmt->bind_param("sssis", $cName, $cCode, $level, $uID, $duration);

        // Execute the prepared statement and check if the course was created successfully. If the execution is successful, we will display a success message to the user. If there is an error during the execution, we will display the error message to the user. This will provide feedback on whether the course creation was successful or if there were any issues that need to be addressed. The success message is displayed using a JavaScript alert, and the user is redirected to the CourseForm.html page after acknowledging the alert. If there is an error, the error message will be displayed directly on the page, allowing the user to see what went wrong during the course creation process.
    if ($stmt->execute()) {
        // If the course was created successfully, display a success message to the user and redirect them back to the course management page or form. This will provide feedback that the course was added successfully and allow them to continue managing courses as needed. The success message is displayed using a JavaScript alert, and the user is redirected to the CourseForm.html page after acknowledging the alert.
        echo "<script>alert('Course Added Successfully!'); window.location='CourseForm.html';</script>";
    } else {
        // If there was an error during the execution of the prepared statement, display the error message to the user. This will help them understand what went wrong and potentially how to fix it. The error message is retrieved from the statement's error property and displayed using a JavaScript alert, allowing the user to see the specific issue that occurred during the course creation process.
        echo "Error: " . $stmt->error;
    }
    // Close the statement to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the statement after we are done with it helps to clean up resources and allows the database connection to be reused for other operations.
    $stmt->close();
    // Close the database connection to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the connection after we are done with all database operations helps to clean up resources and allows the application to manage connections efficiently.
    $conn->close();
}
?>