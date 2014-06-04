<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="trabajos">
		<div class="container">
			<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Histórico de reportes</h4></div>
					<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nuevo_reporte" type="button" class="btn btn-primary btn-block">Nuevo Reporte</a></div>
			</div>
			<br>
			<div class="row">
				<div class="container table-responsive">
					<table class="table table-striped table-hover table-bordered">
						<tr class="titulo-columna">
							<td>Periodo de reporte</td>
							<td>Fecha de creación</td>
                            <?php  if( isset( $level ) && $level == 1){ ?>
	                            <td>Descargar</td>
								<td>Enviar</td>
                            <?php  } ?>
						</tr>
					</table>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-8 col-md-8"></div>
				<div class="col-sm-4 col-md-4">
					<a href="<?php base_url(); ?>index" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
				</div>
			</div>
		</div>
		<div id="dialogConfirm"><span id="spanMessage"></span>
	</nav>
	<footer>
		
	</footer>
</body>