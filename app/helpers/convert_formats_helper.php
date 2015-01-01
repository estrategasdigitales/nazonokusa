<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');




    function jsonp_decode($jsonp, $assoc = false) { // PHP 5.3 adds depth as third parameter to json_decode
        if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        return json_decode(trim($jsonp,'();'), $assoc);
    }

    /**
	 * Helper para convertir las salidas a los formatos especificados
	 */

	if ( ! function_exists('formatos_output_seleccionados') ){
		function formatos_output_seleccionados( $formatos, $jsonp_funcion, $valores_rss, $claves_rss ){
			foreach ( $formatos as $formato ){
				switch ( $formato ){
					case 'json':
						$fseleccionados[] = array('format' => $formato, 'function' => '', 'attributes' => '' );
						break;
					case 'jsonp':
						$fseleccionados[] = array('format' => $formato, 'function' => $jsonp_funcion, 'attributes' => '' );
						break;
					case 'xml':
						$fseleccionados[] = array('format' => $formato, 'function' => '', 'attributes' => '' );
						break;
					case 'rss':
						$fseleccionados[] = array('format' => $formato, 'function' => '', 'attributes' => array_combine( $claves_rss, $valores_rss ) );
						break;
				}
			}
			return json_encode( $fseleccionados );
		}
	}

	// if ( ! function_exists('array_to_xml') ){
	// 	function array_to_xml( $data ){
	// 		$CI =& get_instance();
	// 		$CI->load->library('Array_2_xml');
	// 		$xml = $CI->array_2_xml->createXML( 'root', $data );
	// 		$xml->formatOutput = true;
	// 		return $xml;
	// 	}
	// }

	// if ( ! function_exists('array_to_rss') ){
	// 	function array_to_rss( $rss_values, $data ){
	// 		$CI =& get_instance();
	// 		$CI->load->library('Array_2_xml');
	// 		$xml = new SimpleXMLElement('<rss/>');
	// 		$xml->addAttribute('version','2.0');
	// 		$channel = $xml->addChild('channel');
	// 		$channel->addChild('title', $rss_values[0]);
	// 		$channel->addChild('link', $rss_values[1]);
	// 		$channel->addChild('description', $rss_values[2]);
	// 		$items = $CI->array_2_xml->createXML('item', $data );
	// 		$item = new SimpleXMLElement( $items->saveXML() );
	// 		xml_adopt( $channel, $item );
	// 		// $item = $xml->addChild('item');
	// 		// recursive_nodes_items( $item, $data );
	// 		return $xml;
	// 	}
	// }

	// if ( ! function_exists('xml_adopt') ){
	// 	function xml_adopt($root, $new, $namespace = null) {
	// 		// first add the new node
	// 		$node = $root->addChild($new->getName(), (string) $new, $namespace);
	// 		// add any attributes for the new node
	// 		foreach($new->attributes() as $attr => $value) {
	// 			$node->addAttribute($attr, $value);
	// 		}
	// 		// get all namespaces, include a blank one
	// 		$namespaces = array_merge(array(null), $new->getNameSpaces(true));
	// 		// add any child nodes, including optional namespace
	// 		foreach($namespaces as $space) {
	// 			foreach ($new->children($space) as $child) {
	// 				xml_adopt($node, $child, $space);
	// 			}
	// 		}
	// 	}
	// }

/* End of file convert_formats_helper.php */
/* Location: ./app/helpers/convert_formats_helper.php */