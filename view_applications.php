<?php
include 'db_connect.php';

// SQL JOIN to bring together Student, Application, and Transcript details
$sql = "SELECT 
            s.StudentName, 
            s.RegistrationNumber, 
            a.ApplicationDate, 
            a.ApplicationID,
            t.FilePath 
        FROM Application a
        JOIN Student s ON a.StudentID = s.StudentID
        LEFT JOIN Transcript t ON s.StudentID = t.StudentID
        WHERE a.SerialNumber IS NULL OR a.SerialNumber = ''"; // Only show pending ones

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
        th { background: #2c3e50; color: white; }
        .btn-view { background: #3498db; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.8rem; }
        .btn-gen { background: #27ae60; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Pending Certificate Applications</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Reg Number</th>
                <th>Graduation Date</th>
                <th>Transcript</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['StudentName'] . "</td>";
                    echo "<td>" . $row['RegistrationNumber'] . "</td>";
                    echo "<td>" . $row['ApplicationDate'] . "</td>";
                    
                    // Link to open the PDF in a new tab
                    if ($row['FilePath']) {
                        echo "<td><a href='" . $row['FilePath'] . "' target='_blank' class='btn-view'>View PDF</a></td>";
                    } else {
                        echo "<td><span style='color:red;'>No File</span></td>";
                    }

                    // This button will lead to our future Serial Generation task
                    echo "<td><button class='btn-gen' onclick='generateCert(" . $row['ApplicationID'] . ")'>Process Cert</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No pending applications found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function generateCert(id) {
            if(confirm("Start generating serial number for Application ID: " + id + "?")) {
                // We will build this logic in the Advanced Tasks phase
                alert("Proceeding to Serial Generation Logic...");
            }
        }
    </script>

</body>
</html>