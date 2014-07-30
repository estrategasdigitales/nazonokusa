<?php

require_once __DIR__ . "/../recipes/xml2js.php";


$xstr = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
<rss><tracks><track><path>song.mp3</path><title>SONG ONE</title></track><track><path>song2.mp3</path><title>SONG TWO</title></track></tracks></rss>
END_XML;

$xstr = trim($xstr);

$converter = new xml2js;

$converter->process_string($xstr);

$outtext = $converter->emit();
echo $outtext;

?>