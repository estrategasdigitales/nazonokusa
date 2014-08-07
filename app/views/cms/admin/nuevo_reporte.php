<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'cms/validar_form_nuevo_reporte', array('class' => 'form-horizontal', 'id' => 'form_reporte_nuevo', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Nuevo Reporte</h4></div>
		</div>
		<br>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Datos del Reporte</div>
				<div class="panel-body">
					<div class="col-sm-12 col-md-12">
						<div class="form-group">
							<label for="nombre_reporte" class="col-sm-3 col-md-1 control-label">Nombre</label>
							<div class="col-sm-9 col-md-11">
								<input type="text" class="form-control" id="nombre_reporte" name="nombre_reporte" placeholder="Nombre del reporte">
							</div>
						</div>
						<div class="form-group">
							<label for="fecha_inicio" class="col-sm-3 col-md-1 control-label">Periodo</label>
							<div class="col-sm-6 col-md-6">
								<div class="input-daterange input-group" id="datepicker">
									<input type="text" class="input-sm form-control" name="fecha_inicio">
									<span class="input-group-addon">a</span>
									<input type="text" class="input-sm form-control" name="fecha_termino">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 col-md-1 control-label">Trabajos</label>
							<div class="col-sm-9 col-md-11">
								<?php if ( isset( $trabajos ) ) { ?>
									<?php foreach ( $trabajos as $trabajo ){ ?>
									<div class="col-sm-6 col-md-6 checkbox">
										<label>
											<input type="checkbox" value="<?php echo $trabajo->uid_trabajo; ?>" name="trabajos[]">
											<?php echo $trabajo->nombre; ?>
										</label>
									</div>
									<?php } ?>
								<?php } else { ?>
									<div class="well">
										<em>Aún no has guardado ningún trabajo</em>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-4 col-md-4"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>reportes" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Generar"/>
			</div>
		</div>
		<input type="hidden" id="treeStructure" name="treeStructure" value="">
	<?php echo form_close(); ?>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
<?php $this->load->view('cms/footer'); ?>