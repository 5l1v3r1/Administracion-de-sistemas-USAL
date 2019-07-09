<?php 
  include('server.php');
  include('header.php'); 
  if(!empty($_GET['message'])) {
    if ($_GET['message'] == 'msg_success'){
      echo '<div class="success">
        <p>Mensaje enviado.</p>
      </div>';
    }
  }

?>

  <form method="post" action="contact.php">
    <?php include('errors.php'); ?>
    <nav class="register">
      <h2>Contacto</h2>
      <div>
        <input class="register_fields" type="text" name="nombre" placeholder="Nombre" required>
      </div>
      <div>
        <input class="register_fields" type="email" name="email" placeholder="Email" required>
      </div>
      <div>
        <input class="register_fields" type="text" name="tema" placeholder="Tema" required>
      </div>
      <div>
        <textarea class="contact_msg" type="text" name="msg" placeholder="Mensaje" required></textarea>
      </div>
      <button class="register_but" type="submit" name="contact">Enviar</button>
    </nav>
  </form>
  <?php include('footer.php'); ?>

