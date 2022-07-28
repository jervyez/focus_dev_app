<div class="col-sm-7 pad-10">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#cqr_view" data-toggle="tab" class="view_cqr_list" onclick="view_send_contractor()"><i class="fa fa-file-pdf-o"></i> CQR</a>
		</li>
		<li>
			<a href="#cpo_view" onclick="view_send_contractor_cpo();" data-toggle="tab" id="tab_send_contractor_cpo"><i class="fa fa-file-pdf-o"></i> CPO</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in  clearfix active" id="cqr_view">
			<div class="pad-10">				
				<form method="post" id="cqr_form" class="m-top-5"  action="<?php echo base_url(); ?>etc/process_cqr">
					<div class="m-top-10" id="contractor_list" style = "height: 400px; overflow: auto"></div>
					<div id="" class="hide">
						<input type="text" name="set_cc_emails" class="set_cc_emails">
						<input type="text" name="set_bcc_emails" class="set_bcc_emails">
						<textarea name="extra_msg" class="extra_msg" rows="4" cols="50"></textarea>
						<input type="text" name="project_id" value="<?php echo $project_id; ?>">
						<input type="hidden" name="set_attach_mss" class="set_attach_mss" value="0">
						<button type="submit" class="set_form_email">Submit</button>
					</div>
				</form>
			</div>
		</div>

		<div class="tab-pane fade in  clearfix" id="cpo_view">
			<div class="pad-10">				
				<form method="post" id="cpo_form" class="m-top-5"  action="<?php echo base_url(); ?>etc/process_cpo">
					<div class="m-top-10" id="contractor_list_cpo" style="height: 400px; overflow: auto"></div>
					<div id="" class="hide">
						<input type="text" name="set_cc_emails" class="set_cc_emails">
						<input type="text" name="set_bcc_emails" class="set_bcc_emails">
						<textarea name="extra_msg" class="extra_msg" rows="4" cols="50"></textarea>	
						<input type="text" name="project_id" value="<?php echo $project_id; ?>">
<?php if($job_category == "Maintenance"): ?>
						<input type="hidden" name="set_attach_mss" class="set_attach_mss" value="1">
<?php else: ?>
						<input type="hidden" name="set_attach_mss" class="set_attach_mss" value="0">
<?php endif; ?>

						<button type="submit" class="set_form_email">Submit</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div class="col-sm-5 pad-10">

	<div id="pad-5" style="margin-top: 10px;border-bottom: 1px solid #ccc;padding-bottom: 10px;margin-bottom: 10px;">
		<strong id="" class="" style="font-size: 15px;">Email Form</strong>
		<?php if($job_category == "Maintenance"): ?>
			<div class="pull-right cpo_attach_mss" style="display: none;">
				<label for="check_attach_mss" class="pull-right" style="">Attach Maintenance Site Sheet &nbsp;
					<input type="checkbox" id="check_attach_mss" class="pad-left-10" value="1" checked>
				</label>
			</div>
		<?php endif; ?>

	</div>

	<?php if($is_printed == 1): ?>
		<div class="col-sm-12"><label for="" class = "pull-right show_attach_project_sched">Attach Project Schedule <input type="checkbox" id = "check_attach_ps"></label></div>
	<?php endif; ?>

	<?php /*nif($job_category == "Maintenance"): ?>
	 	<div class="col-sm-12"><label for="" class = "pull-right show_attach_mss">Attach Maintenance Site Sheet <input type="checkbox" id = "check_attach_mss"></label></div>
	<?php endif; */ ?>


	  
	<div class="col-sm-12 m-bottom-10">
		<div class="input-group" style="border: 1px solid #ccc; border-radius: 6px; height: 34px;">
			<span class="input-group-addon border-0" >CC</span>		
			<div class="email-container pull-left clearfix pad-5">
				<input type="email" placeholder="CC Emails" class="pull-left prep_email" id="set_cc_emails" autocomplete="off" name="set_cc_emails" style="border:none;width: 300px;">
			</div>
		</div>
	</div>

	<div class="col-sm-12 m-bottom-10">
		<div class="input-group" style="border: 1px solid #ccc; border-radius: 6px; height: 34px;">
			<span class="input-group-addon border-0" >BCC</span>		
			<div class="email-container pull-left clearfix pad-5">
				<input type="email" placeholder="BCC Emails" class="pull-left prep_email" autocomplete="off" id="set_bcc_emails" name="set_bcc_emails" style="border:none;width: 300px;">
			</div>
		</div>
	</div>
  

	<div class="col-sm-12 m-bottom-10">
		<textarea id="contractor_email_message" class="form-control" style="height: 200px" placeholder="Extra Message"></textarea>
	</div>

	<div class="btn btn-primary pull-left m-left-5" id="process_send_email" >Process and Send</div>
	



