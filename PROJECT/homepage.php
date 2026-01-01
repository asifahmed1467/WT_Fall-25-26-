<?php
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $content = trim($_POST["content"]);
    $imageName = "";

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    if (!empty($_FILES["image"]["name"])) {

        $imageName = time() . "_" . str_replace(" ", "_", basename($_FILES["image"]["name"]));

        $uploadPath = "uploads/" . $imageName;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) {
            $msg = "Failed to upload file. Make sure 'uploads' folder exists and is writable.";
            $imageName = ""; 
        }
    }

    if ($content == "" && $imageName == "") {
        $msg = "What's on your mind?";
    } else {
        mysqli_query($conn, "INSERT INTO posts (content, image) VALUES ('$content', '$imageName')");
    }
}

$posts = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
if (!$posts) {
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Home</title>
<style>
body { font-family: Arial; background: #f0f2f5; }
.container { width: 500px; margin: auto; }
.post-box, .post {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}
textarea { width: 100%; padding: 10px; resize: none; }
button {
    background: #1877f2;
    color: white;
    border: none;
    padding: 8px 15px;
    margin-top: 10px;
    cursor: pointer;
}
.post img { width: 100%; margin-top: 10px; border-radius: 8px; }
.msg { color: red; text-align: center; }
small { color: gray; }
</style>
</head>
<body>

<div class="container">

    <!-- Post Form -->
    <div class="post-box">
        <p class="msg"><?php echo $msg; ?></p>
        <form method="post" enctype="multipart/form-data">
            <textarea name="content" rows="3" placeholder="Share your concern..."></textarea>
            <input type="file" name="image"><br>
            <button type="submit">Post</button>
        </form>
    </div>

    <!-- Display Posts -->
    <?php if (mysqli_num_rows($posts) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($posts)): ?>
            <div class="post">
                <p><?php echo nl2br(htmlspecialchars($row["content"])); ?></p>

                <?php if (!empty($row["image"])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row["image"]); ?>">
                <?php endif; ?>

                <small><?php echo $row["created_at"]; ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No posts yet.</p>
    <?php endif; ?>

</div>

</body>
</html>
