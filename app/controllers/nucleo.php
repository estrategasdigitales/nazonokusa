<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH . '../app/libraries/Tree.php');
require_once( BASEPATH . '../app/libraries/TreeMatch.php');

class Nucleo extends CI_Controller {

	/**
	 * Composer data json
	 * @var ComposerDataSet
	 */
	//private $_composer;
	private $netstorage;
	private $url_storage;
	private $storage_root;

	/**
	 * [__construct description]
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');
		$this->load->helper( array('cron_manager', 'file' ) );

		// Creamos composer para armar el arreglo de información
		//$this->_composer = new ComposerDataSet();
		//
		$this->storage_root = '/';
		$this->url_storage = 'storagemas.upload.akamai.com';
		/** Configuracion de conexión a netstorage */
		$this->netstorage = array(
		 		'hostname' 	=> 'storagemas.upload.akamai.com',
				'username' 	=> 'marcoplata',
				'password' 	=> 'y4mi.99yS',
				'passive'	=> TRUE,
				'debug'		=> FALSE
			);
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

	/**
	 * [alerta description]
	 * @return [type] [description]
	 */
	public function alerta($usr_cel, $usr_carrier, $usr_mail, $nombre_trabajo, $id_mensaje){
		// Cadena para hacer las peticiones al servicio de SMS
		// Ejemplo: http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=525585320763&carrier=iusacell&user=onemex&password=mex11&message=Error prueba de mensajes	
		// 202 - Respuesta success
		// Catalogo de errores.

		//$phone = "525585320763";
		//$message = "Mensaje de error identificado";
		//$usr_carrier = "iusacell";

		$url_sms = "http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=".$phone."&carrier=".$usr_carrier."&user=onemex&password=mex11&message=".$message;
		$sms_reponse = $this->curl->simple_get($url_sms);

		if($sms_reponse == 202)
			echo "Mensaje enviado correctamente";
		else
			echo $sms_reponse;
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
		//$conectar->append_cronjob('*/2 * * * * date >> ~/testCron.log');
	}

