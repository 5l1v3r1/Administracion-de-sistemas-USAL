<?php include('header.php'); 
  if(!empty($_GET['message'])) {
    if ($_GET['message'] == 'del_user'){
      echo '<div class="success">
        <p>Se ha dado de baja con éxito.</p>
      </div>';
    }
  }
?>

    <div class="description">
      <h2>Academia Online M &amp; M</h2>
      <p>Para todos las universidades o cursos a distancia que quieren mejorar la calidad de sus clases, la Academia Online M & M permite que los alumnos disfruten de la experiencia de asistir a clase sin moverse de casa. Somos Manuel López y Marcos Severt alumnos de la universidad a distancia. Aprender sin ir a clase y tener un profesor que resuelva dudas suele ser tedioso; Vitual Academic es una aplicación que permitirá a los alumnos aprender mucho más comodamente en cualquier momento.</p>
      <img class="quien_icon" src="images/back_1.jpg">
    </div>

<?php include('footer.php'); ?>
