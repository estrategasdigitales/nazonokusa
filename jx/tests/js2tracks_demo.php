<?php


require_once "../recipes/js2tracks.php";

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

// You can run the conversion manually...

// $jt = new js2tracks;
// $jt->process($j);
// print $jt->emit();

// But easier to do it in one shot...

echo (new js2tracks($j))->emit();

?>