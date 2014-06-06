<?php $this->load->view('cms/header'); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar verticales</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nueva_vertical" type="button" class="btn btn-primary btn-block">Nueva Vertical</a></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="table-responsive">
				<table class="table table-striped table-hover table-bordered">
					<tr class="titulo-columna">
						<td>Nombre de la Vertical</td>
						<td>Eliminar</td>
					</tr>
					<?php if ( isset($verticales) && !empty($verticales) ): ?>
						<?php foreach( $verticales as $vertical ): ?>
							<tr>
								<td><?php echo $vertical['nombre']; ?></td>
								<td><a href="javascript:ShowDialog3('<?php base_url(); ?>eliminar_vertical/<?php echo $vertical['uuid_vertical'] ?>','<?php echo $vertical['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
							</tr>
						<?php endforeach; ?>
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