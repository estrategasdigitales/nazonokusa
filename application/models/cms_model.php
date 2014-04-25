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


}