<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nucleo extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');	
	}

	public function index() {

		if ($this->session->userdata('session') !== TRUE) {
			redirect("login");
		} else {
			$data['usuario'] = $this->session->userdata('nombre');
			$this->load->view('middleware/index');
		}
	}

     


	public function detectar_campos() {

		$url = utf8_encode(file_get_contents($this->input->post('url')));
		$pos = strpos($url, '(');

		if($pos > -1 && (substr($url, -1)===")")){
			$rest = substr($url, $pos+1, -1); ?>
			<script>jQuery(document).ready(function($) {
				$('#tipo_archivo').html("Tipo de Archivo: JSONP");
			});</script>
		<?php }else{
			$rest = $url; ?>
			<script>jQuery(document).ready(function($) {
				$('#tipo_archivo').html("Tipo de Archivo: JSON");
			});</script>
		<?php }

		if($campos_orig =json_decode($rest, TRUE)){
			$campos=[];
			$cont=-1;
			$items = count($campos_orig);
//-------------------------------------------------------------- Parte para el Json y Json-P
			if(!empty($campos_orig[0])){
				for ($i=0; $i < count($campos_orig) ; $i++) {
					foreach ($campos_orig[$i] as $key => $value) {
						if(is_array($value)){
							if(!empty($campos[$key])){
								$campos[$key] = $this->claves($value,$campos[$key]);
							}else{
								$campos[$key] = $this->claves($value,$campos[$key]=[]);
							}
						}else{
							if(!array_key_exists($key, $campos)){
								$campos[$key]="";
							}
						}
					}
				}
			}else{
				foreach ($campos_orig as $key => $value) {
					if(is_array($value)){
						if(!empty($campos[$key])){
							$campos[$key] = $this->claves($value,$campos[$key]);
						}else{
							$campos[$key] = $this->claves($value,$campos[$key]=[]);
						}
					}else{
						if(!array_key_exists($key, $campos)){
							$campos[$key]="";
						}
					}
				}
			}
			
			foreach ($campos as $key => $value) {
				$cont++;
				if($cont%4===0){ ?>
					<div class="row"></div>
					<br>
				<?php }

				if(is_array($value)){ ?>
					<div class="col-sm-3 col-md-3">
						<div class="checkbox" id="<?php echo $key; ?>">
							<label>
								<input onchange="desplega(this);" type="checkbox" name="claves[]" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
							<?php $this->hijos($value,5,$key); ?>
						</div>
					</div>
				<?php }else{ ?>
					<div class="col-sm-3 col-md-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="claves[]" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
				<?php }
			}
		}else{
//-------------------------------------------------------------- Parte para el XML y RSS 2.0
			$xml = simplexml_load_file($this->input->post('url'));
			if($xml->channel){
				$rest=json_encode($xml);
				$campos_orig =json_decode($rest, TRUE); ?>
				<script>jQuery(document).ready(function($) {
				$('#tipo_archivo').html("Tipo de Archivo: RSS 2.0");
			});</script>
			<?php }else{
				$rest=json_encode($xml);
				$campos_orig =json_decode($rest, TRUE); ?>
				<script>jQuery(document).ready(function($) {
				$('#tipo_archivo').html("Tipo de Archivo: XML");
			});</script>
			<?php }
			$campos=[];
			$cont=-1;
			$items = count($campos_orig);
			if(!empty($campos_orig[0])){
				for ($i=0; $i < count($campos_orig) ; $i++) {
					foreach ($campos_orig[$i] as $key => $value) {
						if(is_array($value)){
							if(!empty($campos[$key])){
								$campos[$key] = $this->claves($value,$campos[$key]);
							}else{
								$campos[$key] = $this->claves($value,$campos[$key]=[]);
							}
						}else{
							if(!array_key_exists($key, $campos)){
								$campos[$key]="";
							}
						}
					}
				}
			}else{
				foreach ($campos_orig as $key => $value) {
					if(is_array($value)){
						if(!empty($campos[$key])){
							$campos[$key] = $this->claves($value,$campos[$key]);
						}else{
							$campos[$key] = $this->claves($value,$campos[$key]=[]);
						}
					}else{
						if(!array_key_exists($key, $campos)){
							$campos[$key]="";
						}
					}
				}
			}
			
			foreach ($campos as $key => $value) {
				$cont++;

				if($cont%4===0){ ?>
					<div class="row"></div>
					<br>
				<?php }

				if(is_array($value)){ ?>
					<div class="col-sm-3 col-md-3">
						<div class="checkbox" id="<?php echo $key; ?>">
							<label>
								<input onchange="desplega(this);" type="checkbox" name="claves[]" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
							<?php $this->hijos($value,5,$key); ?>
						</div>
					</div>
				<?php }else{ ?>
					<div class="col-sm-3 col-md-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="claves[]" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
				<?php }
			}
		}

	}


	/* 28/5 */ 
	public function form_editar_trabajo($uuid_trabajo){
    	if($this->session->userdata('session') !== TRUE){
    		redirect('login');
    	}else {
    	    $trabajo = $this->cms->get_trabajo_editar($uuid_trabajo);

            if($trabajo !== false){
            	$data['usuario']    = $this->session->userdata('nombre');
				$data['categorias'] = $this->cms->get_categorias();
				$data['verticales'] = $this->cms->get_verticales();
          		$data['trabajo_editar'] = $trabajo[0];
          		$data['cron_date'] = json_decode($trabajo[0]['cron_config'],true);
              	$this->load->view('cms/admin/editar_trabajo',$data);
			}else{
               	$data['error'] = "Ha ocurrido un error con el trabajo";
               	$this->load->view('cms/admin/editar_trabajo',$data);
			}
    	}
	}



	public function eliminar_trabajo($uuid_trabajo){
		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$trabajo = $this->cms->get_trabajo_editar($uuid_trabajo);
			$cronjob = json_decode($trabajo[0]['cron_config'],true);
			$api_delete = $this->cms->delete_cronjob($cronjob['id']);
			$eliminar = $this->cms->delete_trabajo($uuid_trabajo);
			if( $eliminar !== false && $api_delete !== false)
			{
				redirect('trabajos');
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] = "No se a podido eliminar el trabajo";
				$this->load->view('cms/admin/trabajos',$data);
			}
		}

	}

    
    public function ejecutar_trabajo($uuid_trabajo){
    	$trabajo = $this->cms->get_trabajo_editar($uuid_trabajo);
    	$elegidos=[];
    	$formatos=[];

        $cf = json_decode($trabajo[0]['formato_salida'], true);
        


		foreach ($cf['campos']  as $key => $value) { $elegidos[]=explode(",",$value); }
		foreach ($cf['formatos']  as $key => $value) {	$formatos[]=$value; }

    	if($trabajo !== false) {

    		

    		$indice=0;
			$url = utf8_encode(file_get_contents($trabajo[0]['url_origen']));
			$pos = strpos($url, '(');
            $rest = ($pos > -1 && (substr($url, -1)===")"))? substr($url, $pos+1, -1) : $url;
					
			if($campos_orig =json_decode($rest, TRUE)){
				if(!empty($campos_orig[0])){
					for ($i=0; $i < count($campos_orig) ; $i++) {
						foreach ($campos_orig[$i] as $key => $value) {
							for ($j=0; $j < count($elegidos) ; $j++) {
								$tmp=0;
								if(count($elegidos[$j])>$indice){
									if($elegidos[$j][$indice] === (string)$key){ $tmp++; break; }
								}
							}								
							if($tmp>0){
								if(is_array($value)){ $campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1); }
			     			}else{ unset($campos_orig[$i][$key]); }
						}
					}
				}else{
					foreach ($campos_orig as $key => $value) {
						for ($j=0; $j < count($elegidos) ; $j++) {
							$tmp=0;
							if(count($elegidos[$j])>$indice){
								if($elegidos[$j][$indice] === (string)$key){ $tmp = $tmp + 1; break; }
							}
						}								
						if($tmp>0){
							if(is_array($value)){ $campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1); }
						}else{ unset($campos_orig[$key]); }
					}
				}
			}else{
				$xml=simplexml_load_file($trabajo[0]['url_origen']);
				if($xml->channel){
					$rest=json_encode($xml);
					$campos_orig =json_decode($rest, TRUE);
				}else{
					$rest=json_encode($xml);
					$campos_orig =json_decode($rest, TRUE);
				}
				if(!empty($campos_orig[0])){
					for ($i=0; $i < count($campos_orig) ; $i++) {
						foreach ($campos_orig[$i] as $key => $value) {
							for ($j=0; $j < count($elegidos) ; $j++) {
								$tmp=0;
								if(count($elegidos[$j])>$indice){
									if($elegidos[$j][$indice] === (string)$key){
										$tmp = $tmp + 1;
										break;
									}
								}
							}								
							if($tmp>0){
								if(is_array($value)){
									$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
								}
							}else{
								unset($campos_orig[$i][$key]);										
							}
						}
					}
				}else{
					foreach ($campos_orig as $key => $value) {
						for ($j=0; $j < count($elegidos) ; $j++) {
							$tmp=0;
							if(count($elegidos[$j])>$indice){
								if($elegidos[$j][$indice] === (string)$key){
									$tmp = $tmp + 1;
									break;
								}
							}
						}								
						if($tmp>0){
							if(is_array($value)){ $campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1); }
						}else{ unset($campos_orig[$key]);	}
					}
				}
			}
			for ($i=0; $i < count($formatos) ; $i++) {
				if($formatos[$i]==='xml'){ $this->convert_xml($campos_orig); }
				if($formatos[$i]==='rss2'){ $this->convert_rss($campos_orig,$trabajo['claves_rss'],$trabajo['valores_rss']); }
				if($formatos[$i]==='json'){ $this->convert_json($campos_orig); }
				if($formatos[$i]==='jsonp'){ $this->convert_jsonp($campos_orig,$this->input->post('nom_funcion')); }
			}
    	redirect('trabajos');	
           
    	}else return false;
        
		

    }


	public function validar_form_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$this->form_validation->set_rules('nombre', 'nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'url-origen', 'required|min_length[3]|xss_clean');
			// $this->form_validation->set_rules('destino-local', 'destino-local', 'min_length[3]|xss_clean');
			// $this->form_validation->set_rules('destino-net', 'destino-net', 'min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'categoria', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'formato', 'required|xss_clean');

			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 		= $this->session->userdata('uuid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				// $trabajo['destino-local']   = $this->input->post('destino-local');
				// $trabajo['destino-net']  	= $this->input->post('destino-net');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['campos'] 			= $this->input->post('claves');
				
				$formats['campos'] = $this->input->post('claves'); 
                $formats['formatos'] = $this->input->post('formato'); 
                $formats['valores_rss'] = $this->input->post('valores_rss'); 
                $formats['claves_rss'] = $this->input->post('claves_rss'); 
                
                $trabajo['formato_salida'] = json_encode($formats);

				$elegidos=[];
				foreach ($trabajo['campos']  as $key => $value) {
					$elegidos[]=explode(",",$value);
				}
				$formatos=[];
				foreach ($formats['formatos']  as $key => $value) {
					$formatos[]=$value;
				}

                $cron_date['mes'] = ($this->input->post('cron_mes')!=="*") ? (int)$this->input->post('cron_mes'):"*";
                $cron_date['hora'] =  ($this->input->post('cron_hora')!=="*") ? (int)$this->input->post('cron_hora') : "*";
                $cron_date['minuto'] = (is_string($this->input->post('cron_minuto')) && $this->input->post('cron_minuto') != "0" ) ? $this->input->post('cron_minuto') : (int)$this->input->post('cron_minuto') ;
                if((int)$this->input->post('cron_diames')){
                	$cron_date['diames'] = (int)$this->input->post('cron_diames');
                	$cron_date['diasemana'] = "*";
                }else{ 
                	$cron_date['diames'] = "*";
                	$cron_date['diasemana'] = (int)$this->input->post('cron_diasemana');
                }
                
				//Cron date config 
                $trabajo['cron_date'] = json_encode($cron_date);
                $trabajo['cron_expression'] = $cron_date['minuto']." ".$cron_date['hora']." ".$cron_date['diames']." ".$cron_date['mes']." ".$cron_date['diasemana'];
                
                //Trabajo id (cronjob's name)
				$guardar = $this->cms->add_trabajo($trabajo);

               	$trabajo['cron_url'] = site_url("")."ejecutar_trabajo/".$guardar;
                
                //Set cronjob in easycron
                $cron_result = $this->cms->set_cronjob($guardar, $trabajo['cron_expression'], $trabajo['cron_url']);
                if($cron_result["status"] === "success"){ 
                	$cron_date["id"] = $cron_result["cron_job_id"];
                	$saveId = $this->cms->save_cronconfig($guardar,json_encode($cron_date));
                } 
                
				if( $guardar == FALSE ){
					echo 'x02 - La información del trabajo no puedo ser actualizada';
				} else {	
					$indice=0;
					$url = utf8_encode( file_get_contents( $this->input->post( 'url-origen' ) ) );
					$pos = strpos($url, '(');
						if($pos > -1 && (substr($url, -1)===")")){
							$rest = substr($url, $pos+1, -1);
						}else{
							$rest = $url;
						}
						if($campos_orig = json_decode($rest, TRUE)){
							if(!empty($campos_orig[0])){
								for ($i=0; $i < count($campos_orig) ; $i++) {
									foreach ($campos_orig[$i] as $key => $value) {
										for ($j=0; $j < count($elegidos) ; $j++) {
											$tmp=0;
											if(count($elegidos[$j])>$indice){
												if($elegidos[$j][$indice] === (string)$key){
													$tmp = $tmp + 1;
													break;
												}
											}
										}								
										if($tmp>0){
											if(is_array($value)){
												$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
											}
										}else{
											unset($campos_orig[$i][$key]);										
										}
									}
								}
							}else{
								foreach ($campos_orig as $key => $value) {
									for ($j=0; $j < count($elegidos) ; $j++) {
										$tmp=0;
										if(count($elegidos[$j])>$indice){
											if($elegidos[$j][$indice] === (string)$key){
												$tmp = $tmp + 1;
												break;
											}
										}
									}								
									if($tmp>0){
										if(is_array($value)){
											$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
										}
									}else{
										unset($campos_orig[$key]);										
									}
								}
							}
						}else{
							$xml=simplexml_load_file($this->input->post('url-origen'));
							if($xml->channel){
								$rest=json_encode($xml);
								$campos_orig =json_decode($rest, TRUE);
							}else{
								$rest=json_encode($xml);
								$campos_orig =json_decode($rest, TRUE);
							}
							if(!empty($campos_orig[0])){
								for ($i=0; $i < count($campos_orig) ; $i++) {
									foreach ($campos_orig[$i] as $key => $value) {
										for ($j=0; $j < count($elegidos) ; $j++) {
											$tmp=0;
											if(count($elegidos[$j])>$indice){
												if($elegidos[$j][$indice] === (string)$key){
													$tmp = $tmp + 1;
													break;
												}
											}
										}								
										if($tmp>0){
											if(is_array($value)){
												$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
											}
										}else{
											unset($campos_orig[$i][$key]);										
										}
									}
								}
							}else{
								foreach ($campos_orig as $key => $value) {
									for ($j=0; $j < count($elegidos) ; $j++) {
										$tmp=0;
										if(count($elegidos[$j])>$indice){
											if($elegidos[$j][$indice] === (string)$key){
												$tmp = $tmp + 1;
												break;
											}
										}
									}								
									if($tmp>0){
										if(is_array($value)){
											$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
										}
									}else{
										unset($campos_orig[$key]);										
									}
								}
							}
						}
						
						for ($i=0; $i < count($formatos) ; $i++) {
							if($formatos[$i]==='xml'){
								$this->convert_xml($campos_orig, strtolower( url_title( $trabajo['nombre'] ) ));
							}
							if($formatos[$i]==='rss2'){
								$this->convert_rss($campos_orig,strtolower( url_title( $trabajo['nombre'] ) ), $trabajo['claves_rss'], $trabajo['valores_rss']);
							}
							if($formatos[$i]==='json'){
								$this->convert_json($campos_orig, strtolower( url_title( $trabajo['nombre'] ) ));
							}
							if($formatos[$i]==='jsonp'){
								$this->convert_jsonp($campos_orig,strtolower( url_title( $trabajo['nombre'] ) ), $this->input->post('nom_funcion'));
							}
						}
				}
				echo TRUE;
			} else {
					echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	public function validar_form_editar_trabajo(){


		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'url-origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('destino-local', 'destino-local', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('destino-net', 'destino-net', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'categoria', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'formato', 'required|xss_clean');

			if ($this->form_validation->run() === TRUE)
			{
				$trabajo['usuario'] 		= $this->session->userdata('uuid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				$trabajo['destino-local']   = $this->input->post('destino-local');
				$trabajo['destino-net']  	= $this->input->post('destino-net');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['campos'] 			= $this->input->post('claves');
				$trabajo['uuid_trabajo']    = $this->input->post('id_trabajo');
				
				$formats['campos'] = $this->input->post('claves'); 
                $formats['formatos'] = $this->input->post('formato'); 
                $formats['valores_rss'] = $this->input->post('valores_rss'); 
                $formats['claves_rss'] = $this->input->post('claves_rss'); 
                
                $trabajo['formato_salida'] = json_encode($formats);

				$elegidos=[];
				foreach ($trabajo['campos']  as $key => $value) {
					$elegidos[]=explode(",",$value);
				}
				$formatos=[];
				foreach ($formats['formatos']  as $key => $value) {
					$formatos[]=$value;
				}

                $cron_date['mes'] = ($this->input->post('cron_mes')!=="*") ? (int)$this->input->post('cron_mes'):"*";
                $cron_date['hora'] =  ($this->input->post('cron_hora')!=="*") ? (int)$this->input->post('cron_hora') : "*";
                $cron_date['minuto'] = (is_string($this->input->post('cron_minuto')) && $this->input->post('cron_minuto') != "0" ) ? $this->input->post('cron_minuto') : (int)$this->input->post('cron_minuto') ;
                if((int)$this->input->post('cron_diames')){
                	$cron_date['diames'] = (int)$this->input->post('cron_diames');
                	$cron_date['diasemana'] = "*";
                }else{ 
                	$cron_date['diames'] = "*";
                	$cron_date['diasemana'] = (int)$this->input->post('cron_diasemana');
                }
                
                

                
                //Cron date config 
                $trabajo['cron_date'] = json_encode($cron_date);
                $trabajo['cron_expression'] = $cron_date['minuto']." ".$cron_date['hora']." ".$cron_date['diames']." ".$cron_date['mes']." ".$cron_date['diasemana'];
                
                //Trabajo id (cronjob's name)
				$guardar = $this->cms->update_trabajo($trabajo);


               	 $trabajo['cron_url'] = site_url("")."ejecutar_trabajo/".$guardar;
                
                //Set cronjob in easycron
                $cron_result = $this->cms->set_cronjob($guardar, $trabajo['cron_expression'], $trabajo['cron_url']);
                if($cron_result["status"] === "success"){ 

                	$cron_date["id"] = $cron_result["cron_job_id"];
                	$saveId = $this->cms->save_cronconfig($guardar,json_encode($cron_date));


                } 
                
				if( $guardar == false ){
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
					$this->load->view('cms/admin/nuevo_trabajo',$data);
				}else{	
					$indice=0;
					$url = utf8_encode(file_get_contents($this->input->post('url-origen')));
					$pos = strpos($url, '(');


						if($pos > -1 && (substr($url, -1)===")")){
							$rest = substr($url, $pos+1, -1);
						}else{
							$rest = $url;
						}

						


						if($campos_orig =json_decode($rest, TRUE)){
							if(!empty($campos_orig[0])){
								for ($i=0; $i < count($campos_orig) ; $i++) {
									foreach ($campos_orig[$i] as $key => $value) {
										for ($j=0; $j < count($elegidos) ; $j++) {
											$tmp=0;
											if(count($elegidos[$j])>$indice){
												if($elegidos[$j][$indice] === (string)$key){
													$tmp = $tmp + 1;
													break;
												}
											}
										}								
										if($tmp>0){
											if(is_array($value)){
												$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
											}
										}else{
											unset($campos_orig[$i][$key]);										
										}
									}
								}
							}else{
								foreach ($campos_orig as $key => $value) {
									for ($j=0; $j < count($elegidos) ; $j++) {
										$tmp=0;
										if(count($elegidos[$j])>$indice){
											if($elegidos[$j][$indice] === (string)$key){
												$tmp = $tmp + 1;
												break;
											}
										}
									}								
									if($tmp>0){
										if(is_array($value)){
											$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
										}
									}else{
										unset($campos_orig[$key]);										
									}
								}
							}

						}else{

							$xml=simplexml_load_file($this->input->post('url-origen'));

							if($xml->channel){
								$rest=json_encode($xml);
								$campos_orig =json_decode($rest, TRUE);
							}else{
								$rest=json_encode($xml);
								$campos_orig =json_decode($rest, TRUE);
							}

							if(!empty($campos_orig[0])){
								for ($i=0; $i < count($campos_orig) ; $i++) {
									foreach ($campos_orig[$i] as $key => $value) {
										for ($j=0; $j < count($elegidos) ; $j++) {
											$tmp=0;
											if(count($elegidos[$j])>$indice){
												if($elegidos[$j][$indice] === (string)$key){
													$tmp = $tmp + 1;
													break;
												}
											}
										}								
										if($tmp>0){
											if(is_array($value)){
												$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
											}
										}else{
											unset($campos_orig[$i][$key]);										
										}
									}
								}
							}else{
								foreach ($campos_orig as $key => $value) {
									for ($j=0; $j < count($elegidos) ; $j++) {
										$tmp=0;
										if(count($elegidos[$j])>$indice){
											if($elegidos[$j][$indice] === (string)$key){
												$tmp = $tmp + 1;
												break;
											}
										}
									}								
									if($tmp>0){
										if(is_array($value)){
											$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
										}
									}else{
										unset($campos_orig[$key]);										
									}
								}
							}

						}
						
						for ($i=0; $i < count($formatos) ; $i++) {
							if($formatos[$i]==='xml'){
								$this->convert_xml( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ) );
							}
							if($formatos[$i]==='rss2'){
								$this->convert_rss( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ), $trabajo['claves_rss'], $trabajo['valores_rss'] );
							}
							if($formatos[$i]==='json'){
								$this->convert_json( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ) );
							}
							if($formatos[$i]==='jsonp'){
								$this->convert_jsonp( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ), $this->input->post( 'nom_funcion' ) );
							}
						}


                 }

			     redirect('trabajos');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
					$this->load->view('cms/admin/nuevo_trabajo',$data);
				}
			}

		}


	function arreglo_nuevo($arreglo,$elegidos,$indice){

		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				foreach ($arreglo[$i] as $key => $value) {
					for ($j=0; $j < count($elegidos) ; $j++) {
						$tmp=0;
						if(count($elegidos[$j])>$indice){
							if($elegidos[$j][$indice] === (string)$key){
								$tmp = $tmp + 1;
								break;
							}
						}
					}								
					if($tmp>0){
						if(is_array($value)){
							$arreglo[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
						}
					}else{
						unset($arreglo[$i][$key]);										
					}
				}
			}
		}else{
			foreach ($arreglo as $key => $value) {
				for ($j=0; $j < count($elegidos) ; $j++) {
					$tmp=0;
					if(count($elegidos[$j])>$indice){
						if($elegidos[$j][$indice] === (string)$key){
							$tmp = $tmp + 1;
							break;
						}
					}
				}								
				if($tmp>0){
					if(is_array($value)){
						$arreglo[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
					}
				}else{
					unset($arreglo[$key]);										
				}
			}
		}
		return $arreglo;

	}

	function hijos($arreglo,$espacio,$clave){

		foreach ($arreglo as $key => $value) {
			if(is_array($value)){ ?>
			<div id="<?php echo $key; ?>" style="display:none; margin-top:8px; margin-left:<?php echo ($espacio+20)."px"; ?>">
				<label>
					<input onchange="desplega(this);" type="checkbox" name="claves[]" value="<?php echo $clave.",".$key; ?>">
					<?php echo $key; ?>
				</label>	
				<?php $this->hijos($value,$espacio,$clave.",".$key); ?>
			</div>	
			<?php }else{ ?>
			<div class="checkbox" style="display:none; margin-top:8px; margin-left:<?php echo $espacio."px"; ?>">
				<label>
					<input type="checkbox" name="claves[]" value="<?php echo $clave.",".$key; ?>">
					<?php echo $key; ?>
				</label>
			</div>	
			<?php }
		}

	}

	function claves($arreglo,$origin){

		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						if(!empty($origin[$key])){
							$origin[$key] = $this->claves($value,$origin[$key]);
						}else{
							$origin[$key] = $this->claves($value,$origin[$key]=[]);
						}
					}else{
						if(!array_key_exists($key, $origin)){
							$origin[$key] = "";
						}
					}												
				}
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					if(!empty($origin[$key])){
						$origin[$key] = $this->claves($value,$origin[$key]);
					}else{
						$origin[$key] = $this->claves($value,$origin[$key]=[]);
					}
				}else{
					if(!array_key_exists($key, $origin)){
						$origin[$key] = "";
					}
				}												
			}
		}
		return $origin;

	}

	function convert_xml($arreglo, $nombre){

		$open = fopen( "./outputs/". $nombre ."xml.xml", "w");
		$cabeceras ="<?xml version='1.0' encoding='utf-8' ?>\n";
		fwrite($open, $cabeceras);
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				fwrite($open,"\n<elemento>");
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						fwrite($open, "\n<".$key.">".$this->formato_xml($value)."</".$key.">");
					}else{
						fwrite($open, "\n<".$key.">".$value."</".$key.">");
					}
				}
				fwrite($open,"\n</elemento>");
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					fwrite($open, "\n<".$key.">".$this->formato_xml($value)."</".$key.">");
				}else{
					fwrite($open, "\n<".$key.">".$value."</".$key.">");
				}															
			}
		}
		fclose($open);

	}

	function formato_xml($arreglo){
		$etiquetas = "";
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				$etiquetas.= "\n<elemento>";
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						$etiquetas.="\n<".$key.">".$this->formato_xml($value)."</".$key.">";
					}else{
						$etiquetas.="\n<".$key.">".$value."</".$key.">";
					}
				}
				$etiquetas.="\n</elemento>\n";
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					$etiquetas.="\n<".$key.">".$this->formato_xml($value)."</".$key.">";
				}else{
					$etiquetas.="\n<".$key.">".$value."</".$key.">";
				}															
			}
		}
		return $etiquetas;
	}

	function convert_rss($arreglo, $nombre, $nodos, $valores){
		$open = fopen( "./outputs/". $nombre ."rss.xml", "w");
		$cabeceras ="<?xml version='1.0' encoding='utf-8' ?>\n<rss version='2.0'>\n<channel>\n";
		fwrite($open, $cabeceras);
		for ($i=0; $i < count($nodos) ; $i++) {
			fwrite($open,"\n<".$nodos[$i].">".$valores[$i]."</".$nodos[$i].">");
		}
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				fwrite($open,"\n<item>");
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						fwrite($open, "\n<".$key.">".$this->formato_rss($value)."</".$key.">");
					}else{
						fwrite($open, "\n<".$key.">".$value."</".$key.">");
					}
				}
				fwrite($open,"\n</item>");
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					fwrite($open, "\n<".$key.">".$this->formato_rss($value)."</".$key.">");
				}else{
					fwrite($open, "\n<".$key.">".$value."</".$key.">");
				}															
			}
		}
		$cierre = "\n</channel>\n</rss>";
		fwrite($open, $cierre);
		fclose($open);

	}

	function formato_rss($arreglo){
		$etiquetas = "";
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				$etiquetas.="\n<item>";
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						$etiquetas.="\n<".$key.">".$this->formato_rss($value)."</".$key.">";
					}else{
						$etiquetas.="\n<".$key.">".$value."</".$key.">";
					}
				}
				$etiquetas.="\n</item>\n";
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					$etiquetas.="\n<".$key.">".$this->formato_rss($value)."</".$key.">";
				}else{
					$etiquetas.="\n<".$key.">".$value."</".$key.">";
				}															
			}
		}
		return $etiquetas;
	}

	function convert_json($arreglo, $nombre){

		$open = fopen( "./outputs/". $nombre ."json.js", "w");
		$final= json_encode($arreglo);
		fwrite($open, stripslashes($final));
		fclose($open);

	}

	function convert_jsonp($arreglo, $nombre, $funcion){

		$open = fopen( "./outputs/". $nombre ."jsonp.js", "w");
		$final= $funcion."(".json_encode($arreglo).")";
		fwrite($open, stripslashes($final));
		fclose($open);

	}

}