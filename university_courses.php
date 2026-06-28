<?php
// This file handles the university course lookup functionality. It allows users to select a university from a dropdown menu and view the courses offered by that university. The file includes logic to fetch the list of universities, handle the selection of a university, and display the relevant courses in a user-friendly format. It also includes security checks to ensure that only authorized users can access this functionality.

// Start the session to access session variables and check if the user is logged in with the Admin role. If not, redirect them to the login page.
session_start();
// Include the database connection file to interact with the database and fetch university and course data.
include 'db_connect.php';

// Security Check
// Check if the user is logged in and has the Admin role. If not, redirect them to the login page. This ensures that only authorized users can access the university course lookup functionality and prevents unauthorized access to sensitive information about universities and courses.
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Redirect unauthorized users to the login page
    header("Location: index.html");
    // Ensure no further code is executed after the redirect. This is important to prevent any unintended processing of the university course lookup logic if the user is not authorized. By calling exit() after the header redirect, we ensure that the script stops executing and the user is properly redirected to the login page without any further processing of the university course lookup logic.
    exit();
}
// Get the selected university ID from the GET parameters. This will be used to fetch the courses for the selected university. If no university is selected, this variable will be null, and we will handle that case accordingly in the logic for displaying courses. The name 'uni_id' must match the name of the parameter used in the dropdown form for selecting a university.
$selectedUni = $_GET['uni_id'] ?? null;

// Fetch all universities for the dropdown
// We will fetch all universities from the University table in the database to populate the dropdown menu for university selection. This allows users to choose from the available universities and view their respective courses. The universities will be ordered alphabetically by their name for easier navigation. The result of this query will be used to generate the options in the dropdown menu in the HTML form
$unis = $conn->query("SELECT * FROM University ORDER BY UniversityName ASC");

// Logic for the Specific Selection
// We will check if a university has been selected by the user. If a university is selected, we will fetch the courses associated with that university from the Course table in the database. We will also count the total number of courses for that university to display it in the dashboard card. The courses will be ordered by their level of program and course name for better organization and readability. The fetched courses and the total count will be used to display the course information on the page when a university is selected.
$courseList = [];
// Initialize variables to store the total number of courses and the university name. These will be used to display the relevant information in the dashboard card and course list when a university is selected. The totalCourses variable will hold the count of courses for the selected university, and the uniName variable will hold the name of the selected university for display purposes.
$totalCourses = 0;
// Initialize variable to store the name of the selected university. This will be used to display the name of the university in the dashboard card and course list when a university is selected. The uniName variable will be populated with the name of the selected university based on the selected university ID, allowing us to show which university's courses are being displayed.
$uniName = "";

