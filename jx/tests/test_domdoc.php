<?php

require __DIR__ . '/../domdoc.php';
require __DIR__ . '/../util.php';


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

}

?>
