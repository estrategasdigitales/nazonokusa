<?php if ( ! defined('BASEPATH' ) ) exit( 'No direct script access allowed' );

require_once( __DIR__ . '/jsonpath/JsonStore.php' );

/**
*  Miguel Martinez <natacion@gmail.com>
*  v 1.5
*/

class Node{
	private $curl;

	var $URL_INPUT 		= null;
	var $INPUT 		    = null;

	var $URL_TEMPLATE 	= null;
	var $TEMPLATE 		= null;

	var $ORIGIN_PATHS 	= [];
	var $PATHS 			= [];

	var $STORE 			= null;
	
	function __construct( $arguments = [] ){
		$this->URL_INPUT 	 	= $arguments['input'];
		$this->URL_TEMPLATE 	= $arguments['template'];
		$this->ORIGIN_PATHS 	= $arguments['paths'];
		$this->PATHS 		 	= $this->_setChildPath();

		$this->_decodeDataURLS();

		$this->STORE = new JsonStore();
		$this->curl =& get_instance();
		$this->curl->load->helper( 'file_get_contents_curl' );
	}

	private function _getINPUT(){
		return $this->INPUT;
	}

	private function _getTEMPLATE(){
		return $this->TEMPLATE;
	}

	private function _sortBycolumn( &$arr, $col, $dir = SORT_ASC ){
	    $sort_col = array();
	    foreach ( $arr as $key=> $row ){
	        $sort_col[ $key ] = $row[ $col ];
	    }
	    array_multisort( $sort_col, $dir, $arr );
	}

	private function _decodeDataURL( $url ){
		$file = file_get_contents_curl( $url );
		$file = preg_replace( '/^\(/', '', $file );
		$file = preg_replace( '/\)$/', '', $file );

		return ( json_decode( $file, TRUE ) );
	}

	private function _decodeDataURLS(){
		$this->INPUT 		= $this->_decodeDataURL( $this->URL_INPUT );
		$this->TEMPLATE 	= $this->_decodeDataURL( $this->URL_TEMPLATE );
	}

	private function _groupBy( $array, $key ){
	    $return = array();
	    foreach ( $array as $val )
	        $return[ $val[ $key ] ][] = $val;

	    return $return;
	}

	private function _setChild( $array ){
	    $return = array();
	    foreach ( $array as $val ) 
	    	$return[] = [ 'path' => $val[0]['path'], 'order' => strlen( $val[0]['path'] ), 'child' => $val ];
	    
	    return $return;
	}

	private function _searchKey( $needle_key, $array ){
		foreach ( $array AS $key => $value ){
			if ( substr_count( $key, $needle_key ) ) return TRUE;
			if ( is_array( $value ) ){
				if ( ( $result = $this->_searchKey( $needle_key, $value ) ) !== FALSE )
					return TRUE;
			}
		}
		return FALSE;
	} 

	private function _removeRoot( $path ){
		return preg_replace('/^(tree\[\*\]|tree)./', '', $path);
	}

	private function _generatePath( $path ){
		$path = $this->_removeRoot( $path );
		$afeed = explode( '[*].', $path );

		$keyFeed = $afeed[ count( $afeed ) - 1 ];
		$_afeed = $afeed;
		array_pop( $_afeed );
		$pathFeed = implode('[*].', $_afeed ) . '[*]';

		return [ 'path' => $pathFeed, 'key' => $keyFeed ];
	}

	private function _parsePATHS(){
		$result = [];
		if ( ! is_array( $this->ORIGIN_PATHS ) )
			$this->ORIGIN_PATHS = json_decode( $this->ORIGIN_PATHS, TRUE );

		foreach ( $this->ORIGIN_PATHS as $path ){
			$pathFeed = $this->_generatePath( $path['feed1'] );
			$value = $this->_removeRoot( $path['feed2'] );
			$result[] = [ 'path' => $pathFeed['path'], 'key' => $pathFeed['key'], 'value' => $value ];
		}
		return $result;
	}
	
	private function _setChildPath(){
		$paths = $this->_parsePATHS();	
		$paths = $this->_groupBy( $paths, 'path' );
		$paths = $this->_setChild( $paths );
		$this->_sortBycolumn( $paths, 'order');
		return $paths;
	}

