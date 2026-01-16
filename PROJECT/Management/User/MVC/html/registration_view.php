<!DOCTYPE html>
<html>
<head>
    <title>Registration | Crime Detection</title>
    <link rel="stylesheet" type="text/css" href="../css/registration_style.css">
</head>
<body>

<div class="form-box">
    <h2>REGISTRATION</h2>

    <?php if(!empty($msg)): ?>
        <div class="status-msg <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="registration_controller.php" autocomplete="off">
        <div class="input-group">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="input-group" style="display: flex; gap: 10px;">
            <select name="division" id="division" onchange="loadDistricts()" required>
                <option value="">Select Division</option>
                <?php 
                $divs = ["Dhaka", "Chattogram", "Rajshahi", "Khulna", "Barishal", "Sylhet", "Rangpur", "Mymensingh"];
                foreach($divs as $d) {
                    $sel = ($division == $d) ? "selected" : "";
                    echo "<option $sel>$d</option>";
                }
                ?>
            </select>
            <select name="district" id="district" required>
                <option value="">District</option>
            </select>
        </div>

        <div class="input-group">
            <label class="dob-label">Date of Birth</label>
            <input type="date" name="dob" value="<?php echo $dob; ?>" required>
        </div>

        <div class="input-group">
            <input type="text" name="phone" placeholder="Phone Number (11 Digits)" value="<?php echo htmlspecialchars($phone); ?>" required>
        </div>

        <div class="input-group security-box">
            <span class="sec-label">Account Recovery Settings</span>
            <select name="security_question" required style="margin-bottom: 10px;">
                <option value="">Select Security Question</option>
                <option value="What is your pet's name?">What is your pet's name?</option>
                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                <option value="What was your first school?">What was your first school?</option>
                <option value="What is your favorite city?">What is your favorite city?</option>
            </select>
            <input type="text" name="security_answer" placeholder="Your Answer" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        </div>

        <div class="input-group">
            <input type="password" name="cpassword" placeholder="Confirm Password" required>
        </div>

        <button type="submit">Create Account</button>
        
        <div class="footer">
            Already have an account? <a href="login_controller.php">Login</a>
        </div>
    </form>
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
    else if (div == "Khulna") list = ["Khulna", "Jashore", "Satkhira", "Bagerhat", "Kushtia", "Magura", "Meherpur", "Narail", "Chuadanga", "Jhenaidah"];
    else if (div == "Barishal") list = ["Barishal", "Bhola", "Patuakhali", "Pirojpur", "Jhalokathi", "Barguna"];
    else if (div == "Sylhet") list = ["Sylhet", "Moulvibazar", "Habiganj", "Sunamganj"];
    else if (div == "Rangpur") list = ["Rangpur", "Dinajpur", "Kurigram", "Gaibandha", "Nilphamari", "Panchagarh", "Thakurgaon", "Lalmonirhat"];
    else if (div == "Mymensingh") list = ["Mymensingh", "Jamalpur", "Netrokona", "Sherpur"];

    list.forEach(function(d) {
        var opt = document.createElement("option");
        opt.value = d; opt.text = d;
        dist.add(opt);
    });
}

window.onload = function() {
    var savedDistrict = "<?php echo $district; ?>";
    if(document.getElementById("division").value !== "") {
        loadDistricts();
        document.getElementById("district").value = savedDistrict;
    }
};
</script>

</body>
</html>