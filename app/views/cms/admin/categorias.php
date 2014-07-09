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
								<td>categoria-demo/algo-mas/algo-mas/</td>
								<td><a href="<?php echo base_url(); ?>editar_categoria?<?php //echo $categoria; ?>" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
								<!--<td><a href="javascript:ShowDialog2('<?php echo base_url(); ?>eliminar_categoria/<?php //echo $categoria['uid_categoria'] ?>','<?php //echo $categoria['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>-->
								<td><a href="javascript:eliminarCategoria('<?php echo $categoria->uid_categoria; ?>');" type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#myModal">Eliminar</a></td>
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
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Eliminar Categoría</h4>
				</div>
				<div class="modal-body">
					<p>¿Estás seguro que deseas eliminar esta categoría?</p>
					<p>Al eliminar la categoría, también se eliminarán los archivos de salida ligados a la misma</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-danger">Estoy seguro</button>
				</div>
			</div>
		</div>
	</div>
	<div id="dialogConfirm"><span id="spanMessage"></span></div>
<?php $this->load->view( 'cms/footer' ); ?>