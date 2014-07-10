<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'cms/validar_form_usuario_editar', array('class' => 'form-horizontal', 'id' => 'form_usuario_nuevo', 'method' => 'POST', 'role' => 'form', 'autcomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Editar Usuario</h4></div>
		</div>
		<br>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Datos del Usuario</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
						<div class="col-sm-9 col-md-10">
							<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($usuario_editar->nombre) && !empty($usuario_editar->nombre) ? $usuario_editar->nombre:''; ?>" placeholder="Nombre">
						</div>
					</div>
					<div class="form-group">
						<label for="apellidos" class="col-sm-3 col-md-2 control-label">Apellido (s)</label>
						<div class="col-sm-9 col-md-10">
							<input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo isset($usuario_editar->apellidos) && !empty($usuario_editar->apellidos) ? $usuario_editar->apellidos:''; ?>" placeholder="Apellido (s)">
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-3 col-md-2 control-label">Email</label>
						<div class="col-sm-9 col-md-10">
							<input type="email" class="form-control" id="email" name="email" value="<?php echo isset($usuario_editar->email) && !empty($usuario_editar->email) ? $usuario_editar->email:''; ?>" placeholder="Email">
						</div>
					</div>
					<div class="form-group">
						<label for="extension" class="col-sm-3 col-md-2 control-label">Extensión Telefónica</label>
						<div class="col-sm-9 col-md-10">
							<input type="text" class="form-control" id="extension" name="extension" value="<?php echo isset($usuario_editar->extension) && !empty($usuario_editar->extension) ? $usuario_editar->extension:''; ?>" placeholder="Extension">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 col-md-2 control-label">Contraseña</label>
						<div class="col-sm-9 col-md-10">
							<input type="password" class="form-control" id="password" name="password" value="<?php echo isset($usuario_editar->password) && !empty($usuario_editar->password) ? $usuario_editar->password:''; ?>" placeholder="Contraseña">
						</div>
					</div>
					<div class="form-group">
						<label for="password_2" class="col-sm-3 col-md-2 control-label">Confirmar Contraseña</label>
						<div class="col-sm-9 col-md-10">
							<input type="password" class="form-control" id="password_2" name="password_2" value="<?php echo isset($usuario_editar->password) && !empty($usuario_editar->password) ? $usuario_editar->password:''; ?>" placeholder="Confirmar Contraseña">
						</div>
					</div>
					<div class="form-group">
						<label for="celular" class="col-sm-3 col-md-2 control-label">Número Celular</label>
						<div class="col-sm-9 col-md-10">
							<input type="text" class="form-control" id="celular" name="celular" value="<?php echo isset($usuario_editar->celular) && !empty($usuario_editar->celular) ? $usuario_editar->celular:''; ?>" placeholder="Número Celular">
						</div>
					</div>
					<div class="form-group">
						<label for="compania_celular" class="col-sm-3 col-md-2 control-label">Compañía Celular</label>
						<div class="col-sm-9 col-md-10">
							<select name="compania_celular" id="compania_celular" class="form-control">
								<option value="0">Selecciona una opción</option>
								<?php foreach ( $companias as $compania ){ ?>
									<option value="<?php echo $compania->id; ?>" <?php if( $usuario_editar->compania_celular === $compania->id ) echo 'selected'; ?>><?php echo $compania->compania; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="rol_usuario" class="col-sm-3 col-md-2 control-label">Rol de usuario</label>
						<div class="col-sm-9 col-md-10">
							<select name="rol_usuario" id="rol_usuario" class="form-control">
								<option value="0">Selecciona una opción</option>
								<?php foreach ( $roles as $rol ){ ?>
									<?php if ( $this->session->userdata( 'nivel' ) == 1 ){ ?>
										<option value="<?php echo $rol->id; ?>" <?php if( $usuario_editar->nivel === $rol->id ) echo 'selected'; ?>><?php echo $rol->nombre_rol; ?></option>
									<?php } else {
										if ( $rol->id > 1 ){ ?>
											<option value="<?php echo $rol->id; ?>" <?php if( $usuario_editar->nivel === $rol->id ) echo 'selected'; ?>><?php echo $rol->nombre_rol; ?></option>
									<?php } } ?>
								<?php } ?>
							</select>
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
												<input type="checkbox" name="categoria[]" <?php if( isset( $cats ) && !empty( $cats ) ): foreach( $cats as $cat ): if( $categoria->uid_categoria === $cat->uid_categoria ): ?> checked <?php endif; endforeach; endif; ?> value="<?php echo $categoria->uid_categoria; ?>">
												<?php echo $categoria->nombre; ?>
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
												<input type="checkbox" name="vertical[]" <?php if( isset( $vers ) && !empty( $vers ) ): foreach( $vers as $ver ): if( $vertical->uid_vertical === $ver->uid_vertical ): ?> checked <?php endif; endforeach; endif; ?> value="<?php echo $vertical->uid_vertical; ?>">
												<?php echo $vertical->nombre; ?>
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
				<a href="<?php echo base_url(); ?>usuarios" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Actualizar"/>
			</div>
		</div>
		<input type="hidden" name="token" value="<?php echo base64_encode( $uid ); ?>">
	<?php echo form_close(); ?>
<?php $this->load->view('cms/footer'); ?>