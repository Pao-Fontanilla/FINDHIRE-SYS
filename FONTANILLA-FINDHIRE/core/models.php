<?php
// models.php: Contains reusable database operations

// Function to register a new user
function registerUser($username, $password, $role, $pdo) {
    $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':role' => $role]);
    return $stmt->rowCount();  // Return the number of affected rows (1 if successful)
}

// Function to log in a user by checking username and password
function loginUser($username, $password, $pdo) {
    $sql = "SELECT * FROM users WHERE username = :username";  // Get user by username
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch user record

    // If the user exists and the password matches, return the user data
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;  // Return null if no match is found
}

// Function to create a new job post
function createJobPost($title, $description, $created_by, $pdo) {
    $sql = "INSERT INTO job_posts (title, description, created_by) VALUES (:title, :description, :created_by)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':title' => $title, ':description' => $description, ':created_by' => $created_by]);
    return $stmt->rowCount();  // Return the number of affected rows
}

// Function to get all job posts
function getJobPosts($pdo) {
    $query = "
        SELECT jp.*, u.username AS created_by 
        FROM job_posts jp
        JOIN users u ON jp.created_by = u.user_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get job post details by its ID for editing
function getJobPostById($job_post_id, $pdo) {
    $query = "SELECT * FROM job_posts WHERE job_post_id = :job_post_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':job_post_id' => $job_post_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);  // Return the job post data
}

// Function to update a job post
function updateJobPost($job_post_id, $title, $description, $pdo) {
    $query = "UPDATE job_posts SET title = :title, description = :description WHERE job_post_id = :job_post_id";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':job_post_id' => $job_post_id
    ]);
}

// Function to apply to a job by saving the application data
function applyToJob($applicant_id, $job_post_id, $resume_path, $pdo) {
    $query = "INSERT INTO applications (applicant_id, job_post_id, resume_path, application_date) VALUES (:applicant_id, :job_post_id, :resume_path, NOW())";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([ ':applicant_id' => $applicant_id, ':job_post_id' => $job_post_id, ':resume_path' => $resume_path ]);
}

// Function to send a message from sender to receiver
function sendMessage($sender_id, $receiver_id, $message_text, $pdo) {
    if ($sender_id == $receiver_id) {
        return false;  // Prevent sending a message to oneself
    }

    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ ':sender_id' => $sender_id, ':receiver_id' => $receiver_id, ':message_text' => $message_text ]);
    
    return $stmt->rowCount() > 0;  // Return true if message was inserted
}

// Function to get all messages for a user
function getMessages($user_id, $pdo) {
    $sql = "SELECT m.message_id, m.message_text, m.sent_date, u.username AS sender 
            FROM messages m 
            JOIN users u ON m.sender_id = u.user_id 
            WHERE m.sender_id = :user_id OR m.receiver_id = :user_id 
            ORDER BY m.sent_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all messages for the user
}

// Function to update the status of a job application
function updateApplicationStatus($application_id, $status, $pdo) {
    $query = "UPDATE applications SET status = :status WHERE application_id = :application_id";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([ ':status' => $status, ':application_id' => $application_id ]);
}

// Function to get all applications for a specific HR (based on HR ID)
function getApplicationsByHR($hr_id, $pdo) {
    $query = "SELECT a.application_id, a.resume_path, a.status, a.application_date, 
                     u.username AS applicant_name, jp.title AS job_title 
              FROM applications a 
              JOIN job_posts jp ON a.job_post_id = jp.job_post_id 
              JOIN users u ON a.applicant_id = u.user_id 
              WHERE jp.created_by = :hr_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':hr_id' => $hr_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all applications for the HR
}

// Function to get all applications made by a specific applicant (based on applicant ID)
function getApplicationsByApplicant($applicant_id, $pdo) {
    $query = "SELECT a.resume_path, a.status, a.application_date, jp.title AS job_title 
              FROM applications a 
              JOIN job_posts jp ON a.job_post_id = jp.job_post_id 
              WHERE a.applicant_id = :applicant_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':applicant_id' => $applicant_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all applications for the applicant
}

// Function to delete a job post by its ID
function deleteJobPost($job_post_id, $pdo) {
    $query = "DELETE FROM job_posts WHERE job_post_id = :job_post_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':job_post_id' => $job_post_id]);
    return $stmt->rowCount() > 0;  // Return true if the job post was successfully deleted
}

// Function to get the username of a specific user by their user ID
function getCurrentUsername($user_id, $pdo) {
    $query = "SELECT username FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['username'] : 'Guest';  // Return the username, or 'Guest' if not found
}

// Function to get all users except the logged-in user (useful for selecting a message recipient)
function getAllUsersExcept($user_id, $pdo) {
    $query = "SELECT user_id, username FROM users WHERE user_id != :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all users except the logged-in user
}

?>