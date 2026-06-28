<?php
// This page allows administrators to manage universities, including updating and deleting university records. It includes safety checks to prevent deletion of universities linked to courses or students.

// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins can manage universities
// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}

// Handle Delete Logic
// Check if delete_id is set in the GET parameters
if (isset($_GET['delete_id'])) {
    // Get the university ID to be deleted from the GET parameters
    $id = $_GET['delete_id'];

    // Safety check: Prevent deletion if university is linked to courses or students
    // Prepare a statement to check for linked courses
    $checkCourses = $conn->prepare("SELECT COUNT(*) FROM Course WHERE UniversityID = ?");
    // Bind the university ID parameter and execute the query
    $checkCourses->bind_param("i", $id);
    // Execute the query and bind the result to $courseCount
    $checkCourses->execute();
    // Fetch the count of linked courses
    $checkCourses->bind_result($courseCount);
    // Fetch the result to get the count of linked courses
    $checkCourses->fetch();
    // Close the statement after fetching the result
    $checkCourses->close();

    // Prepare a statement to check for linked students
    $checkStudents = $conn->prepare("SELECT COUNT(*) FROM Student WHERE UniversityID = ?");
    // Bind the university ID parameter and execute the query
    $checkStudents->bind_param("i", $id);
    // Execute the query and bind the result to $studentCount
    $checkStudents->execute();
    // Fetch the count of linked students
    $checkStudents->bind_result($studentCount);
    // Fetch the result to get the count of linked students
    $checkStudents->fetch();
    // Close the statement after fetching the result
    $checkStudents->close();

    // If there are linked courses or students, prevent deletion and show an error message
    // Check if the university is linked to any courses or students
    if ($courseCount > 0 || $studentCount > 0) {
        echo "<script>alert('Error: This university is linked to courses or students and cannot be deleted.'); window.location='manage_university.php';</script>";
    } else {
        // If there are no linked records, proceed with deletion
        $delete = $conn->prepare("DELETE FROM University WHERE UniversityID = ?");
        // Bind the university ID parameter and execute the delete statement
        $delete->bind_param("i", $id);
        // Execute the delete statement and check if it was successful
        if ($delete->execute()) {
            echo "<script>alert('University removed successfully.'); window.location='manage_university.php';</script>";
        } else {
            // If there was an error during deletion, display the error message
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Fetch updated list of universities
// Execute a query to retrieve university records from the database
$result = $conn->query("SELECT UniversityID, UniversityName, UniversityCode FROM University");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: white; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 5px; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <h2>University Management Portal</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>University Name</th>
                <th>University Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['UniversityID']; ?></td>
                <td><?php echo htmlspecialchars($row['UniversityName']); ?></td>
                <td><?php echo htmlspecialchars($row['UniversityCode']); ?></td>
                <td>
                    <a href="update_university.php?id=<?php echo $row['UniversityID']; ?>" class="btn btn-edit">Update</a>
                    <a href="manage_university.php?delete_id=<?php echo $row['UniversityID']; ?>" 
                       class="btn btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this university?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
