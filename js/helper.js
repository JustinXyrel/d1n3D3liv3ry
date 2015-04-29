/////////////////////////
//// Created by Reyp
(function($) {

	$.extend({
		beep: function(options){
		   var settings = $.extend({
		    status : "success"
		   }, options);
		   if(settings.status == "success")
		    $.playSound(baseUrl+'img/beep');
		   else if(settings.status == "error")
		    $.playSound(baseUrl+'img/beeperror');

	 	}
	});

	//$.beep;

	$.fn.disableSelection = function() {	
        return this
                 .attr('unselectable', 'on')
                 .css('user-select', 'none')
                 .on('selectstart', false);
    };
	$.fn.exists = function(){return this.length>0;}
	$.fn.hasAttr = function(name) {  
	   return this.attr(name) !== undefined;
	};
	$.fn.rOkay = function(options)	{
		var settings = $.extend({
			passTo		:	this.attr('action'),
			addData		:	"",
			checkCart	:	null,
			validate	: 	true,
			asJson		: 	false,
			btn_load	: 	null,
			goSubmit	: 	true,
			bnt_load_remove	: 	true,
			onComplete	:	function(response){}
		},options);

		function generate(text){
			var n = noty({
			       text        : text,
			       type        : 'error',
			       dismissQueue: true,
			       layout      : 'topRight',
			       theme       : 'defaultTheme',
			       animation	: {
								open: {height: 'toggle'},
								close: {height: 'toggle'},
								easing: 'swing',
								speed: 500 // opening & closing animation speed
							}
			   }).setTimeout(3000);
		}

		var errors = 0;
		var check_form = $(this);
		if(settings.validate){
			check_form.find('.rOkay').each(function(){
				if($(this).val() == ""){
					var txt = $(this).prev('label').text();
					var msg = $(this).attr('ro-msg');

					var display_msg = 'Error! '+txt+' must not be empty.';
					if(typeof msg !== 'undefined' && msg !== false)
						display_msg = msg;
						
					generate(display_msg);
					$(this).focus();
					errors = errors+1;
					return false;
				}
				
			});
		}
		if(settings.goSubmit){
			if(errors == 0){
				var form = check_form;
				var formData = check_form.serialize();
				if(settings.addData != "")
					formData = formData+'&'+settings.addData;

				// alert(formData);
				if(settings.btn_load != null){
					// var pretext = check_form.attr('id');
					var pretext = check_form.attr('id');
					var lastTxt = settings.btn_load.html();
					// settings.btn_load.attr("disabled", "disabled").after(' <img src="'+baseUrl+'/images/preloader.gif" height="20" id="'+pretext+'-preloader">');
					settings.btn_load.attr("disabled", "disabled").html(lastTxt+' <i class="fa fa-spinner fa-spin fa-fw"></i>');
				}


				if(settings.asJson){
					if(settings.checkCart != null){
						$.post(baseUrl+'wagon/check_wagon/'+settings.checkCart,function(check){
							if(check.error > 0){
								settings.btn_load.html(lastTxt);
								settings.btn_load.removeAttr("disabled");
								generate('Error! '+ check.msg);
							}
							else{
								
								if(settings.btn_load != null && settings.bnt_load_remove){
									settings.btn_load.html(lastTxt);
									settings.btn_load.removeAttr("disabled");
								}

								$.post(baseUrl+settings.passTo,formData,function(data){
									// alert(data);
									settings.onComplete.call(this,data);
								// });		
								},"json");		
							}
						},"json");
					}
					else{
						$.post(baseUrl+settings.passTo,formData,function(data){
							if(settings.btn_load != null && settings.bnt_load_remove){
								settings.btn_load.html(lastTxt);
								settings.btn_load.removeAttr("disabled");
							}
							settings.onComplete.call(this,data);
						// });
						},"json");
					}
				}
				else{
					if(settings.checkCart != null){
						$.post(baseUrl+'wagon/check_wagon/'+settings.checkCart,function(check){
							if(check.error > 0){
								settings.btn_load.html(lastTxt);
								settings.btn_load.removeAttr("disabled");
								generate('Error! '+ check.msg);
							}
							else{
								// alert(formData);	
								if(settings.btn_load != null && settings.bnt_load_remove){
									settings.btn_load.html(lastTxt);
									settings.btn_load.removeAttr("disabled");
								}
								$.post(baseUrl+settings.passTo,formData,function(data){
									settings.onComplete.call(this,data);
								});		
							}
						},"json");
					}
					else{
						$.post(baseUrl+settings.passTo,formData,function(data){
							if(settings.btn_load != null && settings.bnt_load_remove){
								settings.btn_load.html(lastTxt);
								settings.btn_load.removeAttr("disabled");
							}
							settings.onComplete.call(this,data);
						});	
					}
				}
			}		
		}
		else{

			if(errors > 0)
				return false
			else
				return true;
		}
	}

	$.fn.rLoad = function(options) {
		var settings = $.extend({
			url		:	''
		}, options);
		// this.html('<center><img src="'+baseUrl+'/images/preloader.gif"></center>').load(settings.url);
		this.html('<center><span><i class="fa fa-spinner fa-lg fa-fw fa-spin"></i></span></center>').load(settings.url);
	}
	$.fn.goLoad = function(options) {
		var settings = $.extend({
			load 	:  true
		}, options);
		var txt = this.html();
		if(settings.load){
			this.attr("disabled", "disabled").html(txt+' <i class="fa fa-spinner fa-spin fa-fw go-load-spinner"></i>');
		}
		else{
			this.removeAttr("disabled");
			this.find('.go-load-spinner').remove();
		}
	}
	$.fn.rPopForm = function(options) {
		$(this).click(function(){
			var settings = $.extend({
				loadUrl		:	$(this).attr('href'),
				passTo		:	$(this).attr('rata-pass'),
				title		:	$(this).attr('rata-title'),
				rform		:	$(this).attr('rata-form'),
				wide 		:   false,
				addData		:	"",
				hide		:	false,
				asJson		:	false,
				onComplete	:	function(response){}
			}, options);

			// alert(settings.loadUrl);
			// alert($(this).attr('href'));
			if(settings.wide)
				var classN = "bootbox-wide";
			else
				var classN = null;

			bootbox.dialog({
			  message: baseUrl+settings.loadUrl,
			  title: settings.title,
			  className : classN,
			  buttons: {
			    cancel: {
			      label: "Cancel",
			      className: "btn-default",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			      }
			    },
			    submit: {
			      label: "<i class='fa fa-save'></i> Submit",
			      className: "btn-primary rFormSubmitBtn",
			      callback: function() {
						// var formData = $('#'+settings.rform).serialize();
						// if(settings.addData != "")
						// 	formData = formData+'&'+settings.addData;
						// $.post(baseUrl+settings.passTo,formData,function(data){
						// 	settings.onComplete.call(this,data);
						// });

						$('#'+settings.rform).rOkay({
								onComplete	: 	settings.onComplete,
								passTo		: 	settings.passTo,
								addData		: 	settings.addData,
								asJson		: 	settings.asJson,
								btn_load	: 	$('.rFormSubmitBtn'),
						});
						return settings.hide;	
			      }
			    }
			  }
			});	
			return false;			
		})
	}

	$.fn.rPopFormFile = function(options) {
		$(this).click(function(){
			var settings = $.extend({
				loadUrl		:	$(this).attr('href'),
				passTo		:	$(this).attr('rata-pass'),
				title		:	$(this).attr('rata-title'),
				rform		:	$(this).attr('rata-form'),
				wide 		:   false,
				addData		:	"",
				hide		:	false,
				asJson		:	false,
				onComplete	:	function(response){}
			}, options);

			// alert(settings.loadUrl);
			// alert($(this).attr('href'));
			if(settings.wide)
				var classN = "bootbox-wide";
			else
				var classN = null;

			bootbox.dialog({
			  message: baseUrl+settings.loadUrl,
			  title: settings.title,
			  className : classN,
			  buttons: {
			    cancel: {
			      label: "Cancel",
			      className: "btn-default",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			      }
			    },
			    submit: {
			      label: "<i class='fa fa-save'></i> Submit",
			      className: "btn-primary rFormSubmitBtn",
			      callback: function() {
						var noError = $('#'+settings.rform).rOkay({
			    			asJson			: 	settings.asJson,
							btn_load		: 	$('.rFormSubmitBtn'),
							goSubmit		: 	false,
							bnt_load_remove	: 	true
			    		});
			    		if(noError){
			    			var dtype = 'script';
			    			if(settings.asJson)
			    				dtype = 'json';
			    			$('#'+settings.rform).submit(function(e){
							    var formObj = $(this);
							    var formURL = settings.passTo;
							    var formData = new FormData(this);
							    $.ajax({
							        url: baseUrl+formURL,
							        type: 'POST',
							        data:  formData,
							        dataType:  dtype,
							        mimeType:"multipart/form-data",
							        contentType: false,
							        cache: false,
							        processData:false,
							        success: function(data, textStatus, jqXHR){
							         	settings.onComplete.call(this,data);
							        },
							        error: function(jqXHR, textStatus, errorThrown){
							        }          
							    });
							    e.preventDefault();
							//     e.unbind();
							}); 
							$('#'+settings.rform).submit();
						}
						return settings.hide;	
			       }
			    }
			  }


			});	
			return false;			
		})
	}

	$.fn.rPopView = function(options) {
		$(this).click(function(){
			var settings = $.extend({
				loadUrl		:	$(this).attr('href'),
				title		:	$(this).attr('rata-title'),
				wide		: 	false,
				addData		:	""
			}, options);

			if(settings.wide){
				var bootClass = "bootbox-wide";
			}
			else
				var bootClass = "";

			bootbox.dialog({
			  message: baseUrl+settings.loadUrl,
			  title: settings.title,
			  className: bootClass,
			  buttons: {
			    cancel: {
			      label: "Close",
			      className: "btn-default",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			      }
			    }
			  }


			});	
			// bootbox.classes.add('bootbox-wide');
			return false;			
		})
	}

	$.fn.rPrint = function(options) {
		var settings = $.extend({
			loadUrl		:	$(this).attr('href'),
			title		:	$(this).attr('print-title')
			// wide		: 	false,
			// addData		:	""
		}, options);
		$(this).click(function(){
			var title = $(this).attr('print-title');
			var path = $(this).attr('href');

			// alert(settings.loadUrl);
			// window.open(url+path,title);
			window.open(path,title,"height=600,width=800");
			return false;			
		})
	}

	$.fn.rChangeSetVal = function(options) {
		var settings = $.extend({
			tbl		:	'',
			where	:	'',
			col		:	'',
			changee : 	''
		}, options);
		
		this.change(function(){
			
			var val = $(this).val();
			var formData = 'tbl='+settings.tbl+'&col='+settings.col+'&where='+settings.where+'&val='+val;
			
			goCall(formData, function(data){
				var obj = $('#'+settings.changee);
				if( obj.is('input') || obj.is('select') || obj.is('textarea') ) {
					 obj.val(data);
				}
				else{
					obj.text(data);
				}
			});	

			function goCall(formData, callback){
			    $.post(baseUrl+'/site/custom_get_val', formData, function(data){
			        callback(data);
			    });
			}
		});
	}

	$.fn.rWagon = function(options) {
		var opt = $.extend({
			cart      		: 	'',
			tbl  	  		: 	$(this),
			add_wagon_cell	: 	null,
			input_row 		: 	null,
			inputs	 		: 	null,
			reset_row		:   true,
			datas     		: 	null,
			removeAdd  		: 	false,
			// add_datas 		: null,
			// function(response){}
			beforeAddShow	:	null, 
			onAdd	  		:	function(response){},
			onUpdate  		:	function(response){},
			onCancelUpdate  :	function(response){},
			onEdit  		:	function(response){},
			onDelete  		:	function(response){}
		}, options);
		set_edit_rows();
		make_add_item_row();
		append_add_wagon_btns();
		links_act();
		function links_act(){
			$('#show-input'+opt.cart).click(function(){
				var before = true;
				var ret = null;
				if($.isFunction(opt.beforeAddShow)){
					var ret = opt.beforeAddShow.call(this);
				}
				if(ret != null){
					before = ret;
				}
				
				if(before){
					var row = $(opt.input_row);
					row.show();
					$('#show-input-row'+opt.cart).hide();
					var pos = $('#show-input-row'+opt.cart).index();
					opt.tbl.find('tbody > tr').eq(pos).after(row);
				}
				return false;
			});
			$('#wAddCancel'+opt.cart).click(function(){
				var row = $(opt.input_row);
				row.hide();
				$('#show-input-row'+opt.cart).show();
				
				return false;
			});
			$('#wAdd'+opt.cart).click(function(){
				add_row();
				return false;
			});
			$('#wUpdate'+opt.cart).click(function(){
				// e.preventDefault;
				var id = $(this).attr('ref');
				// alert(ref);
				update_row(id);
				return false;
			});
			$('#wUpdateCancel'+opt.cart).click(function(){
				// e.preventDefault;
				var row = $(opt.input_row);
				var id = $(this).attr('ref');
				$('.line-row').show();
				$('#line-'+opt.cart+'-'+id).show();
				$('.wagon-add-btns').show();
				$('.wagon-update-btns').hide();
				$('.wagon-update-btns').removeAttr('ref');
				row.hide();
				$('#show-input-row').show();
				opt.onCancelUpdate.call(this);
				return false;
			});
		}
		function add_row(){
			var pos = $('#show-input-row').index();
			var formData = "";
			var row = $(opt.input_row);
			var ctr = 1;
			var string_row = "";
			$.each(opt.inputs,function(key,val){
				var elem = row.find(val['from']);
				if(elem.exists()){
					var s = "";
					if(ctr > 1){
						s="&";
					}
					// formData = formData+s+key+'='+elem.val(); 
					if( elem.is('input') ||  elem.is('textarea') ) {
						var echo = elem.val();
						formData = formData+s+key+'='+elem.val(); 
					}
					else if(elem.is('select')){
						var echo = elem.find(":selected").text();
						formData = formData+s+key+'='+elem.val(); 
					}
					else{
						var echo = elem.text();
						formData = formData+s+key+'='+elem.text(); 
					}

					if(val['show'] != null){
						if(isNumber(echo) && typeof(val['string-type'])=='undefined'){
							var number = formatNumber(parseFloat(echo),val['dec']);
							string_row = string_row+"<td>"+number+"</td>";
						}
						else
							string_row = string_row+"<td>"+echo+"</td>";
					}
				}
				ctr++;
			});			
			
			$.post(baseUrl+'wagon/add_to_wagon/'+opt.cart,formData,function(data){
				var string = "<tr id='line-"+opt.cart+"-"+data.id+"' class='line-row line-row-"+opt.cart+"'>";
				var string = string+string_row;
				string = string+"<td><a href='#' id='edit-"+opt.cart+"-"+data.id+"' class = 'edit-"+opt.cart+"' ref='"+data.id+"'><i class='fa fa-edit fa-lg'></i></a>";
				string = string+" <a href='#' id='delete-"+opt.cart+"-"+data.id+"' class = 'delete-"+opt.cart+"' ref='"+data.id+"'><i class='fa fa-trash-o fa-lg'></i></a></td>";
				string = string+"</tr>";
				opt.tbl.find('tbody > tr').eq(pos).before(string);
				
				edit_row(opt.cart,data.id,data.items);
				opt.onAdd.call(this,data);

				if(opt.reset_row)
					reset_row();

			},"json");
			// });
		}	
		function reset_row(){
			$.each(opt.inputs,function(key,val){
				var row = $(opt.input_row);
				var elem = row.find(val['from']);
				if(elem.exists()){
					if( elem.is('input') ||  elem.is('textarea') ) {
						elem.val("");
					}
					else if(elem.is('select')){
						if(elem.hasClass('dropitize')){
							elem.find('option:first-child').prop('selected', true)
							    .end().trigger('chosen:updated');
						}
						else{
							elem.val(elem.find("option:first").val());
						}
						// elem.find("option").removeAttr('selected').find(':first').attr('selected','selected');
					}
					else{
						elem.text("");
					}
				}
				
			});	
		}
		function update_row(id){
			var pos = $('#line-'+opt.cart+'-'+id).index();
			var formData = "";
			var string_row = "";
			var ctr = 1;
			var row = $(opt.input_row);
			$.each(opt.inputs,function(key,val){
				var elem = row.find(val['from']);
				if(elem.exists()){
					var s = "";
					if(ctr > 1){
						s="&";
					}
					// formData = formData+s+key+'='+elem.val(); 
					if( elem.is('input') ||  elem.is('textarea') ) {
						var echo = elem.val();
						formData = formData+s+key+'='+elem.val(); 
					}
					else if(elem.is('select')){
						var echo = elem.find(":selected").text();
						formData = formData+s+key+'='+elem.val(); 
					}
					else{

						var echo = elem.text();
						formData = formData+s+key+'='+elem.text(); 
						
					}

					if(val['show'] != null){
						
						if(isNumber(echo) && typeof(val['string-type'])=='undefined'){
							var number = formatNumber(parseFloat(echo),val['dec']);
							string_row = string_row+"<td>"+number+"</td>";
						}
						else
							string_row = string_row+"<td>"+echo+"</td>";
					}
				}
				ctr++;
			});	
			
			formData = formData+'&update='+id;
			
			$.post(baseUrl+'wagon/update_to_wagon/'+opt.cart,formData,function(data){
				$('#line-'+opt.cart+'-'+id).remove();
				var string = "<tr id='line-"+opt.cart+"-"+data.id+"' class='line-row line-row-"+opt.cart+"'>";
				var string = string+string_row;
				string = string+"<td><a href='#' id='edit-"+opt.cart+"-"+data.id+"' class = 'edit-"+opt.cart+"' ref='"+data.id+"'><i class='fa fa-edit fa-lg'></i></a>";
				string = string+" <a href='#' id='delete-"+opt.cart+"-"+data.id+"' class = 'delete-"+opt.cart+"' ref='"+data.id+"'><i class='fa fa-trash-o fa-lg'></i></a></td>";
				string = string+"</tr>";
				opt.tbl.find('tbody > tr').eq(pos).before(string);
				// alert(data.items);
				edit_row(opt.cart,data.id,data.items);
				opt.onUpdate.call(this,data);
			},"json");	
			$('.wagon-add-btns').show();
			$('.wagon-update-btns').hide();
			$('.wagon-update-btns').removeAttr('ref');
			row.hide();
			$('#show-input-row').show();
		}
		function edit_row(cart_name,id,items){
			$("#edit-"+cart_name+"-"+id).click(function(e){
				e.preventDefault();
				var row = $(opt.input_row);
				row.show();
				var pos = $('#line-'+cart_name+'-'+id).index(); 

				$.each(opt.inputs,function(key,val){
					var from = row.find(val['from']);
					var elem = row.find(val['show']);

					if(elem.exists()){

						if( elem.is('input') ||  elem.is('textarea') ) {
							 elem.val(items[key]);

						}
						else if( elem.is('select') ){
							elem.val(items[key]).trigger("chosen:updated");
						}
						else{
							
							// if(!isNaN(parseFloat(items[key])) && typeof(val['string-type'])=='undefined'){
							// 	elem.number(items[key],val['dec']);
							// }
							// else	
							
							elem.text(items[key]);
						}
					}
					if(from.exists()){

						if( from.is('input') ||  from.is('textarea') ) {
							 from.val(items[key]);

						}
						else if( from.is('select') ){
							from.val(items[key]).trigger("chosen:updated");
						}
						else{
							
							if(isNumber(items[key]) && typeof(val['string-type'])=='undefined'){
								from.number(items[key],val['dec']);
							}
							else	
								from.text(items[key]);
						}
					}
				});

				$('.line-row').show();
				$('#line-'+cart_name+'-'+id).hide();
				opt.tbl.find('tbody > tr').eq(pos).after(row);
				
				$('.wagon-add-btns').hide();
				$('.wagon-update-btns').show();
				$('.wagon-update-btns').attr('ref',id);

				$('#show-input-row').hide();
				opt.onEdit.call(this);
			});	

			$("#delete-"+cart_name+"-"+id).click(function(e){
				var formData = 'delete='+id;
				$.post(baseUrl+'wagon/delete_to_wagon/'+cart_name,formData,function(data){
					$('#line-'+cart_name+'-'+id).remove();
					opt.onDelete.call(this,data);
				},"json");	
				e.preventDefault();
			});	
		}
		function set_edit_rows(){
			var edit_rows = $('.wagon-edit-rows');
			// alert(edit_rows.length);
			if(edit_rows.length > 0){
				edit_rows.each(function(){
					var row_id = $(this).attr('ref');
					var cart = $(this).attr('cart');
					if(cart == opt.cart){
						var string = "";
						$(this).addClass("line-row line-row-"+opt.cart);
						$(this).attr("id","line-"+opt.cart+"-"+row_id);
						var rowa = $(this);
						$.post(baseUrl+'wagon/get_wagon/'+opt.cart+'/'+row_id,function(data){
							string = string+"<a href='#' id='edit-"+opt.cart+"-"+row_id+"' class = 'edit-"+opt.cart+"' ref='"+row_id+"'><i class='fa fa-edit fa-lg'></i></a>";
							string = string+" <a href='#' id='delete-"+opt.cart+"-"+row_id+"' class = 'delete-"+opt.cart+"' ref='"+row_id+"'><i class='fa fa-trash-o fa-lg'></i></a>";
							// alert(data);
							var cell = rowa.find('td:last-child');
							// alert(data.items);
							// alert(cell);
							cell.html(string);
							edit_row(opt.cart,row_id,data.items);						
						// });
						},"json");
					}
				});
			}
		}
		function append_add_wagon_btns(){
			var row = $(opt.input_row);
			var cell = row.find('td:last-child');
			if(opt.add_wagon_cell != null){
				cell = row.find(opt.add_wagon_cell);
			}
			var string = "";
			string += "<a href='#' id='wAdd"+opt.cart+"' class='wagon-add-btns'><i class='fa fa-check fa-lg'></i></a> <a href='#' id='wAddCancel"+opt.cart+"' class='wagon-add-btns'><i class='fa fa-ban fa-lg'></i></a>";
			string += "<a href='#' id='wUpdate"+opt.cart+"' class='wagon-update-btns' style='display:none;'><i class='fa fa-check fa-lg'></i></a> <a href='#' id='wUpdateCancel"+opt.cart+"' class='wagon-update-btns' style='display:none;'><i class='fa fa-ban fa-lg'></i></a>";
			cell.html(string);
		}
		function make_add_item_row(){
			// $('#'+opt.tbl);
			var pos = 0;
			var string = "<tr id='show-input-row"+opt.cart+"'>";
				string += "<td colspan='100%'><a href='#' id='show-input"+opt.cart+"' >Add an Item</a></td>";
			string += "</tr>";

			var edit_rows = opt.tbl.find('.wagon-edit-rows');
			// alert(edit_rows.length);
			if(edit_rows.length > 0){
				ctr = 1;
				edit_rows.each(function(){
					pos = ctr;
					ctr++;
				});
			}
				
			if(!opt.removeAdd){
				opt.tbl.find('tbody > tr').eq(pos).after(string);
			}
		}
		function isNumber(n) {
 			 return !isNaN(parseFloat(n)) && isFinite(n);
		}
	}
	
	$.fn.rWagonClear = function(options) {
		var opt = $.extend({
			cart      		: 	'',
			tbl  	  		: 	$(this),
			beforeClear		:	null, 
			onClear  		:	function(response){}
		}, options);

		var before = true;
		var ret = null;
		if($.isFunction(opt.beforeClear)){
			var ret = opt.beforeClear.call(this);
		}
		if(ret != null){
			before = ret;
		}

		if(before){
			opt.tbl.find(".line-row-"+opt.cart).hide();
			$.post(baseUrl+'wagon/clear_wagon/'+opt.cart,function(data){
				opt.tbl.find(".line-row-"+opt.cart).remove();
			});	
		}
	}	
}(jQuery));