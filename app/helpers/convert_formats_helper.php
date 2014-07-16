<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * Helper para convertir las salidas a los formatos especificados
	 */
	
	if ( ! function_exists('convert_formats') ){
		function convert_formats( $output, $formatos, $jsonp_funcion, $valores_rss, $claves_rss ){
			foreach ( $formatos as $formato ){
				switch ( $formato ) {
					case 'json':
						$salida[] = array( 'formato' => $formato, 'output' => $output, 'extension' => '-json.js' );
						break;
					case 'jsonp':
						$salida[] = array( 'formato' => 'jsonp', 'output' => $jsonp_funcion . "(" . $output . ")", 'extension' => '-jsonp.js' );
						break;
					case 'xml':
						$array = json_decode( $output, TRUE );
						$salida[] = array( 'formato' => 'xml', 'output' => array_to_xml( $array ), 'extension' => '-xml.xml' );
						break;
					case 'rss':
						# code...
						break;
				}
			}
			return json_encode( $salida );
		}
	}

	if ( ! function_exists('array_to_xml') ){
		function array_to_xml( array $array, $xml = FALSE ){
			if ( $xml === FALSE ){
				$xml = new SimpleXMLElement('<root/>');
			}
			foreach ( $array as $key => $value ){
				is_array( $value )
				 	? array_to_xml($value, $xml->addChild( $key ) )
				 	: $xml->addChild( $key, $value );
			}
			return $xml->asXML();
		}
	}

/* End of file convert_formats_helper.php */
/* Location: ./app/helpers/convert_formats_helper.php */