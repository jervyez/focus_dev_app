<?php use App\Modules\Dashboard\Controllers\Dashboard; ?>
<?php $this->dashboard = new Dashboard(); ?>

<?php use App\Modules\Dashboard\Models\Dashboard_m; ?>
<?php $this->dashboard_m = new Dashboard_m(); ?>

<?php use App\Modules\Dashboard\Controllers\Estimators; ?>
<?php $this->estimators = new Estimators(); ?>

<?php use App\Modules\Users\Models\Users_m; ?>
<?php $this->user_model = new Users_m(); ?>

<?php $months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"); ?>
<!-- title bar -->

<?php
	
	$current_year = date("Y");
	$current_month = date("m");
	$current_day = date("d");

	$last_year = $current_year-1;
	$start_last_year = "01/01/$last_year";
	$last_year_same_date = "$current_day/$current_month/$last_year";

	$start_current_year = "01/01/$current_year";
	$current_date = date("d/m/Y");

$set_colors = array('#D68244','#e08d73', '#57a59c', '#57a59c', '#B13873', '#3f51b5', '#ff22e4', '#D6D544' );
$estimator_colors = array();
$counter = 0;

$estimator = $this->dashboard_m->fetch_project_estimators();
$estimator_list = $estimator->getResult();

foreach ($estimator_list as $est ) {
	if($est->user_first_name != ''){
		$estimator_name = strtolower(str_replace(' ','_', $est->user_first_name) ); 
		$estimator_colors[$estimator_name] = $set_colors[$counter];
		$counter++;
	}
}


$estimator_colors['Danikka'] = $set_colors[3];
$estimator_colors['Ernan'] = $set_colors[4];
$estimator_colors['Alexandria'] = $set_colors[6];
?>

<style type="text/css">
	<?php 
		foreach ($estimator_colors as $key => $value) {
			echo '.'.strtolower($key).'{ background-color: '.$value.' !important; }';
		}
		$site_url = site_url();
	?>
	.red_deadline{       background-image: url( <?php echo $site_url; ?>img/grid-end.png);   background-repeat:no-repeat; }
</style>


<?php

/*
	if(isset($pm_setter_focus_id) && $pm_setter_focus_id != ''){
		$user_focus_company_id = $pm_setter_focus_id;
	}else{
		$user_focus_company_id = $this->session->get("user_focus_company_id");
	}	
*/
	if(isset($assign_id) && $assign_id != '' && $assign_id > 0){
		$user_id = $assign_id;
	}else{
		$user_id = $this->session->get('user_id');
	}

	$pm_type = $this->dashboard->pm_type($user_id); 

	$fetch_user = $this->user_model->fetch_user($user_id);
	$getResultArray = $fetch_user->getResultArray();
	$user_details = array_shift($getResultArray);

	if($pm_type == 1){ // for director/pm
		$direct_company = explode(',',$user_details['direct_company'] );
	}

	if($pm_type == 2){ // for pm only
		$direct_company = explode(',',$user_details['user_focus_company_id'] );
	}


?>


<?php
	//$focus_company_location = $user_details['user_focus_company_id'];



	if(isset($pm_setter_focus_id) && $pm_setter_focus_id != ''){
		$user_focus_company_id = $pm_setter_focus_id;
		$focus_company_location =  $user_focus_company_id;
	}else{
		$user_focus_company_id = $this->session->get("user_focus_company_id");
		$focus_company_location =  $user_focus_company_id;
	}	

	$project_manager = $this->dashboard_m->fetch_pms_year(date("Y"),0,' AND `users`.`user_focus_company_id` = '.$focus_company_location.' ');
	$project_manager_list = $project_manager->getResult();


?>


<?php 


if($pm_setter != '' && isset($pm_setter)){
	$pm_name = $pm_setter;

	  
echo '<script type="text/javascript">$("span#simulation_pm_name").text("'.$pm_name.'");</script>';
	 
}else{
	$pm_name = $this->session->get('user_first_name').' '.$this->session->get('user_last_name'); 
}

 
?>



 <!-- maps api js -->

<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyDs1g6kHxbVrkQe7e_CmR6MsfV_3LmLSlc"></script>

<script type="text/javascript">
	var data = { "locations": <?php echo $this->dashboard->focus_get_map_locations_pm($assign_id); ?>};
	var emp_data = { "locations": <?php echo $this->dashboard->emp_get_locations_points(); ?>};
</script>

<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/markerclusterer_packed.js"></script>

<!--[if IE]><script type="text/javascript" src="<?php echo site_url(); ?>js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo site_url(); ?>js/jquery.knob.min.js"></script>
<script type="text/javascript"> $(function() {  $(".knob").knob(); });</script>
<script type="text/javascript">$('knob').trigger('configure', {width:100}); </script>

<!-- maps api js -->

<style type="text/css">body{background: #ECF0F5 !important;}</style>

<!-- title bar -->

<script type="text/javascript">
	var site_url = "<?php echo site_url(); ?>";
	function pre_load_module(target,clsFnctn,timeDelay){
		$(window).load(function() {
			setTimeout(function() {
				$.ajax({
					'url' : site_url+clsFnctn,
					'type' : 'GET',
					'success' : function(data){

						$(target).hide().html(data).fadeIn();

						/*$(target).html(data)*/
						$('.tooltip-enabled').tooltip(); 
						$(".knob").knob();
					}
				});
			}, timeDelay);
		});
	}
</script>

<div class="container-fluid adv"  style="background: #ECF0F5;">


	<!-- Example row of columns -->
	<div class="row dash">				
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11 pad-0-imp no-m-imp">
			<div class="">

				<div class="clearfix pad-10">
					<div class="widget_area row pad-0-imp no-m-imp">

							<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
								<div class="widget wid-type-e small-widget">
									<div class="box-area clearfix">
										<div class="widg-icon-inside col-xs-3"><i class="fa fa-list-alt text-center fa-3x"></i></div>
										<div class="widg-content fill col-xs-9 clearfix">
											<div class="pad-5">
												<div class=" " id=""><p>Invoiced <span class="pull-right"><?php echo date('Y'); ?></span> <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much is been invoiced from begining of the year to date."></i></span> </p></div>
												<hr class="" style="margin: 5px 0px 0px;">
												<script type="text/javascript"> pre_load_module('#invoiced_pm_area','dashboard/invoiced_pm/<?php echo $assign_id; ?>',7000); </script>
												<div class="pad-top-5" id="invoiced_pm_area" >
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p><?php //$this->dashboard->invoiced_pm($assign_id); ?>
												</div>
											</div>							
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
								<div class="widget wid-type-a small-widget">
									<div class="box-area clearfix">
										<div class="widg-icon-inside col-xs-3"><i class="fa fa-list  text-center fa-3x"></i></div>

										<div class="widg-content fill col-xs-9 clearfix">
											<div class="pad-5">
												<div class=" " id=""><p>Uninvoiced <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much is still un-invoiced from the begining to date."></i></span></p></div>
												<hr class="" style="margin: 5px 0px 0px;">
												<script type="text/javascript"> pre_load_module('#uninvoiced_widget_pm_area','dashboard/uninvoiced_widget_pm/<?php echo $assign_id; ?>',7500); </script>
												<div class="pad-top-5" id="uninvoiced_widget_pm_area" >
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //$this->dashboard->uninvoiced_widget_pm($assign_id); ?>
												</div>
											</div>							
										</div>
									</div>
								</div>
							</div>  

							<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
								<div class="widget wid-type-f small-widget">
									<div class="box-area clearfix">
										<div class="widg-icon-inside col-xs-3"><i class="fa fa-server text-center fa-3x"></i></div>

										<div class="widg-content fill col-xs-9 clearfix">
											<div class="pad-5">
												<div class=" " id=""><p>Outstanding <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much outstainding invoices needs payment from the begining to date."></i></span></p></div>
												<hr class="" style="margin: 5px 0px 0px;">
												<script type="text/javascript"> pre_load_module('#outstanding_payments_widget_pm_area','dashboard/outstanding_payments_widget_pm/<?php echo $assign_id; ?>',8000); </script>
												<div class="pad-top-5" id="outstanding_payments_widget_pm_area" >
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //$this->dashboard->outstanding_payments_widget_pm($assign_id); ?>
												</div>
											</div>							
										</div>
									</div>
								</div>
							</div>


							<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
								<div class="widget wid-type-b small-widget">
									<div class="box-area clearfix">
										<div class="widg-icon-inside col-xs-3"><i class="fa fa-tasks  text-center fa-3x"></i></div>


										<div class="widg-content fill col-xs-9 clearfix">
											<div class="pad-5">
												<div class=" " id=""><p>WIP <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much in total of current WIP projects."></i></span></p></div>
												<hr class="" style="margin: 5px 0px 0px;">
												<script type="text/javascript"> pre_load_module('#wip_widget_pm_area','dashboard/wip_widget_pm/<?php echo $assign_id; ?>',8500); </script>
												<div class="pad-top-5" id="wip_widget_pm_area" >
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //$this->dashboard->wip_widget_pm($assign_id); ?>
												</div>
											</div>							
										</div>
									</div>
								</div>
							</div>

							<div class=" col-xs-12 box-widget pad-10">
								<div class="progress no-m progress-termometer   tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="Tells the progress made for reaching the target sales. WIP todate plus current invoiced progress claims." title="" >
									<div class="progress-bar progress-bar-danger active progress-bar-striped full_p tooltip-enabled tooltip-pb" title="" data-original-title="Overall Forecast Progress" style="background-color: rgb(251, 25, 38); border-radius: 0px 10px 10px 0px;"></div> 
								</div>
							</div>	
							<script type="text/javascript">
								$(window).load(function() {
									setTimeout(function() {
										$.ajax({
											'url' : site_url+'dashboard/pm_sales_widget_pm/<?php echo $assign_id; ?>/1',
											'type' : 'GET',
											'success' : function(result){
												var raw_overall = result;
												var overall_arr =  raw_overall.split('_');
												var overall_progress = parseInt(overall_arr[0]);
												var status_forecast = overall_arr[1];
												$('.full_p').css('width',overall_progress+'%');
												$('.full_p').html(overall_progress+'%');
												$('.full_p').prop('title','$'+status_forecast+' - Overall Progress');
												$('.tooltip-pb').tooltip();				 
											}
										});
									}, 9000);
								});
							</script>



						<div class=" col-xs-12 box-widget pad-10">
							<?php //$pm_id_user =  $user_id = $this->session->get('user_id'); ?>

 

							<?php if($pm_type == 1): // for director/pm ?>

								<?php if($user_focus_company_id == 5): ?>
									<script type="text/javascript">
										pre_load_module('#progressBar_area','dashboard/progressBar/5/1',9500);
									</script>
								<?php endif; ?>


								<?php if($user_focus_company_id == 6): ?>
									<script type="text/javascript">
										pre_load_module('#progressBar_area','dashboard/progressBar/6/1',9500);
									</script>
								<?php endif; ?>

							<?php endif; ?>

							<?php if($pm_type == 2): // pm ?>
								<script type="text/javascript"> pre_load_module('#progressBar_area','dashboard/progressBar/<?php echo $user_id; ?>',9500); </script>
							<?php endif; ?>












							<div class="progress no-m progress-termometer" id="progressBar_area">
								<?php //echo $this->dashboard->progressBar($assign_id); ?>
							</div>
						</div>

						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-7 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head fill pad-5">
									<strong>Sales Forecast - <?php echo date('Y'); ?></strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells what is being forecasted per month along what is WIP and Invoiced."></i></span>
									<select class="pull-right input-control input-sm chart_data_selection" style="background: #AAAAAA; padding: 0;margin: -8px 0 0 0;width: 175px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">



										<?php if($pm_type == 1): ?>
											<?php
												$custom_q_uses_lst = " AND `users`.`user_focus_company_id` = '$focus_company_location' AND (`users`.`user_role_id` = '20' OR `users`.`user_role_id` = '3') ";
												$user_list_selection_q = $this->user_model->list_user_short($custom_q_uses_lst);
												$user_list_select = $user_list_selection_q->getResult();
											?>
											<?php foreach ($user_list_select  as $key => $value): ?> 


												<option value="<?php echo $value->user_first_name.' '.$value->user_last_name; ?>" ><?php echo $value->user_first_name.' '.$value->user_last_name; ?></option>



											<?php endforeach; ?>

										<?php else: ?>
										<?php  echo '<option value="'.$pm_name.'">'.$pm_name.'</option>'; ?>
										<?php endif; ?>


 

										<?php foreach ($focus_company as $key => $value): ?>

											<?php 
												if( in_array($value->company_id, $direct_company) ){
													echo '<option value="F'.$value->shortname.'">Focus '.$value->shortname.'</option>';
												}
											?>
										<?php endforeach; ?>

									</select>
									<script type="text/javascript"> $('select.chart_data_selection').val('<?php echo $pm_name; ?>').trigger('change');</script>
								</div>

								<?php //var_dump($project_manager); ?>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix">
										<div class="loading_chart" style="height: 457px; text-align: center; padding: 100px 53px; color: #ccc;"><i class="fa fa-spin fa-refresh fa-4x"></i></div>
										<div class id="job_book_area">
										<div id="chart"></div></div> 	
									</div>
									 
								</div>

							</div>
						</div>



						<!-- ************************ -->





						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-2 box-widget pad-10">
							<div class="widget   widg-head-styled" style="height: 501px; overflow: visible;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head  fill pad-5" style="background-color:#964dd7  !important; color:#FFF !important;  border-top: 3px solid #6e27ad; height: 36px; overflow: hidden; line-height: 26px;"><strong>Average Final Invoice Days</strong> <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how many days (average) a project been fully invoiced."></i></span> </div>
								<div class="box-area clearfix">
									<div class="widg-content fill clearfix" style="height: 100%; position: relative;">
										<div class="" id="">



											<script type="text/javascript"> pre_load_module('#average_date_invoice_pm_area','dashboard/average_date_invoice_pm/<?php echo $assign_id; ?>',10000); </script>
											<div class="clearfix center knob_box small-widget" style=" margin: 90px 0px 0px;" id="average_date_invoice_pm_area">
												<p><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php  //echo $this->dashboard->average_date_invoice(); ?>
											</div>	
											<style type="text/css">.knob_box canvas{width: 100% !important;}</style>									 
										</div>							
									</div>
								</div>
							</div>
						</div>







						<!-- ************************ -->
					

						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled" style="height: 501px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><strong>Project Manager Sales</strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much is the forecasted amounth per Project Manager along their Invoiced and WIP totals for the year."></i></span> <span class="badges pull-right"> <span class="pull-right"><?php echo date('Y'); ?></span> </span></div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">
										<script type="text/javascript"> pre_load_module('#pm_sales_widget_pm_area','dashboard/pm_sales_widget_pm/<?php echo $assign_id; ?>',10500); </script>
										<div class="" id="pm_sales_widget_pm_area">
											<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
										</div>							
									</div>
								</div>
							</div>
						</div>



							<!-- ******** Labour Schedule Chart **************** -->
						
						<div class="clearfix"></div>


 

						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head pad-5 fill">



									<?php  $labour_sched_totals = $this->dashboard->labour_sched_dates_totals(); ?>

									<?php  $sched_totals = explode(',', $labour_sched_totals); ?>


									<strong>Site Labour Chart</strong>

									<span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the total labour hours spent on a job per week."></i></span>

<span class="toggle_lbrscd_dsp Overall_lbr_scdl">
&nbsp; &nbsp;  &nbsp; &nbsp; <strong id="" class="">WIP: <?php echo  number_format($sched_totals[4],2); ?>  
&nbsp; &nbsp;  &nbsp; &nbsp;  Quote: <?php echo  number_format($sched_totals[5],2); ?></strong></span>


<span class="toggle_lbrscd_dsp FocusShopfit_lbr_scdl" style="display:none;">
&nbsp; &nbsp;  &nbsp; &nbsp; <strong id="" class="">WIP: <?php echo  number_format($sched_totals[0],2); ?>  
&nbsp; &nbsp;  &nbsp; &nbsp;  Quote: <?php echo  number_format($sched_totals[1],2); ?></strong></span>


<span class="toggle_lbrscd_dsp FocusShopfitNSW_lbr_scdl" style="display:none;">
&nbsp; &nbsp;  &nbsp; &nbsp; <strong id="" class="">WIP: <?php echo  number_format($sched_totals[2],2); ?>  
&nbsp; &nbsp;  &nbsp; &nbsp;  Quote: <?php echo  number_format($sched_totals[3],2); ?></strong></span>


 


<select class="pull-right input-control input-sm chart_labour_schedule hide" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 210px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
<option value="Overall ">Overall Focus</option>
	<?php foreach ($focus_company as $key => $value): ?>


<?php $com_name_lbsl = trim($value->company_name); ?>  
<?php $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl); ?>


	<?php 

			if($value->company_id == $user_focus_company_id){
				$user_company_name = $com_name_lbsl;

			}



		 ?>



