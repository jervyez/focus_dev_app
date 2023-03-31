<?php use App\Modules\Company\Controllers\Company; ?>
<?php $this->company = new Company(); ?>

<?php use App\Modules\Projects\Controllers\Projects; ?>
<?php $this->projects = new Projects(); ?>

<?php use App\Modules\Users\Controllers\Users; ?>
<?php $this->users = new Users(); ?>

<?php use App\Modules\Shopping_center\Controllers\Shopping_center; ?>
<?php $this->shopping_center = new Shopping_center(); ?>

<?php use App\Modules\Admin\Controllers\Admin; ?>
<?php $this->admin = new Admin(); ?>

<script type="text/javascript">
function check_required_fields(){

	event.preventDefault();
	var error_msg = '';

	var clientn_type = $('select.slct_client_type').val();
 
	if(  $('#project_name').val() == null || $('#project_name').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Project name is missing</strong></li>';
	}

 	if(clientn_type == 1){
 		if(  $('#pending_comp_id').val() == null || $('#pending_comp_id').val().length === 0 ){
 			error_msg = error_msg+'<li><strong class="text-danger">No pending client company selected</strong></li>';
 		}
	}else{
 		if(  $('#company_prg').val() == null || $('#company_prg').val().length === 0 ){
 			error_msg = error_msg+'<li><strong class="text-danger">No client company selected</strong></li>';
 		}
		
 		if(  $('#contact_person').val() == null || $('#contact_person').val().length === 0 ){
 			error_msg = error_msg+'<li><strong class="text-danger">No client contact person selected</strong></li>';
 		}
	}

	if(  $('#brand').val() == null || $('#brand').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">No brand selected</strong></li>';
	}

	if(  $('#job_type').val() == null || $('#job_type').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">No Job Type selected</strong></li>';
	}

	if(  $('#job_category').val() == null || $('#job_category').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">No Job Category selected</strong></li>';
	}

	if(  $('#site_start').val() == null || $('#site_start').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">No Site Start date selected</strong></li>';
	}

	if(  $('#site_finish').val() == null || $('#site_finish').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">No Site Finish date selected</strong></li>';
	}

	if(  $('#project_total').val() == null || $('#project_total').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Project total estimate needs a value</strong></li>';
	}

	if(  $('#labour_hrs_estimate').val() == null || $('#labour_hrs_estimate').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Site labour estimate needs a value</strong></li>';
	}

	if(  $('#project_markup').val() == null || $('#project_markup').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Project mark-up needs a value</strong></li>';
	}

	var job_category = $('#job_category').val();

	if($('#job_category').val().length > 0){
		if(job_category != 'Strip Out' && job_category != 'Minor Works' && job_category != 'Maintenance'){
			if(  $('#project_area').val() == null || $('#project_area').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Project area needs a value</strong></li>';
			}
		}
	}

	var job_type = $('#job_type').val();

	if($('#job_type').val().length > 0){
		if(job_type == 'Shopping Center'){
			
			if(  $('#selected_shopping_center_detail').val() == null || $('#selected_shopping_center_detail').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Please select your shopping centre</strong></li>';
			}

			if(  $('#shop_tenancy_number').val() == null || $('#shop_tenancy_number').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Shop tenancy number is required</strong></li>';
			}
		}else{

			if(  $('#street').val() == null || $('#street').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Site Address: Street is required</strong></li>';
			}

			if(  $('#state_a').val() == null || $('#state_a').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Site Address: State is required</strong></li>';
			}

			if(  $('#suburb_a').val() == null || $('#suburb_a').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Site Address: Suburb is required</strong></li>';
			}

			if(  $('#postcode_a').val() == null || $('#postcode_a').val().length === 0 ){
				error_msg = error_msg+'<li><strong class="text-danger">Site Address: Postcode is required</strong></li>';
			}
		}
	}

	if(  $('#project_manager').val() == null || $('#project_manager').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Please select your Project Manager</strong></li>';
	}

	if(  $('#project_administrator').val() == null || $('#project_administrator').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Please select your Project Administrator</strong></li>';
	}

	if(  $('#estimator').val() == null || $('#estimator').val().length === 0 ){
		error_msg = error_msg+'<li><strong class="text-danger">Please select your Estimator</strong></li>';
	}

	var leading_hand = $('select#leading_hand').val();

	var leading_text = $('select#leading_hand option:selected').text();

 
	if(leading_text == 'Other'){

		if(  $('#lh_name').val() == null || $('#lh_name').val().length === 0 ){
			error_msg = error_msg+'<li><strong class="text-danger">Other Leading Hand: Full name is required</strong></li>';
		}

		if(  $('#lh_mobile_no').val() == null || $('#lh_mobile_no').val().length === 0 ){
			error_msg = error_msg+'<li><strong class="text-danger">Other Leading Hand: Mobile number is required</strong></li>';
		}
	}
 
	if(error_msg.length === 0 ){
		$('#loading_modal').modal({"backdrop": "static", "show" : true} );
		$( ".add_new_project_form" ).submit();
	}else{
		$('.area_form').html(error_msg);
		$('#form_error_modal').modal({"backdrop": "static", "show" : true} );
	}

}
</script> 

<?php $this->users->_check_user_access('projects',2); ?>

<script src="<?php echo site_url(); ?>js/vue.js"></script>
<script src="<?php echo site_url(); ?>js/vue-select.js"></script>
<link rel="stylesheet" href="<?php echo site_url(); ?>css/vue-select.css">
<script src="<?php echo site_url(); ?>js/moment.min.js"></script>
<script src="<?php echo site_url(); ?>js/jmespath.js"></script>
<script src="<?php echo site_url(); ?>js/axios.min.js"></script>
<!-- title bar -->
<div class="container-fluid head-control">
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-6 col-sm-4 col-xs-12 pull-left">
				<header class="page-header">
					<h3><?php $datestring = "l, F d, Y"; $time = time(); //use time() for timestamp  ?>
						<?php echo $screen; ?> Screen<br><small><?php echo date($datestring, $time); #echo date("l, F d, Y"); ?></small>
					</h3>
				</header>
			</div>

			<div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
				<ul class="nav nav-tabs navbar-right">
					<li>
						<a href="<?php echo site_url(); ?>"><i class="fa fa-home"></i> Home</a>
					</li>
					<li>
						<a href="<?php echo site_url(); ?>projects" class="btn-small">Projects</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- title bar -->

<div id="proj_app">
<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">				
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<form class="form-horizontal add_new_project_form" role="form" method="post" action="">				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-10">
							<div class="left-section-box">				
					
								<?php if(@$form_error): ?>
								<div class="pad-10 no-pad-t">
									<div class="border-less-box alert alert-danger fade in">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<h4>Oh snap! You got an error!</h4>
										<?php echo $form_error;?>
									</div>
								</div>
								<?php endif; ?>


 

					
								
									<div class="box-head pad-10 clearfix">
										<div class="pull-right">

											<button  type="reset" class="btn btn-warning pull-right">Reset Form</button ><br />
											<select name="focus" class="form-control focus m-top-10 select-focus" id="focus" required="">
												<option value="" class="comp_0">Select Focus Company</option>
												<?php foreach ($focus as $key => $value): ?>
													<option value="<?php echo $value->company_id; ?>" class="comp_<?php echo $value->company_id; ?>"><?php echo $value->company_name; ?></option>
												<?php endforeach; ?>
											</select>	


											<script type="text/javascript">
												$(document).ready(function() {
													$('.focus').val('<?php echo ($this->request->getPost('focus') ? $this->request->getPost('focus') : $this->session->get('user_focus_company_id')); ?>').trigger('change');
												});
											</script>

											<?php 

												if($this->session->get('user_focus_company_id') == 3197){
												//	echo ' <style type="text/css"> option.comp_0,option.comp_4,option.comp_5, option.comp_6{display: none; visibility: hidden;}</style> ';
													echo ' <style type="text/css">	.maintenance_site_cont_form{display:  block !important;	}</style> ';
												}


											 ?>



											 
											

										</div>	
										<label>Add New Project</label>

