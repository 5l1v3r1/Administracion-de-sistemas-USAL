<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
date_default_timezone_set('Etc/UTC');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// initializing variables
$username = "";
$email    = "";
$nombre = "";
$apellidos = "";
$direccion = "";
$errors = array();

// connect to the database
include("/var/www/html/connection.php");
include("/var/www/html/auth.php");

// REGISTER USER
if (isset($_POST['reg_user'])) {
  
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);

  $reserved = array("admin", "user", "mail", "ldap", "manuals");

  if (in_array($username, $reserved)){
    array_push($errors, "Usuario no permitido");
  }
  $strings = array('AbCd1zyZ9', $username);
  foreach ($strings as $testcase) {
    if (! ctype_alnum($testcase)) {
      array_push($errors, "Solo se permiten números y letras para el nombre de usuario.");
    }
}
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM Cuenta WHERE username='$username' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  if ($user) { // if user exists
    array_push($errors, "Username ya existe");
  }
    // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $mail_check_query = "SELECT * FROM Cuenta WHERE email='$email' LIMIT 1";
  $result = mysqli_query($db, $mail_check_query);
  $mail_check = mysqli_fetch_assoc($result);
  if ($mail_check) { // if user exists
    array_push($errors, "Email ya existe");
  }
  // Mail que no exista

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $mail_token = md5(generate_random_string(48)); //create the token for mail validation
    $_SESSION['token'] = $mail_token;
    $_SESSION['username'] = $username;

    $subject = 'M & M Confirmation Account';
    $body= "
    Please click on the link below to confirm your user:<br><br>
    
    <a href='http://mmacademy.es/confirm.php?token=$mail_token'> Click here </a>  
   
    ";
    $query = "INSERT INTO Mail_Conf (username, token) VALUES('$username', '$mail_token')";
    mysqli_query($db, $query);
    $query = "INSERT INTO Cuenta (username, email) VALUES('$username', '$email')";
    mysqli_query($db, $query);

    enviar_mail($body, $subject, $email, $username);
    header('Location: registered.php?message=reg_user');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  $ip = getenv("REMOTE_ADDR");
  $fecha_act = time();
  $query = "SELECT * FROM intento where ip='$ip'";
  $results = mysqli_query($db, $query);
  if (mysqli_num_rows($results) != 0) {
    $line = mysqli_fetch_array($results);
    $fecha = $line['fecha'];
    if ($fecha > $fecha_act - 60*10){
      if ($line['intentos'] >= 5){
        header("location: login.php?message=banned");exit();
      }
    }
  }

  $query = "SELECT * FROM Cuenta where username='$username' AND confirmado=1";
  $results = mysqli_query($db, $query);
  if (mysqli_num_rows($results) != 0){
    $_SESSION['username'] = $username;
    if (count($errors) == 0) {
      $rol = check_rol();
      if ($rol != -1){
        switch($rol){
          case 0:
          $r_1 = ldap_bind($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $password);
          break;
          case 1:
          $r_2 = ldap_bind($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $password);
          break;
          case 2:
          $r_3 = ldap_bind($ds, "cn=$username,cn=administradores,ou=grupos,dc=mmacademy,dc=es", $password);
          break;
        }
        if ($r_1 || $r_2 || $r_3) {
    
          $filter="(sn=$username)";
          $justthese = array("gidNumber", "mail", "givenName", "postalAddress");
          $result = ldap_search($ds, "dc=mmacademy,dc=es", $filter, $justthese) or exit("Unable to search LDAP server");
          $info = ldap_get_entries($ds, $result);
    
          $_SESSION['nombre'] = $info[0]['givenname'][0];
          $_SESSION['direccion'] = $info[0]['postaladdress'][0];
          $_SESSION['email'] = $info[0]['mail'][0];
    
          $token = md5(generate_random_string(48));
          $time_exp = time() + 900;
          setcookie ("session", $token, $time, "/", ".mmacademy.es");
          $query = "SELECT * FROM Session_token WHERE username='$username'";
          $results = mysqli_query($db, $query);
          while ($line = mysqli_fetch_array($results)){
            if ($line['time'] < time()){
              $query = "DELETE FROM Session_token WHERE token='".$line['token']."' and username='".$line['username']."'";
              mysqli_query($db, $query);
            }
          }
          $query = "INSERT INTO Session_token (username, token, time) VALUES('$username', '$token', '$time_exp')";
          mysqli_query($db, $query);

          if ($r_1 || $r_2){
            header('location: http://user.mmacademy.es/index.php');
          }
          else if ($r_3){
            $ip = getUserIpAddr();
            $subject = 'M & M: Ha accedido un usuario admin';
            $body= "
            Un usuario Administrador ha accedido a la plataforma.<br>
            IP: $ip
            <br>";
        
            enviar_mail($body, $subject, "manulpb@gmail.com", "Admin");
            header('location: http://admin.mmacademy.es/index.php');
          }
        }else {
          inc_ban($db);
          array_push($errors, "Wrong username/password combination");
        }
      }
    }
  } else {
    inc_ban($db);
    array_push($errors, "Wrong username/password combination");
  }
}

