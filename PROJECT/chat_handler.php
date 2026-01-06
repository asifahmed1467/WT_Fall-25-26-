<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $msg_text = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    if (!empty($msg_text) && $user_id != 0) {

        $sql = "INSERT INTO messages (user_id, message) VALUES ('$user_id', '$msg_text')";
        mysqli_query($conn, $sql);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT messages.*, users.name FROM messages 
            JOIN users ON messages.user_id = users.id 
            ORDER BY messages.id ASC LIMIT 50";
    
    $result = mysqli_query($conn, $sql);
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($data); 
    exit;
}
?>