<?php
// This file processes the certificate generation logic when a regulator approves an application. It generates a unique certificate number based on the university code, application date, course code, and application ID. It then updates the Application table with the generated certificate number, the specific course ID, and the regulator's user ID who approved the application. Finally, it redirects to the view_issued.php page with a success message.

// Start the session to access session variables and include the database connection file
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Check if we have the Application ID and a logged-in User
// We need the Application ID to know which application we're processing, and we need the User ID from the session to record who approved the certificate
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    // Get the Application ID from the GET parameters and the current Regulator's User ID from the session
    $appID = $_GET['id'];
    // Get the current Regulator's User ID from the session to record who approved the certificate
    $currentRegulatorID = $_SESSION['user_id'];

    // 1. Fetch data: UNI code, Course code, Reg No, and Graduation Year
    // We join Course via the Student's CourseName to ensure the codes match perfectly
    $query = "SELECT 
                u.UniversityCode, 
                c.CourseCode, 
                c.CourseID,
                s.RegistrationNumber, 
                s.DateOfGraduation,
                a.ApplicationDate
              FROM Application a
              JOIN Student s ON a.StudentID = s.StudentID
              JOIN University u ON s.UniversityID = u.UniversityID
              JOIN Course c ON s.CourseName = c.CourseName
              WHERE a.ApplicationID = ? LIMIT 1";
    
    // Create a prepared statement and execute the query
    $stmt = $conn->prepare($query);
    // Bind the Application ID parameter to the prepared statement and execute it
    $stmt->bind_param("i", $appID);
    // Execute the prepared statement and get the result
    $stmt->execute();
    // Fetch the result of the query and store it in an associative array
    $result = $stmt->get_result();

    // Check if we got a result and then proceed to generate the certificate number
    if ($row = $result->fetch_assoc()) {
        // Extract the necessary data from the result to generate the certificate number
        $uCode = $row['UniversityCode'];
        $cCode = $row['CourseCode'];
        $reg = $row['RegistrationNumber'];
        $appDate = date('Ymd', strtotime($row['ApplicationDate']));
        
        // 2. The Formal Concatenation Logic
        // Format: UNI + APPDATE + COURSE + APP_ID (As per the requirement)
        $serialNumber = strtoupper($uCode . $appDate . $cCode . $appID);

        // 3. Update the Application table with Serial, specific CourseID, and Regulator ID
        $update = $conn->prepare("UPDATE Application SET CertificateNumber = ?, CourseID = ?, ApprovedBy = ? WHERE ApplicationID = ?");
        // Bind the parameters to the prepared statement and execute it
        $update->bind_param("siii", $serialNumber, $row['CourseID'], $currentRegulatorID, $appID);

        // Execute the update statement and check if it was successful
        if ($update->execute()) {
            // If the update was successful, show a success message with the generated certificate number and redirect to the view_issued.php page
            echo "<script>alert('Certificate Generated: $serialNumber processed by " . $_SESSION['realname'] . "'); window.location='view_issued.php';</script>";
        } else {
            // If there was an error during the update, display the error message
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // If no matching application data was found, display an error message
        echo "Application data not found.";
    }
} else {
    // If the required parameters are missing, display an access denied message
    echo "Access Denied: Missing Application ID or Session User ID.";
}
?>