// MOD USER
if (isset($_POST['mod_user']) && isset($_SESSION['username'])) {
  // receive all input values from the form
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
  $direccion = mysqli_real_escape_string($db, $_POST['direccion']);
  $username = $_SESSION['username'];
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($nombre)) { array_push($errors, "Nombre is required"); }
  if (empty($direccion)) { array_push($errors, "Direccion is required"); }

  if (count($errors) == 0) {
    $attr["givenname"] = $nombre;
    $attr["mail"] = $email;
    $attr["postaladdress"] = $direccion;
    $rol = check_rol();
    switch($rol){
      case 0:
      $r_1 = ldap_modify($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $attr);
      break;
      case 1:
      $r_2 = ldap_modify($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $attr);
      break;
    }
    if ($r_1 || $r_2){
      $query = "UPDATE Cuenta SET email='$email' WHERE username='".$_SESSION['username']."'";
      mysqli_query($db, $query);
      $_SESSION['email'] = $email;
      $_SESSION['nombre'] = $nombre;
      $_SESSION['apellidos'] = $apellidos;
      $_SESSION['direccion'] = $direccion;
      header('location: settings.php?message=mod_data_success');
    }
  }
}
// MOD PASS
if (isset($_POST['mod_pass'])) {
  
  // receive all input values from the form
  $username = $_SESSION['username'];
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $password_3 = mysqli_real_escape_string($db, $_POST['password_3']);

  if (empty($password_3)) { array_push($errors, "Contraseña anterior is required"); }
  else if (empty($password_1)) { array_push($errors, "Password is required"); }
  else if ($password_1 !== $password_2) {
	  array_push($errors, "The two passwords do not match");
  }
  if (strlen($password_1) < 8){
    array_push($errors, "Contraseña demasiado corta (Mínimo 8 caracteres)");
  }
  if (!preg_match("#[0-9]+#", $password_1)){
    array_push($errors, "La contraseña debe contener al menos un número");
  }
  if (!preg_match("#[a-zA-Z]+#", $password_1)) {
    array_push($errors, "La contraseña debe contener al menos una letra");
  } 
  if (count($errors) == 0) {
    $attr["userPassword"] = ssha($password_1);
    $rol = check_rol();
    switch($rol){
      case 0:
      $r_1 = ldap_bind($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $password_3);
      if ($r_1){
        $r_1 = ldap_modify($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $attr);
        header('location: settings.php?message=mod_data_success');
      } else {
        array_push($errors, "Contraseña anterior incorrecta.");
      }
      break;
      case 1:
        $r_2 = ldap_bind($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $password_3);
        if ($r_2){
          $r_2 = ldap_modify($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $attr);
          header('location: settings.php?message=mod_data_success');
        } else {
          array_push($errors, "Contraseña anterior incorrecta.");
        }
      break;
    }
  }
}
// DEL USER
if (isset($_POST['del_user'])) {
  // receive all input values from the form
  $username = $_SESSION['username'];
  $password_1 = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($password_1)) { array_push($errors, "Contraseña is required"); }
  if (count($errors) == 0) {
    $rol = check_rol();
    switch($rol){
      case 0:
      $r_1 = ldap_bind($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $password_1);
      break;
      case 1:
        $r_2 = ldap_bind($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $password_1);
      break;
    }
    if ($r_1 || $r_2){
      $r_3 = ldap_bind($ds, "cn=admin,dc=mmacademy,dc=es", "Ud?Yug62H2LQxb");
      if ($r_3){
        $filter="(sn=$username)";
        $justthese = array("cn");
        $result = ldap_search($ds, "dc=mmacademy,dc=es", $filter, $justthese) or exit("Unable to search LDAP server");
        $info = ldap_get_entries($ds, $result);
        ldap_delete($ds, $info[0]['dn']);
        $query = "DELETE FROM `Cuenta` WHERE username='$username'";
        mysqli_query($db, $query);
        $query = "DELETE FROM Session_token WHERE username='$username'";
        mysqli_query($db, $query);
        header('location: http://mmacademy.es/index.php?message=del_user');
      } else {
        array_push($errors, "Error borrando su cuenta, vuelva a intentarlo más tarde");
      }
    } else {
      array_push($errors, "Contraseña anterior incorrecta.");
    }
  }
}
//CONTACTO
if (isset($_POST['contact'])) {
  $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $tema = mysqli_real_escape_string($db, $_POST['tema']);
  $mensaje = mysqli_real_escape_string($db, $_POST['msg']);

  if (empty($nombre)) { array_push($errors, "Nombre no puede estar vacio."); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($tema)) { array_push($errors, "Tema is required"); }
  if (empty($mensaje)) { array_push($errors, "Mensaje is required"); }

  if (count($errors) == 0) {
    $time = date("d M, Y");
  	$query = "INSERT INTO Contacto (name, email, topic, msg, time) 
  			  VALUES('$nombre', '$email', '$tema', '$mensaje', '$time')";
    mysqli_query($db, $query);
  	header('Location: contact.php?message=msg_success');
  }
}
//Borrar feedback
if (isset($_POST['remove_feed'])){
  $cod = mysqli_real_escape_string($db, $_POST['id']);
  $query = "DELETE FROM Contacto WHERE id=$cod";
  mysqli_query($db, $query);
}
// Recordad contraseña
if (isset($_POST['forgot_pass'])) {
  $user_or_mail = mysqli_real_escape_string($db, $_POST['username']);
  if (empty($user_or_mail)) {
    array_push($errors, "Username/email is required");
  }
  if (count($errors) == 0) {
    $query = "SELECT * FROM Cuenta WHERE username='$user_or_mail' or email='$user_or_mail'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) != 0) {
      $line = mysqli_fetch_array($results);
      $email = $line['email'];

      $mail_token = md5(generate_random_string(20)); //create the token for mail validation
      $user_mail = base64_encode($email);
  
      $subject = 'M & M Reincio de contraseña';
      $body= "
      Please click on the link below to confirm your user:<br><br>
      
     <a href='http://mmacademy.es/reset.php?user=$user_mail&token=$mail_token'> Click here </a> 
     
      ";
  
      if (enviar_mail($body, $subject, $email, "")) {
        $time_exp = time() + 900;
        $query = "INSERT INTO Reset_pass (user_mail, token, time) VALUES('$user_mail', '$mail_token', '$time_exp')";
        mysqli_query($db, $query);
        header('location: forgot.php?message=reset_pass');
      } else {
        array_push($errors, "Failed sending reset email, try again later.");
      }
    }
  }
}
//Reiniciar contraseña
if (isset($_POST['reset_pass'])){
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  if (isset($_SESSION['user_mail']) && isset($_SESSION['mail_token'])){
    $user_mail= $_SESSION['user_mail'];
    $mail_token = $_SESSION['mail_token'];
    $query = "SELECT * FROM Reset_pass WHERE user_mail='$user_mail' AND token='$mail_token'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) != 0) {
      if ($password_1 !== $password_2) {
        array_push($errors, "The two passwords do not match");
      }
      if (strlen($password_1) < 8){
        array_push($errors, "Contraseña demasiado corta (Mínimo 8 caracteres)");
      }
      if (!preg_match("#[0-9]+#", $password_1)){
        array_push($errors, "La contraseña debe contener al menos un número");
      }
      if (!preg_match("#[a-zA-Z]+#", $password_1)) {
        array_push($errors, "La contraseña debe contener al menos una letra");
      }
      if ( mysqli_fetch_array($results)['time'] < time()){
        array_push($errors, "Reinicio de contraseña caducado, solicitelo de nuevo.");
      }
      if (count($errors) == 0){
        $query = "DELETE FROM Reset_pass WHERE user_mail='$user_mail' AND token='$mail_token'";
        mysqli_query($db, $query);
        $user_mail = base64_decode($user_mail);
        $query = "SELECT * FROM Cuenta WHERE email='$user_mail' or username='$user_mail'";
        $results = mysqli_query($db, $query);
        $line = mysqli_fetch_array($results);
        $username = $line['username'];
        $_SESSION['username'] = $username;
        $attr["userPassword"] = ssha($password_1);
        $rol = check_rol();
        switch($rol){
          case 0:
            $r_1 = ldap_modify($ds, "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $attr);
            break;
          case 1:
            $r_2 = ldap_modify($ds, "cn=$username,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $attr);
          break;
        }
        if ($r_1 || $r_2) {;
          header('Location: login.php?message=reset_pass');
        }
      }
    }
  }
}
if(isset($_POST["upload_man"])) {
  $man_path = "/var/www/manuals/";
  if (check_rol() != 0){
    if(isset($_FILES["file_to_upload"]) && $_FILES['file_to_upload']['error'] === 0){
      $allowed = array("htm" => "text/html", "html" => "text/html", "txt" => "text/plain", "pdf" => "application/pdf", "doc" => "application/msword", "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "odt" => "application/vnd.oasis.opendocument.text");
      $filename = str_replace(' ', '', $_FILES["file_to_upload"]["name"]);
      $filesize = $_FILES["file_to_upload"]["size"];
      //https://stackoverflow.com/questions/7349473/php-file-upload-mime-or-extension-based-verification
      $mimetype = mime_content_type($_FILES["file_to_upload"]["tmp_name"]);
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if(!array_key_exists($ext, $allowed)){
          array_push($errors, "Seleccione un formato de fichero válido.");
      }//die("Error: Please select a valid file format.");
      $maxsize = 25 * 1024 * 1024; //Max Size: 25MB
      if($filesize > $maxsize){
          array_push($errors, "El fichero es mayor que el limite permitido.");
      } //die("Error: File size is larger than the allowed limit.");
      if (count($errors) == 0) {
          if(in_array($mimetype, $allowed)){
              if(file_exists($man_path . $filename)){
                  array_push($errors, $filename . " ya existe.");
              } else {
                  if(move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $man_path . $filename)){
                      header('Location: http://manuals.mmacademy.es/index.php?message=up_success');
                  } else {
                      array_push($errors, "Error subiendo el fichero.");
                  }
              }
          } else {
              array_push($errors, "Seleccione un formato de fichero válido.");
          } 
      } else {
          array_push($errors, "Error subiendo el fichero.");
      }
    } else {
      array_push($errors, "Error subiendo el fichero.");
    }
  }
}
if (isset($_POST['delete_man'])) {
  $rol = check_rol();
  if ($rol == 1 || $rol == 2){
    $man_path = "/var/www/manuals/";
    $file_to_del = $man_path . $_POST['file_name'];
    if (file_exists($file_to_del)){
      unlink($file_to_del);
    }
    header('Location: http://manuals.mmacademy.es/index.php?message=del_success');
  }
}

//Activar servicio Blog
if (isset($_POST['activa_blog'])){
  if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $query = "SELECT * FROM Cuenta WHERE username='$username' AND blog=0";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      post_mi_blog($username);
      $query = "UPDATE Cuenta SET blog=1 WHERE username='$username'";
      mysqli_query($db, $query);
      sleep (2);
      header('Location: http://blog'.$username.'.mmacademy.es/');
    }
  }
}
//Activar servicio Web
if (isset($_POST['activa_web'])){
    if (isset($_SESSION['username'])){
      $username = $_SESSION['username'];
      $query = "SELECT * FROM Cuenta WHERE username='$username' AND web=0";
      $results = mysqli_query($db, $query);
      if (mysqli_num_rows($results) == 1) {
        post_mi_web($username);
        $query = "UPDATE Cuenta SET web=1 WHERE username='$username'";
        mysqli_query($db, $query);
        sleep (2);
        header('Location: http://'.$username.'.mmacademy.es');
      }
  }
}
// Reenviar mail de confirmacion
if (isset($_POST['resend_mail_conf'])) {
  if (isset($_SESSION['username']) && isset($_SESSION['token'])){
    $username = $_SESSION['username'];
    $mail_token = $_SESSION['token'];
    $query = "SELECT * FROM Cuenta WHERE username='$username'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $line = mysqli_fetch_array($results);
      $email = $line['email'];
      $subject = 'M & M Confirmation Account';
      $body= "
      Please click on the link below to confirm your user:<br><br>
      
      <a href='http://mmacademy.es/confirm.php?token=$mail_token'> Click here </a> 
    
      ";
      if (enviar_mail($body, $subject, $email, $username)) {
        header('Location: registered.php?message=re_mail');
      } else {
        array_push($errors, "Failed sending verification email, try again later.");
      }
    }
  }
}
// Dar de alta tecnico
if (isset($_POST['up_cliente'])){
  $user = $_POST['up_clientes'];
    $attr["gidnumber"] = "501";
    $r_1 = ldap_modify($ds, "cn=$user,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $attr);
    if (! $r_1) {
      array_push($errors, "No ha sido posible");
    }
}
// Dar de alta admin
if (isset($_POST['up_tecnico'])){
  $user = $_POST['up_tecnicos'];
    $attr["gidnumber"] = "502";
    $r_1 = ldap_modify($ds, "cn=$user,cn=clientes,ou=grupos,dc=mmacademy,dc=es", $attr);
    if (! $r_1) {
      $r_2 = ldap_modify($ds, "cn=$user,cn=tecnicos,ou=grupos,dc=mmacademy,dc=es", $attr);
      if (! $r_2){
        array_push($errors, "No ha sido posible");
      }
    }
}
// CONFIRMAR USER
if (isset($_POST['conf_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
  $apellidos = mysqli_real_escape_string($db, $_POST['apellidos']);
  $direccion = mysqli_real_escape_string($db, $_POST['direccion']);

  $query = "SELECT * FROM Cuenta WHERE username='$username'";
  $results = mysqli_query($db, $query);
  if (mysqli_num_rows($results) == 1) {
    $email = mysqli_fetch_array($results)['email'];
  }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if (empty($nombre)) { array_push($errors, "Nombre is required"); }
  if (empty($apellidos)) { array_push($errors, "Apellidos is required"); }
  if (empty($direccion)) { array_push($errors, "Direccion is required"); }
  if ($password_1 !== $password_2) {
	  array_push($errors, "The two passwords do not match");
  }
  if (strlen($password_1) < 8){
    array_push($errors, "Contraseña demasiado corta (Mínimo 8 caracteres)");
  }
  if (!preg_match("#[0-9]+#", $password_1)){
    array_push($errors, "La contraseña debe contener al menos un número");
  }
  if (!preg_match("#[a-zA-Z]+#", $password_1)) {
    array_push($errors, "La contraseña debe contener al menos una letra");
  } 
  if (count($errors) == 0) {
    $dn = "cn=$username,cn=clientes,ou=grupos,dc=mmacademy,dc=es";
    $ldaprecord['cn'] = "$username";
    $ldaprecord['sn'] = "$username";
    $ldaprecord['givenName'] = "$nombre $apellidos";
    $ldaprecord['objectclass'][0] = "top";
    $ldaprecord['objectclass'][1] = "posixAccount";
    $ldaprecord['objectclass'][2] = "inetOrgPerson";
    $ldaprecord['uid'] = "$username";
    $ldaprecord['homedirectory'] = "/home/$username";
    $ldaprecord['userpassword'] = ssha($password_1);
    $ldaprecord['loginshell'] = '/bin/bash';
    $ldaprecord['uidnumber'] = findLargestUidNumber($ds) + 1;
    $ldaprecord['gidnumber'] = '500';
    $ldaprecord['mail'] = "$email";
    $ldaprecord['postalAddress'] = "$direccion";

    $r = ldap_add($ds, $dn, $ldaprecord);
    if ($r){
      $query = "DELETE FROM Mail_Conf WHERE username='$username'";
      mysqli_query($db, $query);
      $query = "UPDATE Cuenta SET Confirmado=1 WHERE username='$username'";
      mysqli_query($db, $query);
      //post_req($username, '500');
      header("location: http://mmacademy.es/login.php?message=reg_user");
    } else {
      array_push($errors, "Error creando su usuario, intentelo de nuevo más tarde.");
    }
  }
}

function enviar_mail($body, $subject, $email, $nombre){
  require '/usr/share/PHPMailer/vendor/autoload.php';

  $mail = new PHPMailer;
  $mail->SMTPOptions = array(
      'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      )
  );
  $mail->isSMTP();
  $mail->SMTPDebug = 0; // 0 for production
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 587;
  $mail->SMTPSecure = 'tls';
  $mail->SMTPAuth = true;
  $mail->Username = "manuel2019ASI@gmail.com";
  $mail->Password = "XXXXXXXXXXXXX";
  $mail->setFrom('manuel2019ASI@gmail.com', 'Manuel López');
  $mail->addAddress($email, $nombre);
  $mail->Subject = $subject;
  $mail->Body= $body;
  $mail->IsHTML(true); 
  return $mail->send();
}
function ssha($password) // SSHA with random 4-character salt
{
  $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',4)),0,4);
  return '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ). $salt);
}
function generate_random_string($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-{}.';
  $characters_length = strlen($characters);
  $random_string = '';
  for ($i = 0; $i < $length; $i++) {
      $random_string .= $characters[rand(0, $characters_length - 1)];
  }
  return $random_string;
}
function findLargestUidNumber($ds)
{
  $s = ldap_search($ds, "dc=mmacademy,dc=es", 'uidnumber=*');
  if ($s)
  {
    ldap_sort($ds, $s, "uidnumber");
    $result = ldap_get_entries($ds, $s);
    $count = $result['count'];
    $biguid = $result[$count-1]['uidnumber'][0];
    if ($biguid == 0) $biguid = 1000;
    return $biguid;
  }
  return null;
}
function post_req($parm1, $parm2){
  $data = array(
    'username' => $parm1,
    'grupo' => parm2
  );
  $url = 'http://localhost/cgi-bin/reg_user.cgi';
  $ch = curl_init($url);
  $postString = http_build_query($data, '', '&');
  # Setting our options
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  # Get the response
  $response = curl_exec($ch);
  echo $response;
  curl_close($ch);
}
function post_mi_web($username){
  $data = array(
    'username' => $username
  );
  $url = 'https://mmacademy.es/scripts/run_web.cgi';
  $ch = curl_init($url);
  $postString = http_build_query($data, '', '&');
  # Setting our options
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  # Get the response
  $response = curl_exec($ch);
  echo $response;
  curl_close($ch);
}
function post_mi_blog($username){
  $data = array(
    'username' => $username
  );
  $url = 'https://mmacademy.es/scripts/run_blog.cgi';
  $ch = curl_init($url);
  $postString = http_build_query($data, '', '&');
  # Setting our options
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  # Get the response
  $response = curl_exec($ch);
  echo $response;
  curl_close($ch);
}
function getUserIpAddr(){
  if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
      $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
function inc_ban($db){
  $ip = getenv("REMOTE_ADDR");
  $fecha_act = time();
  $query = "SELECT * FROM intento where ip='$ip'";
  $results = mysqli_query($db, $query);
  if (mysqli_num_rows($results) != 0) {
    $line = mysqli_fetch_array($results);
    $fecha = $line['fecha'];
    if ($fecha > $fecha_act - 60*10){
      if ($line['intentos'] >= 5){
        header("location: login.php?message=banned");exit();
      } else {
        $intentos = $line['intentos'] +1;
        $query = "UPDATE intento SET intentos=$intentos WHERE ip='$ip'";
        mysqli_query($db, $query);
      }
    } else {
      $intentos = 1;
      $fecha = time();
      $query = "UPDATE intento SET intentos=$intentos, fecha=$fecha WHERE ip='$ip'";
      mysqli_query($db, $query);
    }
  } else {
    $query = "INSERT INTO intento (intentos, fecha, ip) VALUES(1, $fecha_act, '$ip')";
    mysqli_query($db, $query);
  }
}

?>
