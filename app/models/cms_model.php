<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cms_model extends CI_Model {

    private $key_encrypt;
    private $timezone;

    /**
     * [__construct description]
     */
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->key_encrypt  = $_SERVER['HASH_ENCRYPT'];
        $this->timezone     = 'UM6';
    }

    /** CONSULTAS **/

        public function check_user( $email, $uid = '' ){
            //$this->db->cache_off();
            $this->db->where('uid_usuario !=', $uid );
            $this->db->where('email', "AES_ENCRYPT('{$email}','{$this->key_encrypt}')", FALSE);
            $verifica = $this->db->count_all_results($this->db->dbprefix('usuarios'));
            if($verifica > 0) return TRUE;
            else return FALSE;
            $verifica->free_result();
        }

        public function get_all_estructuras( $limit = '', $start = '' ){
            $this->db->select('uid_estructura, nombre, formato_salida, fecha_registro, activo');
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $this->db->order_by('nombre', 'ASC');
            $result = $this->db->get($this->db->dbprefix( 'estructuras_salida' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_categorias( $order, $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select( 'uid_categoria, nombre, slug_categoria, fecha_registro' );
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $this->db->order_by( $order, 'DESC' );
            $result = $this->db->get($this->db->dbprefix( 'categorias' ) );
            if ($result->num_rows() > 0) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_categorias_asignadas( $uid ){
            //$this->db->cache_off();
            $this->db->select( 'uid_categoria' );
            $this->db->where( 'uid_usuario', $uid );
            $result = $this->db->get($this->db->dbprefix( 'categorias_asignadas' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_catalogo_roles(){
            //$this->db->cache_on();
            $this->db->select( 'id, nombre_rol' );
            $roles = $this->db->get($this->db->dbprefix( 'catalogo_rol_usuarios' ) );
            if ($roles->num_rows() > 0 ) return $roles->result();
            else return FALSE;
            $roles->free_result();
        }

        public function get_categorias_usuario( $uid ){
            //$this->db->cache_on();
            $this->db->distinct( 'c.uid_categoria' );
            $this->db->select( 'c.nombre, c.uid_categoria' );
            $this->db->from( $this->db->dbprefix( 'categorias' ) . ' AS c' );
            $this->db->join( $this->db->dbprefix( 'categorias_asignadas' ) . ' AS ca', 'ca.uid_categoria = c.uid_categoria', 'INNER' );
            $this->db->where( 'ca.uid_usuario', $uid );
            $result = $this->db->get();
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_companias_celular(){
            //$this->db->cache_on();
            $this->db->select( 'id, compania' );
            $companias = $this->db->get($this->db->dbprefix( 'catalogo_compania_celular' ) );
            if ($companias->num_rows() > 0 ) return $companias->result();
            else return FALSE;
            $companias->free_result();
        }

        public function get_estructuras(){
            $this->db->select('uid_estructura, nombre');
            $this->db->where('activo', 1);
            $this->db->order_by('nombre', 'ASC');
            $result = $this->db->get($this->db->dbprefix( 'estructuras_salida' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_recupera_usuario( $recovery ){
            //$this->db->cache_off();
            $this->db->select("AES_DECRYPT(password,'{$this->key_encrypt}') AS contrasena", FALSE);
            $this->db->where('uid_usuario', $recovery['uid'] );
            $this->db->where( 'email', "AES_ENCRYPT('{$recovery['email']}','{$this->key_encrypt}')", FALSE );
            $result = $this->db->get( $this->db->dbprefix('usuarios') );
            if( $result->num_rows() > 0 ) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_reportes( $limit = '', $start = '' ){
            $this->db->select('uid_reporte, nombre_reporte, fecha, fecha_inicio, fecha_fin');
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $this->db->order_by('fecha', 'ASC');
            $result = $this->db->get( $this->db->dbprefix( 'reportes' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_reporte_detalle( $uid ){
            $this->db->select('uid_reporte, slug_nombre_reporte, fecha_inicio, fecha_fin');
            $this->db->where('uid_reporte', $uid );
            $result = $this->db->get( $this->db->dbprefix( 'reportes' ) );
            if ( $result->num_rows() > 0 ) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_reportes_editor( $uid, $limit = '', $start = '' ){
            $this->db->select('uid_reporte, nombre_reporte, fecha, fecha_inicio, fecha_fin');
            $this->db->where('uid_usuario', $uid );
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $this->db->order_by('fecha', 'ASC');
            $result = $this->db->get( $this->db->dbprefix( 'reportes' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_reporte_resultado( $reporte ){
            $this->db->select('uid_trabajo, status');
            $this->db->select("FROM_UNIXTIME(time) AS time", FALSE);
            $this->db->where( 'time >= ', $reporte->fecha_inicio );
            $this->db->where( 'time <= ', $reporte->fecha_fin );
            $resultado = $this->db->get( $this->db->dbprefix('cron_log') );
            return $resultado;
            $resultado->free_result();
        }

        public function get_template_feed( $salida ){
            $this->db->select( 'json_estructura' );
            $this->db->where('uid_estructura', $salida['id'] );
            $template = $this->db->get( $this->db->dbprefix('estructuras_salida') );
            return $template->row();
            $template->free_result();
        }

        public function get_total_categorias(){
            $total_categorias = $this->db->count_all( $this->db->dbprefix( 'categorias' ) );
            return $total_categorias;
        }

        public function get_total_estructuras(){
            $total_estructuras = $this->db->count_all( $this->db->dbprefix( 'estructuras_salida' ) );
            return $total_estructuras;
        }

        public function get_total_trabajos(){
            $total_trabajos = $this->db->count_all( $this->db->dbprefix( 'trabajos' ) );
            return $total_trabajos;
        }

        public function get_total_trabajos_editor( $uid ){
            $this->db->where( 'uid_usuario', $uid );
            $total_trabajos = $this->db->count_all( $this->db->dbprefix( 'trabajos' ) );
            return $total_trabajos;
        }

        public function get_total_usuarios( $nivel, $uid ){
            if ($nivel == 1)
                $this->db->where( 'nivel >=', 1 );
            else
                $this->db->where( 'nivel >=', 2 );
            $this->db->where( 'uid_usuario !=', $uid );
            $total_usuarios = $this->db->count_all( $this->db->dbprefix( 'usuarios' ) );
            return $total_usuarios;
        }

        public function get_total_verticales(){
            $total_verticales = $this->db->count_all( $this->db->dbprefix( 'verticales' ) );
            return $total_verticales;
        }

        public function get_total_reportes(){
            $total_reportes = $this->db->count_all( $this->db->dbprefix( 'reportes' ) );
            return $total_reportes;
        }

        public function get_total_reportes_editor( $uid ){
            $this->db->where( 'uid_usuario', $uid );
            $total_reportes = $this->db->count_all( $this->db->dbprefix( 'reportes' ) );
            return $total_reportes;
        }

        public function get_trabajos( $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select( 'a.uid_trabajo, a.uid_usuario, a.uid_categoria, a.uid_vertical, a.url_origen, b.slug_categoria, c.slug_vertical, a.nombre, a.slug_nombre_feed, a.activo, a.cron_config, a.fecha_registro, a.tipo_salida, d.formato_salida' );
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $result = $this->db->get();
            if ($result->num_rows() > 0) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_trabajo_editar( $uid_trabajo ){
            //$this->db->cache_on();
            $this->db->select('uid_trabajo, id_trabajo, nombre, url_origen, fecha_registro, uid_usuario, cron_config');
            $this->db->where('uid_trabajo',$uid_trabajo);
            $result = $this->db->get($this->db->dbprefix( 'trabajos' ) );
            if ( $result->num_rows() > 0 ) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_trabajos_editor( $uid, $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select('a.uid_trabajo, a.uid_categoria, a.uid_vertical, a.url_origen, b.slug_categoria, c.slug_vertical, a.nombre, a.url_origen, a.activo, a.uid_usuario, a.fecha_registro, a.tipo_salida, d.formato_salida');
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );
            $this->db->where( 'a.uid_usuario',$uid );
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $result = $this->db->get();
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_trabajo_ejecutar( $uid ){
            //$this->db->cache_off();
            $this->db->select( 'a.uid_usuario, a.uid_trabajo, b.slug_categoria, c.slug_vertical, 
                a.slug_nombre_feed, a.url_origen, a.campos_seleccionados, a.activo, 
                a.cron_config, a.tipo_salida, a.plantilla, d.json_estructura, d.formato_salida, d.encoding, d.cabeceras' );
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );
            $this->db->where( 'uid_trabajo ', $uid );
            $result = $this->db->get();
            if ($result->num_rows() > 0) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_trabajos_formatos( $uid ){
            $this->db->select( 'formato' );
            $this->db->where( 'uid_trabajo', $uid );
            $formatos = $this->db->get( $this->db->dbprefix( 'trabajos_formatos' ) );
            if ( $formatos->num_rows() > 0 ) return $formatos->result();
            else return NULL;
            $formatos->free_result();
        }

        public function get_usuario( $usuario ){
            //$this->db->cache_off();
            $this->db->select('uid_usuario, nombre, apellidos, extension, nivel, compania_celular');
            $this->db->select( "AES_DECRYPT( email,'{$this->key_encrypt}') AS email", FALSE );
            $this->db->select( "AES_DECRYPT( celular,'{$this->key_encrypt}') AS celular", FALSE );
            $this->db->where( 'email', "AES_ENCRYPT('{$this->db->escape_str( $usuario['usuario'] )}','{$this->key_encrypt}')", FALSE );
            $this->db->where( 'password', "AES_ENCRYPT('{$this->db->escape_str( $usuario['password'] )}','{$this->key_encrypt}')", FALSE );
            $result = $this->db->get( $this->db->dbprefix('usuarios') );
            if ($result->num_rows() === 1) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_usuarios( $nivel, $uid, $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select('uid_usuario, nombre, apellidos, nivel');
            if ($nivel == 1)
                $this->db->where( 'nivel >=', 1 );
            else
                $this->db->where( 'nivel >=', 2 );
            $this->db->where( 'uid_usuario !=', $uid );
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $result = $this->db->get( $this->db->dbprefix( 'usuarios' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_usuario_alertas( $uid_job ){
            $this->db->select( 'a.uid_trabajo, b.nombre, b.apellidos, b.compania_celular, c.slug_compania as carrier' );
            $this->db->select( "AES_DECRYPT( b.email,'{$this->key_encrypt}') email", FALSE );
            $this->db->select( "AES_DECRYPT( b.celular,'{$this->key_encrypt}') celular", FALSE );
            $this->db->from( $this->db->dbprefix('trabajos').' AS a');
            $this->db->join( $this->db->dbprefix('usuarios').' AS b', 'a.uid_usuario = b.uid_usuario');
            $this->db->join( $this->db->dbprefix('catalogo_compania_celular'). ' AS c', 'b.compania_celular = c.id');
            $this->db->where( 'a.uid_trabajo = ', $uid_job );
            $this->db->where( 'a.activo = ', 1 );
            $data = $this->db->get();
            if ( $data && $data->num_rows() > 0 ) return $data->row();
            else return FALSE;
            $data->free_result();
        }

        public function get_usuario_editar( $uid ){
            //$this->db->cache_off();
            $this->db->select('uid_usuario, nombre, apellidos, extension, compania_celular, nivel ');
            $this->db->select( "AES_DECRYPT( email,'{$this->key_encrypt}') AS email", FALSE );
            $this->db->select( "AES_DECRYPT( celular,'{$this->key_encrypt}') AS celular", FALSE );
            $this->db->select( "AES_DECRYPT( password,'{$this->key_encrypt}') AS password", FALSE );
            $this->db->where('uid_usuario', $uid);
            $result = $this->db->get($this->db->dbprefix( 'usuarios' ) );
            if ($result->num_rows() > 0) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_usuario_forgot( $usuario ){
            //$this->db->cache_off();
            $this->db->select('uid_usuario');
            $this->db->where( 'email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
            $result = $this->db->get($this->db->dbprefix('usuarios'));
            if ($result->num_rows() === 1) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function get_verticales( $order, $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select('uid_vertical, nombre, slug_vertical, fecha_registro');
            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $this->db->order_by($order, 'DESC');
            $result = $this->db->get($this->db->dbprefix( 'verticales' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_verticales_asignadas( $uid ){
            //$this->db->cache_off();
            $this->db->select( 'uid_vertical' );
            $this->db->where( 'uid_usuario', $uid );
            $result = $this->db->get($this->db->dbprefix( 'verticales_asignadas' ) );
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_verticales_usuario( $uid ){
            //$this->db->cache_on();
            $this->db->distinct( 'v.uid_vertical' );
            $this->db->select( 'v.nombre, v.uid_vertical' );
            $this->db->from( $this->db->dbprefix( 'verticales' ) . ' AS v' );
            $this->db->join( $this->db->dbprefix( 'verticales_asignadas' ) . '  AS va', 'va.uid_vertical = v.uid_vertical', 'INNER' );
            $this->db->where( 'va.uid_usuario', $uid );
            $result = $this->db->get();
            if ( $result->num_rows() > 0 ) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function validar_categoria( $slug ){
            //$this->db->cache_off();
            $this->db->where('slug_categoria', $slug );
            $verifica = $this->db->count_all_results( $this->db->dbprefix( 'categorias' ) );
            if( $verifica > 0 ) return TRUE;
            else return FALSE;
        }

        public function validar_vertical( $slug ){
            //$this->db->cache_off();
            $this->db->where('slug_vertical', $slug );
            $verifica = $this->db->count_all_results( $this->db->dbprefix( 'verticales' ) );
            if( $verifica > 0 ) return TRUE;
            else return FALSE;
        }
    /** TERMINAN CONSULTAS **/

    /** INSERCIONES **/

        public function add_categoria( $categoria ){
            $timestamp = time();
            $this->db->set('uid_categoria', "UUID()", FALSE);
            $this->db->set('nombre', $categoria['nombre']);
            $this->db->set('slug_categoria', $categoria['slug_categoria']);
            $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
            $this->db->insert( $this->db->dbprefix( 'categorias') );
            //$this->db->cache_delete_all();
            if ($this->db->affected_rows() > 0) return TRUE;
            else return FALSE; 
        }

        public function add_estructura( $trabajo ){
            $timestamp = time();
            $this->db->set( 'uid_estructura', "UUID()", FALSE );
            $this->db->set( 'uid_usuario', $trabajo['usuario'] );
            $this->db->set( 'nombre', $trabajo['nombre'] );
            $this->db->set( 'slug_nombre_feed', $trabajo['slug_nombre_feed'] );
            $this->db->set( 'url_origen', $trabajo['url-origen'] );
            $this->db->set( 'formato_salida', $trabajo['formato_salida'] );
            $this->db->set( 'json_estructura', $trabajo['json_estructura'] );
            $this->db->set( 'encoding', base64_encode( $trabajo['encoding'] ) );
            $this->db->set( 'cabeceras', $trabajo['headers'] );
            $this->db->set( 'fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
            $this->db->insert( $this->db->dbprefix( 'estructuras_salida' ) );
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;       
        }

        public function add_reporte( $reporte ){
            $timestamp = time();
            $this->db->set('uid_reporte', "UUID()", FALSE);
            $this->db->set('uid_usuario', $reporte['uid_usuario']);
            $this->db->set('nombre_reporte', $reporte['nombre_reporte']);
            $this->db->set('slug_nombre_reporte', $reporte['slug_nombre_reporte']);;
            $this->db->set('fecha', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
            $this->db->set('fecha_inicio', $reporte['fecha_inicio'] );
            $this->db->set('fecha_fin', $reporte['fecha_fin'] );
            $this->db->set('trabajos', $reporte['trabajos'] );
            $this->db->insert( $this->db->dbprefix( 'reportes' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;         
        }

        public function add_trabajo( $trabajo ){
            $timestamp = time();
            $this->db->set( 'uid_trabajo', "UUID()", FALSE );
            $this->db->set( 'uid_usuario', $trabajo['usuario'] );
            $this->db->set( 'uid_categoria', $trabajo['categoria'] );
            $this->db->set( 'uid_vertical', $trabajo['vertical'] );
            $this->db->set( 'nombre', $trabajo['nombre'] );
            $this->db->set( 'slug_nombre_feed', $trabajo['slug_nombre_feed'] );
            $this->db->set( 'url_origen', $trabajo['url-origen'] );
            $this->db->set( 'tipo_salida', $trabajo['tipo_salida'] );
            if ( $trabajo['tipo_salida'] == 2 )
                $this->db->set( 'plantilla', $trabajo['uid_plantilla'] );
            $this->db->set( 'campos_seleccionados', $trabajo['campos_seleccionados'] );
            $this->db->set( 'fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
            $this->db->set( 'cron_config', $trabajo['cron_config'] );
            $this->db->insert( $this->db->dbprefix( 'trabajos' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ){
                if ( $trabajo['tipo_salida'] == 1 ){
                    $this->db->select( 'uid_trabajo' );
                    $this->db->where( 'id_trabajo', $this->db->insert_id() );
                    $result = $this->db->get( $this->db->dbprefix( 'trabajos' ) );
                    if ( $result->num_rows() > 0 ){
                        $row = $result->row();
                        $formatos = $this->trabajos_formatos( $trabajo['formatos'], $row->uid_trabajo );
                        if ( $formatos == TRUE ) return TRUE;
                        else return FALSE;
                    } else {
                        return FALSE;
                    }
                    $result->free_result();
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function add_usuario( $usuario ){
            $timestamp = time();
            $this->db->set( 'uid_usuario', "UUID()", FALSE );
            $this->db->set( 'nombre', $usuario['nombre'] );
            $this->db->set( 'apellidos', $usuario['apellidos'] );
            $this->db->set( 'email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'extension', $usuario['extension'] );
            $this->db->set( 'celular', "AES_ENCRYPT('{$usuario['celular']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'compania_celular', $usuario['compania_celular'] );
            $this->db->set( 'nivel', $usuario['rol_usuario'] );
            $this->db->set( 'password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'fecha_registro',  gmt_to_local( $timestamp, $this->timezone, TRUE) );
            $this->db->insert($this->db->dbprefix( 'usuarios' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ){
                //$this->db->cache_off();
                $this->db->select( 'uid_usuario' );
                $this->db->where( 'email',"AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
                $result = $this->db->get( $this->db->dbprefix( 'usuarios' ) );
                if ( $result->num_rows() > 0 ){
                    $row = $result->row();
                    $c_asign = $this->categorias_asignadas( $usuario['categorias'], $row->uid_usuario );
                    $v_asign = $this->verticales_asigandas( $usuario['verticales'], $row->uid_usuario );
                    if ($c_asign === TRUE && $v_asign === TRUE ) return TRUE;
                    else return FALSE;
                } else {
                    return FALSE;
                }
                $result->free_result();
            } else {
                return FALSE;
            }            
        }

        public function add_vertical( $vertical ){
            $timestamp = time();
            $this->db->set('uid_vertical', "UUID()", FALSE);
            $this->db->set('nombre', $vertical['nombre']);
            $this->db->set('slug_vertical', $vertical['slug_vertical']);
            $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
            $this->db->insert( $this->db->dbprefix( 'verticales' ) );
            //$this->db->cache_delete_all();
            if ($this->db->affected_rows() > 0) return TRUE;
            else return FALSE;
        }

        private function categorias_asignadas( $categorias = array(), $uid = '' ){
            $this->db->delete( $this->db->dbprefix( 'categorias_asignadas'), array( 'uid_usuario' => $uid) );
            $categorias = json_decode( $categorias );
            foreach ($categorias as $categoria ){
                $this->db->set('uid_usuario', $uid);
                $this->db->set('uid_categoria', $categoria);
                $this->db->insert($this->db->dbprefix( 'categorias_asignadas' ) );
            }
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        private function verticales_asigandas( $verticales = array(), $uid = '' ){
            $this->db->delete( $this->db->dbprefix( 'verticales_asignadas'), array( 'uid_usuario' => $uid) );
            $verticales = json_decode( $verticales );
            foreach ( $verticales as $vertical ){
                $this->db->set('uid_usuario', $uid);
                $this->db->set('uid_vertical', $vertical);
                $this->db->insert($this->db->dbprefix( 'verticales_asignadas' ) );
            }
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        private function trabajos_formatos( $formatos = array(), $uid = '' ){
            $this->db->delete( $this->db->dbprefix( 'trabajos_formatos' ), array( 'uid_trabajo' => $uid ) );
            $formatos = json_decode( $formatos );
            foreach ( $formatos as $formato ){
                $this->db->set( 'uid_trabajo', $uid );
                $this->db->set( 'formato', json_encode( $formato ) );
                $this->db->insert( $this->db->dbprefix( 'trabajos_formatos' ) );
            }
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }
    /** TERMINAN INSERCIONES **/

    /** ACTUALIZACIONES **/

        public function active_job( $job ){
            $this->db->set( 'activo', $job['status'] );
            $this->db->where( 'uid_trabajo', $job['uidjob'] );
            $this->db->update( $this->db->dbprefix( 'trabajos' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        public function editar_usuario( $usuario ){
            $this->db->set( 'nombre', $usuario['nombre'] );
            $this->db->set( 'apellidos', $usuario['apellidos']);
            $this->db->set( 'email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'extension', $usuario['extension'] );
            $this->db->set( 'celular', "AES_ENCRYPT('{$usuario['celular']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'compania_celular', $usuario['compania_celular'] );
            $this->db->set( 'nivel', $usuario['rol_usuario'] );
            $this->db->set( 'password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE );
            $this->db->where('uid_usuario', $usuario['uid_usuario'] );
            $this->db->update($this->db->dbprefix( 'usuarios' ) );
            //$this->db->cache_delete_all();
            $c_asign = $this->categorias_asignadas( $usuario['categorias'], $usuario['uid_usuario'] );
            $v_asign = $this->verticales_asigandas( $usuario['verticales'], $usuario['uid_usuario'] );
            if ($c_asign === TRUE && $v_asign === TRUE ) return TRUE;
            else return FALSE;
        }

        public function update_perfil_usuario( $perfil ){
            $perfil['password_actual'] = base64_decode( $perfil['password_actual'] );
            $this->db->set( 'nombre', $perfil['nombre'] );
            $this->db->set( 'apellidos', $perfil['apellidos']);
            $this->db->set( 'email', "AES_ENCRYPT('{$perfil['email']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'extension', $perfil['extension'] );
            $this->db->set( 'celular', "AES_ENCRYPT('{$perfil['celular']}','{$this->key_encrypt}')", FALSE );
            $this->db->set( 'compania_celular', $perfil['compania_celular'] );
            if ( isset( $perfil['password'] ) ){
                $this->db->set( 'password', "AES_ENCRYPT('{$perfil['password']}','{$this->key_encrypt}')", FALSE );
            }
            $this->db->where('uid_usuario', $perfil['uid'] );
            $this->db->where('password', "AES_ENCRYPT('{$perfil['password_actual']}','{$this->key_encrypt}')", FALSE );
            $this->db->update($this->db->dbprefix( 'usuarios' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }
    /** TERMINAN ACTUALIZACIONES **/

    /** ELIMINACION **/

        public function delete_categoria( $uid ){
            $this->db->delete( $this->db->dbprefix( 'categorias_asignadas' ), array('uid_categoria' => $uid));
            $this->db->delete( 'categorias', array( 'uid_categoria' => $uid ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        public function delete_estructura( $uid ){
            $this->db->delete( 'estructuras_salida', array( 'uid_estructura' => $uid ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        public function delete_trabajo( $uid_trabajo ){
            $this->db->delete($this->db->dbprefix( 'trabajos_categorias' ), array( 'uid_trabajo' => $uid_trabajo ) );
            $this->db->delete($this->db->dbprefix( 'trabajos' ), array( 'uid_trabajo' => $uid_trabajo ) );
            //$this->db->cache_delete_all();
            return ( $this->db->affected_rows() > 0 );
        }

        public function delete_usuario( $uid ){
            $this->db->delete( $this->db->dbprefix( 'categorias_asignadas' ), array( 'uid_usuario' => $uid ) );
            $this->db->delete( $this->db->dbprefix( 'verticales_asignadas' ), array( 'uid_usuario' => $uid ) );
            $this->db->delete( $this->db->dbprefix( 'usuarios' ), array( 'uid_usuario' => $uid ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        public function delete_vertical( $uid ){
            $this->db->delete( $this->db->dbprefix( 'verticales_asignadas' ), array( 'uid_vertical' => $uid ) );
            $this->db->delete( 'verticales', array( 'uid_vertical' => $uid ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE; 
        }       
    /** TERMINA ELIMINACION **/    
}