<?php if($value->company_id != 3197): ?>

		<option value="<?php echo $com_name_lbsl; ?>"><?php echo $com_name_lbsl; ?></option>

<?php endif; ?>


	<?php endforeach; ?>
</select>

								 



 
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix" id=" "  >	

<div id="chart_labourSched" style="display:block; height:460px;" ></div>	



	<?php 
		$today = date('m/d/Y');
		$this_year = date('Y');
		$curr_week_number = strftime("%U", strtotime($today));
		$weekStart = $curr_week_number-2;
		$arr_weeksCount = array_fill($weekStart,24,0);
		$weeks_list = array_keys($arr_weeksCount);
		$days_list = implode(',', $weeks_list);
	?>


<?php   ?>

<p id="" class="monthLabels">
<?php 

$loop_counter = 0;

//var_dump($weeks_list);

$month_label_list = array();


foreach ($weeks_list as $key => $value) {

	$month_name = strftime('%b',strtotime($this_year."W".$value));

	array_push($month_label_list, $month_name);


 

}


$month_counts = array_count_values($month_label_list);

foreach ($month_counts as $key => $value) {
	echo   '<span style="width:'.($value*4.16).'%; ">'.$key.'</span>';
}


?>


</p>									 
 
<div id="" class="">
	



 <script>

 var arrWeekName = [<?php echo $days_list; ?>];
 

var chart_labourSched = c3.generate({
      size: {
        height: 350
      },data: {
        x : 'x',
        columns: [
          ['x', <?php echo $days_list; ?> ], // months labels



// 	Overall Last Year Sales 



// PM Project Manager Sales Last Year
 
/*

['Stuart Hubrich Last Year',3,5,8,12,3,2,1,6,5,12,10,3,8,10,8,8,8,8,8,9,9,9,9,9,9,9,10,10,6,4,4],
['Stuart Hubrich WIP',3,5,8,12,3,2,1,6,5,12,10,3,8,3,2,1,6,5,12,10,3,8,10,8,8,8],
['Stuart Hubrich Quote',0,8,8,8,8,8,12,10,3,8,0,2,3,2,1,0,0,0,0,]

*/

          <?php echo $this->dashboard->labour_sched_dates(); ?>

 
 
        ],
        selection: {enabled: true},
        type: 'bar',
      colors: { 
   		'Overall Last Year': '#AAAAAA',
      	'Overall WIP': '#FA9B47',
      	'Overall Quote': '#555',  




<?php foreach ($focus_company as $key => $value): ?>   


<?php $com_name_lbsl = trim($value->company_name); ?>  
<?php $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl); ?>



      	'<?php echo $com_name_lbsl; ?>WIP': '#FA9B47',
      	'<?php echo $com_name_lbsl; ?>Quote': '#555',  
      	'<?php echo $com_name_lbsl; ?>Last Year': '#AAAAAA',  


 

<?php endforeach; ?>
      
      
 
        }, 
      	
        groups: [  
 
 


['Overall Quote','Overall WIP'],




<?php foreach ($focus_company as $key => $value): ?>      


<?php $com_name_lbsl = trim($value->company_name); ?>  
<?php $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl); ?>


	['<?php echo $com_name_lbsl; ?>Quote','<?php echo $com_name_lbsl; ?>WIP'],


<?php endforeach; ?>





],

        order: null,
      },


        grid: {
      x: {
        lines: [
          {value: 2, text: 'Current Week', class: 'gridBarCurr'}
        ]
      }
    },




    tooltip: {
        grouped: true // false // Default true
    },
             bindto: "#chart_labourSched",
bar:{ width:{ ratio: 0.5 }},
point:{ select:{ r: 6 }},
onrendered: function () { $('.loading_chart').remove(); },
//zoom: {enabled: true, rescale: true,extent: [1, 7]},
legend: { show: false },


axis: {x: {type: 'category', tick: {rotate: 0,multiline: false}, height: 0} },
tooltip: {
        format: {
          	title: function (x) { return 'Week: ' + arrWeekName[x]; },
            value: function (value, ratio, id) {
               // var format = id === 'data1' ? d3.format(',') : d3.format('$');
                var format = d3.format(',');
                
             	var mod_value = Math.round(value * 100) / 100;
                return  format(mod_value)+' hrs';
            }
        } 

    }
    });

//chart.select();
//chart.hide();
chart_labourSched.hide();
//setTimeout(function () {
	//chart_labourSched.show(['Overall Last Year','Overall WIP','Overall Quote']);
//}, 1000);	


$('.chart_labour_schedule').change(function(){
	var view_chart = $(this).val();
chart_labourSched.hide();
/*
setTimeout(function () {
	chart_labourSched.show([view_chart+'Last Year',view_chart+'WIP',view_chart+'Quote']);
}, 500);	
*/

var select_val = view_chart.replaceAll(/\s/g,'');


$('.toggle_lbrscd_dsp').hide();

 
setTimeout(function () {
	$('.'+select_val+'_lbr_scdl').show();
}, 250);	


 

/*
Overall 

Focus Shopfit 

Focus Shopfit NSW */
});

//chart.select();
	//chart.show(['Current','Average','Last Year']);




	setTimeout(function () {
		$('select.chart_labour_schedule').val('<?php echo $user_company_name; ?>').change();
		var view_chart = '<?php echo $user_company_name; ?>';
		chart_labourSched.show([view_chart+'Last Year',view_chart+'WIP',view_chart+'Quote']);
	}, 500);	




setTimeout(function () {

$('#chart_labourSched .c3-zoom-rect').after('<svg width="24%" height="320" x="9%" y="-5"><rect class="" width="10%" height="320" style="fill: rgb(252 231 141);stroke-width:3;opacity: .5;"></rect></svg>');



}, 2000);


</script>

<style type="text/css">
	


	.gridBarCurr line {
		stroke: #000;
	}
	.gridBarCurr text {
		fill: #000;
	}


	p.monthLabels {
  padding-left: 40px !important;
    display: block;
    margin-top: -10px !important;
    font-size: 12px;
    width: 100%;
} 

.monthLabels span {
  
    text-align: center;
    display: inline-block;
}


</style>

</div>


  



									</div> 
								</div>

							</div>
						</div>



							<!-- ******** Labour Schedule Chart **************** -->











<!-- Induction, Health Safety -->

						<div class="clearfix"></div>



						<div class="col-xs-12 col-lg-6 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled" style="overflow: visible;" >
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head fill pad-5 clearfix" >


									<div class="col-sm-12"><strong>Induction, Health &amp; Safety</strong> </div>


									



								<span class="pointer hide"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="right" data-original-title="Tells the number of projects undertaken year to date for maintenance and that compared to the same time the previous year."></i></span>

								 </div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">

										






<div class=" " id="" style="display: block; height: 86px; overflow: hidden;">


	



	<div id="growContainer">
		<div class="grow prime" style="background-color:#5393c8;"><strong style="background-color: #416987;"><i class="fa fa-calendar-times-o"></i></strong> <?php echo $this->dashboard->ihs('get_recent_lost_time_days'); ?> <span style="opacity: 1 !important; visibility: visible;">Days Since Lost Time Injury</span></div>
		<div class="grow" style="background-color:#F36460;"><strong style="background-color: #D9534F;"><i class="fa fa-exclamation-triangle"></i></strong> <?php echo $this->dashboard->ihs('get_incident_count'); ?> <span>Incidents Reported</span></div>
		<div class="grow" style="background-color:#A58377;"><strong style="background-color: #795548;"><i class="fa fa-user-plus"></i></strong> <?php echo $this->dashboard->ihs('get_count_inducted'); ?> <span>Individuals Inducted</span></div>
		<div class="grow" style="background-color:#8AD38A; border-right:0 !important"><strong style="background-color: #4DAB4D;"><i class="fa fa-check-square-o"></i></strong> <?php echo $this->dashboard->ihs('get_company_inducted_count'); ?> <span>Companies Inducted</span></div>
	</div>


	</div>  


							<style type="text/css">

								.grow strong{
									height: 100%;
									display: inline-block;
							}

