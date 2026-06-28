<?php
// This page allows administrators to manage students, including updating and deleting student records. It includes safety checks to prevent deletion of students linked to transcripts or applications.

// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins can manage students
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
    // Get the student ID to be deleted from the GET parameters
    $id = $_GET['delete_id'];

    // Safety check: Prevent deletion if student is linked to transcripts or applications
    // Prepare a statement to check for linked transcripts
    $checkTranscripts = $conn->prepare("SELECT COUNT(*) FROM Transcript WHERE StudentID = ?");
    // Bind the student ID parameter and execute the query
    $checkTranscripts->bind_param("i", $id);
    // Execute the query and bind the result to $transCount
    $checkTranscripts->execute();
    // Fetch the count of linked transcripts
    $checkTranscripts->bind_result($transCount);
    // Fetch the result to get the count of linked transcripts
    $checkTranscripts->fetch();
    // Close the statement after fetching the result
    $checkTranscripts->close();
    // Prepare a statement to check for linked applications
    $checkApplications = $conn->prepare("SELECT COUNT(*) FROM Application WHERE StudentID = ?");
    // Bind the student ID parameter and execute the query
    $checkApplications->bind_param("i", $id);
    // Execute the query and bind the result to $appCount
    $checkApplications->execute();
    // Fetch the count of linked applications
    $checkApplications->bind_result($appCount);
    // Fetch the result to get the count of linked applications
    $checkApplications->fetch();
    // Close the statement after fetching the result
    $checkApplications->close();

    // If there are linked transcripts or applications, prevent deletion and show an error message
    // Check if the student is linked to any transcripts or applications
    if ($transCount > 0 || $appCount > 0) {
        echo "<script>alert('Error: This student is linked to transcripts or applications and cannot be deleted.'); window.location='manage_student.php';</script>";
    } else {
        // If there are no linked records, proceed with deletion
        $delete = $conn->prepare("DELETE FROM Student WHERE StudentID = ?");
        // Bind the student ID parameter and execute the delete statement
        $delete->bind_param("i", $id);

        // Execute the delete statement and check if it was successful
        if ($delete->execute()) {
            echo "<script>alert('Student removed successfully.'); window.location='manage_student.php';</script>";
        } else {
            // If there was an error during deletion, display the error message
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Fetch updated list of students
// Execute a query to retrieve student records from the database
$result = $conn->query("SELECT StudentID, RegistrationNumber, StudentName, StudentEmail, DateOfAdmission, DateOfGraduation, CourseName, LevelOfProgram, UniversityID FROM Student");
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
    <h2>Student Management Portal</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Reg Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Admission Date</th>
                <th>Graduation Date</th>
                <th>Course</th>
                <th>Level</th>
                <th>University ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['StudentID']; ?></td>
                <td><?php echo htmlspecialchars($row['RegistrationNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
                <td><?php echo htmlspecialchars($row['StudentEmail']); ?></td>
                <td><?php echo $row['DateOfAdmission']; ?></td>
                <td><?php echo $row['DateOfGraduation']; ?></td>
                <td><?php echo htmlspecialchars($row['CourseName']); ?></td>
                <td><?php echo $row['LevelOfProgram']; ?></td>
                <td><?php echo $row['UniversityID']; ?></td>
                <td>
                    <a href="update_student.php?id=<?php echo $row['StudentID']; ?>" class="btn btn-edit">Update</a>
                    <a href="manage_student.php?delete_id=<?php echo $row['StudentID']; ?>" 
                       class="btn btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
