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

/* End of file tree_helper.php */
/* Location: ./app/helpers/tree_helper.php */
