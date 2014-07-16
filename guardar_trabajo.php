<?php	
	/**
     * [validar_form_trabajo description]
     * @return [type] [description]
     */
	public function validar_form_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');

			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 		= $this->session->userdata('uid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['feed_tipo']		= $this->input->post('tipo_feed_entrada');
				$trabajo['campos'] 			= json_encode( $this->input->post('claves') );
				//$trabajo['feed_salida']		= campos_seleccionados( $trabajo['campos'], $trabajo['json_entrada']);
                $formats['formatos'] 		= $this->input->post('formato');
                $formats['valores_rss'] 	= $this->input->post('valores_rss');
                $formats['claves_rss'] 		= $this->input->post('claves_rss');
                
                //$trabajo['formato_salida'] = json_encode($formats);

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