<?php date_default_timezone_set("Australia/Perth");  // date is set to perth and important setting for diff timezone acounts ?>
<?php $this->load->module('company'); ?>
<?php $this->load->module('site_labour'); ?>
<?php $this->load->module('admin'); ?>
<?php $notice_days_annual = $this->admin->get_notice_days('1'); ?>
<?php $warehouse = $this->admin->list_warehouse_location(); ?>
<?php $assigned_warehouse = $this->admin->list_assigned_warehouse(); ?>
<?php
	$curr_tab = 'admin_defaults';
	if(isset($_GET['curr_tab'])){
		$curr_tab = $_GET['curr_tab'];

	}

?>

<script src="<?php echo base_url(); ?>js/vue.js"></script>
<script src="<?php echo base_url(); ?>js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>js/axios.min.js"></script>
<!-- title bar -->
<div class="container-fluid head-control">
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-6 col-sm-4 col-xs-12 pull-left">
				<header class="page-header">
					<h3><?php $datestring = "%l, %F %d, %Y"; $time = time(); //use time() for timestamp  ?>
						<?php echo $screen; ?> Screen<br><small><?php echo mdate($datestring, $time); #echo date("l, F d, Y"); ?></small>
					</h3>
				</header>
			</div>

			<div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
				<ul class="nav nav-tabs navbar-right">
					<li>
						<a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>admin" class="btn-small">Defaults</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>admin/company" class="btn-small">Company</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>users" class="btn-small">Users</a>
					</li>
					<!-- <li>
						<a href="" class="btn-small"><i class="fa fa-magic"></i> Tour</a>
					</li> -->
				</ul>
			</div>

		</div>
	</div>
</div>
<!-- title bar -->

<div class="container-fluid" id = "defaults">
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
					
					<div class="col-sm-12">
						<div class="left-section-box">
							<div class="row clearfix" style="margin-bottom: -6px;">
								<div class="col-lg-6 col-md-6 col-sm-4">
									<div class="pad-left-15 clearfix">										
										<label class="h3"><?php echo $screen; ?></label>
										<p>This is where the administrator default settings are listed.</p>
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-8" style="margin-top: 23px;">
									<div class="pad-top-15 pad-right-15 pad-bottom-5 clearfix box-tabs">	
										<ul id="myTab" class="nav nav-tabs pull-right">
											<li class="<?php echo ($curr_tab == 'admin_defaults' ? 'active' : '' ); ?>"><a href="#defaults_section" id="defaults_btn" role="tab" data-toggle="tab" aria-controls="defaults" aria-expanded="true"><i class="fa fa-wrench fa-lg"></i> Admin Defaults</a></li>
											<li><a href="#cost_matrix" id="cost_matrix_btn" role="tab" data-toggle="tab" aria-controls="cost_matrix" aria-expanded="true"><i class="fa fa-bar-chart fa-lg"></i> Cost Matrix</a></li>
											<li class="<?php echo ($curr_tab == 'users' ? 'active' : '' ); ?>"><a href="#users" id="uers_btn" role="tab" data-toggle="tab" aria-controls="users" aria-expanded="true"><i class="fa fa-users fa-lg"></i> Users</a></li>
											<li class="<?php echo ($curr_tab == 'themes' ? 'active' : '' ); ?>"><a href="#themes" id="themes_btn" role="tab" data-toggle="tab" aria-controls="themes" aria-expanded="true"><i class="fa fa-gears fa-lg"></i> Themes</a></li>
										</ul>
									</div>
								</div>
							</div>


							<div class="box-area pad-10 border-top">

								<?php if(@$matrix_errors): ?>
									<div class="no-pad-t m-bottom-10">
										<div class="border-less-box alert alert-danger fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Oh snap! You got an error!</h4>
											<?php echo $matrix_errors;?>
										</div>
									</div>
								<?php endif; ?>


								<?php if(@$this->session->flashdata('update_matrix')): ?>
									<div class="no-pad-t m-bottom-10">
										<div class="border-less-box alert alert-success fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Cheers!</h4>
											<?php echo $this->session->flashdata('update_matrix');?>
										</div>
									</div>
								<?php endif; ?>



									<div id="myTabContent" class="tab-content">

									<div class="tab-pane  fade in" id="cost_matrix" aria-labelledby="cost_matrix">
										<!-- Cost Matrix -->

								<form class="form-horizontal"  role="form" method="post" action="<?php echo current_url(); ?>/matrix">
									<div  class=" box yellow-border m-top-0" >
										<div class="box-head yellow-bg pad-5 m-bottom-5">
											<label class="yellow-title"><i class="fa fa-bar-chart-o fa-lg"></i> Cost Matrix</label>
										</div>

										<div class="box-area pad-5 clearfix">
											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('total_days')){ echo 'has-error';} ?>">
												<label for="total_days" class="col-sm-3 control-label">Total Days</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="total_days" name="total_days" placeholder="Total Days" value="<?php echo ($this->input->post('total_days') ?  $this->input->post('total_days') : $total_days ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('rate')){ echo 'has-error';} ?>">
												<label for="rate" class="col-sm-3 control-label">Hour Rate</label>
												<div class="col-sm-9">
													<div class="input-group">
														<span class="input-group-addon" id="">$</span>
														<input type="text" class="form-control" id="rate" name="rate" placeholder="Rate ($)" value="<?php echo ($this->input->post('rate') ?  $this->input->post('rate') : $rate ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('hours')){ echo 'has-error';} ?>">
												<label for="hours" class="col-sm-3 control-label">Total Hours</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="hours" name="hours" placeholder="Hours" value="<?php echo ($this->input->post('hours') ?  $this->input->post('hours') : $hours ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('superannuation')){ echo 'has-error';} ?>">
												<label for="superannuation" class="col-sm-4 control-label">Superannuation</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon" id="">%</span>
														<input type="text" class="form-control" id="superannuation1" name="superannuation" placeholder="Superannuation" value="<?php echo ($this->input->post('superannuation') ?  $this->input->post('superannuation') : $super_annuation ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('workers-comp')){ echo 'has-error';} ?>">
												<label for="workers-comp" class="col-sm-5 control-label">Workers Comp</label>
												<div class="col-sm-7">
													<div class="input-group">
														<span class="input-group-addon" id="">%</span>
														<input type="text" class="form-control" id="workers-comp" name="workers-comp" placeholder="Workers Comp" value="<?php echo ($this->input->post('workers-comp') ?  $this->input->post('workers-comp') : $worker_compensation ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('public-holidays')){ echo 'has-error';} ?>">
												<label for="public-holidays" class="col-sm-4 control-label">Public Holidays</label>
												<div class="col-sm-8">
													<input type="text" class="form-control" id="public-holidays" name="public-holidays" placeholder="Public Holidays" value="<?php echo ($this->input->post('public-holidays') ?  $this->input->post('public-holidays') : $public_holidays ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('rdos')){ echo 'has-error';} ?>">
												<label for="rdos" class="col-sm-3 control-label">RDO's</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="rdos" name="rdos" placeholder="RDO's" value="<?php echo ($this->input->post('rdos') ?  $this->input->post('rdos') : $rdos ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sick-leave')){ echo 'has-error';} ?>">
												<label for="sick-leave" class="col-sm-3 control-label">Sick Leave</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="sick-leave" name="sick-leave" placeholder="Sick Leave" value="<?php echo ($this->input->post('sick-leave') ?  $this->input->post('sick-leave') : $sick_leave ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('carers-leave')){ echo 'has-error';} ?>">
												<label for="carers-leave" class="col-sm-3 control-label">Carers Leave</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="carers-leave" name="carers-leave" placeholder="Carers Leave" value="<?php echo ($this->input->post('carers-leave') ?  $this->input->post('carers-leave') : $carers_leave ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('annual-leave')){ echo 'has-error';} ?>">
												<label for="annual-leave" class="col-sm-3 control-label">Annual Leave</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="annual-leave" name="annual-leave" placeholder="Annual Leave" value="<?php echo ($this->input->post('annual-leave') ?  $this->input->post('annual-leave') : $annual_leave ); ?>">
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('downtime')){ echo 'has-error';} ?>">
												<label for="downtime" class="col-sm-3 control-label">Downtime</label>
												<div class="col-sm-9">
													<div class="input-group">
														<span class="input-group-addon" id="">%</span>
														<input type="text" class="form-control" id="downtime" name="downtime" placeholder="Downtime" value="<?php echo ($this->input->post('downtime') ?  $this->input->post('downtime') : $down_time ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('leave-loading')){ echo 'has-error';} ?>">
												<label for="leave-loading" class="col-sm-4 control-label">Leave Loading</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon" id="">%</span>
														<input type="text" class="form-control" id="leave-loading" name="leave-loading" placeholder="Leave Loading" value="<?php echo ($this->input->post('leave-loading') ?  $this->input->post('leave-loading') : $sc_leave_loading ); ?>">


													</div>
												</div>
											</div>

										</div>
									</div>
								


									<div class="m-top-15 clearfix">
								    	<div>
								        	<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Cost Matrix</button>
								        </div>
								    </div>
								</form>					
								<!-- Cost Matrix -->



<p><hr /></p>

								<!-- Site Labour On Cost Matrix -->

								<?php if(@$cost_labour_matrix_errors): ?>
									<div class="no-pad-t">
										<div class="border-less-box alert alert-danger fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Oh snap! You got an error!</h4>
											<?php echo $cost_labour_matrix_errors;?>
										</div>
									</div>
								<?php endif; ?>


								<?php if(@$this->session->flashdata('update_labour_cost_matrix')): ?>
									<div class="no-pad-t">
										<div class="border-less-box alert alert-success fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Cheers!</h4>
											<?php echo $this->session->flashdata('update_labour_cost_matrix');?>
										</div>
									</div>
								<?php endif; ?>

								<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/labour_cost_matrix">

									<div class="box yellow-border m-top-0">
										<div class="box-head yellow-bg pad-5 m-bottom-5">
											<label class="yellow-title"><i class="fa fa-ticket fa-lg"></i> Site Labour On Cost Matrix</label>
										</div>

										<div class="box-area pad-5 clearfix">

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('superannuation')){ echo 'has-error';} ?>">
												<label for="superannuation" class="col-sm-4 control-label">Superannuation</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Superannuation (%)" id="superannuation" name="superannuation" value="<?php echo ($this->input->post('superannuation') ?  $this->input->post('superannuation') : $superannuation ); ?>">
													</div>
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('workers_compensation')){ echo 'has-error';} ?>">
												<label for="workers_compensation" class="col-sm-5 control-label">Workers Compensation</label>
												<div class="col-sm-7">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Workers Compensation (%)" id="workers_compensation" name="workers_compensation" value="<?php echo ($this->input->post('workers_compensation') ?  $this->input->post('workers_compensation') : $workers_compensation ); ?>">
													</div>
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('payroll_tax')){ echo 'has-error';} ?>">
												<label for="payroll_tax" class="col-sm-4 control-label">Payroll Tax</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Payroll Tax (%)" id="payroll_tax" name="payroll_tax" value="<?php echo ($this->input->post('payroll_tax') ?  $this->input->post('payroll_tax') : $payroll_tax ); ?>">
													</div>
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('leave_loading')){ echo 'has-error';} ?>">
												<label for="leave_loading" class="col-sm-4 control-label">Leave Loading  <?php echo "$lc_leave_loading"; ?></label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Leave Loading (%)" id="leave_loading" name="leave_loading" value="<?php echo ($this->input->post('leave_loading') ?  $this->input->post('leave_loading') : $lc_leave_loading ); ?>">
													</div>
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('other')){ echo 'has-error';} ?>">
												<label for="other" class="col-sm-4 control-label">Other</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Other (%)" id="other" name="other" value="<?php echo ($this->input->post('other') ?  $this->input->post('other') : $other ); ?>">
													</div>
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('total_leave_days')){ echo 'has-error';} ?>">
												<label for="total_leave_days" class="col-sm-4 control-label">Total Leave Days</label>
												<div class="col-sm-8">
													<input type="text" class="form-control" placeholder="Total Leave Days" id="total_leave_days" name="total_leave_days" value="<?php echo ($this->input->post('total_leave_days') ?  $this->input->post('total_leave_days') : $total_leave_days ); ?>">
													
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('total_work_days')){ echo 'has-error';} ?>">
												<label for="total_work_days" class="col-sm-4 control-label">Total Work Days</label>
												<div class="col-sm-8">
													<input type="text" class="form-control" placeholder="Total Work Days" id="total_work_days" name="total_work_days" value="<?php echo ($this->input->post('total_work_days') ?  $this->input->post('total_work_days') : $total_work_days ); ?>">
													
												</div>	
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('total_work_days')){ echo 'has-error';} ?>">
												<div class="col-sm-12">
													<div class="input-group">
														<span class="input-group-addon"  style="padding: 9px;">Leave Percentage: <strong class="leave_percentage"><?php echo $leave_percentage; ?>%</strong></span>
														<span class="input-group-addon"  style="padding: 9px;">Grand Total: <strong class"grand_total"><?php echo $grand_total; ?>%</strong></span>
													</div>
												</div>	
											</div>

											
										</div>
									</div>

									<div class="m-top-15 clearfix">
								    	<div>
								        	<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Labour Matrix</button>
								        </div>
								    </div>	

							    </form>

								<!-- Site Labour On Cost Matrix -->



<p><hr /></p>




<!-- Category Mark-Up -->

								<?php if(@$this->session->flashdata('update_prj_mrk')): ?>
									<div class="no-pad-t">
										<div class="border-less-box alert alert-success fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Cheers!</h4>
											<?php echo $this->session->flashdata('update_prj_mrk');?>
										</div>
									</div>
								<?php endif; ?>

									<form method="post" action="<?php echo current_url(); ?>/project_mark_up" class="form-horizontal">								

									<div class="box yellow-border m-top-0">

									<div class="box-head yellow-bg pad-5 m-bottom-5">
										<label class="yellow-title"><i class="fa fa-calculator fa-lg"></i> Category Mark-Up</label>
									</div>

									<div class="box-area pad-5 clearfix">

										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="design_works" class="col-sm-4 control-label">Design Works (%)</label>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="design_works">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="design_works" name="design_works" value="<?php echo ($this->input->post('design_works') ?  $this->input->post('design_works') : $design_works ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_design_works">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_design_works" name="min_design_works" value="<?php echo ($this->input->post('min_design_works') ?  $this->input->post('min_design_works') : $min_design_works ); ?>">
												</div>
											</div>
										</div>



										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="kiosk" class="col-sm-4 control-label">Kiosk (%)</label>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="kiosk">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="kiosk" name="kiosk" value="<?php echo ($this->input->post('kiosk') ?  $this->input->post('kiosk') : $kiosk ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_kiosk">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_kiosk" name="min_kiosk" value="<?php echo ($this->input->post('min_kiosk') ?  $this->input->post('min_kiosk') : $min_kiosk ); ?>">
												</div>
											</div>
										</div>

										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Full fitout (%)</label>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="full-fitout">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="full-fitout" name="full-fitout" value="<?php echo ($this->input->post('full-fitout') ?  $this->input->post('full-fitout') : $full_fitout ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_full_fitout">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_full_fitout" name="min_full_fitout" value="<?php echo ($this->input->post('min_full_fitout') ?  $this->input->post('min_full_fitout') : $min_full_fitout ); ?>">
												</div>
											</div>
										</div>

										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Refurbishment (%)</label>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="refurbishment">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="refurbishment" name="refurbishment" value="<?php echo ($this->input->post('refurbishment') ?  $this->input->post('refurbishment') : $refurbishment ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_refurbishment">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_refurbishment" name="min_refurbishment" value="<?php echo ($this->input->post('min_refurbishment') ?  $this->input->post('min_refurbishment') : $min_refurbishment ); ?>">
												</div>
											</div>

										</div>

										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Stripout (%)</label>
											
											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="stripout">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="stripout" name="stripout" value="<?php echo ($this->input->post('stripout') ?  $this->input->post('stripout') : $stripout ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_stripout">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_stripout" name="min_stripout" value="<?php echo ($this->input->post('min_stripout') ?  $this->input->post('min_stripout') : $min_stripout ); ?>">
												</div>
											</div>
										</div>


										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Maintenance (%)</label>
											
											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="maintenance">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="maintenance" name="maintenance" value="<?php echo ($this->input->post('maintenance') ?  $this->input->post('maintenance') : $maintenance ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_maintenance">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_maintenance" name="min_maintenance" value="<?php echo ($this->input->post('min_maintenance') ?  $this->input->post('min_maintenance') : $min_maintenance ); ?>">
												</div>
											</div>
										</div>

										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Minor Works (%)</label>
																						
											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="minor-works">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="minor-works" name="minor-works" value="<?php echo ($this->input->post('minor-works') ?  $this->input->post('minor-works') : $minor_works ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_minor_works">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_minor_works" name="min_minor_works" value="<?php echo ($this->input->post('min_minor_works') ?  $this->input->post('min_minor_works') : $min_minor_works ); ?>">
												</div>
											</div>
										</div>




										<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
											<label for="input" class="col-sm-4 control-label">Joinery Only (%)</label>
																						
											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="joinery-only">Default</span>
													<input type="text" class="form-control" placeholder="Default" id="joinery-only" name="joinery-only" value="<?php echo ($this->input->post('joinery-only') ?  $this->input->post('joinery-only') : $joinery_only ); ?>">
												</div>
											</div>

											<div class="col-sm-4">
												<div class="input-group">
													<span class="input-group-addon" id="min_joinery_only">Min</span>
													<input type="text" class="form-control" placeholder="Min" id="min_joinery_only" name="min_joinery_only" value="<?php echo ($this->input->post('min_joinery_only') ?  $this->input->post('min_joinery_only') : $min_joinery_only ); ?>">
												</div>
											</div>
										</div>


									</div>
								</div>	

								        <button type="submit" class="btn btn-success m-top-15"><i class="fa fa-floppy-o"></i> Save Mark-Up</button>
								    </form>


<!-- Category Mark-Up -->
								  
<p><hr /></p>  


<!-- Defaults Labour Split -->


								<?php if(@$default_errors): ?>
									<div class="no-pad-t">
										<div class="border-less-box alert alert-danger fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Oh snap! You got an error!</h4>
											<?php echo $default_errors;?>
										</div>
									</div>
								<?php endif; ?>


								<?php if(@$this->session->flashdata('update_default')): ?>
									<div class="no-pad-t">
										<div class="border-less-box alert alert-success fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Cheers!</h4>
											<?php echo $this->session->flashdata('update_default');?>
										</div>
									</div>
								<?php endif; ?>

								<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/defaults">

									<div class="box yellow-border m-top-0">
										<div class="box-head yellow-bg pad-5 m-bottom-5">
											<label class="yellow-title"><i class="fa fa-cog fa-lg"></i> Defaults Labour Split</label>
										</div>

										<div class="box-area pad-5 clearfix">

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
												<label for="time-half" class="col-sm-4 control-label">Time &amp; Half Labour</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" placeholder="Time &amp; Half (%)" id="time-half1" name="time-half" value="<?php echo ($this->input->post('time-half') ?  $this->input->post('time-half') : $labor_split_time_and_half ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
												<label for="double-time" class="col-sm-4 control-label">Double Time</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" id="double-time" name="double-time" placeholder="Double Time (%)"  value="<?php echo ($this->input->post('double-time') ?  $this->input->post('double-time') : $labor_split_double_time ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('standard-labour')){ echo 'has-error';} ?>">
												<label for="standard-labour" class="col-sm-4 control-label">Standard Labour</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon standard-labour text-left" style="padding: 9px;">% <?php echo ($this->input->post('standard-labour') ?  $this->input->post('standard-labour') : $labor_split_standard ); ?></span>
														<input type="hidden" class="form-control" id="standard-labour" readonly="readonly" name="standard-labour" placeholder="Standard Labour (%)"  value="<?php echo ($this->input->post('standard-labour') ?  $this->input->post('standard-labour') : $labor_split_standard ); ?>">
														<span class="input-group-addon" style="padding: 9px;"></span>
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('installation-labour')){ echo 'has-error';} ?>">
												<label for="installation-labour" class="col-sm-4 control-label">Installation Labour</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">Markup %</span>
														<input type="text" class="form-control" id="installation-labour" name="installation-labour" placeholder="Installation Labour (%)"  value="<?php echo ($this->input->post('installation-labour') ?  $this->input->post('installation-labour') : $installation_labour_mark_up ); ?>">
													</div>
												</div>
											</div>

											<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('gst-rate')){ echo 'has-error';} ?>">
												<label for="gst-rate" class="col-sm-4 control-label">GST Rate</label>
												<div class="col-sm-8">
													<div class="input-group">
														<span class="input-group-addon">%</span>
														<input type="text" class="form-control" id="gst-rate" name="gst-rate" placeholder="GST Rate (%)"  value="<?php echo ($this->input->post('gst-rate') ?  $this->input->post('gst-rate') : $gst_rate ); ?>">
													</div>
												</div>
											</div>
											
										</div>
									</div>

									<div class="m-top-15 clearfix">
								    	<div>
								        	<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Defaults</button>
								        </div>
								    </div>	

							    </form>

<!-- Defaults Labour Split -->


<p><hr /></p>



<!-- Hour Rate Cost Inc On Costs -->

						<div class="box yellow-border">
							<div class="box-head yellow-bg pad-5">
								<label class="yellow-title"><i class="fa fa-cube fa-lg"></i> Total Hour Rate</label>
							</div>

							<div class="box-area clearfix pad-5 m-top-10">
								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix">
									<label for="total-hour" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Hour </label>
									<div class="col-sm-7">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" value="<?php echo $gp_on_cost_total_hr; ?>">
										</div>
									</div>						
								</div>

								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix">
									<label for="total-hour" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Time Half </label>
									<div class="col-sm-7">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" value="<?php echo $gp_on_cost_time_half_hr; ?>">
										</div>
									</div>						
								</div>

								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix">
									<label for="total-hour" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Double Time </label>
									<div class="col-sm-7">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" value="<?php echo $gp_on_cost_time_double_hr; ?>">
										</div>
									</div>						
								</div>

								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix">
									<label for="total-hour" class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">Amalgamated Rate </label>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" value="<?php echo $gp_amalgamated_rate;?>">
										</div>
									</div>						
								</div>
								
							</div>
						</div> 

<!-- Hour Rate Cost Inc On Costs -->




