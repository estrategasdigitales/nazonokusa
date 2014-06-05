<?php $this->load->view('cms/header'); ?>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<?php if( $this->session->userdata('nivel') === '1' ): ?>
				<a href="<?php base_url(); ?>usuarios" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Usuarios</a>
			<?php endif; ?>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<?php if( $this->session->userdata('nivel') === '1' || $this->session->userdata('nivel') === '2'): ?>
				<a href="<?php base_url(); ?>trabajos" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Trabajos</a>
			<?php endif; ?>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<?php if( $this->session->userdata('nivel') === '1' ): ?>
				<a href="<?php base_url(); ?>categorias" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Categorías</a>
			<?php endif; ?>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<?php if( $this->session->userdata('nivel') === '1' ): ?>
				<a href="<?php base_url(); ?>verticales" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Verticales</a>
			<?php endif; ?>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<a href="<?php base_url(); ?>reportes" type="button" class="btn btn-primary btn-lg btn-block">Reportes</a>
		</div>
		<div class="col-md-3"></div>
	</div>
<?php $this->load->view('cms/footer'); ?>

