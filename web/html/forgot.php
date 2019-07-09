<?php
include('server.php');
if (check_auth()){
  header('Location: http://user.mmacademy.es/index.php');
}
include('header.php');
if(!empty($_GET['message'])) {
    if ($_GET['message'] == 'reset_pass'){
      echo '<nav class="dav"><div class="success">
        <p>Revise su email.</p>
      </div></nav>';
    }
}
?>

<form method="post" action="forgot.php">
    <?php include('errors.php'); ?>
    <nav class="login">
        <h2>¿Ha olvidado su contraseña?</h2>
        <div>
            <p class="login_text">Nombre de usuario o email:</p>
            <input class="login_fields" type="text" name="username">
        </div>
        <button class="login_but" type="submit" name="forgot_pass">Entrar</button>
    </nav>
</form>

<?php include('footer.php'); ?>