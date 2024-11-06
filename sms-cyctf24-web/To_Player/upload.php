<?php

session_start();
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["logo"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
include 'db.php';
include 'header.php'; 
require('Captcha.php');
use Phppot\Captcha;



try {
    if (isset($_POST["submit"]) && isset($_POST['hash'])) {
        $captcha = new Captcha();
        if (!$captcha->validateCaptcha($_POST['captcha'])){
                echo "Captcha Failed";
                die();
        }

    if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] == UPLOAD_ERR_NO_FILE) {
        echo "No file uploaded.";
        $uploadOk = 0;
        exit;
        }

        if (preg_match('/^[0-9a-f]{40}$/', $_POST['hash']) === 0) {
            $uploadOk = 0;
            throw new Exception("Invalid Hash Format");
        }

        $new_name = $_POST['hash'] . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            throw new Exception("File is not an image.");
        }

        if (file_exists($target_dir . $new_name)) {
            $uploadOk = 0;
            throw new Exception("Sorry, file already exists.");
        }

        if ($_FILES["logo"]["size"] > 500000) {
            $uploadOk = 0;
            throw new Exception("Sorry, your file is too large.");
        }

        // last thing is to check the extension to avoid logical bugs XX(
        if ($imageFileType !== "jpg" && $imageFileType !== "png" && $imageFileType !== "jpeg") {
            $uploadOk = 0;
            throw new Exception("Sorry, only JPG, JPEG & PNG files are allowed.");
        }

        if ($uploadOk == 0) {
            throw new Exception("Sorry, your file was not uploaded.");
        } else {
            $dest_path = $target_dir . $new_name;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $dest_path)) {
                echo "File has been uploaded successfully.";

                $stmt = $pdo->prepare("INSERT INTO uploads (file_name, user_hash) VALUES (:file_name, :user_hash)");
                $stmt->bindParam(':file_name', basename($_FILES["logo"]["name"]));
                $stmt->bindParam(':user_hash', $_POST['hash']);

                if (!$stmt->execute()) {
                    throw new Exception("DB Error occurred");
                }
            } else {
                throw new Exception("File Saving Error occurred");
            }
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
