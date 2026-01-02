<?php
session_start();
include "db.php";

$msg = "";

$user_id = $_SESSION["user_id"] ?? 0;
$user_name = $_SESSION["user_name"] ?? "";
if ($user_id == 0) {
    die("Please login first.");
}

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
            $msg = "Image upload failed!";
            $imageName = "";
        }
    }

    if ($content == "" && $imageName == "") {
        $msg = "What's on your mind?";
    } else 
    {
        $stmt = $conn->prepare(
            "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $content, $imageName);
        $stmt->execute();
        $stmt->close();
    }
}

$posts = mysqli_query(
    $conn,
    "SELECT posts.content, posts.image, posts.created_at, users.name
     FROM posts
     LEFT JOIN users ON posts.user_id = users.id
     ORDER BY posts.id DESC"
);
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
        .post-name { font-weight: bold; }
        small { color: gray; }
    </style>
</head>
<body>

<div class="container">

    <div class="post-box">
        <p><strong>Logged in as:</strong> <?php echo htmlspecialchars($user_name); ?></p>
        <p class="msg"><?php echo $msg; ?></p>

        <form method="post" enctype="multipart/form-data">
            <textarea name="content" rows="3" placeholder="Share your concern..."></textarea>
            <input type="file" name="image"><br>
            <button type="submit">Post</button>
        </form>
    </div>

    <?php if (mysqli_num_rows($posts) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($posts)): ?>
            <div class="post">
                <div class="post-name">
                    <?php echo htmlspecialchars($row["name"] ?? "Unknown User"); ?>
                </div>

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
