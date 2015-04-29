<?php

function indexPage($pending, $cancelled,$on_process,$on_hold, $voided){	
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	$time = $CI->site_model->get_db_now();

	$CI->load->model('core/user_model');
		$CI->make->sDiv(array('id'=>'centralize'));
			$CI->make->sDivRow();
			#LEFT PANE
				$CI->make->sDivCol(9,'left',0,array('class'=>'centralize-left'));
					#PENDING TRANSACTION
					
					$CI->make->sDiv(array('class'=>'first-container'));
						$code = $CI->make->code();
							
						$code .= makeFirstTable($pending);
					$CI->make->eDiv();

					#SECOND CONTAINER

				$CI->make->sDiv(array('class'=>'second-container'));
					$code .= $CI->make->code();

					$code .= makeSecondTable($on_process, 'process', 'CONFIRMED TRANSACTIONS');
				$CI->make->eDiv();
			#RIGHT PANE
				$CI->make->sDivCol(3,'right',0,array('class'=>'centralize-right'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(12);
							$CI->make->button('<span id="count_process">'.fa('fa-check fa-2x fa-fw').' IN PROCESS &nbsp;&nbsp;</span><span class="badge">'.count($on_process).'</span>',array('class'=>'status-type btn-block no-raduis cpanel-btn-red double btnheader'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(12);
							$CI->make->button('<span id="count_pending">'.fa('fa-clock-o fa-2x fa-fw').' PENDING &nbsp;&nbsp;</span><span class="badge">'.count($pending).'</span>',array('class'=>'status-type btn-block no-raduis cpanel-btn-red double btnheader'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(12);
							$CI->make->button('<span id="count_cancelled">'.fa('fa-times fa-2x fa-fw').' NOT AVAILABLE &nbsp;&nbsp;</span><span class="badge">'.count($cancelled).'</span>',array('class'=>'status-type btn-block no-raduis cpanel-btn-red double btnheader'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(12);
							$CI->make->button('<span id="count_voided">'.fa('fa-ban fa-2x fa-fw').' VOIDED &nbsp;&nbsp;</span><span class="badge">'.count($voided).'</span>',array('class'=>'status-type btn-block no-raduis cpanel-btn-red double btnheader'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(12);
							$CI->make->button('<span id="count_hold">'.fa('fa-pause fa-2x fa-fw').' ON HOLD &nbsp;&nbsp;</span><span class="badge">'.count($on_hold).'</span>',array('class'=>'status-type btn-block no-raduis cpanel-btn-red double btnheader'));
						$CI->make->eDivCol();

						$CI->make->sDivCol(12);

							$CI->make->sDiv(array('class'=>'container-cancelled'));
											
								$CI->make->sDiv(array('style'=>'color: #fafafa; padding-left:10px;'));
									$CI->make->H(4, 'CANCELLED TRANSACTIONS');
								$CI->make->eDiv();
								$CI->make->sDiv(array('class'=>'left-cancelled', 'style'=>'height:130px; overflow-x: auto;'));
									if(!empty($cancelled))
									{	
										foreach($cancelled as $key => $val)
										{
											$CI->make->sDiv(array('style'=>'text-align:center; width: 98%; margin:2px; padding: 4px 0px;','ref'=>$val->sales_id));
												$CI->make->append(fa('fa-fw fa-ellipsis-v').' '.$val->trans_ref.' | '.strtoupper($val->username));
											$CI->make->eDiv();
										}
									}else{
										$CI->make->sDiv(array('style'=>'text-align:center; width: 98%; margin: 2px; padding: 4px 0px;','class'=>'non-cancelled'));
												$CI->make->append('No cancelled order.');
										$CI->make->eDiv();
									}
								$CI->make->eDiv();
							
							$CI->make->eDiv();
							$CI->make->sDiv(array('class'=>'container-hold'));
							
								$CI->make->sDiv(array('style'=>'color: #fafafa; padding-left:10px;'));
									$CI->make->H(4, 'HOLD TRANSACTIONS');
								$CI->make->eDiv();
								$CI->make->sDiv(array('class'=>'left-hold', 'style'=>'height:130px; overflow-x: auto;'));
								if(!empty($on_hold))
								{
									foreach($on_hold as $key => $val)
									{
										$CI->make->sDiv(array('style'=>'text-align:center; width:98%; margin: 2px; padding: 4px 0px;', 'ref'=>$val->sales_id));
											$CI->make->append(fa('fa-fw fa-ellipsis-v').' '.$val->trans_ref.' | '.strtoupper($val->username));
										$CI->make->eDiv();
									}
								}else{
									$CI->make->sDiv(array('style'=>'text-align:center; width: 98%; margin: 2px; padding: 4px 0px;','class'=>'non-on-hold'));
											$CI->make->append('No on hold order.');
									$CI->make->eDiv();
								}
								$CI->make->eDiv();
							$CI->make->eDiv();
							
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDivCol();
			
			$CI->make->eDivRow();
		$CI->make->eDiv();
	$code .= $CI->make->code();
	return $code;
}

function makeFirstTable($list){
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	$CI->load->model('core/user_model');

	$time = $CI->site_model->get_db_now();

	$CI->make->sDiv(array('class'=>'pending_trans'));
		$CI->make->sDiv(array('style'=>'color: #fafafa; padding-left:10px;'));
			$CI->make->H(4, 'PENDING TRANSACTIONS');
		$CI->make->eDiv();
		$CI->make->sDiv(array('class'=>'table-responsive ', 'style'=>'background-color: #fafafa; margin:1%; height: 260px; overflow-x: auto;'));
			$CI->make->sTable(array('class'=>'table table-striped table-bordered','id'=>'pending-tbl'));
				$CI->make->sRow();
					// $CI->make->th('No.',array('style'=>'width:2%;'));
					$CI->make->th('Order #',array('style'=>'width:8%;'));
					$CI->make->th('Date',array('style'=>'width:15%;'));
					$CI->make->th('Branch',array('style'=>'width:20%;'));
					$CI->make->th('Agent',array('style'=>'width:20%;'));
				$CI->make->eRow();
				$count = 0;
				foreach($list as $key=>$val)
				{
					$agent = '';
					$agent = $CI->user_model->get_users($val->user_id);
					if(!empty($agent))
					{
						$agent = $agent[0];
						$agent = ucwords($agent->fname . " " . $agent->lname);
					}
					$count++;
					$CI->make->sRow(array('ref'=>$val->sales_id));
						// $CI->make->td($count);
				        $CI->make->td(strong($val->trans_ref));
				        $CI->make->td(date2SQlMonDateTime($val->datetime) .'<br/><small>'. ago($val->datetime,$time).'</small>'); //date2SQlMonDateTime($val->datetime)
				        $CI->make->td($val->branch_name);
				        $CI->make->td($agent);
				    $CI->make->eRow();
				}

			$CI->make->eTable();
		$CI->make->eDiv();
	$CI->make->eDiv();
	
	return $CI->make->code();
}

function makeSecondTable($list=array(), $status=null, $header){
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	$CI->load->model('core/user_model');

	$time = $CI->site_model->get_db_now();

	$CI->make->sDiv(array('style'=>'color: #fafafa; padding-left:10px;'));
		$CI->make->H(4, $header);
	$CI->make->eDiv();
	$CI->make->sDiv(array('class'=>'tbl_'.$status));
		$CI->make->sDiv(array('class'=>'trans_'.$status));
			$CI->make->sDiv(array('class'=>'table-responsive ', 'style'=>'background-color: #fafafa; margin:1%; height: 270px; overflow-x: auto;'));
				$CI->make->sTable(array('class'=>'table table-striped table-bordered','id'=>'tbl_'.$status));
					$CI->make->sRow();
						
						$CI->make->th('Order #',array('style'=>'width:8%;'));
						$CI->make->th('Date',array('style'=>'width:15%;'));
						$CI->make->th('Branch',array('style'=>'width:20%;'));
						$CI->make->th('Agent',array('style'=>'width:15%;'));
						$CI->make->th('Status',array('style'=>'width:20%;'));

					$CI->make->eRow();
					$count = 0;
					foreach($list as $key=>$val)
					{
						$agent = '';
						$agent = $CI->user_model->get_users($val->user_id);
						if(!empty($agent))
						{
							$agent = $agent[0];
							$agent = ucwords($agent->fname . " " . $agent->lname);
						}
						$count++;
						$CI->make->sRow(array('ref'=>$val->sales_id));
							$CI->make->td(strong($val->trans_ref));
					        $CI->make->td(date2SQlMonDateTime($val->datetime) .'<br/><small>'. ago($val->datetime,$time).'</small>'); //date2SQlMonDateTime($val->datetime)
				            $CI->make->td($val->branch_name);
				            $CI->make->td($agent);
					        
							if(!is_null($val->rider_id))
			            		$status = 'Dispatched';
				            else
			            		$status = 'In Process';
			            	if($val->tr_inactive == 0 && is_null($val->start_time))
			            		$status = 'Pending'; 
				            if($val->tr_inactive == 1)
				            	$status = 'Voided';
				            if($val->tr_inactive == 0 && $val->delivered == 1)
				            	$status = 'Delivered';
				            if($val->on_hold == 1)
				            	$status = 'On Hold';
				         
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
							$bar1 = ($status == 'In Process') ? $ok : $bar1;
							$bar1 = (($mins > $max) && ($status == 'In Process')) ? $danger : $bar1;
							
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
									   In Process
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
					    $CI->make->eRow();
					}
					$CI->make->eTable();
				$CI->make->eDiv();
			$CI->make->eDiv();
		$CI->make->eDiv();
	$CI->make->eDivCol();

	return $CI->make->code();
}

function makeProgressBarTD($list){
	$CI =& get_instance();

	$tds_array = array();

	foreach($list as $key=>$val)
	{
		if(!is_null($val->rider_id))
			$status = 'Dispatched';
	    else
			$status = 'In Process';
		if($val->tr_inactive == 0 && is_null($val->start_time))
			$status = 'Pending'; 
	    if($val->tr_inactive == 1)
	    	$status = 'Voided';
	    if($val->tr_inactive == 0 && $val->delivered == 1)
	    	$status = 'Delivered';
	    if($val->on_hold == 1)
	    	$status = 'On Hold';
	 
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
		$bar1 = ($status == 'In Process') ? $ok : $bar1;
		$bar1 = (($mins > $max) && ($status == 'In Process')) ? $danger : $bar1;
		
		$bar2 = ($status == 'Dispatched') ? $bar1 = $bar2 = $ok : $bar2;
		$bar2 = (($mins > $max) && ($status == 'Dispatched')) ? $bar1 = $bar2 = $danger : $bar2;
		
		$bar3 = ($status == 'Delivered') ? $bar1 = $bar2 = $bar3 = $ok : $bar3;

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
				   In Process
				</div>");
				$pBar .= $CI->make->append("<div class='".$bar2."' style='width: 33.3%'>
				   Dispatched
				</div>");
				$pBar .= $CI->make->append("<div class='".$bar3."' style='width: 33.3%'>
				    Delivered
				</div>");
			}
		$pBar .= $CI->make->append("</div>");
		
		$tds_array[$val->sales_id] = $CI->make->code();
	}

	return $tds_array;


	// print_r($tds_array);

}

function makeAppendRow($status, $list, $count){
	$CI =& get_instance();
	$CI->load->model('site/site_model');
	$time = $CI->site_model->get_db_now();
	$CI->load->model('core/user_model');


	if($status == 'pending'){
		foreach($list as $key=>$val)
		{

			$agent = '';
			$agent = $CI->user_model->get_users($val->user_id);
			if(!empty($agent))
			{
				$agent = $agent[0];
				$agent = ucwords($agent->fname . " " . $agent->lname);
			}
		
			$CI->make->sRow(array('ref'=>$val->sales_id));
				// $CI->make->td($count);
		        $CI->make->td(strong($val->trans_ref));
		        $CI->make->td(date2SQlMonDateTime($val->datetime) .'<br/><small>'. ago($val->datetime,$time).'</small>'); //date2SQlMonDateTime($val->datetime)
		        $CI->make->td($val->branch_name);
		        $CI->make->td($agent);
		    $CI->make->eRow();
		}
	}else if($status == 'on_process')
	{
		foreach($list as $key=>$val)
		{
			$agent = '';
			$agent = $CI->user_model->get_users($val->user_id);
			if(!empty($agent))
			{
				$agent = $agent[0];
				$agent = ucwords($agent->fname . " " . $agent->lname);
			}
			$count++;
			$CI->make->sRow(array('ref'=>$val->sales_id));
				// $CI->make->td($count);
		        $CI->make->td(strong($val->trans_ref));
		        $CI->make->td(date2SQlMonDateTime($val->datetime) .'<br/><small>'. ago($val->datetime,$time).'</small>'); //date2SQlMonDateTime($val->datetime)
		        $CI->make->td($val->branch_name);
		        $CI->make->td($agent);
		        
				if($val->completed == 1)
		    		$status = 'Dispatched';
		        else
		    		$status = 'In Process';
		    	if($val->tr_inactive == 0 && is_null($val->start_time))
		    		$status = 'Pending'; 
		        if($val->tr_inactive == 1)
		        	$status = 'Voided';
		        if($val->tr_inactive == 0 && $val->delivered == 1)
		        	$status = 'Delivered';
		        if($val->on_hold == 1)
		        	$status = 'On Hold';
		     
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
				$bar1 = ($status == 'In Process') ? $ok : $bar1;
				$bar1 = (($mins > $max) && ($status == 'In Process')) ? $danger : $bar1;
				
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
						   In Process
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
		    $CI->make->eRow();
		}


	}else
		;


	return $CI->make->code();
}