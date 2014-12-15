<?php

require_once __DIR__ . '/../domdoc.php';
require_once __DIR__ . '/../util.php';

class Test_DOMDoc extends PHPUnit_Framework_TestCase
{

    public function test_one()
    {

        $x = new DOMDoc;
        $r = $x->addElement($x, "rubber", "sticks");
        $x->addElement($r, "glue", "Elmers");

        $xstr = trim($x->saveXML());

        $target_xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<rubber>sticks<glue>Elmers</glue></rubber>
END_XML;
        $target_xstr = trim($target_xstr);
        $this->assertEquals($xstr, $target_xstr);

    }

    public function test_two()
    {

        $x = new DOMDoc('1.0', 'UTF-8');
        $x->addNS('content', 'http://purl.org/rss/1.0/modules/content/');

        $assets = $x->addElement($x, 'assets');
        $tracks = $x->addElement($assets, 'tracks', null,
                    array('content:media' => 'slorp', 'blip' => 'slip' ));

        $currentTrack = $x->addElement($tracks, 'track');
        $x->addElement($currentTrack, 'path', 'song.mp3');
        $x->addElement($currentTrack, 'title', 'Title One');

        $currentTrack = $x->addElement($tracks, 'track');
        $x->addElement($currentTrack, 'path', 'song2.mp3');
        $x->addElement($currentTrack, 'title', 'Title Two');
        $xstr = trim($x->saveXML());

        $target_xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<assets xmlns:content="http://purl.org/rss/1.0/modules/content/"><tracks content:media="slorp" blip="slip"><track><path>song.mp3</path><title>Title One</title></track><track><path>song2.mp3</path><title>Title Two</title></track></tracks></assets>
END_XML;
        $target_xstr = trim($target_xstr);

        $this->assertEquals($xstr, $target_xstr);
    }

    public function test_CDATA_basic()
    {
        $before = '***';
        $x = CDATA($before);
        $y = de_CDATA($x);

        $this->assertEquals($x, "<![CDATA[***]]>");
        $this->assertEquals($y, $before);
    }

    public function test_CDATA_random_string()
    {

        $length = rand(10, 300);
        $before = generateRandomString($length);
        $x = CDATA($before);
        $y = de_CDATA($x);

        $this->assertEquals(strpos($x, "<![CDATA["), 0);
        $this->assertEquals(strrpos($x, "]]>"), strlen($x)-3);
        $this->assertEquals($y, $before);
    }


    public function test_add_node_parts() {
        $t = new DOMDoc;

        $t->add_node_parts(array('one'));
        $this->assertEquals(line2($t->saveXML()), "<one/>");

        // intended repetition; should be idempotent
        $t->add_node_parts(array('one'));
        $this->assertEquals(line2($t->saveXML()), "<one/>");

        $t->add_node_parts(array('one', 'two'));
        $this->assertEquals(line2($t->saveXML()), "<one><two/></one>");

        // intended repetition; should be idempotent
        $t->add_node_parts(array('one', 'two'));
        $this->assertEquals(line2($t->saveXML()), "<one><two/></one>");

        $t->add_node_parts(array('one', 'two', 'three'));
        $this->assertEquals(line2($t->saveXML()), "<one><two><three/></two></one>");

        $t->add_node_parts(array('one', 'more', 'time'));
        $this->assertEquals(line2($t->saveXML()), "<one><two><three/></two><more><time/></more></one>");

    }

    public function test_xresults() {
        $xml = "<one><two><three>3</three><three type='roman'>iii</three><three>tres</three></two></one>";
        $d = new DOMDoc;
        $d->loadXML($xml);

        $this->assertEquals(xresults($d->xpath('//two')), true);
        $this->assertEquals(xresults($d->xpath('//three')), true);
        $this->assertEquals(xresults($d->xpath('//four')), false);
    }
    
}

/**
 * Helper function to return the second line of a string.
 * Useful in testing XML results, where the first line is usually
 * an XML header, and the second line is the actual XML content.
 */

function line2($s) {
    $lines = split("\n", $s);
    return $lines[1];
}

?>
