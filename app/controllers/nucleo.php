<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH . '../app/libraries/Tree.php');
require_once( BASEPATH . '../app/libraries/TreeMatch.php');
require_once( BASEPATH . '../app/libraries/TreeFeed.php');

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
		//$this->url_storage = $_SERVER['STORAGE_URL'];
		$this->url_storage = 'outputs';
		/** Configuracion de conexión a netstorage */
		$this->netstorage = array(
		 		'hostname' 	=> $_SERVER['STORAGE_URL'],
				'username' 	=> $_SERVER['STORAGE_USER'],
				'password' 	=> $_SERVER['STORAGE_PASS'],
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

	public function job_process(){
		$job['status'] 	= $this->input->post('status');
		$job['uidjob'] 	= base64_decode( $this->input->post('uidjob') );
		$process 		= $this->cms->active_job( $job );
		if ( $process === TRUE ){
			if ( $job['status'] == 1 ){
				$trabajoObject = $this->cms->get_trabajo_ejecutar( $job['uidjob']);
				/**
				 * Se generan los archivos de salida en outputs
				 */
				//$trabajos = json_decode( $trabajo );
				if ( ! file_exists( './outputs/' . $trabajoObject->uid_categoria ) ){
					mkdir( './outputs/' . $trabajoObject->uid_categoria );
				}

				if ( ! file_exists( './outputs/' . $trabajoObject->uid_categoria . '/' . $trabajoObject->uid_vertical ) ){
					mkdir( './outputs/' . $trabajoObject->uid_categoria . '/' . $trabajoObject->uid_vertical );
				}

				if ( ! file_exists( './outputs/' . $trabajoObject->uid_categoria . '/' . $trabajoObject->uid_vertical . '/' . $trabajoObject->uid_usuario ) ){
					mkdir( './outputs/' . $trabajoObject->uid_categoria . '/' . $trabajoObject->uid_vertical . '/' . $trabajoObject->uid_usuario );
				}
				$trabajos = json_decode( $trabajoObject->feeds_output );
				foreach ( $trabajos as $trabajo ){
					$open = fopen( "./" . $trabajo->url, "w" );
					$final = $trabajo->output;
					fwrite( $open, stripslashes( $final ) );
					fclose( $open );
				}
			}
			echo TRUE;
		} else {
			echo '<span class="error">Ocurrió un problema al intentar <b>activar/desactivar</b> la tarea. </span>';
		}
	}

	/**
	 * [feed_service description]
	 * @return [type] [description]
	 */
	public function feed_service(){
		$output = array();
		$url = $this->input->get('url');
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		$url = html_entity_decode( $url );
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
					$rss = new SimpleXMLElement( $url, LIBXML_NOCDATA );
					foreach ( $rss->channel as $item ){
						$feed[] = $this->mapAttributes( json_encode( $item ) );
					}
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes( $contents );
				} else {
					$xml = new SimpleXMLElement( $url, LIBXML_NOCDATA );
					foreach ( $xml as $item ){
						$feed[] = $this->mapAttributes( json_encode( $xml ) );
					}
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes( $contents );
				}
			}
		}
		$data = array( 'indices' => $indices[0] );
		$this->load->view('cms/service', $data );
	}

	/**
	 * [feed_service_content description]
	 * @return [type] [description]
	 */
	public function feed_service_content(){
		$output = array();
		$url = $this->input->get('url');
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		$url = html_entity_decode( $url );
		if ( $feed = json_decode( $url ) ){
			$contenido_feed = $url;
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos + 1, -1 );
				$contenido_feed = json_decode( $feed );
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = new SimpleXMLElement( $url, LIBXML_NOCDATA );
					$contenido_feed = $rss->channel;
				} else {
					$xml = new SimpleXMLElement( $url, LIBXML_NOCDATA );
					$contenido_feed = $xml;
				}
			}
		}
		$data = array( 'contenido_feed' => $contenido_feed );
		$this->load->view('cms/service_content', $data );
	}

	/**
	 * [detectar_campos description]
	 * @return [type] [description]
	 */
	public function detectar_campos(){
		$url = base_url() . 'nucleo/feed_service?url=' . urlencode( base64_encode( $this->input->post('url') ) );
		print_r( $url );die;
		$content = json_decode( file_get_contents_curl( $url ) );
		$tree = new Tree($content, true);
		$arbol = array('tree' => serialize( $tree ) );
		$nodes = array('nodes' => serialize( $tree->getNodes() ) );
		$this->session->set_userdata( $arbol );
		$this->session->set_userdata( $nodes );
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
			$eliminar = $this->cms->delete_trabajo($uid_trabajo);
			if ( $eliminar !== FALSE ){
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podido eliminar el <b>trabajo</b>.</span>';
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

	public function validar_form_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'trim|required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');
			//$this->form_validation->set_rules('claves', 'Campos seleccionados', 'required|xss_clean');
			if ( ! empty( $this->input->post('formato') ) ){
				if ( in_array('rss2', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('valores_rss[]', 'Campos adicionales para RSS', 'required|xss_clean');
				}

				if ( in_array('jsonp', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('nom_funcion', 'Campos adicionales para JSONP', 'trim|alpha_dash|required|min_length[3]|xss_clean');
				}
			}
			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 			= $this->session->userdata('uid');
				$trabajo['nombre']   			= $this->input->post('nombre');
				$trabajo['slug_nombre_feed']	= url_title( $this->input->post('nombre'), 'dash', TRUE );
				$trabajo['url-origen']   		= $this->input->post('url-origen');
				$trabajo['categoria']   		= $this->input->post('categoria');
				$trabajo['vertical']   			= $this->input->post('vertical');
				$trabajo['campos']				= $this->input->post('claves');
				$trabajo['arbol_json']			= base64_decode( $this->input->post('tree_json') );
				$trabajo['json_output']			= $this->getItems( json_decode( $trabajo['campos'] ), $trabajo['url-origen'] );
				$trabajo['formatos']			= formatos_output_seleccionados( $this->input->post('formato'), $this->input->post('nom_funcion'), $this->input->post('valores_rss'), $this->input->post('claves_rss') );
				$trabajo['feeds_output']		= conversion_feed_output( $this->input->post('formato'), $trabajo['json_output'], $this->input->post('nom_funcion'), $this->input->post('valores_rss'), $this->input->post('claves_rss'), $this->url_storage, $trabajo['usuario'], $trabajo['categoria'], $trabajo['vertical'], $trabajo['slug_nombre_feed'] );
				$trabajo 						= $this->security->xss_clean( $trabajo );
				$guardar 						= $this->cms->add_trabajo( $trabajo );
				if ( $guardar !== FALSE ){
					echo TRUE;
				} else {
					echo '<span class="error">Ocurrió un problema al intentar guardar el <b>Trabajo</b></span>';
				}
			} else {
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	private function getItems( $campos, $urlFeed ){
		$tree = $this->session->userdata('tree');
		$nodesSelected = $this->session->userdata('nodes');
		$tree = unserialize( $tree );
		$nodesSelected = unserialize( $nodesSelected );
		foreach ( $campos->info as $campo ){
			$this->selected( $tree, $campo->identifier, $nodesSelected );
		}
		$feed = base_url() . 'nucleo/feed_service_content?url=' . urlencode( base64_encode( $urlFeed ) );
		$feed = json_decode( file_get_contents_curl( $feed ) );

		if ( is_array( $feed ) ){
			$root = array_keys( $feed );
		} else {
			$root = array_keys( get_object_vars( $feed ) );

			$feed  = $feed->$root[0];
		}

		new TreeMatch( $feed, $nodesSelected );

		$jsonStr = "[";
		$jsonStr .= $this->treeBuild($tree, true);

		if (json_decode($jsonStr)) {
			$jsontmp = $jsonStr;
		} else {
			$i = -20;

			$jsontmp = substr($jsonStr, 0, $i) . "]}]";
			while(!json_decode($jsontmp)) {
				$i++;
				$jsontmp = substr($jsonStr, 0, $i) . "]}]";
			}
		}

		$jsontmp = '{"category": ' . $jsontmp . '}';
		
		$jsonValidate = new TreeFeed($feed, 1, $jsontmp, 'category');

		print_r( $feed );die;

		return json_encode( $feed );
	}

	private function jsonContent( $url ){
		$output = array();
		$url = file_get_contents_curl( $url );
		$url = html_entity_decode( utf8_decode( $url ) );
		if ( $feed = json_decode( $url ) ){
			$contenido_feed = $url;
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos + 1, -1 );
				$contenido_feed = $feed;
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = new SimpleXMLElement( $url, LIBXML_NOCDATA );
					$contenido_feed = json_encode( $rss->channel );
				} else {
					$xml = new SimpleXMLElement( $url );
					$contenido_feed = json_encode( $xml, LIBXML_NOCDATA );
				}
			}
		}
		return $contenido_feed;
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
	 * Construcción de cuerpo
	 * @param  Tree $tree
	 * @param boolean $selected Solo elementos seleccionados
	 * @return string
	 */
	function treeBuild ($tree, $selected = false) {
		if ($selected === false) {
			return $this->wSelected($tree);
		} else {
			return $this->yselected($tree);
		}
	}

	/**
	 * [wSelected description]
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	function wSelected($tree) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if (!isset($str)) {
				$str = "";
			}
			$str .=  '{"identifier": "' . $value . '",  "name": "' . $value->getName() . '", "type":';
			if ($value->getType() === 'folder') {
				$str .=  '"' . $value->getType() . '",' . '"additionalParameters": { "children":[';
				$str .= $this->treeBuild($value);
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
	function yselected( $tree ) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			// Si no existe lo creamos
			if (!isset($str)) $str = "[!content!]";

			// Si no esta seleccionado entonces vámos por otro elemento.
			if ($value->getSelected() !== true && $value->getChildrensAsSelected() !== true) {
				continue;
			}

			// si no tiene hijos pintamos directamente
			if (!$value->getChildrensAsSelected() && !$value->getParent()) {
				$replace =  '{"' . $value->getName() . '": "' . $value->getName() . '"},[!content!]';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);
			}
			// El elemento raíz con hijo esta siendo seleccionado
			elseif ($value->getSelected() && !$value->getParameters()->getChildrens()) {
				$replace =  '{"' . $value->getName() . '": "' . $value->getName() . '"},[!content!]';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);
			}
			// Si tiene hijos entonces mostramos con un diferente formato.
			elseif ($value->getChildrensAsSelected()) {
				$replace =  '{"' . $value->getName() . '": [[!childsContent!]},[!content!]}';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);

				$childsStr = $this->treeBuild($value, true);

				$str = preg_replace('/\[\!childsContent\!\]/i', $childsStr, $str);
			}
		}
		$str = preg_replace('/,\[\!content\!\]/i', ']', $str);

		return $str;
	}

	/**
	 * Selecciona un elemento con determinado indice
	 */
	function selected ($tree, $index, &$nodesSelected, $depth = false) {
		if ($depth === false) {
			$depth = 0;
		}

		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if ((string)$value === $index) {
				$item = array_search((string)$value, $nodesSelected[$depth+1]);
				$value->setSelected();

				// actualizamos
				if ($item !== false) {
					if ($value->getParent()) {
						$value->getParent()->setSelected();
						$itemArray = array_search((string)$value->getParent(), $nodesSelected[$depth]);
						if ($itemArray !== false) {
							$nodesSelected[$depth][$itemArray] = $value->getParent();
						}
					}
					$nodesSelected[$depth+1][$item] = $value;
				}
				return $value;
			}
			if ($value->getType() === 'folder') {
				$depth++;
				$this->selected($value, $index, $nodesSelected, $depth);
				$depth--;
			}
		}
	}

	function valid_option($str) {
        if ($str == 0) {
            $this->form_validation->set_message('valid_option', '<b class="requerido">*</b> Es necesario que selecciones una <b>%s</b>.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}