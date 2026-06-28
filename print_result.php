<?php
// This file handles the verification of certificates by students and employers.

// It retrieves the certificate details based on the registration number and graduation year provided by the user.
// It then displays the certificate information in a formatted manner, allowing users to download or print the certificate as a PDF. If no matching record is found, it shows an error message.


// Include the database connection file to interact with the database
include 'db_connect.php';
// Check if the required POST parameters are set, if not redirect back to the input form
if(!isset($_POST['reg_num']) || !isset($_POST['grad_year'])) {
    // If the registration number or graduation year is not provided, redirect back to the input form
    header("Location: print.html");
    // Ensure no further code is executed after the redirect
    exit();
}

// Retrieve the registration number and graduation year from the POST parameters
$regNum = $_POST['reg_num'];
$gradYear = $_POST['grad_year'];

// Lookup student and application using reg_num + grad_year
// Prepare a SQL query to retrieve the certificate details based on the registration number and graduation year
$sql = "SELECT s.StudentName, s.CourseName, s.LevelOfProgram, u.UniversityName, 
               a.ApplicationDate, a.CertificateNumber, r.RealName as RegulatorName
        FROM Application a
        JOIN Student s ON a.StudentID = s.StudentID
        JOIN University u ON s.UniversityID = u.UniversityID
        LEFT JOIN Users r ON a.ApprovedBy = r.UserID
        WHERE s.RegistrationNumber = ? 
          AND YEAR(a.ApplicationDate) = ? 
        LIMIT 1";

// Create a prepared statement 
$stmt = $conn->prepare($sql);
// Bind the registration number and graduation year parameters to the prepared statement
$stmt->bind_param("si", $regNum, $gradYear);
// Execute the prepared statement and get the result
$stmt->execute();
// Fetch the result of the query and store it in an associative array
$result = $stmt->get_result();
// Fetch the first row of the result as an associative array, which contains the certificate details
$row = $result->fetch_assoc();
// Capture the certificate number for potential use in the HTML output
$certNo = isset($_GET['certNo']) ? trim($_GET['certNo']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CUCMS - Formal Verification</title>
    <style>
        body { background: #f4f6f9; font-family: "Times New Roman", Times, serif; padding: 20px; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;
            position: fixed; top: 20px; right: 20px; font-size: 14px; }
            
        .certificate-container {
            width: 850px;
            margin: 0 auto;
            background: white;
            padding: 70px;
            border: 10px double #2c3e50;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .cert-header { text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 40px; text-transform: uppercase; }
        
        .cert-body {
            font-size: 22px;
            line-height: 1.8;
            text-align: justify;
            margin-bottom: 80px;
        }

        .highlight { font-weight: bold; text-decoration: underline; }

        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
        }

        .regulator-side { border-top: 2px solid #000; padding-top: 5px; width: 280px; text-align: center; font-size: 18px; }
        .serial-side { font-family: 'Courier New', monospace; font-weight: bold; font-size: 18px; }

        .btn-print { display: block; width: 200px; margin: 30px auto; padding: 12px; background: #27ae60; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; font-family: sans-serif; }

        @media print {
            @page { size: auto; margin: 15mm; }
            body { background: white; padding: 0; }
            .certificate-container { box-shadow: none; border: 5px solid #000; width: 100%; margin: 0; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<?php if ($row): ?>
    <div class="certificate-container">
        <div class="cert-header">Certificate of Authenticity</div>
        
        <div class="cert-body">
            This is to hereby certify that the bearer of this certificate named 
            <span class="highlight"><?php echo htmlspecialchars($row['StudentName']); ?></span> 
            has achieved the merit of <span class="highlight"><?php echo htmlspecialchars($row['LevelOfProgram']); ?></span> of 
            <span class="highlight"><?php echo htmlspecialchars($row['CourseName']); ?></span> in the year 
            <span class="highlight"><?php echo date('Y', strtotime($row['ApplicationDate'])); ?></span> 
            from <span class="highlight"><?php echo htmlspecialchars($row['UniversityName']); ?></span> 
            and has been approved by <strong>The Centralised University Certificate Commission</strong>.
        </div>

        <div class="cert-footer">
            <div class="regulator-side">
                <?php echo !empty($row['RegulatorName']) ? htmlspecialchars($row['RegulatorName']) : "Authorized Official"; ?><br>
                <strong>Official Regulator</strong>
            </div>
            <div class="serial-side">
                #<?php echo htmlspecialchars($row['CertificateNumber']); ?>
            </div>
        </div>
    </div>
    
    <button class="btn-print" onclick="window.print()">Download / Print PDF</button>
<!-- If no matching record is found, display an error message-->
<?php else: ?>
    <div style="text-align:center; padding:100px; font-family: sans-serif;">
        <h1 style="color:#e74c3c;">✗ INVALID CERTIFICATE</h1>
        <p>No record found for serial number: <strong><?php echo htmlspecialchars($certNo); ?></strong></p>
        <a href="verify.html">Try Again</a>
    </div>
    <!-- If there was an error during the lookup, display an error message-->
<?php endif; ?>
<a href="logout.php" class="logout-btn">Logout</a>
</body>
</html>