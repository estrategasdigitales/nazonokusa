<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Crontabs_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper( 'cron_manager', 'file' );
    }

    public function index(){

    }

    /**
	 * Escribe el crontab en el servidor
	 * @param [type] $config_cron    [description]
	 * @param [type] $trabajo_url_id [description]
	 */
	public function set_cron( $config_cron, $trabajo_url_id ){
		$trabajo_url_id = 'curl '. base_url() . 'job_execute?token='.urlencode( base64_encode( $trabajo_url_id ) );

		$host= 		$_SERVER['CRON_HOST'];
		$port=		$_SERVER['CRON_HOST_PORT'];
		$username=	$_SERVER['CRON_HOST_USER'];	
		$password=	$_SERVER['CRON_HOST_PASS'];
		
		$cron_setup = new cron_manager();
		// Si no se puede conectar, enviar error a pantalla.
		$resp_con = $cron_setup->connect( $host, $port, $username, $password ); 
		$path 	 = $_SERVER['CRON_PATH'];
		$handle	 = $_SERVER['CRON_HANDLE'];
		
		if ( $trabajo_url_id && $trabajo_url_id != '' ){
			$cron_setup->write_to_file( $path, $handle ); // Verifica que el archivo exista y este activo, si no, lo crea y lo activa
			$nueva_tarea = $cron_setup->append_cronjob( $config_cron. ' ' . $trabajo_url_id );
		}
	}

	/**
	 * Elimina el crontab en el servidor
	 * @param  [type] $config_cron    [description]
	 * @param  [type] $trabajo_url_id [description]
	 * @return [type]                 [description]
	 */
	public function unset_cron( $config_cron, $trabajo_url_id ){
		$trabajo_url_id = 'curl '. base_url() . 'job_execute?token='.urlencode( base64_encode( $trabajo_url_id ) );

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
			$nueva_tarea = $cron_setup->remove_cronjob( $config_cron. ' ' . $trabajo_url_id );
		}
	}
}