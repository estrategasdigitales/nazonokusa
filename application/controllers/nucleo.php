<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nucleo extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index() {

		$this->load->view('middleware/index');

	}

	public function detectar_campos() {

		$url=file_get_contents($this->input->post('url'));
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
									<input onchange="desplega(this);" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $key; ?>">
									<?php echo $key; ?>
								</label>	
								<?php $this->hijos($value,5); ?>
							</div>
						</div>
						<?php }else{ ?>
						<div class="col-sm-3 col-md-3">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="" value="">
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
									<input onchange="desplega(this);" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $key; ?>">
									<?php echo $key; ?>
								</label>	
								<?php $this->hijos($value,5); ?>
							</div>
						</div>
						<?php }else{ ?>
						<div class="col-sm-3 col-md-3">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="" value="">
									<?php echo $key; ?>
								</label>
							</div>
						</div>
						<?php }
					}
				}
			}else{
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
									<input onchange="desplega(this);" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $key; ?>">
									<?php echo $key; ?>
								</label>	
								<?php $this->hijos($value,5); ?>
							</div>
						</div>
						<?php }else{ ?>
						<div class="col-sm-3 col-md-3">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="" value="">
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
									<input onchange="desplega(this);" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $key; ?>">
									<?php echo $key; ?>
								</label>	
								<?php $this->hijos($value,5); ?>
							</div>
						</div>
						<?php }else{ ?>
						<div class="col-sm-3 col-md-3">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="" value="">
									<?php echo $key; ?>
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
						<input onchange="desplega(this);" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $key; ?>">
						<?php echo $key; ?>
					</label>	
					<?php $this->hijos($value,$espacio); ?>
				</div>	
				<?php }else{ ?>
				<div class="checkbox" style="display:none; margin-left:<?php echo $espacio."px"; ?>">
					<label>
						<input type="checkbox" name="" value="">
						<?php echo $key; ?>
					</label>
				</div>	
				<?php }
			}	
		}
		function ordenar($array,$offset,$col,$clave){ ?>

		<?php foreach ($array as $key => $value) { ?>
		<?php if(is_array($value)) { ?>					
		<div style="" class="<?php echo $clave; ?> <?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="campo[]" value="<?php echo $key; ?>">
					<?php echo $key; ?>
				</label>
			</div>
		</div>
		<?php $this->ordenar($value,$offset+1,$col-1,$clave);
	} else { ?>					
	<div style="" class="<?php echo $clave; ?> <?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
		<div class="checkbox">
			<label>
				<input type="checkbox" name="campo[]" value="<?php echo $key; ?>">
				<?php echo $key; ?>
			</label>
		</div>
	</div>
	<?php }
}
}


}