	/**
	 * [feed_service description]
	 * @return [type] [description]
	 */
	public function feed_service(){
		$url = $this->input->get('url');
		$output = array();
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		$url = html_entity_decode( utf8_decode( $url ) );
		if ( $feed = json_decode( $url ) ){
			foreach ( $feed as $item ){
				$cont[] = $this->mapAttributes( json_encode( $item ) );
			}
			$contents = $this->array_unique_multidimensional( $cont );
			$indices = create_indexes( $contents );
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos + 1, -1 );
				$feed = json_decode( $feed );
				foreach ( $feed as $item ){
					$cont[] = $this->mapAttributes( json_encode( $item ) );
				}
				$contents = $this->array_unique_multidimensional( $cont );
				$indices = create_indexes( $contents );
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = new SimpleXMLElement( $url );
					// print_r( $rss->children()->getName(true) );die;
					// $rss = fetch_rss( $url );
					foreach ( $rss as $item ){
						$feed[] = $this->mapAttributes( json_encode( $item ) );
					}
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes( $contents );
				} else {
					//$xml 			= simplexml_load_string( $url, 'SimpleXMLElement', LIBXML_NOCDATA );
					$xml = new SimpleXMLElement( $url );
					foreach ( $xml as $item ){
						$feed[] = $this->mapAttributes( json_encode( array( $xml ) ) );
					}
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes( $contents );
				}
			}
		}

		// print_r( $indices );die;

		$data = array( 'indices' => $indices[0] );
		$this->load->view('cms/service', $data );
	}

	/**
	 * [detectar_campos description]
	 * @return [type] [description]
	 */
	// public function detectar_campos(){
	// 	$output = array();
	// 	$url = file_get_contents( $this->input->post( 'url' ) );
	// 	$url = utf8_encode( $url );
	// 	if ( $feed = json_decode( $url ) ){
	// 		$feed_type 		= 'JSON';
	// 		foreach ( $feed as $item ){
	// 			$cont[] 	= $this->mapAttributes( json_encode( $item ) );
	// 		}
	// 		$contents 		= $this->array_unique_multidimensional( $cont );
	// 		$feed_content 	= create_tree( $contents );
	// 	} else {
	// 		$pos = strpos( $url, '(' );
	// 		if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
	// 			$feed 			= substr( $url, $pos + 1, -1 );
	// 			$feed_type 		= 'JSONP';
	// 			$feed 			= json_decode( $feed );
	// 			foreach ( $feed as $item ){
	// 				$cont[] 	= $this->mapAttributes( json_encode( $item ) );
	// 			}
	// 			$contents 		= $this->array_unique_multidimensional( $cont );
	// 			$feed_content 	= create_tree( $contents );
	// 		} else {
	// 			$dom = new DOMDocument();
	// 			$dom->loadXML( $url );
	// 			if ( $dom->documentElement->nodeName == 'rss' ){
	// 				$feed_type 		= 'RSS';
	// 				$rss 			= fetch_rss( $this->input->post( 'url' ) );
	// 				foreach ( $rss->items as $item ){
	// 					$feed[] 	= $this->mapAttributes( json_encode( $item ) );
	// 				}
	// 				$contents 		= $this->array_unique_multidimensional( $feed );
	// 				$feed_content 	= create_tree( $contents );
	// 			} else {
	// 				$feed_type 		= 'XML';
	// 				$xml 			= simplexml_load_string( $url, 'SimpleXMLElement', LIBXML_NOCDATA );
	// 				foreach ( $xml as $item ){
	// 					$feed[] 	= $this->mapAttributes( json_encode( array( $xml ) ) );
	// 				}
	// 				$contents 		= $this->array_unique_multidimensional( $feed );
	// 				$feed_content 	= create_tree( $contents );
	// 			}
	// 		}
	// 	}

	// 	$salida = array(
	// 		'feed_type'		=>	$feed_type,
	// 		'feed_content'	=>	$feed_content
	// 	);

	// 	echo json_encode( $salida );
	// }
	// 
	public function detectar_campos(){
		$url = base_url() . 'nucleo/feed_service?url=' . urlencode(base64_encode( $this->input->post('url') ) );
		$content = json_decode( file_get_contents_curl( $url ) );
		//print_r( $url );die;
		$tree = new Tree($content, true);
		$arbol = array('tree' => serialize( $tree ) );
		$this->session->set_userdata($arbol);
		$jsonStr = "[";
		$jsonStr .= $this->treeBuild($tree);
		$jsonStr = substr($jsonStr, 0, -5) . "]";
		$jsonStr =  preg_replace("/,\]\}/", "]}", $jsonStr);

		$data = array(
			'nodes' => $jsonStr
		);

		$this->load->view('cms/tree_feed', $data);
	}

	function iterateNodesJSON(stdClass $write, array $nodes, $parent = false) {
		$tree = [];
		// Obtenemos las propiedades
		$keys = get_object_vars($write);
		// Iteramos sobre cada una de las claves
		foreach ($keys as $key => $value) {
			$__item = array(
				'name' => $value,
				'type' => 'item'
			);

			if (is_array($value) || $value instanceof stdClass) {
				$__item = array(
					'name' => $key,
					'type' => 'folder'
				);

				array_push($nodes, $__item);

				// Tiene hijos entonces hacemos recursividad
				$nodes = $this->iterateNodesJSON($value, $nodes, $key);
			} else {
				if ($parent) {
					$__item = array(
						'name' => $value,
						'type' => 'item',
						'additionalParameters' => [],
						'parent' => $parent
					);

					$nodes[count($nodes) - 1]['additionalParameters']['children'][] = $__item;
				} else {
					$__item['type'] = 'item';
					array_push($nodes, $__item);
				}
			}
		}
		return $nodes;
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
			$url 	= utf8_encode( file_get_contents_curl( $trabajo[0]['url_origen'] ) );
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
	// public function validar_form_trabajo(){
	// 	if ( $this->session->userdata('session') !== TRUE ){
	// 		redirect( 'login' );
	// 	} else {
	// 		$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'required|min_length[3]|xss_clean');
	// 		$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
	// 		$this->form_validation->set_rules('categoria', 'Categoría', 'required|xss_clean');
	// 		$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
	// 		$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');

	// 		if ( $this->form_validation->run() === TRUE ){
	// 			$trabajo['usuario'] 		= $this->session->userdata('uid');
	// 			$trabajo['nombre']   		= $this->input->post('nombre');
	// 			$trabajo['url-origen']   	= $this->input->post('url-origen');
	// 			$trabajo['categoria']   	= $this->input->post('categoria');
	// 			$trabajo['vertical']   		= $this->input->post('vertical');
	// 			$trabajo['feed_tipo']		= $this->input->post('tipo_feed_entrada');
	// 			$trabajo['campos'] 			= json_encode( $this->input->post('claves') );
	// 			//$trabajo['feed_salida']		= campos_seleccionados( $trabajo['campos'], $trabajo['json_entrada']);
 //                $formats['formatos'] 		= $this->input->post('formato'); 
 //                $formats['valores_rss'] 	= $this->input->post('valores_rss'); 
 //                $formats['claves_rss'] 		= $this->input->post('claves_rss'); 
                
 //                $trabajo['formato_salida'] = json_encode($formats);

	// 			$elegidos=[];
	// 			foreach ($trabajo['campos']  as $key => $value) {
	// 				$elegidos[]=explode(",",$value);
	// 			}
	// 			$formatos=[];
	// 			foreach ($formats['formatos']  as $key => $value) {
	// 				$formatos[]=$value;
	// 			}

 //                $cron_date['mes'] = ($this->input->post('cron_mes')!=="*") ? (int)$this->input->post('cron_mes'):"*";
 //                $cron_date['hora'] =  ($this->input->post('cron_hora')!=="*") ? (int)$this->input->post('cron_hora') : "*";
 //                $cron_date['minuto'] = (is_string($this->input->post('cron_minuto')) && $this->input->post('cron_minuto') != "0" ) ? $this->input->post('cron_minuto') : (int)$this->input->post('cron_minuto') ;
 //                if((int)$this->input->post('cron_diames')){
 //                	$cron_date['diames'] = (int)$this->input->post('cron_diames');
 //                	$cron_date['diasemana'] = "*";
 //                }else{ 
 //                	$cron_date['diames'] = "*";
 //                	$cron_date['diasemana'] = (int)$this->input->post('cron_diasemana');
 //                }
                
	// 			//Cron date config 
 //                $trabajo['cron_date'] = json_encode($cron_date);
 //                $trabajo['cron_expression'] = $cron_date['minuto']." ".$cron_date['hora']." ".$cron_date['diames']." ".$cron_date['mes']." ".$cron_date['diasemana'];
                
 //                //Trabajo id (cronjob's name)
	// 			$guardar = $this->cms->add_trabajo($trabajo);

 //               	$trabajo['cron_url'] = site_url("")."ejecutar_trabajo/".$guardar;
                
 //                //Set cronjob in easycron
 //                $cron_result = $this->cms->set_cronjob($guardar, $trabajo['cron_expression'], $trabajo['cron_url']);
 //                if($cron_result["status"] === "success"){ 
 //                	$cron_date["id"] = $cron_result["cron_job_id"];
 //                	$saveId = $this->cms->save_cronconfig($guardar,json_encode($cron_date));
 //                } 
                
	// 			if( $guardar == FALSE ){
	// 				echo 'x02 - La información del trabajo no puedo ser actualizada';
	// 			} else {	
	// 				$indice=0;
	// 				$url = utf8_encode( file_get_contents( $this->input->post( 'url-origen' ) ) );
	// 				$pos = strpos($url, '(');
	// 					if($pos > -1 && (substr($url, -1)===")")){
	// 						$rest = substr($url, $pos+1, -1);
	// 					}else{
	// 						$rest = $url;
	// 					}
	// 					if($campos_orig = json_decode($rest, TRUE)){
	// 						if(!empty($campos_orig[0])){
	// 							for ($i=0; $i < count($campos_orig) ; $i++) {
	// 								foreach ($campos_orig[$i] as $key => $value) {
	// 									for ($j=0; $j < count($elegidos) ; $j++) {
	// 										$tmp=0;
	// 										if(count($elegidos[$j])>$indice){
	// 											if($elegidos[$j][$indice] === (string)$key){
	// 												$tmp = $tmp + 1;
	// 												break;
	// 											}
	// 										}
	// 									}								
	// 									if($tmp>0){
	// 										if(is_array($value)){
	// 											$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 										}
	// 									}else{
	// 										unset($campos_orig[$i][$key]);										
	// 									}
	// 								}
	// 							}
	// 						}else{
	// 							foreach ($campos_orig as $key => $value) {
	// 								for ($j=0; $j < count($elegidos) ; $j++) {
	// 									$tmp=0;
	// 									if(count($elegidos[$j])>$indice){
	// 										if($elegidos[$j][$indice] === (string)$key){
	// 											$tmp = $tmp + 1;
	// 											break;
	// 										}
	// 									}
	// 								}								
	// 								if($tmp>0){
	// 									if(is_array($value)){
	// 										$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 									}
	// 								}else{
	// 									unset($campos_orig[$key]);										
	// 								}
	// 							}
	// 						}
	// 					}else{
	// 						$xml=simplexml_load_file($this->input->post('url-origen'));
	// 						if($xml->channel){
	// 							$rest=json_encode($xml);
	// 							$campos_orig =json_decode($rest, TRUE);
	// 						}else{
	// 							$rest=json_encode($xml);
	// 							$campos_orig =json_decode($rest, TRUE);
	// 						}
	// 						if(!empty($campos_orig[0])){
	// 							for ($i=0; $i < count($campos_orig) ; $i++) {
	// 								foreach ($campos_orig[$i] as $key => $value) {
	// 									for ($j=0; $j < count($elegidos) ; $j++) {
	// 										$tmp=0;
	// 										if(count($elegidos[$j])>$indice){
	// 											if($elegidos[$j][$indice] === (string)$key){
	// 												$tmp = $tmp + 1;
	// 												break;
	// 											}
	// 										}
	// 									}								
	// 									if($tmp>0){
	// 										if(is_array($value)){
	// 											$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 										}
	// 									}else{
	// 										unset($campos_orig[$i][$key]);										
	// 									}
	// 								}
	// 							}
	// 						}else{
	// 							foreach ($campos_orig as $key => $value) {
	// 								for ($j=0; $j < count($elegidos) ; $j++) {
	// 									$tmp=0;
	// 									if(count($elegidos[$j])>$indice){
	// 										if($elegidos[$j][$indice] === (string)$key){
	// 											$tmp = $tmp + 1;
	// 											break;
	// 										}
	// 									}
	// 								}								
	// 								if($tmp>0){
	// 									if(is_array($value)){
	// 										$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 									}
	// 								}else{
	// 									unset($campos_orig[$key]);										
	// 								}
	// 							}
	// 						}
	// 					}
						
	// 					for ($i=0; $i < count($formatos) ; $i++) {
	// 						if($formatos[$i]==='xml'){
	// 							$this->convert_xml($campos_orig, strtolower( url_title( $trabajo['nombre'] ) ));
	// 						}
	// 						if($formatos[$i]==='rss2'){
	// 							$this->convert_rss($campos_orig,strtolower( url_title( $trabajo['nombre'] ) ), $trabajo['claves_rss'], $trabajo['valores_rss']);
	// 						}
	// 						if($formatos[$i]==='json'){
	// 							$this->convert_json($campos_orig, strtolower( url_title( $trabajo['nombre'] ) ));
	// 						}
	// 						if($formatos[$i]==='jsonp'){
	// 							$this->convert_jsonp($campos_orig,strtolower( url_title( $trabajo['nombre'] ) ), $this->input->post('nom_funcion'));
	// 						}
	// 					}
	// 			}
	// 			echo TRUE;
	// 		} else {
	// 			echo validation_errors('<span class="error">','</span>');
	// 		}
	// 	}
	// }
	// 
	public function validar_form_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');
			//$this->form_validation->set_rules('claves', 'Campos seleccionados', 'required|xss_clean');
			if ( ! empty( $this->input->post('formato') ) ){
				if ( in_array('rss2', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('valores_rss[]', 'Campos adicionales para RSS', 'required|xss_clean');
				}

				if ( in_array('jsonp', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('nom_funcion', 'Campos adicionales para JSONP', 'required|min_length[3]|xss_clean');
				}
			}
			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 			= $this->session->userdata('uid');
				$trabajo['nombre']   			= $this->input->post('nombre');
				$trabajo['slug_nombre_feed']	= url_title( $this->input->post('nombre'), 'dash', TRUE );
				//$trabajo['url-origen']   		= $this->input->post('url-origen');
				$trabajo['url-origen']   		= 'http://feeds.esmas.com/data-feeds-esmas/ipad/telenovelas.js';
				$trabajo['categoria']   		= $this->input->post('categoria');
				$trabajo['vertical']   			= $this->input->post('vertical');
				//$trabajo['feed_tipo']			= $this->input->post('tipo_feed_entrada');
				//$trabajo['campos'] 			= json_encode( $this->input->post('claves') );
				$trabajo['campos']				= $this->input->post('responseJson');
				$campos = json_decode( $trabajo['campos'] );
				$tree = $this->session->userdata('tree');
				$tree = unserialize( $tree );
				foreach ( $campos->info as $campo ){
					$this->selected( $tree, $campo->identifier );
				}

				$jsonStr = "[";
				$jsonStr .= $this->treeBuild($tree, true);

				if (json_decode($jsonStr)) {
					$jsontmp = $jsonStr;
				} else {
					$i = -20;

					$jsontmp = substr($jsonStr, 0, $i) . "]}]";

					//$jsonStr .= "]}]";

					while(!json_decode($jsontmp)) {
						$i++;
						$jsontmp = substr($jsonStr, 0, $i) . "]}]";
					}
				}

				$jsontmp = '{"category": ' . $jsontmp . '}';


				$json = (array)json_decode($jsontmp);

				$content = file_get_contents_curl( $trabajo['url-origen'] );
				$content = (array)json_decode($content);
				// Armamos la clase con los valores de constructor.
				$treeMatch = new TreeMatch($content,  $json);
				// Número de elementos
				$totalItems = count($json['category']);
				for($i = 0; $i<$totalItems; $i++) {
					if (property_exists($json['category'][$i], 'program')) {
						foreach ($content['category'][0]->program as $key => $value) {
							new TreeMatch($content['category'][0]->program[$key], $json['category'][$i]->program);
							
							$totalItemsCategory = count($json['category'][$i]->program);

							for($j = 0; $j<$totalItemsCategory; $j++) {
								if (property_exists($json['category'][$i]->program[$j], 'videos')) {
									foreach ($content['category'][0]->program[$key]->videos as $__key => $__value) {
										new TreeMatch($content['category'][0]->program[$key]->videos[$__key], $json['category'][$i]->program[$j]->videos);


										$totalItemsCategoryVideos = count($json['category'][$i]->program[$j]->videos);
										
										for($k = 0; $k<$totalItemsCategoryVideos; $k++) {
											if (property_exists($json['category'][$i]->program[$j]->videos[$k], 'urls')) {
												foreach ($content['category'][0]->program[$key]->videos[$__key]->urls as $___key => $___value) {
													new TreeMatch($content['category'][0]->program[$key]->videos[$__key]->urls, $json['category'][$i]->program[$j]->videos[$k]->urls);
												}
											}
										}
									}
								}
							}

						}
					}
				}
				$content['category'] = $content['category'][0];

				$content = json_encode($content);

				$trabajo['json_output']			= $content;
				//$trabajo['feed_salida']		= campos_seleccionados( $trabajo['campos'], $trabajo['json_entrada']);
                $trabajo['formatos'] 			= $this->input->post('formato');
                $trabajo['jsonp_function']		= $this->input->post('nom_funcion');
                $trabajo['valores_rss'] 		= $this->input->post('valores_rss');
                $trabajo['claves_rss'] 			= $this->input->post('claves_rss');

				$trabajo 						= $this->security->xss_clean( $trabajo );
				// $this->ftp->connect( $this->netstorage );
				// $files = $this->ftp->list_files( $this->storage_root );
				// print_r( $files );die;
				$guardar 						= $this->cms->add_trabajo( $trabajo );
				if ( $guardar !== FALSE ){
					$salidas = convert_formats( $trabajo['json_output'], $trabajo['formatos'], $trabajo['jsonp_function'], $trabajo['valores_rss'], $trabajo['claves_rss'] );
					if ( ! file_exists( './outputs/' . $trabajo['categoria'] ) ){
						mkdir( './outputs/' . $trabajo['categoria'] );
					}

					if ( ! file_exists( './outputs/' . $trabajo['categoria'] . '/' . $trabajo['vertical'] ) ){
						mkdir( './outputs/' . $trabajo['categoria'] . '/' . $trabajo['vertical'] );
					}

					if ( ! file_exists( './outputs/' . $trabajo['categoria'] . '/' . $trabajo['vertical'] . '/' . $trabajo['usuario'] ) ){
						mkdir( './outputs/' . $trabajo['categoria'] . '/' . $trabajo['vertical'] . '/' . $trabajo['usuario'] );
					}

					$salidas = json_decode( $salidas );
					foreach ( $salidas as $salida ){
						$open = fopen( "./outputs/" . $trabajo['categoria'] . "/" . $trabajo['vertical'] . "/" . $trabajo['usuario'] . "/" . $trabajo['slug_nombre_feed'] . $salida->extension, "w" );
						$final = $salida->output;
						fwrite( $open, stripslashes( $final ) );
						fclose( $open );
					}
					echo TRUE;
				} else {
					echo '<span class="error">Ocurrió un problema al intentar guardar el <b>Trabajo</b></span>';
				}
			} else {
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [validar_form_editar_trabajo description]
	 * @return [type] [description]
	 */
	// public function validar_form_editar_trabajo(){
	// 	if ( $this->session->userdata('session') !== TRUE ){
	// 		redirect('login');
	// 	} else {
	// 		$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'required|min_length[3]|xss_clean');
	// 		$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
	// 		$this->form_validation->set_rules('categoria', 'Cetegoría', 'required|xss_clean');
	// 		$this->form_validation->set_rules('vertical', 'Vertical', 'required|xss_clean');
	// 		$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');

	// 		if ( $this->form_validation->run() === TRUE ){
	// 			$trabajo['usuario'] 		= $this->session->userdata('uid');
	// 			$trabajo['nombre']   		= $this->input->post('nombre');
	// 			$trabajo['url-origen']   	= $this->input->post('url-origen');
	// 			$trabajo['categoria']   	= $this->input->post('categoria');
	// 			$trabajo['vertical']   		= $this->input->post('vertical');
	// 			$trabajo['campos'] 			= $this->input->post('claves');
	// 			$trabajo['uid_trabajo']    	= $this->input->post('id_trabajo');
				
	// 			$formats['campos'] 			= $this->input->post('claves'); 
 //                $formats['formatos'] 		= $this->input->post('formato'); 
 //                $formats['valores_rss'] 	= $this->input->post('valores_rss'); 
 //                $formats['claves_rss'] 		= $this->input->post('claves_rss'); 
                
 //                $trabajo['formato_salida'] = json_encode($formats);

	// 			$elegidos=[];
	// 			foreach ($trabajo['campos']  as $key => $value) {
	// 				$elegidos[]=explode(",",$value);
	// 			}
	// 			$formatos=[];
	// 			foreach ($formats['formatos']  as $key => $value) {
	// 				$formatos[]=$value;
	// 			}

 //                $cron_date['mes'] = ($this->input->post('cron_mes')!=="*") ? (int)$this->input->post('cron_mes'):"*";
 //                $cron_date['hora'] =  ($this->input->post('cron_hora')!=="*") ? (int)$this->input->post('cron_hora') : "*";
 //                $cron_date['minuto'] = (is_string($this->input->post('cron_minuto')) && $this->input->post('cron_minuto') != "0" ) ? $this->input->post('cron_minuto') : (int)$this->input->post('cron_minuto') ;
 //                if((int)$this->input->post('cron_diames')){
 //                	$cron_date['diames'] = (int)$this->input->post('cron_diames');
 //                	$cron_date['diasemana'] = "*";
 //                }else{ 
 //                	$cron_date['diames'] = "*";
 //                	$cron_date['diasemana'] = (int)$this->input->post('cron_diasemana');
 //                }
                
 //                //Cron date config 
 //                $trabajo['cron_date'] = json_encode($cron_date);
 //                $trabajo['cron_expression'] = $cron_date['minuto']." ".$cron_date['hora']." ".$cron_date['diames']." ".$cron_date['mes']." ".$cron_date['diasemana'];
                
 //                //Trabajo id (cronjob's name)
	// 			$guardar = $this->cms->update_trabajo($trabajo);

 //               	 $trabajo['cron_url'] = site_url("")."ejecutar_trabajo/".$guardar;
                
 //                //Set cronjob in easycron
 //                $cron_result = $this->cms->set_cronjob($guardar, $trabajo['cron_expression'], $trabajo['cron_url']);
 //                if($cron_result["status"] === "success"){ 
 //                	$cron_date["id"] = $cron_result["cron_job_id"];
 //                	$saveId = $this->cms->save_cronconfig($guardar,json_encode($cron_date));
 //                } 
                
	// 			if ( $guardar == FALSE ){
	// 				$data['usuario'] 	= $this->session->userdata('nombre');
	// 				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
	// 				$this->load->view('cms/admin/nuevo_trabajo',$data);
	// 			} else {	
	// 				$indice = 0;
	// 				$url = utf8_encode(file_get_contents($this->input->post('url-origen')));
	// 				$pos = strpos($url, '(');
	// 					if($pos > -1 && (substr($url, -1)===")")){
	// 						$rest = substr($url, $pos+1, -1);
	// 					}else{
	// 						$rest = $url;
	// 					}

	// 					if ( $campos_orig = json_decode($rest, TRUE ) ){
	// 						if(!empty($campos_orig[0])){
	// 							for ( $i = 0; $i < count( $campos_orig ) ; $i++) {
	// 								foreach ( $campos_orig[$i] as $key => $value ){
	// 									for ( $j = 0; $j < count( $elegidos ) ; $j++ ){
	// 										$tmp = 0;
	// 										if ( count($elegidos[$j]) > $indice ){
	// 											if ( $elegidos[$j][$indice] === (string)$key ){
	// 												$tmp = $tmp + 1;
	// 												break;
	// 											}
	// 										}
	// 									}
	// 									if ( $tmp > 0 ){
	// 										if ( is_array( $value ) ){
	// 											$campos_orig[$i][$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 										}
	// 									} else {
	// 										unset( $campos_orig[$i][$key] );
	// 									}
	// 								}
	// 							}
	// 						} else {
	// 							foreach ($campos_orig as $key => $value) {
	// 								for ($j=0; $j < count($elegidos) ; $j++) {
	// 									$tmp=0;
	// 									if(count($elegidos[$j])>$indice){
	// 										if($elegidos[$j][$indice] === (string)$key){
	// 											$tmp = $tmp + 1;
	// 											break;
	// 										}
	// 									}
	// 								}								
	// 								if($tmp>0){
	// 									if(is_array($value)){
	// 										$campos_orig[$key] = $this->arreglo_nuevo($value,$elegidos,$indice + 1);
	// 									}
	// 								}else{
	// 									unset($campos_orig[$key]);										
	// 								}
	// 							}
	// 						}
	// 					}else{
	// 						$xml = simplexml_load_file( $this->input->post('url-origen') );
	// 						if ( $xml->channel ){
	// 							$rest = json_encode( $xml );
	// 							$campos_orig = json_decode( $rest, TRUE );
	// 						} else {
	// 							$rest = json_encode( $xml );
	// 							$campos_orig = json_decode( $rest, TRUE );
	// 						}

	// 						if ( ! empty( $campos_orig[0] ) ){
	// 							for ( $i = 0; $i < count( $campos_orig ) ; $i++ ){
	// 								foreach ( $campos_orig[$i] as $key => $value ){
	// 									for ( $j = 0; $j < count($elegidos) ; $j++ ){
	// 										$tmp = 0;
	// 										if ( count( $elegidos[$j] ) > $indice ){
	// 											if ( $elegidos[$j][$indice] === (string)$key ){
	// 												$tmp = $tmp + 1;
	// 												break;
	// 											}
	// 										}
	// 									}
	// 									if ( $tmp > 0 ){
	// 										if ( is_array( $value ) ){
	// 											$campos_orig[$i][$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
	// 										}
	// 									} else {
	// 										unset( $campos_orig[$i][$key] );
	// 									}
	// 								}
	// 							}
	// 						} else {
	// 							foreach ( $campos_orig as $key => $value ){
	// 								for ( $j = 0; $j < count( $elegidos ) ; $j++ ){
	// 									$tmp = 0;
	// 									if ( count( $elegidos[$j] ) > $indice ){
	// 										if ( $elegidos[$j][$indice] === (string)$key ){
	// 											$tmp = $tmp + 1;
	// 											break;
	// 										}
	// 									}
	// 								}
	// 								if ( $tmp > 0 ){
	// 									if ( is_array( $value ) ){
	// 										$campos_orig[$key] = $this->arreglo_nuevo( $value, $elegidos, $indice + 1 );
	// 									}
	// 								} else {
	// 									unset( $campos_orig[$key] );
	// 								}
	// 							}
	// 						}
	// 					}
						
	// 					for ($i=0; $i < count($formatos) ; $i++) {
	// 						if($formatos[$i]==='xml'){
	// 							$this->convert_xml( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ) );
	// 						}
	// 						if($formatos[$i]==='rss2'){
	// 							$this->convert_rss( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ), $trabajo['claves_rss'], $trabajo['valores_rss'] );
	// 						}
	// 						if($formatos[$i]==='json'){
	// 							$this->convert_json( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ) );
	// 						}
	// 						if($formatos[$i]==='jsonp'){
	// 							$this->convert_jsonp( $campos_orig, strtolower( url_title( $trabajo['nombre'] ) ), $this->input->post( 'nom_funcion' ) );
	// 						}
	// 					}
 //                 }

	// 		     redirect('trabajos');
	// 		} else {
	// 			$data['usuario'] 	= $this->session->userdata('nombre');
	// 			$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
	// 			$this->load->view('cms/admin/nuevo_trabajo',$data);
	// 		}
	// 	}
	// }

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
	 * [mapAttributes description]
	 * @param  [type] $feed [description]
	 * @return [type]       [description]
	 */
	public function mapAttributes( $feed ){
		$campos_orig 	= json_decode( $feed, TRUE );
		$campos 		= [];
		$items 			= count( $campos_orig );
		if ( ! empty( $campos_orig[0] ) ){
			for ($i = 0; $i < count( $campos_orig); $i++){
				foreach ( $campos_orig[$i] as $key => $value ){
					if ( is_array( $value ) ){
						if ( ! empty($campos[$key] ) ){
							$campos[$key] = $this->claves( $value, $campos[$key] );
						}else{
							$campos[$key] = $this->claves( $value, $campos[$key] = [] );
						}
					}else{
						if ( ! array_key_exists($key, $campos) ){
							$campos[$key] = '';
						}
					}
				}
			}
		}else{
			foreach ($campos_orig as $key => $value ){
				if ( is_array( $value ) ){
					if ( ! empty( $campos[$key] ) ){
						$campos[$key] = $this->claves( $value, $campos[$key] );
					}else{
						$campos[$key] = $this->claves( $value, $campos[$key] = [] );
					}
				}else{
					if( ! array_key_exists( $key, $campos ) ){
						$campos[$key] = '';
					}
				}
			}
		}

		return $campos;
	}

	/**
	 * [array_unique_multidimensional description]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	public function array_unique_multidimensional( $input ){
		$serialized = array_map( 'serialize', $input );
		$unique = array_unique( $serialized );
		return array_intersect_key( $input, $unique );
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

	/**
	 * [convert_xml description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $nombre  [description]
	 * @return [type]          [description]
	 */
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

	/**
	 * [formato_xml description]
	 * @param  [type] $arreglo [description]
	 * @return [type]          [description]
	 */
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

	/**
	 * [convert_rss description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $nombre  [description]
	 * @param  [type] $nodos   [description]
	 * @param  [type] $valores [description]
	 * @return [type]          [description]
	 */
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

	/**
	 * [formato_rss description]
	 * @param  [type] $arreglo [description]
	 * @return [type]          [description]
	 */
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

	/**
	 * [convert_json description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $nombre  [description]
	 * @return [type]          [description]
	 */
	function convert_json( $arreglo, $nombre ){
		$open = fopen( "./outputs/" . $nombre . "json.js", "w" );
		$final= json_encode( $arreglo );
		fwrite( $open, stripslashes( $final ) );
		fclose( $open );
	}

	/**
	 * [convert_jsonp description]
	 * @param  [type] $arreglo [description]
	 * @param  [type] $nombre  [description]
	 * @param  [type] $funcion [description]
	 * @return [type]          [description]
	 */
	function convert_jsonp( $arreglo, $nombre, $funcion ){
		$open = fopen( "./outputs/" . $nombre . "jsonp.js", "w");
		$final= $funcion . "(" . json_encode( $arreglo ). ")";
		fwrite( $open, stripslashes( $final ) );
		fclose( $open );
	}

	/**
	 * Construcción de cuerpo
	 * @param  Tree $tree
	 * @param boolean $selected Solo elementos seleccionados
	 * @return string
	 */
	function treeBuild ( $tree, $selected = FALSE ) {
		if ( $selected === false) {
			return $this->wSelected( $tree );
		} else {
			return $this->yselected( $tree );
		}
	}

	/**
	 * [wSelected description]
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	function wSelected( $tree ){
		foreach ( $tree->getParameters()->getChildrens() as $key => $value ) {
			if ( ! isset( $str ) ) $str = ""; 

			$str .=  '{"identifier": "' . $value . '",  "name": "' . $value->getName() . '", "type":';
			if ( $value->getType() === 'folder' ) {
				$str .=  '"' . $value->getType() . '",' . '"additionalParameters": { "children":[';
				$str .= $this->treeBuild( $value );
			} else {
				$str .=  '"item"';
				$str .=  '},';
			}
		}
		$str .=  ']}},';
		return $str;
	}

	/**
	 * [yselected description]
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	function yselected( $tree ){
		foreach ( $tree->getParameters()->getChildrens() as $key => $value ){
			// Si no existe lo creamos
			if ( ! isset( $str ) ) $str = "[!content!]";

			// Si no esta seleccionado entonces vámos por otro elemento.
			if ( $value->getSelected() !== TRUE ) {
				continue;
			}

			// si no tiene hijos pintamos directamente
			if ( ! $value->getChildrensAsSelected() && ! $value->getParent() ){
				$replace =  '{"' . $value->getName() . '": "' . $value->getName() . '"},[!content!]';
			}
			// Si tiene hijos entonces mostramos con un diferente formato.
			elseif ( $value->getChildrensAsSelected() && ! $value->getParent() ){
				$replace =  '{"' . $value->getName() . '": {[!childsContent!]}, [!content!]}';
			}

			$str = preg_replace( '/\[\!content\!\]/i', $replace, $str );
		}

		$str = preg_replace( '/,\[\!content\!\]/i', ']', $str );

		return $str;
	}

	/**
	 * Selecciona un elemento con determinado indice
	 */
	function selected ($tree, $index) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if ((string)$value === $index) {
				$value->setSelected();
				return $value;
			}

			if ( $value->getType() === 'folder' ){
				$this->selected( $value, $index );
			}
		}
	}
}