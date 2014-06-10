<?php $this->load->view( 'cms/header' ); ?>
	<?php echo form_open( 'cms/validar_form_categoria', array('class' => 'form-horizontal', 'id' => 'form_categoria_nueva', 'method' => 'POST', 'role' => 'form', 'autocomplete' => 'off' ) ); ?>
		<div class="row">
			<div class="col-sm-8 col-md-8"><h4>Nueva Categoría</h4></div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="form-group">
					<label for="nombre" class="col-sm-4 col-md-4 control-label">Nombre de la Categoría</label>
					<div class="col-sm-8 col-md-8">
						<input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" placeholder="Nombre de la Categoría">
					</div>
				</div>
				<div class="form-group">
					<label for="path_categoria" class="col-sm-4 col-md-4 control-label">Path para la Categoría</label>
					<div class="col-sm-8 col-md-8">
						<input type="text" class="form-control" id="path_categoria" name="path_categoria" placeholder="Path para la Categoría">
					</div>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-2 col-md-2"></div>
			<div class="col-sm-4 col-md-4">
				<a href="<?php echo base_url(); ?>categorias" type="button" class="btn btn-danger btn-block">Cancelar</a>
			</div>
			<div class="col-sm-4 col-md-4">
				<input style="padding:8px;" type="submit" class="btn btn-success btn-block" value="Enviar"/>
			</div>
			<div class="col-sm-2 col-md-2"></div>
		</div>
	<?php echo form_close(); ?>
<?php $this->load->view( 'cms/footer' ); ?>