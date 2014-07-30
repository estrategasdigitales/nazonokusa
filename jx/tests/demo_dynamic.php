<?php

require_once __DIR__ . "/../recipes/dynamic.php";

function demo_simple() {

    $jstr = <<<END_JSON

         {
             "songs": {
                 "tracks" : [
                     {
                         "path": "song.mp3",
                         "title": "Song One"
                     }

                 ]
             }
         }
END_JSON;


    $recipe = array(
        array('/songs/tracks/[]/path', '/resources/resource/@src'),
        array('/songs/tracks/[]/title', '/resources/resource/song-name'),
    );


    $xstr_target = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<resources><resource src="song.mp3"><song-name>Song One</song-name></resource></resources>
END_XML;

    echo "\n\n### ", __FUNCTION__, "\n\n";
    $converter = new dynamic_js2xml($recipe);
    $converter->process_string($jstr);
    $outtext = $converter->emit();
    echo "\n", $outtext;
    echo "\n\n", str_repeat("-", 50), "\n\n";

}


function demo_array() {

    $jstr = <<<END_JSON

         {
             "songs": {
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


    $recipe = array(
        array('/songs/tracks/[]/path', '/resources/resource/@src'),
        array('/songs/tracks/[]/title', '/resources/resource/song-name'),
    );


    $xstr_target = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<resources><resource src="song.mp3"><song-name>Song One</song-name><resource src="song2.mp3"><song-name>Song Two</song-name></resource></resources>
END_XML;

    echo "\n\n### ", __FUNCTION__, "\n\n";
    $converter = new dynamic_js2xml($recipe);
    $converter->process_string($jstr);
    $outtext = $converter->emit();
    echo "\n", $outtext;
    echo "\n\n", str_repeat("-", 50), "\n\n";

}

demo_simple();


demo_array();

?>