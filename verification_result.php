<?php
include 'db_connect.php';

// Check if cert_no was actually posted
if(!isset($_POST['cert_no'])) {
    header("Location: verify.html");
    exit();
}

$certNo = $_POST['cert_no'];

// Updated SQL to match print_result.php for accurate, up-to-date data
$sql = "SELECT s.StudentName, s.CourseName, s.LevelOfProgram, u.UniversityName, 
               a.ApplicationDate, a.CertificateNumber, r.RealName as RegulatorName
        FROM Application a
        JOIN Student s ON a.StudentID = s.StudentID
        JOIN University u ON s.UniversityID = u.UniversityID
        LEFT JOIN Users r ON a.ApprovedBy = r.UserID
        WHERE a.CertificateNumber = ? LIMIT 1";
        
//creating a prepared statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $certNo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    // Capture visitor info
    $visitorName = $_POST['visitor_name'];

    // Insert into Visitors table
    $insert = $conn->prepare("INSERT INTO Visitors (VisitorName, CertificateNumber) VALUES (?, ?)");
    $insert->bind_param("ss", $visitorName, $certNo);
    $insert->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CUCMS - Verification Result</title>
    <style>
    
             
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f4f6f9; }
        
        /* Verification Card Styling */
        .verification-card {
            width: 600px;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 8px solid <?php echo ($row) ? '#27ae60' : '#e74c3c'; ?>;
        }

        .cert-info { text-align: left; margin: 30px 0; line-height: 1.8; }
        .cert-info p { border-bottom: 1px solid #eee; padding-bottom: 5px; }
        
        .action-buttons { margin-top: 20px; }
        .btn-print { background: #34495e; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; margin-right: 10px; }
        .btn-back { color: #7f8c8d; text-decoration: none; font-size: 0.9rem; }

        /* PRINT STYLES - This is the magic part */
        @media print {
            body { background: white; }
            .verification-card { 
                box-shadow: none; 
                border: 2px solid #eee; 
                width: 100%; 
                margin: 0;
            }
            .action-buttons, .btn-back { display: none !important; } /* Hide buttons on the PDF */
            header, footer { display: none; }
        }
    
    </style>
</head>
<body>

<div class="verification-card">
    <?php if ($row): ?>
        <h1 style="color:#27ae60; margin-bottom: 5px;">✓ VERIFIED</h1>
        <p style="color: #7f8c8d; font-size: 0.9rem;">Official Record from Centralised University Database</p>
        <hr>

        <div class="cert-info">
            <p><strong>Certificate No:</strong> <span style="font-family: monospace;"><?php echo htmlspecialchars($row['CertificateNumber']); ?></span></p>
            <p><strong>Graduate Name:</strong> <?php echo htmlspecialchars($row['StudentName']); ?></p>
            <p><strong>Institution:</strong> <?php echo htmlspecialchars($row['UniversityName']); ?></p>
            <p><strong>Awarded:</strong> <?php echo htmlspecialchars($row['CourseName']); ?></p>
            <p><strong>Level of Program:</strong> <?php echo htmlspecialchars($row['LevelOfProgram']); ?></p>
            <p><strong>Graduation Year:</strong> <?php echo date('Y', strtotime($row['ApplicationDate'])); ?></p>
        </div>

        <div class="action-buttons">
            <button onclick="window.print()" class="btn-print">Download / Print PDF</button>
            <br><br>
            <a href="verify.html" class="btn-back">← Verify Another Certificate</a>
        </div>
        

    <?php else: ?>
        <h1 style="color:#e74c3c;">✗ INVALID</h1>
        <p>No record found for serial number: <strong><?php echo htmlspecialchars($certNo); ?></strong></p>
        <div class="action-buttons">
            <a href="verify.html" class="btn-back">Try Again</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
