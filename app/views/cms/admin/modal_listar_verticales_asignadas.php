<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h3 class="text-left">Verticales Asignadas</h3>
</div>
<div class="modal-body">
	<p>Estas son las <b>Verticales Asignadas</b> para este usuario</p>
	<ul>
		<?php if ( ! empty( $verticales ) ) : ?>
			<?php foreach ( $verticales as $vertical ) : ?>
				<li><?php echo $vertical->nombre; ?></li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
<div class="modal-footer">
	<button class="btn btn-default" data-dismiss="modal">Aceptar</button>
</div>