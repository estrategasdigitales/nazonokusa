<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * Helper para convertir las salidas a los formatos especificados
	 */
	
	if ( ! function_exists('conversion_feed_output') ){
		function conversion_feed_output( $formatos, $output, $jsonp_funcion, $valores_rss, $claves_rss, $storage, $usuario, $categoria, $vertical, $nombre ){
			foreach ( $formatos as $formato ){
				switch ( $formato ) {
					case 'json':
						print_r( $output );die;
						$salida[] = array( 'formato' => $formato, 'output' => $output, 'url' => $storage.'/'.$categoria.'/'.$vertical.'/'.$usuario.'/'.$nombre.'-json.js' );
						break;
					case 'jsonp':
						$salida[] = array( 'formato' => $formato, 'output' => $jsonp_funcion . "(" . $output . ")", 'url' => $storage.'/'.$categoria.'/'.$vertical.'/'.$usuario.'/'.$nombre.'-jsonp.js' );
						break;
					case 'xml':
						print_r( $output );die;
						$array = json_decode( $output, TRUE );
						print_r( $array );die;
						$salida[] = array( 'formato' => $formato, 'output' => array_to_xml( $array ), 'url' => $storage.'/'.$categoria.'/'.$vertical.'/'.$usuario.'/'.$nombre.'-xml.xml' );
						break;
					case 'rss':
						//$array = json_decode( $output, TRUE );
						$salida[] = array( 'formato' => $formato, 'output' => '', 'url' => '#' );
						break;
				}
			}
			return json_encode( $salida );
		}
	}

	if ( ! function_exists('formatos_output_seleccionados') ){
		function formatos_output_seleccionados( $formatos, $jsonp_funcion, $valores_rss, $claves_rss ){
			foreach ( $formatos as $formato ){
				switch ( $formato ){
					case 'json':
						$fseleccionados[] = array('formato' => $formato, 'funcion' => '', 'claves_rss' => '', 'valores_rss' => '' );
						break;
					case 'jsonp':
						$fseleccionados[] = array('formato' => $formato, 'funcion' => $jsonp_funcion, 'claves_rss' => '', 'valores_rss' => '' );
						break;
					case 'xml':
						$fseleccionados[] = array('formato' => $formato, 'funcion' => '', 'claves_rss' => '', 'valores_rss' => '' );
						break;
					case 'rss':
						$fseleccionados[] = array('formato' => $formato, 'funcion' => '', 'claves_rss' => $claves_rss, 'valores_rss' => $valores_rss );
						break;
				}
			}
			return json_encode( $fseleccionados );
		}
	}

	if ( ! function_exists('array_to_xml') ){
		function array_to_xml( array $array, $xml = FALSE ){
			print_r( $array );die;
			if ( $xml === FALSE ){
				$xml = new SimpleXMLElement('<root/>');
			}
			foreach ( $array as $key => $value ){
				is_array( $value )
				 	? array_to_xml($value, $xml->addChild( $key ) )
				 	: $xml->addChild( $key, $value );
			}
			print_r( $xml->asXML );die;
			return $xml->asXML();
		}
	}

	// if ( ! function_exists('array_to_rss') ){
	// 	function array_to_rss( array $array, $xml = FALSE ){
	// 		if ( $xml === FALSE ){
	// 			$xml = new SimpleXMLElement('<root/>');
	// 		}
	// 		foreach ( $array as $key => $value ){
	// 			is_array( $value )
	// 			 	? array_to_xml($value, $xml->addChild( $key ) )
	// 			 	: $xml->addChild( $key, $value );
	// 		}
	// 		return $xml->asXML();
	// 	}
	// }

/* End of file convert_formats_helper.php */
/* Location: ./app/helpers/convert_formats_helper.php */