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
		$this->load->model( 'cronlog_model', 'cronlog' );
		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria ) ){
			if ( ! mkdir( './outputs/' . $trabajo->slug_categoria ) ){
				$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e05');
			}
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical ) ){
			if ( ! mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical ) ){
				$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e05');
			}
		}

		if ( ! file_exists( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario ) ){
			if( ! mkdir( './outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario ) ){
				$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e05');
			}
		}
		$feed_output = 'outputs/'. $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
		$ftp_server 	= $_SERVER['STORAGE_URL'];
		$ftp_user_name 	= $_SERVER['STORAGE_USER'];
		$ftp_user_pass 	= $_SERVER['STORAGE_PASS'];
		$ftp_conn = ftp_connect($ftp_server);
		$login = ftp_login( $ftp_conn, $ftp_user_name, $ftp_user_pass );
		$ftpath = '/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario. '/';
		$this->mksubdirs( $ftp_conn, '/', $ftpath );
		ftp_close( $ftp_conn );
		switch ( $trabajo->tipo_salida ){
			case 1:
				$formatos = json_decode( $trabajo->formatos );
				/**
				 * Aquí se pudiera iniciar la sesión para que el cron trabaje con el tree
				 */
				$url = base_url() . 'nucleo/feed_service?url=' . urlencode( base64_encode( $trabajo->url_origen ) );
				$content = json_decode( file_get_contents_curl( $url ) );
				$tree = new Tree( $content, true );
				$arbol = serialize( $tree );
				$nodes = serialize( $trabajo->campos_seleccionados );
				$output = $this->getItems( json_decode( $trabajo->campos_seleccionados ), $trabajo->url_origen, $arbol, $nodes );
				
				if ( $this->session->userdata( 'session' ) !== TRUE && ( json_decode( $output ) === FALSE || json_decode( $output ) === NULL ) ){
					$this->alertas->alerta( $trabajo->uid_trabajo, 'Error al intentar obtener los items de origen - getItems' );
					$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e01');
					die;
				}
				
				foreach ( $formatos as $formato ){
					switch ( $formato->formato ) {
						case 'xml':
							$open = fopen( "./" . $feed_output . $trabajo->slug_nombre_feed . '-' . $formato->formato.'.xml', "w" );
							$array = json_decode( $output, TRUE );
							$final = array_to_xml( $array )->saveXML();
							if ( $this->session->userdata( 'session' ) !== TRUE && ( $final === FALSE || $final === NULL ) ){
								$this->alertas->alerta( $trabajo->uid_trabajo, 'Error al intentar convertir a XML - array_to_xml' );
								$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e01');
								die;
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
								$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e02');
								die;
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
								$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e03');
								die;
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
								$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e04');
								die;
							}
							fwrite( $open, stripslashes( $final ) );
							fclose( $open );
							$this->upload_netstorage( $feed_output, $ftpath );
							break;
					}
				}
				break;
			case 2:
				$node = new Node(
						[
							'input' 	=> base_url() . 'nucleo/feed_service_content?url=' . urlencode( base64_encode( $trabajo->url_origen ) ),
							'template' 	=> $trabajo->json_estructura,
							'paths' 	=> base64_decode( $trabajo->relacion_especificos ),
						]
					);
				switch ( $trabajo->formato_salida ) {
					case 1:
						$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-rss.xml';
						$final = $node->toXML( $file );
						if ( ! $final ){
							$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e02');
							die;
						}
						$this->upload_netstorage( $feed_output, $ftpath );
						break;
					case 2:
						$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-xml.xml';
						$final = $node->toXML( $file );
						if ( ! $final ){
							$this->cronlog->set_cronlog( $trabajo->uid_trabajo, 'e02');
							die;
						}
						$this->upload_netstorage( $feed_output, $ftpath );
						break;
					case 3:
						# json
						break;
					case 4:
						# json-p
						break;
				}
				break;
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
		@ftp_chdir( $ftpcon, $ftpbasedir );
		$parts = explode('/', $ftpath );
		foreach( $parts as $part ){
			if ( ! @ftp_chdir( $ftpcon, $part ) ){
				ftp_mkdir( $ftpcon, $part );
				ftp_chdir( $ftpcon, $part );
				//ftp_chmod($ftpcon, 0777, $part);
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