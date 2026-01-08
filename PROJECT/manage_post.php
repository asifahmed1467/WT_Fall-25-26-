<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;

if ($user_id == 0) {
    echo json_encode(["status" => "error", "message" => "Session expired"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') 
    {
    
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
    $new_content = mysqli_real_escape_string($conn, $_POST['content']);

    $check_query = "SELECT id FROM posts WHERE id = '$post_id' AND user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE posts SET content = '$new_content' WHERE id = '$post_id'";
        
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(["status" => "success", "message" => "Post updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed"]);
        }
    } 
    exit();
}
?>