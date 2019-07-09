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
    echo "<nav class='admin-nav'><h2>Bienvenido ".$_SESSION['username']."</h2>";
    $ssh = fsockopen("localhost", 22, $errno, $errstr, 2);
    fclose($ssh);
    $mysql = fsockopen("localhost", 3306, $errno, $errstr, 2);
    fclose($mysql);
    $ldap = fsockopen("localhost", 389, $errno, $errstr, 2);
    fclose($ldap);
    $context = stream_context_create(array('ssl'=>array(
        'verify_peer' => true,
        "verify_peer_name"=>false,
        'cafile' => '/etc/apache2/ssl/apache.crt'
    )));
    $mail = fopen("http://mail.mmacademy.es", "r", false, $context);
    echo "<nav class='mod_data'><h3>Servicios</h3>";
    echo "<div><p>·Webmail &emsp;&emsp;";
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
    echo "<br><div><p>·MySQL&emsp;&emsp;&emsp;";
    if ($mysql){
        echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
    } else {
        echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
    }
    echo "<br><div><p>·LDAP&emsp;&emsp;&emsp;&emsp;";
    if ($ldap){
        echo "<i style='color: green;' class='fa fa-check-circle'></i></p></div>";
    } else {
        echo "<i style='color: red;' class='fa fa-times-circle'></i></p></div>";
    }
    echo "</nav>";
    include('/var/www/html/errors.php');
    echo "<nav class='mod_data'><h3>Dar de alta técnicos</h3>";
    $filter="(|(gidnumber=500))";
    $justthese = array("sn");
    $result = ldap_search($ds, "dc=mmacademy,dc=es", $filter, $justthese);
    $info = ldap_get_entries($ds, $result);
    echo "<form method='post' action='index.php'><div><select name='up_clientes'>";
    for ($i = 1; $i < $info["count"]; $i++){
        echo "<option value=".$info[$i]["sn"]["0"].">".$info[$i]["sn"]["0"]."</option>";
    }
    echo "</select><br>";
    echo "<button type='submit' name='up_cliente'>Dar de alta</button>";
    echo "</div></form></nav>";

    $filter="(|(gidnumber=500)(gidnumber=501))";
    $justthese = array("sn");
    $result = ldap_search($ds, "dc=mmacademy,dc=es", $filter, $justthese);
    $info = ldap_get_entries($ds, $result);
    echo "<nav class='mod_data'><h3>Dar de alta administradores</h3>";
    echo "<form method='post' action='index.php'><div><select name='up_tecnicos'>";
    for ($i = 2; $i < $info["count"]; $i++){
        echo "<option value=".$info[$i]["sn"]["0"].">".$info[$i]["sn"]["0"]."</option>";
    }
    echo "</select><br>";
    echo "<button type='submit' name='up_tecnico'>Dar de alta</button>";
    echo "</div></form></nav>";
?>
    </body>
</html>
