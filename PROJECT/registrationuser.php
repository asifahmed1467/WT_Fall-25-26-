<?php
$msg = "";
$fetch = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $name     = $_POST["name"] ?? "";
    $email    = $_POST["email"] ?? "";
    $division = $_POST["division"] ?? "";
    $district = $_POST["district"] ?? "";
    $dob      = $_POST["dob"] ?? "";
    $phone    = $_POST["phone"] ?? "";

    if (str_word_count($name) < 2) 
    {
        $msg = "Name must contain first name and last name";
    }
    else if (strpos($email, "@") === false || strpos($email, ".com") === false) 
    {
        $msg = "Email must contain @ and .com";
    }
    else if (!ctype_digit($phone) || strlen($phone) < 11)
    {
        $msg = "Phone number must be numeric and at least 11 digits";
    }
    else
    {
        $fetch = "
        <b>Registered Data:</b><br>
        Name: $name <br>
        Email: $email <br>
        Division: $division <br>
        District: $district <br>
        DOB: $dob <br>
        Phone: $phone";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Registration Form</title>

<style>
body {
    font-family: Arial;
    background:
        linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)),
        url("bg_rg.png");
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;

    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding-top: 60px;
}
.form-box 
{
    background: white;
    padding: 20px;
    width: 360px;
    border-radius: 8px;
    box-shadow: 0 0 10px gray;
    margin-top: 100px;
}
h2 { text-align: center; }
input, select 

{
    width: 100%;
    padding: 8px;
    margin: 6px 0;
}
button 
{
    width: 100%;
    padding: 10px;
    background: green;
    color: white;
    border: none;
}
.error 
{
    color: red;
    text-align: center;
}
.result 
{
    margin-top: 15px;
    padding: 10px;
    background: #e8f5e9;
}
</style>
</head>

<body>

<div class="form-box">
<h2>Registration</h2>

<p class="error"><?php echo $msg; ?></p>

<form method="post">

<input type="text" name="name" placeholder="Full Name" required>
<input type="text" name="email" placeholder="Email" required>

<select name="division" id="division" onchange="loadDistricts()" required>
    <option value="">Select Division</option>
    <option>Dhaka</option>
    <option>Chattogram</option>
    <option>Rajshahi</option>
    <option>Khulna</option>
    <option>Barishal</option>
    <option>Sylhet</option>
    <option>Rangpur</option>
    <option>Mymensingh</option>
</select>

<select name="district" id="district" required>
    <option value="">Select District</option>
</select>

<input type="date" name="dob" required>
<input type="text" name="phone" placeholder="Phone Number" required>

<button type="submit">Register</button>
</form>

<div class="result"><?php echo $fetch; ?></div>
</div>

<script>
function loadDistricts() 
{
    var division = document.getElementById("division").value;
    var districtBox = document.getElementById("district");

    districtBox.innerHTML = "<option value=''>Select District</option>";
    var list = [];

    if (division == "Dhaka")
    
   {
        list = ["Dhaka","Gazipur","Narayanganj","Narsingdi","Tangail",
                "Kishoreganj","Munshiganj","Manikganj","Faridpur",
                "Madaripur","Shariatpur","Gopalganj","Rajbari"];
    }
    else if (division == "Chattogram") 
    {
        list = ["Chattogram","Cox's Bazar","Comilla","Noakhali","Feni",
                "Chandpur","Bandarban","Rangamati","Khagrachari",
                "Lakshmipur","Brahmanbaria"];
    }
    else if (division == "Rajshahi") 
    {
        list = ["Rajshahi","Bogura","Naogaon","Natore","Pabna",
                "Sirajganj","Joypurhat","Chapainawabganj"];
    }
    else if (division == "Khulna") 
    {
        list = ["Khulna","Jashore","Satkhira","Bagerhat","Jhenaidah",
                "Magura","Narail","Chuadanga","Meherpur"];
    }
    else if (division == "Barishal") 
    
    {
        list = ["Barishal","Bhola","Patuakhali","Pirojpur",
                "Jhalokathi","Barguna"];
    }
    else if (division == "Sylhet") 
    {
        list = ["Sylhet","Moulvibazar","Habiganj","Sunamganj"];
    }
    else if (division == "Rangpur") 
    {
        list = ["Rangpur","Dinajpur","Kurigram","Gaibandha",
                "Lalmonirhat","Nilphamari","Panchagarh","Thakurgaon"];
    }
    else if (division == "Mymensingh") 
    {
        list = ["Mymensingh","Jamalpur","Netrokona","Sherpur"];
    }

    for (var i = 0; i < list.length; i++) 
    {
        var opt = document.createElement("option");
        opt.value = list[i];
        opt.text = list[i];
        districtBox.add(opt);
    }
}
</script>

</body>
</html>
