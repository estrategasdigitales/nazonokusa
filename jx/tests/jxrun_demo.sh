
FILEREL="../../samples/inputs/match_lineup.js"
FILEABS=`php -r "echo realpath('$FILEREL');"`

php ../jxrun.php -i $FILEABS -r js2xml >testout