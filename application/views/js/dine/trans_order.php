<script>
$(document).ready(function(){

	<?php if($use_js == 'transListJS'): ?>

		$(document).on('click', '.void-transaction', function(){
			var $this = $(this);
			var id = $(this).attr('ref');

			bootbox.dialog({
			  message: baseUrl +'trans_order/pop_void_order/'+id,
			  title: "Void Transaction",
			  buttons: {
			   submit: {
			      label: "Void",
			      className: "btn-primary",
			      callback: function(data){
			      	var reason = $('#inp_reason').val(),
						url = 'trans_order/pop_void_order_db',
						formData = 'reason='+reason+'&id='+id+'&inactive=1';

						if(reason!='')
							$.post(baseUrl+url,formData,function(data){
								if(data){
									rMsg('Transaction has been voided.','success');	
									window.location.reload();	
								}else
									rMsg('Unable to process transaction', 'error');
							});
						else{
							$('#inp_reason').focus();
							rMsg('Must provide reason to void transaction.', 'error');
							return false;
						}
			      }
			    }
			  }
			});
		});

		$(document).on('keyup', '#trans-search', function(){
			var val = $(this).val();
			
			if(val == ""){
				var url = 'trans_order/get_found_transaction';		
				$.post(baseUrl+url,function(data){
					$('#transactions-tbl .t-rows').remove();
					$('#transactions-tbl').append(data.code);
				},'json');
			}

			$('#trans-search').typeaheadmap({
				"source": function(search, process) {
					var url = $('#trans-search').attr('search-url'),
						branch_id = $('.nav-tabs li.active a').attr('ref'),
						formData = 'search='+search+'&branch_id='+branch_id;
					$.post(baseUrl+url,formData,function(data){
						process(data);
					},'json');
				},
			    "key": "key",
			    "value": "value",
			    "listener": function(k, v) {
			    	var url = 'trans_order/get_found_transaction', formData = 'id='+v;
			    	$.post(baseUrl+url,formData,function(data){
						$('#transactions-tbl .t-rows').remove();
						$('#transactions-tbl').append(data.code);
					},'json');
				}
			});

		});
		
		function notifLoadCheck() {
			$.post('trans_order/countNotif', function(data) {
			  	$.each(data, function(k, v){
			  		$('.nav-tabs li').find('[ref='+v.branch_id+']').parent().find('span').html(v.count).attr('class','badge pull-top badge-warning').css({'margin': '0px 0px 0px 10px'});
			  	});
			},'json');
		}

		setInterval(notifLoadCheck, 1000);

		$('.tab_link').click(function(event)
		{
			event.preventDefault();
			var id = $(this).attr('id');
			loader('#'+id);
		});

		loader('#details_link');
		
		function loader(btn)
		{
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href'); 
			$(tabPane).rLoad({url:baseUrl+loadUrl});
		}
		function disableTabs(id,enable)
		{
			if (enable) {
				$(id).parent().removeClass('disabled');
				$(id).removeAttr('disabled','disabled');
				$(id).attr('data-toggle','tab');
			} else {
				$(id).parent().addClass('disabled');
				$(id).attr('disabled','disabled');
				$(id).removeAttr('data-toggle','tab');
			}
		}

		//updates list of pending transactions.
		(function display_new_pending(){
			$.post(baseUrl+'trans_order/get_pending_trans',function(data){
				if((typeof data.code != "undefined") || ( data.code != null))
				{
					$('#pending-trans-tbl').append(data.code);
					$('.title_pending span.badge').text(data.count);
				}

				$.each(data.status, function(k, v){
					$('.row-'+k+' td:eq(2)').html(v.time_ago);
					if(v.result != null)
						$('.row-'+k +' span.result').html(v.result);
				});

				var  cancelled = $('span.result .label-danger').length;
				$('.title_cancel span.badge').text(cancelled);
				$('.title_unprocessed span.badge').text(data.count - cancelled);

			},'json');

			setTimeout(display_new_pending, 1000)
		})();

	<?php elseif($use_js == 'viewTransactionJS'): ?>
		
	<?php endif; ?>

	

});
</script>