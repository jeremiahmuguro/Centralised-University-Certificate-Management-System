<?php
// update_course.php - Admin can update course details
// Security: Only Admins can access this page
// This page allows an Admin to update the details of a specific course. It first checks if the user is logged in and has the 'Admin' role. If not, it redirects them to the login page. Then, it retrieves the current details of the course using its ID passed via GET parameters. The form is pre-filled with the existing course details, allowing the Admin to make changes. Upon form submission, it updates the course information in the database and provides feedback on whether the update was successful or if there was an error.

// Start the session to manage user authentication and access control
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins can update courses
// Check if the user is logged in and has the 'Admin' role. If not, redirect them to the login page. This ensures that only authorized users can access this page and perform course updates, maintaining the security of the application.
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // If the user is not logged in or does not have the 'Admin' role, redirect them to the login page. This prevents unauthorized access to the course update functionality and ensures that only Admin users can make changes to course details.
    header("Location: index.html");
    // Ensure no further code is executed after the redirect. This is important to prevent any unintended processing of the course update logic if the user is not authorized. By calling exit() after the header redirect, we ensure that the script stops executing and the user is properly redirected to the login page without any further processing of the course update logic.
    exit();
}

// Get the course ID from the GET parameters to identify which course to update. This ID is used to fetch the current details of the course and to perform the update operation when the form is submitted. The 'id' parameter must be passed in the URL for this page to function correctly, as it specifies which course's details are being updated.
$id = $_GET['id'];

// 1. Fetch current course details
// Prepare a SQL statement to select the course details from the Course table where the CourseID matches the provided ID. This allows us to retrieve the current information of the course that is being updated, which will be displayed in the form for the Admin to edit. The prepared statement helps to prevent SQL injection and ensures that we are safely querying the database for the course details based on the provided ID.
$stmt = $conn->prepare("SELECT * FROM Course WHERE CourseID = ?");
// Bind the course ID parameter to the prepared statement and execute it to fetch the current details of the course. This will allow us to retrieve the existing information of the course that is being updated, which will be displayed in the form for the Admin to edit. The result of the query is fetched as an associative array, which can be used to pre-fill the form fields with the current course details. The 'id' parameter corresponds to the CourseID in the Course table, and the prepared statement ensures that the query is executed safely without risking SQL injection attacks.
$stmt->bind_param("i", $id);
// Execute the prepared statement and fetch the current details of the course. This will allow us to retrieve the existing information of the course that is being updated, which will be displayed in the form for the Admin to edit. The result of the query is fetched as an associative array, which can be used to pre-fill the form fields with the current course details. The 'id' parameter corresponds to the CourseID in the Course table, and the prepared statement ensures that the query is executed safely without risking SQL injection attacks.
$stmt->execute();
// Fetch the course details as an associative array to be used for pre-filling the form fields. This allows the Admin to see the current information of the course and make necessary changes. The fetched data includes all relevant details of the course, such as CourseName, CourseCode, LevelOfProgram, UniversityID, and Duration, which will be displayed in the form for editing. The 'id' parameter corresponds to the CourseID in the Course table, and the prepared statement ensures that the query is executed safely without risking SQL injection attacks. The fetched course details are stored in the $course variable, which can be used to populate the form fields with the existing course information. 
$course = $stmt->get_result()->fetch_assoc();

