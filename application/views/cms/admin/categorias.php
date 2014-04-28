<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="categorias">
		<div class="container">
			<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Administrar Categorias</h4></div>
					<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nueva_categoria" type="button" class="btn btn-primary btn-block">Nueva Categoria</a></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="table-responsive">
						<table class="table table-striped table-hover table-bordered">
							<tr class="titulo-columna">
								<td>Nombre del Categoria</td>
								<td>Eliminar</td>
							</tr>
							<?php if ( isset($categorias) && !empty($categorias) ): ?>
								<?php foreach( $categorias as $categoria ): ?>
									<tr>
										<td><?php echo $categoria['nombre']; ?></td>
										<td><a href="javascript:ShowDialog2('<?php base_url(); ?>eliminar_categoria/<?php echo $categoria['uuid_categoria'] ?>','<?php echo $categoria['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</table>
					</div>
				</div>
				<div class="col-md-3"></div>
			</div>
			<br>
			<?php if ( isset($error) && !empty($error) ) : ?>
			<div class="row">
				<div class="container">
					<div class="alert alert-danger"><?php echo $error; ?></div>
				</div>
			</div>
			<br>
			<?php endif; ?>
			<div class="row">
				<div class="col-sm-8 col-md-8"></div>
				<div class="col-sm-4 col-md-4">
					<a href="<?php base_url(); ?>index" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
				</div>
			</div>
		</div>
		<div id="dialogConfirm"><span id="spanMessage"></span>
	</nav>
	<footer>
		
	</footer>
</body>