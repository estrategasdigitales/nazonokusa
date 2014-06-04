<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<div class="categorias">
		<div class="container">
			<?php if ( isset($error) && !empty($error) ) : ?>
				<div class="alert alert-danger"><?php echo $error; ?></div>
			<?php endif; ?>
			<div class="row">
				<div class="col-sm-8 col-md-8"><h4>Administrar Categorías</h4></div>
				<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nueva_categoria" type="button" class="btn btn-primary btn-block">Nueva Categoría</a></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="table-responsive">
						<table class="table table-striped table-hover table-bordered">
							<tr class="titulo-columna">
								<td>Nombre del Categoría</td>
								<td>Eliminar</td>
							</tr>
							<?php if ( isset($categorias) && !empty($categorias) ): ?>
								<?php foreach( $categorias as $categoria ): ?>
									<tr>
										<td><?php echo $categoria['nombre']; ?></td>
										<td><a href="javascript:ShowDialog2('<?php echo base_url(); ?>eliminar_categoria/<?php echo $categoria['uuid_categoria'] ?>','<?php echo $categoria['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
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
		</div>
		<div id="dialogConfirm"><span id="spanMessage"></span>
	</div>
	<footer></footer>
</body>