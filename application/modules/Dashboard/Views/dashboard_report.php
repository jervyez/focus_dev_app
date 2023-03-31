<?php use App\Modules\Dashboard\Controllers\Dashboard; ?>
<?php $this->dashboard = new Dashboard(); ?>

<link href="<?php echo site_url(); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">        
<link href="<?php echo site_url(); ?>css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo site_url(); ?>css/main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="<?php echo site_url(); ?>js/jquery-2.1.3.js"></script>
<script src="<?php echo site_url(); ?>js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

<link href="<?php echo site_url(); ?>css/c3.css" rel="stylesheet" type="text/css">
<script src="<?php echo site_url(); ?>js/c3/d3.js" charset="utf-8"></script>
<script src="<?php echo site_url(); ?>js/c3/c3.js"></script> 


<script type="text/javascript" src="<?php echo site_url(); ?>js/jquery.knob.min.js"></script>
<script type="text/javascript"> $(function() {  $(".knob").knob({'width':200,'height':200,'thickness':.1}); });</script> 
 

<div class="main_print">

<script src="<?php echo site_url(); ?>js/vendor/bootstrap.min.js"></script>


<style type="text/css">
@media all {


  .mr_comp_name{
    width: 70% !important;
    float: left;
    
    padding: 4px;
    border-top: 1px solid #eee;
  }


  .mr_comp_val{
    width: 15% !important;
    float: left;
    padding: 4px;
        border-top: 1px solid #eee;
  }



  body,html{
    margin-top: 0 !important;
    padding-top: 0 !important;

    margin-bottom: 0 !important;
    padding-bottom: 0 !important; 
 
height: 99% !important; 

  }


  .table_style_a td{
    padding: 4px;
    font-size: 16px !important;
  }

  .print_bttn{
      position: fixed;
      background: green;
      color: white;
      font-size: 17px;
      padding: 2px 10px;
      top: 5px;
      border-radius: 10px;
      left: 15px;
      z-index: 10000;
  }

  .print_bttn:hover{
    color:#fff;
  }

  .table_style_a tr{
    border-bottom: 1px solid #eee;
  } 


  .col-md-8{

    padding:2px;
  }

  .col-md-1{

    padding:2px;
  }

  .col-md-3{

    padding:2px;
  }


.col-sm-12{

  float: left;
  width: 100%;
  display: none;
  visibility: hidden;
}


  .col-md-8{
    float: left;
    width: 60%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }

  .col-md-1{
    float: left;
    width: 15%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }

  .col-md-3{
    float: left;
    width: 20%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }


  .donut_style div, .donut_style div p {
    margin: 0 auto;
    display: block !important;
    background-color: transparent !important;
    color: #555 !important;
  }

}
@media print {

  .donut_style div, .donut_style div p {
    margin: 0 auto;
    display: block !important;
    background-color: #fff !important;
    color: #555 !important;
  }

  table,tr,td{
    margin: 0;
    padding: 0;
  }


 


  body,html{
    margin-top: 0 !important;
    padding-top: 0 !important;

    margin-bottom: 0 !important;
    padding-bottom: 0 !important; 
 
height: 99% !important; 

  }

  .table_style_a tr{
    border-bottom: 1px solid #eee;
  } 
  .table_style_a td{
    padding: 4px;
    font-size: 14px !important;
  }


  .col-md-8{
    float: left;
    width: 75%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }

  .col-md-1{
    float: left;
    width: 10%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }

.col-sm-12{

  float: left;
  width: 100%;
  display: none;
  visibility: hidden;
}
  .col-md-3{
    float: left;
    width: 15%;
    display: block;
    padding: 3px !important;
    border-top: 1px solid #eee;
  }

  table{
    font-size: 12px;
  }

  .main-content{
    margin: 0 !important;
    padding: 0 !important;
  }
}


.top_20 .col-sm-12{
  display: none !important;
  visibility: hidden !important;
  width: 0 !important;
  height:  0 !important;
}

