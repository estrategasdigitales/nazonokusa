<?php /* 29/05 */ 

var_dump($cron_date);
var_dump($trabajo_editar); 

$meses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
$dias = array(0=>"Domingo",1=>"Lunes",2=>"Martes",3=>'Mi&eacute;rcoles',4=>"Jueves",5=>"Viernes",6=>'S&aacute;bado');

?>



<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="trabajos">

		<div class="container">
			<?php echo form_open('nucleo/validar_form_editar_trabajo',array('class' => 'form-horizontal', 'id' => 'form_trabajo_editar', 'role' => 'form')); ?>
				<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Editar Trabajo</h4></div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<input type="hidden" value="<?php echo (isset($trabajo_editar['uuid_trabajo']) && !empty($trabajo_editar['uuid_trabajo'])) ? $trabajo_editar['uuid_trabajo']:''; ?>" name="id_trabajo"  />

						<div class="panel-heading">Datos del Trabajo</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($trabajo_editar['nombre']) && !empty($trabajo_editar['nombre']) ? $trabajo_editar['nombre']:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="url-origen" class="col-sm-3 col-md-2 control-label">URL de origen</label>
								<div class="col-sm-9 col-md-10">
									<input type="url" class="form-control" id="url-origen" name="url-origen" value="<?php echo isset($trabajo_editar['url_origen']) && !empty($trabajo_editar['url_origen']) ? $trabajo_editar['url_origen']:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-local" class="col-sm-3 col-md-2 control-label">Destino local</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="destino-local" name="destino-local" value="<?php echo isset($trabajo_editar['url_local']) && !empty($trabajo_editar['url_local']) ? $trabajo_editar['url_local']:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-net" class="col-sm-3 col-md-2 control-label">Destino net-storage</label>
								<div class="col-sm-9 col-md-10">
									<input type="tel" class="form-control" id="destino-net" name="destino-net" value="<?php echo isset($trabajo_editar['url_storage']) && !empty($trabajo_editar['url_storage']) ? $trabajo_editar['url_storage']:''; ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-md-2 control-label">Categoría</label>
								<?php if( isset($categorias) && !empty($categorias) ): ?>
									<div class="col-sm-9 col-md-10">
										<select class="form-control" name="categoria">							
											<?php foreach($categorias as $categoria): ?>
												<option value="<?php echo $categoria['uuid_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php else: ?>
									<div class="col-sm-9 col-md-10">
										<h5 class="form-control">Este usuario no tiene asignada ninguna categoría</h5>
									</div>
								<?php endif; ?>								
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-md-2 control-label">Vertical</label>
								<?php if( isset($verticales) && !empty($verticales) ): ?>
									<div class="col-sm-9 col-md-10">
										<select class="form-control" name="vertical">							
											<?php foreach($verticales as $vertical): ?>
												<option value="<?php echo $vertical['uuid_vertical']; ?>"><?php echo $vertical['nombre']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php else: ?>
									<div class="col-sm-9 col-md-10">
										<h5 class="form-control">Este usuario no tiene asignada ninguna vertical</h5>
									</div>
								<?php endif; ?>	
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Selecciona el formato de salida</div>
						<div class="panel-body">
							<div class="col-sm-6 col-md-6">							
								<div class="form-group">
									<div class="checkbox">
										<label>
											<input onChange="datosAdicionales(this);" type="checkbox" name="formato[]" value="json" id="json">
											JSON
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input onChange="datosAdicionales(this);" type="checkbox" name="formato[]" value="jsonp" id="jsonp">
											JSON-P
										</label>
									</div>									
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
								<div class="form-group">
									<div class="checkbox">
										<label>
											<input onChange="datosAdicionales(this);" type="checkbox" name="formato[]" value="xml" id="xml">
												XML
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input onChange="datosAdicionales(this);" type="checkbox" name="formato[]" value="rss2" id="rss2">
											RSS 2.0
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-md-4"><button onclick="cargar_campos();" type="button" class="btn btn-primary btn-block">Detectar Campos</button></div>
					<div class="col-sm-8 col-md-8">
						<h4>* Debes dar clic en esta opción para que el sistema procese la informacion de origen.</h4>
					</div>
				</div>
				<br>
				<script>
				function cargar_campos(){

					$.ajax({
						url: '<?= base_url(""); ?>nucleo/detectar_campos',
						type: 'POST',
						dataType: 'html',
						data: {url: $('#url-origen').val()},
					})
					.done(function(data) {
						$('.campos-feed .panel-body').html('');
						$('.campos-feed .panel-body').append(data);
						$('.campos-feed').slideDown();
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					});
					
				}
				</script>
				<div class="container row campos-feed">
					<div class="panel panel-primary">
						<div class="panel-heading">Selecciona los campos que deseas obtener en la salida<span class="navbar-right" id="tipo_archivo"></span></div>
						<div class="panel-body">

						</div>
					</div>
				</div>
				<br>
				<div class="container row campos_rss">
					<div class="panel panel-primary">
						<div class="panel-heading">Campos adicionales para el Formato RSS 2.0</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="channel_title" class="col-sm-3 col-md-2 control-label">Title</label>
								<div class="col-sm-9 col-md-10">
									<input type="hidden" name="claves_rss[]" value="title">
									<input type="text" class="form-control" id="channel_title" name="valores_rss[]">
								</div>
							</div>
							<div class="form-group">
								<label for="channel_link" class="col-sm-3 col-md-2 control-label">Link</label>
								<div class="col-sm-9 col-md-10">
									<input type="hidden" name="claves_rss[]" value="link">
									<input type="url" class="form-control" id="channel_link" name="valores_rss[]">
								</div>
							</div>
							<div class="form-group">
								<label for="channel_description" class="col-sm-3 col-md-2 control-label">Description</label>
								<div class="col-sm-9 col-md-10">
									<input type="hidden" name="claves_rss[]" value="description">
									<input type="text" class="form-control" id="channel_description" name="valores_rss[]">
								</div>
							</div>
							<div class="form-group agregar_campo">
								<div class="col-sm-4 col-md-4">
								</div>
								<div class="col-sm-4 col-md-4">
								</div>
								<div class="col-sm-4 col-md-4">
									<div type="button" onclick="ShowDialog4();" class="btn btn-success btn-block">Agregar Campo</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="container row campos_jsonp">
					<div class="panel panel-primary">
						<div class="panel-heading">Campos adicionales para el Formato JSONP</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="nom_funcion" class="col-sm-3 col-md-2 control-label">Nombre de la Función</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nom_funcion" name="nom_funcion">
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Datos para programar la tarea</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-4 col-md-4">
									<label for="cron_mes" class="form-trabajos-label"> Mes: </label>
									<select class="form-control form-trabajos-date" name="cron_mes">
                                              <option value="*" <?= ($cron_date["mes"]=="*") ?'selected="selected"':''; ?> >Cada mes</option>
										<?php 
											foreach($meses as $k => $v){
												$s = ($cron_date["mes"]==$k)?' selected="selected" ':'';
												echo '<option '.$s.' value="'.$k.'">Cada '.$v.'</option>';
											}
										?>
										
									</select>
								</div>
								<div class="col-sm-4 col-md-4">
									<label for="cron_diasemana" class="form-trabajos-date2"> D&iacute;a(s) de la semana: </label>
									<select class="form-control form-trabajos-date2" name="cron_diasemana">
										<option value="*" <?= ($cron_date["diasemana"]=="*") ?'selected="selected"':''; ?> >Todos los d&iacute;as</option>
										<?php 
											foreach($dias as $k => $v){
												$s = ($cron_date["diasemana"]==$k)?' selected="selected" ':'';
												echo '<option '.$s.' value="'.$k.'">Cada '.$v.'</option>';
											}
										?>
									</select>
								</div>
								<div class="col-sm-4 col-md-4">	
									<label for="cron_diasemana" class="form-trabajos-label">D&iacute;a del mes: </label>
									<select class="form-control form-trabajos-date" name="cron_diames">
										<option value="*" <?= ($cron_date["diames"]=="*") ?'selected="selected"':''; ?> > -- </option>
										<?php 

											for($i=1;$i<=31;$i++){
												$s = ($cron_date["diames"]==$i)?' selected="selected" ':'';
												echo '<option '.$s.' value="'.$i.'">'.$i.'</option>';
											}

									    ?>
									</select>
								</div>
								
							</div>
							<br>
							<div class="row">
								
								
								<div class="col-sm-4 col-md-4">
									<label class="form-trabajos-hora">Hora:</label>
									<select class="form-control form-trabajos-hora" name="cron_hora">
										<?php 

											for($i=0;$i<=23;$i++){
												$s = ($cron_date["hora"]==$i)?' selected="selected" ':'';
												echo '<option '.$s.' value="'.$i.'">'.str_pad($i,2,"0",STR_PAD_LEFT).'</option>';
											}

									    ?>
										
									</select>


									<select class="form-control form-trabajos-hora" name="cron_minuto">
										<?php 

											for($i=0;$i<=59;$i++){
												$s = ($cron_date["minuto"]==$i)?' selected="selected" ':'';
												echo '<option '.$s.' value="'.$i.'">'.str_pad($i,2,"0",STR_PAD_LEFT).'</option>';
											}

									    ?>
										
									</select>
								</div>
								<div class="col-sm-8 col-md-8"> <span class="form-trabajos-dia"> *Si se selecciona un n&uacute;mero de d&iacute;a por mes se omite el d&iacute;a de la semana.</span> </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-md-4">
						<a href="<?php echo site_url("trabajos") ?>" type="button" class="btn btn-warning btn-block">Ejecutar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<a href="<?php echo site_url("trabajos") ?>" type="button" class="btn btn-danger btn-block">Cancelar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Enviar"/>
					</div>
				</div>
			<?php echo form_close(); ?>
			<?php if ( isset($error) ) : ?>
				<div class="alert alert-danger"><?php print_r($error); ?></div>
			<?php endif; ?>
		</div>
	</nav>
	<div id="agregarCampo" style="display:none;">
		<div class="row">
		<div class="form-group">
			<div class="col-sm-12 col-md-12">
				<input placeholder="Nombre del Campo" type="text" class="form-control" id="nuevo_nombre" name="nuevo_nombre">
			</div>
		</div>
		</div>
	</div>
	<footer>
		
	</footer>
</body>