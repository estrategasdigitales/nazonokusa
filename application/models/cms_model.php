<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cms_model extends CI_Model {

    private $key_encrypt;

    function __construct() {

        parent::__construct();
        $this->load->database();
        $this->key_encrypt = $_SERVER['HASH_ENCRYPT'];

    }

    public function get_usuario($usuario){

        $this->db->select('uuid_usuario, nombre, nivel');
        $this->db->where('nombre', $usuario['usuario']);
        $this->db->where('password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')", FALSE);
        $result = $this->db->get($this->db->dbprefix('usuarios'));
        if ($result->num_rows() === 1)
        {
            return $result->row();
        }
        else
        {
            return False;
        }

    }

    public function get_usuarios(){

        $this->db->select('uuid_usuario,nombre,nivel');
        $this->db->where('nivel','2');
        $result = $this->db->get('usuarios');
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function get_usuario_editar($uuid){

        $this->db->select("uuid_usuario,nombre,apellidos,AES_DECRYPT(email,'$this->key_encrypt') as email,extension,AES_DECRYPT(celular,'$this->key_encrypt') as celular, AES_DECRYPT(password,'$this->key_encrypt') as password",False);
        $this->db->where('uuid_usuario',$uuid);
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

    public function get_ver_cat($uuid){

        $this->db->select('uuid_usuario,uuid_categoria,uuid_vertical');
        $this->db->where('uuid_usuario',$uuid);
        $result = $this->db->get($this->db->dbprefix('usuarios_categorias_verticales'));
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function add_usuario($usuario){

        $this->db->set('uuid_usuario', "UUID()", False);
        $this->db->set('nombre', $usuario['nombre']);
        $this->db->set('apellidos', $usuario['apellidos']);
        $this->db->set('email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')",false);
        $this->db->set('extension', $usuario['extension']);
        $this->db->set('celular', "AES_ENCRYPT('{$usuario['celular']}','{$this->key_encrypt}')",false);
        $this->db->set('password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')",false);
        $this->db->set('fecha_registro', "UNIX_TIMESTAMP(NOW())", False);
        $this->db->set('nivel', '2');
        $this->db->insert('usuarios');
        if ($this->db->affected_rows() > 0)
        {            
            $this->db->select('uuid_usuario');
            $this->db->where('nombre',$usuario['nombre']);
            $this->db->where('email',"AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')",false);
            $this->db->where('password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')",false);
            $result = $this->db->get('usuarios');
            if ($result->num_rows() > 0)
            {
                $row = $result->row();
                $result->free_result();
                foreach ($usuario['categorias'] as $key => $value) {                    
                    foreach ($usuario['verticales'] as $key2 => $value2) {
                        $this->db->set('uuid_usuario', $row->uuid_usuario);
                        $this->db->set('uuid_categoria', $value);
                        $this->db->set('uuid_vertical', $value2);
                        $this->db->insert($this->db->dbprefix('usuarios_categorias_verticales')); 
                    }
                }
                
            }else
            {
                return False;
            }           
        }
        else
        {
            return False; 
        }            

    }

    public function editar_usuario($usuario){

        $this->db->set('nombre', $usuario['nombre']);
        $this->db->set('apellidos', $usuario['apellidos']);
        $this->db->set('email', "AES_ENCRYPT('{$usuario['email']}','{$this->key_encrypt}')",false);
        $this->db->set('extension', $usuario['extension']);
        $this->db->set('celular', "AES_ENCRYPT('{$usuario['celular']}','{$this->key_encrypt}')",false);
        $this->db->set('password', "AES_ENCRYPT('{$usuario['password']}','{$this->key_encrypt}')",false);
        $this->db->set('fecha_registro', "UNIX_TIMESTAMP(NOW())", False);
        $this->db->where('uuid_usuario',$usuario['uuid_usuario']);
        $this->db->update('usuarios');
        if ($this->db->affected_rows() > 0)
        {
            $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uuid_usuario' => $usuario['uuid_usuario']));
            foreach ($usuario['categorias'] as $key => $value) {                    
                foreach ($usuario['verticales'] as $key2 => $value2) {
                    $this->db->set('uuid_usuario', $usuario['uuid_usuario']);
                    $this->db->set('uuid_categoria', $value);
                    $this->db->set('uuid_vertical', $value2);
                    $this->db->insert($this->db->dbprefix('usuarios_categorias_verticales')); 
                }
            }
            
        }
        else
        {
            return False; 
        }            

    }

    public function delete_usuario($uuid){
        
        $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uuid_usuario' => $uuid));
        $this->db->set_dbprefix('mw_');
        $this->db->delete('usuarios', array('uuid_usuario' => $uuid));
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }

    public function get_trabajos(){

        $this->db->select('uuid_trabajo,nombre,url_origen,url_storage,fecha_ejecucion,uuid_usuario');
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

    public function get_categorias(){

        $this->db->select('uuid_categoria,nombre,fecha_registro');
        $result = $this->db->get('categorias');
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function get_categorias_usuario($uuid){

        $this->db->select('distinct mw_categorias.uuid_categoria,categorias.nombre',false);
        $this->db->from('categorias');
        $this->db->join('usuarios_categorias_verticales', 'usuarios_categorias_verticales.uuid_categoria = categorias.uuid_categoria');
        $this->db->where('usuarios_categorias_verticales.uuid_usuario',$uuid);
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

    public function add_categoria($categoria){

        $this->db->set('uuid_categoria', "UUID()", False);
        $this->db->set('nombre', $categoria['nombre']);
        $this->db->set('fecha_registro', "UNIX_TIMESTAMP(NOW())", False);
        $this->db->insert('categorias');
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }

    public function delete_categoria($uuid){

        $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uuid_categoria' => $uuid));
        $this->db->set_dbprefix('mw_');
        $this->db->delete('categorias', array('uuid_categoria' => $uuid));
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }

    public function get_verticales(){

        $this->db->select('uuid_vertical,nombre,fecha_registro');
        $result = $this->db->get('verticales');
        if ($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return False;
        }

    }

    public function get_verticales_usuario($uuid){

        $this->db->select('distinct mw_verticales.uuid_vertical,verticales.nombre',false);
        $this->db->from('verticales');
        $this->db->join('usuarios_categorias_verticales', 'usuarios_categorias_verticales.uuid_vertical = verticales.uuid_vertical');
        $this->db->where('usuarios_categorias_verticales.uuid_usuario',$uuid);
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

        $this->db->set('uuid_vertical', "UUID()", False);
        $this->db->set('nombre', $vertical['nombre']);
        $this->db->set('fecha_registro', "UNIX_TIMESTAMP(NOW())", False);
        $this->db->insert('verticales');
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }

    public function delete_vertical($uuid){

        $this->db->delete($this->db->dbprefix('usuarios_categorias_verticales'), array('uuid_vertical' => $uuid));
        $this->db->set_dbprefix('mw_');
        $this->db->delete('verticales', array('uuid_vertical' => $uuid));
        if ($this->db->affected_rows() > 0)
            return True;
        else
            return False; 

    }


}