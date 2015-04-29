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
	$pdf->setPrintFooter(true);

	
			$reasons_complaint = array(
				"Wrong Delivery",
				"Product Quality",
				"Incomplete Delivery",
				"Late Delivery",
				"Others"
			);

			foreach($list['list'] as $k=>$r)
			{
				$pdf->AddPage();
				$rep_content = "<h4>Date: As of ".  date('M d, Y', strtotime($k))."</h4>";
				$rep_content .="<table  width=\"100%\" cellspacing=\"0\" cellpadding=\"1px\" border=\"1px\">
								<tr style=\"background-color:red; color: white; \">
									<th style=\"text-align:center\">Store</th>";
									foreach($reasons_complaint as $reason)
										$rep_content .= "<th  style=\"text-align:center\">".$reason."</th>";
									$rep_content .= "<th style=\"text-align:center\">Total # Of Occurences</th>
								</tr>";

				if(!empty($r))
				{
					foreach($r as $key=>$value)
					{
						$rep_content .= "<tr>";
				            $rep_content .= "<td>".$key."</td>";
				            $occur=0;
				            foreach($reasons_complaint as $reason)
							{
								if(isset($value[$reason]))
								{
									$rep_content .= "<td style=\"text-align:center\">".$value[$reason]."</td>";
									$occur+=$value[$reason];
								}	
								else
									$rep_content .= "<td style=\"text-align:center\">0</td>";
							}
							$rep_content .= "<td style=\"text-align:center\">".$occur."</td>";
					 	$rep_content .= "</tr>";
					}
				}else{
					$rep_content .= "<tr colspan=".(count($reasons_complaint)+2).">";
			        $rep_content .= "<td>No available data.</td>";
		        	$rep_content .= "</tr>";
				}
	
				$rep_content .= "</table>";
				$pdf->writeHTML($rep_content,true,false,false,false,'');
		    }
			
	
		// =============================================================================================================================== //
		
			
		
		$pdf->Output($details['filename'], 'I');	
?>