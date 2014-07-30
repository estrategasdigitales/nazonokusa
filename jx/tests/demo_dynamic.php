<?php

require_once __DIR__ . "/../recipes/dynamic.php";

function dynamic_run($name, $jstr, $recipe, $expected) {
    echo "\n\n### $name\n\n";
    $converter = new dynamic_js2xml($recipe);
    $converter->process_string($jstr);
    $outtext = $converter->emit();
    echo "\n", $outtext;
    echo "\n\n", str_repeat("-", 50), "\n\n";
}

/**
 * Basic example. Shows dynamic, data-driven conversion based on a
 * recipe that could be provided in code, from a database, from a
 * JSON configuration file, etc. Note that some data goes into
 * standard XML nodes, while others deposited in XML attributes.
 * Note also structure of output data doesn't necessarily match
 * that of input. Description field, e.g., is dropped.
 */

function demo_simple() {

    $jstr = <<<END_JSON

         {
             "songs": {
                 "tracks" : [
                     {
                         "path": "song.mp3",
                         "title": "Song One",
                          "description": "And then there was this <b>thing</b>!"

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

    dynamic_run(__FUNCTION__, $jstr, $recipe, $xstr_target);

}



/**
 * Demonstrate proper downsampling of HTML embedded in JSON so that output XML will be valid
 */

function demo_HTML() {

    $jstr = <<<END_JSON

         {
             "songs": {
                 "tracks" : [
                     {
                         "path": "song.mp3",
                         "title": "Song One",
                         "description": "And then there was this <b>thing</b>!"
                     }
                  ]
             }
         }
END_JSON;


    $recipe = array(
        array('/songs/tracks/[]/path', '/resources/resource/@src'),
        array('/songs/tracks/[]/title', '/resources/resource/song-name'),
        array('/songs/tracks/[]/description', '/resources/resource/message')
    );


    $xstr_target = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<resources><resource src="song.mp3"><song-name>Song One</song-name><message>And then there was this &lt;b&gt;thing&lt;/b&gt;!</message></resource></resources>
END_XML;

    dynamic_run(__FUNCTION__, $jstr, $recipe, $xstr_target);
}


/**
 * Demonstrate special CDATA handling instructions
 */

function demo_CDATA() {

    $jstr = <<<END_JSON

         {
             "songs": {
                 "tracks" : [
                     {
                         "path": "song.mp3",
                         "title": "Song One",
                         "description": "And then there was this <b>thing</b>!"
                     }
                  ]
             }
         }
END_JSON;


    $recipe = array(
        array('/songs/tracks/[]/path', '/resources/resource/@src'),
        array('/songs/tracks/[]/title', '/resources/resource/song-name'),
        array('/songs/tracks/[]/description', '/resources/resource/message', 'CDATA')
    );


    $xstr_target = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<resources><resource src="song.mp3"><song-name>Song One</song-name><message><![CDATA[And then there was this <b>thing</b>!]]></message></resource></resources>
END_XML;

    dynamic_run(__FUNCTION__, $jstr, $recipe, $xstr_target);
}


/**
 * Demonstrate handling of multiple items from anonymous JSON arrays
 * NOTE: NOT WORKING. Depends on completion of anon array handling code.
 */


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

    dynamic_run(__FUNCTION__, $jstr, $recipe, $xstr_target);
}

demo_simple();
demo_html();
demo_CDATA();

// demo_array();

?>