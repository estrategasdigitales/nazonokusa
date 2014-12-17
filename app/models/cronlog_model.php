<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cronlog_model extends CI_Model {

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

    public function set_cronlog( $uid = '', $status = '', $description = '' ){
        $timestamp = time();
        $this->db->set( 'uid_trabajo', $uid );
        $this->db->set( 'time', gmt_to_local( $timestamp, $this->timezone, TRUE ) );
        $this->db->set( 'status', $status );
        $this->db->set( 'description', $description );
        $this->db->insert( $this->db->dbprefix( 'cron_log') );
    }
}