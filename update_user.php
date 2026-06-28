<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html");
    exit();
}

$id = $_GET['id'];

// 1. Fetch current details (Including phone and username)
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Handle Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['real_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $new_pass = $_POST['password'];

    if (!empty($new_pass)) {
        // Update everything INCLUDING new password (hashed)
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE Users SET RealName=?, UserPhone=?, UserEmail=?, UserName=?, UserPassword=?, UserRole=? WHERE UserID=?");
        $update->bind_param("ssssssi", $name, $phone, $email, $username, $hashed_pass, $role, $id);
    } else {
        // Update everything EXCEPT password
        $update = $conn->prepare("UPDATE Users SET RealName=?, UserPhone=?, UserEmail=?, UserName=?, UserRole=? WHERE UserID=?");
        $update->bind_param("sssssi", $name, $phone, $email, $username, $role, $id);
    }
    
    if ($update->execute()) {
        echo "<script>alert('User Profile Updated Successfully!'); window.location='manage_users.php';</script>";
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
        <h2>Update User: <?php echo htmlspecialchars($user['UserName']); ?></h2>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="real_name" value="<?php echo htmlspecialchars($user['RealName']); ?>" required>
            
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['UserPhone']); ?>" required>
            
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['UserEmail']); ?>" required>
            
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['UserName']); ?>" required>
            
            <label>New Password (leave blank to keep current):</label>
            <input type="password" name="password" placeholder="********">
            
            <label>User Role:</label>
            <select name="role">
                <option value="Admin" <?php if($user['UserRole'] == 'Admin') echo 'selected'; ?>>Admin</option>
                <option value="UniversityStaff" <?php if($user['UserRole'] == 'UniversityStaff') echo 'selected'; ?>>University Staff</option>
                <option value="Regulator" <?php if($user['UserRole'] == 'Regulator') echo 'selected'; ?>>Regulator</option>
            </select>
            
            <button type="submit">Save Changes</button>
            <p style="text-align:center;"><a href="manage_users.php" style="color:#7f8c8d; text-decoration:none;">← Back to List</a></p>
        </form>
    </div>
</body>
</html>