<span class="fa fa-film pointer open_help_vids_mpd" data-toggle="modal" data-target="#help_video_group"> </span>

										


										<p>Fields having * is requred.</p>	
										<!-- <p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>						 -->
									</div>
									
									<div class="box-area pad-10 clearfix">											
										
										<div class="form-group clearfix pad-5 no-pad-b">
	        								<div class="col-sm-6">
	         									<div class="input-group <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_name') ? 'has-error' : '';  ?>">
													<span class="input-group-addon"><i class="fa fa-briefcase  fa-lg"></i></span>
													<input type="text" class="form-control" required placeholder="Project Name*" name="project_name" id="project_name" maxlength="35" tabindex='1' value="<?php echo $this->request->getPost('project_name'); ?>">
													<span class="input-group-addon char-counter-remains"> </span>
												</div>
	        								</div>

	        								<div class="col-sm-6 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_date') ? 'has-error' : '';  ?>">
												<label for="project_date" class="col-sm-3 control-label">Project Date*</label>
												<div class="col-sm-9">
													<input id="project_date" class="project_date form-control" name="project_date" readonly="readonly" type="text" value="<?php echo date("d/m/Y"); ?>">
												</div>	
											</div>
	      								</div>	      								
	      								
	      								<div class="box m-bottom-15 clearfix">
											<div class="box-head pad-5 m-bottom-5">
												<label><i class="fa fa-book fa-lg"></i> General</label>

												<select class="input-sm pull-right slct_client_type" v-model="sel_client_type" v-on:change="select_client_type" name="client_type">
													<option value="0">Existing Client</option>
													<option value="1">Pending Client</option>
												</select>
											</div>
											
											<div class="box-area pad-5 clearfix">

																		 
																											
												<div v-show="client_type">												
												
													<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('company_prg') ? 'has-error' : '';  ?>">
														<label for="company_prg" class="col-sm-3 control-label">Client*</label>
														<div class="col-sm-9">
															<div class="input-group">
																<span class="input-group-addon"><i class="fa fa-briefcase  fa-lg"></i></span>
																<select name="company_prg" class="form-control chosen find_contact_person get_address_invoice" id="company_prg" required style="width: 100%;" tabindex="2">
																	<option value=''>Select Client Name*</option>																												
																	<?php $this->company->company_list('dropdown'); ?>
																</select>
																<script type="text/javascript">$('select#company_prg').val('<?php echo $this->request->getPost('company_prg'); ?>');</script>
															</div>
														</div>
													</div>
													
													<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('contact_person') ? 'has-error' : '';  ?>">
														<label for="contact_person" class="col-md-3 col-sm-5 control-label">Contact Person*</label>
														<div class="col-md-9 col-sm-7 here">
															<select name="contact_person" class="form-control"  id="contact_person" style="width: 100%;"  tabindex="26">		
																<option value=''>Select Contact Person*</option>
																<?php if($this->request->getPost('company_prg')): ?>
																	<?php $comp_arr = explode('|', $this->request->getPost('company_prg')); ?>		
																	<?php $this->projects->find_contact_person($comp_arr[1]); ?>
																<?php endif; ?>
															</select>
															<script type="text/javascript">$('select#contact_person').val('<?php echo $this->request->getPost('contact_person'); ?>');</script>
														</div>
													</div>
												</div>

												<div v-show="!client_type">
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="pending_comp_id" class="col-sm-3 control-label">Pending Client* </label>
														<div class="col-sm-9">	
															<input type="text" name="pending_comp_id" id="pending_comp_id" style="color: #fff; border: 0; height: 0;">													
															<v-select name = "company_prg_pending" v-model = "temp_company_id" :options="options" id="company_prg_pending" @input="select_pending_client"></v-select>
														</div>
													</div>
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="cont_person" class="col-sm-3 control-label">Contact Person* </label>
														<div class="col-sm-9">														
															<input type="text" class="form-control input-sm" id="cont_person" disabled="" v-model="cont_person">
														</div>
													</div>
												</div>

												
												<div class="col-sm-6 m-bottom-10 hide clearfix">
													<label for="sub_client" class="col-sm-3 control-label">Sub-Client</label>
													<div class="col-sm-9">		  
															<select name="sub_client" class="form-control sub_client" id="sub_client" style="width: 100%;" tabindex="2">
																<option selected value=''>None</option>		 
															</select>
															<script type="text/javascript">$('select#sub_client').val('<?php echo $this->request->getPost('sub_client'); ?>');</script>
													 
													</div>
												</div>

												<div class="clearfix"></div>

												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('brand_name') ? 'has-error' : '';  ?>">
													<label for="brand" class="col-sm-3 control-label">Brand*</label>
													<div class="col-sm-9">		  
															<select name="brand_name" required class="form-control brand" id="brand" style="width: 100%;" tabindex="2">
																<option value=''>Select Brand</option>
																<?php echo $this->projects->list_all_brands('select'); ?>	 
															</select>
															<script type="text/javascript">$('select#brand').val('<?php echo $this->request->getPost('brand_name'); ?>');</script>
													</div>
												</div>



												<div class="clearfix"></div>
												
												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="client_po" class="col-sm-3 control-label">Client PO</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="client_po" placeholder="Client PO" tabindex='3' name="client_po" value="<?php echo $this->request->getPost('client_po'); ?>">
													</div>
												</div>
												
												<div class="col-sm-6 m-bottom-10 clearfix" >
													<label for="job_date" class="col-sm-3 control-label"><i class="fa fa-calendar"></i> Job Date</label>
													<div class="col-sm-9">
														<input disabled="disabled" type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" title="Warning: Changing a value in the the Job date affects the project in the WIP section." class="tooltip-enabled job-date-set form-control datepicker" id="job_date" name="job_date" value="<?php echo $this->request->getPost('job_date'); ?>">
													</div>													
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('job_type') ? 'has-error' : '';  ?>">
													<label for="job_category" class="col-sm-3 control-label">Job Type*</label>													
													<div class="col-sm-9  col-xs-12">
														<select class="form-control" required id="job_type" name="job_type" tabindex='4'>
															<option value="" class="hide">Choose a Job Type...</option>
															<option value="Shopping Center">Shopping Center</option>
															<option value="Street Site">Street Site</option>
															<option value="Office">Office</option>															
														</select>
														<script type="text/javascript">$("select#job_type").val("<?php echo $this->request->getPost('job_type'); ?>");</script>
													</div>
												</div>
												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('job_category') ? 'has-error' : '';  ?>">
													<label for="job_category" class="col-sm-3 control-label">Job Category*</label>													
													<div class="col-sm-9  col-xs-12">

														<select class="form-control tooltip-test" required id="job_category" name="job_category" tabindex='4' data-original-title="On selecting the Job Category, Project Area is only optional if Job Category is Strip Out, Minor Works or Maintenance.">															
															
															<option class="def_slct_optn" value="Design Works">Design Works</option>
															<option class="def_slct_optn" value="Joinery Only">Joinery Only</option>
															<option class="def_slct_optn" value="Kiosk">Kiosk</option>
															<option class="def_slct_optn" value="Full Fitout">Full Fitout</option>
															<option class="def_slct_optn" value="Refurbishment">Refurbishment</option>
															<option class="def_slct_optn" value="Strip Out">Strip Out</option>
															<option class="def_slct_optn" value="Minor Works">Minor Works (Under $20,000.00)</option>
															<option class="main_slct_optn" value="Maintenance">Maintenance</option>
															<option value="" selected="" class="hide">Choose a Job Category</option>
														</select>
														<script type="text/javascript">$("select#job_category").val("<?php echo $this->request->getPost('job_category'); ?>");</script>


													</div>
												</div>

												<div class="maintenance_site_cont_form p-top-10 p-bottom-10">
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="site_start" class="col-sm-4 control-label">Site Contact Person</label>
														<div class="col-sm-8">
															<input type="text" placeholder="Contact Person Name" class="form-control" id="site_cont_person" name="site_cont_person" value="<?php echo ($this->request->getPost('site_cont_person') ?  $this->request->getPost('site_cont_person') : '' ); ?>">
														</div>
													</div>

													<div class="col-sm-6 m-bottom-10 clearfix ->hasError('site_cont_number') ? 'has-error' : '';  ?>">
														<label for="site_start" class="col-sm-4 control-label">Site Contact Number</label>
														<div class="col-sm-8">
															<input type="text" placeholder="Contact Number" class="form-control" id="site_cont_number" name="site_cont_number" value="<?php echo ($this->request->getPost('site_cont_number') ?  $this->request->getPost('site_cont_number') : '' ); ?>">
														</div>
													</div>

													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="site_start" class="col-sm-4 control-label">Site Contact Mobile</label>
														<div class="col-sm-8">
															<input type="text" placeholder="Contact Person Mobile" class="form-control" id="site_cont_mobile" name="site_cont_mobile" value="<?php echo ($this->request->getPost('site_cont_mobile') ?  $this->request->getPost('site_cont_mobile') : '' ); ?>">
														</div>
													</div>

													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="site_start" class="col-sm-4 control-label">Site Contact E-mail</label>
														<div class="col-sm-8">
															<input type="text" placeholder="Contact E-mail" class="form-control" id="site_cont_email" name="site_cont_email" value="<?php echo ($this->request->getPost('site_cont_email') ?  $this->request->getPost('site_cont_email') : '' ); ?>">
														</div>
													</div>
												</div>


												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('site_start') ? 'has-error' : '';  ?>">
													<label for="site_start" class="col-sm-3 control-label">Site Start*</label>
													<div class="col-sm-9">
