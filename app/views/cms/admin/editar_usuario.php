<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<div class="usuarios">
		<div class="container">
			<?php if ( isset($error) ) : ?>
				<div class="alert alert-danger"><?php echo $error; ?></div>
			<?php endif; ?>
			<?php echo form_open( 'cms/validar_form_usuario_editar', array('class' => 'form-horizontal', 'id' => 'form_usuario_nuevo', 'method' => 'POST', 'role' => 'form', 'autcomplete' => 'off' ) ); ?>
				<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Editar Usuario</h4></div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Datos del Usuario</div>
						<div class="panel-body">
							<input type="hidden" name="uuid_usuario" value="<?php echo isset($usuario_editar->uuid_usuario) && !empty($usuario_editar->uuid_usuario) ? $usuario_editar->uuid_usuario:''; ?>"/>
							<div class="form-group">
								<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($usuario_editar->nombre) && !empty($usuario_editar->nombre) ? $usuario_editar->nombre:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="apellidos" class="col-sm-3 col-md-2 control-label">Apellido (s)</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo isset($usuario_editar->apellidos) && !empty($usuario_editar->apellidos) ? $usuario_editar->apellidos:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="email" class="col-sm-3 col-md-2 control-label">Email</label>
								<div class="col-sm-9 col-md-10">
									<input type="email" class="form-control" id="email" name="email" value="<?php echo isset($usuario_editar->email) && !empty($usuario_editar->email) ? $usuario_editar->email:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="extension" class="col-sm-3 col-md-2 control-label">Extensión Telefónica</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="extension" name="extension" value="<?php echo isset($usuario_editar->extension) && !empty($usuario_editar->extension) ? $usuario_editar->extension:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="password" class="col-sm-3 col-md-2 control-label">Contraseña</label>
								<div class="col-sm-9 col-md-10">
									<input type="password" class="form-control" id="password" name="password" value="<?php echo isset($usuario_editar->password) && !empty($usuario_editar->password) ? $usuario_editar->password:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="password_2" class="col-sm-3 col-md-2 control-label">Confirmar Contraseña</label>
								<div class="col-sm-9 col-md-10">
									<input type="password" class="form-control" id="password_2" name="password_2" value="<?php echo isset($usuario_editar->password) && !empty($usuario_editar->password) ? $usuario_editar->password:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="celular" class="col-sm-3 col-md-2 control-label">Número Celular</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="celular" name="celular" value="<?php echo isset($usuario_editar->celular) && !empty($usuario_editar->celular) ? $usuario_editar->celular:''; ?>">
								</div>
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
														<input type="checkbox" name="categoria[]"
														<?php if( isset($ver_cat) && !empty($ver_cat) ): ?>
														<?php foreach($ver_cat as $check): ?>
														<?php if($categoria['uuid_categoria'] === $check['uuid_categoria']): ?>
															checked
														<?php endif; ?>
														<?php endforeach; ?>
														<?php endif; ?>
														 value="<?php echo $categoria['uuid_categoria']; ?>">
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
														<input type="checkbox" name="vertical[]" 
														<?php if( isset($ver_cat) && !empty($ver_cat) ): ?>
														<?php foreach($ver_cat as $check): ?>
														<?php if($vertical['uuid_vertical'] === $check['uuid_vertical']): ?>
															checked
														<?php endif; ?>
														<?php endforeach; ?> 
														<?php endif; ?>
														value="<?php echo $vertical['uuid_vertical']; ?>">
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
						<a href="<?php echo site_url("usuarios") ?>" type="button" class="btn btn-danger btn-block">Cancelar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Enviar"/>
					</div>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<footer>
		
	</footer>
</body>