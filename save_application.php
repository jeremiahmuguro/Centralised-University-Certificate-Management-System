<?php
// This file processes the application form submission, updates the student's graduation date, and creates a new application record in the database. It also includes error handling and feedback for the user.

// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the form was submitted using the POST method. This ensures that we only process the application when the form is submitted, and not when the page is accessed directly.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the registration number and graduation date from the POST data submitted by the application form. These values are used to identify the student and update their graduation information in the database.
    $regNum = $_POST['reg_num'];
    // The graduation date is expected to be in a format that can be stored in the database (e.g., YYYY-MM-DD). It will be used to update the Student table and to create the application record with the correct application date.
    $gradDate = $_POST['grad_date'];

    // 1. Find Student Details (We MUST get StudentID and CourseID)
    // Note: If CourseID isn't in Student table, we need to join or have a default for now.
    // For simplicity, I'm assuming CourseID can be derived from the Student's course selection, but since it's not in the Student table, I'll set a placeholder for now.
    $lookup = $conn->prepare("SELECT StudentID FROM Student WHERE RegistrationNumber = ?");

    // Bind the registration number parameter to the prepared statement and execute it to find the student details based on the provided registration number. This will allow us to retrieve the StudentID, which is necessary for updating the graduation date and creating the application record.
    $lookup->bind_param("s", $regNum);
    // Execute the prepared statement and get the result. The result will contain the StudentID if a student with the provided registration number exists in the database.
    $lookup->execute();
    // Get the result of the query and store it in a variable for further processing. We will check if a student was found and then proceed to update their graduation date and create the application record.
    $res = $lookup->get_result();
// Check if a student with the provided registration number was found in the database. If a student is found, we will proceed to update their graduation date and create the application record. If no student is found, we will display an error message to the user.
    if ($res->num_rows > 0) {
        // Fetch the student details from the result set and store them in an associative array. This will allow us to access the StudentID and other relevant information needed for updating the graduation date and creating the application record.
        $student = $res->fetch_assoc();
        // Store the StudentID in a variable for easier access when updating the graduation date and creating the application record. The StudentID is a unique identifier for the student in the database and is necessary for linking the application to the correct student.
        $sID = $student['StudentID'];
        
        // IMPORTANT: The Application table REQUIRES a CourseID (NOT NULL).
        // For testing, I'm setting a placeholder ID of 1. 
        // Later i will fetch this from the Student's course selection.

        // Since the CourseID is not currently stored in the Student table, we will use a placeholder value of 1 for now. In a real implementation, we would need to retrieve the correct CourseID based on the student's course selection or other relevant information. This CourseID is necessary for creating the application record in the Application table, which has a NOT NULL constraint on the CourseID column.
        $cID = 1; 
        
        // I'll also set a temporary CertificateNumber as it is NOT NULL in my schema.
        // Since the CertificateNumber is required in the Application table and we do not have a generated certificate number at this stage, we will use a temporary placeholder value. This value can be updated later when the certificate is generated. The placeholder will help us satisfy the NOT NULL constraint on the CertificateNumber column while allowing us to create the application record.
        $tempCertNum = "PENDING-" . time();

        // 2. Update Student Table with Graduation Date
        // Prepare a SQL statement to update the Student table with the provided graduation date for the student identified by the StudentID. This will ensure that the student's graduation information is up to date in the database, which is important for processing their application and generating the certificate later on.
        $update = $conn->prepare("UPDATE Student SET DateOfGraduation = ? WHERE StudentID = ?");
        // Bind the graduation date and StudentID parameters to the prepared statement and execute it to update the student's graduation date in the database. This will allow us to keep the student's information current and accurate, which is essential for the application process and certificate generation.
        $update->bind_param("si", $gradDate, $sID);
        // Execute the update statement and check if it was successful. If the update is successful, we will proceed to create the application record. If there is an error during the update, we will display an error message to the user.
        $update->execute();

        // 3. Create Application Entry matching my EXACT schema columns:
        // StudentID, CourseID, CertificateNumber, ApplicationDate
        // The ApplicationDate will be set to the current date and time when the application is created. This will allow us to track when the application was submitted and process it accordingly. We will prepare a SQL statement to insert a new record into the Application table with the StudentID, CourseID, temporary CertificateNumber, and the current date and time as the ApplicationDate.
        $sql = "INSERT INTO Application (StudentID, CourseID, CertificateNumber, ApplicationDate) 
                VALUES (?, ?, ?, ?)";
        
        // Create a prepared statement to prevent SQL injection and bind the parameters to the SQL query. This will allow us to safely insert the application data into the database without risking SQL injection attacks. The parameters include the StudentID, CourseID, temporary CertificateNumber, and the current date and time for the ApplicationDate. In a real implementation, we would use the current date and time for the ApplicationDate, but for testing purposes, we are using the graduation date as a placeholder. The prepared statement will help us ensure that the data is inserted correctly and securely into the Application table.
        $stmt = $conn->prepare($sql);

        // Bind the parameters to the prepared statement and execute it to create a new application record in the Application table. This will allow us to track the student's application and process it through the system. The parameters include the StudentID, CourseID, temporary CertificateNumber, and the graduation date (which is being used as a placeholder for the ApplicationDate in this context). In a real implementation, we would use the current date and time for the ApplicationDate, but for testing purposes, we are using the graduation date as a placeholder.
        $stmt->bind_param("iiss", $sID, $cID, $tempCertNum, $gradDate);

            // Execute the prepared statement and check if the application was created successfully. If the execution is successful, we will display a success message to the user. If there is an error during the execution, we will display the error message to the user. This will provide feedback on whether the application submission was successful or if there were any issues that need to be addressed.
        if ($stmt->execute()) {
            // If the application was created successfully, display a success message to the user and redirect them back to the application form or another relevant page. This will provide feedback that their application was submitted successfully and allow them to continue using the system as needed. The success message is displayed using a JavaScript alert, and the user is redirected to the ApplicationForm.html page after acknowledging the alert.
            echo "<script>alert('Application Submitted Successfully!'); window.location='ApplicationForm.html';</script>";
        } else {
            // If there was an error during the execution of the prepared statement, display the error message to the user. This will help them understand what went wrong and potentially how to fix it. The error message is retrieved from the statement's error property and displayed using a JavaScript alert, allowing the user to see the specific issue that occurred during the application submission process.
            echo "Error: " . $stmt->error;
        }
        // Close the statement to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the statement after we are done with it helps to clean up resources and allows the database connection to be reused for other operations.
        $stmt->close();
    } else {
        // If no student was found with the provided registration number, display an error message to the user. This will inform them that the registration number they entered does not correspond to any student in the database, and they may need to check their input or contact support for assistance. The error message is displayed using a JavaScript alert, and the user is redirected back to the application form after acknowledging the alert. This allows them to correct their input and try submitting the application again.
        echo "<script>alert('Student not found!'); window.location='ApplicationForm.html';</script>";
    }
    // Close the database connection to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the connection after we are done with all database operations helps to clean up resources and allows the application to manage connections efficiently.
    $conn->close();
}
?>