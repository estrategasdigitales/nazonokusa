<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'nucleo/validar_form_nueva_estructura', array('class' => 'form-horizontal', 'id' => 'form_estructura_nuevo', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
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
						<div class="form-group">
							<label for="url-origen" class="col-sm-3 col-md-2 control-label">URL de origen</label>
							<div class="col-sm-9 col-md-10">
								<input type="url" class="form-control" id="url-origen" name="url-origen">
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
		<div class="row">
			<div class="col-sm-4 col-md-4"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>estructuras" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Guardar"/>
			</div>
		</div>
		<input type="hidden" id="treeStructure" name="treeStructure" value="">
	<?php echo form_close(); ?>
	<div class="modal fade bs-example-modal-lg" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	<script type="text/javascript">
		tvs(function($){
			$("#tree-constructor").jstree({
				'json_data' : {
					'data' : [
						{
							'data' : {
								'title' : 'title',
							},
						},
						{
							'data' : {
								'title' : 'link',
							},
						},
						{
							'data' : {
								'title' : 'description',
							},
						},
						{
							'data' : {
								'title' : 'image',
							},
						},
						{
							'data' : {
								'title' : 'language',
							},
						},
						{
							'data' : {
								'title' : 'copyright',
							},
						},
						{
							'data' : {
							'title' : 'managingEditor',
							},
						},
						{
							'data' : {
								'title' : 'webMaster',
							},
						},
						{
							'data' : {
								'title' : 'pubDate',
							},
						},
						{
							'data' : {
								'title' : 'lastBuildDate',
							},
						},
						{
							'data' : {
								'title' : 'category',
							},
						},
						{
							'data' : {
							'title' : 'generator',
							},
						},
					]
				},
                 'types' : {
					// I set both options to -2, as I do not need depth and children count checking
					// Those two checks may slow jstree a lot, so use only when needed
					'max_depth' : -2,
					'max_children' : -2,
					// I want only `drive` nodes to be root nodes
					// This will prevent moving or creating any other type as a root node
					'valid_children' : [ 'all' ],
					'types' : {
					// The default type
						"default" : {
							"valid_children" : "all",
                            "check_node" : false,
                            "uncheck_node" : false
						}
                     },
                 },
                 'plugins' : [ 'themes', 'types', 'json_data', 'ui', 'dnd' ],
                 'ui' : {
                     'initially_select' : [ 'node3' ]
                 },
                 'core' : {
                     'initially_open' : [ 'node1' , 'node2' ]
                 },
                 // set a theme
				'themes' : {
            		'theme' : 'proton',
            		'icons': false
        		}
             });
         });
	</script>
<?php $this->load->view('cms/footer'); ?>