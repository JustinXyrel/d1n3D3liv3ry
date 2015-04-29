<?php
function trans_display($list=null, $menus=null){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'wizard_step'));
				$CI->make->H(3,'Customer',array());
					$CI->make->sSection();
						$CI->make->sForm("customers/customer_details_db/true",array('id'=>'new_customer_form'));
								$CI->make->sDivRow();
									$CI->make->sDivCol(4);
										$CI->make->input(null,'customer-search',null,'Contact No.',array('search-url'=>'trans/customer_search'),'',fa('fa-search'));
										$CI->make->hidden('cust_id',null,array());
									$CI->make->eDivCol();	
									$CI->make->sDivCol(6);
									$CI->make->eDivCol();
									$CI->make->sDivCol(2);
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow();
									$CI->make->sDivCol(4);
										$CI->make->input('First Name','fname',null,'',array('class'=>'rOkay'));
									$CI->make->eDivCol();	
									$CI->make->sDivCol(4);
										$CI->make->input('Last Name','lname',null,'',array('class'=>'rOkay'));
									$CI->make->eDivCol();	
									$CI->make->sDivCol(4);
										$CI->make->input('Middle Name','mname',null,'',array());
									$CI->make->eDivCol();	
								$CI->make->eDivRow();
								$CI->make->sDivRow();
									$CI->make->sDivCol(4);
										$CI->make->input('Suffix','suffix',null,'',array());
									$CI->make->eDivCol();	
									$CI->make->sDivCol(4);
										$CI->make->input('Email Address','email',null,'',array('class'=>'rOkay'));
									$CI->make->eDivCol();	
									$CI->make->sDivCol(4);
										$CI->make->input('Phone No.','phone',null,'',array('class'=>'rOkay'));
										$CI->make->hidden('cust_addr_id',null,array());
									$CI->make->eDivCol();	
								$CI->make->eDivRow();
								$CI->make->sDivCol(12);
									$CI->make->append("<hr style='border-top: 1px solid #B8B8B8;' />");
								$CI->make->eDivCol();
								$CI->make->sDivRow();	
									$CI->make->sDivCol(6);
										$CI->make->H(3,'Delivery Address',array());
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->A(fa('fa-external-link fa-fw ').' Add New Address','trans/pop_new_address',array('class'=>'btn btn-primary',
																	'style'=>'float:right; margin-top:2%;',	
																	'disabled'=>'disabled',
																	'id'=>'add-new-address',
																	'rata-title'=>'Add New Address',
																	'rata-pass'=>'trans/pop_new_address_db',
																	'rata-form'=>'new_address_form',
																	'return'=>false));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
						$CI->make->sDivRow();
						$CI->make->sDiv(array('id'=>'tbl_address'));
							$CI->make->sDivRow();
									$CI->make->sDivCol();
										$CI->make->sDiv(array('class'=>'table-responsive', 'style'=>'height: 150px; overflow-x: auto;'));
											$CI->make->sTable(array('class'=>'table table-striped','id'=>'address-tbl'));
												$CI->make->sRow();
													$CI->make->th('Base Location',array('style'=>'width:15%;'));
													$CI->make->th('Street No',array('style'=>'width:15%;'));
													$CI->make->th('Street Address',array('style'=>'width:15%;'));
													$CI->make->th('City',array('style'=>'width:20%;'));
													$CI->make->th('Region',array('style'=>'width:20%;'));
													$CI->make->th('Zip Code',array('style'=>'width:10%;'));
												$CI->make->eRow();
												if(count($list) > 0){
													foreach ($list as $val) {
														$CI->make->sRow(array('class'=>'t-rows', 'ref'=>$val->id, 'id'=>'row-'.$val->id));
												            $CI->make->td(ucwords($val->base_location));
												            $CI->make->td(ucwords($val->street_no));
												            $CI->make->td(ucwords($val->street_address));
													        $CI->make->td(ucwords($val->city));
												            $CI->make->td(ucwords($val->region));
												            $CI->make->td(ucwords($val->zip));
												        $CI->make->eRow();
													}
												}
											$CI->make->eTable();
										$CI->make->eDiv();
									$CI->make->eDivCol();
									$CI->make->sDivCol(10);
									$CI->make->eDivCol();
									$CI->make->sDivCol(2);
										$CI->make->button(fa('fa-save').' Save',array('id'=>'save-new-cust-btn','class'=>'btn-block'),'primary');
									$CI->make->eDivCol();
							$CI->make->eDivRow();
						$CI->make->eDiv();
						$CI->make->eForm();	
					$CI->make->eSection();
			$CI->make->H(3,'Branch',array());					
				$CI->make->sSection();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input(null,'branch-search',null,'Branch',array('search-url'=>'trans/branch_search'),'',fa('fa-search'));
							$CI->make->hidden('branch_id',null,array());
						$CI->make->eDivCol();	
						$CI->make->sDiv(array('class'=>'col-md-4 col-md-offset-4'));
							$CI->make->button('Clear Search',array('id'=>'clear-search','style'=>'float: right'),'primary');
						$CI->make->eDiv();	
					$CI->make->eDivRow();
					$CI->make->sDiv(array('class'=>'table-responsive'));
						$CI->make->sTable(array('class'=>'table table-striped','id'=>'branch-list-tbl'));
							$CI->make->sRow();
								$CI->make->th('Name',array('style'=>'width:30%;'));
								$CI->make->th('Address',array('style'=>'width:20%;'));
								$CI->make->th('Base Location', array('style'=>'width:20%'));
								$CI->make->th('Delivery Time<br/><i>(Non-designated area)</i>', array('style'=>'width:15%', 'id'=>'non_del_time'));
								$CI->make->th('Contact No.',array('style'=>'width:18%;'));
							$CI->make->eRow();
						$CI->make->eTable();
					$CI->make->eDiv();
				$CI->make->eSection();
			$CI->make->H(3,'Order Details',array());
				$CI->make->sSection();
					$CI->make->hidden('line_id',null,array());
					$CI->make->hidden('curr_mod_hid_item',null,'',array());
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$options = array();
								$options['Select a Menu Item'] = "";
								$selected = "";
								foreach ($menus as $res) {
									$options[$res->menu_code . ' - ' . $res->menu_name] = array('value'=>$res->menu_id, 'cost'=>$res->cost, 'sched'=>$res->menu_sched_id);
								}
								$CI->make->select('Menu Items','menu-drop',$options,$selected,array(),null,null);
							$CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->input('Quantity','qty',null,'',array('class'=>'rOkay'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(2,'left');	
								$CI->make->button(fa('fa-plus').' Add',array('id'=>'add-menu-item','class'=>'btn-block' ,'style'=>'margin-top:12%;'),'primary');
							$CI->make->eDivCol();	
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12);
								$CI->make->sForm("",array('id'=>'new_customer_form'));
									$CI->make->sDiv(array('class'=>'table-responsive', 'style'=>'height: 290px; overflow-x: auto;'));
										$CI->make->sTable(array('class'=>'table table-striped','id'=>'menu-items-tbl'));
											$CI->make->sRow();
												$CI->make->th('Qty',array('style'=>'width:10%;'));
												$CI->make->th('Name',array('style'=>'width:40%;'));
												$CI->make->th('Cost',array('style'=>'width:23%;'));
												$CI->make->th('&nbsp;', array('style'=>'width265%;'));
											$CI->make->eRow();
										$CI->make->eTable();
									$CI->make->eDiv();
								$CI->make->eForm();
							$CI->make->eDivCol();
						$CI->make->eDivRow();			
				$CI->make->eSection();	
			$CI->make->H(3,'Order Summary',array());
				$CI->make->sSection();
					$CI->make->sDiv(array('id'=>'order_summary'));
					$CI->make->eDiv();
				$CI->make->eSection();
		$CI->make->eDiv();
	return $CI->make->code();
}
function pop_new_address_form()
{
	$CI =& get_instance();
	$CI->make->sForm("trans/pop_new_address_db",array('id'=>'new_address_form'));
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Street No.','street_no',iSetObj('','street_no'),'Type Street No.',array('class'=>'rOkay'));
				$CI->make->input('Street Address','street_address',iSetObj('','street_address'),'Type Street Address',array('class'=>'rOkay'));
				$CI->make->input('City','city',iSetObj('','city'),'Type City',array('class'=>'rOkay'));
				$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->input('Region','region',iSetObj('','region'),'Type Region',array('class'=>'rOkay'));
				$CI->make->input('Zip Code','zip',iSetObj('','zip'),'Type Zip Code',array('class'=>'rOkay'));
				$CI->make->input('Base Location','base_location',iSetObj('','base_location'),'Type Base Location',array('class'=>'rOkay'));
				$CI->make->hidden('cust_id',iSetObj('','cust_id'),array());
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function pop_edit_menu_item_form($item, $id, $items){
	extract($item);
	$CI =& get_instance();
	$CI->make->sForm("trans/pop_edit_menu_item_db",array('id'=>'pop_edit_menu_item_form'));
		$CI->make->sDivRow();
			$CI->make->sDivCol(12);
				$CI->make->input('Menu Item','name',$name,'',array('class'=>'rOkay', 'disabled'=>'disable'));
				$CI->make->input('Cost','cost',$cost,'',array('class'=>'rOkay', 'disabled'=>'disable'));
				$CI->make->input('Quantity','qty',$qty,'Type Quantity',array('class'=>'rOkay'));
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}

function pop_add_modifier_form($mod_grp, $row)
{
	$CI =& get_instance();
		$CI->make->sForm('trans/pop_add_modifier_db',array('id'=>'new_modifier_form'));
			if(count($mod_grp) > 0){
				$CI->make->sDivRow();
					foreach ($mod_grp as $key => $val) 
					{
						$CI->make->eDivRow(array('class'=>'row-div-btn'));
							$CI->make->sDivCol();
								$CI->make->H(4, $key ,array());
								$CI->make->hidden('order_row',$row,array());
									
							$CI->make->eDivCol();
							foreach($val as $v)
							{
								$CI->make->sDivCol(3);
									$CI->make->A(ucwords($v->name),'#'.$v->name,array('class'=>'btn btn-primary add-to-menu-mod btn-block',
																			'return'=>false,
																			'line_id'=>$row,
																			'data-cost'=>$v->cost,
																			'ref'=>$v->mod_id));
								$CI->make->eDivCol();
							}
						$CI->make->sDivRow();
					}
				$CI->make->eDivRow();
			}else{
				$CI->make->sDivCol();
					$CI->make->H(4, 'No modifier available.',array());
				$CI->make->eDivCol();

			}
		$CI->make->eForm();
	return $CI->make->code();
}
function customer_address_tbl($cust_id, $list)
{
	$CI =& get_instance();
		if(count($list) > 0){
			foreach ($list as $val) {
				$CI->make->sRow(array('class'=>'t-rows', 'ref'=>$val->id,'id'=>'row-'.$val->id));
		            $CI->make->td(ucwords($val->base_location));
		            $CI->make->td(ucwords($val->street_no));
		            $CI->make->td(ucwords($val->street_address));
			        $CI->make->td(ucwords($val->city));
		            $CI->make->td(ucwords($val->region));
		            $CI->make->td(ucwords($val->zip));
		        $CI->make->eRow();
			}
		}
	return $CI->make->code();
}

function branches_list_tbl($list, $vicinity){
	$CI =& get_instance();
		if(count($list) > 0){
			foreach ($list as $val) {
				$CI->make->sRow(array('class'=>'t-rows', 'ref'=>$val->branch_id,'id'=>'row-'.$val->branch_id));
		            $CI->make->td(ucwords($val->branch_name));
		            $CI->make->td(ucwords($val->address));
		            $CI->make->td(ucwords($val->base_location));
		            $CI->make->td($val->non_del_time . ' mins');
		            $CI->make->td(ucwords($val->contact_no));
			    $CI->make->eRow();
			}
		}
	return $CI->make->code();
}

function makeOrderSummary($total_amount, $cust_address, $cust_details, $branch)
{
	$cart = sess('ord_cart');
	$modifiers = sess('ord_mod');
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->H(5,'<strong>Customer Name: </strong>&nbsp;'.ucwords($cust_details->fname . ' ' . $cust_details->mname . ' ' . $cust_details->lname),array());
				$CI->make->H(5,'<strong>Address: </strong>&nbsp;'.ucwords($cust_address->street_no.' '.$cust_address->street_address.', '.$cust_address->city.', '.$cust_address->region.', '.$cust_address->base_location.' '.$cust_address->zip),array());
				$CI->make->H(5,'<strong>Mode of payment: </strong>&nbsp;',array());
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->H(5,'<strong>Branch: </strong>&nbsp;'. $branch->branch_code . " - " .$branch->branch_name,array());
				$CI->make->H(5,'<strong>Order Date: </strong>&nbsp;'. phpNow(),array());
				$CI->make->H(5,'<strong>Order Type:</strong> Delivery &nbsp;',array());
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
		$CI->make->sDiv(array('class'=>'table-responsive','style'=>'height: 300px; overflow-x: auto;'));
			$CI->make->sTable(array('class'=>'table table-striped'));

				$CI->make->sRow();
					$CI->make->th('Qty',array('style'=>'width:30%;'));
					$CI->make->th('Name',array('style'=>'width:30%;'));
					$CI->make->th('Cost', array('style'=>'width:30%'));
				$CI->make->eRow();
				foreach($cart as $k=>$p)
				{
					$CI->make->sRow();
			            $CI->make->td(ucwords($p['qty']));
			            $CI->make->td(ucwords($p['name']));
			            $CI->make->td('PHP '. num($p['cost']));
			   		$CI->make->eRow();
					
					$curr = $p['line_id'];
					foreach($modifiers as $key=>$val)
					{
						if($curr == $val['line_id'])
						{
							$CI->make->sRow();
					            $CI->make->td();
					            $CI->make->td(ucwords($val['mod_name']), array('colspan'=>2));
					     	$CI->make->eRow();
				   		}
					}
			   	}
			$CI->make->eTable();
		$CI->make->eDiv();
   		$CI->make->sDivRow();
		    $CI->make->sDivCol(12);
		      $CI->make->sBox('default',array('class'=>'box-solid'));
			       $CI->make->sBoxHead(array());
			        	$CI->make->boxTitle('<strong>Total Cost: &nbsp;&nbsp;&nbsp'. $branch->currency. ' ' .number_format($total_amount,2).'</strong>&nbsp;&nbsp;', array('style'=>"float:right;"));
			       $CI->make->eBoxHead();
		      $CI->make->eBox();
	      	$CI->make->eDivCol();
		 $CI->make->eDivRow();
		
	return $CI->make->code();
}

function makeDisplayCode($uniq_code){
	$CI =& get_instance();
		$CI->make->sDivRow();
		    $CI->make->sDivCol(12);
		      $CI->make->sBox('default',array('class'=>'box-solid'));
			       $CI->make->sBoxHead(array());
			        	$CI->make->boxTitle('<strong><h4>Present this code upon delivery.<br/><strong>Code: &nbsp;&nbsp;'.$uniq_code.'</h4></strong>', array('style'=>''));
			       $CI->make->eBoxHead();
		      $CI->make->eBox();
	      	$CI->make->eDivCol();
		 $CI->make->eDivRow();
	return $CI->make->code();
		
}
function makeDisplayAlertMsg($msg=null){
	$CI =& get_instance();
		$CI->make->sDivRow();
		    $CI->make->sDivCol(12);
		      $CI->make->sBox('default',array('class'=>'box-solid'));
			       $CI->make->sBoxHead(array());
			        	$CI->make->boxTitle('<strong><h4>No available branch for the customers location. <br/>All branches will be displayed. </h4></strong>', array('style'=>''));
			       $CI->make->eBoxHead();
		      $CI->make->eBox();
	      	$CI->make->eDivCol();
		 $CI->make->eDivRow();
	return $CI->make->code();
}