.grow .fa{
	display: inline-block;
    width: 40%;
    text-align: center; 
    height: 100%;
    color: #fff;
    padding: 22px 17px;
    font-size: 2em;
}

#growContainer{
	display: table;
	width:100%;
	height:100%;
	}
	.grow{
		display: table-cell;
		height:100%;
		width:15%;
		-webkit-transition:width 100ms;
		-moz-transition:width 100ms;
		transition:width 100ms;
		 
		color: #fff;

		font-size: 18px; 
		font-weight: bold;
border-right: 4px solid #FFF;
	}

.grow span{ display:inline-block; 

    -webkit-transition: opacity  300ms ease-in;
    -moz-transition: opacity  300ms ease-in;
    transition: opacity 300ms ease-in;
    opacity: 0  !important;
    visibility: hidden;



	}
#growContainer:hover .grow{
	width:15% !important;
}


#growContainer .grow.prime{
	width: 55%;
}

#growContainer:hover .grow:hover {
	width:55% !important;
}



#growContainer:hover .grow:hover span{
  display:inline-block;
    opacity: 1  !important;
    visibility: visible;
}

</style>

<script type="text/javascript">
	
	if (    $(window).width() >= 1180 && $(window).width() <= 1680    ){

		$('.grow span').css('font-size','14px');
		$('.grow span').css('font-weight','normal');
	}


</script>



					
									</div>
								</div>
							</div>
						</div>
 


<!-- Induction, Health Safety -->


						 




<!-- Availability List -->



						<div class="col-xs-12 col-lg-6  box-widget pad-10">
							<div class="widget wid-type-c widg-head-styled" style="overflow: visible;" >
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head fill pad-5 clearfix" >


									<div class="col-sm-4"><strong>Users Availability</strong> </div>



									<div class="col-sm-8 text-right" ><span style="text-shadow: none !important; color: white; background: darkorange;" data-placement="bottom" class="tooltip-enabled badge" title="" data-original-title="(Could also read AWAY) - Available via mobile &amp; email only"><i class="fa fa-arrow-circle-left"></i> Out of Office   </span>
									<span style="text-shadow: none !important; color: white; background: red;" data-placement="bottom" class="tooltip-enabled badge" title="" data-original-title="This means that you are not contactable right now, send a message"><i class="fa fa-exclamation-circle"></i> Busy </span>
									<span style="text-shadow: none !important; color: white; background: gray;" data-placement="bottom" class="tooltip-enabled badge" title="" data-original-title="Unavailable, forward if URGENT, will respond to message on return"><i class="fa fa-minus-circle"></i> Leave   </span>
									<span style="text-shadow: none !important; color: white; background: purple;" data-placement="bottom" class="tooltip-enabled badge" title="" data-original-title="Unavailable, take a message, forward if URGENT"><i class="fa fa-times-circle"></i> Sick  </span></div>
									



								<span class="pointer hide"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="right" data-original-title="Tells the number of projects undertaken year to date for maintenance and that compared to the same time the previous year."></i></span> </div>
								<div class="box-area clearfix"  style="height: 94px;">
									<div class="widg-content clearfix">

										<div class="" style="position:relative;">
										<?php echo $this->dashboard->users_availability(); ?>


										<style type="text/css">

											.av_unit{
												overflow: hidden;
												width: 50px;
												height: 50px;
												border-radius: 50px;
												border: 2px solid;
											}

											.avbty p{ text-align: center; }

											.av_out_of_office{ border-color: darkorange; }
											.av_sick{ border-color: purple; }
											.av_leave{ border-color: gray; }
											.av_busy{ border-color: red; }

										</style>

 
										</div>							
									</div>
								</div>
							</div>
						</div>

						<div class="clearfix"></div>


<!-- Availability List -->



						<!-- ************************ -->

						<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-b small-widget">
								<div class="box-area clearfix">
									<div class="widg-icon-inside col-xs-3" ><div id="" class=""><i class="fa fa-cube  text-center fa-3x"></i></div></div>
									<div class="widg-content fill col-xs-9 clearfix">
										<div class="pad-3">
											<div class="pad-left-5 pad-top-3" id=""><p>Invoiced - WIP Count <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how many current wip count."></i></span>  <span class="pull-right"><?php echo date('Y'); ?></span></p></div>
											<hr class="" style="margin: 5px 0px 2px;">
											<script type="text/javascript"> pre_load_module('#focus_projects_count_widget_pm_area','dashboard/focus_projects_count_widget_pm/<?php echo $assign_id; ?>',11000); </script>
											<div class="pad-top-5" id="focus_projects_count_widget_pm_area" >
												<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php // $this->dashboard->focus_projects_count_widget_pm($assign_id); ?>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-0 small-widget">
								<div class="box-area clearfix">
									<div class="widg-icon-inside col-xs-3" ><div id="" class=""><i class="fa  fa-user-times text-center fa-3x"></i></div></div>
									<div class="widg-content fill col-xs-9 clearfix">
										<div class="pad-5">
											<div class=" " id=""><p>Quotes Unaccepted  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much the un-accepted projects. This is being broken down to each focus company, each project manager and to each estimators."></i></span>  <span class="pull-right"><?php echo date('Y'); ?></span></p></div>
											<hr class="" style="margin: 5px 0px 0px;">
											<script type="text/javascript"> pre_load_module('#pm_estimates_widget_pm_area','dashboard/pm_estimates_widget_pm/<?php echo $assign_id; ?>',11500); </script>
											<div class="pad-top-5" id="pm_estimates_widget_pm_area" >
												<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php //$this->dashboard->pm_estimates_widget_pm($assign_id); ?>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>
<div class="clearfix col-sm-6"></div>
						<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-c small-widget">
								<div class="box-area clearfix">
									<div class="widg-icon-inside col-xs-3" ><div id="" class=""><i class="fa fa-calendar  text-center fa-3x"></i></div></div>
									<div class="widg-content fill col-xs-9 clearfix">
										<div class="pad-5">
											<div class=" " id=""><p>Maintenance AVG Days <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how many days spent (average) for a Maintenance Project. (<?php echo date('Y'); ?>)"></i></span></p></div>
											<hr class="" style="margin: 5px 0px 0px;">
											<script type="text/javascript"> pre_load_module('#maintanance_average_pm_area','dashboard/maintanance_average_pm/<?php echo $assign_id; ?>',12000); </script>
											<div class="pad-top-5" id="maintanance_average_pm_area" >
												<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php // $this->dashboard->maintanance_average_pm($assign_id); ?>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>



						<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-d small-widget">
								<div class="box-area clearfix">
									<div class="widg-icon-inside col-xs-3"><i class="fa fa-list-alt text-center fa-3x"></i></div>
									<div class="widg-content fill col-xs-9 clearfix">
										<div class="pad-5">
											<div class=" " id=""><p>Purchase Orders <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells how much purchase orders is still outstainding on each Focus Company per year."></i></span> <span class="pull-right"><?php echo date('Y'); ?></span></p></div>
											<hr class="" style="margin: 5px 0px 0px;">

 
<script type="text/javascript"> pre_load_module('#focus_get_po_widget_pm_area','dashboard/focus_get_po_widget/<?php echo $user_focus_company_id; ?>',12500); </script>

<?php /*
											<!-- // for director/pm  -->
											<?php if($pm_type == 1): ?>
												<?php <script type="text/javascript"> //pre_load_module('#focus_get_po_widget_area','dashboard/focus_get_po_widget/<?php echo $direct_company; ?>',12500); </script>  ?>


												

												<!-- testing 123 -->

											<?php endif; ?>



											<!-- // for for pm only  -->
											<?php if($pm_type == 2): ?>
												<script type="text/javascript"> pre_load_module('#focus_get_po_widget_pm_area','dashboard/focus_get_po_widget_pm/<?php echo $assign_id; ?>',12500); </script>
											<?php endif; ?>

*/ ?>



											<div class="pad-top-5" id="focus_get_po_widget_pm_area" >
												<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php //$this->dashboard->focus_get_po_widget_pm($assign_id); ?>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>





							<!-- ************************ -->
						
						<div class="clearfix"></div>

						
 


 


						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head pad-5 fill">
									<strong>Project Completion Calendar</strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the on-going projects completion dates ordered chronologically."></i></span>


									<select class="pull-right input-control input-sm chart_data_selection_pmsgp" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 150px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
										<option value="ongoingwip">Overall</option>
										<?php  
											foreach ($project_manager_list as $pmj ) {


		  if( $pmj->user_id != 29 ): 


												$pm_name_sltc = 'ongoingwip_'.strtolower( str_replace(' ','_',  $pmj->user_first_name   )   ).'_'.strtolower( str_replace(' ','_',  $pmj->user_last_name   )   );  



												echo '<option value="'.$pm_name_sltc.'">'.$pmj->user_first_name.' '.$pmj->user_last_name.'</option>';
		endif;
											} 
										?>
									</select>

									
									<div class="clearfix pull-right m-right-10">
										<?php $pcc_cats_arr = explode(',', $pcc_cats); ?>
										<?php foreach ($pcc_cats_arr as $key => $value) { echo '<div class="pcc_cats '. strtolower( str_replace(' ', '',$value)  ) .'"  >'.$value.'</div>'; } ?>
									</div>





									<script type="text/javascript">

										$('select.chart_data_selection_pmsgp').on("change", function(e) {
											$('.gantt_chart_grp').hide();
											var data = $(this).val(); 
											$('#'+data).show();




											document.getElementById('project_completion_calendar_area_scrol').scrollTop = 0;
											document.getElementById('project_completion_calendar_area_scrol').scrollRight = 0;
										});

									</script>
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix" id="project_completion_calendar_area_scrol" style="    overflow: auto;    height: 457px;   ">	
										<?php //echo $this->estimators->load_calendar_planner(); ?>
 


<div class="gantt_chart_grp" id="ongoingwip"></div>
<script>
        $(function() {
            $("#ongoingwip").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress('',' AND `users`.`user_focus_company_id` = '.$focus_company_location.'  '); ?> ],
                navigate: "buttons",
                scale: "days",
                maxScale: "days",
                minScale: "days",
                itemsPerPage: 50,
                useCookie: true,
                scrollToToday: true,
                onRender: function() {
                	//$('#est_deadline_all').hide();
                    //$('[data-toggle="tooltip"]').tooltip(); 
                }
            }); 
        });
</script>
  

<?php 
 

		foreach ($project_manager_list as $pm ): ?>

		<?php if( $pm->project_manager_id != 29 ): ?>
			

			<?php $pm_name_grp = strtolower( str_replace(' ','_',  $pm->user_first_name   )   ).'_'.strtolower( str_replace(' ','_',  $pm->user_last_name   )   ); ?>



<div class="gantt_chart_grp gnt_onload_hide" id="ongoingwip_<?php echo $pm_name_grp; ?>" ></div>
<script>
        $(function() {
            $("#ongoingwip_<?php echo $pm_name_grp; ?>").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress($pm->project_manager_id); ?> ],
                navigate: "buttons",
                scale: "days",
                maxScale: "days",
                minScale: "days",
                itemsPerPage: 50,
                useCookie: true,
                scrollToToday: true,
                onRender: function() {
                	//$('#est_deadline_all').hide();
					$('#ongoingwip_<?php echo $pm_name_grp; ?>').hide();
                    //$('[data-toggle="tooltip"]').tooltip(); 
                }
            }); 
        });
</script>



			<?php $pm_name_init_slt = strtolower( str_replace(' ','_',  $pm_name   )   ); ?>



<script type="text/javascript">
	
	setTimeout(function () {
		$('.gantt_chart_grp#ongoingwip').hide();
		$('select.chart_data_selection_pmsgp').val('ongoingwip_<?php echo strtolower($pm_name_init_slt); ?>');
		$('.gantt_chart_grp#ongoingwip_<?php echo strtolower($pm_name_init_slt); ?>').fadeIn();
	}, 5000);

</script>

<?php endif; ?>

<?php endforeach; ?>


 



  



									</div> 
								</div>

							</div>
						</div>




						<!-- ************************ -->

