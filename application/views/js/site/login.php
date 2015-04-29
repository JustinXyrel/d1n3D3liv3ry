<script>
$(document).ready(function(){
	<?php if($use_js == 'loginJs'): ?>
		$('.login-by').click(function(){
			$('.logins').hide();
			$($(this).attr('act')).show();
			return false;
		});

		$('#uname-login').click(function(){
			$("#uname-login-form").rOkay({
				btn_load		: 	$('#uname-login'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error_msg != null){
											rMsg(data.error_msg,'error');
										}
										else{
											window.location = data.redirect_address;
										}
										// if(data.error_msg != null){
										// 	rMsg(data.error_msg,'error');
										// }
										// else{
										// 	window.location = baseUrl+'cashier';
										// }

										// if(data.error_msg != null){
										// 	rMsg(data.error_msg,'error');
										// }else{
										// 	//Crew Head
										// 	if(data.role_id == 7)
										// 		window.location = baseUrl+'kitchen';
										// 	//Cashier Monitor
										// 	else if(data.role_id == 6)
										// 		window.location = baseUrl+'monitor';
										// 	//Store Manager
										// 	else if(data.role_id ==  2)
										// 		window.location = baseUrl;
										// 	//Else
										// 	else
										// 		rMsg('Sorry. You do not have access rights on this site.','error');
										// }
									}
			});
			return false;
		});
		$('#pin-login').click(function(){
			var pin = $('#pin').val();
			$.post(baseUrl+'site/go_login','pin='+pin,function(data){
				if(data.error_msg != null){
					rMsg(data.error_msg,'error');
				}
				else{
					window.location = data.redirect_address;
				}
			},'json');
			return false;
		});
		// $('#login-btn').click(function(){
		// 	$("#login-form").rOkay({
		// 		btn_load		: 	$('#login-btn'),
		// 		bnt_load_remove	: 	true,
		// 		asJson			: 	true,
		// 		onComplete		:	function(data){
		// 								// alert(data);
		// 								if(data.error_msg != null){
		// 									rMsg(data.error_msg,'error');
		// 								}
		// 								else{
		// 									window.location = baseUrl;
		// 								}
		// 							}
		// 	});
		// 	return false;
		// });

	<?php endif; ?>
});
</script>