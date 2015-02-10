<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

require_once( BASEPATH . '../app/controllers/nucleo.php');

class Alertas_model extends Nucleo {

    /**
     * [__construct description]
     */
    function __construct() {
        parent::__construct();
        $this->load->model( 'cronlog_model', 'cronlog' );
    }

    /**
     * [alerta description]
     * @return [type] [description]
     */
    public function alerta( $uid_trabajo, $id_mensaje = ''){
        // Cadena para hacer las peticiones al servicio de SMS
        // Ejemplo: http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=525585320763&carrier=iusacell&user=onemex&password=mex11&message=Error prueba de mensajes    
        // 202 - Respuesta success
        // Catalogo de errores.

        //$phone = "525585320763";
        //$message = "Mensaje de error identificado";
        //$usr_carrier = "iusacell";
        //$uid_trabajo = '7e35bac6-18cc-11e4-bf05-7054d2e34de1';
        //$id_mensaje = 'Error - prueba - mensaje';
        
        $url_sms_service    =  $_SERVER['URL_SMS_SERVICE'];
        $user_sms           =  $_SERVER['USER_SMS_SERVICE'];
        $pass_sms           =  $_SERVER['PASS_SMS_SERVICE'];

        $user = $this->cms->get_usuario_alertas( $uid_trabajo );

        if ( $user != FALSE ){
            if ( isset( $id_mensaje ) && ! empty( $id_mensaje ) ) $message = $id_mensaje;
            else $message = "Falla al identificar error especifico";

            $body['uid_job']        = $uid_trabajo;
            $body['name_job']       = $user->name_job;
            $body['name_category']  = $user->name_category;
            $body['name_vertical']  = $user->name_vertical;
            $body['time']           = date('Y/m/d H:i:s', time() );
            $body['message']        = $message;
            
            $this->email->from( 'desarrollo@estrategasdigitales.com', 'Sistema de Administración de Tareas y Contenidos para Middleware' );
            $this->email->to( $user->email );
            $this->email->subject( 'Error en trabajo ' . $uid_trabajo );
            $this->email->message( $this->load->view('cms/mail/codigo_recuperacion'), $body, TRUE );
            $this->email->send();

            //$url_sms = "http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=".$phone."&carrier=".$usr_carrier."&user=onemex&password=mex11&message=".$message;
            $url_sms = $url_sms_service . '?msisdn=52' . $user->celular . '&carrier=' . $user->carrier . '&user=' . $user_sms . '&password=' . $pass_sms . '&message=' . rawurlencode( $message );
            
            $sms_reponse = $this->curl->simple_get( $url_sms );
            if ( $sms_reponse != 202 )
                $this->cronlog->set_cronlog( $uid_trabajo, 'E404 - Falló el envío de alerta SMS');
            else
                return FALSE;
        }
    }
}

?>