<?php if($user_id == 16): ?>





						<!-- ************************ -->
						
						<div class="clearfix"></div>

						<!-- ************************ -->




 




											<script type="text/javascript"> //pre_load_module('#up_coming_deadline_area','dashboard/estimators/up_coming_deadline',15000); </script>

						<div class="col-sm-12 col-xs-12 box-widget pad-10 client_supplies_area">
							<div class="widget wid-type-b widg-head-styled" style="max-height: 501px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head fill pad-5">

								<strong>Client Supply - Deliveries</strong>




								<span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title=""></i></span> 
 



 <select class="pull-right input-control input-sm slct_client_supply_sort" onchange="client_supply_tble(this.value)" style="  color: #FFF;  background: #62a762;padding: 2px 0 0 0;margin: -8px 0 0 30px; font-size: 15px; height: 35px;border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
    <option value="1" selected="">Delivery Date ASC</option>
    <option value="2">Delivery Date DESC</option> 
</select>



 <select class="pull-right input-control input-sm sltc_search_on_client_supply" onchange="search_on_client_supply_tble('6',this.value)" style="  color: #FFF;  background: #62a762;padding: 2px 0 0 0;margin: -8px 0 0 30px; font-size: 15px; height: 35px;border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
    <option class="comp_suply_5 bnd_optn" value="5_outbnd" selected="">Outbound</option>
    <option class="comp_suply_5 bnd_optn" value="5_inbnd">Inbound</option>

    <option class="comp_suply_6 bnd_optn" style="display:none;" value="6_outbnd" selected="">Outbound</option>
    <option class="comp_suply_6 bnd_optn" style="display:none;" value="6_inbnd">Inbound</option>
</select>


								<div id="" class="pull-right comp_cs_toggle">
									<button class="pull-right bttn_csp active" id="6">NSW</button>
								</div>

								<script type="text/javascript">


								$('button.bttn_csp').click(function(){
									var company = $(this).attr('id');

									$('button.bttn_csp').removeClass('active');
									$(this).addClass('active');


									$('option.bnd_optn').hide();
									$('option.comp_suply_'+company).show();


								






									var sort_spply = $('select.slct_client_supply_sort').val();


									var bnd_slct_val = $('select.sltc_search_on_client_supply').val();

									var arr_bnd = bnd_slct_val.split("_");




									client_supply_tble(sort_spply);

									var search_val = company+'_'+arr_bnd[1];




									search_on_client_supply_tble('6',search_val);



								});



								function set_as_delivered(supply_id,obj){
									$.ajax({
										'url' : site_url+'client_supply/set_as_delivered/'+supply_id,
										'type' : 'GET'
									});
									$(obj).parent().parent().parent().addClass('is_supply_delivrd').removeClass('late_delv');
									$(obj).hide();
								}

								function set_as_arrived(supply_id,obj){
									$.ajax({
										'url' : site_url+'client_supply/set_as_arrived/'+supply_id,
										'type' : 'GET'
									});
									$(obj).parent().parent().parent().addClass('is_supply_delivrd').removeClass('late_delv');
									$(obj).hide();
								}


								</script>


								<style type="text/css">


									.comp_cs_toggle button{


										color: #FFF;
										border-radius: 5px 5px 0 0;
										border: none;
										margin: 0 3px 0;
										padding: 3px 15px;
										background-color: #417741;
									}



									table#dataTable_client_supply .active{
										background-color: #FFF !important;
										color: #518e51 !important;
									}


									tr.late_delv td {
										background-color: #ffc6c6 !important;
									}

									tr.is_supply_delivrd td {
										background-color: #bef5be !important;
									}


								</style>
 

								</div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">


  


<div id="" class="" style=" max-height: 452px;    padding: 0 5px 0;    overflow: hidden;    overflow-x: hidden; ">
											<div class="clearfix center " id="joinery_in_wip">
												<!-- <p style="margin:-15px -20px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p> -->
											
<table id="dataTable_client_supply" class="table table-striped table-bordered" cellspacing="0" width="100%">



                              <thead  style="background-color:#f3f1f1;" ><tr><th class="hide">id</th><th>Project</th><th>Client</th><th>Supply Name</th><th>Warehouse</th><th>Delivery</th><th class="hide hidden"></th> </tr></thead>
										 <tbody>                              



 
										 	<?php $this->dashboard->list_client_supply_tbl(6); ?> 
										 	<?php $this->dashboard->list_client_supply_tbl(6,1); ?>







                              </tbody>



									</table>

									</div>


 



 
										</div>							
									</div>
								</div>
							</div>
						</div>

						<style type="text/css">
							.client_supplies_area #dataTable_client_supply_wrapper div.row{
								display: none; visibility: hidden;
							}


							tr.late_delv td {
								background-color: #ffc6c6 !important;
							}


							tr.focus_comp_loc_6{
								display: none;
							}
 
						</style>


<script type="text/javascript"> 




	setTimeout(function () {
		 search_on_client_supply_tble('6','6_outbnd');

	}, 3000);



</script>





						<!-- ************************ -->








<?php endif; ?>

						<div class="clearfix"></div>




						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head pad-5 fill">
									<strong>Quote Deadline Calendar</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Lists the quoted projects in completion date order chronologically. The deadline day is shown as a target in the calendar."></i></span>

									<select class="pull-right input-control input-sm chart_data_selection_est" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 100px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
										<option value="all">Overall</option>
										<?php
											foreach ($estimator_list as $est ) {
												if($est->user_first_name != ''){
													if($est->user_first_name != 'Nycel' && $est->user_first_name != 'Areeya Belle'){
														$est_name_list =  str_replace(' ','_',strtolower($est->user_first_name) );
														echo '<option value="'.$est_name_list.'">'.$est->user_first_name.'</option>';
													}
												}
											}
										?>
									</select>
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix" style="    overflow: auto;    height: 465px;    overflow: hidden;">	
										<?php //echo $this->estimators->load_calendar_planner(); ?>
<script src="<?php echo site_url(); ?>js/jquery.fn.gantt.js"></script>
<link href="<?php echo site_url(); ?>css/gant-style.css" type="text/css" rel="stylesheet"> 

<?php $custom_q = " AND `project`.`focus_company_id` = '$focus_company_location' "; ?>

<div class="gantt" id="est_deadline_all"></div>
<script>
        $(function() {
            $("#est_deadline_all").gantt({
                source: [ <?php echo $this->estimators->list_deadlines('',$custom_q); ?> {name: "",dataObj: "",values: [{from: "<?php echo date('Y/m/d', strtotime('- 5 days')); ?>", to: "<?php echo date('Y/m/d', strtotime('+ 35 days')); ?>", "desc": " ",customClass: "curr_date"}]}, ],
                navigate: "buttons",
                scale: "days",
                maxScale: "days",
                minScale: "days",
                itemsPerPage: 14,
                useCookie: true,
                scrollToToday: true,
                onRender: function() {
                	//$('#est_deadline_all').hide();
                    //$('[data-toggle="tooltip"]').tooltip(); 
                }
            }); 
        });
</script>

<?php foreach ($estimator_list as $est ): ?>
	<?php if($est->user_first_name != ''): ?>
		<?php if($est->user_first_name != 'Nycel' && $est->user_first_name != 'Areeya Belle'): ?>

<?php $est_name_list =  str_replace(' ','_',strtolower($est->user_first_name) );  ?>
<?php $est_id_list = $est->project_estiamator_id;  ?>

<div class="gantt" id="est_deadline_<?php echo $est_name_list; ?>"></div>
<script>
        $(function() {
            $("#est_deadline_<?php echo $est_name_list; ?>").gantt({
                source: [ <?php echo $this->estimators->list_deadlines($est_id_list,$custom_q); ?> ],
                navigate: "buttons",
                scale: "days",
                maxScale: "days",
                minScale: "days",
                itemsPerPage: 14,
                useCookie: true,
                scrollToToday: true,
                onRender: function() {
					$('#est_deadline_<?php echo $est_name_list; ?>').hide();

                

                    //$('[data-toggle="tooltip"]').tooltip(); 
                }
            }); 
        });
</script>

		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>




