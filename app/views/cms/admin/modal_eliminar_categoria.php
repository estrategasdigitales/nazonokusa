<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>js/middleware.js"></script>
<?php echo form_open('eliminar_categoria', array('class' => 'form-horizontal', 'id' => 'form_eliminar_categoria', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3 class="text-left">Eliminar Categoría</h3>
	</div>
	<div class="modal-body">
		<p>¿Estás seguro de que deseas eliminar la categoría <b><?php echo base64_decode( $nombre_categoria ); ?></b>?</p>
		<p>Este proceso es completamente irreversible, se eliminarán también todas las verticales contenidas en esta categoría, así como los archivo de salida.</p>
		<div class="alert" id="messagesModal"></div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-danger" id="deleteUserSubmit">Aceptar</button>
		<button class="btn btn-default" data-dismiss="modal">Cancelar</button>
	</div>
	<input type="hidden" id="token" name="token" value="<?php echo $uid; ?>">
<?php echo form_close(); ?>