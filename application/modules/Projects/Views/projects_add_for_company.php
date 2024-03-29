<?php use App\Modules\Company\Controllers\Company; ?>
<?php $this->company = new Company(); ?>

<?php use App\Modules\Projects\Controllers\Projects; ?>
<?php $this->projects = new Projects(); ?>

<?php use App\Modules\Users\Controllers\Users; ?>
<?php $this->users = new Users(); ?>

<?php $this->users->_check_user_access('projects',2); ?>
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
					<li>
						<a href="" class="btn-small"><i class="fa fa-magic"></i> Tour</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- title bar -->

<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">				
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<form class="form-horizontal" role="form" method="post" action="">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-9">
							<div class="left-section-box">				
					
								<?php if(@$error): ?>
								<div class="pad-10 no-pad-t">
									<div class="border-less-box alert alert-danger fade in">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<h4>Oh snap! You got an error!</h4>
										<?php echo $error;?>
									</div>
								</div>
								<?php endif; ?>
					
								
									<div class="box-head pad-10 clearfix">
										<div class="pull-right">

											<button  type="reset" class="btn btn-warning pull-right">Reset Form</button ><br />
											<select name="focus" class="form-control focus m-top-10 select-focus" id="focus">
												<option value="">Select Focus Company</option>
												<?php foreach ($focus as $key => $value): ?>
													<option value="<?php echo $value->company_id; ?>"><?php echo $value->company_name; ?></option>
												<?php endforeach; ?>
											</select>
											<script type="text/javascript">$('.focus').val('<?php echo ($this->request->getPost('focus') ? $this->request->getPost('focus') : $this->session->get('user_focus_company_id')); ?>');</script>
											

										</div>	
										<label>Add New Project</label><span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the clients screen." data-original-title="Welcome">?</a>)</span>
										<p>Fields having * is required.</p>					
									</div>
									
									<div class="box-area pad-10 clearfix">											
										
										<div class="form-group clearfix pad-5 no-pad-b">
	        								<div class="col-sm-6">
	         									<div class="input-group <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_name') ? 'has-error' : '';  ?>">
													<span class="input-group-addon"><i class="fa fa-briefcase  fa-lg"></i></span>
													<input type="text" class="form-control" placeholder="Project Name*" name="project_name" required id="project_name" maxlength="35" tabindex='1' value="<?php echo $this->request->getPost('project_name'); ?>">
													<span class="input-group-addon char-counter-remains"> </span>
												</div>
	        								</div>

	        								<div class="col-sm-6 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_date') ? 'has-error' : '';  ?>">
												<label for="project_date" class="col-sm-3 control-label">Project Date*</label>
												<div class="col-sm-9">
													<input id="project_date" class="project_date form-control"  required name="project_date" readonly="readonly" type="text" value="<?php echo date("d/m/Y"); ?>">
												</div>	
											</div>
	      								</div>	      								
	      								
	      								<div class="box m-bottom-15 clearfix">
											<div class="box-head pad-5 m-bottom-5">
												<label><i class="fa fa-book fa-lg"></i> General</label>
											</div>
											
											<div class="box-area pad-5 clearfix">																																
												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('company_prg') ? 'has-error' : '';  ?>">
													<label for="company_prg" class="col-sm-3 control-label">Client*</label>
													<div class="col-sm-9">														
														<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-briefcase  fa-lg"></i></span>
															<select name="company_prg"  required class="form-control chosen find_contact_person get_address_invoice" id="company_prg" style="width: 100%;" tabindex="2">
																<option value=''>Select Client Name*</option>
																<option value="Fsf Group Pty Ltd|75">Fsf Group Pty Ltd</option>
																<option value="Focus Shopfit Nsw Pty Ltd|72">Focus Shopfit Nsw Pty Ltd</option>
																<option value="Focus Shopfit Pty Ltd|726">Focus Shopfit Pty Ltd</option>
																	
															</select>
															<script type="text/javascript">$('select#company_prg').val('<?php echo $this->request->getPost('company_prg'); ?>');</script>
														</div>
													</div>
												</div>
												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('contact_person') ? 'has-error' : '';  ?>">
													<label for="contact_person" class="col-md-3 col-sm-5 control-label">Contact Person*</label>
													<div class="col-md-9 col-sm-7 here">
														<select name="contact_person" required  class="form-control" id="contact_person" style="width: 100%;"  tabindex="26">		
															<option value=''>Select Contact Person*</option>
															<?php if($this->request->getPost('company_prg')): ?>
																<?php $comp_arr = explode('|', $this->request->getPost('company_prg')); ?>		
																<?php $this->projects->find_contact_person($comp_arr[1]); ?>
															<?php endif; ?>
														</select>
														<script type="text/javascript">$('select#contact_person').val('<?php echo $this->request->getPost('contact_person'); ?>');</script>
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
													<label for="job_type" class="col-sm-3 control-label">Job Type*</label>													
													<div class="col-sm-9  col-xs-12">
														<select class="form-control" id="job_type" required  name="job_type" tabindex='4'>
															<option value="Company">Company</option>															
														</select>
													</div>
												</div>
												
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('job_category') ? 'has-error' : '';  ?>">
													<label for="job_category" class="col-sm-3 control-label">Job Category*</label>													
													<div class="col-sm-9  col-xs-12">

														<select class="form-control postcode-option tooltip-test"  required id="job_category" name="job_category" tabindex='4' data-original-title="On selecting the Job Category, Project Area is only optional if Job Category is Strip Out, Minor Works, Kiosk or Maintenance.">															
														 
															<option value="Company">Company</option>
														</select>
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('site_start') ? 'has-error' : '';  ?>">
													<label for="site_start" class="col-sm-3 control-label">Site Start*</label>
													<div class="col-sm-9">
														<input tabindex='6' type="text" placeholder="DD/MM/YYYY"  required class="form-control" id="site_start" name="site_start" tabindex="4" value="<?php echo $this->request->getPost('site_start'); ?>">
													</div>
												</div>
													
												<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('site_finish') ? 'has-error' : '';  ?>">
													<label for="site_finish" class="col-sm-3 control-label">Site Finish*</label>
													<div class="col-sm-9 col-xs-12">
														<input tabindex='7' type="text" placeholder="DD/MM/YYYY"  required class="form-control" id="site_finish" name="site_finish" tabindex="4" value="<?php echo $this->request->getPost('site_finish'); ?>">
													</div>
												</div>
											</div>
										</div>

										<div class="box hide" >
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

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_total') ? 'has-error' : 'green-estimate';  ?>">
													<label for="project_total" class="col-sm-3 control-label ">Project Estimate* </label>
													<div class="input-group ">
														<span class="input-group-addon">($)</span>
														<?php $est_amt = str_replace (',','', $this->request->getPost('project_total') ); ?>
														<input type="text" name="project_total" id="project_total"  required class="form-control number_format"  tabindex="10" placeholder="Project Estimate" value="1" readonly="" />
													</div>
												</div>

												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('labour_hrs_estimate') ? 'has-error' : 'green-estimate';  ?>">
													<label for="labour_hrs_estimate" class="col-sm-4 control-label text-center">Site Labour Estimate* </label>
													<div class="input-group ">
														<span class="input-group-addon">(Hrs)</span>
														<input type="text" name="labour_hrs_estimate" id="labour_hrs_estimate"  required class="form-control" tabindex="11" placeholder="Site Labour Estimate" value="1" readonly="" />
													</div>
												</div>


												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_markup') ? 'has-error' : '';  ?>">
													<label for="project_markup" class="col-sm-3 control-label">Project Markup* </label>
													<div class="input-group ">
														<span class="input-group-addon">(%)</span>
														<input type="text" name="project_markup" id="project_markup"   required readonly=""  class="form-control" tabindex="12" placeholder="Markup %" value="0.00" />
														<p class="min_mark_up hidden"></p>
													</div>

												</div>

												<input type="hidden" name="min_mark_up" value="" class="min_mark_up">											
													
												<div class="col-sm-6 m-bottom-10 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_area') ? 'has-error' : '';  ?>">
													<label for="project_area" class="col-sm-3 control-label text-center">Project Area*</label>
													<div class="col-sm-9">												

														<div class="input-group ">
															<span class="input-group-addon">SQM</span>
															<input type="text" name="project_area" id="project_area"  required readonly="" class="form-control tooltip-test" data-original-title="Project Area is only optional if Job Category is Strip Out, Minor Works, Kiosk or Maintenance." tabindex="13" placeholder="Project Area" value="1"/>
														</div>
													</div>
												</div>


											</div>
										</div>
					
										<div class="clearfix"  ></div>
											
	      								
										
										<div class="box m-bottom-15 clearfix">
											<div class="box-head pad-5 m-bottom-5">
												<label><i class="fa fa-users fa-lg"></i> Personel</label>
											</div>
											
											<div class="box-area pad-5 clearfix">
												<div class="col-sm-4 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_manager') ? 'has-error' : '';  ?>">
													<div class="col-sm-12">
														<label for="project_manager" class="control-label">Project Manager*</label><br />
														<select name="project_manager" class="form-control presonel_add" id="" style="width: 100%;" tabindex="27">
															<option selected="" value="20">Gary Ford</option>
														</select>
																							
													</div>
												</div>
	   								
			      								<div class="col-sm-4 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('project_admin') ? 'has-error' : '';  ?>">											
													<div class="col-sm-12">
														<label for="project_administrator" class="control-label">Project Admin*</label>
														<select name="project_admin" class="form-control  "  required id=" " style="width: 100%;" tabindex="28">

															<option selected="" value="20">Gary Ford</option>

														</select>														
													</div>
												</div>
												
			      								<div class="col-sm-4 m-bottom-5 clearfix <?= $_SERVER['REQUEST_METHOD'] === 'POST' && $validation->hasError('estimator') ? 'has-error' : '';  ?>">						
													<div class="col-sm-12">
														<label for="estimator" class="control-label">Estimator*</label>													
														<select name="estimator" class="form-control  "  required id=" " style="width: 100%;" tabindex="29">

															
															<option selected="" value="20">Gary Ford</option>
															
														</select>
													</div>
												</div>
												
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
														<textarea class="form-control" id="project_notes" rows="7"  tabindex="30" name="comments" style="resize: vertical;"><?php echo $this->request->getPost('comments'); ?></textarea>														
													</div>
												</div>
											</div>
										</div>
										
									    <div class="m-top-15 clearfix">
									    	<div>

									    	<button type="submit" class="btn btn-success pull-left"><i class="fa fa-floppy-o"></i> Save</button>


									        </div>
									    </div>
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
				<button type="submit" class="btn btn-success add-contact-submit"><i class="fa fa-floppy-o"></i> Save</button>
			</div>
		</div>
	</div>
