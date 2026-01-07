<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
if ($user_id == 0) { header("Location: loginuser.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax_update'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $division = mysqli_real_escape_string($conn, $_POST['division']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);

    $update_sql = "UPDATE users SET name='$name', phone='$phone', division='$division', district='$district' WHERE id='$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION["user_name"] = $name; 
        echo json_encode(["status" => "success", "message" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }
    exit();
}


$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 220px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .main { margin-left: 220px; padding: 40px; width: 100%; display: flex; justify-content: center; }
        .edit-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        
        .nav-btn { display: block; background: #34495e; color: white; text-decoration: none; padding: 12px; margin-top: 10px; border-radius: 6px; font-size: 14px; text-align: center; }
        
        .input-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; color: #666; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        
        .btn-save { width: 100%; padding: 12px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        #response-msg { padding: 10px; margin-top: 15px; border-radius: 5px; text-align: center; display: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <a href="homepage.php" class="nav-btn">Dashboard</a>
    <a href="profile.php" class="nav-btn">My History</a>
    <a href="logout.php" class="nav-btn" style="background:#c0392b; margin-top:40px;">Logout</a>
</div>

<div class="main">
    <div class="edit-card">
        <h3>Edit Personal Information</h3>
        <p style="font-size: 13px; color: #888;">Update your account details below.</p>
        <hr>

        <form id="editForm">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="input-group">
                <label>Email Address (Cannot change)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f9f9f9;">
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="input-group">
                <label>Division</label>
                <select id="division" onchange="loadDistricts()">
                    <option value="">Select Division</option>
                    <option <?php if($user['division'] == "Dhaka") echo "selected"; ?>>Dhaka</option>
                    <option <?php if($user['division'] == "Chattogram") echo "selected"; ?>>Chattogram</option>
                    <option <?php if($user['division'] == "Rajshahi") echo "selected"; ?>>Rajshahi</option>
                    <option <?php if($user['division'] == "Khulna") echo "selected"; ?>>Khulna</option>
                    <option <?php if($user['division'] == "Barishal") echo "selected"; ?>>Barishal</option>
                    <option <?php if($user['division'] == "Sylhet") echo "selected"; ?>>Sylhet</option>
                    <option <?php if($user['division'] == "Rangpur") echo "selected"; ?>>Rangpur</option>
                    <option <?php if($user['division'] == "Mymensingh") echo "selected"; ?>>Mymensingh</option>
                </select>
            </div>

            <div class="input-group">
                <label>District</label>
                <select id="district">
                    <option value=""><?php echo htmlspecialchars($user['district']); ?></option>
                </select>
            </div>

            <button type="button" onclick="saveProfile()" class="btn-save">Save Changes</button>
            <div id="response-msg"></div>
        </form>
    </div>
</div>



<script>

function loadDistricts() {
    var div = document.getElementById("division").value;
    var dist = document.getElementById("district");
    dist.innerHTML = "<option value=''>Select District</option>";
    var list = [];
    if (div == "Dhaka") list = ["Dhaka", "Gazipur", "Narayanganj", "Narsingdi", "Tangail", "Kishoreganj", "Manikganj", "Munshiganj", "Rajbari", "Madaripur", "Gopalganj", "Faridpur", "Shariatpur"];
    else if (div == "Chattogram") list = ["Chattogram", "Cox's Bazar", "Cumilla", "Feni", "Brahmanbaria", "Noakhali", "Lakshmipur", "Chandpur", "Khagrachari", "Rangamati", "Bandarban"];
    else if (div == "Rajshahi") list = ["Rajshahi", "Bogura", "Pabna", "Sirajganj", "Naogaon", "Natore", "Joypurhat", "Chapai Nawabganj"];


    list.forEach(function(d) 
    {
        var opt = document.createElement("option");
        opt.value = d; opt.text = d;
        dist.add(opt);
    });
}

function saveProfile() {
    var msgBox = document.getElementById("response-msg");
    var formData = "ajax_update=true" +
                   "&name=" + encodeURIComponent(document.getElementById("name").value) +
                   "&phone=" + encodeURIComponent(document.getElementById("phone").value) +
                   "&division=" + encodeURIComponent(document.getElementById("division").value) +
                   "&district=" + encodeURIComponent(document.getElementById("district").value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_profile.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var res = JSON.parse(xhr.responseText);
            msgBox.style.display = "block";
            msgBox.innerHTML = res.message;
            msgBox.style.background = (res.status == "success") ? "#d4edda" : "#f8d7da";
            msgBox.style.color = (res.status == "success") ? "#155724" : "#721c24";
        }
    };
    xhr.send(formData);
}


window.onload = function() {
    loadDistricts();
    document.getElementById("district").value = "<?php echo $user['district']; ?>";
};
</script>

</body>
</html>