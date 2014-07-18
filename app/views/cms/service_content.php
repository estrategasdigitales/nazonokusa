<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');
	header( 'Content-type:application/json' );

	if ( isset( $contenido_feed ) ){
		echo json_encode( $contenido_feed );
	} else {
		echo json_encode( array( 'error' => true ) );
	}