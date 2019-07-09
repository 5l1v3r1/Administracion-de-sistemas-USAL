<?php
    include('/var/www/html/server.php');
    if (! check_auth()){
        header('Location: http://mmacademy.es/login.php');
    }
    $rol = check_rol();
    if ($rol === -1){
        header('Location: http://mmacademy.es/login.php');
    } else if ($rol === 2){
        header('Location: .http://admin.mmacademy.es/');
    }
    include('header.php');
    include('menu.php');
    include('/var/www/html/errors.php');
    if(!empty($_GET['message'])) {
        if ($_GET['message'] == 'mod_data_success'){
          echo '<nav class="dav"><div class="success">
            <p>Datos modificados con exito.</p>
          </div></nav>';
        }
        else if ($_GET['message'] == 'mod_pass_success'){
            echo '<nav class="dav"><div class="success">
              <p>Contraseña modificada con exito.</p>
            </div></nav>';
        }
    }

?>

    <nav class="admin-nav">
        <h2>Ajustes</h2>
        <form method="post" action="settings.php">
            <nav class="mod_data">
                <h3>Modificar Datos Personales</h3>
                <div>
                    <p>Email:</p>
                    <input name="email" type="email" value="<?php echo ($_SESSION['email']);?>">
                </div>
                <div>
                    <p>Nombre y apellidos:</p>
                    <input name="nombre" type="text" value="<?php echo ($_SESSION['nombre']);?>">
                </div>
                <div>
                    <p>Dirección postal:</p>
                    <input name="direccion" type="text" value="<?php echo $_SESSION['direccion'];?>">
                </div>
                <div>
                    <button type="submit" name="mod_user">Guardar</button>
                </div>
            </nav>
        </form>
        <form method="post" action="settings.php">
            <nav class="mod_data">
                <h3>Darse de baja</h3>
                <div>
                    <p>Contraseña:</p>
                    <input name="password" type="password">
                </div>
                <div>
                    <button class="del_button" type="submit" name="del_user">Enviar</button>
                </div>
            </nav>
        </form>
        <form method="post" action="settings.php">
            <nav class="mod_data">
                <h3>Modificar Contraseña</h3>
                <div>
                    <p>Contraseña anterior:</p>
                    <input name="password_3" type="password">
                </div>
                <div>
                    <p>Contraseña:</p>
                    <input name="password_1" id="password" type="password">
                    <span id="result"></span>
                </div>
                <div>
                    <p>Confirmar contraseña:</p>
                    <input name="password_2" type="password">
                </div>
                <div>
                    <button type="submit" name="mod_pass">Guardar</button>
                </div>
            </nav>
        </form>
    </nav>
    </body>
</html>