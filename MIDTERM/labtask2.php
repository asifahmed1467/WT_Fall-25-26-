<!DOCTYPE html>
<html>
<head>
  <title>Student Registration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 30px;
      background-color: #f0f8ff;
    }

    h2 {
      text-align: center;
      color: #003366;
    }

    form {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      width: 300px;
      margin: 0 auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    input
   {
      width: 100%;
      padding: 8px;
      margin-top: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      width: 30%;
      padding: 10px;
      margin-top: 10px;
      background-color: #003366;
      color: white;
      border-radius: 10px;
      cursor: pointer;
    }

    button:hover 
    {
      background-color: #0055aa;
    }

    #output 
    {
      margin-top: 20px;
      text-align: center;
      font-size: 16px;
      color: #003366;
    }

    #error 
    {
      margin-top: 10px;
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>

  <h2>Student Registration</h2>

  <form onsubmit="return handleSubmit()">
    <label="name">Full Name:</label>
    <input type="text" id="name" />

    <label="email">Email:</label>
    <input type="email" id="email" />

    <label="password">Password:</label>
    <input type="password" id="password" />

    <label="confirmPassword">Confirm Password:</label>
    <input type="password" id="confirmPassword" />

    <button type="submit">Register</button>
  </form>

  <div id="error"></div>
  <div id="output"></div>
  
  <script>
    function handleSubmit() 
    {
      var name = document.getElementById("name").value.trim();
      var email = document.getElementById("email").value.trim();
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("confirmPassword").value;

      var errorDiv = document.getElementById("error");
      var outputDiv = document.getElementById("output");
      errorDiv.innerHTML = "";
      outputDiv.innerHTML = "";

      if (name === "" || email === "" || password === "" || confirmPassword === "") 
      {
        errorDiv.innerHTML = "All fields are required.";
        return false;
      }

      if (!email.includes('@')) 
      {
        errorDiv.innerHTML = "Please enter a valid email address.";
        return false;
      }

      if (password !== confirmPassword) 
      {
        errorDiv.innerHTML ="Passwords do not match.";
        return false;
      }
      outputDiv.innerHTML = `
        <strong>Registration Successful!</strong><br><br>
        Name:${name}<br>
        Email:${email}
      `;
      return false; 
    }
  </script>

</body>
</html>
