<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Televisa Interactive Media - Middleware</title>
	<?php echo link_tag('css/middleware.css'); ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.form.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/spin.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/middleware.js"></script>
</head>
<body>
	<div id="foo"></div>
	<div id="messages"></div>
	<div id="container">
		<?php $attr = array('id'=>'read_feed_form', 'name'=>'read_feed_form', 'method'=>'POST', 'autocomplete'=>'off'); ?>
		<?php echo form_open('middleware/read_feed', $attr); ?>
			<label>Feed: </label>
			<input type="text" placeholder="Introduce la URL del feed aquí" id="url_feed" name="url_feed" value="<?php echo set_value('url_feed'); ?>">
			<input type="submit" value="Leer" name="leer_feed">
		<?php echo form_close(); ?>
		<div>
			<?php if(isset($response)) print_r($response); ?>
		</div>
	</div>
</body>
</html>