<?php 
require 'core/dbConfig.php';
require 'core/models.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$sender_id = $_SESSION['user_id'];

// Fetch current user's username and message-related data
$current_username = getCurrentUsername($sender_id, $pdo);
$users = getAllUsersExcept($sender_id, $pdo);
$messages = getMessages($sender_id, $pdo);

// Handle message status
$messageSent = isset($_GET['success']) && $_GET['success'] == 1;
$messageError = isset($_GET['error']) && $_GET['error'] == 1;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>SEND MESSAGE</h1>
    <div class="container">
        <!-- Message form -->
        <form method="POST" action="core/handleForms.php">
            <input type="hidden" name="action" value="send_message">
            <label for="receiver_id">Select Receiver:</label>
            <select name="receiver_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="message_text" placeholder="Type your message here" required></textarea><br>
            <button type="submit">Send Message</button>
        </form>
        
        <!-- Display success or error messages -->
        <?php if ($messageSent): ?>
            <p class="success">Message sent!</p>
        <?php elseif ($messageError): ?>
            <p class="error">Failed to send message. Please try again.</p>
        <?php endif; ?>
    </div>
    <a href="dashboard.php">Back to Dashboard</a><br>
    <h2>YOUR MESSAGES</h2>

    <!-- Display sent messages -->
    <?php if ($messages): ?>
        <table border="1">
            <tr>
                <th>Sender</th>
                <th>Message</th>
                <th>Date Sent</th>
            </tr>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($msg['sender']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></td>
                    <td><?php echo htmlspecialchars($msg['sent_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>

  
</body>
</html>


