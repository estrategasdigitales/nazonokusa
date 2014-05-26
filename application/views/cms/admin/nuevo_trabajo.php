<?php $this->load->view('cms/head'); ?>
<body>
	<?php $this->load->view('cms/header'); ?>
	<nav class="trabajos">
		<div class="container">
			<?php echo form_open('nucleo/validar_form_trabajo',array('class' => 'form-horizontal', 'id' => 'form_trabajo_nuevo', 'role' => 'form')); ?>
				<div class="row">
					<div class="col-sm-8 col-md-8"><h4>Nuevo Trabajo</h4></div>
				</div>
				<br>
				<div class="container row">
					<div class="panel panel-primary">
						<div class="panel-heading">Datos del Trabajo</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="nombre" name="nombre">
								</div>
							</div>
							<div class="form-group">
								<label for="url-origen" class="col-sm-3 col-md-2 control-label">URL de origen</label>
								<div class="col-sm-9 col-md-10">
									<input type="url" class="form-control" id="url-origen" name="url-origen">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-local" class="col-sm-3 col-md-2 control-label">Destino local</label>
								<div class="col-sm-9 col-md-10">
									<input type="text" class="form-control" id="destino-local" name="destino-local">
								</div>
							</div>
							<div class="form-group">
								<label for="destino-net" class="col-sm-3 col-md-2 control-label">Destino net-storage</label>
								<div class="col-sm-9 col-md-10">
									<input type="tel" class="form-control" id="destino-net" name="destino-net">
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
						url: '<?php base_url(); ?>nucleo/detectar_campos',
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
							<div class="form-group">
								<div class="col-sm-4 col-md-4">
								</div>
								<div class="col-sm-4 col-md-4">
								</div>
								<div class="col-sm-4 col-md-4">
									<div type="button" class="btn btn-success btn-block">Agregar Campo</div>
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
									<select class="form-control">
										<option value="">MES</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
									</select>
								</div>
								<div class="col-sm-4 col-md-4">
									<select class="form-control">
										<option value="">DÍA DE LA SEMANA</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
									</select>
								</div>
								<div class="col-sm-4 col-md-4">
									<select class="form-control">
										<option value="">HORA</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
										<option value="13">13</option>
										<option value="14">14</option>
										<option value="15">15</option>
										<option value="16">16</option>
										<option value="17">17</option>
										<option value="18">18</option>
										<option value="19">19</option>
										<option value="20">20</option>
										<option value="21">21</option>
										<option value="22">22</option>
										<option value="23">23</option>
										<option value="24">24</option>
									</select>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-4 col-md-4">	
									<select class="form-control">
										<option value="">DÍA DEL MES</option>
										<option value="">1</option>
										<option value="">2</option>
										<option value="">3</option>
										<option value="">4</option>
										<option value="">5</option>
										<option value="">6</option>
										<option value="">7</option>
									</select>
								</div>
								<div class="col-sm-4 col-md-4">
								</div>
								<div class="col-sm-4 col-md-4">
									<select class="form-control">
										<option value="">MINUTO</option>
										<option value="">2</option>
										<option value="">3</option>
										<option value="">4</option>
										<option value="">5</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-md-4">
						<a href="<?php base_url(); ?>usuarios" type="button" class="btn btn-warning btn-block">Ejecutar</a>
					</div>
					<div class="col-sm-4 col-md-4">
						<a href="<?php base_url(); ?>usuarios" type="button" class="btn btn-danger btn-block">Cancelar</a>
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
	<footer>
		
	</footer>
</body>