<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');
	}

	public function index() {

		 if ($this->session->userdata('session') !== TRUE) {
            redirect("login");
        } else {
        	$data['usuario'] = $this->session->userdata('nombre');
        	$this->load->view('cms/principal',$data);
        }

	}

	public function login() {

		 if ($this->session->userdata('session') !== TRUE) {
		 	$this->load->view('cms/login');
        } else {
        	$data['usuario'] = $this->session->userdata('nombre');
        	$this->load->view('cms/principal',$data);
        }

	}

	public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

	public function validar_usuario(){

		$this->form_validation->set_rules('usuario', 'Usuario', 'required|xss_clean');
		$this->form_validation->set_rules('password', 'Contraseña', 'required|xss_clean');
		if ($this->form_validation->run() === TRUE)
		{
			$usuario['usuario']   = $username = $this->input->post('usuario');
			$usuario['password']  = $username = $this->input->post('password');
			$valido = $this->cms->validar_usuario($usuario);
			if( $valido )
			{
				$session = array(
                    'session'	 => TRUE,
                    'uuid' 		 => $valido->uuid_usuario,
                    'nombre' 	 => $valido->nombre,
                    'nivel' 	 => $valido->nivel
                );
                $this->session->set_userdata($session);
                redirect('inicio');
			}else{
				$data['error'] = "El Usurio y/o la contraseña son incorrectos o no existe el Usuario";
				$this->load->view('cms/login', $data);
			}
		}
		else
		{			
			$this->load->view('cms/login');
		}

	}	
}