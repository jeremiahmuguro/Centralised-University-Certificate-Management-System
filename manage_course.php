<?php
// This page allows administrators to manage courses, including updating and deleting course records. It includes safety checks to prevent deletion of courses linked to students or applications.
// Start the session to manage user login state
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins can manage courses
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
    // Get the course ID to be deleted from the GET parameters
    $id = $_GET['delete_id'];

    // Safety check: Prevent deletion if course is linked to students or applications
    $checkStudents = $conn->prepare("SELECT COUNT(*) FROM Student WHERE CourseName IN (SELECT CourseName FROM Course WHERE CourseID = ?)");
    // Bind the course ID parameter and execute the query
    $checkStudents->bind_param("i", $id);
    // Execute the query and bind the result to $studentCount
    $checkStudents->execute();
    // Fetch the count of linked students
    $checkStudents->bind_result($studentCount);
    // Fetch the result to get the count of linked students 
    $checkStudents->fetch();
    // Close the statement after fetching the result
    $checkStudents->close();

    // Check for linked applications
    $checkApplications = $conn->prepare("SELECT COUNT(*) FROM Application WHERE CourseID = ?");
    // Bind the course ID parameter and execute the query
    $checkApplications->bind_param("i", $id);
    // Execute the query and bind the result to $appCount
    $checkApplications->execute();
    // Fetch the count of linked applications
    $checkApplications->bind_result($appCount);
    // Fetch the result to get the count of linked applications
    $checkApplications->fetch();
    // Close the statement after fetching the result
    $checkApplications->close();

    // If there are linked students or applications, prevent deletion and show an error message
    if ($studentCount > 0 || $appCount > 0) {
        echo "<script>alert('Error: This course is linked to students or applications and cannot be deleted.'); window.location='manage_course.php';</script>";
    } else {
        $delete = $conn->prepare("DELETE FROM Course WHERE CourseID = ?");
        $delete->bind_param("i", $id);

        // Execute the delete statement and check if it was successful
        if ($delete->execute()) {
            echo "<script>alert('Course removed successfully.'); window.location='manage_course.php';</script>";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Fetch updated list of courses
$result = $conn->query("SELECT CourseID, CourseName, CourseCode, LevelOfProgram, UniversityID, Duration FROM Course");
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
    <h2>Course Management Portal</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Level</th>
                <th>University ID</th>
                <th>Duration (Years)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['CourseID']; ?></td>
                <td><?php echo htmlspecialchars($row['CourseName']); ?></td>
                <td><?php echo htmlspecialchars($row['CourseCode']); ?></td>
                <td><?php echo $row['LevelOfProgram']; ?></td>
                <td><?php echo $row['UniversityID']; ?></td>
                <td><?php echo $row['Duration']; ?></td>
                <td>
                    <a href="update_course.php?id=<?php echo $row['CourseID']; ?>" class="btn btn-edit">Update</a>
                    <a href="manage_course.php?delete_id=<?php echo $row['CourseID']; ?>" 
                       class="btn btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