	private function _getPaths(){
		return $this->PATHS;
	}

	private function _extractData( $record, $string ){
	   	$current_data = $record;
	    foreach ( $string as $name ){
			if ( key_exists( $name, $current_data ) ){
				$current_data = $current_data[$name];
			} else {
				return null;
			}
		}
	    return $current_data;
	} 

	private function _fixkeys( $array ){
	    $numberCheck = FALSE;
	    foreach ( $array as $k => $val ){
	    	if ( ! is_numeric( $k ) and is_array( $val ) and array_key_exists( 0, $val ) ){
	        	$array[$k] = $val[0];
	    	}
	        if ( is_array( $val ) and count( $val ) > 1 ) $array[$k] = $this->_fixkeys( $val );
	    }
	    return $array;
	}

    private function __do( $paths, $id, $j, $input, $_input, $template, $output, $pathParent = null ){
		$node = $paths[ $id ];
		if ( $id > 0 ){
			$apath = explode( $pathParent . ".", $node['path'] );
			$path  = $apath[ count( $apath ) - 1 ];
		} else {
			$path = $node['path'];
			$pathParent = $path;
		}

		$store = $this->STORE;
		$inputs = $store->get( $input, "$.". $path );
		$tpath = count( $paths ) - 1;

		if ( $id < $tpath )
			$id++;

		foreach ( $inputs as $i => $record ){
			if ( ! @array_key_exists( $i, $output ) and $j == 0 )
			 	$output[$i] = $template;

			foreach ( $node['child'] as $child ){
				$akey = explode(".",$child["key"]);
				$key  = '["' . implode( '"]["', $akey ) . '"]';
				$return = $this->_extractData( $record, $akey );
				$avalue = explode( '.', $child['value'] );
				$value  = '["' . implode('"]["', $avalue ) . '"][]';
				if ( ! empty( $return ) ){	


					if ( $j == 0 )
						eval("\$output[$i]$value = \"$return\";");
					else
						eval("\$output[$j]$value = \"$return\";");
				}
			}
			$output = $this->__do( $paths, $id, $i, $record, $inputs, $template, $output, $pathParent );
		}
		return $output;
    }

    private function isAssociativeArray( &$arr ){
		return  (bool)( preg_match( '/\D/', implode( array_keys( $arr ) ) ) );
	}

    private function _do( $paths, $id, $j, $input, $_input, $template, $output, $pathParent = null ){
		$node = $paths[$id];
		$path = $node["path"];
		if ($path == "[*]")
			$path = current( array_keys( $input ) );
		else
			$path = preg_replace( '/(\[\*\])$/', '', $path );

		$pathParent = $path;
		$store 		= $this->STORE;
		$inputs 	= $store->get( $input, "$." . $path );

		$tpath = count( $paths ) - 1;
		if ( $id < $tpath )
			$id++;

		if ( is_array( $inputs ) and count( $inputs ) > 0 ){
			foreach ( $inputs as $i => $record ){
				if ( $this->isAssociativeArray( $record ) ){
					foreach ( $node["child"] as $child ){
						$akey 	= explode(".", $child["key"] );
						$key  	= '["'.implode('"]["', $akey).'"]';				
						$return = $this->_extractData( $record, $akey );
						$avalue = explode(".",$child["value"]);
						$avalue = str_replace("[*]", "", $avalue);
					 	$value  = '["'.implode('"]["',$avalue).'"][]';
						if( ! empty( $return ) ){	
							eval("\$output[$j]$value = \"$return\";");
						}
					}
					$output = $this->_do($paths,$id,$i,$record,$inputs,$template,$output,$pathParent);
			 	} else {
			 		foreach ( $record as $ii => $rrecord ){
						foreach ( $node["child"] as $child ){
							$akey 	= explode(".",$child["key"]);
							$key  	= '["'.implode('"]["', $akey).'"]';					
							$return = $this->_extractData( $rrecord, $akey );
							$avalue = explode(".",$child["value"]);
							$avalue = str_replace("[*]", "", $avalue);
							$value  = '["'.implode('"]["',$avalue).'"][]';
							if ( ! empty( $return ) ){	
								eval("\$output[$ii]$value = \"$return\";");	
							}
						}
						$output = $this->_do( $paths, $id, $ii, $rrecord, $inputs, $template, $output, $pathParent );
			 		}
				}
			}
		}
	}

