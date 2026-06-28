<?php
//start the session to access session variables
session_start();
//include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins can update students
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html");
    exit();
}

$id = $_GET['id'];

// 1. Fetch current student details
$stmt = $conn->prepare("SELECT * FROM Student WHERE StudentID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// 2. Handle Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sname   = $_POST['s_name'];
    $regNum  = $_POST['reg_num'];
    $email   = $_POST['email'];
    $admDate = $_POST['adm_date'];
    $gradDate = $_POST['grad_date']; // optional
    $course  = $_POST['course_name'];
    $level   = $_POST['level'];
    $uniID   = $_POST['uni_id'];

    $update = $conn->prepare("UPDATE Student 
                              SET StudentName=?, RegistrationNumber=?, StudentEmail=?, DateOfAdmission=?, DateOfGraduation=?, CourseName=?, LevelOfProgram=?, UniversityID=? 
                              WHERE StudentID=?");
    $update->bind_param("sssssssii", $sname, $regNum, $email, $admDate, $gradDate, $course, $level, $uniID, $id);

    if ($update->execute()) {
        echo "<script>alert('Student Updated Successfully!'); window.location='manage_student.php';</script>";
    } else {
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
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 500px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; cursor: pointer; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Update Student: <?php echo htmlspecialchars($student['StudentName']); ?></h2>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="s_name" value="<?php echo htmlspecialchars($student['StudentName']); ?>" required>
            
            <label>Registration Number:</label>
            <input type="text" name="reg_num" value="<?php echo htmlspecialchars($student['RegistrationNumber']); ?>" required>
            
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['StudentEmail']); ?>" required>
            
            <label>Date of Admission:</label>
            <input type="date" name="adm_date" value="<?php echo $student['DateOfAdmission']; ?>" required>
            
            <label>Date of Graduation (optional):</label>
            <input type="date" name="grad_date" value="<?php echo $student['DateOfGraduation']; ?>">
            
            <label>Course Name:</label>
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($student['CourseName']); ?>" required>
            
            <label>Level of Program:</label>
            <select name="level" required>
                <option value="Certificate" <?php if($student['LevelOfProgram'] == 'Certificate') echo 'selected'; ?>>Certificate</option>
                <option value="Diploma" <?php if($student['LevelOfProgram'] == 'Diploma') echo 'selected'; ?>>Diploma</option>
                <option value="Bachelor" <?php if($student['LevelOfProgram'] == 'Bachelor') echo 'selected'; ?>>Bachelor</option>
                <option value="Master" <?php if($student['LevelOfProgram'] == 'Master') echo 'selected'; ?>>Master</option>
                <option value="PhD" <?php if($student['LevelOfProgram'] == 'PhD') echo 'selected'; ?>>PhD</option>
            </select>
            
            <label>University ID:</label>
            <input type="number" name="uni_id" value="<?php echo $student['UniversityID']; ?>" required>
            
            <button type="submit">Save Changes</button>
            <p style="text-align:center;"><a href="manage_student.php" style="color:#7f8c8d; text-decoration:none;">← Back to List</a></p>
        </form>
    </div>
</body>
</html>
