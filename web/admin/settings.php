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
                <h3>Modificar Contraseña</h3>
                <div>
                    <p>Contraseña anterior:</p>
                    <input name="password_3" type="password">
                </div>
                <div>
                    <p>Nueva contraseña:</p>
                    <input name="password_1" type="password">
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