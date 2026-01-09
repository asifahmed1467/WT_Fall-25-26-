<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"] ?? 0;
if ($user_id == 0) { header("Location: loginuser.php"); exit(); }

$success = $error = "";

if (isset($_POST['submit_report'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $lat = mysqli_real_escape_string($conn, $_POST['lat']);
    $lng = mysqli_real_escape_string($conn, $_POST['lng']);

    if (empty($lat) || empty($lng)) {
        $error = "Please click on the map to select a location!";
    } else {
        $sql = "INSERT INTO posts (user_id, content, lat, lng, status) VALUES ('$user_id', '$content', '$lat', '$lng', 'Pending')";
        
        if (mysqli_query($conn, $sql)) {
            $radius = 2.0; 
            $nearby_sql = "SELECT id FROM users WHERE id != '$user_id' AND 
                (6371 * acos(cos(radians($lat)) * cos(radians(user_lat)) * cos(radians(user_lng) - radians($lng)) + sin(radians($lat)) * sin(radians(user_lat)))) < $radius";
            
            $result = mysqli_query($conn, $nearby_sql);
            while($row = mysqli_fetch_assoc($result)) {
                $near_id = $row['id'];
                mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('$near_id', 'Danger! Crime reported near you.')");
            }
            
            $success = "Report submitted and nearby users notified!";
        } else {
            $error = mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Crime | Map Selection</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #1a252f; color: white; height: 100vh; position: fixed; padding: 20px; }
        .main { margin-left: 250px; padding: 40px; width: 100%; }

        #map { 
            height: 400px; 
            width: 100%; 
            border-radius: 10px; 
            border: 2px solid #34495e; 
            margin-bottom: 20px;
        }
        
        .form-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .nav-btn { display: block; background: #34495e; color: white; padding: 12px; text-decoration: none; margin-bottom: 10px; border-radius: 5px; text-align: center; }
        textarea { width: 100%; padding: 15px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        .sub-btn { background: #e74c3c; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 16px; }
    </style>

    <script>
        let marker;
        function initMap() {

            const dhaka = { lat: 23.8103, lng: 90.4125 };
            const map = new google.maps.Map(document.getElementById("map"), 
            {
                zoom: 13,
                center: dhaka,
            });

            map.addListener("click", (event) => 
            {
                addMarker(event.latLng, map);
            });
        }

        function addMarker(location, map) 
        {
            if (marker) {
                marker.setMap(null); 
            }
            marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP
            });

            document.getElementById("lat").value = location.lat();
            document.getElementById("lng").value = location.lng();
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>CRIME REPORT</h2>
    <a href="profile.php" class="nav-btn">My Profile</a>
    <a href="report_crime.php" class="nav-btn" style="background: #e74c3c;">Report Crime</a>
    <a href="logout.php" class="nav-btn" style="margin-top: 50px; background: #c0392b;">Logout</a>
</div>

<div class="main">
    <div class="form-container">
        <h2>Report New Incident</h2>
        <p>1. Click on the map to select the exact location of the crime.</p>
        
        <div id="map"></div>

        <form method="post">
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">

            <p>2. Provide details about the incident:</p>
            <textarea name="content" rows="4" placeholder="What happened?" required></textarea><br><br>
            
            <input type="submit" name="submit_report" value="Submit Report & Notify Neighbors" class="sub-btn">
        </form>

        <p style="color:green;"><?php echo $success; ?></p>
        <p style="color:red;"><?php echo $error; ?></p>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsnn0ZmcSODM68Vx2LBCHp3MvpKKXR_kQ&callback=initMap" async defer></script>

</body>
</html>
