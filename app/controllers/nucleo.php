<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nucleo extends CI_Controller {

	/**
	 * [__construct description]
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');
		$this->load->helper('cron_manager');
		$this->load->spark('curl');
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index() {
		if ( $this->session->userdata( 'session' ) !== TRUE ){
			redirect( 'login' );
		} else {
			$data['usuario'] = $this->session->userdata( 'nombre' );
			$this->load->view( 'middleware/index' );
		}
	}

	public function set_cron()
	{
		$host='107.170.237.101'; 
		$port='22';
		$username='root';	
		$password='yyqoypklcwza';

		$conectar = new cron_manager();
		$resp_con = $conectar->connect($host, $port, $username, $password);
		//print_r($resp_con);
		$conectar->write_to_file();
		//* * * * * /usr/bin/curl http://www.midominio.com/archivo.php
		//$conectar->append_cronjob('{cadena que contendrá el cronjob a escribir}');
				
	}

	public function alerta()
	{
		// $usr_cel, $usr_carrier, $usr_mail, $nombre_trabajo, $id_mensaje
		// Cadena para hacer las peticiones al servicio de SMS
		// http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=$PHONE&carrier=telcel&user=onemex&password=mex11&message=$MESSAGE	
		// Catalogo de errores.

		$phone = "5585320763";
		$message = "Mensaje de error identificado";
		$usr_carrier = "iusacell";

		$url_sms = "http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=".$phone."&carrier=".$usr_carrier."&user=onemex&password=mex11&message=".$message;
		//echo $this->curl->simple_get($url_sms);
		

	}
	
	


	/**
	 * [set_cron description]
	 */
	public function set_cron(){
		$host='107.170.237.101'; 
		$port='22';
		$username='root';	
		$password='yyqoypklcwza';

		$conectar = new cron_manager();
		$resp_con = $conectar->connect($host, $port, $username, $password);
		//print_r($resp_con);
		$conectar->write_to_file();
		//* * * * * /usr/bin/curl http://www.midominio.com/archivo.php
		$conectar->append_cronjob('*/2 * * * * date >> ~/testCron.log');
				
	}

	/**
	 * [detectar_campos description]
	 * @return [type] [description]
	 */
	public function detectar_campos(){
		$output = array();
		$url = file_get_contents( $this->input->post( 'url' ) );
		$url = utf8_encode( $url );
		if ( $feed = json_decode( $url ) ){
			$feed_type 		= 'JSON';
			$feed_content 	= $url;
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos + 1, -1 );
				$feed_type 		= 'JSONP';
				$feed_content 	= $feed;
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$feed_type 		= 'RSS';
					$rss = fetch_rss( $this->input->post( 'url' ) );
					$feed_content 	= json_encode( $rss->items );
				} else {
					$feed_type 		= 'XML';
					$xml 			= simplexml_load_string( $url );
					$feed_content 	= json_encode( $xml );
				}
			}
		}

		$salida = array(
			'feed_type'		=>	$feed_type,
			'feed_content'	=>	$feed_content
		);

		echo json_encode( $salida );
	}

	/**
	 * [editar_trabajo description]
	 * @param  [type] $uid_trabajo [description]
	 * @return [type]              [description]
	 */
	public function editar_trabajo( $uid_trabajo ){
    	if( $this->session->userdata('session') !== TRUE ){
    		redirect('login');
    	}else {
    	    $trabajo = $this->cms->get_trabajo_editar( $uid_trabajo );
           	$data['usuario']    	= $this->session->userdata('nombre');
			$data['categorias'] 	= $this->cms->get_categorias();
			$data['verticales'] 	= $this->cms->get_verticales();
       		$data['trabajo_editar'] = $trabajo->uid_trabajo;
       		$data['cron_date'] 		= json_decode($trabajo->cron_config, true);
           	$this->load->view('cms/admin/editar_trabajo', $data);
	   	}
	}


	/**
	 * [eliminar_trabajo description]
	 * @param  [type] $uid_trabajo [description]
	 * @return [type]              [description]
	 */
	public function eliminar_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$uid_trabajo = $this->input->post('uidjob');
			// $trabajo = $this->cms->get_trabajo_editar( $uid_trabajo );
			// $cronjob = json_decode($trabajo->cron_config, true);
			//$api_delete = $this->cms->delete_cronjob( $uid_trabajo );
			$eliminar = $this->cms->delete_trabajo($uid_trabajo);
			//if ( $eliminar !== FALSE && $api_delete !== FALSE ){
			if ( $eliminar !== FALSE ){
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podigo eliminar el <b>trabajo</b>.</span>';
			}
		}
	}

    /**
     * [ejecutar_trabajo description]
     * @param  [type] $uid_trabajo [description]
     * @return [type]              [description]
     */
    public function ejecutar_trabajo( $uid_trabajo ){
    	$trabajo 	=	$this->cms->get_trabajo_editar( $uid_trabajo );
    	$elegidos 	=	[];
    	$formatos 	=	[];
		$cf 		= 	json_decode( $trabajo[0]['formato_salida'], TRUE );

		foreach ( $cf['campos']  as $key => $value ){ $elegidos[] = explode( "," , $value ); }
		foreach ( $cf['formatos']  as $key => $value ){ $formatos[] = $value; }

    	if ( $trabajo !== FALSE ){
    		$indice = 0;
			$url 	= utf8_encode( file_get_contents( $trabajo[0]['url_origen'] ) );
			$pos 	= strpos( $url, '(' );
            $rest 	= ( $pos > -1 && ( substr( $url, -1 ) === ")" ) ) ? substr( $url, $pos + 1, -1 ) : $url;
					
			if ( $campos_orig = json_decode( $rest, TRUE ) ){/*Si el formato corresponde a JSON, obtener los datos y procesarlos*/
				if ( ! empty( $campos_orig[0] ) ){
					for ( $i = 0; $i < count( $campos_orig ); $i++ ){
						foreach ( $campos_orig[$i] as $key => $value ){
							for ($j = 0; $j < count( $elegidos ); $j++ ){
								$tmp = 0;
								if ( count( $elegidos[$j]) > $indice ){
									if ( $elegidos[$j][$indice] === (string)$key ){ $tmp++; break; }
								}
							}
							if ( $tmp > 0 ){
								if ( is_array( $value ) ){ $campos_orig[$i][$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 ); }
			     			} else { unset( $campos_orig[$i][$key] ); }
						}
					}
				} else {
					foreach ( $campos_orig as $key => $value ){
						for ( $j = 0; $j < count( $elegidos ); $j++ ){
							$tmp = 0;
							if ( count( $elegidos[$j]) > $indice ){
								if ( $elegidos[$j][$indice] === (string)$key ){ $tmp = $tmp + 1; break; }
							}
						}
						if ( $tmp > 0 ){
							if ( is_array( $value ) ){ $campos_orig[$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 ); }
						} else { unset($campos_orig[$key]); }
					}
				}
			} else {	/*Si el formato NO es JSON, es XML, obtener los datos y procesarlos*/
				$xml = simplexml_load_file( $trabajo[0]['url_origen'] );
				if ( $xml->channel ){
					$rest 			= 	json_encode( $xml );
					$campos_orig 	=	json_decode( $rest, TRUE );
				} else {
					$rest 			= 	json_encode( $xml );
					$campos_orig 	= 	json_decode( $rest, TRUE );
				}
				if ( ! empty( $campos_orig[0] ) ){
					for ( $i = 0; $i < count( $campos_orig ); $i++ ){
						foreach ( $campos_orig[$i] as $key => $value ) {
							for ( $j = 0; $j < count( $elegidos ); $j++ ){
								$tmp = 0;
								if ( count( $elegidos[$j] ) > $indice ){
									if ( $elegidos[$j][$indice] === (string)$key ){
										$tmp = $tmp + 1;
										break;
									}
								}
							}	
							if ( $tmp > 0 ){
								if ( is_array( $value ) ){
									$campos_orig[$i][$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
								}
							} else {
								unset( $campos_orig[$i][$key] );										
							}
						}
					}
				} else {
					foreach ( $campos_orig as $key => $value ){
						for ( $j = 0; $j < count( $elegidos ); $j++ ){
							$tmp = 0;
							if ( count($elegidos[$j] ) > $indice ){
								if ( $elegidos[$j][$indice] === (string)$key ){
									$tmp = $tmp + 1;
									break;
								}
							}
						}			
						if ( $tmp > 0 ){
							if ( is_array( $value ) ){ $campos_orig[$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 ); }
						} else { unset( $campos_orig[$key] ); }
					}
				}
			}
			for ( $i = 0; $i < count( $formatos ); $i++ ){
				if( $formatos[$i] === 'xml' ){ $this->convert_xml( $campos_orig ); }
				if( $formatos[$i] === 'rss2' ){ $this->convert_rss( $campos_orig, $trabajo['claves_rss'], $trabajo['valores_rss'] ); }
				if( $formatos[$i] === 'json' ){ $this->convert_json( $campos_orig ); }
				if( $formatos[$i] === 'jsonp' ){ $this->convert_jsonp( $campos_orig, $this->input->post( 'nom_funcion' ) ); }
			}
    	redirect('trabajos');
    	} else return false;
    }

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
			// $this->form_validation->set_rules('destino-local', 'destino-local', 'min_length[3]|xss_clean');
			// $this->form_validation->set_rules('destino-net', 'destino-net', 'min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');

			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 		= $this->session->userdata('uid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				// $trabajo['destino-local']   = $this->input->post('destino-local');
				// $trabajo['destino-net']  	= $this->input->post('destino-net');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['campos'] 			= $this->input->post('claves');
				
				$formats['campos'] 			= $this->input->post('claves'); 
                $formats['formatos'] 		= $this->input->post('formato'); 
                $formats['valores_rss'] 	= $this->input->post('valores_rss'); 
                $formats['claves_rss'] 		= $this->input->post('claves_rss'); 
                
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

	/**
	 * [validar_form_editar_trabajo description]
	 * @return [type] [description]
	 */
	public function validar_form_editar_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Cetegoría', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');

			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 		= $this->session->userdata('uid');
				$trabajo['nombre']   		= $this->input->post('nombre');
				$trabajo['url-origen']   	= $this->input->post('url-origen');
				$trabajo['categoria']   	= $this->input->post('categoria');
				$trabajo['vertical']   		= $this->input->post('vertical');
				$trabajo['campos'] 			= $this->input->post('claves');
				$trabajo['uid_trabajo']    	= $this->input->post('id_trabajo');
				
				$formats['campos'] 			= $this->input->post('claves'); 
                $formats['formatos'] 		= $this->input->post('formato'); 
                $formats['valores_rss'] 	= $this->input->post('valores_rss'); 
                $formats['claves_rss'] 		= $this->input->post('claves_rss'); 
                
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
                
				if ( $guardar == FALSE ){
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
					$this->load->view('cms/admin/nuevo_trabajo',$data);
				} else {	
					$indice = 0;
					$url = utf8_encode(file_get_contents($this->input->post('url-origen')));
					$pos = strpos($url, '(');
						if($pos > -1 && (substr($url, -1)===")")){
							$rest = substr($url, $pos+1, -1);
						}else{
							$rest = $url;
						}

						if ( $campos_orig = json_decode($rest, TRUE ) ){
							if(!empty($campos_orig[0])){
								for ( $i = 0; $i < count( $campos_orig ) ; $i++) {
									foreach ( $campos_orig[$i] as $key => $value ){
										for ( $j = 0; $j < count( $elegidos ) ; $j++ ){
											$tmp = 0;
											if ( count($elegidos[$j]) > $indice ){
												if ( $elegidos[$j][$indice] === (string)$key ){
													$tmp = $tmp + 1;
													break;
												}
											}
										}
										if ( $tmp > 0 ){
											if ( is_array( $value ) ){
												$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
											}
										} else {
											unset( $campos_orig[$i][$key] );
										}
									}
								}
							} else {
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
							$xml = simplexml_load_file( $this->input->post('url-origen') );
							if ( $xml->channel ){
								$rest = json_encode( $xml );
								$campos_orig = json_decode( $rest, TRUE );
							} else {
								$rest = json_encode( $xml );
								$campos_orig = json_decode( $rest, TRUE );
							}

							if ( ! empty( $campos_orig[0] ) ){
								for ( $i = 0; $i < count( $campos_orig ) ; $i++ ){
									foreach ( $campos_orig[$i] as $key => $value ){
										for ( $j = 0; $j < count($elegidos) ; $j++ ){
											$tmp = 0;
											if ( count( $elegidos[$j] ) > $indice ){
												if ( $elegidos[$j][$indice] === (string)$key ){
													$tmp = $tmp + 1;
													break;
												}
											}
										}
										if ( $tmp > 0 ){
											if ( is_array( $value ) ){
												$campos_orig[$i][$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
											}
										} else {
											unset( $campos_orig[$i][$key] );
										}
									}
								}
							} else {
								foreach ( $campos_orig as $key => $value ){
									for ( $j = 0; $j < count( $elegidos ) ; $j++ ){
										$tmp = 0;
										if ( count( $elegidos[$j] ) > $indice ){
											if ( $elegidos[$j][$indice] === (string)$key ){
												$tmp = $tmp + 1;
												break;
											}
										}
									}
									if ( $tmp > 0 ){
										if ( is_array( $value ) ){
											$campos_orig[$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
										}
									} else {
										unset( $campos_orig[$key] );
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
			} else {
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/nuevo_trabajo',$data);
			}
		}
	}

	/**
	 * [arreglo_nuevo description]
	 * @param  [type] $arreglo  [description]
	 * @param  [type] $elegidos [description]
	 * @param  [type] $indice   [description]
	 * @return [type]           [description]
	 */
	function arreglo_nuevo( $arreglo, $elegidos, $indice ){
		if ( ! empty( $arreglo[0] ) ){
			for ( $i = 0; $i < count( $arreglo ); $i++ ){
				foreach ( $arreglo[$i] as $key => $value ){
					for ( $j = 0; $j < count( $elegidos ); $j++ ){
						$tmp = 0;
						if ( count( $elegidos[$j] ) > $indice ){
							if ( $elegidos[$j][$indice] === (string)$key ){
								$tmp = $tmp + 1;
								break;
							}
						}
					}
					if ( $tmp > 0 ){
						if ( is_array( $value ) ){
							$arreglo[$i][$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
						}
					} else {
						unset( $arreglo[$i][$key] );										
					}
				}
			}
		} else {
			foreach ( $arreglo as $key => $value ){
				for ( $j = 0; $j < count( $elegidos ); $j++ ){
					$tmp = 0;
					if ( count( $elegidos[$j]) > $indice ){
						if ( $elegidos[$j][$indice] === (string)$key ){
							$tmp = $tmp + 1;
							break;
						}
					}
				}				
				if ( $tmp > 0){
					if ( is_array( $value ) ){
						$arreglo[$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
					}
				} else {
					unset( $arreglo[$key] );										
				}
			}
		}
		return $arreglo;
	}

	/**
	 * [hijos description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $espacio [description]
	 * @param  [type] $clave   [description]
	 * @return [type]          [description]
	 */
	function hijos( $arreglo, $espacio, $clave ){
		$hijosFeed = array();
		foreach ( $arreglo as $key => $value ){
			if ( is_array( $value ) ){
				echo 'entra aqui';die;
				$hijos 	= '<div id="' . $key . '" class="campo-hijo"><label><input onchange="desplega(this);" type="checkbox" name="claves[]" value="' . $clave . ',' . $key . '">' . $key . '</label>';
					$nietos = $this->hijos( $value, $espacio, $clave . ',' . $key );
					foreach ( $nietos as $nieto ){
						$hijos .= $nieto;
					}
				$hijos .= '</div>';
				array_push( $hijosFeed, $hijos );
			} else {
				echo 'entra sin nietos';die;
				$hijos = '<div class="checkbox" class="campo-hijo"><label><input type="checkbox" name="claves[]" value="' . $clave . ',' . $key . '">' . $key . '</label></div>';
				array_push( $hijosFeed, $hijos );
			}
			//array_push( $hijosFeed, $hijos );
		}
		return $hijosFeed;
	}

	/**
	 * [get_campos_json description]
	 * @param  [type] $campos_orig [description]
	 * @return [type]              [description]
	 */
	function get_campos_json( $campos_orig ){
		$campos = [];
		$cont 	= -1;
		$items 	= count( $campos_orig );
		if ( !empty( $campos_orig[0] ) ){
			for ( $i = 0; $i < count( $campos_orig ); $i++){
				foreach ( $campos_orig[$i] as $key => $value ){
					if ( is_array( $value ) ){
						if ( ! empty( $campos[$key] ) ){
							$campos[$key] = $this->claves( $value, $campos[$key] );
						} else {
							$campos[$key] = $this->claves( $value, $campos[$key] = [] );
						}
					} else {
						if ( ! array_key_exists( $key, $campos ) ){
							$campos[$key] = '';
						}
					}
				}
			}
		} else {
			foreach ( $campos_orig as $key => $value ){
				if ( is_array( $value ) ){
					if ( ! empty( $campos[$key] ) ){
						$campos[$key] = $this->claves( $value,$campos[$key] );
					} else {
						$campos[$key] = $this->claves( $value, $campos[$key] = [] );
					}
				} else {
					if ( ! array_key_exists( $key, $campos ) ){
						$campos[$key] = '';
					}
				}
			}
		}
		
		foreach ( $campos as $key => $value ){
			$cont++;
			if ( $cont%4 === 0 ){
				$fieldFeed = '<div class="row"></div><br>';
			}

			if ( is_array( $value ) ){
				$fieldFeed .= '<div class="col-sm-3 col-md-3"><div class="checkbox" id="' . $key . '"><label><input onchange="desplega(this);" type="checkbox" name="claves[]" value="' . $key . '">' . $key . '</label>';
					$hijos = $this->hijos( $value, 5, $key );
					foreach ( $hijos as $hijo ){
						$fieldFeed .= $hijo;
					}
				$fieldFeed .= '</div></div>';
			} else {
				$fieldFeed .= '<div class="col-sm-3 col-md-3"><div class="checkbox"><label><input type="checkbox" name="claves[]" value="' . $key . '">' . $key . '</label></div></div>';
			}
		}
		return $fieldFeed;
	}

	/**
	 * [get_campos_xml description]
	 * @param  [type] $campos_orig [description]
	 * @return [type]              [description]
	 */
	function get_campos_xml( $campos_orig ){
		$campos 	= 	[];
		$cont 		=	-1;
		$items 		= count( $campos_orig );
		if ( ! empty( $campos_orig[0] ) ){
			for ( $i = 0; $i < count( $campos_orig ); $i++ ){
				foreach ( $campos_orig[$i] as $key => $value ){
					if ( is_array( $value ) ){
						if ( ! empty( $campos[$key] ) ){
							$campos[$key] = $this->claves( $value, $campos[$key] );
						} else {
							$campos[$key] = $this->claves( $value, $campos[$key] = [] );
						}
					} else {
						if ( ! array_key_exists( $key, $campos ) ){
							$campos[$key] = '';
						}
					}
				}
			}
		} else {
			foreach ( $campos_orig as $key => $value ){
				if ( is_array( $value ) ){
					if ( ! empty( $campos[$key] ) ){
						$campos[$key] = $this->claves( $value,$campos[$key] );
					} else {
						$campos[$key] = $this->claves( $value, $campos[$key] = [] );
					}
				} else {
					if ( ! array_key_exists( $key, $campos ) ){
						$campos[$key] = '';
					}
				}
			}
		}
		
		foreach ( $campos as $key => $value ){
			$cont++;
			if ( $cont%4 === 0 ){
				$fieldFeed = '<div class="row"></div><br>';
			}

			if ( is_array( $value ) ){
				$fieldFeed .= '<div class="col-sm-3 col-md-3"><div class="checkbox" id="' . $key . '"><label><input onchange="desplega(this);" type="checkbox" name="claves[]" value="' . $key . '">' . $key . '</label>';
					$hijos = $this->hijos( $value, 5, $key );
					foreach ( $hijos as $hijo){
						$fieldFeed .= $hijo;
					}
				$fieldFeed .= '</div></div>';
			} else {
				$fieldFeed .= '<div class="col-sm-3 col-md-3"><div class="checkbox"><label><input type="checkbox" name="claves[]" value="' . $key . '">' . $key . '</label></div></div>';
			}

			return $fieldFeed;
		}
	}

	/**
	 * [claves description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $origin  [description]
	 * @return [type]          [description]
	 */
	function claves( $arreglo, $origin ){
		if ( ! empty( $arreglo[0] ) ){
			for ($i = 0; $i < count( $arreglo ); $i++ ){
				foreach ( $arreglo[$i] as $key => $value ){
					if ( is_array( $value ) ){
						if ( ! empty( $origin[$key] ) ){
							$origin[$key] = $this->claves( $value, $origin[$key] );
						} else {
							$origin[$key] = $this->claves( $value, $origin[$key] = [] );
						}
					} else {
						if ( ! array_key_exists( $key, $origin ) ){
							$origin[$key] = '';
						}
					}												
				}
			}
		} else {
			foreach ( $arreglo as $key => $value ){
				if ( is_array( $value ) ){
					if ( ! empty( $origin[$key] ) ){
						$origin[$key] = $this->claves( $value, $origin[$key] );
					} else {
						$origin[$key] = $this->claves( $value, $origin[$key] = [] );
					}
				} else {
					if ( ! array_key_exists( $key, $origin ) ){
						$origin[$key] = '';
					}
				}												
			}
		}
		return $origin;
	}

	function convert_xml( $arreglo, $nombre ){
		$open = fopen( "./outputs/". $nombre . "xml.xml", "w");
		$cabeceras = "<?xml version='1.0' encoding='utf-8' ?>\n";
		fwrite( $open, $cabeceras );
		if ( ! empty( $arreglo[0] ) ){
			for ( $i = 0; $i < count( $arreglo ); $i++ ){
				fwrite( $open, "\n<elemento>" );
				foreach ( $arreglo[$i] as $key => $value ){
					if ( is_array( $value ) ){
						fwrite( $open, "\n<" . $key . ">" . $this->formato_xml( $value ) . "</" . $key . ">" );
					} else {
						fwrite( $open, "\n<" . $key . ">" . $value . "</" . $key . ">" );
					}
				}
				fwrite( $open, "\n</elemento>" );
			}
		}else{
			foreach ( $arreglo as $key => $value ){
				if ( is_array( $value ) ){
					fwrite( $open, "\n<" . $key . ">" . $this->formato_xml( $value ) . "</" . $key . ">" );
				} else {
					fwrite( $open, "\n<" . $key . ">" . $value . "</" . $key . ">" );
				}															
			}
		}
		fclose( $open );
	}

	function formato_xml( $arreglo ){
		$etiquetas = "";
		if ( ! empty( $arreglo[0] ) ){
			for ( $i = 0; $i < count( $arreglo ); $i++ ){
				$etiquetas.= "\n<elemento>";
				foreach ( $arreglo[$i] as $key => $value ){
					if ( is_array( $value ) ){
						$etiquetas.= "\n<" . $key . ">" . $this->formato_xml( $value ) . "</" . $key . ">";
					} else {
						$etiquetas.= "\n<" . $key . ">" . $value . "</" . $key . ">";
					}
				}
				$etiquetas.= "\n</elemento>\n";
			}
		} else {
			foreach ( $arreglo as $key => $value ){
				if ( is_array( $value ) ){
					$etiquetas.= "\n<" . $key . ">" . $this->formato_xml( $value ) . "</" . $key . ">";
				} else {
					$etiquetas.= "\n<" . $key . ">" . $value . "</" . $key . ">";
				}															
			}
		}
		return $etiquetas;
	}

	function convert_rss( $arreglo, $nombre, $nodos, $valores ){
		$open = fopen( "./outputs/" . $nombre . "rss.xml", "w" );
		$cabeceras = "<?xml version='1.0' encoding='utf-8' ?>\n<rss version='2.0'>\n<channel>\n";
		fwrite( $open, $cabeceras );
		for ( $i = 0; $i < count( $nodos ); $i++ ){
			fwrite( $open, "\n<" . $nodos[$i] . ">" . $valores[$i] . "</" . $nodos[$i] . ">" );
		}
		if ( ! empty( $arreglo[0] ) ){
			for ( $i = 0; $i < count( $arreglo ); $i++ ){
				fwrite( $open, "\n<item>" );
				foreach ( $arreglo[$i] as $key => $value ){
					if ( is_array( $value ) ){
						fwrite( $open, "\n<" . $key . ">" . $this->formato_rss( $value ) . "</" . $key . ">" );
					} else {
						fwrite( $open, "\n<" . $key . ">" . $value . "</" . $key . ">" );
					}
				}
				fwrite( $open, "\n</item>" );
			}
		}else{
			foreach ( $arreglo as $key => $value ){
				if ( is_array( $value ) ){
					fwrite( $open, "\n<" . $key . ">" . $this->formato_rss( $value ) . "</" . $key . ">" );
				} else {
					fwrite( $open, "\n<" . $key . ">" . $value . "</" . $key . ">" );
				}															
			}
		}
		$cierre = "\n</channel>\n</rss>";
		fwrite( $open, $cierre );
		fclose( $open );
	}

	function formato_rss( $arreglo ){
		$etiquetas = "";
		if ( ! empty( $arreglo[0] ) ){
			for ( $i = 0; $i < count( $arreglo ); $i++ ){
				$etiquetas.= "\n<item>";
				foreach ( $arreglo[$i] as $key => $value ){
					if ( is_array( $value ) ){
						$etiquetas.= "\n<" . $key . ">" . $this->formato_rss( $value ) . "</" . $key . ">";
					} else {
						$etiquetas.= "\n<" . $key . ">" . $value . "</" . $key . ">";
					}
				}
				$etiquetas.= "\n</item>\n";
			}
		} else {
			foreach ( $arreglo as $key => $value ){
				if ( is_array( $value ) ){
					$etiquetas.= "\n<" . $key . ">" . $this->formato_rss( $value ) . "</" . $key . ">";
				} else {
					$etiquetas.= "\n<" . $key . ">" . $value . "</" . $key . ">";
				}															
			}
		}
		return $etiquetas;
	}

	function convert_json( $arreglo, $nombre ){
		$open = fopen( "./outputs/" . $nombre . "json.js", "w" );
		$final= json_encode( $arreglo );
		fwrite( $open, stripslashes( $final ) );
		fclose( $open );
	}

	function convert_jsonp( $arreglo, $nombre, $funcion ){
		$open = fopen( "./outputs/" . $nombre . "jsonp.js", "w");
		$final= $funcion . "(" . json_encode( $arreglo ). ")";
		fwrite( $open, stripslashes( $final ) );
		fclose( $open );
	}
}