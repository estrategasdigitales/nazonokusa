<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * create_tree()
 * Genera el árbol con los campos del feed con la estructura padre, hijo, nieto, etc...
 */

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
		$entradas = json_decode( base64_decode( $entradas ) );
		print_r( array_intersect_key( $elegidos, (array)$entradas) );
	}
}

if ( ! function_exists('create_indexes_specific') ){
	function create_indexes_specific( $contents ){
		foreach ( $contents as $content ){
			$tree[] = recursive_nodes_index_specific( $content );
		}
		return $tree;
	}
}

if ( ! function_exists('recursive_nodes_index_specific') ){
	function recursive_nodes_index_specific( $matriz ){
		$tree_node = [];
		$object = new stdClass();
		foreach ( $matriz as $key => $value ){
			if( is_array( $value ) ){
				$tree_node[] = $object->$key = null;
				$tree_node[] = $object->$key = recursive_nodes_index_specific( $value );
			}else{
				$tree_node[] = $object->$key = null;
			}
		}
		return $object;
	}
}

/* End of file tree_helper.php */
/* Location: ./app/helpers/tree_helper.php */
