<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH . '../app/libraries/Node.php');

class Nucleo extends CI_Controller {

	/**
	 * Constructor de la clase, se inicializan valores, se cargan librerías y helpers extras
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model( 'cms_model', 'cms' );
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index() {
		if ( $this->session->userdata( 'session' ) !== TRUE ){
			redirect( 'login' );
		} else {
			// $this->load->model('alertas_model', 'alertas');
			// $this->alertas->alerta('003101e8-3394-11e4-b1fe-e385f026ed30','E303 - Error desconocido aún');die;
			$data['usuario'] = $this->session->userdata( 'nombre' );
			$this->load->view( 'middleware/index' );
		}
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
	 * [cargar_template_salida description]
	 * @return [type] [description]
	 */
	public function cargar_template_salida(){
		$template['id'] = $this->input->post( 'id_template' );
		$feed_salida = $this->cms->get_template_feed( $template );
		if ( $feed_salida != FALSE ){
			echo $feed_salida->json_estructura;
		} else {
			echo '<span class="error">Ocurrió un problema al intentar <b>cargar el template de salida</b>. </span>';
		}
	}

	/**
	 * [detectar_campos description]
	 * @return [type] [description]
	 */
	public function detectar_campos(){
		$url = base_url() . 'nucleo/feed_service?url=' . urlencode( base64_encode( $this->input->post( 'url' ) ) );
		echo $url;

	}
	
