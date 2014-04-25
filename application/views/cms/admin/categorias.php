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
							<?php if ( $categorias ): ?>
								<?php foreach( $categorias as $categoria ): ?>
									<tr>
										<td><?php echo $categoria['nombre']; ?></td>
										<td><a href="#" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
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
	</nav>
	<footer>
		
	</footer>
</body>