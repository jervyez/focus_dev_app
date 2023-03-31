<?php use App\Modules\Contacts\Controllers\Contacts; ?>
<?php $this->contacts = new Contacts(); ?>

<?php use App\Modules\Bulletin_board\Controllers\Bulletin_board; ?>
<?php $this->bulletin_board = new Bulletin_board(); ?>

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
						<a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a>
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
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="left-section-box">	
								<div class="row clearfix">

									<div class="col-lg-4 col-md-12">
										<div class="box-head pad-left-15 clearfix" style="border: none;">

											<label class="custom-title">Focus Contact List</label><span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the Applications of Leave screen." data-original-title="Welcome">?</a>)</span>
											<p>This is where the contacts are listed.</p>
										</div>
									</div>

									<br>

									<div class="col-lg-8 col-md-12">
										<div class="pad-left-15 pad-right-10 clearfix box-tabs" style="margin-bottom: -1px;">	
											<ul id="myTab" class="nav nav-tabs pull-right" style="border-bottom: none;">
												<li class="active">
													<a href="#focus" data-toggle="tab" class="focus-tab"><i class="fa fa-address-book fa-lg"></i> Focus Company Contacts</a>
												</li>
												<li class="">
													<a href="#company" data-toggle="tab" class="company-tab"><i class="fa fa-building fa-lg"></i> Company Contacts</a>
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="box-area">
									<div class="box-tabs m-bottom-15">
										<div class="tab-content">
											
											<div class="tab-pane clearfix active" id="focus" style="border: 1px solid #DDDDDD; border-left: 0 !important;">
												<div class="m-bottom-15 clearfix">
													<div class="box-area pad-10 focus_area">

														<table style="width: 100%;" id="focus_contacts_tbl" class="table table-striped table-bordered dataTable no-footer" cellspacing="0" width="100%">
														  	<thead>
														  		<th>Contact Name</th>
														  		<th>Company</th>
														  		<th>Direct Landline</th>
														  		<th>Mobile Number</th>
														  		<th>Personal Mobile Number</th>
														  		<th>Email Address</th>
														  		<th>Personal Email Address</th>
														  		<th>Skype ID</th>
														  	</thead>
															<tbody>
																<?php echo $this->contacts->display_focus_contacts(); ?>
															</tbody>
														</table>

													</div><!-- ./box-area./focus_area -->
												</div><!-- ./m-bottom-15./clearfix -->
											</div><!-- ./tab-pane clearfix -->

											<div class="tab-pane clearfix" id="company" style="border: 1px solid #DDDDDD; border-left: 0 !important;">
												<div class="m-bottom-15 clearfix">
													<div class="box-area pad-10 contacts_area">
														<table style="width: 100%;" id="contacts_tbl" class="table table-striped table-bordered dataTable no-footer" cellspacing="0" width="100%">
														  	<thead>
														  		<th style="display: none;">Contact Type</th>
														  		<th>Contact Name</th>
														  		<th>Company</th>
														  		<th>Description</th>
														  		<th>Suburb</th>
														  		<th>State</th>
														  		<th width="150px;">Contact Number</th>
														  		<th>Email</th>
														  	</thead>
															<tbody>
																<?php echo $this->contacts->display_contacts(0); ?>
															</tbody>
														</table>

													</div><!-- /.box-area -->
												</div><!-- ./m-bottom-15./clearfix -->
											</div><!-- ./tab-pane./clearfix./active -->

										</div><!-- ./tab-content -->
									</div><!-- ./box-tabs./m-bottom-15 -->
								</div><!-- ./box-area -->
							</div><!-- ./left-section-box -->
						</div><!-- ./col-md-12 -->
					</div><!-- ./row-->				
				</div><!-- ./container-fluid -->
		</div><!-- ./section./col-sm-12./col-md-11./col-lg-11 -->
	</div><!-- ./row -->
</div><!-- ./container-fluid -->

<div style="display:none;" class="filter_contacts">
	<select id="filter_contacts" class="form-control pull-right input-sm m-left-10" style="width:200px;">
		<option value="">Select Contact Type</option>
		<option value="">All</option>
		<option value="1">Client</option>
		<option value="2">Contractor</option>
		<option value="3">Supplier</option>		
	</select>
</div>

<form style="display: none;" class="generate_focus_contacts form-inline pull-right m-left-20" role="form">
  <div class="form-group pull-right"">
    <select id="gen_type" class="form-control input-sm m-left-10" style="width:200px;">
		<option value="0">PDF</option>
		<option value="1">CSV</option>
	</select>

	<button type="button" id="gen_btn" class="btn btn-success input-sm">Generate</button>
  </div>
</form>

<div class="report_result hide hidden"></div>

<!-- Modal -->
<div class="modal fade" id="contact_information" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style = "width: 700px">
	    <div class="modal-content">
	        <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		        <h4 class="modal-title">Contact Information</h4>
	        </div>
	        <div class="modal-body" style = "height: 400px">
	        	<div class="col-sm-2 pad-5">Contact Name: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "contact_name" style = "font-size: 18px"></label></div>

	        	<div class="col-sm-12"></div>
	        	
	        	<div class="col-sm-2 pad-5">Company: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "company_name"></label></div>
	        	
	        	<div class="col-sm-12"></div>

	        	<div class="col-sm-2 pad-5">Location: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "company_location"></label></div>
	        	
				<div class="col-sm-12"></div>

	        	<div class="col-sm-2 pad-5">Description: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "company_desc"></label></div>
	        	
	        	<div class="col-sm-12"></div>

	        	<div class="col-sm-12 pad-10"><label for="">Contact Details</label></div>
	        	<hr style = "display: block; border-style: inset; border-width: 1px;">

	        	<div class="col-sm-2 pad-5">Office Number: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "office_num"></label></div>

	        	<div class="col-sm-12"></div>

	        	<div class="col-sm-2 pad-5">Mobile: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "mobile"></label></div>
	        	
	        	<div class="col-sm-12"></div>
	        	
	        	<div class="col-sm-2 pad-5">E-mail: </div>
	        	<div class="col-sm-10 pad-5"><label for="" id = "cont_email"></label></div>
	        </div>
	        <div class="modal-footer">
	          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
	    </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php $this->bulletin_board->list_latest_post(); ?>
<?php echo view('assets/logout-modal'); ?>

<script type="text/javascript">
	
	$('a.company-tab').on('shown.bs.tab', function (e) {
		$('label.custom-title').text('Company Contact List');
	})

	$('a.focus-tab').on('shown.bs.tab', function (e) {
		$('label.custom-title').text('Focus Contact List');
	})

</script>