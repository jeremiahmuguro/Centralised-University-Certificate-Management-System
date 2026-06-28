<?php
// This page provides interactive analytics and reports for administrators to explore system data with various filters.

// It checks if the user is logged in and has the admin role before allowing access. It retrieves options for universities, courses, regulators, and other filters from the database to populate dropdowns. Based on the selected filters, it dynamically builds and executes SQL queries to fetch relevant statistics such as student population and regulator performance. The results are displayed in a user-friendly format with additional details available for deeper insights. Only administrators can access this page to ensure that sensitive data is protected.

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

// Initialize variables for filters
// These variables will hold the selected filter values from the POST request. If a filter is not selected, it defaults to 'All' to indicate that no filtering should be applied for that category.
$selectedUni = $_POST['university'] ?? 'All';
$selectedCourse = $_POST['course'] ?? 'All';
$selectedYear = $_POST['year'] ?? 'All';
$selectedReg = $_POST['regulator'] ?? 'All';
$selectedLevel = $_POST['level'] ?? 'All';
$selectedMonth = $_POST['month'] ?? 'All';


// 1. Fetch Options for Dropdowns
// Execute queries to get the list of universities, courses, and regulators to populate the filter dropdowns on the page
$unis = $conn->query("SELECT * FROM University");
// Get distinct course names from the Course table to populate the course filter dropdown
$courses = $conn->query("SELECT DISTINCT CourseName FROM Course");
// Get the list of regulators (users with the Regulator role) to populate the regulator filter dropdown
$regulators = $conn->query("SELECT UserID, RealName FROM Users WHERE UserRole = 'Regulator'");

// 2. Build the Dynamic Query for Student Stats (Scenario 2)
// Start building the SQL query to count students based on the selected filters. The query joins the Student, University, and Application tables to allow filtering by university, course, graduation year, level of program, and application month.
$query = "SELECT COUNT(*) as count 
          FROM Student s 
          JOIN University u ON s.UniversityID = u.UniversityID 
          JOIN Application a ON s.StudentID = a.StudentID 
          WHERE 1=1";

// Append conditions based on selected filters
// If a specific university is selected, add a condition to filter by that university
// If a specific course is selected, add a condition to filter by that course
// If a specific graduation year is selected, add a condition to filter by that year
// If a specific level of program is selected, add a condition to filter by that level
// If a specific application month is selected, add a condition to filter by that month
if ($selectedUni !== 'All') $query .= " AND u.UniversityID = '$selectedUni'";
if ($selectedCourse !== 'All') $query .= " AND s.CourseName = '$selectedCourse'";
if ($selectedYear !== 'All') $query .= " AND YEAR(s.DateOfGraduation) = '$selectedYear'";
if ($selectedLevel !== 'All') $query .= " AND s.LevelOfProgram = '$selectedLevel'";
if ($selectedMonth !== 'All') $query .= " AND MONTH(a.ApplicationDate) = '$selectedMonth'";

// Execute the query and get the count
// The result will contain the count of students that match the selected filters for university, course, graduation year, level of program, and application month
$filteredCount = $conn->query($query)->fetch_assoc()['count'];