<!--  Total Hour Rate -->

	<div class="box yellow-border m-bottom-10">
							<div class="box-head yellow-bg pad-5"><label class="yellow-title"><i class="fa fa-cube fa-lg"></i> Hour Rate Cost Inc On Costs</label></div>

							<div class="box-area clearfix pad-5 m-top-10">								


								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix ">
									<label for="total-hour" class="col-sm-6 control-label m-top-5 text-right" style="font-weight: normal;">Total Hour Rate</label>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" id="total-hour" name="total-hour" placeholder="Total Hour"  value="<?php echo ($this->input->post('total-hour') ?  $this->input->post('total-hour') : $total_hour ); ?>">
										</div>
									</div>
								</div>

								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
									<label for="time-half" class="col-sm-6 control-label m-top-5 text-right" style="font-weight: normal;">Time Half Rate</label>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" id="time-half" name="time-half" placeholder="Total Time Half" value="<?php echo ($this->input->post('time-half') ?  $this->input->post('time-half') : $total_time_half ); ?>">
										</div>

									</div>
								</div>

								<div class="col-lg-3 col-md-6  col-sm-6 col-xs-12 m-bottom-10 clearfix ">
									<label for="double-time" class="col-sm-6 control-label m-top-5 text-right" style="font-weight: normal;">Double Time Rate</label>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" id="double-time1" name="double-time" placeholder="Total Double Time" value="<?php echo ($this->input->post('double-time') ?  $this->input->post('double-time') : $total_double_time ); ?>">
										</div>

									</div>
								</div>

								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix ">
									<label for="amalgamated-rate" class="col-sm-6 control-label m-top-5 text-right" style="font-weight: normal;">Amalgamated Rate</label>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon" id="">$</span>
											<input type="text" class="form-control" readonly="readonly" id="amalgamated-rate" name="amalgamated-rate" placeholder="Amalgamated Rate" value="<?php echo ($this->input->post('amalgamated-rate') ?  $this->input->post('amalgamated-rate') : $total_amalgamated_rate ); ?>">
										</div>

									</div>
								</div>
							</div>
						</div>    

<!--  Total Hour Rate -->




									</div>
										
								


								<div class="tab-pane fade in <?php echo ($curr_tab == 'admin_defaults' ? 'active' : '' ); ?>" id="defaults_section" aria-labelledby="defaults">



									<div class="box blue-border m-top-0">
										<div class="box-head blue-bg pad-5" style="">
											<label class="blue-title"><i class="fa fa-handshake-o fa-lg"></i> Projects Default Settings</label>
										</div>

										<div class="box-area clearfix pad-10">

											<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/warranty_categories">

												<?php if(@$warranty_setup_errors): ?>
													<div class="no-pad-t">
														<div class="border-less-box alert alert-danger fade in">
															<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
															<h4>Oh snap! You got an error!</h4>
															<?php echo $warranty_setup_errors;?>
														</div>
													</div>
												<?php endif; ?>

												<?php if(@$this->session->flashdata('warranty_setup')): ?>
													<div class="col-sm-12 ">							
														<div class="no-pad-t">
															<div class="border-less-box alert alert-success fade in m-top-5">
																<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
																<?php echo $this->session->flashdata('warranty_setup'); ?>
															</div>
														</div>
													</div>
												<?php endif; ?>

												<div class="box yellow-border">

													<div class="box-head yellow-bg pad-5 m-bottom-5" style="background: none !important; background-color: #fcf8e3 !important;">
														<label class="yellow-title"><i class="fa fa-certificate fa-lg"></i> Warranty Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">

														<div class="col-md-8">
															<div class="col-md-12 col-sm-12 col-xs-12 m-top-5 m-bottom-10 clearfix <?php if(form_error('warranty_setup')){ echo 'has-error';} ?>">
																<label for="warranty_categories" class="col-sm-4 control-label">Select Job Categories:</label>
																<div class="col-sm-8">
																	<?php 
																		$ls_job_category_arr = explode(",",$warranty_categories);
																		$ls_design_works = 0;
																		$ls_kiosk = 0;
																		$ls_full_fitout = 0;
																		$ls_refurbishment = 0;
																		$ls_strip_out = 0;
																		$ls_minor_works = 0;
																		$ls_maintenance = 0;

																		foreach ($ls_job_category_arr as &$value) {
																			if($value == "Design Works"){
																				$ls_design_works = 1;
																			}

																			if($value == "Kiosk"){
																				$ls_kiosk = 1;
																			}

																			if($value == "Full Fitout"){
																				$ls_full_fitout = 1;
																			}

																			if($value == "Refurbishment"){
																				$ls_refurbishment = 1;
																			}
																			if($value == "Strip Out"){
																				$ls_strip_out = 1;
																			}
																			if($value == "Minor Works"){
																				$ls_minor_works = 1;
																			}

																			if($value == "Maintenance"){
																				$ls_maintenance = 1;
																			}
																		}
																	?>
																	<table width = 100%>
																		<tr>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Design Works" <?php if($ls_design_works == 1){ echo 'checked'; } ?>></td>
																			<td>Design Works</td>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Kiosk" <?php if($ls_kiosk == 1){ echo 'checked'; } ?>></td>
																			<td>Kiosk</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Full Fitout" <?php if($ls_full_fitout == 1){ echo 'checked'; } ?>></td>
																			<td>Full Fitout</td>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Refurbishment" <?php if($ls_refurbishment == 1){ echo 'checked'; } ?>></td>
																			<td>Refurbishment</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Strip Out" <?php if($ls_strip_out == 1){ echo 'checked'; } ?>></td>
																			<td>Strip Out</td>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Minor Works" <?php if($ls_minor_works == 1){ echo 'checked'; } ?>></td>
																			<td>Minor Works</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "warranty_categories[]" value = "Maintenance" <?php if($ls_maintenance == 1){ echo 'checked'; } ?>></td>
																			<td>Maintenance</td>
																		</tr>
																	</table>
																</div>
															</div>

															<div class="col-sm-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10">
																<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Warranty Categories</button>
															</div>
														</div>
													
											</form>

											<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/warranty_setup">

														<div class="col-md-4">
															<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('warranty_months')){ echo 'has-error';} ?>">
																<label for="warranty_years" class="col-sm-6 control-label">Warranty Month(s):</label>
																<div class="col-sm-2 pad-5">
												      				<input type="text" placeholder="1" class="form-control text-center" id="warranty_years" name="warranty_years" value="<?php echo $warranty_years ?>">
												      				<input type="hidden" id="warranty_years_hidden" name="warranty_years_hidden" value="<?php echo $warranty_months ?>">
																</div>
															</div>

															<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('warranty_months')){ echo 'has-error';} ?>">
																<label for="warranty_months" class="col-sm-6 control-label">Extension Month(s):</label>
																<div class="col-sm-2 pad-5">
												      				<input type="text" placeholder="2" class="form-control text-center" id="warranty_months" name="warranty_months" value="<?php echo $warranty_months ?>">
												      				<input type="hidden" id="warranty_months_hidden" name="warranty_months_hidden" value="<?php echo $warranty_months ?>">
																</div>
															</div>

															<div class="col-sm-12 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
																<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Warranty Date Setup</button>
															</div>

														</div>
													</div>
												</div>
										    </form>

											<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_unaccepted_projects">

												<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-times-circle-o fa-lg"></i> Unaccepted Project Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">

														<div class="col-md-8">
															<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
																<label for="time-half" class="col-sm-4 control-label">Select Job Categories:</label>
																<div class="col-sm-8">
																	<?php 
																		$job_category_arr = explode(",",$unaccepted_date_categories);
																		$design_works = 0;
																		$kiosk = 0;
																		$full_fitout = 0;
																		$refurbishment = 0;
																		$strip_out = 0;
																		$ninor_works = 0;
																		$maintenance = 0;

																		foreach ($job_category_arr as &$value) {
																			if($value == "Design Works"){
																				$design_works = 1;
																			}

																			if($value == "Kiosk"){
																				$kiosk = 1;
																			}

																			if($value == "Full Fitout"){
																				$full_fitout = 1;
																			}

																			if($value == "Refurbishment"){
																				$refurbishment = 1;
																			}
																			if($value == "Strip Out"){
																				$strip_out = 1;
																			}
																			if($value == "Minor Works"){
																				$ninor_works = 1;
																			}

																			if($value == "Maintenance"){
																				$maintenance = 1;
																			}
																		}
																	?>
																	<table width = 100%>
																		<tr>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Design Works" <?php if($design_works == 1){ echo 'checked'; } ?>></td>
																			<td>Design Works</td>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Kiosk" <?php if($kiosk == 1){ echo 'checked'; } ?>></td>
																			<td>Kiosk</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Full Fitout" <?php if($full_fitout == 1){ echo 'checked'; } ?>></td>
																			<td>Full Fitout</td>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Refurbishment" <?php if($refurbishment == 1){ echo 'checked'; } ?>></td>
																			<td>Refurbishment</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Strip Out" <?php if($strip_out == 1){ echo 'checked'; } ?>></td>
																			<td>Strip Out</td>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Minor Works" <?php if($ninor_works == 1){ echo 'checked'; } ?>></td>
																			<td>Minor Works</td>
																		</tr>
																		<tr>
																			<td><input type="checkbox" name = "unaccepted_proj_categories[]" value = "Maintenance" <?php if($maintenance == 1){ echo 'checked'; } ?>></td>
																			<td>Maintenance</td>
																		</tr>
																	</table>
																</div>

															</div>
														</div>

														<div class="col-md-4">

															<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
																<label for="time-half" class="col-sm-6 control-label">Default Number of Days Before Commencement Date:</label>
																<div class="col-sm-2 pad-5">
																	<input type = "text" class="form-control text-center" id="unaccepted_num_days" name="unaccepted_num_days" value = "<?php echo ($this->input->post('unaccepted_num_days') ?  $this->input->post('unaccepted_num_days') : $unaccepted_no_days ); ?>">
																</div>

																<p class="clearfix"><br /></p>

																<label for="time-half" class="col-sm-6 control-label">Days Quote Deadline:</label>
																<div class="col-sm-2 pad-5">
																	<input type="text" placeholder="Days Quote Deadline" class="form-control text-center" id="days_quote_deadline" name="days_quote_deadline" value = "<?php echo ($this->input->post('days_quote_deadline') ?  $this->input->post('days_quote_deadline') : $days_quote_deadline ); ?>">
																</div>
															</div>

														</div>

														<div class="col-sm-12 col-xs-12 clearfix m-top-20 m-bottom-10">
													        <button type="submit" class="btn btn-success pull-right"><i class="fa fa-floppy-o"></i> Save Unaccepted Project Defaults</button>
													    </div>

													</div>
												</div>
												
										    </form>

										    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_progress_report">

												<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-line-chart fa-lg"></i> Progress Report Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">

														<div class="col-md-8 col-sm-8 col-xs-12 m-top-20m-bottom-10 clearfix <?php if(form_error('progress_report_categories')){ echo 'has-error';} ?>">
															<label for="progress_report_categories" class="col-sm-4 control-label">Select Job Categories:</label>
															<div class="col-sm-8">
																<?php 
																	$ls_job_category_arr = explode(",",$progress_report_categories);
																	$ls_design_works = 0;
																	$ls_kiosk = 0;
																	$ls_full_fitout = 0;
																	$ls_refurbishment = 0;
																	$ls_strip_out = 0;
																	$ls_minor_works = 0;
																	$ls_maintenance = 0;

																	foreach ($ls_job_category_arr as &$value) {
																		if($value == "Design Works"){
																			$ls_design_works = 1;
																		}

																		if($value == "Kiosk"){
																			$ls_kiosk = 1;
																		}

																		if($value == "Full Fitout"){
																			$ls_full_fitout = 1;
																		}

																		if($value == "Refurbishment"){
																			$ls_refurbishment = 1;
																		}
																		if($value == "Strip Out"){
																			$ls_strip_out = 1;
																		}
																		if($value == "Minor Works"){
																			$ls_minor_works = 1;
																		}

																		if($value == "Maintenance"){
																			$ls_maintenance = 1;
																		}
																	}
																?>
																<table width = 100%>
																	<tr>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Design Works" <?php if($ls_design_works == 1){ echo 'checked'; } ?>></td>
																		<td>Design Works</td>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Kiosk" <?php if($ls_kiosk == 1){ echo 'checked'; } ?>></td>
																		<td>Kiosk</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Full Fitout" <?php if($ls_full_fitout == 1){ echo 'checked'; } ?>></td>
																		<td>Full Fitout</td>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Refurbishment" <?php if($ls_refurbishment == 1){ echo 'checked'; } ?>></td>
																		<td>Refurbishment</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Strip Out" <?php if($ls_strip_out == 1){ echo 'checked'; } ?>></td>
																		<td>Strip Out</td>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Minor Works" <?php if($ls_minor_works == 1){ echo 'checked'; } ?>></td>
																		<td>Minor Works</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "progress_report_categories[]" value = "Maintenance" <?php if($ls_maintenance == 1){ echo 'checked'; } ?>></td>
																		<td>Maintenance</td>
																	</tr>
																</table>
															</div>
														</div>

														<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
													    	<!-- <div> -->
													        	<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Progress Report Defaults</button>
													        <!-- </div> -->
													    </div>
														
													</div>
												</div>

										    </form>

										    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_labour_schedule">

												<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-calendar fa-lg"></i> Projects Labour Schedule Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">

														<div class="col-md-8 col-sm-8 col-xs-12 m-top-20 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
															<label for="time-half" class="col-sm-4 control-label">Select Excluded Job Categories:</label>
															<div class="col-sm-8">
																<?php 
																	$ls_job_category_arr = explode(",",$labour_sched_categories);
																	$ls_design_works = 0;
																	$ls_kiosk = 0;
																	$ls_full_fitout = 0;
																	$ls_refurbishment = 0;
																	$ls_strip_out = 0;
																	$ls_minor_works = 0;
																	$ls_maintenance = 0;
																	$ls_joinery_only = 0;
																	$ls_company = 0;

																	foreach ($ls_job_category_arr as &$value) {
																		if($value == "Design Works"){
																			$ls_design_works = 1;
																		}

																		if($value == "Kiosk"){
																			$ls_kiosk = 1;
																		}

																		if($value == "Full Fitout"){
																			$ls_full_fitout = 1;
																		}

																		if($value == "Refurbishment"){
																			$ls_refurbishment = 1;
																		}
																		if($value == "Strip Out"){
																			$ls_strip_out = 1;
																		}
																		if($value == "Minor Works"){
																			$ls_minor_works = 1;
																		}

																		if($value == "Maintenance"){
																			$ls_maintenance = 1;
																		}

																		if($value == "Joinery Only"){
																			$ls_joinery_only = 1;
																		}

																		if($value == "Company"){
																			$ls_company = 1;
																		}
																	}
																?>
																<table width = 100%>
																	<tr>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Design Works" <?php if($ls_design_works == 1){ echo 'checked'; } ?>></td>
																		<td>Design Works</td>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Kiosk" <?php if($ls_kiosk == 1){ echo 'checked'; } ?>></td>
																		<td>Kiosk</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Full Fitout" <?php if($ls_full_fitout == 1){ echo 'checked'; } ?>></td>
																		<td>Full Fitout</td>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Refurbishment" <?php if($ls_refurbishment == 1){ echo 'checked'; } ?>></td>
																		<td>Refurbishment</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Strip Out" <?php if($ls_strip_out == 1){ echo 'checked'; } ?>></td>
																		<td>Strip Out</td>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Minor Works" <?php if($ls_minor_works == 1){ echo 'checked'; } ?>></td>
																		<td>Minor Works</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Maintenance" <?php if($ls_maintenance == 1){ echo 'checked'; } ?>></td>
																		<td>Maintenance</td>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Joinery Only" <?php if($ls_joinery_only == 1){ echo 'checked'; } ?>></td>
																		<td>Joinery Only</td>
																	</tr>

																	<tr>
																		<td><input type="checkbox" name = "labour_sched_categories[]" value = "Company" <?php if($ls_company == 1){ echo 'checked'; } ?>></td>
																		<td>Company</td>
																	</tr>
																</table>
															</div>
														</div>

														<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
													        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Projects Labour Sched Defaults</button>
													    </div>
														
													</div>
												</div>
										    </form>




 



										    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/update_cat_pcc" id="update_cat_pcc">

												<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-calendar fa-lg"></i> Displayed Categpries: Project Completion Calendar</label>
														<button type="submit" class="btn btn-success btn-xs pull-right m-top-3"><i class="fa fa-floppy-o"></i> Save Category Selected</button>
													</div>

													<div class="box-area pad-5 clearfix">

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Company" id="select_display_compny_pcc">
																<label class="form-check-label" for="select_display_compny_pcc">Company</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Design Works" id="select_display_designworks_pcc">
																<label class="form-check-label" for="select_display_designworks_pcc">Design Works</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Full Fitout" id="select_display_fullfitout_pcc">
																<label class="form-check-label" for="select_display_fullfitout_pcc">Full Fitout</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Joinery Only" id="select_display_maintenannce_pcc">
																<label class="form-check-label" for="select_display_maintenannce_pcc">Joinery Only</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Kiosk" id="select_display_kiosk_pcc">
																<label class="form-check-label" for="select_display_kiosk_pcc">Kiosk</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Maintenance" id="select_display_maintenance_pcc">
																<label class="form-check-label" for="select_display_maintenance_pcc">Maintenance</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Minor Works" id="select_display_minor_works_pcc">
																<label class="form-check-label" for="select_display_minor_works_pcc">Minor Works</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Refurbishment" id="select_display_refurbishment_pcc">
																<label class="form-check-label" for="select_display_refurbishment_pcc">Refurbishment</label>
															</div>
														</div>

														<div class="col-md-4 col-sm-6 col-xs-6 ">
															<div class="form-check m-left-10">
																<input class="form-check-input  cat_pcc_dash" name="cat_pcc_dashboard[]" type="checkbox" value="Strip Out" id="select_display_strip_out_pcc">
																<label class="form-check-label" for="select_display_strip_out_pcc">Strip Out</label>
															</div>
														</div>
 
													
														
													</div>
												</div>
										    </form>






										    	<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-calendar fa-lg"></i> Induction Project Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">
														<div class="col-sm-6">
															<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_induction_project">
															<div class="col-sm-3 pad-5"><label for="time-half" class="pull-right">Select Job Categories:</label></div>
															<div class="col-sm-9">
																<?php 
																	$induction_categories_arr = explode(",",$induction_categories);
																	$ic_design_works = 0;
																	$ic_kiosk = 0;
																	$ic_full_fitout = 0;
																	$ic_refurbishment = 0;
																	$ic_strip_out = 0;
																	$ic_minor_works = 0;
																	$ic_maintenance = 0;

																	foreach ($induction_categories_arr as &$value) {
																		if($value == "Design Works"){
																			$ic_design_works = 1;
																		}

																		if($value == "Kiosk"){
																			$ic_kiosk = 1;
																		}

																		if($value == "Full Fitout"){
																			$ic_full_fitout = 1;
																		}

																		if($value == "Refurbishment"){
																			$ic_refurbishment = 1;
																		}
																	
																		if($value == "Strip Out"){
																			$ic_strip_out = 1;
																		}
																	
																		if($value == "Minor Works"){
																			$ic_minor_works = 1;
																		}

																		if($value == "Maintenance"){
																			$ic_maintenance = 1;
																		}
																	}
																?>
																<table width = 100%>
																	<tr>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Design Works" <?php if($ic_design_works == 1){ echo 'checked'; } ?>></td>
																		<td>Design Works</td>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Kiosk" <?php if($ic_kiosk == 1){ echo 'checked'; } ?>></td>
																		<td>Kiosk</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Full Fitout" <?php if($ic_full_fitout == 1){ echo 'checked'; } ?>></td>
																		<td>Full Fitout</td>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Refurbishment" <?php if($ic_refurbishment == 1){ echo 'checked'; } ?>></td>
																		<td>Refurbishment</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Strip Out" <?php if($ic_strip_out == 1){ echo 'checked'; } ?>></td>
																		<td>Strip Out</td>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Minor Works" <?php if($ic_minor_works == 1){ echo 'checked'; } ?>></td>
																		<td>Minor Works</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" name = "induction_categories[]" value = "Maintenance" <?php if($ic_maintenance == 1){ echo 'checked'; } ?>></td>
																		<td>Maintenance</td>
																	</tr>
																</table>

															</div>	

															<div class="col-sm-3 pad-5 text-right">Work Value: </div>
															<div class="col-sm-9 pad-5"><input type="text" name = "work_value" class = "form-control input-sm" value = "<?php echo number_format($induction_work_value,2) ?>"></div>

															<div class="col-sm-3 pad-5 text-right">Project Total: </div>
															<div class="col-sm-9 pad-5"><input type="text" name = "project_total" class = "form-control input-sm" value = "<?php echo number_format($induction_project_value,2) ?>"></div>
															
															<div class="col-sm-12 pad-5" style = "border-bottom: 1px solid #888">
													        	<button type="submit" class="btn btn-success pull-right"><i class="fa fa-floppy-o"></i> Save Induction Defaults</button>
													    	</div>
															</form>
													    	<div class="col-sm-12 pad-5">
													    		<label for=""><b>Exempted Projects</b></label>
													    	</div>
													    	<div class="col-sm-3 pad-5">Select Project Number</div>
													    	<div class="col-sm-7 pad-5">
													    		<select name="project_number" id="project_number" class = "form-control">
													    			<option v-for = "project_list in project_list" :value="project_list.project_id">{{ project_list.project_id }}</option>
													    		</select>
													    	</div>
													    	<div class="col-sm-2 pad-5"><button type = "button" class = "btn btn-success" id = "add_exempted_project" v-on:click="add_exempted_project">Add</button></div>
													    	<div class="col-sm-12" style = "height: 150px; overflow:auto">
																<table class = "table table-border" style = "font-size: 12px">
																	<thead>
																		<th></th>
																		<th>Project Number</th>
																		<th>Project Name</th>
																	</thead>
																	<tbody>
																		<tr v-for = "exempted_project_list in exempted_project_list">
																			<td><button type = "button" class = "btn btn-danger btn-sm" v-on:click="remove_exempted_project(exempted_project_list.induction_exempted_projects_id)">Remove</button></td>
																			<td>{{ exempted_project_list.project_id }}</td>
																			<td>{{ exempted_project_list.project_name | replaceApos }}</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>

														<div class="col-sm-6">
															<div class="col-sm-12 pad-5">
																<label for=""><b>Exempted Postcode</b></label>
															</div>
															<div class="col-sm-12 pad-5">
																<div class="col-sm-3 pad-5">State:</div>
																<div class="col-sm-9 pad-5">
																	<select name="" id="" class = "form-control input-sm" v-model = "induction_filter_state">
																		<option v-for = "state_list in state_list" :value="state_list.id">{{ state_list.name }}</option>
																	</select>
																</div>
																<div class="col-sm-3 pad-5" >Start POSTCODE:</div>
																<div class="col-sm-9 pad-5"><input type="text" class = "form-control input-sm" v-model = "induction_filter_start_postcode"></div>
																<div class="col-sm-3 pad-5">End POSTCODE:</div>
																<div class="col-sm-9 pad-5"><input type="text" class = "form-control input-sm" v-model = "induction_filter_ends_postcode"></div>

																<div class="col-sm-12"><button type = "button" class = "btn btn-success btn-sm pull-right" v-on:click = "add_exempted_postcode">Add</button></div>
															</div>

															<div class="col-sm-12" style = "height: 200px; overflow:auto">
																<table class = "table table-border" style = "font-size: 12px">
																	<thead>
																		<th></th>
																		<th>State Name</th>
																		<th>Range Postcode</th>
																	</thead>
																	<tbody>
																		<tr v-for = "exemptedPostcodeList in exemptedPostcodeList">
																			<td><button type = "button" class = "btn btn-danger btn-sm" v-on:click="remove_exempted_postcode(exemptedPostcodeList.induction_postcode_filters_id)">Remove</button></td>
																			<td>{{ exemptedPostcodeList.name }}</td>
																			<td v-if="exemptedPostcodeList.start_postcode == exemptedPostcodeList.end_postcode">{{ exemptedPostcodeList.start_postcode }}</td>
																			<td v-if="exemptedPostcodeList.start_postcode !== exemptedPostcodeList.end_postcode">{{ exemptedPostcodeList.start_postcode +" - "+ exemptedPostcodeList.end_postcode}}</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>

													</div>
												</div>

										    	<div class="box yellow-border">
													<div class="box-head yellow-bg pad-5 m-bottom-5">
														<label class="yellow-title"><i class="fa fa-paperclip fa-lg"></i> Doc Storage Defaults</label>
													</div>

													<div class="box-area pad-5 clearfix">
														<div class="col-sm-6">
															<div class="col-sm-12 pad-5"><b>Doc type that require authorization</b></div>
															<div class="col-sm-3 pad-5">Select Doc Type:</div>
															<div class="col-sm-6 pad-5">
																<select class = "form-control input-sm" v-model = "doc_type">
																	<option v-for = "doc_type_list in doc_type_list" :value = "doc_type_list.storage_doc_type_id">{{ doc_type_list.doc_type_name }}</option>
																</select>
															</div>
															<div class="col-sm-3 pad-5"><button type = "button" class = "btn btn-success btn-sm btn-block" v-on:click="add_doc_type_required">Add</button></div>

															<div class="col-sm-12 pad_5" style = "height: 200px; overflow: auto">
																 <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
													                <thead class="thead-dark">
													                    <tr>
													                    	<th></th>
													                    	<th>Doc Type</th>
													                    </tr>
													                </thead>
												                  	<tbody>
													                    <tr v-for = "doc_type_required_list in doc_type_required_list">
													                    	<td>
													                    		<button type = "button" class = "btn btn-danger btn-xs" v-on:click = "delete_doc_required(doc_type_required_list.doc_storage_required_notification_id)" title = "Remove Product"><i class="fa fa-trash"></i></button>
													                     	</td>
													                    	<td>{{ doc_type_required_list.doc_type_name }}</td>
													                    </tr>
													                </tbody>
													            </table>
															</div>

															<div class="col-sm-12 pad-5"  style = "font-size: 12px"><b>Receiver:</b></div>
															<div class="col-sm-12 pad-5" style = "border: 1px solid #888">
																<div class="col-sm-6 pad-5"><input type="checkbox" name="notif_receiver" v-model = "notif_receiver" value = "pm">Project Manager</div>
																<div class="col-sm-6 pad-5"><input type="checkbox" name="notif_receiver" v-model = "notif_receiver" value = "pa">Project Administrator</div>
																<div class="col-sm-6 pad-5"><input type="checkbox" name="notif_receiver" v-model = "notif_receiver" value = "set_out">Set Out</div>
																<div class="col-sm-6 pad-5"><input type="checkbox" name="notif_receiver" v-model = "notif_receiver" value = "joinery">Joinery Procurement</div>
															</div>

															<div class="clearfix"></div>

															<div class="col-sm-4 pad-5" style = "font-size: 12px">Construction Manager for WA:</div>
															<div class="col-sm-8 pad-5">
																<select class = "form-control input-sm" v-model = "cm_wa">
																	<option v-for = "users_list in users_list" :value = "users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
																</select>
															</div>

															<div class="clearfix"></div>

															<div class="col-sm-4 pad-5"  style = "font-size: 12px">Construction Manager for NSW:</div>
															<div class="col-sm-8 pad-5">
																<select class = "form-control input-sm" v-model = "cm_nsw">
																	<option v-for = "users_list in users_list" :value = "users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
																</select>
															</div>	

															<div class="col-sm-12 pad-5"><button type = "button" class = "btn btn-success btn-sm pull-right" v-on:click = "save_receiver">Save</button></div>


														</div>

														<div class="col-sm-6">
															<div class="col-sm-3 pad-5">Authorized By:</div>
															<div class="col-sm-9 pad-5">
																<select class = "form-control input-sm" v-model = "authorize_role_id">
																	<option v-for = "role_list in role_list" :value = "role_list.role_id">{{ role_list.role_types }}</option>
																</select>

															</div>

															<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be send to contractor/supplier once file is approved. <i class="fa fa-quote-right"></i></p></strong></div>

															<div class="col-sm-3 pad-5">Subject:</div>
															<div class="col-sm-9 pad-5"><input type="text" class="form-control input-sm" v-model = "email_subject"></div>
															<div class="col-sm-12">Message</div>
															<div class="col-sm-12 pad-5">
																<textarea class = "form-control input-sm" style = "height: 200px" v-model = "email_content"></textarea>
															</div>

															<div class="col-sm-12 pad-5"><button type = "button" class = "btn btn-success btn-sm pull-right" v-on:click = "save_doc_storage_default">Save</button></div>
														</div>

													</div>
												</div>

										</div>
									</div>

									<p><hr /></p>

									<div class="box blue-border m-top-0">
										<div class="box-head blue-bg pad-5" style="">
											<label class="blue-title"><i class="fa fa-building fa-lg"></i> Company Default Settings </label>
										</div>

										<div class="box-area clearfix pad-10">
											<div class="box yellow-border">

												<div class="box-head yellow-bg pad-5 m-bottom-5" style="background: none !important; background-color: #fcf8e3 !important;">
													<label class="yellow-title"><i class="fa fa-certificate fa-lg"></i> Induction Required License and Certificates</label>
												</div>
												<div class="box-area pad-10 clearfix">
													<div class="row">
														<div class="col-sm-12">
															<div class="col-sm-1">
																<label for="">Activity: </label>
															</div>
															<div class="col-sm-2">
																<select class = "form-control input-sm" v-model="activity_id">
																	<option value="0">All</option>
																	<option v-for= "jobCategories in jobCategories" :value="jobCategories.job_category_id">{{ jobCategories.job_category | reformat }}</option>
																</select>
															</div>

															<div class="col-sm-2">
																<label for="">License/Certificate: </label>
															</div>
															<div class="col-sm-2">
																<select class = "form-control input-sm" v-model = "license_cert_id">
																	<option v-for = "licenseCertTypes in licenseCertTypes" :value="licenseCertTypes.licences_certs_types_id">{{ licenseCertTypes.lc_type_name }}</option>
																</select>
															</div>

															<div class="col-sm-1">
																<label for="">State: </label>
															</div>
															<div class="col-sm-2">
																<select class = "form-control input-sm" v-model = "lc_state_id">
																	<option value = "0">All</option>
																	<option v-for = "state_list in state_list" :value="state_list.id">{{ state_list.name }}</option>
																</select>
															</div>

															<div class="col-sm-2"><button type = "button" class = "btn btn-success btn-sm" v-on:click="addRequiredLC">Add Required License / Certificates</button></div>

															
														</div>
														<div class="col-sm-12"></div>
													</div>
													<div class="row">
														<div class="col-sm-12 pad-5" style = "height: 200px; overflow:auto">
										                    <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
										                      	<thead>
										                        	<tr>
											                          	<th style = "width: 20px"></th>
											                          	<th>Activity Name</th>
											                          	<th>License / Certificate Type</th>
											                          	<th>State</th>
										                        	</tr>  
										                      	</thead>
										                      	<tbody>
										                      		<tr v-for="requiredLC in requiredLC">
										                      			<td style = "width: 20px"><button type = "button" class = "btn btn-danger btn-sm" v-on:click="removeRequiredLC(requiredLC.required_license_certificate_id)">Remove</button></td>
										                      			<td v-if = "requiredLC.job_category_id !== '0'">{{ requiredLC.job_category | reformat }}</td>
										                      			<td v-if = "requiredLC.job_category_id == '0'">All</td>
										                      			<td>{{ requiredLC.lc_type_name }}</td>
										                      			<td>{{ requiredLC.state_name }}</td>
										                      		</tr>
										                      	</tbody>
										                    </table>
														</div>
													</div>
												</div>
											</div>

