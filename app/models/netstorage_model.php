<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

require_once( BASEPATH . '../app/libraries/Node.php');
require_once( BASEPATH . '../app/controllers/nucleo.php');

class Netstorage_model extends Nucleo {

	private $netstorage;
	private $url_storage;
	private $storage_root;

    function __construct() {
        parent::__construct();
        $this->load->helper( 'file' );
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

    public function index(){
    }

    /**
	 * [harddisk_write description]
	 * @param  [type] $trabajo [description]
	 * @return [type]          [description]
	 */
	public function harddisk_write( $trabajo ){
		$this->load->model( 'alertas_model', 'alertas' );
		$CI =& get_instance();
		$CI->load->model( 'cronlog_model', 'cronlog' );
		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria ) ){
			if ( ! mkdir( './outputs/' . $trabajo->slug_categoria ) ){
				$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E01 - No se ha podido crear el directorio de la categoría');
			}
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical ) ){
			if ( ! mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical ) ){
				$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E01 - No se ha podido crear el directorio de la vertical');
			}
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario ) ){
			if( ! mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario ) ){
				$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E01 - No se ha podido crear el directorio del usuario');
			}
		}
		$feed_output 	= 'outputs/'. $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
		$ftp_server 	= $_SERVER['STORAGE_URL'];
		$ftp_user_name 	= $_SERVER['STORAGE_USER'];
		$ftp_user_pass 	= $_SERVER['STORAGE_PASS'];
		$ftp_conn 		= ftp_connect( $ftp_server, 21, 90 );
		$login 			= ftp_login( $ftp_conn, $ftp_user_name, $ftp_user_pass );
		if ( $login ){
			$ftpath 		= '/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
			$this->mksubdirs( $ftp_conn, '/', $ftpath );
			ftp_close( $ftp_conn );
			switch ( $trabajo->tipo_salida ){
				case 1:
					/**
					 * Aquí se pudiera iniciar la sesión para que el cron trabaje con el tree
					 */
					$content = base_url() . 'nucleo/feed_service_content?url=' . urlencode( base64_encode( $trabajo->url_origen ) );
					$formatos = $this->cms->get_trabajos_formatos( $trabajo->uid_trabajo );
					foreach ( $formatos as $formato ){
						$formato = json_decode( $formato->formato );
						switch ( $formato->format ) {
							case 'xml':
								$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.xml', "w" );
								$array = json_decode( $output, TRUE );
								$final = array_to_xml( $array )->saveXML();
								if ( $this->session->userdata( 'session' ) !== TRUE && ( $final === FALSE || $final === NULL ) ){
									$this->alertas->alerta( $trabajo->uid_trabajo, 'Error al intentar convertir a XML - array_to_xml' );
									$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E03 - No se ha podido obtener el archivo de salida XML / array_to_xml');
									return FALSE;
								}
								fwrite( $open, stripslashes( $final ) );
								fclose( $open );
								$this->upload_netstorage( $feed_output, $ftpath );
								break;
							case 'rss':
								$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.xml', "w" );
								$array = json_decode( $output, TRUE );
								$formatos = json_decode( $trabajo->formatos );
								foreach ( $formatos as $formato ){
									$final = array_to_rss( $formato->valores_rss, $array )->saveXML();
								}
								if ( $this->session->userdata( 'session' ) !== TRUE && ( $final === FALSE || $final === NULL ) ){
									$this->alertas->alerta( $trabajo->uid_trabajo, 'Error al intentar convertir a RSS - array_to_rss' );
									$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E04 - No se ha podido obtener el archivo de salida RSS / array_to_rss');
									return FALSE;
								}
								fwrite( $open, stripslashes( $final ) );
								fclose( $open );
								$this->upload_netstorage( $feed_output, $ftpath );
								break;
							case 'json':
								$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.js', "w" );
								$final = $output;
								if ( $this->session->userdata( 'session' ) !== TRUE && ( $final === FALSE || $final === NULL ) ){
									$this->alertas->alerta($trabajo->uid_trabajo, 'Error al intentar convertir a JSON');
									$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E05 - No se ha podido obtener el archivo de salida JSON');
									return FALSE;
								}
								fwrite( $open, stripslashes( $final ) );
								fclose( $open );
								$this->upload_netstorage( $feed_output, $ftpath );
								break;
							case 'jsonp':
								$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.js', "w" );
								$final = $formato->funcion . '(' . $output . ')';
								if ( $this->session->userdata( 'session' ) !== TRUE && ( $final === FALSE || $final === NULL ) ){
									$this->alertas->alerta($trabajo->uid_trabajo, 'Error al intentar convertir a JSON-P');
									$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E05 - No se ha podido obtener el archivo de salida JSON');
									return FALSE;
								}
								fwrite( $open, stripslashes( $final ) );
								fclose( $open );
								$this->upload_netstorage( $feed_output, $ftpath );
								break;
						}
					}
					break;
				case 2:
				//print_r( $trabajo );die;
					$node = new Node(
							[
								'input' 	=> base_url() . 'nucleo/feed_service_content?url=' . urlencode( base64_encode( $trabajo->url_origen ) ),
								'template' 	=> $trabajo->json_estructura,
								'paths' 	=> base64_decode( $trabajo->campos_seleccionados ),
							]
						);
					$encoding = base64_decode( $trabajo->encoding );
					switch ( $trabajo->formato_salida ) {
						case 'RSS':
							$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-rss.xml';
							$final = $node->toRSS( $file );
							//$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'E06 - No se ha podido obtener el archivo de salida específica RSS / toXML');
							$this->upload_netstorage( $feed_output, $ftpath );
							break;
						case 'XML':
							$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-xml.xml';
							$final = $node->toXML( $file, $encoding );
							$this->upload_netstorage( $feed_output, $ftpath );
							break;
						case 'JSON':
							$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-json.js';
							$final = $node->toJSON( $file );
							//$this->upload_netstorage( $feed_output, $ftpath );
							break;
						case 4:
							# json-p
							break;
					}
					break;
			}
		} else {
			$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'No se ha podido establecer una conexion con el netstorage');
			$this->alertas->alerta($trabajo->uid_trabajo, 'No se ha podido establecer una conexion con el netstorage');
			return FALSE;
		}
	}

	/**
	 * [mksubdirs description]
	 * @param  [type] $ftpcon     [description]
	 * @param  [type] $ftpbasedir [description]
	 * @param  [type] $ftpath     [description]
	 * @return [type]             [description]
	 */
	private function mksubdirs( $ftpcon, $ftpbasedir, $ftpath ){
		if( ! ftp_chdir( $ftpcon, $ftpath ) ){
			@ftp_chdir( $ftpcon, $ftpbasedir, 0777 );
			$parts = explode('/', $ftpath );
			foreach( $parts as $part ){
				if ( ! @ftp_chdir( $ftpcon, $part ) ){
					ftp_mkdir( $ftpcon, $part );
					ftp_chdir( $ftpcon, $part );
				}
			}
		}
	}

	/**
	 * Sube los archivos al netstorage
	 * @return [type] [description]
	 */
	public function upload_netstorage( $file, $ftpath ){
		$this->load->library( 'ftp' );
		$this->ftp->connect( $this->netstorage );
		$this->ftp->mirror( './' . $file, '/' . $ftpath, 'ascii', 0775 );
		$this->ftp->close();
	}
}