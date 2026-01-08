<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
$user_name = $_SESSION["user_name"] ?? "";

if ($user_id == 0) { 
    header("Location: loginuser.php"); 
    exit(); 
}

$success = $error = "";
$mode = $_GET['mode'] ?? 'view';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM posts WHERE id=$id AND user_id=$user_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: profile.php?mode=delete");
        exit();
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $sql = "UPDATE posts SET content='$content' WHERE id=$id AND user_id=$user_id";
    if (mysqli_query($conn, $sql)) {
        $success = "Update successful!";
    } else {
        $error = mysqli_error($conn);
    }
}

$editPost = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM posts WHERE id=$id AND user_id=$user_id");
    $editPost = mysqli_fetch_assoc($result);
}

$user_q = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_q);

$total_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id'");
$pending_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id' AND status = 'Pending'");
$resolved_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id' AND status = 'Resolved'");

$total_stats = mysqli_fetch_assoc($total_q)['count'];
$pending_stats = mysqli_fetch_assoc($pending_q)['count'];
$resolved_stats = mysqli_fetch_assoc($resolved_q)['count'];

$sql_list = "SELECT posts.*, crime_categories.category_name 
             FROM posts 
             LEFT JOIN crime_categories ON posts.category_id = crime_categories.id 
             WHERE user_id = '$user_id' ORDER BY posts.id DESC";
$reports = mysqli_query($conn, $sql_list);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e74c3c; margin-top: 0; font-size: 1.2rem; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .nav-btn { display: block; background: #34495e; color: white; text-decoration: none; padding: 12px; margin-top: 10px; border-radius: 6px; font-size: 14px; text-align: center; transition: 0.3s; }
        .active-btn { background: #e74c3c; }
        .delete-btn-sidebar { background: #c0392b; border: 2px dashed #ff9999; }
        .delete-btn-sidebar:hover { background: #a93226; }
        .main { margin-left: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .profile-header { display: flex; justify-content: space-between; align-items: center; }
        .edit-profile-btn { background: #3498db; color: white; text-decoration: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: bold; transition: 0.3s; }
        .edit-profile-btn:hover { background: #2980b9; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; text-align: center; border-bottom: 4px solid #ddd; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-size: 12px; text-transform: uppercase; }
        .btn-edit { color: #3498db; text-decoration: none; font-weight: bold; }
        .btn-del { color: #e74c3c; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #e74c3c; border-radius: 4px; }
        .btn-del:hover { background: #e74c3c; color: white; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .Pending { background: #fff3cd; color: #856404; }
        .Resolved { background: #d4edda; color: #155724; }
        textarea { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-family: inherit; box-sizing: border-box; }
        .save-btn { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        .delete-highlight { border: 2px solid #e74c3c; background: #fff5f5; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <a href="homepage.php" class="nav-btn">Dashboard</a>
    <a href="profile.php" class="nav-btn <?php echo ($mode == 'view' && !$editPost) ? 'active-btn' : ''; ?>">My Profile</a>
    <a href="update_profile.php" class="nav-btn">⚙️ Edit Profile</a>
    <a href="profile.php?mode=delete" class="nav-btn delete-btn-sidebar <?php echo ($mode == 'delete') ? 'active-btn' : ''; ?>">Delete Reports</a>
    <a href="logout.php" class="nav-btn" style="margin-top:40px; background:#c0392b;">Logout</a>
</div>

<div class="main">
    <div class="card">
        <div class="profile-header">
            <h3>Account Information</h3>
            <a href="update_profile.php" class="edit-profile-btn">Edit Profile Settings ⚙️</a>
        </div>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email'] ?? 'N/A'); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($user_data['district'] ?? '') . ", " . htmlspecialchars($user_data['division'] ?? ''); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone'] ?? 'N/A'); ?></p>
    </div>

    <?php if ($editPost): ?>
    <div class="card" style="border: 2px solid #3498db;">
        <h3>Update Report #<?php echo $editPost['id']; ?></h3>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
            <textarea name="content" rows="4"><?php echo htmlspecialchars($editPost['content']); ?></textarea><br><br>
            <input type="submit" name="update" value="Save Changes" class="save-btn">
            <a href="profile.php" style="margin-left: 15px; color: #666;">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card" style="border-color: #3498db;"><p>Total</p><h1><?php echo $total_stats; ?></h1></div>
        <div class="stat-card" style="border-color: #f1c40f;"><p>Pending</p><h1><?php echo $pending_stats; ?></h1></div>
        <div class="stat-card" style="border-color: #2ecc71;"><p>Resolved</p><h1><?php echo $resolved_stats; ?></h1></div>
    </div>

    <p style="color:green; font-weight:bold;"><?php echo $success; ?></p>
    <p style="color:red; font-weight:bold;"><?php echo $error; ?></p>

    <div class="card <?php echo ($mode == 'delete') ? 'delete-highlight' : ''; ?>">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($reports) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($reports)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['category_name'] ?? 'General'); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['content']); ?></td>
                        <td><span class="status-badge <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                        <td>
                            <?php if ($mode == 'delete'): ?>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Are you sure?')">Confirm Delete</a>
                            <?php else: ?>
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #999; padding: 30px;">No reports found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>