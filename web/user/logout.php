<?php
include('/var/www/html/connection.php');
session_start();
if (!isset($_SESSION['username'])){
    header('Location: /var/www/html/login.php');
}
$username = $_SESSION['username'];
$query = "DELETE FROM Session_token WHERE username='$username' AND token='".$_COOKIE['session']."'";
mysqli_query($db, $query);
session_destroy();
setcookie ("session", "", time() - 3600, "/", "", true, true);
header('Location: http://mmacademy.es/index.php');
?>