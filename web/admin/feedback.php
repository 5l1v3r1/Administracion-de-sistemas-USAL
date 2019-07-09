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

    echo '
    <nav class="admin-nav">
    ';
    if(empty($_GET['id'])) {
        echo '<h2>Feedback</h2>';
        $query = "SELECT * FROM Contacto";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) > 0) {
            while($line = mysqli_fetch_assoc($result)){
                echo "
                <div>
                <a href='feedback.php?id=".$line['id']."'>".$line['topic']." (".$line['name'].") - ".$line['time']."</a>
                </div>
                ";
            }   
        } else {
            echo "<div><p>No hay mensajes.<p></div>";
        }
    } else {
        echo '
        <a style="text-decoration: none;color: black;" href="feedback.php"><h2>Feedback</h2></a>
        ';
        $cod = mysqli_real_escape_string($db, intval($_GET['id']));
        $query = "SELECT * FROM Contacto where id=$cod";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) > 0) {
            $line = mysqli_fetch_assoc($result);
            echo "<div class='feed'><h3>".$line['topic']."</h3>";
            echo "<p>".$line['name']." (".$line['time'].") </p>";
            echo "<hr>";
            echo nl2br("<p>".$line['msg']."</p>");
            echo "<hr>";
            echo "<p>By: ". $line['email']."</p>";
            echo "
            <form method='post' action='feedback.php'>
                <input type='hidden' name='id' value='".$line['id']."'>
                <button type='submit' name='remove_feed'>
                <i class='fa fa-trash'></i>
                </button>
                <a href='mailto:".$line['email']."' class='fa fa-reply'></a>
            </form>
            ";
            echo "</div>";
        }
    }

    echo '</nav>';
?>



    </body>
</html>
