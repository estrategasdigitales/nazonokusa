<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view( 'cms/header' ); ?>
	<div class="row">
		<div class="col-sm-8 col-md-8"><h4>Histórico de reportes</h4></div>
		<div class="col-sm-4 col-md-4"><a href="<?php base_url(); ?>nuevo_reporte" type="button" class="btn btn-primary btn-block">Nuevo Reporte</a></div>
	</div>
	<br>
	<div class="row">
		<div class="container table-responsive">
			<table class="table table-striped table-hover table-bordered">
				<tr class="titulo-columna">
					<td>Nombre del reporte</td>
					<td>Fecha de creación</td>
					<td>Rango de fechas</td>
					<td>Descargar</td>
					<!--<td>Enviar</td>-->
				</tr>
				<?php if ( isset( $reportes ) && ! empty( $reportes ) ){
					foreach ( $reportes as $reporte ){ ?>
						<tr>
							<td><?php echo $reporte->nombre_reporte; ?></td>
							<td><?php echo unix_to_human( $reporte->fecha ); ?></td>
							<td><?php echo unix_to_human( $reporte->fecha_inicio ); ?> - <?php echo unix_to_human( $reporte->fecha_fin ); ?></td>
							<td class="text-center file-export">
								<a href="<?php echo base_url(); ?>generar_reporte_pdf?token=<?php echo base64_encode( $reporte->uid_reporte ); ?>" title="PDF" class="btn" target="_blank"><i class="fa fa-file-pdf-o"></i></a></a>
								<a href="<?php echo base_url(); ?>generar_reporte_csv?token=<?php echo base64_encode( $reporte->uid_reporte ); ?>" title="CSV" class="btn" target="_blank"><i class="fa fa-file-text-o"></i></a></a>
								<a href="<?php echo base_url(); ?>generar_reporte_xls?token=<?php echo base64_encode( $reporte->uid_reporte ); ?>" title="XLS" class="btn" target="_blank"><i class="fa fa-file-excel-o"></i></a></a>
							</td>
							<!--<td class="text-center file-export">
								<a href="#" title="Enviar" class="btn"><i class="fa fa-envelope-o"></i></a>
							</td>-->
						</tr>
				<?php }
				} else { ?>
					<tr>
						<td colspan="5">No existen reportes</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<br>
	<div class="row">
        <div class="col-xs-12">
            <div id="paginacion">
            	<?php echo $links; ?>
            </div>
        </div>
    </div>
    <br>
	<div class="row">
		<div class="col-sm-8 col-md-8"></div>
		<div class="col-sm-4 col-md-4">
			<a href="<?php base_url(); ?>inicio" type="button" class="btn btn-success btn-block">Volver al Menú Principal</a>
		</div>
	</div>
	<div id="dialogConfirm"><span id="spanMessage"></span></div>
<?php $this->load->view( 'cms/footer' ); ?>