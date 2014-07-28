<?php

/**
 * Object-oriented simplification of PHP DOMDocument for constructing
 * XML trees. Also, some utility functions for parsing or constructing said
 * trees.
 */

class DOMDoc extends DOMDocument {

    public $namespaces = array();
    public $xpathobj = null;

    public function __construct($version='1.0', $encoding='UTF-8') {
        parent::__construct($version, $encoding);
        $this->xpathobj = new DOMXPath($this);
    }

    public function addNS($ns, $uri) {
        $this->namespaces[$ns] = $uri;
    }

    function addElement($node, $tag, $value=null, $attribs=null) {
        $newNode = $this->createElement($tag, $value);
        $node->appendChild($newNode);
        if ($attribs !== null) {
            foreach ($attribs as $attname => $attvalue) {
                $this->addAttrib($newNode, $attname, $attvalue);
            }
        }
        return $newNode;
    }

    function addAttrib($node, $name, $value) {
        $colon = strpos($name, ':');
        if ($colon === false) { # standard attribute
            $attrib = $this->createAttribute($name);
            $attrib->value = $value;
        }
        else {                  # namespaced attribute
            $ns = substr($name, 0, $colon);
            $attrib = $this->createAttributeNS($this->namespaces[$ns], $name);
            $attrib->value = $value;
        }
        return $node->appendChild($attrib);
    }

    /**
     * Run the given XPath query, return the result
     */

    function xpath($query) {
        return $this->xpathobj->query($query);
    }

    /**
     * set the text of the given elemnt to the given string
     */

    function setText($element, $str) {
        $newtext = $this->createTextNode($str);
        $element->normalize();
        $element->replaceChild($newtext, $element->firstChild);
    }
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


/**
 * Parse the given path into an array of path componetns
 */

function _pathparts($path) {
    if (startsWith($path, "/")) {
        $path = substr($path, 1, strlen($path)-1);
    }
    $parts = split("/", $path);
    return $parts;
}


/**
 * Make the given xpath real in the given DOMDocument tree. Accepts
 * only a very limited subset of xpath expressions--specifically,
 * absolute paths that extend all the way from the root to the desired
 * node. Only tag names may be specified, not indices. Intended
 * for tree construction not querying. Order that nodes added is important.
 */

function dom_add_tree($tree, $xpath, $value=null, $attribs=null) {
    $xpathobj = new DOMXpath($tree);
    $xparts = _pathparts($xpath);
    $traversed = array();
    $cursor = $tree;
    $xprefix = '/';
    foreach ($xparts as $xpart) {
        $xprefix = "$xprefix/$xpart";
        $results =  $xpathobj->query($xprefix);
        if ($results !== null) {
            $cursor = $results[0];
        }
        else {
            $cursor = "TO BE DONE";
        }
    }
}


?>