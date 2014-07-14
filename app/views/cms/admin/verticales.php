<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar verticales</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nueva_vertical" type="button" class="btn btn-primary btn-block">Nueva Vertical</a></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="table-responsive">
				<table class="table table-striped table-hover table-bordered">
					<tr class="titulo-columna">
						<td>Nombre de la Vertical</td>
						<td>Path</td>
						<td>Fecha de creación</td>
						<td>Eliminar</td>
					</tr>
					<?php if ( isset($verticales) && !empty($verticales) ): ?>
						<?php foreach( $verticales as $vertical ): ?>
							<tr>
								<td><?php echo $vertical->nombre; ?></td>
								<td><?php echo $vertical->path_storage; ?></td>
								<td><?php echo unix_to_human( $vertical->fecha_registro ); ?></td>
								<td><a href="javascript:ShowDialog3('<?php echo base_url(); ?>eliminar_vertical/<?php //echo $vertical['uid_vertical'] ?>','<?php //echo $vertical['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="4">No existen verticales para mostrar</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-8 col-md-8"></div>
		<div class="col-sm-4 col-md-4">
			<a href="<?php base_url(); ?>index" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div id="dialogConfirm"><span id="spanMessage"></span></div>
<?php $this->load->view( 'cms/footer' ); ?>