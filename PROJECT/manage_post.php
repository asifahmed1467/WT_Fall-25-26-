<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
if ($user_id == 0) exit();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);

    $check = mysqli_query($conn, "SELECT id FROM posts WHERE id='$post_id' AND user_id='$user_id'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    if ($action == 'delete') {
        $query = "DELETE FROM posts WHERE id='$post_id'";
        if (mysqli_query($conn, $query)) 
        {
            echo json_encode(["status" => "success"]);
        }
    } 
    elseif ($action == 'update') {
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $query = "UPDATE posts SET content='$content' WHERE id='$post_id'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success"]);
        }
    }
    exit();
}
?>