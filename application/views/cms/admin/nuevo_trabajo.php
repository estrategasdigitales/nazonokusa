<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="trabajos">
		<div class="container">
			<?php echo form_open('cms/validar_form_trabajo',array('class' => 'form-horizontal', 'id' => 'form_trabajo_nuevo', 'role' => 'form')); ?>
				<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Nuevo Trabajo</h4></div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Datos del Trabajo</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nombre" name="nombre">
								</div>
							</div>
							<div class="form-group">
								<label for="url-origen" class="col-sm-3 col-md-2 control-label">URL de origen</label>
								<div class="col-sm-9 col-md-10">
									<input type="url" class="form-control" id="url-origen" name="url-origen">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-local" class="col-sm-3 col-md-2 control-label">Destino local</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="destino-local" name="destino-local">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-net" class="col-sm-3 col-md-2 control-label">Destino net-storage</label>
								<div class="col-sm-9 col-md-10">
									<input type="tel" class="form-control" id="destino-net" name="destino-net">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-md-2 control-label">Categoría</label>
								<?php if( isset($categorias) && !empty($categorias) ): ?>
									<div class="col-sm-9 col-md-10">
										<select class="form-control">							
											<?php foreach($categorias as $categoria): ?>
												<option value="<?php echo $categoria['uuid_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php else: ?>
									<div class="col-sm-9 col-md-10">
										<h5 class="form-control">Este usuario no tiene asignada ninguna categoría</h5>
									</div>
								<?php endif; ?>								
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-md-2 control-label">Vertical</label>
								<?php if( isset($verticales) && !empty($verticales) ): ?>
									<div class="col-sm-9 col-md-10">
										<select class="form-control">							
											<?php foreach($verticales as $vertical): ?>
												<option value="<?php echo $vertical['uuid_vertical']; ?>"><?php echo $vertical['nombre']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php else: ?>
									<div class="col-sm-9 col-md-10">
										<h5 class="form-control">Este usuario no tiene asignada ninguna vertical</h5>
									</div>
								<?php endif; ?>	
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Categorias y Verticales de Contenido que Puede Editar</div>
						<div class="panel-body">
							<div class="col-sm-6 col-md-6">							
								<div class="form-group">
									<?php if( isset($categorias) && !empty($categorias) ): ?>
										<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
											<h5>Categorías:</h5>
										</div>
										<br>
										<br>									
										<?php foreach($categorias as $categoria): ?>
											<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
												<div class="checkbox">
													<label>
														<input type="checkbox" name="categoria[]" value="<?php echo $categoria['uuid_categoria']; ?>">
														<?php echo $categoria['nombre']; ?>
													</label>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else: ?>
										<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
											<h5>No Existen Categorías Aún</h5>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
								<div class="form-group">
									<?php if( isset($verticales) && !empty($verticales) ): ?>
										<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
											<h5>Verticales:</h5>
										</div>
										<br>
										<br>									
										<?php foreach($verticales as $vertical): ?>
											<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
												<div class="checkbox">
													<label>
														<input type="checkbox" name="vertical[]" value="<?php echo $vertical['uuid_vertical']; ?>">
														<?php echo $vertical['nombre']; ?>
													</label>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else: ?>
										<div class="col-sm-offset-1 col-md-offset-1 col-sm-11 col-md-11">
											<h5>No Existen Verticales Aún</h5>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-md-4"></div>
					<div class="col-sm-4 col-md-4">
						<a href="<?php base_url(); ?>usuarios" type="button" class="btn btn-danger btn-block">Cancelar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Enviar"/>
					</div>
				</div>
			<?php echo form_close(); ?>
			<?php if ( isset($error) ) : ?>
				<div class="alert alert-danger"><?php echo $error; ?></div>
			<?php endif; ?>
		</div>
	</nav>
	<footer>
		
	</footer>
</body>