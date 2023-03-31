<?php 
	$this->load->module('site_labour'); 
	$user_id = 0;
	if(isset($_GET['user_id'])){
	    $user_id = $_GET['user_id'];
	    echo '<script>window.history.pushState("","","")</script>';
	}
	$curr_tab = "site_labour_details";
?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing,places&key=AIzaSyBeChq1YXq8NzMWhQ9NIT7_Cy5MnEHjJa4"></script>

    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/jquery-1.12.0.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/dataTables.fixedColumns.min.js"></script>

    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/jquery-2.1.3.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/tableHeadFixer.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/jquery.tablednd.0.7.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/tablefilter.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        
    <link href="<?php echo base_url(); ?>css/font-awesome.min.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

    <script src="<?php echo base_url(); ?>js/pathseg.js"></script>
 
    <link href="<?php echo base_url(); ?>css/c3.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>css/jquery.datetimepicker.css" rel="stylesheet" type="text/css">
    

    <script src="<?php echo base_url(); ?>js/c3/d3.js" charset="utf-8"></script>
    <script src="<?php echo base_url(); ?>js/c3/c3.js"></script>

    <link href="<?php echo base_url(); ?>css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
    <script src="<?php echo base_url(); ?>js/bootstrap-datetimepicker.min.js" ></script>

    <script src="<?php echo base_url(); ?>js/segment.js"></script>

    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/rcswitcher.css">
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-customselect.css">
    <script type="text/javascript" src="<?php echo base_url(); ?>js/rcswitcher.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-customselect.js"></script>

    
    <script src="<?php echo base_url(); ?>js/jquery.datetimepicker.full.js"></script>

    <script src="<?php echo base_url(); ?>js/vue.js"></script>
    <script src="<?php echo base_url(); ?>js/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>js/vue-select.js"></script>
    <script src="<?php echo base_url(); ?>js/axios.min.js"></script>
    



<!-- title bar -->
<input type="hidden" id ="base_url" value = "<?php echo base_url(); ?>">
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
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- title bar -->

