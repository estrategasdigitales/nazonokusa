<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Recuperación de contraseña</title>
</head>
<body>
	<table align="left" border="0" cellpadding="0" cellspacing="0" width="600px">
		<tr>
			<td style="padding-bottom:10px;">Hola, Solicitaste el envío de tu contraseña a ésta dirección de correo, te sugerimos no compartirla con nadie y guardarla en un lugar seguro.</td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;">Contraseña: <b><?php echo $contrasena; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;">Este mensaje fué remitido automáticamente, por favor no lo reenvíes.</td>
		</tr>
	</table>
</body>
</html>