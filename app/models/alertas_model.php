<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

require_once( BASEPATH . '../app/controllers/nucleo.php');

class Alertas_model extends Nucleo {

    /**
     * [__construct description]
     */
    function __construct() {
        parent::__construct();
    }

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
        
        $url_sms_service    =  $_SERVER['URL_SMS_SERVICE'];
        $user_sms           =  $_SERVER['USER_SMS_SERVICE'];
        $pass_sms           =  $_SERVER['PASS_SMS_SERVICE'];

        $user = $this->cms->get_usuario_alertas( $uid_trabajo );

        if ( isset( $id_mensaje ) && ! empty( $id_mensaje ) ) $message = $id_mensaje;
        else $message = "Falla al identificar error especifico";
        
        $this->email->from( 'desarrollo@estrategasdigitales.com', 'Sistema de Administración de Tareas y Contenidos para Middleware' );
        $this->email->to( $user->email );
        $this->email->subject( 'Error en trabajo de Middleware' );
        $this->email->message( 'Ha ocurrido el siguiente error: ' . $message );
        $this->email->send();

        //$url_sms = "http://kannel.onemexico.com.mx:8080/send_mt.php?msisdn=".$phone."&carrier=".$usr_carrier."&user=onemex&password=mex11&message=".$message;
        $url_sms = $url_sms_service . '?msisdn=52' . $user->celular . '&carrier=' . $user->carrier . '&user=' . $user_sms . '&password=' . $pass_sms . '&message=' . $message;
        
        $sms_reponse = $this->curl->simple_get( $url_sms );
        /*
        if($sms_reponse == 202)
            echo "Mensaje enviado correctamente";
        else
            echo $sms_reponse;
        */
    }
}

?>