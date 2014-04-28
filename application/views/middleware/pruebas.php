<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
<?php $contenido = file_get_contents("http://static-televisadeportes.esmas.com/sportsdata/futbol/data/332/jornadas/jornada_3793.js"); 
$data_array = json_decode($contenido,true);
print_r($data_array); echo$_SERVER['HASH_ENCRYPT']; ?>
</html>