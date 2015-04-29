<script>
$(document).ready(function(){

	$('.daterangepicker').keydown(function(e) {
	   e.preventDefault();
	   return false;
	});
		
	
	<?php if($use_js == 'controlPanelJs'): ?>
		
		$('input.daterangepicker').daterangepicker({separator:' to ',
 													format: 'MM-DD-YYYY',
													});

		var scrolled=0;
		startTime();
		// // terminal = $('#terminal-btn').attr('type');
		status = 'badge-pending';
		// // types = $('#types-btn').attr('type');
		now = $('#now-btn').attr('type');
		search_id = '#';
		search_val = '';
		search_by = 'name';
		var daterange = '';
		// loadOrders(status,now,search_id);
		// .order-view-list
    	(function setup_notification(){
    		$.post(baseUrl + 'cashier/set_notification', function(data){
    		    $('span.badge-cancel').html(data.count_cancelled);
			    $('span.badge-pending').html(data.count_unprocessed);
    		},'json');
    		setTimeout(setup_notification, 1000)
		})();


		$('.notification div').click(function(){

			$('#orders-search').hide();
			$('#all-orders').hide();
  			if($(this).attr('src') == 'cancel')
	  			status = 'cancelled';			
  			if($(this).attr('src') == 'pending')
  				status = 'pending';
  			
			now = $('#now-btn').attr('type');
	    	search_id = '#';
    		loadOrders(status,now,search_id);
    	});

		$('.notification div[src=pending]').click();
    	
    	$('.orders-lists').perfectScrollbar({suppressScrollX: true});
		$('#manager-btn').click(function(){
			event.preventDefault();
			window.location = baseUrl+'manager';
		});
		$('#gift-card-btn').click(function(){
			window.location = baseUrl+'gift_cards/cashier_gift_cards';
			return false;
		});
		$('#customer-btn').click(function(){
			window.location = baseUrl+'customers/cashier_customers';
			return false;
		});
		$('#time-clock-btn').click(function(){
			window.location = baseUrl+'clock';
			return false;
		});
		$('#dine-in-btn').click(function(){
			window.location = baseUrl+'cashier/tables';
			return false;
		});
		$('.order-date-form').rPopFormFile({
			asJson : true,
			onComplete: function(data){
				
				var formData = 'order_delivery_time='+data.time+'&order_delivery_date='+data.date;
				$.post(baseUrl+'wagon/add_to_wagon/trans_date_time',formData, function(){
					$('[data-bb-handler=cancel]').click();
					window.location = baseUrl+'cashier/delivery';
				});
					return false;
			}
		});
		$('#delivery-btn').click(function(){
			$('a#order_date_id').click();
			$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red
		});
		$('#pickup-btn').click(function(){
			window.location = baseUrl+'cashier/pickup';
			return false;
		});
		$('#counter-btn').click(function(){
			window.location = baseUrl+'cashier/counter/counter';
			return false;
		});
		$('#drive-thru-btn').click(function(){
			window.location = baseUrl+'cashier/counter/drivethru';
			return false;
		});
		$('#takeout-btn').click(function(){
			window.location = baseUrl+'cashier/counter/takeout';
			return false;
		});
		$('#back-office-btn').click(function(){
			window.location = baseUrl;
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		
		$("#refresh-btn").click(function(){
			$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red

			$('#orders-search').hide();
			// terminal = $('#terminal-btn').attr('type');
    		// status = $('#status-btn').attr('type');
    		// types = $('#types-btn').attr('type');
    		now = $('#now-btn').attr('type');
    		search_id = '#';
    		loadOrders(status,now,search_id);
			//loadOrders();
			return false;
		});
		$("#hold-btn, #resume-btn").click(function(){
			var id = $('.order-view-list').attr('ref'), btn = $(this);
			var hold_status = $(this).attr('hold_status');
			btn.goLoad();
			$.post(baseUrl+'cashier/hold_status_db/'+id+'/'+hold_status, function(data){
				rMsg(data.msg, 'success');
				btn.goLoad({load:false});
				if(hold_status == '0') //ihohold
				{
					$('#hold-btn').attr('hold_status',1).closest('div').css('display','block');
					$('#resume-btn').closest('div').css('display','none');
				}else //ireresume
				{
					$('#resume-btn').attr('hold_status',0).closest('div').css('display','block');
					$('#hold-btn').closest('div').css('display','none');
				}	

			},'json');

		});
		$("#recall-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			window.location = baseUrl+'cashier/counter/'+type+'/null/'+id;
			return false;
		});
		$("#split-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			window.location = baseUrl+'cashier/split/'+type+'/null/'+id;
			return false;
		});
		$("#combine-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			window.location = baseUrl+'cashier/combine/'+type+'/null/'+id;
			return false;
		});
		$("#void-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var status = $('.order-view-list').attr('status');
			loadDivs('reasons');
			return false;
		});
		$("#complain-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var status = $('.order-view-list').attr('status');
			$('#cashier-panel button.complaint-selected').removeClass('complaint-selected');
			$('#remarks').val('');

			$.post(baseUrl+'cashier/get_complain_order/'+id,function(data){
				if(data.list != null)
				{
					var datetime = null;
					$.each( data.list, function( key, value ) {
						$( ".complaint-btns:contains('"+value.reason+"')" ).addClass('complaint-selected');
						if ($("#complain_remarks:contains(" + value.remarks + ")").length == 0){
							$('#complain_remarks').append(' '+value.remarks);
					    }
					});
				}	
			},'json');
			
			loadDivs('complaints');
			return false;
		});
		$(document).on('click', '.complaint-btns', function() { 
			var btn = $(this);
			btn.toggleClass( "complaint-selected");
		});
		$(document).on('click', '.submit-complaints-btn', function(){
			var btn = $(this);
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var remarks = $('#complain_remarks').val();
			var selected = $('#cashier-panel button.complaint-selected').length;
			btn.goLoad();
		
			if(selected > 0)
			{
				var error = 0;
				var message = '';

				$('#cashier-panel button.complaint-selected').each(function(){
					var reason = $(this).text();
					message+=reason+"|";
				});	
				
				formData = 'reason='+message+'&remarks='+remarks;
					$.post(baseUrl+'cashier/complain_order/'+id,formData,function(data){
						// alert(data);
					},'json');
					// });
				$(".cancel-complaints-btn").trigger('click');
				rMsg('Complaints have been added.','success');
			}else
				rMsg('Please select reason of customer complaints.','error');
			
			btn.goLoad({load:false});
			return false;
		});
		$(document).on('click', '.reason-btns', function() { 
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var reason = $(this).text();
			var btn = $(this);
			btn.goLoad();
			formData = 'reason='+reason;
			$.post(baseUrl+'cashier/void_order/'+id,formData,function(data){
				if(data.error == ""){
					$("#refresh-btn").trigger('click');
					rMsg('Success!  Voided '+type+' #'+id,'success');
				}
				else{
					rMsg(data.error,'error');
				}
				btn.goLoad({load:false});
			},'json');
			// });
			return false;
		});

		$(".cancel-reason-btn, .cancel-complaints-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			$('#order-btn-'+id).trigger('click');
			return false;
		});
		$("#settle-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			window.location = baseUrl+'cashier/settle/'+id;
			return false;
		});
		$("#cash-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			window.location = baseUrl+'cashier/settle/'+id+'#cash';
			return false;
		});
		$("#credit-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			window.location = baseUrl+'cashier/settle/'+id+'#credit';
			return false;
		});
		$("#receipt-btn").click(function(event){
			event.preventDefault();
			var id = $('.order-view-list').attr('ref');
			$.post(baseUrl+'cashier/print_sales_receipt/'+id,'',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
		});
		$("#back-order-list-btn").click(function(){
			if(status == 'all')
				loadDivs(status);
			else		
				loadDivs('orders');
			return false;
		});
		function loadDivs(type){
			$('.center-loads-div').hide();
			$('.'+type+'-div').show();
		}
		function loadOrders(status,now,search_val){
			
			$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red
			$('.orders-lists').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');

			$.post(baseUrl+'cashier/orders/'+status+'/'+now+'/'+search_val+'/box/'+search_by,function(data){
				// alert(data);
				$('.orders-lists').html(data.code);
				$('#all-orders').hide();
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						$('#order-btn-'+id).click(function(){
							$('.orders-view').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin"></i></div></center>');
								
							$.post(baseUrl+'cashier/order_view/'+id,function(data){
								loadDivs('orders-view');

								$('.order-view-list').attr('ref',id);
								$('.order-view-list').attr('type',val.type);
								$('.order-view-list').attr('status',val.status);
								
								if(data.on_hold == '0'){
									$('#hold-btn').attr('hold_status',1).closest('div').css('display','block');
									$('#resume-btn').closest('div').css('display','none');
								}else{
									$('#resume-btn').attr('hold_status',0).closest('div').css('display','block');
									$('#hold-btn').closest('div').css('display','none');
								}

								if(data.delivered == '0'){
									$('#complain-btn').closest('div').css('display','none');
									$('#void-btn').closest('div').css('display','block');
								}
								else{
									$('#void-btn').closest('div').css('display','none');
									$('#complain-btn').closest('div').css('display','block');
								}
								
								$('.order-view-list').html(data.code);
							
								$('#customer_info').html(data.info);
							
								$('.orders-lists').perfectScrollbar({suppressScrollX: true});
								$('.order-view-list .body').perfectScrollbar({suppressScrollX:true});
								$('.order-view-list .footer').perfectScrollbar({suppressScrollX:true});

							},'json');
							// });
							return false;
						});
					});
				}
				loadDivs('orders');
			},'json');
			// });
		}
		function loadOrderSearch(status,now,search_val){

			$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red
			$('.search-result').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			$.post(baseUrl+'cashier/orders/'+status+'/'+now+'/'+search_val+'/box/'+search_by+'/'+daterange,function(data){
					$('.search-result').html(data.code);
					if(data.ids != null){
						$('.orders-lists').html('');
						$.each(data.ids,function(id,val){
							
							$('.order-btn-'+id).click(function(){
								$('.order-view-list').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin"></i></div></center>');
									
								$.post(baseUrl+'cashier/order_view/'+id,function(data){
									$('#orders-search').hide();
									$('#all-orders').hide();
									loadDivs('orders-view');

									$('.order-view-list').attr('ref',id);
									$('.order-view-list').attr('type',val.type);
									$('.order-view-list').attr('status',val.status);
									
									if(data.on_hold == '0'){
										$('#hold-btn').attr('hold_status',1).closest('div').css('display','block');
										$('#resume-btn').closest('div').css('display','none');
									}else{
										$('#resume-btn').attr('hold_status',0).closest('div').css('display','block');
										$('#hold-btn').closest('div').css('display','none');
									}
									if(data.delivered == '0'){
										$('#complain-btn').closest('div').css('display','none');
										$('#void-btn').closest('div').css('display','block');
									}
									else{
										$('#void-btn').closest('div').css('display','none');
										$('#complain-btn').closest('div').css('display','block');
									}

									$('.order-view-list').html(data.code);
								
									$('#customer_info').html(data.info);
									$('.search-result').perfectScrollbar({suppressScrollX: true});
									$('.order-view-list .body').perfectScrollbar({suppressScrollX:true});
									$('.order-view-list .footer').perfectScrollbar({suppressScrollX:true});

								},'json');
								return false;
							});
						});
					}
			},'json');
			// });
		}

		//timeticker
		function startTime(){
                var today = new Date();
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();

                // add a zero in front of numbers<10
                m = checkTime(m);
                s = checkTime(s);

                //Check for PM and AM
                var day_or_night = (h > 11) ? "PM" : "AM";

                //Convert to 12 hours system
                if (h > 12)
                    h -= 12;

                //Add time to the headline and update every 500 milliseconds
                $('#time').html(h + ":" + m + ":" + s + " " + day_or_night);
                setTimeout(function() {
                    startTime()
                }, 500);
        }
        function checkTime(i){
            if (i < 10)
            {
                i = "0" + i;
            }
            return i;
        }
	    ///////////////////////////////Jed//////////////////////////////////
	    ////////////////////////////////////////////////////////////////////
    	$('#terminal-btn').click(function(){
    		$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'my'){
    			act = 'all';
    			$(this).attr('type',act);
    			$('#terminal_text').html('<i class="fa fa-users fa-2x fa-fw"></i><br>ALL');
    		}else{
    			act = 'my';
    			$(this).attr('type',act);
    			$('#terminal_text').html('<i class="fa fa-desktop fa-2x fa-fw"></i><br>MY');
    		}

    		// terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		// types = $('#types-btn').attr('type');
    		now = $('#now-btn').attr('type');
    		search_id = '#';

    		loadOrders(status,now,search_id);
    	});

  		

  		$('.status-btn').click(function(){
    		$('#orders-search').hide();
    		// btn = $(this).attr('btn');
    		status = $(this).attr('type');
    		if(status == 'all')
    		{
	    		$('.orders-lists').hide();
	    		$('.orders-div').html('');
	    		$('.reasons-div').hide();
	    		$('.orders-view-div').hide();
	    		$('#orders-search').hide();
	    		$('#all-orders').show();
	    		$('.search-result').perfectScrollbar({suppressScrollX: true});
    		}	
    		else
	    		loadOrders(status,now,search_id);

    	});

    	$('#types-btn').click(function(){
    		$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red
    		$('#all-orders').hide();
    		$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'all'){
    			act = 'dinein';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-sign-in fa-2x fa-fw"></i><br>DINE IN');
    		}
    		else if(type == 'dinein'){
    			act = 'delivery';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-truck fa-2x fa-fw"></i><br>DELIVERY');
    		}
    		else if(type == 'delivery'){
    			act = 'counter';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-keyboard-o fa-2x fa-fw"></i><br>COUNTER');
    		}
    		else if(type == 'counter'){
    			act = 'retail';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-usd fa-2x fa-fw"></i><br>RETAIL');
    		}
    		else if(type == 'retail'){
    			act = 'pickup';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-briefcase fa-2x fa-fw"></i><br>PICKUP');
    		}
    		else if(type == 'pickup'){
    			act = 'takeout';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-sign-out fa-2x fa-fw"></i><br>TAKEOUT');
    		}
    		else if(type == 'takeout'){
    			act = 'drivethru';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-road fa-2x fa-fw"></i><br>DRIVE-THRU');
    		}else if(type == 'drivethru'){
    			act = 'all';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-book fa-2x fa-fw"></i><br>ALL TYPES');
    		}

    		// terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		// types = $('#types-btn').attr('type');
    		now = $('#now-btn').attr('type');
    		search_id = '#';

    		loadOrders(status,now,search_id);
    	});
		$('#now-btn').click(function(){
			$('#look-btn').attr('style', 'background-color: #D22121 !important;'); //red

			$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'all_trans'){
    			act = 'now';
    			$(this).attr('type',act);
    			$('#day_text').html('<i class="fa fa-clock-o fa-2x fa-fw"></i><br>TODAY');
    		}else{
    			act = 'all_trans';
    			$(this).attr('type',act);
    			$('#day_text').html('<i class="fa fa-clock-o fa-2x fa-fw"></i><br>ALL');
    		}

    		// terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		// types = $('#types-btn').attr('type');
    		now = $('#now-btn').attr('type');
    		search_id = '#';

    		loadOrders(status,now,search_id);
    	});
    	$('#look-btn').click(function(){
    		$('#look-btn').attr('style', 'background-color: #edbb0e !important;');
    		$('.orders-lists').hide();
    		$('.reasons-div').hide();
    		$('.orders-view-div').hide();
    		$('.orders-div').html('');
    		$('#all-orders').hide();
    		$('#orders-search').show();
    		$('.search-result').perfectScrollbar({suppressScrollX: true});
    		
    	});
    	$('#go-all-btn').click(function(){
    		daterange = $('#all_daterange').val();
    		search_val = 'all';
    		loadOrderSearch(status,now,search_val);
    	});
    	
		$('#go-search-btn').click(function(event){
			
			search_by = $('#search_by option:selected').val();
			search_val = $('#search_transaction').val();
			daterange = $('#daterange').val();
			
    		loadOrderSearch(status,now,search_val);
    		$('.orders-lists').hide();
    		$('#look-btn').attr('style', 'background-color: #edbb0e !important;');
    		// $('#search_transaction').val('');
    		event.preventDefault();
		});

    <?php elseif($use_js == 'counterJs'): ?>


    	var scrolled=0;
		var transScroll=0;
		var minimum = 0;
		var type_ , id_ , opt_, trans_id_;

		var get_min_purchase = function(){
				var branch_id = $('#branch_id').val();
				
				$.post(baseUrl+'cashier/set_minimum_purchase','branch_id='+branch_id,function(data){
					minimum = data.min_purchase;
				}, 'json');
		}

		get_min_purchase();
		loadAutoCharges();

		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadMenuCategories();
		loadTransCart();
		loadTransChargeCart();
		set_label_disc_amount();

		$('#is_absolute').on('change', function(){
			set_label_disc_amount();
		});

		function set_label_disc_amount(){
			var selected = $('#is_absolute option:selected').val();
			if(selected == 1)  //fixed amount
			{
				$('#disc-amount').next('span').text('php');
				$('#disc-amount').val('');
			}else
			{
				$('#disc-amount').next('span').text('%');
				$('#disc-amount').val('');
			}	
		}

		$('#submit-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans/true/null/false/0/null/null/'+print,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}
			},'json');
		
			return false;
		});
		$('#print-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i> NO PRINT');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i> PRINT');
			}
		});
		$('#hold-all-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}
			},'json');
			return false;
		});
		$('#settle-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					newTransaction(false);
					if(btn.attr('id') == 'settle-btn'){
						rMsg('Success! Transaction Submitted.','success');
					}
					else{
						rMsg('Transaction Hold.','warning');
					}
					btn.prop('disabled', false);
					window.location = baseUrl+'cashier/settle/'+data.id;
				}
			},'json');
			return false;
		});
		$('#cash-btn').click(function(){
			if(!$('#add-vip-btn').is(':disabled'))
				$.post(baseUrl+'cashier/check_vip_cart',function(data){
					if(data.count == 0)
					{
						bootbox.dialog({
							  message: baseUrl +'cashier/vip_confirmation',
							  title: "",
							  buttons: {
							   success: {
							      label: "Yes",
							      className: "btn-success",
							      callback: function() {
							      		$('#add-vip-btn').trigger('click');
							      		$('#disc-amount').focus();
							      }
							    },
							    danger: {
							      label: "No",
							      className: "btn-danger",	
							      callback: function() {
							      		cash_function();
							      }
							    }
							  }
						});			
					}else{
						cash_function();
					}
				},'json');
			else
				cash_function();

			return false;
		});
		function cash_function(){
			var confirmed = $('#counter').attr('confirmed');
			if($('.unavailable-1').length == 0)
			{
				var btn = $(this);
				btn.prop('disabled', true);
				var formData = 'instruction='+ $('#instruction').val();

				$.post(baseUrl+'wagon/add_to_wagon/trans_instruction',formData);
				
				$.post(baseUrl+'cashier/submit_trans/true/settle/',{'confirmed':confirmed},function(data){
					
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/payment_method/'+data.id+'#cash';
					}
		
				},'json');
				
			}else{
				rMsg('Please remove all cancelled items.','error');
			}
		}
		$('#credit-btn').click(function(){
			
			if(!$('#add-vip-btn').is(':disabled'))
				$.post(baseUrl+'cashier/check_vip_cart',function(data){
					
					if(data.count == 0)
					{
						bootbox.dialog({
							  message: baseUrl +'cashier/vip_confirmation',
							  title: "",
							  buttons: {
							   success: {
							      label: "Yes",
							      className: "btn-success",
							      callback: function() {
							      		$('#add-vip-btn').trigger('click');
							      		$('#disc-amount').focus();
							      }
							    },
							    danger: {
							      label: "No",
							      className: "btn-danger",
							      callback: function() {
							      		credit_function();
							      }
							    }
							  }
						});			
					}else{
						credit_function();
					}
				},'json');
				// });
			else{
				credit_function();
			}
			return false;
		});
		function credit_function(){
			var confirmed = $('#counter').attr('confirmed');
			if($('.unavailable-1').length == 0)
			{
				var btn = $(this);
				btn.prop('disabled', true);
				var formData = 'instruction='+ $('#instruction').val();
				$.post(baseUrl+'wagon/add_to_wagon/trans_instruction',formData);

				$.post(baseUrl+'cashier/submit_trans/true/settle',{'confirmed':confirmed},function(data){
					// alert(data);
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/payment_method/'+data.id+'#credit';
					}
				},'json');
				// });
			}else{
				rMsg('Please remove all cancelled items.','error');
			}
		}

		$('#qty-btn').click(function(){
			$('#qty-amount').val(1);
			var sel = $('.selected');
			if(sel.exists()){
				loadsDiv('qty',null,null,null);
			}
			return false;
		});

		$('#qty-btn-cancel,#qty-btn-done').click(function(){
			$('.loads-div').hide();
			$('.menus-div').show();
			loadsDiv(type_, id_, opt_, trans_id_);
			return false;
		});
		$(".edit-qty-btn").click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var formData = 'value='+btn.attr('value')+'&operator='+btn.attr('operator');
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
				var qty = data.qty;
				$('#trans-row-'+id+' .qty').text(qty);
				$('#qty-amount').val(qty);
				btn.prop('disabled', false);
				transTotal();
			},'json');
			return false;
		});

		$('#qty-amount').change(function() {
	  		var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var formData = 'value=0&operator=none'+'&qty='+$(this).val();
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
				var qty = data.qty;
				$('#trans-row-'+id+' .qty').text(qty);
				btn.prop('disabled', false);
				transTotal();
			},'json');
			return false;
		});

		/*
		# VIP DISCOUNTS
		*/
		$('#add-vip-btn').click(function(){
			loadsDiv('vip-discount',null,null,null);
			return false;
		});

		$('#add-vip-disc-btn').click(function(){
			var is_absolute = $('#is_absolute option:selected').val();
			var disc_rate = $('#disc-amount').val();
			var formData = 'is_absolute='+is_absolute+'&disc_rate='+disc_rate;
			if(disc_rate != '')
				$.post(baseUrl+'cashier/add_trans_vip_disc',formData,function(data){
					$('#trans-discount-row-vip').remove();
					makeDiscountItemRow(data.id, data.items);
					rMsg('VIP discount has been added.','success');
					transTotal();
				},'json');
			else
				rMsg('Please input discount rate.','error');
			return false;
		});
		//START FROM NEW DINE

		$('#remove-disc-btn').click(function(){
			var disc_code = $('#disc-disc-code').val();
			$.post(baseUrl+'cashier/del_trans_disc/'+disc_code,function(data){
				rMsg('Discounts Removed','success');
				$('.disc-person').remove();
				$('#disc-form')[0].reset();
				$('#disc-guests').val('');
				transTotal();
			});
			return false;
		});

		$('#add-discount-btn').click(function(){
			loadsDiv('sel-discount',null,null,null);
			loadSRDiscounts();
			return false;
		});
		$('#add-disc-person-btn').click(function(){
			var noError = $('#disc-form').rOkay({
		     				btn_load		: 	$(this),
		     				goSubmit		: 	false,
		     				bnt_load_remove	: 	true
						  });
			if(noError){
				var guests = $('#disc-guests').val();
				var ref = $(this).attr('ref');
				var disc_rate = $('#disc-disc-rate').val();
				var formData = $('#disc-form').serialize();
				formData = formData+'&type='+ref+'&guests='+guests+'&disc-disc-rate='+disc_rate;
				$.post(baseUrl+'cashier/add_person_disc',formData,function(data){
					if(data.error==""){
						$('#disc-guests').val(data.count);
						$('.disc-persons-list-div').html(data.code);
						$.each(data.items,function(code,opt){
							$("#disc-person-"+code).click(function(){
								var lin = $(this);
								$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
									lin.remove();
									rMsg('Person Removed.','success');
									$('#disc-guests').val($('#disc-guests').val()-1);
									transTotal();
								});
								return false;
							});
						});
						transTotal();
						$('#disc-cust-name, #disc-cust-code, #disc-cust-bday').val('');
					}
					else{
						rMsg(data.error,'error');
					}
				},'json');
				// });
				$('#disc-guests').val(guests);
			}
			return false;
		})
		
		$('.disc-btn-row').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				// alert(data);
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					if(data.id == 'SNDISC')
						$('#trans-discount-row-'+data.id).remove();

					makeDiscountItemRow(data.id, data.items);
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// });
			return false;
		});

		function makeDiscountItemRow(id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-discount-row-'+id,'discount-id':id,'ref':id,'class':'sel-row trans-discount-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					loadsDiv('sel-discount',null,null,null);
					loadSRDiscounts();
					return false;
				});
			$('<span/>').attr('class','qty').html('<i class="fa fa-tag"></i>').css('margin-left','10px').appendTo('#trans-discount-row-'+id);
			$('<span/>').attr('class','name').text(opt.disc_code).appendTo('#trans-discount-row-'+id);
			var tx = opt.amount;
			
			// $('<span/>').attr('class','cost').number(tx, 2).prepend('( ₱ ').append(' )').css('margin-right','10px').appendTo('#trans-discount-row-'+id);
			
			if(opt.is_absolute == 0){
				tx = opt.amount+'%';
				$('<span/>').attr('class','cost').text(tx).css('margin-right','10px').appendTo('#trans-discount-row-'+id);

			}else{
				$('<span/>').attr('class','cost').number(tx, 2).prepend('( ₱ ').append(' )').css('margin-right','10px').appendTo('#trans-discount-row-'+id);
			}


		
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}

		//---END OF FROM NEW DINE
		$('#remove-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				var id = sel.attr('ref');
				var cart = 'trans_cart';
				if(sel.hasClass('trans-sub-row'))
					cart = 'trans_mod_cart';
				else if(sel.hasClass('trans-charge-row'))
					cart = 'trans_charge_cart';
				$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
					sel.prev().addClass('selected');
					sel.remove();
					if(cart == 'trans_cart'){
						$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
							var cat_id = $(".category-btns:first").attr('ref');
							var cat_name = $(".category-btns:first").text();
							var val = {'name':cat_name};
							loadsDiv('menus',cat_id,val,null);
							$('.trans-sub-row[trans-id="'+id+'"]').remove();
						});
					}
					$('.counter-center .body').perfectScrollbar('update');
					transTotal();
				},'json');
			}
			return false;
		});
		$('#charges-btn').click(function(){
			$('.charges-div .title').text('Select Charges');
			loadCharges();
			loadsDiv('charges',null,null,null);
			return false;
		});
		$('#manager-btn').click(function(){
			window.location = baseUrl+'manager';
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$("#menu-cat-scroll-down").on("click" ,function(){
		    scrolled=scrolled+100;
			$(".menu-cat-container").animate({
			        scrollTop:  scrolled
			});
		});
		$("#menu-cat-scroll-up").on("click" ,function(){
			scrolled=scrolled-100;
			$(".menu-cat-container").animate({
			        scrollTop:  scrolled
			});
		});
		$(".menu-cat-container").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		function loadMenuCategories(){
		 	$.post(baseUrl+'cashier/get_menu_categories',function(data){
		 		showMenuCategories(data,1);
		 	},'json');
		}
		function showMenuCategories(data,ctr){
				$.each(data,function(cat_id,val){
					if(ctr == 1){
						loadsDiv('menus',cat_id,val,null);
					}
		 			$('<button/>')
		 			.attr({'id':'menu-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-silver double btn btn-default'})
		 			.text(val.name)
		 			.appendTo('.menu-cat-container')
		 			.click(function(){
		 				loadsDiv('menus',cat_id,val,null);
		 				return false;
		 			});
					ctr++;
				});
		}
		function loadsDiv(type,id,opt,trans_id){

			if(type == 'menus'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				$('.menus-div .title').text(opt.name);
				$('.menus-div .items-lists').html('');
				$.post(baseUrl+'cashier/get_menus/'+id,function(data){
					$.each(data,function(menu_id,opt){
						$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-default'})
			 			.css({'margin':'5px','width':'205px'})
			 			.text(opt.name)
			 			.appendTo('.menus-div .items-lists')
			 			.click(function(){
			 				$.post(baseUrl+'trans/check_menu_availability/'+opt.branch_menu_id, function(data){
			 					if(data.error != '')
			 					{
			 						rMsg(data.msg, data.error);
			 						return false;
			 					}else{
					 				$.post(baseUrl+'trans/check_menu_schedule/'+opt.sched_id,function(data)
		 							{
		 								if(data.msg != '')
						 					rMsg(data.msg,'error');
						 				else{
							 				addTransCart(menu_id,opt);
							 				return false;
							 			}
							 			
					 				},'json');			 						
			 					}

 							},'json');
			 			});
			 		
			 		});
			 	},'json');
				// });
			}
			else if(type=='mods'){
				$('.mods-div .title').text(opt.name+" Modifiers");
				$('.mods-div .mods-lists').html('');
				var trans_det = opt;
				$.post(baseUrl+'cashier/get_menu_modifiers/'+id,function(data){
					if(!$.isEmptyObject(data)){
						$('.loads-div').hide();
						$('.'+type+'-div').show();
						$.each(data,function(mod_group_id,opt){
							var row = $('<div/>').attr({'class':'mod-group','id':'mod-group-'+mod_group_id}).appendTo('.mods-div .mods-lists');
							$.each(opt.details,function(mod_id,det){
								var mname = det.name; 
								$('<button/>')
								.attr({'id':'mod-'+mod_id,'ref':mod_id,'class':'counter-btn-silver btn btn-default'})
								.css({'margin':'5px'})
								.text(mname.toUpperCase())
								.appendTo('#mod-group-'+mod_group_id)
								.click(function(){
									addTransModCart(trans_id,mod_group_id,mod_id,det,id,$(this),trans_det);
									return false;
								});
				 			});
				 			$('<hr/>').appendTo('#mod-group-'+mod_group_id);
				 		});
					}
			 	},'json');
			}
			else if(type=='qty'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='charges'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
			else if(type=='sel-discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}else if(type='vip-discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
				//vip-discount-div
			}
		}
		function setModValues(type, id, opt, trans_id){
			$('#qty-btn').trigger('click');
			type_ = type;
			id_ = id;
			opt_ = opt;
			trans_id_ = trans_id;
		}
		function addTransCart(menu_id,opt){
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&no_tax='+opt.no_tax+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				setModValues('mods',menu_id,data.items,data.id);
				transTotal();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det){
			var formData = 'trans_id='+trans_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg('Modifier is Already Added!','error');
				}
				else{
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				btn.prop('disabled', false);
				transTotal();
			},'json');
		}
		function makeItemRow(id,menu_id,opt){
			
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'unavailable-'+opt.cancelled+' sel-row trans-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					if($(this).hasClass('unavailable-1') == true)
					{
						$('#add-discount-btn, #charges-btn, #qty-btn').attr("disabled","disabled");
					}else
						$('#add-discount-btn, #charges-btn, #qty-btn').removeAttr("disabled");

					selector($(this));
					loadsDiv('mods',menu_id,opt,id);
					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').number(opt.cost, 2).prepend('₱ ').css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'unavailable-'+opt.cancelled+' trans-sub-row sel-row'})
								   .click(function(){
									if($(this).hasClass('unavailable-1') == true)
										$('#add-discount-btn, #charges-btn, #qty-btn').attr("disabled","disabled");
									else
										$('#add-discount-btn, #charges-btn, #qty-btn').removeAttr("disabled");

										selector($(this));
										loadsDiv('mods',trans_det.menu_id,trans_det,trans_id);
										return false;
									});
			$('<span/>').attr('class','name').css('margin-left','26px').text(opt.name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').number(opt.cost, 2).prepend('₱ ').appendTo(subRow);
			$('.selected').after(subRow);
			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				// alert(data);
				var net = data.total;
				var discount = data.discount;
				var total = net + data.discount;

				if(total >= minimum)
				{
					$('#credit-btn, #cash-btn').removeAttr('disabled');
				}else{
					$('#credit-btn, #cash-btn').attr('disabled', 'disable');
				}

				$("#total-txt").number(total,2).prepend('₱ ');
				$("#discount-txt").number(discount,2).prepend('₱ ');
				$("#net-discount-txt").number(net,2).prepend('₱ ');
			},'json');
			// });
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
				
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt);
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt);
							});
						}
						if(ctr == len)
							$('.selected').trigger('click');
						ctr++;
					});
				}
				transTotal();
				
			},'json');
			// });
		}
		function loadTransChargeCart(){
			$.post(baseUrl+'cashier/get_trans_charges/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(charge_id,opt){
						makeChargeItemRow(charge_id,opt);
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function newTransaction(redirect,type){
			$.post(baseUrl+'cashier/new_trans/true/'+type,function(data){
				if(!redirect){
					$('#trans-datetime').text(data.datetime);
					var tp = data.type;
					$('#trans-header').text(tp.toUpperCase());

					$('.trans-lists').find('li').remove();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					transTotal();
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
				}
				else{
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
					else
						window.location = baseUrl+'cashier/counter/'+data.type;
				}
			},'json');
		}
		function loadDefault(){
			var cat_id = $(".category-btns:first").attr('ref');
			var cat_name = $(".category-btns:first").text();
			var val = {'name':cat_name};
			loadsDiv('menus',cat_id,val,null);
		}
	
		function loadDiscounts(){
			$.post(baseUrl+'cashier/get_discounts',function(data){
				$('.select-discounts-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#item-disc-btn-'+id).click(function(){
						loadsDiv('discount',null,null,null);
						$('.discount-div .title').text($(this).text());
						$('.discount-div #rate-txt').number(opt.disc_rate,2);
						$('#disc-disc-id').val(opt.disc_id);
						$('#disc-disc-rate').val(opt.disc_rate);
						$('#disc-disc-code').val(opt.disc_code);
						$('#disc-no-tax').val(opt.no_tax);
						$('#disc-guests').val(opt.guest);
						$.post(baseUrl+'cashier/load_disc_persons/'+opt.disc_code,function(data){
							$('.disc-persons-list-div').html(data.code);
							$.each(data.items,function(code,opt){
								$("#disc-person-"+code).click(function(){
									var lin = $(this);
									$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
										lin.remove();
										rMsg('Person Removed.','success');
										transTotal();
									});
									return false;
								});
							});
						},'json');
						
						return false;
					});
				});
			},'json');
		}
		function loadSRDiscounts(){
			$.post(baseUrl+'cashier/get_discounts/true',function(data){
				// $('.select-discounts-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					// $('#item-disc-btn-'+id).click(function(){
						loadsDiv('discount',null,null,null);
						$('.discount-div .title').text($(this).text());
						$('.discount-div #rate-txt').number(opt.disc_rate,2);
						$('#disc-disc-id').val(opt.disc_id);
						$('#disc-disc-rate').val(opt.disc_rate);
						$('#disc-disc-code').val(opt.disc_code);
						$('#disc-no-tax').val(opt.no_tax);
						$('#disc-guests').val(opt.guest);
						$.post(baseUrl+'cashier/load_disc_persons/'+opt.disc_code,function(data){

							$('.disc-persons-list-div').html(data.code);
							$.each(data.items,function(code,opt){
								$("#disc-person-"+code).click(function(){
									var lin = $(this);
									$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
										lin.remove();
										rMsg('Person Removed.','success');
										transTotal();
									});
									return false;
								});
							});
						},'json');
						
						return false;
					// });
				});
			},'json');
		}
		function loadAutoCharges(){
			$.post(baseUrl+'cashier/get_charges',function(data){
				$.each(data.apply,function(id,opt){
					addChargeCart(id,opt, false);
					return false;
				});
				
			},'json');
		}	
		function loadCharges(){
			$.post(baseUrl+'cashier/get_charges',function(data){
				$('.charges-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#charges-btn-'+id).click(function(){
						addChargeCart(id,opt, true);
						return false;
					});
				});
			},'json');
		}
		function addChargeCart(id,row, withError){
			var formData = 'name='+row.charge_name+'&code='+row.charge_name+'&amount='+row.charge_amount+'&absolute='+row.absolute;
			$.post(baseUrl+'wagon/add_to_wagon/trans_charge_cart/'+id,formData,function(data){
				if(data.error == null){
					makeChargeItemRow(data.id,data.items);
					transTotal();
				}
				else{
					if(withError)
						rMsg(data.error,'error');
				}
			},'json');
		}
		function makeChargeItemRow(id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-charge-row-'+id,'charge-id':id,'ref':id,'class':'sel-row trans-charge-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					loadCharges();
					loadsDiv('charges',null,null,null);
					return false;
				});
			$('<span/>').attr('class','qty').html('<i class="fa fa-tag"></i>').css('margin-left','10px').appendTo('#trans-charge-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-charge-row-'+id);
			var tx = opt.amount;
			if(opt.absolute == 0){
				tx = opt.amount+'%';
				$('<span/>').attr('class','cost').text(tx).css('margin-right','10px').appendTo('#trans-charge-row-'+id);

			}else{
				$('<span/>').attr('class','cost').number(tx, 2).prepend('₱ ').css('margin-right','10px').appendTo('#trans-charge-row-'+id);
			}

		
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				// alert(data);
			});
		}

	<?php elseif($use_js == 'newAddressJs'): ?>
		$('#city').typeaheadmap({
			"source": function(search, process) {
				var url = $('#city').attr('search-url');
				$.post(baseUrl+url,'search='+search,function(data){
					process(data);
				},'json');
			},
		    "key": "key",
		    "value": "value",
		    "listener": function(k, v) {
				$('#region').attr('value', v);
			}
		});    


	<?php elseif($use_js == 'settleJs'): ?>
		var hashTag = window.location.hash;
		if(hashTag == '#cash'){
			loadDivs('cash-payment',true);
		} else if(hashTag == '#credit'){
			loadDivs('credit-payment',true);
		} else if(hashTag == '#debit'){
			loadDivs('debit-payment',true);
		} else if(hashTag == '#gc'){
			loadDivs('gc-payment',true);
		}

		$('.order-view-list .body').perfectScrollbar({suppressScrollX: true});
		$('#cancel-btn,#finished-btn').click(function(){
			if($('#settle').attr('type') == 'dinein')
				window.location = baseUrl+'cashier/tables/';
			else if($('#settle').attr('type') == 'delivery')
				window.location = baseUrl+'cashier/delivery/';
			else if($('#settle').attr('type') == 'pickup')
				window.location = baseUrl+'cashier/pickup/';
			else
				window.location = baseUrl+'cashier/counter/'+$('#settle').attr('type');
			return false;
		});
		$('#recall-btn').click(function(){
			window.location = baseUrl+'cashier/counter/'+$(this).attr('type')+'/null/'+$(this).attr("sale");
			return false;
		});
		$('#transactions-btn').click(function(){
			loadDivs('transactions-payment',false);
			loadTransactions();
			return false;
		});
		$('#cash-btn').click(function(){
			loadDivs('cash-payment',true);
			return false;
		});
		$('#credit-card-btn').click(function(){
			loadDivs('credit-payment',true);
			return false;
		});
		$('#debit-card-btn').click(function(){
			loadDivs('debit-payment',true);
			return false;
		});
		$('#gift-cheque-btn').click(function(){
			loadDivs('gc-payment',true);
			$('#gc-code').blur();
			return false;
		});
		$('.amounts-btn').click(function(){
			var val = $(this).attr('val');
			var cash = $('#cash-input').val();
			if(cash == ""){
				$('#cash-input').val(val);
			}
			else{
				var tot = parseFloat(val) + parseFloat(cash);
				$('#cash-input').val(tot);
			}
			return false;
		});
		$('#cash-exact-btn,#cash-next-btn').click(function(){
			$('#cash-input').val($(this).attr('amount'));
			return false;
		});
		$('#exact-amount-btn').click(function(){
			$('#cash-input').val($(this).attr('amount'));
			return false;
		});
		$('#cash-enter-btn').click(function(){
			var amount = $('#cash-input').val().replace(/,/g ,"");

			if (! $.isNumeric(amount) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}

			var id = $('#settle').attr('sales');
			addPayment(id,amount,'cash');
			return false;
		});
		/* DEBIT PAYMENT */
		$('#debit-card-num,#debit-amt').focus(function(){
			$('#tbl-debit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#debit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#debit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#debit-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'debit');
		});
		/* End of DEBIT PAYMENT */
		/* CREDIT PAYMENT */
		$('.credit-type-btn').on('click',function(event)
		{
			event.preventDefault();
			$('#credit-type-hidden').val($(this).val());
			$('.credit-type-btn').attr('style','background-color:#2daebf !important;');
			$(this).attr('style','background-color:#007d9a !important;');
		});
		$('#credit-card-num,#credit-app-code,#credit-amt').focus(function()
		{
			$('#tbl-credit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#credit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#credit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#credit-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'credit');
		});
		/* End of CREDIT PAYMENT */
		/* GIFT CHEQUE */
		$('#gc-enter-btn').on('click',function(event)
		{
			event.preventDefault();
			var m_mode = $(this).attr('mode');
			if (m_mode == 'search') {
				var code = $('#gc-code').val();
				$.post(baseUrl + 'cashier/search_gift_card/'+code,{},function(data)
				{
					if (typeof data.error != "undefined") {
						rMsg(data.error,"error");
					} else {
						$('#hid-gc-id').val(data.gc_id);
						$('#gc-amount').val(data.amount);
						$('#gc-code').val(data.card_no);
						$('#gc-enter-btn').html("Enter");
						$('#gc-enter-btn').attr('mode','finalize');
					}
				},'json');
			} else if (m_mode == 'finalize') {
				var amount = $('#gc-amount').val();
				var id = $('#settle').attr('sales');
				addPayment(id,amount,'gc');
			}
		});
		$('#gc-code').blur(function(event)
		{
			event.preventDefault();
			$('#gc-enter-btn').html('<i class="fa fa-search fa-lg"></i> Search');
			$('#gc-enter-btn').attr('mode','search');
		});
		/* End of GIFT CHEQUE */
		$('#cancel-cash-btn,#trsansactions-close-btn,#cancel-debit-btn,#cancel-credit-btn,#cancel-gc-btn').click(function(){
			loadDivs('select-payment',false);
			return false;
		});
		$('#add-payment-btn').click(function(){
			loadDivs('select-payment',true);
			return false;
		});
		$('#print-btn').click(function(event){
			var sales_id = $(this).attr('ref');
			$.post(baseUrl+'cashier/print_sales_receipt/'+sales_id,'',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
			event.preventDefault();
		});
		function addPayment(id,amount,type){
			var formData = {};
			amount = amount.replace(/,/g ,"");
			if (type == 'credit') {
				formData = 'card_type='+$('#credit-type-hidden').val()+
						'&card_number='+$('#credit-card-num').val()+
						'&approval_code='+$('#credit-app-code').val();
			} else if (type == 'debit') {
				formData = 'card_number='+$('#debit-card-num').val();
			} else if (type == 'gc') {
				formData = 'gc_id='+$('#hid-gc-id').val()+'&gc_code='+$('#gc-code').val();
			}
			$.post(baseUrl+'cashier/add_payment/'+id+'/'+amount+'/'+type,formData,function(data){
				if(data.error == ""){
					rMsg('Success! Payment Submitted.','success');
					$('#amount-tendered-txt').number(data.tendered,2);
					$('#change-due-txt').number(data.change,2);
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#cash-input').val('');
					loadDivs('after-payment',false);
				} else {

				}
			},'json');
		}
		function deletePayment(pay_id,sales_id){
			$('#void-payment-btn-'+pay_id).click(function(){
				$.post(baseUrl+'cashier/delete_payment/'+pay_id+'/'+sales_id,function(data){
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#pay-row-div-'+pay_id).remove();
				},'json');
				return false;
			});
		}
		function loadTransactions(){
			var id = $('#settle').attr('sales');
			$.post(baseUrl+'cashier/settle_transactions/'+id,function(data){
				$('.transactions-payment-div .body').html(data.code);
				$('.transactions-payment-div .body').perfectScrollbar({suppressScrollX: true});
				$.each(data.ids,function(key,pay_id){
					deletePayment(pay_id,id);
				});
			},'json');
		}
		function loadDivs(type,check){
			var go = true
			if(check){
				go = checkBal();
			}
			if(go){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
		}
		function checkBal(){
			var bal = $('#settle').attr('balance');
			if(bal == ""){
				balance = 0;
			}
			else
				var balance = parseFloat(bal.replace(',','.').replace(' ',''));
			if(balance <= 0){
				rMsg('Error! No more to pay.','error');
				return false;
			}
			else
				return true;
		}
	<?php elseif($use_js == 'splitJs'): ?>
		var scrolled=0;
		var transScroll=0;
		$('.counter-split-right .actions-div').perfectScrollbar({suppressScrollX: true});
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadTransCart();
		$('#save-split-btn').click(function(){
			var btn = $(this);
			if(btn.attr('by') == 'select-items'){
				btn.goLoad();
				$.post(baseUrl+'cashier/save_split',function(data){
					if(data.error == "")
						window.location = baseUrl+'cashier';
					else{
						rMsg(data.error,'error');
						btn.goLoad({load:false});
					}
				},'json');
			}
			else if(btn.attr('by') == 'even-split'){
				var num = parseFloat($('#even-spit-num').text());
				btn.goLoad();
				$.post(baseUrl+'cashier/even_split/'+num,function(data){
					if(data.error == "")
						window.location = baseUrl+'cashier';
					else{
						rMsg(data.error,'error');
						btn.goLoad({load:false});
					}
				},'json');
				// });
			}
			else{
				rMsg('Select Split Action','error');
			}
			return false;
		});
		$('.split-bys').click(function(){
			var load = $(this).attr('ref');
			$('#save-split-btn').attr('by',load);
			var btn = $(this);
			clearTransSplitCart(btn);
			loadDivs(load);
			return false;
		});
		$('#add-sel-block-btn').click(function(){
			newSelBlock();
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#even-up-btn,#even-down-btn').click(function(){
			var num = parseFloat($('#even-spit-num').text());
			var go = $(this).attr('num');
			if(go == 'up'){
				num += 1;
			}
			else{
				if(num > 2){
					num -= 1;
				}
			}
			$('#even-spit-num').text(num);
			return false;
		});
		$("#refresh-btn").click(function(){
			var btn = $(this);
			clearTransSplitCart(btn);
			return false;
		});
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
		function newSelBlock(){
			if($('#hid-num').exists()){
				var num = parseFloat($('#hid-num').val());
				num += 1;
				$('#hid-num').val(num);
			}
			else{
				$('<input>').attr({'type':'hidden','id':'hid-num'}).val(0).appendTo('.select-items-div');
				var num = 0;
			}
			$.post(baseUrl+'cashier/new_split_block/'+num,function(data){
				// var num = data.num;
				$('#add-btn-div').before(data.code);
				$('.counter-split-right .actions-div').perfectScrollbar('update');
				
				addDelFunc(num);
			},'json');
			// });
		}
		function addDelFunc(num){
			$('#sel-div-'+num+' .add-btn').click(function(){
				var sel = $('.selected');
				if(sel.exists()){
					if(sel.hasClass('trans-sub-row')){
						selectModMenu();
					}
					var id = sel.attr('trans-id');
					var btn = $(this);
					addToTransSplitCart(num,id,btn);
				}
				return false;
			});
			$('#sel-div-'+num+' .del-btn').click(function(){
				var sel = $('#sel-div-'+num+' .splicted');
				if(sel.exists()){
					var id = sel.attr('trans-id');
					var btn = $(this);
					minusToTransSplitCart(num,id,btn);
				}
				return false;
			});
			$('#sel-div-'+num+' .remove-btn').click(function(){
				var sel = $('#sel-div-'+num);
				if(sel.exists()){
					var btn = $(this);
					btn.goLoad();
					$.post(baseUrl+'cashier/remove_split_block/'+num,function(data){
						sel.parent().remove();
						$.each(data.content,function(id,qty){
							$('#trans-row-'+id).show();
							$('.trans-sub-row[trans-id="'+id+'"]').show();
							$('#trans-row-'+id).find('.qty').text(qty);
							selector($('#trans-row-'+id));
							$('#even-spit-num').text('2');
						});
						btn.goLoad({load:false});
					// });
					},'json');
				}
				return false;
			});
		}
		function addToTransSplitCart(num,id,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/add_split_block/'+num+'/'+id,function(data){
			
				var sel = $('#trans-row-'+id).clone();
				if($('#trans-split-row-'+num+'-'+id).exists()){
					$('#trans-split-row-'+num+'-'+id).find('.qty').text(data.split_qty);
					splictor($('#trans-split-row-'+num+'-'+id));
				}
				else{
					var ul = $('#sel-div-'+num+' ul');
					sel.attr('id','trans-split-row-'+num+'-'+id);
					sel.attr('ref',num);
					sel.removeClass('trans-row');
					sel.removeClass('sel-row');
					sel.removeClass('selected');
					sel.addClass('trans-split-row');
					sel.addClass('split-row');
					sel.find('.qty').text(data.split_qty);
					sel.appendTo(ul).click(function(){
						splictor($(this));
						return false;
					});
					splictor($('#trans-split-row-'+num+'-'+id));
					if($('.trans-sub-row[trans-id="'+id+'"]').exists()){
						$('.trans-sub-row[trans-id="'+id+'"]').each(function(){
							var li = $(this).clone();
							li.addClass('trans-split-row-'+num+'-'+id);
							li.removeClass('trans-sub-row');
							li.removeClass('sel-row');
							li.appendTo(ul);
						});
					}
				}
				if(data.from_qty <= 0){
					$('#trans-row-'+id).hide();
					$('#trans-row-'+id).removeClass('selected');
					$('.trans-sub-row[trans-id="'+id+'"]').hide();
					$('.trans-sub-row[trans-id="'+id+'"]').removeClass('selected');
				}
				else{
					$('#trans-row-'+id).find('.qty').text(data.from_qty);
				}
				btn.goLoad({load:false});
			},'json');
			// });
		}
		function minusToTransSplitCart(num,id,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/minus_split_block/'+num+'/'+id,function(data){
				if(data.from_qty > 0){
					$('#trans-row-'+id).show();
					$('.trans-sub-row[trans-id="'+id+'"]').show();
					$('#trans-row-'+id).find('.qty').text(data.from_qty);
					selector($('#trans-row-'+id));
				}
				if(data.split_qty <= 0){
					var sel = $('#sel-div-'+num+' .splicted');
					$('.trans-split-row-'+num+'-'+id).remove();
					sel.remove();
				}
				else{
					$('#trans-split-row-'+num+'-'+id).find('.qty').text(data.split_qty);
				}
				btn.goLoad({load:false});
			},'json');
			// });
		}
		function clearTransSplitCart(btn){
			var sel = $('.sel-div');
			btn.goLoad();
			$.post(baseUrl+'cashier/clear_split',function(data){
				sel.parent().remove();
				$('#hid-num').remove();
				$.each(data.content,function(id,qty){
					$('#trans-row-'+id).show();
					$('.trans-sub-row[trans-id="'+id+'"]').show();
					$('#trans-row-'+id).find('.qty').text(qty);
					selector($('#trans-row-'+id));
				});
				btn.goLoad({load:false});
			},'json');
		}
		function addTransCart(menu_id,opt){

			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det){
			var formData = 'trans_id='+trans_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg('Modifier is Already Added!','error');
				}
				else{
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				btn.prop('disabled', false);
				transTotal();
			},'json');
		}
		function makeItemRow(id,menu_id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));

					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det){
			
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row'})
								   .click(function(){
										selector($(this));

										return false;
									});

			$('<span/>').attr('class','name').css('margin-left','26px').text(opt.name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').text(opt.cost).appendTo(subRow);
			$('.selected').after(subRow);
			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function splictor(li){
			$('.split-row').removeClass('splicted');
			li.addClass('splicted');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var net = data.total;
				var discount = data.discount;
				var total = net + data.discount;

				if(total >= minimum)
				{
					$('#credit-btn, #cash-btn').removeAttr('disabled');
				}else{
					$('#credit-btn, #cash-btn').attr('disabled', 'disable');
				}

				$("#total-txt").number(total,2).prepend('₱ ');
				$("#discount-txt").number(discount,2).prepend('₱ ');
				$("#net-discount-txt").number(net,2).prepend('₱ ');
			},'json');
		}

		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt);
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt);
							});
						}
						ctr++;
					});
				}
				transTotal();
			},'json');
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				// alert(data)
			// },'json');
			});
		}
	<?php elseif($use_js == 'combineJs'): ?>
		var scrolled=0;
		var transScroll=0;
		$('.counter-split-right .actions-div').perfectScrollbar({suppressScrollX: true});
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		$('.orders-list-combine').perfectScrollbar({suppressScrollX: true});
		$('.orders-to-combine').perfectScrollbar({suppressScrollX: true});
		loadTransCart();
		loadMenuCategories();
		$('#combine-btn').click(function(){
			$(this).goLoad();
			$.post(baseUrl+'cashier/save_combine',function(data){
				window.location = baseUrl+'cashier';
			});
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#clear-btn').click(function(){
			$('.combine-row').remove();
			$.post(baseUrl+'wagon/clear_wagon/trans_combine_cart/',function(data){
				$('#refresh-btn').trigger('click');
			});
			return false;
		});
		$('#refresh-btn').click(function(){
			var terminal = $('.orders-list-combine').attr('terminal');
			var types = $('.orders-list-combine').attr('types');
			loadOrders(terminal,types);
			return false;
		});
		$('.my-all-btns').click(function(){
			var terminal = $(this).attr('ref');
			var types = $('.orders-list-combine').attr('types');
			$('.orders-list-combine').attr('terminal',terminal);
			loadOrders(terminal,types);
			return false;
		});
		function loadMenuCategories(){
				var data = {
					"All TYPES": {"id":"all"},
					"DINE IN": {"id":"dinein"},
					"DELIVERY": {"id":"delivery"},
					"COUNTER": {"id":"counter"},
					"RETAIL": {"id":"retail"},
					"PICKUP": {"id":"pickup"},
					"TAKEOUT": {"id":"takeout"},
					"DRIVE-THRU": {"id":"drivethru"},
				}
				var ctr = 1;
				$.each(data,function(txt,opt){
		 			$('<button/>')
		 			.attr({'id':opt.id+'-btn','ref':opt.id,'class':'types-btns btn-block category-btns counter-btn-teal double btn btn-default'})
		 			.text(txt)
		 			.appendTo('.type-container')
		 			.click(function(){
		 				var terminal = $('.orders-list-combine').attr('terminal');
		 				loadOrders(terminal,opt.id);
						$('.orders-list-combine').attr('types',opt.id);
		 				loadOrders(terminal,opt.id);		 				return false;
		 			});
					if(ctr == 1){
						$('#'+opt.id+'-btn').trigger('click');
					}
					ctr++;
				});
		}
		function loadOrders(terminal,types){
			$('.orders-list-combine').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			$.post(baseUrl+'cashier/orders/'+terminal+'/open/'+types+'/null/combineList',function(data){
				$('.orders-list-combine').html(data.code);
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						addDelFunc(id,val);
					});
					$('.orders-list-combine').perfectScrollbar('update');
				}
			},'json');
		}
		function addDelFunc(id,val){
			$('#add-to-btn-'+id).click(function(){
				var btn = $(this);
				var formData = 'sales_id='+id+'&balance='+val.amount;
				var clone = $('#order-btnish-'+id).clone();
				var orig = $('#order-btnish-'+id).clone();
				btn.goLoad();
				$.post(baseUrl+'wagon/add_to_wagon/trans_combine_cart',formData,function(data){
					var com_id = data.id;
					var btn = $('<button/>')
								.html('<i class="fa fa-times fa-lg fa-fw"></i>')
								.attr({'id':'remove-combine-btn-'+id,'ref':id,'class':'btn-block counter-btn-red'})
								.click(function(){

									var rBtn = $(this);
									rBtn.goLoad();
									$.post(baseUrl+'wagon/delete_to_wagon/trans_combine_cart/'+com_id,function(data){
										$('.orders-list-combine .orders-list-div-btnish:first-child').before(orig);
										$('#combine-row-'+com_id).remove();
										addDelFunc(id,val);
										$('.orders-list-combine').perfectScrollbar('update');
										rBtn.goLoad({load:false});
									},'json');
									return false;
								});
					clone
					.attr('id','combine-row-'+com_id)
					.addClass('combine-row')
					.find('.add-btn-row').remove();
					clone.find('.order-btn-right-container').append(btn);
					clone.appendTo('.orders-to-combine');
					$('#order-btnish-'+id).remove();
					$('.orders-to-combine.orders-to-combine').perfectScrollbar('update');
					btn.goLoad({load:false});
				},'json');
				return false;
			});
		}
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
		function addTransCart(menu_id,opt){
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det){
			var formData = 'trans_id='+trans_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg('Modifier is Already Added!','error');
				}
				else{
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				btn.prop('disabled', false);
				transTotal();
			},'json');
		}
		function makeItemRow(id,menu_id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));

					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row'})
								   .click(function(){
										selector($(this));

										return false;
									});
			$('<span/>').attr('class','name').css('margin-left','26px').text(opt.name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').text(opt.cost).appendTo(subRow);
			$('.selected').after(subRow);
			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var net = data.total;
				var discount = data.discount;
				var total = net + data.discount;

				if(total >= minimum)
				{
					$('#credit-btn, #cash-btn').removeAttr('disabled');
				}else{
					$('#credit-btn, #cash-btn').attr('disabled', 'disable');
				}

				$("#total-txt").number(total,2).prepend('₱ ');
				$("#discount-txt").number(discount,2).prepend('₱ ');
				$("#net-discount-txt").number(net,2).prepend('₱ ');
			},'json');
		}
		
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt);
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt);
							});
						}
						ctr++;
					});
				}
				transTotal();
			},'json');
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				// alert(data)
			// },'json');
			});
		}
	<?php elseif($use_js == 'tablesJs'): ?>
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#back-btn,#back-occ-btn').click(function(){
			loadDivs('select-table');
			return false;
		});
		$('#guest-enter-btn').click(function(){
			var tbl = $('#select-table').attr('ref');
			var guest = $('#guest-input').val();
			var formData = 'type=dinein&table='+tbl+'&guest='+guest;
			$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
				window.location = baseUrl+'cashier/counter/dinein';
			},'json');
			return false;
		});
		$('#start-new-btn').click(function(){
			loadDivs('no-guest');
			return false;
		});
		$.post(baseUrl+'cashier/get_branch_details',function(data){
			var img = data.layout;
			if(img != "" ){
				var img_real_width=0,
				    img_real_height=0;
				$("<img/>")
				.attr("src", img)
			    .attr("id", "image-layout")
			    .load(function(){
		           img_real_width = this.width;
		           img_real_height = this.height;
		           $(this).appendTo('#image-con');
		           $("<div/>")
				    .attr("class", "rtag")
				    .attr("id", "rtag-div")
				    .css("height", img_real_height)
				    .css("width", img_real_width)
				    .appendTo('#image-con');
					loadMarks();

				});
			}
		},'json');
		function loadMarks(){
			$.post(baseUrl+'cashier/get_tables',function(data){
				$.each(data,function(tbl_id,val){
					$('<a/>')
	    			.attr('href','#')
	    			// .attr('class','marker-red')
	    			.attr('class','marker-'+val.stat)
	    			.attr('id','mark-'+tbl_id)
	    			.css('top',val.top+'px')
	    			.css('left',val.left+'px')
	    			.appendTo('#rtag-div')
	    			.click(function(e){
	    				if(val.stat == 'red'){
	    					$("#occ-num").text(val.name);
	    					$('#select-table').attr('ref',tbl_id);
	    					loadDivs('occupied');
	    					get_table_orders(tbl_id);
	    				}
	    				else{
	    					$('#select-table').attr('ref',tbl_id);
	    					loadDivs('no-guest');
	    				}
	    				return false;
    				});
				});

			},'json');
		}
		function get_table_orders(tbl_id){
			$('.occ-orders-div').html('<br><center><i class="fa fa-spinner fa-spin fa-2x"></i></center>');
			$.post(baseUrl+'cashier/get_table_orders/true/'+tbl_id,function(data){
				$('.occ-orders-div').html(data.code);
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						$('#order-btn-'+id).click(function(){
							window.location = baseUrl+'cashier/counter/dinein/null/'+id;
							return false;
						});
					});
				}
			},'json');
		}
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
	<?php elseif($use_js == 'deliveryJs'): ?>

		$(document).on('click','.list-phone-div .grp-list', function(event){
			var btn = $(this);
			$('.list-phone-div .grp-list').find('span').html('').removeAttr('class');
			btn.find('span').append('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp; Default').attr('class','label label-primary').css({'float':'right'});
			var text = btn.html();
			var r = /<(\w+)[^>]*>.*<\/\1>/gi;
			text = text.replace(r,"");


			$.post(baseUrl+'wagon/add_to_wagon/default_no',formData='phone_no='+text, function(){
				rMsg('Default number has been saved.','success');
			},'json');
		});

		$(document).on('click', '#add-new-phone', function(event){ 
			var phone = $('#phone_no').val();

			if(phone != '')
			{
				$('#add-grp-list-div').find('.grp-no-list').remove();
				$.post(baseUrl+'wagon/add_to_wagon/phone_nos',formData='phone_no='+phone, function(){
					$('<a/>').html(phone + '<span/>')
							.attr('class','rp-btn grp-list grp-btn grp-list  list-group-item')
							.appendTo('#add-grp-list-div');
					$('#phone_no').val('');
				},'json');

			}else{
				rMsg('Please enter phone number','error');
				$('#phone_no').focus();
			}
		});

		$('#add-new-address').rPopFormFile({
			asJson	  : true,
			onComplete: function(data){

				$('#address-tbl').find('.no-rows').remove();
				$('#address-tbl').append(data.code);	
					
				var formData = 'street_no='+data.items.street_no
								+'&landmark='+data.items.landmark
								+'&city='+data.items.city
								+'&street_address='+data.items.street_address
								+'&region='+data.items.region
								+'&zip='+data.items.zip;

				$.post(baseUrl+'wagon/add_to_wagon/cust_address',formData);

				rMsg('Customer\'s address has been added.','success');
				$('[data-bb-handler=cancel]').click();
			}	
		});

		$('.listings').perfectScrollbar({suppressScrollX: true});
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		/*
		$('#search-customer,.key-ins')
			.keyboard({
				alwaysOpen: true,
				usePreview: false,
				autoAccept : true
			})
			.addNavigation({
				position   : [0,0],
				toggleMode : false,
				focusClass : 'hasFocus'
			});
		*/
		$('#search-customer').on('keyup',function(e){	
			
			code = (e.keyCode || e.which);
			if(code === 16)
				return;

			var txt = $(this).val();
			var ul = $('#cust-search-list');
			ul.find('li').remove();
			ul.goLoad();
			$.post(baseUrl+'cashier/search_customers/'+txt,function(data){
				if(!$.isEmptyObject(data)){
					$.each(data,function(cust_id,val){
						var li = $('<li/>')
									.attr({'class':'cust-row','id':'cust-row-'+cust_id})
									.css({'cursor':'pointer','border-bottom':'1px solid #ddd'})
									.click(function(event){
										event.preventDefault();	
										$.post(baseUrl+'cashier/get_customer_details/'+cust_id,function(cust){
											$('#continue-btn').attr('disabled','disabled');
											$('#address-tbl').find('.no-rows').remove();
											$('#address-tbl .t-rows').remove();
											$('#address-tbl').append(cust.code);
											$('#list-phone').html(cust.contacts);
											$('#cust_id').val(cust_id);
											$.each(cust.cust,function(id,col){
												$('#'+id).val(col);
											});
										},'json');
									});
						var vip_label = '';

						if(val.is_vip == 1)
							vip_label = '<span class="label label-danger" style="float:right;">VIP</span>';
						
						$('<h4/>').html(val.name + vip_label).appendTo(li);
						$('<h5/>').html(val.phone).appendTo(li);
						li.appendTo(ul);
					});
					$('.listings').perfectScrollbar('update');
				}
				ul.goLoad({load:false});
			},'json');
			// });
		});
		$('#continue-btn').click(function(){
			var btn = $(this);
			$('#customer-form').rOkay({
				btn_load 	: 	$('#continue-btn'),
				bnt_load_remove : 	false,
				asJson 		: 	true,
				onComplete 	: 	function(data){

					var id = data.id;

					var type = $('#trans_type').val(), addr_id = $('#cust_addr_id').val();
					var formData = 'type='+type+'&customer_id='+id;
					
					if(addr_id != "") 
					{
						formData += '&address_id='+addr_id;
						//------------
						localStorage.setItem("trans_type_cart_formdata",  formData);
						
						$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){

							window.location = baseUrl+'cashier/branch_zoning/'+id;
						},'json');


					}else{
						var address = JSON.parse(localStorage['selected_address']);
						$.post(baseUrl+'cashier/get_cust_addr_db/'+id,{'address[]' : address},function(data){
							formData += '&address_id='+data.id;
 							$('#cust_addr_id').val(data.id);
 							
							//------------
						
							localStorage.setItem("trans_type_cart_formdata",  formData);
							$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
								window.location = baseUrl+'cashier/branch_zoning/'+id;
							},'json');

						},'json');					
					}

					rMsg(data.msg, 'success');
						

				}
			});	
			
			return false;
		});

		$(document).on('click', '.t-rows', function(event){ 
			event.preventDefault();

			var id = '', tbl = $(this).closest('table').attr('id');
			$('#'+tbl+' .t-rows').removeClass('active');
			var tbl_tr = $('#address-tbl .active');
			
			$(this).addClass('active');

			if(tbl == 'address-tbl')
			{
				id = $('#address-tbl .active').attr('ref');
				
				if(typeof id == "undefined") 
				{
					var arr_formData = [];
					$('#address-tbl .active td').each(function() {
					   arr_formData.push($(this).html());
					});
					localStorage.setItem("selected_address",  JSON.stringify(arr_formData));
					$('#cust_addr_id').val('');

				}else{
					$('#cust_addr_id').val(id);
				}

				$('#continue-btn').removeAttr('disabled');
				
			}
		});

		$('#clear-btn').click(function(){
			$('.cust-form').find("input[type=text],input[type=hidden]").val("");
			$('#add-grp-list-div').find('a').remove();

			$('<a/>').html('No phone number.')
					.attr('class', 'grp-btn grp-no-list grp-btn grp-no-list  list-group-item ')
					.appendTo('#add-grp-list-div');

			$('#address-tbl').find('.t-rows').remove();
			$('#address-tbl .no-rows').remove();

			$('<tr/>').html('<td colspan="6">No available address.</td>')
						.attr('class', 'no-rows')
						.appendTo('#address-tbl');

			return false;
		});

	<?php elseif($use_js == 'branchZoningJs'):?>

		$('#back-btn').click(function(){
			var id = $('#customer_id').val();
			window.location = baseUrl+'cashier/delivery/'+id+'/false';
			return false;
		});

		$('#cancel-btn').click(function(){
			// $(this).goLoad();
			var id = $('#customer_id').val();

			bootbox.dialog({
				  message: baseUrl +'cashier/save_cust_address',
				  title: "",
				  buttons: {
				   success: {
				      label: "Okay",
				      className: "btn-success",
				      callback: function() {
				       $.post(baseUrl+'cashier/add_reference_address/'+id, function(data){
				       		if(data.id)
				       		{
				       			rMsg('Address has been saved for reference.','success');
				       			window.location = baseUrl+'cashier/';
				       		}
				       },'json');
				      }
				    },
				    danger: {
				      label: "Cancel",
				      className: "btn-danger",
				      callback: function() {
				       
				      }
				    }
				  }
			});
			// $(this).goLoad({load:false});
		});

		$('.list-branch').click(function(event){
				var branch_id = $(this).attr('ref'),
				id = $(this).attr('data-cust-id');

			//window.location.href = baseUrl+'cashier/branch_zoning/'+id+'/'+branch_id;

				$.post(baseUrl+'cashier/get_branch_db/'+branch_id,function(branch){
					$.each(branch,function(id,col){
						$.each(col, function(field,val) {
							$('#'+field).val(val);
						});
					});

				},'json');
			$('#travel_time').val($(this).find('h5[data_travel_time]').attr('data_travel_time'));
		});

		$('.list-branch').first().click();


		$('#clear-btn').click(function(){
			var id = $('#customer_id').val();
			window.location = baseUrl+'cashier/branch_zoning/'+id;
			return false;
		});
		$('#continue-btn').click(function(){
			var branch_id=$('#branch_id').val(),travel_time=$('#travel_time').val(), id = $('#customer_id').val(), type = 'delivery', formData = 'type='+type+'&customer_id='+id+'&branch_id='+branch_id+'&travel_time='+travel_time;
			if(branch_id){
				formData = localStorage["trans_type_cart_formdata"] + '&branch_id='+branch_id+'&travel_time='+travel_time;
				$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
					// console.log(data);
					window.location = baseUrl+'cashier/counter/'+type+'/'+branch_id;
				},'json');
			}else{
				rMsg('Please select a branch.','error');
			}
			return false;

		});
	<?php elseif($use_js == 'paymentJs'):?>

		$('#cash-input').attr('placeholder', 'Enter Amount');
		var hashTag = window.location.hash;
		
		if(hashTag == '#cash'){
			loadDivs('cash-payment',true);
		} else if(hashTag == '#credit'){
			var id = $('#settle').attr('sales');
			setPayment(id,null,'credit');
			
		} else if(hashTag == '#debit'){
			loadDivs('debit-payment',true);
		} else if(hashTag == '#gc'){
			loadDivs('gc-payment',true);
		}

		(function setup_notification(){
    		$.post(baseUrl + 'cashier/set_notification', function(data){
    			$('span.badge-cancel').html(data.count_cancelled);
			    $('span.badge-pending').html(data.count_unprocessed);
    		},'json');
    		setTimeout(setup_notification, 1000)
		})();

		$('.order-view-list .body').perfectScrollbar({suppressScrollX: true});
		$('.order-view-list .footer').perfectScrollbar({suppressScrollX: true});
		$('#finished-btn').click(function(){
			if($('#settle').attr('type') == 'dinein')
				window.location = baseUrl+'cashier/tables/';
			else if($('#settle').attr('type') == 'delivery')
			{
				var sales_id = $('#settle').attr('sales')
				$.post(baseUrl+'cashier/finish_trans_del/'+sales_id,function(data){
					window.location = baseUrl+'cashier';
				},'json');
			}else if($('#settle').attr('type') == 'pickup')
				window.location = baseUrl+'cashier/pickup/';
			else
				window.location = baseUrl+'cashier/counter/'+$('#settle').attr('type');
			return false;
		});
		$('#cancel-btn').click(function(){
			if($('#settle').attr('type') == 'dinein')
				window.location = baseUrl+'cashier/tables/';
			else if($('#settle').attr('type') == 'delivery')
				window.location = baseUrl+'cashier/delivery/';
			else if($('#settle').attr('type') == 'pickup')
				window.location = baseUrl+'cashier/pickup/';
			else
				window.location = baseUrl+'cashier/counter/'+$('#settle').attr('type');
			return false;
		});
		$('#recall-btn').click(function(){
			window.location = baseUrl+'cashier/counter/'+$(this).attr('type')+'/null/'+$(this).attr("sale");
			return false;
		});
		$('#transactions-btn').click(function(){
			loadDivs('transactions-payment',false);
			loadTransactions();
			return false;
		});
		$('#cash-btn').click(function(){
			loadDivs('cash-payment',true);
			return false;
		});
		$('#credit-card-btn').click(function(){
			var id = $('#settle').attr('sales');
			setPayment(id,null,'credit');
			//loadDivs('credit-payment',true);
			return false;
		});
		$('#debit-card-btn').click(function(){
			var id = $('#settle').attr('sales');
			setPayment(id,null,'debit');
			//loadDivs('debit-payment',true);
			return false;
		});
		$('#gift-cheque-btn').click(function(){
			loadDivs('gc-payment',true);
			$('#gc-code').blur();
			return false;
		});
		$('.amounts-btn').click(function(){
			var val = $(this).attr('val');
			var cash = $('#cash-input').val();
			if(cash == ""){
				$('#cash-input').val(val);
			}
			else{
				var tot = parseFloat(val) + parseFloat(cash);
				$('#cash-input').val(tot);
			}
			return false;
		});
		$('#cash-exact-btn,#cash-next-btn').click(function(){
			$('#cash-input').val($(this).attr('amount'));
			return false;
		});
		$('#exact-amount-btn').click(function(){
			$('#cash-input').val($(this).attr('amount'));
			return false;
		});
		$('#cash-enter-btn').click(function(){
			var amount = $('#cash-input').val().replace(/,/g ,"");

			if (! $.isNumeric(amount) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}

			var id = $('#settle').attr('sales');
			setPayment(id,amount,'cash');
			return false;
		});
		/* DEBIT PAYMENT */
		$('#debit-card-num,#debit-amt').focus(function(){
			$('#tbl-debit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#debit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#debit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#debit-amt').val();
			var id = $('#settle').attr('sales');
			setPayment(id,amount,'debit');
		});
		/* End of DEBIT PAYMENT */
		/* CREDIT PAYMENT */
		$('.credit-type-btn').on('click',function(event)
		{
			event.preventDefault();
			$('#credit-type-hidden').val($(this).val());
			$('.credit-type-btn').attr('style','background-color:#2daebf !important;');
			$(this).attr('style','background-color:#007d9a !important;');
		});
		$('#credit-card-num,#credit-app-code,#credit-amt').focus(function()
		{
			$('#tbl-credit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#credit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#credit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#credit-amt').val();
			var id = $('#settle').attr('sales');
			setPayment(id,amount,'credit');
		});
		/* End of CREDIT PAYMENT */
		/* GIFT CHEQUE */
		$('#gc-enter-btn').on('click',function(event)
		{
			event.preventDefault();
			var m_mode = $(this).attr('mode');
			if (m_mode == 'search') {
				var code = $('#gc-code').val();
				$.post(baseUrl + 'cashier/search_gift_card/'+code,{},function(data)
				{
					if (typeof data.error != "undefined") {
						rMsg(data.error,"error");
					} else {
						$('#hid-gc-id').val(data.gc_id);
						$('#gc-amount').val(data.amount);
						$('#gc-code').val(data.card_no);
						$('#gc-enter-btn').html("Enter");
						$('#gc-enter-btn').attr('mode','finalize');
					}
				},'json');
			} else if (m_mode == 'finalize') {
				var amount = $('#gc-amount').val();
				var id = $('#settle').attr('sales');
				setPayment(id,amount,'gc');
			}
		});
		$('#gc-code').blur(function(event)
		{
			event.preventDefault();
			$('#gc-enter-btn').html('<i class="fa fa-search fa-lg"></i> Search');
			$('#gc-enter-btn').attr('mode','search');
		});
		/* End of GIFT CHEQUE */
		$('#cancel-cash-btn,#trsansactions-close-btn,#cancel-debit-btn,#cancel-credit-btn,#cancel-gc-btn').click(function(){
			loadDivs('select-payment',false);
			return false;
		});
		$('#add-payment-btn').click(function(){
			loadDivs('select-payment',true);
			return false;
		});
		$('#print-btn').click(function(event){
			var sales_id = $(this).attr('ref');
			$.post(baseUrl+'cashier/print_sales_receipt/'+sales_id,'',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
			event.preventDefault();
		});
		function setPayment(id, amount, type){
			var formData = {};
			if(amount)
				amount = amount.replace(/,/g ,"");

			if (type == 'credit') {
				// formData = 'card_type='+$('#credit-type-hidden').val()+
				// 		'&card_number='+$('#credit-card-num').val()+
				// 		'&approval_code='+$('#credit-app-code').val();
			} else if (type == 'debit') {
				formData = 'card_number='+$('#debit-card-num').val();
			} else if (type == 'gc') {
				formData = 'gc_id='+$('#hid-gc-id').val()+'&gc_code='+$('#gc-code').val();
			}

			$.post(baseUrl+'cashier/set_payment/'+id+'/'+amount+'/'+type,formData,function(data){
				var text_type = (type == 'cash') ? 'Cash' : 'Credit Card';
				text_type = (type == 'debit') ? 'Debit Card' : text_type;
				
				if(type == 'cash')
				{
					$('.tendered, .changed').show();	
				}else{
					$('.tendered, .changed').hide();	
				}
				if(data.error == ""){
					

					rMsg('Payment method submitted.','success');
					$('#payment-method-txt').text(text_type);
					$('#delivery-code-txt').text(data.delivery_code);
					$('#amount-tendered-txt').number(data.tendered,2);
					$('#change-due-txt').number(data.change,2);
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#cash-input').val('');
					loadDivs('after-payment',false);
				}
			},'json');
			// });                                                                                                                                                                                                                                       
		}
		function addPayment(id,amount,type){
			var formData = {};
			amount = amount.replace(/,/g ,"");
			if (type == 'credit') {
				formData = 'card_type='+$('#credit-type-hidden').val()+
						'&card_number='+$('#credit-card-num').val()+
						'&approval_code='+$('#credit-app-code').val();
			} else if (type == 'debit') {
				formData = 'card_number='+$('#debit-card-num').val();
			} else if (type == 'gc') {
				formData = 'gc_id='+$('#hid-gc-id').val()+'&gc_code='+$('#gc-code').val();
			}
			$.post(baseUrl+'cashier/add_payment/'+id+'/'+amount+'/'+type,formData,function(data){
				if(data.error == ""){
					rMsg('Success! Payment Submitted.','success');
					$('#amount-tendered-txt').number(data.tendered,2);
					$('#change-due-txt').number(data.change,2);
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#cash-input').val('');
					loadDivs('after-payment',false);
				}
			},'json');
		}
		function deletePayment(pay_id,sales_id){
			$('#void-payment-btn-'+pay_id).click(function(){
				$.post(baseUrl+'cashier/delete_payment/'+pay_id+'/'+sales_id,function(data){
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#pay-row-div-'+pay_id).remove();
				},'json');
				return false;
			});
		}
		function loadTransactions(){
			var id = $('#settle').attr('sales');
			$.post(baseUrl+'cashier/settle_transactions/'+id,function(data){
				$('.transactions-payment-div .body').html(data.code);
				$('.transactions-payment-div .body').perfectScrollbar({suppressScrollX: true});
				$.each(data.ids,function(key,pay_id){
					deletePayment(pay_id,id);
				});
			},'json');
		}
		function loadDivs(type,check){
			// var go = true
			// if(check){
			// 	go = checkBal();
			// }
			// if(go){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			// }
		}
		function checkBal(){
			var bal = $('#settle').attr('balance');
			if(bal == ""){
				balance = 0;
			}
			else
				var balance = parseFloat(bal.replace(',','.').replace(' ',''));
			if(balance <= 0){
				rMsg('Error! No more to pay.','error');
				return false;
			}
			else
				return true;
		}
	<?php elseif($use_js == 'order_date_js'):?>
		$(".timepicker").timepicker({
			    showInputs: false
		});

	<?php endif; ?>
});
</script>