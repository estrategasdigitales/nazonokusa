<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>js/middleware.js"></script>
<?php echo form_open('cms/eliminar_estructura', array('class' => 'form-horizontal', 'id' => 'form_eliminar_estructura', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3 class="text-left">Eliminar Estructura</h3>
	</div>
	<div class="modal-body">
		<p>¿Estás seguro de que deseas eliminar la estructura <b><?php echo base64_decode( $nombre_estructura ); ?></b>?</p>
		<p>Este proceso es completamente irreversible, y afectará a todas las tareas que la tengan asignadas.</p>
		<div class="alert" id="messagesModal"></div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-danger" id="deleteStructureSubmit">Aceptar</button>
		<button class="btn btn-default" data-dismiss="modal">Cancelar</button>
	</div>
	<input type="hidden" id="token" name="token" value="<?php echo $uid; ?>">
<?php echo form_close(); ?>