<div class="container-fluid" id="app">
	<!-- Example row of columns -->
	<div class="row">				
		<?php $this->load->view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<div class="container-fluid">
				<div class="row" style = "border-bottom: 1px solid; border-color: #CCC">
		        	<div class="col-lg-4 col-md-12 hidden-md hidden-sm hidden-xs">
		           		<?php if(@$this->session->flashdata('project_deleted')): ?>
		           		<div class="m-15">
		               		<div class="border-less-box alert alert-danger fade in">
		               			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		               			<h4>Opps! No turning back now!</h4>
		               			<?php echo $this->session->flashdata('project_deleted');?>
		               		</div>
		           		</div>
		           		<?php endif; ?>
		        		
		           		<div class="box-head pad-10 clearfix">
		               		<label style = "font-size: 21px"><?php echo $screen; ?> List</label><span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the clients screen." data-original-title="Welcome">?</a>)</span>
		               		<p>This is where the site labours are listed.</p>
		               		<p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>               
		           		</div>
		           		
		        	</div>
		        </div>

		        <div class="col-sm-12 pad_5">

		        	<div class="col-sm-5 pad-5">
		        		<div class="col-sm-12 pad-5"><input type="text" class="form-control input-sm" placeholder="search..." v-model="searchProject"></div>
		        		<div class="col-sm-12 pad-5" style = "height: 500px; overflow: auto">
			        		<table id="sitelabourTable1" class="table table-striped table-bordered table-hover fancyTable" cellspacing="0" width="100%">
		            			<thead> 
		              				<tr> 
		                				<th>Client</th>
		                				<th>Project Name</th>
		                				<th>Project ID</th>
		                				<th>logo</th>
		                				<th></th>
		              				</tr>
		        				</thead> 
		        				<tbody>
		        					<tr v-for="project_list in sortProjectList" v-on:click="selectProject(project_list.project_number,project_list.project_name)">
		        						<td>{{ project_list.company_name }}</td>
		        						<td>{{ project_list.project_number }}</td>
		        						<td>{{ project_list.project_name }}</td>
		        						<td>
		        							<!-- <img :src="img_path(project_list.logo_path)" style = "width: 80px"> -->
		        							<img :src="img_path(project_list.brand_id)" style = "width: 80px">
		        						</td>
		        						<td>
		        							<button type = "button" class = "btn btn-xs btn-success" title = "View Project QR Code" v-on:click="view_qrcode(project_list.project_number)" style = "font-size: 10px">
		        								View <br>
		        								QRCode
		        							</button>
		        							<!-- <button type = "button" class = "btn btn-sm btn-success" title = "View Project Location" v-on:click="showMapModal(project_list.proj_latlong)"><i class="fa fa-map-marker"></i></button> -->
		        						</td>
		        					</tr>
		        				</tbody>
		        			</table>
	        			</div>
		        	</div>
		        	<div class="col-sm-7 pad-5">
		        		<div class="col-sm-12 pad-5">
		        			<div class="col-sm-10 pad-5">Project Name: {{ projectName }}</div>
		        			<div class="col-sm-2 pad-5"><button type = "button" class = "btn btn-success btn-sm pull-right" v-on:click="addNewEntry">Add New</button></div>
		        		</div>
		        		<div class="col-sm-12 pad-5">
		        			<div class="col-sm-8">
		        				<div class="col-sm-6"><input type="date" class="form-control input-sm" v-model = "loginDate"></div>
		        				<div class="col-sm-6">
		        					<select class = "form-control input-sm" v-model = "loginUser">
		        						<option value = 0>All</option>
		        						<option v-for="logged_site_staff in logged_site_staff" :value="logged_site_staff.user_id+' '+logged_site_staff.is_contractor">
		        							{{ logged_site_staff.site_staff }}
		        						</option>
		        					</select>
		        				</div>
		        			</div>
		        			<div class="col-sm-2"><button type = "button" class = "btn btn-success btn-sm" v-on:click="filter_site_staff_login">Filter</button></div>
		        			<div class="col-sm-2"><button type = "button" class = "btn btn-warning btn-sm pull-right" v-if="projectName !== ''" v-on:click = "print_pdf">Print PDF</button></div>
		        		</div>
		        		<div class="col-sm-12 pad-5" style = "height: 500px; overflow: auto">
				           	<table id="sitelabourTable1" class="table table-striped table-bordered table-hover fancyTable" cellspacing="0" width="100%">
		            			<thead> 
		              				<tr> 
		              					<th style = "width: 20px"></th>
		              					<th>Date</th>
		                				<th>Site Staff Name</th>
		                				<th>Company Name</th>
		                				<th>Start Time</th>
		                				<th>End Time</th>
		              				</tr>
		        				</thead> 
		        				<tbody>
		        					<tr v-for="site_staff_logs in site_staff_logs" v-if="project_id == site_staff_logs.project_id">
		        						<th style = "width: 20px"><button type = "button" class = "btn btn-xs btn-danger" v-on:click="delete_logs(site_staff_logs.site_staff_site_login_id)"><i class="fa fa-trash"></i></button></th>
		        						<td>{{ site_staff_logs.login_datetime | ausdate }}</td>
		        						<td>{{ site_staff_logs.site_staff_name }}</td>
		        						<td>{{ site_staff_logs.company_name }}</td>
		        						<td>{{ site_staff_logs.login_datetime | getTime }}</td>
		        						<td>{{ site_staff_logs.logout_datetime | getTime }}</td>
		        					</tr>
		        				</tbody>
		        			</table>
	        			</div>
			        </div>	
		        </div>
		        
			</div>
		</div>
	</div>

	<!-- Modal Start-->

	<div class="modal fade" id="frm_induction_login_qrcode" role="dialog">
	    <div class="modal-dialog">
	      <!-- Modal content-->
		    <div class="modal-content">
		        <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title">Induction Site Login Link QR Code for {{ project_id }}</h4>
		        </div>
		        <div class="modal-body row text-center">
		          	<img :src="fetchQRCode()" style = "width: 200px" />';
		        </div>
		        <div class="modal-footer">
		          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        </div>
		    </div>  
	    </div>
	</div>

	<div class="modal fade" id="modal_map" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog  modal-sm" style = "width:1000px">
	      <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	            <h4 class="modal-title" id="myModalLabel">Location</h4>
	        </div>
	        <div class="modal-body" style = "height: 600px">
	        	<div class = "col-sm-12" style = "height: 500px; overflow: auto" id = "map"></div>
	        </div>
	        <div class="modal-footer">
	          <button type = "button" class="btn pull-right" data-dismiss="modal">close</button>
	            
	        </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="mdl_new_entry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	            <h4 class="modal-title" id="myModalLabel">Add New Entry</h4>
	        </div>
	        <div class="modal-body row">
	        	<div class="col-sm-3 pad-5">Site Staff Type:</div>
	        	<div class="col-sm-9 pad-5">
	        		<select class = "form-control input-sm" v-model="site_staff_type" v-on:change = "selectCompType">
	        			<option value = 0>Focus Site Staff</option>
	        			<option value = 1>Contractor Site Staff</option>
	        			<option value = 2>Other Site Staff</option>
	        		</select>
	        	</div>

	        	<div class="col-sm-3 pad-5" v-if="site_staff_type > 0">Site Staff Company:</div>
	        	<div class="col-sm-9 pad-5" v-if="site_staff_type > 0">
	        		<select class = "form-control input-sm" v-model = "site_staff_comp" v-on:change = "selectComp">
	        			<option v-for="company_list in company_list" :value = "company_list.comp_id">{{ company_list.comp_name }}</option>
	        		</select>
	        	</div>

	        	<div class="col-sm-3 pad-5">Site Staff:</div>
	        	<div class="col-sm-9 pad-5">
	        		<select class = "form-control input-sm" v-model = "site_staff">
	        			<option v-for = "site_staff_list in site_staff_list" :value="site_staff_list.site_staff_id">{{ site_staff_list.ss_fname +" "+ site_staff_list.ss_sname}}</option>
	        		</select>
	        	</div>

	        	<div class="col-sm-3 pad-5">Project:</div>
	        	<div class="col-sm-9 pad-5">
	        		<select class = "form-control input-sm" v-model = "project_id">
	        			<option v-for = "project_list in project_list" :value = "project_list.project_id">{{ project_list.project_name }}</option>
	        		</select>
	        	</div>

	        	<div class="col-sm-3 pad-5">Log-in Date Time:</div>
	        	<div class="col-sm-5 pad-5"><input type="date" class="form-control input-sm" v-model = "login_date"></div>
	        	<div class="col-sm-4 pad-5"><input type="time" class="form-control input-sm" v-model = "login_time"></div>

	        	<div class="col-sm-3 pad-5">Log-out Date Time:</div>
	        	<div class="col-sm-5 pad-5"><input type="date" class="form-control input-sm" v-model = "logout_date"></div>
	        	<div class="col-sm-4 pad-5"><input type="time" class="form-control input-sm" v-model = "logout_time"></div>

	        </div>
	        <div class="modal-footer">
	          	<button type = "button" class="btn pull-right" data-dismiss="modal">close</button>
	        	<button type = "button" class="btn btn-success pull-right" data-dismiss="modal" v-on:click="addNewSiteStaff">Add</button>
	        </div>
	    </div>
	  </div>
	</div>



	