// 3. Build the Query for Regulator Performance (Scenario 3)
// Start building the SQL query to count the number of certificates processed/verified by the selected regulator. If 'All' is selected, it will count for all regulators.
$regQuery = "SELECT COUNT(*) as total FROM Application WHERE 1=1";
// If a specific regulator is selected, add a condition to filter by that regulator
// This will count the number of certificates processed/verified by the selected regulator. If 'All' is selected, it will return the total count for all regulators.
if ($selectedReg !== 'All') $regQuery .= " AND ApprovedBy = '$selectedReg'";
// Execute the query and get the count of certificates processed/verified by the selected regulator
// The result will contain the count of certificates processed/verified by the selected regulator, or the total count for all regulators if 'All' is selected
$regStat = $conn->query($regQuery)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CUCMS Analytics</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 25px; background: #f4f6f9; }
        .filter-section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 25px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        select, button { padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
        button { background: #2c3e50; color: white; cursor: pointer; border: none; padding: 10px 25px; }
        
        .results-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stat-box { background: #fff; padding: 20px; border-radius: 8px; border-top: 4px solid #3498db; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-box h3 { margin: 0; color: #7f8c8d; font-size: 0.9rem; }
        .stat-box .val { font-size: 2rem; font-weight: bold; margin-top: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

    <h2>Interactive Reports & Analytics</h2>

    <form method="POST" class="filter-section">
        <div class="filter-group">
            <label>University</label>
            <select name="university">
                <option value="All">All Universities</option>
                <?php while($u = $unis->fetch_assoc()): ?>
                    <option value="<?php echo $u['UniversityID']; ?>" <?php echo ($selectedUni == $u['UniversityID']) ? 'selected' : ''; ?>>
                        <?php echo $u['UniversityName']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Course</label>
            <select name="course">
                <option value="All">All Courses</option>
                <?php while($c = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $c['CourseName']; ?>" <?php echo ($selectedCourse == $c['CourseName']) ? 'selected' : ''; ?>>
                        <?php echo $c['CourseName']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Graduation Year</label>
            <select name="year">
                <option value="All">All Time</option>
                <?php for($i=2020; $i<=2030; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($selectedYear == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="filter-group">
    <label>Level of Program</label>
    <select name="level">
        <option value="All">All Levels</option>
        <option value="PhD" <?php echo ($selectedLevel == 'PhD') ? 'selected' : ''; ?>>PhD</option>
        <option value="Master" <?php echo ($selectedLevel == 'Master') ? 'selected' : ''; ?>>Master</option>
        <option value="Bachelor" <?php echo ($selectedLevel == 'Bachelor') ? 'selected' : ''; ?>>Bachelor</option>
        <option value="Diploma" <?php echo ($selectedLevel == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
        <option value="Certificate" <?php echo ($selectedLevel == 'Certificate') ? 'selected' : ''; ?>>Certificate</option>
    </select>
</div>

<div class="filter-group">
    <label>Application Month</label>
    <select name="month">
        <option value="All">All Months</option>
        <?php 
        for ($m=1; $m<=12; $m++) {
            $monthName = date("F", mktime(0,0,0,$m,1));
            echo "<option value='$m' ".(($selectedMonth == $m) ? 'selected' : '').">$monthName</option>";
        }
        ?>
    </select>
</div>


        <button type="submit">Generate Report</button>
    </form>

    <div class="results-grid">
        <div class="stat-box">
            <h3>Student Population (Filtered)</h3>
            <div class="val"><?php echo $filteredCount; ?></div>
            <p style="font-size: 0.8rem; color: #95a5a6;">Matches selected University, Course, and Year.</p>
        </div>

        <div class="stat-box" style="border-top-color: #27ae60;">
            <h3>Regulator Performance</h3>
            <form method="POST" style="display:inline;">
                <select name="regulator" onchange="this.form.submit()" style="padding: 5px; margin-top: 10px;">
                    <option value="All">All Regulators</option>
                    <?php 
                    $regulators->data_seek(0);
                    while($r = $regulators->fetch_assoc()): ?>
                        <option value="<?php echo $r['UserID']; ?>" <?php echo ($selectedReg == $r['UserID']) ? 'selected' : ''; ?>>
                            <?php echo $r['RealName']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
            <div class="val"><?php echo $regStat; ?></div>
            <p style="font-size: 0.8rem; color: #95a5a6;">Certificates processed/verified by this officer.</p>
        </div>
        <?php if ($selectedReg !== 'All'): ?>
    <div style="margin-top: 15px; font-size: 0.85rem; text-align: left;">
        <strong>Recent Actions:</strong>
        <ul style="padding-left: 20px; color: #34495e;">
            <?php
            $regDetails = $conn->query("SELECT s.StudentName, a.CertificateNumber 
                                        FROM Application a 
                                        JOIN Student s ON a.StudentID = s.StudentID 
                                        WHERE a.ApprovedBy = '$selectedReg' 
                                        ORDER BY a.ApplicationDate DESC LIMIT 5");
            while($d = $regDetails->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($d['StudentName']) . " (" . $d['CertificateNumber'] . ")</li>";
            }
            if ($regDetails->num_rows == 0) echo "<li>No certificates processed yet.</li>";
            ?>
        </ul>
    </div>
<?php endif; ?>
    </div>

    <?php if ($selectedUni !== 'All'): ?>
        <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px;">
            <h3>Course Catalog for Selected University</h3>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Course Code</th>
                        <th>Level</th>
                        <th>Duration (Yrs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $catQuery = $conn->query("SELECT * FROM Course WHERE UniversityID = '$selectedUni'");
                    while($row = $catQuery->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['CourseName']; ?></td>
                            <td><strong><?php echo $row['CourseCode']; ?></strong></td>
                            <td><?php echo $row['LevelOfProgram']; ?></td>
                            <td><?php echo $row['Duration']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php
$detailsQuery = $conn->query("SELECT s.StudentName, s.RegistrationNumber, s.LevelOfProgram, 
                                     c.CourseName, u.UniversityName, a.ApplicationDate 
                              FROM Student s
                              JOIN University u ON s.UniversityID = u.UniversityID
                              JOIN Course c ON s.CourseName = c.CourseName
                              JOIN Application a ON s.StudentID = a.StudentID
                              WHERE 1=1
                              ".($selectedUni !== 'All' ? " AND u.UniversityID = '$selectedUni'" : "")."
                              ".($selectedCourse !== 'All' ? " AND s.CourseName = '$selectedCourse'" : "")."
                              ".($selectedYear !== 'All' ? " AND YEAR(s.DateOfGraduation) = '$selectedYear'" : "")."
                              ".($selectedLevel !== 'All' ? " AND s.LevelOfProgram = '$selectedLevel'" : "")."
                              ".($selectedMonth !== 'All' ? " AND MONTH(a.ApplicationDate) = '$selectedMonth'" : "")."
                              ORDER BY a.ApplicationDate DESC");
?>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Reg. Number</th>
            <th>Level</th>
            <th>Course</th>
            <th>University</th>
            <th>Application Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $detailsQuery->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['StudentName']; ?></td>
                <td><?php echo $row['RegistrationNumber']; ?></td>
                <td><?php echo $row['LevelOfProgram']; ?></td>
                <td><?php echo $row['CourseName']; ?></td>
                <td><?php echo $row['UniversityName']; ?></td>
                <td><?php echo $row['ApplicationDate']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


</body>
</html>