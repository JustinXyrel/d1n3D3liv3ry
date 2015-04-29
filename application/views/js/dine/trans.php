<script>
$(document).ready(function(){

	<?php if($use_js == 'transLoadJS'): ?>

		$("#wizard_step").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            stepsOrientation: "horizontal",
            enableAllSteps: true,
            onStepChanging: function (event, currentIndex, newIndex)
		    {
		    	if(currentIndex > newIndex)
		    		return true;

		    	switch(newIndex) {
				    case 1:
				    	var req1 = ["#cust_id","#cust_addr_id"];
				        if(before_order_details(req1)===true)	return true;
		    			else{
		    				rMsg('Please provide customer details.','error');
		    				return false;
		    			}
				        break;
				    case 2:
			       		var req3 = ["#branch_id"];
				        if(before_order_details(req3)===true)	return true;
				    	else{
		    				rMsg('Please select a branch.','error');
		    				return false;
				        }
				        break;
				    case 3:
				   		var req2 = $('#menu-items-tbl tr').length;
				        if(req2>1)	return true;
		    			else{
		    				rMsg('Please select menu item/s.','error');
		    				return false;
				        }
				        break;
			       
				    default:
				        return true;
				}
		    },
		    onStepChanged: function (event, currentIndex, priorIndex)
		    {
		    	var id = $('#branch_id').val(),
			    	addr_id = $('#cust_addr_id').val(),
					cust_id = $('#cust_id').val();

				switch(currentIndex) 
				{
				    case 1:
						if($('#branch-list-tbl tr').length == 1)
							get_list_branches();
		    			break;
		    		case 3:
						var formData = 'branch_id='+id+'&addr_id='+addr_id+'&cust_id='+cust_id;
						$.post(baseUrl+'wagon/add_to_wagon/trans_header/cust_id',formData);
						
						$.post(baseUrl+'trans/get_order_details',formData,function(data){
							$('#order_summary').html(data.code);
						},'json');
						break;
					default:
						break;
		    	}
		    }, 
		    onFinishing: function (event, currentIndex)
		    {
	    		$.post(baseUrl + 'trans/submit_trans_db',null,function(data){
		    		console.log(data);
		    		if (data.error) {
		    			rMsg(data.error, 'error');
		    			return false;
		    		}else{
		    			bootbox.dialog({
						  message: baseUrl +'trans/pop_display_code/'+data.uniq_code,
						  title: "Delivery Code",
						  buttons: {
						   main: {
						      label: "Done",
						      className: "btn-primary",
						      callback: function() {
						       	location.reload();
						      }
						    }
						  }
						});
					}
		    		},'json');
			  	return true;
		    },
		    onFinished: function (event, currentIndex)
		    {
		        rMsg('Transaction submitted.', 'success');
		    }
		});

		$('#wizard_step > div.actions').attr('id','pagination_btns');
		$('#wizard_step > div.actions').clone().insertBefore('#wizard_step > div.steps').attr('id','clone_btns');

		$("#clone_btns [role=menuitem]:contains('Next')").click(function(){
		    $("#pagination_btns [role=menuitem]:contains('Next')").click();
		});
		$("#clone_btns [role=menuitem]:contains('Previous')").click(function(){
		    $("#pagination_btns [role=menuitem]:contains('Previous')").click();
		});
		$("#clone_btns [role=menuitem]:contains('Finish')").click(function(){
		    $("#pagination_btns [role=menuitem]:contains('Finish')").click();
		});

		function get_list_branches()
		{
			var id = $('#cust_addr_id').val(), 
					$row = '#row-'+id, 
					address_str = '';
					$($row+' td').each(function(){
					  address_str += $(this).text() + " ";
					});

			var formData = 'addr_arr='+address_str+'&addr_id='+id;
				$.post(baseUrl+'trans/branches_db',formData,function(data){
					if(!data.under_vicinity)
					{
						bootbox.alert('trans/trans_message');
					}
					$('#branch-list-tbl .t-rows').remove();
					$('#branch-list-tbl').append(data.code);
				},'json');
		}

		$('#clear-search').click(function(event){
			event.preventDefault();
			$('#branch-search').val('');
			get_list_branches();
		});
		$('#branch-search').typeaheadmap({
			"source": function(search, process) {
				var url = $('#branch-search').attr('search-url');
				$.post(baseUrl+url,'search='+search,function(data){
					process(data);
				},'json');
			},
		    "key": "key",
		    "value": "value",
		    "listener": function(k, v) {
				$.post(baseUrl+'trans/get_found_branch','id='+v,function(data){
					$('#branch-list-tbl .t-rows').remove();
					$('#branch-list-tbl').append(data.code);
					$('#branch_id').val('');
				},'json');
			}
		});    
        $('#customer-search').typeaheadmap({
			"source": function(search, process) {
				var url = $('#customer-search').attr('search-url');
				$.post(baseUrl+url,'search='+search,function(data){
					process(data);
				},'json');
			},
		    "key": "key",
		    "value": "value",
		    "listener": function(k, v) {
				$('#cust_id').val(v);
				$('#add-new-address').attr("rata-pass","trans/pop_new_address_db?cust_id="+v);
				$('#add-new-address').removeAttr('disabled');
				get_customer_details(v);
			}
		});    
		// $('#add-new-address').rPopFormFile({
		// 	asJson	  : true,
		// 	onComplete: function(data){
		// 		$('#address-tbl').append(data.row);
		// 		rMsg(data.msg,'success');
		// 		$('[data-bb-handler=cancel]').click();
		// 	}	
		// });
		$('#add-new-address').rPopFormFile({
			asJson	  : true,
			onComplete: function(data){
				var formData = 'base_location='+data.items.base_location+'&city='+data.items.city+'&street_address='+data.items.street_address+'&city='+data.items.street_no+'&region='+data.items.street_no+'&zip='+data.items.zip;
				$.post(baseUrl+'wagon/add_to_wagon/cust_address',formData);

				$('#address-tbl').append(data.row);
				rMsg(data.msg,'success');
				$('[data-bb-handler=cancel]').click();
			}	
		});
		$('#save-new-cust-btn').click(function(event){
			event.preventDefault();
			$("#new_customer_form").rOkay({
				btn_load		: 	$('#save-new-cust-btn'),
				btn_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){

										$('#cust_id').val(data.id);
										// $('#add-new-address').attr("rata-pass","trans/pop_new_address_db?cust_id="+data.id);
										$('#add-new-address').removeAttr('disabled');
										
										if(data.id == '')
											rMsg(data.msg,'error');
										else
											rMsg(data.msg,'success');
									}
			});
			return false;
		});
		$(document).on('click', '.t-rows', function(event) { 
			event.preventDefault();
			var tbl = $(this).closest('table').attr('id');
			$('#'+tbl+' .t-rows').removeClass('active');
			$(this).addClass('active');

			if(tbl == 'address-tbl')
				$('#cust_addr_id').val($(this).attr('ref'));
			if(tbl == 'branch-list-tbl')
				$('#branch_id').val($(this).attr('ref'));
		});
		function get_customer_details(v)
		{
			var formData = 'cust_id='+v;
			$.post(baseUrl+'trans/get_customer_details_db',formData,function(data){
				if(data != "")
				{
					$("#fname").val(data.fname);
					$("#mname").val(data.mname);
					$("#lname").val(data.lname);
					$("#suffix").val(data.suffix);
					$("#email").val(data.email);
					$("#phone").val(data.phone);
					$("#cust_id").val(data.cust_id);
				}
			},'json');

			$.post(baseUrl+'trans/get_customer_address',formData,function(data){
				$('#address-tbl .t-rows').remove();
				$('#address-tbl').append(data.code);
			},'json');
		}
		function before_order_details(req){
			var okay = true;
			$.each(req, function(k, v){
				if($(v).val() == '')
				{
					 okay = false;
					 return okay;
				}
			});
			return okay;
		}
		function compute_menu_item(el, qty, cost)
		{
			var sum_amount = 0;
			$(el).each(function(key, val){
				sum_amount += parseInt($(this).text());
			});
			var sum_cost =(parseInt(cost)+parseInt(sum_amount))*parseInt(qty);
			return sum_cost;
		}
		$('#add-menu-item').click(function(event)
		{
			event.preventDefault();


			var url = 'wagon/get_wagon/ord_cart', formData;		
				$.post(baseUrl+url,formData,function(data){
					var line = parseInt((data.items).length) || 0;
					$('#line_id').val(line);
				},'json');

			var url = 'wagon/add_to_wagon/ord_cart', 
				name = $('#menu-drop').find('option:selected').text(),
				qty = $('#qty').val(),
				cost = $('#menu-drop').find('option:selected').attr('cost'),
				sched_id = $('#menu-drop').find('option:selected').attr('sched'),
				sum_cost = (cost*qty).toFixed(2),
 				id = $('#menu-drop').val(),
 				line_id = $('#line_id').val();


 				if(line_id == '') 
 					line_id = 0;
 				else if(line_id == 0)
 					line_id=1;
 				else
 					line_id++;

 				formData = 'line_id='+line_id+'&menu_id='+ id+'&name='+name+'&cost='+sum_cost+'&qty='+qty;
				
 			$.post(baseUrl+'trans/check_menu_schedule/'+sched_id,formData,function(data)
 			{
 				console.log(data);
 				if(data.msg != '')
 					rMsg(data.msg,'error');
 				else{
	 				if(id != '' && qty != '' && qty > 0  && $.isNumeric(qty))
						$.post(baseUrl+url,formData,function(data){

							var tbl = $('#menu-items-tbl'), items = data.items;
							
							$('#curr_mod_hid_item').val('#ord-row-'+data.id);
							$('<tr/>')
								.attr({'id':'ord-row-'+data.id,'class':'ord-row', 'ref':data.id , 'data-menu-id':items.menu_id})
								.appendTo(tbl);
							
							$('<td/>').html('<span id="qty">'+items.qty+'</span>')
								.appendTo('#ord-row-'+data.id);
							
							$('<td/>').html('<span>'+items.name+'</span><table id="mod-items" class="table"></table>')
								.appendTo('#ord-row-'+data.id);
							
							$('<td/>').html('PHP '+ '<span id="cost" >'+items.cost+'</span><input type="hidden" id="cost" name="cost" value="'+cost+'"/>')
								.appendTo('#ord-row-'+data.id);
							var links = $('<td/>');
							
							$('<a/>')	
								.attr({
										'id':'edit-menu-item-'+data.id,
										'href': 'trans/pop_edit_menu_item/'+data.id,
										'rata-title' : 'Edit Menu',
										'rata-pass' : 'trans/pop_edit_menu_item_db/'+data.id,
										'rata-form' : 'pop_edit_menu_item_form',
										'class': 'btn btn-primary',
									 })
								.html('<i class="fa fa-edit"></i>&nbsp;Edit')
								.appendTo(links);
							$('<a/>')	
								.attr({
										'id':'add-new-modifier-'+data.id,
										'href': 'trans/pop_add_modifier?row='+data.id+'&id='+items.menu_id,
										'rata-title' : 'Add Modifier',
										'rata-pass' : 'trans/pop_add_modifier_db',
										'rata-form' : 'new_modifier_form',
										'class': 'btn btn-primary',
									 })
								.html('<i class="fa fa-plus"></i>&nbsp;Add Mod')
								.appendTo(links);

							$('<a/>')
							.attr({
									'id':'remove-menu-item-'+data.id,
									'class': 'remove-menu-item btn btn-primary',
								 })
							.html('<i class="remove-menu-item fa fa-trash-o"></i>&nbsp;Remove')
							.appendTo(links);

							links.appendTo('#ord-row-'+data.id);
							jsModifierAdd(data.id);
							jsModifierEdit(data.id);
							
							var msg = items.name + ' has been added.';
							rMsg(msg,'success');
							
						},'json');
					else{
						var msg = 'Menu item and quantity must not be empty and quantity must contain numbers.';
						rMsg(msg,'error');
					}
				}
			},'json');
		});
		//Remove menu item from the cart (session)
		$(document).on('click','.remove-menu-item', function(event)
		{
			event.preventDefault();
			var	line_id = $(this).closest('td').parent('tr').attr('ref'),
				url = 'wagon/delete_to_wagon/ord_cart/'+line_id;
				$.post(baseUrl+url,null,function(data)
				{
					$('#ord-row-'+line_id).remove();
					rMsg('Item has been deleted.','success');
					
				});
				remove_menu_mod_item(line_id);
		});

		function remove_menu_mod_item(line_id){
			$.post(baseUrl+'trans/check_menu_mod_items/'+line_id,null,function(data){
				$.each(data, function(k){
					url = 'wagon/delete_to_wagon/ord_mod/'+k;
					$.post(baseUrl+url,null);
				});
			
			},'json');
		}

		//remove modifier item from the mod (session)
		$(document).on('click','.remove-mod-item', function(event)
		{
			event.preventDefault();
			var	line_id = $(this).closest('td').parent('tr').attr('ref'),
				parent_row = $(this).parents('table:first').closest('tr').attr('id'),
				url = 'wagon/delete_to_wagon/ord_mod/'+line_id;

				$.post(baseUrl+url,null,function(data)
				{
					$('#mod-row-'+line_id).remove();
					rMsg('Modifier has been deleted.','success');

					var	el = '#'+parent_row +' #mod-items .mod-row .mod-cost',
						qty = parseInt($('#'+parent_row + ' #qty').text()),
						cost = $('#'+parent_row+' input#cost').val(),
						sum_cost = compute_menu_item(el, qty, cost);
						$('#'+parent_row+' #cost').html(sum_cost.toFixed(2));

					var menu_id = $('#ord-row-'+line_id).attr('data-menu-id'),
					menu_name = $('#ord-row-'+line_id).children('td:eq(1)').find('span:first').text();
				

					var updateData = 'line_id='+line_id+'&menu_id='+ menu_id+'&name='+menu_name+'&cost='+sum_cost+'&qty='+qty+'&update='+line_id;
					update_cart_from_wagon('wagon/update_to_wagon/ord_cart',updateData);
				});
		});

		$(document).on('click', '.add-to-menu-mod', function(event) 
		{ 	
			event.preventDefault();
			var url = 'wagon/add_to_wagon/ord_mod',
				mod_id = $(this).attr('ref'),
				cost=$(this).attr('data-cost'),
				sum_cost=0,
				mod_name=$(this).text(),
				row=$('#curr_mod_hid_item').val(),
				order_row = $('#order_row').val(),
				menu_id = $('#ord-row-'+order_row).attr('data-menu-id'),
				menu_name = $('#ord-row-'+order_row).children('td:eq(1)').find('span:first').text(),
				link = $('#ord-row-'+order_row+' #mod-items'),
				formData = 'menu_id='+menu_id+'&line_id='+order_row+'&mod_id='+ mod_id+'&mod_name='+mod_name+'&cost='+cost;

				var url_check_row = 'trans/check_rows_from_wagon',
					formData_check_row = 'line_id='+order_row+'&menu_id='+menu_id+'&name=ord_mod&ref_menu_item_id='+order_row+'&mod_id='+mod_id,
					error;
					
				$.post(baseUrl+url_check_row,formData_check_row,function(data){	
					if(data.error === null){
						$.post(baseUrl+url,formData,function(data){
								var items = data.items;
									$('<tr/>').attr({'id':'mod-row-'+data.id,'class':'mod-row', 'ref':data.id})
										.appendTo(link);
									$('<td/>').html('<span>'+items.mod_name+'</span>')
										.appendTo('#mod-row-'+data.id);
									$('<td/>').html('<span class="mod-cost">'+items.cost+'</span>')
										.appendTo('#mod-row-'+data.id);
									var links = $('<td/>');
									$('<a/>')
										.attr({
												'id':'remove-modifier-'+data.id,
												'class':'remove-mod-item',
												'ref':data.id
											 })
										.html('<i class="fa fa-trash-o fa-fw"></i>')
										.appendTo(links);
									links.appendTo('#mod-row-'+data.id);

								var el = '#ord-row-'+order_row +' #mod-items .mod-row .mod-cost',
									qty = $('#ord-row-'+order_row + ' #qty').text(),
									cost = $('#ord-row-'+order_row+' input#cost').val();

								var sum_cost = compute_menu_item(el, qty, cost);
									$('#ord-row-'+order_row+' #cost').html(sum_cost.toFixed(2));

									var updateData = 'line_id='+order_row+'&menu_id='+ menu_id+'&name='+menu_name+'&cost='+sum_cost+'&qty='+qty+'&update='+order_row;
									update_cart_from_wagon('wagon/update_to_wagon/ord_cart',updateData);
						},'json');
					}else{
							rMsg(data.error,'error');
					}
				},'json');
		});
		function update_cart_from_wagon(url, formData){
			$.post(baseUrl+url,formData);
		}
		function jsModifierAdd(id)
		{
			$('#add-new-modifier-'+id).rPopView({
				asJson : true,
				onComplete: function(data){
					$('[data-bb-handler=cancel]').click();
				}		
			});
		}
		function jsModifierEdit(id)
		{
			$('#edit-menu-item-'+id).rPopFormFile({
				asJson : true,
				onComplete: function(data){
					if(data.qty != '' && data.qty > 0  && $.isNumeric(data.qty))
					{
						$('#ord-row-'+id +' span#qty').text(data.qty);

						var el = '#ord-row-'+id +' #mod-items .mod-row .mod-cost',
						qty = $('#ord-row-'+id + ' #qty').text(),
						cost = $('#ord-row-'+id+' input#cost').val();

						var sum_cost = compute_menu_item(el, qty, cost);

						$('#ord-row-'+id+' td span#cost').text(sum_cost.toFixed(2));

						var updateData = 'line_id='+id+'&menu_id='+ data.item.menu_id+'&name='+data.item.name+'&cost='+sum_cost+'&qty='+data.qty+'&update='+id;
						update_cart_from_wagon('wagon/update_to_wagon/ord_cart',updateData);
						$('[data-bb-handler=cancel]').click();
					}else{
						rMsg('Quantity must be a number.','error')
						$('#pop_edit_menu_item_form #qty').focus();
						return false;
					}
					
				}		
			});
		}
	<?php endif; ?>

});
</script>