<?php
include "db.php";

// 1. Create Categories Table
$sql1 = "CREATE TABLE IF NOT EXISTS crime_categories (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE
)";

// 2. Add Columns to Posts Table
$sql2 = "ALTER TABLE posts ADD COLUMN IF NOT EXISTS category_id INT(11) AFTER content";
$sql3 = "ALTER TABLE posts ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending' AFTER image";

// 3. Add Role to Users Table
$sql4 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(10) DEFAULT 'user' AFTER password";

// Execute queries procedurally
if (mysqli_query($conn, $sql1)) echo "Table 'crime_categories' ready.<br>";
if (mysqli_query($conn, $sql2)) echo "Column 'category_id' added.<br>";
if (mysqli_query($conn, $sql3)) echo "Column 'status' added.<br>";
if (mysqli_query($conn, $sql4)) echo "Column 'role' added.<br>";

// Insert default categories
mysqli_query($conn, "INSERT IGNORE INTO crime_categories (category_name) VALUES ('Theft'), ('Assault'), ('Fraud')");

echo "Database Update Complete!";
?>