<!--  INDUCTION =================================================== -->

												<div class="box yellow-border">

													<div class="box-head yellow-bg pad-5 m-bottom-5" style="background: none !important; background-color: #fcf8e3 !important;">
														<label class="yellow-title"><i class="fa fa-file-video-o fa-lg"></i> Induction Slide Defaults</label>
													</div>
													<div class="box-area pad-10 clearfix">
														<div class="row">
															<div class="col-sm-6">
																<div class="col-sm-12 pad-5"><b>Slide 4 (Amenities and Emergency Exits) Slide Notes</b></div>
																<div class="col-sm-12 pad-5"><textarea id="txt_slide4_note" class = "form-control input-sm" style = "height: 100px"><?php echo $induction_slide4_notes ?></textarea></div>
															</div>
															<div class="col-sm-6">
																<div class="col-sm-12 pad-5"><b>Slide 6 (Personal Protective Equipment) Slide Notes</b></div>
																<div class="col-sm-12 pad-5"><textarea id="txt_slide6_note" class = "form-control input-sm" style = "height: 100px"><?php echo $induction_slide6_notes ?></textarea></div>
															</div>
														</div>
														
														<div class="row">
															<div class="col-sm-12"><button type = "button" class = "btn btn-success pull-right" onclick="saveSlideNotes()">Save</button></div>
														</div>


													</div>
												</div>


<!--  INDUCTION =================================================== -->

<div id="archive_documents_settings" class=""><p><br /></p><p><br /></p></div>

	


<!-- ********************************* Archive Documents ********************************************** -->
				
				
					<div class="box yellow-border m-top-10" >
						<!-- <div class="col-sm-12"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-edit fa-lg"></i> Archive Documents Settings</label>
						</div>

						<div class="box-area clearfix ">		




							<div class="col-md-4 col-sm-6 col-xs-12 clearfix pad-10">
<p class="h4" style="font-size: 16px;"><strong>Archive Reminder Settings</strong>  <em class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Weekend days is excluded for the email sendout."></em></p>


								<hr style="margin-top: 5px;    margin-bottom: 10px;" />







								<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/archive_documents_settings">

									<div class=" clearfix m-top-10 m-bottom-10">
										<div class="input-group">
											<span class="input-group-addon" id=""><i class="fa fa-calendar"></i> Reminder Starts Before Expiry </span>
											<select class="form-control" name="ad_no_of_weeks" id="ad_no_of_weeks">
												<option value="">Select</option>
												<option value="1">1 Week</option>
												<option value="2">2 Weeks</option>
												<option value="3">3 Weeks</option>
												<option value="4">4 Weeks</option>
												<option value="5">5 Weeks</option>
											</select>
										</div>				
									</div>

									<div class=" clearfix m-top-10 m-bottom-10"> 
										<div class="input-group">
											<span class="input-group-addon" id=""><i class="fa fa-calendar"></i> Day Reminder </span>
											<select class="form-control" name="ad_day_reminder" id="ad_day_reminder">
												<option value="">Select</option>
												<option value="Monday">Monday</option>
												<option value="Tuesday">Tuesday</option>
												<option value="Wednesday">Wednesday</option>
												<option value="Thursday">Thursday</option>
												<option value="Friday">Friday</option>
											</select>
										</div>				
									</div> 




									<div class="input-group  m-bottom-10 "> 
										<span class="input-group-addon"  ><i class="fa fa-envelope-o"></i> Expired Document Reminder</span> 

										<select class="form-control" name="remind_emp_on_expire" id="remind_emp_on_expire">		
											<option value="" class="hide">Select</option>							
											<?php foreach($users as $key => $user_data): ?>
												<option value="<?php echo $user_data->user_id; ?>" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
											<?php endforeach; ?>
										</select>


									</div>

									<div class="input-group  m-bottom-10 "> 
										<span class="input-group-addon"  ><i class="fa fa-envelope-o"></i> CC Email Expired Reminder</span> 
										<input type="email" id="ad_remind_late_email"  name="ad_remind_late_email" placeholder="Optional" value="<?php echo $static_defaults[0]->remind_cc_email; ?>" class="form-control"  autocomplete="off"> 

									</div>

									<button type="submit"  class="btn btn-success pull-right" type="button"> <i class="fa fa-floppy-o"></i> Save</button> 

								</form>
							</div>






							<div class="col-md-4 col-sm-6 col-xs-12  clearfix pad-10">




								<p class="h4" style="font-size: 16px;"><strong>Add New Archive Type</strong>  <em class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Weekend days is excluded for the email sendout."></em></p>
								<hr style="margin:0; padding:3px 0 5px 0;" />


								<form method="post" action="<?php echo current_url(); ?>/new_arc_doc_type">
									<div class="input-group m-bottom-15">
										<span class="input-group-addon"  ><i class="fa fa-file"></i></span>
										<input class="form-control" placeholder="New Archive Name" name='archive_name'  autocomplete="off">
										<span class="input-group-btn"> <input type="submit"  class="btn btn-primary" value="Save"> </span>
									</div>
								</form>

	  
								<form method="post" autocomplete="off" action="<?php echo current_url(); ?>/set_assignmnt_doc_type">
									<p class="h4" style="font-size: 16px;"><strong>Archive Assignments</strong></p>
									<hr  style="margin:0; padding:3px 0 5px 0;"/>

									<div class="input-group m-bottom-10">
										<span id="" class="input-group-addon">Document Type</span>
										<select class="form-control" name="doc_type_name">
											<option disabled selected value="">Select Document Type</option>
											<?php foreach($archive_types as $key => $archive_data): ?>
												<option value="<?php echo $archive_data->registry_types_id; ?>_<?php echo $archive_data->registry_name; ?>" > <?php echo $archive_data->registry_name; ?> </option>
											<?php endforeach; ?>
										</select>
									</div>

									<div class="input-group  m-bottom-10 "> 
										<span class="input-group-addon"  >Assign Employee</span> 

										<select class="form-control" name="emp_name" id="assgn_emp_name">
											<option disabled selected value="" class="hide">Select Employee</option>
											<?php foreach($users as $key => $user_data): ?>
												<option value="<?php echo $user_data->user_id; ?>" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
											<?php endforeach; ?>
										</select>
									</div>


									<div class=" clearfix m-top-10 m-bottom-10"> 
										<div class="input-group">
											<span class="input-group-addon" id=""><i class="fa fa-calendar"></i> Date Expiry </span>
											<input type="text" placeholder="DD/MM/YYYY" class="form-control datepicker" data-date-format="dd/mm/yyyy" id="date_expiry_arhdoc" name="date_expiry_arhdoc" value="" style="z-index:0 !important;"  autocomplete="off">
											<span class="input-group-btn"> <button type="submit"  class="btn btn-success" type="button"> <i class="fa fa-floppy-o"></i> Save</button> </span> 
										</div>				
									</div> 

								</form>
							</div>

							<div class="col-md-4 col-sm-6 col-xs-12  clearfix pad-10">

								<p class="h4" style="font-size:16px;"><strong>Employee Archive Assignments</strong></p>
								<hr style="margin-top: 5px;    margin-bottom: 10px;" />

								<?php foreach($archive_types as $key => $archive_data): ?>
									<p class="m-bottom-15">
										<strong><?php echo $archive_data->registry_name; ?>:</strong>
										<?php echo $this->admin->list_employee_per_archive($archive_data->registry_types_id); ?>
									</p>
								<?php endforeach; ?>								
							</div>

							<script type="text/javascript">
								setTimeout(function () {
									$('select#ad_day_reminder').val('<?php echo $static_defaults[0]->day_remind; ?>'); 
									$('select#ad_no_of_weeks').val('<?php echo $static_defaults[0]->no_of_weeks; ?>');
									$('select#remind_emp_on_expire').val('<?php echo $static_defaults[0]->remind_late_email; ?>');

								}, 500);
							</script>


						</div>

						<!-- </div> -->
					</div>


<!-- ********************************* Archive Documents ********************************************** -->



					<div id="client_supply_settings" class=""><p><br /></p><p><br /></p></div>

<!-- ********************************* Client Supply ********************************************** -->
					<div class="box yellow-border m-top-10" >
						<!-- <div class="col-sm-12"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-cube fa-lg"></i> Client Supply Settings</label>
						</div>

						<div class="box-area clearfix ">		

							<div class="col-md-4 col-sm-6 col-xs-12 clearfix pad-10">


								<p class="h4" style="font-size: 16px;"><strong>Company Settings</strong>  <em class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Weekend days is excluded for the email sendout."></em></p>
								<hr style="margin: 0px; padding: 3px 0px 5px;">



								<form method="post" action="<?php echo base_url(); ?>admin/update_emp_supply_remnd">
									<div class="input-group m-bottom-10">

										<span class="input-group-addon"><i class="fa fa-user"></i> WA Employee &nbsp; </span> 


										<?php $wa_emp_assnmt = $this->admin->get_employee_supply_rem(5); ?>

										<input type="hidden" name="client_supply_settings_id" value="<?php echo $wa_emp_assnmt['client_supply_settings_id']; ?>">
										<select class="form-control" id="remind_emp_csup_user" name="remind_emp_csup_user"  tabindex="-1">


											<option value="" disabled="" > Select</option>							
											<?php foreach($users as $key => $user_data): ?>
												<?php if($user_data->user_focus_company_id == 5): ?>
												

												<?php if($wa_emp_assnmt['user_id'] == $user_data->user_id ): ?>
													<option value="<?php echo $user_data->user_id; ?>" selected="1" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
												<?php else: ?>
													<option value="<?php echo $user_data->user_id; ?>" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
												<?php endif; ?>



												<?php endif; ?>
											<?php endforeach; ?> 
										</select>
									</div>
									<div class="input-group m-bottom-10">


										<span class="input-group-addon"><i class="fa fa-calendar"></i> Days Before Delivery To Site - Reminder</span>

										<input placeholder="Number of Days" name="days_lead_reminder" autocomplete="off" class="form-control" value="<?php echo $wa_emp_assnmt['reminder_lead_days']; ?>"> 
										<span class="input-group-addon"> Default <?php echo $static_defaults[0]->client_supply_reminder_dys; ?> Days </span> 
									</div>


									<div class="input-group m-bottom-10">
										<span class="input-group-addon"><i class="fa fa-envelop"></i> CC Email</span>
										<input type="email" placeholder="cc email" name="cc_email" autocomplete="off" class="form-control" value="<?php echo $wa_emp_assnmt['cc_email']; ?>">  
										<span class="input-group-btn"><input type="submit" value="Save" class="btn btn-primary"></span>
									</div>



								</form>

								<p><hr style="margin: 0px; padding:5px 0px 8px;"></p>





								<form method="post" action="<?php echo base_url(); ?>admin/update_emp_supply_remnd">
									<div class="input-group m-bottom-10">

										<span class="input-group-addon"><i class="fa fa-user"></i> NSW Employee</span> 	
										<?php $nsw_emp_assnmt = $this->admin->get_employee_supply_rem(6); ?>
										<input type="hidden" name="client_supply_settings_id" value="<?php echo $nsw_emp_assnmt['client_supply_settings_id']; ?>">
										<select class="form-control" id="remind_emp_csup_user" name="remind_emp_csup_user"  tabindex="-1">


											<option value="" disabled="" selected="1"> Select</option>							
											<?php foreach($users as $key => $user_data): ?>
												<?php if($user_data->user_focus_company_id == 6): ?>


												<?php if($nsw_emp_assnmt['user_id'] == $user_data->user_id ): ?>
													<option value="<?php echo $user_data->user_id; ?>" selected="1" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
												<?php else: ?>
													<option value="<?php echo $user_data->user_id; ?>" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
												<?php endif; ?>


												<?php endif; ?>
											<?php endforeach; ?> 
										</select>

									</div>
									<div class="input-group m-bottom-10">


										<span class="input-group-addon"><i class="fa fa-calendar"></i> Days Before Delivery To Site - Reminder</span>

										<input placeholder="Number of Days" name="days_lead_reminder" autocomplete="off" class="form-control" value="<?php echo $nsw_emp_assnmt['reminder_lead_days']; ?>"> 
										<span class="input-group-addon"> Default <?php echo $static_defaults[0]->client_supply_reminder_dys; ?> Days</span> 
									</div>



									<div class="input-group m-bottom-10">
										<span class="input-group-addon"><i class="fa fa-envelop"></i> CC Email</span>
										<input type="email" placeholder="cc email" name="cc_email" autocomplete="off" class="form-control" value="<?php echo $nsw_emp_assnmt['cc_email']; ?>">  
										<span class="input-group-btn"><input type="submit" value="Save" class="btn btn-primary"></span>
									</div>



								</form>

 
							</div>

							<div class="col-md-4 col-sm-6 col-xs-12  clearfix pad-10">

								<p class="h4" style="font-size: 16px;"><strong>System Days Lead Reminder</strong> <em title="" data-html="true" data-placement="top" data-original-title="Weekend days is excluded for the email sendout." class="fa fa-info-circle tooltip-enabled"></em></p>
								<hr style="margin: 0px; padding: 3px 0px 5px;">

								<form method="post" action="<?php echo base_url(); ?>admin/update_lead_days">
									<div class="input-group m-bottom-10">

										<span class="input-group-addon"><i class="fa fa-calendar"></i> Default Days Reminder</span> 
										<input class="form-control" placeholder="Number of Lead Days" name="days" autocomplete="off" value="<?php echo $static_defaults[0]->client_supply_reminder_dys; ?>" > 
							
										<span class="input-group-btn"><input type="submit" value="Save" class="btn btn-primary"></span>

									</div>
								</form>


								<form method="post" action="<?php echo base_url(); ?>admin/update_weeks_delivery">
									<div class="input-group m-bottom-10">

										<span class="input-group-addon"><i class="fa fa-calendar"></i> Delivery After Completion Date</span> 
										<input class="form-control" placeholder="Number of Lead Days" name="weeks" autocomplete="off" value="<?php echo $static_defaults[0]->weeks_delivery; ?>" > 
										<span class="input-group-addon"><i class="fa fa-calendar"></i> Weeks</span> 
							
										<span class="input-group-btn"><input type="submit" value="Save" class="btn btn-primary"></span>

									</div>
								</form>

								<p><hr style="margin: 0px; padding: 3px 0px 5px;"></p>



								<p class="h4" style="font-size: 16px;"><strong><br />Setup Warehouse Assignment</strong></p>
								<hr style="margin: 0px; padding: 3px 0px 5px;">



								<form method="post" action="<?php echo base_url(); ?>admin/set_company_warehouse">
									<div class="input-group m-bottom-10">

										<span class="input-group-addon"><i class="fa fa-user"></i> Company </span>
										<select class="form-control" id="company_brand_name" name="company_brand_name"  tabindex="-1">
											<option value="" disabled="" selected="1"> Select</option>							
											<?php echo $this->admin->get_client_companies(1); ?>
										</select>
									</div>
									<div class="input-group m-bottom-10">


										<span class="input-group-addon"> Warehouse</span>




										<select class="form-control" id="warehouse_location" name="warehouse_location"  tabindex="-1">
											<option value="" disabled="" selected="1"> Select</option>
											<?php foreach ($warehouse as $key => $wh_data) {
												echo '<option value="'.$wh_data->warehouse_id.'">'.$wh_data->company_name.' - '.$wh_data->location.'</option>';
											} ?>
											
										</select>

 

										<span class="input-group-btn"><input type="submit" value="Save" class="btn btn-primary"></span>
									</div>
 
 
								</form>


 	 
							</div>




							<div class="col-md-4 col-sm-6 col-xs-12  clearfix pad-10">
								


								<p class="h4" style="font-size: 16px;"><strong>Assignments</strong></p>
								<hr style="margin: 0px; padding: 3px 0px 5px;">


								<?php foreach ($assigned_warehouse as $key => $assgn_wh_data) {
									echo '<p> <em class="fa fa-chevron-circle-right"></em> <strong>'.$assgn_wh_data->client_name.'</strong> - '.$assgn_wh_data->company_name.', '.$assgn_wh_data->location.' <a id="xxxx" class="btn btn-xs btn-danger pull-right" href="?delcs='.$assgn_wh_data->client_storage_id.'"><em class="fa fa-trash-o"></em></a></p>';
								} ?>


							</div>
						</div>
 

						<!-- </div> -->
					</div>

					<div id="" class=""><p><br /></p></div>