<!-- Modal End -->
</div>

<style>
	.select2-container.chosen{
		padding:0;
	}
</style>



<script>
	jQuery(document).ready(function () {
        'use strict';

        jQuery('#start_date, #end_date, #edit_start_date, #edit_end_date, #travel_start_date, #travel_end_date, #edit_travel_start_date, #edit_travel_end_date').datetimepicker({format: 'd/m/Y H:i'});
    });
	
	var apiKey = 'AIzaSyBeChq1YXq8NzMWhQ9NIT7_Cy5MnEHjJa4';
	var polylines = [];
	var baseurl = $("#base_url").val();
	var current_total_normal_hrs = 0;
	var current_total_timehalf = 0;
	var current_total_doubletime = 0;
	var current_total_doubletimehalf = 0;
	var total_meal_qty = 0;
	var current_total_travel = 0;
	var current_user_id = 0;
	var current_day_name = "";

	var calculated_normal_hrs = 0;
	var calculated_timehalf_hrs = 0;
	var calculated_doubletime_hrs = 0;
	var calculated_doubletimehalf_hrs = 0;
	var calculated_total_travel = 0;
	var total_travel_pay = 0;

	var day_total_duration = 0;
	var meal_qty = 0;
	var has_meal_break = 0;


	var current_date = "";
	var change_user_id = "";
	var row_number = 1;

	var SLArray = [];
	var dayDuration = 0;

	var work_travel_time = 0;
	var current_work_travel_time = 0;

	var app = new Vue({
	  	el: '#app',
	  	data: {
	  		site_staff_logs: [],
	  		project_list: [],
	  		searchProject:"",
	  		project_id: 0,
	  		projectName: "",
	  		logged_site_staff: [],

	  		loginDate: '',
			loginUser: 0,

			site_staff_list: [],

			company_list: [],
			site_staff_type: '',
			site_staff_comp: 0,
			site_staff: 0,
			login_date: '',
			login_time: '',
			logout_date: '',
			logout_time: '',
	  	},

	  	mounted: function(){
	  		this.fetchSiteStaffLogs();
	  		this.fetchProjects();
	  		
	  	},

	  	filters: {
	  		getDayname: function(date){
        		return moment(date).format('ddd');
        	},
        	ausdate: function(date) {
        		return moment(date).format('DD/MM/YYYY');
        	},
        	getTime: function(date){
        		return moment(date).format('hh:mm a');
        	}
	  	},

	   	methods: {
	   		fetchSiteStaffLogs: function(){
	   			axios.post(baseurl+"induction_health_safety/fetch_site_staff_login", 
	          	{
	          		'project_id': this.project_id,
	          	}).then(response => {
	            	this.site_staff_logs = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
	   		},

	   		fetchProjects: function(){
	   			axios.post(baseurl+"induction_health_safety/fetch_induction_projects", 
	          	{
	          	}).then(response => {
	            	this.project_list = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
	   		},

	   		fetchLoggedSiteStaff: function(){
	   			axios.post(baseurl+"induction_health_safety/fetch_logged_sitestaff", 
	          	{
	          		'project_id': this.project_id
	          	}).then(response => {
	            	this.logged_site_staff = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
	   		},

	   		fetchSiteStaffComp: function(){
	   			axios.post(baseurl+"induction_health_safety/fetch_site_staff_company", 
	          	{
	          		'project_id': this.project_id,
	          		'cont_type': this.site_staff_type
	          	}).then(response => {
	            	this.company_list = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
	   		},

	   		fetchSiteStaffList: function(){
	   			axios.post(baseurl+"induction_health_safety/fetch_site_staff", 
	          	{
	          		'cont_type': this.site_staff_type,
	          		'company_id': this.site_staff_comp
	          	}).then(response => {
	            	this.site_staff_list = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
	   			
	   		},

	   		img_path: function(brand_id){
	   			var img_path = '<?php echo base_url() ?>uploads/brand_logo/'+brand_id+'.jpg';
	   			return img_path;
	   		},

	   		selectProject: function(project_id,projectName){
	   			this.project_id = project_id;
	   			this.projectName = projectName;
	   			this.fetchSiteStaffLogs();
	   			this.fetchLoggedSiteStaff();
	   		},

	   		showMapModal: function(gpsLoc){
		    	var gps_arr = gpsLoc.split(',');
		    	var lat = parseFloat(gps_arr[0]);
		    	var lng = parseFloat(gps_arr[1]);

		    	var uluru = {lat: lat, lng: lng};
			    var map = new google.maps.Map(document.getElementById('map'), {
			        zoom: 15,
			        center: uluru
			    });
			    var marker = new google.maps.Marker({
			        position: uluru,
			        map: map
			    });
		    	$("#modal_map").modal('show');
		    },

		    filter_site_staff_login: function(){
		    	axios.post(baseurl+"induction_health_safety/filter_logged_sitestaff", 
	          	{
	          		'project_id': this.project_id,
	          		'user_id': this.loginUser,
					'login_date': this.loginDate
	          	}).then(response => {
	            	this.site_staff_logs = response.data;                  
	          	}).catch(error => {
	            	console.log(error.response)
	          	}); 
		    },

		    addNewEntry: function(){
		    	$("#mdl_new_entry").modal('show');
		    },

			selectCompType: function(){
				if(this.site_staff_type == 0){
					this.fetchSiteStaffList();
				}
				this.fetchSiteStaffComp();
			},

			selectComp: function(){
				this.fetchSiteStaffList();
			},

			addNewSiteStaff: function(){
				if(this.project_id == 0 || this.site_staff_type == '' || this.site_staff == 0 || this.login_date == '' || this.login_time == '' || this.logout_date == '' || this.logout_time == ''){
					alert("All Fields Required");
				}else{
					var login_datetime = this.login_date+" "+this.login_time;
					var logout_datetime = this.logout_date+" "+this.logout_time;
					var r = confirm("Are you sure all information are correct?");
					if (r == true) {
					  	axios.post(baseurl+"induction_health_safety/insert_site_staff_site_login", 
			          	{
			          		'project_id': this.project_id,
			          		'user_id': this.site_staff,
							'login_datetime': login_datetime,
							'logout_datetime': logout_datetime,
							'is_contractor': this.site_staff_type
			          	}).then(response => {
			            	this.fetchSiteStaffLogs();                 
			          	}).catch(error => {
			            	console.log(error.response)
			          	}); 
					}
				}
				
			},

			delete_logs: function(site_staff_site_login_id){
				var r = confirm("Are you sure you want to delete selected log?");
				if (r == true) {
				  	axios.post(baseurl+"induction_health_safety/delete_site_staff_site_login", 
		          	{
		          		'site_staff_site_login_id': site_staff_site_login_id
		          	}).then(response => {
		            	this.fetchSiteStaffLogs();                 
		          	}).catch(error => {
		            	console.log(error.response)
		          	}); 
				}
			},

		    print_pdf: function(){
		    	var project_id = this.project_id
		    	$.post(baseurl+"induction_health_safety/generate_site_diary_qrcode",
	            {
	              project_id: this.project_id
	            },
	            function(result){
	            	window.open(baseurl+"induction_health_safety/induction_site_diary_pdf?project_id="+project_id);
	            });
		    	
		    },

		    view_qrcode: function(project_id){
	            	$("#frm_induction_login_qrcode").modal("show");
	            
	        },
	        
	        fetchQRCode: function(){
	        	return "<?php echo base_url() ?>induction_health_safety/induction_loginqrcode?project_id="+this.project_id;
	        },

	  	},

	  	computed:{
	  		sortProjectList: function(){
	          //this.project_list.sort((a,b) => a.project_id > b.project_id ? 1 : -1);

	          return this.project_list.filter((project) => {
	            return project.company_name.toLowerCase().match(this.searchProject.toLowerCase()) || project.project_name.toLowerCase().match(this.searchProject.toLowerCase()) || project.project_id.toLowerCase().match(this.searchProject.toLowerCase());
	          });
	        },
	  	},

	 	
	})
</script>