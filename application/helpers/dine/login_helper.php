<?php
function makeLoginPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('class'=>'pos-wrapper'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(7,'left',0,array('class'=>'bg-kinda-white no-spaces full-height'));
					$CI->make->sDiv(array('class'=>'nav-bar bg-gray no-spaces'));
						$CI->make->img(base_url().'img/logo.png',array('class'=>'logo', 'style'=>'width: 19%;'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(5,'left',0,array('class'=>'bg-dark-red full-height'));
					$CI->make->sDivRow(array('style'=>'margin-top:80px;'));
						$CI->make->sDivCol(3);
							$CI->make->sDiv(array('act'=>'#loginPin','class'=>'login-by tsc_awb_large tsc_awb_silver tsc_flat'));
								$CI->make->img(base_url().'img/Passcode.png',array('style'=>'width:60px;'));
							$CI->make->eDiv();
							$CI->make->sDiv(array('act'=>'#loginUsPwd','class'=>'login-by tsc_awb_large tsc_awb_silver  tsc_flat'));
								$CI->make->img(base_url().'img/pinCodeLogIn.png',array('style'=>'width:60px;'));
							$CI->make->eDiv();
							// $CI->make->sDiv(array('class'=>'login-by tsc_awb_large tsc_awb_silver  tsc_flat'));
							// $CI->make->eDiv();
							// $CI->make->sDiv(array('class'=>'login-by tsc_awb_large tsc_awb_silver  tsc_flat'));
							// $CI->make->eDiv();
							// $CI->make->sDiv(array('class'=>'login-by tsc_awb_large tsc_awb_silver  tsc_flat'));
							// $CI->make->eDiv();
						$CI->make->eDivCol();

						$CI->make->sDivCol(8,'left',0,array('id'=>'loginUsPwd','class'=>'logins','style'=>'margin-top:10px;display:none;'));
							$CI->make->sForm("site/go_login",array('id'=>'uname-login-form'));
							$CI->make->input(null,'username',null,'USERNAME',array('class'=>'rOkay login-input'));	
							$CI->make->pwd(null,'password',null,'PASSWORD',array('class'=>'rOkay login-input'));	
							$CI->make->unbutton('Enter',array('id'=>'uname-login','class'=>'login-btn tsc_awb_large tsc_flat tsc_awb_green'));
							$CI->make->eForm();
						$CI->make->eDivCol();
						
						$CI->make->sDivCol(9,'left',0,array('id'=>'loginPin','class'=>'logins'));
							$CI->make->append(onScrNumPad('pin','pin-login'));
						$CI->make->eDivCol();

						$CI->make->eDivRow();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();

		$CI->make->sDiv(array('id'=>'wrap'));
		$CI->make->eDiv();
		
	return $CI->make->code();
}
?>