<style type="text/css">
    .navigate,.nav-link.nav-zoomIn,.nav-link.nav-zoomOut,.page-number,.nav-link.nav-page-back,.nav-link.nav-page-next,.nav-link.nav-now,.nav-link.nav-prev-week,.nav-link.nav-prev-day,.nav-link.nav-next-day,.nav-link.nav-next-week{ display: none; visibility: hidden; width: 0; height: 0; }
    .gantt .tooltip,.gantt .tooltip .tooltip-inner{ width: 350px !important; max-width: 350px !important;	}
    .fn-gantt .leftPanel{width: 75px !important; overflow: visible !important;}
    .bar.curr_date { background: #fff8da; border-top: 3px solid #fff8da; border-bottom: 2px solid #fff8da; height: 23px; margin-top: -3px; border-radius: 0; box-shadow: none; z-index: 9;}
    .fn-gantt .rightPanel {overflow-x: scroll !important;    overflow-y: hidden !important; }
    .fn-gantt .fn-content {background: #f1f1f1 !important;}


	.stripout{ background:#416987 !important; }
	.fullfitout{ background:#D9534F !important; }
	.minorworks{ background:#4DB9D7 !important; }
	.refurbishment{ background:#5E2971 !important; }
	.kiosk{ background:#4DAB4D !important; }
	.maintenance{ background:#F7901E !important; }

	.designworks{ background:#A58377 !important; }
	.joineryonly{ background:#5393C8 !important; }
	.company{ background:#FF00FC !important; }

	.pcc_cats{
		padding: 1px 8px; font-size: 12px; float: right; border: 1px solid #FFF; height: 20px; margin: 0px 5px; border-radius: 10px; display: block;
	}
</style>

<script type="text/javascript">
	$('select.chart_data_selection_est').on("change", function(e) {
		var data = $(this).val();
		$('.gantt').hide();
		$('.gantt#est_deadline_'+data).show();
	});
</script>



									</div> 
								</div>

							</div>
						</div>


						<!-- ************************ -->






						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 box-widget pad-10">
							<div class="widget wid-type-c widg-head-styled" style="height: 501px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head fill pad-5"><strong>Up-coming Deadline</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the number of days when the next deadline is occurring."></i></span>  <span class="badges pull-right"> <span class="pull-right"><?php echo date('Y'); ?></span> </span></div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">


 
										<div class="pad-10" id="">

											<script type="text/javascript"> pre_load_module('#up_coming_deadline_area','dashboard/estimators/up_coming_deadline/0/<?php echo $focus_company_location; ?>',15000); </script>
											<div class="clearfix center knob_box pad-10 small-widget" id="up_coming_deadline_area">
												<p style="margin:-15px -20px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php //echo $this->estimators->up_coming_deadline(); ?>
											</div>
											<style type="text/css">.knob_box canvas{width: 100% !important;}</style>


										</div>							
									</div>
								</div>
							</div>
						</div>





						<!-- ************************ -->


						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled" style="height: 501px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><strong>Project Estimator Quotes</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells each estimators current quoted projects for the year and compares to last year."></i></span> <span class="badges pull-right"> <span class="pull-right"><?php echo date('Y'); ?></span> </span></div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">
										<script type="text/javascript"> pre_load_module('#estimators_quotes_completed_area','dashboard/estimators/estimators_quotes_completed',15500); </script>
										<div class="" id="estimators_quotes_completed_area">
											<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
											<?php // $status_forecast = $this->estimators->estimators_quotes_completed(); ?>
										</div>							
									</div>
								</div>
							</div>
						</div>

						<!-- ************************ -->






						<!-- ************************ -->
						
					 
						
						<div class="clearfix"></div>


						<!-- ************************ -->
 
 


						<!-- ************************ -->


						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 box-widget pad-10 pie_toggle_custom_a" >
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><i class="fa fa-tags  text-center "></i> <strong>Projects by Type </strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="The total project costs per project category, only completed projects of the current year to date are included."></i></span> <span class="pull-right"> <?php echo date('Y'); ?></span></div>
								<div class="box-area clearfix" style="height:320px;">
									<div class="widg-content clearfix">

										<div id="" class="pad-5" style="">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 box-widget pie_display_dyn">
											<div class="" id="donut_prj_bt" style="text-align: center;"></div>
</div>
										


											<script type="text/javascript"> pre_load_module('#focus_projects_by_type_widget_pm_area','dashboard/focus_projects_by_type_widget_pm/<?php  echo $user_id;   ?>',16000); </script>
									<div id="" class="text_display_dyn_tq" style="display:none; float:right;">
																				<div class="col-xs-12 box-widget m-top-10 " id="focus_projects_by_type_widget_pm_area">
																					<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
																					<?php //echo $this->dashboard->focus_projects_by_type_widget(); ?>
																				</div>
									
									</div>




										</div>
										<p class="m-top-10 pad-top-10"><button class="btn btn-xs btn-primary toggle_pie_text m-top-10 m-left-15" style="display:none;">Toggle Display</button></p>

						<script type="text/javascript">

											//	alert( $(window).width() );

										
setTimeout(function () {

	if (    $(window).width() >= 1180 && $(window).width() <= 1680    ){

	$('.text_display_dyn_tq').removeAttr("style");
	$('.pie_display_dyn').css('width','100%');
 	$('.text_display_dyn_tq').css('display','none');
	$('.text_display_dyn_tq').css('width','100%');
	$('.toggle_pie_text').show();

												//		$('.text_display_dyn').hide();	
												//		$('.pie_display_dyn').hide();	
												//		$('.toggle_pie_text').show();	

}else{
	$('.text_display_dyn_tq').removeAttr("style");
 	$('.text_display_dyn_tq').show();	

 	


	$('.pie_display_dyn').css('width','50%');
	$('.text_display_dyn_tq').css('width','50%');
	$('.text_display_dyn_tq').css('float','right');
	$('.toggle_pie_text').hide();
}


}, 16005);



											$(window).resize(function() {
											//	alert( $(window).width() );



if (    $(window).width() >= 1180 && $(window).width() <= 1680    ){

	$('.text_display_dyn_tq').removeAttr("style");
	$('.pie_display_dyn').css('width','100%');
 	$('.text_display_dyn_tq').css('display','none');
	$('.text_display_dyn_tq').css('width','100%');
	$('.toggle_pie_text').show();

												//		$('.text_display_dyn').hide();	
												//		$('.pie_display_dyn').hide();	
												//		$('.toggle_pie_text').show();	

}else{

	$('.text_display_dyn_tq').removeAttr("style");
 	$('.text_display_dyn_tq').show();	
	$('.pie_display_dyn').css('width','50%');
	$('.text_display_dyn_tq').css('width','50%');
	$('.text_display_dyn_tq').css('float','right');
	$('.toggle_pie_text').hide();
}

/*

if ($(window).width() >= 1400 && $(window).width() <= 1660) {
														alert('Less than 960');
														$('.text_display_dyn').show();	
														$('.pie_display_dyn').show();	
														$('.toggle_pie_text').show();	
													}else {
														alert('More than 960');
														$('.text_display_dyn').hide();	
														$('.pie_display_dyn').show();	
														$('.toggle_pie_text').hide();	
													}
*/
												});
 

											$('.toggle_pie_text').click(function(){
												$('.text_display_dyn_tq').toggle();	
												$('.pie_display_dyn').toggle();	


										 	//  $('.text_display_dyn').css('width','100%');
											//	$('.text_display_dyn').css('display','block');

											}); 


										</script>

										<style type="text/css">

 

											@media (min-width: 1180px) and (max-width: 1680px) {
 
												.pie_toggle_custom_a{
													width: 25%;
												}


												.pie_toggle_custom_b{
													width: 75%;
												}

											}
 


										</style>

									</div>
								</div>
							</div>
						</div>




						<style type="text/css">
							@media (min-width: 1200px) and (max-width: 1500px) {
								.knob {
									font-size: 50px !important;
								}
							}


							@media (min-width: 1510px) and (max-width: 1800px) {
								.knob {
									font-size: 75px !important;
								}
							}
						</style>












						<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 box-widget pad-10 pie_toggle_custom_b">
							<div class="widget wid-type-e widg-head-styled">
								<div class="  fill box-widg-head pad-right-10 pad-left-10 m-left-15 pull-right pad-top-3 m-3">									
									<strong>
										<?php echo date('Y'); ?> <span  data-placement="left" class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="left" data-original-title="Top 20 Clients/Contractors/Suppliers having the highest job cost for the year <?php echo date('Y'); ?>."></i></span>
									</strong>
								</div>
								<div class="tabs_widget" >
									<ul  class="nav nav-tabs" role="tablist" style="height: 32px;">
										<li role="presentation" class="active"><a href="#clients" class="tab_btn_dhb" role="tab" id="clients-tab_a" data-toggle="tab" >Clients</a></li>
										<li role="presentation" class=""><a href="#contractors" class="tab_btn_dhb" role="tab" id="contractors-tab_a" data-toggle="tab" >Contractors</a></li>
										<li role="presentation" class=""><a href="#suppliers" class="tab_btn_dhb" role="tab" id="suppliers-tab_a" data-toggle="tab" >Suppliers</a></li>


										<li role="presentation" class="active"><a href="#clients_mtnc" style="display:none;" class="tab_btn_dhb_mntnc" role="tab" id="clients-tab_b" data-toggle="tab" >Clients</a></li>
										<li role="presentation" class=""><a href="#contractors_mtnc" style="display:none;" class="tab_btn_dhb_mntnc" role="tab" id="contractors-tab_b" data-toggle="tab" >Contractors</a></li>
										<li role="presentation" class=""><a href="#suppliers_mtnc" style="display:none;" class="tab_btn_dhb_mntnc" role="tab" id="suppliers-tab_b" data-toggle="tab" >Suppliers</a></li>


									</ul>


									<div id="myTabContent" class="tab-content pad-10 clearfix"> 
										
										<div role="tabpanel" class="tab-pane active fade in" id="clients" >
											<div id="" class="col-lg-5">
												<div class="loading_chart" style="height: 300px; text-align: center; padding: 100px 53px; color: #ccc;"><i class="fa fa-spin fa-refresh fa-4x"></i></div>
												<div class="" id="donut_a" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_clients_pm_area','dashboard/focus_top_ten_clients_pm/<?php echo $user_id; ?>',16500); </script>
												<div id="focus_top_ten_clients_pm_area" class="" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_clients_pm($assign_id); //focus_top_ten_clients_pm ?>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane fade in" id="contractors">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_b" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_pm_area_a','dashboard/focus_top_ten_con_sup_pm/2/<?php echo $user_id; ?>',17000); </script>
												<div id="focus_top_ten_con_sup_pm_area_a" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup_pm('2',$assign_id); ?>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane fade in" id="suppliers">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_c" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_area_c','dashboard/focus_top_ten_con_sup_pm/3/<?php echo $user_id; ?>',17500); </script>
												<div id="focus_top_ten_con_sup_area_c" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup_pm('3',$assign_id); ?>
												</div>
											</div>
										</div>
   
									</div>
								</div>
							</div>
						</div>































						<div class="clearfix"></div>


						<!-- ************************ -->




						<!-- ************************ -->

<?php /*

<?php 

		$maint_last_year = $this->dashboard->get_count_maint_per_week($current_year-1);
		$maint_this_year = $this->dashboard->get_count_maint_per_week($current_year);
		$maint_average = $this->dashboard->get_count_maint_per_week(2015,1);


		$maint_last_year_arr = explode(',', $maint_last_year);
		$ave_maint_last_year = (array_sum($maint_last_year_arr) / 52) ;


		$maint_this_year_arr = explode(',', $maint_this_year);
		$ave_maint_this_year = (array_sum($maint_this_year_arr) / count(array_filter($maint_this_year_arr)) );

		$maint_average_arr = explode(',', $maint_average);
		$ave_maint_average = (array_sum($maint_average_arr) / 52) ;




 ?>

						<div class="col-md-9 col-sm-8 col-xs-12 col-lg-10 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head fill  pad-5">
									<strong>Maintenance Projects : Average Per Day - <?php echo date('Y'); ?></strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Lists every months week numbers and tells how many in average a maintenance project has taken place and compares values to last year of the same week number."></i></span>


									<span style="float: right;    font-weight: bold;">
										<span class=" tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Average Last Year: <?php echo $current_year-1; ?>">Avg Last Year: <?php echo round($ave_maint_last_year,2); ?></span> &nbsp;  &nbsp;  &nbsp; 
										<span class=" tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Average This Year: <?php echo $current_year; ?>">Avg This Year: <?php echo round($ave_maint_this_year,2); ?></span> &nbsp;  &nbsp;  &nbsp; 
										<span class=" tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Average Overall">Over All Avg: <?php echo round($ave_maint_average,2); ?></span>

									</span>
							
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix">
										
										<div class id="job_book_area">
											<div id="chart_main"></div>
											<div id="" class="" style="margin: -6px -26px 17px 53px;    display: block;    clear: both;    font-size: 10px;">
												<?php foreach ($months as $key => $value): ?>
													<div id="" class="mos"><?php echo $value; ?></div>
												<?php endforeach; ?>
											</div>
											<style type="text/css">.mos {    float: left;    width: 8.1%;    text-align: center;}</style>
										</div> 	
									</div>
								</div>

							</div>
						</div>


						<script type="text/javascript">


var chart = c3.generate({
	size: {
		height: 310
	},data: {
		x : 'x',
		columns: [
          ['x',  // months labels

          <?php 

          for($i=1; $i <53 ; $i++){
          	echo "'".$i."',";
          }


          ?> ], // months labels



// 	Overall Last Year Sales
<?php
echo "['Last Year',";
echo $maint_last_year;
echo "],";
?>
// 	Overall Last Year Sales


// Overall Sales
<?php
echo "['Current',";
echo $maint_this_year;
echo "],";
?>

 
<?php
echo "['Average',";
echo $maint_average;
echo "]";
?>





],
selection: {enabled: true},
type: 'bar',
colors: {
	'Average': '#FF7F0E',
	'Current': '#2CA02C',
	'Last Year': '#9467BD',

        },
        types: {   'Average' : 'line',  
},

order: null,
},
tooltip: {
        grouped: true // false // Default true
    },
    bindto: "#chart_main",
    bar:{ width:{ ratio: 0.5 }},
    point:{ select:{ r: 6 }},
   // onrendered: function () { $('.loading_chart').remove(); },
//zoom: {enabled: true, rescale: true,extent: [1, 7]},
legend: { show: false },


axis: {x: {type: 'category', tick: {rotate: 0,multiline: false}, height: 0}, y: {        tick: {          format: d3.format('.2f')        }      } }, 
tooltip: {
	format: {
     title: function (x) { return 'Week '+(x+1); },
     value: function (value, ratio, id) {
               // var format = id === 'data1' ? d3.format(',') : d3.format('$');
               var format = d3.format('.2f');

               var mod_value =  value.toFixed(2); //Math.ceil(value,2)  // need to get 2 decimal places

           //    var mod_value_x = parseFloat(Math.round(mod_value * Math.pow(10, 2)) /Math.pow(10,2)).toFixed(2);

            // var mod_value_y =   d3.format('.2f')

            //   var mod_value = parseFloat(Math.round(value * 100) / 100).toFixed(2);
               //return '$ '+format(mod_value);
               return format(mod_value);
           }//
       } 

   }
});



chart.select();

</script>


						<!-- ************************ -->




						<div class="col-md-3 col-sm-4 col-xs-12 col-lg-2 box-widget pad-10">
							<div class="widget wid-type-c widg-head-styled" style="height: 364px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head  box-widg-head fill pad-5"><strong>Maintenance Projects</strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the number of projects undertaken year to date for maintenance and that compared to the same time the previous year."></i></span> </div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">

										<div class="pad-10" style="position:relative;">
										<?php echo $this->dashboard->get_count_maintenance(); ?>


											<style type="text/css">.knob_box canvas{width: 100% !important;}.knob_box p{font-size: 15px !important; }</style>
										</div>							
									</div>
								</div>
							</div>
						</div>






 
						<div class="clearfix"></div>


<?php /*
						<!-- ************************ -->


						<!-- ************   LEAVE CHART   ************ -->
						<!-- ************   PM   ************ -->
						

						<div id="" class="hide hidden">


<?php
	$custom_q = '';
	if($pm_type == 1){
		$custom_q = " AND `users`.`user_focus_company_id` = '$focus_company_location' ";
		$user_list_q = $this->user_model->list_user_short($custom_q);
		$user_list= $user_list_q->result();
	}else{
								//	$custom_q = " AND `users`.`user_id` = '$user_id' ";
		$user_list_q = $this->user_model->list_user_short(" AND `users`.`user_id` = '$user_id' ");
		$user_list= $user_list_q->result();
	}
?>

<?php 
	$user_list_q = $this->user_model->list_user_short($custom_q);
	$user_list= $user_list_q->result();
?>


							
							<?php 

							$q_leave_types = $this->user_model->fetch_leave_type();
							$leave_types = $q_leave_types->result();

							$added_data = new StdClass();
							$added_data->{"leave_type_id"} = '0';
							$added_data->{"leave_type"} = 'Public Holiday';
							$added_data->{"remarks"} = '';


							array_push($leave_types, $added_data);
 


							if($pm_type == 1){

								$leave_totals =  $this->dashboard->get_count_per_week(2,'','',$custom_q);
								$last_year_leave = $this->dashboard->get_count_per_week(2,$last_year,'',$custom_q);
							}else{

								$leave_totals =  $this->dashboard->get_count_per_week(2,'',$user_id);
								$last_year_leave = $this->dashboard->get_count_per_week(2,$last_year,$user_id);
							}

							?>
						</div>

						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head fill  pad-5">
									<strong>Employee Leave Chart : <?php echo date('Y'); ?></strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Lists every months week number and displays how many leaves taken place, the chart can be broken down into individual employees."></i></span>
<?php 
          	if($pm_type == 1): ?>

	<select class="pull-right input-control input-sm chart_data_selection_emps" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 100px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
										
										<option disabled="" value="0">Select View</option>
										<option value="grouped"  selected="all">Grouped</option>


										<?php foreach ($user_list as $key => $value): ?> 
												<option class="emp_opy_select"  value="<?php echo $value->user_first_name.' '.$value->user_last_name.'|'.$value->primary_user_id; ?>" ><?php echo $value->user_first_name.' '.$value->user_last_name; ?></option>
										<?php endforeach; ?>
									</select>

<?php endif; ?>
							
								</div>

<?php $color_leave_type = array('#A7184B','#0092CE','#3D00A4','#FE2712','#FD5309','#0047FE','#8600AF');  ?>


<?php 
$leave_type_list = array();

$leave_type_list[1] = 'Annual Leave';
$leave_type_list[5] = 'Unpaid Leave';
$leave_type_list[2] = 'Personal (Sick Leave)';
$leave_type_list[6] = 'RDO (Rostered Day Off)';
$leave_type_list[0] = 'Public Holiday';
$leave_type_list[3] = 'Personal (Carers Leave)';
$leave_type_list[4] = 'Personal (Comp. Leave)';

?>



								<div class="box-area clearfix row pad-right-10 pad-left-10 pad-bottom-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix">
										<div class="chart_main_leave_loading_chart" style="height: 457px; text-align: center; padding: 100px 53px; color: #ccc;"><i class="fa fa-spin fa-refresh fa-4x"></i></div>
										<div class id=" pad-bottom-10 ">
											
											<div id="chart_main_leave"  ></div>

											<div id="" class="" style="margin: -6px -26px 17px 53px;    display: block;    clear: both;    font-size: 10px;">
												<?php foreach ($months as $key => $value): ?>
													<div id="" class="mos"><?php echo $value; ?></div>
												<?php endforeach; ?>
											</div>

											<script type="text/javascript"> var default_totals_c = ''; var default_totals_o = ''; </script>

											<div id="" class="clearfix" style="margin: 20px 10px 0px;">
												<?php $current_total_leaves = ''; $previou_total_leaves= ''; ?>


												<?php foreach ($leave_type_list as $leave_data_id => $leave_name_value): ?>
													<div class="" style="padding:2px; float:left; display:block; width: 14%;  background: <?php echo $color_leave_type[$leave_data_id];  ?>;">
														<p class="pointer leave_type_selection tooltip-enabled type_label_<?php echo $leave_data_id; ?>" title="" data-html="true" data-placement="top" data-original-title="Total Applied: <?php echo $leave_totals[$leave_data_id]; ?><br />Last Year:  <?php echo $last_year_leave[$leave_data_id]; ?>" id="<?php echo $leave_name_value; ?>" style="color: #fff;   font-size: 12px;  text-align: center;"><?php echo $leave_name_value; ?></p>
													</div>

 

													<?php 
														$current_total_leaves .= $leave_data_id.'-'.$leave_totals[$leave_data_id].'|';
														$previou_total_leaves .= $leave_data_id.'-'.$last_year_leave[$leave_data_id].'|';
													 ?>


												<?php endforeach; ?>
											</div>

											<div id="" class="hide hidden">
												<p class="current_total_leaves" ><?php echo "$current_total_leaves"; ?></p>
												<p class="previou_total_leaves" ><?php echo "$previou_total_leaves"; ?></p>
											</div>

											<style type="text/css">.mos {    float: left;    width: 8.1%;    text-align: center;}</style>
										</div> 	
									</div>
								</div>

							</div>
						</div>




						<script type="text/javascript">


var chart_emply = c3.generate({
	size: {
		height: 340

	},data: {
		x : 'x',
		columns: [
          ['x',  // months labels

          <?php 

          for($i=1; $i <53 ; $i++){


					if($i > 2){

          				echo "'".$i."',";
					}
          }


          ?> ], // months labels


          <?php 


		//          if($user_id == 16 || $user_id == 15){

          if($pm_type == 1){
          	echo $this->dashboard->get_count_per_week(1,'','',$custom_q);

          }else{
          	echo $this->dashboard->get_count_per_week(3,$current_year,$user_id);

          }


           ?>

 


],
selection: {enabled: true},
type: 'bar', 
 
colors: {
	//*'Average': '#FF7F0E',
	//'Current': '#2CA02C',
//	'Last Year': '#9467BD',

	<?php #/*foreach ($leave_types as $leave_data): ?>
		'<?php # echo $this->session->get("user_first_name")." ".$this->session->get("user_last_name")." ".$leave_data->leave_type; ?>': '<?php echo $color_leave_type[$leave_data->leave_type_id];  ?>',
	<?php #endforeach; ?>

	
	<?php foreach ($user_list as $key => $value): ?> 
		<?php foreach ($leave_types as $leave_data): ?>
		'<?php echo $value->user_first_name.' '.$value->user_last_name." ".$leave_data->leave_type; ?>': '<?php echo $color_leave_type[$leave_data->leave_type_id]; ?>',
	<?php endforeach; ?>
<?php endforeach; ?>


        },

groups: [



[
	<?php # /*foreach ($leave_types as $leave_data): ?>
		'<?php #echo $this->session->get("user_first_name")." ".$this->session->get("user_last_name")." ".$leave_data->leave_type; ?>',
	<?php #endforeach; ?>


	 
<?php foreach ($user_list as $key => $value): ?> 
	<?php foreach ($leave_types as $leave_data): ?>
		'<?php echo $value->user_first_name.' '.$value->user_last_name.' '.$leave_data->leave_type; ?>',
	<?php endforeach; ?>
<?php endforeach; ?>



],


],
order: null,
}, 
    
tooltip: {
        grouped: true // false // Default true
    },
    bindto: "#chart_main_leave",
    bar:{ width:{ ratio: 0.5 }},
    point:{ select:{ r: 6 }},
 onrendered: function () {

  $('.chart_main_leave_loading_chart').remove();




   },
//zoom: {enabled: true, rescale: true,extent: [1, 7]},
legend: { show: false },

axis: {x: {type: 'category', tick: {rotate: 0,multiline: false}, height: 0}, y: {       tick: {          format: d3.format('.2f')        }      } }, 

tooltip: {
	format: {
     title: function (x) { return 'Week '+(x+3); },
     value: function (value, ratio, id) {
               // var format = id === 'data1' ? d3.format(',') : d3.format('$');
               var format = d3.format('.2f');

               var mod_value =  value.toFixed(2); //Math.ceil(value,2)  // need to get 2 decimal places

           //    var mod_value_x = parseFloat(Math.round(mod_value * Math.pow(10, 2)) /Math.pow(10,2)).toFixed(2);

            // var mod_value_y =   d3.format('.2f')

            //   var mod_value = parseFloat(Math.round(value * 100) / 100).toFixed(2);
               //return '$ '+format(mod_value);
            //   return format(mod_value);


               if(mod_value > 0){
               	 return format(mod_value);
               }


           }//
       } 

   }
});

//chart_emply.hide();


				chart_emply.show();
				chart_emply.hide(['Overall Annual Leave','Overall Personal (Sick Leave)','Overall Personal (Carers Leave)','Overall Personal (Compassionate Leave)','Overall Unpaid Leave','Overall Public Holiday','Overall RDO (Rostered Day Off)']);
	


 

$('select.chart_data_selection_emps').on("change", function(e) {

	$('#loading_modal').modal({"backdrop": "static", "show" : true} );

	var data = $(this).val();

	var current_total_leaves = $('p.current_total_leaves').text().split('|');
	var previou_total_leaves = $('p.previou_total_leaves').text().split('|');



	setTimeout(function(){


		chart_emply.hide(); 
		chart_emply.unselect();

		if(data == 'all'){
			setTimeout(function () { 
				chart_emply.show(['Overall Annual Leave','Overall Personal (Sick Leave)','Overall Personal (Carers Leave)','Overall Personal (Compassionate Leave)','Overall Unpaid Leave','Overall Public Holiday','Overall RDO (Rostered Day Off)']);
			}, 500);



			for (var i = 0; i < current_total_leaves.length; i++) {
				var data_thisYear = current_total_leaves[i].split('-');
				var data_lastYear = previou_total_leaves[i].split('-');
				$("p.type_label_"+data_thisYear[0]).attr('data-original-title', "Total Applied: "+data_thisYear[1]+"<br />Last Year: "+data_lastYear[1]);
			}




		}else if(data == 'grouped'){
			setTimeout(function () {
				chart_emply.show();
				chart_emply.hide(['Overall Annual Leave','Overall Personal (Sick Leave)','Overall Personal (Carers Leave)','Overall Personal (Compassionate Leave)','Overall Unpaid Leave','Overall Public Holiday','Overall RDO (Rostered Day Off)']);
			}, 500);


			for (var i = 0; i < current_total_leaves.length; i++) {
				var data_thisYear = current_total_leaves[i].split('-');
				var data_lastYear = previou_total_leaves[i].split('-');
				$("p.type_label_"+data_thisYear[0]).attr('data-original-title', "Total Applied: "+data_thisYear[1]+"<br />Last Year: "+data_lastYear[1]);
			}



		}else{

			var user_data_selected = data.split('|');

//alert(user_data_selected[1]);
			setTimeout(function () {
//get_count_per_week($return_total = 0, $set_year = '', $set_emp_id = '' )

				$.ajax({
					'url' : site_url+'dashboard/get_count_per_week/2/<?php echo $current_year; ?>/'+user_data_selected[1],
					'type' : 'GET',
					'success' : function(dataValCurr){
						var data_arr_curr_dataVal = dataValCurr.split('|');
						
						$.ajax({
							'url' : site_url+'dashboard/get_count_per_week/2/<?php echo $last_year; ?>/'+user_data_selected[1],
							'type' : 'GET',
							'success' : function(dataVal){

								var data_arr_dataVal = dataVal.split('|');

								for (var i = 0; i < data_arr_curr_dataVal.length; i++) {

									var data_thisYear = data_arr_curr_dataVal[i].split('-');
									var data_lastYear = data_arr_dataVal[i].split('-');

									//alert(data_thisYear[0]+' **** '+data_thisYear[1]);
									//alert(data_lastYear[0]+' **** '+data_lastYear[1]);

									$("p.type_label_"+data_thisYear[0]).attr('data-original-title', "Total Applied: "+data_thisYear[1]+"<br />Last Year: "+data_lastYear[1]);
								}

							}
						});
					}
				});



				chart_emply.show([user_data_selected[0]+' Annual Leave',user_data_selected[0]+' Personal (Sick Leave)',user_data_selected[0]+' Personal (Carers Leave)',user_data_selected[0]+' Personal (Compassionate Leave)',user_data_selected[0]+' Unpaid Leave',user_data_selected[0]+' Public Holiday',user_data_selected[0]+' RDO (Rostered Day Off)']);
			}, 500);
		}

	//	chart_emply.select();

	 
	},500);	

	setTimeout(function(){
		$('#loading_modal').modal('hide');
	},5000);	

	});


$('.leave_type_selection').click(function(){
	$('select.chart_data_selection_emps').val('0');
	$('#loading_modal').modal({"backdrop": "static", "show" : true} );
	var leave_type = $(this).attr('id');


	setTimeout(function () {
		chart_emply.hide(); 
		chart_emply.unselect();
	}, 500);
 
	setTimeout(function () {
		chart_emply.show([

			<?php foreach ($user_list as $key => $value): ?>  
				'<?php echo $value->user_first_name.' '.$value->user_last_name.' '; ?>'+leave_type,
			<?php endforeach; ?> 

		]);
	}, 1000);

	setTimeout(function(){
		//chart_emply.select();
		$('#loading_modal').modal('hide');
	},3000);

});

</script>

*/ ?>
						<!-- ************************ -->

						<!-- ************   LEAVE CHART   ************ -->
						<!-- ************   PM   ************ -->
						<!-- ************   LEAVE CHART   ************ -->





						<!-- ************************ -->
						
						<div class="clearfix"></div>



						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-a widg-head-styled ">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><i class="fa fa-map-marker fa-lg"></i> <strong>On-Going Projects in Australia</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Displays the map of Australia and plots down the location of all on-going projects."></i></span></div>
								<div class="box-area clearfix  pad-0-imp" style="height:500px;">
									<div class="widg-content clearfix pad-0-imp">
										<div id="map"></div>									
									</div>
								</div>
							</div>
						</div>


						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 box-widget pad-10" >
							<div class="widget wid-type-a widg-head-styled ">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5">
									<i class="fa fa-users  fa-lg"></i> 
									<strong>Focus Employee Locations</strong>
									 <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Displays a map where the office of each focus employees are located."></i></span>
								</div>
								<div class="box-area clearfix  pad-0-imp" style="height:500px;">
									<div class="widg-content clearfix pad-0-imp">
										<div id="employee-map-canvas" class="" style="width: 100%; height: 100%;"></div>
									</div>
								</div>
							</div>
						</div>


						<div class="clearfix"></div>

						<!-- ************************ -->

					</div>
				</div>				
			</div>
		</div>
	</div>
</div>



<?php echo view('assets/logout-modal'); ?>



<script>
var chart = c3.generate({
      size: {
        height: 457
      },data: {
        x : 'x',
        columns: [
          ['x',  // months labels

          <?php 

          for($i=0; $i < 12 ; $i++){
          	$alternator = $calendar_view; $counter = $i;

          	if($alternator == 1){
          		$counter = $counter + 6;
          	}

          	if($alternator == 1 && $counter > 11){
          		$counter = $counter - 12;
          	}

          	$month_index = $months[$counter];
          	echo "'".$month_index."',";
          }


          ?> ], // months labels



// 	Overall Last Year Sales
<?php
echo "['".$fcsO['company_name']."',";

for($i=0; $i < 12 ; $i++){	
	$alternator = $calendar_view;
	$counter = $i;

	if($alternator == 1){
		$counter = $counter + 6;
	}

	if($alternator == 1 && $counter > 11){
		$counter = $counter - 12;
	}

	$month_index = 'rev_'.strtolower($months[$counter]);
	echo $fcsO[$month_index].',';
}

echo "],";
?>
// 	Overall Last Year Sales



// PM Project Manager Sales Last Year
	<?php foreach ($pms_sales_last_year->getResultArray() as $pm_sales_data ) {
		if($pm_sales_data['user_pm_name'] == 'Maintenance Manager' && $pm_sales_data['focus_comp_id'] == '6'  ){

		}else{
			echo "['".$pm_sales_data['user_pm_name']." Last Year',".$pm_sales_data['rev_jan'].",".$pm_sales_data['rev_feb'].",".$pm_sales_data['rev_mar'].",".$pm_sales_data['rev_apr'].",".$pm_sales_data['rev_may'].",".$pm_sales_data['rev_jun'].",".$pm_sales_data['rev_jul'].",".$pm_sales_data['rev_aug'].",".$pm_sales_data['rev_sep'].",".$pm_sales_data['rev_oct'].",".$pm_sales_data['rev_nov'].",".$pm_sales_data['rev_dec']."],";
		}
	} ?>
// PM Project Manager Sales Last Year



//	Focus Company Last Year Sales
<?php foreach ($focus_indv_comp_sales_old->getResultArray() as $indv_comp_sales): ?>
	<?php //var_dump($indv_comp_sales); ?>

	<?php echo "['".$indv_comp_sales['company_name']." Last Year',"; ?>

		<?php 
          for($i=0; $i < 12 ; $i++){	
          	$alternator = $calendar_view;
          	$counter = $i;

          	if($alternator == 1){
          		$counter = $counter + 6;
          	}

          	if($alternator == 1 && $counter > 11){
          		$counter = $counter - 12;
          	}

          	$month_index = 'rev_'.strtolower($months[$counter]);
          	$item_forecast = $indv_comp_sales[$month_index];
          	echo $item_forecast.',';
          }

          ?>
	<?php echo "],"; ?>
<?php endforeach; ?>
//	Focus Company Last Year Sales





// Overall Sales
<?php
echo "['".$swout['company_name']."',";
for($i=0; $i < 12 ; $i++){	
	$alternator = $calendar_view;
	$counter = $i;

	if($alternator == 1){
		$counter = $counter + 6;
	}

	if($alternator == 1 && $counter > 11){
		$counter = $counter - 12;
	}

	$month_index = 'sales_data_'.strtolower($months[$counter]);
	echo $swout[$month_index].',';
}

echo "],";

?>


// Focus Overall WIP
<?php
	echo "['Focus Overall WIP',";
	for($i=0; $i < 12 ; $i++){	
		echo $focus_wip_overall[$i].',';
	}
	echo "],";
?>
// Focus Overall WIP

// Overall Sales
 

 
 



// Focus Company Sales
<?php foreach ($focus_indv_comp_sales->getResultArray() as $indv_comp_sales): ?>
	<?php //var_dump($indv_comp_sales); ?>
	<?php echo "['".$indv_comp_sales['company_name']."',"; ?>
		<?php 
          for($i=0; $i < 12 ; $i++){	
          	$alternator = $calendar_view;
          	$counter = $i;

          	if($alternator == 1){
          		$counter = $counter + 6;
          	}

          	if($alternator == 1 && $counter > 11){
          		$counter = $counter - 12;
          	}

          	$month_index = 'rev_'.strtolower($months[$counter]);
          	$item_forecast = $indv_comp_sales[$month_index];
          	echo $item_forecast.',';
          }

        ?>
	<?php echo "],"; ?>
<?php endforeach; ?>
// Focus Company Sales


 
 

// PM Project Manager Sales
	<?php foreach ($pms_sales_c_year->getResultArray() as $pm_sales_data ) {
		if($pm_sales_data['user_pm_name'] == 'Maintenance Manager' && $pm_sales_data['focus_comp_id'] == '6'  ){

		}else{
			echo "['".$pm_sales_data['user_pm_name']."',".$pm_sales_data['rev_jan'].",".$pm_sales_data['rev_feb'].",".$pm_sales_data['rev_mar'].",".$pm_sales_data['rev_apr'].",".$pm_sales_data['rev_may'].",".$pm_sales_data['rev_jun'].",".$pm_sales_data['rev_jul'].",".$pm_sales_data['rev_aug'].",".$pm_sales_data['rev_sep'].",".$pm_sales_data['rev_oct'].",".$pm_sales_data['rev_nov'].",".$pm_sales_data['rev_dec']."],";
		}
	} ?>
// PM Project Manager Sales

 



// Focus Company WIP
<?php 
foreach ($focus_comp_wip as $key => $value) {

	if( array_sum($focus_comp_wip[$key]) > 0){
		echo "['$key WIP',";

		for($i=0; $i < 12 ; $i++){
			echo round($focus_comp_wip[$key][$i],2).',';
		}

		echo "],";
	}
}

?>
// Focus Company WIP


// Focus PM WIP
<?php 
foreach ($focus_pm_wip as $key => $value) {

	if( array_sum($focus_pm_wip[$key]) > 0){
		echo "['$key WIP',";

		for($i=0; $i < 12 ; $i++){
			echo round($focus_pm_wip[$key][$i],2).',';
		}

		echo "],";
	}
}

?>
// Focus PM WIP


//	Focus Company Forecast
<?php foreach ($focus_indv_comp_forecast->getResultArray() as $indv_comp_forec): ?>
	<?php echo "['".$indv_comp_forec['company_name']." Forecast',"; ?>
		<?php 
          for($i=0; $i < 12 ; $i++){	
          	$alternator = $calendar_view;
          	$counter = $i;

          	if($alternator == 1){
          		$counter = $counter + 6;
          	}

          	if($alternator == 1 && $counter > 11){
          		$counter = $counter - 12;
          	}

          	$comp_total_forec = $indv_comp_forec['total'] *($indv_comp_forec['forecast_percent']/100);
          	$month_index = 'forecast_'.strtolower($months[$counter]);
          	$item_forecast = $comp_total_forec * ($fetch_forecast[$month_index]/100);
          	echo round($item_forecast,2).',';
          }
        ?>
	<?php echo "],"; ?>
<?php endforeach; ?>
//	Focus Company Forecast
		
//	Overall Forecast
		['Forecast',
		<?php 

          for($i=0; $i < 12 ; $i++){	
          	$alternator = $calendar_view;
          	$counter = $i;

          	if($alternator == 1){
          		$counter = $counter + 6;
          	}

          	if($alternator == 1 && $counter > 11){
          		$counter = $counter - 12;
          	}

          	$month_index = 'forecast_'.strtolower($months[$counter]);
          	$item_forecast = $fetch_forecast['total'] * ($fetch_forecast[$month_index]/100);
          	echo $item_forecast.',';
          }

        ?>],
//	Overall Forecast


/*

<?php //  var_dump($focus_pm_forecast); ?>

---------------------

<?php // var_dump($focus_data_forecast_p); ?>
*/

// PM Project Manager Forecast
          <?php foreach ($focus_pm_forecast as $pm_fct){
         // 	$amount = $focus_data_forecast_p[$pm_fct->comp_id] * ($pm_fct->forecast_percent / 100);

 

          	if( $pm_fct->pm_id > 0  ){


        //  	$amount = $focus_data_forecast_p[$pm_fct->pm_id];

          		$amount = $pm_fct->total * ($pm_fct->forecast_percent / 100) * ($focus_data_forecast_p[$pm_fct->comp_id] / 100);



          	echo "['$pm_fct->user_pm_name Forecast',";
          	echo $amount * ( $pm_fct->forecast_jan / 100 ).','.
          	$amount * ( $pm_fct->forecast_feb / 100 ).','.
          	$amount * ( $pm_fct->forecast_mar / 100 ).','.
          	$amount * ( $pm_fct->forecast_apr / 100 ).','.
          	$amount * ( $pm_fct->forecast_may / 100 ).','.
          	$amount * ( $pm_fct->forecast_jun / 100 ).','.
          	$amount * ( $pm_fct->forecast_jul / 100 ).','.
          	$amount * ( $pm_fct->forecast_aug / 100 ).','.
          	$amount * ( $pm_fct->forecast_sep / 100 ).','.
          	$amount * ( $pm_fct->forecast_oct / 100 ).','.
          	$amount * ( $pm_fct->forecast_nov / 100 ).','.
          	$amount * ( $pm_fct->forecast_dec / 100 ).',';
          	echo "],";


          	}


          }?>
// PM Project Manager Forecast
 

        ],
        selection: {enabled: true},
        type: 'bar',
        colors: {
            'Forecast': '#CC79A7',
            'Overall Sales': '#E69F00',
            'Focus Overall WIP': '#009E73',
            'Last Year Sales': '#AAAAAA',

            'Focus Shopfit Pty Ltd Forecast': '#CC79A7',
            'Focus Shopfit Pty Ltd Last Year': '#AAAAAA',
            'Focus Shopfit Pty Ltd WIP': '#009E73',
            'Focus Shopfit Pty Ltd': '#E69F00',

            'Focus Shopfit NSW Pty Ltd Forecast': '#CC79A7',
            'Focus Shopfit NSW Pty Ltd Last Year': '#AAAAAA',
            'Focus Shopfit NSW Pty Ltd WIP': '#009E73',
            'Focus Shopfit NSW Pty Ltd': '#E69F00',

<?php $project_manager_q = $this->user_model->fetch_user_by_role(3); $project_manager = $project_manager_q->getResult(); ?>
<?php foreach ($project_manager as $pm):

echo "'".$pm->user_first_name." ".$pm->user_last_name." Forecast': '#CC79A7',
      '".$pm->user_first_name." ".$pm->user_last_name." Last Year': '#AAAAAA',
      '".$pm->user_first_name." ".$pm->user_last_name." WIP': '#009E73',
      '".$pm->user_first_name." ".$pm->user_last_name."': '#E69F00',
      ";

endforeach; ?>

<?php $project_manager_q = $this->user_model->fetch_user_by_role(20); $project_manager = $project_manager_q->getResult(); ?>
<?php foreach ($project_manager as $pm):

echo "'".$pm->user_first_name." ".$pm->user_last_name." Forecast': '#CC79A7',
      '".$pm->user_first_name." ".$pm->user_last_name." Last Year': '#AAAAAA',
      '".$pm->user_first_name." ".$pm->user_last_name." WIP': '#009E73',
      '".$pm->user_first_name." ".$pm->user_last_name."': '#E69F00',
      ";

endforeach; ?>

 
        },
        types: {   'Forecast' : 'line',  





<?php foreach ($focus_indv_comp_forecast->getResultArray() as $indv_comp_forec): ?>


	<?php echo "'".$indv_comp_forec['company_name']." Forecast': 'line',"; ?>


	<?php endforeach; ?>
 
	<?php foreach ($focus_pm_forecast as $pm_fct){
		echo "'$pm_fct->user_pm_name Forecast': 'line',";
	}?>

        },
        groups: [ ['Focus Overall WIP','Overall Sales'],['Focus Shopfit Pty Ltd','Focus Shopfit Pty Ltd WIP'],['Focus Shopfit NSW Pty Ltd','Focus Shopfit NSW Pty Ltd WIP'],
 

<?php $project_manager_q = $this->user_model->fetch_user_by_role(3); $project_manager = $project_manager_q->getResult(); ?>
<?php foreach ($project_manager as $pm):
echo "['".$pm->user_first_name." ".$pm->user_last_name."','".$pm->user_first_name." ".$pm->user_last_name." WIP'],";
endforeach; ?>

<?php $project_manager_q = $this->user_model->fetch_user_by_role(20); $project_manager = $project_manager_q->getResult(); ?>
<?php foreach ($project_manager as $pm):
echo "['".$pm->user_first_name." ".$pm->user_last_name."','".$pm->user_first_name." ".$pm->user_last_name." WIP'],";
endforeach; ?>

],

        order: null,
      },
    tooltip: {
        grouped: true // false // Default true
    },
             bindto: "#chart",
bar:{ width:{ ratio: 0.5 }},
point:{ select:{ r: 6 }},
onrendered: function () { $('.loading_chart').remove(); },
//zoom: {enabled: true, rescale: true,extent: [1, 7]},
legend: { show: false },


axis: {x: {type: 'category', tick: {rotate: 0,multiline: false}, height: 0} },
tooltip: {
        format: {
     //     title: function (x) { return 'Data ' + x; },
            value: function (value, ratio, id) {
               // var format = id === 'data1' ? d3.format(',') : d3.format('$');
                var format = d3.format(',');
                
             	var mod_value = Math.round(value)
                return '$ '+format(mod_value);
            }
        } 

    }
    });

chart.select();
chart.hide();

setTimeout(function () {

	

	chart.show(['<?php echo $pm_name; ?>','<?php echo $pm_name; ?> WIP','<?php echo $pm_name; ?> Forecast','<?php echo $pm_name; ?> Last Year']);

 
}, 1000);	

chart.select();
	chart.show(['Current','Average','Last Year']);

$('select.chart_data_selection').on("change", function(e) {
	var data = $(this).val();
	
	if(data == 'Overall'){
		chart.hide(); 
		setTimeout(function () {
			chart.show(['Overall Sales','Forecast','Last Year Sales','Focus Overall WIP']);
		}, 500);
	}

	if(data == 'Outstanding'){
		chart.hide();
		setTimeout(function () {
			chart.show(['Focus Shopfit Pty Ltd Outstanding','Focus Shopfit NSW Pty Ltd Outstanding']);
		}, 500);	
	}


	if(data == 'Pm_Outstanding'){
		chart.hide();
		setTimeout(function () {
			chart.show(['Trevor Gamble Outstanding','Alan Liddell Outstanding','Stuart Hubrich Outstanding','Pyi Paing Aye Win Outstanding','Kristoff Kiezun Outstanding','Maintenance Manager Outstanding']);
		}, 500);	
	}



	if(data == 'FWA'){
		chart.hide(); 
		setTimeout(function () {
			chart.show(['Focus Shopfit Pty Ltd Forecast', 'Focus Shopfit Pty Ltd Last Year', 'Focus Shopfit Pty Ltd' ,'Focus Shopfit Pty Ltd WIP']);
		}, 500);
	}

	if(data == 'FNSW'){ 
		chart.hide(); 
		setTimeout(function () {
			chart.show(['Focus Shopfit NSW Pty Ltd Forecast', 'Focus Shopfit NSW Pty Ltd Last Year', 'Focus Shopfit NSW Pty Ltd' ,'Focus Shopfit NSW Pty Ltd WIP']);
		}, 500);
	}



<?php $project_manager_q = $this->user_model->fetch_user_by_role(3); $project_manager = $project_manager_q->getResult(); ?>
<?php foreach ($project_manager as $pm):

echo "if(data == '".$pm->user_first_name." ".$pm->user_last_name."'){ 
		chart.hide(); 
		setTimeout(function () {
			chart.show(['".$pm->user_first_name." ".$pm->user_last_name."','".$pm->user_first_name." ".$pm->user_last_name." WIP','".$pm->user_first_name." ".$pm->user_last_name." Forecast','".$pm->user_first_name." ".$pm->user_last_name." Last Year']);
		}, 500);
	}";

endforeach; ?>

<?php $account_manager_q = $this->user_model->fetch_user_by_role(20); $account_manager = $account_manager_q->getResult(); ?>
<?php foreach ($account_manager as $pm):

echo "

if(data == '".$pm->user_first_name." ".$pm->user_last_name."'){ 
		chart.hide(); 
		setTimeout(function () {
			chart.show(['".$pm->user_first_name." ".$pm->user_last_name."','".$pm->user_first_name." ".$pm->user_last_name." WIP','".$pm->user_first_name." ".$pm->user_last_name." Forecast','".$pm->user_first_name." ".$pm->user_last_name." Last Year']);
		}, 500);
	}

	";

endforeach; ?>
 
});
  



function bttnA(element_obj){
	var forecast_display = element_obj.getAttribute("id");
	chart.hide();
	setTimeout(function () {
			chart.show(['Outstanding', 'Focus Sales','Forecast','Last Year Sales']);
			element_obj.setAttribute("id", "hidden_forecast");
	}, 500);	
}


function bttnB(element_obj){
	var forecast_display = element_obj.getAttribute("id");
	chart.hide(); 
	setTimeout(function () {
			chart.show(['Overall Sales','Forecast','Last Year Sales']);
	}, 500);
}


 
function bttnC(element_obj){
	var forecast_display = element_obj.getAttribute("id");
	chart.hide(); 
	setTimeout(function () {
			chart.show(['Focus Shopfit Pty Ltd Forecast', 'Focus Shopfit Pty Ltd Last Year', 'Focus Shopfit Pty Ltd','Focus Shopfit Pty Ltd Outstanding','Focus Shopfit Pty Ltd Overall']);
	}, 500);
}


function bttnD(element_obj){
	var forecast_display = element_obj.getAttribute("id");
	chart.hide(); 
	setTimeout(function () {
			chart.show(['Focus Shopfit NSW Pty Ltd Forecast', 'Focus Shopfit NSW Pty Ltd Last Year', 'Focus Shopfit NSW Pty Ltd','Focus Shopfit NSW Pty Ltd Outstanding','Focus Shopfit NSW Pty Ltd Overall']);
	}, 500);
}



function bttnE(element_obj){
	var forecast_display = element_obj.getAttribute("id");
	chart.hide(); 
	setTimeout(function () {
			chart.show(['Alan Liddell', 'Stuart Hubrich', 'Pyi Paing Aye Win','Kristoff Kiezun','Maintenance Manager','Trevor Gamble']);
	}, 500);
}


 
var donuta = c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php   $this->dashboard->focus_top_ten_clients_pm_donut($assign_id); //focus_top_ten_clients_pm_donut ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_a",
	donut: {
		title: '<?php echo date('Y'); ?> Projects by Type'
	},tooltip: {
		format: {
			value: function (value, ratio, id) {
				var format = d3.format(',');
				var rounded_percent = Math.round( ratio * 1000 ) / 10;
				var mod_value = Math.round(value);
			return '$ '+format(mod_value)+' '+rounded_percent+'%';
		}
	} 
}
});



													 
 
var donuta = c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php   $this->dashboard->focus_top_ten_con_sup_pm_donut('2',$assign_id); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_b",
	donut: {
		title: '<?php echo date('Y'); ?> Projects by Type'
	},tooltip: {
		format: {
			value: function (value, ratio, id) {
				var format = d3.format(',');
				var rounded_percent = Math.round( ratio * 1000 ) / 10;
				var mod_value = Math.round(value);
			return '$ '+format(mod_value)+' '+rounded_percent+'%';
		}
	} 
}
});



 
var donuta = c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php    $this->dashboard->focus_top_ten_con_sup_pm_donut('3',$assign_id); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_c",
	donut: {
		title: '<?php echo date('Y'); ?> Projects by Type'
	},tooltip: {
		format: {
			value: function (value, ratio, id) {
				var format = d3.format(',');
				var rounded_percent = Math.round( ratio * 1000 ) / 10;
				var mod_value = Math.round(value);
			return '$ '+format(mod_value)+' '+rounded_percent+'%';
		}
	} 
}
});



 






var donutg = c3.generate({
	size: {
		height: 250,
		width: 250
	},data: {
		columns: [ <?php echo $this->dashboard->focus_projects_by_type_widget_pm($assign_id,'1'); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_prj_bt",
	tooltip: {
		format: {
			value: function (value, ratio, id) {
				var format = d3.format(',');
				var rounded_percent = Math.round( ratio * 1000 ) / 10;
				var mod_value = Math.round(value);
			return '$ '+format(mod_value)+' '+rounded_percent+'%';
		}
	} 
}
});






    </script>



<script type="text/javascript">

/*
	setTimeout(function() {
		$("select.chart_data_selection_emps").val('<?php echo $pm_name; ?>|<?php echo $user_id; ?>');


		chart_emply.show(['<?php echo $pm_name; ?> Annual Leave','<?php echo $pm_name; ?> Personal (Sick Leave)','<?php echo $pm_name; ?> Personal (Carers Leave)','<?php echo $pm_name; ?> Personal (Compassionate Leave)','<?php echo $pm_name; ?> Unpaid Leave','<?php echo $pm_name; ?> Public Holiday','<?php echo $pm_name; ?> RDO (Rostered Day Off)']);


	}, 10000);
*/


$(function() {

    		setTimeout(function () {

    			    $('.gnt_onload_hide').hide();


	}, 1000);

});


</script>


<!-- Modal -->
<div class="modal fade" id="add_data_chart" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Data</h4>
      </div>
      <form method="post" >
      <div class="modal-body pad-10">

      	<input type="text" class="form-control m-bottom-10 data_name" id="data_name" name="data_name" placeholder="Data Name" name="" value="">
      	<input type="text" class="form-control m-bottom-10 year" id="year" name="year" placeholder="Year" name="" value="">

      	<select name="data_color" class="form-control m-bottom-10">
      		<option value="#FD0000">Red</option>
      		<option value="#00CA00">Green</option>
      		<option value="#7008A8">Violet</option>
      		<option value="#FD7300">Orange</option>
      	</select>

      	<textarea placeholder="Values" class="form-control m-bottom-10 value_items" id="value_items" name="value_items"></textarea>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-success add_data_chart" value="Save changes">
      </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/maps.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/employee_map.js"></script>