.top_20 .col-md-3,.top_20 .col-md-2{
width: 15% !important;
    float: left;
    padding: 4px;
    border-top: 1px solid #eee;
}

.top_20 .col-sm-8{
  width: 70% !important;
    float: left;
    padding: 4px;
    border-top: 1px solid #eee;
}

</style>

<a href="#" onclick="this.parentNode.removeChild(this); window.print(); window.close();" class="print_bttn print_me_now">Print Now!</a>


<?php //var_dump($user_details);   company_details?>

<?php 
  $months = array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
  $forecast_values = array();
  $sales_values = array();
  $old_sales_values = array();

  $forecasted_amount = 0;
?>


<?php
$company_forecasted_amount = 0;
 



if($is_company == 1){


  $comp_id_set = $company_details['company_id'];

  $user_details['user_profile_photo'] = '';
  $user_details['user_first_name'] = $company_details['company_name']; 
  $user_details['user_last_name'] = '';
  $user_details['user_focus_company_id'] = $company_details['company_id'];




  $int_comp_id = $company_details['company_id'];

if( isset($focus_comp_forecast[$company_details['company_id']]) ){
    $company_forecasted_amount = $focus_comp_forecast[$company_details['company_id']];
 }else{
   $company_forecasted_amount = 0;
 }

//var_dump($company_forecasted_amount);




  $pm_forecast['forecast_percent'] = 100;



}elseif($is_company == 2){
// OVERALL HERE

 // var_dump( $pm_forecast);

  $user_details['user_profile_photo'] = '';
  $user_details['user_first_name'] = 'Overall Focus Company';
  $user_details['user_last_name'] = '';
  $comp_id_set = '';
  $user_details['user_focus_company_id'] = 0;
 
  $company_forecasted_amount = $pm_forecast['total'];
  $pm_forecast['forecast_percent'] = 100;

}else{

 // echo "<p>solo</p>";

  $user_details_set = $user_details['user_focus_company_id'] ?? 0;

  if(  array_key_exists( $user_details_set , $focus_comp_forecast)  ){


//echo "<p> ___ ** ___</p>";
 //   var_dump($q_focus_data_forecast_p);

 //   echo "<p>$q_focus_data_forecast_p->total ***</p>";

//echo "<p> ___ ** ___</p>";
// var_dump($q_focus_data_forecast_p);  

//echo "<p>--</p>"; 


  //  $company_forecasted_amount = $q_focus_data_forecast_p->total * 
// q_focus_data_forecast_p
//focus_comp_forecast

// $company_forecasted_amount = $q_focus_data_forecast_p->total * ($q_focus_data_forecast_p->forecast_percent/100);

    $company_forecasted_amount = $focus_comp_forecast[$user_details['user_focus_company_id']];
  }

}

 //echo "<p>00 ";
// var_dump($pm_forecast);
 //echo " 55</p>";

// echo "<p>------</p>";
// var_dump($pm_forecast);

// echo "<p>------</p>";

foreach ($months as $key => $value) {



$init_pm_forecst = strval('forecast_'.$value);



 //echo "<p>forecast month-> ".$init_pm_forecst."___ </p>";

 //var_dump($pm_forecast);

 //echo "<p>------</p>";

 if(is_array ($pm_forecast)){
  // if($is_company > 0 ){
   // $forecasted_amount = $company_forecasted_amount * ( $pm_forecast['forecast_percent']/100 ) *  0;
   //   echo "<p>XXX  $forecasted_amount</p>";
 //  }else{
     
    //  echo "<p>??  $forecasted_amount</p>";
 //  }

}
// var_dump($company_forecasted_amount);


if(isset($pm_forecast['forecast_'.$value])){

  if($is_company == 1){
    $forecasted_amount = $company_forecasted_amount  *  ($pm_forecast['forecast_'.$value]/100);
  }elseif($is_company == 2){
    $forecasted_amount = $company_forecasted_amount  *  ($pm_forecast['forecast_'.$value]/100);
  }else{
    $forecasted_amount = $company_forecasted_amount * ( $pm_forecast['forecast_percent']/100 ) *  ($pm_forecast['forecast_'.$value]/100);
  }

}

$set_actual_sales = $pm_actual_sales['rev_'.$value] ?? 0;
$set_pm_last_year_sales = $pm_last_year_sales['rev_'.$value] ?? 0;

  array_push($forecast_values, round($forecasted_amount));
  array_push($sales_values, round($set_actual_sales));
  array_push($old_sales_values, round($set_pm_last_year_sales));

}
$forecast_costs = implode(',', $forecast_values);
$sales_costs = implode(',', $sales_values);
$old_sales_costs = implode(',', $old_sales_values);


