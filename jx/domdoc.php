<?php

/**
 * Object-oriented simplification of PHP DOMDocument for constructing
 * XML trees. Also, some utility functions for parsing or constructing said
 * trees.
 */

require_once __DIR__ . '/util.php';

class DOMDocException extends DOMException {}

class DOMDoc extends DOMDocument {

    public $namespaces = array();

    public function __construct($version='1.0', $encoding='UTF-8') {
        parent::__construct($version, $encoding);
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
        $xpathobj = new DOMXPath($this);
        try {
            return $xpathobj->query($query);
        }
        catch (Exception $e) {
            throw new DOMDocException("failed to match xpath '$query'");
        }
    }

    /**
     * Run the given XPath query, return the first result
     */

    function xpathone($query) {
        $result = $this->xpath($query);
        foreach ($result as $r) { return $r; }
        return null;
    }



    /**
     * set the text of the given elemnt to the given string
     */

    function setText($element, $str) {
        $newtext = $this->createTextNode($str);
        $element->normalize();
        $element->replaceChild($newtext, $element->firstChild);
    }


    /**
     * Make the given xpath real in the given DOMDoc tree. Accepts
     * only a very limited subset of xpath expressions--specifically,
     * absolute paths that extend all the way from the root to the desired
     * node. Only tag names may be specified, not indices. Intended
     * for tree construction not querying. Order that nodes added is important.
     */

    function add_tree($xpath, $value=null, $attribs=null) {
        $results = $this->xpath($xpath);
        $xpathparts = pathparts($xpath);

        if (!xresults($results)) {   // must build to this point
            if (count($xpathparts) > 1) {
                $allbutlast = array_slice($xpathparts, 0, count($xpathparts)-1);
                $here = $this->add_node_parts($allbutlast);
            }
            else {
                $here = $this;
            }
            $this->addElement($here, end($xpathparts), $value, $attribs);
        }
        else {
            if (startsWith(end($xpathparts), "@")) {
                $allbutlast = array_slice($xpathparts, 0, count($xpathparts)-1);
                $results = $this->xpath($allbutlast);
                foreach (iterator_to_array($results) as $here) {
                    $this->addAttrib($here, end($xpathpaths), $value);
                }
            }
            else {
                foreach (iterator_to_array($results) as $here) {
                    $this->addElement($here, $value, $attribs);
                }
            }
        }

    }
    /**
     * Given an array of xpath components, make sure the nodes
     * exist in the tree.
     */

    function add_node_parts($parts) {
        $xpath = makepath($parts);
        $results = $this->xpath($xpath);
        if ($results->length === 0) {
            if (count($parts) > 1) {
                $prev = $this->add_node_parts(array_slice($parts, 0, count($parts)-1));
            }
            else {
                $prev = null;
            }
            $where = $prev ? $prev : $this;  // where to add node?
            $here = $this->addElement($where, end($parts));
            return $here;
        }
        else {        // else path exists already; pass it back
            return iterator_to_array($results)[0];

        }
    }


}

/**
 * Are there xpath results?
 */

function xresults($results) {
    return ($results !== null) && ($results->length > 0);
}

/**
 * Unwrap CDATA-wrapped content into just the content
 */

function de_CDATA($s) {
    $start = strpos($s, "<![CDATA[");
    if ($start !== false) {
        $end = strrpos($s, "]]>");
        return substr($s, $start + 9, $end-$start-9);
    }

}

/**
 * Wrap content in CDATA markup
 */

function CDATA($s) {
    return "<![CDATA[$s]]>";
}


?>