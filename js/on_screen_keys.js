$(document).ready(function(){

	$('.scr-pad-key').disableSelection();
	$('.scr-pad-key').click(function(){
		var txt = $(this).text();
		var par = $(this).closest('table');
		if(par.hasAttr('target')){
			var input = $(par.attr('target'));
		}
		else
			var input = par.find('input');
		var inputTxt = input.val();

		if($(this).hasClass('btn-del')){
			if(inputTxt != ""){
				inputTxt = inputTxt.slice(0,-1);
				input.val(inputTxt);
			}
		}
		else if($(this).hasClass('btn-clear')){
			if(inputTxt != ""){
				input.val('');
			}
		}
		else if(!$(this).hasClass('btn-enter')) {
			input.val(inputTxt+txt);
			input.focus();
		}

		return false;
	});

});

