<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar verticales</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nueva_vertical" type="button" class="btn btn-primary btn-block">Nueva Vertical</a></div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-12 col-md-12">
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
								<td><?php echo '/'.$vertical->slug_vertical; ?></td>
								<td><?php echo unix_to_human( $vertical->fecha_registro ); ?></td>
								<td><a href="<?php echo base_url(); ?>eliminar_vertical?name=<?php echo base64_encode( $vertical->nombre ); ?>&token=<?php echo base64_encode( $vertical->uid_vertical ); ?>" type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
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
	</div>
	<br>
	<div class="row">
		<div class="col-sm-8 col-md-8"></div>
		<div class="col-sm-4 col-md-4">
			<a href="<?php base_url(); ?>inicio" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
<?php $this->load->view( 'cms/footer' ); ?>