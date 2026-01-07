<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
$user_name = $_SESSION["user_name"] ?? "";

if ($user_id == 0) { 
    header("Location: loginuser.php"); 
    exit(); 
}

$filter = $_GET['filter'] ?? 'All';

$total_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id'");
$pending_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id' AND status = 'Pending'");
$resolved_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id' AND status = 'Resolved'");

$total_stats = mysqli_fetch_assoc($total_q)['count'];
$pending_stats = mysqli_fetch_assoc($pending_q)['count'];
$resolved_stats = mysqli_fetch_assoc($resolved_q)['count'];

$sql = "SELECT posts.*, crime_categories.category_name 
        FROM posts 
        LEFT JOIN crime_categories ON posts.category_id = crime_categories.id 
        WHERE user_id = '$user_id'";

if ($filter == 'Pending') {
    $sql .= " AND status = 'Pending'";
} elseif ($filter == 'Resolved') {
    $sql .= " AND status = 'Resolved'";
}

$sql .= " ORDER BY posts.id DESC";
$reports = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile | Crime Detection</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        
        .sidebar { width: 220px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e74c3c; margin-top: 0; font-size: 1.2rem; }
        
        .nav-btn { display: block; background: #34495e; color: white; text-decoration: none; padding: 12px; margin-top: 10px; border-radius: 6px; font-size: 14px; transition: 0.3s; text-align: center; }
        .nav-btn:hover { background: #e74c3c; }
        .active-btn { background: #e74c3c; }

        .main { margin-left: 220px; padding: 40px; width: 100%; box-sizing: border-box; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #ddd; }
        .stat-card h1 { margin: 10px 0; color: #2c3e50; font-size: 2.2rem; }
        .stat-card p { color: #7f8c8d; margin: 0; text-transform: uppercase; font-size: 11px; font-weight: bold; }
        .total-card { border-color: #3498db; }
        .pending-card { border-color: #f1c40f; }
        .resolved-card { border-color: #2ecc71; }

        .filter-section { background: white; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-btns a { text-decoration: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; color: #555; background: #eee; margin-right: 5px; transition: 0.2s; }
        .filter-btns a.active-filter { background: #2c3e50; color: white; }

        .table-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-size: 12px; text-transform: uppercase; }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .Pending { background: #fff3cd; color: #856404; }
        .Resolved { background: #d4edda; color: #155724; }
        
        .view-link { color: #e74c3c; text-decoration: none; font-size: 12px; font-weight: bold; }
        .view-link:hover { text-decoration: underline; }
        .btn-edit {
    background: #34495e;
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-edit:hover 
{
    background: #e74c3c;
}
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <p style="font-size:14px;">User Settings</p>
    <hr style="border: 0; border-top: 1px solid #34495e; margin: 20px 0;">
    <a href="homepage.php" class="nav-btn">Dashboard</a>
    <a href="profile.php" class="nav-btn active-btn">My Profile</a>
    <a href="logout.php" class="nav-btn" style="margin-top:40px; background:#c0392b;">Logout</a>
</div>


<div class="main">
    <h2 style="color: #2c3e50;">Welcome, <?php echo htmlspecialchars($user_name); ?></h2>

    <div class="stats-grid">
        <div class="stat-card total-card">
            <p>Total Reports</p>
            <h1><?php echo $total_stats; ?></h1>
        </div>
        <div class="stat-card pending-card">
            <p>Under Review</p>
            <h1><?php echo $pending_stats; ?></h1>
        </div>
        <div class="stat-card resolved-card">
            <p>Successfully Resolved</p>
            <h1><?php echo $resolved_stats; ?></h1>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-btns">
            <a href="profile.php?filter=All" class="<?php echo $filter == 'All' ? 'active-filter' : ''; ?>">All History</a>
            <a href="profile.php?filter=Pending" class="<?php echo $filter == 'Pending' ? 'active-filter' : ''; ?>">Pending</a>
            <a href="profile.php?filter=Resolved" class="<?php echo $filter == 'Resolved' ? 'active-filter' : ''; ?>">Resolved</a>
        </div>
        <span style="font-size: 12px; color: #888;">Filter reports by their current status</span>
    </div>

    <div class="profile-card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Account Information</h3>
        <a href="update_profile.php" class="btn-edit">Edit Profile ⚙️</a>
    </div>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
    <p><strong>Location:</strong> <?php echo $user_data['district'] . ", " . $user_data['division']; ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></p>
</div>

    

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Date Reported</th>
                    <th>Category</th>
                    <th>Incident Description</th>
                    <th>Evidence</th>
                    <th>Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($reports) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($reports)): ?>
                    <tr>
                        <td style="font-size: 13px; color: #666;">
                            <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                        </td>
                        <td>
                            <strong style="color: #2c3e50;"><?php echo htmlspecialchars($row['category_name'] ?? 'General'); ?></strong>
                        </td>
                        <td style="max-width: 350px; font-size: 14px; color: #555;">
                            <?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 100))); ?>...
                        </td>
                        <td>
                            <?php if($row['image']): ?>
                                <a href="uploads/<?php echo $row['image']; ?>" target="_blank" class="view-link">View Proof</a>
                            <?php else: ?>
                                <span style="color: #bbb; font-size: 12px;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $row['status']; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; color: #999;">
                            You haven't submitted any reports yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>