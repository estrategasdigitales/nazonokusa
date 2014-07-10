<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<div class="row">
		<div class="col-md-3"></div>
		<?php if ( $this->session->userdata('nivel') >= '1' && $this->session->userdata('nivel') <= '2'  ) : ?>
			<div class="col-md-6">
				<a href="<?php echo base_url(); ?>usuarios" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Usuarios</a>
			</div>
		<?php endif; ?>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<a href="<?php echo base_url(); ?>trabajos" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Trabajos</a>
		</div>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<?php if( $this->session->userdata('nivel') >= '1' && $this->session->userdata('nivel') <= '2' ): ?>
			<div class="col-md-6">
				<a href="<?php echo base_url(); ?>categorias" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Categorías</a>
			</div>
		<?php endif; ?>
		<div class="col-md-3"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3"></div>
		<?php if( $this->session->userdata('nivel') >= '1' && $this->session->userdata('nivel') <= '2' ): ?>
			<div class="col-md-6">
				<a href="<?php echo base_url(); ?>verticales" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Verticales</a>
			</div>
		<?php endif; ?>
		<div class="col-md-3"></div>
	</div>
	<br>
	<!--<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<a href="<?php echo base_url(); ?>reportes" type="button" class="btn btn-primary btn-lg btn-block">Reportes</a>
		</div>
		<div class="col-md-3"></div>
	</div>-->
<?php $this->load->view('cms/footer'); ?>