	/**
	 * [editar_trabajo description]
	 * @param  [type] $uid_trabajo [description]
	 * @return [type]              [description]
	 */
	public function editar_trabajo( $uid_trabajo ){
    	if( $this->session->userdata( 'session' ) !== TRUE ){
    		redirect('login');
    	}else {
    	    $trabajo = $this->cms->get_trabajo_editar( $uid_trabajo );
           	$data['usuario']    	= $this->session->userdata( 'nombre' );
			$data['categorias'] 	= $this->cms->get_categorias();
			$data['verticales'] 	= $this->cms->get_verticales();
       		$data['trabajo_editar'] = $trabajo->uid_trabajo;
       		$data['cron_date'] 		= json_decode( $trabajo->cron_config, true );
           	$this->load->view( 'cms/admin/editar_trabajo', $data );
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
			$uid_trabajo = base64_decode( $this->input->post( 'token' ) );
			$eliminar = $this->cms->delete_trabajo( $uid_trabajo );
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
		$url = $this->input->get('url');
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		if ( mb_detect_encoding( $url ) != 'UTF-8' ){
			$url = html_entity_decode( $url );
		}

		if ( $feed = json_decode( $url,true ) ){

			//$cont = $this->mapAttributes( $feed  );

			// foreach ( $feed as $item ){
			// 	if ( is_array( $item ) && count( $item ) == 1 )
			// 		$item = (array)$item[0];
			// 	else
			// 		$item = json_encode( $item );

			// 	$cont[] = $this->mapAttributes( $item  );
			// }
			$cont[] = $this->mapAttributes( $feed  );
			$contents = $this->array_unique_multidimensional( $cont );
			$indices = create_indexes_specific( $contents );
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos );
				$feed = str_replace( '(', '[', $feed );
				$feed = str_replace( ')', ']', $feed );
				$feed = json_decode( $feed, TRUE );
				foreach ( $feed as $item ){
					if ( is_array( $item ) && count( $item ) == 1 )
						$item = (array)$item[0];
					else
						$item = json_encode( $item );

					$cont[] = $this->mapAttributes( $item  );
				}
				$contents = $this->array_unique_multidimensional( $cont );
				$indices = create_indexes_specific( $contents );
			} else {
				$dom = new DOMDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $rss['rss']['channel']['item'] ) );
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes_specific( $contents );
				} else {
					$xml = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $xml ) );
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes_specific( $contents );
				}
			}
		}
		$data = array( 'indices' => $indices );
		$this->load->view('cms/service', $data );
	}

	/**
	 * [feed_service_specific description]
	 * @return [type] [description]
	 */
	public function feed_service_specific(){
		$url = $this->input->get( 'url' );
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		if ( mb_detect_encoding( $url ) != 'UTF-8' ){
			$url = html_entity_decode( $url );
		}
		if ( $feed = json_decode( $url ) ){
			foreach ( $feed as $item ){
				if ( is_array( $item ) && count( $item ) == 1 )
					$item = (array)$item[0];
				else
					$item = json_encode( $item );

				$cont[] = $this->mapAttributes( $item  );
			}	
			$contents = $this->array_unique_multidimensional( $cont );
			$indices = create_indexes_specific( $contents );
		} else {
			$pos = strpos( $url, '(' );
			if ( $pos > -1 && ( substr( $url, -1 ) === ')' ) ){
				$feed = substr( $url, $pos );
				$feed = str_replace( '(', '[', $feed );
				$feed = str_replace( ')', ']', $feed );
				$feed = json_decode( $feed, TRUE );
				foreach ( $feed as $item ){
					if ( is_array( $item ) && count( $item ) == 1 )
						$item = (array)$item[0];
					else
						$item = json_encode( $item );

					$cont[] = $this->mapAttributes( $item  );
				}
				$contents = $this->array_unique_multidimensional( $cont );
				$indices = create_indexes_specific( $contents );
			} else {
				$dom = new DOMDocument();
				$dom->loadXML( $url );
				if ( $dom->documentElement->nodeName == 'rss' ){
					$rss = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $rss['rss']['channel']['item'] ) );
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes_specific( $contents );
				} else {
					$xml = $this->xml_2_array->createArray( $url );
					$feed[] = $this->mapAttributes( json_encode( $xml ) );
					$contents = $this->array_unique_multidimensional( $feed );
					$indices = create_indexes_specific( $contents );
				}
			}
		}
		$data = array( 'indices' => $indices[0] );
		$this->load->view( 'cms/service_specific', $data );
	}

	/**
	 * [feed_service_content description]
	 * @return [type] [description]
	 */
	public function feed_service_content(){
		$url = $this->input->get( 'url' );
		$url = urldecode( base64_decode( $url ) );
		$url = file_get_contents_curl( $url );
		if ( mb_detect_encoding( $url ) != 'UTF-8' ){
			$url = html_entity_decode( $url );
		}
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
					$contenido_feed = $feed[0];
				} else {
					$xml = $this->xml_2_array->createArray( $url );
					$feed[] = $xml;
					$contenido_feed = $xml;
				}
			}
		}
		$data = array( 'contenido_feed' => $contenido_feed );
		$this->load->view( 'cms/service_content', $data );
	}

	/**
	 * [job_execute description]
	 * @return [type] [description]
	 */
	public function job_execute(){
		$this->load->model( 'netstorage_model', 'storage' );
		$token = urldecode( base64_decode( $this->input->get('token') ) );
		$trabajo = $this->cms->get_trabajo_ejecutar( $token );
		/**
		 * Se generan los archivos de salida en outputs
		 */
		$this->storage->harddisk_write( $trabajo );
	}

	/**
	 * [job_process description]
	 * @return [type] [description]
	 */
    public function job_process(){
        $this->load->model( 'netstorage_model','storage' );
        $CI =& get_instance();
        $CI->load->model( 'crontabs_model','crontabs' );
        $job['status'] 	= $this->input->post( 'status' );
        $job['uidjob'] 	= base64_decode( $this->input->post('uidjob') );
        $process 		= $this->cms->active_job( $job );
        if ( $process == TRUE ){
            if ( $job['status'] == 1 ){
                $trabajo = $this->cms->get_trabajo_ejecutar( $job['uidjob'] );
                $this->storage->harddisk_write( $trabajo );
                $CI->crontabs->set_cron( $trabajo->cron_config, $job['uidjob'] );
                echo TRUE;
            } else {
                $trabajo = $this->cms->get_trabajo_ejecutar( $job['uidjob'] );
                $CI->crontabs->unset_cron( $trabajo->cron_config, $job['uidjob'] );
                echo TRUE;
            }
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
		$campos_orig 	= is_array($feed) ? $feed : json_decode( $feed, TRUE );
		$campos 		= [];
		//print_r($campos_orig);
		$items 			= count( $campos_orig );
		if ( ! empty( $campos_orig[0] ) ){
			for ( $i = 0; $i < count( $campos_orig ); $i++ ){
				foreach ( $campos_orig[$i] as $key => $value ){
					if ( is_object( $value ) ){
						$value = get_object_vars( $value );
					}

					if ( is_array( $value ) ){
						if ( ! empty( $campos[$key] ) ){
							$campos[$key] = $this->claves( $value, $campos[$key] );
						} else {
							$campos[$key] = $this->claves( $value, $campos[$key] = [] );
						}
					} else {
						if ( ! array_key_exists($key, $campos) ){
							$campos[$key] = '';
						}
					}
				}
			}
		} else {
			foreach ( $campos_orig as $key => $value ){
				if ( is_object( $value ) ){
					$value = get_object_vars( $value );
				}

				if ( is_array( $value ) ){
					if ( ! empty( $campos[$key] ) ){
						$campos[$key] = $this->claves( $value, $campos[$key] );
					} else {
						$campos[$key] = $this->claves( $value, $campos[$key] = [] );
						
					}
				} else {
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
		if ( $this->session->userdata( 'session' ) !== TRUE ){
			redirect(' login' );
		} else {
			$this->form_validation->set_rules( 'nombre', 'Nombre del Trabajo', 'trim|required|min_length[3]|xss_clean' );
			$this->form_validation->set_rules( 'url-origen', 'URL Origen', 'required|min_length[3]|xss_clean' );
			$this->form_validation->set_rules( 'categoria', 'Categoría', 'required|callback_valid_option|xss_clean' );
			$this->form_validation->set_rules( 'vertical', 'Vertical', 'required|callback_valid_option|xss_clean' );
			if ( $this->input->post( 'tipo_salida' ) == 1 ){
				$this->form_validation->set_rules( 'formato', 'Formato', 'required|xss_clean' );
				//$this->form_validation->set_rules( 'claves', 'Campos seleccionados', 'required|xss_clean' );
				if ( ! empty( $this->input->post( 'formato' ) ) ){
					if ( in_array('rss', $this->input->post( 'formato' ) ) ){
						$this->form_validation->set_rules('valores_rss[]', 'Campos adicionales para RSS', 'required|xss_clean');
					}

					if ( in_array('jsonp', $this->input->post('formato' ) ) ){
						$this->form_validation->set_rules('nom_funcion', 'Campos adicionales para JSONP', 'trim|alpha_dash|required|min_length[3]|xss_clean');
					}
				}
			}
			$cronjob_config = $this->input->post('cron_minuto').' '.$this->input->post('cron_hora').' '.$this->input->post('cron_diames').' '.$this->input->post('cron_mes').' '.$this->input->post('cron_diasemana');

			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 			= $this->session->userdata( 'uid' );
				$trabajo['nombre']   			= $this->input->post( 'nombre' );
				$trabajo['slug_nombre_feed']	= url_title( $this->input->post( 'nombre' ), 'dash', TRUE );
				$trabajo['url-origen']   		= $this->input->post( 'url-origen' );
				$trabajo['categoria']   		= $this->input->post( 'categoria' );
				$trabajo['vertical']   			= $this->input->post( 'vertical' );
				$trabajo['campos']				= $this->input->post( 'claves' );
				$trabajo['tipo_salida']			= $this->input->post( 'tipo_salida' );
				if ( $this->input->post( 'tipo_salida' ) == 2 )
					$trabajo['uid_plantilla']		= $this->input->post( 'formato_especifico' );
				$trabajo['campos_seleccionados'] = $this->input->post( 'campos_seleccionados' );
				if ( $this->input->post('tipo_salida') == 1 ){
					$trabajo['formatos']			= formatos_output_seleccionados( $this->input->post('formato'), $this->input->post('nom_funcion'), $this->input->post('valores_rss'), $this->input->post('claves_rss') );
				}
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
					if ( is_object( $value ) ){
						$value = get_object_vars( $value );
					}

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
					if ( is_object( $value ) ){
						$value = get_object_vars( $value );
					}

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
	 * Funcion extra para form_validate, para detectar si en un selector se ha elegido algo diferente de cero o la opción por defecto
	 * @param  [type] $str valor
	 * @return [type]      Regresa FALSE si el dato no es válido, TRUE si el dato es válido
	 */
	function valid_option( $str ){
        if ( $str == '0' ) {
            $this->form_validation->set_message('valid_option', '<b class="requerido">*</b> Es necesario que selecciones una <b>%s</b>.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}