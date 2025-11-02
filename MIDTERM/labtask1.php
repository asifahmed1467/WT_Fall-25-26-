<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clinic Patient Registration</title>
</head>
<body style="font-family: Arial, sans-serif; display: flex; justify-content: center; padding: 40px;">
 
  <div style="background-color: #fffaf6; width: 100%; max-width: 600px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px;">
 
    <h2 style="text-align: center; color: #003366; margin-top: 0;">Clinic Patient Registration</h2>
    <form>
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Full Name:</label><br>
      <input type="text" style="width: 100%; padding: 15px; margin-bottom: 20px;  line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Age:</label><br>
      <input type="number" style="width: 100%; padding: 12px; margin-bottom: 20px; line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Phone Number:</label><br>
      <input type="text"  style="width: 100%; padding: 15px; margin-bottom: 20px; line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Email Address:</label><br>
      <input type="email"  style="width: 100%; padding: 12px; margin-bottom: 20px;  line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Insurance Provider:</label><br>
      <select style="width: 100%; padding: 14px; margin-bottom: 20px; line-height: 1.4;">
        <option>Select Provider</option>
        <option>RG</option>
        <option>Zenetic Esports</option>
        <option>BFF</option>
      </select><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Insurance Policy Number:</label><br>
      <input type="text" style="width: 100%; padding: 14px; margin-bottom: 20px;   line-height: 1.4;"><br>

      <h3 style="text-align: center;  margin-bottom: 20px;">Additional Information</h3>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Username:</label><br>
      <input type="text" style="width: 100%; padding: 12px; margin-bottom: 20px;line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Password:</label><br>
      <input type="password" style="width: 100%; padding: 12px; margin-bottom: 20px; line-height: 1.4;"><br>
      
      <label style="font-weight: bold; font-size: 1.1rem; line-height: 1.5;">Confirm Password:</label><br>
      <input type="password" style="width: 100%; padding: 12px; margin-bottom: 30px; 1rem; line-height: 1.4;"><br>
      
      <button type="submit" style="width: 100%; background-color: #00796b; color: white; padding: 14px">
        Register
      </button>
    </form>
  </div>
</body>
</html>
