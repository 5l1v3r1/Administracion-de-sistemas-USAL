<?php
    include('/var/www/html/server.php');
    if (! check_auth()){
        header('Location: http://mmacademy.es/login.php');
    }
    $rol = check_rol();
    if ($rol === -1){
        header('Location: http://mmacademy.es/login.php');
    } else if ($rol === 2){
        header('Location: http://admin.mmacademy.es/');
    }
    include('header.php');
    include('menu.php');
    $username = $_SESSION['username'];
    if (!file_exists("/home/".$username)) {
        echo '<nav class="service_un">
            <h2>Debe haberse logeado al servidor SSH para poder activar su mail</h2>
            <p>ssh '.$username.'@mmacademy.es
            </nav>
            ';
    } else {
        header("location: http://mail.mmacademy.es");
    }

?>

    </body>
</html>
