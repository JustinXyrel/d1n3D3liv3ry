<script>
$(document).ready(function(){
	<?php if($use_js == 'managerJs'): ?>
		$('#cash-drawer-btn').click(function(){
			var this_url = baseUrl+'drawer';
			$('.manager-content').rLoad({url:this_url});
			return false;
		});

		$('#end-of-day-btn').click(function(){
			var this_url = baseUrl+'manager/manager_end_of_day';

			$.post(this_url, {}, function(data){
				$('.manager-content').html(data);
			});
			return false;
		});

		$('#report-btn').click(function(event)
		{
			event.preventDefault();
			$.ajax({url:baseUrl+'manager/manager_reports'}).done(function(data){$('.manager-content').html(data);});
			// $.post(baseUrl+'manager/manager_reports',{},function(data)
			// {
			// 	alert(data);
			// });
		});

		$('#order-btn').click(function(){
			// alert('Order');
			var this_url = baseUrl+'manager/manager_orders';

			$.post(this_url, {}, function(data){
				$('.manager-content').html(data);
			});
			return false;
		});

		$('#system-btn').click(function(){
			var this_url = baseUrl+'manager/manager_settings';

			$.post(this_url, {}, function(data){
				$('.manager-content').html(data);
			});
			return false;
		});

		$('#exit-btn').click(function(){
			// window.location = baseUrl+'cashier';
			window.location = baseUrl+'manager/go_logout';
			return false;
		});

	<?php elseif($use_js == 'managerLoginJs'): ?>
		$('#pin-login').on('click',function(event)
		{
			event.preventDefault();

			var hashTag = window.location.hash;

			var formData = 'pin='+$('#pin').val();
			$.post(baseUrl+'manager/go_login',formData,function(data)
			{
				if (typeof data.error_msg === 'undefined')
					location.reload();
				else
					rMsg(data.error_msg,'error');
			},'json');
		});
		$('#cancel-btn').on('click',function(event)
		{
			event.preventDefault();
			window.history.back();
		});
	<?php elseif($use_js == 'endofdayJS'): ?>
		$('#day-report-btn').click(function(event)
		{
			event.preventDefault();
			$('#manager-content').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-4x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'manager/manager_end_of_day_report';
			$.post(this_url, {}, function(data){
				$('#manager-content').html(data);
			});
		});
		$('#day-xread-btn').click(function(event){
			event.preventDefault();
			$('#manager-content').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-4x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'manager/manager_xread';

			$.post(this_url, {}, function(data){
				$('#manager-content').html(data);
			});
		});
		$('#day-zread-btn').click(function(event){
			$('#manager-content').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-4x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'manager/manager_zread';

			$.post(this_url, {}, function(data){
				$('#manager-content').html(data);
			});
		});
		$('#day-report-btn').click();
	<?php elseif($use_js == 'endofdayReportJS'): ?>
		$('#print-btn').click(function(event){
			$.post(baseUrl+'manager/print_endofday_receipt','',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
			event.preventDefault();
		});
		$('.div-report-txt').perfectScrollbar({suppressScrollX:true});
	<?php elseif($use_js == 'xreadJS'): ?>
		$('#print-btn').click(function(event){
			event.preventDefault();

			bootbox.confirm('<h4>You are about to end this shift schedule (X-Read). Would you like to proceed?</h4>',function(result)
			{
				if (result) {
					$.post(baseUrl+'cashier/print_xread',{},function(data)
					{
						$('#day-xread-btn').click();
					},'json');
					// 	alert(data);
					// });
				}
			});
			// bootbox.dialog({
			// 	message: "<h3>You are about to end this shift schedule (X-Read). Would you like to proceed?</h3>",

			// });

		});

		$.post(baseUrl+'cashier/show_xread',{},function(data)
		{
			$('#read-txt').html(data.txt);
		},'json');

		$('.div-report-txt').perfectScrollbar({suppressScrollX:true});
	<?php elseif($use_js == 'zreadJS'): ?>
		$('#print-btn').click(function(event){
			event.preventDefault();

			bootbox.confirm('<h4>You are about to run end of day report (Z-Read). Would you like to proceed?</h4>',function(result)
			{
				if (result) {
					$.post(baseUrl+'cashier/print_zread',{},function(data)
					{
						if (typeof data.error_msg === 'undefined')
							rMsg(data.msg,'success');
						else
							rMsg(data.error_msg,'error');
					},'json');
				}
			});

		});

		$.post(baseUrl+'cashier/show_zread',{},function(data)
		{
			$('#read-txt').html(data.txt);
		},'json');

		$.post(baseUrl+'manager/check_zread_okay',{},function(data)
		{
			if (typeof data.error != 'undefined') {
				$('#print-btn').attr('disabled','disabled');
				rMsg(data.error,'error');
			}
		},'json');
		// 	console.log(data);
		// });

		$('.div-report-txt').perfectScrollbar({suppressScrollX:true});
	<?php elseif($use_js == 'systemJS'): ?>
		$('#settings-btn').click(function(){
			var this_url = baseUrl+'manager/system_settings';
			$('.manager-settings-center').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			$.post(this_url, {}, function(data){
				$('.manager-settings-center').html(data);
			});
			return false;
		});	<?php elseif($use_js == 'systemSettingsJS'): ?>
		$('#save-btn').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-btn'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										rMsg(data.msg,'success');
									}
			});
			return false;
		});
		/*
		$('#branch_code,#branch_desc,#tin,#bir,#branch_name,#address,#machine_no,#email,#contact_no,#delivery_no,#permit_no,#serial,#website')
        .keyboard({
            alwaysOpen: false,
            usePreview: false
        })
        .addNavigation({
            position   : [0,0],
            toggleMode : false,
            focusClass : 'hasFocus'
        });
	*/
   <?php elseif($use_js == 'managerOrdersJS'): ?>
		// alert('Manager Orders');
		var scrolled=0;
		var transScroll=0;
		$('.manager-orders-list').perfectScrollbar({suppressScrollX: true});
		$('.manager-orders-main-btns').show();
		$('.manager-orders-close-trx').hide();

		function loadOrders(terminal, status,types){
			// alert(terminal+'---'+status+'---'+types);
			$('.manager-orders-list').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			// $.post(baseUrl+'cashier/manager_view_orders/'+terminal+'/open/'+types+'/null/combineList',function(data){
			$.post(baseUrl+'cashier/manager_view_orders/'+terminal+'/'+status+'/'+types+'/null/combineList',function(data){
				// alert(baseUrl+'cashier/manager_view_orders/'+terminal+'/'+status+'/'+types+'/null/combineList'+'-----'+data);
				$('.manager-orders-list').html(data.code);
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						addDelFunc(id,val);

					});
					$('.manager-orders-list').perfectScrollbar('update');
				}
			},'json');
			// });
		}

		function addDelFunc(num){
			$('#order-btnish-'+num).click(function(){
				// alert(num);
				$('.sel-row').removeClass('selected');
				$(this).addClass('selected');
			});
		}

		// //working
		function loadTrx(sales_id){
			// var id = $('#settle').attr('sales');
			// alert('Load trx:'+sales_id);

			$.post(baseUrl+'cashier/manager_settle_transactions/'+sales_id,function(data){
				// alert('dsadasd-----'+data);
				$('.manager-orders-menu').hide();
				$('.manager-orders-main-btns').hide();
				$('.manager-orders-close-trx').show();

				$('.manager-orders-list').html(data.code);
				$('.manager-orders-list').perfectScrollbar({suppressScrollX: true});
				$.each(data.ids,function(key,pay_id){
					// deletePayment(pay_id,id);
				});
			},'json');
			// });
		}

		$('#close-trx-btn').click(function(){
			$('.manager-orders-menu').show();
			$('.manager-orders-main-btns').show();
			$('.manager-orders-close-trx').hide();
			return false;
		});

		// function loadTrx(sales_id){
			// // var id = $('#settle').attr('sales');
			// // alert('Load trx:'+sales_id);

			// $.post(baseUrl+'cashier/manager_settle_transactions/'+sales_id,function(data){
				// // alert('dsadasd-----'+data);
				// $('.manager-orders-btns').html(data.code);
				// $('.manager-orders-btns').perfectScrollbar({suppressScrollX: true});
				// $.each(data.ids,function(key,pay_id){
					// // deletePayment(pay_id,id);
				// });
			// },'json');
			// // });
		// }

		loadOrders('my','open','all');

		$('.my-all-btns').click(function(){
			var terminal = $(this).attr('ref');
			var status = $(this).attr('status');
			var types = $('.manager-orders-list').attr('types');
			$('.manager-orders-list').attr('terminal',terminal);
			$('#clickedmenu').val(terminal);
			loadOrders(terminal,status,types);
			return false;
		});

		$('#recall-btn').click(function(){
			var sel = $('.selected');
			var ref = sel.attr('ref');
			var type = sel.attr('type');
			// alert(ref+'-----'+type);
			window.location = baseUrl+'cashier/counter/'+type+'/'+ref;
			return false;
		});

		$('#view-trx-btn').click(function(){
			// alert('View Transactions');
			var sel = $('.selected');
			var ref_id = sel.attr('ref');
			var type = sel.attr('type');
			// alert(ref_id+'-----'+type);
			loadTrx(ref_id);
			return false;
		});

		$('#print-selected-btn').click(function(){
			var sel = $('.selected');
			var ref_id = sel.attr('ref');
			var type = sel.attr('type');
			// alert(ref_id+'-----'+type);

			$.post(baseUrl+'cashier/print_sales_receipt/'+ref_id,'',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
			return false;
		});

		$('#print-all-btn').click(function(){
			var terminal = $('#clickedmenu').val();
			var types = $('.manager-orders-list').attr('types');
			// alert('Print all: '+terminal+'---'+types);
			$.post(baseUrl+'cashier/manager_print_all_receipts/'+terminal+'/open/'+types+'/null/combineList/true',function(data){
				// alert(data);
				// console.log(data);
				rMsg(data.msg,'success');
			},'json');

			return false;
		});

	<?php elseif ($use_js == "managerReportsJs"): ?>
		$('.btn-manager-report').on('click',function(event)
		{
			event.preventDefault();
			var id = $(this).attr('id');

			$('#report-form-div').html('<i class="fa fa-4x fa-spinner fa-spin"></i>');
			$.ajax({
				url:baseUrl+'manager/manager_report_form/'+id,
				dataType:'json'
				})
			 .done(function(data){
			 		$('#report-form-div').html(data.code);
			 	});
		});
		$('.scrollbar-div').perfectScrollbar({suppressScrollX: true});
	<?php endif; ?>
});
</script>