<?php 
include('server.php');
include('header.php');
?>
    
<form method="post" action="registro.php">
    <?php include('errors.php'); ?>
    <nav class="login">
    <h2>Registro</h2>
    <div>
        <p class="login_text">Nombre de usuario:</p>
        <input class="login_fields" name="username" id="username" type="text" value="<?php echo $username;?>">
        <span id="user_result" class="weak" style="display:none"></span>
    </div>
    <div>
        <p class="login_text">Email:</p>
        <input class="login_fields" name="email" id="email" type="email" value="<?php echo $email; ?>">
        <span id="email_result" class="weak" style="display:none"></span>
    </div>
    <button class="login_but" type="submit" id ="reg_user" name="reg_user">Enviar</button>
    <a class="login_forgot" href="login.php">¿Ya tienes cuenta?</a>
    </nav>
</form>
<?php include('footer.php'); ?>


