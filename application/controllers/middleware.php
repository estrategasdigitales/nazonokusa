<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Middleware extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	/**
	 *
	 */
	public function index() {
		$this->load->view('middleware/index');
	}

	public function read_feed() {
		$this->form_validation->set_rules('url_feed','URL del Feed','trim|required|min_length[3]|xss_clean');
		if($this->form_validation->run() === FALSE) {
			echo validation_errors('<span class="error">', '</span>');
		}else{
			$url = $this->input->post('url_feed');
			$url_type = pathinfo($this->input->post('url_feed'));
			switch ($url_type['extension']) {
				case 'js':
					$data['response'] = file_get_contents($url);
					$this->load->view('middleware/index', $data);
					break;
				case 'xml':
					$simple = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
					print_r($simple);die;
					$data['response'] = json_decode(json_encode($simple), 1);
					// $data['response'] = file_get_contents($url, true);
					$this->load->view('middleware/index', $data);
					break;
			}
		}
	}
}

/* End of file middleware.php */
/* Location: ./application/controllers/middleware.php */