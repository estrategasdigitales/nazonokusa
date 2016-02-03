<?php if ( ! defined('BASEPATH' ) ) exit( 'No direct script access allowed' );

require_once( __DIR__ . '/jsonpath/JsonStore.php' );
require_once( __DIR__ . '/ArrayToXML.php' );
/**
 *  Miguel Martinez <natacion@gmail.com>
 *  v 1.5
 */

class Node{
    private $curl;

    var $ARRAY_EVAL     = [];
    var $URL_INPUT      = null;
    var $INPUT          = null;

    var $URL_TEMPLATE   = null;
    var $ESPECIFICO_URL_SALIDA     = null;
    var $TEMPLATE       = null;

    var $ORIGIN_PATHS   = [];
    var $PATHS          = [];

    var $TEMPLATE_PATHS = [];

    var $FORMATO_ORIGEN = "";

    var $STORE          = null;

    var $isTemplate     = false;

    var $cdata          = false;

    var $ESPECIFICO     = false;
    var $ESPECIFICO_FORMATO = "";
    var $isJsonVariable = false;

    var $isJson         = false;

    var $isHeader       = false;
    var $originFormat   = "JSON";

    function __construct($arguments = [])
    {
        $this->URL_INPUT     = $arguments["input"];
        $this->URL_TEMPLATE = isset($arguments["template"]) ? $arguments["template"] : "";
        $this->ORIGIN_PATHS = $arguments["paths"];

        $this->FORMATO_ORIGEN = $arguments["formato_origen"];

        $this->PATHS            = $this->_setChildPath();

        $this->TEMPLATE_PATHS = $this->_setChildPath("template");

        $this->_decodeDataURLS();

        $this->STORE            = new JsonStore();
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
        $this->INPUT    = $this->_decodeDataURL($this->URL_INPUT);
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

        //print_r($paths);

        $tree = [];


        if(($this->FORMATO_ORIGEN == "JSON" OR $this->FORMATO_ORIGEN == "JSONP") and $paths[0]["path"] !="")
        {
            //echo 'XXX';

            $new_childs[0] = $paths[0];
            return  $return = ["path" => "", "child" => $paths ];
            
            /*foreach($paths as $path)
            {
                if(substr_count($path["path"],$paths[0]["path"]) == 0 )
                    $new_childs[] = $path;

            }
            if(count($new_childs) > 1)
            {
                $return = ["path" => "", "child" => $new_childs ];
                return $return;
            }
            else
            {
                $return = $new_childs[0];
                return $return;
            }*/

        }else if(count($paths) > 2 and $paths[0]["path"] !="" ){

            //echo 'YYY';

            $new_childs = array();

            $new_paths = $paths;

            $new_childs[] = $paths[0];
            array_shift($paths);

            foreach($paths as $path)
            {
                array_shift($new_paths);

                $regex = $path["path"];
                if(!$this->in_array_match($regex, $new_paths) )
                    $new_childs[] = $path;

            }

            return $new_childs;

        }else
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



    private function in_array_match($regex, $array) {
        if (!is_array($array))
            trigger_error('Argument 2 must be array');
        foreach ($array as $v => $record) {
            //$match = preg_match($regex, $record["path"]);
            if (substr_count($record["path"],$regex) > 0) {
                return true;
                break;
            }
        }
        return false;
    }

    private function _extractData($record,$string) {

        $current_data = $record;
        $key = key($current_data);

        foreach ($string as $name) {

            if (key_exists($name, $current_data)) {
                return $current_data[$name];
            } else if(!is_numeric($key)){

                /*echo '<br>'.$key;
                echo '<br>'.$name .'<br>';
                print_r($current_data);
                print_r($current_data[$key][$name]);
                echo '<br><br>';*/

                //return $current_data[$key][$name];
                if(isset($current_data[$key][$name]))
                    return $current_data[$key][$name];
                else
                    return '';

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

        if($id_path > 0)
        {

            $apath = explode($path_parent.".",$node["path"]);
            $path  = $apath[count($apath)-1];

            //$path_parent = $path;

        }else
        {
            if(array_key_exists(0,$input))
            {
                $this->isJson = true;
                $path = "";
                if( isset($node[0]) and isset($node[0]["path"]) and $node[0]["path"] != "")
                    $node = ["path" => "", "child" => $node  ] ;
            }else
            {
                 if(array_key_exists(0,$node))
                     $node = $node[0];

                    $path = $node["path"];
            }


            $original_input = $input;
            $path_parent = $path;
        }

        $store = $this->STORE;

        if($path == "")
        {
            if($input and is_array($input) and array_key_exists(0,$input))
                $inputs = $input;
            else
            {
                $inputs[0] = $input;
            }
        }
        else{

            if($this->startsWith("media:",$path))
            {
                $path = preg_replace("/\[\*\]$/","",$path);

                if($path == "")
                    $inputs[0] =$input ;
                else
                    $inputs = $store->get($input, "$.".$path);


                if(array_key_exists(0,$inputs) and count($inputs) == 1)
                {
                    $input_key = key($inputs[0]);

                    //if(count($inputs[0][$input_key]) > 1 and is_array($inputs[0][$input_key]) and $input_key!="@attributes" )
                    //    $inputs = $inputs[0];
                }


            }else
            {
                $new_path = preg_replace("/\[\*\]$/","",$path);
                $inputs = $store->get($input, "$.".$new_path);

                if(count($inputs) == 0 or isset($inputs[0][0]) )
                    $inputs = $store->get($input, "$.".$path);
            }



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

            $key = key($inputs);
            if(isset($inputs[$key]) and is_array($inputs[$key]) and array_key_exists(0,$inputs[$key]))
            {

                $inputs = $inputs[0];

                if(is_array($inputs) and array_key_exists(0,$inputs))
                    $this->isJson = true;
            }

        }



        $tpath = count($paths)-1;


        if($id_path <= $tpath)
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
            }
            else
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
            $eval_especifico = $eval;

            if(!isset($this->ARRAY_EVAL[$last_eval.$property_eval]))
                $this->ARRAY_EVAL[$last_eval.$property_eval] = $i;
            else{

                $i_eval = $this->ARRAY_EVAL[$last_eval.$property_eval] + 1;


                $eval_especifico = $last_eval.$property_eval."['".$i_eval ."']";
                $this->ARRAY_EVAL[$last_eval.$property_eval] = $i_eval;
            }



            foreach ($node["child"] as $child_value => $child) {

                if(count($node["child"]) == 1)// extra
                    $eval = $eval2;// extra


                if(isset($child["child"]))
                {

                    if($path_parent and substr_count($path_parent, $path) == 0)
                        $path_parent = $path_parent.".".$path;



                    if(!$this->isJson and count($node["child"]) == 1 and $this->originFormat != "XML") // extra
                        $eval = $last_eval; // extra

                    $output = $this->__do($child,$record,$original_input,$id_path,$output,$path_parent,$eval,$last_eval,$i);
                }
                else
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
                    $key  = "['".implode("']['",$akey)."']";

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
                    //if($child["path"].".".$child["key"] != $child["value"] and $child["path"] != "")
                    if($this->ESPECIFICO)
                    {
                        $this->isTemplate = true;
                        //resources[*].resource[*].attributes[*].pubDate

                        preg_match_all('/\[(.*?)\]/', $eval_especifico, $matches);

                        //$last_iteration = $matches[1][0];
                       
                        if($this->ESPECIFICO_FORMATO == 'RSS'){
                            $last_iteration = $matches[1][1];
                        }
                        else
                        {
                            if($path == 'guid[*]')
                                $last_iteration = $matches[1][0];
                            else if ($path == '@attributes[*]')
                                $last_iteration = $matches[1][0];
                            else
                                $last_iteration = $matches[1][count($matches[1])-1];
                        }

                        //echo ' PATH_N '. $path . '<br>';
                        //echo ' PATH_P '. $path_parent . '<br>';

                        //$last_iteration = $matches[1][count($matches[1])-1];
                        //$last_iteration = intval($last_iteration);

                        //resource[*].attributes[*].pubDate
                        $eval_template = explode(".",$child["value"]);



                        $key_eval_template = array_search('item[*]', $eval_template);

                        if(is_numeric($key_eval_template))
                        {
                            unset($eval_template[$key_eval_template]);
                            $eval_template = array_values($eval_template);
                        }elseif(count($eval_template) > 1)
                            array_shift($eval_template);


                        $first_node_iteration = explode("[*]",$eval_template[0]);
                        $first_node_iteration = $first_node_iteration[0];

                       /*
                        if(isset($output[$first_node_iteration]))
                            $total_childs  = count($output[$first_node_iteration]);
                        else
                            $total_childs = 0;
                        */

                        $current_iteration = $last_iteration;

                        /*
                        if($last_iteration!=$total_childs)
                            $current_iteration = $total_childs + $last_iteration;

                        */

                        foreach($eval_template as $eval_template_value => $eval_template_record)
                        {

                            $eval_template_record = explode("[*]",$eval_template_record);


                            if($eval_template_record[0] == "item")
                            {

                            }
                            elseif(($this->isJsonVariable or $this->ESPECIFICO_FORMATO == "RSS" or $this->ESPECIFICO_FORMATO == "JSON" ))
                            {
                                $n_eval_template_record[0] = "[*]";
                                $n_eval_template_record[1] = "['".$eval_template_record[0]."']";

                                $eval_template_record = implode("",$n_eval_template_record);

                            }elseif( $eval_template_record > 1  or $this->isJsonVariable)
                            {


                                $eval_template_record[0] = "['".$eval_template_record[0]."']";
                                $eval_template_record[1] = "[*]";
                                $eval_template_record = implode("",$eval_template_record);
                            }else
                            {
                                $eval_template_record = "['".$eval_template_record[0]."']";
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

                    }
                    else
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


    private function getDate()
    {
        return date('D, d M Y H:i:s')." GMT";
    }



    private function _toXML($writer,$nodes,$parentKey,$kind = "",$attributes = [])
    {
        /*
        if(isset($nodes['media:group']))
        {    
             $tmp = $nodes['media:group'];
            foreach ($nodes as $key => $value) {
                if($key!='media:group'){
                    $tmp[$key] = $value;
                }
            }            
            $nodes=$tmp;
            $parentKey = 'media:group';
        }
        if(isset($nodes['media:content']))
        {   
            $tmp = $nodes['media:content'];
            foreach ($nodes as $key => $value) {
                if($key!='media:content'){
                    $tmp[$key] = $value;
                }
            }            
            $nodes=$tmp;
            $parentKey = 'media:content';
        }*/       

       
       foreach ($nodes as $nKey => $nValue) 
       { 

        if(is_array($nValue) && count($nValue) == 1 && !array_key_exists("@attributes", $nValue) )
           $nValue = $nValue[key($nValue)];
        if(is_array($nValue) && count($nValue) == 1 && !array_key_exists("@attributes", $nValue) )
           $nValue = $nValue[key($nValue)];

            $key = $parentKey;
            $value = $nValue;
            $isOpen = false;


            //echo 'PK:'.$parentKey.'  '.'KY:'.$nKey.'<br>';
            //echo 'Is Array:'.is_array($nValue).'<br>';

            if(!is_array($nValue))
            {
                $is_numeric = explode("x",$nKey);
                if(count($is_numeric) > 1 )
                {
                    if(is_numeric($is_numeric[0]) and is_numeric($is_numeric[1]))
                        $nKey ="_".$nKey;
                }

                if(is_numeric($nKey))
                {
                    $writer->startElement($key);
                    $writer->writeCData($nValue);
                    $writer->endElement();
                }
                else if(substr_count($nKey,"@") == 0)
                {
                    //echo 'Opening '.$nKey.'<br>';
                    $writer->startElement($nKey);
                    $writer->writeCData($nValue);
                    $writer->endElement();
                }
                
            }
            else
            {

                if(isset($nValue["@value"]))
                {
                    if(!is_numeric($nKey))
                    {
                        $writer->startElement($nKey);
                        $isOpen = true;
                    }
                    else
                    {
                        $writer->startElement($key);
                        $isOpen = true;
                    }

                    $writer->writeCData($nValue["@value"]);
                    unset($nValue["@value"]);

                    if($isOpen)
                    {
                        $writer->endElement();
                    }
                    
                    continue;
                }
                if(array_key_exists("@cdata", $nValue))
                {
                    if(!is_numeric($nKey))
                    {
                        $writer->startElement($nKey);
                        $isOpen = true;
                    }
                    else
                    {
                        $writer->startElement($key);
                        $isOpen = true;
                    }

                    $writer->writeCData($nValue["@cdata"]);
                    unset($nValue["@cdata"]);

                    if($isOpen)
                    {
                        $writer->endElement();
                    }

                    continue;
                }     



                if( !is_numeric($nKey))
                {
                    if($nKey!="@attributes")
                    {
                        $writer->startElement($nKey);    
                        $isOpen = true;
                    }
                    
                }
                else 
                {                    
                    if(!is_numeric($key) ){
                        
                        if($key == 'program')
                            $nKey = 'resource';
                        if($key == 'image_assets')
                            $nKey = 'image_asset';
                        
                        if(!is_numeric($nKey) )
                            $key = $nKey;

                        //echo 'PK:'.$key.'  '.'KY:'.$nKey.'<br>';
                       
                         
                        $writer->startElement($key);
                        $isOpen = true;                    


                    }
                    else {

                        //echo 'PK:'.$key.'  '.'KY:'.$nKey.'<br>';
                        $writer->startElement($nKey);                        
                        $isOpen = true;
                    }

                }

                if(array_key_exists("@attributes", $nValue)){
                    foreach ($nValue["@attributes"] as $katt => $vatt) {
                        foreach ($vatt as $kvatt => $vvatt) {
                            $writer->writeAttribute($kvatt, $vvatt);
                        }
                    }
                    unset($nValue["@attributes"]);
                }

                $this->_toXML($writer,$nValue,$nKey,$kind);

                if($isOpen)
                    $writer->endElement();

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
        $campos_orig    = is_array($feed) ? $feed : json_decode( $feed, TRUE );
        $campos         = [];

        if(!is_array($campos_orig))
            return [];

        $items          = count( $campos_orig );
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


        //echo '<br><br>';

        //print_r($paths);
        //print_r($input);


        $data = $this->__do($paths,$input);

        //print_r($data); die;


        if($this->isTemplate)
        {
            $template = $this->_getTemplate();
            if(count($template) > 0)
            {
                $key = key($template);

                $template = count($template) > 1 ? $template : $template[$key];

                if(array_key_exists(0,$template))
                    $template = $template[0];

                //print_r($data);
                $this->createEmptyChildren($data,$template);
            }

        }

        //print_r($data);
        //die;
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
                                //$key = key($child);
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

    public function setZero(&$_child)
    {


        foreach($_child as $key => $child)
        {
            if(is_array($child) and count($child) > 0) {
                $this->setZero($child);
            }else{
                $_child[0][$key] =  $child;
                unset($_child[$key]);
            }
        }
    }

    public function getDataFixed()
    {
        return $this->_fixkeys($this->getData());
    }

    public function _getHeaderRSS($url)
    {
        $data = file_get_contents_curl($url);
        $xml2array = xml2array( $data );

        $channel = $xml2array["rss"]["channel"];
        unset($channel["item"]);

        return $channel;
    }

    public function _setHeaderRSS($writer,$headers)
    {
        foreach($headers as $tag => $text)
        {
            $writer->startElement($tag);
            $writer->writeAttribute('isHeader', 'true');

            if(is_array($text))
                $this->_setHeaderRSS($writer,$text);
            else
                $writer->text($text);


            $writer->endElement();

        }
    }

    public function _headerRSS($writer, $attributes = [])
    {

        if($this->ESPECIFICO_FORMATO == "RSS")
        {
            $headers = $this->_getHeaderRSS($this->ESPECIFICO_URL_SALIDA);
            $this->_setHeaderRSS($writer,$headers);

        }else{

            $writer->startElement("title");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text(isset($attributes->title) ? $attributes->title : "televisa.com");
            $writer->endElement();

            $writer->startElement("link");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text(isset($attributes->link) ? $attributes->link : "http://www.televisa.com");
            $writer->endElement();

            $writer->startElement("description");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text(isset($attributes->description) ? $attributes->description : "El sitio número de internet de habla hispana con el mejor contenido de noticias, espectáculos, telenovelas, deportes, futbol, estadísticas y mucho más");
            $writer->endElement();

            $writer->startElement("image");
            $writer->writeAttribute('isHeader', 'true');
            $writer->startElement("title");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("televisa.com");
            $writer->endElement();

            $writer->startElement("link");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("http://i.esmas.com/img/univ/portal/rss/feed_1.jpg");
            $writer->endElement();

            $writer->startElement("link");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("http://www.televisa.com");
            $writer->endElement();
            $writer->endElement();

            $writer->startElement("language");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("es-mx");
            $writer->endElement();

            $writer->startElement("copyright");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("2005 Comercio Mas S.A. de C.V");
            $writer->endElement();

            $writer->startElement("managingEditor");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("ulises.blanco@esmas.net (Ulises Blanco)");
            $writer->endElement();

            $writer->startElement("webMaster");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("feeds@esmas.com (feeds Esmas.com)");
            $writer->endElement();

            $writer->startElement("pubDate");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text($this->getDate());
            $writer->endElement();

            $writer->startElement("lastBuildDate");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text($this->getDate());
            $writer->endElement();

            $writer->startElement("category");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("Home Principal esmas");
            $writer->endElement();

            $writer->startElement("generator");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("GALAXY 1.0");
            $writer->endElement();

            $writer->startElement("atom:link");
            $writer->writeAttribute('isHeader', 'true');
            $writer->writeAttribute( 'href', 'http://feeds.esmas.com/data-feeds-esmas/xml/index.xml' );
            $writer->writeAttribute( 'rel', 'self' );
            $writer->writeAttribute( 'type', 'application/rss+xml' );
            $writer->endElement();

            $writer->startElement("ttl");
            $writer->writeAttribute('isHeader', 'true');
            $writer->text("60");
            $writer->endElement();

        }
    }

    public function toRSS( $_nodes= [],$file = 'rss.xml', $encoding = 'UTF-8', $attributes = [] )
    {

        $nodes = $_nodes;

        $template = $this->_getTEMPLATE();
        $key = key($template);

        $writer = new XMLWriter();
        $writer->openURI( $file );
        $writer->startDocument( '1.0', $encoding );
        $writer->setIndent( 4 );
        
        $writer->startElement( 'rss' );
        //$this->move_to_bottom($nodes,"item");
        $writer->writeAttribute( 'version', '2.0' );
        $writer->writeAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );
        $writer->writeAttribute( 'xmlns:media', 'http://search.yahoo.com/mrss/' );
        $writer->writeAttribute( 'xmlns:atom','http://www.w3.org/2005/Atom' );
        $writer->writeAttribute( 'xmlns:itunes','http://www.itunes.com/dtds/podcast-1.0.dtd' );
        $writer->writeAttribute( 'xmlns:slash','http://purl.org/rss/1.0/modules/slash/' );
        $writer->writeAttribute( 'xmlns:rawvoice','http://www.rawvoice.com/rawvoiceRssModule' );


        if(isset($nodes['subcategories']))
            $nodes=$nodes['subcategories'];

        if(isset($nodes['resources']))
            $nodes=$nodes['resources'];

        if(isset($nodes['channel']))
            $nodes=$nodes['channel'];

        if(is_array($nodes) && count($nodes) == 1)
            $nodes = $nodes[key($nodes)];
        if(is_array($nodes) && count($nodes) == 1)
            $nodes = $nodes[key($nodes)];


        $writer->startElement( 'channel' );
        $this->_headerRSS($writer,$attributes);
            
        $this->_toXML( $writer, $nodes, 'item', 'rss' );

        $writer->endElement();

        $writer->endDocument();
        $writer->flush();


        $doc         = new DOMDocument();
        $doc->load( $file );
        $xpath       = new DOMXPath($doc);
        $resources   = $xpath->evaluate("/rss/channel")->item(0);
        
        $domElemsToRemove = array();
        foreach ($resources->childNodes as $resource) {
            if($resource->nodeName!='item' && $resource->nodeName!='resource' && $resource->nodeName!='resources' && $resource->nodeName!='channel' && $resource->nodeType==1 && !$resource->hasAttribute('isHeader') )
            {
                $domElemsToRemove[] = $resource;
            }
            else if($resource->nodeName=='#cdata-section'){
                $domElemsToRemove[] = $resource;
            }
        }

        if(count($domElemsToRemove)>0){
            $newresource = $doc->createElement('item');
            $resources->appendChild($newresource);           
            foreach ($resources->childNodes as $resource) {
                if($resource->nodeName!='item' and $resource->nodeType==1 && !$resource->hasAttribute('isHeader'))
                {
                    $xpath->evaluate("/rss/channel")->item(0)->lastChild->appendChild($resource->cloneNode(true));
                }
                else if($resource->nodeName=='#cdata-section'){
                    $xpath->evaluate("/rss/channel")->item(0)->lastChild->appendChild($resource->cloneNode(true));
                }
            }
            
            foreach( $domElemsToRemove as $domElement ){ 
              $domElement->parentNode->removeChild($domElement); 
            } 

            $doc->save($file);
        }



    }

    public function toXML( $nodes = [], $file = 'xml.xml', $encoding = 'UTF-8' ){
        $template = $this->_getTEMPLATE();
        $this->isHeader = false;
        $key = key($template);

            $writer = new XMLWriter();
            $writer->openURI( $file );
            //$writer->startDocument( '1.0', "UTF-8" );
            $writer->startDocument( '1.0', $encoding );
            $writer->setIndent( 4 );
            $paths = $this->_getPaths();

            //echo 'KEY: '.$key.'<br>';
            $openResources = true;


            if(isset($nodes['subcategories']))
                $nodes=$nodes['subcategories'];

            if(isset($nodes['resources']))
                $nodes=$nodes['resources'];
            if(isset($nodes['channel']))
                $nodes=$nodes['channel'];

            if(is_array($nodes) && count($nodes) == 1)
                $nodes = $nodes[key($nodes)];
            if(is_array($nodes) && count($nodes) == 1)
                $nodes = $nodes[key($nodes)];


            if($openResources)
            {  
                $writer->startElement("resources");
                $writer->writeAttribute('xmlns:media','http://search.yahoo.com/mrss/');
            }

                $this->_toXML( $writer, $nodes, 'resource', 'xml' );

            if($openResources)
                $writer->endElement();

            $writer->endDocument();
            $writer->flush();

    }

    public function _toJSON( &$nodes )
    {

        foreach ($nodes as $nKey => $nValue) {
            if ( is_array( $nValue ) and array_key_exists(0,$nValue) ){

                foreach($nValue as $keynValue => $recordnValue)
                {
                    if( isset( $recordnValue["@attributes"] ) or  isset( $recordnValue["@value"] ) )
                    {

                        if ( isset( $recordnValue["@attributes"] ) and isset( $recordnValue["@value"] ) ){
                            $attributes = $recordnValue["@attributes"];
                            $value = $recordnValue["@value"];

                            foreach ( $attributes as $katt => $vatt ) {
                                foreach ( $vatt as $kvatt => $vvatt )
                                    $nValue[$keynValue][$kvatt] = $vvatt;
                            }
                            $nValue[$keynValue][$value] = $value;

                            unset($nValue[$keynValue]["@attributes"]);
                            unset($nValue[$keynValue]["@value"]);
                            //unset($nValue[0]);

                        }else if( isset( $recordnValue["@value"] ) ){
                            if(array_key_exists(0,$nValue))
                              $nValue = $recordnValue["@value"];  
                            else
                                $nValue[$keynValue] = $recordnValue["@value"];

                        }elseif($recordnValue["@attributes"])
                        {
                            $attributes = $recordnValue["@attributes"];

                            foreach ( $attributes as $katt => $vatt ) {
                                foreach ( $vatt as $kvatt => $vvatt )
                                    $nValue[$keynValue][$kvatt] = $vvatt;
                            }

                            unset($nValue[$keynValue]["@attributes"]);
                        }

                        //unset($nValue[$keynValue]);
                    }else
                        $this->_toJSON( $recordnValue );
                }



            }elseif ( is_array( $nValue ) and count( $nValue ) > 0 ){
                $this->_toJSON( $nValue );
            }
        }
    }

    public function toJSON( $data = [], $file = 'json.json', $function = '' ){
        $this->_toJSON($data);
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
