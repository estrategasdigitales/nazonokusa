<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
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
					<td width="35%">Nombre de Usuario</td>
					<td>Perfil</td>
					<td class="text-center">Categorías asignadas</td>
					<td class="text-center">Verticales asignadas</td>
					<td class="text-center" width="15%">Editar Información</td>
					<td class="text-center" width="15%">Eliminar</td>
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
							<td class="text-center"><a href="#" class="petroleo glyphicon glyphicon-list-alt"></a></td>
							<td class="text-center"><a href="#" class="petroleo glyphicon glyphicon-list-alt"></a></td>
							<td><a href="<?php echo base_url(); ?>editar/<?php echo base64_encode( $usuario->uid_usuario ); ?>" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
							<td><a href="<?php echo base_url(); ?>eliminar_usuario?name=<?php echo base64_encode( $usuario->nombre. ' ' . $usuario->apellidos ); ?>&token=<?php echo base64_encode( $usuario->uid_usuario ); ?>" class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="6">No existen usuarios para mostrar</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-8 col-md-8"></div>
		<div class="col-sm-4 col-md-4">
			<a href="<?php echo base_url(); ?>inicio" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
<?php $this->load->view( 'cms/footer' ); ?>