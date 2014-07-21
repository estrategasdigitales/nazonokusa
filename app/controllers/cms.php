<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Controller {

	private $storage_root;
	private $netstorage;

	/**
	 * [__construct description]
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('cms_model', 'cms');

		$this->storage_root = '/';

		/** Configuracion de conexión a netstorage */
		$this->netstorage = array(
				'hostname' 	=> 'storagemas.upload.akamai.com',
				'username' 	=> 'marcoplata',
				'password' 	=> 'y4mi.99yS',
				'passive'	=> TRUE,
				'debug'		=> FALSE
			);
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
				$data['usuarios']	= $this->cms->get_usuarios( $this->session->userdata( 'nivel' ), $this->session->userdata( 'uid' ) );
				//print_r( count($data['usuarios']) );die;
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
			$data['usuario'] 	= $this->session->userdata('nombre');
			if ( $this->session->userdata('nivel') >= 1 && $this->session->userdata('nivel') <= 2 ){
				$data['trabajos']	= $this->cms->get_trabajos();
			}else {
				$data['trabajos']	= $this->cms->get_trabajos_editor( $this->session->userdata( 'uid' ) );
			}
			$this->load->view('cms/admin/trabajos',$data);
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
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['categorias']	= $this->cms->get_categorias();
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
				$data['usuario'] 	= $this->session->userdata('nombre');
				$data['verticales']	= $this->cms->get_verticales();
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
				$this->load->view( 'cms/admin/estructuras' );
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

	// public function agregar_campo_rss(){
	// 	$this->form_validation->set_rules('nuevo_campo_rss', 'Nombre del Campo', 'required|trim|min_length[3]|max_lenght[180]|xss_clean');
	// 	if ( $this->form_validation->run() === TRUE ){
	// 		$data['nuevo_campo']	= $this->input->post('nuevo_campo_rss');
	// 		$success = '<div class="form-group">
	// 						<label for="channel_description" class="col-sm-3 col-md-2 control-label">' . $data['nuevo_campo'] . '</label>
	// 						<div class="col-sm-9 col-md-10">
	// 							<input type="hidden" name="claves_rss[]" value="' . url_title( $data['nuevo_campo'], 'dash', TRUE ) . '">
 //        						<input type="text" class="form-control" name="valores_rss[]">
 //    						</div>
	// 					</div>';
	// 		echo json_encode( array('success' => $success ) );
	// 	} else {
	// 		//echo validation_errors('<span class="error">','</span>');
	// 		echo json_encode( array('errores' => validation_errors('<span class="error">','</span>') ) );
	// 	}
	// }

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

	// public function modal_agregar_campo_rss(){
	// 	$data['nuevo_campo_rss'] = '';
	// 	$this->load->view('cms/admin/modal_agregar_campo_rss', $data);
	// }


	/**
	 * [nuevo_usuario description]
	 * @return [type] [description]
	 */
	public function nuevo_usuario(){
		if ( $this->session->userdata('session') !== TRUE ){
			redirect('login');
		} else {
			$data['usuario'] 	= $this->session->userdata('nombre');
			$data['categorias'] = $this->cms->get_categorias();
			$data['verticales'] = $this->cms->get_verticales();
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
				$data['categorias'] 		= $this->cms->get_categorias();
				$data['verticales'] 		= $this->cms->get_verticales();
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
			$data['usuario'] 	= $this->session->userdata('nombre');
			$this->load->view('cms/admin/nueva_categoria',$data);
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
			$data['usuario'] 	= $this->session->userdata('nombre');
			$this->load->view( 'cms/admin/nueva_vertical', $data );
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
				$data['categorias'] = $this->cms->get_categorias();
				$data['verticales'] = $this->cms->get_verticales();
			} else {
				$data['categorias'] = $this->cms->get_categorias_usuario( $this->session->userdata( 'uid' ) );
				$data['verticales'] = $this->cms->get_verticales_usuario( $this->session->userdata( 'uid' ) );
			}
			$this->load->view( 'cms/admin/nuevo_trabajo', $data );
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

	function nombre_valido( $str ){
		if ( ! preg_match( '/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]/', $str ) ){
			$this->form_validation->set_message( 'nombre_valido','<b class="requerido">*</b> La información introducida en <b>%s</b> no es válida.' );
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function valid_phone( $str ) {
        if ( $str ) {
            if ( ! preg_match( '/\([0-9]\)| |[0-9]/', $str ) ){
                $this->form_validation->set_message( 'valid_phone', '<b class="requerido">*</b> El <b>%s</b> no tiene un formato válido.' );
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