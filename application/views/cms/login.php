<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="login">
		<div class="container">
			<div class="row">				
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<h3>Bienvenido al Sistema de Administración de Tareas y Contenidos para Middleware</h3>
				</div>
				<div class="col-md-2"></div>
			</div>
			<br>
			<br>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="datos-login">
						<?php echo form_open('cms/validar_usuario',array('class'=>'form-horizontal','role' => 'form')); ?>
							<div class="form-group">
								<div class="col-md-12">
									<input type="usuario" class="form-control" id="usuario" name="usuario"
							placeholder="Usuario">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<input type="password" class="form-control" id="password" name="password" 
							placeholder="Contraseña">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-8 col-md-8"></div>
								<div class="col-xs-12 col-sm-4 col-md-4">
									<button type="submit" class="btn btn-primary col-md-12 btn-block">INGRESAR</button>
								</div>
							</div>
        				<?php echo form_close(); ?>
					</div>
					<?php if ( validation_errors() ) { ?>
						<div class="alert alert-danger"><?php echo validation_errors(); ?></div>
					<?php } ?>
					<?php if ( isset($error) ) { ?>
						<div class="alert alert-danger"><?php echo $error; ?></div>
					<?php } ?>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>		
	</nav>	
	<footer>
		
	</footer>
</body>
<?php $this->load->view('cms/footer'); ?>