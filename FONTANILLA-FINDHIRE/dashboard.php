<?php 
require 'core/dbConfig.php';
require 'core/models.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];  // To identify the current user

// Fetch the current user's username using the model function
$current_username = getCurrentUsername($user_id, $pdo);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>FINDHIRE DASHBOARD</h1>
        
    <div class="dashboard-container">
        <!-- Display current user's name -->
        <p>Welcome, <b><?php echo htmlspecialchars($current_username); ?></b>!</p>
        <!-- Display HR-specific buttons -->
        <?php if ($role === 'HR'): ?>
            <div class="button-row">
                <a href="createPost.php" class="button">Create Job Post</a>
                <a href="manageApplications.php" class="button">Manage Applications</a>
                <a href="sendMessage.php?receiver_id=<?php echo $user_id; ?>" class="button">Send Message</a>
            </div>
        <?php elseif ($role === 'applicant'): ?>
            <div class="button-row">
                <a href="viewJobs.php" class="button">View Job Posts</a>
                <a href="viewApplications.php" class="button">View My Applications</a>
                <a href="sendMessage.php?receiver_id=<?php echo $user_id; ?>" class="button">Send Message</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Logout button centered below -->
    <div class="logout-row">
        <a href="core/handleForms.php?action=logout" class="button logout">Logout</a>
    </div>
</body>
</html>
