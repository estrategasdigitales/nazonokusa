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
	
	function __construct($arguments = [])
	{
		 $this->URL_INPUT 	 = $arguments["input"];
		 $this->URL_TEMPLATE = isset($arguments["template"]) ? $arguments["template"] : "";
		 $this->ORIGIN_PATHS = $arguments["paths"];


		 $this->PATHS 		 = $this->_setChildPath();
		 
		 $this->_decodeDataURLS();

		$this->STORE 			= new JsonStore();
		$this->curl =& get_instance();
		$this->curl->load->helper( 'file_get_contents_curl' );
	}

	private function _getINPUT()
	{
		return $this->INPUT;
	}

	private function _getTEMPLATE()
	{
		return $this->TEMPLATE;
	}

	private function _sortBycolumn(&$arr, $col, $dir = SORT_ASC) {
	    $sort_col = array();
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }

	    array_multisort($sort_col, $dir, $arr);
	}


	private function _decodeDataURL($url)
	{
		$file = file_get_contents_curl($url);

		$file = preg_replace('/^\(/','',$file);
		$file = preg_replace('/\)$/','',$file);

		return (json_decode($file,true));
	}

	private function _decodeDataURLS()
	{
		$this->INPUT 	= $this->_decodeDataURL($this->URL_INPUT);

		$this->TEMPLATE = $this->_decodeDataURL($this->URL_TEMPLATE);
	}


	private function _groupBy($array, $key) {
	    $return = array();
	    foreach($array as $val) 
	        $return[$val[$key]][] = $val;
	    
	    return $return;
	}

	private function _setChild($array) {
	    $return = array();
	    foreach($array as $val) 
	    	$return[] = [ "path"=>$val[0]["path"],"order"=>strlen($val[0]["path"]), "child" => $val ];
	    
	    return $return;
	}

	private function _searchKey( $needle_key, $array ) {
	  foreach($array AS $key=>$value){
	    if(substr_count($key, $needle_key)) return true;
	    if(is_array($value)){
	      if( ($result = $this->_searchKey($needle_key,$value)) !== false)
	        return true;
	    }
	  }
	  return false;
	} 

	private function _removeRoot($path)
	{
		return preg_replace('/^(tree\[\*\]|tree)./', '', $path);
	}

	private function _generatePath($path)
	{
		$path = $this->_removeRoot($path);
		$afeed = explode("[*].",$path);

		$keyFeed = $afeed[count($afeed)-1];
		$_afeed = $afeed;
		array_pop($_afeed);
		$pathFeed = implode("[*].",$_afeed)."[*]";

		return ["path" => $pathFeed, "key" => $keyFeed];
	}



	private function _parsePATHS()
	{
		$result = [];

		if(!is_array($this->ORIGIN_PATHS))
			$this->ORIGIN_PATHS = json_decode($this->ORIGIN_PATHS,true);

		//$this->_sortBycolumn($this->ORIGIN_PATHS,"feed1");

		

		foreach ($this->ORIGIN_PATHS as $path) {
			
			if($path==null)
				continue;

			$pathFeed = $this->_generatePath($path["feed1"]);

			$value = $this->_removeRoot($path["feed2"]);

			$result[] = ["path"=>$pathFeed["path"],"key"=>$pathFeed["key"], "value" =>$value];
		}
		return $result;
	}
	
	private function _setChildPath()
	{

		$paths = $this->_parsePATHS();	
		$paths = $this->_groupBy($paths,"path");
		$paths = $this->_setChild($paths);
		$this->_sortBycolumn($paths,"order");

		return $paths;
	}


	private function _getPaths()
	{
		return $this->PATHS;
	}



	private function _extractData($record,$string) {

	    $current_data = $record;

	    foreach ($string as $name) {

	            if (key_exists($name, $current_data)) {
	                    return $current_data[$name];
	            } else {
	                    return null;
	            }
	    }

	    return $current_data;
	} 


	private function _fixkeys($array) {

	    $numberCheck = false;
	    foreach ($array as $k => $val) {

	    	// echo count($val)."\n";
	    	// print_r($val); 
	    	// echo "key =".$k."\n";
	    	// echo "\n\n\n\n";

	    	

	    		// echo count($val);
	    		// print_r($val);
	    		// echo "-------\n\n";
	    	if (is_array($val) and count($val) >= 1) 
	    	{

	    		$array[$k] = $this->_fixkeys($val); //recurse
	    	}
	    	
			if(is_array($val) and array_key_exists(0, $val))
	    	{
	    		//$numberCheck = array("k" => $k,"v"=>$val);

	    		if(count($val) > 1)
	    			$array[$k] = $val;
	    	    else
	        		$array[$k] = $val[0];
	        	//return $array;
	    	}

	        

	    }


	    return $array;
	}

    private function __do($paths,$id,$j,$input,$_input,$template,$output,$pathParent = null)
    {
        // foreach ($paths as $path) {

		$node = $paths[$id];


		if($id > 0)
		{

			
			//$apath = explode(".",$node["path"]);
			$apath = explode($pathParent.".",$node["path"]);
			$path  = $apath[count($apath)-1];

			//$pathParent = $path;
			// echo $pathParent."\n";
			// echo $node["path"]."\n";
			// echo $path."\n";
			// echo "\n\n";
			//$pathParent = $path;

		}else
		{
			$path = $node["path"];
			$pathParent = $path;
		}


			$store = $this->STORE;
			$inputs = $store->get($input, "$.".$path);

			$tpath = count($paths)-1;


			if($id < $tpath)
				$id++;



			foreach ($inputs as $i => $record) {


				if (!@array_key_exists($i, $output) and $j == 0)
				 	$output[$i] = $template;
				    

					foreach ($node["child"] as $child) {


						$akey = explode(".",$child["key"]);
						$key  = '["'.implode('"]["',$akey).'"]';

						
						$return = $this->_extractData($record,$akey);
						

						$avalue = explode(".",$child["value"]);
						// $value  = '["'.implode('"]["',$avalue).'"]';
						//   if($id > 0)
						 	$value  = '["'.implode('"]["',$avalue).'"][]';


						if(!empty($return))
						{	

							if($i == 0)
								eval("\$output[$j]$value = \"$return\";");	
							else
								eval("\$output[$j]$value = \"$return\";");

						}
					}


					$output = $this->__do($paths,$id,$i,$record,$inputs,$template,$output,$pathParent);
				
				}


         return $output;


    }
	private function    isAssociativeArray( &$arr ) {
	    return  (bool)( preg_match( '/\D/', implode( array_keys( $arr ) ) ) );
	}



    private function _do($paths,$id,$j,$input,$_input,$template,$output,$pathParent = null)
    {
      // foreach ($paths as $path) {
		$node = $paths[$id];


			$path = $node["path"];

			if ($path == "[*]")
				$path = current(array_keys($input));
			else
				$path = preg_replace('/(\[\*\])$/', '', $path);


			$pathParent = $path;


			$store = $this->STORE;
			$inputs = $store->get($input, "$.".$path);

			$tpath = count($paths)-1;


			if($id < $tpath)
				$id++;

			
			if(is_array($inputs) and count($inputs) > 0)
			foreach ($inputs as $i => $record) {


				// if (!@array_key_exists($i, $output) and $j == 0)
				//   	$output = $template;
				    
				  
				 	if($this->isAssociativeArray($record))
				 	{
						foreach ($node["child"] as $child) {

							$akey = explode(".",$child["key"]);
							$key  = '["'.implode('"]["',$akey).'"]';
							
							$return = $this->_extractData($record,$akey);
							print_r($akey);
							print_r($record);
							$avalue = explode(".",$child["value"]);
							// $value  = '["'.implode('"]["',$avalue).'"]';
							//   if($id > 0)
								$avalue = str_replace("[*]", "", $avalue);
							 	$value  = '["'.implode('"]["',$avalue).'"][]';


							if(!empty($return))
							{	
								//print_r($value);
								eval("\$output[$j]$value = \"$return\";");	


							}
						}
						
						$output = $this->_do($paths,$id,$i,$record,$inputs,$template,$output,$pathParent);
						
				 	}else
				 	{
				 		foreach ($record as $ii => $rrecord) {


							foreach ($node["child"] as $child) {

								$akey = explode(".",$child["key"]);
								$key  = '["'.implode('"]["',$akey).'"]';

								
								$return = $this->_extractData($rrecord,$akey);
								
								$avalue = explode(".",$child["value"]);
								// $value  = '["'.implode('"]["',$avalue).'"]';
								//   if($id > 0)

									$avalue = str_replace("[*]", "", $avalue);
								 	$value  = '["'.implode('"]["',$avalue).'"][]';


								if(!empty($return))
								{	

									eval("\$output[$ii]$value = \"$return\";");	
									

								}
							}

							$output = $this->_do($paths,$id,$ii,$rrecord,$inputs,$template,$output,$pathParent);
				 		}

				 		
				 	}
				 		


					
				
				}



         return $output;


    }


	private function _toXML($writer,$nodes,$parentKey,&$i = 0)
	{
		foreach ($nodes as $nKey => $nValue) {
			 
			 $key = $parentKey;
			 $value = $nValue;


			if(is_array($nValue) and count($nValue) > 0)
			 {


			 	if(array_key_exists("@cdata", $nValue))
			 	{
			 		
					$writer->startElement($nKey);
					$writer->writeCData($nValue["@cdata"][0]);
					$writer->endElement();
					

			 	}elseif(array_key_exists("@attributes", $nValue)){



			 			if(!array_key_exists(0, $nValue["@attributes"]))
			 			{
		 					$writer->startElement($nKey);
		 					foreach ($nValue["@attributes"] as $katt => $vatt)
		 					{
								if(is_array($vatt) and count($vatt) == 1)
		 							$vatt = $vatt[0];
		 						$writer->writeAttribute($katt, $vatt); 
		 					}
		 
		 					$writer->endElement();
			 			}else
			 			{
				 			foreach ($nValue["@attributes"] as $katt => $vatt) {

				 				$writer->startElement("media:group");

				 				foreach ($vatt as $kvatt => $vvatt) {

				 					$writer->startElement($nKey);
				 					$writer->writeAttribute($katt, $vvatt); 
				 					$writer->endElement();
				 				}

				 				$writer->endElement();
				 			}
			 			}

			 	}else
			 	{

			 		if(!is_numeric($key) and $i > 0)
				 		$writer->startElement($key); 
				 	
				 	// echo "\n----\n\n";
				 	// print_r($nValue);
				 	// print_r($nKey);
				 	// echo "\n----\n\n";
				 	$this->_toXML($writer,$nValue,$nKey);

				 	if(!is_numeric($key) and $i > 0)	
				 		$writer->endElement(); 

				 	$i++;
				 }

			 }

			 if(!is_array($nValue) and !is_array($key) and !empty($nValue))
		 	 {
		 	 	
		 	 	// if(!array_key_exists("@cdata", $nValue))
		 	 	// {
					$writer->startElement($key); 
					$writer->text($nValue); 
					$writer->endElement(); 
		 	 	// }

				
		 	 }


				
		}
	}

    public function getData()
    {
    	$paths = $this->_getPaths();
    	
    	//print_r($paths);
    	// die;
    	//array_shift($paths);


    	$input    = $this->_getINPUT();
    	$template = $this->_getTEMPLATE();

    	$return = array();

    	if($paths[0]["path"] == "[*]")
    		$return = $this->_do($paths,0,0,$input,$input,$template,$output=[]);
    	else
    		$return = $this->__do($paths,0,0,$input,$input,$template,$output=[]);


    	//print_r($return);

    	return $return;
    }

    public function getDataFixed()
    {
    	return $this->_fixkeys($this->getData());
    }


	public function toRSS( $file = 'rss.xml', $encoding = 'UTF-8', $header = '', $attributes = [] ){

    	$template = $this->_getTEMPLATE();
    	$nodes = $this->getDataFixed();

    	//print_r($nodes);
    	echo json_encode($nodes);

		$writer = new XMLWriter();
		$writer->openURI($file);
		$writer->startDocument( '1.0', $encoding );
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
    	$nodes = $this->getDataFixed();



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


    public function toJSON( $file = 'json.json', $function = '' ){

    	$data = $this->getDataFixed();
    	
    	if ( ! empty ( $function ) )
    		$json = $function . '('. json_encode( $data ) .')';
    	else
    		$json = json_encode( $data );

    	if ( $file != 'json.json' )
    		file_put_contents($file, $json);
    	else
    		return $json;
    }
}

/* End of file Node.php */
/* Location: ./app/libraries/Node.php */