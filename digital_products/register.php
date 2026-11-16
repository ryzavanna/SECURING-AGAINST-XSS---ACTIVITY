<?php 
session_start();
if (isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Digital Products System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="container" style="max-width: 500px;">
        <h1>User Registration</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="msg-error">
                <?php 
                    if ($_GET['error'] === 'usernametaken') echo "Username is already taken. Try another.";
                    elseif ($_GET['error'] === 'emptyinput') echo "Please fill in all fields.";
                    elseif ($_GET['error'] === 'weakpassword') echo "Password must be at least 8 characters and contain uppercase, lowercase, and numeric characters.";
                    else echo "Registration failed. Please try again.";
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="core/handleForms.php" method="POST">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Username</label>
                    <input type="text" name="userName" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastName" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        minlength="8" 
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                        title="Must contain at least 8 characters, including at least one number, one uppercase letter, and one lowercase letter." 
                        required
                    >
                    <small style="color: var(--text-muted); font-size: 0.78rem; margin-top: 5px;">
                        Minimum 8 characters with at least 1 uppercase, 1 lowercase, and 1 number.
                    </small>
                </div>

                <input type="submit" name="registerUserBtn" value="Register Account" class="btn-submit">
            </form>
            <p style="margin-top: 15px; text-align: center; font-size: 0.9rem;">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

</body>
</html>