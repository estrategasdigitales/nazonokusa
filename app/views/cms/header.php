<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Cache-control" content="no-cache">
	<title>Televisa Interactive Media - Middleware</title>
	<?php echo link_tag('css/bootstrap.min.css'); ?>
	<?php echo link_tag('css/jquery-ui-1.10.4.custom.css'); ?>
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<?php echo link_tag('css/bootstrap-datepicker.css'); ?>
	<?php echo link_tag('js/extjs/resources/ext-theme-neptune/ext-theme-neptune-all.css'); ?>
	<?php echo link_tag('css/middleware.css'); ?>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <?php
        $jquery = base_url()."js/jquery.min.js";
    ?>
    <script>window.jQuery || document.write('<script src="<?=$jquery?> "><\/script>')</script>

	<script>
		$(document).ready(function(){
			$.ajaxSetup({
				timeout:25000,
				error: function(jqXHR, exception) {
		            if (jqXHR.status == 404) {
		                alert('El recurso solicitado no está disponible');
		            } else if (jqXHR.status == 500) {
		                alert('Ocurrió un error interno, favor de contactar al administrador.');
		            } else if (exception === 'parsererror') {
		                alert('El formato solicitado no es valido.');
		            } else if (exception === 'timeout') {
		                alert('El tiempo de espera ha excedido el limite permitido, favor de revisar su conexión a internet.');
		                location.reload();
		            } else if (exception === 'abort') {
		                alert('Petición cancelada.');
		            } else {
		                alert('Error en la conexión, favor de revisar su equipo.');
		            }
		        }
			});
		});
	</script>

	<!--<script type="text/javascript" src="<?php echo base_url(); ?>js/no.conflict.js"></script>-->
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.10.4.custom.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/spin.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.form.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/extjs/ext-all.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/init-tree-alquimia.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/base64_encode.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/serialize.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/tree-f2.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/middleware.js"></script>

</head>
<body>
	<div class="container-fluid">
		<div id="foo"></div>
		<header class="row">
				<div class="banner">
					<div>
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
						<div class="row-fluid">
							<div class="navbar navbar-default navbar-custom" role="navigation">
								<div class="container">
									<div class="navbar-header">
										<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
											<span class="sr-only">Toggle navigation</span>
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
										</button>
										<span class="navbar-brand">BIENVENIDO:&nbsp;&nbsp;&nbsp;<a class="text-uppercase btn-link blancos" href="<?php echo base_url(); ?>actualizar_perfil" title="Actualizar Perfil"><?php echo $this->session->userdata( 'nombre' ). ' ' . $this->session->userdata( 'apellidos' ); ?></a></span>
									</div>
								 	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
										<a href="<?php echo base_url(); ?>salir" class="navbar-brand navbar-right">
											SALIR&nbsp;&nbsp;<span class="glyphicon glyphicon-log-out"></span>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="barra-naranja"></div>
		</header>
		<div class="wrapper">
			<div class="container">
				<div class="alert" id="messages"></div>