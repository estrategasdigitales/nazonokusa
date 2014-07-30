<?php

require_once(__DIR__ . "/../jxbase.php");


/**
 * A generalized JS to XML recipe
 */

class xml2js extends X2J_Recipe {

    public $listitems = array();

    public $top_node_name = null;

    function topnode() {


        $this->node = json_encode($this->tree, $this->assoc);
    }

}

?>