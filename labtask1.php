<!DOCTYPE html>
<html>
<head>
    <title>PHP Form Validation</title>
</head>

<body>
<h1>PHP Form Validation</h1>

<?php
$name = $email = $day = $month = $year = "";
$nameErr = $emailErr = $dobErr = $genderErr = $degreeErr = $bloodErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // NAME validation
    if (empty($_POST["name"])) {
        $nameErr = "Name cannot be empty";
    } else {
        $name = test_input($_POST["name"]);
        if (!preg_match("/^[A-Za-z][A-Za-z .-]*$/", $name)) 
        {
            $nameErr = "Invalid name format";
        } elseif (str_word_count($name) < 2) 
        {
            $nameErr = "Name must contain at least two words";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email cannot be empty";
    } 
    else 
        {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["day"]) || empty($_POST["month"]) || empty($_POST["year"])) {
        $dobErr = "Date of birth cannot be empty";
    } else {
        $day = $_POST["day"];
        $month = $_POST["month"];
        $year = $_POST["year"];

        if ($day < 1 || $day > 31 || $month < 1 || $month > 12 || $year < 1953 || $year > 1998)
        {
            $dobErr = "Invalid date of birth";
        }
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Select at least one gender";
    }


    if (empty($_POST["degree"]) || count($_POST["degree"]) < 2) 
        {
        $degreeErr = "Select at least two degrees";
    }


    if (empty($_POST["blood"]))
     {
        $bloodErr = "Select a blood group";
    }
}

function test_input($data) {
    return trim($data);
}
?>

<form method="post">

    Name:
    <input type="text" name="name" value="<?php echo $name; ?>">
    <span style="color:red"><?php echo $nameErr; ?></span>
    <br><br>

    Email:
    <input type="text" name="email" value="<?php echo $email; ?>">
    <span style="color:red"><?php echo $emailErr; ?></span>
    <br><br>

    Date of Birth:
    <input type="text" name="day" size="2"> /
    <input type="text" name="month" size="2"> /
    <input type="text" name="year" size="4">
    
    <br><br>

    Gender:
    <input type="radio" name="gender" value="Male"> Male
    <input type="radio" name="gender" value="Female"> Female
    <span style="color:red"><?php echo $genderErr; ?></span>
    <br><br>

    Degree:
    <input type="checkbox" name="degree[]" value="SSC"> SSC
    <input type="checkbox" name="degree[]" value="HSC"> HSC
    <input type="checkbox" name="degree[]" value="BSc"> BSc
    <span style="color:red"><?php echo $degreeErr; ?></span>
    <br><br>

    Blood Group:
    <select name="blood">
        <option value="">Select</option>
        <option value="A+">A+</option>
        <option value="B+">B+</option>
        <option value="O+">O+</option>
    </select>
    <span style="color:red"><?php echo $bloodErr; ?></span>
    <br><br>
    <input type="submit" value="Submit">

</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" &&
    empty($nameErr) && empty($emailErr) && empty($dobErr) &&
    empty($genderErr) && empty($degreeErr) && empty($bloodErr)) {

    echo "<h3>Form Submitted Successfully</h3>";

    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Date of Birth:</strong> $day/$month/$year</p>";
    echo "<p><strong>Gender:</strong> ".$_POST['gender']."</p>";

    echo "<p><strong>Degree:</strong> ";
    echo implode(", ", $_POST['degree']);
    echo "</p>";

    echo "<p><strong>Blood Group:</strong> ".$_POST['blood']."</p>";
}
?>
</body>
</html>
