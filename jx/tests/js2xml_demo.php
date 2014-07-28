<?php

// best run as `php js2xml_allfiles.php >testout` else the character IO will eat you alive

require "../recipes/js2xml.php";

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

$j = json_decode($jstr);
var_export($j);

echo "\n\n================\n\n";

$jt = new js2xml;

// set up custom sub items for tracks
$jt->listitems['tracks'] = 'track';

// process the JS data
$jt->process($j);

// custom post processing using XPath searches
foreach ($jt->xpath('//path') as $element) {
    $jt->setText($element, strtoupper($element->textContent));
}

print $jt->emit();


echo "\n\n================\n\n";

?>