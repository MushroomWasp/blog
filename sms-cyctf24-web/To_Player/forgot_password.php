<?php
include 'db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $reset_code = bin2hex(random_bytes(32)); 
        $current_time = date('Y-m-d H:i:s');

        $update_stmt = $pdo->prepare("UPDATE users SET code = :reset_code, code_created_at = :code_created_at WHERE username = :username");
        $update_stmt->bindParam(':reset_code', $reset_code);
        $update_stmt->bindParam(':code_created_at', $current_time);
        $update_stmt->bindParam(':username', $username);


        if ($update_stmt->execute()) {
            $_SESSION['reset_user'] = $username;
            
                header('Location: reset.php');
        } else {
            echo "Failed to generate reset code. Please try again later.";
        }
    } else {
        echo "The username you entered does not exist.";
    }
}
?>

<?php include 'header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="my-5 text-center">Forgot Your Password?</h1>
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <p class="text-center">Enter your username to reset your password.</p>
                        <form action="forgot_password.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Send Reset Link">
                        </form>
                        <div class="mt-3 text-center">
                            <a href="login.php" class="text-light">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
