<?php
function makeIndexPage(){
		$CI =& get_instance();
			$CI->make->sDiv(array('id'=>'header-form'));

				$CI->make->sDivRow();
					$CI->make->sDivCol(4);
						$CI->make->reportsDrop('Reports','type_report','1',null);
						$CI->make->append('<div class="btn-group" data-toggle="buttons" style="margin-bottom: 10px;">
							  <label class="btn btn-primary " style="padding: 6px 74px;">
							    <input type="radio" name="type_date" id="type_date" autocomplete="off"  > Date
							  </label>
							  <label class="btn btn-primary active" style="padding: 6px 56px;">
							    <input type="radio" name="type_date" id="type_range" autocomplete="off" checked> Date Range
							  </label>
						</div>');
					$CI->make->eDivCol();
				$CI->make->eDivRow();

				$CI->make->sDivRow();
					$CI->make->sDivCol(4);
						$CI->make->sDiv(array('class'=>'div_range'));
							$CI->make->input('','daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
						$CI->make->eDiv();
						
						$CI->make->sDiv(array('class'=>'div_date', 'style'=>'display:none'));
							$CI->make->input('','datepicker','','Date',array('data-date-format'=>'mm/dd/yyyy', 'class'=>'rOkay datepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
						$CI->make->eDiv();
					$CI->make->eDivCol();
				$CI->make->eDivRow();
					
				$CI->make->sDivRow();
					$CI->make->sDivCol(2);
						$CI->make->button(fa('fa-file-pdf-o fa-lg').' Print PDF',array('id'=>'print-pdf-btn','class'=>'btn-block'));
					$CI->make->eDiv();
					$CI->make->sDivCol(2);
						$CI->make->button(fa('fa-file-excel-o fa-lg').' Print Excel',array('id'=>'print-excel-btn','class'=>'btn-block'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
				
			$CI->make->eDiv();
			$CI->make->append('<hr/>');	
			$CI->make->sDiv(array('id'=>'view_excel', 'style'=>'margin: 0px 5px 0px 5px;'));
			$CI->make->eDiv();

			
		$CI->make->eDivRow();
		
		return $CI->make->code();
}
function make_hit_rate_report_form(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'header-form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(3);
					$CI->make->append('<div class="btn-group" data-toggle="buttons">
						  <label class="btn btn-primary " style="padding: 6px 53px;">
						    <input type="radio" name="type_date" id="type_date" autocomplete="off"  > Date
						  </label>
						  <label class="btn btn-primary active" style="padding: 6px 30px;">
						    <input type="radio" name="type_date" id="type_range" autocomplete="off" checked> Date Range
						  </label>
						</div>');
				$CI->make->eDivCol();

				$CI->make->sDivCol(3);
					$CI->make->sDiv(array('class'=>'div_range'));
						$CI->make->input('','daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'div_date', 'style'=>'display:none'));
						$CI->make->input('','datepicker','','Date',array('data-date-format'=>'mm/dd/yyyy', 'class'=>'rOkay datepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(1);
					$CI->make->button('Go',array('id'=>'go-view-btn','class'=>'btn-block'));
				$CI->make->eDivCol();
				
				$CI->make->sDivCol(1); $CI->make->eDivCol();
				
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-pdf-o fa-lg').' Print PDF',array('disabled'=>'disable','id'=>'print-pdf-btn','class'=>'btn-block'));
				$CI->make->eDiv();
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-excel-o fa-lg').' Print Excel',array('disabled'=>'disable','id'=>'print-excel-btn','class'=>'btn-block'));
				$CI->make->eDivCol();	

			$CI->make->sDivRow();
			
		$CI->make->eDiv();
		$CI->make->append('<hr/>');	
		$CI->make->sDiv(array('id'=>'view_excel', 'style'=>'margin: 0px 5px 0px 5px;'));
		$CI->make->eDiv();

		
	$CI->make->eDivRow();
	
	return $CI->make->code();
}

function make_complaint_report_form(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'header-form'));
			$CI->make->sDivRow();
			
				$CI->make->sDivCol(3);
					$CI->make->append('<div class="btn-group" data-toggle="buttons">
						  <label class="btn btn-primary " style="padding: 6px 53px;">
						    <input type="radio" name="type_date" id="type_date" autocomplete="off"  > Date
						  </label>
						  <label class="btn btn-primary active" style="padding: 6px 30px;">
						    <input type="radio" name="type_date" id="type_range" autocomplete="off" checked> Date Range
						  </label>
						</div>');
				$CI->make->eDivCol();

				$CI->make->sDivCol(3);
					$CI->make->sDiv(array('class'=>'div_range'));
						$CI->make->input('','daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'div_date', 'style'=>'display:none'));
						$CI->make->input('','datepicker','','Date',array('data-date-format'=>'mm/dd/yyyy', 'class'=>'rOkay datepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(1);
					$CI->make->button('Go',array('id'=>'go-view-btn','class'=>'btn-block'));
				$CI->make->eDivCol();
				
				$CI->make->sDivCol(1); $CI->make->eDivCol();
				
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-pdf-o fa-lg').' Print PDF',array('disabled'=>'disable','id'=>'print-pdf-btn','class'=>'btn-block'));
				$CI->make->eDiv();
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-excel-o fa-lg').' Print Excel',array('disabled'=>'disable','id'=>'print-excel-btn','class'=>'btn-block'));
				$CI->make->eDivCol();	

			$CI->make->sDivRow();
			
		$CI->make->eDiv();
		$CI->make->append('<hr/>');	
		$CI->make->sDiv(array('id'=>'view_excel', 'style'=>'margin: 0px 5px 0px 5px;'));
		$CI->make->eDiv();

		
	$CI->make->eDivRow();
	
	return $CI->make->code();

}

function make_complaint_report_data($list = null){
	$CI =& get_instance();
		$reasons_complaint = array(
			"Wrong Delivery",
			"Product Quality",
			"Incomplete Delivery",
			"Late Delivery",
			"Others"
		);

		$CI->make->sTable(array('class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
			$CI->make->sRow();
				$CI->make->th('Store');
				foreach($reasons_complaint as $reason)
				{
					$CI->make->th($reason);
				}
				$CI->make->th('Total # Of Occurences', array('width: 40%'));
			$CI->make->eRow();
			if(!empty($list))
			{
				foreach($list as $k=>$r)
				{
					$CI->make->sRow();
			            $CI->make->td($k);
			            $occur=0;
			            foreach($reasons_complaint as $reason)
						{
							if(isset($r[$reason]))
							{
								$CI->make->td($r[$reason]);
								$occur+=$r[$reason];
							}	
							else
								$CI->make->td('0');
						}
						$CI->make->td($occur);
				 	$CI->make->eRow();
				}
			}
			else
			{
				$CI->make->sRow();
		            $CI->make->td('No available data.', array('colspan'=>(count($reasons_complaint)+2)));
		     	$CI->make->eRow();
			}
		$CI->make->eTable();

	return $CI->make->code();
}

function make_hit_rate_report_data($list = null){
	$CI =& get_instance();
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

		$CI->make->sTable(array('class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
			$CI->make->sRow();
				foreach($header as $hr)
				{
					$CI->make->th($hr);
				}
			$CI->make->eRow();
			if(!empty($list))
			{
				foreach($list as $k=>$r)
				{
			            foreach($r as $key=>$val)
			            {
							$CI->make->sRow();
								$CI->make->td($val->branch_code);
								$CI->make->td(ucwords($val->cust_name));
								$CI->make->td($val->prep_time);
								$CI->make->td($val->delivery_time);
								$CI->make->td($val->datetime);	
								$CI->make->td($val->_confirmation_sent);
								$CI->make->td($val->d_post_confirmed); //duration agent post - confirmation sent
								$CI->make->td($val->_process_time);
								$CI->make->td($val->d_confirmed_process); //duration confirmation sent - process time
								$CI->make->td($val->_done_process_time);
								$CI->make->td($val->_delivered_time);
								$CI->make->td($val->d_dispatch_delivery); //duration dispatch - delivered

								$total_duration = strtotime('00:00:00');
								if(!is_null($val->d_post_confirmed))
									$total_duration+=(strtotime($val->d_post_confirmed));
								if(!is_null($val->d_confirmed_process))
									$total_duration+=(strtotime($val->d_confirmed_process));
								if(!is_null($val->d_dispatch_delivery))
									$total_duration+=(strtotime($val->d_dispatch_delivery));

										$total_duration = date('H:i:s',$total_duration);
										$CI->make->td($total_duration);

									if(!is_null($val->d_dispatch_delivery))
									{
										$time = explode(':', $val->d_dispatch_delivery);
										$total_minutes = (($time[0]*3600) + ($time[1]*60) + $time[2])/60;

										if($total_minutes > $val->delivery_time)
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
			else
			{
				$CI->make->sRow();
		            $CI->make->td('No available data.', array('colspan'=>(count($header))));
		     	$CI->make->eRow();
			}
		$CI->make->eTable();
	// echo $CI->make->code();
	return $CI->make->code();
}

function make_cancelled_report_form(){
		$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'header-form'));
			$CI->make->sDivRow();
			
				$CI->make->sDivCol(3);
					$CI->make->append('<div class="btn-group" data-toggle="buttons">
						  <label class="btn btn-primary " style="padding: 6px 53px;">
						    <input type="radio" name="type_date" id="type_date" autocomplete="off"  > Date
						  </label>
						  <label class="btn btn-primary active" style="padding: 6px 30px;">
						    <input type="radio" name="type_date" id="type_range" autocomplete="off" checked> Date Range
						  </label>
						</div>');
				$CI->make->eDivCol();

				$CI->make->sDivCol(3);
					$CI->make->sDiv(array('class'=>'div_range'));
						$CI->make->input('','daterange','','Date range',array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
					$CI->make->sDiv(array('class'=>'div_date', 'style'=>'display:none'));
						$CI->make->input('','datepicker','','Date',array('data-date-format'=>'mm/dd/yyyy', 'class'=>'rOkay datepicker','style'=>'position:initial;'),null,fa('fa-calendar'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(1);
					$CI->make->button('Go',array('id'=>'go-view-btn','class'=>'btn-block'));
				$CI->make->eDivCol();
				
				$CI->make->sDivCol(1); $CI->make->eDivCol();
				
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-pdf-o fa-lg').' Print PDF',array('disabled'=>'disable','id'=>'print-pdf-btn','class'=>'btn-block'));
				$CI->make->eDiv();
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-file-excel-o fa-lg').' Print Excel',array('disabled'=>'disable','id'=>'print-excel-btn','class'=>'btn-block'));
				$CI->make->eDivCol();	

			$CI->make->sDivRow();
			
		$CI->make->eDiv();
		$CI->make->append('<hr/>');	
		$CI->make->sDiv(array('id'=>'view_excel', 'style'=>'margin: 0px 5px 0px 5px;'));
		$CI->make->eDiv();

		$CI->make->eDivRow();
		
		return $CI->make->code();
}

function make_cancelled_report_data($list = null){
		$CI =& get_instance();
		$void_reason = array(
			"Change of Mind",
			"Change Order",
			"No Show Delivery",
			"Took Too long",
			"Others"
		);

		$CI->make->sTable(array('class'=>'table table-striped table-bordered','id'=>'complaint_tbl'));
			$CI->make->sRow();
				$CI->make->th('Store');
				foreach($void_reason as $reason)
				{
					$CI->make->th($reason);
				}
				$CI->make->th('Total # Of Occurences', array('width: 40%'));
			$CI->make->eRow();
			
			if(!empty($list))
			{
				foreach($list as $k=>$r)
				{
					foreach($r as $key=>$val)
					{
						$CI->make->sRow();
				            $CI->make->td($key);
				            $occur=0;
				            foreach($void_reason as $reason)
							{
								if(isset($val[$reason]))
								{
									$CI->make->td($val[$reason]);
									$occur+=$val[$reason];
								}	
								else
									$CI->make->td('');
							}
							$CI->make->td($occur);
					 	$CI->make->eRow();
					}
				}
			}
			else
			{
				$CI->make->sRow();
		            $CI->make->td('No available data.', array('colspan'=>(count($void_reason)+2)));
		     	$CI->make->eRow();
			}
		$CI->make->eTable();
	echo $CI->make->code();
	// return $CI->make->code();
}

?>