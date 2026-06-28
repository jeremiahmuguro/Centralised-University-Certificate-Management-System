<?php
// This page serves as the welcome dashboard for administrators, providing an overview of system statistics and recent activities.

// It checks if the user is logged in and has the admin role before allowing access. It retrieves counts of universities, students, issued certificates, and users from the database to display on the dashboard. Additionally, it shows a table of recently issued certificates for quick reference. Only administrators can access this page, ensuring that sensitive system information is protected.

// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check
// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}

// 1. Fetch System-Wide Counts (Scenario 1)
// Execute queries to get counts of universities, students, issued certificates, and users for display on the dashboard
$uniCount = $conn->query("SELECT COUNT(*) as total FROM University")->fetch_assoc()['total'];
// Get the total number of students from the Student table
$studentCount = $conn->query("SELECT COUNT(*) as total FROM Student")->fetch_assoc()['total'];
// Get the total number of issued certificates by counting applications with a certificate number that does not start with 'PENDING'
$issuedCount = $conn->query("SELECT COUNT(*) as total FROM Application WHERE CertificateNumber NOT LIKE 'PENDING%'")->fetch_assoc()['total'];
// Get the total number of system users from the Users table
$userCount = $conn->query("SELECT COUNT(*) as total FROM Users")->fetch_assoc()['total'];

// 2. Fetch Recent Activity (Bonus for Scenario 1)
// Execute a query to get the 5 most recently issued certificates, including the student name, university code, and application date
$recentCerts = $conn->query("SELECT s.StudentName, u.UniversityCode, a.ApplicationDate 
                             FROM Application a 
                             JOIN Student s ON a.StudentID = s.StudentID 
                             JOIN University u ON s.UniversityID = u.UniversityID 
                             WHERE a.CertificateNumber NOT LIKE 'PENDING%' 
                             ORDER BY a.ApplicationDate DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CUCMS Overview</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 30px; background: #f4f6f9; color: #333; }
        .welcome-header { margin-bottom: 30px; }
        
        /* Card Grid Layout */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid #2c3e50;
            transition: transform 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { margin: 0; font-size: 0.9rem; color: #7f8c8d; text-transform: uppercase; }
        .stat-card .number { font-size: 2.2rem; font-weight: bold; margin: 10px 0; color: #2c3e50; }

        /* Specific Colors for context */
        .card-uni { border-left-color: #3498db; }
        .card-student { border-left-color: #27ae60; }
        .card-cert { border-left-color: #f1c40f; }
        .card-users { border-left-color: #e74c3c; }

        /* Recent Activity Table */
        .activity-section { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .activity-section h2 { font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }
        th { color: #95a5a6; font-weight: 500; }
    </style>
</head>
<body>

<div class="welcome-header">
    <h1>System Overview</h1>
    <p>Welcome back, <strong><?php echo $_SESSION['realname']; ?></strong>. Here is the current system status.</p>
</div>

<div class="card-grid">
    <div class="stat-card card-uni">
        <h3>Registered Universities</h3>
        <div class="number"><?php echo $uniCount; ?></div>
    </div>
    
    <div class="stat-card card-student">
        <h3>Total Students</h3>
        <div class="number"><?php echo $studentCount; ?></div>
    </div>

    <div class="stat-card card-cert">
        <h3>Certificates Issued</h3>
        <div class="number"><?php echo $issuedCount; ?></div>
    </div>

    <div class="stat-card card-users">
        <h3>System Users</h3>
        <div class="number"><?php echo $userCount; ?></div>
    </div>
</div>

<div class="activity-section">
    <h2>Recently Issued Certificates</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Institution</th>
                <th>Date Issued</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through the recent certificates and display them-->
            <?php while($row = $recentCerts->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
                <td><?php echo $row['UniversityCode']; ?></td>
                <td><?php echo date('M d, Y', strtotime($row['ApplicationDate'])); ?></td>
                <td><span style="color: #27ae60; font-weight:bold;">● Verified</span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>