<?php
include 'db.php'; 


session_start();

if (!isset($_SESSION['reset_user'])) {
    echo "No reset request found. Please request a password reset first.";
    exit;
}

$username = $_SESSION['reset_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['code'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_code = $row['code'];
        $code_created_at = strtotime($row['code_created_at']);
        $current_time = time();

        if ($input_code === $stored_code) {
            // Check if the code was created within the last 2 minutes 
            if (($current_time - $code_created_at) <= 120) {
            if (isset($_POST['new_password'])) {
                $new_password = $_POST['new_password'];
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_stmt = $pdo->prepare("UPDATE users SET password = :new_password, code = NULL WHERE username = :username");
                $update_stmt->bindParam(':new_password', $hashed_password);
                $update_stmt->bindParam(':username', $username);

                if ($update_stmt->execute()) {
                    echo "Password has been reset successfully.";
                    unset($_SESSION['reset_user']); 
                    header('Location: login.php'); 
                    exit;
                } else {
                    echo "Failed to reset the password. Please try again later.";
                }
            } else {
                ?>
                <?php include 'header.php'; ?>
                <div class="container">
                    <h1 class="my-5 text-center">Enter Your New Password</h1>
                    <form action="reset.php" method="POST">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter your new password" required>
                        </div>
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($input_code); ?>">
                        <input type="submit" class="btn btn-primary" value="Reset Password">
                    </form>
                </div>
                <?php
            }            }else {
                echo 'expired reset code';
            }
        } else {
            echo "Invalid reset code.";
        }
    } else {
        echo "User not found.";
    }
} else {
    ?>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1 class="my-5 text-center">Reset Your Password</h1>
        <form action="reset.php" method="POST">
            <div class="mb-3">
                <label for="code" class="form-label">Reset Code</label>
                <input type="text" class="form-control" id="code" name="code" placeholder="Enter your reset code" required>
            </div>
            <input type="submit" class="btn btn-primary" value="Verify Code">
        </form>
    </div>
    <?php
}
?>
