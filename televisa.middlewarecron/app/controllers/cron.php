<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

	private $path;
	private $handle;
	private $cron_file;

	public function __construct(){
        parent::__construct();
        $this->path 	 = $_SERVER['CRON_PATH'];
		$this->handle	 = $_SERVER['CRON_HANDLE'];
		$this->cron_file = "{$this->path}{$this->handle}";

        $this->load->model( 'cron_model', 'cron' );
        $this->load->helper('url');
        $this->load->helper( 'file' );
    }

	public function index()
	{
		echo 'Solicitud no valida';
	}

	public function update_cronjobs(){

		$this->remove_crontab();
		$crons = $this->cron->get_cronjobs();

		foreach ($crons as $key => $value) {
			$this->set_cron($value->cron_config,$value->uid_trabajo);
		}
		print_r($crons);
	}

	public function run_cronjob(){

		$token = $this->input->get('token') ;
		$uid = urldecode(base64_decode($token));

		$job = $this->cron->get_trabajo_ejecutar($uid);

		if(!$job){
			//Guardar en LOG
			die;
		}

		/*$this->load->library('curl');
		$url = $_SERVER['BASE_URL'].'/job_execute?token='.$token; 
		$output = $this->curl->simple_post($url, $post_data);*/

		$ch = curl_init();                   
		$url = $_SERVER['BASE_URL'].'/job_execute'; 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, true);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'token='.$token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch,CURLOPT_TIMEOUT,240);
		$output = curl_exec ($ch); 
		curl_close ($ch); 
		 
	}

	public function set_cron( $config_cron = '', $trabajo_url_id = '' ){
        //$trabajo_url_id = 'wget -O - '. base_url() . 'job_execute?token='.( base64_encode( $trabajo_url_id ) )." > /dev/null 2>&1";
        $trabajo_url_id = 'curl --silent '. base_url() . 'cron/run_cronjob?token='.( base64_encode( $trabajo_url_id ) )." > /dev/null 2>&1";

		if ( $trabajo_url_id && $trabajo_url_id != '' ){
			$this->write_to_file( $this->path, $this->handle ); // Verifica que el archivo exista y este activo, si no, lo crea y lo activa
			$this->append_cronjob( $config_cron. ' ' . $trabajo_url_id );
		}
	}

	public function exec(){
        $argument_count = func_num_args();
        try{
                if ( ! $argument_count) throw new Exception( 'There is nothing to exececute, no arguments specified.' );
                $arguments = func_get_args();
                $command_string = ($argument_count > 1) ? implode(" && ", $arguments) : $arguments[0];
                echo $command_string .'<br/>';
                $stream = exec($command_string );
                //if ( ! $stream) throw new Exception( 'Unable to execute the specified commands: <br />{$command_string}' );
        }
        catch ( Exception $e ){
                $this->error_message( $e->getMessage() );
        }

        return $this;
    }


	public function remove_file(){
        //print_r( 'se procede a borrar el archivo' );die;
        if ( $this->crontab_file_exists() ) $this->exec( "rm {$this->cron_file}" );
        return $this;
    }

    public function append_cronjob( $cron_jobs = NULL ){
        //print_r( 'se procede a agregar linea al archivo' );die;
        if ( is_null( $cron_jobs ) ) $this->error_message( 'Nothing to append!  Please specify a cron job or an array of cron jobs.' );
        $append_cronfile        = "echo '";
        $append_cronfile        .= ( is_array( $cron_jobs ) ) ? implode( "\n", $cron_jobs ) : $cron_jobs;
        $append_cronfile        .= "'  >> {$this->cron_file}";
        $install_cron           = "crontab {$this->cron_file}";
        $this->write_to_file()->exec( $append_cronfile, $install_cron )->remove_file();
        return $this;           
    }


	public function remove_cronjob( $cron_jobs = NULL ){

        if ( is_null( $cron_jobs ) ) $this->error_message('Nothing to remove!  Please specify a cron job or an array of cron jobs.');
        $cron_array = file( $this->cron_file, FILE_IGNORE_NEW_LINES );
        if ( empty( $cron_array ) ){
                //$this->remove_file()->error_message('Nothing to remove!  The cronTab is already empty.');
        }
        $original_count = count( $cron_array );
        if ( is_array( $cron_jobs ) ){
                foreach ($cron_jobs as $cron_regex){
                        $cron_array = preg_grep( $cron_regex, $cron_array, PREG_GREP_INVERT );
                }
        } else {
                $index = array_search( $cron_jobs, $cron_array );
                unset( $cron_array[$index] );
                //$cron_array = preg_grep( $cron_jobs, $cron_array, PREG_GREP_INVERT );
        }
        return ( $original_count === count( $cron_array ) ) ? $this->remove_file() : $this->remove_crontab()->append_cronjob( $cron_array );
    }

     public function write_to_file( $path = NULL, $handle = NULL ){
	    if ( ! $this->crontab_file_exists() ){
	            $this->handle           = ( is_null( $handle ) ) ? $this->handle : $handle;
	            $this->path             = ( is_null( $path ) )   ? $this->path   : $path;
	            $this->cron_file        = "{$this->path}{$this->handle}";
	            $init_cron                      = "crontab -l > {$this->cron_file} && [ -f {$this->cron_file} ] || > {$this->cron_file}";
	            $this->exec( $init_cron );
	    }
	    return $this;   
    }

	public function remove_crontab(){
        $this->remove_file()->exec("crontab -r");
        return $this;
    }

    public function crontab_file_exists(){
        return file_exists( $this->cron_file );
    }

    private function error_message( $error ){
		//die("<pre style='color:#EE2711'>ERROR: {$error}</pre>");
		echo("<pre style='color:#EE2711'>ERROR: {$error}</pre>");
	}

}
