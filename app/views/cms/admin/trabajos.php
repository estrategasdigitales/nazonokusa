<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="trabajos">
		<div class="container">
			<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Administrar Trabajos</h4></div>
					<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nuevo_trabajo" type="button" class="btn btn-primary btn-block">Nuevo Trabajo</a></div>
			</div>
			<br>
			<div class="row">
				<div class="container table-responsive">
					<table class="table table-striped table-hover table-bordered">
						<tr class="titulo-columna">
							<td>Nombre del Trabajo</td>
							<td>URL origen</td>
							<td>Destino</td>
							<td>Tarea Programada</td>
							<td>Ejecutar</td>
                            
                            <?php  if(isset($level) && $level == 1){ ?>
                            <td>Editar</td>
							<td>Eliminar</td>
                            <?php  } ?>

						</tr>
						<?php if ( $trabajos ): ?>
							<?php foreach( $trabajos as $trabajo ): ?>
								<tr>
									<td><?php echo $trabajo['nombre']; ?></td>
									<td><?php echo $trabajo['url_origen']; ?></td>
									<td><?php echo $trabajo['url_storage']; ?></td>
									<td>
										<?php if( !empty( $trabajo['fecha_ejecucion'] ) ){ ?>
											<div class="checkbox">
												<label>
													<input type="checkbox" checked> programada
												</label>
											</div>
										<?php }else{ ?>
											<div class="checkbox">
												<label>
													<input type="checkbox"> programada
												</label>
											</div>
										<?php } ?>
									</td>
									<td><a href="<?php base_url(); ?>ejecutar_trabajo/<?php echo $trabajo['uuid_trabajo'] ?>" type="button" class="btn btn-warning btn-sm btn-block">Ejecutar Ahora</a></td>
                                    
                                    <?php  if(isset($level) && $level == 1){ ?>
                                       <td><a href="<?php base_url(); ?>editar_trabajo/<?php echo $trabajo['uuid_trabajo'] ?>" type="button" class="btn btn-warning btn-sm btn-block">Editar</a></td>
							           <td><a href="javascript:ShowDialogT('<?php base_url(); ?>eliminar_trabajo/<?php echo $trabajo['uuid_trabajo'] ?>','<?php echo $trabajo['nombre']; ?>');" type="button" class="btn btn-danger btn-sm btn-block">Eliminar</a></td>
                                    <?php  } ?>

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
		<div id="dialogConfirm"><span id="spanMessage"></span>
	</nav>
	<footer>
		
	</footer>
</body>