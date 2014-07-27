<?php

require_once(__DIR__ . "/../jxbase.php");


/**
 * A generalized JS to XML recipe
 */

class js2xml extends J2X_Recipe {

    public $listitems = array();
    public $top_node_name = null;

    function topnode($node, $dom, $lastkey=null) {
        if ($lastkey !== null) {
            $this->top_node_name = $lastkey;
        }

        $this->anynode($node, $dom, $lastkey);
    }

    function anynode($node, $dom, $lastkey) {

        if (!$lastkey) {
            $lastkey = $this->top_node_name;
            if (!$lastkey) {
                $lastkey = "root";
            }
        }

        if (is_array($node)) {
            if (key_exists($lastkey, $this->listitems)) {
                $item_tag = $this->listitems[$lastkey];
            }
            else {
                $item_tag = $lastkey . "-item";
            }
            foreach ($node as $index => $item) {
                $elt = addElement($dom, xtag($item_tag));
                $this->anynode($item, $elt, $item_tag);
            }
        }
        else {
            foreach (properties($node) as $value) {
                $value_of_value = $node->{$value};
                if (isScalar($value_of_value)) {
                    $elt = addElement($dom, xtag($value), $value_of_value);
                }
                else {
                    $elt = addElement($dom, xtag($value));
                    $this->anynode($value_of_value, $elt, $value);
                }
            }
        }
    }

}

?>