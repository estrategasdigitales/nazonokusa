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

	public function validar_form_trabajo(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			//$this->form_validation->set_rules('nombre', 'nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'url-origen', 'required|min_length[3]|xss_clean');
			//$this->form_validation->set_rules('destino-local', 'destino-local', 'required|min_length[3]|xss_clean');
			//$this->form_validation->set_rules('destino-net', 'destino-net', 'required|min_length[3]|xss_clean');
			//$this->form_validation->set_rules('categoria', 'categoria', 'required|xss_clean');
			//$this->form_validation->set_rules('vertical', 'vertical', 'required|xss_clean');
			//$this->form_validation->set_rules('formato_salida', 'formato_salida', 'required|xss_clean');

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
				$trabajo['formatos'] 		= $this->input->post('formato');
				$trabajo['valores_rss']		= $this->input->post('valores_rss');
				$trabajo['claves_rss']		= $this->input->post('claves_rss');

				$elegidos=[];
				foreach ($trabajo['campos']  as $key => $value) {
					$elegidos[]=explode(",",$value);
				}
				$formatos=[];
				foreach ($trabajo['formatos']  as $key => $value) {
					$formatos[]=$value;
				}

				/*$guardar = $this->cms->add_trabajo($trabajo);
				if( $guardar !==false )
				{*/
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
								$this->convert_xml($campos_orig);
							}
							if($formatos[$i]==='rss2'){
								$this->convert_rss($campos_orig,$trabajo['claves_rss'],$trabajo['valores_rss']);
							}
							if($formatos[$i]==='json'){
								$this->convert_json($campos_orig);
							}
							if($formatos[$i]==='jsonp'){
								$this->convert_jsonp($campos_orig,$this->input->post('nom_funcion'));
							}
						}

						/*redirect('trabajos');
					}
					else
					{
						$data['usuario'] 	= $this->session->userdata('nombre');
						$data['error'] = "El nuevo trabajo no pudo ser agregado";
						$this->load->view('cms/admin/nuevo_trabajo',$data);
					}	*/			
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

	function convert_xml($arreglo){

		$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba_xml.xml", "w");
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
		$etiquetas="";
		if(!empty($arreglo[0])){
			for ($i=0; $i < count($arreglo) ; $i++) {
				$etiquetas.="\n<elemento>";
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

	function convert_rss($arreglo,$nodos,$valores){
		$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba_rss.xml", "w");
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
		$etiquetas="";
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

	function convert_json($arreglo){

		$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba_json.js", "w");
		$final= json_encode($arreglo);
		fwrite($open, stripslashes($final));
		fclose($open);

	}

	function convert_jsonp($arreglo,$funcion){

		$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba_jsonp.js", "w");
		$final= $funcion."(".json_encode($arreglo).")";
		fwrite($open, stripslashes($final));
		fclose($open);

	}

}