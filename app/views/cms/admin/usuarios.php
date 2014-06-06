<?php $this->load->view( 'cms/header' ); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar Usuarios</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nuevo_usuario" type="button" class="btn btn-primary btn-block">Nuevo Usuario</a></div>
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
				<?php if ( isset($usuarios) && !empty($usuarios) ): ?>
					<?php foreach( $usuarios as $usuario ): ?>
						<tr>
							<td><?php echo $usuario->nombre . ' ' . $usuario->apellidos; ?></td>
							<td>
								<?php
									switch ($usuario->nivel) {
										case 1:
											echo 'Super administrador';
											break;
										case 2:
											echo 'Administrador';
											break;
										case 3:
											echo 'Editor';
											break;
									}
								?>
							</td>
							<td><a href="<?php echo base_url(); ?>editar/<?php echo $usuario->uid_usuario; ?>" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
							<td><a href="javascript:ShowDialog('<?php echo base_url(); ?>eliminar/<?php echo $usuario->uid_usuario; ?>','<?php echo $usuario->nombre; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
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
			<a href="<?php echo base_url(); ?>index" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div id="dialogConfirm"><span id="spanMessage"></span></div>
<?php $this->load->view( 'cms/footer' ); ?>