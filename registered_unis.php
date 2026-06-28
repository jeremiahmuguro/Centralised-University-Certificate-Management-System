<?php
// This file displays a list of all registered universities in the system. It is accessible only to Admin users and retrieves university data from the database to present it in a structured format.

// Start the session to access session variables
session_start();
// Include the database connection file to interact with the database
include 'db_connect.php';

// Security Check: Only Admins should see this
// Check if the user is logged in and has the Admin role. If not, redirect them to the login page.
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect
    exit();
}

// Fetch all registered universities
// Build the SQL query to select university information from the University table, ordered by UniversityID in ascending order
$sql = "SELECT UniversityID, UniversityName, UniversityCode FROM University ORDER BY UniversityID ASC";
// Execute the query and get the result. The result will contain all registered universities in the system, including their ID, name, and code.
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Universities | CUCMS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f4f6f9; color: #333; }
        
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        h2 { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; color: #2c3e50; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        
        th { background-color: #2c3e50; color: white; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        
        tr:hover { background-color: #f9f9f9; }
        
        .id-badge { background: #34495e; color: white; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.9rem; }
        
        .code-text { font-weight: bold; color: #27ae60; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registered Institutions</h2>
    <p>Below is a list of all universities currently integrated into the Centralised System.</p>

    <table>
        <thead>
            <tr>
                <th>University ID</th>
                <th>Institution Name</th>
                <th>University Code</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are any universities in the result set. If there are, loop through each university and display its information in a table row. If there are no universities, display a message indicating that no universities were found.
            if ($result->num_rows > 0) {
                // Loop through each university in the result set and display its information in a table row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><span class='id-badge'>" . htmlspecialchars($row['UniversityID']) . "</span></td>";
                    echo "<td>" . htmlspecialchars($row['UniversityName']) . "</td>";
                    echo "<td><span class='code-text'>" . htmlspecialchars($row['UniversityCode']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                // If there are no universities in the database, display a message indicating that no universities were found
                echo "<tr><td colspan='3' style='text-align:center;'>No universities found in the database.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>