	private function _toXML( $writer, $nodes, $parentKey, &$i = 0 ){
		foreach ( $nodes as $nKey => $nValue ){
			$key = $parentKey;
			$value = $nValue;
			if ( is_array( $nValue ) and count( $nValue ) > 0 ){
			 	if ( array_key_exists( '@cdata', $nValue ) ){
					$writer->startElement( $nKey );
					$writer->writeCData( $nValue['@cdata'][0] );
					$writer->endElement();
			 	} elseif ( array_key_exists( '@attributes', $nValue ) ){
		 			if ( ! array_key_exists( 0, $nValue['@attributes'] ) ){
	 					$writer->startElement( $nKey );
	 					foreach ( $nValue['@attributes'] as $katt => $vatt ){
							if ( is_array( $vatt ) and count( $vatt ) == 1)
	 							$vatt = $vatt[0];
	 						$writer->writeAttribute( $katt, $vatt );
	 					}
	 					$writer->endElement();
		 			} else {
			 			foreach ( $nValue['@attributes'] as $katt => $vatt ){
			 				$writer->startElement( 'media:group' );
			 				foreach ( $vatt as $kvatt => $vvatt ){
			 					$writer->startElement( $nKey );
			 					$writer->writeAttribute( $katt, $vvatt );
			 					$writer->endElement();
			 				}
			 				$writer->endElement();
			 			}
		 			}
			 	} else {
			 		if ( ! is_numeric( $key ) and $i > 0 )
				 		$writer->startElement($key); 

				 	$this->_toXML( $writer, $nValue, $nKey );
				 	if ( ! is_numeric($key) and $i > 0 )
				 		$writer->endElement();
				 	$i++;
				}
			}

			if ( ! is_array( $nValue ) and ! is_array( $key ) and ! empty( $nValue ) ){
				$writer->startElement( $key );
				$writer->text( $nValue );
				$writer->endElement();
		 	}
		}
	}

    public function getData(){
    	$paths 		= $this->_getPaths();
    	$input    	= $this->_getINPUT();
    	$template 	= $this->_getTEMPLATE();
		if ( $paths[0]["path"] == "[*]" )
			return $this->_do( $paths, 0, 0, $input, $input, $template, $output = [] );
		else
			return $this->__do( $paths, 0, 0, $input, $input, $template, $output = [] );
    }

    public function getDataFixed(){
    	return $this->_fixkeys( $this->getData() );
    }

    public function toRSS( $file = 'rss.xml' ){
    	$template = $this->_getTEMPLATE();
    	$nodes = $this->getData();
		$writer = new XMLWriter();
		$writer->openURI($file);
		$writer->startDocument( '1.0','UTF-8' );
		$writer->setIndent( 4 );
		$writer->startElement( 'rss' );
		$writer->writeAttribute( 'version', '2.0' );
    	if ( $this->_searchKey( 'media:content', $template ) )
    		$writer->writeAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );

    	if ( $this->_searchKey('media:', $template ) )
    		$writer->writeAttribute( 'xmlns:media', 'http://search.yahoo.com/mrss/' );

    	if ( $this->_searchKey( 'atom:link', $template ) )
    		$writer->writeAttribute( 'xmlns:atom','http://www.w3.org/2005/Atom' );
			$writer->startElement( 'channel');
				$this->_toXML( $writer, $nodes, 'item');
			$writer->endElement(); 
		$writer->endElement(); 
		$writer->endDocument(); 
		$writer->flush();
    }

    public function toXML( $file = 'xml.xml', $encoding = 'UTF-8' ){
    	$template = $this->_getTEMPLATE();
    	$nodes = $this->getData();
		$writer = new XMLWriter();  
		$writer->openURI( $file );
		$writer->startDocument( '1.0', $encoding );
		$writer->setIndent( 4 );
		$writer->startElement( 'xml' ); 
		$this->_toXML( $writer, $nodes, 'item' );
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();
    }

    public function toJSON( $file = 'json.json' ){
    	$data = $this->getData();
    	$json =json_encode( $data );
    	if ( $file != 'json.json' )
    		file_put_contents( $file, $json );
    	else
    		return $json;
    }
}