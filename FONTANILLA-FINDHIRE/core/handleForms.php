<?php
require_once 'dbConfig.php';  // Include the correct database connection
require_once 'models.php';     // Include reusable functions from models.php

// Handle user registration
if (isset($_POST['action']) && $_POST['action'] == 'register') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        // Attempt to register the user
        $result = registerUser($username, $password, $role, $pdo);

        if ($result) {
            // Redirect to login page on success
            header("Location: ../register.php?success=1");
            exit();
        } else {
            // Redirect back with a generic error
            header("Location: ../register.php?error=generic");
            exit();
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Check for duplicate entry error
            header("Location: ../register.php?error=duplicate");
            exit();
        } else {
            // Log the error and redirect with a generic error
            error_log("Error during registration: " . $e->getMessage());
            header("Location: ../register.php?error=generic");
            exit();
        }
    }
}

// Handle user login
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Attempt to log the user in
        $user = loginUser($username, $password, $pdo);
        
        if ($user) {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../dashboard.php");  // Redirect to dashboard
            exit();
        } else {
            // Redirect with error flag if login failed
            header("Location: ../login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Log error and redirect with a different error flag
        error_log("Error during login: " . $e->getMessage());
        header("Location: ../login.php?error=2");
        exit();
    }
}

// Handle user logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_start();
    session_unset();  // Remove all session variables
    session_destroy();  // Destroy the session
    header('Location: ../login.php');  // Redirect to login page
    exit();
}

// Handle job post creation
if (isset($_POST['action']) && $_POST['action'] == 'create_job_post') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];  // Get the current HR user

    try {
        // Attempt to create the job post
        $result = createJobPost($title, $description, $created_by, $pdo);
        
        if ($result) {
            header("Location: ../createPost.php?success=1");  // Redirect after success
        } else {
            header("Location: ../createPost.php?error=1");  // Redirect with error flag
        }
        exit();
    } catch (PDOException $e) {
        // Log error and redirect with an error flag
        error_log("Error during job post creation: " . $e->getMessage());
        header("Location: ../createPost.php?error=2");
        exit();
    }
}

/// Handle job application submission (Applicant role)
if (isset($_POST['action']) && $_POST['action'] == 'apply' && $_SESSION['role'] == 'applicant') {
    $job_post_id = $_POST['job_post_id'];

    // Get the uploaded file's MIME type and extension
    $file_type = mime_content_type($_FILES['resume']['tmp_name']);
    $file_extension = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));

    // Ensure the file is a PDF
    if ($file_type === 'application/pdf' && $file_extension === 'pdf') {
        // Path relative to the 'core' folder
        $resume_path = '../uploads/' . $_FILES['resume']['name'];

        // Move the uploaded resume file to the 'uploads' directory
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            $applicant_id = $_SESSION['user_id'];  // Get the current applicant's ID

            try {
                // Attempt to apply for the job
                $result = applyToJob($applicant_id, $job_post_id, $resume_path, $pdo);

                if ($result) {
                    // Redirect with success message and job_post_id
                    header("Location: ../viewJobs.php?success=1&job_post_id=$job_post_id");
                    exit();
                } else {
                    // Redirect with error flag and job_post_id
                    header("Location: ../viewJobs.php?error=1&job_post_id=$job_post_id");
                    exit();
                }
            } catch (PDOException $e) {
                // Log error and redirect with an error flag
                error_log("Error applying for job: " . $e->getMessage());
                header("Location: ../viewJobs.php?error=2&job_post_id=$job_post_id");
                exit();
            }
        } else {
            // Handle file upload failure
            header("Location: ../viewJobs.php?error=3&job_post_id=$job_post_id");
            exit();
        }
    } else {
        // If the file is not a PDF, redirect with an error
        header("Location: ../viewJobs.php?error=4&job_post_id=$job_post_id");
        exit();
    }
}


// Handle message sending
if (isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $sender_id = $_SESSION['user_id'];  // Get the logged-in user's ID
    $receiver_id = $_POST['receiver_id'];  // Get the receiver's ID from the form
    $message_text = $_POST['message_text'];  // Get the message content

    try {
        // Attempt to send the message
        if (sendMessage($sender_id, $receiver_id, $message_text, $pdo)) {
            header("Location: ../sendMessage.php?success=1");  // Redirect after success
            exit();
        } else {
            header("Location: ../sendMessage.php?error=1");  // Redirect with error flag
            exit();
        }
    } catch (PDOException $e) {
        // Log error and redirect with an error flag
        error_log("Error sending message: " . $e->getMessage());
        header("Location: ../sendMessage.php?error=2");
        exit();
    }
}

// Handle updating application status (HR role)
if (isset($_GET['action']) && $_GET['action'] == 'update_application_status' && $_SESSION['role'] == 'HR') {
    $application_id = $_GET['application_id'];
    $status = $_GET['status'];  // 'accepted' or 'rejected'

    try {
        // Update the status of the application
        $result = updateApplicationStatus($application_id, $status, $pdo);
        
        // Redirect back to manageApplications.php after the status is updated
        header("Location: ../manageApplications.php");
        exit();
    } catch (PDOException $e) {
        // Optionally log the error or handle it
        error_log("Error updating application status: " . $e->getMessage());
    }
}

// Handle job post update (edit job post)
if (isset($_POST['action']) && $_POST['action'] == 'update_job_post') {
    $job_post_id = $_POST['job_post_id'];  // The job post ID to update
    $title = $_POST['title'];              // The updated title
    $description = $_POST['description'];  // The updated description

    try {
        // Call the update function
        $result = updateJobPost($job_post_id, $title, $description, $pdo);

        if ($result) {
            header("Location: ../createPost.php?update_success=1");  // Redirect on success
            exit();
        } else {
            header("Location: ../createPost.php?update_error=1");  // Redirect with error flag
            exit();
        }
    } catch (PDOException $e) {
        // Handle any errors
        error_log("Error updating job post: " . $e->getMessage());
        header("Location: ../createPost.php?update_error=1");
        exit();
    }
}

?>
