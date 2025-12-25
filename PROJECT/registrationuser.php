<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>

    <style>
        body 
        {
            font-family: Arial;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box 
        {
            background: white;
            padding: 20px;
            width: 320px;
            border-radius: 8px;
            box-shadow: 0 0 10px gray;
        }

        h2 {
            text-align: center;
        }

        input 
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
            cursor: pointer;
        }

        button:hover 
        {
            background: darkgreen;
        }

        .error 
        {
            color: red;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="form-box">
    <h2>Registration</h2>

    <p id="msg" class="error"></p>

    <input type="text" id="name" placeholder="Full Name">
    <input type="email" id="email" placeholder="Email">

    <input type="text" id="location" placeholder="Location">
    <input type="text" id="area" placeholder="Area">

    <input type="date" id="dob">
    <input type="tel" id="phone" placeholder="Phone Number">

    <button onclick="register()">Register</button>
</div>

<script>
    function register() {
        var name = document.getElementById("name").value;
        var email = document.getElementById("email").value;
        var location = document.getElementById("location").value;
        var area = document.getElementById("area").value;
        var dob = document.getElementById("dob").value;
        var phone = document.getElementById("phone").value;
        var msg = document.getElementById("msg");

        if (name == "" || email == "" || location == "" || area == "" || dob == "" || phone == "") {
            msg.innerText = "Please fill all details";
        } else {
            msg.innerText = "";
            alert("Registration Successful!");
        }
    }
</script>

</body>
</html>
