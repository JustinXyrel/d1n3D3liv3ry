<script>
$(document).ready(function(){
	<?php if($use_js == 'drawerJs'): ?>
		loadDivs('curr-shift');
		loadDrops('curr-shift');
		$('#deposit-list').perfectScrollbar({suppressScrollX: true});
		$('#withdraw-list').perfectScrollbar({suppressScrollX: true});
		$('#deno-div').perfectScrollbar({suppressScrollX: true});
		$('#curr-shift-btn').click(function(){
			loadDivs('curr-shift');
			loadDrops('curr-shift');
			return false;
		});
		$('#deposit-btn').click(function(){
			loadDivs('deposit');
			loadDrops('deposit');
			return false;
		});
		$('#deposit-submit-btn').click(function(){
			var amount = $('#deposit-input').val();
			$.post(baseUrl+'drawer/deposit/'+amount,function(data){
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					$('#deposit-list').append(data.code);
					$('#deposit-input').val('');
					rMsg('Amount Deposited.','success');
					delEntry(data.id);
				}
			},'json');
			return false;
		});
		$('#withdraw-btn').click(function(){
			loadDivs('withdraw');
			loadDrops('withdraw');
			return false;
		});
		$('#withdraw-submit-btn').click(function(){
			var amount = $('#withdraw-input').val();
			$.post(baseUrl+'drawer/withdraw/'+amount,function(data){
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					$('#withdraw-list').append(data.code);
					$('#withdraw-input').val('');
					rMsg('Amount Withdrawn.','success');
					delEntry(data.id);
				}
			},'json');
			return false;
		});
		//COUNT
		$('#cash-count-btn').click(function(){
			loadDivs('cash-count');
			$.post(baseUrl+'drawer/get_over_all_total/',function(data){
				$('#overall-total').val(data.overAllTotal);
				$('.drawer-amount').number(data.overAllTotal,2);
			},'json');
			return false;
		});
		$("#count-btn").attr('target','credit');
		$('#ref-input').prop('disabled', false);
		$('.refy').show();
		$('.count-type-btn').click(function(){
			var type = $(this).attr('ref');
			var label = type+' Amount';
			$('#amt-label').text(label.toUpperCase());
			$("#count-btn").attr('target',type);
			if(type == 'credit' || type == 'gift'){
				$('#ref-input').prop('disabled', false);
				$('.refy').show();
				countsDivs('count-tbl');
			}
			else{
				$('#amt-label').text('Quantity');
				$('#ref-input').prop('disabled', true);
				$('.refy').hide();				
				countsDivs('count-cash');
				showDenominations();
			}
			resetInput();
			return false;
		});
		$("#count-btn").click(function(){
			if($(this).hasAttr('target')){
				var type = $(this).attr('target');
				var amt = $('#count-input').val();
				var ref = "";
				if(amt != ""){
					if(type == 'credit' || type == 'gift'){
						ref = $('#ref-input').val();
						if(ref != ""){
							addToCountCart(type,amt,ref);							
						}
						else{
							rMsg('Invalid Reference #.','error');
						}
					}
					else if(type=='cash'){
						var btn = $('.deno-sel');
						var val = parseFloat(btn.attr('val'));
						var qty = parseFloat(amt);
						addToCountCart(type,(qty * val),val);	
						btn.find('.deno-qty').number(qty,2);
					}
					else{
						addToCountCart(type,amt,val);
					}	
				}
				else{
					rMsg('Invalid amount.','error');
				}
			}
			return false;
		});
		$("#cash-go-back-btn").click(function(){
			$('#ref-input').prop('disabled', false);
			$('.refy').show();
			countsDivs('count-tbl');

			var label = 'Credit Amount';
			$('#amt-label').text(label.toUpperCase());
			$("#count-btn").attr('target','credit');
			return false;
		});
		$('.count-inputs').focus(function(){
			$('#count-key-tbl').attr('target','#'+$(this).attr('id'));
		});
		$('.save-count-btn').click(function(){
			var print = false;
			if($(this).hasClass('count-print'))
				print = true;
			$.post(baseUrl+'drawer/save_count/'+$('#overall-total').val()+'/'+print,function(data){
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Drawer count saved.','success');
					$('#cash-drawer-btn').trigger('click');
				}
			},'json');
			// alert(data);
			// });
		});
		function addToCountCart(type,amt,ref){
			var formData = 'type='+type+'&amount='+amt+'&ref='+ref;
			$.post(baseUrl+'wagon/add_to_wagon/count_cart',formData,function(data){
				var row = data.items;
				var id = data.id;
				// if(row.type == 'gift' || row.type=='check')
				createRow(id,row);	
				totalCounts(row.type);
				resetInput();
			},'json');
			// alert(data);
			// });
		}
		function createRow(id,row){
			var sDivRow = $('<div/>').attr({'class':'row orders-list-div-btnish'});
			var sDivCol1 = $('<div/>').attr({'class':'col-md-8'}).appendTo(sDivRow);
			$('<h4/>').css({'margin-left':'10px'}).number(row.amount,2).appendTo(sDivCol1);
			$('<h5/>').css({'margin-left':'10px'}).text(row.ref).appendTo(sDivCol1);
			var sDivCol2 = $('<div/>').attr({'class':'col-md-4'}).appendTo(sDivRow);
			$('<button/>').attr({'class':'btn-block manager-btn-red'})
						  .css({'margin-top':'10px'})
						  .html('<i class="fa fa-times"></i>')
						  .click(function(){
						  	var btn = $(this);
						  	$.post(baseUrl+'wagon/delete_to_wagon/count_cart/'+id,function(data){
								totalCounts(row.type);
								resetInput();
								btn.parent().parent().remove();
						  	},'json');	
						  	return false;
						  })
						  .appendTo(sDivCol2);
			$('#'+row.type+'-list').append(sDivRow);
		}
		function showDenominations(){
			$.post(baseUrl+'drawer/show_denominations/',function(data){
				$('#deno-div').html(data.code);
				$('#deno-div').perfectScrollbar('update');
				$.each(data.ids,function(key,id){
					$('#deno-btn-'+id).click(function(){
						$('.orders-list-div-btnish').removeClass('bg-green');
						$('.orders-list-div-btnish').removeClass('deno-sel');
						$('#deno-btn-'+id).addClass('bg-green');
						$('#deno-btn-'+id).addClass('deno-sel');
						resetInput();
						return false;
					});
					$('#del-cash-'+id).click(function(){
						var val = $(this).attr('val');
						$.post(baseUrl+'drawer/del_cash_in_count_cart/'+val,function(data){
							$('#deno-btn-'+id+' .deno-qty').number(0,2);
							checkCart();
							totalCounts('cash');
							resetInput();
						});
						return false;
					});
				});
			},'json');
			// alert(data);	
			// });	
		}
		function totalCounts(type){
			$.post(baseUrl+'drawer/count_totals/'+type,function(data){
				$('*[ref="'+type+'"]').find('.amt').number(data.total,2);
				$('.drawer-count-amount').number(data.overall,2);
				// checkCart();
			// });	
			},'json');	
		}
		function resetInput(){
			$('#count-input').val('');
			$('#ref-input').val('');
		}
		function loadDivs(type){
			$('.draws-div').hide();
			$('#'+type+'-div').show();
		}
		function countsDivs(type){
			$('.counts-div').hide();
			$('#'+type+'-div').show();
		}
		function loadDrops(type){
			$.post(baseUrl+'drawer/drops/'+type,function(data){
				$('#'+type+'-list').html(data.code);
				$('#'+type+'-list').perfectScrollbar('update');
				$.each(data.ids,function(key,id){					
					delEntry(id);
				});
			// alert(data);
			// });
			},'json');
		}
		function delEntry(id){
			$('#del-'+id).click(function(){
				var row = $(this).parent().parent();
				$.post(baseUrl+'drawer/delete_entry/'+id,function(data){
					rMsg('Line Deleted.','success');
					row.remove();
				});
				return false;
			});
		}
		function checkCart(){
			$.post(baseUrl+'wagon/get_wagon/count_cart/null/true',function(data){
				alert(data);
		  	});
		}
	<?php endif; ?>
});
</script>