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
    if(!empty($_GET['message'])) {
        if ($_GET['message'] === 'up_success'){
          echo '<nav class="dav-man"><div class="success">
            <p>Fichero subido con éxito.</p>
          </div></nav>';
        }
        else if ($_GET['message'] === 'del_success'){
            echo '<nav class="dav-man"><div class="success">
            <p>Fichero eliminado con éxito.</p>
            </div></nav>';
        }
    }
?>
    <nav class="admin-nav">
        <h2>Manuales</h2>
        <?php 
            $my_dir = '/var/www/manuals';
            $dir_contents = scandir($my_dir);
            echo '<ul class="manual">';
            foreach ($dir_contents as $file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $ext_allowed = array("txt", "pdf", "doc", "docx", "odt", "html", "htm");
                if(in_array($extension, $ext_allowed)){
                    echo "<li><a href='$my_dir/$file'><img src='../icons/$extension.svg' alt='$file'>$file</a></li>";
                }
            }
            echo '</ul>';
            $rol = check_rol();
            if ($rol == 1 || $rol == 2){
            echo '
                <div class="manual-up">
                    <form action="manuals.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="up_man_input" name="file_to_upload" multiple>
                    <p id="up_man_p"><br>Arrastre aquí o clique para subir un manual.<br>(.txt, .pdf, .doc, .docx, .odt, .html, .htm)</p>
                    <button name="upload_man" type="submit">Subir</button>
                    </form>
                </div>';
            echo '
            <form action="manuals.php" method="post">
                <div class="manual-del">
                    <p style="margin-left:-1.9%;">
                        <select name="file_name">';
                        foreach ($dir_contents as $file) {
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            $ext_allowed = array("txt", "pdf", "doc", "docx", "odt", "html", "htm");
                            if(in_array($extension, $ext_allowed)){
                                echo "<option value=$file>$file</option>";
                            }
                        }
                        echo '</select> 
                    </p>
                    <button type="submit" name="delete_man">Eliminar Manual</button>
                </div>
            </form>';
            }
        ?>

    </nav>


    </body>
</html>
