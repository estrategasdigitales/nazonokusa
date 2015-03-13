<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

require_once( BASEPATH . '../app/libraries/Node.php');
require_once( BASEPATH . '../app/controllers/nucleo.php');

class Netstorage_model extends Nucleo {

	private $netstorage;
	private $url_storage;
	private $storage_root;

    function __construct() {
        parent::__construct();
        $this->load->model( 'alertas_model', 'alertas' );
        $CI =& get_instance();
		$CI->load->model( 'cronlog_model', 'cronlog' );
        $this->load->helper( 'file' );
        $this->storage_root = '/';
		//$this->url_storage = $_SERVER['STORAGE_URL'];
		$this->url_storage = 'outputs';
		/** Configuracion de conexión a netstorage */
		$this->netstorage = array(
		 		'hostname' 		=> $_SERVER['STORAGE_URL'],
				'username' 		=> $_SERVER['STORAGE_USER'],
				'password' 		=> $_SERVER['STORAGE_PASS'],
				'passive'		=> TRUE,
				'debug'			=> TRUE,
				'base_folder'	=> '/'
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
		$CI =& get_instance();
		$CI->load->model('cronlog_model', 'cronlog');
		if ( file_get_contents_curl( $trabajo->url_origen ) ) {
			/*$CI =& get_instance();
			$CI->load->model('cronlog_model', 'cronlog');*/
			if (! file_exists('./outputs/' . $trabajo->slug_categoria)) {
				if (!mkdir('./outputs/' . $trabajo->slug_categoria)) {
					$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'error', 'E01 - No se ha podido crear el directorio de la categoría' );
					$this->alertas->alerta($trabajo->uid_trabajo, 'E01');
				}
			}

			if (!file_exists('./outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical)) {
				if (!mkdir('./outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical)) {
					$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E01 - No se ha podido crear el directorio de la vertical');
					$this->alertas->alerta($trabajo->uid_trabajo, 'E01');
				}
			}

			if (!file_exists('./outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario)) {
				if (!mkdir('./outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario)) {
					$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E01 - No se ha podido crear el directorio del usuario');
					$this->alertas->alerta($trabajo->uid_trabajo, 'E01');
				}
			}
			$feed_output = 'outputs/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/';

			$ftp_server = $_SERVER['STORAGE_URL'];
			$ftp_user_name = $_SERVER['STORAGE_USER'];
			$ftp_user_pass = $_SERVER['STORAGE_PASS'];
			$ftp_conn = ftp_connect($ftp_server, 21, 90);
			$login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

			$login = true;

			if ( $login ) {
				$ftpath = '/' . $trabajo->slug_categoria . '/' . $trabajo->slug_vertical . '/' . $trabajo->uid_usuario . '/';
				$this->mksubdirs($ftp_conn, '/', $ftpath);
				ftp_close($ftp_conn);
				switch ($trabajo->tipo_salida) {
					case 1:
						$content = base_url() . 'nucleo/feed_service_content?url=' . urlencode(base64_encode($trabajo->url_origen));
						$formatos = $this->cms->get_trabajos_formatos($trabajo->uid_trabajo);
						$node = new Node(
							[
                                'formato_origen' => $trabajo->formato_origen,
								'input' => base_url() . 'nucleo/feed_service_content?url=' . urlencode(base64_encode($trabajo->url_origen)),
								'template' => base_url() . 'nucleo/feed_service?url=' . urlencode(base64_encode($trabajo->url_origen)),
								'paths' => base64_decode($trabajo->campos_seleccionados),
							]
						);

						$node->originFormat = $trabajo->formato_origen;
						$data = $node->getData();

						foreach ($formatos as $formato) {
							$formato = json_decode($formato->formato);
							switch ($formato->format) {
								case 'xml':
									$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-xml.xml';
									$final = $node->toXML($data, $file);
									if ($final != NULL) {
										$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E03 - Ocurrió un problema al intentar crear la salida estándar para XML');
										$this->alertas->alerta($trabajo->uid_trabajo, 'E03');
									}
									$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $formato->format);
									break;
								case 'rss':
									$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-rss.xml';
									$final = $node->toRSS($data, $file, 'UTF-8', $formato->attributes);
									if ($final != NULL) {
										$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E04 - Ocurrió un problema al intentar crear la salida estándar para RSS');
										$this->alertas->alerta($trabajo->uid_trabajo, 'E04');
									}
									$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $formato->format);
									break;
								case 'json':
									$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-json.js';
									$final = $node->toJSON($data, $file);
									if ($final != NULL) {
										$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E05 - Ocurrió un problema al intentar crear la salida estándar para JSON');
										$this->alertas->alerta($trabajo->uid_trabajo, 'E05');
									}
									$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $formato->format);
									break;
								case 'jsonp':
									$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-jsonp.js';
									$final = $node->toJSON($data, $file, $formato->function);
									if ($final != NULL) {
										$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E06 - Ocurrió un problema al intentar crear la salida estándar para JSONP');
										$this->alertas->alerta($trabajo->uid_trabajo, 'E06');
									}
									$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $formato->format);
									break;
							}
						}
						break;
					case 2:
						$node = new Node(
							[
                                'formato_origen' => $trabajo->formato_origen,
								'input' => base_url() . 'nucleo/feed_service_content?url=' . urlencode(base64_encode($trabajo->url_origen)),
								'template' => base_url() . $trabajo->json_estructura,
								'paths' => base64_decode($trabajo->campos_seleccionados),
							]
						);
						$node->originFormat = $trabajo->formato_origen;
						$node->ESPECIFICO = TRUE;
                        $node->ESPECIFICO_FORMATO = $trabajo->formato_salida;

						$encoding = base64_decode($trabajo->encoding);
						$header = base64_decode($trabajo->cabeceras);
						switch ($trabajo->formato_salida) {
							case 'RSS':
								$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-rss.xml';
								$data = $node->getData();
								$final = $node->toRSS($data, $file, $encoding, $header);
								if ($final != NULL) {
									$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E07 - Ocurrió un problema al intentar crear la salida específica para RSS');
									$this->alertas->alerta($trabajo->uid_trabajo, 'E07');
								}
								$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $trabajo->formato_salida);
								break;
							case 'XML':
								$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-xml.xml';
								$data = $node->getData();
								$final = $node->toXML($data, $file, $encoding);
								if ($final != NULL) {
									$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E08 - Ocurrió un problema al intentar crear la salida específica para XML');
									$this->alertas->alerta($trabajo->uid_trabajo, 'E08');
								}
								$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $trabajo->formato_salida);
								break;
							case 'JSON':
								$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-json.js';
								$data = $node->getData();
								$final = $node->toJSON($data, $file);
								if ($final != NULL) {
									$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E09 - Ocurrió un problema al intentar crear la salida específica para JSON');
									$this->alertas->alerta($trabajo->uid_trabajo, 'E09');
								}
								$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $trabajo->formato_salida);
								break;
							case 'JSONP':
								$node->isJsonVariable = $trabajo->variable;
								$file = './' . $feed_output . $trabajo->slug_nombre_feed . '-json.js';
								$data = $node->getData();
								$final = $node->toJSON($data, $file);
								if ($final != NULL) {
									$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'E09 - Ocurrió un problema al intentar crear la salida específica para JSON');
									$this->alertas->alerta($trabajo->uid_trabajo, 'E09');
								}
								$this->upload_netstorage($feed_output, $ftpath, $trabajo->uid_trabajo, $trabajo->tipo_salida, $trabajo->formato_salida);
								break;
							case 4:
								# json-p
								break;
						}
						break;
				}
			} else {
				return FALSE;
				exit;
			}
		} else {
			$CI->cronlog->set_cronlog($trabajo->uid_trabajo, 'error', 'El contenido del feed de origen no esta disponible');
			$this->alertas->alerta($trabajo->uid_trabajo, 'E12-Feed Content Unavailable');
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
	private function upload_netstorage( $file, $ftpath, $trabajo, $tipo_salida, $formato_salida ){
		$CI =& get_instance();
		$CI->load->model( 'cronlog_model', 'cronlog' );
		$this->load->library( 'ftp' );
		if ( $tipo_salida == 1 ) $tipo_salida = 'Estándar';
		else $tipo_salida = 'Específica';
		if ( $this->ftp->connect( $this->netstorage ) ) {
			if ( $this->ftp->mirror('./' . $file, '/' . $ftpath, 'ascii', 0775 ) ) {
				$CI->cronlog->set_cronlog($trabajo, 'success', 'Operación exitosa para la conversión ' . $tipo_salida . ' del formato: ' . $formato_salida);
			} else {
				$CI->cronlog->set_cronlog($trabajo, 'error', 'E10 - Ocurrió un problema al intentar subir el archivo de salida en formato:' . $formato_salida . ' al netstorage de la conversión ' . $tipo_salida);
				$this->alertas->alerta($trabajo, 'E10');
			}
		} else {
			$CI->cronlog->set_cronlog( $trabajo->uid_trabajo, 'error', 'No se ha podido establecer una conexion con el netstorage');
			$this->alertas->alerta( $trabajo->uid_trabajo, 'E10-Fail Connection Netstorage' );
		}
		$this->ftp->close();

	}

}