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

    var $cdata          = false;

    var $isStandardOutPut = true;
    var $isJsonVariable   = false;

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

        if($keyFeed == "@cdata")
            $this->cdata = true;

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

        if($this->cdata)
            $onlyPaths[] = "";

        foreach($onlyPaths as $path)
        {
            $subpath = "";
            $validate = [];
            $apath = explode(".",$path);

            if(!in_array("",$paths) and $path == "")
                $return[$path] = isset($paths[$path]) ? $paths[$path] : [];
            else
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
                if($parent_path == $path_child["path"]  and $path_child["order"]!=$path["order"])
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
        $key = key($current_data);

        foreach ($string as $name) {

            if (key_exists($name, $current_data)) {
                return $current_data[$name];
            } else if(!is_numeric($key)){
                return $current_data[$key][$name];
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


    private function startsWith($needle, $haystack) {
        return preg_match('/^' . preg_quote($needle, '/') . '/', $haystack);
    }


    public function startWithVariable($string = '')
    {
        preg_match_all('/^(\S+?)(?:[=;]|\s+)\[/', $string, $matches); //credits for mr. @booobs for this regex

        if(isset($matches[1][0]))
            return $matches[1][0];
        else
            return false;
    }


    private function __do($paths,$input,$original_input = [],$id_path = 0,$output = [],$path_parent = "",$last_eval = "",$parent_eval="",$last_eval_template = "")
    {

        $node = $paths;
        $xml = false;

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

        if($path == "")
        {
            if($input and is_array($input) and array_key_exists(0,$input))
                $inputs = $input;
            else
                $inputs[0] = $input;
        }
        else{

            if($this->startsWith("media:",$path))
            {
                $xml = true;
                $path = preg_replace("/\[\*\]$/","",$path);

                if($path == "")
                    $inputs[0] =$input ;
                else
                    $inputs = $store->get($input, "$.".$path);


                if(array_key_exists(0,$inputs) and count($inputs) == 1)
                {
                    $input_key = key($inputs[0]);

                    if(count($inputs[0][$input_key]) > 1 and is_array($inputs[0][$input_key]) )
                        $inputs = $inputs[0];
                }


            }else
                $inputs = $store->get($input, "$.".$path);


        }

        if($input)
        {
            if(count($inputs) == 0 and array_key_exists(0,$input))
            {
                $inputs = $input;
                $path = $path_parent;
            }


            if(count($inputs) > 0)
            {
                if(array_key_exists(0,$inputs) and is_array($inputs))
                    $first  = $inputs[0];
                else
                    $first = [];

                if(!is_array($first))
                {
                    $path = preg_replace("/\[\*\]$/","",$path);
                    $inputs = $store->get($input, "$.".$path);
                }
            }
        }







        $tpath = count($paths)-1;


        if($id_path < $tpath)
            $id_path++;


        if($path != "")
            $property_eval = "['".str_replace("[*]","",$path)."']";
        else
            $property_eval = "";

        foreach ($inputs as $i => $record) {


            if($property_eval and substr_count($last_eval,$property_eval) > 0)
            {
                if(!is_numeric($i)){
                    if($property_eval == "")
                        $eval2 = $property_eval."['".$i."']";
                    else
                        $eval2 = $property_eval."['0']['".$i."']";
                }else
                    $eval2 = $property_eval."['".$i."']";// extra
            }else
            {
                if(!is_numeric($i))
                {
                    if($property_eval == "")
                        $eval2 = $last_eval.$property_eval."['".$i."']";
                    else
                        $eval2 = $last_eval.$property_eval."['0']['".$i."']";
                }
                else
                    $eval2 = $last_eval.$property_eval."['".$i."']";// extra
            }



            $eval = $last_eval.$property_eval."['".$i."']";



            foreach ($node["child"] as $child_value => $child) {

                if(count($node["child"]) == 1)// extra
                    $eval = $eval2;// extra



                if(isset($child["child"]))
                {

                    if($path_parent and substr_count($path_parent, $path) == 0)
                        $path_parent = $path_parent.".".$path;


                    if(count($node["child"]) == 1 and !$xml) // extra
                        $eval = $last_eval; // extra

                    $output = $this->__do($child,$record,$original_input,$id_path,$output,$path_parent,$eval,$last_eval,$i);
                }else
                {
                    if(count($node["child"]) == 1)
                    {
                        $last_eval = $eval; // extra

                        preg_match_all('^\[\'(.*?)\'\]^', $last_eval, $out);
                        $is_number = $out[1][count($out[1])-2];

                        if(is_numeric($is_number))
                        {
                            array_pop($out[1]);
                            $last_eval = preg_replace('/\[\'[(0-9a-z:. )]\'\]$/',"",$last_eval);
                        }

                    }// extra


                    $akey = explode(".",$child["key"]);
                    $key  = '["'.implode('"]["',$akey).'"]';

                    if(count($akey) == 1)
                    {
                        $akey = explode("[*]",$child["key"]);
                        $akey = [$akey[0]];
                    }

                    if(is_array($record))
                        $return = $this->_extractData($record,$akey);
                    else
                        $return = $record;
                    $return = str_replace('"','\"',$return);

                    // when set template!!!
                    if($child["path"].".".$child["key"] != $child["value"] and $child["path"] != "")
                    {
                        $this->isTemplate = true;
                        //resources[*].resource[*].attributes[*].pubDate

                        preg_match_all('/\[(.*?)\]/', $eval, $matches);

                        $last_iteration = $matches[1][count($matches[1])-1];
                        //$last_iteration = intval($last_iteration);

                        //resource[*].attributes[*].pubDate
                        $eval_template = explode(".",$child["value"]);

                        if(count($eval_template) > 1)
                            array_shift($eval_template);

                        //resource[0].attributes[*].pubDate


                        $first_node_iteration = explode("[*]",$eval_template[0]);
                        $first_node_iteration = $first_node_iteration[0];

                        if(isset($output[$first_node_iteration]))
                            $total_childs  = count($output[$first_node_iteration]);
                        else
                            $total_childs = 0;


                        $current_iteration = $last_iteration;

                        /*
                        if($last_iteration!=$total_childs)
                            $current_iteration = $total_childs + $last_iteration;

                        */

                        foreach($eval_template as $eval_template_value => $eval_template_record)
                        {

                            if($this->isJsonVariable)
                            {
                                $eval_template_record = explode("[*]",$eval_template_record);

                                $n_eval_template_record[0] = "[*]";
                                $n_eval_template_record[1] = "['".$eval_template_record[0]."']";

                                $eval_template_record = implode("",$n_eval_template_record);

                            }elseif(substr_count($eval_template_record,"[*]") > 0 or $this->isJsonVariable)
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

                        $eval_template = explode("[*]",$eval_template);
                        $eval_template = implode("",$eval_template);

                        eval("\$output$eval_template = \"$return\";"); // when set template!!!

                    }else
                    {

                        $key = explode("[*]",$key);
                        $key = implode("",$key);


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






    private function _toXML($writer,$nodes,$parentKey,$kind = "",$attributes = [])
    {

        $paths = $this->_getPaths();

        if(($parentKey == "channel" or $parentKey == "resources") and count($nodes) > 1 and !is_numeric($parentKey))
        {
            $writer->startElement($parentKey);
        }


        foreach ($nodes as $nKey => $nValue) {

            $key = $parentKey;
            $value = $nValue;

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
                if(!is_numeric($nKey))
                    $writer->startElement($nKey);

                $writer->writeCData($nValue["@cdata"]);

                if(!is_numeric($nKey))
                    $writer->endElement();


            }elseif(array_key_exists("@attributes", $nValue)){



                if(!array_key_exists(0, $nValue["@attributes"]))
                {
                    if(is_numeric($key))
                        $writer->startElement($nKey);
                    else
                        $writer->startElement($key);

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


                        if(is_numeric($key))
                            $writer->startElement($nKey);
                        else
                            $writer->startElement($key);

                        foreach ($vatt as $kvatt => $vvatt) {

                            //$writer->startElement($nKey);
                            $writer->writeAttribute($kvatt, $vvatt);
                            //$writer->endElement();
                        }

                        $writer->endElement();
                    }
                }

            }elseif(is_array($nValue) and count($nValue) > 0)
            {

               if($kind == "xml")
                {

                    if($this->startsWith("media:",$nKey))
                    {
                        if($nKey=="media:group")
                            $writer->startElement($nKey);

                    }else if(is_numeric($parentKey))
                        $writer->startElement($nKey);
                    elseif($parentKey == "resources")
                    {

                        $writer->writeAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );
                        $writer->writeAttribute( 'xmlns:media', 'http://search.yahoo.com/mrss/' );

                        $writer->startElement("resources");
                        $nKey = "resource";

                    }else if($parentKey == "resource")
                        $writer->startElement("resource");
                    elseif(is_numeric($nKey))
                    {
                        if(count($nodes) > 1)
                            $writer->startElement($parentKey);
                    }
                    else
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);

                    if($this->startsWith("media:",$nKey))
                    {
                        if($nKey=="media:group")
                            $writer->endElement();

                    }elseif(!is_numeric($nKey))
                        $writer->endElement();
                    elseif(count($nodes) > 1)
                        $writer->endElement();



                }else if($kind == "rss")
                {


                    if($this->startsWith("media:",$nKey))
                    {
                        if($nKey=="media:group")
                            $writer->startElement($nKey);

                    }elseif(is_numeric($parentKey))
                        $writer->startElement($nKey);
                    elseif($parentKey == "channel")
                    {
                        $nKey = "item";

                        if(count($nodes) == 1)
                        {
                            $writer->startElement("channel");

                            if($attributes and is_array($attributes))
                            {
                                foreach($attributes as $k_att => $v_att )
                                {
                                    $writer->startElement($k_att);
                                    $writer->writeCData($v_att);
                                    $writer->endElement();
                                }
                            }
                        }






                    }else if($parentKey == "item")
                        $writer->startElement("item");
                    elseif(is_numeric($nKey))
                    {
                        if(count($nodes) > 1)
                            $writer->startElement($parentKey);
                    }
                    else
                        $writer->startElement($nKey);

                    $this->_toXML($writer,$nValue,$nKey,$kind);

                    if(($parentKey == "channel" or $parentKey == "resources") and count($nodes) > 1 and !is_numeric($parentKey))
                    {

                    }
                    elseif($this->startsWith("media:",$nKey))
                    {
                        if($nKey=="media:group")
                            $writer->endElement();

                    }elseif(!is_numeric($nKey))
                        $writer->endElement();
                    elseif(count($nodes) > 1)
                        $writer->endElement();

                }


            }

        }


        if(($parentKey == "channel" or $parentKey == "resources") and count($nodes) > 1 and !is_numeric($parentKey))
        {
            $writer->endElement();
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

        if(!is_array($campos_orig))
            return [];

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

        $data = $this->__do($paths,$input);

        if($this->isTemplate)
        {
            $template = $this->_getTemplate();
            $key = key($template);

            $template = count($template) > 1 ? $template : $template[$key];

            $this->createEmptyChildren($data,$template);
        }

        return $data;
    }

    public function move_to_bottom(&$array, $key) {
        if(isset($array[$key]))
        {
            $value = $array[$key];
            unset($array[$key]);
            $array[$key] = $value;
        }

    }


    public function createEmptyChildren(&$data = [],$childs =[],$i = 0)
    {
        $i++;

        if($data)
        {
            foreach($data as $index_record => &$record)
            {

                if(is_array($childs) and $childs)
                {
                    foreach($childs as $index_child => $child)
                    {

                        if($i == 1 and !array_key_exists(0,$data) )
                            $record_exits = isset($data[$index_child]) ? true : false;
                        else
                            $record_exits = isset($record[$index_child]) ? true : false;

                        if(!$record_exits)
                        {
                            if(!is_array($child))
                            {
                                if($i == 1 and !array_key_exists(0,$data) )
                                    $data[$index_child] = "";
                                else
                                    $record[$index_child] = "";
                            }
                            else
                            {
                                if($i == 1 and !array_key_exists(0,$data) )
                                    $data[$index_child] = $child;
                                else
                                    $record[$index_child] = $child;
                            }


                        }else
                        {

                            if($i == 1 and !array_key_exists(0,$data) )
                            {
                                if(is_array($data[$index_child]))
                                    $this->createEmptyChildren($data[$index_child],$childs[$index_child],$i);
                            }else
                            {
                                if(is_array($record[$index_child]))
                                    $this->createEmptyChildren($record[$index_child],$childs[$index_child],$i);
                            }

                        }

                    }
                }

            }
        }


    }


    public function getDataFixed()
    {
        return $this->_fixkeys($this->getData());
    }




    public function toRSS( $nodes= [],$file = 'rss.xml', $encoding = 'UTF-8', $attributes = [] ){

        $writer = new XMLWriter();
        $writer->openURI($file);
        $writer->startDocument( '1.0', 'UTF-8' );
        $writer->setIndent( 4 );
        $writer->startElement( 'rss' );


        $this->move_to_bottom($nodes,"item");

        $writer->writeAttribute( 'version', '2.0' );

            $writer->writeAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );


            $writer->writeAttribute( 'xmlns:media', 'http://search.yahoo.com/mrss/' );


            $writer->writeAttribute( 'xmlns:atom','http://www.w3.org/2005/Atom' );



        $paths = $this->_getPaths();

        if(!$paths["path"])
        {
            $writer->startElement("channel");

            if($attributes and is_array($attributes) and count($attributes) > 0)
            {
                foreach($attributes as $k_att => $v_att )
                {
                    $writer->startElement($k_att);
                    $writer->writeCData($v_att);
                    $writer->endElement();
                }
            }


            $this->_toXML( $writer, $nodes, 'item','rss',$attributes);
        }else
        {

            $this->_toXML( $writer, $nodes, 'channel','rss',$attributes);
        }



        if(!$paths["path"])
            $writer->endElement();


        $writer->endElement();

        $writer->endDocument();
        $writer->flush();
    }








    public function toXML( $nodes = [], $file = 'xml.xml', $encoding = 'UTF-8' ){


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
            $writer->startDocument( '1.0', "UTF-8" );
            $writer->setIndent( 4 );

            $paths = $this->_getPaths();


            if($this->isTemplate)
            {
                $template = $this->_getTEMPLATE();
                $key = key($template);

                $this->_toXML( $writer, $nodes, $key, 'xml' );

            }elseif(!$paths["path"])
            {
                $writer->startElement("resources");

                $writer->writeAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );
                $writer->writeAttribute( 'xmlns:media', 'http://search.yahoo.com/mrss/' );


                    $this->_toXML( $writer, $nodes, 'resource', 'xml' );

            }else
                $this->_toXML( $writer, $nodes, 'resources', 'xml' );


            if(!$paths["path"])
                $writer->endElement();

            $writer->endDocument();
            $writer->flush();
        }
    }



    public function toJSON( $data = [], $file = 'json.json', $function = '' ){

        //$data = $this->getDataFixed();

        if ( ! empty ( $function ) )
            $json = $function . '('. json_encode( $data ) .')';
        else
            $json = json_encode( $data );

        if($this->isJsonVariable)
            $json = $this->isJsonVariable."=[" . json_encode( $data ) . "]";

        if ( $file != 'json.json' )
            file_put_contents($file, $json);
        else
            return $json;
    }
}

/* End of file Node.php */
/* Location: ./app/libraries/Node.php */