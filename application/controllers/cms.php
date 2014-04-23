<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index() {
		 if ($this->session->userdata('session') != TRUE) {
            redirect("login");
        } else {
        	$this->load->view('cms/principal');
        }
	}

	public function login() {
		$this->load->view('cms/login');
	}
	
}