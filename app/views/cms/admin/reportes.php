<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view( 'cms/header' ); ?>
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
					<td>Descargar</td>
					<td>Enviar</td>
				</tr>
				<tr>
					<td>20/05/2014 - 03/06/2014</td>
					<td>03/06/2014</td>
					<td class="text-center"><a href="#" title="Descargar" class="btn"><span class="glyphicon glyphicon-download-alt"></span></a></td>
					<td class="text-center"><a href="#" title="Enviar" class="btn"><span class="glyphicon glyphicon-envelope"></span></a></td>
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
	<div id="dialogConfirm"><span id="spanMessage"></span></div>
<?php $this->load->view( 'cms/footer' ); ?>