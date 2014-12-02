<?php if ( ! defined('BASEPATH' ) ) exit( 'No direct script access allowed' );

require_once( __DIR__ . '/jsonpath/JsonStore.php' );
require_once( __DIR__ . '/ArrayToXML.php' );
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

    var $TEMPLATE_PATHS = [];

    var $STORE 			= null;

    var $isTemplate     = false;

    function __construct($arguments = [])
    {
        $this->URL_INPUT 	 = $arguments["input"];
        $this->URL_TEMPLATE = isset($arguments["template"]) ? $arguments["template"] : "";
        $this->ORIGIN_PATHS = $arguments["paths"];


        $this->PATHS 		    = $this->_setChildPath();

        $this->TEMPLATE_PATHS = $this->_setChildPath("template");

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
        $this->TEMPLATE = $this->mapAttributes($this->TEMPLATE);
    }


    private function _groupBy($array, $key) {
        $return = array();
        foreach($array as $val)
            $return[$val[$key]][] = $val;

        return $return;
    }

    private function _setChild($array) {
        $return = array();
        foreach($array as $i => $val)
        {
            if(count($val) > 0)
                $return[] = [ "path"=>$val[0]["path"],"order"=>strlen($val[0]["path"]), "child" => $val ];
            else
                $return[] = [ "path"=>$i,"order"=>strlen($i), "child" => [] ];
        }


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
        $afeed = explode(".",$path);

        $keyFeed = $afeed[count($afeed)-1];
        $_afeed = $afeed;
        array_pop($_afeed);
        $pathFeed = implode(".",$_afeed);

        return ["path" => $pathFeed, "key" => $keyFeed];
    }



    private function _parsePATHS($kind = "input")
    {
        $result = [];

        if(!is_array($this->ORIGIN_PATHS))
            $this->ORIGIN_PATHS = json_decode($this->ORIGIN_PATHS,true);

        //$this->_sortBycolumn($this->ORIGIN_PATHS,"feed1");



        foreach ($this->ORIGIN_PATHS as $path) {

            if($path==null)
                continue;

            if($kind == "input")
            {
                $pathFeed = $this->_generatePath($path["feed1"]);
                $value = $this->_removeRoot($path["feed2"]);
            }else{
                $pathFeed = $this->_generatePath($path["feed2"]);
                $value = $this->_removeRoot($path["feed1"]);
            }


            $result[] = ["path"=>$pathFeed["path"],"key"=>$pathFeed["key"], "value" =>$value];
        }
        return $result;
    }

    private function setNewPaths($paths)
    {
        $return = [];
        $onlyPaths = [];

        foreach($paths as $value => $path)
            $onlyPaths[] = $value;

        foreach($onlyPaths as $path)
        {
            $subpath = "";
            $validate = [];
            $apath = explode(".",$path);

            $return[$path] = $paths[$path];

            foreach($apath as $rapath)
            {
                $subpath[]= $rapath;

                $validate = implode(".",$subpath);

                if(!in_array($validate,$onlyPaths))
                    $return[$validate] = [];
            }

        }

        return $return;
    }

    private  function toTree($paths)
    {
        $this->_sortBycolumn($paths,"order",SORT_DESC);
        $tree = $paths;

        foreach($paths as $i => $path)
        {


            $parent_path = explode(".",$path["path"]);
            $parent_path = str_replace(".".$parent_path[count($parent_path)-1],'',$path["path"]);


            if($path["path"] == $parent_path)
                $parent_path = "";

            foreach($tree as $j => $path_child)
            {
                if($parent_path == $path_child["path"]  )
                {
                    $tree[$j]["child"][count($tree[$j]["child"])] = $tree[$i];

                }

            }


        }

        $this->_sortBycolumn($tree,"order");

        return $tree;
    }


    private function _setChildPath($kind = "input")
    {

        $paths = $this->_parsePATHS($kind);
        $paths = $this->_groupBy($paths,"path");


        $paths = $this->setNewPaths($paths);

        $paths = $this->_setChild($paths);
        $paths = $this->toTree($paths);


        return $paths[0];
    }


    private function _getPaths()
    {
        return $this->PATHS;
    }


    private function _getTemplatePaths()
    {
        return $this->TEMPLATE_PATHS;
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

    private function getArrayByString($keys,$array)
    {
        $value = $array;

        foreach($keys as $key)
            $value = $value[$key];

        return $value;
    }




    private function __do($paths,$input,$original_input = [],$id_path = 0,$output = [],$path_parent = "",$last_eval = "",$parent_eval="",$last_eval_template = "")
    {

        $node = $paths;

        if($id_path > 0)
        {

            $apath = explode($path_parent.".",$node["path"]);
            $path  = $apath[count($apath)-1];

            //$path_parent = $path;

        }else
        {
            $original_input = $input;
            $path = $node["path"];
            $path_parent = $path;
        }

        $store = $this->STORE;
        $inputs = $store->get($input, "$.".$path);

        $first  = $inputs[0];

        if(!is_array($first))
        {
            $path = preg_replace("/\[\*\]$/","",$path);
            $inputs = $store->get($input, "$.".$path);
        }

        $tpath = count($paths)-1;


        if($id_path < $tpath)
            $id_path++;


        $property_eval = "['".str_replace("[*]","",$path)."']";


        foreach ($inputs as $i => $record) {


            if(substr_count($last_eval,$property_eval) > 0)// extra
                $eval2 = $property_eval."[".$i."]";// extra
            else
                $eval2 = $last_eval.$property_eval."[".$i."]";// extra

            $eval = $last_eval.$property_eval."[".$i."]";

            foreach ($node["child"] as $child_value => $child) {

                if(count($node["child"]) == 1)// extra
                    $eval = $eval2;// extra

                if(isset($child["child"]))
                {

                    if(substr_count($path_parent, $path) == 0)
                        $path_parent = $path_parent.".".$path;

                    if(count($node["child"]) == 1) // extra
                        $eval = $last_eval; // extra

                    $output = $this->__do($child,$record,$original_input,$id_path,$output,$path_parent,$eval,$last_eval,$i);
                }else
                {
                    if(count($node["child"]) == 1) // extra
                        $last_eval = $eval; // extra

                    $akey = explode(".",$child["key"]);
                    $key  = '["'.implode('"]["',$akey).'"]';
                    $return = $this->_extractData($record,$akey);

                    // when set template!!!
                    if($child["path"].".".$child["key"] != $child["value"])
                    {
                        $this->isTemplate = true;
                        //resources[*].resource[*].attributes[*].pubDate

                        preg_match_all('/\[(.*?)\]/', $eval, $matches);

                        $last_iteration = $matches[1][count($matches[1])-1];
                        $last_iteration = intval($last_iteration);

                        //resource[*].attributes[*].pubDate
                        $eval_template = explode(".",$child["value"]);
                        array_shift($eval_template);

                        //resource[0].attributes[*].pubDate


                        $first_node_iteration = explode("[*]",$eval_template[0]);
                        $first_node_iteration = $first_node_iteration[0];

                        if(isset($output[$first_node_iteration]))
                            $total_childs  = count($output[$first_node_iteration]) -1;
                        else
                            $total_childs = 0;


                        $current_iteration = $last_iteration;


                        if($last_iteration!=$total_childs)
                            $current_iteration = $total_childs + $last_iteration;



                        foreach($eval_template as $eval_template_value => $eval_template_record)
                        {

                            if(substr_count($eval_template_record,"[*]") > 0)
                            {
                                $eval_template_record = explode("[*]",$eval_template_record);

                                $eval_template_record[0] = "['".$eval_template_record[0]."']";
                                $eval_template_record[1] = "[*]";
                                $eval_template_record = implode("",$eval_template_record);
                            }else
                            {
                                $eval_template_record = "['".$eval_template_record."']";
                            }


                            if($eval_template_value == 0)
                            {

                                $eval_template[$eval_template_value] = str_replace("*",$current_iteration,$eval_template_record);
                            }
                            else
                                $eval_template[$eval_template_value] = str_replace("*",0,$eval_template_record);
                        }

                        //resource[0].attributes[0].pubDate

                        $eval_template = implode("",$eval_template);

                        eval("\$output$eval_template = \"$return\";"); // when set template!!!

                    }else
                    {

                        eval("\$output$eval$key = \"$return\";"); // without template
                    }

                }

            }



        }


        return $output;


    }
    private function    isAssociativeArray( &$arr ) {
        return  (bool)( preg_match( '/\D/', implode( array_keys( $arr ) ) ) );
    }





    private function _toXML($writer,$nodes,$parentKey,$kind = "")
    {
        foreach ($nodes as $nKey => $nValue) {

            $key = $parentKey;
            $value = $nValue;


            if(is_array($nValue) and count($nValue) > 0)
            {

                if($this->isTemplate)
                {
                    if(is_numeric($parentKey))
                        $writer->startElement($nKey);
                    elseif($parentKey == "resourses")
                    {
                        $writer->startElement("resourses");
                        $nKey = "resourse";

                    }else if($parentKey == "resourse")
                        $writer->startElement("resourse");
                    elseif(is_numeric($nKey))
                        $writer->startElement($parentKey);
                    else
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);


                    $writer->endElement();

                }elseif($kind == "xml")
                {
                    /*
                    if(!is_numeric($nKey) and array_key_exists(0,$nValue))
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);

                    if(!is_numeric($nKey) and array_key_exists(0,$nValue))
                        $writer->endElement();
                    */
                    if(is_numeric($parentKey))
                        $writer->startElement($nKey);
                    elseif($parentKey == "resourses")
                    {
                        $writer->startElement("resourses");
                        $nKey = "resourse";

                    }else if($parentKey == "resourse")
                        $writer->startElement("resourse");
                    elseif(is_numeric($nKey))
                        $writer->startElement($parentKey);
                    else
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);


                        $writer->endElement();

                }else if($kind == "rss")
                {



                    if(is_numeric($parentKey))
                        $writer->startElement($nKey);
                    elseif($parentKey == "channels")
                    {
                        $writer->startElement("channels");
                        $nKey = "channel";

                    }else if($parentKey == "channel")
                        $writer->startElement("channel");
                    elseif(is_numeric($nKey))
                        $writer->startElement($parentKey);
                    else
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);


                    $writer->endElement();


                }




            }else
            {
                if(!is_array($nValue))
                {
                    $is_numeric = explode("x",$nKey);

                    if(count($is_numeric) > 1 )
                    {
                        if(is_numeric($is_numeric[0]) and is_numeric($is_numeric[1]))
                            $nKey ="_".$nKey;
                    }

                    $writer->startElement($nKey);
                    $writer->writeCData($nValue);
                    $writer->endElement();

                }elseif(array_key_exists("@cdata", $nValue))
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

                }

            }






        }
    }

    /**
     * [claves description]
     * @param  [type] $arreglo [description]
     * @param  [type] $origin  [description]
     * @return [type]          [description]
     */
    function claves( $arreglo, $origin ){
        if ( ! empty( $arreglo[0] ) ){
            for ($i = 0; $i < count( $arreglo ); $i++ ){
                foreach ( $arreglo[$i] as $key => $value ){
                    if ( is_object( $value ) ){
                        $value = get_object_vars( $value );
                    }

                    if ( is_array( $value ) ){
                        if ( ! empty( $origin[$key] ) ){
                            $origin[$key] = $this->claves( $value, $origin[$key] );
                        } else {
                            $origin[$key] = $this->claves( $value, $origin[$key] = [] );
                        }
                    } else {
                        if ( ! array_key_exists( $key, $origin ) ){
                            $origin[$key] = '';
                        }
                    }
                }
            }
        } else {
            foreach ( $arreglo as $key => $value ){
                if ( is_array( $value ) ){
                    if ( is_object( $value ) ){
                        $value = get_object_vars( $value );
                    }

                    if ( ! empty( $origin[$key] ) ){
                        $origin[$key] = $this->claves( $value, $origin[$key] );
                    } else {
                        $origin[$key] = $this->claves( $value, $origin[$key] = [] );
                    }
                } else {
                    if ( ! array_key_exists( $key, $origin ) ){
                        $origin[$key] = '';
                    }
                }
            }
        }
        return $origin;
    }


    public function mapAttributes( $feed ){
        $campos_orig 	= is_array($feed) ? $feed : json_decode( $feed, TRUE );
        $campos 		= [];

        $items 			= count( $campos_orig );
        if ( ! empty( $campos_orig[0] ) ){
            for ( $i = 0; $i < count( $campos_orig ); $i++ ){
                foreach ( $campos_orig[$i] as $key => $value ){
                    if ( is_object( $value ) ){
                        $value = get_object_vars( $value );
                    }

                    if ( is_array( $value ) ){
                        if ( ! empty( $campos[$key] ) ){
                            $campos[$key] = $this->claves( $value, $campos[$key] );
                        } else {
                            $campos[$key] = $this->claves( $value, $campos[$key] = [] );
                        }
                    } else {
                        if ( ! array_key_exists($key, $campos) ){
                            $campos[$key] = '';
                        }
                    }
                }
            }
        } else {
            foreach ( $campos_orig as $key => $value ){
                if ( is_object( $value ) ){
                    $value = get_object_vars( $value );
                }

                if ( is_array( $value ) ){
                    if ( ! empty( $campos[$key] ) ){
                        $campos[$key] = $this->claves( $value, $campos[$key] );
                    } else {
                        $campos[$key] = $this->claves( $value, $campos[$key] = [] );

                    }
                } else {
                    if( ! array_key_exists( $key, $campos ) ){
                        $campos[$key] = '';
                    }
                }
            }
        }
        return $campos;
    }

    public function getData()
    {
        $paths = $this->_getPaths();

        $input    = $this->_getINPUT();

        return $this->__do($paths,$input);
    }




    public function getDataFixed()
    {
        return $this->_fixkeys($this->getData());
    }


    public function toRSS( $file = 'rss.xml', $encoding = 'UTF-8', $header = '', $attributes = [] ){

        $template = $this->_getTEMPLATE();
        $nodes = $this->getDataFixed();

        //print_r($nodes);
        //echo json_encode($nodes);

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

        //$writer->startElement( 'channels');
        $this->_toXML( $writer, $nodes, 'channels','rss');

        //$writer->endElement();
        $writer->endElement();

        $writer->endDocument();
        $writer->flush();
    }








    public function toXML( $file = 'xml.xml', $encoding = 'UTF-8' ){

        $nodes = $this->getDataFixed();

        if($this->isTemplate)
        {
            $template = $this->_getTEMPLATE();

            $xml = new ArrayToXML();
            $output =  $xml->buildXML($nodes,key($template));

            file_put_contents($file, $output);
        }else
        {
            $writer = new XMLWriter();
            $writer->openURI( $file );
            $writer->startDocument( '1.0', $encoding );
            $writer->setIndent( 4 );
            $writer->startElement( 'xml' );
            $this->_toXML( $writer, $nodes, 'resourses', 'xml' );
            $writer->endElement();
            $writer->endDocument();
            $writer->flush();
        }



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