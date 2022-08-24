<?php date_default_timezone_set("Australia/Perth");  // date is set to perth and important setting for diff timezone acounts ?>
<?php $this->load->module('company'); ?>

<!-- title bar -->
<div class="container-fluid head-control">
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-6 col-sm-4 col-xs-12 pull-left">
				<header class="page-header">
					<h3><?php $datestring = "%l, %F %d, %Y"; $time = time(); //use time() for timestamp  ?>
						Company Screen<br><small><?php echo mdate($datestring, $time); #echo date("l, F d, Y"); ?></small>
					</h3>
				</header>
			</div>

			<div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
				<ul class="nav nav-tabs navbar-right">
					<li>
						<a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a>
					</li>
					<li class="active">
						<a href="<?php echo base_url(); ?>company" class="btn-small">Clients</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>company/contractor" class="btn-small">Contractor</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>company/supplier" class="btn-small">Supplier</a>
					</li>
					<li>
						<a href="#" class="btn-small"><i class="fa fa-magic"></i> Tour</a>
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
		<?php $this->load->view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<div class="container-fluid">

				<div class="row hidden">
					<div class="col-xs-12">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<p>Change this and that and try again. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum.</p>
							<p>
								<button type="button" class="btn btn-danger" id="loading-example-btn"  data-loading-text="Loading..." >Take this action</button>
								<button type="button" class="btn btn-default">Or do this</button>
							</p>
						</div>
					</div>
				</div>

				<div class="row">
					<form class="form-horizontal company-form" role="form" method="post" action="">
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
								
								<?php if(@$success): ?>
								<div class="pad-10 no-pad-t">
									<div class="border-less-box alert alert-success fade in">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<h4>Congratulations!</h4>
										<?php echo $success;?>
									</div>
								</div>
								<?php endif; ?>	
								
								<?php if(@$this->session->flashdata('update_company_id')): ?>
								<div class="pad-10 no-pad-t">
									<div class="border-less-box alert alert-success fade in">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
										<h4>Cheers!</h4>
										<?php echo $this->session->flashdata('update_message');?>
									</div>
								</div>
								<?php endif; ?>	
								
													
								
								
					
								
									<div class="box-head pad-10 clearfix">
										<input  type="reset" class="btn btn-warning pull-right edit-record" value="Edit Record" />			
										<label>Company Profile Page</label><span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the clients screen." data-original-title="Welcome">?</a>)</span>
										<p>Fields having * is requred.</p>
										<p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>						
									</div>
									<div class="box-area pad-10">										
	      								
	      								<div class="col-sm-12">
											<div class="form-group pad-5 no-pad-b">        								
	         									<div class="input-group <?php if(form_error('company_name')){ echo 'has-error has-feedback';} ?>">
													<span class="input-group-addon"><i class="fa fa-briefcase  fa-lg"></i></span>
													<input type="text" class="form-control" placeholder="Company Name*" readonly name="company_name" 
													value="<?php echo $this->input->post('company_name'); if(isset($company_name)){ echo $company_name;}?>">
													
													
												</div>
	        								</div>
	      								</div>
	      								
	      								<div class="box-tabs m-bottom-15">
											<ul id="myTab" class="nav nav-tabs">
												<li class="active">
													<a href="#physicalAddress" data-toggle="tab"><i class="fa fa-globe fa-lg"></i> Physical Address</a>
												</li>
												<li class="">
													<a href="#postalAddress" data-toggle="tab"><i class="fa fa-inbox fa-lg"></i> Postal Address</a>
												</li>											
											</ul>
											<div class="tab-content">
												<div class="tab-pane fade active in clearfix" id="physicalAddress">
													
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="unit_level" class="col-sm-3 control-label">Unit/Level</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="unit_level" placeholder="Unit/Level" name="unit_level" value="<?php if(isset($unit_level)){ echo $unit_level;}else{ echo $this->input->post('unit_level');}?>">
														</div>
													</div>
												
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="number" class="col-sm-3 control-label">Number</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="number" placeholder="Number" name="unit_number" value="<?php if(isset($unit_number)){ echo $unit_number;}else{echo $this->input->post('unit_number'); }?>">
														</div>
													</div>
												
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('street')){ echo 'has-error has-feedback';} ?>">
														<label for="street" class="col-sm-3 control-label">Street*</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="street" placeholder="Street" name="street" value="<?php  if(isset($street)){ echo $street;}else{echo $this->input->post('street');}?>">
														</div>
													</div>
													
													<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('suburb_a')){ echo 'has-error';} ?>">
														<label for="suburb" class="col-sm-3 control-label">Suburb*</label>
														<div class="col-sm-9 col-xs-12">
															<select class="form-control suburb-option-a" disabled id="suburb_a" name="suburb_a">
																<?php if($this->input->post('suburb_a')): ?>
																<option value="<?php echo $this->input->post('suburb_a'); ?>"><?php echo $this->company->set_suburb($this->input->post('suburb_a')); ?></option>
																<?php endif; ?>
																
																<?php if(isset($suburb)): ?>
																<option value="<?php echo $suburb.'|'.$state.'|'.$area_code; ?>"><?php echo ucwords(strtolower($suburb)); ?></option>
																<?php endif; ?>
																
																<option value="">Choose a Suburb...</option>
																<option value="add">Add New</option>
																<?php //$this->company->suburb_list('dropdown'); ?>
																<?php
																foreach ($suburb_list as $row){
																	echo '<option value="'.$row->suburb.'|'.$row->name.'|'.$row->phone_area_code.'">'.ucwords(strtolower($row->suburb)).'</option>';
																}?>
															</select>
														</div>
													</div>
													
													<!--
													<div id="datepicker" class="input-prepend date">
														<span class="add-on"><i class="icon-th"></i></span>
														<input class="span2" type="text" value="02-16-2012">
													</div>
													-->
													
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('state_a')){ echo 'has-error has-feedback';} ?>">
														<label for="state" class="col-sm-3 control-label">State*</label>
														<div class="col-sm-9 col-xs-12">
																													
														</div>
														<div class="col-sm-9">
															<input type="text" class="form-control disabled state" readonly="readonly" id="state_a" placeholder="State" name="state_a" value="<?php if(isset($state)){ echo $state;}else{echo $this->input->post('state_a');}?>">
														</div>
													</div>
													
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('postcode_a')){ echo 'has-error has-feedback';} ?>">
														<label for="postcode_a" class="col-sm-3 control-label">Postcode*</label> <?php //echo $this->input->post('postcode_a'); ?>													
														<div class="col-sm-9  col-xs-12">
															<select class="form-control postcode-option" disabled id="postcode_a" name="postcode_a">
																
																<?php if(isset($postcode)): ?>
																<option value="<?php echo $postcode; ?>"><?php echo $postcode; ?></option>
																<?php endif; ?>
																
																<?php if($this->input->post('postcode_a')!=''): ?>
																<option value="<?php echo $this->input->post('postcode_a'); ?>"><?php echo $this->input->post('postcode_a'); ?></option>
																<?php else: ?>
																<option value="">Choose a Postcode...</option>
																<?php endif; ?>
															</select>
														</div>
													</div>
													
													<div class="col-md-offset-1 col-sm-offset-2 col-sm-7 col-lg-6 col-md-7 m-bottom-10 clearfix">
														<div class="col-sm-12">
															<div class="input-group">
																<input type="text" id="disabledInput" value="Same values to Postal Address?" readonly class="form-control disabled" disabled>
																<span class="input-group-addon"> <input type="checkbox" class="sameToPost" <?php if($this->input->post('issamepost')){ echo 'checked';} if($p_street == $street){ echo 'checked';}?> name="issamepost"> Yes</span>
															</div>
														</div>
													</div>
												
												</div>
												<div class="tab-pane fade clearfix" id="postalAddress">
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="pobox" class="col-sm-3 control-label">PO Box</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="pobox" readonly placeholder="PO Box" name="pobox" value="<?php if(isset($p_po_box)){ echo $p_po_box;} ?>">
														</div>
													</div>
													
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="unitlevel2" class="col-sm-3 control-label">Unit/Level</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="unitlevel2" placeholder="Unit/Level" name="unit_level_b" value="<?php if(isset($p_unit_level)){ echo $p_unit_level;} ?>">
														</div>
													</div>
												
													<div class="col-sm-6 m-bottom-10 clearfix">
														<label for="number2" class="col-sm-3 control-label">Number</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="number2" placeholder="Number" name="number_b" value="<?php if(isset($p_unit_number)){ echo $p_unit_number;}else{echo $this->input->post('number_b'); }?>"\>
														</div>
													</div>
												
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('street_b')){ echo 'has-error has-feedback';} ?>">
														<label for="street2" class="col-sm-3 control-label">Street*</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" readonly id="street2" placeholder="Street" name="street_b" value="<?php if(isset($p_street)){ echo $p_street;}else{echo $this->input->post('street_b');}?>">
														</div>
													</div>												
													
													<div class="col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('suburb_b')){ echo 'has-error';} ?>">
														<label for="suburb" class="col-sm-3 control-label">Suburb*</label>
														<div class="col-sm-9 col-xs-12">
															<select class="form-control suburb-option-b" id="suburb_b" disabled name="suburb_b">
																<?php if($this->input->post('suburb_b')): ?>
																<option value="<?php echo $this->input->post('suburb_b'); ?>"><?php echo $this->company->set_suburb($this->input->post('suburb_b')); ?></option>
																<?php endif; ?>															
																
																<?php if(isset($p_suburb)): ?>
																<option value="<?php echo $p_suburb.'|'.$state.'|'.$area_code; ?>"><?php echo ucwords(strtolower($p_suburb)); ?></option>
																<?php endif; ?>
																
																<option value="">Choose a Suburb...</option>
																<option value="add">Add New</option>
																<?php //$this->company->suburb_list('dropdown'); ?>
																<?php
																foreach ($suburb_list as $row){
																	echo '<option value="'.$row->suburb.'|'.$row->name.'|'.$row->phone_area_code.'">'.ucwords(strtolower($row->suburb)).'</option>';
																}?>
															</select>
														</div>
													</div>
													
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('state_a')){ echo 'has-error has-feedback';} ?>">
														<label for="state_b" class="col-sm-3 control-label">State*</label>
														<div class="col-sm-9">
															<input type="text" class="form-control disabled state" readonly id="state_b" name="state_b" placeholder="State" value="<?php if(isset($p_state)){ echo $p_state;}else{echo $this->input->post('state_b'); }?>">
														</div>
													</div>
													
													<!-- <div class="col-sm-6 m-bottom-10 clearfix">
														<label for="postcode2" class="col-sm-3 control-label">Postcode*</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="postcode2" placeholder="Postcode">
														</div>
													</div> -->
													
													<div class="col-sm-6 m-bottom-10 clearfix <?php if(form_error('postcode_b')){ echo 'has-error has-feedback';} ?>">
														<label for="postcode_b" class="col-sm-3 control-label">Postcode*</label>													
														<div class="col-sm-9  col-xs-12">
															<select class="form-control postcode-option" disabled id="postcode_b" name="postcode_b">
																<?php if(isset($p_postcode)): ?>
																<option value="<?php echo $p_postcode; ?>"><?php echo $p_postcode; ?></option>
																<?php endif; ?>
																	
																<?php if($this->input->post('postcode_b')!=''): ?>
																<option value="<?php echo $this->input->post('postcode_b'); ?>"><?php echo $this->input->post('postcode_b'); ?></option>
																<?php else: ?>
																<option value="">Choose a Postcode...</option>
																<?php endif; ?>
															</select>
														</div>
													</div>
													
													
												</div>
											</div>
	