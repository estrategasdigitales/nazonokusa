<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');

class Cron_model extends CI_Model {

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

    
        public function get_total_trabajos(){
            $total_trabajos = $this->db->count_all( $this->db->dbprefix( 'trabajos' ) );
            return $total_trabajos;
        }

        public function get_trabajos( $limit = '', $start = '' ){
            //$this->db->cache_on();
            $this->db->select( 'a.id_trabajo,a.uid_trabajo, a.uid_usuario, a.uid_categoria, a.uid_vertical, a.url_origen, b.slug_categoria, c.slug_vertical, a.nombre, a.slug_nombre_feed, a.activo, a.cron_config, a.fecha_registro, a.tipo_salida, d.formato_salida' );
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );

            $this->db->order_by("a.id_trabajo");

            if ( ! empty( $limit ) )
                $this->db->limit( $limit, $start );
            $result = $this->db->get();
            if ($result->num_rows() > 0) return $result->result();
            else return FALSE;
            $result->free_result();
        }

        public function get_trabajo_ejecutar( $uid ){
            //$this->db->cache_off();
            $this->db->select( 'a.uid_usuario, a.uid_trabajo, a.formato_origen, b.slug_categoria, c.slug_vertical,
                a.slug_nombre_feed, a.url_origen, a.campos_seleccionados, a.activo, 
                a.cron_config, a.tipo_salida, a.plantilla, d.json_estructura, d.formato_salida, d.encoding, d.cabeceras,d.variable' );
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );
            $this->db->where( 'uid_trabajo ', $uid );
            $result = $this->db->get();
            if ($result->num_rows() > 0) return $result->row();
            else return FALSE;
            $result->free_result();
        }

        public function active_job( $job ){
            $this->db->set( 'activo', $job['status'] );
            $this->db->where( 'uid_trabajo', $job['uidjob'] );
            $this->db->update( $this->db->dbprefix( 'trabajos' ) );
            //$this->db->cache_delete_all();
            if ( $this->db->affected_rows() > 0 ) return TRUE;
            else return FALSE;
        }

        public function delete_trabajo( $uid_trabajo ){
            $this->db->delete($this->db->dbprefix( 'trabajos_categorias' ), array( 'uid_trabajo' => $uid_trabajo ) );
            $this->db->delete($this->db->dbprefix( 'trabajos' ), array( 'uid_trabajo' => $uid_trabajo ) );
            //$this->db->cache_delete_all();
            return ( $this->db->affected_rows() > 0 );
        }

        public function get_cronjobs($status = 1){
            $this->db->select( 'a.uid_usuario, a.uid_trabajo, a.formato_origen, b.slug_categoria, c.slug_vertical,
                a.slug_nombre_feed, a.url_origen, a.campos_seleccionados, a.activo, 
                a.cron_config, a.tipo_salida, a.plantilla, d.json_estructura, d.formato_salida, d.encoding, d.cabeceras,d.variable' );
            $this->db->from( $this->db->dbprefix('trabajos') . ' AS a' );
            $this->db->join( $this->db->dbprefix('categorias'). ' AS b', 'a.uid_categoria = b.uid_categoria','INNER' );
            $this->db->join( $this->db->dbprefix('verticales'). ' AS c', 'a.uid_vertical = c.uid_vertical','INNER' );
            $this->db->join( $this->db->dbprefix('estructuras_salida'). ' AS d', 'a.plantilla = d.uid_estructura','LEFT' );
            $this->db->where( 'a.activo ', $status );
            $result = $this->db->get();
            if ($result->num_rows() > 0) return $result->result();
            else return FALSE;
            $result->free_result();
        }

}