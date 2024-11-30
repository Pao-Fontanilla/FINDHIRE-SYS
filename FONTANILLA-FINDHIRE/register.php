<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>
    <h1>REGISTRATION FORM</h1>
    <div class="container">
        <!-- Registration Form -->
        <form method="POST" action="core/handleForms.php">
            <label for="username">Username:</label>
            <input type="hidden" name="action" value="register">
            <input type="text" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br><br>
            <label for="role">Role:</label>
            <select name="role">
                <option value="HR">HR</option>
                <option value="applicant">Applicant</option>
            </select><br><br>
            <button type="submit">Register</button>
        </form>

        <!-- Display messages based on query parameters -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success">Registration successful!</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'duplicate'): ?>
            <p class="error">Account already exists. Please choose a different username.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'generic'): ?>
            <p class="error">An error occurred during registration. Please try again.</p>
        <?php endif; ?>
    </div>

    <p>Already have an account? You may login <a href="login.php">here</a>.</p>
</body>
</html>