// 2. Handle Update Logic
// Check if the form was submitted using the POST method. This ensures that we only process the update when the form is submitted, and not when the page is accessed directly. If the form is submitted, we will retrieve the updated course details from the POST data and execute an UPDATE query to save the changes to the database. The form fields must have the correct 'name' attributes for this to work correctly, as they correspond to the keys in the $_POST array that we will use to get the updated values. This check helps to prevent errors and ensures that we are only processing valid update attempts from the form submission. If the form is not submitted, we will simply display the form with the current course details for the Admin to edit. T
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve updated course details from the POST data. This includes the course name, course code, level of program, university ID, and duration. These values correspond to the input fields in the form that the Admin will fill out when updating the course details. The 'name' attributes of the form fields must match these keys for this to work correctly, as they are used to access the updated values from the $_POST array. This allows us to get the new information that the Admin has entered for the course and use it to update the course details in the database when we execute the UPDATE query. The retrieved values are stored in variables that will be used in the UPDATE query to save the changes to the database. 
    $cname = $_POST['c_name'];
    $ccode = $_POST['c_code'];
    $level = $_POST['level'];
    $uid   = $_POST['u_id'];
    $duration = $_POST['duration'];

    // Execute the UPDATE query to save the changes to the database. This will update the course details in the Course table based on the provided course ID. The prepared statement is used to prevent SQL injection and ensure that the query is executed safely with the updated values. The parameters correspond to the columns in the Course table that we want to update, and the course ID is used in the WHERE clause to specify which course record should be updated with the new information provided by the Admin through the form submission. After executing the UPDATE query, we check if it was successful and provide feedback to the user accordingly. If the update was successful, we display a success message and redirect back to the manage_course.php page. If there was an error during the update, we display an error message with details about what went wrong. 
    $update = $conn->prepare("UPDATE Course 
                              SET CourseName=?, CourseCode=?, LevelOfProgram=?, UniversityID=?, Duration=? 
                              WHERE CourseID=?");
                              // The prepared statement is used to prevent SQL injection and ensure that the query is executed safely with the updated values. The parameters correspond to the columns in the Course table that we want to update, and the course ID is used in the WHERE clause to specify which course record should be updated with the new information provided by the Admin through the form submission. After executing the UPDATE query, we check if it was successful and provide feedback to the user accordingly. If the update was successful, we display a success message and redirect back to the manage_course.php page. If there was an error during the update, we display an error message with details about what went wrong. The 'name' attributes of the form fields must match the keys used to retrieve the updated values from the $_POST array for this to work correctly, as they are used to access the updated values that the Admin has entered for the course and use them to update the course details in the database when we execute the UPDATE query.  
    $update->bind_param("sssiii", $cname, $ccode, $level, $uid, $duration, $id);
// The prepared statement is used to prevent SQL injection and ensure that the query is executed safely with the updated values. The parameters correspond to the columns in the Course table that we want to update, and the course ID is used in the WHERE clause to specify which course record should be updated with the new information provided by the Admin through the form submission. 
    if ($update->execute()) {
        // If the update was successful, display a success message and redirect back to the manage_course.php page. This provides feedback to the user that the course details were updated successfully and allows them to return to the course management page to see the updated information. The 'name' attributes of the form fields must match the keys used to retrieve the updated values from the $_POST array for this to work correctly, as they are used to access the updated values that the Admin has entered for the course and use them to update the course details in the database when we execute the UPDATE query.  
        echo "<script>alert('Course Updated Successfully!'); window.location='manage_course.php';</script>";
    } else {
        // If there was an error during the update, display an error message with details about what went wrong. This provides feedback to the user that there was an issue with updating the course details and allows them to understand what the problem is. 
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="reg-styles.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f4f6f9; padding: 20px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 450px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; cursor: pointer; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Update Course: <?php echo htmlspecialchars($course['CourseName']); ?></h2>
        <form method="POST">
            <label>Course Name:</label>
            <input type="text" name="c_name" value="<?php echo htmlspecialchars($course['CourseName']); ?>" required>
            
            <label>Course Code:</label>
            <input type="text" name="c_code" value="<?php echo htmlspecialchars($course['CourseCode']); ?>" required>
            
            <label>Level of Program:</label>
            <select name="level" required>
                <option value="Certificate" <?php if($course['LevelOfProgram'] == 'Certificate') echo 'selected'; ?>>Certificate</option>
                <option value="Diploma" <?php if($course['LevelOfProgram'] == 'Diploma') echo 'selected'; ?>>Diploma</option>
                <option value="Bachelor" <?php if($course['LevelOfProgram'] == 'Bachelor') echo 'selected'; ?>>Bachelor</option>
                <option value="Master" <?php if($course['LevelOfProgram'] == 'Master') echo 'selected'; ?>>Master</option>
                <option value="PhD" <?php if($course['LevelOfProgram'] == 'PhD') echo 'selected'; ?>>PhD</option>
            </select>
            
            <label>University ID:</label>
            <input type="number" name="u_id" value="<?php echo htmlspecialchars($course['UniversityID']); ?>" required>
            
            <label>Duration (Years):</label>
            <input type="number" name="duration" value="<?php echo htmlspecialchars($course['Duration']); ?>" required>
            
            <button type="submit">Save Changes</button>
            <p style="text-align:center;"><a href="manage_course.php" style="color:#7f8c8d; text-decoration:none;">← Back to List</a></p>
        </form>
    </div>
</body>
</html>
