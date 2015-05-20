<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
//
// CAMPOS
//
$campos_seleccionados = base64_decode($trabajo->campos_seleccionados);
$campos_seleccionados = json_decode($campos_seleccionados);
$feed1 = [];

foreach ($campos_seleccionados as $feeds) 
{
	if(isset($feeds->feed1))
		$feed1[] = $feeds->feed1;
}

$feed1 = json_encode($feed1);


//
// CRON
//

$cron_config = $trabajo->cron_config;
$cron_config = explode(" ", $cron_config);

$cron_minuto 	= $cron_config[0];
$cron_hora 	 	= $cron_config[1];
$cron_diames 	= $cron_config[2];
$cron_mes 	 	= $cron_config[3]; //
$cron_diasemana = $cron_config[4];

?>
<?php $this->load->view('cms/header'); ?>
	<?php echo form_open( 'nucleo/editar_form_trabajo/'.$uid_trabajo, array('class' => 'form-horizontal', 'id' => 'form_trabajo_nuevo', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Editar Trabajo</h4></div>
		</div>
		<div class="container row">
			<div class="panel panel-primary">
				<div class="panel-heading">Datos del Trabajo</div>
				<div class="panel-body">
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label for="nombre" class="col-sm-3 col-md-2 control-label">Nombre</label>
							<div class="col-sm-9 col-md-10">
								<span class="info-value"><?=$trabajo->nombre?></span>
							</div>
						</div>
						<div class="form-group">
							<label for="url-origen" class="col-sm-3 col-md-2 control-label">URL de origen</label>
							<div class="col-sm-9 col-md-10">
								<a href="<?=$trabajo->url_origen?>" target="_blank"><?=$trabajo->url_origen?></a>
								<input type="hidden" value="<?=$trabajo->url_origen?>" id="url-origen" name="url-origen">
							</div>
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						<div class="form-group">
							<label class="col-sm-3 col-md-2 control-label">Categoría</label>
							
								<div class="col-sm-9 col-md-10">
									<span class="info-value"><?=$trabajo->categoria?></span>
								</div>

						</div>
						<div class="form-group">
							<label class="col-sm-3 col-md-2 control-label">Vertical</label>
							
								<div class="col-sm-9 col-md-10">
									<span class="info-value"><?=$trabajo->vertical?></span>
								</div>

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
							<input type="hidden" value="1" id="tipo_salida" name="tipo_salida">
							<span class="info-value">Salida estándar</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="formatos_estandar">

			<div class="container row">
				<div class="panel panel-primary">
					<div class="panel-heading">Formatos estándar</div>
					<div class="panel-body">
						<?php
							foreach ($formatos_salida as $formato) {
						?>
							<div class="col-sm-3 col-md-3">
								<div class="form-group">
									<div class="checkbox">
										<label>
											<?php 
											 $json_formato = $formato->formato;
											 $array_formato = json_decode($json_formato);
											 echo strtoupper($array_formato->format);
											?>
										</label>
									</div>

								</div>
							</div>

						<?php		
							}
						?>


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
								<?php
									$meses = [
												"*" => "Cada mes",
												"1"	=> "Cada Enero",
												"2"	=> "Cada Febrero",
												"3"	=> "Cada Marzo",
												"4"	=> "Cada Abril",
												"5" => "Cada Mayo",
												"6" => "Cada Junio",
												"7" => "Cada Julio",
												"8" => "Cada Agosto",
												"9" => "Cada Septiembre",
												"10"=> "Cada Octubre",
												"11"=> "Cada Noviembre",
												"12"=> "Cada Diciembre",
											];

									foreach ($meses as $key => $value) {
										if($key == $cron_mes)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
									}		
								?>

								

							</select>
						</div>
						<div class="col-sm-4 col-md-4">
							<label for="cron_diasemana" class="form-trabajos-date2"> D&iacute;a(s) de la semana: </label>
							<select class="form-control form-trabajos-date2" name="cron_diasemana">

								<?php

									$dias = [
												"*" => "Todos los d&iacute;as",
												"0"	=> "Cada Domingo",
												"1"	=> "Cada Lunes",
												"2"	=> "Cada Martes",
												"3"	=> "Cada Mi&eacute;rcoles",
												"4" => "Cada Jueves",
												"5" => "Cada Viernees",
												"6" => "Cada S&aacute;bado"
											];

									foreach ($dias as $key => $value) {

										if($key === $cron_diasemana)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
									}		
								?>
							</select>
						</div>
						<div class="col-sm-4 col-md-4">	
							<label for="cron_diames" class="form-trabajos-label0">D&iacute;a del mes: </label>
							<select class="form-control form-trabajos-date" name="cron_diames">

								<?php

									for ($i = 0; $i <= 31; $i++){

										$value = $i;
										$key   = $i;

										if($key == 0)
										{
											$key   = "*";
											$value = "Todos los d&iacute;as";
										}
											

										if($key === $cron_diames)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
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

									$dias = [
												"*" => "Cada hr.",
												"*/2"	=> "Cada 2hrs.",
												"*/6"	=> "Cada 6hrs.",
												"*/12"	=> "Cada 12hrs."
											];

									foreach ($dias as $key => $value) {

										if($key === $cron_hora)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
									}	


									for ($i = 1; $i <= 23; $i++){

										$key 	= $i;
										$value  = $i;
										if(strlen($value) == 1)
											$value = "0".$value;

										if($key === $cron_hora)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
									}	
								?>



							</select>
							<select class="form-control form-trabajos-hora" name="cron_minuto">


		
								<?php

									$dias = [
												"*/5" 	=> "Cada 5 mins",
												"*/10"	=> "Cada 10 mins",
												"*/15"	=> "Cada 15 mins",
												"*/30"	=> "Cada 30 mins"
											];

									foreach ($dias as $key => $value) {

										if($key === $cron_minuto)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
									}	


									for ($i = 0; $i <= 59; $i++){

										$key 	= $i;
										$value  = $i;
										if(strlen($value) == 1)
											$value = "0".$value;

										if($key === $cron_minuto)
											echo '<option selected value="'.$key.'">'.$value.'</option>';
										else
											echo '<option value="'.$key.'">'.$value.'</option>';
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
			<div class="col-sm-4 col-md-4"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>trabajos" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Editar"/>
			</div>
		</div>
		<!--<input type="hidden" id="claves" name="claves">-->
		<input type="hidden" id="campos_seleccionados" name="campos_seleccionados">
		<!--<input type="hidden" name="tree_json" id="tree_json">-->



		<script type="text/javascript">
			cargar_campos_estandar();
			checkeds = '<?php echo $feed1?>';
		</script>

	<?php echo form_close(); ?>
	<?php //$this->load->view('cms/modals'); ?>
<?php $this->load->view('cms/footer'); ?>