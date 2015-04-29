<?php

	declare_tcpdf();
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	
	date_default_timezone_set('Asia/Manila');
	$filename = $details['filename'];
	$title = $details['title'];

	class MyTCPDF extends TCPDF {
	
		public function Footer(){
			$this->SetY(-15);
			$this->SetFont('helvetica','',10);
			$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}

	
	$pdf = new MyTCPDF("L", "mm", 'LEGAL', true, 'UTF-8', false);
	
	$pdf->SetTitle($title);

	$pdf->SetHeaderData('clickLogo.png', 40, $title, REPORTS_HEADER_SUBJECT);
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));


	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

	$pdf->SetTopMargin(25);
	$pdf->SetFooterMargin(10);
	$pdf->SetAutoPageBreak(true);
	$pdf->SetDisplayMode('real','default');
	// $pdf->setPrintHeader(true);
	$pdf->setPrintFooter(true);

		$header = array(
					"Store",
					"Guest",
					"Prep Time (mins)",
					"Del Time (mins)",
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

						foreach($list['list'] as $k=>$r)
						{
							$pdf->AddPage();
							$rep_content = "<h3>Date: As of ".  date('M d, Y', strtotime($k))."</h3>";
							$CI->make->sTable(array('border'=>'1px', 'cellpadding'=>'1px','class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
							$CI->make->sRow();
								foreach($header as $hr)
									$CI->make->th($hr, array("style"=>"background-color:red;color: white;text-align: center;", ));
							$CI->make->eRow();
							    $withData = false;
							    foreach($r as $key=>$val)
					            {

					            	if(!empty($val))
					            	{
					            		$withData = true;
										foreach($val as $col=>$item)
						            	{
						            		if(!empty($item))
						            		{
								            	$CI->make->sRow();
													$CI->make->td($item->branch_code);
													$CI->make->td(ucwords($item->cust_name));
													$CI->make->td($item->prep_time);
													$CI->make->td($item->delivery_time);
													$CI->make->td($item->datetime);	
													$CI->make->td($item->_confirmation_sent);
													$CI->make->td($item->d_post_confirmed); //duration agent post - confirmation sent
													$CI->make->td($item->_process_time);
													$CI->make->td($item->d_confirmed_process); //duration confirmation sent - process time
													$CI->make->td($item->_done_process_time);
													$CI->make->td($item->_delivered_time);
													$CI->make->td($item->d_dispatch_delivery); //duration dispatch - delivered

													$total_duration = strtotime('00:00:00');
													if(!is_null($item->d_post_confirmed))
														$total_duration+=(strtotime($item->d_post_confirmed));
													if(!is_null($item->d_confirmed_process))
														$total_duration+=(strtotime($item->d_confirmed_process));
													if(!is_null($item->d_dispatch_delivery))
														$total_duration+=(strtotime($item->d_dispatch_delivery));

															$total_duration = date('H:i:s',$total_duration);
															$CI->make->td($total_duration);

														if(!is_null($item->d_dispatch_delivery))
														{
															$time = explode(':', $item->d_dispatch_delivery);
															$total_minutes = (($time[0]*3600) + ($time[1]*60) + $time[2])/60;

															if($total_minutes > $item->delivery_time)
																$CI->make->td('<span style="color: red;">MISS</span>');
															else
																$CI->make->td('<span style="color: blue;">HIT</span>');
														}else{
															$CI->make->td('',array('style'=>'background-color: #008402;'));	
														}
											 	$CI->make->eRow();	
							            			
						            		}
						            	}
					            	 }
					            }
					            if($withData == false)
					            {
									$CI->make->sRow();
							            $CI->make->td('No available data.', array('colspan'=>(count($header))));
							     	$CI->make->eRow();
								}
					        $CI->make->eTable();

							$rep_content .= $CI->make->code();
							$pdf->writeHTML($rep_content,true,false,false,false,'');
						}
					
		$pdf->Output($details['filename'], 'I');	
?>