$wip_values = array();

for ($i=1; $i<13; $i++){
  $month = $i;
  if($i == 12){
    $wip_date_a = "1/$month/$report_year";
    $wip_date_b = "1/1/".($report_year+1);

  }else{
    $wip_date_a = "1/$month/$report_year"; 
    $wip_date_b = "1/".($month+1)."/$report_year";
  }


  if($is_company==1){
    $wip_val_raw = $this->dashboard->get_wip_value_permonth($wip_date_a,$wip_date_b,$comp_id_set);
  }elseif($is_company==2){
    $wip_val_raw = $this->dashboard->get_wip_value_permonth($wip_date_a,$wip_date_b);
  }else{
    $wip_val_raw = $this->dashboard->get_wip_value_permonth($wip_date_a,$wip_date_b,$pm_id,1);
  }

  array_push($wip_values,  $wip_val_raw);
}


$wip_costs = implode(',', $wip_values);



?>

<table width="100%" style="">
  <tr>
    <td valign="top" width="33%" valign="middle"><p><br /><br /><br /><img src="<?php echo site_url(); ?>img/focus-logo-print.png"></p></td>
   

    <td valign="top" width="33%" valign="middle">
      <p style="text-align:center; font-size: 32px !important; font-weight: bold;"><br /> Progress Report Calendar: <?php echo $report_year; ?> </p>

      <?php if($is_company > 0): ?>   
        <p style="text-align:center; font-size: 22px; font-weight: bold;"> <?php echo $user_details['user_first_name']; ?></p>
      <?php endif; ?>

    </td>
    <td valign="top" width="33%" valign="middle">
      <?php if($is_company == 0): ?>
        <div style="float:right; text-align:center;">

          <div style="height: 100px;    width: 100px;    border-radius: 50px;    overflow: hidden;    margin-bottom: 10px;">
            <img src="<?php echo site_url(); ?>uploads/users/<?php echo $user_details['user_profile_photo'] ?? 'no_photo.jpg'; ?>" style="border-radius: 6px; display: block;    max-width: 100%;    height: auto; vertical-align: middle;">
          </div>
          <?php $set_user_fname = $user_details['user_first_name'] ?? ''; ?>
          <?php $set_user_lname = $user_details['user_last_name'] ?? ''; ?>
          <p><strong><?php echo $set_user_fname.' '.$set_user_lname; ?></strong></p>
        </div>
      <?php endif; ?>
    </td>
  </tr>
</table>
 
<p><hr /></p>

 <table width="100%" style="">
<tr>
  <td valign="top" width="20%" ><div id="chart_b" class=""></div></td>
  <td valign="top" width="30%" ><div style="padding-left:15px;"><p style="text-align:center;"><strong>Average Final Invoice Days</strong></p>

  <div class="donut_style" style="margin:0 auto;width: 100%;display: block;position: relative;">
