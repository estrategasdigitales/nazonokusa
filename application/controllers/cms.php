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

		$this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Contraseña', 'trim|required|xss_clean');
		if ($this->form_validation->run() === TRUE) {
			$usuario['usuario'] 	=	$this->input->post('usuario');
			$usuario['password'] 	=	$this->input->post('password');
			$usuario 				=	$this->security->xss_clean($usuario);
			$valido 				=	$this->cms->get_usuario($usuario);
			if( $valido !== FALSE ) {
				$session = array(
					'session'	 => TRUE,
					'uuid' 		 => $valido->uuid_usuario,
					'nombre' 	 => $valido->nombre,
					'nivel' 	 => $valido->nivel
					);
				$this->session->set_userdata($session);
				redirect('inicio');
			} else {
				$data['error'] = "El Usurio y/o la contraseña son incorrectos o no existe el Usuario";
				$this->load->view('cms/login', $data);
			}
		} else {			
			$this->load->view('cms/login');
		}
	}

	public function admin_usuarios(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$data['usuarios']	= $this->cms->get_usuarios();
			$this->load->view('cms/admin/usuarios',$data);
		}

	}

	public function admin_trabajos(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			if($this->session->userdata('nivel')==='1')
				$data['trabajos']	= $this->cms->get_trabajos();
			else
				$data['trabajos']	= $this->cms->get_trabajos_editor($this->session->userdata('uuid'));

			$this->load->view('cms/admin/trabajos',$data);
		}

	}

	public function admin_categorias(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$data['categorias']	= $this->cms->get_categorias();
			$this->load->view('cms/admin/categorias',$data);
		}

	}

	public function admin_verticales(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$data['verticales']	= $this->cms->get_verticales();
			$this->load->view('cms/admin/verticales',$data);
		}

	}

	public function nuevo_usuario(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$data['categorias'] = $this->cms->get_categorias();
			$data['verticales'] = $this->cms->get_verticales();
			$this->load->view('cms/admin/nuevo_usuario',$data);
		}

	}

	public function validar_form_usuario(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('apellidos', 'Apellidos', 'xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
			$this->form_validation->set_rules('extension', 'Extensión', 'xss_clean');
			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[5]|xss_clean');
			$this->form_validation->set_rules('celular', 'Número Celular', 'required|xss_clean');

			if ($this->form_validation->run() === TRUE)
			{
				$usuario['nombre']   = $this->input->post('nombre');
				$usuario['apellidos']   = $this->input->post('apellidos');
				$usuario['email']   = $this->input->post('email');
				$usuario['extension']   = $this->input->post('extension');
				$usuario['password']   = $this->input->post('password');
				$usuario['celular']   = $this->input->post('celular');
				$usuario['verticales'] = $this->input->post('vertical');
				$usuario['categorias'] = $this->input->post('categoria');
				$guardar = $this->cms->add_usuario($usuario);
				if( $guardar !==false )
				{
					redirect('usuarios');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] = "El nuevo usuario no pudo ser agregado";
					$this->load->view('cms/admin/nuevo_usuario',$data);
				}
			}
			else
			{			
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/nuevo_usuario',$data);
			}
		}

	}

	public function editar_usuario($uuid){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$usuario = $this->cms->get_usuario_editar($uuid);
			if( $usuario !== false )
			{
				$data['usuario'] 		 = $this->session->userdata('nombre');
				$data['categorias'] = $this->cms->get_categorias();
				$data['verticales'] = $this->cms->get_verticales();
				$data['usuario_editar']  = $usuario;
				$data['ver_cat']  = $this->cms->get_ver_cat($uuid);
				$this->load->view('cms/admin/editar_usuario',$data);
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] = "No se a encontrado el usuario";
				$this->load->view('cms/admin/editar_usuario',$data);
			}
		}

	}

	public function validar_form_usuario_editar(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('apellidos', 'Apellidos', 'xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
			$this->form_validation->set_rules('extension', 'Extensión', 'xss_clean');
			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[5]|xss_clean');
			$this->form_validation->set_rules('celular', 'Número Celular', 'required|xss_clean');

			if ($this->form_validation->run() === TRUE)
			{
				$usuario['uuid_usuario']   = $this->input->post('uuid_usuario');
				$usuario['nombre']   = $this->input->post('nombre');
				$usuario['apellidos']   = $this->input->post('apellidos');
				$usuario['email']   = $this->input->post('email');
				$usuario['extension']   = $this->input->post('extension');
				$usuario['password']   = $this->input->post('password');
				$usuario['celular']   = $this->input->post('celular');
				$usuario['verticales'] = $this->input->post('vertical');
				$usuario['categorias'] = $this->input->post('categoria');
				$guardar = $this->cms->editar_usuario($usuario);
				if( $guardar !==false )
				{
					redirect('usuarios');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] = "El nuevo usuario no pudo ser editado";
					$this->load->view('cms/admin/editar_usuario',$data);
				}
			}
			else
			{			
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/editar_usuario',$data);
			}
		}

	}


	public function eliminar_usuario($uuid){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$eliminar = $this->cms->delete_usuario($uuid);
			if( $eliminar !== false )
			{
				redirect('usuarios');
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] = "No se a podido eliminar el usuario";
				$this->load->view('cms/admin/usuarios',$data);
			}
		}

	}

	public function nueva_categoria(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$this->load->view('cms/admin/nueva_categoria',$data);
		}

	}

	public function validar_form_categoria(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|min_length[3]|xss_clean');
			if ($this->form_validation->run() === TRUE)
			{
				$categoria['nombre']   = $this->input->post('nombre');
				$guardar = $this->cms->add_categoria($categoria);
				if( $guardar !== false )
				{
					redirect('categorias');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] = "La nueva categoria no pudo ser guardada";
					$this->load->view('cms/admin/nueva_categoria',$data);
				}
			}
			else
			{			
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/nueva_categoria',$data);
			}
		}

	}

	public function eliminar_categoria($uuid){
		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$eliminar = $this->cms->delete_categoria($uuid);
			if( $eliminar !== false )
			{
				redirect('categorias');
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] = "No se a podido eliminar la categoria";
				$this->load->view('cms/admin/categorias',$data);
			}
		}
	}

	public function nueva_vertical(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$this->load->view('cms/admin/nueva_vertical',$data);
		}

	}

	public function validar_form_vertical(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|min_length[3]|xss_clean');
			if ($this->form_validation->run() === TRUE)
			{
				$vertical['nombre']   = $this->input->post('nombre');
				$guardar = $this->cms->add_vertical($vertical);
				if( $guardar !== false )
				{
					redirect('verticales');
				}
				else
				{
					$data['usuario'] 	= $this->session->userdata('nombre');
					$data['error'] = "La nueva vertical no pudo ser guardada";
					$this->load->view('cms/admin/nueva_vertical',$data);
				}
			}
			else
			{			
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] 	= "Ocurrio un problema y los datos no pudieron ser guardados";
				$this->load->view('cms/admin/nueva_vertical',$data);
			}
		}

	}

	public function eliminar_vertical($uuid){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$eliminar = $this->cms->delete_vertical($uuid);
			if( $eliminar !== false )
			{
				redirect('verticales');
			}
			else
			{
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['error'] = "No se a podido eliminar la vertical";
				$this->load->view('cms/admin/verticales',$data);
			}
		}
		
	}

	public function nuevo_trabajo(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			if ($this->session->userdata('nivel') === "1") {
				$data['categorias'] = $this->cms->get_categorias();
				$data['verticales'] = $this->cms->get_verticales();
			}else{
				$data['categorias'] = $this->cms->get_categorias_usuario($this->session->userdata('uuid'));
				$data['verticales'] = $this->cms->get_verticales_usuario($this->session->userdata('uuid'));
			}
			$this->load->view('cms/admin/nuevo_trabajo',$data);
		}

	}

}