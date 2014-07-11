<?php if (!defined('BASEPATH')) exit ('No tienes permiso para acceder a este archivo');
	header( 'Content-type:application/json' );

	if ( isset( $indices ) ){
		echo '{' . json_encode( $indices ) . '}';
	} else {
		echo json_encode( array( 'error' => true ) );
	}