<?php
    include('/var/www/html/server.php');
    if (! check_auth()){
        header('Location: http://mmacademy.es/login.php');
    }
    $rol = check_rol();
    if ($rol === -1){
        header('Location: http://mmacademy.es/login.php');
    } else if ($rol === 0 || $rol === 1){
        header('Location: http://user.mmacademy.es');
    }
    include('header.php');
    include('menu.php');
    $username = $_SESSION['username'];
    if (!file_exists("/home/".$username)) {
        echo '<nav class="service_un">
            <h2>Debe haberse logeado al servidor SSH para poder activar su blog</h2>
            <p>ssh '.$username.'@mmacademy.es
            </nav>
            ';
    } else {
        $query = "SELECT * FROM Cuenta WHERE username='$username' AND blog=1";
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
            header('Location: http://'.$_SESSION['username'].'.mmacademy.es/wp');
        } else {
            echo '
            <form method="post" action="blog.php">
                <nav class="service">
                    <h2>Activar Servicio Mi Blog</h2>
                    <div>
                        <button type="submit" name="activa_blog">Activar</button>
                    </div>
                </nav>
            </form>
            ';
        }
    }

?>

    </body>
</html>
