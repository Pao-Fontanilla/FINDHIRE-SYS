<?php   
// File: manageApplications.php

require 'core/dbConfig.php';  // Include database configuration
require 'core/models.php';    // Include reusable database operations

// Ensure the user is an HR, redirect if not logged in or not HR
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header('Location: login.php');
    exit();
}

// Get the logged-in HR's user ID
$hr_id = $_SESSION['user_id'];

// Fetch applications made for job posts created by the logged-in HR
$applications = getApplicationsByHR($hr_id, $pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Job Applications</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>MANAGE JOB APPLICATIONS</h1>
    <!-- Link back to the dashboard -->
    <a href="dashboard.php">Back to Dashboard</a><br>

    <?php if ($applications): ?>
        <!-- Display a table with the list of applications -->
        <table border="1">
            <tr>
                <th>Applicant Name</th>
                <th>Job Title</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Application Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <!-- Display application details for each row -->
                    <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                    <td><a href="FONTANILLA-FINDHIRE/<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank">View Resume</a></td>
                    <td><?php echo ucfirst($app['status']); ?></td>
                    <td><?php echo htmlspecialchars($app['application_date']); ?></td>
                    <td>
                        <?php if ($app['status'] === 'pending'): ?>
                            <!-- Convert the buttons to links -->
                            <a href="core/handleForms.php?action=update_application_status&status=accepted&application_id=<?php echo $app['application_id']; ?>" class="button">Accept</a>
                            <a href="core/handleForms.php?action=update_application_status&status=rejected&application_id=<?php echo $app['application_id']; ?>" class="button">Reject</a>
                        <?php else: ?>
                            <!-- Display the current status if it's no longer pending -->
                            <?php echo ucfirst($app['status']); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <!-- Message displayed if no applications are found -->
        <p>No applications found.</p>
    <?php endif; ?>
</body>
</html>
