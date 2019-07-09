<?php
function check_auth(){
    include('/var/www/html/connection.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['username'])){
        return false;
    }
    $flag = false;
    $username = $_SESSION['username'];
    $query = "SELECT * FROM Session_token WHERE username='$username'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) !== 0) {
        while ($line = mysqli_fetch_array($results)){
            if ($line['time'] > time()){
                if (! isset($_COOKIE['session'])){
                    return false;
                }
                else {
                    if ($_COOKIE['session'] == $line['token']){
                        $flag = true;
                        break;
                    }
                }
            }
        }
        if (!$flag){
            return false;
        }
    } else {
        return false;
    }
    return true;
}

function check_rol(){
    include('/var/www/html/connection.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['username'])){
        header('Location: login.php');
    }
    $username = $_SESSION['username'];
    $filter="(sn=$username)";
    $justthese = array("gidNumber");
    $result = ldap_search($ds, "dc=mmacademy,dc=es", $filter, $justthese) or exit("Unable to search LDAP server");
    $info = ldap_get_entries($ds, $result);
    $gid = $info[0]['gidnumber'][0];
    if ($gid ) {
        if ($gid  === "500"){
            return 0;
        } else if ($gid  === "501"){
            return 1;
        } else if ($gid  === "502"){
            return 2;
        }
    } else {
        return -1;
    }
}
?>
