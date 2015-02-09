<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Controller {

	/**
	 * [__construct description]
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index() {

		if ( $this->session->userdata( 'session' ) !== TRUE ){
			redirect('login');
		} else {
			$this->load->view( 'cms/principal' );
		}
	}

	/**
	 * [login description]
	 * @return [type] [description]
	 */
	public function login() {
		if ( $this->session->userdata('session') !== TRUE ){
			$this->load->view('cms/login');
		} else {
			$this->load->view('cms/principal' );
		}
	}

	/**
	 * [logout description]
	 * @return [type] [description]
	 */
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
			$usuario['usuario'] 	= $this->input->post( 'usuario' );
			$usuario['password'] 	= $this->input->post( 'password' );
			$usuario 				= $this->security->xss_clean( $usuario );
			$valido 				= $this->cms->get_usuario( $usuario );
			if ( $valido !== FALSE ){
				$session = array(
					'session'	 	=> TRUE,
					'uid' 		 	=> $valido->uid_usuario,
					'nombre' 	 	=> $valido->nombre,
					'apellidos'		=> $valido->apellidos,
					'email'			=> $valido->email,
					'extension'		=> $valido->extension,
					'celular'		=> $valido->celular,
					'telefonica'	=> $valido->compania_celular,
					'nivel' 	 	=> $valido->nivel
					);
				$this->session->set_userdata($session);
				echo TRUE;
			} else {
				echo '<span class="error">Datos incorrectos!</span>';
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
		if ( $this->session->userdata('session') !== TRUE ) {
			redirect('login');
		} else {
			if ( $this->session->userdata('nivel') <= 2 ){
				$config['base_url'] 			= base_url() . 'usuarios/page/';
	            $config['per_page'] 			= 10;
	            $config['num_links'] 			= 4;
	            $config['uri_segment'] 			= 3;
	            $config['full_tag_open'] 		= '<ul class="pagination">';
	            $config['full_tag_close'] 		= '</ul>';
	            $config['first_tag_open'] 		= '<li>';
	            $config['first_tag_close'] 		= '</li>';
	            $config['first_link'] 			= 'Primero';
	            $config['last_tag_open'] 		= '<li>';
	            $config['last_tag_close'] 		= '</li>';
	            $config['last_link'] 			= 'Último';
	            $config['next_tag_open'] 		= '<li>';
	            $config['next_tag_close'] 		= '</li>';
	            $config['next_link'] 			= '&raquo;';
	            $config['prev_tag_open'] 		= '<li>';
				$config['prev_tag_close'] 		= '</li>';
	            $config['prev_link'] 			= '&laquo;';
	            $config['num_tag_open'] 		= '<li>';
	            $config['num_tag_close'] 		= '</li>';
	            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
	            $config['cur_tag_close'] 		= '</a></li>';
	            $config['total_rows'] 			= $this->cms->get_total_usuarios( $this->session->userdata( 'nivel' ), $this->session->userdata( 'uid' ) );
	            $this->pagination->initialize( $config );
	            $page 							= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 					= $this->pagination->create_links();
				$data['usuarios']				= $this->cms->get_usuarios( $this->session->userdata( 'nivel' ), $this->session->userdata( 'uid' ), $config['per_page'], $page );
				$this->load->view( 'cms/admin/usuarios', $data );
			} else {
				redirect('inicio');
			}
		}
	}

	/**
	 * [admin_trabajos description]
	 * @return [type] [description]
	 */
	public function admin_trabajos(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$config['base_url'] 			= base_url() . 'trabajos/page/';
            $config['per_page'] 			= 10;
            $config['num_links'] 			= 4;
            $config['uri_segment'] 			= 3;
            $config['full_tag_open'] 		= '<ul class="pagination">';
            $config['full_tag_close'] 		= '</ul>';
            $config['first_tag_open'] 		= '<li>';
            $config['first_tag_close'] 		= '</li>';
            $config['first_link'] 			= 'Primero';
            $config['last_tag_open'] 		= '<li>';
            $config['last_tag_close'] 		= '</li>';
            $config['last_link'] 			= 'Último';
            $config['next_tag_open'] 		= '<li>';
            $config['next_tag_close'] 		= '</li>';
            $config['next_link'] 			= '&raquo;';
            $config['prev_tag_open'] 		= '<li>';
			$config['prev_tag_close'] 		= '</li>';
            $config['prev_link'] 			= '&laquo;';
            $config['num_tag_open'] 		= '<li>';
            $config['num_tag_close'] 		= '</li>';
            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
            $config['cur_tag_close'] 		= '</a></li>';
			if ( $this->session->userdata('nivel') >= 1 && $this->session->userdata('nivel') <= 2 ){
				$config['total_rows'] 		= $this->cms->get_total_trabajos();
	            $this->pagination->initialize( $config );
	            $page 						= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 				= $this->pagination->create_links();
				$data['trabajos']			= $this->cms->get_trabajos( $config['per_page'], $page );
			}else {
				$config['total_rows'] 		= $this->cms->get_total_trabajos( $this->session->userdata( 'uid' ) );
	            $this->pagination->initialize( $config );
	            $page 						= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 				= $this->pagination->create_links();
				$data['trabajos']			= $this->cms->get_trabajos_editor( $this->session->userdata( 'uid' ), $config['per_page'], $page );
			}
			$this->load->view( 'cms/admin/trabajos', $data );
		}
	}

	/**
	 * [admin_categorias description]
	 * @return [type] [description]
	 */
	public function admin_categorias(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			if ( $this->session->userdata('nivel') <= 2 ){
				$config['base_url'] 			= base_url() . 'categorias/page/';
	            $config['per_page'] 			= 10;
	            $config['num_links'] 			= 4;
	            $config['uri_segment'] 			= 3;
	            $config['full_tag_open'] 		= '<ul class="pagination">';
	            $config['full_tag_close'] 		= '</ul>';
	            $config['first_tag_open'] 		= '<li>';
	            $config['first_tag_close'] 		= '</li>';
	            $config['first_link'] 			= 'Primero';
	            $config['last_tag_open'] 		= '<li>';
	            $config['last_tag_close'] 		= '</li>';
	            $config['last_link'] 			= 'Último';
	            $config['next_tag_open'] 		= '<li>';
	            $config['next_tag_close'] 		= '</li>';
	            $config['next_link'] 			= '&raquo;';
	            $config['prev_tag_open'] 		= '<li>';
				$config['prev_tag_close'] 		= '</li>';
	            $config['prev_link'] 			= '&laquo;';
	            $config['num_tag_open'] 		= '<li>';
	            $config['num_tag_close'] 		= '</li>';
	            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
	            $config['cur_tag_close'] 		= '</a></li>';
	            $config['total_rows'] 			= $this->cms->get_total_categorias();
	            $this->pagination->initialize( $config );
	            $page 							= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 					= $this->pagination->create_links();
				$data['categorias']				= $this->cms->get_categorias( 'fecha_registro', $config['per_page'], $page );
				$this->load->view( 'cms/admin/categorias', $data );
			} else {
				redirect('inicio');
			}
		}
	}

	/**
	 * [admin_verticales description]
	 * @return [type] [description]
	 */
	public function admin_verticales(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			if ( $this->session->userdata('nivel') <= 2 ){
				$config['base_url'] 			= base_url() . 'verticales/page/';
	            $config['per_page'] 			= 10;
	            $config['num_links'] 			= 4;
	            $config['uri_segment'] 			= 3;
	            $config['full_tag_open'] 		= '<ul class="pagination">';
	            $config['full_tag_close'] 		= '</ul>';
	            $config['first_tag_open'] 		= '<li>';
	            $config['first_tag_close'] 		= '</li>';
	            $config['first_link'] 			= 'Primero';
	            $config['last_tag_open'] 		= '<li>';
	            $config['last_tag_close'] 		= '</li>';
	            $config['last_link'] 			= 'Último';
	            $config['next_tag_open'] 		= '<li>';
	            $config['next_tag_close'] 		= '</li>';
	            $config['next_link'] 			= '&raquo;';
	            $config['prev_tag_open'] 		= '<li>';
				$config['prev_tag_close'] 		= '</li>';
	            $config['prev_link'] 			= '&laquo;';
	            $config['num_tag_open'] 		= '<li>';
	            $config['num_tag_close'] 		= '</li>';
	            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
	            $config['cur_tag_close'] 		= '</a></li>';
	            $config['total_rows'] 			= $this->cms->get_total_verticales();
	            $this->pagination->initialize( $config );
	            $page 							= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 					= $this->pagination->create_links();
				$data['verticales']				= $this->cms->get_verticales( 'fecha_registro', $config['per_page'], $page );
				$this->load->view('cms/admin/verticales', $data);
			} else {
				redirect('inicio');
			}
		}
	}

	/**
	 * [admin_estructuras description]
	 * @return [type] [description]
	 */
	public function admin_estructuras(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			if ( $this->session->userdata('nivel') <= 2 ){
				$config['base_url'] 			= base_url() . 'estructuras/page/';
	            $config['per_page'] 			= 10;
	            $config['num_links'] 			= 4;
	            $config['uri_segment'] 			= 3;
	            $config['full_tag_open'] 		= '<ul class="pagination">';
	            $config['full_tag_close'] 		= '</ul>';
	            $config['first_tag_open'] 		= '<li>';
	            $config['first_tag_close'] 		= '</li>';
	            $config['first_link'] 			= 'Primero';
	            $config['last_tag_open'] 		= '<li>';
	            $config['last_tag_close'] 		= '</li>';
	            $config['last_link'] 			= 'Último';
	            $config['next_tag_open'] 		= '<li>';
	            $config['next_tag_close'] 		= '</li>';
	            $config['next_link'] 			= '&raquo;';
	            $config['prev_tag_open'] 		= '<li>';
				$config['prev_tag_close'] 		= '</li>';
	            $config['prev_link'] 			= '&laquo;';
	            $config['num_tag_open'] 		= '<li>';
	            $config['num_tag_close'] 		= '</li>';
	            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
	            $config['cur_tag_close'] 		= '</a></li>';
	            $config['total_rows'] 			= $this->cms->get_total_estructuras();
	            $this->pagination->initialize( $config );
	            $page 							= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 					= $this->pagination->create_links();
	            //$data['total']				= $config['total_rows'];
				$data['estructuras'] 			= $this->cms->get_all_estructuras( $config['per_page'], $page );
				$this->load->view( 'cms/admin/estructuras', $data );
			}
		}
	}

	/**
	 * [recuperar_contrasena description]
	 * @return [type] [description]
	 */
	public function recuperar_contrasena(){
		if ( $this->session->userdata('session') !== TRUE ){
			$this->load->view('cms/admin/forgot');
		} else {
			redirect('inicio');
		}
	}

	/**
	 * [recupera_contrasena description]
	 * @return [type] [description]
	 */
	public function recupera_contrasena(){
		if ( $this->session->userdata('session') !== TRUE ){
			$this->form_validation->set_rules('forgot_email', 'Correo Electrónico', 'trim|required|valid_email|xss_clean');
			if ( $this->form_validation->run() === TRUE ){
				$recupera['email'] 		=	$this->input->post( 'forgot_email' );
				$recupera 				=	$this->security->xss_clean( $recupera );
				$valido 				=	$this->cms->get_usuario_forgot( $recupera );
				if ( $valido !== FALSE ){
					$token 	= urlencode( base64_encode( $valido->uid_usuario ) );
					$hash 	= base64_encode( $recupera['email'] );
					$data_forgot['url'] = base_url().'forgot_validate?token=' . $token . '&hash=' . $hash;
					$this->email->from('desarrollo@estrategasdigitales.com', 'Sistema de Administración de Tareas y Contenidos para Middleware');
	                $this->email->to( $recupera['email'] );
	                $this->email->subject('Código de recuperación de contraseña');
	                $this->email->message( $this->load->view('cms/mail/codigo_recuperacion', $data_forgot, TRUE ) );
	                $this->email->send();
					echo TRUE;
				} else {
					echo '<span class="error">Datos incorrectos!</span>';
				}
			} else {
				echo validation_errors('<span class="error">','</span>');
			}
		} else {
			redirect('inicio');
		}
	}

	/**
	 * [recuperar_contrasena_validar description]
	 * @return [type] [description]
	 */
	public function recuperar_contrasena_validar(){
		if ( ! empty( $this->input->get('token') ) && ! empty( $this->input->get('hash') ) ){
			$recovery['uid'] 	= urldecode( base64_decode( $this->input->get('token') ) );
			$recovery['email'] 	= base64_decode( $this->input->get('hash') );
			$recovery 			= $this->security->xss_clean( $recovery );
			$usuario_recupera 	= $this->cms->get_recupera_usuario( $recovery );
			if ( $usuario_recupera !== FALSE ){
				$data['contrasena'] = $usuario_recupera->contrasena;
				$this->email->from('desarrollo@estrategasdigitales.com', 'Sistema de Administración de Tareas y Contenidos para Middleware');
                $this->email->to( $recovery['email'] );
                $this->email->subject('Recuperación de contraseña');
                $this->email->message( $this->load->view('cms/mail/recovery_password', $data, TRUE ) );
                $this->email->send();
				exit('En breve recibiras un correo con tu contrase&ntilde;a');
			} else {
				exit('Error');	
			}
		} else {
			exit('Error');
		}
	}

	/**
	 * [actualizar_perfil description]
	 * @return [type] [description]
	 */
	public function actualizar_perfil(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$perfil['uid']			= $this->session->userdata('uid');
			$perfil['nombre']		= $this->session->userdata('nombre');
			$perfil['apellidos']	= $this->session->userdata('apellidos');
			$perfil['email']		= $this->session->userdata('email');
			$perfil['celular']		= $this->session->userdata('celular');
			$perfil['extension']	= $this->session->userdata('extension');
			$perfil['telefonica']	= $this->session->userdata('telefonica');
			$perfil['companias']	= $this->cms->get_companias_celular();
			$this->load->view('cms/admin/actualizar_perfil', $perfil);
		}
	}

	/**
	 * [actualizar_perfil_actualizar description]
	 * @return [type] [description]
	 */
	public function actualizar_perfil_actualizar(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|callback_nombre_valido|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|min_length[3]|max_lenght[180]|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean');
			$this->form_validation->set_rules('extension', 'Extensión', 'trim|numeric|xss_clean');
			if ( ( ! empty( $this->input->post('password') ) ) || ( ! empty( $this->input->post('password_2') ) ) ){
				$this->form_validation->set_rules('password', 'Contraseña', 'required|trim|min_length[8]|xss_clean');
				$this->form_validation->set_rules('password_2', 'Confirmar Contraseña', 'required|trim|min_length[8]|xss_clean');
			}
			$this->form_validation->set_rules('celular', 'Número Celular', 'required|trim|callback_valid_phone|xss_clean');
			$this->form_validation->set_rules('compania_celular', 'Compañía Celular', 'required|callback_valid_option|xss_clean');
			$this->form_validation->set_rules('password_actual', 'Contraseña Actual', 'required|trim|min_length[8]|xss_clean');

			if ($this->form_validation->run() === TRUE){
				$check_user = $this->cms->check_user( $this->input->post( 'email' ), base64_decode( $this->input->post( 'usuario' ) ) );
				if ( $check_user === FALSE ){
					$usuario['nombre']   			= $this->input->post( 'nombre' );
					$usuario['apellidos']   		= $this->input->post( 'apellidos' );
					$usuario['email']   			= $this->input->post( 'email' );
					$usuario['extension']   		= $this->input->post( 'extension' );
					if ( ( ! empty( $this->input->post('password') ) ) || ( ! empty( $this->input->post('password_2') ) ) ){
						if ($this->input->post( 'password' ) === $this->input->post( 'password_2' ) ){
							$usuario['password']   			= $this->input->post( 'password' );
						} else {
							echo '<span class="error">La <b>Contraseña</b> y la <b>Confirmación</b> no coinciden, verifícalas.</span>';
							break;
						}
					}
					$usuario['celular']   			= $this->input->post( 'celular' );
					$usuario['compania_celular']   	= $this->input->post( 'compania_celular' );
					$usuario['uid']   				= base64_decode( $this->input->post( 'usuario' ) );
					$usuario['password_actual']   	= base64_encode( $this->input->post( 'password_actual' ) );
					$usuario 						= $this->security->xss_clean( $usuario );
					$actualizar 					= $this->cms->update_perfil_usuario( $usuario );
					if ( $actualizar !== FALSE ){
						$session = array(
							'session'	 	=> TRUE,
							'uid' 		 	=> $usuario['uid'],
							'nombre' 	 	=> $usuario['nombre'],
							'apellidos'		=> $usuario['apellidos'],
							'email'			=> $usuario['email'],
							'extension'		=> $usuario['extension'],
							'celular'		=> $usuario['celular'],
							'telefonica'	=> $usuario['compania_celular'],
							'nivel' 	 	=> $this->session->userdata('nivel')
							);
						$this->session->set_userdata( $session );
						echo TRUE;
					} else {
						echo '<span class="error">Ocurrió un problema al intentar actualizar la información.</span>';
					}
				} else {
					echo '<span class="error">El <b>Correo electrónico</b> ya se encuentra asignado a una cuenta.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
			}

		}
	}

	/**
	 * [modal_eliminar_usuario description]
	 * @return [type] [description]
	 */
	public function modal_eliminar_usuario(){
		$data['nombre_completo'] 	= $this->input->get('name');
		$data['uid'] 				= $this->input->get('token');
		$this->load->view( 'cms/admin/modal_eliminar_usuario', $data );
	}

	/**
	 * [modal_eliminar_categoria description]
	 * @return [type] [description]
	 */
	public function modal_eliminar_categoria(){
		$data['nombre_categoria'] 	= $this->input->get('name');
		$data['uid'] 				= $this->input->get('token');
		$this->load->view( 'cms/admin/modal_eliminar_categoria', $data );
	}

	/**
	 * [modal_eliminar_vertical description]
	 * @return [type] [description]
	 */
	public function modal_eliminar_vertical(){
		$data['nombre_vertical'] 	= $this->input->get('name');
		$data['uid'] 				= $this->input->get('token');
		$this->load->view( 'cms/admin/modal_eliminar_vertical', $data );
	}

	/**
	 * [modal_eliminar_trabajo description]
	 * @return [type] [description]
	 */
	public function modal_eliminar_trabajo(){
		$data['nombre_trabajo']		= $this->input->get('name');
		$data['uid']				= $this->input->get('token');
		$this->load->view( 'cms/admin/modal_eliminar_trabajo', $data );
	}

	/**
	 * [modal_eliminar_estructura description]
	 * @return [type] [description]
	 */
	public function modal_eliminar_estructura(){
		$data['nombre_estructura']		= $this->input->get('name');
		$data['uid']				= $this->input->get('token');
		$this->load->view( 'cms/admin/modal_eliminar_estructura', $data );
	}

	/**
	 * [modal_listar_categorias_asignadas description]
	 * @return [type] [description]
	 */
	public function modal_listar_categorias_asignadas(){
		$data['uid']		= $this->input->get( base64_decode( 'token' ) );
		$data['categorias']	= $this->cms->get_categorias_usuario( $data['uid'] );
		$this->load->view( 'cms/admin/modal_listar_categorias_asignadas', $data );
	}

	/**
	 * [modal_listar_verticales_asignadas description]
	 * @return [type] [description]
	 */
	public function modal_listar_verticales_asignadas(){
		$data['uid']		= $this->input->get( base64_decode( 'token' ) );
		$data['verticales']	= $this->cms->get_verticales_usuario( $data['uid'] );
		$this->load->view( 'cms/admin/modal_listar_verticales_asignadas', $data );
	}

	/**
	 * [nuevo_usuario description]
	 * @return [type] [description]
	 */
	public function nuevo_usuario(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$data['categorias'] = $this->cms->get_categorias( 'nombre' );
			$data['verticales'] = $this->cms->get_verticales( 'nombre' );
			$data['companias']	= $this->cms->get_companias_celular();
			$data['roles']		= $this->cms->get_catalogo_roles();
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
					$check_user = $this->cms->check_user( $this->input->post( 'email' ) );
					if ( $check_user === FALSE ){
						$usuario['nombre']   			= $this->input->post( 'nombre' );
						$usuario['apellidos']   		= $this->input->post( 'apellidos' );
						$usuario['email']   			= $this->input->post( 'email' );
						$usuario['extension']   		= $this->input->post( 'extension' );
						$usuario['password']   			= $this->input->post( 'password' );
						$usuario['celular']   			= $this->input->post( 'celular' );
						$usuario['compania_celular']   	= $this->input->post( 'compania_celular' );
						$usuario['rol_usuario']   		= $this->input->post( 'rol_usuario' );
						$usuario['verticales'] 			= json_encode( $this->input->post( 'vertical' ) );
						$usuario['categorias'] 			= json_encode( $this->input->post( 'categoria' ) );
						$usuario 						= $this->security->xss_clean( $usuario );
						$guardar 						= $this->cms->add_usuario( $usuario );
						if ( $guardar !== FALSE ){
							echo TRUE;
						} else {
							echo '<span class="error"><b>E01</b> - El nuevo usuario no pudo ser agregado</span>';
						}
					} else {
						echo '<span class="error">El <b>Correo electrónico</b> ya se encuentra asignado a una cuenta.</span>';
					}
				} else {
					echo '<span class="error">La <b>Contraseña</b> y la <b>Confirmación</b> no coinciden, verificalas.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [editar_usuario description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function editar_usuario( $uid = '' ){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$uid = base64_decode( $uid );
			$usuario = $this->cms->get_usuario_editar( $uid );
			if ( $usuario !== FALSE ){
				$data['usuario']    		= $this->session->userdata('nombre');
				$data['categorias'] 		= $this->cms->get_categorias('nombre');
				$data['verticales'] 		= $this->cms->get_verticales('nombre');
				$data['usuario_editar']  	= $usuario;
				$data['cats']				= $this->cms->get_categorias_asignadas( $uid );
				$data['vers']				= $this->cms->get_verticales_asignadas( $uid );
				$data['companias']			= $this->cms->get_companias_celular();
				$data['roles']				= $this->cms->get_catalogo_roles();
				$data['uid']				= $uid;
				$this->load->view('cms/admin/editar_usuario',$data);
			} else {
				$data['error'] = "No se ha encontrado el usuario";
				$this->load->view('cms/admin/editar_usuario', $data);
			}
		}
	}

	/**
	 * [validar_form_usuario_editar description]
	 * @return [type] [description]
	 */
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
					$uid = base64_decode( $this->input->post('token') );
					$check_user = $this->cms->check_user( $this->input->post( 'email' ), $uid );
					if ( $check_user === FALSE ){
						$usuario['nombre']   			= $this->input->post( 'nombre' );
						$usuario['apellidos']   		= $this->input->post( 'apellidos' );
						$usuario['email']   			= $this->input->post( 'email' );
						$usuario['extension']   		= $this->input->post( 'extension' );
						$usuario['password']   			= $this->input->post( 'password' );
						$usuario['celular']   			= $this->input->post( 'celular' );
						$usuario['compania_celular']   	= $this->input->post( 'compania_celular' );
						$usuario['rol_usuario']   		= $this->input->post( 'rol_usuario' );
						$usuario['verticales'] 			= json_encode( $this->input->post( 'vertical' ) );
						$usuario['categorias'] 			= json_encode( $this->input->post( 'categoria' ) );
						$usuario['uid_usuario']			= $uid;
						$usuario 						= $this->security->xss_clean( $usuario );
						$guardar 						= $this->cms->editar_usuario( $usuario );
						if ( $guardar !== FALSE ){
							echo TRUE;
						} else {
							echo '<span class="error"><b>E02</b> - La información del usuario no puedo ser actualizada</span>';
						}
					} else {
						echo '<span class="error">El <b>Correo electrónico</b> ya se encuentra asignado a una cuenta.</span>';
					}
				} else {
					echo '<span class="error">La <b>Contraseña</b> y la <b>Confirmación</b> no coinciden, verificalas.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [eliminar_usuario description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function eliminar_usuario(){
		$uid = $this->input->post('token');
		$eliminar = $this->cms->delete_usuario( base64_decode( $uid ) );
		if ( $eliminar !== FALSE ){
			echo TRUE;
		} else {
			echo '<span class="error">No se ha podido eliminar al usuario</span>';
		}
	}

	/**
	 * [nueva_categoria description]
	 * @return [type] [description]
	 */
	public function nueva_categoria(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->load->view( 'cms/admin/nueva_categoria' );
		}
	}

	/**
	 * [validar_form_categoria description]
	 * @return [type] [description]
	 */
	public function validar_form_categoria(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$this->form_validation->set_rules( 'nombre_categoria', 'Nombre de la Categoría', 'required|min_length[3]|xss_clean' );
			if ( $this->form_validation->run() === TRUE ){
				if ( $this->cms->validar_categoria( url_title( $this->input->post( 'nombre_categoria' ), 'dash', TRUE ) ) != TRUE ){
					$categoria['nombre']   			= $this->input->post( 'nombre_categoria' );
					$categoria['slug_categoria']   	= url_title( $this->input->post( 'nombre_categoria' ), 'dash', TRUE );
					$categoria 						= $this->security->xss_clean( $categoria );
					$guardar 						= $this->cms->add_categoria( $categoria );
					if ( $guardar !== FALSE ){
						echo TRUE;
					} else {
						echo '<span class="error">La nueva categoria no pudo ser guardada</span>';
					}
				} else {
					echo '<span class="error">La <b>Categoría</b> ya existe, prueba con otro nombre.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [eliminar_categoria description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function eliminar_categoria(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$uid = $this->input->post('token');
			$eliminar = $this->cms->delete_categoria( base64_decode( $uid ) );
			if ( $eliminar !== FALSE ){
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podido eliminar la categoría</span>';
			}
		}
	}

	/**
	 * [nueva_vertical description]
	 * @return [type] [description]
	 */
	public function nueva_vertical(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->load->view( 'cms/admin/nueva_vertical' );
		}
	}

	/**
	 * [validar_form_vertical description]
	 * @return [type] [description]
	 */
	public function validar_form_vertical(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre_vertical', 'Nombre de la Vertical', 'required|min_length[3]|xss_clean');
			if ( $this->form_validation->run() === TRUE ){
				if ( $this->cms->validar_vertical( url_title( $this->input->post( 'nombre_vertical' ), 'dash', TRUE ) ) != TRUE ){
					$vertical['nombre']   		= $this->input->post('nombre_vertical');
					$vertical['slug_vertical']	= url_title( $this->input->post('nombre_vertical'), 'dash', TRUE );
					$vertical 					= $this->security->xss_clean( $vertical );
					$guardar 					= $this->cms->add_vertical( $vertical );
					if ( $guardar !== FALSE ){
						echo TRUE;
					} else {
						echo '<span class="error">La nueva vertical no pudo ser guardada</span>';
					}
				} else {
					echo '<span class="error">La <b>Vertical</b> ya existe, prueba con otro nombre.</span>';
				}
			} else {			
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [eliminar_vertical description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function eliminar_vertical(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$uid = $this->input->post('token');
			$eliminar = $this->cms->delete_vertical( base64_decode( $uid ) );
			if ( $eliminar !== FALSE ){
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podido eliminar la vertical</span>';
			}
		}
	}

	/**
	 * [nuevo_trabajo description]
	 * @return [type] [description]
	 */
	public function nuevo_trabajo(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			if ( $this->session->userdata( 'nivel' ) == 1 ) {
				$data['categorias'] = $this->cms->get_categorias('nombre');
				$data['verticales'] = $this->cms->get_verticales('nombre');
			} else {
				$data['categorias'] = $this->cms->get_categorias_usuario( $this->session->userdata( 'uid' ) );
				$data['verticales'] = $this->cms->get_verticales_usuario( $this->session->userdata( 'uid' ) );
			}
			$data['estructuras']	= $this->cms->get_estructuras();
			$this->load->view( 'cms/admin/nuevo_trabajo', $data );
		}
	}

	/**
	 * [nueva_estructura description]
	 * @return [type] [description]
	 */
	public function nueva_estructura(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->load->view( 'cms/admin/nueva_estructura' );
		}
	}

	/**
	 * [validar_form_nueva_estructura description]
	 * @return [type] [description]
	 */
	public function validar_form_nueva_estructura(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre', 'Nombre de la estructura', 'trim|required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('url-origen', 'URL Origen', 'required|min_length[3]|xss_clean');
			
			if ( $this->form_validation->run() === TRUE ){
				$trabajo['usuario'] 			= $this->session->userdata('uid');
				$trabajo['nombre']   			= $this->input->post('nombre');
				$trabajo['slug_nombre_feed']	= url_title( $this->input->post('nombre'), 'dash', TRUE );
				$trabajo['url-origen']   		= $this->input->post('url-origen');
				$trabajo['formato_salida']		= $this->cms->detect_format( $this->input->post('url-origen') );
				switch ( $trabajo['formato_salida'] ){
                    case 'JSON':
                        $trabajo['encoding']	= '';
                        $trabajo['headers']		= '';
                        break;
					case 'XML':
						$trabajo['encoding']	= $this->detect_encoding( $this->input->post( 'url-origen' ) );
						$trabajo['headers']		= '';
						break;
					case 'RSS':
						$trabajo['encoding']	= $this->detect_encoding( $this->input->post( 'url-origen' ) );
						$trabajo['headers']		= base64_encode( $this->detect_headers( $this->input->post( 'url-origen' ) ) );
						break;
					default:
						$trabajo['encoding']	= '';
						$trabajo['headers']		= '';

                        $trabajo['variable']       = $trabajo['formato_salida'];
                        $trabajo['formato_salida'] = 'JSONP';


						break;
				}


				$trabajo['json_estructura']		= 'nucleo/feed_service_specific?url=' . urlencode( base64_encode( $this->input->post('url-origen') ) );
				$trabajo 						= $this->security->xss_clean( $trabajo );
				$guardar 						= $this->cms->add_estructura( $trabajo );
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
	 * [eliminar_estructura description]
	 * @return [type] [description]
	 */
	public function eliminar_estructura(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$uid = $this->input->post('token');
			$eliminar = $this->cms->delete_estructura( base64_decode( $uid ) );
			if ( $eliminar !== FALSE ){
				echo TRUE;
			} else {
				echo '<span class="error">No se ha podido eliminar la estructura</span>';
			}
		}
	}

	/**
	 * [reportes description]
	 * @return [type] [description]
	 */
	public function reportes() {
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			$config['base_url'] 			= base_url() . 'reportes/page/';
            $config['per_page'] 			= 10;
            $config['num_links'] 			= 4;
            $config['uri_segment'] 			= 3;
            $config['full_tag_open'] 		= '<ul class="pagination">';
            $config['full_tag_close'] 		= '</ul>';
            $config['first_tag_open'] 		= '<li>';
            $config['first_tag_close'] 		= '</li>';
            $config['first_link'] 			= 'Primero';
            $config['last_tag_open'] 		= '<li>';
            $config['last_tag_close'] 		= '</li>';
            $config['last_link'] 			= 'Último';
            $config['next_tag_open'] 		= '<li>';
            $config['next_tag_close'] 		= '</li>';
            $config['next_link'] 			= '&raquo;';
            $config['prev_tag_open'] 		= '<li>';
			$config['prev_tag_close'] 		= '</li>';
            $config['prev_link'] 			= '&laquo;';
            $config['num_tag_open'] 		= '<li>';
            $config['num_tag_close'] 		= '</li>';
            $config['cur_tag_open'] 		= '<li class="active"><a href="#">';
            $config['cur_tag_close'] 		= '</a></li>';
			if ( $this->session->userdata('nivel') >= 1 && $this->session->userdata('nivel') <= 2 ){
				$config['total_rows'] 		= $this->cms->get_total_reportes();
	            $this->pagination->initialize( $config );
	            $page 						= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 				= $this->pagination->create_links();
				$data['reportes']			= $this->cms->get_reportes( $config['per_page'], $page );
			}else {
				$config['total_rows'] 		= $this->cms->get_total_reportes_editor( $this->session->userdata( 'uid' ) );
	            $this->pagination->initialize( $config );
	            $page 						= ( $this->uri->segment(3) ) ? $this->uri->segment(3) : 0;
	            $data['links'] 				= $this->pagination->create_links();
				$data['reportes']			= $this->cms->get_reportes_editor( $this->session->userdata( 'uid' ), $config['per_page'], $page );
			}
			$this->load->view( 'cms/admin/reportes', $data );
		}
	}


	/**
	 * [nuevo_reporte description]
	 * @return [type] [description]
	 */
	public function nuevo_reporte(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect( 'login' );
		} else {
			if ( $this->session->userdata('nivel') >= 1 && $this->session->userdata('nivel') <= 2 ){
				$data['trabajos']	= $this->cms->get_trabajos();
			}else {
				$data['trabajos']	= $this->cms->get_trabajos_editor( $this->session->userdata( 'uid' ) );
			}
			$this->load->view('cms/admin/nuevo_reporte', $data);
		}
	}

	/**
	 * [validar_form_nuevo_reporte description]
	 * @return [type] [description]
	 */
	public function validar_form_nuevo_reporte(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$this->form_validation->set_rules('nombre_reporte', 'Nombre del Reporte', 'trim|required|min_length[3]|max_lenght[45]|xss_clean');
			$this->form_validation->set_rules('fecha_inicio', 'Fecha Inicio', 'required|callback_valid_date|xss_clean');
			$this->form_validation->set_rules('fecha_termino', 'Fecha Final', 'required|callback_valid_date|xss_clean');
			$this->form_validation->set_rules('trabajos', 'Trabajos', 'required|xss_clean');
			
			if ( $this->form_validation->run() === TRUE ){
				$reporte['uid_usuario'] 			= $this->session->userdata('uid');
				$reporte['nombre_reporte'] 			= $this->input->post('nombre_reporte');
				$reporte['slug_nombre_reporte']		= url_title( $this->input->post('nombre_reporte'), 'dash', TRUE );
				$reporte['fecha_inicio']   			= strtotime( $this->input->post('fecha_inicio') );
				$reporte['fecha_fin']				= strtotime( $this->input->post('fecha_termino') );
				$reporte['trabajos']				= json_encode( $this->input->post('trabajos') );
				$reporte 							= $this->security->xss_clean( $reporte );
				$guardar 							= $this->cms->add_reporte( $reporte );
				if ( $guardar !== FALSE ){
					echo TRUE;
				} else {
					echo '<span class="error">Ocurrió un problema al intentar guardar el <b>Reporte</b></span>';
				}
			} else {
				echo validation_errors('<span class="error">','</span>');
			}
		}
	}

	/**
	 * [generar_reporte_csv description]
	 * @return [type] [description]
	 */
	public function generar_reporte_csv(){
		$reporte = $this->cms->get_reporte_detalle( base64_decode( $this->input->get('token') ) );
		$resultado = $this->cms->get_reporte_resultado( $reporte );
		echo query_to_csv( $resultado, TRUE, $reporte->slug_nombre_reporte . '.csv');
	}

	/**
	 * [generar_reporte_excel description]
	 * @return [type] [description]
	 */
	public function generar_reporte_excel(){
		$reporte = $this->cms->get_reporte_detalle( base64_decode( $this->input->get('token') ) );
		$resultado = $this->cms->get_reporte_resultado( $reporte );
		echo query_to_excel( $resultado, $reporte->slug_nombre_reporte . '.xls');
	}

	/**
	 * [generar_reporte_pdf description]
	 * @return [type] [description]
	 */
	public function generar_reporte_pdf(){
		$reporte = $this->cms->get_reporte_detalle( base64_decode( $this->input->get('token') ) );
		$resultado = $this->cms->get_reporte_resultado( $reporte );
		$this->pdf = new Pdf();
		$this->pdf->AddPage();
		$this->pdf->AliasNbPages();
		$this->pdf->SetTitle('Reporte de Tareas');
		$this->pdf->SetLeftMargin(15);
        $this->pdf->SetRightMargin(15);
        $this->pdf->SetFillColor(200,200,200);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->Cell(15,3,'ID','TBL',0,'C','1');
        $this->pdf->Cell(25,3,'FECHA','TB',0,'L','1');
        $this->pdf->Cell(25,3,'STATUS','TB',0,'L','1');
        $this->pdf->Ln(3);
        $x = 1;
        foreach ($resultado->result() as $result) {
            // se imprime el numero actual y despues se incrementa el valor de $x en uno
            $this->pdf->Cell(15,3,$x++,'BL',0,'C',0);
            // Se imprimen los datos de cada trabajo
            $this->pdf->Cell(25,3,$result->uid_trabajo,'B',0,'L',0);
            $this->pdf->Cell(25,3,$result->time,'B',0,'L',0);
            $this->pdf->Cell(25,3,$result->status,'B',0,'L',0);
            //Se agrega un salto de linea
            $this->pdf->Ln(3);
        }
        $this->pdf->Output( $reporte->slug_nombre_reporte . '.pdf', 'I');
	}

	/**
	 * Método para detectar el encoding del feed
	 * @param  string $url Url del Feed
	 * @return
	 */
	private function detect_encoding( $url ){
		$url = file_get_contents_curl( $url );
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML( $url );
		return $dom->xmlEncoding;
	}

	/**
	 * Método para detectar los headers / namespaces del feed
	 * @param  string $url Url del feed
	 * @return
	 */
	private function detect_headers( $url ){
		$headers = '';
		$url = file_get_contents_curl( $url );	
		preg_match_all('/<rss[^>]+>/i', $url, $headers );
		return htmlentities( $headers[0][0] );
	}

	/**
	 * [nombre_valido description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	function nombre_valido( $str ){
		if ( ! preg_match( '/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]/', $str ) ){
			$this->form_validation->set_message( 'nombre_valido','<b class="requerido">*</b> La información introducida en <b>%s</b> no es válida.' );
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * [valid_phone description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	function valid_phone( $str ){
        if ( $str ) {
            if ( ! preg_match( '/\([0-9]\)| |[0-9]/', $str ) ){
                $this->form_validation->set_message( 'valid_phone', '<b class="requerido">*</b> El <b>%s</b> no tiene un formato válido.' );
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * [valid_option description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    function valid_option( $str ){
        if ($str == 0) {
            $this->form_validation->set_message('valid_option', '<b class="requerido">*</b> Es necesario que selecciones una <b>%s</b>.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * [valid_date description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    function valid_date( $str ){
    	$arr = explode('/', $str);
    	if ( count($arr) == 3 ){
    		$m = $arr[0];
    		$d = $arr[1];
    		$y = $arr[2];
    		if ( is_numeric( $m ) && is_numeric( $d ) && is_numeric( $y ) ){
    			return checkdate($m, $d, $y);
    		} else {
    			$this->form_validation->set_message('valid_date', '<b class="requerido">*</b> El campo <b>%s</b> debe tener una fecha válida con el formato MM/DD/YYYY.');
    			return FALSE;
    		}
    	} else {
    		$this->form_validation->set_message('valid_date', '<b class="requerido">*</b> El campo <b>%s</b> debe tener una fecha válida con el formato MM/DD/YYYY.');
    		return FALSE;
    	}
    }
}