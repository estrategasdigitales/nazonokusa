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
        $this->db->where('uid_usuario !=', $uid );
        $this->db->where('email', "AES_ENCRYPT('{$email}','{$this->key_encrypt}')", FALSE);
        $verifica = $this->db->count_all_results($this->db->dbprefix('usuarios'));
        if($verifica > 0) return TRUE;
        else return FALSE;
        $verifica->free_result();
    }
}

?>