<div id="" class=" d_set_cstom"  style="text-align: center;padding-bottom: 90px;">
    <?php if($is_company == 1): ?>

      <?php echo $this->dashboard->average_date_invoice($pm_id,$report_year); ?>
    <?php elseif($is_company == 2): ?>


      <?php echo $this->dashboard->average_date_invoice('',$report_year); ?>
    <?php else: ?>


      <?php echo $this->dashboard->average_date_invoice_pm($pm_id,$report_year); ?>
    <?php endif; ?>


</div>


<style type="text/css">
.d_set_cstom div{
  background-color: none !important;
  border:none !important;

}
  


.d_set_cstom  .yr_st_val{
  display: none !important;
}

</style>




  </div></div>

  </td>
</tr>
</table>

<table width="100%" class="table_style_a" style="margin-bottom:14px;">
  <tr>
    <td></td>
    <td><strong>Forecast</strong></td>
    <td><strong>WIP</strong></td>
    <td><strong>Sales</strong></td>
    <td><strong>Total</strong></td>
    <td><strong>Previous Year</strong></td>
    <td><strong>Difference</strong></td>
    <td></td>
  </tr>



<?php foreach ($months as $key => $value): ?>
<?php $total_costs = round($wip_values[$key]+$sales_values[$key]); ?>
<?php 
  $oldFigure = $old_sales_values[$key];
  $newFigure = $total_costs;

  if($newFigure > 0){
    $percentChange = (1 - $oldFigure / $newFigure) * 100;
  }else{
    $percentChange = (1 - $oldFigure / 1) * 100;
  }

  if($percentChange == 0){
    $feed_back = 'black !important';
  }elseif($percentChange < 0){
    $feed_back = 'red !important';
  }else{
    $feed_back = 'green !important';
  }
  $difference = $newFigure - $oldFigure;
?>
  <tr>
    <td><strong><?php echo date("F",strtotime($value)); ?></strong></td>
    <td  style="border-right:1px solid #eee;">$ <?php echo number_format($forecast_values[$key]); ?></td>
    <td>$ <?php echo number_format($wip_values[$key]); ?></td>
    <td>$ <?php echo number_format($sales_values[$key]); ?></td>
    <td  style="border-right:1px solid #eee;"><strong>$ <?php echo number_format($total_costs); ?></strong></td>
    <td>$ <?php echo number_format($oldFigure); ?></td>
    <td><strong style="color:<?php echo $feed_back; ?>;">$ <?php echo number_format($difference); ?></strong></td>
    <td><strong style="color:<?php echo $feed_back; ?>;">% <?php echo number_format($percentChange,2); ?></strong></td>
  </tr>
<?php endforeach; ?>


  <?php 

  $total_wip = array_sum($wip_values);
  $total_sales = array_sum($sales_values);

  $current_total = round(array_sum($wip_values) + array_sum($sales_values));
  $old_total = array_sum($old_sales_values);
  $forecast_total = array_sum($forecast_values);

  $oldFigure = $old_total;
  $newFigure = $current_total;

  if($newFigure > 0){
    $percentChange = (1 - $oldFigure / $newFigure) * 100;
  }else{
    $percentChange = (1 - $oldFigure / 1) * 100;
  }

  if($percentChange == 0){
    $feed_back = 'black !important';
  }elseif($percentChange < 0){
    $feed_back = 'red !important';
  }else{
    $feed_back = 'green !important';
  }
  $total_difference = $newFigure - $oldFigure;
  

   ?>
   

<tr>
    <td><strong>Total</strong></td>
    <td><strong>$ <?php echo number_format($forecast_total); ?></strong></td>
    <td><strong>$ <?php echo number_format($total_wip); ?></strong></td>
    <td><strong>$ <?php echo number_format($total_sales); ?></strong></td>
    <td  style="border-right:1px solid #eee;"><strong>$ <?php echo number_format($current_total); ?></strong></td>
    <td>$ <?php echo number_format($old_total); ?></td>
    <td><strong style="color:<?php echo $feed_back; ?>;">$ <?php echo number_format($total_difference); ?></strong></td>
    <td><strong style="color:<?php echo $feed_back; ?>;">% <?php echo number_format($percentChange,2); ?></strong></td>
  </tr>
