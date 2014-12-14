<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<div class="row">				
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<h3 class="text-left">¿No recuerdas tu contraseña?</h3>
		</div>
		<div class="col-md-2"></div>
	</div>
		<br>
		<br>
		<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="datos-recuperar">
				<?php echo form_open( 'cms/recupera_contrasena', array( 'id' => 'form_recupera_contrasena','class' => 'form-horizontal', 'method' => 'POST', 'autocomplete' => 'off', 'role' => 'form' ) ); ?>
					<div class="form-group">
						<p>Danos tu email y te enviaremos un correo electrónico para que puedas recuperar tu contraseña</p>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<input type="email" class="form-control" id="forgot_email" name="forgot_email" placeholder="Correo Electrónico">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 col-sm-4 col-md-4">
							<a href="<?php echo base_url(); ?>login" type="submit" class="btn btn-danger btn-block">CANCELAR</a>
						</div>
						<div class="col-sm-8 col-md-8">
							<button type="submit" class="btn btn-success col-md-12 btn-block">RECUPERAR</button>
						</div>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div>
<?php $this->load->view('cms/footer'); ?>