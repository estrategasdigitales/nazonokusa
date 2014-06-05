<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');
	}

	public function index() {
		if ( $this->session->userdata( 'session' ) !== TRUE ){
			redirect('login');
		} else {
			$this->load->view( 'cms/principal' );
		}
	}

	public function login() {
		if ($this->session->userdata('session') !== TRUE) {
			$this->load->view('cms/login');
		} else {
			$this->load->view('cms/principal' );
		}
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect('login');
	}

	/**
	 * [validar_usuario description]
	 * @return [type] [description]
	 */
	public function validar_usuario(){
		$this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('password', 'Contraseña', 'trim|required|min_length[8]|xss_clean');
		if ( $this->form_validation->run() === TRUE ){
			$usuario['usuario'] 	=	$this->input->post( 'usuario' );
			$usuario['password'] 	=	$this->input->post( 'password' );
			$usuario 				=	$this->security->xss_clean( $usuario );
			$valido 				=	$this->cms->get_usuario( $usuario );
			if ( $valido !== FALSE ){
				$session = array(
					'session'	 	=> TRUE,
					'uuid' 		 	=> $valido->uuid_usuario,
					'nombre' 	 	=> $valido->nombre,
					'apellidos'		=> $valido->apellidos,
					'email'			=> $valido->email,
					'nivel' 	 	=> $valido->nivel
					);
				$this->session->set_userdata($session);
				echo TRUE;
			} else {
				echo 'Los datos de usuario son incorrectos o el usuario no existe';
			}
		} else {			
			echo validation_errors('<span class="error">','</span>');
		}
	}

	/**
	 * [admin_usuarios description]
	 * @return [type] [description]
	 */
	public function admin_usuarios(){
		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuarios']	= $this->cms->get_usuarios( $this->session->userdata( 'nivel' ), $this->session->userdata( 'uuid' ) );
			$this->load->view( 'cms/admin/usuarios', $data );
		}
	}

	public function admin_trabajos(){

		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			if($this->session->userdata('nivel')==='1'){
				$data['trabajos']	= $this->cms->get_trabajos();
			    $data['level'] = 1;
			}else
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


	/**
	 * [validar_form_usuario description]
	 * @return [type] [description]
	 */
	public function validar_form_usuario(){
		if ($this->session->userdata('session') !== TRUE) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|callback_nombre_valido|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean');
			$this->form_validation->set_rules('extension', 'Extensión', 'trim|numeric|xss_clean');
			$this->form_validation->set_rules('password', 'Contraseña', 'required|trim|min_length[8]|xss_clean');
			$this->form_validation->set_rules('password_2', 'Confirmar Contraseña', 'required|trim|min_length[8]|xss_clean');
			$this->form_validation->set_rules('celular', 'Número Celular', 'required|trim|callback_valid_phone|xss_clean');
			$this->form_validation->set_rules('compania_celular', 'Compañía Celular', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('rol_usuario', 'Rol de usuario', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|callback_valid_option|xss_clean');

			if ($this->form_validation->run() === TRUE){
				if ($this->input->post( 'password' ) === $this->input->post( 'password_2' ) ){
					$usuario['nombre']   			= $this->input->post( 'nombre' );
					$usuario['apellidos']   		= $this->input->post( 'apellidos' );
					$usuario['email']   			= $this->input->post( 'email' );
					$usuario['extension']   		= $this->input->post( 'extension' );
					$usuario['password']   			= $this->input->post( 'password' );
					$usuario['celular']   			= $this->input->post( 'celular' );
					$usuario['compania_celular']   	= $this->input->post( 'compania_celular' );
					$usuario['rol_usuario']   		= $this->input->post( 'rol_usuario' );
					$usuario['verticales'] 			= $this->input->post( 'vertical' );
					$usuario['categorias'] 			= $this->input->post( 'categoria' );
					$guardar 						= $this->cms->add_usuario( $usuario );
					if ( $guardar !== FALSE ){
						echo TRUE;
					} else {
						echo 'E01 - El nuevo usuario no pudo ser agregado';
					}
				} else {
					echo '<span class="error">La <b>Contraseña</b> y la <b>Confirmación</b> no coinciden, verificalas.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
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
				$data['usuario']    = $this->session->userdata('nombre');
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
		if ( $this->session->userdata('session') !== TRUE ) {
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|callback_nombre_valido|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean');
			$this->form_validation->set_rules('extension', 'Extensión', 'trim|numeric|xss_clean');
			$this->form_validation->set_rules('password', 'Contraseña', 'required|trim|min_length[8]|xss_clean');
			$this->form_validation->set_rules('password_2', 'Confirmar Contraseña', 'required|trim|min_length[8]|xss_clean');
			$this->form_validation->set_rules('celular', 'Número Celular', 'required|trim|callback_valid_phone|xss_clean');
			$this->form_validation->set_rules('compania_celular', 'Compañía Celular', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('rol_usuario', 'Rol de usuario', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('categoria', 'Categoría', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('vertical', 'Vertical', 'required|callback_valid_option|xss_clean');

			if ( $this->form_validation->run() === TRUE ){
				if ($this->input->post( 'password' ) === $this->input->post( 'password_2' ) ){
					$usuario['nombre']   			= $this->input->post( 'nombre' );
					$usuario['apellidos']   		= $this->input->post( 'apellidos' );
					$usuario['email']   			= $this->input->post( 'email' );
					$usuario['extension']   		= $this->input->post( 'extension' );
					$usuario['password']   			= $this->input->post( 'password' );
					$usuario['celular']   			= $this->input->post( 'celular' );
					$usuario['compania_celular']   	= $this->input->post( 'compania_celular' );
					$usuario['rol_usuario']   		= $this->input->post( 'rol_usuario' );
					$usuario['verticales'] 			= $this->input->post( 'vertical' );
					$usuario['categorias'] 			= $this->input->post( 'categoria' );
					$guardar 					= $this->cms->editar_usuario( $usuario );
					if ( $guardar !== FALSE ){
						redirect('usuarios');
					} else {
						echo 'E02 - La información del usuario no puedo ser actualizada';
					}
				} else {
					echo '<span class="error">La <b>Contraseña</b> y la <b>Confirmación</b> no coinciden, verificalas.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
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

	public function reportes() {
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$data['usuario']	=	$this->session->userdata('nombre');
			$data['level']		=	$this->session->userdata('nivel');
			$this->load->view( 'cms/admin/reportes', $data );
		}
	}

	function nombre_valido($str){
		if(!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]/', $str)){
			$this->form_validation->set_message('nombre_valido','<b class="requerido">*</b> La información introducida en <b>%s</b> no es válida.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function valid_phone($str) {
        if ($str) {
            if (!preg_match('/\([0-9]\)| |[0-9]/', $str)) {
                $this->form_validation->set_message('valid_phone', '<b class="requerido">*</b> El <b>%s</b> no tiene un formato válido.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    function valid_option($str) {
        if ($str == 0) {
            $this->form_validation->set_message('valid_option', '<b class="requerido">*</b> Es necesario que selecciones una <b>%s</b>.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}