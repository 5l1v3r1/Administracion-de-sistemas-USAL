<?php
include('server.php');

if (isset($_SESSION['token'])){
  $mail_token = $_SESSION['token'];
}
else if(!empty($_GET['token'])){
  $mail_token = $_GET['token'];
  $_SESSION['token'] = $_GET['token'];
}
else {
  header('Location: login.php');
}

include('header.php');
$query = "SELECT * FROM Mail_Conf WHERE token='$mail_token'";
$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1) {
  $username = mysqli_fetch_array($results)['username'];

  include('errors.php');
  echo '
  <form method="post" action="confirm.php">
    <?php  ?>
    <nav class="register">
    <h2>Registro</h2>
    <div>
        <p class="register_text">Contraseña:</p>
        <input class="register_fields" name="password_1" type="password" id="password">
        <span id="result"></span>
    </div>
    <div>
        <p class="register_text">Confirmar contraseña:</p>
        <input class="register_fields" name="password_2" type="password">
    </div>
    <input name="username" type="hidden" value="'.$username.'">
    <div>
        <p class="register_text">Nombre:</p>
        <input class="register_fields" name="nombre" type="text" value="'.$nombre.'">
    </div>
    <div>
        <p class="register_text">Apellidos:</p>
        <input class="register_fields" name="apellidos" type="text" value="'.$apellidos.'">
    </div>
    <div>
        <p class="register_text">Dirección Postal:</p>
        <input class="register_fields" name="direccion" type="text" value="'.$direccion.'">
    </div>
    <button class="register_but" type="submit" id ="reg_user" name="conf_user">Confirmar</button>
    </nav>
  </form>
  ';
}

?>