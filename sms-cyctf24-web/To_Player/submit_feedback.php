<?php
include 'db.php'; 
include 'header.php'; 
require('Captcha.php');
use Phppot\Captcha;


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $captcha = new Captcha();
        if (!$captcha->validateCaptcha($_POST['captcha'])){
                echo "Captcha Failed";
                die();
        }

	if (isset($_POST['username']) && isset($_POST['feedback'])){
    $username = htmlspecialchars(trim($_POST['username']));
    $feedback = trim($_POST['feedback']);
	$date = isset($_SERVER['HTTP_DATE']) ? $_SERVER['HTTP_DATE'] : time();
        try {
    		$stmt = $pdo->prepare("INSERT INTO feedback (feedback, date, username) VALUES (:feedback, '$date', :username)");
            $stmt->bindParam(':username', htmlspecialchars($username));
            $stmt->bindParam(':feedback', htmlspecialchars($feedback));
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error Occured";
        }
    } else {
        echo "Both username and feedback are required.";
    }
}
?>
