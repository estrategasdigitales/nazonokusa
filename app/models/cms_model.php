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

    /**
     * [get_usuario description]
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function get_usuario( $usuario ){
        $this->db->select('uid_usuario, nombre, apellidos, extension, nivel, compania_celular');
        $this->db->select( "AES_DECRYPT( email,'{$this->key_encrypt}') AS email", FALSE );
        $this->db->select( "AES_DECRYPT( celular,'{$this->key_encrypt}') AS celular", FALSE );
        $this->db->where( 'email', "AES_ENCRYPT('{$usuario['usuario']}','{$this->key_encrypt}')", FALSE );
        $this->db->where( 'password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE );
        $result = $this->db->get( $this->db->dbprefix('usuarios') );
        if ($result->num_rows() === 1) return $result->row();
        else return FALSE;
        $result->free_result();
    }

    /**
     * [get_usuario_forgot description]
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function get_usuario_forgot( $usuario ){
        $this->db->select('uid_usuario');
        $this->db->where( 'email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
        $result = $this->db->get($this->db->dbprefix('usuarios'));
        if ($result->num_rows() === 1) return $result->row();
        else return FALSE;
        $result->free_result();
    }

    /**
     * [get_recupera_usuario description]
     * @param  [type] $recovery [description]
     * @return [type]           [description]
     */
    public function get_recupera_usuario( $recovery ){
        $this->db->select("AES_DECRYPT(password,'{$this->key_encrypt}') AS contrasena", FALSE);
        $this->db->where('uid_usuario', $recovery['uid'] );
        $this->db->where( 'email', "AES_ENCRYPT('{$recovery['email']}','{$this->key_encrypt}')", FALSE );
        $result = $this->db->get( $this->db->dbprefix('usuarios') );
        if( $result->num_rows() > 0 ) return $result->row();
        else return FALSE;
        $result->free_result();
    }

    /**
     * [get_usuarios description]
     * @param  [type] $nivel [description]
     * @param  [type] $uid   [description]
     * @return [type]        [description]
     */
    public function get_usuarios( $nivel, $uid ){
        $this->db->select('uid_usuario, nombre, apellidos, nivel');
        // $this->db->select( "AES_DECRYPT( apellidos,'{$this->key_encrypt}') AS apellidos", FALSE );
        if ($nivel == 1)
            $this->db->where( 'nivel >=', 1 );
        else
            $this->db->where( 'nivel >=', 2 );
        $this->db->where( 'uid_usuario !=', $uid );
        $result = $this->db->get( $this->db->dbprefix( 'usuarios' ) );
        if ( $result->num_rows() > 0 ) return $result->result();
        else return False;
        $result->free_result();
    }

    /**
     * [check_user description]
     * @param  [type] $email [description]
     * @param  string $uid   [description]
     * @return [type]        [description]
     */
    public function check_user( $email, $uid = '' ){
        $this->db->where('uid_usuario !=', $uid );
        $this->db->where('email', "AES_ENCRYPT('{$email}','{$this->key_encrypt}')", FALSE);
        $verifica = $this->db->count_all_results($this->db->dbprefix('usuarios'));
        if($verifica > 0) return TRUE;
        else return FALSE;
    }

    /**
     * [get_usuario_editar description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_usuario_editar( $uid ){
        $this->db->select('uid_usuario, nombre, apellidos, extension, compania_celular, nivel ');
        $this->db->select( "AES_DECRYPT( email,'{$this->key_encrypt}') AS email", FALSE );
        $this->db->select( "AES_DECRYPT( celular,'{$this->key_encrypt}') AS celular", FALSE );
        $this->db->select( "AES_DECRYPT( password,'{$this->key_encrypt}') AS password", FALSE );
        $this->db->where('uid_usuario', $uid);
        $result = $this->db->get($this->db->dbprefix( 'usuarios' ) );
        if ($result->num_rows() > 0) return $result->row();
        else return FALSE;
    }

    /**
     * [get_categorias_asignadas description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_categorias_asignadas( $uid ){
        $this->db->select( 'uid_categoria' );
        $this->db->where( 'uid_usuario', $uid );
        $result = $this->db->get($this->db->dbprefix( 'categorias_asignadas' ) );
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
        $result->free_result();
    }

    /**
     * [get_verticales_asignadas description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_verticales_asignadas( $uid ){
        $this->db->select( 'uid_vertical' );
        $this->db->where( 'uid_usuario', $uid );
        $result = $this->db->get($this->db->dbprefix( 'verticales_asignadas' ) );
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
        $result->free_result();
    }

    /**
     * [get_companias_celular description]
     * @return [type] [description]
     */
    public function get_companias_celular(){
        $this->db->select( 'id, compania' );
        $companias = $this->db->get($this->db->dbprefix( 'catalogo_compania_celular' ) );
        if ($companias->num_rows() > 0 ) return $companias->result();
        else return FALSE;
        $companias->free_result();

    }

    /**
     * [get_catalogo_roles description]
     * @return [type] [description]
     */
    public function get_catalogo_roles(){
        $this->db->select( 'id, nombre_rol' );
        $roles = $this->db->get($this->db->dbprefix( 'catalogo_rol_usuarios' ) );
        if ($roles->num_rows() > 0 ) return $roles->result();
        else return FALSE;
        $roles->free_result();
    }

    /**
     * [add_usuario description]
     * @param [type] $usuario [description]
     */
    public function add_usuario( $usuario ){
        $timestamp = time();
        $this->db->set( 'uid_usuario', "UUID()", FALSE);
        $this->db->set( 'nombre', $usuario['nombre'] );
        $this->db->set( 'apellidos', $usuario['apellidos'] );
        $this->db->set( 'email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')", FALSE );
        $this->db->set( 'extension', $usuario['extension']);
        $this->db->set( 'celular', "AES_ENCRYPT('{$usuario['celular']}','{$this->key_encrypt}')", FALSE );
        $this->db->set( 'compania_celular', $usuario['compania_celular'] );
        $this->db->set( 'nivel', $usuario['rol_usuario'] );
        $this->db->set( 'password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE );
        $this->db->set( 'fecha_registro',  gmt_to_local( $timestamp, $this->timezone, TRUE) );
        $this->db->insert($this->db->dbprefix( 'usuarios' ) );
        if ($this->db->affected_rows() > 0){            
            $this->db->select('uid_usuario');
            $this->db->where('email',"AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')",FALSE);
            $result = $this->db->get($this->db->dbprefix( 'usuarios' ) );
            if ($result->num_rows() > 0){
                $row = $result->row();
                $c_asign = $this->categorias_asignadas( $usuario['categorias'], $row->uid_usuario );
                $v_asign = $this->verticales_asigandas( $usuario['verticales'], $row->uid_usuario );
                if ($c_asign === TRUE && $v_asign === TRUE ) return TRUE;
                else return FALSE;
            } else {
                return FALSE;
            }           
        } else {
            return FALSE;
        }            
    }

    /**
     * [editar_usuario description]
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
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
        $c_asign = $this->categorias_asignadas( $usuario['categorias'], $usuario['uid_usuario'] );
        $v_asign = $this->verticales_asigandas( $usuario['verticales'], $usuario['uid_usuario'] );
        if ($c_asign === TRUE && $v_asign === TRUE ) return TRUE;
        else return FALSE;
    }

    /**
     * [update_perfil_usuario description]
     * @param  [type] $perfil [description]
     * @return [type]         [description]
     */
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
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [categorias_asignadas description]
     * @param  array  $categorias [description]
     * @param  string $uid        [description]
     * @return [type]             [description]
     */
    private function categorias_asignadas( $categorias = array(), $uid = '' ){
        $this->db->delete( $this->db->dbprefix( 'categorias_asignadas'), array( 'uid_usuario' => $uid) );
        $categorias = json_decode( $categorias );
        foreach ($categorias as $categoria ){
            $this->db->set('uid_usuario', $uid);
            $this->db->set('uid_categoria', $categoria);
            $this->db->insert($this->db->dbprefix( 'categorias_asignadas' ) );
        }
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [verticales_asigandas description]
     * @param  array  $verticales [description]
     * @param  string $uid        [description]
     * @return [type]             [description]
     */
    private function verticales_asigandas( $verticales = array(), $uid = '' ){
        $this->db->delete( $this->db->dbprefix( 'verticales_asignadas'), array( 'uid_usuario' => $uid) );
        $verticales = json_decode( $verticales );
        foreach ( $verticales as $vertical ){
            $this->db->set('uid_usuario', $uid);
            $this->db->set('uid_vertical', $vertical);
            $this->db->insert($this->db->dbprefix( 'verticales_asignadas' ) );
        }
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [delete_usuario description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function delete_usuario( $uid ){
        $this->db->delete( $this->db->dbprefix( 'categorias_asignadas' ), array( 'uid_usuario' => $uid ) );
        $this->db->delete( $this->db->dbprefix( 'verticales_asignadas' ), array( 'uid_usuario' => $uid ) );
        $this->db->delete( $this->db->dbprefix( 'usuarios' ), array( 'uid_usuario' => $uid ) );
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [delete_trabajo description]
     * @param  [type] $uid_trabajo [description]
     * @return [type]              [description]
     */
    public function delete_trabajo( $uid_trabajo ){
        $this->db->delete($this->db->dbprefix( 'trabajos_categorias' ), array( 'uid_trabajo' => $uid_trabajo ) );
        $this->db->delete($this->db->dbprefix( 'trabajos' ), array( 'uid_trabajo' => $uid_trabajo ) );
        return ( $this->db->affected_rows() > 0 );
    }

    /**
     * [get_trabajos description]
     * @return [type] [description]
     */
    public function get_trabajos(){
        $this->db->select( 'uid_trabajo, nombre, url_origen, activo, uid_usuario' );
        $result = $this->db->get( $this->db->dbprefix( 'trabajos' ) );
        if ($result->num_rows() > 0) return $result->result();
        else return FALSE;
    }

    /**
     * [get_trabajos_editor description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_trabajos_editor( $uid ){
        $this->db->select('uid_trabajo, nombre, url_origen, activo, uid_usuario');
        $this->db->where( 'uid_usuario',$uid );
        $result = $this->db->get($this->db->dbprefix( 'trabajos' ) );
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
    }

    /**
     * [active_job description]
     * @param  [type] $job [description]
     * @return [type]      [description]
     */
    public function active_job( $job ){
        $this->db->set( 'activo', $job['status'] );
        $this->db->where('uid_trabajo', $job['uidjob'] );
        $this->db->update($this->db->dbprefix( 'trabajos' ) );
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [config_cronjob description]
     * @param  [type] $uid_trabajo [description]
     * @return [type]              [description]
     */
    public function config_cronjob($uid_trabajo){
        $this->db->select('cron_config',False);
        $this->db->where('uid_trabajo',$uid_trabajo);
        $result = $this->db->get('trabajos');
        if($result->num_rows() > 0){
            return $result->row();
        }else{
            return False;
        }
    }

    /**
     * [call description]
     * @param  [type] $method [description]
     * @param  array  $data   [description]
     * @return [type]         [description]
     */
    private function call($method, $data = array()){
        $uri = 'https://www.easycron.com/rest/'; //API
        $arguments = array();
        foreach ($data as $name => $value) {
            $arguments[] = $name . '=' . urlencode($value);
        }
        $temp = implode('&', $arguments);

        $url = $uri . $method . '?' . $temp;
        $result = file_get_contents($url);

        if ($result) {
            return json_decode($result, true);   
        } else {
            return $result;
        }
    }

    /**
     * [save_cronconfig description]
     * @param  [type] $uid_trabajo [description]
     * @param  [type] $config      [description]
     * @return [type]              [description]
     */
    public function save_cronconfig($uid_trabajo, $config){
        $this->db->set('cron_config', $config);
        $this->db->where('uid_trabajo',$uid_trabajo);
        $this->db->update('trabajos');
        return ($this->db->affected_rows() > 0);
    }

    /**
     * [set_cronjob description]
     * @param [type]  $name       [description]
     * @param [type]  $expression [description]
     * @param [type]  $url        [description]
     * @param integer $email_me   [description]
     * @param integer $output     [description]
     * @param string  $token      [description]
     * @param integer $test       [description]
     */
    public function set_cronjob($name, $expression, $url, $email_me = 0, $output = 0, $token = "bfb06b51988cf4f017606be4c28c89d1", $test = 0){ 
        $data['token'] = $token;
        $data['cron_job_name'] = $name;
        $data['cron_expression'] = $expression ;
        $data['url'] = $url ;
        $data['email_me'] = $email_me;
        $data['log_output_length'] = $output ;
        $data['testfirst'] = $test ;
        return $this->call("add", $data); 
    }

    /**
     * [delete_cronjob description]
     * @param  [type] $id    [description]
     * @param  string $token [description]
     * @return [type]        [description]
     */
    public function delete_cronjob($id, $token = "bfb06b51988cf4f017606be4c28c89d1"){ 
        $data['token'] = $token;
        $data['id'] = $id;
        return $this->call("delete", $data); 
     }

    /**
     * [edit_cronjob description]
     * @return [type] [description]
     */
    public function edit_cronjob(){ return TRUE; }

    /**
     * [get_trabajo_editar description]
     * @param  [type] $uid_trabajo [description]
     * @return [type]              [description]
     */
    public function get_trabajo_editar( $uid_trabajo ){
        $this->db->select('uid_trabajo, id_trabajo, nombre, url_origen, fecha_registro, fecha_ejecucion, formato_salida, uid_usuario, cron_config');
        $this->db->where('uid_trabajo',$uid_trabajo);
        $result = $this->db->get($this->db->dbprefix( 'trabajos' ) );
        if ( $result->num_rows() > 0 ) return $result->row();
        else return FALSE;
    }

    /**
     * [add_trabajo description]
     * @param [type] $trabajo [description]
     */
    public function add_trabajo( $trabajo ){
        $timestamp = time();
        $this->db->set('uid_trabajo', "UUID()", FALSE);
        $this->db->set('nombre', $trabajo['nombre']);
        $this->db->set('url_origen', $trabajo['url-origen']);
        // $this->db->set('url_local',  $trabajo['destino-local']);
        // $this->db->set('url_storage', $trabajo['destino-net']);
        $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->set('fecha_ejecucion', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->set('formato_salida', $trabajo['formato_salida']);
        $this->db->set('cron_config', $trabajo['cron_date']);
        $this->db->set('uid_usuario', $trabajo['usuario']);
        $this->db->insert($this->db->dbprefix( 'trabajos' ) );
        if ( $this->db->affected_rows() > 0 ){            
            $this->db->select('uid_trabajo');
            $this->db->where('nombre',$trabajo['nombre']);
            $this->db->where('url_origen', $trabajo['url-origen']);
            // $this->db->where('url_local', $trabajo['destino-local']);
            // $this->db->where('url_storage', $trabajo['destino-net']);
            $this->db->where('formato_salida', $trabajo['formato_salida']);
            $this->db->where('uid_usuario', $trabajo['usuario']);
            $result = $this->db->get('trabajos');
            if ( $result->num_rows() > 0 ){
                $row = $result->row();
                $result->free_result();
                $this->db->set('uid_trabajo', $row->uid_trabajo);
                $this->db->set('uid_categoria', $trabajo['categoria']);
                $this->db->set('uid_vertical', $trabajo['vertical']);
                $this->db->insert($this->db->dbprefix( 'trabajos_categorias' ) ); 
                return $row->uid_trabajo;
            } else {
                return FALSE;
            }           
        } else {
            return FALSE; 
        }            
    }

    /**
     * [update_trabajo description]
     * @param  [type] $trabajo [description]
     * @return [type]          [description]
     */
    public function update_trabajo($trabajo){
        $timestamp = time();
        $this->db->set('nombre', $trabajo['nombre']);
        $this->db->set('url_origen', $trabajo['url-origen']);
        // $this->db->set('url_local',  $trabajo['destino-local']);
        // $this->db->set('url_storage', $trabajo['destino-net']);
        // $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->set('fecha_ejecucion', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->set('formato_salida', $trabajo['formato_salida']);
        $this->db->set('cron_config', $trabajo['cron_date']);
        $this->db->where('uid_trabajo', $trabajo['uid_trabajo']);
        $this->db->update('trabajos');
        if ($this->db->affected_rows() > 0){            
            $this->db->select('uid_trabajo');
            $this->db->where('nombre',$trabajo['nombre']);
            $this->db->where('url_origen', $trabajo['url-origen']);
            // $this->db->where('url_local', $trabajo['destino-local']);
            // $this->db->where('url_storage', $trabajo['destino-net']);
            $this->db->where('formato_salida', $trabajo['formato_salida']);
            $this->db->where('uid_usuario', $trabajo['usuario']);
            $result = $this->db->get('trabajos');
            if ($result->num_rows() > 0){
                $row = $result->row();
                $result->free_result();
                $this->db->set('uid_categoria', $trabajo['categoria']);
                $this->db->set('uid_vertical', $trabajosjo['vertical']);
                $this->db->where('uid_trabajo', $row->uid_trabajo);
                $this->db->update('trabajos_categorias'); 
                return $row->uid_trabajo;
                                
            } else {
                return FALSE;
            }           
        } else {
            return FALSE; 
        }
    }

    /**
     * [get_categorias description]
     * @return [type] [description]
     */
    public function get_categorias(){
        $this->db->select( 'uid_categoria, nombre, path_storage, fecha_registro' );
        $result = $this->db->get($this->db->dbprefix( 'categorias' ) );
        if ($result->num_rows() > 0){
            return $result->result();
        } else {
            return FALSE;
        }
    }

    /**
     * [get_categorias_usuario description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_categorias_usuario( $uid ){
        $this->db->distinct( 'c.uid_categoria' );
        $this->db->select( 'c.nombre' );
        $this->db->from( $this->db->dbprefix( 'categorias' ) . ' AS c' );
        $this->db->join( $this->db->dbprefix( 'categorias_asignadas' ) . ' AS ca', 'ca.uid_categoria = c.uid_categoria', 'INNER' );
        $this->db->where( 'ca.uid_usuario', $uid );
        $result = $this->db->get();
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
    }

    /**
     * [add_categoria description]
     * @param [type] $categoria [description]
     */
    public function add_categoria( $categoria ){
        $timestamp = time();
        $this->db->set('uid_categoria', "UUID()", FALSE);
        $this->db->set('nombre', $categoria['nombre']);
        $this->db->set('slug_categoria', $categoria['slug_categoria']);
        $this->db->set('path_storage', $categoria['path']);
        $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->insert( $this->db->dbprefix( 'categorias') );
        if ($this->db->affected_rows() > 0) return TRUE;
        else return FALSE; 
    }

    /**
     * [delete_categoria description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function delete_categoria( $uid ){
        $this->db->delete( $this->db->dbprefix( 'categorias_asignadas' ), array('uid_categoria' => $uid));
        $this->db->delete( 'categorias', array( 'uid_categoria' => $uid ) );
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /**
     * [get_verticales description]
     * @return [type] [description]
     */
    public function get_verticales(){
        $this->db->select('uid_vertical, nombre, path_storage, fecha_registro');
        $result = $this->db->get($this->db->dbprefix( 'verticales' ) );
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
    }

    /**
     * [get_verticales_usuario description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_verticales_usuario( $uid ){
        $this->db->distinct( 'v.uid_vertical' );
        $this->db->select( 'v.nombre' );
        $this->db->from( $this->db->dbprefix( 'verticales' ) . ' AS v' );
        $this->db->join( $this->db->dbprefix( 'verticales_asignadas' ) . '  AS va', 'va.uid_vertical = v.uid_vertical', 'INNER' );
        $this->db->where( 'va.uid_usuario', $uid );
        $result = $this->db->get();
        if ( $result->num_rows() > 0 ) return $result->result();
        else return FALSE;
    }

    /**
     * [add_vertical description]
     * @param [type] $vertical [description]
     */
    public function add_vertical( $vertical ){
        $timestamp = time();
        $this->db->set('uid_vertical', "UUID()", FALSE);
        $this->db->set('nombre', $vertical['nombre']);
        $this->db->set('slug_vertical', $vertical['slug_vertical']);
        $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->insert( $this->db->dbprefix( 'verticales' ) );
        if ($this->db->affected_rows() > 0) return TRUE;
        else return FALSE;
    }

    /**
     * [delete_vertical description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function delete_vertical( $uid ){
        $this->db->delete( $this->db->dbprefix( 'verticales_asignadas' ), array( 'uid_vertical' => $uid ) );
        $this->db->delete( 'verticales', array( 'uid_vertical' => $uid ) );
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE; 
    }
}