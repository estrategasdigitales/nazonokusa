<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h3 class="text-left">Categorías Asignadas</h3>
</div>
<div class="modal-body">
	<p>Estas son las <b>Categorías Asignadas</b> para este usuario</p>
	<ul>
		<?php if ( ! empty( $categorias ) ) : ?>
			<?php foreach ( $categorias as $categoria ) : ?>
				<li><?php echo $categoria->nombre; ?></li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
<div class="modal-footer">
	<button class="btn btn-default" data-dismiss="modal">Aceptar</button>
</div>