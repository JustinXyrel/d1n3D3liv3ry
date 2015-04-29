<script>
$(document).ready(function(){
	<?php if($use_js == 'centralizedViewJs'): ?>
        $('aside.left-side').addClass('collapse-left');

        $('.status-type').click(function(){
            var id = $(this).find('span').attr('id').replace('count_', '');
            if(id != 'pending')
                $.post(baseUrl + 'agents/get_list_by_status/'+id, function(data){
                    $('.second-container').html(data.code);
                },'json'); 
            else
                $.post(baseUrl + 'agents/get_list_pending/'+id, function(data){
                    $('.first-container').html(data.code);
                },'json'); 
        });

		(function update_centralized(){
    		$.post(baseUrl + 'agents/update_centralized', function(data){
                    $('#pending-tbl tr').each(function(){
                        if(typeof $(this).attr('ref')  != "undefined")
                        {
                            var ref = $(this).attr('ref');
                            if(jQuery.inArray(ref, data.pending.current) === -1)
                                $(this).remove();   
                        }
                    });

                    $('.left-cancelled div').each(function(){
                        if($(this).attr('ref')  != '')
                        {
                            var ref = $(this).attr('ref');
                            if(jQuery.inArray(ref, data.cancelled.current) === -1)
                                $(this).remove();   
                        }
                    });

                    $('.left-hold div').each(function(){
                        if($(this).attr('ref')  != '' && !$(this).hasClass('non-on-hold'))
                        {
                            var ref = $(this).attr('ref');
                            if(jQuery.inArray(ref, data.on_hold.current) === -1)
                                $(this).remove();   
                        }
                    });
  
                    
                    if(data.pending.code != '')
                       $('#pending-tbl').append(data.pending.code);
                       
					if(data.on_process.code != '')
						$('#tbl_process').append(data.on_process.code);

                    $('#count_pending').next('.badge').text(data.pending.count);
                    $('#count_process').next('.badge').text(data.on_process.count);
    				$('#count_hold').next('.badge').text(data.on_hold.count);
					$('#count_cancelled').next('.badge').text(data.cancelled.count);
                    $('#count_voided').next('.badge').text(data.void.count);

                    var data_  = data.cancelled.data;
                    if(data_.length > 0);
                    {
                        $('.left-cancelled').find('.non-cancelled').remove();
                        $.each(data_, function(k, v){
                            $('<div/>').html('<i class="fa fa-fw fa-fw fa-ellipsis-v"></i>' + v.trans_ref + ' | ' + v.username.toUpperCase() )
                                       .css({margin: '2px', padding: '4px 0px', width:'98%','text-align':'center' })
                                        .attr('ref', v.sales_id)
                                       .appendTo('.left-cancelled');
                        });
                    }

                    var data_  = data.on_hold.data;
                    if(data_.length > 0);
                    {
                        $(document).find('.non-on-hold').remove();
                        $.each(data_, function(k, v){
                            $('<div/>').html('<i class="fa fa-fw fa-fw fa-ellipsis-v"></i>'  + v.trans_ref + ' | ' + v.username.toUpperCase())
                                      .css({margin: '2px', padding: '4px 0px', width:'98%','text-align':'center' })
                                       .attr('ref', v.sales_id)
                                       .appendTo('.left-hold');
                        });
                    }

                if($('.left-hold div').length == 0)
                    $('.left-hold').append('<div  style="text-align:center; width: 98%; margin: 2px; padding: 4px 0px;" class="non-on-hold">No on hold order.</div>');
                
                if($('.left-cancelled div').length == 0)
                    $('.left-cancelled').append('<div style="text-align:center; width: 98%; margin: 2px; padding: 4px 0px;" class="non-cancelled">No cancelled order.</div>');
                  
                var data_ = data.on_process.tds;
                var len = Object.keys(data_).length;

                if(len > 0)
                {
                    var td;
                     $.each(data_, function(k, v){
                        td = '#tbl_process tr[ref='+k+']';
                        $(td+' td:last').html(v);
                    });
                }

    		},'json'); 
    		setTimeout(update_centralized, 1000)
		})();

	<?php endif; ?>
});
</script>