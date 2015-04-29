<?php

function indexPage($void_reasons=array(), $complaint_reasons){
	$CI =& get_instance();
	$user = $CI->session->userdata('user');
		$CI->make->sDiv(array('id'=>'cashier-panel'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(2,'left',0,array('class'=>'cpanel-left'));
					$CI->make->H(5,'STATUS',array('class'=>'headline text-center','style'=>'margin-bottom:5px;'));
					$CI->make->button('<span id="status_text">'.fa('fa-list-alt fa-2x fa-fw').' &nbsp; ALL ORDERS</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'all','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-angle-double-up fa-2x fa-fw').' &nbsp; ADVANCE</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'advance','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-times fa-2x fa-fw').' &nbsp; REJECTED</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'rejected','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-arrow-up fa-2x fa-fw').' &nbsp; IN PROCESS</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'open','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-check fa-2x fa-fw').'  &nbsp; DISPATCHED</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'processed','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-truck fa-2x fa-fw mirror').'  &nbsp; DELIVERED</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'delivered','btn'=>'status'));
					$CI->make->button('<span id="status_text">'.fa('fa-pause fa-2x fa-fw').'  &nbsp; ON HOLD</span>',array('class'=>'btn-block no-raduis cpanel-btn-blue double btnheader status-btn','id'=>'status-btn','type'=>'hold','btn'=>'status'));
					
				$CI->make->eDivCol();
				$CI->make->sDivCol(10);
					$CI->make->sDiv(array('class'=>'cpanel-center'));
						$CI->make->sDivRow(array('class'=>'center-btns'));
							$CI->make->sDivCol(4);
								$a = $CI->make->A('<span>'.fa('fa-list-alt fa-2x fa-fw').'<br> GET ORDER','cashier/order_date_form',array(
																									'id'=>'order_date_id',
																									'class'=>'order-date-form',
																									'rata-title'=>'Date and Time of Delivery',
																									'rata-pass'=>'cashier/pop_order_date_form_db',
																									'rata-form'=>'pop_order_date_form',
																									'style'=>'text-decoration: none !important; color: #ffffff !important;',
																									'return'=>true));

								$CI->make->button($a, array('class'=>'btn-block no-raduis cpanel-btn-red double btnheader' ));
							$CI->make->eDivCol();
								$CI->make->button('<span id="day_text">'.fa('fa-clock-o fa-2x fa-fw').'<br> NOW</span>',array('class'=>'btn-block no-raduis cpanel-btn-red double btnheader','style'=>'display:none','id'=>'now-btn','type'=>'now','btn'=>'now'));
							$CI->make->sDivCol(4);
								$CI->make->button(fa('fa-search fa-2x fa-fw').'<br> LOOKUP',array('class'=>'btn-block no-raduis cpanel-btn-red double btnheader','id'=>'look-btn'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(4);
								$CI->make->button(fa('fa-refresh fa-2x fa-fw').'<br> REFRESH',array('id'=>'refresh-btn','class'=>'btn-block no-raduis cpanel-btn-orange double'));
							$CI->make->eDivCol();
						
						$CI->make->eDivRow();
						$CI->make->sDiv(array('class'=>'orders-lists center-loads-div orders-div','style'=>'margin-top:10px;'));
						$CI->make->eDiv();
						$CI->make->sDiv(array('style'=>'margin-top:10px;display:none;','id'=>'orders-search','class'=>''));
							$CI->make->sDivRow();
								$CI->make->sDivCol(4);
									$CI->make->input('','daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
								 	$CI->make->searchByDrop('','search_by',$value='name','',array());
									$CI->make->input(null,'search_transaction',null, 'Type keyword...', array('class'=>''));
									$CI->make->button(fa('fa-search').' Search',array('style'=>'height: 40px !important; width: 100%;','id'=>'go-search-btn','class'=>'bg-green'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(8);
									$CI->make->sDiv(array('class'=>'search-result orders-div ps-container'));
									$CI->make->eDiv();
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eDiv();
						$CI->make->sDiv(array('style'=>'margin-top:10px;display:none;','id'=>'all-orders','class'=>'all-div'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(4);
									$CI->make->input('','all_daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(2);
								 	$CI->make->button('Go',array('style'=>'height: 35px !important; width: 100%;','id'=>'go-all-btn','class'=>'bg-green'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
							$CI->make->sDivRow();
								$CI->make->sDiv(array('class'=>'search-result orders-div ps-container', 'style'=>'max-height: 333px !important; min-height: 333px !important; '));
								$CI->make->eDiv();
							$CI->make->eDivRow();
						$CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'orders-view-div center-loads-div'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(6);
									$CI->make->sDiv(array('class'=>'order-view-list','ref'=>null));
									$CI->make->eDiv();
								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
									$CI->make->sDivRow();


										$buttons = array("recall"	=> fa('fa-search fa-lg fa-fw')." Recall",
														 "hold"	=> fa('fa-pause fa-fw')." Hold",
														 "resume"	=> fa('fa-play fa-fw')." Resume",
														);
										foreach ($buttons as $id => $text) {
											$CI->make->sDivCol(12,'left',0,array("style"=>'margin-bottom:10px;'));
												$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block cpanel-btn-blue'));
											$CI->make->eDivCol();
										}

										$buttons = array("void"	=> fa('fa-ban fa-lg fa-fw')." Void",
														 "complain" => fa('fa-envelope-o fa-lg fa-fw')." Complain",
														 "back-order-list"	=> fa('fa-reply fa-lg fa-fw')." Back");
										foreach ($buttons as $id => $text) {

											$CI->make->sDivCol(12,'left',0,array("style"=>'margin-bottom:10px;'));
												$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block cpanel-btn-red'));
											$CI->make->eDivCol();
										}
										$CI->make->sDivCol(12,'left',0,array("id"=>"customer_info", "style"=>'margin-bottom:10px;'));
										$CI->make->eDivCol();
									$CI->make->eDivRow();
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eDiv();

						$CI->make->sDiv(array('class'=>'reasons-div center-loads-div'));
							$CI->make->sDivRow();
							
								foreach ($void_reasons as $key=>$text) {
									$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:10px;'));
										$CI->make->button($text->reason,array('class'=>'btn-block cpanel-btn-red reason-btns double'));
									$CI->make->eDivCol();
								}
								$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:10px;'));
										$CI->make->button(fa('fa-reply fa-lg fa-fw')." Back",array('class'=>'btn-block cpanel-btn-orange cancel-reason-btn double'));
									$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'complaints-div center-loads-div'));
							$CI->make->sDivRow();
								// $buttons = array(
								// 	"Wrong Delivery",
								// 	"Product Quality",
								// 	"Incomplete Delivery",
								// 	"Late Delivery",
								// 	"Others"
								// );
								$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:10px;'));
								foreach ($complaint_reasons as $key=>$text) {
										$CI->make->button($text->reason,array('class'=>'btn-block cpanel-btn-red complaint-btns double'));
								}
								$CI->make->eDivCol();
							
								$CI->make->sDivCol(8,'left',0,array("style"=>'margin-bottom:10px;'));
									$CI->make->sDivRow();
										$CI->make->sDivCol(12);
											$CI->make->textarea('Remarks', 'complain_remarks',null, 'Type remarks', array('style'=>'display: block'));
										$CI->make->eDivCol();
									$CI->make->eDivRow();
									$CI->make->sDivRow();
										$CI->make->sDivCol(6);
											$CI->make->button(fa('fa-reply fa-lg fa-fw')." Back",array('class'=>'btn-block cpanel-btn-orange cancel-complaints-btn double'));
										$CI->make->eDivCol();
										$CI->make->sDivCol(6);
											$CI->make->button(fa('fa-reply fa-lg fa-fw')." Submit",array('class'=>'btn-block cpanel-btn-green submit-complaints-btn double'));
										$CI->make->eDivCol();
										
									$CI->make->eDivRow();
								$CI->make->eDivCol();
							$CI->make->eDivRow();

						$CI->make->eDiv();
					$CI->make->eDiv();

					$CI->make->sDivRow(array('class'=>'cpanel-bottom'));
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('id'=>'time','class'=>' headline text-center'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->button(fa('fa-power-off fa-2x fa-fw').'<br> LOGOUT',array('id'=>'logout-btn','class'=>'btn-block cpanel-btn-red double'));
						$CI->make->eDivCol();
					$CI->make->eDivRow();

				$CI->make->eDivCol();
			
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function counterPage($type=null,$time=null, $ins = null, $branch_id = null, $cust = null){
	$CI =& get_instance();
		$confirmed='';
		$confirmed = $CI->session->userdata('confirmed');
		if(!empty($branch_id))
			$CI->make->hidden('branch_id',$branch_id);
		
		$CI->make->sDiv(array('id'=>'counter', 'confirmed'=>$confirmed));
			$CI->make->sDivRow();
				#LEFT
				$CI->make->sDivCol(2,'left',0,array('class'=>'counter-left'));
					$CI->make->sDiv(array('class'=>'counter-left-side'));
						$CI->make->button(fa('fa-tags fa-lg fa-fw').'<br> QUANTITY',array('id'=>'qty-btn','class'=>'btn-block counter-btn-red double'));
						$CI->make->button(fa('fa-certificate fa-lg fa-fw').'<br> SENIOR DISCOUNT',array('id'=>'add-discount-btn','class'=>'btn-block counter-btn-red double'));
						if($cust->is_vip == 1)
							$CI->make->button(fa('fa-certificate fa-lg fa-fw').'<br> ADD VIP DISCOUNT',array('id'=>'add-vip-btn','class'=>'btn-block counter-btn-red double'));
						else
							$CI->make->button(fa('fa-certificate fa-lg fa-fw').'<br> ADD VIP DISCOUNT',array('id'=>'add-vip-btn','disabled'=>'disable','class'=>'btn-block counter-btn-red double'));
											
						$CI->make->button(fa('fa-tag fa-lg fa-fw').'<br> ADD CHARGES',array('id'=>'charges-btn','class'=>'btn-block counter-btn-red double'));
						$CI->make->button(fa('fa-times fa-lg fa-fw').'<br>REMOVE',array('id'=>'remove-btn','class'=>'btn-block counter-btn-red double'));
						// $CI->make->button(fa('fa-keyboard-o fa-lg fa-fw').'<br>MISC',array('class'=>'btn-block counter-btn-red double'));
						// $CI->make->button(fa('fa-barcode fa-lg fa-fw').'<br>RETAIL',array('class'=>'btn-block counter-btn-red double'));
						// $CI->make->button(fa('fa-file fa-lg fa-fw').'<br>RECALL',array('class'=>'btn-block counter-btn-red double'));
						// $CI->make->button(fa('fa-magnet fa-lg fa-fw').'<br>HOLD ALL',array('id'=>'hold-all-btn','class'=>'btn-block counter-btn-red double'));
						$CI->make->button(fa('fa-power-off fa-lg fa-fw').'<br>LOGOUT',array('id'=>'logout-btn','class'=>'btn-block counter-btn-red double'));
						$CI->make->button(fa('fa-reply fa-lg fa-fw').'<br>BACK',array('id'=>'cancel-btn','class'=>'btn-block counter-btn-red double'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				#CENTER
				$CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'center-div counter-center list-div'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'title'));
								$CI->make->H(3,strtoupper($type),array('id'=>'trans-header','class'=>'receipt text-center text-uppercase'));
								$CI->make->H(5,$time,array('id'=>'trans-datetime','class'=>'receipt text-center'));
								$CI->make->append('<hr>');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							#LISTS
							$CI->make->sDivCol(12,'left',0,array('class'=>'body'));
								$CI->make->sUl(array('class'=>'trans-lists'));
								$CI->make->eUl();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'foot'));
								$CI->make->append('<hr>');
								$CI->make->H(4,'TOTAL: <span id="total-txt">0.00</span>',array('class'=>'receipt text-center'));
								$CI->make->H(5,'DISCOUNT: <span id="discount-txt">0.00</span>',array('class'=>'receipt text-center'));
								$CI->make->H(5,'NET AMOUNT DUE: <span id="net-discount-txt">0.00</span>',array('class'=>'receipt text-center'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'foot'));
								$CI->make->append('<hr>');
								$CI->make->textarea('','instruction',$ins,'Special Instruction',array('style'=>'height: 88px;'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'counter-center-btns center-div list-div'));
						$CI->make->H(4,'PAYMENT METHOD');
						$CI->make->sDivRow();
							$CI->make->sDivCol(6);
								$CI->make->button(fa('fa-money fa-lg fa-fw').' CASH',array('id'=>'cash-btn','class'=>'btn-block counter-btn-teal double'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(6);
								$CI->make->button(fa('fa-credit-card fa-lg fa-fw').' CREDIT CARD',array('id'=>'credit-btn','class'=>'btn-block counter-btn-teal double'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
				$CI->make->eDivCol();
				#CATEGORIES
				$CI->make->sDivCol(2,'left');
					$CI->make->button(fa('fa-chevron-circle-up fa-2x fa-fw'),array('id'=>'menu-cat-scroll-up','class'=>'btn-block counter-btn double'));
					$CI->make->sDiv(array('class'=>'menu-cat-container'));
					$CI->make->eDiv();
					$CI->make->button(fa('fa-chevron-circle-down fa-2x fa-fw'),array('id'=>'menu-cat-scroll-down','class'=>'btn-block counter-btn double'));
				$CI->make->eDivCol();
				#ITEMS
				$CI->make->sDivCol(4,'left',0);
					$CI->make->sDiv(array('class'=>'counter-right'));
						#MENU
						$CI->make->sDiv(array('class'=>'menus-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'&nbsp;',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->sDiv(array('class'=>'items-lists'));
							$CI->make->eDiv();
						$CI->make->eDiv();
						#MODS
						$CI->make->sDiv(array('class'=>'mods-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'MODIFIERS',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->sDiv(array('class'=>'mods-lists'));
							$CI->make->eDiv();
						$CI->make->eDiv();
						#QTY
						$CI->make->sDiv(array('class'=>'qty-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'Quantity',array('class'=>'receipt text-center title','style'=>'margin-bottom:20px'));
							$CI->make->sDiv(array('class'=>'qty-lists'));
								$CI->make->sDivRow(array('style'=>'margin-bottom:20px;'));
									$CI->make->sDivCol();
										$CI->make->number(null, 'qty-amount',null, 'Quantity', array('class'=>'input-lg','style'=>'display: block'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->button(fa('fa-plus fa-2x'),array('value'=>'1','operator'=>'plus','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->button(fa('fa-minus fa-2x'),array('value'=>'1','operator'=>'minus','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									$CI->make->eDivCol();
									// $CI->make->sDivCol(6);
									// 	$CI->make->button('+5',array('value'=>'5','operator'=>'plus','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									// $CI->make->eDivCol();
									// $CI->make->sDivCol(6);
									// 	$CI->make->button('+10',array('value'=>'10','operator'=>'plus','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									// $CI->make->eDivCol();
									// $CI->make->sDivCol(6);
									// 	$CI->make->button('x2',array('value'=>'2','operator'=>'times','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									// $CI->make->eDivCol();
									// $CI->make->sDivCol(6);
									// 	$CI->make->button('x10',array('value'=>'10','operator'=>'times','class'=>'btn-block edit-qty-btn counter-btn-silver double'));
									// $CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDiv(array('class'=>'counter-center-btns'));
									$CI->make->sDivRow();
										$CI->make->sDivCol(6);
											$CI->make->button('Reset',array('value'=>'1','operator'=>'equal','class'=>'btn-block edit-qty-btn counter-btn-red double'));
										$CI->make->eDivCol();
										$CI->make->sDivCol(6);
											$CI->make->button('Finished',array('id'=>'qty-btn-done','class'=>'btn-block counter-btn-green double'));
										$CI->make->eDivCol();
									$CI->make->eDivRow();
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
						#VIP DISCOUNT
						$CI->make->sDiv(array('class'=>'vip-discount-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'VIP Member Discount',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->sForm("",array('id'=>'vipdisc-form'));
 								$CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
									 $CI->make->sDivCol(12);
									 	 $CI->make->fixedPercentageDrop(null,'is_absolute',null,'',null);
								 	 $CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
									 $CI->make->sDivCol(12);
								 	 	$CI->make->input(null, 'disc-amount',null, 'Rate', array('style'=>'display: block'), null , '%' );
								 	 $CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
									$CI->make->sDivCol(12);
										$CI->make->button(fa('fa-plus fa-lg fa-fw').' APPLY DISCOUNT ',array('id'=>'add-vip-disc-btn','class'=>'btn-block counter-btn-green'));
								 	$CI->make->eDivCol();
								$CI->make->eDivRow();
							 $CI->make->eForm();
						$CI->make->eDiv();

						#DISCOUNT
						$CI->make->sDiv(array('class'=>'sel-discount-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'SELECT DISCOUNT',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->sDiv(array('class'=>'select-discounts-lists'));
							$CI->make->eDiv();
						$CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'discount-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'&nbsp;',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->H(3,'RATE: % <span id="rate-txt"></span>',array('class'=>'receipt text-center','style'=>'margin-bottom:10px'));
							$CI->make->sDiv(array('class'=>'discounts-lists'));
								 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
									 $CI->make->sDivCol(12);
								 	 	$CI->make->input(null,'disc-guests','0','Total No. Of Senior Citizen',array('disabled'=>'disable'),fa('fa-user'));
								 	 $CI->make->eDivCol();
								 $CI->make->eDivRow();
								 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
								 	 // $CI->make->sDivCol(4);
								 	 // 	$CI->make->button('ALL ITEMS',array('ref'=>'all','class'=>'disc-btn-row btn-block counter-btn-teal'));
								 	 // $CI->make->eDivCol();
								 	 // $CI->make->sDivCol(4);
								 	 // 	$CI->make->button('EQUALLY DIVIDED',array('ref'=>'equal','class'=>'disc-btn-row btn-block counter-btn-orange'));
								 	 // $CI->make->eDivCol();
								 	 $CI->make->sDivCol(8);
								 	 	$CI->make->button('HIGHEST VALUE ORDERED',array('ref'=>'highest','class'=>'disc-btn-row btn-block counter-btn-orange'));
								 	 $CI->make->eDivCol();
								 	 $CI->make->sDivCol(4);
								 	 	$CI->make->button(fa('fa fa-times fa-lg fa-fw').'REMOVE',array('id'=>'remove-disc-btn','class'=>'btn-block counter-btn-red'));
								 	 $CI->make->eDivCol();
								 $CI->make->eDivRow();
								 $CI->make->sForm("",array('id'=>'disc-form'));
									 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
									 	 $CI->make->sDivCol(12);
									 	 	$CI->make->input(null,'disc-cust-name',null,'Senior Citizen Name',array('class'=>'rOkay','ro-msg'=>'Add Senior Citizen name for discount'),fa('fa-user'));
									 	 	$CI->make->hidden('disc-disc-id',null);
									 	 	$CI->make->hidden('disc-disc-rate',null);
									 	 	$CI->make->hidden('disc-disc-code',null);
									 	 	$CI->make->hidden('disc-no-tax',null);
									 	 $CI->make->eDivCol();
									 
									 $CI->make->eDivRow();
									 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
										 $CI->make->sDivCol(12);
									 	 	$CI->make->input(null,'disc-cust-code',null,'Senior Citizen ID No.',array('class'=>'','ro-msg'=>'Add Customer Code for Discount'),fa('fa-credit-card'));
									 	 $CI->make->eDivCol();
									 $CI->make->eDivRow();
									 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
										 $CI->make->sDivCol(12);
									 	 	// $CI->make->input(null,'disc-cust-bday',null,'Birthday (MM/DD/YYYY)',array('ro-msg'=>'Add Customer Birthdate for Discount'),fa('fa-calendar'));
									 	 $CI->make->date('','disc-cust-bday',null,'Birthday (MM/DD/YYYY)',array('ro-msg'=>'Add Customer Birthdate for Discount') );
									 	 $CI->make->eDivCol();
									 $CI->make->eDivRow();
									 $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
										 $CI->make->sDivCol(12);
										 	$CI->make->button(fa('fa-plus fa-lg fa-fw').' ADD SENIOR CITIZEN ',array('id'=>'add-disc-person-btn','class'=>'btn-block counter-btn-green'));
									 	 $CI->make->eDivCol();
									 $CI->make->eDivRow();
									 $CI->make->sDivRow();
										 $CI->make->sDivCol(12);
										 	$CI->make->sDiv(array('class'=>'disc-persons-list-div listings','style'=>'height:280px;overflow:auto;'));
											$CI->make->eDiv();
									 	 $CI->make->eDivCol();
									 $CI->make->eDivRow();
								 $CI->make->eForm();
							
							$CI->make->eDiv();
						$CI->make->eDiv();
						#CHARGES
						$CI->make->sDiv(array('class'=>'charges-div loads-div','style'=>'display:none'));
							$CI->make->H(3,'Select Charges',array('class'=>'receipt text-center title','style'=>'margin-bottom:10px'));
							$CI->make->sDiv(array('class'=>'charges-lists'));
							$CI->make->eDiv();
						$CI->make->eDiv();
					$CI->make->eDiv();
				$CI->make->eDivCol();
			$CI->make->eDivRow();

			$CI->make->sDiv();
			$CI->make->eDiv();

		$CI->make->eDiv();
	return $CI->make->code();
}
function settlePage($ord=null,$det=null,$discs=null,$totals=null,$charges=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'settle','sales'=>$ord['sales_id'],'type'=>$ord['type'],'balance'=>$ord['balance']));
			$CI->make->sDivRow();
				$CI->make->sDivCol(5,'left',0,array('class'=>'settle-left'));
					$CI->make->sBox('default',array('class'=>'box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-red'));
							$CI->make->boxTitle('BALANCE DUE');
							$CI->make->boxTitle('PHP <span id="balance-due-txt">'.num($ord['balance']).'</span>',array('class'=>'pull-right','style'=>'margin-right:10px;'));
						$CI->make->eBoxHead();
						$CI->make->sBoxBody();
							$CI->make->sDiv(array('class'=>'order-view-list'));
								$CI->make->H(3,strtoupper($ord['type'])." #".$ord['sales_id'],array('class'=>'receipt text-center'));
						        $CI->make->H(5,sql2DateTime($ord['datetime']),array('class'=>'receipt text-center'));
						        $CI->make->append('<hr>');
						        $CI->make->sDiv(array('class'=>'body'));
						            $CI->make->sUl();
						                $total = 0;
						                foreach ($det as $menu_id => $opt) {
						                    $qty = $CI->make->span($opt['qty'],array('class'=>'qty','return'=>true));
						                    $name = $CI->make->span($opt['name'],array('class'=>'name','return'=>true));
						                    $cost = $CI->make->span($opt['price'],array('class'=>'cost','return'=>true));
						                    $price = $opt['price'];
						                    $CI->make->li($qty." ".$name." ".$cost);
						                    if(count($opt['modifiers']) > 0){
						                        foreach ($opt['modifiers'] as $mod_id => $mod) {
						                            $name = $CI->make->span($mod['name'],array('class'=>'name','style'=>'margin-left:36px;','return'=>true));
						                            $cost = "";
						                            if($mod['price'] > 0 )
						                                $cost = $CI->make->span($mod['price'],array('class'=>'cost','return'=>true));
						                            $CI->make->li($name." ".$cost);
						                            $price += $mod['price'];
						                        }
						                    }
						                    $total += $opt['qty'] * $price  ;
						                }
						                if(count($charges) > 0){
						                    foreach ($charges as $charge_id => $ch) {
						                        $qty = $CI->make->span(fa('fa fa-tag'),array('class'=>'qty','return'=>true));
						                        $name = $CI->make->span($ch['name'],array('class'=>'name','return'=>true));
						                        $tx = $ch['amount'];
						                        if($ch['absolute'] == 0)
						                            $tx = $ch['amount']."%";
						                        $cost = $CI->make->span($tx,array('class'=>'cost','return'=>true));
						                        $CI->make->li($qty." ".$name." ".$cost);
						                    }
						                }
						            $CI->make->eUl();
						        $CI->make->eDiv();
						        $CI->make->append('<hr>');
						        $CI->make->H(3,'TOTAL: PHP '.num($totals['total']),array('class'=>'receipt text-center'));
						        $CI->make->H(4,'DISCOUNT: PHP '.num($totals['discount']),array('class'=>'receipt text-center'));
							$CI->make->eDiv();
						$CI->make->eBoxBody();
						$CI->make->sBoxFoot();
							$CI->make->sDivRow();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-bars fa-lg fa-fw').' Transactions',array('id'=>'transactions-btn','class'=>'btn-block settle-btn double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-reply fa-lg fa-fw').' Recall',array('id'=>'recall-btn','type'=>$ord['type'],'sale'=>$ord['sales_id'],'class'=>'btn-block settle-btn-orange double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-times fa-lg fa-fw').' Cancel',array('id'=>'cancel-btn','type'=>$ord['type'],'class'=>'btn-block settle-btn-red double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxFoot();
					$CI->make->eBox();
				$CI->make->eDivCol();
				$CI->make->sDivCol(7,'left',0,array('class'=>'settle-right'));

					$CI->make->sBox('default',array('class'=>'loads-div select-payment-div box-solid bg-dark-green'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('&nbsp;');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-dark-green'));
								$buttons = array("cash"	=> fa('fa-money fa-lg fa-fw')."<br> CASH",
												 "credit-card"	=> fa('fa-credit-card fa-lg fa-fw')."<br> CREDIT CARD",
												 "debit-card"	=> fa('fa-credit-card fa-lg fa-fw')."<br> DEBIT CARD",
												 "gift-cheque"	=> fa('fa-gift fa-lg fa-fw')."<br> GIFT CHEQUE",
												 "check"	=> fa('fa-check-square-o fa-lg fa-fw')."<br> CHECK"
												 );
								$CI->make->sDivRow();
									// $CI->make->sDivCol(6,'left',0,array("style"=>'margin-bottom:10px;'));
									// 	$CI->make->H(3,'SELECT DISCOUNT',array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
									// 	if(count($discounts) > 0 ){
									// 		foreach ($discounts as $res) {
									// 			$CI->make->button(strtoupper($res->disc_code)." ".strtoupper($res->disc_name),array('ref'=>$res->disc_id,'opt'=>$res->disc_rate."-".$res->disc_code,'class'=>'disc-btns btn-block settle-btn-green double'));
									// 		}
									// 	}
									// $CI->make->eDivCol();
										$CI->make->H(3,'SELECT PAYMENT METHOD',array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
										foreach ($buttons as $id => $text) {
											$CI->make->sDivCol(6,'left',0,array("style"=>'margin-bottom:10px;'));
												$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block settle-btn-green double'));
											$CI->make->eDivCol();
										}
								$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div cash-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' CASH PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-red-white'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(3);
									$CI->make->sDiv(array('class'=>'shorcut-btns'));
										$buttons = array(
													 "5"	=> 'PHP 5',
													 "10"	=> 'PHP 10',
													 "20"	=> 'PHP 20',
													 "50"	=> 'PHP 50',
													 "100"	=> 'PHP 100',
													 "200"	=> 'PHP 200',
													 "500"	=> 'PHP 500',
													 "1000"	=> 'PHP 1000'
													 );
										$CI->make->sDivRow(array('style'=>'margin-top:10px;margin-left:10px;'));
											foreach ($buttons as $id => $text) {
													$CI->make->sDivCol(12,'left',0);
														$CI->make->button($text,array('val'=>$id,'class'=>'amounts-btn btn-block settle-btn-red-gray'));
													$CI->make->eDivCol();
											}
										$CI->make->eDivRow();
									$CI->make->eDiv();
								$CI->make->eDivCol();
								$CI->make->sDivCol(9);
									$CI->make->append(onScrNumDotPad('cash-input','cash-enter-btn'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
						$CI->make->sBoxFoot();
							$CI->make->sDivRow();
								$CI->make->sDivCol(4,'left');
									$CI->make->button('Exact Amount',array('id'=>'cash-exact-btn','amount'=>num($ord['balance']),'class'=>'btn-block settle-btn double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button('Next Amount',array('id'=>'cash-next-btn','amount'=>num(round($ord['balance'])),'class'=>'btn-block settle-btn-red-gray double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-reply fa-lg fa-fw').' Change Payment Method',array('id'=>'cancel-cash-btn','class'=>'btn-block settle-btn-red double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxFoot();
					$CI->make->eBox();	

					$CI->make->sBox('default',array('class'=>'loads-div debit-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' DEBIT PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow(array('style'=>'margin:auto 0;'));
								$CI->make->sDivCol(6);
									$CI->make->input('Card #','debit-card-num','','',array('maxlength'=>'30',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','debit-amt',number_format($ord['balance'],2),'',array('maxlength'=>'10',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-debit-target',
										'#debit-card-num',
										'debit-enter-btn',
										'cancel-debit-btn',
										'Change Payment Method'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div credit-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' CREDIT PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow(array('style'=>'margin:auto 0;'));
								$buttons = array(
									"Master Card"	=> fa('fa-cc-mastercard fa-2x')."<br/>Master Card",
									"VISA"	=> fa('fa-cc-visa fa-2x')."<br/>VISA",
									"AmEx"	=> fa('fa-cc-amex fa-2x')."<br/>American Express",
									"Discover"	=> fa('fa-cc-discover fa-2x')."<br/>Discover",
								);
								foreach ($buttons as $id => $text) {
									$CI->make->sDivCol(3,'left',0,array('style'=>'padding:0;margin:0'));
										$CI->make->button($text,array('value'=>$id,'class'=>'credit-type-btn double settle-btn-teal btn-block'));
									$CI->make->eDivCol();
								}
							$CI->make->eDivRow();
							$CI->make->sDivRow(array('style'=>'margin:auto 0;padding:10px 0 8px;'));
								$CI->make->sDivCol(6,'left');
									$CI->make->hidden('credit-type-hidden','Master Card');
									$CI->make->input('Card #','credit-card-num','','',array('maxlength'=>'30',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Approval Code','credit-app-code','','',array('maxlength'=>'15',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','credit-amt',number_format($ord['balance'],2),'',array('maxlength'=>'10',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6,'left');
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-credit-target',
										'#credit-card-num',
										'credit-enter-btn',
										'cancel-credit-btn',
										'Change Payment Method'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div gc-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' GIFT CHEQUE');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(6);
									$CI->make->hidden('hid-gc-id');
									$CI->make->input('Gift Cheque code','gc-code','','',array(
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','gc-amount','','',array('readonly'=>'readonly',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-gc-target',
										'#gc-code',
										'gc-enter-btn',
										'cancel-gc-btn',
										'Change Payment Method',false));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div after-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('&nbsp;');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-dark-green'));
							$CI->make->sDiv(array('class'=>'body'));
								$CI->make->H(3,'AMOUNT TENDERED: PHP '.strong('<span id="amount-tendered-txt"></span>'),array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
								$CI->make->H(3,'CHANGE DUE: PHP '.strong('<span id="change-due-txt"></span>'),array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
							$CI->make->eDiv();
							$CI->make->sDivRow();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-plus fa-lg fa-fw').' Additonal Payment',array('id'=>'add-payment-btn','class'=>'btn-block settle-btn-teal double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-print fa-lg fa-fw').' Print Receipt',array('id'=>'print-btn','class'=>'btn-block settle-btn-orange double','ref'=>$ord['sales_id']));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-check fa-lg fa-fw').' Finish',array('id'=>'finished-btn','class'=>'btn-block settle-btn-green double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div transactions-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('Transactions');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-red-white'));
							$CI->make->sDiv(array('class'=>'body'));

							$CI->make->eDiv();
							$CI->make->sDivRow();
								$CI->make->sDivCol(12,'left');
									$CI->make->button(fa('fa-times fa-lg fa-fw').' Close',array('id'=>'trsansactions-close-btn','class'=>'btn-block settle-btn-orange double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function splitPage($type=null,$time=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'counter'));
			$CI->make->sDivRow();
				#CENTER
				$CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'counter-center-btns center-div list-div','style'=>"margin-left:10px;margin-top:10px;"));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'bg-red','style'=>'padding:15px;'));
								$CI->make->H(5,'SPLIT ORDER',array('class'=>'headline text-center'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'center-div counter-center list-div','style'=>"margin-left:10px;"));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'title'));
								$CI->make->H(3,strtoupper($type),array('id'=>'trans-header','class'=>'receipt text-center text-uppercase'));
								$CI->make->H(5,$time,array('id'=>'trans-datetime','class'=>'receipt text-center'));
								$CI->make->append('<hr>');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							#LISTS
							$CI->make->sDivCol(12,'left',0,array('class'=>'body'));
								$CI->make->sUl(array('class'=>'trans-lists'));
								$CI->make->eUl();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'foot'));
								$CI->make->append('<hr>');
								$CI->make->H(3,'TOTAL: <span id="total-txt">0.00</span>',array('class'=>'receipt text-center'));
								$CI->make->H(5,'DISCOUNTS: <span id="discount-txt">0.00</span>',array('class'=>'receipt text-center'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'counter-center-btns center-div list-div','style'=>"margin-left:10px;"));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12);
								$CI->make->button(fa('fa-times fa-lg fa-fw').' Cancel',array('id'=>'cancel-btn','class'=>'btn-block counter-btn-red double'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
				$CI->make->eDivCol();
				#ITEMS
				$CI->make->sDivCol(8,'left',0);
					$CI->make->sDiv(array('class'=>'counter-split-right','ref'=>''));
						$CI->make->sDivRow();
							$CI->make->sDivCol(4);
								$CI->make->button(fa('fa-flask fa-lg fa-fw').'<br> Item Split',array('id'=>'select-items-btn','ref'=>'select-items','class'=>'split-bys btn-block counter-btn-red-gray double'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(4);
								$CI->make->button(fa('fa-bars fa-lg fa-fw').'<br> Number of Guest',array('id'=>'even-split-btn','ref'=>'even-split','class'=>'split-bys btn-block counter-btn-red-gray double'));
							$CI->make->eDivCol();
							// $CI->make->sDivCol(3);
							// 	$CI->make->button(fa('fa-users fa-lg fa-fw').'<br> Split By Guest',array('id'=>'split-by-guest-btn','ref'=>'split-by-guest','class'=>'split-bys btn-block counter-btn-red-gray double'));
							// $CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->button(fa('fa-save fa-lg fa-fw').'<br> Save',array('id'=>'save-split-btn','class'=>'btn-block counter-btn-green double'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->button(fa('fa-retweet fa-lg fa-fw'),array('id'=>'refresh-btn','class'=>'btn-block counter-btn-orange double'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDiv(array('class'=>'actions-div'));
							#SPLIT BY ITEMS
							$CI->make->sDiv(array('class'=>'select-items-div loads-div','style'=>'display:none;'));
								$CI->make->sDivRow();
									$CI->make->sDivCol(4,'left',0,array('id'=>'add-btn-div'));
										$CI->make->sDiv(array('style'=>'margin:50px;'));
											$CI->make->button(fa('fa-plus fa-lg fa-fw').'<br> Add Partition',array('id'=>'add-sel-block-btn','class'=>'btn-block counter-btn-green double'));
										$CI->make->eDiv();
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->eDiv();
							$CI->make->sDiv(array('class'=>'even-split-div loads-div','style'=>'display:none;'));
								$CI->make->sDivRow(array('style'=>'margin-top:20px;'));
									$CI->make->sDivCol(4,'left');
									$CI->make->eDivCol();
									$CI->make->sDivCol(2,'left');
										$CI->make->H(1,'2',array('style'=>'margin-top:25px;font-size:78px;','id'=>'even-spit-num'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(2,'left');
										$CI->make->button(fa('fa-caret-square-o-up fa-3x fa-fw'),array('id'=>'even-up-btn','num'=>'up','class'=>'btn-block counter-btn-red-gray double'));
										$CI->make->button(fa('fa-caret-square-o-down fa-3x fa-fw'),array('id'=>'even-down-btn','num'=>'down','class'=>'btn-block counter-btn-red-gray double'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->eDiv();
						$CI->make->eDiv();
					$CI->make->eDiv();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function combinePage($type=null,$time=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'counter'));
			$CI->make->sDivRow();
				#CENTER
				$CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'center-div counter-center list-div','style'=>"margin-left:10px;"));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'title'));
								$CI->make->H(3,strtoupper($type),array('id'=>'trans-header','class'=>'receipt text-center text-uppercase'));
								$CI->make->H(5,$time,array('id'=>'trans-datetime','class'=>'receipt text-center'));
								$CI->make->append('<hr>');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							#LISTS
							$CI->make->sDivCol(12,'left',0,array('class'=>'body body-taller'));
								$CI->make->sUl(array('class'=>'trans-lists'));
								$CI->make->eUl();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'left',0,array('class'=>'foot'));
								$CI->make->append('<hr>');
								$CI->make->H(3,'TOTAL: <span id="total-txt">0.00</span>',array('class'=>'receipt text-center'));
								$CI->make->H(5,'DISCOUNTS: <span id="discount-txt">0.00</span>',array('class'=>'receipt text-center'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'counter-center-btns center-div list-div','style'=>"margin-left:10px;"));
						$CI->make->sDivRow();
							$CI->make->sDivCol(12);
								$CI->make->button(fa('fa-times fa-lg fa-fw').' Cancel',array('id'=>'cancel-btn','class'=>'btn-block counter-btn-red double'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
				$CI->make->eDivCol();
				#CATEGORIES
				$CI->make->sDivCol(8,'left',0,array('style'=>'margin-top:10px;'));
					$CI->make->sDiv(array('class'=>'counter-combine-right'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(2,'left',0,array('style'=>'margin-top:70px;'));
								$CI->make->sDiv(array('class'=>'type-container','style'=>'margin-right:10px;'));
								$CI->make->eDiv();
							$CI->make->eDivCol();
							$CI->make->sDivCol(10,'left');
									$CI->make->sDivRow();
										$CI->make->sDivCol(6);
											$CI->make->sDiv(array('class'=>'orders-list-combine-div'));
												$CI->make->sDivRow();
													$CI->make->sDivCol(5);
														$CI->make->button(fa('fa-user fa-lg fa-fw').'<br> MY ORDERS',array('ref'=>'my','class'=>'my-all-btns btn-block counter-btn-red-gray double'));
													$CI->make->eDivCol();
													$CI->make->sDivCol(5);
														$CI->make->button(fa('fa-users fa-lg fa-fw').'<br> ALL ORDERS',array('id'=>'all','class'=>'my-all-btns btn-block counter-btn-red-gray double'));
													$CI->make->eDivCol();
													$CI->make->sDivCol(2);
														$CI->make->button(fa('fa-refresh fa-lg fa-fw'),array('id'=>'refresh-btn','class'=>'btn-block counter-btn-orange double'));
													$CI->make->eDivCol();
												$CI->make->eDivRow();
												$CI->make->sDivRow();
													$CI->make->sDivCol(12,'left',0,array('class'=>'orders-list-combine','terminal'=>'my','types'=>'all'));
													$CI->make->eDivCol();
												$CI->make->eDivRow();
											$CI->make->eDiv();
										$CI->make->eDivCol();
										$CI->make->sDivCol(6);
											$CI->make->sDiv(array('class'=>'orders-to-combine-div'));
												$CI->make->sDivRow();
													$CI->make->sDivCol(10);
														$CI->make->button(fa('fa-compress fa-lg fa-fw').'<br> GO COMBINE',array('id'=>'combine-btn','class'=>'btn-block counter-btn-green double'));
													$CI->make->eDivCol();
													$CI->make->sDivCol(2);
														$CI->make->button(fa('fa-times fa-lg fa-fw').'<br> CLEAR',array('id'=>'clear-btn','class'=>'btn-block counter-btn-red double'));
													$CI->make->eDivCol();
												$CI->make->eDivRow();
												$CI->make->sDivRow();
													$CI->make->sDivCol(12,'left',0,array('class'=>'orders-to-combine'));
													$CI->make->eDivCol();
												$CI->make->eDivRow();
											$CI->make->eDiv();
										$CI->make->eDivCol();
									$CI->make->eDivRow();
							$CI->make->eDivCol();
						$CI->make->eDivRow();

					$CI->make->eDiv();
				$CI->make->eDivCol();
				#CATEGORIES
				// $CI->make->sDivCol(2,'left',0,array('style'=>'margin-top:10px;'));
				// 	$CI->make->sDiv(array('class'=>'type-container','style'=>'margin-right:10px;'));
				// 	$CI->make->eDiv();
				// $CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function tablesPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'tables'));
			#NAVBAR
			$CI->make->sDiv(array('class'=>'select-table-div loads-div','id'=>'select-table'));
				$CI->make->sDiv(array('class'=>'nav-btns-con'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('class'=>'title bg-red'));
								$CI->make->H(3,'SELECT A TABLE',array('class'=>'headline text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->sDiv(array('class'=>'exit'));
								$CI->make->button(fa('fa-sign-out fa-lg fa-fw').' EXIT',array('id'=>'exit-btn','class'=>'btn-block tables-btn'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDiv();
				$CI->make->sDiv(array('id'=>'image-con'));
				$CI->make->eDiv();
			$CI->make->eDiv();
			$CI->make->sDiv(array('class'=>'no-guest-div loads-div','style'=>'display:none;'));
				$CI->make->sDiv(array('class'=>'nav-btns-con'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('class'=>'title bg-red'));
								$CI->make->H(3,'No. Of Guest',array('class'=>'headline text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->sDiv(array('class'=>'exit'));
								$CI->make->button(fa('fa-reply fa-lg fa-fw').' BACK',array('id'=>'back-btn','class'=>'btn-block tables-btn-red'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->append(onScrNumDotPad('guest-input','guest-enter-btn'));
				$CI->make->eDiv();
			$CI->make->eDiv();
			$CI->make->sDiv(array('class'=>'occupied-div loads-div','style'=>'display:none;'));
				$CI->make->sDiv(array('class'=>'nav-btns-con'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('class'=>'title bg-red'));
								$CI->make->H(3,'<span id="occ-num"></span> Is In Use',array('class'=>'headline text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->sDiv(array('class'=>'exit'));
								$CI->make->button(fa('fa-reply fa-lg fa-fw').' BACK',array('id'=>'back-occ-btn','class'=>'btn-block tables-btn-red'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow(array('style'=>'margin-top:10px;'));
						$CI->make->sDivCol(12);
							$CI->make->sDiv(array('class'=>'bg-orange'));
								$CI->make->H(3,'Table is currently in use. Choose from the following options to continue.',array('class'=>'headline text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow(array('style'=>'margin-top:10px;'));
						$CI->make->sDivCol(4);
							$CI->make->sDiv(array('style'=>'margin:10px;'));
								// $CI->make->button(fa('fa-search fa-lg fa-fw').' RECALL',array('id'=>'exit-btn','class'=>'btn-block tables-btn-red double'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->sDiv(array('style'=>'margin:10px;'));
								$CI->make->button(fa('fa-file fa-lg fa-fw').' Start New',array('id'=>'start-new-btn','class'=>'btn-block tables-btn-green double'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->sDiv(array('style'=>'margin:10px;'));
								// $CI->make->button(fa('fa-check-square-o fa-lg fa-fw').' Settle',array('id'=>'exit-btn','class'=>'btn-block tables-btn-orange double'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDiv(array('class'=>'occ-orders-div'));
					$CI->make->eDiv();
				$CI->make->eDiv();
			$CI->make->eDiv();

		$CI->make->eDiv();
	return $CI->make->code();
}
function deliveryPage($det=null,$type='delivery', $list=array(), $selected=null, $contacts=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'tables'));
			#NAVBAR
			$CI->make->sDiv(array('class'=>'select-table-div loads-div','id'=>'select-table'));
				$CI->make->sDiv(array('class'=>'nav-btns-con'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('class'=>'title bg-h-gray'));
								$CI->make->H(3,'Enter Customer Information',array('class'=>'header_line text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->sDiv(array('class'=>'exit'));
								$CI->make->button(fa('fa-sign-out fa-lg fa-fw').' EXIT',array('id'=>'exit-btn','class'=>'btn-block tables-btn'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDiv();

				$CI->make->sDiv(array('style'=>'margin:10px;'));
				$CI->make->sDivRow();
					$CI->make->sDivCol(4);
						$CI->make->sBox('default');
							$CI->make->sBoxBody();
								$CI->make->sDiv(array('style'=>'min-height: 547px; height: 547px;'));
									$CI->make->input(null,'search-customer',null,'Search number or Customer Name',array(),fa('fa-search'));
									$CI->make->sDiv(array('class'=>'listings'));
										$CI->make->sUl(array('id'=>'cust-search-list'));
										$CI->make->eUl();
									$CI->make->eDiv();
								$CI->make->eDiv();
							$CI->make->eBoxBody();
						$CI->make->eBox();
					$CI->make->eDivCol();
					$CI->make->sDivCol(8);
						$CI->make->sBox('default',array('class'=>'box-solid'));
							$CI->make->sBoxBody();
								$CI->make->sDiv(array('class'=>'cust-form', 'style'=>'min-height: 547px; height: 547px;'));
									$CI->make->sForm('customers/customer_details_db/true',array('id'=>'customer-form'));
									$CI->make->hidden('trans_type',$type);
									$CI->make->hidden('cust_id',iSetObj($det,'cust_id'), array('class'=>''));
									$CI->make->hidden('cust_addr_id',iSetObj($det,'cust_addr_id'));
										
									$CI->make->sDivRow();

										$CI->make->sDivCol(4);
											$CI->make->input('First Name','fname',iSetObj($det,'fname'),'Type First Name',array('class'=>'rOkay key-ins'));
											$CI->make->input('Middle Name','mname',iSetObj($det,'mname'),'Type Middle Name',array());
											$CI->make->input('Last Name','lname',iSetObj($det,'lname'),'Type Last Name',array('class'=>'rOkay key-ins'));
										$CI->make->eDivCol();
										$CI->make->sDivCol(4);
											$CI->make->input('Suffix','suffix',iSetObj($det,'suffix'),'Type Suffix',array('class'=>' key-ins'));
											$CI->make->input('Email Address','email',iSetObj($det,'email'),'Type Email Address',array('class'=>'rOkay key-ins'));
										$CI->make->eDivCol();

										$CI->make->sDivCol(4);
											$CI->make->sDiv(array('id'=>'list-phone', 'class'=>'list-phone-div'));
												$CI->make->sBox('primary');
												    $CI->make->sBoxHead();
												        $CI->make->boxTitle('Contact Information');
												    $CI->make->eBoxHead();
												    $CI->make->sBoxBody();
												        $btn = "<a id='add-new-phone'>Add</a>";
														$CI->make->input(null,'phone_no',null,'Type Phone Number',array(),null, $btn);
														
												        $listPhone = array();
												        // $listPhone[fa('fa-plus').' Add New'] = array('id'=>'add-new-phone','class'=>'grp-list');
												        if(!empty($contacts))
												        {
													        foreach($contacts as $key=>$val){
													        	$span = '<span/>';
													        	if($val->default_no == 1)
													        		$span = ' <span class="label label-primary" style="float: right;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp; Default</span>';

													            $name = $val->phone_no .' '. $span;
													            $listPhone[$name] = array('class'=>'grp-btn grp-list','id'=>'grp-list-'.$val->id,'ref'=>$val->id);
													        }
													    }else{
													    	$listPhone['No phone number.'] = array('class'=>'grp-btn grp-no-list');
													    }
												        $CI->make->listGroup($listPhone,array('id'=>'add-grp-list-div'));
												    $CI->make->eBoxBody();
												$CI->make->eBox();
											$CI->make->eDiv();
										$CI->make->eDivCol();
									$CI->make->sDivRow();


									$CI->make->eDivRow();
										$CI->make->sDivCol(10);
										$CI->make->eDivCol();
										$CI->make->sDivCol(2);
											$CI->make->A(fa('fa-external-link fa-fw ').' Add New Address','cashier/pop_new_address',
														array('class'=>'btn bg-orange',
															'style'=>'float:right;',	
															'id'=>'add-new-address',
															'rata-title'=>'Add New Address',
															'rata-pass'=>'cashier/new_cust_address_row',
															'rata-form'=>'new_address_form',
															'return'=>false));
										$CI->make->eDivCol();

										$CI->make->sDivCol(12);
										$CI->make->eDivCol();
										$CI->make->sDivCol();
											$CI->make->sDiv(array('class'=>'table-responsive', 'style'=>'margin-top:1%; height: 150px; overflow-x: auto;'));
												$CI->make->sTable(array('class'=>'table table-striped','id'=>'address-tbl'));
													$CI->make->sRow();
														$CI->make->th('Street No',array('style'=>'width:10%;'));
														$CI->make->th('Street Address',array('style'=>'width:15%;'));
														$CI->make->th('City',array('style'=>'width:20%;'));
														$CI->make->th('Region',array('style'=>'width:20%;'));
														$CI->make->th('Zip Code',array('style'=>'width:10%;'));
														$CI->make->th('Landmark',array('style'=>'width:20%;'));
													$CI->make->eRow();
														if(count($list) > 0){
															foreach ($list as $val) {
																$class = 't-rows';
																if($val->id == $selected)
																	$class .= ' active';

																$CI->make->sRow(array('class'=>$class, 'ref'=>$val->id,'id'=>'row-'.$val->id));
															        $CI->make->td(ucwords($val->street_no));
															        $CI->make->td(ucwords($val->street_address));
															        $CI->make->td(ucwords($val->city));
															        $CI->make->td(ucwords($val->region));
															        $CI->make->td(ucwords($val->zip));
															        $CI->make->td(ucwords($val->landmark));
															    $CI->make->eRow();
															}
														}else{
														$CI->make->sRow(array('class'=>'no-rows'));
														        $CI->make->td('No available address.', array('colspan'=>'6'));
															$CI->make->eRow();
														}
												$CI->make->eTable();
											$CI->make->eDiv();
										$CI->make->eDivCol();
									$CI->make->eDivRow();

									$CI->make->sDivRow();
										$CI->make->sDivCol(6);
										$CI->make->eDivCol();
										$CI->make->sDivCol(3);
											$CI->make->sDiv(array('style'=>'display: -webkit-box;'));
												$CI->make->button('Continue',array('id'=>'continue-btn','class'=>'btn-block tables-btn-green','style'=>'margin-top:18px; margin-right: 5px;', 'disabled'=>'disabled'),'primary');
												$CI->make->button('Clear',array('id'=>'clear-btn','class'=>'btn-block tables-btn-red','style'=>'margin-top:18px;'),'primary');
											$CI->make->eDiv();
										$CI->make->eDivCol();
									$CI->make->eDivRow();

								$CI->make->eDiv();
							$CI->make->eBoxBody();
						$CI->make->eBox();
					$CI->make->eDivCol();
				$CI->make->eDivRow();
				$CI->make->eDiv();

			$CI->make->eDiv();
		$CI->make->eDiv();
	return $CI->make->code();
}
function customer_no_list($cust_id=null, $lists=null){
	$CI =& get_instance();
	$CI->make->sBox('primary');
	    $CI->make->sBoxHead();
			$CI->make->boxTitle('Contact Information');
	    $CI->make->eBoxHead();
	    $CI->make->sBoxBody();
	     	$btn = "<a id='add-new-phone'>Add</a>";
			$CI->make->input(null,'phone_no',null,'Type Phone Number',array(),null, $btn);

	        $list = array();
	        // $list[fa('fa-plus').' Add New'] = array('id'=>'add-new-phone','class'=>'grp-list');
	        if(!empty($lists)){
		        foreach($lists as $key=>$val){
		            $span = '<span/>';
		        	if($val->default_no == 1)
		        		$span = ' <span class="label label-primary" style="float: right;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp; Default</span>';

		            $name = $val->phone_no .' '. $span;
		            $list[$name] = array('class'=>'grp-btn grp-list','id'=>'grp-list-'.$val->id,'ref'=>$val->id);
		        }
		    }else
		    {
		        $list['No phone number.'] = array('class'=>'grp-btn grp-no-list');
		    }
	        $CI->make->listGroup($list,array('id'=>'add-grp-list-div'));
	    $CI->make->eBoxBody();
	$CI->make->eBox();
	
	return $CI->make->code();
}
function customer_address_tbl($cust_id=null, $list=array()){

	$CI =& get_instance();
		if(count($list) > 0){
			foreach ($list as $val) {
				$CI->make->sRow(array('class'=>'t-rows', 'ref'=>$val->id,'id'=>'row-'.$val->id));
		            $CI->make->td(ucwords($val->street_no));
		            $CI->make->td(ucwords($val->street_address));
			        $CI->make->td(ucwords($val->city));
		            $CI->make->td(ucwords($val->region));
		            $CI->make->td(ucwords($val->zip));
		            $CI->make->td(ucwords($val->landmark));
		        $CI->make->eRow();
			}
		}else{
			$CI->make->sRow(array('class'=>'no-rows'));
		            $CI->make->td('No available address.', array('colspan'=>'6'));
		   	$CI->make->eRow();
		}
	return $CI->make->code();	
}
function pop_new_address_form(){
	$CI =& get_instance();
	$CI->make->sForm("cashier/new_cust_address_row",array('id'=>'new_address_form'));
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Street No.','street_no',iSetObj('','street_no'),'Type Street No.',array('class'=>'rOkay'));
				$CI->make->input('Street Address','street_address',iSetObj('','street_address'),'Type Street Address',array('class'=>'rOkay'));
				$CI->make->input('City','city',iSetObj('','city'),'Type City',array('search-url'=>'cashier/city_search', 'class'=>'rOkay'));
				$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->input('Region','region',iSetObj('','region'),'Type Region',array('class'=>'rOkay'));
				$CI->make->input('Zip Code','zip',iSetObj('','zip'),'Type Zip Code',array('class'=>''));
				$CI->make->input('Landmark','landmark',iSetObj('','landmark'),'Type landmark',array('class'=>'rOkay'));
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function branches_list_tbl($cust_id=null,$branches=null, $info=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'tables'));
			$CI->make->sDiv(array('class'=>'select-table-div','id'=>'select-table'));

				$CI->make->sDiv(array('class'=>'nav-btns-con'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(10);
							$CI->make->sDiv(array('class'=>'title bg-h-gray'));
								$CI->make->H(3,'Select Branch',array('class'=>'header_line  text-center text-uppercase','style'=>'padding:12px;'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->sDiv(array('class'=>'exit'));
								$CI->make->button(fa('fa-reply fa-lg fa-fw').' Back',array('id'=>'back-btn','class'=>'btn-block tables-btn'));
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDiv();

				$CI->make->sDiv(array('style'=>'margin:10px;'));

					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->sBox('default');
								$CI->make->sBoxBody();
									$CI->make->sDiv(array('style'=>'min-height: 520px; height: 520px;  overflow-y: auto;'));
										$CI->make->sDiv(array('class'=>'listings'));
											$CI->make->sUl(array('id'=>'branch-list'));
												if(count($branches) > 0)
													foreach($branches as $key => $val)
													{
														$br = '<h4><b>'.$val->branch_code . '</b> : ' . $val->branch_name.'</h4>';
														$br.= '<h5>'.$val->branch_desc.'</h5>';
														$br.= '<h5 data_travel_time='.$val->travel_time.'>'.$val->travel_time.' mins</h5>';
														$CI->make->li($br, array('data-cust-id'=>$cust_id,'class'=>'list-branch','ref'=>$val->branch_id,'style'=>'cursor: pointer; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(221, 221, 221);'));
													}
												else{
													$CI->make->li('<h5>No available branch for the customer\'s address</h5>', array('style'=>'cursor: pointer; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(221, 221, 221);'));												
												}
											$CI->make->eUl();
										$CI->make->eDiv();
									$CI->make->eDiv();
								$CI->make->eBoxBody();
							$CI->make->eBox();
						$CI->make->eDivCol();
						$CI->make->sDivCol(8);
							$CI->make->sBox('default');
								$CI->make->sBoxBody();
									$CI->make->sDiv(array('style'=>'min-height: 520px; height: 520px;'));
									$CI->make->sForm('branches/branch_details_db',array('id'=>'branch_details_form'));
										$CI->make->hidden('branch_id','', array('class'=>'rOkay'));
										$CI->make->hidden('travel_time');
										$CI->make->hidden('customer_id',$cust_id, array('class'=>'rOkay'));
										
										$CI->make->sDivRow();
											$CI->make->sDivCol(4);
												$CI->make->input('Branch Name','branch_name',iSetObj($info,'branch_name'),'Type Branch name',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(4);
												$CI->make->input('Branch Code','branch_code',iSetObj($info,'branch_code'),'Type Branch code',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(4);
												$CI->make->currenciesDrop('Currency','currency',iSetObj($info,'currency'),'Select Currency',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
										$CI->make->eDivRow();
										$CI->make->sDivRow();
											$CI->make->sDivCol(12);
												$CI->make->input('Branch Description','branch_desc',iSetObj($info,'branch_desc'),'Type Description',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
								    	$CI->make->eDivRow();
								    	$CI->make->sDivRow();
											$CI->make->sDivCol(6);
												$CI->make->input('Contact No.','contact_no',iSetObj($info,'contact_no'),'Type Contact no.',array('disabled'=>'disabled'),fa('fa-phone fa-fw'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(6);
												$CI->make->input('Delivery No.','delivery_no',iSetObj($info,'delivery_no'),'Type Delivery no.',array('disabled'=>'disabled'),fa('fa-truck fa-fw'));
											$CI->make->eDivCol();
									    $CI->make->eDivRow();
									    $CI->make->sDivRow();
											$CI->make->sDivCol(6);
												$CI->make->input('Address','address',iSetObj($info,'address'),'Type Address',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(6);
												$CI->make->input('Base Location','base_loc',iSetObj($info,'base_loc'),'Type Base Location',array('disabled'=>'disabled'));
											$CI->make->eDivCol();
										$CI->make->eDivRow();
									$CI->make->eForm();
										$CI->make->sDivRow();
											$CI->make->sDivCol(6);
											$CI->make->eDivCol();
											$CI->make->sDivCol(3);
												$CI->make->sDiv(array('style'=>'display: -webkit-box;'));
													if(count($branches) > 0)
														$CI->make->button('Continue',array('id'=>'continue-btn','ref'=>$cust_id,'class'=>'btn-block tables-btn-green','style'=>'margin-top:18px; margin-right: 5px;'),'primary');
													else
														$CI->make->button('Cancel',array('id'=>'cancel-btn','ref'=>$cust_id,'class'=>'btn-block tables-btn-green','style'=>'margin-top:18px; margin-right: 5px;'),'primary');
												
													$CI->make->button('Clear',array('id'=>'clear-btn','class'=>'btn-block tables-btn-red','style'=>'margin-top:18px;'),'primary');
												$CI->make->eDiv();
											$CI->make->eDivCol();
										$CI->make->eDivRow();
									$CI->make->eDiv();
								$CI->make->eBoxBody();
							$CI->make->eBox();
						$CI->make->eDivCol();

					$CI->make->eDivRow();
					
				$CI->make->eDiv();
			$CI->make->eDiv();
		$CI->make->eDiv();

	return $CI->make->code();
}
function paymentMethodPage($ord=null,$det=null,$discs=null,$totals=null,$charges=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'settle','sales'=>$ord['sales_id'],'type'=>$ord['type'],'balance'=>$ord['balance']));
			$CI->make->sDivRow();
				$CI->make->sDivCol(5,'left',0,array('class'=>'settle-left'));
					$CI->make->sBox('default',array('class'=>'box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-red'));
							$CI->make->boxTitle('BALANCE DUE');
							$CI->make->boxTitle('PHP <span id="balance-due-txt">'.num($ord['balance']).'</span>',array('class'=>'pull-right','style'=>'margin-right:10px;'));
						$CI->make->eBoxHead();
						$CI->make->sBoxBody();
							$CI->make->sDiv(array('class'=>'order-view-list'));
								$CI->make->H(3,strtoupper($ord['type'])." #".$ord['sales_id'],array('class'=>'receipt text-center'));
						        $CI->make->H(5,sql2DateTime($ord['datetime']),array('class'=>'receipt text-center'));
						        $CI->make->append('<hr>');
						        $CI->make->sDiv(array('class'=>'body'));
						            $CI->make->sUl();
						                $total = 0;
						                foreach ($det as $menu_id => $opt) {
						                    $qty = $CI->make->span($opt['qty'],array('class'=>'qty','return'=>true));
						                    $name = $CI->make->span($opt['name'],array('class'=>'name','return'=>true));
						                    $cost = $CI->make->span('₱ '.num($opt['price']),array('class'=>'cost','return'=>true));
						                    $price = $opt['price'];
						                    $CI->make->li($qty." ".$name." ".$cost);
						                    if(count($opt['modifiers']) > 0){
						                        foreach ($opt['modifiers'] as $mod_id => $mod) {
						                            $name = $CI->make->span($mod['name'],array('class'=>'name','style'=>'margin-left:36px;','return'=>true));
						                            $cost = "";
						                            if($mod['price'] > 0 )
						                                $cost = $CI->make->span('₱ '.num($mod['price']),array('class'=>'cost','return'=>true));
						                            $CI->make->li($name." ".$cost);
						                            $price += $mod['price'];
						                        }
						                    }
						                    $total += $opt['qty'] * $price  ;
						                }
						                if(count($charges) > 0){
						                    foreach ($charges as $charge_id => $ch) {
						                        $qty = $CI->make->span(fa('fa fa-tag'),array('class'=>'qty','return'=>true));
						                        $name = $CI->make->span($ch['name'],array('class'=>'name','return'=>true));
						                        $tx = $ch['amount'];
						                        if($ch['absolute'] == 0)
						                            $tx = $ch['amount']."%";
						                        else
						                            $tx = '₱ '.num($ch['amount']);
						                        	
						                        $cost = $CI->make->span($tx,array('class'=>'cost','return'=>true));
						                        $CI->make->li($qty." ".$name." ".$cost);
						                    }
						                }

						                if(count($discs > 0)){
						                	foreach ($discs as $disc_id => $ch) {

						                		$qty = $CI->make->span(fa('fa fa-tag'),array('class'=>'qty','return'=>true));
						                        $name = $CI->make->span($ch['disc_code'],array('class'=>'name','return'=>true));
						                        $tx = '₱ '.num($ch['amount']);
						                        	
						                        $cost = $CI->make->span("(".$tx.")",array('class'=>'cost','return'=>true));
						                        $CI->make->li($qty." ".$name." ".$cost);
						                    }
						                }
						            $CI->make->eUl();
						        $CI->make->eDiv();
							        if($ord['instruction'])
							        {
								        $CI->make->append('<hr>');
		 								$CI->make->sDiv(array('class'=>'footer', 'style'=>'position: relative;height: 40px; overflow: hidden; width: 100%;'));
								        $CI->make->H(5,'&nbsp;&nbsp;<strong>Instruction: &nbsp;&nbsp;</strong> '.$ord['instruction'],array('class'=>'receipt text-left', 'style'=>'text-align:justify;'));
									    $CI->make->eDiv();
							        }
							        $CI->make->append('<hr>');
								    $total = $totals['total'] + $totals['discount'];
									$net = $totals['total'] - $totals['discount'];

									$CI->make->H(4,'TOTAL: ₱'.num($total),array('class'=>'receipt text-center'));
									$CI->make->H(5,'DISCOUNT: ₱'.num($totals['discount']),array('class'=>'receipt text-center'));
									$CI->make->H(5,'NET AMOUNT DUE: ₱'.num($total-$totals['discount']),array('class'=>'receipt text-center'));

							$CI->make->eDiv();
						$CI->make->eBoxBody();
						$CI->make->sBoxFoot();
							$CI->make->sDivRow();
								$CI->make->sDivCol(6,'left');
									$CI->make->button(fa('fa-reply fa-lg fa-fw').' Recall',array('id'=>'recall-btn','type'=>$ord['type'],'sale'=>$ord['sales_id'],'class'=>'btn-block settle-btn-orange double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(6,'left');
									$CI->make->button(fa('fa-times fa-lg fa-fw').' Cancel',array('id'=>'cancel-btn','type'=>$ord['type'],'class'=>'btn-block settle-btn-red double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxFoot();
					$CI->make->eBox();
				$CI->make->eDivCol();
				$CI->make->sDivCol(7,'left',0,array('class'=>'settle-right'));

					$CI->make->sBox('default',array('class'=>'loads-div select-payment-div box-solid bg-dark-green'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('&nbsp;');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-dark-green'));
								$buttons = array("cash"	=> fa('fa-money fa-lg fa-fw')."<br> CASH",
												 "credit-card"	=> fa('fa-credit-card fa-lg fa-fw')."<br> CREDIT CARD",
												 "debit-card"	=> fa('fa-credit-card fa-lg fa-fw')."<br> DEBIT CARD",
												 );
								$CI->make->sDivRow();
										$CI->make->H(3,'SELECT PAYMENT METHOD',array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
										foreach ($buttons as $id => $text) {
											$CI->make->sDivCol(6,'left',0,array("style"=>'margin-bottom:10px;'));
												$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block settle-btn-green double'));
											$CI->make->eDivCol();
										}
								$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div cash-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' CASH PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-red-white'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(3);
									$CI->make->sDiv(array('class'=>'shorcut-btns'));
										$buttons = array(
													 "5"	=> 'PHP 5',
													 "10"	=> 'PHP 10',
													 "20"	=> 'PHP 20',
													 "50"	=> 'PHP 50',
													 "100"	=> 'PHP 100',
													 "200"	=> 'PHP 200',
													 "500"	=> 'PHP 500',
													 "1000"	=> 'PHP 1000'
													 );
										$CI->make->sDivRow(array('style'=>'margin-top:10px;margin-left:10px;'));
											foreach ($buttons as $id => $text) {
													$CI->make->sDivCol(12,'left',0);
														$CI->make->button($text,array('val'=>$id,'class'=>'amounts-btn btn-block settle-btn-red-gray'));
													$CI->make->eDivCol();
											}
										$CI->make->eDivRow();
									$CI->make->eDiv();
								$CI->make->eDivCol();
								$CI->make->sDivCol(9);
									$CI->make->append(onScrNumDotPad('cash-input','cash-enter-btn'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
						$CI->make->sBoxFoot();
							$CI->make->sDivRow();
								$CI->make->sDivCol(4,'left');
									$CI->make->button('Exact Amount',array('id'=>'cash-exact-btn','amount'=>num($ord['balance']),'class'=>'btn-block settle-btn double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button('Next Amount',array('id'=>'cash-next-btn','amount'=>num(round($ord['balance'])),'class'=>'btn-block settle-btn-red-gray double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-reply fa-lg fa-fw').' Change Payment Method',array('id'=>'cancel-cash-btn','class'=>'btn-block settle-btn-red double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxFoot();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div debit-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' DEBIT PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow(array('style'=>'margin:auto 0;'));
								$CI->make->sDivCol(6);
									$CI->make->input('Card #','debit-card-num','','',array('maxlength'=>'30',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','debit-amt',number_format($ord['balance'],2),'',array('maxlength'=>'10',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-debit-target',
										'#debit-card-num',
										'debit-enter-btn',
										'cancel-debit-btn',
										'Change Payment Method'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div credit-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' CREDIT PAYMENT');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow(array('style'=>'margin:auto 0;'));
								$buttons = array(
									"Master Card"	=> fa('fa-cc-mastercard fa-2x')."<br/>Master Card",
									"VISA"	=> fa('fa-cc-visa fa-2x')."<br/>VISA",
									"AmEx"	=> fa('fa-cc-amex fa-2x')."<br/>American Express",
									"Discover"	=> fa('fa-cc-discover fa-2x')."<br/>Discover",
								);
								foreach ($buttons as $id => $text) {
									$CI->make->sDivCol(3,'left',0,array('style'=>'padding:0;margin:0'));
										$CI->make->button($text,array('value'=>$id,'class'=>'credit-type-btn double settle-btn-teal btn-block'));
									$CI->make->eDivCol();
								}
							$CI->make->eDivRow();
							$CI->make->sDivRow(array('style'=>'margin:auto 0;padding:10px 0 8px;'));
								$CI->make->sDivCol(6,'left');
									$CI->make->hidden('credit-type-hidden','Master Card');
									$CI->make->input('Card #','credit-card-num','','',array('maxlength'=>'30',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Approval Code','credit-app-code','','',array('maxlength'=>'15',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','credit-amt',number_format($ord['balance'],2),'',array('maxlength'=>'10',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6,'left');
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-credit-target',
										'#credit-card-num',
										'credit-enter-btn',
										'cancel-credit-btn',
										'Change Payment Method'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div gc-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-green'));
							$CI->make->boxTitle(' GIFT CHEQUE');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('style'=>'background-color:#F4EDE0;'));
							$CI->make->sDivRow();
								$CI->make->sDivCol(6);
									$CI->make->hidden('hid-gc-id');
									$CI->make->input('Gift Cheque code','gc-code','','',array(
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
									$CI->make->input('Amount','gc-amount','','',array('readonly'=>'readonly',
										'style'=>
											'width:100%;
											height:100%;
											font-size:34px;
											font-weight:bold;
											text-align:right;
											border:none;
											border-radius:5px !important;
											box-shadow:none;
											',
										)
									);
								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
									$CI->make->append(onScrNumOnlyTarget(
										'tbl-gc-target',
										'#gc-code',
										'gc-enter-btn',
										'cancel-gc-btn',
										'Change Payment Method',false));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div after-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('&nbsp;');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-dark-green'));
							$CI->make->sDiv(array('class'=>'body'));
								$CI->make->H(3,'PAYMENT METHOD: '.strong('<span id="payment-method-txt"></span>'),array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
								$CI->make->H(3,'DELIVERY CODE: '.strong('<span id="delivery-code-txt"></span>'),array('class'=>'text-center receipt','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
								$CI->make->H(3,'AMOUNT TO BE TENDERED: PHP '.strong('<span id="amount-tendered-txt"></span>'),array('class'=>'text-center receipt tendered','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
								$CI->make->H(3,'CHANGE DUE: PHP '.strong('<span id="change-due-txt"></span>'),array('class'=>'text-center receipt changed','style'=>'margin-top:0;margin-bottom:25px;padding:0;color:#fff'));
							$CI->make->eDiv();
							$CI->make->sDivRow();
								// $CI->make->sDivCol(4,'left');
								// 	$CI->make->button(fa('fa-plus fa-lg fa-fw').' Additonal Payment',array('id'=>'add-payment-btn','class'=>'btn-block settle-btn-teal double'));
								// $CI->make->eDivCol();
								// $CI->make->sDivCol(4,'left');
								// 	$CI->make->button(fa('fa-print fa-lg fa-fw').' Print Receipt',array('id'=>'print-btn','class'=>'btn-block settle-btn-orange double','ref'=>$ord['sales_id']));
								// $CI->make->eDivCol();

								$CI->make->sDivCol(8);
									$CI->make->button(fa('fa-check fa-lg fa-fw').' Finish',array('id'=>'finished-btn','class'=>'btn-block settle-btn-green double'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(4,'left');
									$CI->make->button(fa('fa-reply fa-lg fa-fw').' Change Payment Method',array('id'=>'cancel-cash-btn','class'=>'btn-block settle-btn-red double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

					$CI->make->sBox('default',array('class'=>'loads-div transactions-payment-div box-solid'));
						$CI->make->sBoxHead(array('class'=>'bg-dark-green'));
							$CI->make->boxTitle('Transactions');
						$CI->make->eBoxHead();
						$CI->make->sBoxBody(array('class'=>'bg-red-white'));
							$CI->make->sDiv(array('class'=>'body'));

							$CI->make->eDiv();
							$CI->make->sDivRow();
								$CI->make->sDivCol(12,'left');
									$CI->make->button(fa('fa-times fa-lg fa-fw').' Close',array('id'=>'trsansactions-close-btn','class'=>'btn-block settle-btn-orange double'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eBoxBody();
					$CI->make->eBox();

				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}

function pop_order_date_form(){
	$CI =& get_instance();
		$CI->make->sForm("dine/pop_order_date_form_db",array('id'=>'pop_order_date_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol();
					$CI->make->date('Date','delivery_date', date('m/d/y') );
					$CI->make->time('Time','delivery_time');
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}

?>