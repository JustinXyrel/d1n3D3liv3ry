<?php
//-----------Branch Details-----start-----allyn
function makeDetailsForm($det=array()){
	$CI =& get_instance();
	
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			//$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("setup/details_db",array('id'=>'details_form'));
						$CI->make->sDivRow(array('style'=>'margin:10px;'));
							$CI->make->sDivCol(12, 'right');
		
									$CI->make->button(fa('fa-save fa-fw').' Save',array('id'=>'save-btn'),'primary');
									
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						
						$CI->make->sDivRow(array('style'=>'margin:10px;'));
							$CI->make->sDivCol(6);
								$CI->make->hidden('tax_id',iSetObj($det,'tax_id'));
								$CI->make->input('Code','branch_code',iSetObj($det,'branch_code'),'Type Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
								$CI->make->input('Name','branch_name',iSetObj($det,'branch_name'),'Type Name',array('class'=>'rOkay'));
								$CI->make->textarea('Description','branch_desc',iSetObj($det,'branch_desc'),'Type Description',array('class'=>'rOkay'));
								$CI->make->input('TIN','tin',iSetObj($det,'tin'),'TIN',array('class'=>'rOkay'));
								$CI->make->input('BIR #','bir',iSetObj($det,'bir'),'BIR',array('class'=>'rOkay'));
								$CI->make->input('Serial #','serial',iSetObj($det,'serial'),'Serial Number',array('class'=>'rOkay'));
								$CI->make->input('Website','website',iSetObj($det,'website'),'Website',array('class'=>'rOkay'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(6);
								$CI->make->input('Contact No.','contact_no',iSetObj($det,'contact_no'),'Type Contact Number',array('class'=>'rOkay'));
								$CI->make->input('Delivery No.','delivery_no',iSetObj($det,'delivery_no'),'Type Delivery Number',array('class'=>'rOkay'));
								$CI->make->textarea('Address','address',iSetObj($det,'address'),'Type Branch Address',array('class'=>'rOkay'));
								$CI->make->input('Machine No.','machine_no',iSetObj($det,'machine_no'),'Machine Number',array('class'=>'rOkay'));
								$CI->make->input('Permit#','permit_no',iSetObj($det,'permit_no'),'Permit Number',array('class'=>'rOkay'));
								$CI->make->input('Email','email',iSetObj($det,'email'),'Email Address',array('class'=>'rOkay'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						
						// $CI->make->sDivRow(array('style'=>'margin:10px;'));
						// 	$CI->make->sDivCol(6);
						// 		$CI->make->currenciesDrop('Currency','currency',iSetObj($det,'currency'),'',array());
						// 	$CI->make->eDivCol();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			//$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	
	return $CI->make->code();
}
//-----------Branch References-----end-----allyn
?>