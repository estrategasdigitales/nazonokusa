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
	function recursive_nodes( $arrs ){
		$tree_node = '';
		if ( is_array( $arrs ) ){
			foreach ( $arrs as $arr => $val ){
				if( is_array( $val ) ){
					$tree_node .= '<div class="checkbox">';
					$tree_node .= '<label><input type="checkbox" name="claves[]" value="' . $arr . '">' . $arr . '</label>';
					$tree_node .= hijos( $val, $arr );
					$tree_node .= '</div>';
				}else{
					$tree_node .= '<div class="checkbox">';
					$tree_node .= '<label><input type="checkbox" name="claves[]" value="' . $arr . '">' . $arr . '</label>';
					$tree_node .= '</div>';
				}
			}
		}else{
			// $tree_node .= '<div class="checkbox">'."\n";
			// $tree_node .= '<label>'."\n";
			// $tree_node .= '<input type="checkbox" name="claves[]" value="'. $arrs . '">'."\n";
			// $tree_node .= $arrs;
			// $tree_node .= '</label>'."\n";
			// $tree_node .= '</div>'."\n";
		}
		return $tree_node;
	}
}

if ( ! function_exists( 'hijos' ) ){
	function hijos( $arreglo, $clave, $hijos = [] ){
		foreach ( $arreglo as $key => $value ){
			print_r( $key );
			// if ( is_array( $value ) ){
			// 	// $hijos = '<div id="' . $key . '">'."\n";
			// 	// $hijos .= '<label><input type="checkbox" name="claves[]" value="' .$clave. '.' . $key . '">' . $key . '</label>'."\n";
			// 	// hijos( $value, $clave . '.' . $key );
			// 	// $hijos .= '</div>'."\n";
			// }else{
			// 	// $hijos = '<div class="checkbox">'."\n";
			// 	// $hijos .= '<label><input type="checkbox" name="claves[]" value="' . $clave . '.' . $key . '">' . $key . '</label>'."\n";
			// 	// $hijos .= '</div>'."\n";
			// }
		}
	}
}

/* End of file tree_helper.php */
/* Location: ./app/helpers/tree_helper.php */
