<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'validar_form_estructura', array('class' => 'form-horizontal', 'id' => 'form_estructura_nuevo', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Nueva Estructura</h4></div>
		</div>
		<br>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Datos de la Estructura</div>
				<div class="panel-body">
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
							<div class="col-sm-9 col-md-10">
								<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la estructura">
							</div>
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label for="url-origen" class="col-sm-3 col-md-2 control-label">Formato</label>
							<div class="col-sm-9 col-md-10">
								<select class="form-control" name="formato_salida">
									<option value="0">Selecciona una Formato de Salida</option>					
									<option value="1">RSS</option>
									<option value="2">XML</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Agrega campos de salida</div>
				<div class="panel-body">
					<div class="col-sm-6 col-md-7">
						<div class="form-group">
							<label for="" class="col-sm-3 col-md-2 control-label">Nombre del campo</label>
							<div class="col-sm-5 col-md-7">
								<input type="text" class="form-control" id="" name="" placeholder="Nombre del campo">
							</div>
							<div class="col-sm-4 col-md-3">
								<select class="form-control" name="tipo_campo">
									<option value="0">Selecciona una Formato de Salida</option>					
									<option value="1">RSS</option>
									<option value="2">XML</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-6 col-md-5">
						<div id="tree-constructor"></div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-4 col-md-4"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>estructuras" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Guardar"/>
			</div>
		</div>
		<input type="hidden" id="claves" name="claves">
	<?php echo form_close(); ?>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	<script type="text/javascript">
		tvs(function($){
			var data = [
				{
				    label: 'node1',
				    children: [
				        { label: 'child1' },
				        { label: 'child2' }
				    ]
				},
				{
				    label: 'node2',
				    children: [
				        { label: 'child3' }
				    ]
				}
			];
			$('#tree-constructor').tree({
				data: data
			});
		});
	</script>
<?php $this->load->view('cms/footer'); ?>