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
$filter_cat = isset($_GET['filter_cat']) ? mysqli_real_escape_string($conn, $_GET['filter_cat']) : "";
$filter_date = isset($_GET['filter_date']) ? mysqli_real_escape_string($conn, $_GET['filter_date']) : "";

$cat_query = "SELECT * FROM crime_categories ORDER BY category_name ASC";
$categories_result = mysqli_query($conn, $cat_query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_post'])) {
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
              LEFT JOIN crime_categories ON posts.category_id = crime_categories.id
              WHERE 1=1"; 

if ($search != "") {
    $sql_fetch .= " AND (posts.content LIKE '%$search%' OR users.name LIKE '%$search%')";
}
if ($filter_cat != "") {
    $sql_fetch .= " AND posts.category_id = '$filter_cat'";
}
if ($filter_date != "") {
    $sql_fetch .= " AND DATE(posts.created_at) = '$filter_date'";
}

$sql_fetch .= " ORDER BY posts.id DESC";
$posts = mysqli_query($conn, $sql_fetch);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        
        .sidebar { width: 220px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e74c3c; margin-top: 0; font-size: 1.2rem; }
        
        .nav-btn { display: block; background: #34495e; color: white; text-decoration: none; padding: 12px; margin-top: 10px; border-radius: 6px; font-size: 14px; transition: 0.3s; text-align: center; }
        .nav-btn:hover { background: #e74c3c; }
        
        .logout-link { display: block; margin-top: 30px; color: #bdc3c7; text-decoration: none; font-size: 14px; border: 1px solid #444; padding: 8px; text-align: center; border-radius: 4px; }

        .chat-sidebar { width: 320px; background: #fff; height: 100vh; position: fixed; right: 0; border-left: 1px solid #ddd; display: flex; flex-direction: column; }
        .chat-header { background: #2c3e50; color: white; padding: 15px; text-align: center; font-weight: bold; }
        #chat-box { flex: 1; overflow-y: auto; padding: 15px; background: #fdfdfd; display: flex; flex-direction: column; }
        .chat-input-area { padding: 15px; border-top: 1px solid #ddd; background: #fff; }

        .msg-bubble { margin-bottom: 12px; padding: 10px; border-radius: 8px; background: #f1f3f5; border-left: 4px solid #e74c3c; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .msg-bubble b { color: #2c3e50; display: block; font-size: 12px; margin-bottom: 3px; }
        .msg-bubble span { font-size: 14px; color: #444; word-wrap: break-word; }
        .msg-time { font-size: 10px; color: #999; margin-top: 5px; text-align: right; }

        .main { margin-left: 220px; margin-right: 320px; padding: 30px; width: 100%; box-sizing: border-box; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
 
        textarea, select, input { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-red { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; width: 100%; }
        .btn-red:hover { background: #c0392b; }

        .filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end; }
        .btn-search { background: #34495e; height: 38px; width: auto; }

        .post-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .tag { background: #3498db; color: white; padding: 4px 12px; border-radius: 20px; font-size: 10px; text-transform: uppercase; }
        .status { font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 4px; }
        .Pending { background: #fff3cd; color: #856404; }
        .Resolved { background: #d4edda; color: #155724; }
        .post-image { width: 100%; border-radius: 8px; margin: 10px 0; max-height: 400px; object-fit: cover; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <p style="font-size:14px;">Logged in as:<br><strong><?php echo htmlspecialchars($user_name); ?></strong></p>
    <hr style="border: 0.5px solid #34495e;">
    <a href="homepage.php" class="nav-btn">Dashboard</a>
    <a href="profile.php" class="nav-btn">My Profile</a>
    <a href="logout.php" class="logout-link">→ Logout</a>
</div>

<div class="main">
    <div class="card">
        <form method="GET" action="homepage.php" class="filter-grid">
            <div>
                <label style="font-size:11px; font-weight:bold;">Search</label>
                <input type="text" name="search" placeholder="Keyword..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div>
                <label style="font-size:11px; font-weight:bold;">Category</label>
                <select name="filter_cat">
                    <option value="">All Categories</option>
                    <?php mysqli_data_seek($categories_result, 0); 
                    while($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if($filter_cat == $cat['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label style="font-size:11px; font-weight:bold;">Date</label>
                <input type="date" name="filter_date" value="<?php echo $filter_date; ?>">
            </div>
            <button type="submit" class="btn-red btn-search">Apply Filters</button>
        </form>
    </div>

    <div class="card">
        <h3>Report an Incident</h3>
        <?php if($msg): ?><p style="color:#e74c3c; font-weight:bold; font-size:13px;"><?php echo $msg; ?></p><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <textarea name="content" rows="2" placeholder="Describe the crime situation..." required></textarea>
            <select name="category_id" required>
                <option value="">-- Choose Category --</option>
                <?php mysqli_data_seek($categories_result, 0); 
                while($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <div style="margin: 5px 0;"><small>Evidence Image:</small> <input type="file" name="image"></div>
            <button type="submit" name="submit_post" class="btn-red">Broadcast Alert</button>
        </form>
    </div>

    <h2 style="color: #34495e; font-size:1.2rem;">Recent Alerts Feed</h2>

    <?php if (mysqli_num_rows($posts) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($posts)): ?>
            <div class="card">
                <div class="post-header">
                    <span style="font-weight:bold; color:#2c3e50;"><?php echo htmlspecialchars($row["name"]); ?></span>
                    <span class="tag"><?php echo htmlspecialchars($row["category_name"] ?? 'General'); ?></span>
                </div>
                <p style="color:#444; line-height:1.5;"><?php echo nl2br(htmlspecialchars($row["content"])); ?></p>
                <?php if ($row["image"]): ?>
                    <img src="uploads/<?php echo $row["image"]; ?>" class="post-image">
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                    <small style="color:#95a5a6;"><?php echo $row["created_at"]; ?></small>
                    <span class="status <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:#999; padding:20px;">No alerts found for these criteria.</p>
    <?php endif; ?>
</div>

<div class="chat-sidebar">
    <div class="chat-header">Global Community Chat</div>
    <div id="chat-box"></div>
    <div class="chat-input-area">
        <input type="text" id="chat-input" placeholder="Type a message..." onkeypress="if(event.key==='Enter') sendMessage()">
        <button class="btn-red" style="margin-top:8px; padding:8px;" onclick="sendMessage()">Send Message</button>
    </div>
</div>

<script>
function fetchChat() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "chat_handler.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var data = JSON.parse(xhr.responseText); 
                var box = document.getElementById("chat-box");
                var html = "";
                data.forEach(function(m) {
                    html += "<div class='msg-bubble'>" +
                            "<b>" + m.name + ":</b>" +
                            "<span>" + m.message + "</span>" +
                            "<div class='msg-time'>" + m.created_at + "</div>" + 
                            "</div>";
                });
                box.innerHTML = html;
                box.scrollTop = box.scrollHeight; 
            } catch(e) { console.error("JSON Error: " + e); }
        }
    };
    xhr.send();
}

function sendMessage() {
    var el = document.getElementById("chat-input");
    var val = el.value.trim();
    if (val == "") return;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "chat_handler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) 
        {
            el.value = ""; 
            fetchChat(); 
        }
    };
    xhr.send("message=" + encodeURIComponent(val));
}

setInterval(fetchChat, 3000);
window.onload = fetchChat;
</script>

</body>
</html>