<?php 
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
