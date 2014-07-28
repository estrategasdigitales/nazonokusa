<?php

/**
 * Functional simplification of PHP DOMDocument for constructing
 * XML trees. Also, some utility functions for parsing or constructing said
 * trees.
 */

$domtree = null;
$namespaces = array();

function addNS($ns, $uri) {
    global $namespaces;
    $namespaces[$ns] = $uri;
}

function addElement($node, $tag, $value=null, $attribs=null) {
    global $domtree;
    $newNode = $domtree->createElement($tag, $value);
    $node->appendChild($newNode);
    if ($attribs !== null) {
        foreach ($attribs as $attname => $attvalue) {
            addAttrib($newNode, $attname, $attvalue);
        }
    }
    return $newNode;
}

function addAttrib($node, $name, $value) {
    global $domtree;
    global $namespaces;
    $colon = strpos($name, ':');
    if ($colon === false) { # standard attribute
        $attrib = $domtree->createAttribute($name);
        $attrib->value = $value;
    }
    else {                  # namespaced attribute
        $ns = substr($name, 0, $colon);
        $attrib = $domtree->createAttributeNS($namespaces[$ns], $name);
        $attrib->value = $value;
    }
    return $node->appendChild($attrib);
}


function de_CDATA($s) {
    $start = strpos($s, "<![CDATA[");
    if ($start !== false) {
        $end = strrpos($s, "]]>");
        return substr($s, $start + 9, $end-$start-9);
    }

}

function CDATA($s) {
    return "<![CDATA[$s]]>";
}

?>