</table>
<?php // <!-- <div class="page-break"></div> useful for breking pages and assignment to display--> ?>


<p>&nbsp;</p>

<table width="100%" style="" class="table_style_a top_20">
<tr  style="border:none !important;">
  <td style="border:none !important;" valign="top" width="15%" ><div id="pie_a" class=""></div></td>
  <td style="border:none !important;" valign="top" width="85%" ><div style="padding-left:25px;"><p style="text-align:center; font-size: 20px; font-weight: bold;">Top 20 Clients</p>


<?php if($is_company == 1): ?>
  <?php echo $this->dashboard->focus_top_ten_clients_pm($pm_id,$report_year,'list','mr',$pm_id); ?>
<?php elseif($is_company == 2): ?>
  <?php echo $this->dashboard->focus_top_ten_clients($report_year); ?>
<?php else: ?>
  <?php echo $this->dashboard->focus_top_ten_clients_pm($pm_id,$report_year,'list','mr'); ?>
<?php  endif; ?>


  </div></td>
</tr>
</table> 

<script type="text/javascript">
   var chart_b = c3.generate({ 
     size: { height: 300 , 
     width: 800 },
     data: {
       x : 'x',
       columns: [
       ['x', 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec', ],
       ['Last Year', <?php echo $old_sales_costs; ?> ], 
       ['Overall Sales', <?php echo $sales_costs; ?> ],
       ['WIP',<?php  echo $wip_costs; ?> ],
       ['Forecast', <?php echo $forecast_costs; ?>],    ],
       selection: {
         enabled: true
       },
       order: null,
       types: {'WIP' : 'bar','Last Year' : 'bar','Overall Sales' : 'bar', 'Forecast' : 'line'  
  },
     },
   legend: {
     show: true //hides label
   },
     tooltip: { show: false    },
 order: null,
 
     bindto: "#chart_b",
     bar: {  width: { ratio: 0.4 } },
     point: {   select: {    r: 6  }},
     zoom: { enabled: false },
     axis: {x: {type: 'category',tick: {rotate: 0, multiline: false},height: 0} },
     tooltip: {
       format: {
            // title: function (d) { return 'Data ' + d; },
            value: function (value, ratio, id) {
                // var format = id === 'data1' ? d3.format(',') : d3.format('$');
                var format = d3.format(',');
 
                var mod_value = Math.round(value)
                return '$ '+format(mod_value);
 
              }
            } 
 
          }
        });
 
 chart_b.groups([['WIP','Overall Sales']]);
 chart_b.data.colors({
 
   'Forecast': '#CC79A7',
   'Overall Sales': '#E69F00',
   'WIP': '#009E73',
   'Last Year': '#AAAAAA'
 
 
 });
 
 
 chart_b.select();
  
 
 var pie_a = c3.generate({
   size: {
     height: 330,
     width: 330
   },data: {
     columns: [



<?php if($is_company == 1): ?>
  <?php echo $this->dashboard->focus_top_ten_clients_pm('', $report_year, 'pie','mr',$pm_id); ?>
<?php elseif($is_company == 2): ?>
  <?php echo  $this->dashboard->focus_top_ten_clients_donut($report_year); ?>
<?php else: ?>
  <?php echo $this->dashboard->focus_top_ten_clients_pm($pm_id,$report_year,'pie','mr'); ?>
<?php endif; ?>



     ],
     type : 'pie',
     onclick: function (d, i) { console.log("onclick", d, i); },
     onmouseover: function (d, i) { console.log("onmouseover", d, i); },
     onmouseout: function (d, i) { console.log("onmouseout", d, i); }
   },
   legend: {
     show: false //hides label
   },
   bindto: "#pie_a",
   donut: {
     title: ''
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
 </script>