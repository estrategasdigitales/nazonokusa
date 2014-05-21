<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nucleo extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index() {

		$this->load->view('middleware/index');

	}

	public function detectar_campos() {

		$url = utf8_encode(file_get_contents($this->input->post('url')));
		$pos = strpos($url, '(');

		if($pos > -1 && $pos <20){
			$rest = substr($url, $pos+1, -1);
		}else{
			$rest = $url;
		}

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
			$xml=simplexml_load_file($this->input->post('url'));

			if($xml->channel->item){
				$rest=json_encode($xml);
				$campos_orig =json_decode($rest, TRUE);
			}else{
				$rest=json_encode($xml);
				$campos_orig =json_decode($rest, TRUE);
			}

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

	public function validar_form_trabajo(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'url-origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('destino-local', 'destino-local', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('destino-net', 'destino-net', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'categoria', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato_salida', 'formato_salida', 'required|xss_clean');

			if ($this->form_validation->run() === TRUE)
			{
				$trabajo['usuario'] 		= $this->session->userdata('uuid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				$trabajo['destino-local']   = $this->input->post('destino-local');
				$trabajo['destino-net']  	= $this->input->post('destino-net');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['formato_salida'] 	= $this->input->post('formato_salida');
				$trabajo['campos'] 			= $this->input->post('claves');

				foreach ($trabajo['campos']  as $key => $value) {
                    echo "$value <br>";
                }
				
				$url = utf8_encode(file_get_contents($this->input->post('url-origen')));
				$pos = strpos($url, '(');

				if($pos > -1 && $pos <20){
					$rest = substr($url, $pos+1, -1);
				}else{
					$rest = $url;
				}

				if($campos_orig =json_decode($rest, TRUE)){

					if(!empty($campos_orig[0])){
						for ($i=0; $i < count($campos_orig) ; $i++) {
							foreach ($campos_orig[$i] as $key => $value) {
								if(is_array($value)){
									if(!empty($campos[$key])){
										$campos[$key] = $this->arreglo_nuevo($value,$campos[$key]);
									}else{
										$campos[$key] = $this->arreglo_nuevo($value,$campos[$key]=[]);
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
									$campos[$key] = $this->arreglo_nuevo($value,$campos[$key]);
								}else{
									$campos[$key] = $this->arreglo_nuevo($value,$campos[$key]=[]);
								}
							}else{
								if(!array_key_exists($key, $campos)){
									$campos[$key]="";
								}
							}
						}
					}

				}else{

					$xml=simplexml_load_file($this->input->post('url'));

					if($xml->channel->item){
						$rest=json_encode($xml);
						$campos_orig =json_decode($rest, TRUE);
					}else{
						$rest=json_encode($xml);
						$campos_orig =json_decode($rest, TRUE);
					}

					foreach ($campos_orig as $key => $value) {
						
					}

				}

				$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba.php", "w");
				$cadena = $funcion.(json_encode($final)).")";
				$remplaza= stripslashes($cadena);
				fwrite($open, $remplaza);
				fclose($open);

				$guardar = $this->cms->add_trabajo($trabajo);
				if( $guardar !==false )
				{
					redirect('trabajos');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] = "El nuevo trabajo no pudo ser agregado";
					$this->load->view('cms/admin/nuevo_trabajo',$data);
				}				
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/nuevo_trabajo',$data);
			}
		}

	}

	function arreglo_nuevo($arreglo,$origin){
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				foreach ($arreglo[$i] as $key => $value) {
					if(is_array($value)){
						if(!empty($origin[$key])){								
							$origin[$key] = $this->arreglo_nuevo($value,$origin[$key]);
						}else{
							$origin[$key] = $this->arreglo_nuevo($value,$origin[$key]=[]);
						}

					}else{
						if(!array_key_exists($key, $origin)){
							$origin[$key]="";
						}
					}												
				}
			}
		}else{
			foreach ($arreglo as $key => $value) {
				if(is_array($value)){
					if(!empty($origin[$key])){								
						$origin[$key] = $this->arreglo_nuevo($value,$origin[$key]);
					}else{
						$origin[$key] = $this->arreglo_nuevo($value,$origin[$key]=[]);
					}
				}else{
					if(!array_key_exists($key, $origin)){
						$origin[$key]="";
					}
				}												
			}
		}
		return $origin;
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
							$origin[$key]="";
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
						$origin[$key]="";
					}
				}												
			}
		}
		return $origin;
	}

}