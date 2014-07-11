<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * create_tree()
 * Genera el árbol con los campos del feed con la estructura padre, hijo, nieto, etc...
 */
if ( ! function_exists('create_tree') ){
	function create_tree( $contents ){
		$tree = '<div class="col-sm-3 col-md-3">';
		foreach ( $contents as $content ){
			$tree .= recursive_nodes( $content );
		}
		$tree .= '</div>';
		return $tree;
	}
}

if ( ! function_exists('recursive_nodes') ){
	function recursive_nodes( $matriz, $parent = '' ){
		$tree_node = '';
		foreach ( $matriz as $key => $value ){
			if( is_array( $value ) ){
				$tree_node .= '<div class="checkbox" id="' . $key . '">';
				$tree_node .= '<label><input onchange="desplega(this);" type="checkbox" name="claves[]" value="' . $key . '">' . $key . '</label>';
				$tree_node .= recursive_nodes( $value, $key );
				$tree_node .= '</div>';
			}else{
				$tree_node .= '<div class="checkbox">';
				$tree_node .= '<label><input type="checkbox" name="claves[]" value="';
				if ( ! empty( $parent) ) $tree_node .= $parent . '.';
				$tree_node .= $key . '">' . $key . '</label>';
				$tree_node .= '</div>';
			}
		}
		return $tree_node;
	}
}

if ( ! function_exists('create_indexes') ){
	function create_indexes( $contents ){
		foreach ( $contents as $content ){
			$tree[] = recursive_nodes_index( $content );
		}
		return $tree;
	}
}

if ( ! function_exists('recursive_nodes_index') ){
	function recursive_nodes_index( $matriz ){
		$tree_node = [];
		$object = new stdClass();
		foreach ( $matriz as $key => $value ){
			if( is_array( $value ) ){
				$tree_node[] = $object->$key = $key;
				$tree_node[] = $object->$key = recursive_nodes_index( $value );
			}else{
				$tree_node[] = $object->$key = $key;
			}
		}
		return $object;
	}
}

if ( ! function_exists('campos_seleccionados') ){
	function campos_seleccionados( $seleccionados, $entradas ){
		$seleccionados = json_decode( $seleccionados );
		foreach ( $seleccionados as $sel ){
			$elegidos[] = explode('.', $sel );
		}
		print_r( $elegidos );die;
		$entradas = json_decode( base64_decode( $entradas ) );
		print_r( array_intersect_key( $elegidos, (array)$entradas) );
	}
}

if ( ! function_exists('pull_dom_json')) {
	/**
	 * Convierte una cadena en un json, con los padres de origen e hijos en nodos secundarios
	 * 
	 * @example
	 * 	[
	 * 		{"name": "id", "type": "folder"},
	 * 		{"name": "name", "type": "folder"}
	 * 		{"name": "program", "type":"folder"}
	 * 		{"name": "id", "type":"folder", "parent": "program"}
	 * 		{"name": "subid", "type":"item", "parent": "id"} 
	 * 	]
	 * 	// El padre más próximo
	 * @param  [type] $json_string [description]
	 * @return [type]              [description]
	 */
	function pull_dom_json($json_string = '[
	  		{"name": "id", "type": "folder"},
	  		{"name": "name", "type": "folder"},
	  		{"name": "program", "type":"folder"},
	  		{"name": "id", "type":"folder", "parent": "program"},
	  		{"name": "subid", "type":"item", "parent": "id"} 
	  	]') {
		// Apilamos todos los objetos, cuando se trata de un folder entonces configuramos todos los
		// elementos internos a este, esto ocurre hasta que se encuentra otro folder al nivel actual
		return json_decode($json_string);
	}
}

/* End of file tree_helper.php */
/* Location: ./app/helpers/tree_helper.php */
