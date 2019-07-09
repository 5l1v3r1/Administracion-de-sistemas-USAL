<?php
include('connection.php');
if (!isset($_GET['op'])){
    exit;
}
switch($_GET['op']){
    case 0:
        $username = mysqli_real_escape_string($db, $_GET['username']);
        if(check_username($username)){
            echo json_encode(array("Result" => true));
        } else {
            echo json_encode(array("Result" => false));
        }
        break;
    case 1:
        $email = mysqli_real_escape_string($db, $_GET['email']);
        if(check_email($email)){
            echo json_encode(array("Result" => true));
        } else {
            echo json_encode(array("Result" => false));
        }
        break;
    default:
        echo "Bad request";
        break;
}

function check_username($username){
    include('connection.php');
    $user_check_query = "SELECT * FROM Cuenta WHERE username='$username' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    if ($user) { // if user exists
        return true;
    }
    return false;
}
function check_email($email){
    include('connection.php');
    $mail_check_query = "SELECT * FROM Cuenta WHERE email='$email' LIMIT 1";
    $result = mysqli_query($db, $mail_check_query);
    $mail_check = mysqli_fetch_assoc($result);
    if ($mail_check) { // if user exists
        return true;
    }
    return false;
}
?>