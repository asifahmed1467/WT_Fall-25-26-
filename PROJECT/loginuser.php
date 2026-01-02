<?php
session_start();          // ✅ REQUIRED
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $msg = "Please enter email and password";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format";
    }
    else {

        /* ✅ SECURE QUERY */
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            if (password_verify($password, $row["password"])) {

                /* ✅ SET SESSION */
                $_SESSION["user_id"]   = $row["id"];
                $_SESSION["user_name"] = $row["name"];

                /* ✅ REDIRECT */
                header("Location: homepage.php");
                exit();

            } else {
                $msg = "Incorrect email or password";
            }

        } else {
            $msg = "Incorrect email or password";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
body { 
    font-family: Arial; 
    background:#f2f2f2; 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    height:100vh; 
}
.form-box { 
    background:#fff; 
    padding:20px; 
    width:320px; 
    border-radius:8px; 
    box-shadow:0 0 10px gray; 
}
h2 { text-align:center; }
input { width:100%; padding:8px; margin:6px 0; }
button { width:100%; padding:10px; background:green; color:white; border:none; }
.msg { text-align:center; color:red; }
</style>
</head>
<body>

<div class="form-box">
<h2>Login</h2>

<p class="msg"><?php echo $msg; ?></p>

<form method="post" autocomplete="off">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

</div>

</body>
</html>
