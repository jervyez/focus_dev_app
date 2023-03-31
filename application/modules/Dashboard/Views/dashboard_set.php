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



		$project_manager = $this->dashboard_m->fetch_pms_year($current_year); // ****--___--***
		$project_manager_list = $project_manager->getResult();



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

 


if($pm_setter != '' && isset($pm_setter)){
	$pm_name = $pm_setter;

	  
echo '<script type="text/javascript">$("span#simulation_pm_name").text("'.$pm_name.'");</script>';
	 
}




if(isset($assign_id) && $assign_id != '' && $assign_id > 0){
	$user_id = $assign_id;
}else{
	 $user_id = $this->session->get('user_id');
}


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

<script src="<?php echo site_url(); ?>js/jquery.fn.gantt.js"></script>
<link href="<?php echo site_url(); ?>css/gant-style.css" type="text/css" rel="stylesheet"> 


<style type="text/css">body{background: #ECF0F5 !important;}</style>


<!-- title bar -->

<div class="container-fluid adv"  style="background: #ECF0F5;">


	<!-- Example row of columns -->
	<div class="row dash">				
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11 pad-0-imp no-m-imp">
			<div class="">

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


			<div class="clearfix pad-10">
				<div class="widget_area row pad-0-imp no-m-imp">


						<div class=" col-xs-12 box-widget pad-10">
							<div class="progress no-m progress-termometer  tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="Tells the progress made for reaching the target sales. WIP todate plus current invoiced progress claims." title="">							
								<span style="    position: absolute;    left: 25px;    top:16px; font-size: 12px;    color: #fff;">ALL</span>
								<div class="progress-bar progress-bar-danger active progress-bar-striped full_p tooltip-pb" title="" style="background-color: rgb(251, 25, 38); border-radius: 0px 10px 10px 0px;"></div> 
								<script type="text/javascript">
									$(window).load(function() {
										setTimeout(function() {
											$.ajax({
												'url' : site_url+'dashboard/pm_sales_widget/1',
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
							</div>
						</div>	



						  <div class=" col-xs-12 col-md-6 box-widget pad-10">
						 	<div class="progress no-m progress-termometer tooltip-enabled" id="progressBar_wa" data-html="true" data-placement="bottom" data-original-title="Tells the progress made for reaching the target sales. WIP todate plus current invoiced progress claims."  >
						 		<span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">WA</span>
						 	</div>
						 </div>
						 <script type="text/javascript">
						 	pre_load_module('#progressBar_wa','dashboard/focus_company_sep_thermo/5/Focus Shopfit Pty Ltd',3025);			 	
						 	setTimeout(function () { $('#progressBar_wa').append('<span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">WA</span>');}, 70200);
						 </script>
 	
						 <div class=" col-xs-12 col-md-6 box-widget pad-10">
						 	<div class="progress no-m progress-termometer tooltip-enabled" id="progressBar_nsw" data-html="true" data-placement="bottom" data-original-title="Tells the progress made for reaching the target sales. WIP todate plus current invoiced progress claims."  >
						 		<span style=" position: absolute;    left: 23px;   top:16px; font-size: 12px;   color: #fff;">NSW</span>
						 	</div>
						 </div>
						 <script type="text/javascript">
						 	pre_load_module('#progressBar_nsw','dashboard/focus_company_sep_thermo/6/Focus Shopfit NSW Pty Ltd',3050); 
						 	setTimeout(function () { $('#progressBar_nsw').append('<span style=" position: absolute;    left: 23px;   top:16px; font-size: 12px;   color: #fff;">NSW</span>');}, 70500);
						 </script>






						<div class=" col-xs-12 box-widget pad-10">
							<div class="progress no-m progress-termometer" id="progressBar_standing_area"><span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">ALL</span></div>
						</div>
						<script type="text/javascript">
						$(function() {
							pre_load_module('#progressBar_standing_area','dashboard/progressBar',90000);
						 	setTimeout(function () { $('#progressBar_standing_area').append('<span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">ALL</span>');}, 120000);
						 	});
						 </script>

						<div class=" col-xs-12 col-md-6 box-widget pad-10 tip_side_mod">
							<div class="progress no-m progress-termometer"  id="progressBar_standing_area_wa"><span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">WA</span></div>
						</div>
						<script type="text/javascript">
						$(function() {
							pre_load_module('#progressBar_standing_area_wa','dashboard/progressBar/5/1',120000);
						 	setTimeout(function () { $('#progressBar_standing_area_wa').append('<span style=" position: absolute;    left: 25px;   top:16px; font-size: 12px;   color: #fff;">WA</span>');}, 150000);
						 	});
						 </script>

						<div class=" col-xs-12 col-md-6 box-widget pad-10 tip_side_mod">
							<div class="progress no-m progress-termometer"  id="progressBar_standing_area_nsw"><span style=" position: absolute;    left: 23px;   top:16px; font-size: 12px;   color: #fff;">NSW</span></div>
						</div>
						<script type="text/javascript">
						$(function() {
							pre_load_module('#progressBar_standing_area_nsw','dashboard/progressBar/6/1',150000);
						 	setTimeout(function () { $('#progressBar_standing_area_nsw').append('<span style=" position: absolute;    left: 23px;   top:16px; font-size: 12px;   color: #fff;">NSW</span>');}, 180000);
						 	});
						 </script>



						<style type="text/css">
						.tip_side_mod .tooltip.right, .tip_side_mod .tooltip.left{
							width: 200px !important;
						}
						</style>

						<style type="text/css">.progress .tooltip{ display: none !important; visibility: hidden !important;  }</style>

				 

						<!-- ************************ -->
  
						
						<div class="clearfix"></div>

						<!-- ************************ -->

<!-- 						<div class="col-md-6 col-lg-3 col-sm-6 col-xs-12 box-widget pad-10 hide">
							<div class="widget wid-type-0 small-widget">
								<div class="box-area clearfix">
									<div class="widg-icon-inside col-xs-3 violet" ><div id="" class=""><i class="fa  fa-code text-center fa-3x"></i></div></div>
									<div class="widg-content col-xs-9 clearfix fill violet">
										<div class="pad-5">
											<div class=" " id=""><p>Widget <span class="pull-right"><?php echo date('Y'); ?></span></p></div>
											<hr class="" style="margin: 5px 0px 0px;">
											<div class="pad-top-5" id="">
												<p class="value"> <strong> &nbsp; </strong></p>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div> -->

						<!-- ************************ -->



				

							<!-- ******** Project Completion Calendar **************** -->
						
						<div class="clearfix"></div>


 

						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head pad-5 fill">
									<strong>Project Completion Calendar</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the on-going projects completion dates ordered chronologically."></i></span>


									<select class="pull-right input-control input-sm chart_data_selection_pmsgp" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 210px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
										<option value="ongoingwip">Overall</option>

										<?php foreach ($focus_company as $key => $value): ?>   
										<?php    if($value->company_id != 3197):   ?>     


											<option value="ongoingwip_<?php echo strtolower($value->company_id); ?>">Focus <?php echo $value->company_name; ?></option>
										<?php endif; ?>
										<?php endforeach; ?>


										<?php  
											foreach ($project_manager_list as $pmj ) {


												$pm_name_sltc = 'ongoingwip_'.strtolower( str_replace(' ','_',  $pmj->user_first_name   )   ).'_'.strtolower( str_replace(' ','_',  $pmj->user_last_name   )   );  


if($pmj->user_id != 29):
												echo '<option class="optn_pm_slct" value="'.$pm_name_sltc.'">'.$pmj->user_first_name.' '.$pmj->user_last_name.'</option>';
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
										});

									</script>
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix" id="project_completion_calendar_area_scrol" style="    overflow: auto;    height: 457px;  ">	
										<?php //echo $this->estimators->load_calendar_planner(); ?>
 


<div class="gantt_chart_grp" id="ongoingwip"></div>
<script>
        $(function() {
            $("#ongoingwip").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress(); ?> ],
                navigate: "scroll",
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




<?php foreach ($focus_company as $key => $value): ?>   

<?php if($value->company_id == 3197): ?>
                
<?php /*
<div class="gantt_chart_grp gnt_onload_hide  mainte" id="ongoingwip_<?php echo strtolower($value->company_id); ?>" ></div>
<script>
        $(function() {
            $("#ongoingwip_<?php echo strtolower($value->company_id); ?>").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress(29); ?> ],
                navigate: "scroll",
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
*/ ?>

<?php  else: ?>                 
                          

<div class="gantt_chart_grp gnt_onload_hide" id="ongoingwip_<?php echo strtolower($value->company_id); ?>" ></div>
<script>
        $(function() {
            $("#ongoingwip_<?php echo strtolower($value->company_id); ?>").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress('',' AND `project`.`focus_company_id` = "'.$value->company_id.'" '); ?> ],
                navigate: "scroll",
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

<?php endif; ?>









<?php endforeach; ?>




  

<?php  foreach ($project_manager_list as $pm ): ?>
			
<?php if($pm->user_id != 29): ?>
			<?php $pm_name_grp = strtolower( str_replace(' ','_',  $pm->user_first_name   )   ).'_'.strtolower( str_replace(' ','_',  $pm->user_last_name   )   ); ?>



<div class="gantt_chart_grp gnt_onload_hide" id="ongoingwip_<?php echo $pm_name_grp; ?>"  ></div>
<script>

 
    $(function() {
            $("#ongoingwip_<?php echo $pm_name_grp; ?>").gantt({
                source: [ <?php echo $this->dashboard->list_projects_progress($pm->project_manager_id); ?>  ],
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


<?php endif; ?>

<?php endforeach; ?>


 



  



									</div> 
								</div>

							</div>
						</div>



							<!-- ******** Project Completion Calendar **************** -->







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


 


<select class="pull-right input-control input-sm chart_labour_schedule" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 210px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
<option value="Overall ">Overall Focus</option>
	<?php foreach ($focus_company as $key => $value): ?>


<?php $com_name_lbsl = trim($value->company_name); ?>  
<?php $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl); ?>


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
setTimeout(function () {
	chart_labourSched.show(['Overall Last Year','Overall WIP','Overall Quote']);
}, 1000);	


$('.chart_labour_schedule').change(function(){
	var view_chart = $(this).val();
chart_labourSched.hide();

setTimeout(function () {
	chart_labourSched.show([view_chart+'Last Year',view_chart+'WIP',view_chart+'Quote']);
}, 500);	


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


	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head pad-5 fill">
									<strong>Quote Deadline Calendar</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Lists the quoted projects in completion date order chronologically. The deadline day is shown as a target in the calendar."></i></span>

									<select class="pull-right input-control input-sm chart_data_selection_est" style="background:#AAAAAA; padding: 0;margin: -8px 0 0 0;width: 120px;height: 35px; border-radius: 0;border: 0;border-bottom: 1px solid #999999;">
										<option value="all">Overall</option>
										<?php
											foreach ($estimator_list as $est ) {
												if($est->user_first_name != ''){
													if($est->user_first_name != 'Nycel'){
														$est_name_list =  str_replace(' ','_',strtolower($est->user_first_name) );
														echo '<option value="'.$est_name_list.'">'.$est->user_first_name.'</option>';
													}
												}
											}
										?>
									</select>
								</div>

								<div class="box-area clearfix row pad-right-10 pad-left-10">									
									<div class="widg-content col-md-12 col-xs-12 clearfix" style="    overflow: auto;    height: 465px;     overflow-y: hidden; ">	

										<p class="qdc_loading_text" ><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
										<?php //echo $this->estimators->load_calendar_planner(); ?>
<script src="<?php echo site_url(); ?>js/jquery.fn.gantt.js"></script>
<link href="<?php echo site_url(); ?>css/gant-style.css" type="text/css" rel="stylesheet"> 

<div class="gantt" id="est_deadline_all"></div>
<script>
        $(function() {
            $("#est_deadline_all").gantt({
                source: [ <?php echo $this->estimators->list_deadlines(); ?> ],
                navigate: "buttons",
                scale: "days",
                maxScale: "days",
                minScale: "days",
                itemsPerPage: 14,
                useCookie: true,
                scrollToToday: true,
                onRender: function() {
                //	$('#est_deadline_all').hide();
                    //$('[data-toggle="tooltip"]').tooltip(); 
                }
            }); 
        });
</script>

<?php foreach ($estimator_list as $est ): ?>
	<?php if($est->user_first_name != ''): ?>
		<?php if($est->user_first_name != 'Nycel'): ?>

<?php $est_name_list =  str_replace(' ','_',strtolower($est->user_first_name) );  ?>
<?php $est_id_list = $est->project_estiamator_id;  ?>

<div class="gantt" id="est_deadline_<?php echo $est_name_list; ?>"></div>
<script>
        $(function() {
            $("#est_deadline_<?php echo $est_name_list; ?>").gantt({
                source: [ <?php echo $this->estimators->list_deadlines($est_id_list); ?>{ values: [{from: "<?php echo date('Y/m/d', strtotime('- 5 days')); ?>", to: "<?php echo date('Y/m/d', strtotime('+ 20 days')); ?>",  customClass: "hide"},{from: "<?php echo date('Y/m/d'); ?>", to: "<?php echo date('Y/m/d'); ?>" ,customClass: "curr_date"}]}, ],
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

	setTimeout(function () {
		$('p.qdc_loading_text').hide();
	}, 5000);



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
								<div class="widg-head  box-widg-head fill pad-5"><strong>Up-coming Deadline</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells the number of days when the next deadline is occurring."></i></span> <span class="badges pull-right"> <span class="pull-right"><?php echo date('Y'); ?></span> </span></div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">


 
										<div class="pad-10">

											<script type="text/javascript"> pre_load_module('#up_coming_deadline_area','dashboard/estimators/up_coming_deadline',15000); </script>

											<div class="clearfix center knob_box pad-10 small-widget" id="up_coming_deadline_area">
												<p style="margin:-15px -20px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
												<?php //echo $this->estimators->up_coming_deadline(); ?>
											</div>
											<style type="text/css">.knob_box canvas{width: 100% !important;}.knob{font-size: 75px !important; }</style>


										</div>							
									</div>
								</div>
							</div>
						</div>





						<!-- ************************ -->


						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled" style="height: 501px;">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><strong>Project Estimator Quotes</strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Tells each estimators current quoted projects for the year and compares to last year."></i></span> <span class="badges pull-right"> <span class="pull-right"><?php echo date('Y'); ?></span> </span></div>
								<div class="box-area clearfix">
									<div class="widg-content clearfix">
										<script type="text/javascript"> pre_load_module('#estimators_quotes_completed_area','dashboard/estimators/estimators_quotes_completed',15500); </script>
										<div class="" id="estimators_quotes_completed_area">
											<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
											<?php  //$status_forecast = $this->estimators->estimators_quotes_completed(); ?>
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


						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 box-widget pad-10 pie_toggle_custom_a">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><i class="fa fa-tags  text-center "></i> <strong>Projects by Type </strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="The total project costs per project category, only completed projects of the current year to date are included."></i></span><span class="pull-right"> <?php echo date('Y'); ?></span></div>
								<div class="box-area clearfix" style="height:320px;">
									<div class="widg-content clearfix">

										<div id="" class="pad-5" style="">
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 box-widget pie_display_dyn">
												<div class="" id="donut_prj_bt" style="text-align: center;"></div>
											</div>
											<script type="text/javascript"> pre_load_module('#focus_projects_by_type_widget_area_x','dashboard/focus_projects_by_type_widget',16000); </script>
									<div id="" class="text_display_dyn_tq" style="display:none; float:right;">
																				<div class="col-xs-12 box-widget m-top-10 " id="focus_projects_by_type_widget_area_x">
																					<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
																					<?php //echo $this->dashboard->focus_projects_by_type_widget(); ?>
																				</div>
									
									</div>
										</div>
										<p class="m-top-10 pad-top-10 clearfix"><div class="clearfix"></div><button class="btn btn-xs btn-primary toggle_pie_text m-top-10 m-left-15" style="display:none;">Toggle Display</button></p>

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
<!-- 

						<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 box-widget pad-10 hide">
							<div class="widget wid-type-b widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5"><i class="fa fa-tags  text-center "></i> <strong>Pie View </strong></div>
								<div class="box-area clearfix" style="height:320px;">
									<div class="widg-content clearfix">

									</div>
								</div>
							</div>
						</div>
 -->


						<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 box-widget pad-10 pie_toggle_custom_b">
							<div class="widget wid-type-e widg-head-styled">
								<div class="  fill box-widg-head pad-right-10 pad-left-10 m-left-15 pull-right pad-top-3 m-3">
									<a href="#clients_mtnc" class="view_main_swtch btn btn-xs btn-default" role="tab" data-toggle="tab"  >View Maintenance</a> &nbsp;
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
												<script type="text/javascript"> pre_load_module('#focus_top_ten_clients_area','dashboard/focus_top_ten_clients',16500); </script>
												<div id="focus_top_ten_clients_area" class="" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_clients(); ?>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane fade in" id="contractors">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_b" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_area_b','dashboard/focus_top_ten_con_sup/2',17000); </script>
												<div id="focus_top_ten_con_sup_area_b" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup('2'); ?>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane fade in" id="suppliers">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_c" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_area_c','dashboard/focus_top_ten_con_sup/3',17500); </script>
												<div id="focus_top_ten_con_sup_area_c" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup('3'); ?>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane fade in" id="clients_mtnc">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_d" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_clients_mn_area','dashboard/focus_top_ten_clients_mn',18000); </script>
												<div id="focus_top_ten_clients_mn_area" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_clients_mn(); ?>
												</div>
											</div>
										</div>
										
										<div role="tabpanel" class="tab-pane fade in" id="contractors_mtnc">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_e" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_mn_area_b','dashboard/focus_top_ten_con_sup_mn/2',18500); </script>
												<div id="focus_top_ten_con_sup_mn_area_b" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup_mn('2'); ?>
												</div>
											</div>
										</div>
										
										<div role="tabpanel" class="tab-pane fade in" id="suppliers_mtnc">
											<div id="" class="center col-lg-5 clearfix">
												<div class="" id="donut_f" style="text-align: center;"></div>
											</div>
											<div id="" class="col-lg-7 clearfix">
												<script type="text/javascript"> pre_load_module('#focus_top_ten_con_sup_mn_area_c','dashboard/focus_top_ten_con_sup_mn/3',19000); </script>
												<div id="focus_top_ten_con_sup_mn_area_c" class="clearfix" style="height: 300px; overflow: auto;">
													<p style=" padding: 2px 0px 3px;"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i> Loading...</p>
													<?php //echo $this->dashboard->focus_top_ten_con_sup_mn('3'); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>





						<div class="clearfix"></div>


						<!-- ************************ -->
 




						<!-- ************   LEAVE CHART   ************ -->

						<div class="clearfix"></div>






						<!-- ************   LEAVE CHART   ************ -->
						<?php $custom_q = ''; //$user_id = $this->session->get('user_id'); ?>

						<div id="" class="hide hidden">
							
							<?php 

							$q_leave_types = $this->user_model->fetch_leave_type();
							$leave_types = $q_leave_types->getResult();

							$added_data = new StdClass();
							$added_data->{"leave_type_id"} = '0';
							$added_data->{"leave_type"} = 'Public Holiday';
							$added_data->{"remarks"} = '';



							$leave_totals =  $this->dashboard->get_count_per_week(2,$current_year,$user_id);
							$last_year_leave = $this->dashboard->get_count_per_week(2,$last_year,$user_id);


								$user_list_q = $this->user_model->list_user_short(" AND `users`.`user_id` = '$user_id' ");
								$user_list= $user_list_q->getResult();

	

							?>
						</div>

						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 box-widget pad-10">
							<div class="widget wid-type-0 widg-head-styled">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head box-widg-head fill  pad-5">
									<strong>Employee Leave Chart : <?php echo date('Y'); ?></strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Lists every months week number and displays how many leaves taken place, the chart can be broken down into individual employees."></i></span>

							
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
														<p class="pointer leave_type_selection tooltip-enabled type_label_<?php echo $leave_data_id; ?>" 
															title="" data-html="true" data-placement="top" 
															data-original-title="Total Applied: <?php echo $leave_totals[$leave_data_id]; ?><br />Last Year:  <?php echo $last_year_leave[$leave_data_id]; ?>" id="<?php echo $leave_name_value; ?>" style="color: #fff;   font-size: 12px;  text-align: center;"><?php echo $leave_name_value; ?></p>
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


	
          	echo $this->dashboard->get_count_per_week(3,$current_year,$user_id);

      


           ?>

 


],
selection: {enabled: true},
type: 'bar', 
 
colors: {
	//*'Average': '#FF7F0E',
	//'Current': '#2CA02C',
//	'Last Year': '#9467BD',

	<?php /*foreach ($leave_types as $leave_data): ?>
		'<?php echo $this->session->get("user_first_name")." ".$this->session->get("user_last_name")." ".$leave_data->leave_type; ?>': '<?php echo $color_leave_type[$leave_data->leave_type_id];  ?>',
	<?php endforeach; */?>

	
	<?php foreach ($user_list as $key => $value): ?> 
		<?php foreach ($leave_types as $leave_data): ?>
		'<?php echo $value->user_first_name.' '.$value->user_last_name." ".$leave_data->leave_type; ?>': '<?php echo $color_leave_type[$leave_data->leave_type_id]; ?>',
	<?php endforeach; ?>
<?php endforeach; ?>


        },

groups: [



[
	<?php /*foreach ($leave_types as $leave_data): ?>
		'<?php echo $this->session->get("user_first_name")." ".$this->session->get("user_last_name")." ".$leave_data->leave_type; ?>',
	<?php endforeach;*/ ?>


	 
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
				//chart_emply.hide(['Overall Annual Leave','Overall Personal (Sick Leave)','Overall Personal (Carers Leave)','Overall Personal (Compassionate Leave)','Overall Unpaid Leave','Overall Public Holiday','Overall RDO (Rostered Day Off)']);
	



</script>
						<!-- ************************ -->

						<!-- ************   LEAVE CHART   ************ -->

						<!-- ************   LEAVE CHART   ************ -->


						<!-- ************************ -->

						<!-- ************   LEAVE CHART   ************ -->



						<div class="clearfix"></div>

						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 box-widget pad-10">
							<div class="widget wid-type-a widg-head-styled ">
								<div class="reload-widget-icon pull-right m-top-8 m-right-10 m-left-5 hide hidden"><i class="fa fa-spin fa-refresh"></i></div>
								<div class="widg-head fill box-widg-head pad-5">
									<i class="fa fa-map-marker fa-lg"></i> 
									<strong>On-Going Projects in Australia</strong> <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Displays the map of Australia and plots down the location of all on-going projects."></i></span>
								</div>
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
									<strong>Focus Employee Locations</strong>  <span class="pointer"><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Displays a map where the office of each focus employees are located."></i></span>
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


<?php //var_dump($focus_pm_forecast); ?>


<?php echo view('assets/logout-modal'); ?>

<script>
	var bttn_click_mtc = 0;
	$('.view_main_swtch').click(function(){
		var text = $(this).text();
		//$(this).parent().removeClass('active');

		if(bttn_click_mtc > 0){

			if(text == 'View Maintenance'){


				$(this).attr("href",'#clients_mtnc');
				$(this).attr("id",'#clients-tab_b');

				$(this).text('Return');

				$('a.tab_btn_dhb').hide();
				$('a.tab_btn_dhb_mntnc').show();
				$('#clients-tab_b').parent().addClass('active');

			}else{
				

				$(this).attr("href",'#clients');
				$(this).attr("id",'#clients-tab_a');

				$(this).text('View Maintenance');

				$('a.tab_btn_dhb').show();
				$('a.tab_btn_dhb_mntnc').hide();
				$('#clients-tab_a').parent().addClass('active');

			}


		}else{

				$(this).attr("href",'#clients_mtnc');
				$(this).attr("id",'#clients-tab_b');

				$(this).text('Return');

				$('a.tab_btn_dhb').hide();
				$('a.tab_btn_dhb_mntnc').show();
				$('#clients-tab_b').parent().addClass('active');
		}

		bttn_click_mtc++;
		//$(this).parent().removeClass('active');


	//	alert(text);
});

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
	if($pm_sales_data['user_pm_name'] == 'Maintenance Manager' ){
		echo "['Maintenance Manager',".$maintenance_current_sales['rev_jan'].",".$maintenance_current_sales['rev_feb'].",".$maintenance_current_sales['rev_mar'].",".$maintenance_current_sales['rev_apr'].",".$maintenance_current_sales['rev_may'].",".$maintenance_current_sales['rev_jun'].",".$maintenance_current_sales['rev_jul'].",".$maintenance_current_sales['rev_aug'].",".$maintenance_current_sales['rev_sep'].",".$maintenance_current_sales['rev_oct'].",".$maintenance_current_sales['rev_nov'].",".$maintenance_current_sales['rev_dec']."],";
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


// PM Project Manager Forecast
<?php foreach ($focus_pm_forecast as $pm_fct){

	if($pm_fct->pm_id == 29){
		$amount = $amount_for_maintenance;
	}else{
		$amount = $focus_data_forecast_p[$pm_fct->comp_id] * ($pm_fct->forecast_percent / 100);
	}

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
}?>
// PM Project Manager Forecast ***


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


            //Focus Shopfit Pty Lt
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

// dashboard->sales_widget()
 	
    	



chart.select();
chart.hide();

setTimeout(function () {
	chart.show(['Overall Sales','Forecast','Last Year Sales','Focus Overall WIP']);
}, 1000);	

chart.show(['Current','Average','Last Year']);






$('select.chart_data_selection').on("change", function(e) {
	var data = $(this).val();
	
	if(data == 'Overall'){
		chart.hide(); 
		setTimeout(function () {
			chart.show(['Overall Sales','Forecast','Last Year Sales','Focus Overall WIP']);
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




// use this donut

var donuta = c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php $this->dashboard->focus_top_ten_clients_donut(); ?> ],
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


var donutb = c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php echo $this->dashboard->focus_top_ten_con_sup_donut('2'); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_b",
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


var donutc= c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php echo $this->dashboard->focus_top_ten_con_sup_donut('3'); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_c",
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

var donutd= c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php echo $this->dashboard->focus_top_ten_clients_mn_donut(); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_d",
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



var donute= c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php echo $this->dashboard->focus_top_ten_con_sup_mn_donut(2); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_e",
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



var donutf= c3.generate({
	size: {
		height: 300,
		width: 300
	},data: {
		columns: [ <?php echo $this->dashboard->focus_top_ten_con_sup_mn_donut(3); ?> ],
		type : 'pie',
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	},
	legend: {
		show: false //hides label
	},
	bindto: "#donut_f",
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









var donutg = c3.generate({
	size: {
		height: 250,
		width: 250
	},data: {
		columns: [ <?php echo $this->dashboard->focus_projects_by_type_widget(1); ?> ],
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




$(function() {

    		setTimeout(function () {

    			    $('.gnt_onload_hide').hide();


	}, 1000);

});



</script>
 







<!-- maps api js -->

<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyDs1g6kHxbVrkQe7e_CmR6MsfV_3LmLSlc"></script>

<script type="text/javascript">
	var data = { "locations": <?php echo $this->dashboard->focus_get_map_locations(); ?>};	
	var emp_data = { "locations": <?php echo $this->dashboard->emp_get_locations_points(); ?>};
</script>

<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/markerclusterer_packed.js"></script>

<!--[if IE]><script type="text/javascript" src="<?php echo site_url(); ?>js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo site_url(); ?>js/jquery.knob.min.js"></script>
<script type="text/javascript">$('knob').trigger('configure', {width:100}); </script>

<!-- maps api js -->



<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/maps.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>js/maps/employee_map.js"></script>