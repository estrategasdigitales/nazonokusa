<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Televisa Interactive Media - Middleware</title>
</head>
<body>
	<span><?php echo validation_errors(); ?></span>
	<?php $attr = array('id'=>'read_feed_form', 'name'=>'read_feed_form', 'method'=>'POST', 'autocomplete'=>'off'); ?>
	<?php echo form_open('middleware/read_feed', $attr); ?>
		<label>Feed: </label>
		<input type="text" placeholder="Introduce la URL del feed aquí" id="url_feed" name="url_feed" value="<?php echo set_value('url_feed'); ?>">
		<input type="submit" value="Leer" name="leer_feed">
	<?php echo form_close(); ?>
	<div>
		<?php if(isset($response)) echo $response; ?>
	</div>
</body>
</html>