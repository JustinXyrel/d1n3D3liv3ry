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

	
	$pdf = new MyTCPDF("P", "mm", 'LEGAL', true, 'UTF-8', false);
	
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

		

		
		$void_reason = array(
			"Change of Mind",
			"Change Order",
			"No Show Delivery",
			"Took Too long",
			"Others"
		);

			
			if(!empty($list['list']))
			{
				foreach($list['list'] as $k=>$r)
				{
					$pdf->AddPage();
					$CI->make->h(4,'Date: As of ' .  date('M d, Y', strtotime($k)));
					$CI->make->sTable(array('border'=>'0.5px', 'cellpadding'=>'1px', 'class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
						$CI->make->sRow();
							$CI->make->th('Store', array("style"=>"background-color:red;color: white;text-align: center;" ));
							foreach($void_reason as $reason)
							{
								$CI->make->th($reason, array("style"=>"background-color:red;color: white;text-align: center;" ));
							}

							$CI->make->th('Total # Of Occurences', array("style"=>"background-color:red;color: white;text-align: center;" ));
						$CI->make->eRow();
					foreach($r as $key=>$value)
					{
						$CI->make->sRow();
				            $CI->make->td($key);
				            $occur=0;
				            foreach($void_reason as $reason)
							{
								if(isset($value[$reason]))
								{
									$CI->make->td($value[$reason]);
									$occur+=$value[$reason];
								}	
								else
									$CI->make->td('');
							}
							$CI->make->td($occur);
				 		$CI->make->eRow();
					}

					$CI->make->eTable();

					$rep_content = $CI->make->code();
					$pdf->writeHTML($rep_content,true,false,false,false,'');
					// $pdf->AddPage();

				}
			}
		
		
	
		// =============================================================================================================================== //
		
			
		
		$pdf->Output($details['filename'], 'I');	
?>