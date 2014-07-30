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

    $converter = new dynamic_js2xml($recipe);
    $converter->process_string($jstr);
    $outtext = $converter->emit();
    echo $outtext;

}

demo_simple();

?>