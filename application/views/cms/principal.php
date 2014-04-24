<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="principal">
		<div class="container">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<?php if( $this->session->userdata('nivel') === '1' ): ?>
						<a href="#" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Usuarios</a>
					<?php endif; ?>
				</div>
				<div class="col-md-3"></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<?php if( $this->session->userdata('nivel') === '1' || $this->session->userdata('nivel') === '2'): ?>
						<a href="#" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Trabajos</a>
					<?php endif; ?>
				</div>
				<div class="col-md-3"></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<?php if( $this->session->userdata('nivel') === '1' ): ?>
						<a href="#" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Categorías</a>
					<?php endif; ?>
				</div>
				<div class="col-md-3"></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<?php if( $this->session->userdata('nivel') === '1' ): ?>
						<a href="#" type="button" class="btn btn-primary btn-lg btn-block">Administrador de Verticales</a>
					<?php endif; ?>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</nav>
	<footer>
		
	</footer>
</body>
<?php $this->load->view('cms/footer'); ?>

