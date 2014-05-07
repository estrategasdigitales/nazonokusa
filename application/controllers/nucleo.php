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
		if($pos > -1){
			$rest = substr($url, $pos+1, -1);
		}else{
			$rest = $url;
		}
		if($jsonIterator =json_decode($rest, TRUE)){
		$espacio="col-sm-offset-0 col-md-offset-0 col-sm-12 col-md-12";
		$final=[];
		$offset=0;
		$col=12;
		$cont=-1;
		foreach ($jsonIterator as $key => $value) { 
			++$cont;
			if($cont%4===0) { ?>
				<div class="row"></div>
			<?php } ?>
			<div class="col-sm-3 col-md-3">
				<?php if(is_array($value)) { ?>
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label onclick="desplegar('<?php echo $key; ?>');">
								<input type="checkbox" name="principal" id="principal_<?php echo $key; ?>" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
					<?php $this->ordenar($value,$offset+1,$col-1,$key);
				} else { ?>					
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="principal" id="<?php echo $key; ?>" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php }
	}else{
		$xml=simplexml_load_file($this->input->post('url'));
		$rest=json_encode($xml->channel->item);
	$jsonIterator =json_decode($rest, TRUE);
	$espacio="col-sm-offset-0 col-md-offset-0 col-sm-12 col-md-12";
		$final=[];
		$offset=0;
		$col=12;
		$cont=-1;
		foreach ($jsonIterator as $key => $value) { 
			++$cont;
			if($cont%4===0) { ?>
				<div class="row"></div>
			<?php } ?>
			<div class="col-sm-3 col-md-3">
				<?php if(is_array($value)) { ?>
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label onclick="desplegar('<?php echo $key; ?>');">
								<input type="checkbox" name="principal" id="principal_<?php echo $key; ?>" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
					<?php $this->ordenar($value,$offset+1,$col-1,$key);
				} else { ?>					
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="principal" id="<?php echo $key; ?>" value="<?php echo $key; ?>">
								<?php echo $key; ?>
							</label>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php }
	}

	}

	function ordenar($array,$offset,$col,$clave){ ?>
	
		<?php foreach ($array as $key => $value) { ?>
			<?php if(is_array($value)) { ?>					
				<div style="display:none;" class="<?php echo $clave; ?> <?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="campo[]" value="<?php echo $key; ?>">
								<?php echo $key; ?>
						</label>
					</div>
				</div>
				<?php $this->ordenar($value,$offset+1,$col-1,$clave);
			} else { ?>					
				<div style="display:none;" class="<?php echo $clave; ?> <?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
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

	public function ejecutar_script($archivo){
		

	}
	
}