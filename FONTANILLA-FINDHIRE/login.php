<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <h1>FINDHIRE JOB APPLICATION SYSTEM</h1>

    <div class="container">
         <!-- Login Form -->
        <form method="POST" action="core/handleForms.php">
            <input type="hidden" name="action" value="login">
            <label for="username">LOGIN WITH USERNAME</label>
            <input type="text" name="username" required><br><br>
            <label for="username">PASSWORD</label>
            <input type="password" name="password" required><br><br>
            <button type="submit">Login</button>
        </form>
        
        <!-- Display messages based on the URL query string -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success">Registration successful! Please login below.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <p class="error">Invalid username or password. Please try again.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 2): ?>
            <p class="error">An error occurred during login. Please try again later.</p>
        <?php endif; ?>
    </div>

    <p>Don't have an account? You may register <a href="register.php">here</a>.</p>
</body>
</html>

