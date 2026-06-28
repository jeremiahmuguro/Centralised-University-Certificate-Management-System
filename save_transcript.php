<?php
/*
// This file handles the logic for saving a new transcript to the database when the transcript upload form is submitted. It processes the form data, validates it, and inserts a new record into the Transcript table in the database. After successfully adding the transcript, it provides feedback to the user and redirects them back to the transcript upload page or form.


// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the form was submitted using the POST method. This ensures that we only process the transcript upload when the form is submitted, and not when the page is accessed directly.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names must match the 'name' attributes in the HTML input tags of the transcript upload form. We will get the student's registration number from the form submission, which will be used to link the uploaded transcript to the correct student record in the database.
    $regNum = $_POST['reg_num'];

    // STEP 1: Search for the StudentID using the RegistrationNumber provided
    // Prepare a SQL statement to look up the StudentID based on the provided RegistrationNumber. This will allow us to find the internal ID of the student in the database, which we will use to link the uploaded transcript to the correct student record. We will use a prepared statement to prevent SQL injection and ensure that the data is queried safely from the database. The parameter is the registration number, which corresponds to the RegistrationNumber column in the Student table.
    $lookupSql = "SELECT StudentID FROM Student WHERE RegistrationNumber = ?";
    // Create a prepared statement to prevent SQL injection and bind the parameter to the SQL query. This will allow us to safely query the database for the student ID based on the provided registration number without risking SQL injection attacks. The parameter is the registration number, which corresponds to the RegistrationNumber column in the Student table. The prepared statement will help us ensure that the data is queried correctly and securely from the database.
    $lookupStmt = $conn->prepare($lookupSql);
    // Bind the registration number parameter to the prepared statement and execute it to look up the StudentID. This will allow us to find the internal ID of the student in the database, which we will use to link the uploaded transcript to the correct student record. The parameter is the registration number, which corresponds to the RegistrationNumber column in the Student table. The prepared statement will help us ensure that the data is queried correctly and securely from the database.
    $lookupStmt->bind_param("s", $regNum);
    // Execute the prepared statement and get the result to check if a student with the provided registration number exists. If a student is found, we will retrieve their StudentID to link the uploaded transcript. If no student is found, we will display an error message to the user and redirect them back to the transcript upload form. This will provide feedback on whether the student was found and allow them to correct any issues with the registration number if needed. The result of the query is checked to see if any rows were returned, indicating that a student with the provided registration number exists in the database.
    $lookupStmt->execute();
    // Get the result of the query to check if a student with the provided registration number exists. If a student is found, we will retrieve their StudentID to link the uploaded transcript. If no student is found, we will display an error message to the user and redirect them back to the transcript upload form. This will provide feedback on whether the student was found and allow them to correct any issues with the registration number if needed. The result of the query is checked to see if any rows were returned, indicating that a student with the provided registration number exists in the database.
    $result = $lookupStmt->get_result();
    // Check if a student with the provided registration number was found. If no student is found, display an error message and redirect back to the transcript upload form. If a student is found, retrieve their StudentID to link the uploaded transcript. This will ensure that the transcript is correctly associated with the right student in the database.

    if ($result->num_rows == 0) {
        // No student found with that Reg Number
        echo "<script>alert('Error: No student found with Registration Number: $regNum'); window.location='TranscriptForm.html';</script>";
        exit();
    }

    // Student found! Get their internal ID
    // Fetch the StudentID from the result set to link the uploaded transcript to the correct student record in the database. The StudentID will be used as a foreign key in the Transcript table to establish the relationship between the transcript and the student. This ensures that we can easily retrieve and manage transcripts based on the associated student records in the future.
    $row = $result->fetch_assoc();
    // Store the StudentID for later use when inserting the transcript record into the Transcript table. This will allow us to link the uploaded transcript to the correct student record in the database, ensuring that we can manage and retrieve transcripts based on the associated student records in the future

    // STEP 2: Handle the File Upload
    $targetDir = "uploads/";
    
    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalName = basename($_FILES["transcript_file"]["name"]);
    // Create a unique name to prevent overwriting: RegNumber_Timestamp.pdf
    $newFileName = $regNum . "_" . time() . ".pdf"; 
    $targetFilePath = $targetDir . $newFileName;

    // Move file to the 'uploads' folder
    if (move_uploaded_file($_FILES["transcript_file"]["tmp_name"], $targetFilePath)) {
        
        // STEP 3: Insert the file path into the Transcript table linked to StudentID
        $sql = "INSERT INTO Transcript (StudentID, FilePath) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $studentID, $targetFilePath);

        if ($stmt->execute()) {
            echo "<script>alert('Transcript successfully linked to Student $regNum'); window.location='TranscriptForm.html';</script>";
        } else {
            echo "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // If there was an error moving the uploaded file, display an error message to the user. This will help them understand that there was an issue with the file upload process and allow them to try again. The error message is displayed using a JavaScript alert, and the user is redirected back to the TranscriptForm.html page after acknowledging the alert, giving them the opportunity to correct any issues with the file upload and resubmit the form.
        echo "<script>alert('Error: Failed to move uploaded file.'); window.location='TranscriptForm.html';</script>";
    }
    // Close the lookup statement to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the statement after we are done with it helps to clean up resources and allows the database connection to be reused for other operations.
    $lookupStmt->close();
    // Close the database connection to free up resources. This is important for maintaining good performance and ensuring that database connections are not unnecessarily held open. Closing the connection after we are done with all database operations helps to clean up resources and allows the application to manage connections efficiently.
    $conn->close();
}*/



include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regNum = $_POST['reg_num'];

    // STEP 1: Search for the StudentID using the RegistrationNumber provided
    $lookupSql = "SELECT StudentID FROM Student WHERE RegistrationNumber = ?";
    $lookupStmt = $conn->prepare($lookupSql);
    $lookupStmt->bind_param("s", $regNum);
    $lookupStmt->execute();
    $result = $lookupStmt->get_result();

    if ($result->num_rows == 0) {
        // No student found with that Reg Number
        echo "<script>alert('Error: No student found with Registration Number: $regNum'); window.location='TranscriptForm.html';</script>";
        exit();
    }

    // Student found! Get their internal ID
    $row = $result->fetch_assoc();
    $studentID = $row['StudentID'];

    // STEP 2: Handle the File Upload
    $targetDir = "uploads/";
    
    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalName = basename($_FILES["transcript_file"]["name"]);
    // Create a unique name to prevent overwriting: RegNumber_Timestamp.pdf
    $newFileName = $regNum . "_" . time() . ".pdf"; 
    $targetFilePath = $targetDir . $newFileName;

    // Move file to the 'uploads' folder
    if (move_uploaded_file($_FILES["transcript_file"]["tmp_name"], $targetFilePath)) {
        
        // STEP 3: Insert the file path into the Transcript table linked to StudentID
        $sql = "INSERT INTO Transcript (StudentID, FilePath) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $studentID, $targetFilePath);

        if ($stmt->execute()) {
            echo "<script>alert('Transcript successfully linked to Student $regNum'); window.location='TranscriptForm.html';</script>";
        } else {
            echo "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error: Failed to move uploaded file.'); window.location='TranscriptForm.html';</script>";
    }

    $lookupStmt->close();
    $conn->close();
}

?>