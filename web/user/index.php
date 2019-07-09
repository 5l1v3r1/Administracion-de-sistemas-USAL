<?php
    include('/var/www/html/server.php');
    if (! check_auth()){
        header('Location: https://mmacademy.es/login.php');
    }
    $rol = check_rol();
    if ($rol === -1){
        header('Location: https://mmacademy.es/login.php');
    } else if ($rol === 2){
        header('Location: https://admin.mmacademy.es');
    }
    include('header.php');
    include('menu.php');
    echo "<nav class='admin-nav'><h2>Bienvenido ".$_SESSION['username']."</h2>";
    $ssh = fsockopen("localhost", 22, $errno, $errstr, 2);
    fclose($ssh);
    $context = stream_context_create(array('ssl'=>array(
        'verify_peer' => true,
        "verify_peer_name"=>false,
        'cafile' => '/etc/apache2/ssl/apache.crt'
    )));
    $mail = fopen("http://mail.mmacademy.es", "r", false, $context);
    $query = "SELECT * FROM Cuenta WHERE username='".$_SESSION['username']."'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) != 0) {
        $line = mysqli_fetch_array($results);
        echo "<nav class='mod_data'><h3>Servicios</h3>";
        echo "<div><p>·Mi Web&emsp;&emsp;&emsp;";
        if ($line['web']){
            echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
        } else {
            echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
        }
        
        echo "<br><div><p>·Mi Blog &emsp;&emsp;&emsp;";
        if ($line['blog']){
            echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
        } else {
            echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
        }
        echo "<br><div><p>·Webmail &emsp;&emsp;";
        if ($mail){
            echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
        } else {
            echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
        }
        echo "<br><div><p>·SSH&emsp;&emsp;&emsp;&emsp;&emsp;";
        if ($ssh){
            echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
        } else {
            echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
        }
        echo "</nav>";
    }
    echo "</nav>";
    ?>
    </nav>
    </body>
</html>
