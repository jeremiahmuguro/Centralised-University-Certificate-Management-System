<?php
// This file handles the logic for saving a new student to the database when the student registration form is submitted. It processes the form data, validates it, and inserts a new record into the Student table in the database. After successfully adding the student, it provides feedback to the user and redirects them back to the student registration page or form.


// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the form was submitted using the POST method. This ensures that we only process the student registration when the form is submitted, and not when the page is accessed directly.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names must match the 'name' attributes in the HTML input tags of the student registration form. We will get the student's name, registration number, email, date of admission, course name, level of the program, and university ID from the form submission. This data will be used to create a new student record in the database.
    $name = $_POST['s_name'];
    $reg = $_POST['reg_num'];
    $email = $_POST['email'];
    $adm_date = $_POST['adm_date'];
    $course = $_POST['course_name'];
    $level = $_POST['level'];
    $uni_id = $_POST['uni_id'];

    // Matching schema: RegistrationNumber, StudentName, StudentEmail, DateOfAdmission, CourseName, LevelOfProgram, UniversityID
    // Prepare a SQL statement to insert a new student record into the Student table. This will allow us to add the new student to the database with the provided information. We will use a prepared statement to prevent SQL injection and ensure that the data is inserted safely into the database. The parameters include the student's registration number, name, email, date of admission, course name, level of the program, and university ID, which correspond to the columns in the Student table.
    $sql = "INSERT INTO Student (RegistrationNumber, StudentName, StudentEmail, DateOfAdmission, CourseName, LevelOfProgram, UniversityID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    // Create a prepared statement to prevent SQL injection and bind the parameters to the SQL query. This will allow us to safely insert the student data into the database without risking SQL injection attacks. The parameters include the student's registration number, name, email, date of admission, course name, level of the program, and university ID, which correspond to the columns in the Student table. The prepared statement will help us ensure that the data is inserted correctly and securely into the Student table.
    $stmt = $conn->prepare($sql);
    // Bind the parameters to the prepared statement and execute it to create a new student record in the Student table. This will allow us to add the new student to the database with the provided information. The parameters include the student's registration number, name, email, date of admission, course name, level of the program, and university ID, which correspond to the columns in the Student table. The prepared statement will help us ensure that the data is inserted correctly and securely into the Student table.
    $stmt->bind_param("ssssssi", $reg, $name, $email, $adm_date, $course, $level, $uni_id);
    // Execute the prepared statement and check if the student was registered successfully. If the execution is successful, we will display a success message to the user. If there is an error during the execution, we will display the error message to the user. This will provide feedback on whether the student registration was successful or if there were any issues that need to be addressed. The success message is displayed using a JavaScript alert, and the user is redirected to the StudentForm.html page after acknowledging the alert. If there is an error, the error message will be displayed directly on the page, allowing the user to see what went wrong during the student registration process.

    if ($stmt->execute()) {
        // If the student was registered successfully, display a success message to the user and redirect them back to the student registration page or form. This will provide feedback that the student was added successfully and allow them to continue registering students as needed. The success message is displayed using a JavaScript alert, and the user is redirected to the StudentForm.html page after acknowledging the alert.
        echo "<script>alert('Student Registered Successfully!'); window.location='StudentForm.html';</script>";
    } else {
        // If there was an error during the execution of the prepared statement, display the error message to the user. This will help them understand what went wrong and potentially how to fix it. The error message is retrieved from the statement's error property and displayed using a JavaScript alert, allowing the user to see the specific issue that occurred during the student registration process.
        echo "Error: " . $stmt->error;
    }
    // Close the statement to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the statement after we are done with it helps to clean up resources and allows the database connection to be reused for other operations.
    $stmt->close();
    // Close the database connection to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the connection after we are done with all database operations helps to clean up resources and allows the application to manage connections efficiently.
    $conn->close();
}
?>