<?php
require 'core/dbConfig.php';
require 'core/models.php';

// Redirect to login if not logged in or not an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit();
}

$applicant_id = $_SESSION['user_id'];

// Fetch applications made by the logged-in applicant
$applications = getApplicationsByApplicant($applicant_id, $pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View My Applications</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>MY APPLICATIONS</h1>
    <a href="dashboard.php">Back to Dashboard</a><br>
    <?php if ($applications): ?>
        <table border="1">
            <tr>
                <th>Job Title</th>
                <th>Resume</th>
                <th>Status</th>
            </tr>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                    <td><a href="FONTANILLA-FINDHIRE/<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank">View Resume</a></td>
                    <td><?php echo ucfirst($app['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have not applied for any jobs yet.</p>
    <?php endif; ?>
</body>
</html>
