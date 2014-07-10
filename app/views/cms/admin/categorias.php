<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view( 'cms/header' ); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar Categorías</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nueva_categoria" type="button" class="btn btn-primary btn-block">Nueva Categoría</a></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="table-responsive">
				<table class="table table-striped table-hover table-bordered">
					<tr class="titulo-columna">
						<td>Nombre del Categoría</td>
						<td>Path</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</tr>
					<?php if ( isset($categorias) && !empty($categorias) ): ?>
						<?php foreach( $categorias as $categoria ): ?>
							<tr>
								<td><?php echo $categoria->nombre; ?></td>
								<td widht="45%">categoria-demo/algo-mas/algo-mas/</td>
								<td widht="15%"><a href="<?php echo base_url(); ?>editar_categoria/<?php echo base64_encode( $categoria->uid_categoria ); ?>" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
								<td><a href="<?php echo base_url(); ?>eliminar_categoria?name=<?php echo base64_encode( $categoria->nombre ); ?>&token=<?php echo base64_encode( $categoria->uid_categoria ); ?>" type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
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
			<a href="<?php echo base_url(); ?>index" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
<?php $this->load->view( 'cms/footer' ); ?>