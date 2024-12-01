<?php
require_once 'core/dbConfig.php'; // Include database configuration
require_once 'core/models.php';    // Include models for database operations

// Redirect to login if not logged in or not an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit();
}

$applicant_id = $_SESSION['user_id'];  // Get logged-in applicant's ID

// Fetch the application ID from the query string
if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    // Fetch application details from the database using the function in models.php
    $application = getApplicationDetails($application_id, $applicant_id, $pdo);  

    if (!$application) {
        // If the application doesn't exist, redirect with an error
        header('Location: viewApplications.php?error=application_not_found');
        exit();
    }
} else {
    // If no application ID is passed, redirect with an error
    header('Location: viewApplications.php?error=invalid_application');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Application Deletion</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="delete-page">
    <h1>Are you sure you want to delete this application?</h1>
    <div class="container">
        <h2>Job Title:</h2><p><?php echo htmlspecialchars($application['job_title']); ?></p>
        <h2>Job Description:</h2><p><?php echo htmlspecialchars($application['job_description']); ?></p>
        <h2>Resume: <a href="FONTANILLA-FINDHIRE/<?php echo htmlspecialchars($application['resume_path']); ?>" target="_blank">View Resume</a></h2>

        <!-- The form for deleting the application -->
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="delete_application">
            <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
            <input type="submit" name="deleteApplicationBtn" value="Delete" class="button delete">
            <a href="viewApplications.php" class="button">Back</a>
        </form>
    </div>
</body>
</html>
