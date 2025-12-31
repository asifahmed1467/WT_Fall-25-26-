<?php
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $msg = "Please enter email and password";
    }
    else if (strpos($email, "@") === false || strpos($email, ".com") === false) {
        $msg = "Email must contain @ and .com";
    }
    else {
        $sql = "SELECT password FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {
                $msg = "Login successful!";
            } else {
                $msg = "Incorrect email or password";
            }
        } else {
            $msg = "Incorrect email or password";
        }
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
    <input type="email" name="email" placeholder="Email" required autocomplete="off" value="">
    <input type="password" name="password" placeholder="Password" required value="" autocomplete="new-password">
    <button type="submit">Login</button>
</form>

</div>

</body>
</html>
