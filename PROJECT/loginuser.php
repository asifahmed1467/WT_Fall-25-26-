<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login {
            background: #ffffff;
            padding: 25px;
            width: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .login button:hover {
            background: #0056b3;
        }

        .show-password {
            font-size: 14px;
            margin-top: 5px;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login">
    <h2>Login</h2>
    <p id="error" class="error"></p>

    <input type="text" id="username" placeholder="Username">
    <input type="password" id="password" placeholder="Password">

    <div class="show-password">
        <input type="checkbox" onclick="togglePassword()"> Show Password
    </div>

    <button onclick="login()">Login</button>
</div>

<script>
    function togglePassword() 
    {
        const passwordField = document.getElementById("password");
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    }

    function login() {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const error = document.getElementById("error");

        if (username === "" || password === "") 
        {
            error.textContent = "Please fill in all fields";
            return;
        }

        // Demo login logic
        if (username === "admin" && password === "1234") {
            alert("Login successful!");
            error.textContent = "";
        } else 
        {
            error.textContent = "Invalid username or password";
        }
    }
</script>

</body>
</html>