<!-- ********************************* Client Supply ********************************************** -->





										</div>
									</div>

									<p><hr /></p>


								<div class="box blue-border m-top-0">
									<div class="box-head blue-bg pad-5">
										<label class="blue-title"><i class="fa fa-envelope-open fa-lg"></i> Job Book and CPO/CQR Settings</label>
									</div>

									<div class="box-area clearfix pad-10">

										<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/invoice_email">

											<?php if(@$invoice_email_errors): ?>
												<div class="no-pad-t">
													<div class="border-less-box alert alert-danger fade in">
														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
														<h4>Oh snap! You got an error!</h4>
														<?php echo $invoice_email_errors;?>
													</div>
												</div>
											<?php endif; ?>

											<?php if(@$this->session->flashdata('invoice_default_email')): ?>
												<div class="no-pad-t">
													<div class="border-less-box alert alert-success fade in">
														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
														<h4>Cheers!</h4>
														<?php echo $this->session->flashdata('invoice_default_email');?>
													</div>
												</div>
											<?php endif; ?>

											<div class="box yellow-border m-top-0">
												<div class="box-head yellow-bg pad-5 m-bottom-5">
													<label class="yellow-title"><i class="fa fa-mail-forward fa-lg"></i> Job Book Email Defaults</label>
												</div>

												<div class="box-area pad-5 clearfix">

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('recipient_email')){ echo 'has-error';} ?>">
														<label for="recipient_email" class="col-sm-4 control-label">Recipient Email</label>
														<div class="col-sm-8">
															<div class="input-group">
																<span class="input-group-addon" id=""><i class="fa fa-envelope-o"></i></span>
																<input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="Recipient Email" value="<?php echo $static_defaults[0]->invoice_to; ?>">
															</div>
														</div>
													</div>

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('optional_cc_email')){ echo 'has-error';} ?>">
														<label for="optional_cc_email" class="col-sm-4 control-label">Optional CC Email</label>
														<div class="col-sm-8">
															<div class="input-group">
																<span class="input-group-addon" id=""><i class="fa fa-envelope-o"></i></span>
																<input type="email" class="form-control" id="optional_cc_email" name="optional_cc_email" placeholder="Optional CC Email" value="<?php echo $static_defaults[0]->invoice_cc; ?>">
															</div>
														</div>
													</div>

													<div class="clearfix col-xs-12 text-center"><strong> <p><i class="fa fa-quote-left"></i> Copies are sent to the Project Manager &amp; the Sender (creator) PLUS the above <i class="fa fa-quote-right"></i></p></strong></div>
													

													<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
												        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Emails</button>
												    </div>
												</div>
											</div>

									    </form>	

										<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_notes">

											<?php if(@$default_errors): ?>
												<div class="no-pad-t">
													<div class="border-less-box alert alert-danger fade in">
														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
														<h4>Oh snap! You got an error!</h4>
														<?php echo $default_errors;?>
													</div>
												</div>
											<?php endif; ?>


											<?php if(@$this->session->flashdata('update_default')): ?>
												<div class="no-pad-t">
													<div class="border-less-box alert alert-success fade in">
														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
														<h4>Cheers!</h4>
														<?php echo $this->session->flashdata('update_default');?>
													</div>
												</div>
											<?php endif; ?>

											<div class="box yellow-border">
												<div class="box-head yellow-bg pad-5 m-bottom-5">
													<label class="yellow-title"><i class="fa fa-sticky-note fa-lg"></i> CPO and CQR Notes Defaults</label>
												</div>

												<div class="box-area pad-5 clearfix">

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
														<label for="time-half" class="col-sm-4 control-label">CQR Notes with Insurance</label>
														<div class="col-sm-8">
															<textarea class="form-control" id="cqr_notes_w_ins" name="cqr_notes_w_ins" style = "height: 60px " maxlength="90"><?php echo ($this->input->post('cqr_notes_w_ins') ?  $this->input->post('cqr_notes_w_ins') : $cqr_notes_w_insurance ); ?></textarea>
														</div>
													</div>

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
														<label for="double-time" class="col-sm-4 control-label">CQR Notes No Insurance</label>
														<div class="col-sm-8">
															<textarea class="form-control" id="cqr_notes_no_ins" name="cqr_notes_no_ins" style = "height: 60px " maxlength="90"><?php echo ($this->input->post('cqr_notes_no_ins') ?  $this->input->post('cqr_notes_no_ins') : $cqr_notes_no_insurance ); ?></textarea>
														</div>
													</div>

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
														<label for="time-half" class="col-sm-4 control-label">CPO Notes with Insurance</label>
														<div class="col-sm-8">
															<textarea class="form-control" id="cpo_notes_w_ins" name="cpo_notes_w_ins" style = "height: 60px " maxlength="90"><?php echo ($this->input->post('cpo_notes_w_ins') ?  $this->input->post('cpo_notes_w_ins') : $cpo_notes_w_insurance ); ?></textarea>
														</div>
													</div>

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
														<label for="double-time" class="col-sm-4 control-label">CPO Notes No Insurance</label>
														<div class="col-sm-8">
															<textarea class="form-control" id="cpo_notes_no_ins" name="cpo_notes_no_ins" style = "height: 60px " maxlength="90"><?php echo ($this->input->post('cpo_notes_no_ins') ?  $this->input->post('cpo_notes_no_ins') : $cpo_notes_no_insurance ); ?></textarea>
														</div>
													</div>
													
													<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
												        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Notes</button>
												    </div>

												</div>
											</div>
									    </form>

									    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_progress_report">

											<div class="box yellow-border">
												<div class="box-head yellow-bg pad-5 m-bottom-5">
													<label class="yellow-title"><i class="fa fa-user-plus fa-lg"></i> Joinery Primary Email Defaults</label>
												</div>

												<div class="box-area pad-5 clearfix">

													<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('progress_report_categories')){ echo 'has-error';} ?>">
														<label for="progress_report_categories" class="col-sm-4 control-label">Select User:</label>
														<div class="col-sm-8">
															<div class="col-sm-12" style = "padding: 5px"><input type = "text" class = "form-control input-sm" onblur="search_user()" id = "txt_search_user"></div>
															<div class="col-sm-12" style = "padding: 5px; height: 100px; overflow:auto; border: 1px solid #888" id = "user_list">
															</div>
														</div>

													</div>

													<div class="col-sm-offset-1 col-md-4 col-sm-4 col-xs-12 m-bottom-10 clearfix <?php if(form_error('progress_report_categories')){ echo 'has-error';} ?>">
														<div class="col-sm-12" style = "padding: 5px"><button type = "button" class = "btn btn-sm btn-success" id = "set_primary">Set as Primary</button><button type = "button" class = "btn btn-sm pull-right btn-danger" id = "remove_joinery_user">Remove</button></div>

														<div class="col-sm-12" style = "padding: 5px; border: 1px solid; height: 100px; overflow:auto" id = "joinery_selected_user">
														</div>
													</div>

													
												</div>
											</div>

											<!-- <div class="m-top-15 clearfix">
										    	<div>
										        	<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Progress Reports Defaults</button>
										        </div>
										    </div>	 -->
											<script>
												var filter = '';
												var baseurl = '<?php echo site_url(); //you have to load the "url_helper" to use this function ?>';
												$.post(baseurl+"admin/fetch_users_list_table",
											    {
											      	filter: filter
											    },
											    function(result){
											    	$("#user_list").html(result);
											    });

												$.post(baseurl+"admin/joinery_selected_user",
											    {
											    },
											    function(result){
											    	$("#joinery_selected_user").html(result);
											    });
										
											    window.search_user = function(){
											    	filter = $('#txt_search_user').val();
											    	$.post(baseurl+"admin/fetch_users_list_table",
												    {
												      	filter: filter
												    },
												    function(result){
												    	$("#user_list").html(result);
												    });
											    }

											    window.joinery_select_user = function(a){
											    	var user_id = a;
											    	$.post(baseurl+"admin/insert_joinery_user",
												    {
												      	user_id: user_id
												    },
												    function(result){
												    	alert("User Set");
												    	$.post(baseurl+"admin/fetch_users_list_table",
													    {
													      	filter: filter
													    },
													    function(result){
													    	$("#user_list").html(result);
													    });

													    $.post(baseurl+"admin/joinery_selected_user",
													    {
													    },
													    function(result){
													    	$("#joinery_selected_user").html(result);
													    });
												    });

											    }
											    var joinery_user_responsible_id = 0;
											    window.select_joinery_user = function(a){
											    	joinery_user_responsible_id = a;
											    }

											    $("#set_primary").click(function(){
											    	$.post(baseurl+"admin/joinery_set_primary",
												    {
												      	joinery_user_responsible_id: joinery_user_responsible_id
												    },
												    function(result){
												    	alert("Selected User was set as Primary");
												    	
													    $.post(baseurl+"admin/joinery_selected_user",
													    {
													    },
													    function(result){
													    	$("#joinery_selected_user").html(result);
													    });
												    });
											    });

											    $("#remove_joinery_user").click(function(){
											    	$.post(baseurl+"admin/joinery_remove_user",
												    {
												      	joinery_user_responsible_id: joinery_user_responsible_id
												    },
												    function(result){
												    	alert("Selected User was removed from joinery user");
												    	
													    $.post(baseurl+"admin/joinery_selected_user",
													    {
													    },
													    function(result){
													    	$("#joinery_selected_user").html(result);
													    });
												    });
											    });
											    
											</script>							    
									    </form>

									</div>
								</div>

<p><hr /></p>

