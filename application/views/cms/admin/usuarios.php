<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="usuarios">
		<div class="container">
			<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Administrar Usuarios</h4></div>
					<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nuevo_usuario" type="button" class="btn btn-primary btn-block">Nuevo Usuario</a></div>
			</div>
			<br>
			<div class="row">
				<div class="container table-responsive">
					<table class="table table-striped table-hover table-bordered">
						<tr class="titulo-columna">
							<td>Nombre de Usuario</td>
							<td>Perfil</td>
							<td>Editar Información</td>
							<td>Eliminar</td>
						</tr>
						<?php if ( $usuarios ): ?>
							<?php foreach( $usuarios as $usuario ): ?>
								<tr>
									<td><?php echo $usuario['nombre']; ?></td>
									<td><?php echo ($usuario['nivel'] === '2') ? 'Editor':''; ?></td>
									<td><a href="#" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
									<td><a href="#" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</table>
				</div>
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