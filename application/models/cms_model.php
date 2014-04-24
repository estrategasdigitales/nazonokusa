<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cms_model extends CI_Model {

    private $key_encrypt;

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->key_encrypt = $_SERVER['HASH_ENCRYPT'];
    }

    public function validar_usuario($usuario){
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

}