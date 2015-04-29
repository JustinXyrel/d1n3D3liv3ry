<?php

function makeTransListDisplay($branches, $pending=null)
{	
	$CI =& get_instance();

	$CI->load->model('site/site_model');
	$time = $CI->site_model->get_db_now();
	$sub_code = '';
	
	$CI->make->sDivRow();
		$CI->make->sDivCol(12);
			
			$CI->make->sDivRow();
				$CI->make->sDivCol(12);

					$new_p_ids = $CI->session->userdata('pending');
					$count = count($new_p_ids);
					$CI->make->H(4,'<strong class="title_pending">Pending Orders &nbsp;<span class="badge">'.$count.'</span></strong>
									<strong class="title_cancel">Cancelled Orders &nbsp;<span class="badge">0</span></strong>
									<strong class="title_unprocessed">Unprocessed Orders &nbsp;<span class="badge">0</span></strong>
									',array());
					$CI->make->sDiv(array('class'=>'table-responsive', 'style'=>'height:150px; overflow-x:auto; margin-bottom: 10px;'));
						$CI->make->sTable(array('class'=>'table table-striped table-bordered"','id'=>'pending-trans-tbl'));
							$CI->make->sRow();
								$CI->make->th('Date',array('style'=>'width:20%;'));
								$CI->make->th('Order #',array());
								$CI->make->th('Actual time',array());
								$CI->make->th('Customer Name',array());
								$CI->make->th('',array('style'=>'width:8%;'));
								$CI->make->th('',array('style'=>'width:8%;'));
							$CI->make->eRow();

							if(count($pending) > 0){
								foreach ($pending as $val) {
									$cust_name = $val->fname.' '.$val->mname. ' ' .$val->lname;		

									$CI->make->sRow(array('class'=>'t-rows row-'.$val->sales_id));
							            $CI->make->td(sql2DateTime($val->datetime));
							            $CI->make->td($val->trans_ref);	
										$CI->make->td(tagWord(strtoupper(ago($val->datetime,$time) ), 'warning' ));
										$CI->make->td(ucwords($cust_name));	
										if($val->confirmed == 3)
											$sub_code = tagWord('Cancelled', 'danger' );
									
										$CI->make->td('<span class="result">'.$sub_code.'</span>');	
										if($val->tr_inactive == 0) 
						            		$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'Void Transaction', 'id'=>'void-'.$val->sales_id,'class'=>'void-transaction','ref'=>$val->sales_id,'return'=>true));
										if($val->tr_inactive == 1 || $val->completed == 1 || $val->delivered == 1)
											$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('id'=>'voided-'.$val->sales_id,'ref'=>$val->sales_id, 'style'=> 'color: #DB9598;', 'return'=>true));
											$a .= $CI->make->A(fa('fa-book fa-fw fa-lg'),base_url().'trans_order/view_transaction/'.$val->sales_id, array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'View Details',  'id'=>'view-'.$val->sales_id,'class'=>'view-transaction','ref'=>$val->sales_id,'return'=>true));
										$CI->make->td($a);						            
										$CI->make->eRow();
								}
							}else{
								$CI->make->sRow(array('class'=>'t-rows'));
							        $CI->make->td('No transactions.', array('colspan'=>5));
							    $CI->make->eRow();
							}

						$CI->make->eTable();
					$CI->make->eDiv();
				$CI->make->eDivCol();				
			$CI->make->eDivRow();

			$CI->make->sTab();
				
				$tabs = array(
					"Open <span></span>" => array('href'=>'#details','ref'=>'open','class'=>'tab_link','load'=>'trans_order/load_trans_list/open','id'=>'details_link'),
					"Voided" => array('href'=>'#details','ref'=>'voided','class'=>'tab_link','load'=>'trans_order/load_trans_list/voided','id'=>'details_link_voided'),
				);

				if(isset($branches))
					foreach($branches as $key=>$val)
						$tabs[$val->branch_code . "<span></span>"] = array('title'=>$val->branch_desc, 'ref'=>$val->branch_id, 'href'=>'#details','class'=>'tab_link','load'=>'trans_order/load_trans_list/'.$val->branch_id,'id'=>'details_link'.$val->branch_id);
					
				$CI->make->tabHead($tabs,null,array());
				$CI->make->sTabBody();
					$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
					$CI->make->eTabPane();
				$CI->make->eTabBody();
					
			$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function makeLoadVoidList($list){
	$CI =& get_instance();
		$CI->make->sDivRow();
			// $CI->make->sDivCol(4);
			// 	$CI->make->input(null,'trans-search',null,'Transaction Reference',array('search-url'=>'trans_order/transaction_search'),'',fa('fa-search'));
			// 	$CI->make->hidden('sales_id',null,array());
			// $CI->make->eDivCol();
			$CI->make->sDivCol();
				$CI->make->sDiv(array('class'=>'table-responsive', array( 'style'=>'height:700px; overflow-x:auto;')));
					$CI->make->sTable(array('class'=>'table table-striped','id'=>'transactions-tbl'));
						$CI->make->sRow();
							$CI->make->th('Date Voided',array('style'=>'width:3%;'));
							$CI->make->th('Order #',array('style'=>'width:3%;'));
							$CI->make->th('Branch Name',array('style'=>'width:15%;'));
							$CI->make->th('',array('style'=>'width:5%;'));
						$CI->make->eRow();
						if(count($list) > 0){
							foreach ($list as $val) {
								$CI->make->sRow(array('class'=>'t-rows'));
						            $CI->make->td(sql2DateTime($val->datetime));
						            $CI->make->td($val->trans_ref);
									$CI->make->td($val->branch_name);	
									$a = $CI->make->A(fa('fa-book fa-fw fa-lg'),base_url().'trans_order/view_transaction/'.$val->void_ref, array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'View Details',  'id'=>'view-'.$val->void_ref,'class'=>'view-transaction','ref'=>$val->void_ref,'return'=>true));
									$CI->make->td($a);							            
								$CI->make->eRow();
							}
						}else{
							$CI->make->sRow(array('class'=>'t-rows'));
						        $CI->make->td('No transactions.', array('colspan'=>2));
						    $CI->make->eRow();
						}
					$CI->make->eTable();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();

	return $CI->make->code();
}
function makeLoadTransList($list){
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input(null,'trans-search',null,'Transaction Reference',array('search-url'=>'trans_order/transaction_search'),'',fa('fa-search'));
				$CI->make->hidden('sales_id',null,array());
			$CI->make->eDivCol();
			$CI->make->sDivCol();
				$CI->make->sDiv(array('class'=>'table-responsive', 'style'=>'height:200px; overflow-x:auto; margin-bottom: 10px;'));
					$CI->make->sTable(array('class'=>'table table-striped','id'=>'transactions-tbl'));
						$CI->make->sRow();
							$CI->make->th('Order #',array('style'=>'width:3%;'));
							$CI->make->th('Date',array('style'=>'width:10%;'));
							$CI->make->th('Customer Name',array('style'=>'width:10%;'));
							$CI->make->th('Preparation Time',array('style'=>'width:7%;'));
							$CI->make->th('Status',array('style'=>'width:5%;'));
							$CI->make->th('Progress',array('style'=>'width:15%;'));
							$CI->make->th('',array('style'=>'width:5%;'));
						$CI->make->eRow();
						if(count($list) > 0){
							foreach ($list as $val) {
								$cust_name = $val->fname.' '.$val->mname. ' ' .$val->lname;								
								$cust_addr = $val->street_no . " " . $val->street_address . ", " . $val->city . ", " . $val->region. ", " . $val->zip. ", " . $val->landmark;

								$CI->make->sRow(array('class'=>'t-rows'));
						            $CI->make->td($val->trans_ref);
						            $CI->make->td(sql2DateTime($val->datetime));
						         	$CI->make->td(ucwords($cust_name));
						            $CI->make->td($val->prep_time . ' mins');

 									// if($val->completed == 1)
						    //         	$status = 'Dispatched';
						    //         else
					     //        		$status = 'On Process';
					     //        	if($val->tr_inactive == 0 && is_null($val->start_time))
					     //        		$status = 'Pending'; 
						    //         if($val->tr_inactive == 1)
						    //         	$status = 'Voided';
						    //         if($val->tr_inactive == 0 && $val->delivered == 1)
						    //         	$status = 'Delivered';
						    //         if($val->on_hold == 1)
						    //         	$status = 'On Hold';
						            
						            
						            			if(!is_null($val->rider_id))
								            		$status = 'Dispatched';
									            else
								            		$status = 'On Process';
								            	if($val->tr_inactive == 0 && is_null($val->start_time))
								            		$status = 'Pending'; 
									            if($val->tr_inactive == 1)
									            	$status = 'Voided';
									            if($val->tr_inactive == 0 && $val->delivered == 1)
									            	$status = 'Delivered';
									            if($val->on_hold == 1)
									            	$status = 'On Hold';
									         
									            $CI->make->td($status);


									            $start_time = $val->start_time;
												$complete_time = $val->complete_time;						          	
									            $dateNow = $CI->site_model->get_db_now('sql');
												$mins = 0;
												$max = $val->prep_time;
												
												if(is_null($complete_time))
													$mins = getInterval($start_time, $dateNow);
												else
													$mins = getInterval($start_time, $complete_time);
																				
												$danger = 'progress-bar progress-bar-danger';
												$ok = 'progress-bar progress-bar-success';

												$bar1 = $bar2 = $bar3 = 'progress-bar progress-bar-warning';
												$bar1 = ($status == 'On Process') ? $ok : $bar1;
												$bar1 = (($mins > $max) && ($status == 'On Process')) ? $danger : $bar1;
												
												$bar2 = ($status == 'Dispatched') ? $bar1 = $bar2 = $ok : $bar2;
												$bar2 = (($mins > $max) && ($status == 'Dispatched')) ? $bar1 = $bar2 = $danger : $bar2;
												
												$bar3 = ($status == 'Delivered') ? $bar1 = $bar2 = $bar3 = $ok : $bar3;

												$CI->make->sTd();

												$pBar = $CI->make->append("<div class='progress'>");

													if($status == 'Voided')
													{
														$pBar .= $CI->make->append("<div class='progress-bar progress-bar-danger' style='width: 100%'>
														   Voided
														</div>");
													}else if($status == 'On Hold'){
														$pBar .= $CI->make->append("<div class='progress-bar progress-bar-danger' style='width: 100%'>
														   On Hold
														</div>");
													}else{
														$pBar .= $CI->make->append("<div class='".$bar1."' style='width: 33.3%'>
														   On Process
														</div>");
														$pBar .= $CI->make->append("<div class='".$bar2."' style='width: 33.3%'>
														   Dispatched
														</div>");
														$pBar .= $CI->make->append("<div class='".$bar3."' style='width: 33.3%'>
														    Delivered
														</div>");
													}
												$pBar .= $CI->make->append("</div>");
												
												$CI->make->eTd();

						           	if($val->tr_inactive == 0) 
						            	$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'Void Transaction', 'id'=>'void-'.$val->sales_id,'class'=>'void-transaction','ref'=>$val->sales_id,'return'=>true));
									if($val->tr_inactive == 1 || $val->completed == 1 || $val->delivered == 1)
										$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('id'=>'voided-'.$val->sales_id,'ref'=>$val->sales_id, 'style'=> 'color: #DB9598;', 'return'=>true));
										$a .= $CI->make->A(fa('fa-book fa-fw fa-lg'),base_url().'trans_order/view_transaction/'.$val->sales_id, array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'View Details',  'id'=>'view-'.$val->sales_id,'class'=>'view-transaction','ref'=>$val->sales_id,'return'=>true));
									$CI->make->td($a);							            
								$CI->make->eRow();
							}
						}else{
							$CI->make->sRow(array('class'=>'t-rows'));
						        $CI->make->td('No transactions.', array('colspan'=>7));
						    $CI->make->eRow();
						}
					$CI->make->eTable();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();

	return $CI->make->code();
}

function makeTransactionSearch($list){
	$CI =& get_instance();
	foreach ($list as $val) {

		$cust_name = $val->fname.' '.$val->mname. ' ' .$val->lname;								
		$cust_addr = $val->street_no . " " . $val->street_address . ", " . $val->city . ", " . $val->region. ", " . $val->zip. ", " . $val->landmark;

		$CI->make->sRow(array('class'=>'t-rows'));
            $CI->make->td($val->trans_ref);
            $CI->make->td(sql2DateTime($val->datetime));
         	$CI->make->td(ucwords($cust_name));
            $CI->make->td($val->prep_time . ' mins');

				if($val->completed == 1)
            	$status = 'Dispatched';
            else
        		$status = 'On Process';
        	if($val->tr_inactive == 0 && is_null($val->start_time))
        		$status = 'Pending'; 
            if($val->tr_inactive == 1)
            	$status = 'Voided';
            if($val->tr_inactive == 0 && $val->delivered == 1)
            	$status = 'Delivered';
            if($val->on_hold == 1)
            	$status = 'On Hold';
            
            $CI->make->td($status);

            $start_time = $val->start_time;
			$complete_time = $val->complete_time;						          	
            $dateNow = $CI->site_model->get_db_now('sql');
			$max = $val->prep_time;
			
			$mins = getInterval($start_time, $dateNow);
			
			$danger = 'progress-bar progress-bar-danger';
			$ok = 'progress-bar progress-bar-success';

			$bar1 = $bar2 = $bar3 = 'progress-bar progress-bar-warning';
			$bar1 = ($status == 'On Process') ? $ok : $bar1;
			$bar1 = (($mins > $max) && ($status == 'On Process')) ? $danger : $bar1;
			
			$mins = getInterval($start_time, $complete_time);
			$bar2 = ($status == 'Dispatched') ? $bar1 = $bar2 = $ok : $bar2;
			$bar2 = (($mins > $max) && ($status == 'Dispatched')) ? $bar1 = $danger : $bar2;
			
			$bar3 = ($status == 'Delivered') ? $bar1 = $bar2 = $bar3 = $ok : $bar3;
			$CI->make->sTd();
			$pBar = $CI->make->append("<div class='progress'>");
				if($status == 'Voided')
				{
					$pBar .= $CI->make->append("<div class='progress-bar progress-bar-danger' style='width: 100%'>
					   Voided
					</div>");
				}else if($status == 'On Hold'){
					$pBar .= $CI->make->append("<div class='progress-bar progress-bar-danger' style='width: 100%'>
					   On Hold
					</div>");
				}else{
					$pBar .= $CI->make->append("<div class='".$bar1."' style='width: 33.3%'>
					   On Process
					</div>");
					$pBar .= $CI->make->append("<div class='".$bar2."' style='width: 33.3%'>
					   Dispatched
					</div>");
					$pBar .= $CI->make->append("<div class='".$bar3."' style='width: 33.3%'>
					    Delivered
					</div>");
				}
			$pBar .= $CI->make->append("</div>");
			
			$CI->make->eTd();

           	if($val->tr_inactive == 0) 
            	$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'Void Transaction', 'id'=>'void-'.$val->sales_id,'class'=>'void-transaction','ref'=>$val->sales_id,'return'=>true));
			if($val->tr_inactive == 1 || $val->completed == 1 || $val->delivered == 1)
				$a = $CI->make->A(fa('fa-minus-circle fa-lg'),'#',array('id'=>'voided-'.$val->sales_id,'ref'=>$val->sales_id, 'style'=> 'color: #DB9598;', 'return'=>true));
				$a .= $CI->make->A(fa('fa-book fa-fw fa-lg'),base_url().'trans_order/view_transaction/'.$val->sales_id, array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'View Details',  'id'=>'view-'.$val->sales_id,'class'=>'view-transaction','ref'=>$val->sales_id,'return'=>true));
			$CI->make->td($a);							            
		$CI->make->eRow();
	}
	return $CI->make->code();
}


function make_pop_void_order($id){
	$CI =& get_instance();
		$CI->make->sForm("trans/pop_void_order_db",array('id'=>'void_order_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(12);
					$CI->make->textarea('Reason for voiding transaction: ', 'inp_reason', '', array());
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}

function makeTransDetailsList($ord=null, $cust=null, $addr=null, $branch=null, $order_menus=null, $order_mods=null, $payment_mode=null){
	$CI =& get_instance();
				$cust_name = $cust->fname . ' ' . $cust->mname . ' ' . $cust->lname;
				$cust_addr = $addr->street_no.', '.$addr->street_address.', '.$addr->city.', '.$addr->region.', '.$addr->landmark.', '.$addr->zip;
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);				
				$CI->make->H(5,'<strong>Customer Name: </strong>&nbsp;'.ucwords($cust_name). '</small>',array());
				$CI->make->H(5,'<strong>Address: </strong>&nbsp;'.ucwords($cust_addr). '</small>',array());
				$CI->make->H(5,'<strong>Phone Number: </strong>&nbsp;'.$cust->phone. '</small>',array());
				$CI->make->H(5,'<strong>Mode of payment: </strong>&nbsp;</small> '.ucwords($payment_mode->payment_type),array());
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);

				if($ord->completed == 1)
			    	$status = 'Dispatched';
			    else
					$status = 'On Process';
				if($ord->inactive == 0 && is_null($ord->start_time))
					$status = 'Pending'; 
			    if($ord->inactive == 1)
			    	$status = 'Voided';
			    if($ord->delivered == 1)
			    	$status = 'Delivered';
			    if($ord->on_hold == 1)
			    	$status = 'On Hold';


				$CI->make->H(5,'<strong>Branch:  </strong>&nbsp;'.ucwords($branch->branch_code . " - " .$branch->branch_name),array());
				$CI->make->H(5,'<strong>Order Date:  </strong>&nbsp;'. $ord->datetime,array());
				$CI->make->H(5,'<strong>Order Type:  </strong>&nbsp; Delivery ',array());
				$CI->make->H(5,'<strong>Status:  </strong>&nbsp;'.$status,array());
			if($status == 'Voided')
			{
				$CI->make->H(5,'<strong>Reason:  </strong>&nbsp;'.ucfirst($ord->reason),array());
			}
			$CI->make->eDivCol();
	    $CI->make->eDivRow(); 
		$CI->make->sDiv(array('class'=>'table-responsive'));
			$CI->make->sTable(array('class'=>'table table-striped'));
				$CI->make->sRow();
					$CI->make->th('Qty',array('style'=>'width:30%;'));
					$CI->make->th('Name',array('style'=>'width:30%;'));
					$CI->make->th('Cost', array('style'=>'width:30%'));
				$CI->make->eRow();
				foreach($order_menus as $k=>$p)
				{
					$CI->make->sRow();
			            $CI->make->td(ucwords($p->qty));
			            $CI->make->td(ucwords($p->menu_name));
			            $CI->make->td('PHP '. num($p->price));
			   		$CI->make->eRow();
					
					$curr = $p->line_id;
					foreach($order_mods as $key=>$val)
						if($curr == $val->line_id)
						{
							$CI->make->sRow();
					            $CI->make->td();
					            $CI->make->td(ucwords($val->mod_name), array('colspan'=>2));
					     	$CI->make->eRow();
				   		}
				}
			$CI->make->eTable();
		$CI->make->eDiv();
		$CI->make->sDivRow();
		    $CI->make->sDivCol(12);
		      $CI->make->sBox('default',array('class'=>'box-solid'));
			       $CI->make->sBoxHead(array());
			        	$CI->make->boxTitle('<strong>Total Cost: &nbsp;&nbsp;&nbsp;'.$branch->currency.' '.number_format($ord->total_amount,2).'</strong>&nbsp;&nbsp;', array('style'=>"float:right; margin-right:15%;"));
			       $CI->make->eBoxHead();
		      $CI->make->eBox();
	      	$CI->make->eDivCol();
		 $CI->make->eDivRow();

	return $CI->make->code();
}



