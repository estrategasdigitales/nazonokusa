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
	 * Constructor de la clase, se inicializan valores, se cargan librerías y helpers extras
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
				'debug'		=> TRUE
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
	 * [detectar_campos description]
	 * @return [type] [description]
	 */
	public function detectar_campos(){
		$url = base_url() . 'nucleo/feed_service?url=' . urlencode( base64_encode( $this->input->post('url') ) );
		$content = json_decode( file_get_contents_curl( $url ) );
		$tree = new Tree( $content, true );
		$arbol = array('tree' => serialize( $tree ) );
		$nodes = array('nodes' => serialize( $tree->getNodes() ) );
		$this->session->set_userdata( $arbol );
		$this->session->set_userdata( $nodes );
		$jsonStr = "[";
		$jsonStr .= $this->treeBuild( $tree );
		$jsonStr = substr($jsonStr, 0, -5) . "]";
		$jsonStr =  preg_replace("/,\]\}/", "]}", $jsonStr);

		$data = array(
			'nodes' => $jsonStr
		);

		$this->load->view('cms/tree_feed', $data);
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
			$uid_trabajo = base64_decode( $this->input->post('token') );
			$eliminar = $this->cms->delete_trabajo($uid_trabajo);
			if ( $eliminar !== FALSE ){
				/** Aquí debe ir el código para borrar los archivos de salida del disco duro de la instancia, se debe consultar la base de datos **/
				/** Aquí se debe incluir el código para borrar el cron de la instancia donde se guardan **/
				// $trabajo = $this->cms->get_trabajo_editar( $uid_trabajo );
				// $cronjob = json_decode($trabajo->cron_config, true);
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podido eliminar el <b>trabajo</b>.</span>';
			}
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
					$rss = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $rss['rss']['channel']['item'] ) );
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes( $contents );
				} else {
					$xml = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $xml ) );
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
			$contenido_feed = json_decode( $url );
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos + 1, -1 );
				$contenido_feed = json_decode( $feed );
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = $this->xml_2_array->createArray( $url );
					$feed[] = $rss['rss']['channel']['item'];
					$contenido_feed = $feed;
				} else {
					$xml = $this->xml_2_array->createArray( $url );
					$feed[] = $xml;
					$contenido_feed = $xml;
				}
			}
		}
		$data = array( 'contenido_feed' => $contenido_feed );
		$this->load->view('cms/service_content', $data );
	}

	public function job_execute(){
		$token = urldecode( base64_decode( $this->input->get('token') ) )
		$trabajoObject = $this->cms->get_trabajo_ejecutar( $token );
		/**
		 * Se generan los archivos de salida en outputs
		 */
		$this->harddisk_write( $trabajoObject );
	}

	/**
	 * [job_process description]
	 * @return [type] [description]
	 */
	public function job_process(){
		$job['status'] 	= $this->input->post('status');
		$job['uidjob'] 	= base64_decode( $this->input->post('uidjob') );
		$process 		= $this->cms->active_job( $job );
		if ( $process === TRUE ){
			if ( $job['status'] == 1 ){
				$trabajoObject = $this->cms->get_trabajo_ejecutar( $job['uidjob'] );
				/**
				 * Se generan los archivos de salida en outputs
				 */
				$this->harddisk_write( $trabajoObject );
			}
			echo TRUE;
		} else {
			echo '<span class="error">Ocurrió un problema al intentar <b>activar/desactivar</b> la tarea. </span>';
		}
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
     * [validar_form_trabajo description]
     * @return [type] [description]
     */
	public function validar_form_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre del Trabajo', 'trim|required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('formato', 'Formato', 'required|xss_clean');
			$this->form_validation->set_rules('claves', 'Campos seleccionados', 'required|xss_clean');
			if ( ! empty( $this->input->post('formato') ) ){
				if ( in_array('rss', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('valores_rss[]', 'Campos adicionales para RSS', 'required|xss_clean');
				}

				if ( in_array('jsonp', $this->input->post('formato' ) ) ){
					$this->form_validation->set_rules('nom_funcion', 'Campos adicionales para JSONP', 'trim|alpha_dash|required|min_length[3]|xss_clean');
				}
			}
			$cronjob_config = $this->input->post('cron_minuto').' '.$this->input->post('cron_hora').' '.$this->input->post('cron_diames').' '.$this->input->post('cron_mes').' '.$this->input->post('cron_diasemana');

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
				//$trabajo['feeds_output']		= conversion_feed_output( $this->input->post('formato'), $trabajo['json_output'], $this->input->post('nom_funcion'), $this->input->post('valores_rss'), $this->input->post('claves_rss'), $this->url_storage, $trabajo['usuario'], $trabajo['categoria'], $trabajo['vertical'], $trabajo['slug_nombre_feed'] );
				$trabajo['cron_config']			= $cronjob_config;
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

	private function harddisk_write( $trabajo ){
		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria ) ){
			mkdir( './outputs/' . $trabajo->slug_categoria );
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical ) ){
			mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical );
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario ) ){
			mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario );
		}
		$feed_output = 'outputs/'. $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
		$ftp_server = $_SERVER['STORAGE_URL'];
		$ftp_user_name = $_SERVER['STORAGE_USER'];
		$ftp_user_pass = $_SERVER['STORAGE_PASS'];
		$ftp_conn = ftp_connect($ftp_server);
		$login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);
		$ftpath = '/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
		$this->mksubdirs( $ftp_conn, '/', $ftpath );
		ftp_close($ftp_conn);
		$formatos = json_decode( $trabajo->formatos );
		$output = $this->getItems( json_decode( $trabajo->campos_seleccionados ), $trabajo->url_origen );
		foreach ( $formatos as $formato ){
			switch ( $formato->formato ) {
				case 'xml':
					$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.xml', "w" );
					$array = json_decode( $output, TRUE );
					$final = array_to_xml( $array )->saveXML();
					fwrite( $open, stripslashes( $final ) );
					fclose( $open );
					$this->upload_netstorage($feed_output, $ftpath);
					break;
				case 'rss':
					$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.xml', "w" );
					$array = json_decode( $output, TRUE );
					$formatos = json_decode( $trabajo->formatos );
					foreach ( $formatos as $formato ){
						$final = array_to_rss( $formato->valores_rss, $array )->saveXML();
					}
					fwrite( $open, stripslashes( $final ) );
					fclose( $open );
					$this->upload_netstorage($feed_output, $ftpath);
					break;
				case 'json':
					$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.js', "w" );
					$final = $output;
					fwrite( $open, stripslashes( $final ) );
					fclose( $open );
					$this->upload_netstorage($feed_output, $ftpath);
					break;
				case 'jsonp':
					$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.js', "w" );
					$final = $formato->funcion . '(' . $output . ')';
					fwrite( $open, stripslashes( $final ) );
					fclose( $open );
					$this->upload_netstorage($feed_output, $ftpath);
					break;
			}
		}
	}

	private function mksubdirs( $ftpcon, $ftpbasedir, $ftpath ){
		@ftp_chdir($ftpcon, $ftpbasedir);
		$parts = explode('/', $ftpath);
		foreach($parts as $part){
			if(!@ftp_chdir($ftpcon, $part)){
				ftp_mkdir($ftpcon, $part);
				ftp_chdir($ftpcon, $part);
				//ftp_chmod($ftpcon, 0777, $part);
			}
		}
	}

	/**
	 * [getItems description]
	 * @param  [type] $campos  [description]
	 * @param  [type] $urlFeed [description]
	 * @return [type]          [description]
	 */
	private function getItems( $campos, $urlFeed ){
		$tree = $this->session->userdata('tree');
		$nodesSelected = $this->session->userdata('nodes');
		$tree = unserialize( $tree );
		$nodesSelected = unserialize( $nodesSelected );
		foreach ( $campos->info as $campo ){
			$this->selected( $tree, $campo->identifier, $nodesSelected );
		}
		$feed = base_url() . 'nucleo/feed_service_content?url=' . urlencode( base64_encode( $urlFeed ) );
		//print_r( $feed );die;
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

		if ( json_decode($jsonStr) ) {
			$jsontmp = $jsonStr;
		} else {
			$i = -20;

			$jsontmp = substr($jsonStr, 0, $i) . "]}]";
			while( ! json_decode( $jsontmp ) ) {
				$i++;
				$jsontmp = substr($jsonStr, 0, $i) . "]}]";
			}
		}

		$jsontmp = '{"category": ' . $jsontmp . '}';
		
		$jsonValidate = new TreeFeed( $feed, 1, $jsontmp, 'category' );
		return json_encode( $feed[0] );
	}

	/**
	 * Escribe el crontab en el servidor
	 * @param [type] $config_cron    [description]
	 * @param [type] $trabajo_url_id [description]
	 */
	private function set_cron($config_cron, $trabajo_url_id){
		$config_cron= '*/2 * * * *';
		$trabajo_url_id = 'curl '. base_url() . 'job_process?uidjob=';

		$host= 		$_SERVER['CRON_HOST'];
		$port=		$_SERVER['CRON_HOST_PORT'];
		$username=	$_SERVER['CRON_HOST_USER'];	
		$password=	$_SERVER['CRON_HOST_PASS'];
		
		$cron_setup = new cron_manager();
		// Si no se puede conectar, enviar error a pantalla.
		$resp_con = $cron_setup->connect($host, $port, $username, $password); 
		//print_r($resp_con);
		
		$path 	 = $_SERVER['CRON_PATH'];
		$handle	 = $_SERVER['CRON_HANDLE'];
		if ( $trabajo_url_id && $trabajo_url_id != '' ){
			$cron_setup->write_to_file($path, $handle); // Verifica que el archivo exista y este activo, si no, lo crea y lo activa
			
			$nueva_tarea = $cron_setup->append_cronjob( $config_cron. ' ' . $trabajo_url_id );
			//$conectar->append_cronjob('*/2 * * * * date >> ~/testCron.log');
		}
	}

	/**
	 * Elimina el crontab en el servidor
	 * @param  [type] $config_cron    [description]
	 * @param  [type] $trabajo_url_id [description]
	 * @return [type]                 [description]
	 */
	private function unset_cron($config_cron, $trabajo_url_id){
		// $config_cron= '*/2 * * * *';
		// $trabajo_url_id = 'curl '. base_url() . 'job_process?uidjob='

		// $host = 	$_SERVER['CRON_HOST'];
		// $port =		$_SERVER['CRON_HOST_PORT'];
		// $username=	$_SERVER['CRON_HOST_USER'];	
		// $password=	$_SERVER['CRON_HOST_PASS'];
		
		// $cron_setup = new cron_manager();
		// // Si no se puede conectar, enviar error a pantalla.
		// $resp_con = $cron_setup->connect($host, $port, $username, $password); 
		// //print_r($resp_con);
		
		// $path 	 = $_SERVER['CRON_PATH'];
		// $handle	 = $_SERVER['CRON_HANDLE'];
		// if ( $trabajo_url_id && $trabajo_url_id != '' ){
		// 	$cron_setup->write_to_file($path, $handle); // Verifica que el archivo exista y este activo, si no, lo crea y lo activa
		// 	$quitar_tarea = $cron_setup->remove_cronjob( $config_cron. ' ' . $trabajo_url_id );
			
		// }
	}

	/**
	 * Sube los archivos al netstorage
	 * @return [type] [description]
	 */
	private function upload_netstorage( $file, $ftpath ){
		$this->load->library('ftp');
		$this->ftp->connect( $this->netstorage );
		$this->ftp->mirror( './' . $file, '/' . $ftpath, 'ascii', 0775 );
		$this->ftp->close();
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
	 * [selected description]
	 * @param  [type]  $tree          [description]
	 * @param  [type]  $index         [description]
	 * @param  [type]  $nodesSelected [description]
	 * @param  boolean $depth         [description]
	 * @return [type]                 [description]
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
	 * Funcion extra para form_validate, para detectar si en un selector se ha elegido algo diferente de cero o la opción por defecto
	 * @param  [type] $str valor
	 * @return [type]      Regresa FALSE si el dato no es válido, TRUE si el dato es válido
	 */
	function valid_option($str) {
        if ($str == 0) {
            $this->form_validation->set_message('valid_option', '<b class="requerido">*</b> Es necesario que selecciones una <b>%s</b>.');
            return FALSE;
        } else {
            return TRUE;
        }
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
}