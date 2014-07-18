<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * Helper para convertir las salidas a los formatos especificados
	 */
	
	if ( ! function_exists('conversion_feed_output') ){
		function conversion_feed_output( $formatos, $output, $jsonp_funcion, $valores_rss, $claves_rss, $storage, $usuario, $categoria, $vertical, $nombre ){
			foreach ( $formatos as $formato ){
				switch ( $formato ) {
					case 'json':
						$salida[] = array( 'formato' => $formato, 'output' => $output, 'url' => $storage.'/'.$categoria.'/'.$vertical.'/'.$usuario.'/'.$nombre.'-json.js' );
						break;
					case 'jsonp':
						$salida[] = array( 'formato' => $formato, 'output' => $jsonp_funcion . "(" . $output . ")", 'url' => $storage.'/'.$categoria.'/'.$vertical.'/'.$usuario.'/'.$nombre.'-jsonp.js' );
						break;
					case 'xml':
						$array = json_decode( $output, TRUE );
						print_r( array_to_xml( $array ) );die;
						$salida[] = array( 'formato' => $formato, 'output' => '', 'url' => '' );
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
		function array_to_xml( $data ){
			$CI =& get_instance();
			$CI->load->library('MY_Xml_writer');
			$xml = new MY_Xml_writer();
			$xml->setRootName('root');
			$xml->initiate();
			// foreach( $data as $key => $value ) {
		 //        if( is_array( $value ) ) {
		 //            $xml->startElement( $key );
		 //            array_to_xml( $xml, $value );
		 //            $xml->endElement( );
		 //            continue;
		 //        }
		 //        $xml->writeElement( $key, $value );
		 //    }
			$xml->endBranch();
			$xml->getXML(true);
			return $xml;
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