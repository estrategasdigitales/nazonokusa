<header>
	<div class="banner">
		<div class="row">
			<div class="container header-content text-right">
				<div class="row bloques-en-linea">
					<div class="header-titulo col-md-12">Sistema de Administración de Tareas y Contenidos para Middleware</div>
				</div>
				<div class="row bloques-en-linea">
					<div class="header-logo col-md-2"></div>
				</div>
			</div>
		</div>
		<?php if( $this->session->userdata('session') === TRUE ): ?>
		<div class="row">
			<div class="navbar navbar-default" role="navigation">
				<div class="container-fluid">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="#">BIENVENIDO:&nbsp;&nbsp;&nbsp;<?php echo strtoupper($usuario); ?> </a>
						</div>
					 	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
							<a href="<?php echo base_url(); ?>salir" class="navbar-brand navbar-right">
								SALIR&nbsp;&nbsp;<span class="glyphicon glyphicon-log-out"></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="barra-naranja"></div>
</header>