<?php 
include('server.php');
if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $query = "SELECT * FROM Mail_Conf WHERE username='$username' LIMIT 1";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
        $line = mysqli_fetch_array($results);
        if (!isset($_SESSION['token'])){
            $_SESSION['token'] = $line['token'];
        }
        include('header.php');
        if(!empty($_GET['message'])) {
            if ($_GET['message'] == 'reg_user'){
            echo '<div class="success">
                <p>Se ha registrado con éxito.</p>
            </div>';
            } 
            if ($_GET['message'] == 're_mail'){
                echo '<div class="success">
                    <p>Se ha reenviado el e-mail con éxito.</p>
                </div>';
                }
        }
        include('errors.php');
        echo '
        <form method="post" action="registered.php">
        <nav class="login">
        <h2>Reenviar e-mail</h2>
        <button class="login_but" type="submit" name="resend_mail_conf">Enviar</button>
        </nav>
        </form>
        ';
    } else {
        header("location: login.php");
    }
} else {
    header("location: login.php");
}

?>