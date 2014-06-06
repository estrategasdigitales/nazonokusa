<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cms_model extends CI_Model {

    private $key_encrypt;
    private $api_token;
    private $timezone;

    function __construct() {

        parent::__construct();
        $this->load->database();
        $this->key_encrypt  = $_SERVER['HASH_ENCRYPT'];
        $this->timezone     = 'UM6';

        //Api Hash 
        $this->api_token = "40d22dba2081ef5b9da5a0ee13e3089d"; 

    }

    /**
     * [get_usuario description]
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function get_usuario( $usuario ){
        $this->db->select('uid_usuario, nombre, apellidos, nivel');
        // $this->db->select( "AES_DECRYPT( apellidos,'{$this->key_encrypt}') AS apellidos", FALSE );
        $this->db->select( "AES_DECRYPT( email,'{$this->key_encrypt}') AS email", FALSE );
        $this->db->where( 'email', "AES_ENCRYPT('{$usuario['usuario']}','{$this->key_encrypt}')", FALSE );
        $this->db->where( 'password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE );
        $result = $this->db->get($this->db->dbprefix('usuarios'));
        if ($result->num_rows() === 1) return $result->row();
        else return FALSE;
    }

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

    public function get_usuario_editar($uid){

        $this->db->select("uid_usuario,nombre,apellidos,AES_DECRYPT(email,'$this->key_encrypt') as email,extension,AES_DECRYPT(celular,'$this->key_encrypt') as celular, AES_DECRYPT(password,'$this->key_encrypt') as password",False);
        $this->db->where('uid_usuario',$uid);
        $result = $this->db->get('usuarios');
        if ($result->num_rows() > 0)
        {
            return $result->row();
        }
        else
        {
            return False;
        }

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
                $result->free_result();
                foreach ($usuario['categorias'] as $key => $value){                    
                    foreach ($usuario['verticales'] as $key2 => $value2) {
                        $this->db->set('uid_usuario', $row->uid_usuario);
                        $this->db->set('uid_categoria', $value);
                        $this->db->set('uid_vertical', $value2);
                        $this->db->insert(  $this->db->dbprefix( 'usuarios_categorias_verticales' ) ); 
                    }
                }
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

    public function delete_usuario( $uid ){
        $this->db->delete($this->db->dbprefix( 'usuarios_categorias_verticales' ), array( 'uid_usuario' => $uid ) );
        $this->db->delete($this->db->dbprefix( 'usuarios' ), array( 'uid_usuario' => $uid ) );
        if ( $this->db->affected_rows() > 0 ) return TRUE;
        else return FALSE;
    }

    /* 29/05 */    
    public function delete_trabajo( $uid_trabajo ){
        $this->db->delete($this->db->dbprefix( 'trabajos_categorias' ), array( 'uid_trabajo' => $uid_trabajo ) );
        $this->db->delete($this->db->dbprefix( 'trabajos' ), array( 'uid_trabajo' => $uid_trabajo ) );
        return ( $this->db->affected_rows() > 0 );
    }

    public function get_trabajos(){
        $this->db->select('uid_trabajo,nombre,url_origen,url_storage,fecha_ejecucion,uid_usuario');
        $result = $this->db->get('trabajos');
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }
    }

    public function get_trabajos_editor( $uid ){
        $this->db->select('uid_trabajo,nombre,url_origen,url_storage,fecha_ejecucion,uid_usuario');
        $this->db->where('uid_usuario',$uid);
        $result = $this->db->get('trabajos');
        if ( $result->num_rows() > 0 ) return $result->result_array();
        else return FALSE;
    }


    /* 30/5 -- Posteriormente mover cron/works a modelo para ello*/
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

    public function save_cronconfig($uid_trabajo, $config){
        $this->db->set('cron_config', $config);
        $this->db->where('uid_trabajo',$uid_trabajo);
        $this->db->update('trabajos');
        return ($this->db->affected_rows() > 0);
    }

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

    public function delete_cronjob($id, $token = "bfb06b51988cf4f017606be4c28c89d1"){ 
        $data['token'] = $token;
        $data['id'] = $id;
        return $this->call("delete", $data); 
     }

    //public function status_cronjob($id, $status=1){ return True;  }
    public function edit_cronjob(){ return TRUE; }

    /* 28/5 */ 
    public function get_trabajo_editar($uid_trabajo){
        $this->db->select('uid_trabajo,id_trabajo,nombre,url_origen,url_local,url_storage,fecha_registro,fecha_ejecucion,formato_salida,uid_usuario,cron_config',False);
        $this->db->where('uid_trabajo',$uid_trabajo);
        $result = $this->db->get('trabajos');
        if($result->num_rows() > 0){
            return $result->result_array();
        }else{
            return False;
        }
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
        $this->db->select( 'uid_categoria,nombre,fecha_registro' );
        $result = $this->db->get($this->db->dbprefix( 'categorias' ) );
        if ($result->num_rows() > 0){
            return $result->result_array();
        } else {
            return FALSE;
        }
    }

    public function get_categorias_usuario($uid){

        $this->db->select('distinct mw_categorias.uid_categoria,categorias.nombre',false);
        $this->db->from('categorias');
        $this->db->join('usuarios_categorias_verticales', 'usuarios_categorias_verticales.uid_categoria = categorias.uid_categoria');
        $this->db->where('usuarios_categorias_verticales.uid_usuario',$uid);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function add_categoria( $categoria ){
        $timestamp = time();
        $this->db->set('uid_categoria', "UUID()", FALSE);
        $this->db->set('nombre', $categoria['nombre']);
        $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->insert('categorias');
        if ($this->db->affected_rows() > 0) return TRUE;
        else return FALSE; 
    }

    public function delete_categoria( $uid ){
        $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uid_categoria' => $uid));
        $this->db->set_dbprefix('mw_');
        $this->db->delete('categorias', array('uid_categoria' => $uid));
        if ($this->db->affected_rows() > 0) return TRUE;
        else return FALSE;
    }

    public function get_verticales(){
        $this->db->select('uid_vertical,nombre,fecha_registro');
        $result = $this->db->get('verticales');
        if ( $result->num_rows() > 0 ) return $result->result_array();
        else return FALSE;
    }

    public function get_verticales_usuario($uid){

        $this->db->select('distinct mw_verticales.uid_vertical,verticales.nombre',false);
        $this->db->from('verticales');
        $this->db->join('usuarios_categorias_verticales', 'usuarios_categorias_verticales.uid_vertical = verticales.uid_vertical');
        $this->db->where('usuarios_categorias_verticales.uid_usuario',$uid);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function add_vertical($vertical){
        $timestamp = time();
        $this->db->set('uid_vertical', "UUID()", False);
        $this->db->set('nombre', $vertical['nombre']);
        $this->db->set('fecha_registro', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->insert('verticales');
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }

    public function delete_vertical($uid){

        $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uid_vertical' => $uid));
        $this->db->set_dbprefix('mw_');
        $this->db->delete('verticales', array('uid_vertical' => $uid));
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }


}