<div class="box blue-border m-top-0">
	<div class="box-head blue-bg pad-5">
		<label class="blue-title"><i class="fa fa-envelope-open fa-lg"></i> Email Default Settings</label>
	</div>

	<div class="box-area clearfix pad-10">
		<div class="box yellow-border">
			<div class="box-head yellow-bg pad-5 m-bottom-5">
				<label class="yellow-title"><i class="fa fa-shield fa-lg"></i> Site Labour Email Defaults</label>
			</div>
			<div class="box-area pad-5 clearfix">
				<div class="col-sm-6 pad-5">
					<div class="col-sm-12 pad-5">
						<div class="panel panel-primary">
					      	<div class="panel-heading">Site Labour Review Notification</div>
					      	<div class="panel-body">
						      	<div class="col-sm-8">
									<select class="form-control find_contact_person chosen" id ="reviewer" style="width: 100%;" tabindex="25">																										
										<?php //$this->company->company_list('dropdown'); ?>
										<option value=''>Select User Name*</option>													
										<?php $this->admin->fetch_users_list(); ?>														
									</select>
									<?php echo $this->input->post('user_id'); ?>
									<script type="text/javascript">$('select#user_assigned_induction').val('<?php echo $induction_assigned_user; ?>');</script>
								</div>
								<div class="col-sm-4 pad-5"><button type = "button" class = "btn btn-success btn-sm" v-on:click = "addReviewer(1)">Add</button></div>
								<div class="col-sm-12 pad-5">
									<table id="sitelabourTable1" class="table table-striped table-bordered table-hover fancyTable" cellspacing="0" width="100%">
					                    <thead> 
					                        <tr> 
					                          	<td style = "width: 20px"></td>
					                            <td>User Name</td>
					                        </tr>
				                        </thead> 
				                        <tbody v-for="sl_email_users in sl_email_users" v-if = "sl_email_users.email_for == 1">
				                        	<tr>
				                        		<td><button type = "button" class = "btn btn-danger btn-sm" title = "Remove User" v-on:click="removeReviewer(sl_email_users.site_labour_email_defaults_id)"><i class="fa fa-times"></i></button></td>
				                        		<td>{{ sl_email_users.user_first_name +" "+ sl_email_users.user_last_name }}</td>
				                        	</tr>
				                        </tbody>
				                    </table>
								</div>
					      	</div>
					    </div>
						
					</div>

				</div>
				<div class="col-sm-6 pad-5">
					<div class="col-sm-12 pad-5">
						<div class="panel panel-primary">
					      	<div class="panel-heading">Site Labour Approved Notification</div>
					      	<div class="panel-body">
						      	<div class="col-sm-8">
									<select class="form-control find_contact_person chosen" id ="accounts" style="width: 100%;" tabindex="25">																										
										<?php //$this->company->company_list('dropdown'); ?>
										<option value=''>Select User Name*</option>													
										<?php $this->admin->fetch_users_list(); ?>														
									</select>
									<?php echo $this->input->post('user_id'); ?>
									<script type="text/javascript">$('select#user_assigned_induction').val('<?php echo $induction_assigned_user; ?>');</script>
								</div>
								<div class="col-sm-4 pad-5"><button type = "button" class = "btn btn-success btn-sm" v-on:click = "addReviewer(2)">Add</button></div>
								<div class="col-sm-12 pad-5">
									<table id="sitelabourTable1" class="table table-striped table-bordered table-hover fancyTable" cellspacing="0" width="100%">
					                    <thead> 
					                        <tr> 
					                          	<td style = "width: 20px"></td>
					                            <td>User Name</td>
					                        </tr>
				                        </thead> 
				                        <tbody v-for="sl_email_users in sl_email_users" v-if = "sl_email_users.email_for == 2">
				                        	<tr>
				                        		<td><button type = "button" class = "btn btn-danger btn-sm" title = "Remove User" v-on:click="removeReviewer(sl_email_users.site_labour_email_defaults_id)"><i class="fa fa-times"></i></button></td>
				                        		<td>{{ sl_email_users.user_first_name +" "+ sl_email_users.user_last_name }}</td>
				                        	</tr>
				                        </tbody>
				                    </table>
								</div>
					      	</div>
					    </div>
					</div>
				</div>
			</div>
		</div>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_induction">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-shield fa-lg"></i> Induction Health and Safety Email Defaults</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_induction" name="sender_name_induction" value = "<?php echo ($this->input->post('sender_name_induction') ?  $this->input->post('sender_name_induction') : $induction_sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_induction" name="sender_email_induction" value = "<?php echo ($this->input->post('sender_email_induction') ?  $this->input->post('sender_email_induction') : $induction_sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_induction" name="subject_induction" value = "<?php echo ($this->input->post('subject_induction') ?  $this->input->post('subject_induction') : $induction_subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_induction" name="bcc_email_induction" value = "<?php echo ($this->input->post('bcc_email_induction') ?  $this->input->post('bcc_email_induction') : $induction_bcc_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_induction" class="form-control find_contact_person chosen" id="user_assigned_induction" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php $this->admin->fetch_users_list(); ?>														
							</select>
							<?php echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_induction').val('<?php echo $induction_assigned_user; ?>');</script>
							
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be send to contractor with the a link to fill up Contractos Site Staff for induction. <i class="fa fa-quote-right"></i></p></strong></div>
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">New Message to Contactor for Induction:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_induction" name="email_msg_induction" style="height: 100px "><?php echo ($this->input->post('email_msg_induction') ?  $this->input->post('email_msg_induction') : $induction_message_content ); ?></textarea>
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be send out automatically to contractor a year after it was entered. <i class="fa fa-quote-right"></i></p></strong></div>

					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">Will be send out automatically to contractor a year after it was entered:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_induction_update" name="email_msg_induction_update" style="height: 100px "><?php echo ($this->input->post('email_msg_induction_update') ?  $this->input->post('email_msg_induction_update') : $induction_message_content_update ); ?></textarea>
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be to Contractor's Site Staff with the link of the Video for Site Specific Induction. <i class="fa fa-quote-right"></i></p></strong></div>

					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">Message to Site Staff for Site Specific Induction:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_induction_video" name="email_msg_induction_video" style="height: 100px "><?php echo ($this->input->post('email_msg_induction_video') ?  $this->input->post('email_msg_induction_video') : $induction_message_content_video ); ?></textarea>
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be send to Focus Site Staff with the link of the Video for Site Specific Induction. <i class="fa fa-quote-right"></i></p></strong></div>

					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">Message to Site Staff for Site Specific Induction:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_induction_video_fss" name="email_msg_induction_video_fss" style="height: 100px "><?php echo ($this->input->post('email_msg_induction_video_fss') ?  $this->input->post('email_msg_induction_video_fss') : $induction_message_content_video_fss ); ?></textarea>
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Will be send to Clients Contractor's Site Staff with the link of the Video for Site Specific Induction. <i class="fa fa-quote-right"></i></p></strong></div>

					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">Message to Site Staff for Site Specific Induction:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_induction_video_oss" name="email_msg_induction_video_oss" style="height: 100px "><?php echo ($this->input->post('email_msg_induction_video_oss') ?  $this->input->post('email_msg_induction_video_oss') : $induction_message_content_video_oss ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>

				</div>
			</div>
	    </form>

	    <p><hr /></p>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-plus-square fa-lg"></i> Insurance Email Defaults</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_no_insurance" name="sender_name_no_insurance" value = "<?php echo ($this->input->post('sender_name_no_insurance') ?  $this->input->post('sender_name_no_insurance') : $sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_no_insurnace" name="sender_email_no_insurnace" value = "<?php echo ($this->input->post('sender_email_no_insurnace') ?  $this->input->post('sender_email_no_insurnace') : $sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_no_insurnace" name="subject_no_insurnace" value = "<?php echo ($this->input->post('subject_no_insurnace') ?  $this->input->post('subject_no_insurnace') : $subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_no_insurnace" name="bcc_email_no_insurnace" value = "<?php echo ($this->input->post('bcc_email_no_insurnace') ?  $this->input->post('bcc_email_no_insurnace') : $bcc_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('double-time')){ echo 'has-error';} ?>">
						<label for="double-time" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_forinsurance" class="form-control find_contact_person chosen" id="user_assigned_forinsurance" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value='' class="hide">Select User Name*</option>													
								<?php $this->admin->fetch_users_list(); ?>														
							</select>
							<?php echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_forinsurance').val('<?php echo $user_id; ?>');</script>
							
						</div>
					</div>

					<div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div>
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('time-half')){ echo 'has-error';} ?>">
						<label for="time-half" class="col-sm-2 control-label">Message to Contractor for Expires or <br>no Insurance:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_no_insurance" name="email_msg_no_insurance" style="height: 100px "><?php echo ($this->input->post('email_msg_no_insurance') ?  $this->input->post('email_msg_no_insurance') : $message_content ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>
	</div>
</div>

<p><hr /></p>

<div class="box blue-border m-top-0">
	<div class="box-head blue-bg pad-5">
		<label class="blue-title"><i class="fa fa-id-card-o fa-lg"></i> Onboarding Settings</label>
	</div>

	<?php if(@$onboarding_email_errors): ?>
		<div class="no-pad-t">
			<div class="border-less-box alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Oh snap! You got an error!</h4>
				<?php echo $onboarding_email_errors;?>
			</div>
		</div>
	<?php endif; ?>

	<?php if(@$this->session->flashdata('onboarding_default_email')): ?>
		<div class="no-pad-t">
			<div class="border-less-box alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Cheers!</h4>
				<?php echo $this->session->flashdata('onboarding_default_email');?>
			</div>
		</div>
	<?php endif; ?>

	<div class="box-area clearfix pad-10">

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_clients">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-link fa-lg"></i> Onboarding Sending Link Email Defaults (Clients)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_clients')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_clients" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_clients" name="sender_name_onboarding_clients" value = "<?php echo ($this->input->post('sender_name_onboarding_clients') ?  $this->input->post('sender_name_onboarding_clients') : $onboarding_sender_name_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_clients')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_clients" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_clients" name="sender_email_onboarding_clients" value = "<?php echo ($this->input->post('sender_email_onboarding_clients') ?  $this->input->post('sender_email_onboarding_clients') : $onboarding_sender_email_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_clients')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_clients" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_clients" name="subject_onboarding_clients" value = "<?php echo ($this->input->post('subject_onboarding_clients') ?  $this->input->post('subject_onboarding_clients') : $onboarding_subject_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_clients')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_clients" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_clients" name="bcc_email_onboarding_clients" value = "<?php echo ($this->input->post('bcc_email_onboarding_clients') ?  $this->input->post('bcc_email_onboarding_clients') : $onboarding_bcc_email_clients ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_foronboarding')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding" class="form-control find_contact_person chosen" id="user_assigned_onboarding" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding').val('<?php //echo $onboarding_assigned_user; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_clients')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_clients" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_clients" name="email_msg_onboarding_clients" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_clients_clients') ?  $this->input->post('email_msg_onboarding_clients') : $onboarding_message_content_clients ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-link fa-lg"></i> Onboarding Sending Link Email Defaults (Contractors/Suppliers)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding" name="sender_name_onboarding" value = "<?php echo ($this->input->post('sender_name_onboarding') ?  $this->input->post('sender_name_onboarding') : $onboarding_sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding" name="sender_email_onboarding" value = "<?php echo ($this->input->post('sender_email_onboarding') ?  $this->input->post('sender_email_onboarding') : $onboarding_sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding')){ echo 'has-error';} ?>">
						<label for="subject_onboarding" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding" name="subject_onboarding" value = "<?php echo ($this->input->post('subject_onboarding') ?  $this->input->post('subject_onboarding') : $onboarding_subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding" name="bcc_email_onboarding" value = "<?php echo ($this->input->post('bcc_email_onboarding') ?  $this->input->post('bcc_email_onboarding') : $onboarding_bcc_email ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_foronboarding')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding" class="form-control find_contact_person chosen" id="user_assigned_onboarding" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding').val('<?php //echo $onboarding_assigned_user; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding" name="email_msg_onboarding" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding') ?  $this->input->post('email_msg_onboarding') : $onboarding_message_content ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_bank">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-bank fa-lg"></i> Onboarding - Bank Details Form</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_bank')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_bank" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_bank" name="sender_name_onboarding_bank" value = "<?php echo ($this->input->post('sender_name_onboarding_bank') ?  $this->input->post('sender_name_onboarding_bank') : $onboarding_sender_name_bank ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_bank')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_bank" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_bank" name="sender_email_onboarding_bank" value = "<?php echo ($this->input->post('sender_email_onboarding_bank') ?  $this->input->post('sender_email_onboarding_bank') : $onboarding_sender_email_bank ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_bank')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_bank" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_bank" name="subject_onboarding_bank" value = "<?php echo ($this->input->post('subject_onboarding_bank') ?  $this->input->post('subject_onboarding_bank') : $onboarding_subject_bank ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_bank')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_bank" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_bank" name="bcc_email_onboarding_bank" value = "<?php echo ($this->input->post('bcc_email_onboarding_bank') ?  $this->input->post('bcc_email_onboarding_bank') : $onboarding_bcc_email_bank ); ?>">
						</div>
					</div>
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_bank')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_bank" class="col-sm-2 control-label">Message to Contractor for Bank Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_bank" name="email_msg_onboarding_bank" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_bank') ?  $this->input->post('email_msg_onboarding_bank') : $onboarding_message_content_bank ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

		<div class="box yellow-border">
			<div class="box-head yellow-bg pad-5 m-bottom-5">
				<label class="yellow-title"><i class="fa fa-file-pdf-o fa-lg"></i> Onboarding - Bank Details Form PDF Attachments</label>
			</div>
			
			<div class="box-area pad-5 clearfix">

				<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_bank_form1')){ echo 'has-error';} ?>">
					<form class="form-horizontal" action="<?php echo base_url(); ?>admin/pdf_do_upload" method="post" enctype="multipart/form-data" id="frmUploadWA">
						<label for="email_msg_onboarding_bank_form1" class="col-sm-4 control-label">Attached Bank Details Form for WA:</label>
						
						<div class="col-sm-2">
							<a id="btnViewWA" class="btn btn-success btn-block" target="_blank" href="<?php echo base_url(); ?>docs/bank_details_form/wa/bank_details_form_wa.PDF" role="button"><i class="fa fa-eye fa-lg"></i> View</a>
						</div>

						<div class="col-sm-2">
							<input type="hidden" name="state" value="wa">
							<input type="hidden" name="upload_path_pdf" value="bank_details_form/wa/">

							<span class="btn btn-warning btn-block btn-file">
						    	<i class = "fa fa-upload fa-lg"></i> Upload<input type="file" id="btnUploadWA" name="userfile" accept=".pdf/*">
							</span>
						</div>
					</form>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_bank_form2')){ echo 'has-error';} ?>">
					<form class="form-horizontal" action="<?php echo base_url(); ?>admin/pdf_do_upload" method="post" enctype="multipart/form-data" id="frmUploadNSW">
						<label for="email_msg_onboarding_bank_form2" class="col-sm-4 control-label">Attached Bank Details Form for NSW:</label>
						<div class="col-sm-2">
							<a id="btnViewNSW" class="btn btn-success btn-block" target="_blank" href="<?php echo base_url(); ?>docs/bank_details_form/nsw/bank_details_form_nsw.PDF" role="button"><i class="fa fa-eye fa-lg"></i> View</a>
						</div>
						<div class="col-sm-2">
							<input type="hidden" name="state" value="nsw">
							<input type="hidden" name="upload_path_pdf" value="bank_details_form/nsw/">

							<span class="btn btn-warning btn-block btn-file">
						    	<i class = "fa fa-upload fa-lg"></i> Upload<input type="file" id="btnUploadNSW" name="userfile" accept=".pdf/*">
							</span>
						</div>
					</form>
				</div>
			</div>
		</div>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_contractor_msg">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-exclamation-circle fa-lg"></i> Onboarding Message for Contractors Only (Onboarding Registration Form)</label>
				</div>

				<?php if(@$onboarding_contractor_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $onboarding_contractor_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_contractor_msg')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_contractor_msg');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_contractor_msg')){ echo 'has-error';} ?>">
						<label for="onboarding_contractor_msg" class="col-sm-2 control-label">Message attached to the form:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_contractor_msg" name="onboarding_contractor_msg" style="height: 100px "><?php echo $static_defaults[0]->onboarding_contractor_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message</button>
				    </div>
				</div>
			</div>
		</form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_email">

			<div class="box yellow-border m-top-0">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa  fa-user fa-lg"></i> Onboarding Email Notification Recipient Defaults</label>
				</div>

				<div class="box-area pad-5 clearfix">

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_recipient_email')){ echo 'has-error';} ?>">
						<label for="onboarding_recipient_email" class="col-sm-4 control-label">Onboarding Recipient Email</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon" id=""><i class="fa fa-envelope-o"></i></span>
								<input type="email" class="form-control" id="onboarding_recipient_email" name="onboarding_recipient_email" placeholder="Recipient Email" value="<?php echo $static_defaults[0]->onboarding_to; ?>">
							</div>
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_optional_cc_email')){ echo 'has-error';} ?>">
						<label for="onboarding_optional_cc_email" class="col-sm-4 control-label">Onboarding Optional CC Email</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon" id=""><i class="fa fa-envelope-o"></i></span>
								<input type="email" class="form-control" id="onboarding_optional_cc_email" name="onboarding_optional_cc_email" placeholder="Optional CC Email" value="<?php echo $static_defaults[0]->onboarding_cc; ?>">
							</div>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Emails</button>
				    </div>
				</div>
			</div>

	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_notif">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-bell fa-lg"></i> Onboarding Email Notification Content Defaults</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_notif')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_notif" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_notif" name="sender_name_onboarding_notif" value = "<?php echo ($this->input->post('sender_name_onboarding_notif') ?  $this->input->post('sender_name_onboarding_notif') : $onboarding_notif_sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_notif')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_notif" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_notif" name="sender_email_onboarding_notif" value = "<?php echo ($this->input->post('sender_email_onboarding_notif') ?  $this->input->post('sender_email_onboarding_notif') : $onboarding_notif_sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_notif')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_notif" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_notif" name="subject_onboarding_notif" value = "<?php echo ($this->input->post('subject_onboarding_notif') ?  $this->input->post('subject_onboarding_notif') : $onboarding_notif_subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_notif')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_notif" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_notif" name="bcc_email_onboarding_notif" value = "<?php echo ($this->input->post('bcc_email_onboarding_notif') ?  $this->input->post('bcc_email_onboarding_notif') : $onboarding_notif_bcc_email ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_foronboarding')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding_notif" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding_notif" class="form-control find_contact_person chosen" id="user_assigned_onboarding_notif" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding_notif').val('<?php //echo $onboarding_notif_assigned_user; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_notif')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_notif" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_notif" name="email_msg_onboarding_notif" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_notif') ?  $this->input->post('email_msg_onboarding_notif') : $onboarding_notif_message_content ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_approved_clients">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-check fa-lg"></i> Onboarding Approved Defaults (Clients)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_approved_clients" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_approved_clients" name="sender_name_onboarding_approved_clients" value = "<?php echo ($this->input->post('sender_name_onboarding_approved_clients') ?  $this->input->post('sender_name_onboarding_approved_clients') : $onboarding_approved_sender_name_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_approved_clients" class="col-sm-4 control-label">Default Senders Email:</label>
							<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_approved_clients" name="sender_email_onboarding_approved_clients" value = "<?php echo ($this->input->post('sender_email_onboarding_approved_clients') ?  $this->input->post('sender_email_onboarding_approved_clients') : $onboarding_approved_sender_email_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_approved_clients" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_approved_clients" name="subject_onboarding_approved_clients" value = "<?php echo ($this->input->post('subject_onboarding_approved_clients') ?  $this->input->post('subject_onboarding_approved_clients') : $onboarding_approved_subject_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_approved_clients" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_approved_clients" name="bcc_email_onboarding_approved_clients" value = "<?php echo ($this->input->post('bcc_email_onboarding_approved_clients') ?  $this->input->post('bcc_email_onboarding_approved_clients') : $onboarding_approved_bcc_email_clients ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding_approved_clients" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding_approved_clients" class="form-control find_contact_person chosen" id="user_assigned_onboarding_approved_clients" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding_approved_clients').val('<?php //echo $onboarding_approved_assigned_user_clients; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_approved_clients')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_approved_clients" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_approved_clients" name="email_msg_onboarding_approved_clients" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_approved_clients') ?  $this->input->post('email_msg_onboarding_approved_clients') : $onboarding_approved_message_content_clients ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_approved">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-check fa-lg"></i> Onboarding Approved Defaults (Contractors/Suppliers)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_approved')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_approved" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_approved" name="sender_name_onboarding_approved" value = "<?php echo ($this->input->post('sender_name_onboarding_approved') ?  $this->input->post('sender_name_onboarding_approved') : $onboarding_approved_sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_approved')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_approved" class="col-sm-4 control-label">Default Senders Email:</label>
							<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_approved" name="sender_email_onboarding_approved" value = "<?php echo ($this->input->post('sender_email_onboarding_approved') ?  $this->input->post('sender_email_onboarding_approved') : $onboarding_approved_sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_approved')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_approved" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_approved" name="subject_onboarding_approved" value = "<?php echo ($this->input->post('subject_onboarding_approved') ?  $this->input->post('subject_onboarding_approved') : $onboarding_approved_subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_approved')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_approved" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_approved" name="bcc_email_onboarding_approved" value = "<?php echo ($this->input->post('bcc_email_onboarding_approved') ?  $this->input->post('bcc_email_onboarding_approved') : $onboarding_approved_bcc_email ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_foronboarding')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding_approved" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding_approved" class="form-control find_contact_person chosen" id="user_assigned_onboarding_approved" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding_approved').val('<?php //echo $onboarding_approved_assigned_user; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_approved')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_approved" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_approved" name="email_msg_onboarding_approved" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_approved') ?  $this->input->post('email_msg_onboarding_approved') : $onboarding_approved_message_content ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_declined_clients">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-times fa-lg"></i> Onboarding Declined Defaults (Clients)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_declined_clients" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_declined_clients" name="sender_name_onboarding_declined_clients" value = "<?php echo ($this->input->post('sender_name_onboarding_declined_clients') ?  $this->input->post('sender_name_onboarding_declined_clients') : $onboarding_declined_sender_name_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_declined_clients" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_declined_clients" name="sender_email_onboarding_declined_clients" value = "<?php echo ($this->input->post('sender_email_onboarding_declined_clients') ?  $this->input->post('sender_email_onboarding_declined_clients') : $onboarding_declined_sender_email_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_declined_clients" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_declined_clients" name="subject_onboarding_declined_clients" value = "<?php echo ($this->input->post('subject_onboarding_declined_clients') ?  $this->input->post('subject_onboarding_declined_clients') : $onboarding_declined_subject_clients ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_declined_clients" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_declined_clients" name="bcc_email_onboarding_declined_clients" value = "<?php echo ($this->input->post('bcc_email_onboarding_declined_clients') ?  $this->input->post('bcc_email_onboarding_declined_clients') : $onboarding_declined_bcc_email_clients ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding_declined_clients" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding_declined_clients" class="form-control find_contact_person chosen" id="user_assigned_onboarding_declined_clients" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding_declined_clients').val('<?php //echo $onboarding_declined_assigned_user_clients; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_declined_clients')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_declined_clients" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_declined_clients" name="email_msg_onboarding_declined_clients" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_declined_clients') ?  $this->input->post('email_msg_onboarding_declined_clients') : $onboarding_declined_message_content_clients ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/default_email_message_onboarding_declined">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-times fa-lg"></i> Onboarding Declined Defaults (Contractors/Suppliers)</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_name_onboarding_declined')){ echo 'has-error';} ?>">
						<label for="sender_name_onboarding_declined" class="col-sm-4 control-label">Default Sender Name:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_name_onboarding_declined" name="sender_name_onboarding_declined" value = "<?php echo ($this->input->post('sender_name_onboarding_declined') ?  $this->input->post('sender_name_onboarding_declined') : $onboarding_declined_sender_name ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('sender_email_onboarding_declined')){ echo 'has-error';} ?>">
						<label for="sender_email_onboarding_declined" class="col-sm-4 control-label">Default Senders Email:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="sender_email_onboarding_declined" name="sender_email_onboarding_declined" value = "<?php echo ($this->input->post('sender_email_onboarding_declined') ?  $this->input->post('sender_email_onboarding_declined') : $onboarding_declined_sender_email ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('subject_onboarding_declined')){ echo 'has-error';} ?>">
						<label for="subject_onboarding_declined" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="subject_onboarding_declined" name="subject_onboarding_declined" value = "<?php echo ($this->input->post('subject_onboarding_declined') ?  $this->input->post('subject_onboarding_declined') : $onboarding_declined_subject ); ?>">
						</div>
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php if(form_error('bcc_email_onboarding_declined')){ echo 'has-error';} ?>">
						<label for="bcc_email_onboarding_declined" class="col-sm-4 control-label">BCC:</label>
						<div class="col-sm-8">
							<input type = "text" class="form-control" id="bcc_email_onboarding_declined" name="bcc_email_onboarding_declined" value = "<?php echo ($this->input->post('bcc_email_onboarding_declined') ?  $this->input->post('bcc_email_onboarding_declined') : $onboarding_declined_bcc_email ); ?>">
						</div>
					</div>

					<!-- <div class="col-md-6 col-sm-6 col-xs-12 m-bottom-10 clearfix <?php //if(form_error('user_assigned_foronboarding')){ echo 'has-error';} ?>">
						<label for="user_assigned_onboarding_declined" class="col-sm-4 control-label">User Responsible: </label>
						<div class="col-sm-8">
							<select name="user_assigned_onboarding_declined" class="form-control find_contact_person chosen" id="user_assigned_onboarding_declined" style="width: 100%;" tabindex="25">																										
								<?php //$this->company->company_list('dropdown'); ?>
								<option value=''>Select User Name*</option>													
								<?php //$this->admin->fetch_users_list(); ?>														
							</select>
							<?php //echo $this->input->post('user_id'); ?>
							<script type="text/javascript">$('select#user_assigned_onboarding_declined').val('<?php //echo $onboarding_declined_assigned_user; ?>');</script>
							
						</div>
					</div> -->

					<!-- <div class="clearfix col-xs-12 m-top-20 text-center"><strong> <p><i class="fa fa-quote-left"></i> Automatically sent to any CONTRACTOR who has no insurance documents loaded OR the insurance documents are expired, WHEN a purchase order is reconciled. <i class="fa fa-quote-right"></i></p></strong></div> -->
					
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('email_msg_onboarding_declined')){ echo 'has-error';} ?>">
						<label for="email_msg_onboarding_declined" class="col-sm-2 control-label">Message to Contractor for Company Details:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="email_msg_onboarding_declined" name="email_msg_onboarding_declined" style="height: 100px "><?php echo ($this->input->post('email_msg_onboarding_declined') ?  $this->input->post('email_msg_onboarding_declined') : $onboarding_declined_message_content ); ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Email Default</button>
				    </div>
				</div>
			</div>
	    </form>

	    <form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_general_msg">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-info-circle fa-lg"></i> Onboarding General Message Box (Included after the Approved and Declined Email Messages)</label>
				</div>

				<?php if(@$onboarding_general_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $onboarding_general_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_general_msg')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_general_msg');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_general_msg')){ echo 'has-error';} ?>">
						<label for="onboarding_general_msg" class="col-sm-2 control-label">General Message:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_general_msg" name="onboarding_general_msg" style="height: 100px "><?php echo $static_defaults[0]->onboarding_general_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_workplace_health_safety">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Workplace Health & Safety</label>
				</div>

				<?php if(@$workplace_health_safety_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $workplace_health_safety_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_workplace_health_safety')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_workplace_health_safety');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_workplace_health_safety')){ echo 'has-error';} ?>">
						<label for="onboarding_workplace_health_safety" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_workplace_health_safety" name="onboarding_workplace_health_safety" style="height: 100px "><?php echo $static_defaults[0]->workplace_health_safety_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_swms">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Safe Work Method Statements (SWMS)</label>
				</div>

				<?php if(@$swms_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $swms_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_swms')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_swms');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_swms')){ echo 'has-error';} ?>">
						<label for="onboarding_swms" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_swms" name="onboarding_swms" style="height: 100px "><?php echo $static_defaults[0]->swms_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_jsa">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Job Safety Analysis (JSA)</label>
				</div>

				<?php if(@$jsa_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $jsa_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_jsa')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_jsa');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_jsa')){ echo 'has-error';} ?>">
						<label for="onboarding_jsa" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_jsa" name="onboarding_jsa" style="height: 100px "><?php echo $static_defaults[0]->jsa_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_reviewed_swms">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Reviewed SWMS</label>
				</div>

				<?php if(@$reviewed_swms_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $reviewed_swms_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_reviewed_swms')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_reviewed_swms');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_reviewed_swms')){ echo 'has-error';} ?>">
						<label for="onboarding_reviewed_swms" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_reviewed_swms" name="onboarding_reviewed_swms" style="height: 100px "><?php echo $static_defaults[0]->reviewed_swms_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_safety_related_convictions">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Safety Related Convictions</label>
				</div>

				<?php if(@$safety_related_convictions_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $safety_related_convictions_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_safety_related_convictions')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_safety_related_convictions');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_safety_related_convictions')){ echo 'has-error';} ?>">
						<label for="onboarding_safety_related_convictions" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_safety_related_convictions" name="onboarding_safety_related_convictions" style="height: 100px "><?php echo $static_defaults[0]->safety_related_convictions_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

		<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/onboarding_confirm_licences_certifications">

			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-warning fa-lg"></i> Confirm Licences Certifications</label>
				</div>

				<?php if(@$confirm_licences_certifications_msg_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $confirm_licences_certifications_msg_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('onboarding_confirm_licences_certifications')): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-success fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Cheers!</h4>
							<?php echo $this->session->flashdata('onboarding_confirm_licences_certifications');?>
						</div>
					</div>
				<?php endif; ?>

				<div class="box-area pad-5 clearfix">
					<div class="col-md-12 col-sm-12 col-xs-12 m-bottom-10 clearfix <?php if(form_error('onboarding_confirm_licences_certifications')){ echo 'has-error';} ?>">
						<label for="onboarding_confirm_licences_certifications" class="col-sm-2 control-label">Reason for Rejection:</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="onboarding_confirm_licences_certifications" name="onboarding_confirm_licences_certifications" style="height: 100px "><?php echo $static_defaults[0]->confirm_licences_certifications_msg; ?></textarea>
						</div>
					</div>

					<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-20 m-bottom-10 text-right">
				        <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Message Default</button>
				    </div>
				</div>

			</div>
		</form>

	</div>
</div>
<p><hr /></p>

	<div class="box blue-border m-top-0">
		<div class="box-head blue-bg pad-5" style="">
			<label class="blue-title"><i class="fa fa-medkit"></i> Incident Report Default Settings</label>
		</div>

		<div class="box-area clearfix pad-10">
			<div class="box yellow-border">
				<div class="box-head yellow-bg pad-5 m-bottom-5">
					<label class="yellow-title"><i class="fa fa-user fa-lg"></i> Person Responsible</label>
				</div>

				<div class="box-area pad-5 clearfix">
					<div class="col-sm-3 pad-5">Responsible for Receiving Reports:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "report_receiver">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-3 pad-5">Alternate for Receiveing Reports:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "alter_report_receiver">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-3 pad-5">Responsible for filling-up Immediate Action WA:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "imidiate_preventive_action_filler">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-3 pad-5">Responsible for filling-up Recommended Action WA:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "recommended_action_filler">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-3 pad-5">Responsible for filling-up Immediate Action NSW:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "imidiate_preventive_action_filler_nsw">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-3 pad-5">Responsible for filling-up Recommended Action NSW:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "recommended_action_filler_nsw">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="clearfix"></div>

					<div class="col-sm-3 pad-5">Responsible Approving/Reviewing Report:</div>
					<div class="col-sm-3 pad-5">
						<select class = "form-control input-sm" v-model = "report_reviewer">
							<option v-for = "users_list in orderUsers" :value="users_list.user_id">{{ users_list.user_first_name +" "+ users_list.user_last_name}}</option>
						</select>
					</div>

					<div class="col-sm-12 pad_5"><button type = "button" class = "btn btn-success pull-right" v-on:click = "save_ir_defaults"><i class="fa fa-floppy-o"></i> Save Changes</button></div>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="tab-pane fade in <?php echo ($curr_tab == 'users' ? 'active' : '' ); ?>" id="users" aria-labelledby="users">
	
	

		<div class="box blue-border m-top-0">
			<div class="box-head blue-bg pad-5">
				<label class="blue-title"><i class="fa fa-user fa-lg"></i> User Default Settings</label>
 

			</div>
<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/user_settings">

			<div class="box-area clearfix pad-10">
				<?php if(@$user_settings_error): ?>
					<div class="no-pad-t">
						<div class="border-less-box alert alert-danger fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4>Oh snap! You got an error!</h4>
							<?php echo $user_settings_error;?>
						</div>
					</div>
				<?php endif; ?>

				<?php if(@$this->session->flashdata('update_user_settings')): ?>
					<div class="col-sm-12 ">							
						<div class="no-pad-t">
							<div class="border-less-box alert alert-success fade in m-top-5">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<?php echo $this->session->flashdata('update_user_settings');?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="box yellow-border m-top-0">
					<!-- <div class="col-sm-12"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-asterisk fa-lg"></i> Password Defaults:</label>
						</div>

						<div class="box-area clearfix pad-5">
							<div class="col-md-5 col-sm-5 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="total-hour" class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">Days Password Expiration </label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control" value="<?php echo $static_defaults[0]->days_psswrd_exp; ?>" name="days_exp">
										<span class="input-group-addon" id="">Days</span>
									</div>
								</div>						
							</div>

							<div class="col-md-5 col-sm-5 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="total-hour" class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">Temporary User Password </label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon" id=""><i class="fa fa-lock"></i></span>
										<input type="text" class="form-control" name="temp_password" value="<?php echo $static_defaults[0]->temp_user_psswrd; ?>">
									</div>
								</div>						
							</div>

							<div class="col-md-offset-6 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<button type="submit" class="btn btn-success pull-right m-bottom-10 m-right-5"><i class="fa fa-floppy-o"></i> Save Password Settings</button>				
							</div>
						</div>

					<!-- </div> -->
				</div>
			</div>
	</form>
		</div>

	<p><hr /></p>

		<?php if(@$leave_approval_notice_errors): ?>
			<div class="no-pad-t">
				<div class="border-less-box alert alert-danger fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4>Oh snap! You got an error!</h4>
					<?php echo $leave_approval_notice_errors;?>
				</div>
			</div>
		<?php endif; ?>

		<?php if(@$this->session->flashdata('leave_approval_notice')): ?>
			<div class="col-sm-12 ">							
				<div class="no-pad-t">
					<div class="border-less-box alert alert-success fade in m-top-5">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<?php echo $this->session->flashdata('leave_approval_notice'); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	

		<div class="box blue-border m-top-0">

			<div class="box-head blue-bg pad-5">
				<label class="blue-title"><i class="fa fa-calendar-minus-o fa-lg"></i> Leave Application Default Settings</label>
			</div>

			<div class="box-area clearfix pad-10">

				<div class="box yellow-border m-top-0">
					
					<div class="box-head yellow-bg pad-5">
						<label class="yellow-title"><i class="fa fa-percent fa-lg"></i> Leave Rates:</label>
					</div>

					<?php if(@$user_settings_error): ?>
						<div class="no-pad-t">
							<div class="border-less-box alert alert-danger fade in">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<h4>Oh snap! You got an error!</h4>
								<?php echo $user_settings_error;?>
							</div>
						</div>
					<?php endif; ?>

					<?php if(@$this->session->flashdata('update_user_settings_leave_rates')): ?>
						<div class="col-sm-12 ">							
							<div class="no-pad-t">
								<div class="border-less-box alert alert-success fade in m-top-5">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<?php echo $this->session->flashdata('update_user_settings_leave_rates');?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="box-area clearfix pad-5">
						
						<div class="row">
							
							<div class="col-sm-12 col-md-6">

								
								<h2 class="text-center">Australian Staff:</h2>

								<div class="clearfix"><br></div>

								<form id="frmSalariedRates" class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/salaried_rates">

									<h4 class="text-center">Salaried</h4>

									<label for="salaried_total_annual_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Annual Leave</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_salaried[0]->annual_leave; ?>" name="salaried_total_annual_leave" id="salaried_total_annual_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_salaried[0]->annual_leave; ?> / 52 weeks = <?php echo round($select_leave_rates_salaried[0]->annual_leave / 52, 6); ?> per week"></i></span>
										</div> 
									</div>

									<div class="clearfix"><br></div>
									<div class="clearfix"><br></div>

									<label for="salaried_total_personal_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Personal Leave</label>
									<div class="col-sm-3">
										<div class="input-group">							
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_salaried[0]->personal_leave; ?>" name="salaried_total_personal_leave" id="salaried_total_personal_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_salaried[0]->personal_leave; ?> / 52 weeks = <?php echo round($select_leave_rates_salaried[0]->personal_leave / 52, 6); ?> per week"></i></span>
										</div>
									</div>

									<div class="clearfix"><br></div>

									<div class="col-md-12 col-sm-12 col-xs-12 clearfix m-top-10 m-bottom-10">
										<center>
											<button type="button" id="btnSalariedRates" class="btn btn-success m-bottom-10"><i class="fa fa-floppy-o"></i> Save Salaried Rates</button>
										</center>
									</div>

								</form>

								<div class="clearfix"><br></div>

								<form class="form-horizontal" id="frmWagesRates" role="form" method="post" action="<?php echo current_url(); ?>/wages_rates">

									<h4 class="text-center">Wages</h4>

									<label for="wages_total_annual_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Annual Leave</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_wages[0]->annual_leave; ?>" name="wages_total_annual_leave" id="wages_total_annual_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_wages[0]->annual_leave; ?> / 52 weeks = <?php echo round($select_leave_rates_wages[0]->annual_leave / 52, 6); ?> per week"></i></span>
										</div>
									</div>

									<div class="clearfix"><br></div>
									<div class="clearfix"><br></div>

									<label for="wages_total_personal_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Personal Leave</label>
									<div class="col-sm-3">
										<div class="input-group">							
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_wages[0]->personal_leave; ?>" name="wages_total_personal_leave" id="wages_total_personal_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_wages[0]->personal_leave; ?> / 52 weeks = <?php echo round($select_leave_rates_wages[0]->personal_leave / 52, 6); ?> per week"></i></span>
										</div>
									</div>

									<!-- <div class="clearfix"><br></div>
									<div class="clearfix"><br></div>

									<label for="wages_total_rdo" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total RDO</label>
									<div class="col-sm-3">
										<div class="input-group">							
											<input type="text" class="form-control text-center" value="<?php //echo $select_leave_rates_wages[0]->rdo_leave; ?>" name="wages_total_rdo" id="wages_total_rdo">
											<span class="input-group-addon" id="">days</span>
										</div>
									</div> -->

									<div class="clearfix"><br></div>

									<div class="col-md-12 col-sm-12 col-xs-12 clearfix m-top-10 m-bottom-10">
										<center>
											<button type="button" id="btnWagesRates" class="btn btn-success m-bottom-10"><i class="fa fa-floppy-o"></i> Save Wages Rates</button>
										</center>
									</div>

								</form>
							</div>

							<div class="col-sm-12 col-md-6">

								<form id="frmManilaRates" class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/manila_rates">

									<h2 class="text-center">Manila Staff:</h2>

									<div class="clearfix"><br></div>
									<div class="clearfix"><br></div>

									<label for="manila_total_vacation_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Vacation Leave</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_manila[0]->annual_leave; ?>" name="manila_total_vacation_leave" id="manila_total_vacation_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_manila[0]->annual_leave; ?> / 12 months = <?php echo round($select_leave_rates_manila[0]->annual_leave / 12, 2); ?> per month"></i></span>
										</div>
									</div>

									<div class="clearfix"><br></div>
									<div class="clearfix"><br></div>

									<label for="manila_total_sick_leave" class="col-sm-5  control-label m-top-5 text-right" style="font-weight: normal;">Total Sick Leave</label>
									<div class="col-sm-3">
										<div class="input-group">							
											<input type="text" class="form-control text-center" value="<?php echo $select_leave_rates_manila[0]->personal_leave; ?>" name="manila_total_sick_leave" id="manila_total_sick_leave">
											<span class="input-group-addon" id="">days <i class="fa fa-info-circle fa-lg tooltip-test" data-toggle="tooltip" data-placement="top" title="<?php echo $select_leave_rates_manila[0]->personal_leave; ?> / 12 months = <?php echo round($select_leave_rates_manila[0]->personal_leave / 12, 2); ?> per month"></i></span>
										</div>
									</div>

									<div class="col-md-12 col-sm-12 col-xs-12 clearfix m-top-10 m-bottom-10">
										<center>
											<button type="button" id="btnManilaRates" class="btn btn-success m-bottom-10"><i class="fa fa-floppy-o"></i> Save Manila Rates</button>
										</center>
									</div>

								</form>
							</div>

							<div class="clearfix"><br></div>
							<div class="clearfix"><br></div>
								
						</div>
					</div>

					<!-- <div class="col-md-4 col-sm-3 col-xs-12 clearfix m-top-10 m-bottom-10">
						<h4 class="text-right">Australia:</h4>
					</div>

					<div class="col-md-5 col-sm-5 col-xs-12 clearfix m-top-10 m-bottom-10">
						<label for="annual_leave_daily_rate" class="col-sm-3  control-label m-top-5 text-right" style="font-weight: normal;">Annual Leave</label>
						<div class="col-sm-6">
							<div class="input-group">
								<input type="text" class="form-control" value="<?php //echo floatval($static_defaults[0]->annual_leave_daily_rate); ?>" name="annual_leave_daily_rate" id="annual_leave_daily_rate">
								<span class="input-group-addon" id="">hours per week</span>
							</div>
						</div>						
					</div>

					<div class="col-md-5 col-sm-5 col-xs-12 clearfix m-top-10 m-bottom-10">
						<label for="personal_leave_daily_rate" class="col-sm-3  control-label m-top-5 text-right" style="font-weight: normal;">Personal Leave</label>
						<div class="col-sm-6">
							<div class="input-group">							
								<input type="text" class="form-control" value="<?php //echo floatval($static_defaults[0]->personal_leave_daily_rate); ?>" name="personal_leave_daily_rate" id="personal_leave_daily_rate">
								<span class="input-group-addon" id="">hours  per week</span>
							</div>
						</div>						
					</div> -->

				</div>
				<div class="box yellow-border m-top-20">

					<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/update_leave_notice">

						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-calendar-times-o fa-lg"></i> Annual Leave Days Notice:</label>
						</div>

						<div class="box-area clearfix pad-5">

							<div class="col-md-3 col-sm-4 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="annual_leave_notice" class="col-sm-12 control-label m-top-5 text-center">Filed Leave (Days):</label>					
							</div>

							<div class="col-md-3 col-sm-4 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="annual_leave_notice" class="col-sm-12 control-label m-top-5 text-center">1 Day Leave</label>					
							</div>

							<div class="col-md-3 col-sm-4 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="annual_leave_notice" class="col-sm-12 control-label m-top-5 text-center">2-5 Days Leave</label>					
							</div>

							<div class="col-md-3 col-sm-4 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="annual_leave_notice" class="col-sm-12 control-label m-top-5 text-center">6 or more Days Leave</label>					
							</div>

							<div class="clearfix"></div>

							<div class="col-md-3 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="annual_leave_notice" class="col-sm-12 control-label m-top-5 text-center">Annual Leave</label>					
							</div>
							<?php 
								$i = 1;
								foreach ($notice_days_annual as $row1): 
							?>
							<div class="col-md-3 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<div class="input-group">
									<span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>
									<input type="text" class="form-control" value="<?php echo $row1->days_advance_notice; ?>" name="annual_leave_notice<?php echo $i; ?>" id="annual_leave_notice<?php echo $i; ?>">
									<span class="input-group-addon" id="">Days</span>
								</div>			
							</div>
							<?php 
								$i++;
								endforeach; 
							?>

							<div class="col-md-12 col-sm-12 col-xs-12 clearfix m-top-10 m-bottom-10">
								<button type="submit" class="btn btn-success pull-right m-bottom-10 m-right-5"><i class="fa fa-floppy-o"></i> Save Notice Days Settings</button>				
							</div>

						</div>

					</form>

				</div>

				<div class="box yellow-border m-top-20">

					<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/update_leave_emails">
					
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-edit fa-lg"></i> Leave Email Defaults:</label>
						</div>

						<?php if(@$leave_approval_email_errors): ?>
							<div class="no-pad-t">
								<div class="border-less-box alert alert-danger fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Oh snap! You got an error!</h4>
									<?php echo $leave_approval_email_errors;?>
								</div>
							</div>
						<?php endif; ?>

						<?php if(@$this->session->flashdata('leave_approval_email')): ?>
							<div class="no-pad-t">
								<div class="border-less-box alert alert-success fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Cheers!</h4>
									<?php echo $this->session->flashdata('leave_approval_email');?>
								</div>
							</div>
						<?php endif; ?>

						<div class="box-area pad-5 clearfix">

							<div class="col-md-6 col-sm-6 col-xs-12 m-top-10 m-bottom-10 clearfix <?php if(form_error('leave_recipient_email')){ echo 'has-error';} ?>">
								<label for="leave_recipient_email" class="col-sm-4 control-label">Recipient Email</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon" id=""><i class="fa fa-envelope"></i></span>
										<input type="email" class="form-control" id="leave_recipient_email" name="leave_recipient_email" placeholder="Recipient Email" value="<?php echo $leave_email_defaults[0]->recipient_email; ?>">
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-6 col-xs-12 m-top-10 m-bottom-10 clearfix <?php if(form_error('leave_cc_email')){ echo 'has-error';} ?>">
								<label for="leave_cc_email" class="col-sm-4 control-label">Cc Email/s</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon" id=""><i class="fa fa-envelope"></i></span>
										<input type="text" class="form-control" id="leave_cc_email" name="leave_cc_email" placeholder="CC Email" value="<?php echo $leave_email_defaults[0]->cc_email; ?>">
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-6 col-xs-12 m-top-10 m-bottom-10 clearfix <?php if(form_error('leave_bcc_email')){ echo 'has-error';} ?>">
								<label for="leave_bcc_email" class="col-sm-4 control-label">Bcc Email/s</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon" id=""><i class="fa fa-envelope"></i></span>
										<input type="text" class="form-control" id="leave_bcc_email" name="leave_bcc_email" placeholder="BCC Email" value="<?php echo $leave_email_defaults[0]->bcc_email; ?>">
									</div>
								</div>
							</div>

							<div class="col-md-12 col-sm-12 col-xs-12 m-top-10 m-bottom-10 clearfix <?php if(form_error('leave_message_content')){ echo 'has-error';} ?>">
								<label for="leave_message_content" class="col-sm-2 control-label">Message Content</label>
								<div class="clearfix ">
									<div class="col-sm-10">
										<textarea class="form-control" id="leave_message_content" name="leave_message_content" style="height: 100px "><?php echo ($this->input->post('leave_message_content') ?  $this->input->post('leave_message_content') : $leave_email_defaults[0]->message ); ?></textarea>												
									</div>
								</div>
							</div>

							<div class="clearfix col-xs-12 text-center m-top-10"><strong> <p><i class="fa fa-quote-left"></i> Please use comma (,) to separate the Cc Emails and Bcc Emails.<i class="fa fa-quote-right"></i></p></strong></div>
							
							<div class="col-md-12 col-sm-12 col-xs-12 clearfix m-top-10 m-bottom-10">
					        	<button type="submit" class="btn btn-success pull-right m-bottom-10 m-right-5"><i class="fa fa-floppy-o"></i> Save Email Settings</button>
					        </div>

						</div>

					</form>
				</div>

		    </div>
		</div>

	<p><hr /></p>












	<div class="box blue-border m-top-0">
			<div class="box-head blue-bg pad-5">
				<label class="blue-title"><i class="fa fa-user fa-lg"></i> Reviews Settings</label>
			</div>
			<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/po_rev_settings">

				<div class="box-area clearfix pad-10">






					<div class="box yellow-border m-top-0">
						<!-- <div class="col-sm-12"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-edit fa-lg"></i> PO Review</label>
						</div>

						<div class="box-area clearfix pad-5">						

							<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="weeks_old" class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">Project Completion Date Greater </label>
								<div class="input-group">
									<span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>
									<select class="form-control" name="weeks_old" id="weeks_old">
									<?php echo '<option selected="" style="display:none;" value="'.$static_defaults[0]->po_rev_prj_age.'">'.($static_defaults[0]->po_rev_prj_age/7).' Week(s)</option>';  ?>

										<option value="7">1 Week</option>
										<option value="14">2 Weeks</option>
										<option value="21">3 Weeks</option>
										<option value="28">4 Weeks</option>
										<option value="31">5 Weeks</option>
									</select>
								</div>				
							</div>
							<script type="text/javascript"> // $('select#weeks_old').val('<?php echo $static_defaults[0]->po_rev_prj_age; ?>'); </script>



							<div class="col-md-3 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10 text-left">
								<label for="reminder_day_no" class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">PO Review Reminder Day</label>
								<div class="input-group col-sm-4">								 
									<select class="form-control" name="reminder_day_no" id="reminder_day_no">

									<?php echo '<option selected="" style="display:none;" value="'.$static_defaults[0]->po_rev_day.'">'.$static_defaults[0]->po_rev_day.'</option>';  ?>
										<?php for ($i_day_loop=1; $i_day_loop < 31; $i_day_loop++): ?>
											<option value="<?php echo $i_day_loop; ?>"><?php echo $i_day_loop; ?></option>										
										<?php endfor; ?>
									</select>
									<?php // echo $static_defaults[0]->days_psswrd_exp; ?>
								</div>				
							</div>
							<script type="text/javascript"> // $('select#reminder_day_no').val('<?php echo $static_defaults[0]->po_rev_day; ?>'); </script>


							<div class="col-md-5 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label for="set_cc_porw" class="col-sm-3 control-label m-top-5 text-right" style="font-weight: normal;">Send - PO Review </label>
								<div class="input-group col-sm-8">
									<input type="text" class="form-control" placeholder="Set Email" name="set_cc_porw" id="set_cc_porw" value="<?php echo $static_defaults[0]->po_email_cc; ?>">									
								</div>				
							</div>
	
								
		 

						</div>

						<!-- </div> -->
					</div>





					<div class="box yellow-border m-top-10">
						<!-- <div class="col-sm-12"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-edit fa-lg"></i> Project WIP Review</label>
						</div>

						<div class="box-area clearfix pad-5">						

							 

							<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label  class="col-sm-6  control-label m-top-5 text-right" style="font-weight: normal;">Project WIP Review Day</label>
								<div class="input-group">
									<span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>								
									<select class="form-control daysWipReportVal" id="days_wip_report" name="days_wip_report" >
										
<?php echo '<option selected="" style="display:none;" value="'.$static_defaults[0]->prj_review_day.'">'.$static_defaults[0]->prj_review_day.'</option>';  ?>

										<option value="Monday">Monday</option>
										<option value="Tuesday">Tuesday</option>
										<option value="Wednesday">Wednesday</option>
										<option value="Thursday">Thursday</option>
										<option value="Friday">Friday</option>
									</select> 
								
 	



								</div>	
							</div>



							<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-top-10 m-bottom-10">
								<label  class="col-sm-7  control-label m-top-5 text-right" style="font-weight: normal;">Send WIP Review Reminder Time</label>
								<div class="input-group">
									<span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>								
									<select class="form-control" id="send_wip_rrtime" name="send_wip_rrtime" >
										
<?php echo '<option selected="" style="display:none;" value="'.$static_defaults[0]->send_wip_rrtime.'">'.$static_defaults[0]->send_wip_rrtime.'</option>';  ?>

										<option value="5:00 AM">5:00 AM</option>
										<option value="6:00 AM">6:00 AM</option>
										<option value="7:00 AM">7:00 AM</option>
										<option value="8:00 AM">8:00 AM</option>
										<option value="9:00 AM">9:00 AM</option>
										<option value="10:00 AM">10:00 AM</option>
										<option value="11:00 AM">11:00 AM</option>
										<option value="1:00 PM">1:00 PM</option>
										<option value="2:00 PM">2:00 PM</option>
										<option value="3:00 PM">3:00 PM</option>
										<option value="4:00 PM">4:00 PM</option>
									</select> 
								
 	



								</div>	
							</div>


	
		 

						</div>

						<!-- </div> -->
					</div>


					<button type="submit" class=" pull-righ  btn btn-success pull-right m-top-10  m-right-5"><i class="fa fa-floppy-o"></i> Save Settings</button>	

				</div>
			</form>
		</div>

	<p><hr /></p>

<!-- Site labour User Settings -->	
		<div class="box blue-border m-top-0">
			<div class="box-head blue-bg pad-5">
				<label class="blue-title"><i class="fa fa-tasks fa-lg"></i> Site Labour Default Settings</label>
			</div>
			<div class="box-area clearfix pad-10">
				<div class="box yellow-border m-top-0">
					<!-- <div class="col-sm-8"> -->
						<div class="box-head yellow-bg pad-5">
							<label class="yellow-title"><i class="fa fa-users fa-lg"></i> Site Labour Employee Rate Set Setting</label>
							<button type = "button" class = "btn btn-xs btn-success pull-right" id = "add_rate_form"> Add Rate Set</button>
						</div>
						<div class="box-area clearfix pad-0">
							<!-- <div class="col-sm-12"> -->
								<table class="table table-condensed table-striped" style = "font-size: 12px; margin-bottom: 0;">
									<thead>
										<tr>
											<th>Rate Set Name</th>
											<th>Normal Rate</th>
											<th>Time and a Half Rate</th>
											<th>Double Time Rate</th>
											<th>Double Time and a Half Rate</th>
											<th>Travel</th>
											<th>Meal</th>
											<th>Leaving away from Home</th>
										</tr>
									</thead>
									<tbody>
										<?php $this->admin->fetch_emp_rate_set(); ?>
									</tbody>
									<?php //$this->admin->fetch_app_users_list(); ?>
								</table>

							<!-- </div> -->
						</div>	
					<!-- </div> -->

					<!-- <div class="col-sm-4"> -->
						<div class="box yellow-border col-sm-6 m-top-20 pad-0">
							<div class="box-head yellow-bg pad-5">
								<label class="yellow-title"><i class="fa fa-users fa-lg"></i> Site Labour Employee Rate Setting</label>
								<button type = "button" class = "btn btn-xs btn-success pull-right" id = "assign_rate"> Add Employee</button>
							</div>
							<div class="box-area clearfix pad-0">
								<!-- <div class="col-sm-12"> -->
									<table class="table table-condensed table-striped" style = "font-size: 12px; margin-bottom: 0;">
										<thead>
											<tr>
												<th>Employee Name</th>
												<th>Rate Set</th>
											</tr>
										</thead>
										<tbody>
											<?php $this->admin->fetch_employee_rate(); ?>
										</tbody>
									</table>

								<!-- </div> -->
							</div>
						</div>
					<!-- </div> -->

				</div>
			</div>
		</div>

		<div class="modal fade" id="add_rate_set_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog  modal-sm" style = "width:600px">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title" id="myModalLabel">Rate Set</h4>
		      		</div>
		      		<div class="modal-body" style = "height: 400px">
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Rate Set Name: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "rate_set_name"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Normal Rate: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "normal_rate"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Time and a Half Rate: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "time_half_rate"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Double Time Rate: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "double_time_rate"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Double Time and a Half Rate: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "double_time_half_rate"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Travel Allowance: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "travel_allowance"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Meal Allowance: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "meal_allowance"></div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 col-sm-offset-2 text-right pad-5">Leave away from home Allowance: </div>
		      			<div class="col-sm-4 pad-5"><input type="text" class = "form-control input-sm" id = "lafh_allowance"></div>
		      		</div>
		      		<div class="modal-footer">
		        		<button type = "button" class="btn pull-right" data-dismiss="modal">close</button>
		        		<button type = "button" class="btn pull-right btn-success" data-dismiss="modal" id = "add_rate_set">Add</button>
		        		<button type = "button" class="btn pull-right btn-success" data-dismiss="modal" id = "update_rate_set">Update</button>
		        		<button type = "button" class="btn pull-left btn-danger" data-dismiss="modal" id = "remove_rate_set">Remove</button>
		      		</div>
		    	</div>
		  	</div>
		</div>

		<div class="modal fade" id="from_assign_rate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog  modal-sm" style = "width:500px">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title" id="myModalLabel">Assign Rate</h4>
		      		</div>
		      		<div class="modal-body">
		      			<div class="col-sm-4 text-right pad-5">Select Employee: </div>
		      			<div class="col-sm-8 pad-5">
		      				<select name="txt_add_user" id="txt_add_user" class = "input-sm form-control">
								<?php echo $this->site_labour->display_app_users_for_form_admin(); ?>
							</select>
						</div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 text-right pad-5">Select Rate: </div>
		      			<div class="col-sm-8 pad-5">
		      				<select name="txt_add_rate" id="txt_add_rate" class = "input-sm form-control">
								<?php echo $this->admin->display_rate_set_for_form(); ?>
							</select></div>
		      			<div class="clearfix"></div>
		      		</div>
		      		<div class="modal-footer">
		        		<button type = "button" class="btn pull-right" data-dismiss="modal">close</button>
		        		<button type = "button" class="btn pull-right btn-success" data-dismiss="modal" id = "add_rate_assignment">Add</button>
		        		<button type = "button" class="btn pull-right btn-success" data-dismiss="modal" id = "update_rate_assignment">Update</button>
		        		<button type = "button" class="btn pull-left btn-danger" data-dismiss="modal" id = "remove_rate_assignment">Remove</button>
		      		</div>
		    	</div>
		  	</div>
		</div>

		<style>
			.modal {
			    overflow-y: auto;
			}
		</style>



		<script>
			$("#from_assign_rate").draggable({
			    handle: ".modal-header"
			});

			$("#add_rate_set_form").draggable({
			    handle: ".modal-header"
			});

			$(document).ready(function() {								

				var baseurl = '<?php echo base_url() ?>';
				var app = new Vue({
			      	el: '#defaults',
			      	data: {
			      		jobCategories:[],
			        	licenseCertTypes: [],
			        	requiredLC: [],
			        	activity_id: "",			
						license_cert_id: "",
						activity_id: "",
						license_cert_id: "",
						exempted_project_list: [],
						project_list: [],
						state_list: [],
						induction_filter_state: "",
						induction_filter_start_postcode: "",
						induction_filter_ends_postcode: "",
						exemptedPostcodeList: [],

						//======== Incident Report
						users_list: [],
			        	report_receiver: "",
						alter_report_receiver: "",
						imidiate_preventive_action_filler: "",
						recommended_action_filler: "",

						imidiate_preventive_action_filler_nsw: "",
						recommended_action_filler_nsw: "",
						report_reviewer: "",
						incidentReportDefaults: [],

						sl_email_users: [],
						reviewer: "",
						lc_state_id: 0,

						role_list: [],
						doc_type_list: [],
						doc_type_required_list: [],
						doc_type:"",
						doc_storage_defaults: [],
						authorize_role_id: 0,
						email_subject: "",
						email_content: "",

						cm_wa: "",
						cm_nsw: "",
						notif_receiver: [],
			        },

			        filters: {
			          	getDayname: function(date){
			            	return moment(date).format('ddd');
			          	},
	          			ausdate: function(date) {
	            			return moment(date).format('DD/MM/YYYY');
	          			},
	          			getTime: function(date){
	            			return moment(date).format('h:m a');
	          			},
	          			replaceApos: function(string){
	          				var string = string.replace("&apos;", "'");
	            			return string;
	          			},

	          			reformat: function(string){
	          				var string = string.replace("&amp;", "&");
	            			return string;
	          			},
	      			},

			        mounted: function(){
			        	this.fetchJobCategories();
			        	this.fetchLicenseCert();
			        	this.fetchRequiredLC();
			        	this.fetchExemptedProjectList();
			        	this.fetchProjectList();
			        	this.fethcState();
			        	this.fetchPostcodeExempttion();

			        	// Incident Report
			        	this.fetchUsers();
		        		this.fetchIncidentReportDefaults();
		        		this.load_sl_email_users();
		        		this.load_roles();
		        		this.load_doc_types();
		        		this.load_doc_type_required();
		        		this.load_doc_storage_defaults();
			        },
			        methods: {
			        	load_doc_storage_defaults: function(){
			        		axios.post(baseurl+"admin/fetch_default_doc_storage", 
					        {}).then(response => { 
					        	this.doc_storage_defaults = response.data;
					        	for (var key in app.doc_storage_defaults) {
						            this.authorize_role_id = app.doc_storage_defaults[key].authorize_role_id;
						            this.email_subject = app.doc_storage_defaults[key].email_subject;
						            this.email_content = app.doc_storage_defaults[key].email_content;

						            if(app.doc_storage_defaults[key].to_pa == 1){
						            	this.notif_receiver.push( "pa" );
						            }

						            if(app.doc_storage_defaults[key].to_pm == 1){
						            	this.notif_receiver.push( "pm" );
						            }

						            if(app.doc_storage_defaults[key].to_set_out == 1){
						            	this.notif_receiver.push( "set_out" );
						            }

						            if(app.doc_storage_defaults[key].to_joinery == 1){
						            	this.notif_receiver.push( "joinery" );
						            }
						           	
						           	this.cm_wa = app.doc_storage_defaults[key].cm_wa;
									this.cm_nsw = app.doc_storage_defaults[key].cm_nsw;
						        }
					        }).catch(error => {
					            console.log(error.response);
					        });
			        	},

			        	save_receiver: function(){
			        		axios.post(baseurl+"admin/insert_default_doc_storage_receiver", 
					        {
					        	'notif_receiver': this.notif_receiver,
					        	'cm_wa': this.cm_wa,
								'cm_nsw': this.cm_nsw
					        }).then(response => { 
					        	alert(response.data);
					        	this.load_doc_storage_defaults();
					        }).catch(error => {
					            console.log(error.response);
					        });
			        	},

			        	save_doc_storage_default: function(){
			        		axios.post(baseurl+"admin/save_default_doc_storage", 
					        {
					        	'authorize_role_id': this.authorize_role_id,
								'email_subject': this.email_subject,
								'email_content': this.email_content
					        }).then(response => { 
					        	alert("Doc storage default saved");
					        	this.load_doc_storage_defaults();
					        }).catch(error => {
					            console.log(error.response);
					        });
			        	},

			        	load_roles: function(){
			        		axios.post(baseurl+"admin/fetch_all_roles", 
					        {}).then(response => { 
					        	this.role_list = response.data;
					        }).catch(error => {
					            console.log(error.response);
					        });
			        	},

			        	load_doc_types: function(){
			        		axios.post(baseurl+"admin/list_doc_type", 
					        {}).then(response => { 
					        	this.doc_type_list = response.data;
					        }).catch(error => {
					            console.log(error.response);
					        });
			        		
			        	},

			        	load_doc_type_required: function(){
			        		axios.post(baseurl+"admin/fetch_doc_storage_required_notification", 
					        {}).then(response => { 
					        	this.doc_type_required_list = response.data;
					        }).catch(error => {
					            console.log(error.response);
					        });
			        		
			        	},

			        	
						add_doc_type_required: function(){
							axios.post(baseurl+"admin/insert_required_doc_type", 
					        {
					        	'doc_type': this.doc_type
					        }).then(response => { 
					        	this.doc_type = "";
					        	this.load_doc_type_required();
					        }).catch(error => {
					            console.log(error.response);
					        });
						},

						delete_doc_required: function(doc_storage_required_notification_id){
							var r = confirm("Are you sure you want to remove selected Doc Type from the list?");
      						if (r == true) {
      							axios.post(baseurl+"admin/delete_required_doc_type", 
						        {
						        	'doc_storage_required_notification_id': doc_storage_required_notification_id
						        }).then(response => { 
						        	this.load_doc_type_required();
						        }).catch(error => {
						            console.log(error.response);
						        });
      						}
						},
						
			        	load_sl_email_users: function(){
			        		axios.post(baseurl+"site_labour/fetch_ss_email_users", 
					        {}).then(response => { 
					        	this.sl_email_users = response.data;
					        }).catch(error => {
					            console.log(error.response);
					        });
			        		
			        	},

			        	addReviewer: function(email_for){
			        		if(email_for == 1){
			        			var reviewer = $("#reviewer").val();
			        		}else{
			        			var reviewer = $("#accounts").val();
			        		}

			        		axios.post(baseurl+"site_labour/insert_site_labour_email_defaults", 
					        {
					        	'email_for': email_for,
								'user_id': reviewer

					        }).then(response => { 
					        	this.load_sl_email_users();
					        	$("#reviewer").val("");
					        }).catch(error => {
					            console.log(error.response);
					        });
			        	},

			        	removeReviewer: function(site_labour_email_defaults_id){
			        		axios.post(baseurl+"site_labour/delete_site_labour_email_defaults", 
					        {
					        	'site_labour_email_defaults_id': site_labour_email_defaults_id
					        }).then(response => { 
					        	this.load_sl_email_users();
					        }).catch(error => {
					            console.log(error.response);
					        });

			        	},

			        	fethcState: function(){
			        		$.post(baseurl+"induction_health_safety/fetch_state",
							{},
							function(result){
							   	app.state_list = JSON.parse(result);
							});
			        		
			        	},

			        	fetchPostcodeExempttion: function(){
			        		$.post(baseurl+"admin/fetch_exempted_postcode",
							{},
							function(result){
							   	app.exemptedPostcodeList = JSON.parse(result);
							});
			        	},

			        	fetchExemptedProjectList: function(){
			        		$.post(baseurl+"admin/fetch_exempted_project_list",
					        {},
					        function(result){
					        	app.exempted_project_list = JSON.parse(result);
					        });
			        	},

			        	fetchProjectList: function(){
			        		$.post(baseurl+"induction_health_safety/fetch_induction_projects_list",
					        {},
					        function(result){
					        	app.project_list = JSON.parse(result);
					        });
			        	},

			        	fetchJobCategories: function(){
			        		$.post(baseurl+"admin/display_all_job_category_type",
					        {},
					        function(result){
					        	app.jobCategories = JSON.parse(result);
					        });
			        	},
			        	
			        	fetchLicenseCert: function(){
			        		$.post(baseurl+"admin/display_license_cert",
					        {},
					        function(result){
					        	app.licenseCertTypes = JSON.parse(result);
					        });	
			        	},

			        	fetchRequiredLC: function(){
			        		$.post(baseurl+"admin/display_required_license_cert",
					        {},
					        function(result){
					        	app.requiredLC = JSON.parse(result);
					        });
			        	},

			        	addRequiredLC: function(){
			        		$.post(baseurl+"admin/add_required_license_cert",
					        {
					        	activity_id: this.activity_id,
								license_cert_id: this.license_cert_id,
								state_id: this.lc_state_id
					        },
					        function(result){
					        	app.requiredLC = JSON.parse(result);
					        });
			        		
			        	},

			        	removeRequiredLC: function(required_license_certificate_id){
			        		$.post(baseurl+"admin/remove_required_license_cert",
					        {
					        	required_license_certificate_id: required_license_certificate_id
					        },
					        function(result){
					        	app.requiredLC = JSON.parse(result);
					        });
			        		
			        	},

			        	add_exempted_project: function(){
			        		var project_number = $("#project_number").val();

							$.post(baseurl+"admin/add_exempted_project",
							{
							   	project_number: project_number
							},
							function(result){
							   	app.exempted_project_list = JSON.parse(result);
							});
			        	},

			        	remove_exempted_project: function(induction_exempted_projects_id){
							$.post(baseurl+"admin/remove_exempted_project",
							{
							   	induction_exempted_projects_id: induction_exempted_projects_id
							},
							function(result){
							   	app.exempted_project_list = JSON.parse(result);
							});
			        	},

			        	add_exempted_postcode: function(){

							$.post(baseurl+"admin/add_exempted_postcode",
							{
							   	induction_filter_state: this.induction_filter_state,
								induction_filter_start_postcode: this.induction_filter_start_postcode,
								induction_filter_ends_postcode: this.induction_filter_ends_postcode
							},
							function(result){
							   	app.exemptedPostcodeList = JSON.parse(result);
							});
			        	},

			        	remove_exempted_postcode:function(induction_postcode_filters_id){

							$.post(baseurl+"admin/remove_exempted_postcode",
							{
							   	induction_postcode_filters_id: induction_postcode_filters_id
							},
							function(result){
							   	app.exemptedPostcodeList = JSON.parse(result);
							});
			        	},

			        	// Incident Report
			        	fetchUsers: function(){
				          	axios.post(baseurl+"incident_report/fetch_users", 
				          	{}).then(response => {
				            	this.users_list = response.data;               
				          	}).catch(error => {
				            	console.log(error.response)
				          	});
				        },

				        fetchIncidentReportDefaults: function(){
				        	setTimeout(() =>{
					        axios.post(baseurl+"incident_report/fetch_incident_report_defaults", 
					        {}).then(response => {
					            this.incidentReportDefaults = response.data; 
					            for (var key in app.incidentReportDefaults) {
						            this.report_receiver = app.incidentReportDefaults[key].received_report;
									this.alter_report_receiver = app.incidentReportDefaults[key].alt_received_report;
									this.imidiate_preventive_action_filler = app.incidentReportDefaults[key].fill_ipa;
									this.recommended_action_filler = app.incidentReportDefaults[key].fill_rpa;

									this.imidiate_preventive_action_filler_nsw = app.incidentReportDefaults[key].fill_ipa_nsw;
									this.recommended_action_filler_nsw = app.incidentReportDefaults[key].fill_rpa_nsw;

									this.report_reviewer = app.incidentReportDefaults[key].reviewer;
						           }   
              
					        }).catch(error => {
					            console.log(error.response)
					        });
					        }, 1000);
				        },

				        save_ir_defaults: function(){
				        	axios.post(baseurl+"incident_report/update_incident_report_defaults", 
					        {
					        	'received_report': this.report_receiver,
								'alt_received_report': this.alter_report_receiver,
								'fill_ipa': this.imidiate_preventive_action_filler,
								'fill_rpa': this.recommended_action_filler,
								'fill_ipa_nsw': this.imidiate_preventive_action_filler_nsw,
								'fill_rpa_nsw': this.recommended_action_filler_nsw,
								'reviewer': this.report_reviewer
					        }).then(response => { 
					        	alert("Defaults Saved");
              					this.fetchIncidentReportDefaults();
					        }).catch(error => {
					            console.log(error.response)
					        });
				        	
				        },


			        },

			        computed:{
				        orderUsers: function(){
				          	return this.users_list.sort((a,b) => a.user_first_name > b.user_first_name ? 1 : -1);
				        },

				    } 


			    });
			

			$("#add_rate_form").click(function(){
				$('#rate_set_name').val("");
				$('#normal_rate').val(0);
				$('#time_half_rate').val(0);
				$('#double_time_rate').val(0);
				$('#double_time_half_rate').val(0);
				$('#travel_allowance').val(0);
				$('#meal_allowance').val(0);
				$('#lafh_allowance').val(0);
				$("#add_rate_set").show();
				$("#update_rate_set").hide();
				$("#remove_rate_set").hide();
				$("#add_rate_set_form").modal('show');
			});

			$("#add_rate_set").click(function(){
				var rate_set_name = $('#rate_set_name').val();
				var normal_rate = $('#normal_rate').val();
				var time_half_rate = $('#time_half_rate').val();
				var double_time_rate = $('#double_time_rate').val();
				var double_time_half_rate = $('#double_time_half_rate').val();
				var travel_allowance = $('#travel_allowance').val();
				var meal_allowance = $('#meal_allowance').val();
				var lafh_allowance = $('#lafh_allowance').val();

				if(rate_set_name == ""){
					alert("Rate Set Name is required");
				}else{
					$.post(baseurl+"admin/insert_rate_set",
				    {
				      	rate_set_name: rate_set_name,
						normal_rate: normal_rate,
						time_half_rate: time_half_rate, 
						double_time_rate: double_time_rate, 
						double_time_half_rate: double_time_half_rate,
						travel_allowance: travel_allowance,
						meal_allowance: meal_allowance,
						lafh_allowance: lafh_allowance
				    },
				    function(result){
				    	window.open(baseurl+'admin?curr_tab=users', '_self', true);
				    });
				}
			});
			var employee_rate_set_id = 0;
			window.edit_rate_set = function(a){
				employee_rate_set_id = a;
				$.post(baseurl+"admin/fetch_selected_rate_set",
				{
				   	employee_rate_set_id: employee_rate_set_id
			    },
				function(result){
					var result_arr = result.split('/');
				    var rate_set_name = result_arr[0];
					var normal_rate = result_arr[1];
					var time_half_rate = result_arr[2];
					var double_time_rate = result_arr[3];
					var double_time_half_rate = result_arr[4];
					var travel_allowance = result_arr[5];
					var meal_allowance = result_arr[6];
					var lafh_allowance = result_arr[7];
					$('#rate_set_name').val(rate_set_name);
					$('#normal_rate').val(normal_rate);
					$('#time_half_rate').val(time_half_rate);
					$('#double_time_rate').val(double_time_rate);
					$('#double_time_half_rate').val(double_time_half_rate);
					$('#travel_allowance').val(travel_allowance);
					$('#meal_allowance').val(meal_allowance);
					$('#lafh_allowance').val(lafh_allowance);
					$("#add_rate_set").hide();
					$("#update_rate_set").show();
					$("#remove_rate_set").show();
					$("#add_rate_set_form").modal('show');


				});
			};

			$("#update_rate_set").click(function(){
				var rate_set_name = $('#rate_set_name').val();
				var normal_rate = $('#normal_rate').val();
				var time_half_rate = $('#time_half_rate').val();
				var double_time_rate = $('#double_time_rate').val();
				var double_time_half_rate = $('#double_time_half_rate').val();
				var travel_allowance = $('#travel_allowance').val();
				var meal_allowance = $('#meal_allowance').val();
				var lafh_allowance = $('#lafh_allowance').val();
				$.post(baseurl+"admin/update_selected_rate_set",
				{
				   	employee_rate_set_id: employee_rate_set_id,
				   	rate_set_name: rate_set_name,
					normal_rate: normal_rate,
					time_half_rate: time_half_rate, 
					double_time_rate: double_time_rate, 
					double_time_half_rate: double_time_half_rate,
					travel_allowance: travel_allowance,
					meal_allowance: meal_allowance,
					lafh_allowance: lafh_allowance
			    },
				function(result){
				    window.open(baseurl+'admin?curr_tab=users', '_self', true);
				});
			})

			$("#remove_rate_set").click(function(){
				$.post(baseurl+"admin/remove_selected_rate_set",
				{
				   	employee_rate_set_id: employee_rate_set_id
			    },
				function(result){
					window.open(baseurl+'admin', '_self', true);
				});
			})

			$("#assign_rate").click(function(){
				$("#add_rate_assignment").show();
				$("#update_rate_assignment").hide();
				$("#remove_rate_assignment").hide();
				$("#from_assign_rate").modal('show');
			});

			$("#add_rate_assignment").click(function(){
				var user_id = $("#txt_add_user").val();
				var rate_id = $("#txt_add_rate").val();

				$.post(baseurl+"admin/insert_employee_rate",
				{
				   	user_id: user_id,
				   	rate_id: rate_id
			    },
				function(result){
					window.open(baseurl+'admin?curr_tab=users', '_self', true);
				});
			});

			var employee_rate_id = 0;
			window.edit_emp_rate = function(a){
				employee_rate_id = a;
				$.post(baseurl+"admin/fetch_assigned_employee_rate",
				{
				   	employee_rate_id: employee_rate_id
			    },
				function(result){
					var result_arr = result.split('/');
					var user_id = result_arr[0];
					var rate_id = result_arr[1];
					$("#txt_add_user").val(user_id);
					$("#txt_add_rate").val(rate_id);

					$("#add_rate_assignment").hide();
					$("#update_rate_assignment").show();
					$("#remove_rate_assignment").show();
					$("#from_assign_rate").modal('show');
				});
			}

			$("#update_rate_assignment").click(function(){
				var user_id = $("#txt_add_user").val();
				var rate_id = $("#txt_add_rate").val();

				$.post(baseurl+"admin/update_employee_rate",
				{
					employee_rate_id: employee_rate_id,
				   	user_id: user_id,
				   	rate_id: rate_id
			    },
				function(result){
					window.open(baseurl+'admin?curr_tab=users', '_self', true);
				});
			});

			$("#remove_rate_assignment").click(function(){
				$.post(baseurl+"admin/remove_employee_rate",
				{
					employee_rate_id: employee_rate_id
			    },
				function(result){
					window.open(baseurl+'admin?curr_tab=users', '_self', true);
				});
			});
			});
		</script>
		
		<p><hr /></p>

		<div class="box blue-border m-top-0">
			<div class="box-head blue-bg pad-5">
				<label class="blue-title"><i class="fa fa-map-marker fa-lg"></i> Employee Location Default Settings</label>
			</div>
			<div class="box-area clearfix pad-5">
				



					<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-bottom-10">

					<?php if(@$this->session->flashdata('delete_location_assign')): ?>
						<div class="m-top-5">							
							<div class="no-pad-t">
								<div class="border-less-box alert alert-success fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Cheers!</h4>
									<?php echo $this->session->flashdata('delete_location_assign');?>
								</div>
							</div>
						</div>
					<?php endif; ?>



						<div class="box yellow-border">
							<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/location_assignments">
								<div class="box-head yellow-bg pad-5">
									<label class="yellow-title"><i class="fa fa-map fa-lg"></i> Employee Location</label>
								</div>
								<div class="box-area pad-10 clearfix" id="container">

								 	<?php $location_b = ''; $counter = 0;?>
									<?php foreach($users_set_location as $key => $set_location_set): ?>
										<?php

											$location_a = $set_location_set->location;


											if($location_a != $location_b && $counter > 0){  echo "<p><br /></p>"; }

											if($location_a != $location_b){
												$location_b = $set_location_set->location;
												echo '<p><strong>'.$set_location_set->location.'</strong></p>'; 
												echo "<hr style='margin-bottom: 5px'>";
												$counter++;
											}

											echo '<a href="?rem_loc_id='.$set_location_set->user_id.'" class="btn btn-danger btn-xs m-bottom-5 m-right-5"><i class="fa fa-times"></i></a>';
											echo $set_location_set->user_first_name.' '.$set_location_set->user_last_name.'<br />'; 

										 ?>
									<?php endforeach; ?>

 
								</div>
							</form>
						</div>


					</div>







					<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-bottom-10">

					<?php if(@$this->session->flashdata('user_location_assign')): ?>
						<div class="m-top-5">							
							<div class="no-pad-t">
								<div class="border-less-box alert alert-success fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Cheers!</h4>
									<?php echo $this->session->flashdata('user_location_assign');?>
								</div>
							</div>
						</div>
					<?php endif; ?>



						<div class="box yellow-border">
							<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/location_assignments">
								<div class="box-head yellow-bg pad-5">
									<label class="yellow-title"><i class="fa fa-info-circle fa-lg"></i> Location Assignments</label>
								</div>
								<div class="box-area pad-10 clearfix" id="container">

									<div class="m-bottom-10 clearfix ">
										<select class="form-control" name="location">
											<option value="" disabled="" selected="">Select Location</option>
											<?php foreach($user_location as $key => $location): ?>
												<option value="<?php echo $location->location_address_id; ?>" ><?php echo $location->location; ?></option>
											<?php endforeach; ?>
										</select>
									</div>

									


									<?php $set_user = array();  ?>
									<?php foreach($set_user_location as $key => $location_set): ?>
										<?php array_push($set_user , $location_set->user_id);  ?>
									<?php endforeach; ?>


									<div class="clearfix ">
										<select name="employee_location[]" multiple="" style="width: 100%; margin-bottom: 10px;" size="9">
											<option value="" disabled="" selected="">Select Employee</option>

											<?php foreach($users as $key => $user): ?>
												<?php if(!in_array($user->user_id, $set_user) ): ?>
													<option value="<?php echo $user->user_id; ?>" ><?php echo $user->user_first_name.' '.$user->user_last_name; ?></option>
												<?php endif; ?>
											<?php endforeach; ?>

										</select>
									</div>

									<button type="submit" class="btn btn-success pull-right m-right-5"><i class="fa fa-floppy-o"></i> Save Asignment</button>	
								</div>
							</form>
						</div>


					</div>

				<div class="col-md-4 col-sm-6 col-xs-12 clearfix m-bottom-10">


					<?php if(@$this->session->flashdata('update_user_location')): ?>
						<div class="m-top-5">							
							<div class="no-pad-t">
								<div class="border-less-box alert alert-success fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Cheers!</h4>
									<?php echo $this->session->flashdata('update_user_location');?>
								</div>
							</div>
						</div>
					<?php endif; ?>


					<div class="box yellow-border">
						<form class="form-horizontal" role="form" method="post" action="<?php echo current_url(); ?>/user_location">
							<div class="box-head yellow-bg pad-5">
								<label class="yellow-title"><i class="fa fa-map-pin fa-lg"></i> Add Location</label>
							</div>
							<div class="box-area pad-10 clearfix" id="container">

								<div class="m-bottom-10 clearfix ">
									<textarea class="form-control" id="location" name="location" placeholder="Location" style="height: 100px; z-index: auto; position: relative; line-height: 20px; font-size: 14px; transition: none; background: transparent !important;"></textarea>
								</div>

								<div class="m-bottom-10 clearfix ">
									<label for="Xcoordinate" class="col-sm-4 control-label">X Coordinate</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="Xcoordinate" name="xcoordinate" placeholder="X coordinate" value="">
									</div>
								</div>

								<div class="m-bottom-10 clearfix ">
									<label for="Ycoordinate" class="col-sm-4 control-label">Y Coordinate</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="Ycoordinate" name="ycoordinate" placeholder="Y coordinate" value="">
									</div>
								</div>

								<button type="submit" class="btn btn-success pull-right m-bottom-10 m-right-5"><i class="fa fa-floppy-o"></i> Save Location</button>	
							</div>
						</form>
					</div>


				</div>
			</div>
		</div>




</div>


	<div class="tab-pane fade in <?php echo ($curr_tab == 'themes' ? 'active' : '' ); ?>" id="themes" aria-labelledby="themes">
	
	<div class="box yellow-border">
										<div class="box-head yellow-bg pad-5 m-bottom-5">
											<label class="yellow-title"><i class="fa fa-leaf fa-lg"></i> Season Setup</label>
										</div>

										<div class="box-area pad-5 clearfix">

										<form class="form-horizontal clearfix form " role="form" method="post" action="<?php echo current_url(); ?>/new_season" >
												
												<div class="col-sm-3">
													<input  type="text"  placeholder="Season Name" class="form-control" id="season_name1" name="season_name" value="" >
												</div>

												<div class="col-sm-2">
													<input  type="text"  placeholder="Start DD/MM" class="form-control" id="bg_start" name="bg_start" value="" >
												</div>

												<div class="col-sm-2">
													<input  type="text"  placeholder="Finish DD/MM" class="form-control" id="bg_finish" name="bg_finish" value="" >
												</div>

												<div class="col-sm-2"><button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save Season</button></div>

											</form>	

											<p><hr style="margin:0px 5px 15px;" /></p>




											<form class="form-horizontal clearfix form " role="form" method="post" action="<?php echo current_url(); ?>/upload_signin_bg" accept-charset="utf-8" enctype="multipart/form-data">
												
												<div class="col-sm-3">
													<input type="file" id="bg_img_signin" name="bg_img_signin" class="form-control"  />
												</div>

												<div class="col-sm-2">
													<select class="form-control" name="season_id">
														<option value="" disabled selected>Select Season</option>
														<?php foreach ($seasons as $s_bg):
															echo '<option value="'.$s_bg->seasons_id.'">'.$s_bg->seasons_label.'</option>';															
														endforeach;?>
														
													</select>
												</div>



												<div class="col-sm-2"><button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Upload Now</button></div>

											</form>	




											<?php //echo $this->admin->list_bg_login(); ?>


										</div>


 

											
										</div> 

							
											<script type="text/javascript">

$('#bg_start').datetimepicker({ format: 'DD/MM' ,useCurrent: false});
$('#bg_finish').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM'
});
$("#bg_start").on("dp.change", function (e) {
$('#bg_finish').data("DateTimePicker").minDate(e.date);

$('#bg_finish').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM'
});
 

});
$("#bg_finish").on("dp.change", function (e) {
$(this).data("DateTimePicker").minDate(e.date);
$('#bg_start').data("DateTimePicker").maxDate(e.date); 
});


function bgDatEdit(myId){ 

	//alert('teste');

	 var bg_id = myId;


	var season_name = $('#'+myId).parent().find('a.seasons_label_view').text();
	 

	var sdate = $('#'+myId).parent().find('strong span.start_date_bg').text();
	var fdate = $('#'+myId).parent().find('strong span.finish_date_bg').text(); 

	$('input#season_name').val(season_name);

	$('input#bg_start_e').val(sdate);
	$('input#bg_finish_e').val(fdate);
	$('input#img_bg_id').val(bg_id);


	 alert(bg_id+my_title);
}

</script>	 






<p><hr /></p>
	


<div class="">
	<h3>Season Backgrounds</h3>
	<p><strong>Note:</strong> Make sure that the <strong>season</strong> is added before asignment of background.</p>
	<div class="panel-group" id="seasons_accordion">


		
	<?php echo $this->admin->get_seasons(); ?>



	</div> 
</div>
	
</div>



							</div>
							</div>
						</div>
					</div>

 

					
				</div>				
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('assets/logout-modal'); ?>

<div class="modal fade" id="bg_design_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog  modal-lg"  style="width: 70%;">
		    	 
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title" id="bg_design_view_label">View</h4>
		      		</div>
		      		<div class="modal-body">
		      			<img src="<?php echo base_url(); ?>img/loading.jpg" class="bg_design_view_img" id="bg_design_view_img" style="width:100%">
		      			<img src="<?php echo base_url(); ?>img/login_overlay.png" id="login_overlay_img" class="login_overlay_img" style="width:100%;position: absolute;top: 0;left: 0;">

						<input type="hidden" name="bg_id" id="img_view_id">
		      		</div>
		      		<div class="modal-footer"> 
		      			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        		<a href="#" class="pull-left" class="use_background_view_img" id="use_background_view_img"><div class="btn-info btn btn-success">Use Background</div>  </a>
		      		</div>
		    	</div>
		  	</div>
		</div>


		<div class="modal fade" id="bg_date_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog  modal-sm" style = "width:500px">
		    	<form method="post" action="<?php echo base_url(); ?>admin/update_bg_login">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title" id="myModalLabel">Update Date</h4>
		      		</div>
		      		<div class="modal-body">
		      			<div class="col-sm-4 text-right pad-5">Season: </div>
		      			<div class="col-sm-8 pad-5">
		      				<input type="text" placeholder="Season Name" class="form-control text-right" id="season_name" name="season_name" value="">
						</div>
		      			<div class="col-sm-4 text-right pad-5">Start Date: </div>
		      			<div class="col-sm-8 pad-5">
		      				<input type="text" placeholder="Start DD/MM" class="form-control text-right" id="bg_start_e" name="bg_start" value="">
						</div>
		      			<div class="clearfix"></div>
		      			<div class="col-sm-4 text-right pad-5">Finish Date: </div>
		      			<div class="col-sm-8 pad-5">
		      				<input type="text" placeholder="Finish DD/MM" class="form-control text-right" id="bg_finish_e" name="bg_finish" value="">
						</div>
						<input type="hidden" name="bg_id" id="img_bg_id">
		      			<div class="clearfix"></div>
		      		</div>
		      		<div class="modal-footer"> 
		        		<input type="submit" value="Save" class="btn-info btn">
		      		</div>
		    	</div>
		    	</form>
		  	</div>
		</div>


		<div class="modal fade" id="archive_document_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog  " style = "width:700px">
		    	
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title" id="myModalLabel">Archive Type Details</h4>
		      		</div>
		      		<div class="modal-body pad-10">	


		      			<p class="h4" style="font-size: 16px;"><strong>Archive Name</strong></p> <hr style="margin-top: 5px; margin-bottom: 10px;">	      		


		      			<form method="post" action="https://localhost/focus/admin/update_arc_doc_type" id="update_arc_doc_type">
		      				<div class="input-group m-bottom-15">
		      					<a href="#" class="input-group-addon btn btn-danger" style="color:#fff;" id="delete_archive_type_lnk" onclick="$('#archive_document_details').modal('hide')" ><i class="fa fa-trash" aria-hidden="true"></i></a>
		      					<span class="input-group-addon"><i class="fa fa-file"></i></span>
		      					<input placeholder="Archive Name" type="text" name="archive_name" requaired autocomplete="off" id="archive_name_edt" class="form-control">
		      					<input type="hidden" name="archive_registry_types" id="archive_registry_types">
		      					<span class="input-group-addon btn btn-success" onClick=" $('form#update_arc_doc_type').submit(); " style="color:#fff;"><i class="fa fa-floppy-o"></i></span>
		      				</div>
		      			</form>


		      			<p class="h4 m-top-20" style="font-size: 16px;"><strong>Employee</strong></p> <hr style="margin-top: 5px; margin-bottom: 10px;">	 


		      			<div id="" class="cust_frm_cntrld">

		      				<form method="post" id="org_form" action="<?php echo base_url(); ?>admin/update_archive_details" class="set_emp_cntrled">
		      					<div class="input-group  m-bottom-10 ">

		      						<a href="#" class="input-group-addon btn btn-danger delete_archive_details" style="color:#fff;"  onclick="$('#archive_document_details'" ><i class="fa fa-trash" aria-hidden="true"></i></a>
		      						<span class="input-group-addon"  ><i class="fa fa-user fa-lg"></i></span> 
		      						<select class="form-control re_assign_emp_edt" name="emp_name" id="" style="">
		      							<?php foreach($users as $key => $user_data): ?>
		      								<option value="<?php echo $user_data->user_id; ?>" > <?php echo $user_data->user_first_name.' '.$user_data->user_last_name; ?> </option>
		      							<?php endforeach; ?>
		      						</select>

		      						<span id="" class="input-group-addon"><i class="fa fa-calendar"></i>  </span>


									<input type="text" placeholder="DD/MM/YYYY" class="form-control datepicker date_expiry_edt" data-date-format="dd/mm/yyyy" id="date_expiry_edt" name="date_expiry_edt" value="" style="z-index:0 !important;"  autocomplete="off">



		      						<span class="input-group-addon btn btn-success" style="color:#fff;" onClick=" $(this).parent().parent().submit(); " ><i class="fa fa-floppy-o"></i></span>
		      					</div>

		      					<input type="hidden" name="archive_id_edt" class="archive_id_edt">
		      					<div class="clearfix"><hr style="margin: 0px; padding: 3px 0px 5px;"></div>
		      					<div class="clearfix"> </div>
		      				</form>

		      			</div>
		      		</div>
		      	</div>
		      </div>
		</div>


<script type="text/javascript">


	$( document ).ready(function() {		

		$("input:file#btnUploadWA").change(function(){
			$("#frmUploadWA").submit();
		});

		$("input:file#btnUploadNSW").change(function(){
			$("#frmUploadNSW").submit();
		});	
		    		
		$('#btnSalariedRates').click(function(){		
					
			$('h4#myModalLabel.modal-title.msgbox').html("Warning");		
			$('#confirmText').html('Leave settings and approved leaves of all the Salaried - Au Staff will be removed, are you sure you want to continue?');		
		    $('#confirmButtons').html('<button type="button" class="btn btn-warning" onclick="confirmSalariedSave();">Yes</button>' +		
		    			              '<button type="button" class="btn btn-success" data-dismiss="modal">No</button>');		
		    $('#confirmModal').modal({backdrop: true, keyboard: false, show: true});		
			$('#confirmModal').data('bs.modal').options.backdrop = 'static';		
		
		});		
		
				
		$('#btnWagesRates').click(function(){		
					
			$('h4#myModalLabel.modal-title.msgbox').html("Warning");		
			$('#confirmText').html('Leave settings and approved leaves of all the Wages - Au Staff will be removed, are you sure you want to continue?');		
		    $('#confirmButtons').html('<button type="button" class="btn btn-warning" onclick="confirmWagesSave();">Yes</button>' +		
		    			              '<button type="button" class="btn btn-success" data-dismiss="modal">No</button>');		
		    $('#confirmModal').modal({backdrop: true, keyboard: false, show: true});		
			$('#confirmModal').data('bs.modal').options.backdrop = 'static';		
		
		});		
		
		$('#btnManilaRates').click(function(){		
					
			$('h4#myModalLabel.modal-title.msgbox').html("Warning");		
			$('#confirmText').html('Leave settings and approved leaves of all the Manila Staff will be removed, are you sure you want to continue?');		
		    $('#confirmButtons').html('<button type="button" class="btn btn-warning" onclick="confirmManilaSave();">Yes</button>' +		
		    			              '<button type="button" class="btn btn-success" data-dismiss="modal">No</button>');		
		    $('#confirmModal').modal({backdrop: true, keyboard: false, show: true});		
			$('#confirmModal').data('bs.modal').options.backdrop = 'static';		
		
		});		
		
	});		
		
	function confirmSalariedSave(){		
		$("#frmSalariedRates").submit();		
	}		
		
	function confirmWagesSave(){		
		$("#frmWagesRates").submit();		
	}		
		
	function confirmManilaSave(){		
		$("#frmManilaRates").submit();		
	}


  setTimeout(function(){  
$('.edit_archive_document_details_btn').click(function(){
	$('form.added_frm').remove();
	var btn_id = $(this).attr('id');
	var loop_count = 0;
	var emp_id = [];
	var exp_dte = [];
	var arch_reg = [];

	var data_archive = btn_id.split("-");

	var user_id_assign = data_archive[0];
	var registry_type_id = data_archive[1];
	var expiry_date = data_archive[5];

	var registry_type = data_archive[3];
	var archive_registry_types = data_archive[1];
	var archive_registry_id = data_archive[2];

	$('a#delete_archive_type_lnk').prop('href','<?php echo base_url(); ?>admin/delete_archive_type/'+registry_type_id)




	$('input#archive_name_edt').val(registry_type);
	$('input#archive_registry_types').val(archive_registry_types);




	emp_id = data_archive[0].split('_');
	exp_dte = data_archive[5].split('_');
	arch_reg = data_archive[2].split('_');

	loop_count = emp_id.length - 1;
	var limiter = 1;

//	alert(loop_count);
//	alert(emp_id.length);

	if( loop_count > 0){

		$('form#org_form select.re_assign_emp_edt').val(emp_id[0]);
		$('form#org_form input.date_expiry_edt').val(exp_dte[0]);
		$('form#org_form input.archive_id_edt').val(arch_reg[0]);
		$('form#org_form a.delete_archive_details').prop('href','<?php echo base_url(); ?>admin/del_arch_det/'+arch_reg[0]);


		//$('input#date_expiry_edt').val('');



		for (var start_loop = loop_count; start_loop >= 1; start_loop--) {
			if(limiter == 1){

				$( "form#org_form" ).clone().addClass('added_frm').attr('id','added_frm_'+start_loop).appendTo( ".cust_frm_cntrld" );



				$('form#added_frm_'+start_loop+' select.re_assign_emp_edt').val(emp_id[start_loop]);
				$('form#added_frm_'+start_loop+' input.date_expiry_edt').val(exp_dte[start_loop]);
				$('form#added_frm_'+start_loop+' input.archive_id_edt').val(arch_reg[start_loop]);
				$('form#added_frm_'+start_loop+' input.date_expiry_edt.datepicker').datepicker();
				$('form#added_frm_'+start_loop+' a.delete_archive_details').prop('href','<?php echo base_url(); ?>admin/del_arch_det/'+arch_reg[start_loop]);



		//	$('form.set_emp_cntrled.added_frm select.re_assign_emp_edt').val(emp_id[i]);
	//	alert(emp_id[i]);
	//	alert('add 1');

}


}



			limiter++;

	}else{

		$('select.re_assign_emp_edt').val(user_id_assign);
		$('input.date_expiry_edt').val(expiry_date);
		$('input.archive_id_edt').val(archive_registry_id);



		$('a.delete_archive_details').prop('href','<?php echo base_url(); ?>admin/del_arch_det/'+archive_registry_id);





	}


});














 },500);
$('#bg_start_e').datetimepicker({ format: 'DD/MM' ,useCurrent: false});
$('#bg_finish_e').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM'
});
$("#bg_start_e").on("dp.change", function (e) {
$('#bg_finish_e').data("DateTimePicker").minDate(e.date);

$('#bg_finish_e').datetimepicker({
useCurrent: false, //Important! See issue #1075
format: 'DD/MM'
});
 

});
$("#bg_finish_e").on("dp.change", function (e) {
$(this).data("DateTimePicker").minDate(e.date);
$('#bg_start_e').data("DateTimePicker").maxDate(e.date); 
});



$('.bg_design_view').click(function(){
	//alert('etst');

	var img_id_sesason = $(this).attr('id');
	var img_id_sesason_label = $(this).find('img').attr('alt');
	var bg_design_view_img = $(this).find('img').attr('src');

 
	$('img#bg_design_view_img').attr('src', bg_design_view_img);
	$('a#use_background_view_img').attr('href', '<?php echo base_url() ?>admin/set_background?id='+img_id_sesason);

	$('h4#bg_design_view_label').text(img_id_sesason_label);
});


$(function() {
	var select = $('select#remind_emp_on_expire');
	select.html(select.find('option').sort(function(x, y) {
		return $(x).text() > $(y).text() ? 1 : -1;
	}));
});


$(function() {
	var select = $('select#assgn_emp_name');
	select.html(select.find('option').sort(function(x, y) {
		return $(x).text() > $(y).text() ? 1 : -1;
	}));
});


$(function() {
	var select = $('select#user_assigned_forinsurance');
	select.html(select.find('option').sort(function(x, y) {
		return $(x).text() > $(y).text() ? 1 : -1;
	}));
});
 




 
	



setTimeout(function () {
	var cat_dsh_pcc_val = '<?php echo $cat_project_completion_calendar; ?>';
	var cat_arr_string = cat_dsh_pcc_val.split(',');
	var arrayLength = cat_arr_string.length;
	for (var i = 0; i < arrayLength; i++) {
		$("input.cat_pcc_dash[value='" + cat_arr_string[i] + "']").prop('checked', true);
	}
}, 1000); 





</script>
