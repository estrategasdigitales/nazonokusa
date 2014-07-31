<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view( 'cms/header' ); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar Estructuras de Salida</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nueva_estructura" type="button" class="btn btn-primary btn-block">Nueva Estructura</a></div>
	</div>
	<br>
	<div class="row">
		<div class="container table-responsive">
			<table class="table table-striped table-hover table-bordered">
				<tr class="titulo-columna">
					<td>Nombre de la Estructura</td>
					<td>Tipo</td>
					<td class="text-center" width="10%">Activar / Desactivar</td>
	                <!--<td class="text-center" width="10%">Editar</td>-->
					<td class="text-center" width="10%">Eliminar</td>
				</tr>
 				<?php if ( $estructuras ): ?>
					<?php foreach( $estructuras as $estructura ): ?>
						<tr>
							<td><?php echo $estructura->nombre; ?></td>
							<td class="text-center">
								<?php
									switch ( $estructura->formato_salida ) {
										case '1':
											echo 'RSS';
											break;
										case '2':
											echo 'XML';
											break;
									}
								?>
							</td>
							<td class="text-center">
								<?php if ( $estructura->activo == 0 ){ ?>
									Desactivada
								<?php } else { ?>
									Activada
								<?php } ?>
							</td>
                            <!--<td><a href="#" type="button" class="btn btn-warning btn-sm btn-block btn-padding">Editar</a></td>-->
							<td><a href="<?php echo base_url(); ?>eliminar_estructura?name=<?php echo base64_encode( $estructura->nombre ); ?>&token=<?php echo base64_encode( $estructura->uid_estructura ); ?>" type="button" class="btn btn-danger btn-sm btn-block btn-padding" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="7">No existen estructuras para mostrar</td>
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