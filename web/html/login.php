<?php 
include('server.php');
if (check_auth()){
  header('Location: http://user.mmacademy.es/index.php');
}
include('header.php');
if(!empty($_GET['message'])) {
    if ($_GET['message'] == 'reg_user'){
      echo '<div class="success">
        <p>Se ha registrado con éxito.</p>
      </div>';
    }
    if ($_GET['message'] == 'reset_pass'){
      echo '<div class="success">
        <p>Se ha cambiado la contraseña con éxito.</p>
      </div>';
    }
    if ($_GET['message'] == 'banned'){
      echo '<div class="error">
        <p>Ha superado el número de intentos.</p>
      </div>';
    }
  }
?>
    <form method="post" action="login.php">
        <?php include('errors.php'); ?>
        <nav class="login">
        <h2>Entrar</h2>
        <div>
            <p class="login_text">Nombre de usuario:</p>
            <input class="login_fields" type="text" name="username">
        </div>
        <div>
            <p class="login_text">Contraseña:</p>
            <input class="login_fields" type="password" name="password" >
        </div>
        <button class="login_but" type="submit" name="login_user">Entrar</button>
        <a class="login_forgot" href="forgot.php">¿Ha olvidado su contraseña?</a>
        </nav>
        <?php 
        ?>
    </form>
    
<?php include('footer.php'); ?>