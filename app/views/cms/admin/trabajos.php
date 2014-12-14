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
					<td>Nombre del Trabajo</td>
					<td>URL origen</td>
					<td >Salidas</td>
					<td class="text-center" width="10%">Activar / Desactivar</td>
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
							<td class="text-center">
								<a href="<?php echo $trabajo->url_origen; ?>" class="petroleo" target="_blank">
									<span class="glyphicon glyphicon-link"></span>
									<span class="glyphicon-class">url</span>
								</a>
							</td>
							<td class="text-center">
								<?php
									if ( $trabajo->activo == 1 ){
										switch ( $trabajo->tipo_salida ){
											case 1:
												$formatos = $this->cms->get_trabajos_formatos( $trabajo->uid_trabajo );
												foreach ( $formatos as $salida ){
													$salida = json_decode( $salida->formato );
													echo '<span>';
															switch ( $salida->format ) {
																case 'json':
																	echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-json.js" title="' . $salida->format .'" class="petroleo" target="_blank">';
																break;
																case 'jsonp':
																	echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-jsonp.js" title="' . $salida->format .'" class="petroleo" target="_blank">';
																break;
																case 'xml':
																	echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-xml.xml" title="' . $salida->format . '" class="petroleo" target="_blank">';
																break;
																case 'rss':
																	echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-rss.xml" title="' . $salida->format . '" class="petroleo" target="_blank">';
																break;
															}
															echo '<span class="glyphicon glyphicon-link"></span>';
															echo '<span class="glyphicon-class">' . $salida->format . '</span>';
														echo '</a> ';
													echo '</span>';
												}
											break;
											case 2:
												echo '<span>';
													switch ( $trabajo->formato_salida ) {
														case 'RSS':
															echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-rss.xml" class="petroleo" target="_blank">';
																echo '<span class="glyphicon glyphicon-link"></span>';
																echo '<span class="glyphicon-class">rss</span>';
															echo '</a>';
														break;
														case 'XML':
															echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-xml.xml" class="petroleo" target="_blank">';
																echo '<span class="glyphicon glyphicon-link"></span>';
																echo '<span class="glyphicon-class">xml</span>';
															echo '</a>';
														break;
														case 'JSON':
															echo '<a href="' . $_SERVER['AWS_FEEDS_URL'] . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/' . $trabajo->slug_nombre_feed . '-json.js" class="petroleo" target="_blank">';
																echo '<span class="glyphicon glyphicon-link"></span>';
																echo '<span class="glyphicon-class">json</span>';
															echo '</a>';
														break;
													}
												echo '</span>';
											break;
										}
									}
								?>
							</td>
							<td class="text-center">
								<?php if ( $trabajo->activo == 0 ){ ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 1, '<?php echo base_url(); ?>nucleo/job_process');" type="button" class="btn btn-danger btn-sm btn-block btn-padding">Activar</a>
								<?php } else { ?>
									<a href="javascript:activarTrabajo('<?php echo base64_encode( $trabajo->uid_trabajo ); ?>', 0, '<?php echo base_url(); ?>nucleo/job_process');" type="button" class="btn btn-success btn-sm btn-block btn-padding">Desactivar</a>
								<?php } ?>
							</td>
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
        <div class="col-xs-12">
            <div id="paginacion">
            	<?php echo $links; ?>
            </div>
        </div>
    </div>
    <br>
	<div class="row">
		<div class="col-sm-8 col-md-8"></div>
		<div class="col-sm-4 col-md-4">
			<a href="<?php echo base_url(); ?>inicio" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<?php $this->load->view('cms/modals'); ?>
<?php $this->load->view( 'cms/footer' ); ?>