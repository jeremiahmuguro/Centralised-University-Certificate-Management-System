<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Regulator') {
    header("Location: index.html");
    exit();
}

// SQL JOIN to fetch only finalized certificates
$sql = "SELECT s.StudentName, u.UniversityName, s.CourseName, a.CertificateNumber
        FROM Application a
        JOIN Student s ON a.StudentID = s.StudentID
        JOIN University u ON s.UniversityID = u.UniversityID
        WHERE a.CertificateNumber NOT LIKE 'PENDING-%'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #27ae60; color: white; }
        .status-badge { background: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .serial-text { font-family: 'Courier New', monospace; font-weight: bold; color: #2c3e50; }
    </style>
</head>
<body>

    <h2>Issued Certificates History</h2>
    <p>Below is the registry of all certificates currently finalized in the system.</p>
    
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Institution</th>
                <th>Course</th>
                <th>Unique Serial Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['StudentName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['UniversityName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['CourseName']) . "</td>";
                    echo "<td><span class='serial-text'>" . htmlspecialchars($row['CertificateNumber']) . "</span></td>";
                    echo "<td><span class='status-badge'>Finalized</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No issued certificates found in the registry.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>