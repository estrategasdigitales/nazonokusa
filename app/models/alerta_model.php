<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Alerta_model extends CI_Model {

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

    public function get_userdata($uid_job)
    {

        /*
        SELECT a.uid_usuario, b.nombre, b.apellidos, AES_ENCRYPT('{$email}','{$this->key_encrypt}')b.email, b.celular, b.compania_celular, c.compania
        FROM mw_trabajos a, mw_usuarios b, mw_catalogo_compania_celular c
        WHERE a.uid_trabajo = '7e35bac6-18cc-11e4-bf05-7054d2e34de1'
        AND a.uid_usuario = b.uid_usuario
        AND b.compania_celular = c.id
        */
        $this->db->select("a.uid_usuario, b.nombre, b.apellidosm, AES_DECRYPT('{$b.email}','{$this->key_encrypt}') email, AES_DECRYPT('{$b.celular}','{$this->key_encrypt}') celular, b.compania_celular, c.compania");
        $this->db->from($this->db->dbprefix('trabajos'). 'as a');
        $this->db->join($this->db->dbprefix('usuarios'). 'as b', 'a.uid_usuario = b.uid_usuario');
        $this->db->join($this->db->dbprefix('catalogo_compania_celular'). 'as c', 'b.compania_celular = c.id');
        $this->db->where('a.uid_trabajo = ', $uid_job );
        $data = $this->db->get();
        print_r($data);
        if ( $data->num_rows() > 0 ) return $data->result();
        else return FALSE;
        $data->free_result();
    }
}

?>