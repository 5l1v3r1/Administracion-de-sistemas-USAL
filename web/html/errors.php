<?php  if (count($errors) > 0) : ?>
	<nav class="dav">
		<div class="error">
			<?php foreach ($errors as $error) : ?>
				<p><?php echo $error ?></p>
			<?php endforeach ?>
		</div>
	</nav>
<?php  endif ?>
