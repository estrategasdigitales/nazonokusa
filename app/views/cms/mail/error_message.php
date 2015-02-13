<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Error en Middleware</title>
</head>
<body>
	<table align="left" border="0" cellpadding="0" cellspacing="0" width="600px">
		<tr>
			<td style="padding-bottom:10px;">Ha ocurrido un problema en el trabajo...</td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">Nombre: <b><?php echo $name_job; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">ID: <b><?php echo $uid_job; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">Categoría: <b><?php echo $name_category; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">Vertical: <b><?php echo $name_vertical; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;">Fecha y Hora: <b><?php echo $time; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">Detalles: <b><?php echo $message; ?></b></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px">Para más detalles, da clic <a href="<?php echo base_url(); ?>/trabajos.html">aquí</a></td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;">Este mensaje fué remitido automáticamente, por favor no lo reenvíes.</td>
		</tr>
	</table>
</body>
</html>