</div>
<script type="text/javascript">

 

	$('#tab_send_contractor_cpo').click(function(){

		setTimeout(function(){ 
			$('.cpo_attach_mss').show();
		}, 250);
	});


	
 

	$('.view_cqr_list').click(function(){
		$('.cpo_attach_mss').hide();
	})

	setTimeout(function(){ 
		$('.view_cqr_list').trigger('click');
	}, 2000);


	$("#contractor_alt_email_add").blur(function(){
		var email = $("#contractor_alt_email_add").val();
		email = email.replace(/\s/g,'');
		email = email.replace(/\;/g, ',');
		$("#contractor_alt_email_add").val(email);
	});

	$("#contractor_cc").blur(function(){
		var email = $("#contractor_cc").val();
		email = email.replace(/\s/g,'');
		email = email.replace(/\;/g, ',');
		$("#contractor_cc").val(email);
	});

	$("#contractor_bcc").blur(function(){
		var email = $("#contractor_bcc").val();
		email = email.replace(/\s/g,'');
		email = email.replace(/\;/g, ',');
		$("#contractor_bcc").val(email);
	});

	var is_cqr = 1;
	var is_cpo = 0;
	var active_tab = '';

	$('.view_cqr_list').click(function(){
		is_cqr = 1;
		is_cpo = 0;
		active_tab = '#cqr_view';
		document.getElementById("cqr_form").reset();
		document.getElementById("cpo_form").reset();
		$('.prep_email, #contractor_email_message, .extra_msg').val('');
		$('.inputted_email_btn').remove();
	});

	$('#tab_send_contractor_cpo').click(function(){
		is_cqr = 0;
		is_cpo = 1;
		active_tab = '#cpo_view';
		document.getElementById("cqr_form").reset();
		document.getElementById("cpo_form").reset();
		$('.prep_email, #contractor_email_message, .extra_msg').val('');
		$('.inputted_email_btn').remove();
	});





	$('#process_send_email').click(function(){

		var inputted_cc_email = $('input#set_cc_emails').val();
		var current_cc_emails = $(active_tab+' input.set_cc_emails').val();
		

		if(current_cc_emails.length == 0){
			var new_set_CCemail = inputted_cc_email;
		}else{
			var new_set_CCemail = current_cc_emails+','+inputted_cc_email;
		}

		$(active_tab+' input.set_cc_emails').val(new_set_CCemail);

		 
		var inputted_bcc_email = $('input#set_bcc_emails').val();
		var current_bcc_emails = $(active_tab+' input.set_bcc_emails').val();

		if(current_bcc_emails.length == 0){
			var new_set_BCCemail = inputted_bcc_email;
		}else{
			var new_set_BCCemail = current_bcc_emails+','+inputted_bcc_email;
		}

		<?php if($job_category == "Maintenance"): ?>
			var remember = document.getElementById("check_attach_mss");
			$('input.set_attach_mss').val('1');
		<?php else: ?>
			$('input.set_attach_mss').val('0');
		<?php endif; ?>

		$(active_tab+' input.set_bcc_emails').val(new_set_BCCemail);
		$(active_tab+' textarea.extra_msg').val(  $('#contractor_email_message').val()   );

		$('#loading_modal').modal({"backdrop": "static", "show" : true} );

		setTimeout(function(){ 
			$(active_tab+" button.set_form_email").trigger('click');
		}, 1000);

	});


	$('#process_send_wo_email').click(function(){
		$('input#set_cc_emails').val('');
		$('input#set_bcc_emails').val('');
		$(active_tab+' textarea.extra_msg').val('');
		$(active_tab+' input.set_bcc_emails').val('');
		$(active_tab+' input.set_cc_emails').val('');

		$('#loading_modal').modal({"backdrop": "static", "show" : true} );

		setTimeout(function(){ 
			$(active_tab+" button.set_form_email").trigger('click');
		}, 1000);
	});










 

	$('input.prep_email').keyup(function(e){

		var inputted_email = $(this).val();
		var input_email_type = $(this).attr('id');

		inputted_email = inputted_email.replace(";", "");
		inputted_email = inputted_email.replace(",", "");
		inputted_email = inputted_email.replace(" ", "");

		var currnt_emails = $(active_tab+' input.'+input_email_type).val();

		if(currnt_emails.length == 0){
			var new_set_email = inputted_email;
		}else{
			var new_set_email = currnt_emails+','+inputted_email;
		}

		if(inputted_email.length > 0){

			if(e.keyCode == 186){
				$(active_tab+' input.'+input_email_type).val(new_set_email);
				$(this).val('');
				$(this).parent().prepend('<button type="button" id="'+inputted_email+'" value="'+inputted_email+'" class="m-right-5 btn btn-default btn-xs inputted_email_btn pull-left m-bottom-5" onclick="remove_prep_email(this)">'+inputted_email+'</button>');
			}
			if(e.keyCode == 188){
				$(active_tab+' input.'+input_email_type).val(new_set_email);
				$(this).val('');
				$(this).parent().prepend('<button type="button" id="'+inputted_email+'" value="'+inputted_email+'" class="m-right-5 btn btn-default btn-xs inputted_email_btn pull-left m-bottom-5" onclick="remove_prep_email(this)">'+inputted_email+'</button>');
			}
			if(e.keyCode == 32){
				$(active_tab+' input.'+input_email_type).val(new_set_email);
				$(this).val('');
				$(this).parent().prepend('<button type="button" id="'+inputted_email+'" value="'+inputted_email+'" class="m-right-5 btn btn-default btn-xs inputted_email_btn pull-left m-bottom-5" onclick="remove_prep_email(this)">'+inputted_email+'</button>');
			} 
		}


	});


function remove_prep_email(obj){
	var elm_val = obj.value;

	var input_email_type = $(obj).parent().find('input').attr('id');
	var email_list_raw = $(active_tab+' input.'+input_email_type).val();
 	var email_list_arr = email_list_raw.split(',');

	for(var i = email_list_arr.length - 1; i >= 0; i--) {
		if(email_list_arr[i] === elm_val) {
			email_list_arr.splice(i, 1);
		}
	}

	var new_set_array_email = email_list_arr.join(',');
	$(active_tab+' input.'+input_email_type).val(new_set_array_email);

	var element = document.getElementById(elm_val);
	element.outerHTML = "";
	delete element;
}




</script>