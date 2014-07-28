<?php

// best run as `php js2xml_allfiles.php >testout` else the character IO will eat you alive

require __DIR__ . "/../recipes/js2xml.php";

$jstr = <<<END_JSON

     {
         "rss": {
             "tracks" : [
                 {
                     "path": "song.mp3",
                     "title": "Song One"
                 },
                 {
                     "path": "song2.mp3",
                     "title": "Song Two"
                 }
             ]
         }
     }
END_JSON;


class Test_js2xml extends PHPUnit_Framework_TestCase
{

    public function test_one()
    {
        global $jstr;

        $target_xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<rss><tracks><tracks-item><path>song.mp3</path><title>Song One</title></tracks-item><tracks-item><path>song2.mp3</path><title>Song Two</title></tracks-item></tracks></rss>
END_XML;
        $target_xstr = trim($target_xstr);
        // note stock conversion of tracks items to tag `tracks-item`
        // this is correct; it is the default if the recipe does not state a better conversion

        $converter = new js2xml;
        $converter->process_string($jstr);
        $xstr = trim($converter->emit());

        $this->assertEquals($xstr, $target_xstr);
    }

    public function test_two()
    {
        global $jstr;

        $target_xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<rss><tracks><track><path>song.mp3</path><title>Song One</title></track><track><path>song2.mp3</path><title>Song Two</title></track></tracks></rss>
END_XML;
        $target_xstr = trim($target_xstr);
        // now we have a prettier XML item tag

        $converter = new js2xml;
        $converter->listitems['tracks'] = 'track';
        // explicit naming of what tracks array items should be called

        $converter->process_string($jstr);
        $xstr = trim($converter->emit());

        $this->assertEquals($xstr, $target_xstr);
    }

    public function test_three()
    {
        global $jstr;

        $target_xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<rss><tracks><track><path>song.mp3</path><title>SONG ONE</title></track><track><path>song2.mp3</path><title>SONG TWO</title></track></tracks></rss>
END_XML;
        $target_xstr = trim($target_xstr);
        // now we have a prettier XML item tag

        $converter = new js2xml;
        $converter->listitems['tracks'] = 'track';
        // explicit naming of what tracks array items should be called


        $converter->process_string($jstr);

        // but now we add custom post processing using XPath search
        // and a setText helper to upper-case the titles
        foreach ($converter->tree->xpath('//title') as $element) {
            $converter->tree->setText($element, strtoupper($element->textContent));
        }

        $xstr = trim($converter->emit());

        $this->assertEquals($xstr, $target_xstr);
    }
}

?>
