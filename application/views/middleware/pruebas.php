<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Televisa Interactive Media - Middleware</title>
	<?php echo link_tag('css/bootstrap.min.css'); ?>
	<?php echo link_tag('css/jquery-ui-1.10.4.custom.css'); ?>
	<?php echo link_tag('css/middleware.css'); ?>
	<?php echo link_tag('css/middleware.css'); ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery-ui-1.10.4.custom.js"></script>
	<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.form.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/spin.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/middleware.js"></script>
</head>
<!--head>
<title>jsonp example</title>
<script src="<?php echo base_url(); ?>js/jquery-1.11.0.js"></script>
<script type="text/javascript">
window.onload = function()
{
  jsonp('http://static-televisadeportes.esmas.com/sportsdata/futbol/data/332/jornadas/jornada_3793.js');
};
 
function jornada(data)
{

	console.log(data);
	var items = [];
	$.each( data, function( key, val ) {
		items.push( "<li id='" + key + "'>" + val.visit.name + "</li>" );
	});
	$( "<ul/>", {
		"class": "my-new-list",
		html: items.join( "" )
	}).appendTo( "body" );
}

function jsonp(url)
{
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = url;
 
  var head = document.getElementsByTagName('head')[0];
  head.appendChild(script);
}
</script>
</head-->
<body>
	<nav class="usuarios">
		<div class="container">
	<?php
	$contenido = file_get_contents("http://feeds.esmas.com/data-feeds-esmas/xml/fotos_dep_tdfutbol.xml");	
	echo $contenido;

	$xml = simplexml_load_string($contenido);
	print_r($xml);
		$url=file_get_contents("http://static-televisadeportes.esmas.com/sportsdata/futbol/data/332/jornadas/jornada_3793.js"); // url de la pagina que queremos obtener  
		$pos = strpos($url, '(');
			$funcion=substr($url, 0, $pos+1);
			$rest = substr($url, $pos+2, -2);
			$original = substr($url, $pos+1, -1);
			$json= json_decode($original, true);
			$jsonIterator = new RecursiveIteratorIterator(
				new RecursiveArrayIterator(json_decode($rest, TRUE)),
				RecursiveIteratorIterator::SELF_FIRST);
			$array=[];
			$arbol=[];
			$espacio="col-sm-offset-0 col-md-offset-0 col-sm-12 col-md-12";
			$final=[];
			$offset=0;
			$col=12;
			ordenar($jsonIterator,$offset,$col);
			function ordenar($array,$offset,$col){
				foreach ($array as $key => $value) {
					if(is_array($value)) { ?>
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="categoria[]" value="<?php echo $key; ?>">
								<?php echo $key ?>
							</label>
						</div>
					</div>
						<?php ordenar($value,$offset+1,$col-1);
					} else { ?>
					<div class="<?php echo 'col-sm-offset-'.($offset).' col-md-offset-'.($offset).' col-sm-'.($col).' col-md-'.($col) ?>">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="categoria[]" value="<?php echo $key; ?>">
								<?php echo $key ?>
							</label>
						</div>
					</div>
					<?php }
				}
			}
			foreach ($array as $key => $value) {
				echo "campo : ".$value."<br>";
			}
			foreach ($arbol as $key => $value) {
				echo "$key: ".$value."<br>";
			}
		foreach ($json as $value) {
			unset($value['tournament']);
			$final[]=$value;
		}
		$open = fopen("/home/edigitales/www/televisa.middleware/application/views/middleware/prueba.php", "w");
		$cadena = $funcion.(json_encode($final)).")";
		$remplaza= stripslashes($cadena);
		fwrite($open, $remplaza);
		fclose($open);
		?>
	</div>
	</nav>
	<footer>
		
	</footer>
</body>
</html>