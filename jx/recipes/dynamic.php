<?php


require_once(__DIR__ . "/../jxbase.php");
require_once(__DIR__ . "/../findnode.php");


class dynamic_js2xml extends J2X_Recipe {

    public $recipe = null;

    function __construct($recipe, $version='1.0', $encoding='UTF-8') {
        parent::__construct(null, $version, $encoding);
        $this->recipe = $recipe;
    }

    function topnode($node, $dom) {
        if ($this->recipe === null) {
            throw new NullData("no recipe provided");
        }
        foreach ($this->recipe as $instruction) {
            list($datapath, $xpath, $extras) = $instruction;
            echo "datapath: $datapath xpath: $xpath extras: $extras\n";
            $dnode = findnode($datapath, $node);
            var_dump($dnode);
            if (is_scalar($dnode)) {
                $xpathparts = pathparts($xpath);
                $xpathtail = end($xpathparts);

                if (startsWith($xpathtail, "@")) {
                    $elt_xpathparts = array_slice($xpathparts, 0, count($xpathparts)-1);
                    $elt_xpath = "/" . join('/', $elt_xpathparts);
                    $attname = substr($xpathtail, 1, strlen($xpathtail)-1);
                    $elt = $dom->add_tree($elt_xpath, null, array($attname => $dnode));
                    // $dom->addAttrib($elt, $attname, $dnode);
                }
                else {
                    $dom->add_tree($xpath, $dnode);
                }
            }
            echo "---\n";
        }
        echo "about to convert\n";

    }
}



    $recipe = array(
        // array('/songs', '/resources'),
        // array('/songs/tracks/[]', '/resources/resource'),
        array('/songs/tracks/[]/path', '/resources/resource/@src'),
        array('/songs/tracks/[]/title', '/resources/resource/song-name'),
    );


?>