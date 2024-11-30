<?php 
require 'core/dbConfig.php';
require 'core/models.php';

// Ensure the user is an applicant
if ($_SESSION['role'] !== 'applicant') {
    header('Location: dashboard.php');
    exit();
}

// Fetch all job posts
$jobPosts = getJobPosts($pdo);

// Handle success or error flags from query string
$applicationSuccess = isset($_GET['success']) && $_GET['success'] == 1;
$applicationError = isset($_GET['error']) && $_GET['error'] == 1;
$fileTypeError = isset($_GET['error']) && $_GET['error'] == 4;  // Check for invalid file type error
$job_post_id = isset($_GET['job_post_id']) ? $_GET['job_post_id'] : null;  // Get the job_post_id from the URL
?>
<!DOCTYPE html>
<html>
<head>
    <title>Job Posts</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>JOB POSTS</h1>
    <a href="dashboard.php">Back to Dashboard</a><br>
    <!-- Display job posts -->
    <?php foreach ($jobPosts as $job): ?>
        <div class="container">
            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
            <p><?php echo htmlspecialchars($job['description']); ?></p>

            <!-- Job application form -->
            <form method="POST" action="core/handleForms.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="apply">  <!-- Set the action to 'apply' -->
                <input type="hidden" name="job_post_id" value="<?php echo $job['job_post_id']; ?>"><br>
                <label for="resume">Upload Resume:</label>
                <input type="file" name="resume" required>
                <button type="submit">Apply</button>
            </form>

            <!-- Display success or error messages for the specific job post -->
            <?php if ($applicationSuccess && $job['job_post_id'] == $job_post_id): ?>
                <p class="success">Application submitted successfully!</p>
            <?php elseif ($applicationError && $job['job_post_id'] == $job_post_id): ?>
                <p class="error">Failed to apply. Please try again.</p>
            <?php elseif ($fileTypeError && $job['job_post_id'] == $job_post_id): ?>
                <p class="error">Only PDF files are allowed. Please try again.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
