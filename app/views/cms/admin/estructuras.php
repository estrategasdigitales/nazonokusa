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
	                <td class="text-center" width="10%">Editar</td>
					<td class="text-center" width="10%">Eliminar</td>
				</tr>
<!-- 				<?php if ( $trabajos ): ?>
					<?php foreach( $trabajos as $trabajo ): ?>
						<tr>
							<td><?php echo $trabajo->nombre; ?></td>
							<td class="text-center">
								<?php
									if ( $trabajo->activo == 1 ){
										$salidas = json_decode( $trabajo->feeds_output );
										foreach ( $salidas as $salida ){
								?>
									<span>
										<a href="<?php echo base_url().$salida->url; ?>" title="<?php echo $salida->formato; ?>" class="petroleo" target="_blank">
											<span class="glyphicon glyphicon-link"></span>
											<span class="glyphicon-class"><?php echo $salida->formato; ?></span>
										</a>
									</span>
								<?php } } ?>
							</td>
							<td class="text-center">
								<?php if ( $trabajo->activo == 0 ){ ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 1);" type="button" class="btn btn-danger btn-sm btn-block btn-padding">Activar</a>
								<?php } else { ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 0);" type="button" class="btn btn-success btn-sm btn-block btn-padding">Desactivar</a>
								<?php } ?>
							</td>
                            <td><a href="#" type="button" class="btn btn-warning btn-sm btn-block btn-padding">Editar</a></td>
							<td><a href="<?php echo base_url(); ?>eliminar_trabajo?name=<?php echo base64_encode( $trabajo->nombre ); ?>&token=<?php echo base64_encode( $trabajo->uid_trabajo ); ?>" type="button" class="btn btn-danger btn-sm btn-block btn-padding" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
						</tr>
					<?php endforeach; ?>
				<!-- <?php else : ?> -->
					<tr>
						<td colspan="7">No existen estructuras para mostrar</td>
					</tr>
				<!-- <?php endif; ?> -->
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
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
<?php $this->load->view( 'cms/footer' ); ?>