<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** PHPExcel */
require_once( __DIR__ . '/excel_pdf/PHPExcel.php' );

/** PHPExcel_IOFactory */
require_once( __DIR__ . '/excel_pdf/PHPExcel/IOFactory.php' );

class Excel_pdf_manager {
	function import( $filename ){

	}

	function export( $table ){
		$objPHPExcel = new PHPExcel(); //creando un objeto excel
		$objPHPExcel->getProperties()->setCreator("Sistema de Administración de Tareas y Contenidos para Middleware"); //propiedades
		$objPHPExcel->setActiveSheetIndex(0); //poniendo active hoja 1
		$objPHPExcel->getActiveSheet()->setTitle("Hoja1");//título de la hoja 1

		//llenando celdas
		$column = 0;
		$row = 1;
		if ( is_array( $table ) ){
			foreach ( $table as $record ){
				foreach ( $record as $value ){
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( $column, $row, $value );
					$column++;
				}
				$column = 0;
				$row++;
			}
		}

		//poniendo en negritas la fila de los títulos
		$styleArray = array( 'font' => array( 'bold' => true ) );
		$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')-> applyFromArray( $styleArray );  

		//poniendo columnas con tamaño auto según el contenido, asumiendo N como la última
		for ( $i = 'A'; $i<= 'N'; $i++)
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true); 
		//código de exportar (ver artículo antes mencionado)
	}
}