<?php
session_start();
include 'db_connect.php';

// Security Check: Only Admins can update universities
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html");
    exit();
}

$id = $_GET['id'];

// 1. Fetch current university details
$stmt = $conn->prepare("SELECT * FROM University WHERE UniversityID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$university = $stmt->get_result()->fetch_assoc();

// 2. Handle Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['u_name'];
    $ucode = $_POST['u_code'];

    $update = $conn->prepare("UPDATE University 
                              SET UniversityName=?, UniversityCode=? 
                              WHERE UniversityID=?");
    $update->bind_param("ssi", $uname, $ucode, $id);

    if ($update->execute()) {
        echo "<script>alert('University Updated Successfully!'); window.location='manage_university.php';</script>";
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
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 450px; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; cursor: pointer; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Update University: <?php echo htmlspecialchars($university['UniversityName']); ?></h2>
        <form method="POST">
            <label>University Name:</label>
            <input type="text" name="u_name" value="<?php echo htmlspecialchars($university['UniversityName']); ?>" required>
            
            <label>University Code:</label>
            <input type="text" name="u_code" value="<?php echo htmlspecialchars($university['UniversityCode']); ?>" required>
            
            <button type="submit">Save Changes</button>
            <p style="text-align:center;"><a href="manage_university.php" style="color:#7f8c8d; text-decoration:none;">← Back to List</a></p>
        </form>
    </div>
</body>
</html>
