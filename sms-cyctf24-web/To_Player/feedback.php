<?php
include 'db.php'; 

session_start();

if (!$_SESSION['admin'])
{
    header('Location: login.php');
    die();
}

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM feedback");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($feedbacks);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
