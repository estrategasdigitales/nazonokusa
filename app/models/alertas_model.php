<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Alertas_model extends CI_Model {

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

    /*public function get_userdata( $uid_job ){
        /*
        SELECT a.uid_usuario, b.nombre, b.apellidos, AES_ENCRYPT('{$email}','{$this->key_encrypt}')b.email, b.celular, b.compania_celular, c.compania
        FROM mw_trabajos a, mw_usuarios b, mw_catalogo_compania_celular c
        WHERE a.uid_trabajo = '7e35bac6-18cc-11e4-bf05-7054d2e34de1'
        AND a.uid_usuario = b.uid_usuario
        AND b.compania_celular = c.id
        */
    /*    $this->db->select("a.uid_usuario, b.nombre, b.apellidosm, AES_DECRYPT('{$b.email}','{$this->key_encrypt}') email, AES_DECRYPT('{$b.celular}','{$this->key_encrypt}') celular, b.compania_celular, c.compania");
        $this->db->from($this->db->dbprefix('trabajos'). 'as a');
        $this->db->join($this->db->dbprefix('usuarios'). 'as b', 'a.uid_usuario = b.uid_usuario');
        $this->db->join($this->db->dbprefix('catalogo_compania_celular'). 'as c', 'b.compania_celular = c.id');
        $this->db->where('a.uid_trabajo = ', $uid_job );
        $data = $this->db->get();
        print_r($data);
        if ( $data->num_rows() > 0 ) return $data->result();
        else return FALSE;
        $data->free_result();
    }*/

    /**
     * [alerta description]
     * @return [type] [description]
     */
    public function alerta($uid_trabajo, $id_mensaje = ''){
        // Cadena para hacer las peticiones al servicio de SMS
        // Ejemplo: http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=525585320763&carrier=iusacell&user=onemex&password=mex11&message=Error prueba de mensajes    
        // 202 - Respuesta success
        // Catalogo de errores.

        //$phone = "525585320763";
        //$message = "Mensaje de error identificado";
        //$usr_carrier = "iusacell";
        //$uid_trabajo = '7e35bac6-18cc-11e4-bf05-7054d2e34de1';
        //$id_mensaje = 'Error - prueba - mensaje';
        
        $url_sms_service =  $_SERVER['URL_SMS_SERVICE'];
        $user_sms =         $_SERVER['USER_SMS_SERVICE'];
        $pass_sms =         $_SERVER['PASS_SMS_SERVICE'];

        $userData = $this->cms->get_userdata($uid_trabajo);

        if ( $id_mensaje && $id_mensaje != '' ) $message = $id_mensaje;
        else $message = "Falla al identificar error especifico";
        
        if ( is_array( $userData ) ){
            foreach ( $userData as $data ) {
                $usr_carrier = $data->compania;
                $phone = $data->celular;
                $userMail = $data->email;
            }
            
            $this->email->from('desarrollo@estrategasdigitales.com', 'Sistema de Administración de Tareas y Contenidos para Middleware');
            $this->email->to( $userMail );
            $this->email->subject('Error en trabajo de middleware');
            $this->email->message( 'Ha ocurrido el siguiente error: '.$message );
            $this->email->send();
        }

        //$url_sms = "http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=".$phone."&carrier=".$usr_carrier."&user=onemex&password=mex11&message=".$message;
        $url_sms = $url_sms_service ."?msisdn=".$phone."&carrier=".$usr_carrier."&user=".$user_sms."&password=".$pass_sms."&message=".$message;
        
        $sms_reponse = $this->curl->simple_get($url_sms);
        /*
        if($sms_reponse == 202)
            echo "Mensaje enviado correctamente";
        else
            echo $sms_reponse;
        */
    }
}

?>