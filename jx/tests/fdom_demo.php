<?php

require '../fdom.php';

/**
 * Demonstration of raw XML tree building, absent any
 * recipe-oriented functions
 */

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


function properties($obj, $as_string=false) {
    $keys = array_keys(get_object_vars($obj));
    return $as_string ? join(", ", $keys) : $keys;
}


$j = json_decode($jstr);
var_export($j);
print "\n\n\n===============\n\n\n";
print $j->{'rss'}->{'tracks'}[0]->{'title'} . "\n";

print "properties(j): " .properties($j, true) . "\n";

print "\n===\n";


$domtree = new DOMDocument('1.0', 'UTF-8');

addNS('content', 'http://purl.org/rss/1.0/modules/content/');

$xmlRoot = addElement($domtree, 'rss', null,
                        array('version' => '2.0'));

$tracks = addElement($xmlRoot, 'tracks', null,
                        array('content:media' => 'slorp',
                              'blip' => 'slip' ));

$currentTrack = addElement($tracks, 'track');
addElement($currentTrack, 'path', 'song.mp3');
addElement($currentTrack, 'title', 'title of song1.mp3');

$currentTrack = addElement($tracks, 'track');
addElement($currentTrack, 'path', 'song2.mp3');
addElement($currentTrack, 'title', 'title of song2.mp3');

/* get the xml printed */
echo $domtree->saveXML();


?>