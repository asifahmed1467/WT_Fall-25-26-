<?php
include "db.php";
$success = $error = "";
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
 
    $username=$_POST["username"];
    $password=$_POST["password"];
    $email=$_POST["email"];
 
    if (empty($username)|| empty($password)|| empty($email))
    {
$error="Invalid do again";
    }
 
    else
    {
        $hassPassword= password_hash($password,PASSWORD_DEFAULT);
 
        $sql= "INSERT INTO users(username,password,email) VALUES ('$username','$hassPassword','$email') ";
 
        if($conn->query($sql))
        {
            $success="Registration complete";
        }
 
        else{
 
            $error="ERROR ". $conn->error;
 
        }
    }
}
 
 
?>
 
 
 
 
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="">
        Username: <input type="text" name="username"><br><br>
        Password: <input type="password" name="password"><br><br>
        Email: <input type="email" name="email"><br><br>
        <input type="submit" value="Register">
    </form>
 
    <p style="color:green;"><?php echo $success; ?></p>
    <p style="color:red;"><?php echo $error; ?></p>
</body>
</html>
 
 
 
 
.................................................>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
<?php
$host="localhost";
$user="root";
$pass="";
$dbname="WT_R";
 
$conn = new mysqli($host,$user,$pass,$dbname);
 
if($conn->connect_error)
{
    die("Connect lost". $conn->connect_error);        
}
 
 
 
?>