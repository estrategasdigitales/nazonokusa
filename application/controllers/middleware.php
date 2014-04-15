<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Middleware extends CI_Controller {

	/**
	 *
	 */
	public function index() {
		$this->load->view('middleware/index');
	}

	public function read_feed() {
		$this->form_validation->set_rules('url_feed','URL del Feed','trim|required|min_length[3]|xss_clean');
		if($this->form_validation->run() === FALSE) {
			$this->load->view('middleware/index');
		}else{
			$url = $this->input->post('url_feed');
			$data['response'] = file_get_contents($url);
			$this->load->view('middleware/index', $data);
		}
	}
}

/* End of file middleware.php */
/* Location: ./application/controllers/middleware.php */