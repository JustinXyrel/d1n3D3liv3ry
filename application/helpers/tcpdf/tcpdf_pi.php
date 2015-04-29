<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
 
class NormalTCPDF extends TCPDF {
	public function Header()
	{

	}

	public function Footer()
	{
		
	}
}

// Extend the TCPDF class to create custom Header and Footer
class OnemoretakePDF extends TCPDF {

	var $top_margin = 40;

	public function Header() 
	{

		$CI =& get_instance();
		$CI->load->model('settings/company_model');	
		$company_details = $CI->company_model->get_company_details();
		
		// $this->Image(base_url().$company_details->logo,12,5,48,27,'','','',true);
		$this->SetFont('helvetica','B',14);
		$this->Ln(10);
        $this->Write(8, $company_details->name, '', 0, 'C', false, 0, false, false, 0);
		$this->Ln();
		$this->SetFont('helvetica','',11);
		$this->Write(5, $company_details->address, '', 0, 'C', false, 0, false, false, 0);
		$this->Ln();
		$this->Write(5, 'Tel. No. '.$company_details->phone, '', 0, 'C', false, 0, false, false, 0);
		$this->Ln();
		$this->Write(5, 'TIN : '.$company_details->tin, '', 0, 'C', false, 0, false, false, 0);
		$this->Ln(8);
		$this->SetLineWidth(0.2);
		$this->Line(11, $this->y, $this->w - 11, $this->y);
		$this->Ln(10);
		$this->top_margin = $this->GetY()+5;
    }
 
    public function Footer() 
	{
      //footer
    }
	
	public function title($title, $from, $to)
	{
		$range = date('m/d/Y', strtotime($from))." - ".date('m/d/Y', strtotime($to));
		$this->Ln(10);
		$this->SetFont('helvetica', 'B', 12);
		$this->Write(5, $title, '', 0, 'L', false, 0, false, false, 0);
		$this->Ln();
		$this->SetFont('helvetica', '', 11);
		$this->Write(5, "Period: ".$range, '', 0, 'L', false, 0, false, false, 0);
		$this->Ln(10);
	}

	public function introTitle($title)
	{
		$this->Ln(18);
		$this->SetFont('helvetica', 'B', 10);
		$this->Write(5, $title, '', 0, 'C', false, 0, false, false, 0);
		$this->Ln(5);
	}


	
	public function error_message()
	{
		$this->SetFont('helvetica', '', 11);
		$this->Write(5, "There is no transaction in the given date.", '', 0, 'L', false, 0, false, false, 0);
	}
}
 
function tcpdf($orientation, $unit, $format)
{
	return new OnemoretakePDF ($orientation, $unit, $format, true);
}


 
?>