<?php
	declare_PHPExcel();
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	date_default_timezone_set('Asia/Manila');
	
	$filename = $details['filename'];
	$title = $details['title'];

	$objPHPExcel = new PHPExcel();

	$header = array(
				"Store",
				"Guest",
				"Prep Time",
				"Del Time",
				"Agent Post",
				"Time Confirmed",
				"Duration", //duration agent post - confirmation sent
				"Process", 
				"Duration", //Confirmed - process
				"Dispatch",
				"Delivered",
				"Duration",
				"Total Duration",
				"STATS"
			);

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
					'wrap'  => false,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
               )
		);
		$style['empty'] =  array(
				'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => '008402'),
		            )
				);

		$activesheet = 0;
		$row=2;
		foreach($list['list'] as $k=>$r)
		{
			$objPHPExcel->setActiveSheetIndex($activesheet);
			$objPHPExcel->getActiveSheet()->setTitle($k);
			
			$col = 0; $row = 4;	
			foreach($header as $hr)
			{

				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $hr);
				$col++;
			}

			$h_col = $objPHPExcel->getActiveSheet()->getHighestColumn();
			
			for ($col = 'A'; $col <= $h_col; $col++) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}

	    	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A', 1, $title);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A', 2, REPORTS_HEADER_SUBJECT);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A', 3, 'As of '. date("M d, Y", strtotime($k)));


			$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$h_col.'1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:'.$h_col.'2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:'.$h_col.'3');


			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$h_col.'1')->applyFromArray($style['header']);
			$objPHPExcel->getActiveSheet()->getStyle('A2:'.$h_col.'2')->applyFromArray($style['subheader']);
			$objPHPExcel->getActiveSheet()->getStyle('A3:'.$h_col.'3')->applyFromArray($style['subheader']);
	    	$objPHPExcel->getActiveSheet()->getStyle("A".($row).":".$h_col.($row))->applyFromArray($style['headings']);
	    
	    	
	    	$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($activesheet);
	
		// //End: Header

			$row = 5;
		    $withData = false;
			foreach($r as $key=>$val)
			{
				$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);	
				
            	if(!empty($val))
            	{
            		$withData = true;
            		foreach($val as $cols=>$item)
		            {
		            	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $item->branch_code);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, ucwords($item->cust_name));
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $item->prep_time);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $item->delivery_time);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $item->datetime);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $item->_confirmation_sent);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $item->d_post_confirmed);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $item->_process_time);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $item->d_confirmed_process);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $item->_done_process_time);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $item->_delivered_time);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $item->d_dispatch_delivery);

						$total_duration = strtotime('00:00:00');
						if(!is_null($item->d_post_confirmed))
							$total_duration+=(strtotime($item->d_post_confirmed));
						if(!is_null($item->d_confirmed_process))
							$total_duration+=(strtotime($item->d_confirmed_process));
						if(!is_null($item->d_dispatch_delivery))
							$total_duration+=(strtotime($item->d_dispatch_delivery));

						$total_duration = date('H:i:s',$total_duration);
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $total_duration);
											
						if(!is_null($item->d_dispatch_delivery))
						{
							$time = explode(':', $item->d_dispatch_delivery);
							$total_minutes = (($time[0]*3600) + ($time[1]*60) + $time[2])/60;

							if($total_minutes > $item->delivery_time)
							{
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, 'MISS');
							}	
							else
							{
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, 'HIT');
							}	

						}else{
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '');
							$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13, $row)->applyFromArray($style['empty']);
						}
						 $row++;
		            }
		        }
		      
		        
			}

			if($withData == false){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'No transaction.');
	        	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.$h_col.$row);
			}

			$dimension = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
			$objPHPExcel->getActiveSheet()->getStyle($dimension)->applyFromArray($style['worksheet']);
			$activesheet++;
		}	 

	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=$filename.xls");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	// declare_tcpdf();
	// $CI =& get_instance();
	// $CI->load->model('site/site_model');
	// date_default_timezone_set('Asia/Manila');
	
	// $filename = $details['filename'];
	// $title = $details['title'];

	// 	$header = array(
	// 				"Store",
	// 				"Guest",
	// 				"Prep Time",
	// 				"Del Time",
	// 				"Agent Post",
	// 				"Time Confirmed",
	// 				"Duration", //duration agent post - confirmation sent
	// 				"Process", 
	// 				"Duration", //Confirmed - process
	// 				"Dispatch",
	// 				"Delivered",
	// 				"Duration",
	// 				"Total Duration",
	// 				"STATS"
	// 			);

	// 			$CI->make->sTable(array('border'=>'1px', 'width'=>'90%','cellpadding'=>'1px','class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
	// 				$CI->make->sRow(array('style'=>'font-size: 20px; font-weight: bold;'));
	// 		            $CI->make->td($title, array('style'=>'text-align:center;','colspan'=>(count($header))));
	// 		     	$CI->make->eRow();
	// 		     	$CI->make->sRow();
	// 		            $CI->make->td(REPORTS_HEADER_SUBJECT, array('style'=>'text-align:center;','colspan'=>(count($header))));
	// 		     	$CI->make->eRow();
				     	
	// 				$CI->make->sRow( array('style'=>'height: 25px; font-size: 15px; font-weight: bold;'));
	// 					foreach($header as $hr)
	// 						$CI->make->th($hr, array("style"=>"background-color:red;color: white;text-align: center;", "witdh"=>"55%"));
	// 				$CI->make->eRow();

	// 				foreach($list['list'] as $k=>$r)
	// 				{
	// 		            foreach($r as $key=>$val)
	// 		            {
	// 						$CI->make->sRow(array('style'=>'font-size: 14px;'));
	// 							$CI->make->td($val->branch_code);
	// 							$CI->make->td(ucwords($val->cust_name));
	// 							$CI->make->td($val->prep_time);
	// 							$CI->make->td($val->delivery_time);
	// 							$CI->make->td($val->datetime);	
	// 							$CI->make->td($val->_confirmation_sent);
	// 							$CI->make->td($val->d_post_confirmed); //duration agent post - confirmation sent
	// 							$CI->make->td($val->_process_time);
	// 							$CI->make->td($val->d_confirmed_process); //duration confirmation sent - process time
	// 							$CI->make->td($val->_done_process_time);
	// 							$CI->make->td($val->_delivered_time);
	// 							$CI->make->td($val->d_dispatch_delivery); //duration dispatch - delivered

	// 							$total_duration = strtotime('00:00:00');
	// 							if(!is_null($val->d_post_confirmed))
	// 								$total_duration+=(strtotime($val->d_post_confirmed));
	// 							if(!is_null($val->d_confirmed_process))
	// 								$total_duration+=(strtotime($val->d_confirmed_process));
	// 							if(!is_null($val->d_dispatch_delivery))
	// 								$total_duration+=(strtotime($val->d_dispatch_delivery));

	// 								$total_duration = date('H:i:s',$total_duration);
	// 								$CI->make->td($total_duration);

	// 							if(!is_null($val->d_dispatch_delivery))
	// 							{
	// 								$time = explode(':', $val->d_dispatch_delivery);
	// 								$total_minutes = (($time[0]*3600) + ($time[1]*60) + $time[2])/60;
	// 								if($total_minutes > $val->delivery_time)
	// 									$CI->make->td('<span style="color: red;">MISS</span>');
	// 								else
	// 									$CI->make->td('<span style="color: blue;">HIT</span>');
	// 							}else{
	// 								$CI->make->td('',array('style'=>'background-color: #008402;'));	
	// 							}
	// 			 			$CI->make->eRow();
	// 		            }
	// 				}
				
	// 			$CI->make->eTable();
	// 		// echo $CI->make->code();
	// 		$rep_content = $CI->make->code();



	// ob_start();
	// echo $rep_content;
	// $output = ob_get_clean();
	
	// header("Content-type: application/x-msdownload; charset=UTF-8"); 
	// header("Content-Disposition: attachment; filename=$filename.xls");
	// header("Pragma: no-cache"); 
	// header("Expires: 0"); 
	// echo $output;
		
?>