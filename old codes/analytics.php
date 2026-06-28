<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html");
    exit();
}

// Initialize variables for filters
$selectedUni = $_POST['university'] ?? 'All';
$selectedCourse = $_POST['course'] ?? 'All';
$selectedYear = $_POST['year'] ?? 'All';
$selectedReg = $_POST['regulator'] ?? 'All';

// 1. Fetch Options for Dropdowns
$unis = $conn->query("SELECT * FROM University");
$courses = $conn->query("SELECT DISTINCT CourseName FROM Course");
$regulators = $conn->query("SELECT UserID, RealName FROM Users WHERE UserRole = 'Regulator'");

// 2. Build the Dynamic Query for Student Stats (Scenario 2)
$query = "SELECT COUNT(*) as count FROM Student s 
          JOIN University u ON s.UniversityID = u.UniversityID 
          WHERE 1=1";

if ($selectedUni !== 'All') $query .= " AND u.UniversityID = '$selectedUni'";
if ($selectedCourse !== 'All') $query .= " AND s.CourseName = '$selectedCourse'";
if ($selectedYear !== 'All') $query .= " AND YEAR(s.DateOfGraduation) = '$selectedYear'";

$filteredCount = $conn->query($query)->fetch_assoc()['count'];

// 3. Build the Query for Regulator Performance (Scenario 3)
$regQuery = "SELECT COUNT(*) as total FROM Application WHERE 1=1";
if ($selectedReg !== 'All') $regQuery .= " AND ApprovedBy = '$selectedReg'";
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

</body>
</html>