<?php

/**
 * Home of conversion foundation classes, functions, and exceptions
 */

require "fdom.php";  // pull in XML helpers
require "util.php";  // pull in PHP utility functions

class ConversionException extends UnexpectedValueException {}

/**
 * Top level class for all JS <-> XML conversions
 */

abstract class JX_Recipe { }


/**
 * Top level class for JS -> XML conversions.
 *
 */

class J2X_Recipe extends JX_Recipe {

    # Generalized supprot for JX conversion.

    public $tree = null;        # DOM tree
    public $xpathobj = null;    # For XPath access

    function __construct($node=null, $version='1.0', $encoding='UTF-8') {
        if ($node === null) {
            return;
        }
        $this->process($node, $version, $encoding);
    }


    /**
     * Process ras wring contents. Create the XML tree structure.
     */

    function process_string($content, $version='1.0', $encoding='UTF-8') {


        $jsonp_tag = jsonp_wrapper($content);
        $json_content = $jsonp_tag ? jsonp_unwrap($content) : $content;
        $j = json_decode($json_content);
        $this->process($j, $version, $encoding);
    }


    /**
     * Process the data node. Create the XML tree structure.
     */

    function process($node, $version='1.0', $encoding='UTF-8') {
        global $domtree;
        $this->tree = new DOMDocument($version, $encoding);
        $this->xpathobj = new DOMXpath($this->tree);

        $domtree = $this->tree;
        $this->topnode($node, $this->tree);
    }

    /**
     * Require the existence of the given property in the JS data
     */

    function need_property($node, $property_name) {
        if (!property_exists($node, $property_name)) {
            throw new ConversionException("$property_name required, but not found");
        }
    }


    /**
     * In many cases, the value in JS can be simply copied into
     * an XML element. This generalizes that process.
     */

    function copyDirect($node, $dom, $tag) {
        return addElement($dom, $tag, $node->{$tag});
    }

    /**
     * In many cases, the value in JS can be simply copied into
     * an XML element. This generalizes that process like copyDirect.
     * But it also copies everything. It's simple, but brute.
     */

    function copyAll($node, $dom) {
        $keys = array_keys(get_object_vars($node));
        foreach ($keys as $key) {
            addElement($dom, $key, $node->{$key});
        }
    }

    /**
     * Emit the final XML document
     */

    function emit() {
        return $this->tree->saveXML();
    }

    /**
     * Soup-to-nuts processing of the given JS node.
     * Emits result to standard output.
     */

    function run($node) {
        $this->process($node);
        return $this->emit();
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
        $newtext = $this->tree->createTextNode($str);
        $element->normalize();
        $element->replaceChild($newtext, $element->firstChild);
    }
}

/**
 * Provide an XML-compatible tag name. If starts with an numeric
 * character, prefix with an "n".
 */

function xtag($s) {
    if (is_numeric(substr($s, 0, 1))) {
        return "n" . $s;
    }
    else {
        return $s;
    }
}

// Full definition of XML tag names. xtag above is only a start at homoenizing
// JSON and XML.

//NameStartChar   ::=
//    ":" | [A-Z] | "_" | [a-z] | [#xC0-#xD6] | [#xD8-#xF6] | [#xF8-#x2FF] |
//    [#x370-#x37D] | [#x37F-#x1FFF] | [#x200C-#x200D] | [#x2070-#x218F] |
//    [#x2C00-#x2FEF] | [#x3001-#xD7FF] | [#xF900-#xFDCF] | [#xFDF0-#xFFFD] |
//    [#x10000-#xEFFFF]
//
//NameChar    ::=
//    NameStartChar | "-" | "." | [0-9] | #xB7 | [#x0300-#x036F] | [#x203F-#x2040]


function jsonp_wrapper($jsonp) {
    if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
        // we have JSONP
       return substr($jsonp, 0, strpos($jsonp, '('));
    }
    else {
        // standard JSON
        return $jsonp;
    }
}

function jsonp_unwrap($jsonp) {
    if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
        // we have JSONP
        $start = strpos($jsonp, '(');
        $end = strrpos($jsonp, ')');
        return substr($jsonp, $start+1, $end-$start-1);
    }
    else {
        // standard JSON
        return $jsonp;
    }
}


/**
 * Base class for XML ->  JS recipes
 */

class X2J_Recipe extends JX_Recipe {

}

?>