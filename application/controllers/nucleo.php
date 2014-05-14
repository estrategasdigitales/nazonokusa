<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nucleo extends CI_Controller {

	private $numeric;
	public function __construct(){
		parent::__construct();
		$this->numeric = [];
		$this->numeric[0] = "cero";
		$this->numeric[1] = "uno";
		$this->numeric[2] = "dos";
		$this->numeric[3] = "tres";
		$this->numeric[4] = "cuatro";
		$this->numeric[5] = "cinco";
		$this->numeric[6] = "seis";
		$this->numeric[7] = "siete";
		$this->numeric[8] = "ocho";
		$this->numeric[9] = "nueve";
		$this->numeric[10] = "diez";
		$this->numeric[11] = "once";
		$this->numeric[12] = "doce";
		$this->numeric[13] = "trece";
		$this->numeric[14] = "catorce";
		$this->numeric[15] = "quince";
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
			if($jsonIterator =json_decode($rest, TRUE)){
				$campos=[];
				$offset=0;
				$col=12;
				$cont=-1;
				$items = count($jsonIterator);
				//-------------------------------------------------------------- Parte para el Json y Json-P
				if($items>1){
					for ($i=0; $i < $items ; $i++) {
						$campos = array_merge_recursive($campos, $jsonIterator[$i]);
					}
					for ($i=0; $i < $items ; $i++) {
						foreach ($jsonIterator[$i] as $key => $value) {
							if(is_array($value)){
								$campos[$key]=$this->limpiar($value,$campos[$key]);
							}else{
								$campos[$key]=$value;
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
										<input onchange="desplega(this);" type="checkbox" name="clave[]" value="<?php echo $key; ?>">
										<?php echo $key; ?>
									</label>	
									<?php $this->hijos($value,5); ?>
								</div>
							</div>
						<?php }else{ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="arreglo[]" value="<?php echo $value; ?>">
										<?php echo $key; ?>
									</label>
								</div>
							</div>
						<?php }
					}
				}else{
					foreach ($jsonIterator as $key => $value) {
						$cont++;
						if($cont%4===0){ ?>
							<div class="row"></div>
							<br>
						<?php }
						if(is_array($value)){ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox" id="<?php echo $key; ?>">
									<label>
										<input onchange="desplega(this);" type="checkbox" name="clave[]" value="<?php echo $key; ?>">
										<?php if(is_numeric($key)) echo $this->numeric[$key]; else echo $key; ?>
									</label>	
									<?php $this->hijos($value,5); ?>
								</div>
							</div>
						<?php }else{ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="arreglo[]" value="<?php echo $value; ?>">
										<?php echo $key; ?>
									</label>
								</div>
							</div>
						<?php }
					}
				}
			}else{
				//-------------------------------------------------------------- Parte para el XML y RSS 2.0
				$xml=simplexml_load_file($this->input->post('url'));
				if($xml->channel->item){
					$rest=json_encode($xml->channel->item);
					$jsonIterator =json_decode($rest, TRUE);
				}else{
					$rest=json_encode(new SimpleXMLElement($url));
					$jsonIterator =json_decode($rest, TRUE);
				}
				$campos=[];
				$offset=0;
				$col=12;
				$cont=-1;
				$items = count($jsonIterator);
				if($items>1){
					foreach ($jsonIterator as $key => $value) {
						$cont++;
						if($cont%4===0){ ?>
							<div class="row"></div>
							<br>
						<?php }
						if(is_array($value)){ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox" id="<?php echo $key; ?>">
									<label>
										<input onchange="desplega(this);" type="checkbox" name="arreglo[]" value="<?php echo $key; ?>">
										<?php echo $key; ?>
									</label>	
									<?php $this->hijos($value,5); ?>
								</div>
							</div>
						<?php }else{ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="" value="<?php echo $value; ?>">
										<?php echo $key; ?>
									</label>
								</div>
							</div>
						<?php }
					}
				}else{
					foreach ($jsonIterator as $key => $value) {
						$cont++;
						if($cont%4===0){ ?>
							<div class="row"></div>
							<br>
						<?php }
						if(is_array($value)){ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox" id="<?php echo $key; ?>">
									<label>
										<input onchange="desplega(this);" type="checkbox" name="arreglo[]" value="<?php echo $key; ?>">
										<?php echo $key; ?>
									</label>	
									<?php $this->hijos($value,5); ?>
								</div>
							</div>
						<?php }else{ ?>
							<div class="col-sm-3 col-md-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="arreglo[]" value="<?php echo $value; ?>">
										<?php if(is_numeric($key)) echo $this->numeric[$key]; else echo $key; ?>
									</label>
								</div>
							</div>
						<?php }
					}
				}
			}

		}

		function limpiar($arr,$orig){

			foreach ($arr as $key => $value) {
				if(is_array($value)){
					$orig[$key]=$this->limpiar($value,$orig[$key]);
				}else{
					if(!array_key_exists($key, $orig)){
						$orig[$key]='';
					}else{
						$orig[$key]='';					
					}
				}
			}			
			return $orig;

		}

		function hijos($arreglo,$espacio){

			foreach ($arreglo as $key => $value) {
				if(is_array($value)){ ?>
					<div id="<?php echo $key; ?>" style="display:none; margin-left:<?php echo ($espacio+20)."px"; ?>">
						<label>
							<input onchange="desplega(this);" type="checkbox" name="clave[]" value="<?php echo $key; ?>">
							<?php if(is_numeric($key)) echo $this->numeric[$key]; else echo $key; ?>
						</label>	
						<?php $this->hijos($value,$espacio); ?>
					</div>	
				<?php }else{ ?>
					<div class="checkbox" style="display:none; margin-left:<?php echo $espacio."px"; ?>">
						<label>
							<input type="checkbox" name="arreglo[]" value="<?php echo $value; ?>">
							<?php if(is_numeric($key)) echo $this->numeric[$key]; else echo $key; ?>
						</label>
					</div>	
				<?php }
			}

		}


}