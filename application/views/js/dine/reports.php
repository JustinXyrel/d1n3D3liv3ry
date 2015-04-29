<script>

$(document).ready(function(){

	$('.datepicker').datepicker({
		format: 'mm-dd-yyyy'
	});


	$('.daterangepicker').keydown(function(e) {
	   e.preventDefault();
	   return false;
	});
	

	$('input.daterangepicker').daterangepicker({separator:' to ',
 													format: 'MM-DD-YYYY',
													});

	$('input[name=type_date]').change(function(){
		var type = $(this).attr('id');
		if(type == 'type_range')
		{
			$('#datepicker').val('');
			$('.div_range').css('display','block');
			$('.div_date').css('display','none');
		}else{
			$('#daterange').val('');
			$('.div_range').css('display','none');
			$('.div_date').css('display','block');
		}
	});

	
	<?php elseif($use_js == 'reportIndexJS'):?>
		$('#print-pdf-btn').click(function(){
			var type_report = $('#type_report').val();
			
			var daterange = $('#daterange').val();
			var datepicker = $('#datepicker').val();
			var date = '';

			if(typeof daterange !== "undefined" && daterange != '')
				date = daterange;
			else if(typeof datepicker !== "undefined" && datepicker != '')
				date = datepicker;

			if(date != '')
			{
				if(type_report == '1')
					window.open(baseUrl+'reports/print_hit_rate_report/pdf/print/'+date,'_blank',"width=829 ,height=501");
				else if(type_report == '2')
					window.open(baseUrl+'reports/print_cancelled_report/pdf/print/'+date,'_blank',"width=829 ,height=501");
				else if (type_report == '3')
					window.open(baseUrl+'reports/print_complaint_report/pdf/print/'+date,'_blank',"width=829 ,height=501");
				else if(type_report == '4')
					window.open(baseUrl+'reports/print_rider_efficiency_report/pdf/print/'+date,'_blank',"width=829 ,height=501");
					
				return false;
			}else
				rMsg('Date must not be empty.', 'error');
		});

		$('#print-excel-btn').click(function(){
			var type_report = $('#type_report').val();
			
			var daterange = $('#daterange').val();
			var datepicker = $('#datepicker').val();
			var date = '';

			if(typeof daterange !== "undefined" && daterange != '')
				date = daterange;
			else if(typeof datepicker !== "undefined" && datepicker != '')
				date = datepicker;
			
			if(date != '')
			{
				if(type_report == '1')
					window.open(baseUrl+'reports/print_hit_rate_report/excel/print/'+date,'_blank',"width=829 ,height=501");
				else if(type_report == '2')
					window.open(baseUrl+'reports/print_cancelled_report/excel/print/'+date,'_blank',"width=829 ,height=501");
				else if (type_report == '3')
					window.open(baseUrl+'reports/print_complaint_report/excel/print/'+date,'_blank',"width=829 ,height=501");
				else if(type_report == '4')
					window.open(baseUrl+'reports/print_rider_efficiency_report/excel/print/'+date,'_blank',"width=829 ,height=501");
					
				return false;
			}else
				rMsg('Date must not be empty.', 'error');
		});


	<?php endif; ?>
});
</script>