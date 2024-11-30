<?php
require 'core/dbConfig.php';
require 'core/models.php';

// Ensure the user is an HR (redirect if not)
if ($_SESSION['role'] !== 'HR') {
    header('Location: dashboard.php');
    exit();
}

// Fetch all job posts
$jobPosts = getJobPosts($pdo);

// Set message for success or failure based on query parameters
if (isset($_GET['success'])) {
    $message = "Job post created successfully!";
    $message_class = 'success'; // Set class to 'success' for green text
} elseif (isset($_GET['error'])) {
    $message = "Failed to create job post.";
    $message_class = 'error'; // Set class to 'error' for red text
} elseif (isset($_GET['update_success'])) {
    $message = "Job post updated successfully!";
    $message_class = 'success'; // Success message
} elseif (isset($_GET['update_error'])) {
    $message = "Failed to update job post.";
    $message_class = 'error'; // Error message
}

// If the update_id is set, fetch the job post for editing
if (isset($_GET['update_id'])) {
    $job_post_id = $_GET['update_id'];
    $jobPost = getJobPostById($job_post_id, $pdo); // Fetch job post details for editing
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Job Post</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>CREATE/EDIT JOB POST</h1>
    <div class="container">
        <!-- Job Post Creation Form or Edit Form -->
        <form method="POST" action="core/handleForms.php">
            <input type="hidden" name="action" value="create_job_post">
            
            <?php if (isset($jobPost)): ?>
                <!-- Edit form with pre-filled job post data -->
                <input type="hidden" name="job_post_id" value="<?php echo $jobPost['job_post_id']; ?>"> <!-- Job Post ID -->
                <input type="text" name="title" value="<?php echo htmlspecialchars($jobPost['title']); ?>" placeholder="Job Title" required><br>
                <textarea name="description" placeholder="Job Description" required><?php echo htmlspecialchars($jobPost['description']); ?></textarea><br>
                <button type="submit" name="action" value="update_job_post">Update</button>
            <?php else: ?>
                <!-- Default form to create a new job post -->
                <input type="text" name="title" placeholder="Job Title" required><br>
                <textarea name="description" placeholder="Job Description" required></textarea><br>
                <button type="submit" name="action" value="create_job_post">Create</button>
            <?php endif; ?>
        </form>

        <!-- Display success or error message -->
        <?php if (isset($message)) { echo "<p class='$message_class'>$message</p>"; } ?>
    </div>

    <!-- Back to dashboard link -->
    <a href="dashboard.php">Back to Dashboard</a><br>

    <h2>EXISTING JOB POSTS</h2>
    <?php if ($jobPosts): ?>
        <table border="1">
            <tr>
                <th>Job Title</th>
                <th>Job Description</th>
                <th>Date Posted</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($jobPosts as $job): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($job['description'])); ?></td>
                    <td><?php echo htmlspecialchars($job['date_posted']); ?></td>
                    <td><?php echo htmlspecialchars($job['created_by']); ?></td>
                    <td>
                        <!-- Edit button (instead of delete) -->
                        <a href="createPost.php?update_id=<?php echo $job['job_post_id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No job posts available.</p>
    <?php endif; ?>
</body>
</html>
