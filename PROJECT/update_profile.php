<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
if ($user_id == 0) { 
    header("Location: loginuser.php"); 
    exit(); 
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];

    $user_q = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id'");
    $user_data = mysqli_fetch_assoc($user_q);

    if (password_verify($old_pass, $user_data['password'])) {
        $hashed_new = password_hash($new_pass, PASSWORD_DEFAULT);
        $update_pass_sql = "UPDATE users SET password='$hashed_new' WHERE id='$user_id'";
        
        if (mysqli_query($conn, $update_pass_sql)) {
            echo json_encode(["status" => "success", "message" => "Password changed successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
    }
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settings | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 220px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e74c3c; margin-top: 0; font-size: 1.2rem; }
        .nav-btn { display: block; background: #34495e; color: white; text-decoration: none; padding: 12px; margin-top: 10px; border-radius: 6px; font-size: 14px; text-align: center; transition: 0.3s; }
        .nav-btn:hover { background: #e74c3c; }
        .main { margin-left: 220px; padding: 40px; width: 100%; display: flex; flex-direction: column; align-items: center; }
        .edit-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; margin-bottom: 25px; }
        .input-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; color: #666; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-save { width: 100%; padding: 12px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { background: #c0392b; }
        .response-box { padding: 10px; margin-top: 15px; border-radius: 5px; text-align: center; display: none; font-size: 14px; }
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
        <p style="font-size: 13px; color: #888;">Update your contact details.</p>
        <hr>
        <form id="profileForm">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" id="name" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            <div class="input-group">
                <label>Email (Permanent)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f9f9f9; color:#999;">
            </div>
            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="input-group">
                <label>Division</label>
                <select id="division" onchange="loadDistricts()">
                    <option value="">Select Division</option>
                    <?php 
                    $divisions = ["Barishal", "Chattogram", "Dhaka", "Khulna", "Mymensingh", "Rajshahi", "Rangpur", "Sylhet"];
                    foreach($divisions as $d) {
                        $sel = ($user['division'] == $d) ? "selected" : "";
                        echo "<option value='$d' $sel>$d</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label>District</label>
                <select id="district">
                    <option value="">Select District</option>
                </select>
            </div>
            <button type="button" onclick="saveProfile()" class="btn-save">Update Profile</button>
            <div id="response-msg" class="response-box"></div>
        </form>
    </div>

    <div class="edit-card">
        <h3>Security Settings</h3>
        <p style="font-size: 13px; color: #888;">Update your login password.</p>
        <hr>
        <div class="input-group">
            <label>Current Password</label>
            <input type="password" id="old_password" placeholder="Verify old password">
        </div>
        <div class="input-group">
            <label>New Password</label>
            <input type="password" id="new_password" placeholder="Min. 6 characters">
        </div>
        <button type="button" onclick="changePassword()" class="btn-save" style="background:#34495e;">Change Password</button>
        <div id="pass-msg" class="response-box"></div>
    </div>
</div>

<script>
function loadDistricts() {
    var div = document.getElementById("division").value;
    var dist = document.getElementById("district");
    dist.innerHTML = "<option value=''>Select District</option>";
    var list = [];
    
    if (div == "Barishal") list = ["Barguna", "Barishal", "Bhola", "Jhalokati", "Patuakhali", "Pirojpur"];
    else if (div == "Chattogram") list = ["Bandarban", "Brahmanbaria", "Chandpur", "Chattogram", "Cumilla", "Cox's Bazar", "Feni", "Khagrachhari", "Lakshmipur", "Noakhali", "Rangamati"];
    else if (div == "Dhaka") list = ["Dhaka", "Faridpur", "Gazipur", "Gopalganj", "Kishoreganj", "Madaripur", "Manikganj", "Munshiganj", "Narayanganj", "Narsingdi", "Rajbari", "Shariatpur", "Tangail"];
    else if (div == "Khulna") list = ["Bagerhat", "Chuadanga", "Jessore", "Jhenaidah", "Khulna", "Kushtia", "Magura", "Meherpur", "Narail", "Satkhira"];
    else if (div == "Mymensingh") list = ["Jamalpur", "Mymensingh", "Netrokona", "Sherpur"];
    else if (div == "Rajshahi") list = ["Bogra", "Chapai Nawabganj", "Joypurhat", "Naogaon", "Natore", "Pabna", "Rajshahi", "Sirajganj"];
    else if (div == "Rangpur") list = ["Dinajpur", "Gaibandha", "Kurigram", "Lalmonirhat", "Nilphamari", "Panchagarh", "Rangpur", "Thakurgaon"];
    else if (div == "Sylhet") list = ["Habiganj", "Moulvibazar", "Sunamganj", "Sylhet"];

    list.forEach(function(d) {
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

function changePassword() {
    var old_p = document.getElementById("old_password").value;
    var new_p = document.getElementById("new_password").value;
    var msgBox = document.getElementById("pass-msg");

    if(!old_p || !new_p) { alert("Fill all password fields"); return; }

    var formData = "ajax_password=true" +
                   "&old_password=" + encodeURIComponent(old_p) +
                   "&new_password=" + encodeURIComponent(new_p);

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
            if(res.status == "success") {
                document.getElementById("old_password").value = "";
                document.getElementById("new_password").value = "";
            }
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