<input tabindex='6' type="text" placeholder="DD/MM/YYYY" class="form-control" id="site_start" required name="site_start" tabindex="4" value="<?php echo $this->request->getPost('site_start'); ?>" autocomplete="off">
													</div>
												</div>
											



												<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('site_finish') ? 'has-error' : '';  ?>">
													<label for="site_finish"  class="col-sm-3 control-label">Site Finish*</label>
													<div class="col-sm-9 col-xs-12">
<input tabindex='7' type="text" placeholder="DD/MM/YYYY" class="form-control" id="site_finish" required name="site_finish" tabindex="4" value="<?php echo $this->request->getPost('site_finish'); ?>" autocomplete="off">
													</div>
												</div>
											</div>
										</div>

										<div class="box">
											<div class="box-head pad-5 m-bottom-5">
												<label><i class="fa fa-bars fa-lg"></i> Project Details</label>
											</div>
											
											<div class="box-area pad-5 clearfix">

												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="install_hrs" class="col-sm-3 control-label">Site Hours</label>
													<div class="input-group ">
														<span class="input-group-addon">(Hrs)</span>
														<input type="text" name="install_hrs" id="install_hrs" class="form-control" tabindex="8" placeholder="Install Hrs" value="<?php echo ($this->request->getPost('install_hrs') ?  $this->request->getPost('install_hrs') : '0' ); ?>"/>
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="is_double_time" class="col-sm-3 control-label">Is Double Time?</label>
													<div class="col-sm-9  col-xs-12">
														<select name="is_double_time" class="form-control" id="is_double_time" style="width: 100%;" tabindex="9">
															<option value="0">No</option>
															<option value="1">Yes</option>
														</select>
														<script type="text/javascript">$('select#is_double_time').val('<?php echo ($this->request->getPost('is_double_time') ?  $this->request->getPost('is_double_time') : '0' ); ?>');</script>
													</div>
												</div>
													
												<div class="clearfix"></div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_total') ? 'has-error' : '';  ?>">
													<label for="project_total" class="col-sm-3 control-label ">Project Estimate* </label>
													<div class="input-group ">
														<span class="input-group-addon">($)</span>
														<?php $est_amt = str_replace (',','', $this->request->getPost('project_total') ); ?>
														<input type="text" name="project_total" required id="project_total" class="form-control number_format tooltip-test" data-original-title = "This must be an accurate estimate" tabindex="10" placeholder="Project Estimate" value="<?php echo ($this->request->getPost('project_total') && $this->request->getPost('project_total')!=0 ?  number_format($est_amt) : '' ); ?>"/>
														<script type="text/javascript">
															$("#project_total").blur(function(){
																var proj_estimate = $("#project_total").val();
																if(proj_estimate < 11){
																	alert("Estimate must be an accurate estimate");
																	$("#project_total").val("");
																}
															});
														</script>
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('labour_hrs_estimate') ? 'has-error' : 'green-estimate';  ?>">
													<label for="labour_hrs_estimate" class="col-sm-4 control-label text-center">Site Labour Estimate* </label>
													<div class="input-group ">
														<span class="input-group-addon">(Hrs)</span>
														<input type="text" required name="labour_hrs_estimate" id="labour_hrs_estimate" class="form-control" tabindex="11" placeholder="Site Labour Estimate" value="<?php echo ($this->request->getPost('labour_hrs_estimate') ?  $this->request->getPost('labour_hrs_estimate') : '' ); ?>"/>
													</div>
												</div>


												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_markup') ? 'has-error' : '';  ?>">
													<label for="project_markup" class="col-sm-3 control-label">Project Markup* </label>
													<div class="input-group ">
														<span class="input-group-addon">(%)</span>
														<input type="text" name="project_markup" required id="project_markup" class="form-control" tabindex="12" placeholder="Markup %" value="<?php echo $this->request->getPost('project_markup'); ?>" />
														<p class="min_mark_up hidden"></p>
													</div>

												</div>

												<input type="hidden" name="min_mark_up" value="" class="min_mark_up">											
													
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_area') ? 'has-error' : '';  ?>">
													<label for="project_area" class="col-sm-3 control-label text-center">Project Area*</label>
													<div class="col-sm-9">												

														<div class="input-group ">
															<span class="input-group-addon">SQM</span>
															<input type="text" name="project_area" id="project_area" class="form-control tooltip-test" data-original-title="Project Area is only optional if Job Category is Strip Out, Minor Works or Maintenance." tabindex="13" placeholder="Project Area" value="<?php echo $this->request->getPost('project_area'); ?>"/>
														</div>
													</div>
												</div>


											</div>
										</div>

										<div class="clearfix" style = "height: 20px"></div>

										<div class="box-tabs m-bottom-10">
											<ul id="myTab" class="nav nav-tabs">
												<li class="active">
													<a href="#physicalAddress" data-toggle="tab"><i class="fa fa-globe fa-lg"></i> Site Address</a>
												</li>

												<li class="" v-show="client_type">
													<a href="#postalAddress" data-toggle="tab"  tabindex="20" ><i class="fa fa-inbox fa-lg"></i> Invoice Address</a>
												</li>
												<input type="hidden" name="is_form_submit" value="1">																					
											</ul>
											<div class="tab-content">
												<div class="tab-pane fade active in clearfix" id="physicalAddress">
													<div class="site_address" <?php echo ($this->request->getPost('is_shopping_center')==1 ?  'style="display:none;"' : '' ); ?>>

													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="unit_level" class="col-sm-3 control-label">Unit/Level</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="unit_level" placeholder="Unit/Level" name="unit_level" tabindex="14" value="<?php echo $this->request->getPost('unit_level'); ?>">
														</div>
													</div>

													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="number" class="col-sm-3 control-label">Number</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="number" placeholder="Number" tabindex="15" name="unit_number" value="<?php echo $this->request->getPost('unit_number'); ?>">
														</div>
													</div>

													<div class="col-sm-6 m-bottom-10 clearfix  <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('street') ? 'has-error' : '';  ?>">
														<label for="street" class="col-sm-3 control-label">Street*</label>
														<div class="col-sm-9">
															<input type="text"  class="form-control" id="street" placeholder="Street" tabindex="16" name="street" value="<?php echo $this->request->getPost('street'); ?>">
														</div>
													</div>

													<div class="clearfix"></div>

													<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('state_a') ? 'has-error' : '';  ?>">
														<label for="state" class="col-sm-3 control-label">State*</label>													
														<div class="col-sm-9">
															<select class="form-control state-option-a chosen"   tabindex="17" id="state_a" name="state_a">
																<?php if($this->request->getPost('focus') && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
																	<?php echo $this->projects->set_jurisdiction($this->request->getPost('focus')); ?>
																<?php else: ?>
																	<?php echo $this->projects->set_jurisdiction($this->session->get('user_focus_company_id')); ?>
																	<?php //foreach ($all_aud_states as $row){ echo '<option value="'.$row->shortname.'|'.$row->name.'|'.$row->phone_area_code.'|'.$row->id.'">'.$row->name.'</option>'; }?>
																<?php endif;  ?>
															</select>
															<script type="text/javascript">$("select#state_a").val("<?php echo $this->request->getPost('state_a'); ?>");</script>
														</div>
													</div>

													<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('suburb_a') ? 'has-error' : '';  ?>">
														<label for="suburb" class="col-sm-3 control-label">Suburb*</label>
														<div class="col-sm-9 col-xs-12">

															<?php if($this->request->getPost('suburb_a')): ?>
																<select  class="form-control suburb-option-a chosen" id="suburb_a"  tabindex='18' name="suburb_a">

																<?php else: ?>
																	<select  class="form-control suburb-option-a disabled chosen"  tabindex="18"  id="suburb_a" name="suburb_a">
																		<option value="">Choose a Suburb...</option>

																	<?php endif; ?>



																	<?php if($this->request->getPost('suburb_a')): ?>
																		<?php $this->company->get_suburb_list('dropdown|state_id|'.$this->request->getPost('state_a')); ?>														
																		<script type="text/javascript">$("select#suburb_a").val("<?php echo $this->request->getPost('suburb_a'); ?>");</script>

																	<?php endif; ?>

																</select>
															</div>
														</div>

														<div class="clearfix"></div>

												<!--
												<div id="datepicker" class="input-prepend date">
													<span class="add-on"><i class="icon-th"></i></span>
													<input class="span2" type="text" value="02-16-2012">
												</div>

												required
											-->



											<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('postcode_a') ? 'has-error' : '';  ?>">
												<label for="postcode_a" class="col-sm-3 control-label">Postcode*</label> <?php //echo $this->request->getPost('postcode_a'); ?>													
												<div class="col-sm-9  col-xs-12">



													<?php if($this->request->getPost('postcode_a')): ?>
														<select class="form-control postcode-option-a chosen" id="postcode_a"  tabindex="19" name="postcode_a">
														<?php else: ?>
															<select class="form-control postcode-option-a disabled chosen"  tabindex="19"  id="postcode_a" name="postcode_a">
																<option value="">Choose a Postcode...</option>
															<?php endif; ?>

															<?php if($this->request->getPost('postcode_a')): ?>
																<?php $suburb_a = explode('|', $this->request->getPost('suburb_a')); ?>
																<?php $this->company->get_post_code_list($suburb_a[0]); ?>													

																<script type="text/javascript">$("select#postcode_a").val("<?php echo $this->request->getPost('postcode_a'); ?>");</script>

															<?php endif; ?>		

														</select>
													</div>
												</div>

												</div>


												<div class="shopping_center" <?php echo ($this->request->getPost('is_shopping_center')==1 ?  '' : 'style="display:none;"' ); ?>>
													<input type="hidden" name="is_shopping_center" class="is_shopping_center" value="<?php echo ($this->request->getPost('is_shopping_center')==1 ?  1 : 0 ); ?>">
													<input type="hidden" name="brand_shopping_center" class="brand_shopping_center" id="brand_shopping_center" value="<?php echo $this->request->getPost('brand_shopping_center'); ?>">
													<input type="hidden" name="selected_shopping_center_detail" class="selected_shopping_center_detail" id="selected_shopping_center_detail" value="<?php echo $this->request->getPost('selected_shopping_center_detail'); ?>">													

													<div class="col-sm-3 m-bottom-5 clearfix">											
														<a href="#" data-toggle="modal" data-target="#select_shopping_center_modal" data-backdrop="static" class="btn btn-primary">Select Shopping Center</a>
													</div>

													<div class="col-sm-4 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('brand_shopping_center') ? 'has-error' : '';  ?>">											
														<p class="m-top-10">Shopping Center: 
															<?php if($this->request->getPost('selected_shopping_center_detail')): ?>
																<strong class="selected_shopping_center_text" id="selected_shopping_center_text"><?php echo $this->request->getPost('selected_shopping_center_detail'); ?></strong>
															<?php else: ?>
																<strong class="selected_shopping_center_text" id="selected_shopping_center_text">Please Select</strong>
															<?php endif; ?>															
														</p>
													</div>

													<div class="col-sm-5 m-bottom-10 clearfix  <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('shop_tenancy_number') ? 'has-error' : '';  ?>">
														<label for="client_po" class="col-sm-6 control-label">Shop/Tenancy Number*</label>
														<div class="col-sm-6">
															<input type="text" class="form-control" id="shop_tenancy_number" placeholder="Shop/Tenancy Number" tabindex="17" name="shop_tenancy_number" value="<?php echo $this->request->getPost('shop_tenancy_number'); ?>">
														</div>
													</div>

												</div>
											</div>
											<div class="tab-pane fade clearfix" id="postalAddress">
												
												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="unitlevel2" class="col-sm-3 control-label">Unit/Level</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="unitlevel2"  tabindex="20"  placeholder="Unit/Level" name="unit_level_b" value="<?php echo $this->request->getPost('unit_level_b'); ?>">
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="number2" class="col-sm-3 control-label">Number</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="number2" placeholder="Number"   tabindex="21" name="number_b" value="<?php echo $this->request->getPost('number_b'); ?>">
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('street_b') ? 'has-error' : '';  ?>">
													<label for="street2" class="col-sm-3 control-label">Street*</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="street2" placeholder="Street"   tabindex="22" name="street_b" value="<?php echo $this->request->getPost('street_b'); ?>">
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix">
													<label for="pobox" class="col-sm-3 control-label">PO Box</label>
													<div class="col-sm-9" style="z-index:0;">
														<input type="text" class="form-control" id="pobox" placeholder="PO Box" name="pobox"  tabindex="23"  style="z-index:0; background:#fff;"  value="<?php echo $this->request->getPost('pobox'); ?>">
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('state_b') ? 'has-error' : '';  ?>">
													<label for="state_b" class="col-sm-3 control-label">State*</label>
													

													<div class="col-sm-9">
														<!-- <input type="text" class="form-control" id="state_a" placeholder="State" name="state_a" value="<?php echo $this->request->getPost('state_a'); ?>"> -->

														<select class="form-control state-option-b chosen"  id="state_b"   tabindex="24" name="state_b"  >															
															<option value="">Choose a State</option>
															<?php
															foreach ($all_aud_states as $row){
																echo '<option value="'.$row->shortname.'|'.$row->name.'|'.$row->phone_area_code.'|'.$row->id.'">'.$row->name.'</option>';
															}?>


														</select>
														<script type="text/javascript">$("select#state_b").val("<?php echo $this->request->getPost('state_b'); ?>");</script>
													</div>

												</div>												
												
												<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('suburb_b') ? 'has-error' : '';  ?>">
													<label for="suburb" class="col-sm-3 control-label">Suburb*</label>
													<div class="col-sm-9 col-xs-12">

														<?php if($this->request->getPost('suburb_b')): ?>
															<select class="form-control suburb-option-b chosen" id="suburb_b" tabindex="25" name="suburb_b">

															<?php else: ?>
																<select class="form-control suburb-option-b chosen" id="suburb_b" tabindex="25" name="suburb_b">
																	<option value="">Choose a Suburb...</option>
																<?php endif; ?>


																<?php if($this->request->getPost('suburb_b')): ?>
																	<?php $this->company->get_suburb_list('dropdown|state_id|'.$this->request->getPost('state_b')); ?>														
																	<script type="text/javascript">$("select#suburb_b").val("<?php echo $this->request->getPost('suburb_b'); ?>");</script>

																<?php endif; ?>

															</select>
														</div>
													</div>

													<div class="clearfix"></div>



												<!-- <div class="col-sm-6 m-bottom-10 clearfix">
													<label for="postcode2" class="col-sm-3 control-label">Postcode*</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="postcode2" placeholder="Postcode">
													</div>
												</div> -->


												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('postcode_b') ? 'has-error' : '';  ?>">
												<label for="postcode_b" class="col-sm-3 control-label">Postcode*</label> <?php //echo $this->request->getPost('postcode_a'); ?>													
												<div class="col-sm-9  col-xs-12">



													
														<select class="form-control postcode-option-b chosen" id="postcode_b"  tabindex="26" name="postcode_b">
														<?php if($this->request->getPost('postcode_b')): ?>
														<?php else: ?>
															<option value="">Choose a Postcode...</option>
														<?php endif; ?>

														<?php if($this->request->getPost('postcode_b')): ?>
															<?php $suburb_b = explode('|', $this->request->getPost('suburb_b')); ?>
															<?php $this->company->get_post_code_list($suburb_b[0]); ?>													

															<script type="text/javascript">$("select#postcode_b").val("<?php echo $this->request->getPost('postcode_b'); ?>");</script>

														<?php endif; ?>		

														</select>
													</div>
												</div>




												</div>
											</div>
										</div>

										<div class="clearfix"></div>													
	      								
										
										<div class="box m-bottom-15 clearfix">
											<div class="box-head pad-5 m-bottom-5">
												<label><i class="fa fa-users fa-lg"></i> Personel</label>
											</div>

											<?php $comp_id_selected = ($this->request->getPost('focus') ? $this->request->getPost('focus') : $this->session->get('user_focus_company_id')); ?>
											
											<div class="box-area pad-5 clearfix">
												<div class="col-sm-3 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_manager') ? 'has-error' : '';  ?>">
													<div class="col-sm-12">
														<label for="project_manager" class="control-label">Project Manager*</label><br />
														<select name="project_manager" required class="form-control presonel_add" id="project_manager" style="width: 100%;" tabindex="27">
															<option value='' selected="" class="hide">Select Project Manager</option>
															<?php
																foreach ($project_manager as $row){
																	if( $row->user_id != 29){

																		if( $this->request->getPost('project_manager') == $row->user_id || $row->user_focus_company_id == $comp_id_selected){
																			echo '<option class="pm_comp_option pm_comp_'.$row->user_focus_company_id.'" value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																		}else{
																			echo '<option style="display:none"  class="pm_comp_option pm_comp_'.$row->user_focus_company_id.'" value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																		}
																	}
																}
															?>

															<?php
															
																foreach ($account_manager as $row){
																	if( $this->request->getPost('project_manager') == $row->user_id || $row->user_focus_company_id == $comp_id_selected){
																		echo '<option class="pm_comp_option pm_comp_'.$row->user_focus_company_id.'" value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																	}else{
																		echo '<option style="display:none"  class="pm_comp_option pm_comp_'.$row->user_focus_company_id.'" value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																	}
																}
															?>



															

															<option value="29">Maintenance Manager</option>
															
														</select>	
																							
													</div>
												</div>

												<?php // svar_dump($project_manager); ?>





			      								<div class="col-sm-3 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_admin') ? 'has-error' : '';  ?>">											
													<div class="col-sm-12">
														<label for="project_administrator" class="control-label">Project Admin*</label>
														<select name="project_admin" required class="form-control presonel_add" id="project_administrator" style="width: 100%;" tabindex="28">
															<option value='' class="hide" disabled>Select Project Admin</option>
															<?php
															foreach ($project_administrator as $row){
																echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
															}?>

															<?php
															foreach ($maintenance_administrator as $row){
																echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
															}?>
														</select>
																												
													</div>
												</div>


			      								<div class="col-sm-3 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('estimator') ? 'has-error' : '';  ?>">						
													<div class="col-sm-12">
														<label for="estimator" class="control-label">Estimator*</label>													
														<select name="estimator" required class="form-control presonel_add" id="estimator" style="width: 100%;" tabindex="29">
															<option value=''   class="hide" disabled>Select Estimator</option>
															<option value='0'>None</option>
															<?php
															foreach ($estimator as $row){
																echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
															}?>

															<?php
															foreach ($maintenance_administrator as $row){
																echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
															}?>
														</select>
														
													</div>
												</div>

												<div class="col-sm-3 m-bottom-5 clearfix">
													<div class="col-sm-12">
														<label for="client_contact_project_manager" class="control-label">FOCUS - Client Contact</label><br />
														<select name="client_contact_project_manager" class="form-control client_contact tooltip-enabled" id="client_contact_project_manager" style="width: 100%;" tabindex="27" data-original-title="Project Manager contact of the Client, Default value is selected Project Manager of the project.">
															<option value='' selected="" class="hide">Select Personnel</option>
															<!-- <option value='9'>Trevor Gamble</option> -->
															<?php
															foreach ($project_manager as $row){																
																echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
															}?>


															<?php
																foreach ($account_manager as $row){
																	echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																}
															?>


															
														</select>
														
														
<script type="text/javascript">
var is_maint_user =	<?php echo (  $this->session->get('user_role_id') == 7 ?  1 : 0 ); ?>; // == 7 == maintenance

if(is_maint_user == 1){
	$('select#project_manager').val('29');
	//$('select#client_contact_project_manager').val('29');
}else{
	$('select#project_manager').val('<?php echo $this->request->getPost('project_manager'); ?>');
	$('select#client_contact_project_manager').val('<?php echo $this->request->getPost('client_contact_project_manager'); ?>'); 
}

$("select.select-focus").on("change", function(e) { 

	if(is_maint_user == 1){
		setTimeout(function(){
			$('select#project_manager').val('29').trigger('change');
			$('select#client_contact_project_manager').val('29').trigger('change');
		}, 1000); // on 1 second
	}

	var selct_foc = $(this).val();
	var selct_jbcat = $("select#job_category").val();


	if(selct_foc == 3197){
		
		setTimeout(function(){ 
			$("select#job_category").val('Maintenance').trigger('change');
		},800);

		setTimeout(function(){ 
			$('select#project_administrator').val('8').trigger('change');
		},800);

		setTimeout(function(){
			$('select.presonel_add#project_manager').val('29').trigger('change'); 
		},500);


	//	$('option.main_slct_optn').show();
	//	$('option.def_slct_optn').hide();
	//	$('select#job_category').val('');
	}else if(selct_foc != 3197 && selct_jbcat == 'Maintenance'){
		$('select#job_category').val('');
		$('input#project_markup').val('');
		$('select#estimator').val('');
		$('select#client_contact_project_manager').val('');
		$('select#project_administrator').val('');



	}else{
	//	$('select#job_category').val('');



	//	$('option.main_slct_optn').hide();
	//	$('option.def_slct_optn').show();
	}

});

$("select#job_category").on("change", function(e) {

	var selct_jbcat = $(this).val();
	var selct_foc = $('select.select-focus').val();

	if( selct_jbcat == 'Maintenance' && selct_foc != '3197'){
		$('select.select-focus').val('3197').trigger('change');


	}else if( selct_jbcat != 'Maintenance' && selct_foc == '3197'){
		$('select.select-focus').val('').trigger('change');

	}else{
		//$('select.select-focus').val('');//.trigger('change');
	}

 

});



if(is_maint_user == 1){
	$('option.main_slct_optn').show();
	$('option.def_slct_optn').hide();

	
setTimeout(function(){
	$('select#job_category').val('Maintenance').trigger('change');
},1000);

	
}



setTimeout(function(){


//	$('select#project_manager').val('');
//	$('select#client_contact_project_manager').val('');


$('select#project_administrator').val('<?php echo $this->request->getPost("project_admin"); ?>');
$('select#estimator').val('<?php echo $this->request->getPost("estimator"); ?>');

},1000);


</script>	

																							
													</div>
												</div>

												<div class="col-sm-3 m-bottom-5 clearfix">		
                                                    <div class="col-sm-12">		
                                                        <label for="leading_hand" class="control-label">Joinery Personnel</label><br />		
                                                        <select id="proj_joinery_user" class="form-control" tabindex="4" name="proj_joinery_user">                                                    		
                                                            <?php $this->admin->joinery_selected_user_select(); ?>		
                                                        </select>		
                                                    </div>		
                                                </div>

												<?php //if($this->session->get('is_admin') == 1 ): ?>

													<div class="col-sm-3 m-bottom-5 clearfix">
														<div class="col-sm-12">
															<label for="leading_hand" class="control-label">Leading Hand</label><br />
															<select name="leading_hand" class="form-control leading_hand" id="leading_hand" style="width: 100%;" tabindex="31">
																<option value='' selected="" class="hide">Select Leading Hand</option>															
																<?php
																foreach ($lead_hand as $row){																
																	echo '<option value="'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
																}?>

																<option value="0">Other</option>

															</select>	
															<script type="text/javascript">$('select#leading_hand').val('<?php echo $this->request->getPost('leading_hand'); ?>');</script>								
														</div>
													</div>

													<div id="addOtherLH" style="display: none;">
														
														<div class="col-sm-3 m-bottom-5 clearfix  <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('lh_name') ? 'has-error' : '';  ?>">
															<div class="col-sm-12">
																<label for="leading_hand" class="control-label">Leading Hand Full Name*</label><br />
																<input type="text" id="lh_name" class="form-control" name="lh_name" placeholder="Leading Hand Full Name" style="text-transform: capitalize;">
																<script type="text/javascript">$('select#leading_hand').val('<?php echo $this->request->getPost('leading_hand'); ?>');</script>								
															</div>
														</div>

														<div class="col-sm-3 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('lh_mobile_no') ? 'has-error' : '';  ?>">
															<div class="col-sm-12">
																<label for="leading_hand" id="lh_mobile" class="control-label">Leading Hand Mobile No.*</label><br />
																<input type="text" class="form-control" id="lh_mobile_no" name="lh_mobile_no" placeholder="Leading Hand Mobile No">
																<script type="text/javascript">$('select#leading_hand').val('<?php echo $this->request->getPost('leading_hand'); ?>');</script>								
															</div>
														</div>

													</div>

												<?php //endif; ?>

												<script type="text/javascript">


/*
                          $(function() {
                            var select = $('select#project_manager');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });

*/
                          $(function() {
                            var select = $('select#project_administrator');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });


                          $(function() {
                            var select = $('select#estimator');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });


                          $(function() {
                            var select = $('select#client_contact_project_manager');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });

                          $(function() {
                            var select = $('select#proj_joinery_user');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });

                          $(function() {
                            var select = $('select#leading_hand');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });



                          $(function() {
                            var select = $('select#job_category');
                            select.html(select.find('option').sort(function(x, y) {
                              return $(x).text() > $(y).text() ? 1 : -1;
                            }));
                          });


                          </script>
												
													
												
											</div>
										</div>
	 									
										
										
										<div class="clearfix"></div>
									    
	      								<div class="box m-top-15">
											<div class="box-head pad-5">
												<label for="project_notes"><i class="fa fa-pencil-square fa-lg"></i> Project Notes</label>
											</div>
											
											<div class="box-area pad-5 clearfix">
												<div class="clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('generalEmail') ? 'has-error' : '';  ?>">
													<div class="">
														<textarea class="form-control" id="project_notes" rows="5"  tabindex="30" name="comments"><?php echo $this->request->getPost('comments'); ?> </textarea>														
													</div>
												</div>
											</div>
										</div>
										
									    <div class="m-top-15 clearfix">
									    	<div>

									        	<div class="btn-group">
									        		<div class="btn btn-success pull-left" onclick="check_required_fields()" ><i class="fa fa-floppy-o"></i> Save</div>



									        		<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									        			<span class="caret"></span>
									        			<span class="sr-only">Toggle Dropdown</span>
									        		</button>
									        		<ul class="dropdown-menu" role="menu">
									        			<li><a href="#" class="set_copy_work"><i class="fa fa-files-o"></i> Copy Work</a></li>
									        		</ul>
									        	</div>

									        </div>
									    </div>

									    <div class=""><p class="">&nbsp;<br />&nbsp;</p><p class="">&nbsp;<br />&nbsp;</p><p class="">&nbsp;<br />&nbsp;</p></div>
									</div>
								
							</div>

						</div>



<div id="copy_works" class="modal fade" tabindex="-1" data-width="760" style="display: none; overflow: hidden;">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Copy Works</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<h4>How to copy</h4>
						<em>To copy the works from an existing project, you can select the client to narrow the list of projects and then select the project below.</em><p></p>
						<div class="col-sm-12 m-bottom-10 clearfix">
							<label for="contact_f_name" class="col-sm-3 control-label m-top-5 text-left"><strong>Select Client</strong></label>
							<div class="col-sm-9">


								<select name="all_company_project" class="form-control all_company_project chosen" id="all_company_project" style="width: 100%;">
									<option value=''>Select Client</option>
									<?php
									foreach ($all_company_project as $row_id => $value){
										echo '<option value="'.$row_id.'|'.$value.'">'.$value.'</option>';
									}?>
								</select>
								<?php if($this->request->getPost('all_company_project')): ?>
									<script type="text/javascript">
										$('select#all_company_project').val('<?php echo $this->request->getPost('all_company_project'); ?>');
										<?php $all_company_project_arr = explode('|', $this->request->getPost('all_company_project')); ?>
										$('.all_company_project a.select2-choice .select2-chosen').text('<?php echo $all_company_project_arr[1]; ?>');
									</script>
								<?php endif; ?>
							</div>
						</div>
						<div class="clearfix"></div>


						<em>You can select the project directly if you desired.</em><p></p>

						<div class="col-sm-12 m-bottom-10 clearfix">
							<label for="contact_l_name" class="col-sm-4 control-label m-top-5 text-left"><strong>Select Existing Project</strong></label>
							<div class="col-sm-8">
								<select name="copy_work_project_id" class="form-control copy_work_project_id chosen_opt_a" id="copy_work_project_id" style="width: 100%;">
									<option value=''>Select Existing Project</option>
									<?php
									foreach ($all_projects->getResultArray() as $row){
										echo '<option class="pg_id_'.$row['company_id'].'" value="'.$row['project_id'].'">'.$row['project_id'].' '.$row['project_name'].'</option>';
									}?>
								</select>
								<?php if($this->request->getPost('copy_work_project_id')): ?>
									<script type="text/javascript">
										$('select#copy_work_project_id').val('<?php echo $this->request->getPost('copy_work_project_id'); ?>');
										$('.copy_work_project_id a.select2-choice .select2-chosen').text('<?php echo $this->request->getPost('copy_work_project_id'); ?>');
									</script>
								<?php endif; ?>

								<script type="text/javascript">var options = $('.chosen_opt_a option');</script>

							</div>
						</div>
						<div class="clearfix"></div>

						<p><hr /></p>


						<div class="col-sm-12 m-bottom-10 clearfix">
							<label for="contact_l_name" class="col-sm-5 control-label m-top-5 text-left"><strong>Include the work estimates?</strong></label>
							<div class="col-sm-7">
								<select name="include_work_estimate" class="form-control include_work_estimate" id="include_work_estimate" style="width: 100%;">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
								<?php if($this->request->getPost('include_work_estimate')): ?>
									<script type="text/javascript">$('select#include_work_estimate').val('<?php echo $this->request->getPost('include_work_estimate'); ?>');</script>
								<?php else: ?>
									<script type="text/javascript">$('select#include_work_estimate').val('1');</script>
								<?php endif; ?>

							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default cancel-contact" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-success add-contact-submit" onclick="check_required_fields()"><i class="fa fa-floppy-o"></i> Save</button>
			</div>
		</div>
	</div>
</div>




			</form>			
						
						<div class="col-md-2">

						
							
							
							<div class="box">
								<div class="box-head pad-5">
									<label><i class="fa fa-info-circle fa-lg"></i> Information</label>
								</div>
								<div class="box-area pad-10">
									<p>
										Completing the basic project information, proceeds to input the projects quotatoin and invoicing.
									</p>
								</div>
							</div>

						
					</div>				
				</div>




			
		</div>
	</div>
</div>



<div class="modal fade" id="select_shopping_center_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Shopping Center Selection</h4>
        <span>Note: Please select a state to view the shopping centers.</span>
      </div>
      <div class="modal-body clearfix pad-10">

        <div class="m-bottom-10">
        	<table id="shoppingCenterTable_prj" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th>Common</th><th>Street</th><th>Suburb</th><th>State</th></tr></thead>
        		<tbody class="shopping_content_tbl">
        			<?php echo $this->shopping_center->display_shopping_center_prj(); ?>
        		</tbody>
        	</table>
        </div>
        <div class="pull-right">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary set_invoice_modal_submit">Submit</button>
        </div>

      </div>
    </div>
  </div>
</div>

<?php $focus_id = ($this->request->getPost('focus') ? $this->request->getPost('focus') : $this->session->get('user_focus_company_id'));  ?>

<div style="display:none;" class="state_select_list">
	<div class="input-group m-bottom-10 pull-right m-right-10" style="width: 210px;">
		<span class="input-group-addon" id=""><i class="fa fa-map-marker"></i></span>
		<select class="form-control select_state_shopping_center m-bottom-10 input-sm">
			<?php echo $this->projects->set_jurisdiction($focus_id); ?>
			<?php /*
				foreach ($all_aud_states as $row):
					echo '<option value="'.$row->name.'">'.$row->name.'</option>';
				endforeach;*/
			?>
		</select>
	</div>
</div>




<!-- Modal -->
<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-sm">
    <div class="modal-content">
       
      <div class="modal-body clearfix pad-10">

      	<center><h3>Loading Please Wait</h3></center>
      	<center><h2><i class="fa fa-circle-o-notch fa-spin fa-5x"></i></h2></center>
      	<p>&nbsp;</p>
  
  

      </div>
    </div>
  </div>
</div>











<div class="modal fade" id="form_error_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Incomplete Form</h4>
			</div>
			<div class="modal-body clearfix pad-10">
				<p class="m-10">Please check the following fields</p>
				<ul class="area_form"></ul>
			</div>
		</div>
	</div>
</div>

</div>

<?php echo view('assets/logout-modal'); ?>



<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->
<?php 
  use App\Modules\Help_videos\Controllers\Help_videos;
  $this->help_videos = new Help_videos();
?>

<div id="help_video_group" class="modal fade" tabindex="-1" data-width="760" >
  <div class="modal-dialog" style="width:85%;">
    <div class="modal-content">
      <div class="modal-header">
        <ul id="myTab" class="nav nav-tabs pull-right" style="border-bottom: 0;">
          <li class="now_playing_tab_btn"><a href="#now_playing" style="color:#555; display:none;" data-toggle="tab"><i class="fa fa-globe fa-lg"></i> Now Playing</a></li>
          <li class="help_videos_tab_btn active"><a href="#help_videos" style="color:#555;" data-toggle="tab" tabindex="20"><i class="fa fa-inbox fa-lg"></i> Videos</a></li> 
        </ul>
        <h4 class="modal-title"><em id="" class="fa fa-film"></em> Help Videos </h4>
      </div>
      <div class="modal-body" style="margin:0 !important; padding:0 !important;">
        <div class="tab-content">
          <div id="now_playing" class="tab-pane fade clearfix  in">
            <iframe style="width: 100%;height: 70%;background-repeat: no-repeat;background-color:#000;background-image: url('<?php echo site_url(); ?>uploads/misc/loading_bub.gif');background-position: center;background-size: 50px;" class="group_video_frame" ></iframe>
          </div>
          <div id="help_videos" class="tab-pane fade clearfix active in">
            <div id="" class="m-10 p-bottom-10 clearfix">
            <p id="" class="m-left-5 p-bottom-10 m-top-5 clearfix" style="font-weight: bold;    font-size: 16px;    border-bottom: 1px solid #ccc;">Project Screen Videos</p>
              <?php $cat_keyword = 'projects'; $sub_cat_keyword = 'add'; ?>
              <?php $this->help_videos->get_help_videos($cat_keyword,$sub_cat_keyword); ?>
            </div>
          </div>
        </div>
      </div> 
    </div>
  </div>
</div>

<script type="text/javascript">
$('.mod_video_toggle').click(function(){
 $('.group_video_frame').attr('src','');
  var video_details_arr = $(this).find('.video_details').text().split('`');
  setTimeout(function(){
    $('.group_video_frame').attr('src',video_details_arr[1]);
  },2000);
  $('li.now_playing_tab_btn a').show().trigger('click');
});

$('.open_help_vids_mpd').click(function(){
  $('li.help_videos_tab_btn a').trigger('click');
    $('li.now_playing_tab_btn a').hide();
});
</script>

<style type="text/css">

.video_bg{
	background: url('https://sojourn.focusshopfit.com.au/uploads/misc/screen_cast_thumb.png');
	color: #fff;
	background-position: center center;
	background-repeat: no-repeat;
	background-size: contain;
	aspect-ratio: 16/8;
	margin: 5px;
	padding: 10px;
	background-color: #000;
	cursor: pointer;
}


.video_bg:hover{
	background: url('https://sojourn.focusshopfit.com.au/uploads/misc/screen_cast_thumb_hover.png');
	background-color: #000;
	background-position: center center;
	background-repeat: no-repeat;
	background-size: contain;

}

.video_box{
	padding: 0 !important;
	margin: 0 !important;
	padding: 5px;
}



  .video_bg:hover .video_title{
    opacity: 0.25;
  }



.video_title{ 

	font-size: 16px;
    bottom: 10%;
    position: absolute;
    padding: 5px 15px 5px 5px;


  }

</style>
<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->









<script type="text/javascript">
	Vue.component('v-select', VueSelect.VueSelect);
	var app = new Vue({
	  	el: '#proj_app',
	  	data: {
	  		client_type: true,
	  		sel_client_type: "0",
		    company: [],
		    temp_company_id: 0,
		    options: [],
			cont_person: "",
			
		  
	  	},
		mounted: function(){
			this.load_contractor_supplier();
		},
  		filters: {
		    getDayname: function(date){
		      return moment(date).format('ddd');
		    },
		    format_date: function(date){
		      return moment(date).format('ll');
		    },
		    ausdate: function(date) {
		      if(date == '0000-00-00'){
		        return '';
		      }else{
		        return moment(date).format('DD/MM/YYYY');
		      }
		      
		    },
		    getTime: function(date){
		      var temp_date = '2020-01-01 '+date;
		      return moment(temp_date).format('h:mm a');
		    },
  		},
  		methods: {
  			load_contractor_supplier: function(){
		    	axios.post("<?php echo site_url(); ?>company/fetch_temporary_comp", 
		      	{
		      		'company_type': 0
		      	}).then(response => {	
		      		this.options = [];
		          	this.company = response.data;    
		          	for (var key in this.company) {
		          		this.options.push({'value': this.company[key].company_details_temp_id, 'label': this.company[key].company_name });
			        }     
	  				     
		      	}).catch(error => {
		        	console.log(error.response)
		      	});
		    },
		    select_client_type : function(){
		    	if(this.sel_client_type == 0){
		    		this.client_type = true;


		    	}else{


		    		this.client_type = false;
		    	}


		    },
		    
			select_pending_client: function(){
				$("#pending_comp_id").val(this.temp_company_id.value+"/"+this.temp_company_id.label);
				for (var key in this.company) {
					if(this.company[key].company_details_temp_id == this.temp_company_id.value){
						this.cont_person = this.company[key].contact_person_fname +" "+ this.company[key].contact_person_sname;
					}
		          	
			    }     
			}
		}
	});

	// MC 02-12-18
	if ($('select#leading_hand').val() === '0'){
		$('#addOtherLH').show();
	}

	$('select#leading_hand').change(function(){

		var leading_hand = $('select#leading_hand').val();

		// alert(leading_hand);

		if (leading_hand === '0'){
			$('#addOtherLH').show();
		} else {
			$('#addOtherLH').hide();
		}
	});
	
	<?php echo ($this->request->getPost('is_form_submit') ? "" : "$('#project_name').focus();" ); ?>


	$('select#project_manager').change(function(){
		var selected_pm_of_project = $(this).val();
		$('select#client_contact_project_manager').val(selected_pm_of_project);
	});

 	$('select#project_manager').change(function(){
		var selected_pm_of_project = $(this).val();

		$.ajax({
			'url' : '<?php echo site_url().'projects/set_pa'; ?>',
			'type' : 'POST',
			'data' : {'selected_pm' : selected_pm_of_project },
			'success' : function(data){

				$('select#project_administrator').val(data);
			}
		});

	});




$('#site_start').datetimepicker({ format: 'DD/MM/YYYY'});
$('#site_finish').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM/YYYY'
});
$("#site_start").on("dp.change", function (e) {
$('#site_finish').data("DateTimePicker").minDate(e.date);

$('#site_finish').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM/YYYY'
});

$('#summ_starting_date').text( e.date.format('DD/MM/YYYY') );

});
$("#site_finish").on("dp.change", function (e) {
$(this).data("DateTimePicker").minDate(e.date);
$('#site_start').data("DateTimePicker").maxDate(e.date);
$('#summ_end_date').text( e.date.format('DD/MM/YYYY') );
});



$('select.slct_client_type').on("change", function(e) {
	var clientn_type = $(this).val();

	$('#s2id_company_prg #select2-chosen-2').text('');
	$('#company_prg_pending .vs__selected').text('');
	
	if(clientn_type == 1){
		$('#pending_comp_id').prop('required',false).val('');
		$("select#company_prg").prop('required',true).val('');

	}else{
		$('.vs__clear').trigger('click');

		$('#pending_comp_id').prop('required',true).val('');
		$("select#company_prg").prop('required',false).val('');
	}
});


$('.vs__clear').click(function(){
	$('#pending_comp_id').val('');
});


</script>

<?php  if($this->session->get('user_focus_company_id') == 3197): ?>
	<script type="text/javascript">

	setTimeout(function(){
		$('select#job_category').val('Maintenance').trigger('change');
}, 500); // on 1 second


		$('option.pm_comp_option').show();
	</script>
<?php endif; ?>
<script type="text/javascript"> $('select#project_manager').val('<?php echo $this->request->getPost('project_manager'); ?>').trigger('change');</script>