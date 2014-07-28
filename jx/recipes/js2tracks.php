<?php

require_once(__DIR__ . "/../jxbase.php");

/**
 * Example of a custom JS to XML recipe class.
 * Manages the conversion of every stage.
 */

class js2tracks extends J2X_Recipe {

    function topnode($node, $dom) {
        $this->rss($node, $dom);
    }

    function rss($node, $dom) {
        $this->need_property($node, 'rss');
        $xmlRoot = addElement($dom, 'rss', null, array('version' => '2.0'));
        $this->tracks($node->{'rss'}, $xmlRoot);
    }

    function tracks($node, $dom) {
        $this->need_property($node, 'tracks');
        $tracksElt = addElement($dom, 'tracks');
        foreach ($node->{'tracks'} as $trackObj) {
            // note insertion of track tag as replacement for
            // anonymous array element in JS source
            $trackElt = addElement($tracksElt, 'track');
            $this->track($trackObj, $trackElt);
        }
    }

    function track($node, $dom) {
        $this->copyAll($node, $dom);

        // $this->copyDirect($node, $dom, 'path');
        // $this->copyDirect($node, $dom, 'title');

        // The copyDirect calls do the trick with less restatement
        // of the 'path' and 'title' strings. Still a lot of $node
        // and $dom restatement...but you do what you can with the
        // time available. Statements below are the older, more
        // explicit way.

        // addElement($dom, 'path', $node->{'path'});
        // addElement($dom, 'title', $node->{'title'});

        // Example transformation
        // $this->copyDirect($node, $dom, 'path');
        // addElement($dom, 'song-title', $node->{'title'});

    }

}

?>