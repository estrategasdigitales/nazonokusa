<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view( 'cms/header' ); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Administrar Trabajos</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php echo base_url(); ?>nuevo_trabajo" type="button" class="btn btn-primary btn-block">Nuevo Trabajo</a></div>
	</div>
	<br>
	<div class="row">
		<div class="container table-responsive">
			<table class="table table-striped table-hover table-bordered">
				<tr class="titulo-columna">
					<td width="35%">Nombre del Trabajo</td>
					<!--<td width="30%">URL origen</td>-->
					<td width="25%">Salidas</td>
					<td>Fecha de alta</td>
					<td class="text-center" width="10%">Activar / Desactivar</td>
					<!--<td class="text-center" width="10%">Ejecutar</td>-->
                    <?php  if ( $this->session->userdata( 'nivel' ) >= 1 && $this->session->userdata( 'nivel' ) <= 2 ){ ?>
	                    <!-- <td class="text-center" width="10%">Editar</td> -->
	                <?php } if ( $this->session->userdata( 'nivel' ) == 1 ){ ?>
						<td class="text-center" width="10%">Eliminar</td>
                    <?php  } ?>
				</tr>
				<?php if ( $trabajos ): ?>
					<?php foreach( $trabajos as $trabajo ): ?>
						<tr>
							<td><?php echo $trabajo->nombre; ?></td>
							<!--<td><?php echo $trabajo->url_origen; ?></td>-->
							<td class="text-center">
								<?php
									if ( $trabajo->activo == 1 ){
										$salidas = json_decode( $trabajo->formatos );
										foreach ( $salidas as $salida ){
								?>
								
									<span>
										<?php
											switch ($salida->formato) {
												case 'json': ?>
													<a href="http://<?php echo $_SERVER['STORAGE_URL'] . '/' . $trabajo->slug_categoria .'/'.$trabajo->slug_vertical.'/'.$this->session->userdata('uid').'/'.$trabajo->slug_nombre_feed.'-json.js'; ?>" title="<?php echo $salida->formato; ?>" class="petroleo" target="_blank">
										<?php 	break;
												case 'jsonp': ?>
													<a href="http://<?php echo $_SERVER['STORAGE_URL'] . '/' . $trabajo->slug_categoria .'/'.$trabajo->slug_vertical.'/'.$this->session->userdata('uid').'/'.$trabajo->slug_nombre_feed.'-jsonp.js'; ?>" title="<?php echo $salida->formato; ?>" class="petroleo" target="_blank">
										<?php 	break;
												case 'xml': ?>
													<a href="http://<?php echo $_SERVER['STORAGE_URL'] . '/' . $trabajo->slug_categoria .'/'.$trabajo->slug_vertical.'/'.$this->session->userdata('uid').'/'.$trabajo->slug_nombre_feed.'-xml.xml'; ?>" title="<?php echo $salida->formato; ?>" class="petroleo" target="_blank">
										<?php 	break;
												case 'rss': ?>
													<a href="http://<?php echo $_SERVER['STORAGE_URL'] . '/' . $trabajo->slug_categoria .'/'.$trabajo->slug_vertical.'/'.$this->session->userdata('uid').'/'.$trabajo->slug_nombre_feed.'-rss.xml'; ?>" title="<?php echo $salida->formato; ?>" class="petroleo" target="_blank">
										<?php 	break;
											
											}
										?>
											<span class="glyphicon glyphicon-link"></span>
											<span class="glyphicon-class"><?php echo $salida->formato; ?></span>
										</a>
									</span>
								<?php } } ?>
							</td>
							<td class="text-center"><?php echo unix_to_human($trabajo->fecha_registro); ?></td>
							<td class="text-center">
								<?php if ( $trabajo->activo == 0 ){ ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 1);" type="button" class="btn btn-danger btn-sm btn-block btn-padding">Activar</a>
								<?php } else { ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 0);" type="button" class="btn btn-success btn-sm btn-block btn-padding">Desactivar</a>
								<?php } ?>
							</td>
							<!--<td><a href="<?php echo base_url(); ?>ejecutar_trabajo/<?php echo $trabajo->uid_trabajo ?>" type="button" class="btn btn-warning btn-sm btn-block btn-padding">Ejecutar Ahora</a></td>-->
                            <?php  if ( $this->session->userdata( 'nivel' ) >= 1 && $this->session->userdata( 'nivel' ) <= 2 ){ ?>
                               <!--<td><a href="<?php echo base_url(); ?>editar_trabajo/<?php echo $trabajo->uid_trabajo; ?>" type="button" class="btn btn-warning btn-sm btn-block btn-padding">Editar</a></td>-->
                               <!-- <td><a href="#" type="button" class="btn btn-warning btn-sm btn-block btn-padding">Editar</a></td> -->
                            <?php } if ( $this->session->userdata( 'nivel' ) == 1 ){ ?>
								<td><a href="<?php echo base_url(); ?>eliminar_trabajo?name=<?php echo base64_encode( $trabajo->nombre ); ?>&token=<?php echo base64_encode( $trabajo->uid_trabajo ); ?>" type="button" class="btn btn-danger btn-sm btn-block btn-padding" data-toggle="modal" data-target="#modalMessage">Eliminar</a></td>
                            <?php  } ?>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<?php if ( $this->session->userdata( 'nivel' ) >= 1 && $this->session->userdata( 'nivel' ) <= 2 ){ ?>
							<td colspan="7">No existen trabajos para mostrar</td>
						<?php } else { ?>
							<td colspan="5">No existen trabajos para mostrar</td>
						<?php } ?>
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