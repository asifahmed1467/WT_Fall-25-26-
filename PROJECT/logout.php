<?php
session_start();
session_destroy();
header("Location: loginuser.php");
exit();