</div>




			</form>			
						
						<div class="col-md-3">

						
							
							
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


<!-- 
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
        	<table id="shoppingCenterTable_prj" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th>Brand</th><th>Common</th><th>Street</th><th>Suburb</th><th>State</th></tr></thead>
        		<tbody class="shopping_content_tbl">
        			<?php //echo $this->shopping_center->display_shopping_center_prj(); ?>
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
 -->
<?php $focus_id = ($this->request->getPost('focus') ? $this->request->getPost('focus') : $this->session->get('user_focus_company_id'));  ?>
<!-- 
<div style="display:none;" class="state_select_list">
	<div class="input-group m-bottom-10 pull-right m-right-10" style="width: 210px;">
		<span class="input-group-addon" id=""><i class="fa fa-map-marker"></i></span>
		<select class="form-control select_state_shopping_center m-bottom-10 input-sm">
			<?php //echo $this->projects->set_jurisdiction($focus_id); ?>
			<?php /*
				foreach ($all_aud_states as $row):
					echo '<option value="'.$row->name.'">'.$row->name.'</option>';
				endforeach;*/
			?>
		</select>
	</div>
</div>
 -->



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


<?php echo view('assets/logout-modal'); ?>
<script type="text/javascript">
	<?php echo ($this->request->getPost('is_form_submit') ? "" : "$('#project_name').focus();" ); ?>




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



</script>