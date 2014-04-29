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
									<td><a href="#" type="button" class="btn btn-warning btn-sm btn-block">Ejecutar Ahora</a></td>
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