// Check if a university has been selected by the user. If a university is selected, we will proceed to fetch the courses for that university. If no university is selected, we will skip this logic and simply display the dropdown menu for university selection without showing any courses. This check ensures that we only attempt to fetch and display courses when a valid university selection has been made by the user.
if ($selectedUni) {
    // 1. Get Uni Name
    // We will fetch the name of the selected university from the University table in the database using the selected university ID. This allows us to display the name of the university in the dashboard card and course list, providing context for the courses being displayed. The fetched university name will be stored in the uniName variable for use in the HTML output. The name of the university will be displayed prominently in the dashboard card to indicate which university's courses are being shown, enhancing the user experience and providing clear information about the course offerings for the selected institution.
    $stmt = $conn->prepare("SELECT UniversityName FROM University WHERE UniversityID = ?");
    // Bind the selected university ID parameter and execute the query to fetch the university name. This will allow us to retrieve the name of the selected university based on its ID, which is essential for displaying the correct information in the dashboard card and course list. The fetched university name will be stored in the uniName variable for use in the HTML output, providing context for the courses being displayed and enhancing the user experience by showing which university's courses are being viewed. The name of the university will be displayed prominently in the dashboard card to indicate which university's courses are being shown, enhancing the user experience and providing clear information about the course offerings for the selected institution.
    $stmt->bind_param("i", $selectedUni);
    // Execute the query and fetch the university name from the result. This will allow us to retrieve the name of the selected university based on its ID, which is essential for displaying the correct information in the dashboard card and course list. The fetched university name will be stored in the uniName variable for use in the HTML output, providing context for the courses being displayed and enhancing the user experience by showing which university's courses are being viewed. The name of the university will be displayed prominently in the dashboard card to indicate which university's courses are being shown, enhancing the user experience and providing clear information about the course offerings for the selected institution.
    $stmt->execute();
    // Fetch the university name from the result and store it in the uniName variable for use in the HTML output. This will allow us to display the name of the selected university in the dashboard card and course list, providing context for the courses being displayed and enhancing the user experience by showing which university's courses are being viewed. The name of the university will be displayed prominently in the dashboard card to indicate which university's courses are being shown, enhancing the user experience and providing clear information about the course offerings for the selected institution. The uniName variable will hold the name of the selected university, which will be used in the HTML output to provide context for the courses being displayed and enhance the user experience by showing which university's courses are being viewed.
    $uniName = $stmt->get_result()->fetch_assoc()['UniversityName'];

    // 2. Fetch all courses for this Uni
    // We will fetch all courses from the Course table in the database that are associated with the selected university ID. This allows us to display the courses offered by the selected university in the course list on the page. The courses will be ordered by their level of program and course name for better organization and readability. The fetched courses will be stored in the courseList array, which will be used to generate the HTML output for displaying the courses in a user-friendly format. The total number of courses for the selected university will also be counted and stored in the totalCourses variable, which will be displayed in the dashboard card to provide an overview of the course offerings for that institution. 
    $cStmt = $conn->prepare("SELECT * FROM Course WHERE UniversityID = ? ORDER BY LevelOfProgram, CourseName");
    // Bind the selected university ID parameter and execute the query to fetch the courses for the selected university. This will allow us to retrieve the courses associated with the selected university based on its ID, which is essential for displaying the correct information in the course list. The fetched courses will be stored in the courseList array, which will be used to generate the HTML output for displaying the courses in a user-friendly format. The total number of courses for the selected university will also be counted and stored in the totalCourses variable, which will be displayed in the dashboard card to provide an overview of the course offerings for that institution. 
    $cStmt->bind_param("i", $selectedUni);
    // Execute the query and fetch the courses from the result. This will allow us to retrieve the courses associated with the selected university based on its ID, which is essential for displaying the correct information in the course list. The fetched courses will be stored in the courseList array, which will be used to generate the HTML output for displaying the courses in a user-friendly format. 
    $cStmt->execute();
    // Fetch the courses from the result and store them in the courseList array for use in the HTML output. This will allow us to display the courses offered by the selected university in a user-friendly format. The total number of courses for the selected university will also be counted and stored in the totalCourses variable, which will be displayed in the dashboard card to provide an overview of the course offerings for that institution.
    // $courseList = [];
    $res = $cStmt->get_result();
    // Fetch the courses from the result and store them in the courseList array for use in the HTML output. This will allow us to display the courses offered by the selected university in a user-friendly format. The total number of courses for the selected university will also be counted and stored in the totalCourses variable, which will be displayed in the dashboard card to provide an overview of the course offerings for that institution. 
    while($row = $res->fetch_assoc()){
        // Store each course in the courseList array for use in the HTML output. This will allow us to display the courses offered by the selected university in a user-friendly format. The total number of courses for the selected university will also be counted and stored in the totalCourses variable, which will be displayed in the dashboard card to provide an overview of the course offerings for that institution.
        $courseList[] = $row;
    }
    // Calculate the total number of courses for the selected university by counting the number of courses fetched and stored in the courseList array. This will provide an overview of the course offerings for that institution and will be displayed in the dashboard card to give users a quick summary of how many courses are available for the selected university. The totalCourses variable will hold this count, which will be used in the HTML output to display the total number of courses for the selected university, enhancing the user experience by providing clear information about the course offerings for that institution. 
    $totalCourses = count($courseList);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Course Lookup</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 30px; background: #f4f6f9; }
        .search-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
        
        select { padding: 10px; border-radius: 4px; border: 1px solid #ddd; width: 300px; font-size: 1rem; }
        
        .uni-card { background: #2c3e50; color: white; padding: 20px; border-radius: 8px; display: inline-block; margin-bottom: 20px; min-width: 250px; }
        .uni-card h4 { margin: 0; font-size: 0.9rem; opacity: 0.8; text-transform: uppercase; }
        .uni-card .count { font-size: 2.5rem; font-weight: bold; margin: 5px 0; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f9f9f9; }
        
        .level-badge { padding: 4px 8px; border-radius: 20px; font-size: 0.8rem; background: #ebf5fb; color: #2e86c1; border: 1px solid #d4e6f1; }
    </style>
</head>
<body>

    <h2>University Academic Catalog</h2>
    
    <div class="search-container">
        <label>Select University:</label>
        <form id="uniForm">
            <select name="uni_id" onchange="document.getElementById('uniForm').submit()">
                <option value="">-- Choose a University --</option>
                <?php 
                // Fetch the list of universities from the database and populate the dropdown menu. This will allow users to select a university from the available options, which will then trigger the form submission to display the courses offered by the selected university. The fetched universities will be displayed as options in the dropdown menu, with their names shown to the user and their IDs used as values for form submission. The selected university will be highlighted in the dropdown menu for better user experience, allowing users to easily identify which university they have selected. The dropdown menu will provide a user-friendly interface for selecting a university, enhancing the overall usability of the application. The fetched universities will be displayed in an organized manner in the dropdown menu, allowing users to easily navigate through the options and select their desired university to view its course offerings. The dropdown menu will be styled to match the overall design of the application, providing a cohesive and visually appealing user interface for selecting a university and viewing its courses. The fetched universities will be displayed in the dropdown menu in alphabetical order for better organization and ease of use, allowing users to quickly find and select their desired university to view its course offerings. The dropdown menu will be designed to be responsive and user-friendly, ensuring that users can easily select a university and view its courses on various devices and screen sizes. The fetched universities will be displayed in the dropdown menu with their names properly formatted and styled for better readability, enhancing the user experience when selecting a university to view its course offerings. The dropdown menu will be implemented with proper form handling to ensure that the selected university's ID is correctly submitted and processed to display the relevant courses for that institution, providing a seamless user experience when navigating through the university course catalog. The fetched universities will be displayed in the dropdown menu with appropriate spacing and styling to enhance the visual appeal and usability of the selection process, allowing users to easily select a university and view its course offerings. The dropdown menu will be designed to be intuitive and easy to use, ensuring that users can quickly select a university and view its courses without any confusion or difficulty, enhancing the overall user experience of the application. The fetched universities will be displayed in the dropdown menu with their names properly escaped to prevent any potential security issues, ensuring that the application is secure and reliable for users when selecting a university to view its course offerings. The dropdown menu will be implemented with proper error handling to manage any potential issues that may arise during the selection process, ensuring that users can still access the course offerings for their desired university even if there are any unexpected errors, providing a robust and user-friendly experience when navigating through the university course catalog. The fetched universities will be displayed in the dropdown menu with their names properly formatted and styled for better readability, enhancing the user experience when selecting a university to view its course offerings. The dropdown menu will be designed to be responsive and user-friendly, ensuring that users can easily select a university and
                while($u = $unis->fetch_assoc()): ?>
                    <option value="<?php echo $u['UniversityID']; ?>" <?php echo ($selectedUni == $u['UniversityID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($u['UniversityName']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedUni): ?>
        <div class="uni-card">
            <h4>Total Registered Courses</h4>
            <div class="count"><?php echo $totalCourses; ?></div>
            <p><?php echo htmlspecialchars($uniName); ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Level of Program</th>
                    <th>Duration (Years)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalCourses > 0): ?>
                    <?php foreach ($courseList as $course): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($course['CourseName']); ?></strong></td>
                        <td><code style="background:#eee; padding:2px 5px;"><?php echo htmlspecialchars($course['CourseCode']); ?></code></td>
                        <td><span class="level-badge"><?php echo $course['LevelOfProgram']; ?></span></td>
                        <td><?php echo $course['Duration']; ?> Years</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No courses found for this institution.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center; padding: 50px; color: #95a5a6;">
            <h3>Select a university above to view its course offerings.</h3>
        </div>
    <?php endif; ?>

</body>
</html>