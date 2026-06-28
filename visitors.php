<?php
// Initialize the session to access global session variables for authentication
session_start();

// Include the database configuration and connection logic
include 'db_connect.php';

// Access Control: Verify if the user is logged in and specifically holds the 'Admin' role
// If unauthorized, redirect to the landing page and stop script execution
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html");
    exit();
}

// Retrieve optional filtering parameters for Year and Month from the URL (GET request)
$yearFilter = isset($_GET['year']) ? $_GET['year'] : null;
$monthFilter = isset($_GET['month']) ? $_GET['month'] : null;

// Define the base SQL query to fetch visitor activity
// This joins the Visitors table with Application, Student, and University tables to provide context for each visit
$sql = "SELECT v.VisitorName, s.StudentName, u.UniversityName, s.CourseName, s.LevelOfProgram, v.VisitDate
        FROM Visitors v
        JOIN Application a ON v.CertificateNumber = a.CertificateNumber
        JOIN Student s ON a.StudentID = s.StudentID
        JOIN University u ON s.UniversityID = u.UniversityID";

// Dynamic Query Building: Initialize arrays to hold WHERE clauses and their corresponding parameters
$conditions = [];
$params = [];
$types = "";

// Add Year filter condition if selected
if ($yearFilter) {
    $conditions[] = "YEAR(v.VisitDate) = ?";
    $params[] = $yearFilter;
    $types .= "i"; // Integer type for bind_param
}

// Add Month filter condition if selected
if ($monthFilter) {
    $conditions[] = "MONTH(v.VisitDate) = ?";
    $params[] = $monthFilter;
    $types .= "i"; // Integer type for bind_param
}

// Append conditions to the main SQL string if any filters were applied
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Sort the results by the most recent visits first
$sql .= " ORDER BY v.VisitDate DESC";

// Prepare the statement and dynamically bind parameters using the argument unpacking operator (...)
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the query and capture the result set
$stmt->execute();
$result = $stmt->get_result();

// Total visits
$totalVisits = $conn->query("SELECT COUNT(*) as total FROM Visitors")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Reports | CUCMS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 30px; background: #f4f6f9; color: #333; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #2c3e50; }
        .stat-card h3 { margin: 0; font-size: 0.9rem; color: #7f8c8d; text-transform: uppercase; }
        .stat-card .number { font-size: 2.2rem; font-weight: bold; margin: 10px 0; color: #2c3e50; }
        .filter-section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }
        th { color: #95a5a6; font-weight: 500; }
    </style>
</head>
<body>

<h1>Visitor Reports</h1>

<!-- Summary Statistics Section -->
<div class="card-grid">
    <div class="stat-card">
        <h3>Total Visits</h3>
        <!-- Display the total count of visitors recorded in the system -->
        <div class="number"><?php echo $totalVisits; ?></div>
    </div>
</div>

<div class="filter-section">
    <form method="GET" action="visitors.php">
        <label for="year">Year:</label>
        <select name="year" id="year">
            <option value="">All</option>
            <!-- Dynamically generate year options from the current year down to 2020 -->
            <?php for($y = date("Y"); $y >= 2020; $y--): ?>
                <option value="<?php echo $y; ?>" <?php if($yearFilter == $y) echo "selected"; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>

        <label for="month">Month:</label>
        <select name="month" id="month">
            <option value="">All</option>
            <!-- Generate month options 1-12 with human-readable names -->
            <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php if($monthFilter == $m) echo "selected"; ?>>
                    <?php echo date("F", mktime(0,0,0,$m,1)); ?>
                </option>
            <?php endfor; ?>
        </select>

        <button type="submit">Filter</button>
    </form>
</div>

<!-- Main Data Table -->
<table>
    <thead>
        <tr>
            <th>Visitor Name</th>
            <th>Student Name</th>
            <th>University</th>
            <th>Program</th>
            <th>Level</th>
            <th>Date of Visit</th>
        </tr>
    </thead>
    <tbody>
        <!-- Loop through each record in the result set and output table rows -->
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['VisitorName']); ?></td>
            <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
            <td><?php echo htmlspecialchars($row['UniversityName']); ?></td>
            <td><?php echo htmlspecialchars($row['CourseName']); ?></td>
            <td><?php echo htmlspecialchars($row['LevelOfProgram']); ?></td>
            <!-- Format the timestamp into a readable date and time format -->
            <td><?php echo date('M d, Y H:i', strtotime($row['VisitDate'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
