<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="verticales">
		<div class="container">
			<?php echo form_open('cms/validar_form_vertical',array('class' => 'form-horizontal', 'id' => 'form_vertical_nueva', 'role' => 'form')); ?>
				<div class="row">
						<div class="col-sm-8 col-md-8"><h4>Nueva Vertical</h4></div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-2"></div>
					<div class="col-md-8">
						<div class="form-group">
							<label for="nombre" class="col-sm-4 col-md-4 control-label">Nombre de la Vertical</label>
							<div class="col-sm-8 col-md-8">
								<input type="text" class="form-control" id="nombre" name="nombre">
							</div>
						</div>
					</div>
					<div class="col-md-2"></div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-2 col-md-2"></div>
					<div class="col-sm-4 col-md-4">
						<a href="<?php base_url(); ?>verticales" type="button" class="btn btn-danger btn-block">Cancelar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Enviar"/>
					</div>
					<div class="col-sm-2 col-md-2"></div>
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