<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
<?php function jsonp_decode($jsonp, $assoc = false) { // PHP 5.3 adds depth as third parameter to json_decode
    if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
       $jsonp = substr($jsonp, strpos($jsonp, '('));
    }
    return json_decode(trim($jsonp,'();'), $assoc);
}

$contenido = file_get_contents("http://static-televisadeportes.esmas.com/sportsdata/futbol/data/332/jornadas/jornada_3793.js");
$data = jsonp_decode($contenido);
print_r($data);
?>
</body>
</html>