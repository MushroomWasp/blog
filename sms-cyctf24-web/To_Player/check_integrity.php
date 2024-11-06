<?php
include 'db.php'; 

session_start();

if (!$_SESSION['admin']) {
    header('Location: login.php');
    die();
}

chdir('uploads'); 

    if (isset($_POST['file_name']) && isset($_POST['user_hash'])) {
        $file_name = $_POST['file_name'];
        $user_hash = $_POST['user_hash'];
        $new_name = $_POST['user_hash'] . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
            $calculated_hash = sha1_file($file_name);
            if ($calculated_hash === $user_hash) {
                echo "File passed integrity checks. You can open it safely.";
            } else {
                // Remove the entry from the uploads table
                $stmt = $pdo->prepare("DELETE FROM uploads WHERE file_name = ?");
                $stmt->execute([$file_name]);

                echo " The file has been removed as it failed integrity checks.";
            }
      
    } else {
        echo "File name and user hash must be provided.";
    }

?>
