<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
	<?php $this->load->view('cms/header'); ?>
		<?php echo form_open( 'cms/actualizar_perfil_actualizar', array('class' => 'form-horizontal', 'id' => 'form_actualizar_perfil', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
			<div class="row">
				<div class="col-sm-8 col-md-8"><h4>Actualizar Perfil</h4></div>
			</div>
			<br>
			<div class="container row">
				<div class="panel panel-primary">
					<div class="panel-heading">Datos del Usuario</div>
					<div class="panel-body">
						<div class="col-sm-6 col-md-6">
							<div class="form-group">
								<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo $nombre; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="apellidos" class="col-sm-3 col-md-2 control-label">Apellido(s)</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellido (s)" value="<?php echo $apellidos; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="email" class="col-sm-3 col-md-2 control-label">Email</label>
								<div class="col-sm-9 col-md-10">
									<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="extension" class="col-sm-3 col-md-2 control-label">Extensión Telefónica</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="extension" name="extension" placeholder="Extensión Telefónica" value="<?php echo $extension; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="celular" class="col-sm-3 col-md-2 control-label">Número Celular</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="celular" name="celular" placeholder="Número Celular" value="<?php echo $celular; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="compania_celular" class="col-sm-3 col-md-2 control-label">Compañía Celular</label>
								<div class="col-sm-9 col-md-10">
									<select name="compania_celular" id="compania_celular" class="form-control">
										<option value="0" <?php if( $telefonica == 0 ) echo 'selected'; ?>>Selecciona una opción</option>
										<?php foreach ( $companias as $compania ){ ?>
											<option value="<?php echo $compania->id; ?>" <?php if( $telefonica == $compania->id ) echo 'selected'; ?>><?php echo $compania->compania; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-6">
							<div class="form-group">
								<label for="password" class="col-sm-3 col-md-2 control-label">Contraseña</label>
								<div class="col-sm-9 col-md-10">
									<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
								</div>
							</div>
							<div class="form-group">
								<label for="password_2" class="col-sm-3 col-md-2 control-label">Confirmar Contraseña</label>
								<div class="col-sm-9 col-md-10">
									<input type="password" class="form-control" id="password_2" name="password_2" placeholder="Confirmar Contraseña">
								</div>
							</div>
							<div class="form-group">
								<label for="password_actual" class="col-sm-3 col-md-2 control-label">Contraseña Actual</label>
								<div class="col-sm-9 col-md-10">
									<input type="password" class="form-control" id="password_actual" name="password_actual" placeholder="Contraseña Actual">
								</div>
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
			<input type="hidden" name="usuario" value="<?php echo base64_encode( $uid ); ?>">
		<?php form_close(); ?>
	<?php $this->load->view('cms/footer'); ?>