<?php

/**
 * Home of conversion foundation classes, functions, and exceptions
 */

require_once __DIR__ . "/domdoc.php";  // XML DOMDocument helpers
require_once __DIR__ . "/util.php";    // utility functions

class ConversionException extends UnexpectedValueException {}
class EmptyInput extends ConversionException {}
class NullData extends ConversionException {}
class ParseError extends ConversionException {}

/**
 * Top level class for all JS <-> XML conversions
 */

class JX_Recipe {

    // steps to take before and after transformation (but before emitting)
    public $preprocessors  = array();
    public $postprocessors = array();

    function preprocess() {
        foreach ($this->preprocessors as $p) {
            $p($this);
        }
    }

    function postprocess() {
        foreach ($this->postprocessors as $p) {
            $p($this);
        }
    }

}


/**
 * Top level class for JS -> XML conversions.
 *
 */

class J2X_Recipe extends JX_Recipe {

    # Generalized support for JX conversion.

    public $source = null;      # the original source, if availbale
    public $tree = null;        # DOM tree

    function __construct($input=null, $version='1.0', $encoding='UTF-8') {
        if ($input === null) {
            return;
        }
        if (is_string($input)) {
            $this->process_string($input, $version, $encoding);
        }
        else {
            $this->process($input, $version, $encoding);
        }
    }


    /**
     * Process string contents. Create the XML tree structure.
     */

    function process_string($content, $version='1.0', $encoding='UTF-8') {
        if (!$content) {
            throw new EmptyInput("empty content provided to process_string");
        }
        $this->source = $content;
        $jsonp_tag = jsonp_wrapper($content);
        $json_content = $jsonp_tag ? jsonp_unwrap($content) : $content;
        $json_data = json_decode($json_content);
        if (!json_data) {
            throw new NullData("json_decode returns nothing in process_string");
        }
        $this->process($json_data, $version, $encoding);
    }


    /**
     * Process the data node. Create the XML tree structure
     * in $this->tree as it goes alog.
     */

    function process($node, $version='1.0', $encoding='UTF-8') {

        if (!$node) {
            throw new EmptyInput("no data provided to process");
        }
        else {
            $this->node = $node;
        }

        $this->tree = new DOMDoc($version, $encoding);

        $this->preprocess();
        $this->topnode($node, $this->tree);
        $this->postprocess();
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
        return $this->tree->addElement($dom, $tag, $node->{$tag});
    }

    /**
     * In many cases, the value in JS can be simply copied into
     * an XML element. This generalizes that process like copyDirect.
     * But it also copies everything. It's simple, but brute.
     */

    function copyAll($node, $dom) {
        $keys = array_keys(get_object_vars($node));
        foreach ($keys as $key) {
            $this->tree->addElement($dom, $key, $node->{$key});
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

    public $source = null;      # the original source, if availbale
    public $tree = null;        # DOM tree
    public $jsonp_wrap = null;  # JSONP wrapping call, if desired

    function __construct($input=null, $jsonp_wrap=null) {
        if ($input === null) {
            return;
        }
        if ($is_string($input)) {
            $this->process_string($input, $jsonp_wrap);
        }
        else {
            $this->process($input, $jsonp_wrap);
        }
    }

    /**
     * Process a string into a dom, then make JS out of it.
     */

    function process_string($content, $jsonp_wrap=null, $assoc=false) {
        if (!$content) {
            throw new EmptyInput("empty content provided to process_string");
        }
        $this->source = $content;
        $tree = DOMDoc::loadXML($content);
        if ($tree === false) {
            throw new ParseError("could not parse XML");
        }
        $this->process($tree, $jsonp_wrap, $assoc);
    }


    /**
     * Process the dom.
     */

    function process($dom, $jsonp_wrap=null, $assoc=false) {
        $this->jsonp_wrap = $jsonp_wrap;
        $this->assoc = $assoc;

        if (!$dom) {
            throw new NullData("no tree given to process");
        }
        $this->tree = $dom;

        $this->preprocess();
        $this->topnode();
        $this->postprocess();
    }

    function emit() {
        return json_encode($this->node);
    }

}

?>