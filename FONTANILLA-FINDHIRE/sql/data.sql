CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,           -- Unique user identifier
    username VARCHAR(50) NOT NULL UNIQUE,             -- Unique username for login
    password VARCHAR(255) NOT NULL,                   -- Hashed password for authentication
    role ENUM('applicant', 'HR') NOT NULL,            -- User role (Applicant or HR)
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Timestamp of account creation
);

CREATE TABLE job_posts (
    job_post_id INT AUTO_INCREMENT PRIMARY KEY,         -- Unique job post identifier
    title VARCHAR(100) NOT NULL,                        -- Job post title
    description TEXT NOT NULL,                          -- Detailed job description
    created_by INT NOT NULL,                            -- Reference to the HR who created the post
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    -- Timestamp of when the post was created
    FOREIGN KEY (created_by) REFERENCES users(user_id)  -- Links to users table (HR)
);

CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,                      -- Unique application identifier
    applicant_id INT NOT NULL,                                          -- Reference to the applicant (user)
    job_post_id INT NOT NULL,                                           -- Reference to the applied job post
    resume_path VARCHAR(255) NOT NULL,                                  -- Path to the applicant's resume
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',   -- Current application status
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,                -- Timestamp of application submission
    FOREIGN KEY (applicant_id) REFERENCES users(user_id),               -- Links to users table (applicant)
    FOREIGN KEY (job_post_id) REFERENCES job_posts(job_post_id)         -- Links to job posts table
);

CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,          -- Unique message identifier
    sender_id INT NOT NULL,                             -- Sender's user ID
    receiver_id INT NOT NULL,                           -- Receiver's user ID
    message_text TEXT NOT NULL,                         -- Content of the message
    sent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,      -- Timestamp of when the message was sent
    FOREIGN KEY (sender_id) REFERENCES users(user_id),  -- Links to sender in users table
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) -- Links to receiver in users table
);
