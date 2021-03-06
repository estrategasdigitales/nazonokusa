<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'nucleo/validar_form_trabajo', array('class' => 'form-horizontal', 'id' => 'form_trabajo_nuevo', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Nuevo Trabajo</h4></div>
		</div>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Datos del Trabajo</div>
				<div class="panel-body">
					<div class="col-sm-6 col-md-6">
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
					</div>
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label class="col-sm-3 col-md-2 control-label">Categoría</label>
							<?php if( isset($categorias) && !empty($categorias) ): ?>
								<div class="col-sm-9 col-md-10">
									<select class="form-control" name="categoria">
										<option value="0">Selecciona una Categoría</option>					
										<?php foreach($categorias as $categoria): ?>
											<option value="<?php echo $categoria->uid_categoria; ?>"><?php echo $categoria->nombre; ?></option>
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
							<?php if ( isset( $verticales ) && ! empty( $verticales ) ): ?>
								<div class="col-sm-9 col-md-10">
									<select class="form-control" name="vertical">
										<option value="0">Selecciona una Vertical</option>
										<?php foreach($verticales as $vertical): ?>
											<option value="<?php echo $vertical->uid_vertical; ?>"><?php echo $vertical->nombre; ?></option>
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
		</div>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Selecciona el tipo de salida</div>
				<div class="panel-body">
					<h5>Puedes seleccionar una salida estándar (conversión directa) o salida específica (conversión estricta)</h5>
					<div class="col-sm-12 col-md-12">
						<div class="form-group">
							<label for="tipo_salida" class="form-trabajos-label">Tipo de salida: </label>
							<select class="form-control form-trabajos-date" name="tipo_salida" id="tipo_salida">
								<option value="0">Selecciona un tipo de salida</option>
								<option value="1">Salida estándar</option>
								<option value="2">Salida específica</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="formatos_estandar" class="hide">
			<div class="row">
				<div class="col-sm-4 col-md-4">
					<button onclick="cargar_campos_estandar();" type="button" class="btn btn-primary btn-block" id="cmdRender">Detectar Campos</button>
				</div>
				<div class="col-sm-8 col-md-8">
					<h4>* Debes dar clic en esta opción para que el sistema procese la informacion de origen.</h4>
				</div>
			</div>
			<br>
			<div class="container row">
				<div class="panel panel-primary">
					<div class="panel-heading">Formatos estándar</div>
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
										<input onChange="datosAdicionales(this);" type="checkbox" name="formato[]" value="rss" id="rss">
										RSS 2.0
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="formatos_especificos" class="hide">
			<div class="row">
				<div class="col-sm-4 col-md-4">
					<button onclick="cargar_campos_especificos();" type="button" class="btn btn-primary btn-block" id="cmdRender">Detectar Campos</button>
				</div>
				<div class="col-sm-8 col-md-8">
					<h4>* Debes dar clic en esta opción para que el sistema procese la informacion de origen.</h4>
				</div>
			</div>
			<br>
			<div class="container row">
				<div class="panel panel-primary">
					<div class="panel-heading">Formatos específicos</div>
					<div class="panel-body">
						<?php if ( isset( $estructuras ) && ! empty( $estructuras ) ): ?>
							<div class="col-sm-12 col-md-12">
								<div class="form-group">
									<label for="formato_especifico" class="form-trabajos-label">Salida específica: </label>
									<select class="form-control form-trabajos-date" name="formato_especifico" id="formato_especifico">
										<option value="0">Selecciona un formato de salida específico</option>
										<?php foreach($estructuras as $estructura): ?>
											<option value="<?php echo $estructura->uid_estructura; ?>"><?php echo $estructura->nombre; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						<?php else: ?>
							<div class="col-sm-12 col-md-12">
								<h5 class="form-control">No existen estructuras específicas disponibles</h5>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="container row campos-feed">
			<div class="panel panel-primary">
				<div class="panel-heading">Selecciona los campos que deseas obtener en la salida (MAPEO MANUAL DE CAMPOS)</div>
				<div class="panel-body">
					<div class="bloque-arbol" id="campos-feed"></div>
				</div>
			</div>
		</div>
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
				</div>
			</div>
		</div>
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
								<option value="*">Cada mes</option>
								<option value="1">Cada Enero</option>
								<option value="2">Cada Febrero</option>
								<option value="3">Cada Marzo</option>
								<option value="4">Cada Abril</option>
								<option value="5">Cada Mayo</option>
								<option value="6">Cada Junio</option>
								<option value="7">Cada Julio</option>
								<option value="8">Cada Agosto</option>
								<option value="9">Cada Septiembre</option>
								<option value="10">Cada Octubre</option>
								<option value="11">Cada Noviembre</option>
								<option value="12">Cada Diciembre</option>
							</select>
						</div>
						<div class="col-sm-4 col-md-4">
							<label for="cron_diasemana" class="form-trabajos-date2"> D&iacute;a(s) de la semana: </label>
							<select class="form-control form-trabajos-date2" name="cron_diasemana">
								<option value="*">Todos los d&iacute;as</option>
								<option value="0">Cada Domingo</option>
								<option value="1">Cada Lunes</option>
								<option value="2">Cada Martes</option>
								<option value="3">Cada Mi&eacute;rcoles</option>
								<option value="4">Cada Jueves</option>
								<option value="5">Cada Viernees</option>
								<option value="6">Cada S&aacute;bado</option>
							</select>
						</div>
						<div class="col-sm-4 col-md-4">	
							<label for="cron_diames" class="form-trabajos-label0">D&iacute;a del mes: </label>
							<select class="form-control form-trabajos-date" name="cron_diames">
								<option value="*">Todos los d&iacute;as</option>
								<option value="1">1</option> <option value="2">2</option> <option value="3">3</option>
								<option value="4">4</option> <option value="5">5</option> <option value="6">6</option>
								<option value="7">7</option> <option value="8">8</option> <option value="9">9</option>
								<option value="10">10</option> <option value="11">11</option> <option value="12">12</option>
								<option value="13">13</option> <option value="14">14</option> <option value="15">15</option>
								<option value="16">16</option> <option value="17">17</option> <option value="18">18</option>
								<option value="19">19</option> <option value="20">20</option> <option value="21">21</option>
								<option value="22">22</option> <option value="23">23</option> <option value="24">24</option>
								<option value="25">25</option> <option value="26">26</option> <option value="27">27</option>
								<option value="28">28</option> <option value="29">29</option> <option value="30">30</option>
								<option value="31">31</option>
							</select>
						</div>
						
					</div>
					<br>
					<div class="row">
						<div class="col-sm-4 col-md-4">
							<label class="form-trabajos-hora">Hora:</label>
							<select class="form-control form-trabajos-hora" name="cron_hora">
								<option value="*">Cada hr.</option> <option value="*/2">Cada 2hrs.</option> <option value="*/6">Cada 6hrs.</option>
								<option value="*/12">Cada 12hrs.</option> <option value="0">00</option> 
								<option value="1">01</option> <option value="2">02</option>
								<option value="3">03</option> <option value="4">04</option> <option value="5">05</option>
								<option value="6">06</option> <option value="7">07</option> <option value="8">08</option>
								<option value="9">09</option> <option value="10">10</option> <option value="11">11</option>
								<option value="12">12</option> <option value="13">13</option> <option value="14">14</option>
								<option value="15">15</option> <option value="16">16</option> <option value="17">17</option>
								<option value="18">18</option> <option value="19">19</option> <option value="20">20</option>
								<option value="21">21</option> <option value="22">22</option> <option value="23">23</option>
							</select>
							<select class="form-control form-trabajos-hora" name="cron_minuto">
								<option value="*/5">Cada 5 mins</option>
								<option value="*/10">Cada 10 mins</option>
								<option value="*/15">Cada 15 mins</option>
								<option value="*/30">Cada 30 mins</option>
								<option value="0">00</option> 
								<option value="1">01</option> <option value="2">02</option> <option value="3">03</option>
								<option value="4">04</option> <option value="5">05</option> <option value="6">06</option>
								<option value="7">07</option> <option value="8">08</option> <option value="9">09</option>
								<option value="10">10</option> <option value="11">11</option> <option value="12">12</option>
								<option value="13">13</option> <option value="14">14</option> <option value="15">15</option>
								<option value="16">16</option> <option value="17">17</option> <option value="18">18</option>
								<option value="19">19</option> <option value="20">20</option> <option value="21">21</option>
								<option value="22">22</option> <option value="23">23</option> <option value="24">24</option>
								<option value="25">25</option> <option value="26">26</option> <option value="27">27</option>
								<option value="28">28</option> <option value="29">29</option> <option value="30">30</option>
								<option value="31">31</option> <option value="32">32</option> <option value="33">33</option>
								<option value="34">34</option> <option value="35">35</option> <option value="36">36</option>
								<option value="37">37</option> <option value="38">38</option> <option value="39">39</option>
								<option value="40">40</option> <option value="41">41</option> <option value="42">42</option>
								<option value="43">43</option> <option value="44">44</option> <option value="45">45</option>
								<option value="46">46</option> <option value="47">47</option> <option value="48">48</option>
								<option value="49">49</option> <option value="50">50</option> <option value="51">51</option>
								<option value="52">52</option> <option value="53">53</option> <option value="54">54</option>
								<option value="55">55</option> <option value="56">56</option> <option value="57">57</option>
								<option value="58">58</option> <option value="59">59</option> 
							</select>
						</div>
						<div class="col-sm-8 col-md-8"> <span class="form-trabajos-dia"> *Si se selecciona un n&uacute;mero de d&iacute;a por mes se omite el d&iacute;a de la semana.</span> </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4 col-md-4"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>trabajos" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Guardar"/>
			</div>
		</div>
		<!--<input type="hidden" id="claves" name="claves">-->
		<input type="hidden" id="campos_seleccionados" name="campos_seleccionados">
		<!--<input type="hidden" name="tree_json" id="tree_json">-->
	<?php echo form_close(); ?>
	<?php //$this->load->view('cms/modals'); ?>
<?php $this->load->view('cms/footer'); ?>