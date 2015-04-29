<?php

	declare_PHPExcel();
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	date_default_timezone_set('Asia/Manila');
	
	$filename = $details['filename'];
	$title = $details['title'];

	$objPHPExcel = new PHPExcel();

		$style['header'] = array(
		    'font' => array(
		        'name' => 'Arial',
		        'color' => array(
		            'rgb' => '333'
		        ),
		        'bold' => true,
		        'size'  => 13,
		    ),
		);
		$style['subheader'] = array(
		    'font' => array(
		        'name' => 'Arial',
		        'color' => array(
		            'rgb' => '333'
		        ),
		        'bold' => true,
		        'size'  => 10,
		    ),
		);
		$style['headings'] = array(
		   'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => 'BD0A0A'),
	        ),
		   'borders' => array(
		          'allborders' => array(
		              'style'  => PHPExcel_Style_Border::BORDER_THIN,
		              'color'  => array(
		              			'rgb' => '808080'),
		          )
		      ),
		    'font' => array(
		        'name' => 'Arial',
		        'color' => array(
		            'rgb' => 'fafafa'
		        ),
		        'bold' => true
		    ),
		);
		$style['worksheet'] =  array(
		     'borders' => array(
		          'allborders' => array(
		              'style'  => PHPExcel_Style_Border::BORDER_THIN,
		              'color'  => array(
		              			'rgb' => '808080'),
		          )
		      ),
		      'alignment' => array(
					'wrap'  => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
               )
		);

		$reasons_complaint = array(
			"Wrong Delivery",
			"Product Quality",
			"Incomplete Delivery",
			"Late Delivery",
			"Others"
		);

		
			if(!empty($list['list']))
			{
				$activesheet = 0;
				$row=2;
				foreach($list['list'] as $k=>$r)
				{
					//Start: Set up header table

						$objPHPExcel->setActiveSheetIndex($activesheet);
						$objPHPExcel->getActiveSheet()->setTitle($k);

						$row = 3;	$col = 0;
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Store');
					    
					    foreach($reasons_complaint as $key=>$value) {
					    	$col++;
					        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
					    }

					    $col++;

					    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total # Of Occurences');


				    	$h_col = $objPHPExcel->getActiveSheet()->getHighestColumn();
				    	
				    	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A', 1, $title);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A', 2, REPORTS_HEADER_SUBJECT);
						
						$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$h_col.'1');
						$objPHPExcel->getActiveSheet()->mergeCells('A2:'.$h_col.'2');
						$objPHPExcel->getActiveSheet()->getStyle('A1:'.$h_col.'1')->applyFromArray($style['header']);
						$objPHPExcel->getActiveSheet()->getStyle('A2:'.$h_col.'2')->applyFromArray($style['subheader']);
				    	
				    	$objPHPExcel->getActiveSheet()->getStyle("A".($row).":".$h_col.($row))->applyFromArray($style['headings']);
				    	
				    	for ($col = 'A'; $col <= $h_col; $col++) {
							$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
						}

				    	$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
						$objPHPExcel->createSheet();
						$objPHPExcel->setActiveSheetIndex($activesheet);
				
					// //End: Header

						$activesheet++;
						$row = 4;
						foreach($r as $key=>$val)
						{
							$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);	
							$col = 0;
						  		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key);
									$col++;
						            $occur=0;
						            foreach($reasons_complaint as $reason)
									{
										if(isset($val[$reason]))
										{
											$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val[$reason]);
											$occur+=$val[$reason];
										}	
										else
											$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
										$col++;
									}
									 $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $occur);
							 $row++;

				    	}
				    	$dimension = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
				    	$objPHPExcel->getActiveSheet()->getStyle($dimension)->applyFromArray($style['worksheet']);
			    }
			}

		//End: Body

	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=$filename.xls");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

?>