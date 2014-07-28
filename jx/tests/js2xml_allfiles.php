<?php

require "../recipes/js2xml.php";

$debug = false;

function process_file($filename, $filepath) {
    global $debug;

    echo "\n\n================ $filename ================\n\n";

    $jstr = file_get_contents($filepath);
    $jwrapper = jsonp_wrapper($jstr);
    $j = json_decode(jsonp_unwrap($jstr));

    if ($debug) {
        var_export($j);
    }

    $jt = new js2xml;
    $jt->process($j);
    print $jt->emit();

}

function test_sample_inputs() {
    $basedir = '../../samples/inputs';
    $files = array('deportes.js', 'telenovelas.js', 'tdfutmex.js', 'jornada_3793.js',
                   'match_lineup.js', 'match_mxm.js', 'TickerFutbol_12.js');
    foreach ($files as $index => $file) {
        process_file($file, "$basedir/$file");
    }

    $count = count($files);
    echo "\n\n================ DONE $count CONVERSIONS ================\n\n";

}

// Run the test N times
$options = getopt("n:");
$run_times = $options['n'];
foreach (range (1, $run_times) as $run_number) {
    test_sample_inputs();
}

?>
