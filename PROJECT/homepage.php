<?php
session_start();
include "db.php"; 

$msg = "";
$user_id = $_SESSION["user_id"] ?? 0;
$user_name = $_SESSION["user_name"] ?? "";

if ($user_id == 0) {
    header("Location: loginuser.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$cat_query = "SELECT * FROM crime_categories ORDER BY category_name ASC";
$categories_result = mysqli_query($conn, $cat_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = mysqli_real_escape_string($conn, trim($_POST["content"]));
    $cat_id = mysqli_real_escape_string($conn, $_POST["category_id"] ?? "");
    $imageName = "";

    if (!is_dir("uploads")) { mkdir("uploads", 0777, true); }

    if (!empty($_FILES["image"]["name"])) {
        $imageName = time() . "_" . str_replace(" ", "_", basename($_FILES["image"]["name"]));
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $imageName);
    }

    if ($content != "" && !empty($cat_id)) {

        $sql = "INSERT INTO posts (user_id, content, category_id, image, status) 
                VALUES ('$user_id', '$content', '$cat_id', '$imageName', 'Pending')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: homepage.php"); 
            exit();
        } else { $msg = "Database Error: " . mysqli_error($conn); }
    } else { 
        $msg = "Please fill in the description and select a category."; 
    }
}

$sql_fetch = "SELECT posts.*, users.name, crime_categories.category_name 
              FROM posts 
              LEFT JOIN users ON posts.user_id = users.id 
              LEFT JOIN crime_categories ON posts.category_id = crime_categories.id";

if ($search != "") {
    $sql_fetch .= " WHERE posts.content LIKE '%$search%' 
                    OR crime_categories.category_name LIKE '%$search%' 
                    OR users.name LIKE '%$search%'";
}

$sql_fetch .= " ORDER BY posts.id DESC";

$posts = mysqli_query($conn, $sql_fetch);

if (!$posts) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }

        .sidebar { width: 240px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e74c3c; margin-top: 0; }
        .logout-link { display: block; margin-top: 30px; color: #bdc3c7; text-decoration: none; font-size: 14px; }

        .main { margin-left: 240px; padding: 30px; width: 100%; max-width: 800px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
 
        textarea, select, .search-input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-red { background: #e74c3c; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; transition: 0.3s; }
        .btn-red:hover { background: #c0392b; }

        .search-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .btn-search { width: auto; background: #34495e; margin: 10px 0; }

        .post-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .author { font-weight: bold; font-size: 1.1rem; color: #2c3e50; }
        .tag { background: #3498db; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; text-transform: uppercase; }
        .status { font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 4px; }
        .Pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .Resolved { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .post-image { width: 100%; border-radius: 8px; margin: 10px 0; max-height: 450px; object-fit: cover; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <p>Logged in as:<br><strong><?php echo htmlspecialchars($user_name); ?></strong></p>
    <hr style="border: 0.5px solid #34495e;">
    <a href="homepage.php" style="color:white; text-decoration:none; display:block; margin-top:10px;">Dashboard</a>
    <a href="logout.php" class="logout-link">→ Logout</a>
</div>

<div class="main">
    <div class="card">
        <form method="GET" action="homepage.php" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Search here" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-red btn-search">Search</button>
        </form>
        <?php if ($search != ""): ?>
            <small>Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong> | <a href="homepage.php" style="color: #e74c3c;">Clear</a></small>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Report a Crime</h3>
        <?php if($msg): ?><p style="color: #e74c3c; font-weight:bold;"><?php echo $msg; ?></p><?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <textarea name="content" rows="3" placeholder="What happened? Provide details..."></textarea>
            
            <select name="category_id" required>
                <option value="">-- Select Crime Category --</option>
                <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <div style="margin: 10px 0;">
                <label style="font-size: 13px; color: #666;">Attach Evidence (Image):</label><br>
                <input type="file" name="image">
            </div>
            
            <button type="submit" class="btn-red">Broadcast Alert</button>
        </form>
    </div>

    <h2 style="color: #34495e;">Recent Alerts</h2>
    
    

    <?php if (mysqli_num_rows($posts) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($posts)): ?>
            <div class="card">
                <div class="post-header">
                    <span class="author"><?php echo htmlspecialchars($row["name"]); ?></span>
                    <span class="tag"><?php echo htmlspecialchars($row["category_name"] ?? 'General'); ?></span>
                </div>
                
                <p style="line-height: 1.6; color: #444;"><?php echo nl2br(htmlspecialchars($row["content"])); ?></p>
                
                <?php if ($row["image"]): ?>
                    <img src="uploads/<?php echo $row["image"]; ?>" class="post-image">
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                    <small style="color: #95a5a6;"><?php echo $row["created_at"]; ?></small>
                    <span class="status <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color: #95a5a6;">No alerts match your criteria.</p>
    <?php endif; ?>
</div>

</body>
</html>