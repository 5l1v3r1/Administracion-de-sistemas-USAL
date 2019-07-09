<?php
// connect to the database
include('server.php');

if(!empty($_GET['user']) && !empty($_GET['token'])) {
    $mail_token = $_GET['token'];
    $user_mail = $_GET['user'];
    $_SESSION['user_mail'] = $user_mail;
    $_SESSION['mail_token'] = $mail_token;

    $query = "SELECT * FROM Reset_pass WHERE user_mail='$user_mail' and token='$mail_token'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
        include('header.php');
        include('errors.php');
        echo '
        <form method="post" action="reset.php?user='.$user_mail.'&token='.$mail_token.'">
            <nav class="login">
            <h2>Reiniciar contraseña</h2>
            <div>
                <p class="login_text">Contraseña:</p>
                <input class="login_fields" type="password" name="password_1" id="password">
                <span id="result"></span>
            </div>
            <div>
                <p class="login_text">Confirmar contraseña:</p>
                <input class="login_fields" type="password" name="password_2" >
            </div>
            <button class="login_but" type="submit" name="reset_pass" id ="reg_user">Entrar</button>
            </nav>
        </form>';
    }
    } else {
        header('Location: login.php');
    }

?>