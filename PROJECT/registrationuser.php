<?php
include "db.php";

$msg = "";
$success = false;

$name = $email = $division = $district = $dob = $phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $name     = mysqli_real_escape_string($conn, $_POST["name"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);
    $division = mysqli_real_escape_string($conn, $_POST["division"]);
    $district = mysqli_real_escape_string($conn, $_POST["district"]);
    $dob      = mysqli_real_escape_string($conn, $_POST["dob"]);
    $phone    = mysqli_real_escape_string($conn, $_POST["phone"]);
    $password = $_POST["password"] ?? "";
    $cpassword = $_POST["cpassword"] ?? "";


    if (str_word_count($name) < 2) {
        $msg = "Name must contain first name and last name";
    }
    else if (strpos($email, "@") === false || strpos($email, ".com") === false) {
        $msg = "Email must contain @ and .com";
    }
    else if (!ctype_digit($phone) || strlen($phone) < 11) {
        $msg = "Phone number must be numeric and at least 11 digits";
    }
    else if (!preg_match("/[@$!%*#?&]/", $password)) {
        $msg = "Password must contain at least one special character";
    }
    else if ($password !== $cpassword) {
        $msg = "Password and Confirm Password do not match";
    }
    else {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, division, district, dob, phone, password)
                VALUES ('$name', '$email', '$division', '$district', '$dob', '$phone', '$hashPassword')";

        if (mysqli_query($conn, $sql)) {
            $msg = "Registration successful! You can now login.";
            $success = true;

            $name = $email = $division = $district = $dob = $phone = "";
        } else {
            $msg = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Join Crime Detect | Secure Registration</title>
    <style>
        :root {
            --primary: #e74c3c;
            --overlay: rgba(0, 0, 0, 0.6);
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: var(--overlay) url("bg_rg.png") no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            width: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        h2 { 
            text-align: center; 
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid var(--primary);
            display: inline-block;
            width: 100%;
            padding-bottom: 10px;
        }

        .input-group { margin-bottom: 15px; }

        label { display: block; margin-bottom: 5px; color: #666; font-size: 13px; font-weight: bold; }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 5px rgba(231, 76, 60, 0.3);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            margin-top: 10px;
        }

        button:hover { background: #c0392b; transform: translateY(-2px); }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }

        .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; }
        .success { color: #2e7d32; background: #e8f5e9; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Registration</h2>

    <?php if($msg): ?>
        <p class="<?php echo $success ? 'success' : 'error'; ?>"><?php echo $msg; ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="input-group">
            <input type="text" name="name" placeholder="Full Name (First & Last)" required value="<?php echo $name; ?>">
        </div>

        <div class="input-group">
            <input type="text" name="email" placeholder="Email Address" required value="<?php echo $email; ?>">
        </div>

        <div style="display: flex; gap: 10px;" class="input-group">
            <select name="division" id="division" onchange="loadDistricts()" required>
                <option value="">Division</option>
                <option <?php if($division=="Dhaka") echo "selected"; ?>>Dhaka</option>
                <option <?php if($division=="Chattogram") echo "selected"; ?>>Chattogram</option>
                <option <?php if($division=="Rajshahi") echo "selected"; ?>>Rajshahi</option>
                <option <?php if($division=="Khulna") echo "selected"; ?>>Khulna</option>
                <option <?php if($division=="Barishal") echo "selected"; ?>>Barishal</option>
                <option <?php if($division=="Sylhet") echo "selected"; ?>>Sylhet</option>
                <option <?php if($division=="Rangpur") echo "selected"; ?>>Rangpur</option>
                <option <?php if($division=="Mymensingh") echo "selected"; ?>>Mymensingh</option>
            </select>

            <select name="district" id="district" required>
                <option value="">District</option>
            </select>
        </div>

        <div class="input-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" required value="<?php echo $dob; ?>">
        </div>

        <div class="input-group">
            <input type="text" name="phone" placeholder="Phone (e.g. 017...)" required value="<?php echo $phone; ?>">
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password (incl. @$!%...)" required autocomplete="new-password">
        </div>

        <div class="input-group">
            <input type="password" name="cpassword" placeholder="Confirm Password" required>
        </div>

        <button type="submit">Create Account</button>
        <a href="loginuser.php" class="login-link">Already have an account? Login</a>
    </form>
</div>

<script>

function loadDistricts() {
    var division = document.getElementById("division").value;
    var districtBox = document.getElementById("district");
    districtBox.innerHTML = "<option value=''>District</option>";

    var list = [];
    if (division == "Dhaka") list = ["Dhaka","Gazipur","Narayanganj","Tangail","Faridpur"];
    else if (division == "Chattogram") list = ["Chattogram","Cox's Bazar","Comilla","Noakhali"];
    else if (division == "Rajshahi") list = ["Rajshahi","Bogura","Pabna"];
    else if (division == "Khulna") list = ["Khulna","Jashore","Satkhira"];
    else if (division == "Barishal") list = ["Barishal","Bhola"];
    else if (division == "Sylhet") list = ["Sylhet","Habiganj"];
    else if (division == "Rangpur") list = ["Rangpur","Dinajpur"];
    else if (division == "Mymensingh") list = ["Mymensingh","Sherpur"];

    for (var i = 0; i < list.length; i++) {
        var opt = document.createElement("option");
        opt.value = opt.text = list[i];
        districtBox.add(opt);
    }
}
</script>

</body>
</html>