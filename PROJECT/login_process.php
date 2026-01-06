<?php
session_start();
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_index($conn, trim($_POST["email"]));
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo "Please enter email and password";
    } else {
 
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_name"] = $row["name"];
                echo "success";
            } else {
                echo "Incorrect password";
            }
        } else {
            echo "Email not found";
        }
    }
}

function mysqli_real_escape_index($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}
?>