<?php

// module created by Jervy 20-9-2022
namespace App\Modules\Reports\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Projects\Controllers\Projects;

use App\Modules\Reports\Models\Reports_m;

use App\Modules\Invoice\Controllers\Invoice;

use Dompdf\Dompdf;
use Dompdf\Options;

require 'Dompdf/autoload.inc.php';

class Reports extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();
    $this->reports_m = new Reports_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $data = array();
    $data['main_content'] = 'App\Modules\Reports\Views\landing';
    echo '<p class="">REPORTS SCREEN LIST HERE ******</p>';

    return view('App\Views\page',$data);
  }

  public function myob_names(){

    $content = '';
    $table_q = $this->reports_m->select_myob_names();
    $content .= 'company_name,myob_name,abn'."\n";

    foreach ($table_q->getResult() as $row){
      $format_abn = $row->abn;
      $format_abn = trim(str_replace(' ', '', $format_abn)); 
      $data_abn = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3); 
      $content .= "\"$row->company_name\"".','."\"$row->myob_name\"".','."\"$data_abn\""."\n";
    }


    $log_time = time();
    $name = 'company_myob_names_'.$log_time.'.csv';


    write_file('./docs/temp/'.$name, $content);


    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename('./docs/temp/'.$name).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('./docs/temp/'.$name));

    flush(); // Flush system output buffer
    readfile('./docs/temp/'.$name);
    delete_files('./docs/temp/'.$name);

  }

  public function company_report(){
    $this->company_m  = new Company_m();

    $data_raw = $_POST['ajax_var'] ?? 'WA|Western Australia|08|6*Client|1*Food|6*A*J*cm_asc';
    $data_val = explode('*',$data_raw);


    if($data_val['3'] == ''){
      $data_val['3'] = 'A';
    }

    if($data_val['4'] == ''){
      $data_val['4'] = 'Z';
    }

    $letter_segment_a = $data_val['3'];
    $letter_segment_b = $data_val['4'];

    $segment_a = ord(strtolower($data_val['3']));
    $segment_b = ord(strtolower($data_val['4']));
    $company_type = '';


    if($data_val['1'] != ''){
      $company_type_arr = explode('|',$data_val['1']);
      $company_type_id = $company_type_arr['1'];
      $my_company_type = $company_type_arr['0'];

      if($company_type_id != '' && $company_type_id != 8){
        $company_type = "AND `company_details`.`company_type_id` = '$company_type_id'";
      }
    }else{
      $my_company_type = 'All Company Types';
    }

    $query = '';
    $content = '';
    $my_activity = '';

    if($data_val['2'] != ''){
      $query .= " AND (";
      $activity_arr = explode(',',$data_val['2']);
      $activity_loops = count($activity_arr);

      $client_category_q = '';
      foreach ($activity_arr as $activity_key => $activity_value) {
        $activity_item_arr = explode('|',$activity_value);
        $client_category_q .= '`company_details`.`activity_id` = \''.$activity_item_arr["1"].'\'';
        $my_activity .= $activity_item_arr["0"].', ';

        if($activity_key < $activity_loops-1){
          $client_category_q .= " OR ";
        }
      }

      $query .= $client_category_q;
      $query .= " )";
    }else{
      $my_activity .= 'All Activities';
    }


    $my_states = '';
    if($data_val['0'] != ''){
      $query .= " AND (";
      $state_arr = explode(',',$data_val['0']);
      $state_loops = count($state_arr);

      $state_q = '';
      foreach ($state_arr as $state_key => $state_value) {
        $state_item_arr = explode('|',$state_value);
        $state_q .= '`states`.`id` = \''.$state_item_arr["3"].'\'';
        $my_states .= $state_item_arr["1"].', ';

        if($state_key < $state_loops-1){
          $state_q .= " OR ";
        }
      }

      $query .= $state_q;
      $query .= " )";
    }else{
      $my_states .= 'All States';
    }


    if($data_val['5'] == 'cm_asc'){
      $order_q = '`company_details`.`company_name` ASC';
      $sort = 'Company Name A-Z';
    }elseif($data_val['5'] == 'cm_desc'){
      $order_q = '`company_details`.`company_name` DESC';
      $sort = 'Company Name Z-A';
    }elseif($data_val['5'] == 'sub_asc'){
      $order_q = '`address_general`.`suburb` ASC';
      $sort = 'Suburb A-Z';
    }elseif($data_val['5'] == 'sub_desc'){
      $order_q = '`address_general`.`suburb` DESC';
      $sort = 'Suburb Z-A';
    }elseif($data_val['5'] == 'state_asc'){
      $order_q = '`states`.`id` ASC';
      $sort = 'States A-Z';
    }elseif($data_val['5'] == 'state_desc'){
      $order_q = '`states`.`id` DESC';
      $sort = 'States Z-A';
    }elseif($data_val['5'] == 'act_asc'){
      $order_q = '`company_details`.`activity_id` ASC';
      $sort = 'Activity A-Z';;
    }elseif($data_val['5'] == 'act_desc'){
      $order_q = '`company_details`.`activity_id` DESC';
      $sort = 'Activity Z-A';
    }else { }


    $table_q = $this->reports_m->select_list_company($company_type,$query,$order_q);
    $records_num = 0;


    $content .= '<div class="def_page"><div class="clearfix header"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block" style="margin-top:-10px; margin-bottom:10px;" ><br />Company List Report</h1></div><br />';


    $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th  width="45%">Company Name</th><th width="15%">Activity</th><th width="12%">Suburb</th><th width="5%">State</th><th width="13%">Contact Number</th><th width="25%">Email</th></tr></thead><tbody>';        

 


    foreach ($table_q->getResult() as $row){

      $start_letter = strtolower(substr($row->company_name,0, 1));
      $segment = ord($start_letter);

      if($segment_a <= $segment){
        if($segment_b >= $segment){
          $content .= '<tr><td>'.$row->company_name.'</td>';

          if($company_type_id != '' && $company_type_id != 8){
            $company_type = "AND `company_details`.`company_type_id` = '$company_type_id'";
            $activity_type = $this->company_m->fetch_company_activity_name_by_type($company_type_arr['0'],$row->activity_id);
            $content .= '<td>'.$activity_type.'</td>';
          }else{
            $content .= '<td></td>';
          }

          $content .= '<td>'.ucwords(strtolower($row->suburb)).'</td><td>'.$row->shortname.'</td><td>';

          $check_num = explode(' ',$row->office_number);

          if($check_num['0'] == '1300' || $check_num['0'] == '1800'){
            $row->area_code = '';
          }

          $content .= ($row->office_number != ''? $row->area_code.' '.$row->office_number : '');

          $content .= '</td><td><a href="mailto:'.strtolower($row->general_email).'?Subject=Inquiry" >'.strtolower($row->general_email).'</a></td></tr>';
          $records_num ++;
        }       
      }

    }


    $content .= '</tbody></table>';

    $footer_text = '';
    $footer_text .= 'State: '.$my_states.'   ';
    $footer_text .= 'Company Type: '.$my_company_type.'    ';
    $footer_text .= 'Activity: '.$my_activity.'    ';
    $footer_text .= 'Letter Segments: '.$letter_segment_a.' - '.$letter_segment_b.'    ';
    $footer_text .= 'Sort: '.$sort.'    ';
    $footer_text .= 'Records Found: '.$records_num;

    $my_pdf = $this->html_form($content,'landscape','A4','companies','temp',$footer_text);
    echo $my_pdf;
  }



  public function html_form($content,$orientation,$paper,$file_name,$folder,$footer_text=null,$auto_clear=TRUE){
    $document = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title></title>';
    $document .= '<style type="text/css">
        *,body,html{margin:0;padding:0}.container:after,.editor_body .clearfix:after,.editor_body .container:after{clear:both}*{font-family:Arial,Helvetica,sans-serif;font-size:12px}body{margin:45px 0!important}table{border-collapse:collapse}tbody tr:nth-child(odd){background-color:#ccc}table,td,th{border:1px solid #000}td,th{height:25px;vertical-align:middle;padding:0 5px}h1{font-size:20px!important;font-weight:700}.header{border-bottom:2px solid #000}.text-right{text-align:right!important;display:block}.text-left{text-align:left!important;display:block}.notes_line{border-bottom:1px solid #010C13;padding:10px 0}.pull-right{float:right!important;display:block}.pull-left{float:left!important;display:block}.container:after,.container:before{content:"";display:table}.hidden,.hide{display:none}.container{zoom:1}.footer{position:absolute;bottom:0;background:#ccc;padding:0 10px;border:1px solid #000}.green-estimate{color:green!important;font-weight:700!important}.invoiced,tr.invoiced td,tr.invoiced td a{color:#ce03cb!important;font-weight:700!important}.paid,tr.paid td,tr.paid td a{color:#0398BD!important;font-weight:700!important}.wip,tr.wip td,tr.wip td a{color:#C16601!important;font-weight:700!important}.wip_b,tr.wip_b td,tr.wip_b td a{color:#000!important}.editor_body,.editor_body p{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;line-height:1.42857143;color:#333}.hidden{visibility:hidden}.editor_body{margin:-20px 10px 20px;position:relative;font-size:14px}.editor_body p{margin-bottom:0;font-size:12px}.editor_body strong{font-weight:700!important}.editor_body .green-estimate,.editor_body .green-estimate label{color:green!important;border-color:green!important;font-weight:700!important}.editor_body .green-estimate .form-control{border-color:green!important;color:green!important;font-weight:700}.editor_body .green-estimate .input-group-addon{color:green!important;border-color:#555!important;background:#B0EDB0;font-weight:700}.editor_body td.green-estimate{font-weight:700;color:green!important}.editor_body table{width:100%}.editor_body table,.editor_body table tr,.editor_body table tr td{border:0;padding:0;margin:0;background:#fff}.editor_body table th{font-size:12px;margin-bottom:10px;border-bottom:1px solid #000!important;text-align:left;padding-left:5px;font-weight:700}.editor_body .header,.editor_body .totals{border:2px solid #000;margin-bottom:10px}.editor_body table td{padding:5px}.editor_body .clearfix:after,.editor_body .clearfix:before{content:" "}.editor_body img{margin-bottom:10px}.editor_body .totals{padding:10px;font-size:18px;background:#DDD}.editor_body .m-left-20,.editor_body .m-right-20{margin-right:20px}.editor_body .footer{border-top:2px solid #000;padding:10px;font-size:18px;position:absolute;bottom:0;width:96%}.editor_body .print_bttn{background:#429742;color:#fff;border-radius:5px;padding:10px 15px;text-decoration:none;position:fixed;right:10px;top:10px}.editor_body hr{border:none;border-top:1px solid #000;margin-bottom:5px;display:block;width:100%;height:1px;background:#000}.editor_body fieldset{border:none}.editor_body fieldset legend{background:#fff;width:auto;border-bottom:0;margin-bottom:-5;font-size:14px!important;font-weight:700!important}.editor_body .full{width:100%;display:block}.editor_body .one-fourth{width:25%;float:left}.editor_body .one-third{width:33.33%;float:left}.editor_body .one-half{width:50%;float:left}.editor_body .two-third{width:66.67%;float:left}.editor_body .border-1{border:1px solid #000}.editor_body .border-2{border:2px solid #000}.editor_body .border-left-1{border-left:1px solid #000}.editor_body .border-left-2{border-left:2px solid #000}.editor_body .border-right-1{border-right:1px solid #000}.editor_body .border-right-2{border-right:2px solid #000}.editor_body .mgn-top-10{margin-top:10px}.editor_body .mgn-top-15{margin-top:15px}.editor_body .mgn-top-20{margin-top:20px}.editor_body .mgn-top-25{margin-top:25px}.editor_body .pad-10{padding:10px}.pad-10{padding:10px !important; }.editor_body .mgn-10{margin:10px}.editor_body .pad-15{padding:15px}.editor_body .mgn-15{margin:15px}.editor_body .pad-20{padding:20px}.editor_body .mgn-20{margin:20px}.editor_body .pad-r-5{padding-right:5}.editor_body .pad-l-10{padding-left:10px} .pad-l-10{padding-left:10px} .editor_body .mgn-l-5{margin-left:5px}.editor_body .pad-r-10{padding-right:10px}.editor_body .mgn-r-5{margin-right:5px}.editor_body .mgn-b-10{margin-bottom:10px}.editor_body .pdf_highlight_doc_mod{color:red;background:#ff0}#highlight_line{height:24px;margin-left:-5px!important}#draggable{background:0 0!important;border:none;color:red;padding:10px;margin-top:0!important}#draggable textarea:hover{overflow:visible}.c_edit{margin-top:10px!important}.d_edit{margin-top:3px!important}.f_edit{margin-top:-5px!important}.g_edit{margin-top:5px!important}.e_edit{margin-top:0!important}.h_edit{margin-top:-10px!important}.i_edit{margin-top:-20px!important}.j_edit{margin-top:12px!important}.k_edit{margin-top:7px!important}.l_edit{margin-top:15px!important}.m_edit{margin-top:-20px!important}.pad-20{padding:20px}.def_page{margin:20px 15px 10px}.def_page table{margin:5px 0 10px}.def_page table td{padding:0 5px}.def_page .footer{padding:5px}.job_book_notes .notes_line p span.pull-right{float:none!important;display:block}.notes_line br{display:none}.block,.notes_line br.block{display:block!important}.notes_line br.block{width:100%!important;height:auto!important;float:none!important}.editor_body .invoices_list_item{font-family:DejaVu Sans,Arial,Helvetica,sans-serif!important}
        </style>';
    $document .= '</head><body>';
    $document .= $content;
    $document .= '</body></html>';
    return $this->pdf_create($document,$orientation,$paper,$file_name,$folder,$footer_text,$auto_clear);
  }

  public function pdf($content='',$file_name='',$footer_text=''){

    if($content != ''){
      $post_value = $content;
    }else{
      $post_value = $_POST['content'];
    }

    $formatted = str_replace('pdf_mrkp_styl="','style="',$post_value);

    $content = '<div class="editor_body"><div class="clearfix">'.$formatted.'</div></div>';
    $my_pdf = $this->html_form($content,'portrait','A4',$file_name,'inv_jbs',$footer_text,FALSE);

    //echo $post_value;
    return $my_pdf;
  }

  public function pdf_create($html, $orientation='portrait', $paper='A4',$filename='generated_file',$folder_type='general',$footer_text=null ,$auto_clear=TRUE,$stream=TRUE){
    
    $options = new Options();
    $options->setIsRemoteEnabled(true);

    $dompdf = new Dompdf($options);
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

 
    $dompdf->setPaper($paper,$orientation);
    $dompdf->loadHtml($html);
    $dompdf->render();

    $canvas = $dompdf->getCanvas();
    $date_gen = date("jS F, Y");

    $user_prepared = ucfirst($this->session->get('user_first_name')).' '.ucfirst($this->session->get('user_last_name'));


//    $font = $fontMetrics->getFont("helvetica", "bold");

    if($orientation == 'portrait'){
      $canvas->page_text(535,10, "Page: {PAGE_NUM} of {PAGE_COUNT} ", "helvetica", 8, array(0,0,0));
      $canvas->page_text(15, 810, "Page: {PAGE_NUM} of {PAGE_COUNT}                   Produced: $date_gen                   Prepared By: $user_prepared", "helvetica", 8, array(0,0,0));
      
    }else{
      $canvas->page_text(780,10, "Page: {PAGE_NUM} of {PAGE_COUNT} ", "helvetica", 8, array(0,0,0));
      $canvas->page_text(10, 575, "$date_gen                   Prepared By: $user_prepared            ".$footer_text, "helvetica", 8, array(0,0,0));
    }


    $output = $dompdf->output();
    $filename .= '-'.date("d-m-Y-His");

    if($auto_clear){
      $this->delete_dir('docs/'.$folder_type);  #remove folder and contents
    }

    //create the folder if it's not already exists
    if(!is_dir('docs/'.$folder_type)){
      mkdir('docs/'.$folder_type,0755,TRUE);
    }

    write_file('docs/'.$folder_type.'/'.$filename.'.pdf','');
    file_put_contents('docs/'.$folder_type.'/'.$filename.'.pdf', $output);

    //unlink('docs/'.$folder_type.'/'.$filename.'.pdf');
    
  //  $dompdf->stream($filename.'.pdf', array("Attachment" => false));
    return $filename;
  }


  private function delete_dir($dir) {
    $handle=opendir($dir);
    while (($file = readdir($handle))!==false) {
      @unlink($dir.'/'.$file);
    }
    closedir($handle);
    rmdir($dir);
  }








public function wip_report(){

  $this->invoice = new Invoice();

  $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
  foreach ($admin_defaults->getResult() as $row){
    $unaccepted_date_categories = $row->unaccepted_date_categories;
    $unaccepted_no_days = $row->unaccepted_no_days;
  }


  if(isset($_GET['ajax_var'])){
    $data_val = explode('*',$_GET['ajax_var']);
  }else{
    $data_val = explode('*',$_POST['ajax_var']);
  }





 //   var data = wip_client+'*'+wip_pm+'*'+wip_find_start_finish_date+'*'+wip_find_finish_date+'*'+wip_cost_total+'*'+selected_cat+'*'+wip_project_total+'*'+wip_project_estimate+'*'+wip_project_quoted+'*'+wip_project_total_invoiced+'*'+wip_sort+'*'+wip_start_date_start_a+'*'+wip_start_date_b+'*'+doc_type+'*'+date_created_start+'*'+date_created+'*'+prj_status+'*'+un_acepted_start_date+'*'+un_acepted_end_date+'*'+output_file;

/*
$data_raw = '10*Alan Liddell|15*01/11/2022*30/12/2022*500,000*Kiosk,Full Fitout,Refurbishment,Strip Out*****prj_num_asc*01/07/2022*30/12/2022*Projects*01/07/2022*30/12/2022*wip***pdf';

$data_val = explode('*',$data_raw);

*/


  $content = '';

  $wip_client = $data_val['0'];
  $wip_pm = $data_val['1'];
  $wip_find_start_finish_date = $data_val['2'];
  $wip_find_finish_date = $data_val['3'];
  $wip_cost_total = $data_val['4'];
  $selected_cat = $data_val['5'];

  $wip_project_total = $data_val['6'];
  $wip_project_estimate = $data_val['7'];
  $wip_project_quoted = $data_val['8'];
  $wip_project_total_invoiced = $data_val['9'];

  $wip_sort = $data_val['10'];

  $date_start_a = $data_val['11'];
  $date_start_b = $data_val['12'];

  $wip_cost_total = str_replace(',', '', $wip_cost_total); 

  $records_num = 0;

  $curr_year = date('Y')+2;


  $doc_type = $data_val['13'];

  $date_created_start = $data_val['14'];
  $date_created = $data_val['15'];  

  $prj_status = $data_val['16'];  


  $unaccepted_date_a = $data_val['17']; 
  $unaccepted_date_b = $data_val['18']; 

  $output_file = $data_val['19'];
  $csv_content = '';


  if($date_start_a == ''){
    $date_a_s = strtotime(str_replace('/', '-', '10/10/1900'));
    $date_start_filter_a = '';
  }else{
    $date_a_s = strtotime(str_replace('/', '-', $date_start_a));
    $date_start_filter_a = $date_start_a;
  }

  if($date_start_b == ''){
    $date_b_s = strtotime(str_replace('/', '-', '31/12/'.$curr_year));
    $date_start_filter_b = '';
  }else{
    $date_b_s = strtotime(str_replace('/', '-', $date_start_b));
    $date_start_filter_b = $date_start_b;
  }



  if($wip_find_start_finish_date == ''){
    $date_a = strtotime(str_replace('/', '-', '10/10/1900'));
    $date_filter_a = '';
  }else{
    $date_a = strtotime(str_replace('/', '-', $wip_find_start_finish_date));
    $date_filter_a = $wip_find_start_finish_date;
  }
  $curr_year = date('Y')+2;

  if($wip_find_finish_date == ''){
    $date_b = strtotime(str_replace('/', '-', '31/12/'.$curr_year));
    $date_filter_b = '';
  }else{
    $date_b = strtotime(str_replace('/', '-', $wip_find_finish_date));
    $date_filter_b = $wip_find_finish_date;
  }


  if($date_created_start == ''){
    $date_c = strtotime(str_replace('/', '-', '31/12/1900'));
    $date_filter_c = '';
  }else{
    $date_c = strtotime(str_replace('/', '-', $date_created_start));
    $date_filter_c = $date_created_start;
  }
  $curr_year = date('Y')+2;

  if($date_created == ''){
    $date_d = strtotime(str_replace('/', '-', '31/12/'.$curr_year));
    $date_filter_d = '';
  }else{
    $date_d = strtotime(str_replace('/', '-', $date_created));
    $date_filter_d = $date_created;
  }

  $wip_client_q = '';
  if (is_numeric($wip_client)) {
    if($wip_client != ''){
      $wip_client_q = 'AND `company_details`.`company_id` =  \''.$wip_client.'\'';
      $wip_client_filter = ' ID: '.$wip_client;
    }else{
      $wip_client_filter = 'All Clients';
    }
  }else{
    if($wip_client != ''){
      $wip_client_q = 'AND `company_details`.`company_name` =  \''.$wip_client.'\'';
      $wip_client_filter = $wip_client;
    }else{
      $wip_client_filter = 'All Clients';
    }
  }

  $wip_pm_q = '';
  if($wip_pm != ''){
    $wip_pm_arr = explode('|', $wip_pm);
    $wip_pm_q = 'AND `project`.`project_manager_id` =  \''.$wip_pm_arr['1'].'\'';
    $wip_pm_filter = $wip_pm_arr['0'];
  }else{
    $wip_pm_filter = 'All Project Managers';
  }

  $wip_cost_total_q = '';
  if($wip_cost_total == ''){
    $wip_cost_total_q = 100000000000000;
    $wip_cost_filter = 'All Prices';
  }else{
    $wip_cost_total_q = $wip_cost_total;
    $wip_cost_filter = $wip_cost_total;
  }

  $selected_cat_q = '';

  if($selected_cat != ''){


    $selected_cat_arr = explode(',',$selected_cat);
    $selected_cat_loops = count($selected_cat_arr);


    $selected_cat_q .= 'AND (';



      foreach ($selected_cat_arr as $selected_cat_key => $selected_cat_val) {

        $selected_cat_q .= '`project`.`job_category` = \''.$selected_cat_val.'\'';

        if($selected_cat_key < $selected_cat_loops-1){
          $selected_cat_q .= " OR ";
        }
      }

      $selected_cat_q .= ')';

}else{

}





$order_q = '';
$sort = '';
if($wip_sort == 'clnt_asc'){
  $order_q = 'ORDER BY `company_details`.`company_name` ASC';
  $sort = 'Client Name A-Z';
}elseif($wip_sort == 'clnt_desc'){
  $order_q = 'ORDER BY `company_details`.`company_name` DESC';
  $sort = 'Client Name Z-A';
}elseif($wip_sort == 'fin_d_asc'){
  $order_q = 'ORDER BY `date_filter_mod` ASC';
  $sort = 'Finish Date Descending';
}elseif($wip_sort == 'fin_d_desc'){
  $order_q = 'ORDER BY `date_filter_mod` DESC';
  $sort = 'Finish Date Ascending';
}elseif($wip_sort == 'srtrt_d_asc'){
  $order_q = 'ORDER BY `start_date_filter_mod` ASC';
  $sort = 'Start Date Ascending';
}elseif($wip_sort == 'srtrt_d_desc'){
  $order_q = 'ORDER BY `start_date_filter_mod` DESC';
  $sort = 'Start Date Descending';
}elseif($wip_sort == 'prj_num_asc'){
  $order_q = 'ORDER BY `project`.`project_id` ASC';
  $sort = 'Project Number Asc';
}elseif($wip_sort == 'prj_num_desc'){
  $order_q = 'ORDER BY `project`.`project_id` DESC';
  $sort = 'Project Number Desc';
}elseif($wip_sort == 'qte_num_asc'){
  $order_q = 'ORDER BY `unix_quote_deadline_date` ASC';
  $sort = 'Quote Deadline Asc';
}elseif($wip_sort == 'qte_num_desc'){
  $order_q = 'ORDER BY `unix_quote_deadline_date` DESC';
  $sort = 'Quote Deadline Desc';
}else { $order_q = ''; }

$type = '';

if($doc_type == 'WIP'){
  $type = ' AND `project`.`job_date` <> \'\' ';
  $file_prefix = 'wip';
}else{
  $file_prefix = 'project';
      //$type = '';
}

$status = '';
$show_invoiced = 0;
$show_un_invoiced = 0;
$page_type = '';




if($prj_status != ''){

  $status_list = explode(',',$prj_status);
  $prj_status_arr_count = count($status_list);




  if($prj_status == 'quote' || $prj_status == 'unaccepted'){
    $doc_type = ucfirst($prj_status);

    $status .= ' AND `project`.`is_paid` = \'0\' ';
    $status .= ' AND `project`.`job_date` = \'\' ';
    $show_invoiced = 0;
    $show_un_invoiced = 1;

  }elseif($prj_status == 'wip'){
    $doc_type = 'WIP Projects';
    $show_invoiced = 0;

    $show_un_invoiced = 1;

    $status .= ' AND `project`.`is_paid` = \'0\' ';
    $status .= ' AND `project`.`job_date` <> \'\' ';

  }elseif($prj_status == 'invoiced'){
    $doc_type = 'Invoiced Projects';
    $status .= ' AND `project`.`job_date` <> \'\' ';
    $show_invoiced = 1;

  }elseif($prj_status == 'paid'){
    $doc_type = 'Paid Projects';

    $status .= ' AND `project`.`is_paid` = \'1\' ';
    $status .= ' AND `project`.`job_date` <> \'\' ';
    $show_invoiced = 1;

  }else{
    $doc_type = '';
  }

  if($prj_status == 'unaccepted'){

    $status .= " AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`unaccepted_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$unaccepted_date_a', '%d/%m/%Y') ) 
      AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`unaccepted_date`, '%d/%m/%Y') ) < UNIX_TIMESTAMP( STR_TO_DATE('$unaccepted_date_b', '%d/%m/%Y') ) ";
  }



    }






    $change_date_type = '';

    if ($doc_type == 'Quote'){

      $change_date_type = 'Quote Deadline';

    } else {

      $change_date_type = 'Job Date';
    }

    $content .= '<div class="def_page"><div class="clearfix header"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block" style="margin-top:-10px; margin-bottom:10px;" ><br />'.$page_type.' '.$doc_type.' List Report</h1></div><br />';
    $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th width="6%">Finish</th><th width="6%">Start</th><th width="20%">Client</th><th width="30%">Project</th><th width="8%">Total</th><th width="6%">'.$change_date_type.'</th><th width="7%">Install Hrs</th><th width="7%">Project No</th><th width="10%">Invoiced $</th></tr></thead><tbody>';       
 

    $wip_list_q = $this->reports_m->select_list_wip($wip_client_q,$wip_pm_q,$selected_cat_q,$order_q,$type,$status);
    $total_list = 0;
    
    $arrs = array();
    $color = '';

    $total_estimate = 0;
    $total_invoiced = 0;
    $total_quoted = 0;

    $total_invoiced_init = 0;
    $is_restricted = 0;

    $has_sched = '';
 

  $csv_content = 'site_finish,site_start,client,project,total,';

  if ($doc_type == 'Quote'){ 
    $csv_content .= 'quote_deadline_date';
  } else { 
    $csv_content .= 'job_date';
  }

  $csv_content .=',install_hrs,project_no,invoiced,pm_name,focus_company'."\n"; 
 
    foreach ($wip_list_q->getResultArray() as $row){

      if($row['job_date'] == '' ){
        $color = 'notwip';
      }


      if( isset($row['project_schedule_task_id'])     ){
        $has_sched = '<span style="color:red!important;font-weight:700!important;">*';
      }else{
        $has_sched = '<span>';
      }


      $unaccepted_date = $row['unaccepted_date'];
      if($unaccepted_date !== ""){
        $unaccepted_date_arr = explode('/',$unaccepted_date);
        $u_date_day = $unaccepted_date_arr[0];
        $u_date_month = $unaccepted_date_arr[1];
        $u_date_year = $unaccepted_date_arr[2];
        $unaccepted_date = $u_date_year.'-'.$u_date_month.'-'.$u_date_day;
      }

      $start_date = $row['date_site_commencement'];
      if($start_date !== ""){
        $start_date_arr = explode('/',$start_date);
        $s_date_day = $start_date_arr[0];
        $s_date_month = $start_date_arr[1];
        $s_date_year = $start_date_arr[2];
        $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;
      } 



      $job_category_arr = explode(",",$unaccepted_date_categories);
      foreach ($job_category_arr as &$value) {
        if($value ==  $row['job_category']){
          $is_restricted = 1;
        }
      }

      $today = date('Y-m-d');
      $unaccepteddate =strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
      $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

      if(strtotime($unaccepteddate) < strtotime($today)){
        if($is_restricted == 1){
          if($unaccepted_date == ""){
            $status = 'quote';
          }else{
            $status = 'unaccepted';
          }
        }else{
          $status = 'unaccepted';
        }

      }else{
        if($unaccepted_date == ""){
          $status = 'quote';
        }else{
          $status = 'unaccepted';
        }

      }



      if($row['job_date'] != '' ){
        $color = 'wip';
        $status =  'wip';
      }

      if($this->invoice->if_invoiced_all($row['project_id'])  && $this->invoice->if_has_invoice($row['project_id']) > 0 ){
        $color = 'invoiced';
        $status = 'invoiced';
      }

      if($row['is_paid'] == 1){
        $color = 'paid';
        $status =  'paid';
      }


 
      $date_site_finish = $row['date_filter_mod'];
      $date_site_start = $row['start_date_filter_mod'];
      $date_project_date = strtotime(str_replace('/', '-', $row['project_date']));




        if($doc_type == 'WIP'){
          $color = 'wip_b';
        }

        if($date_a_s <= $date_site_start){
          if($date_site_start <= $date_b_s){

            if($date_a <= $date_site_finish){
              if($date_site_finish <= $date_b){

                if($date_c <= $date_project_date ){
                  if( $date_project_date <= $date_d ){


 


                    if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 || $row['variation_total'] > 0.00 ){
                      $total_list = $row['project_total']+$row['variation_total'];
                    }else{
                      $total_list = $row['budget_estimate_total'];
                    }

                    if($wip_cost_total_q >= $total_list){





                      if($prj_status == $status){




                      if($this->invoice->if_invoiced_all($row['project_id']) && $this->invoice->if_has_invoice($row['project_id']) > 0 ){

                        if($show_invoiced == 1){


                          if($output_file == 'pdf'){



                            $prj_total_current = $row['project_total']+$row['variation_total'];

                            $content .= '<tr class="'.$color.'"><td>'.$row['date_site_finish'].'</td><td>'.$row['date_site_commencement'].'</td><td>'.$row['company_name'].'</td><td>'.$row['project_name'].'</td>';

                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){

                              $total_quoted = $total_quoted + $prj_total_current;
                              $content .= '<td>'.number_format($prj_total_current ,2).'</td>';
                            }else{

                              $total_estimate = $total_estimate + $row['budget_estimate_total'];
                              $content .= '<td class="green-estimate" style="color:green!important;font-weight:700!important;">'.number_format($row['budget_estimate_total'],2).'</td>';
                            }

                            $content .= '<td>'.$row['job_date'].'</td>';

                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){
                              $content .= '<td>'.number_format($row['install_time_hrs'],2).'</td>';
                            }else{
                              $content .= '<td class="green-estimate" style="color:green!important;font-weight:700!important;">'.number_format($row['labour_hrs_estimate'],2).'</td>';
                            }


                            $total_invoiced_init = $this->invoice->get_project_invoiced($row['project_id'],$row['project_total'],$row['variation_total']);
                            $total_invoiced = $total_invoiced + $total_invoiced_init;

                            $content .= '<td>'.$has_sched.$row['project_id'].'</span></td>';
                            $content .= '<td>'.number_format($total_invoiced_init,2).'</td></tr>';      
                            $records_num++; 

                          }else{

                            $csv_content .= $row['date_site_finish'].','.$row['date_site_commencement'].','.$row['company_name'].','.str_replace(',',' ',$row['project_name']).',';
                                      $prj_total_current = $row['project_total']+$row['variation_total'];
 
                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){

                              $total_quoted = $total_quoted + $prj_total_current;                              
                              $csv_content .= $prj_total_current;
                            }else{

                              $total_estimate = $total_estimate + $row['budget_estimate_total'];
                              $csv_content .= $row['budget_estimate_total'];
                            }

                            $csv_content .= ','.$row['job_date'].',';

                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){ 
                              $csv_content .= $row['install_time_hrs'];
                            }else{
                              $csv_content .= $row['labour_hrs_estimate']; 
                            }

                            $total_invoiced_init = $this->invoice->get_project_invoiced($row['project_id'],$row['project_total'],$row['variation_total']);
                            $total_invoiced = $total_invoiced + $total_invoiced_init;



                            $csv_content .= ','.$row['project_id'].','.$total_invoiced_init;



                              $csv_content .= ','.$row['pm_name'];
                              $csv_content .= ','.$row['f_company_name'];



                              $csv_content .= "\n"; 



                            $records_num++; 
                          }
                        }

                      }else{    

                        if($show_un_invoiced == 1){ 


                          if($output_file == 'pdf'){

                            $prj_total_current = $row['project_total']+$row['variation_total'];

                            $content .= '<tr class="'.$color.'"><td>'.$row['date_site_finish'].'</td><td>'.$row['date_site_commencement'].'</td><td>'.$row['company_name'].'</td><td>'.$row['project_name'].'</td>';



                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 || $row['variation_total'] > 0.00 ){
                              $total_quoted = $total_quoted + $prj_total_current;
                              $content .= '<td>'.number_format($prj_total_current,2).'</td>';
                            }else{
                              $total_estimate = $total_estimate + $row['budget_estimate_total'];
                              $content .= '<td class="green-estimate" style="color:green!important;font-weight:700!important;">'.number_format($row['budget_estimate_total'],2).'</td>';
                            }

                            if ($doc_type == 'Quote'){
                              $content .= '<td>'.$row['quote_deadline_date'].'</td>';
                            } else {
                              $content .= '<td>'.$row['job_date'].'</td>';

                            }

                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){
                              $content .= '<td>'.number_format($row['install_time_hrs'],2).'</td>';
                            }else{
                              $content .= '<td class="green-estimate" style="color:green!important;font-weight:700!important;">'.number_format($row['labour_hrs_estimate'],2).'</td>';
                            }



                            $total_invoiced_init = $this->invoice->get_project_invoiced($row['project_id'],$row['project_total'],$row['variation_total']);
                            $total_invoiced = $total_invoiced + $total_invoiced_init;


                            $content .= '<td>'.$has_sched.$row['project_id'].'</span></td>';
                            $content .= '<td>'.number_format($total_invoiced_init,2).'</td></tr>';  


                            $records_num++;


                          }else{

                            $csv_content .= $row['date_site_finish'].','.$row['date_site_commencement'].','.$row['company_name'].','.str_replace(',',' ',$row['project_name']).',';
                            $prj_total_current = $row['project_total']+$row['variation_total'];

                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){

                              $total_quoted = $total_quoted + $prj_total_current;                              
                              $csv_content .= $prj_total_current;
                            }else{

                              $total_estimate = $total_estimate + $row['budget_estimate_total'];
                              $csv_content .= $row['budget_estimate_total'];
                            }




                            if ($doc_type == 'Quote'){ 
                              $csv_content .= ','.$row['quote_deadline_date'].',';
                            } else { 
                              $csv_content .= ','.$row['job_date'].',';
                              
                            }



                            if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 ){ 
                              $csv_content .= $row['install_time_hrs'];
                            }else{
                              $csv_content .= $row['labour_hrs_estimate']; 
                            }

                            $total_invoiced_init = $this->invoice->get_project_invoiced($row['project_id'],$row['project_total'],$row['variation_total']);
                            $total_invoiced = $total_invoiced + $total_invoiced_init;

                            $csv_content .= ','.$row['project_id'].','.$total_invoiced_init;



                              $csv_content .= ','.$row['pm_name'];
                              $csv_content .= ','.$row['f_company_name'];



                              $csv_content .= "\n"; 




                            $records_num++; 
                          }




                        }

                      }


}
 



                    }
                  }
                }
              }}
            }
          }
        }



      $content .= '</tbody></table>';

      $arrs = implode(',', $arrs);

      $overall_project_total = $total_estimate + $total_quoted;

      $content .= '<p><br /><hr /><p><br /></p><span><strong> All Prices are EXT-GST</strong> &nbsp; &nbsp;</span> &nbsp; &nbsp;   

      <span><strong>Project Total:</strong> '.number_format($overall_project_total,2).'</span> &nbsp; &nbsp;   


      <span class="green-estimate"><strong>Total Estimated:</strong> '.number_format($total_estimate,2).'</span> &nbsp; &nbsp;   

      <span><strong>Total Quoted:</strong> '.number_format($total_quoted,2).'</span></span> &nbsp; &nbsp;   

      <span><strong>Total Invoiced:</strong> '.number_format($total_invoiced,2).'</span><br /></p><p><br /><span><strong class="">Category:</strong> <span class="">'.str_replace(',', ", ", $selected_cat).'</span> &nbsp;  &nbsp;   &nbsp;  &nbsp; <strong>Color Codes:</strong> &nbsp;  &nbsp;  

      <strong class="invoiced">Invoiced</strong> &nbsp;  &nbsp;  <strong class="paid">Paid</strong> &nbsp;  &nbsp;  

      <strong class="wip">WIP</strong> &nbsp;  &nbsp;   <span style="color:red!important;font-weight:700!important;">*Has Project Schedule</span></p>';


 

      $footer_text = '';


    //  $footer_text .= 'Client: '.$wip_client_filter.'   ';
    //  $footer_text .= 'Category: '.$selected_cat.'   ';

      $footer_text .= 'Project Manager: '.$wip_pm_filter.'     ';

      if($date_start_filter_a != '' || $date_start_filter_b != ''){
        $footer_text .= 'Start: '.$date_start_filter_a.' - '.$date_start_filter_b.'     ';
      }

      if($date_filter_a != '' || $date_filter_b != ''){
        $footer_text .= 'Finish: '.$date_filter_a.' - '.$date_filter_b.'     ';
      }
/*
      if(intval($wip_cost_total) < 500000){
        $footer_text .= 'Total Limit: '.$wip_cost_total.'   ';
      }
*/
      if($wip_cost_filter < 500000){
        $footer_text .= 'Cost Range: '.number_format($wip_cost_filter,2).'     ';
      }

      $footer_text .= 'Sort: '.$sort.'    ';
      $footer_text .= 'Records Found: '.$records_num;



      if($output_file == 'pdf'){
          $my_pdf = $this->html_form($content,'landscape','A4',$prj_status,'temp',$footer_text);
          echo $my_pdf;
      }else{


/*
        $log_time = time();
        $fname = strtolower( str_replace(' ','_', $prj_status)  );
        $name = $fname.'_'.$log_time.'.csv';
        force_download($name, $content,TRUE);
*/
        $fname = strtolower( str_replace(' ','_', $prj_status)  );
        $log_time = time();
        $name = $fname.'_'.$log_time.'.csv';
        $content = $csv_content;
        write_file('./docs/temp/'.$name, $content);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename('./docs/temp/'.$name).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('./docs/temp/'.$name));

        flush(); // Flush system output buffer
        readfile('./docs/temp/'.$name);
        delete_files('./docs/temp/'.$name);



      }


      

    }


  public function purchase_order_report(){
/*
    if(isset($ajax_var) && $ajax_var!='' && !isset($_POST['ajax_var'])){
      $data_val = explode('*',$ajax_var);
    }else{
      $data_val = explode('*',$_POST['ajax_var'] ?? '');
    }**/


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $data_val = explode('*',$this->request->getPost('ajax_var'));
    }else{
      $data_val = explode('*',$this->request->getGet('ajax_var'));
    }




    $content = '';
    $pdf_content = '';
    $csv_content = '';
    $total_project_value = 0;
    
    $current_date = date("d/m/Y");

    $project_manager  = $data_val['0'];
    $status       = $data_val['1'];
    $cpo_date_a     = $data_val['2'];
    $cpo_date_b     = $data_val['3'];

    $reconciled_date_a  = $data_val['4'];
    $reconciled_date_b  = $data_val['5'];
    $doc_type       = $data_val['6'];
    $po_sort      = $data_val['7'];
    $focus_company    = $data_val['8'];
    $for_myob     = $data_val['9'];
    $include_duplicate  = $data_val['10'];


    if($cpo_date_a == ''){
      $cpo_date_a = '01/01/2000';
    }

    if($cpo_date_b == ''){
      $cpo_date_b = $current_date;
    }


    if($status == 1){
      $status_filter = 'Reconciled';
    }else{
      $status_filter = 'Outstanding';
    }

    $focus_company_data = explode('|', $focus_company);
    $focus_company_filter = $focus_company_data[0];
    $focus_company_id = $focus_company_data[1];

    $pm_data = explode('|', $project_manager);
    $project_manager_filter = $pm_data[0];
    $date_filter_a = '';
    $date_filter_b = '';

    switch ($po_sort) {
      case "clnt_asc":
      $order = ' ORDER BY `company_details`.`company_name` ASC ';
      $sort = 'Company Name A-Z';
      break;
      case "clnt_desc":
      $order = ' ORDER BY `company_details`.`company_name` DESC ';
      $sort = 'Company Name Z-A';
      break;
      case "cpo_d_asc":
      $order = ' ORDER BY `unix_work_cpo_date` ASC ';
      $sort = 'CPO Date ASC';
      break;
      case "cpo_d_desc":
      $order = ' ORDER BY `unix_work_cpo_date` DESC ';
      $sort = 'CPO Date DESC';
      break;
      case "prj_num_asc":
      $order = ' ORDER BY `works`.`project_id` ASC ';
      $sort = 'Project ID ASC';
      break;
      case "prj_num_desc":
      $order = ' ORDER BY `works`.`project_id` DESC ';
      $sort = 'Project ID DESC';
      break;
      case "reconciled_d_asc":
      $order = ' ORDER BY `unix_reconciled_date` ASC ';
      $sort = 'Reconciled Date ASC';
      break;
      case "reconciled_d_desc":
      $order = ' ORDER BY `unix_reconciled_date` DESC ';
      $sort = 'Reconciled Date DESC';
      break;
      default:
      $order = ' ORDER BY `company_details`.`company_name` ASC ';
      $sort = 'Company Name A-Z';
    }

    if($cpo_date_a != '' && $cpo_date_b != ''){
      $cpo_date = "
      AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_cpo_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$cpo_date_a', '%d/%m/%Y') ) 
      AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_cpo_date`, '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$cpo_date_b', '%d/%m/%Y') ) 
      ";
      $date_filter_a = " [CPO Date] $cpo_date_a-$cpo_date_b ";
    }else{
      $cpo_date = '';
    }

    if($reconciled_date_a != '' && $reconciled_date_b != ''){
      $reconciled_date = "
      AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`reconciled_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$reconciled_date_a', '%d/%m/%Y') ) 
      AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`reconciled_date`, '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$reconciled_date_b', '%d/%m/%Y') ) 
      ";
      $date_filter_b = " [Reconciled Date] $reconciled_date_a-$reconciled_date_b ";
    }else{
      $reconciled_date = '';
    }

    $table_q = $this->reports_m->select_po_data($status,$pm_data[1],$cpo_date,$reconciled_date,$focus_company_id,$order,$for_myob);
    $records_num = 0;


    $content .= '<div class="def_page"><div class="clearfix header"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block"  style="margin-top:-10px; margin-bottom:10px;" ><br />'.$status_filter.' List Report</h1></div><br />';
    $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr>';
    $content .= '<th width="20%">Company Name</th><th width="20%">MYOB Name</th><th>PO Number</th><th>Price</th><th>Project ID</th><th>CPO Date</th><th>Reconciled</th><th>Start Date</th><th>Finished Date</th></tr></thead><tbody>';

    if($for_myob == 1){
      $csv_content .= 'Co./Last Name,Inclusive,Purchase #,Date,Supplier Invoice #,Delivery Status,Item Number,Quantity,Price,Total,Job,Tax Code,Tax Amount,Purchase Status,Order'."\n";
    }else{
      $csv_content .= 'company_name,myob_name,po_number,price,project_id,work_cpo_date,reconciled_date,date_site_commencement,date_site_finish'."\n"; 
    }

    foreach ($table_q->getResult() as $row){
      $code = '';

      if($for_myob == 1){
        if(  ($include_duplicate == 1 && $row->po_set_report_date != '')  || $row->po_set_report_date == '' ){

          $tax_amount = $row->price*0.1;

          $is_other_work_desc = $row->is_other_work_codes ?? 0;

          if($is_other_work_desc == 1 &&  strlen($row->other_myob_item_no) > 0 ){
            $code = $row->other_myob_item_no;
          }else{
            $code = $row->myob_item_name;
          }

          if( $row->other_work_desc == 'Site Installation' ){
            $code = 'INST';
          }

          $csv_content .= "\"$row->myob_name\"".',X,'."\"00$row->works_id\"".','.$row->work_cpo_date.','.$row->project_id.',A,'.$code.',1,"$'.$row->price.'","$'.$row->price.'",'.$row->project_id.',GST,"$'.$tax_amount.'",o,1'."\n";
          $csv_content .= ',,,,,,,,,,,,,,'."\n";
        }

      }else{

        if($doc_type == 'pdf'){

          $pdf_content .= '<tr><td>'.$row->company_name.'</td><td>'.$row->myob_name.'</td>';

          if($row->po_set_report_date != ''){
            $pdf_content .= '<td style="color:green!important;font-weight:700!important;">'.$row->works_id.'</td>';
          }else{
            $pdf_content .= '<td>'.$row->works_id.'</td>';
          }

          $pdf_content .= '<td>'. number_format($row->price,2).'</td>';
          $pdf_content .= '<td>'.$row->project_id.'</td><td>'.$row->work_cpo_date.'</td><td>'.$row->reconciled_date.'</td><td>'.$row->date_site_commencement.'</td><td>'.$row->date_site_finish.'</td></tr>';

        }else{
          $csv_content .= "\"$row->company_name\"".','."\"$row->myob_name\"".','.$row->works_id.','.$row->price.','.$row->project_id.','.$row->work_cpo_date.','.$row->reconciled_date.','.$row->date_site_commencement.','.$row->date_site_finish."\n";
        }

        $total_project_value = $total_project_value + round($row->price,2);
        $records_num++;
      }
    }

    if($doc_type == 'pdf'){
      $content .= $pdf_content;
    }

    $content .= '</tbody></table>';
    $content .= '<hr /><p style="margin-top:10px;"><strong>Project Manager:</strong> '.$project_manager_filter.'  &nbsp;  &nbsp;  &nbsp;  <strong>Project Total Ex-GST:</strong> $'.number_format($total_project_value,2).'  &nbsp;  &nbsp;  &nbsp; <strong> Note:</strong> All Prices are EX-GST, PO Number is <span style="color:green!important;font-weight:700!important;">GREEN</span> if report is already made. <br /></p><br />';
    $content .= '</div>';

    if($doc_type == 'pdf'){


    $footer_text = '';
    $footer_text .= 'Company: '.$focus_company_filter.'   ';
    $footer_text .= 'Date: '.$date_filter_a.'  '.$date_filter_b.'    ';
    $footer_text .= 'Status: '.$status_filter.'    ';
    $footer_text .= 'Sort: '.$sort.'    ';
    $footer_text .= 'Records Found: '.$records_num;

      $my_pdf = $this->html_form($content,'landscape','A4','invoices','temp',$footer_text);
      echo $my_pdf;

    }else{

      $fname = strtolower( str_replace(' ','_', $status_filter)  );
      $log_time = time();
      $name = $fname.'_'.$log_time.'.csv';
      $content = $csv_content;
      write_file('./docs/temp/'.$name, $content);

      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename('./docs/temp/'.$name).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('./docs/temp/'.$name));

      flush(); // Flush system output buffer
      readfile('./docs/temp/'.$name);
      delete_files('./docs/temp/'.$name);
    }
  }

  public function contacts_gen($type){

    $status_filter = 'contacts';
    $csv_content = '';
    $content = '';

    $table_q = $this->reports_m->get_users_contacts();


    if($type == 1){ // CSV

      $csv_content .= 'Staff Full Name,Direct Number,Mobile Number,Personal Mobile Number,Email Address,Personal Email Address,Skype'."\n";

      foreach ($table_q->getResult() as $row){
        $csv_content .= $row->full_name.','.$row->direct_number.','.$row->mobile_number.','.$row->personal_mobile_number.','.$row->general_email.','.$row->personal_email.','.$row->user_skype."\n";
      }



      $fname = strtolower( str_replace(' ','_', $status_filter)  );
      $log_time = time();
      $name = $fname.'_'.$log_time.'.csv';
      $content = $csv_content;
      write_file('./docs/temp/'.$name, $content);

      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename('./docs/temp/'.$name).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('./docs/temp/'.$name));

      flush(); // Flush system output buffer
      readfile('./docs/temp/'.$name);
      delete_files('./docs/temp/'.$name);

    }else{


      


      $content .= '<div class="def_page"><div class="clearfix"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block"  style="margin-top:-10px; margin-bottom:10px;" ><br />Focus Contact</h1></div>';
      $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr>';
      $content .= '<th>Staff Full Name</th><th>Direct Number</th><th>Mobile Number</th><th>Personal Mobile Number</th><th>Email Address</th><th>Personal Email Address</th><th>Skype</th></th></tr></thead><tbody>';
    

      foreach ($table_q->getResult() as $row){
        $content .= '<tr><td>'.$row->full_name.'</td><td>'.$row->direct_number.'</td><td>'.$row->mobile_number.'</td><td>'.$row->personal_mobile_number.'</td><td>'.$row->general_email.'</td><td>'.$row->personal_email.'</td><td>'.$row->user_skype.'</td></tr>';
      }

      $content .= '</tbody></table></div>';



      $my_pdf = $this->html_form($content,'landscape','A4','invoices','temp');
      echo $my_pdf;

    }





  }


  public function invoice_report(){
    $this->projects = new Projects();
    $this->invoice = new Invoice();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $data_val = explode('*',$this->request->getPost('ajax_var'));
    }else{
      $data_val = explode('*',$this->request->getGet('ajax_var'));
    }

    $content = '';

    $project_number = $data_val['0'];
    $progress_claim = $data_val['1'];
    $clinet = $data_val['2'];
    $invoice_date_a = $data_val['3'];
    $invoice_date_b = $data_val['4'];
    $invoice_status = $data_val['5'];
    $invoice_sort = $data_val['6'];
    $project_manager = $data_val['7'];

    $invoiced_date_a = $data_val['8'];
    $invoiced_date_b = $data_val['9'];


    $doc_type = $data_val['10'];
    $focus_comp_id = $data_val['11'];

    $curr_year = date('Y');
    $inv_date_type = 0;


    if($invoice_date_a == ''){
      $date_a = strtotime(str_replace('/', '-', '10/10/1990'));
      $date_filter_a = '';
    }else{
      $date_a = strtotime(str_replace('/', '-', $invoice_date_a));
      $date_filter_a = $invoice_date_a;
      $inv_date_type = 1;
    }

    if($invoice_date_b == ''){
      $date_b = strtotime(str_replace('/', '-', '31/12/'.$curr_year));
      $date_filter_b = '';
    }else{
      $date_b = strtotime(str_replace('/', '-', $invoice_date_b));
      $date_filter_b = $invoice_date_b;
      $inv_date_type = 1;
    }



    if($invoiced_date_a != ''){
      $date_a = strtotime(str_replace('/', '-', $invoiced_date_a));
      $date_filter_a = $invoiced_date_a;
      $inv_date_type = 2;
    }

    if($invoiced_date_b != ''){
      $date_b = strtotime(str_replace('/', '-', $invoiced_date_b));
      $date_filter_b = $invoiced_date_b;
      $inv_date_type = 2;
    }


    $project_num_q = '';
    if($project_number != ''){
      $project_num_q = '`invoice`.`project_id` =  \''.$project_number.'\'';
      $project_num_filter = $project_number;
    }else{
      $project_num_filter = 'All Projects';
    }

    $client_q = '';

    $clinet_arr = explode('|',$clinet);

    if($clinet != ''){
      $client_q = ($project_num_q != '' ? 'AND ' : '');
      $client_q .= '`project`.`client_id` = \''.$clinet_arr['1'].'\' ';
      $client_filter = $clinet_arr['0'];
    }else{
      $client_filter = 'All Clients';
    }

    $invoice_status_q = '';
    $status_filter = '';
    if($invoice_status != ''){
      $invoice_status_q = ($project_num_q != '' || $client_q != '' ? 'AND ' : '');

        if($invoice_status == '1'){
          $invoice_status_q .= '`invoice`.`is_invoiced` = \'0\' AND `invoice`.`is_paid` = \'0\''; 
          $status_filter .= 'Un-Invoiced';      
        }elseif($invoice_status == '2'){
          $invoice_status_q .= '`invoice`.`is_invoiced` = \'1\' ';
          $status_filter .= 'Invoiced';
        }elseif($invoice_status == '3'){
          $invoice_status_q .= '`invoice`.`is_paid` = \'1\' ';
          $status_filter .= 'Paid';
        }elseif($invoice_status == '4'){
          $invoice_status_q .= '`invoice`.`is_invoiced` = \'1\' AND `invoice`.`is_paid` = \'0\'';
          $status_filter .= 'Outstanding';
        }elseif($invoice_status == '5'){
          $invoice_status_q .= '`invoice`.`is_invoiced` = \'0\' AND `invoice`.`is_paid` = \'0\''; 
          $status_filter .= 'Future Invoice';
        }else{

        }
    }else{
      $status_filter = 'All Invoice Status';
    }


      

    if($invoice_status == '1' || $invoice_status == '5'){
      $date_sort_filter = " AND UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`invoice_date_req`, '%d/%m/%Y') ) >= $date_a
      AND UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`invoice_date_req`, '%d/%m/%Y') ) <= $date_b ";
    }else{
      $date_sort_filter = " AND UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`set_invoice_date`, '%d/%m/%Y') ) >= $date_a
      AND UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`set_invoice_date`, '%d/%m/%Y') ) <= $date_b ";
    }


    $progress_claim_q = '';

    if($progress_claim != ''){

      $progress_claim_q = ($project_num_q != '' || $client_q != '' || $invoice_status_q != '' ? 'AND ' : '');

      $progress_claim_arr = explode(',',$progress_claim);
      $progress_claim_loops = count($progress_claim_arr);
      $progress_claim_q .= '(';

      foreach ($progress_claim_arr as $progress_claim_key => $progress_claim_val) {

        if($progress_claim_val == 'VR'){
          $progress_claim_q .= '`invoice`.`label` = \'VR\' ';
        }elseif($progress_claim_val == 'F'){
          $progress_claim_q .= '(`invoice`.`label` <> \'VR\' AND `invoice`.`label` <> \'\' )';
        }else{
          $progress_claim_q .= '`invoice`.`order_invoice` = \''.$progress_claim_val.'\' ';
        }

        if($progress_claim_key < $progress_claim_loops-1){
          $progress_claim_q .= " OR ";
        }
      }

      $progress_claim_q .= ')';
    }

    $project_manager_q = '';
    $project_manager_arr = explode('|',$project_manager);
    if($project_manager != ''){
      $project_manager_q = ($project_num_q != '' || $client_q != '' || $invoice_status_q != '' || $progress_claim_q != '' ? 'AND ' : '');
      $project_manager_q .= '`project`.`project_manager_id` =  \''.$project_manager_arr['1'].'\'';
      $project_manager_filter = $project_manager_arr['0'];
    }else{
      $project_manager_filter = 'All Project Managers';
    }


    $order_q = '';
    $sort = '';
    if($invoice_sort == 'clnt_asc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `company_details`.`company_name` ASC';
      $sort = 'Client Name A-Z';
    }elseif($invoice_sort == 'clnt_desc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `company_details`.`company_name` DESC';
      $sort = 'Client Name Z-A';
    }elseif($invoice_sort == 'inv_d_asc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `in_set_ord` DESC';
      $sort = 'Invoiced Date DESC';
    }elseif($invoice_sort == 'inv_d_desc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `in_set_ord` ASC';
      $sort = 'Invoiced Date ASC';
    }elseif($invoice_sort == 'prj_num_asc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `invoice`.`project_id` ASC';
      $sort = 'Project Number Asc';
    }elseif($invoice_sort == 'prj_num_desc'){
      $order_q = '  GROUP BY `invoice`.`invoice_id`  ORDER BY `invoice`.`project_id` DESC';
      $sort = 'Project Number Desc';
    }elseif($invoice_sort == 'invcing_d_desc'){
      $order_q = 'ORDER BY `unix_invoice_date_req` DESC';
      $sort = 'Invoicing Date Desc';
    }elseif($invoice_sort == 'invcing_d_asc'){
      $order_q = 'ORDER BY `unix_invoice_date_req` ASC';
      $sort = 'Invoicing Date Asc';
    }else { }

    $has_where = '';
    if($project_num_q != '' || $progress_claim_q != '' || $client_q != '' || $invoice_status_q != '' || $project_manager_q != ''){
      $has_where = 'WHERE';
    }



    if($focus_comp_id > 0){
      $has_where = "WHERE `project`.`focus_company_id` = '$focus_comp_id' AND ";
    }else{
      $has_where = 'WHERE ';
    }


    $table_q = $this->reports_m->select_list_invoice($has_where,$project_num_q,$client_q,$invoice_status_q.' '.$date_sort_filter ,$progress_claim_q,$project_manager_q,$order_q);


    $records_num = 0;

    $content .= '<div class="def_page"><div class="clearfix header"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block"  style="margin-top:-10px; margin-bottom:10px;" ><br />'.$status_filter.' List Report</h1></div><br />';
    $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th width="20%">Client Name</th><th width="20%">Project Name</th><th>Project No</th>';

    if($inv_date_type == 2){
      $content .= '<th>Invoiced Date</th>';

    }else{
      $content .= '<th>Invoicing Date</th>';
    }


    $content .= '<th>Finish Date</th><th>Progress Claim</th><th>Percent</th><th>Amount</th><th>Outstanding</th></tr></thead><tbody>';


    $total_project_value = 0;
    $total_outstading_value = 0;
    $is_estimated = 0;
    $total_estimate_value = 0;


    if($doc_type == 'pdf'){

      foreach ($table_q->getResult() as $row){
        $is_estimated = 0;

        $project_total_values = $this->projects->fetch_project_totals($row->invoice_project_id);

        if($row->label == 'VR'){
          $progress_order = 'Variation';
        }elseif($row->label != 'VR' && $row->label != ''){
          $progress_order = $row->label;
        }else{
          $progress_order = $row->invoice_project_id.'P'.$row->order_invoice;       
        }

        if($progress_order == 'Variation'){
          //$project_total_percent = $project_total_values['variation_total'];
          $project_total_percent = $row->variation_total;
        }else{
          if($row->is_paid == 1 ){
            $project_total_percent = $row->invoiced_amount;
          }else{
          //  $project_total_percent = $row->project_total * ($row->progress_percent/100);
          // set revision for estimate budget
            if($row->install_time_hrs > 0 || $row->work_estimated_total > 0.00   ){
              $project_total_percent = $row->project_total * ($row->progress_percent/100);
            }else{
              $project_total_percent = $row->budget_estimate_total * ($row->progress_percent/100);
              $is_estimated = 1;
              $total_estimate_value = $total_estimate_value + ( $row->budget_estimate_total * ($row->progress_percent/100) );
            }
          // set revision for estimate budget
          }
        }

        $outstanding = $this->invoice->get_current_balance($row->invoice_project_id,$row->invoice_id,$project_total_percent);

        if( $row->is_invoiced == '0'){
          $outstanding = '0.00';
        }

        $total_project_value = $total_project_value + $project_total_percent;
        $total_outstading_value = $outstanding + $total_outstading_value;
        $project_total_percent = number_format($project_total_percent,2);
        $outstanding = number_format($outstanding,2);

        $content .= '<tr><td>'.$row->company_name.'</td><td>'.$row->project_name.'</td><td>'.$row->invoice_project_id.'</td>';

        if($inv_date_type == 2){
          $content .= '<td>'.$row->set_invoice_date.'</td>';
        }else{
          $content .= '<td>'.$row->invoice_date_req.'</td>';
        }

        $content .= '<td>'.$row->date_site_finish.'</td><td>'.$progress_order.'</td><td>'.number_format($row->progress_percent,2).'</td><td><span class="estimated_'.$is_estimated.'">'.$project_total_percent.'</span></td><td>'.$outstanding.'</td></tr>';
        $records_num++;
      } 

      $content .= '</tbody></table>';
      $content .= '<hr /><p style="margin-top:10px;"><strong>Project Manager:</strong> '.$project_manager_filter.' &nbsp;  &nbsp;  &nbsp; <strong> Note:</strong> All Prices are EX-GST &nbsp;  &nbsp;  &nbsp; <strong>Project Total Ex-GST:</strong> $ '.number_format($total_project_value,2).'  &nbsp;  &nbsp;  &nbsp; <strong>Outstading Ex-GST:</strong> $ '.number_format($total_outstading_value,2).'     &nbsp;  &nbsp;  &nbsp; <span class="estimated_1"><strong>Estimate Ex-GST:</strong> $ '.number_format($total_estimate_value,2).'</span>   <br /></p><br />';
      $content .= '<style>.estimated_1{ color:green; font-weight:bold; }</style>';
      $content .= '</div>';

      $footer_text = '';
      $footer_text .= 'Client: '.$client_filter.'   ';


      if($date_filter_a != '' || $date_filter_b != ''){
        $footer_text .= 'Date: '.$date_filter_a.' - '.$date_filter_b.'    ';
      }

      $footer_text .= 'Sort: '.$sort.'    ';
      $footer_text .= 'Records Found: '.$records_num;


      $my_pdf = $this->html_form($content,'landscape','A4','invoices','temp',$footer_text);
      echo $my_pdf;
    }else{ /////////// cSV

      $content = "client_name,project_name,project_no,invoiced_date,finish_date,progress_claim,percent,amount,outstanding,focus_company\n";


      foreach ($table_q->getResult() as $row){
        $project_total_values = $this->projects->fetch_project_totals($row->invoice_project_id);

        if($row->label == 'VR'){
          $progress_order = 'Variation';
        }elseif($row->label != 'VR' && $row->label != ''){
          $progress_order = $row->label;
        }else{
          $progress_order = $row->invoice_project_id.'P'.$row->order_invoice;       
        }


        if($progress_order == 'Variation'){
          $project_total_percent = $row->variation_total;
        }else{

          if($row->is_paid == 1 ){
            $project_total_percent = $row->invoiced_amount;
          }else{
            $project_total_percent = $row->project_total * ($row->progress_percent/100);
          }
        }

        $outstanding = $this->invoice->get_current_balance($row->invoice_project_id,$row->invoice_id,$project_total_percent);

        if( $row->is_invoiced == '0'){
          $outstanding = '0.00';
        }

        $total_project_value = $total_project_value + $project_total_percent;
        $total_outstading_value = $outstanding + $total_outstading_value;

        $content .=  str_replace(',', ' ', $row->company_name).','. str_replace(',', ' ', $row->project_name).','.$row->invoice_project_id.',';

        if($inv_date_type == 2){
          $content .= $row->set_invoice_date.',';
        }else{
          $content .= $row->invoice_date_req.',';
        }

        $content .= $row->date_site_finish.','.$progress_order.','.$row->progress_percent.','.$project_total_percent.','.$outstanding.','.$row->focus_company_id."\n";
      }

      $fname = strtolower( str_replace(' ','_', $status_filter)  );
      $log_time = time();
      $name = $fname.'_'.$log_time.'.csv';
      
      write_file('./docs/temp/'.$name, $content);

      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename('./docs/temp/'.$name).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('./docs/temp/'.$name));

      flush(); // Flush system output buffer
      readfile('./docs/temp/'.$name);
      delete_files('./docs/temp/'.$name);
    }
  }


  public function client_supply_report(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $data_val = explode('*',$this->request->getPost('ajax_var'));
    }else{
      $data_val = explode('*',$this->request->getGet('ajax_var'));
    }


    $content = '';
    $pdf_content = '';
    $csv_content = '';
    $total_project_value = 0;
    $custom = '';
    
    $current_date = date("d/m/Y");

    $project_number_supply  = $data_val['0'];
    $project_manager_csply  = $data_val['1'];
    $supply_status      = $data_val['2'];

    $warehouse_delivery_a = $data_val['3'];
    $warehouse_delivery_b = $data_val['4'];
    $goods_arrived_a    = $data_val['5'];
    $goods_arrived_b    = $data_val['6'];

    $delivery_to_site_a   = $data_val['7'];
    $delivery_to_site_b   = $data_val['8'];
    $completed_delivery_a = $data_val['9']; 
    $completed_delivery_b = $data_val['10'];

    $supply_report_sort   = $data_val['11'];

    $data_filter = '';
    $supply_status_selected = '';

    $pm_selected_arr = explode('|', $project_manager_csply);

    if($project_number_supply != ''){
      $custom .= " AND  `client_supply`.`project_id` = '".$project_number_supply."' ";    
    }

    if($project_manager_csply != ''){
      $custom .= " AND  `project`.`project_manager_id` = '".$pm_selected_arr[0]."' ";   
    }

    switch ($supply_status) {
      case "1":
      $custom .= " AND `client_supply`.`is_delivered_date` IS NULL AND `client_supply`.`date_goods_arrived` = '' ";
      $data_filter .= "Status: Inbound  ";
      $supply_status_selected = 'Inbound';
      break;

      case "2":
      $custom .= " AND `client_supply`.`is_delivered_date` IS NULL AND `client_supply`.`date_goods_arrived` != '' ";
      $data_filter .= "Status: Outbound  ";
      $supply_status_selected = 'Outbound';
      break;

      case "3":
      $custom .= " AND `client_supply`.`is_delivered_date` IS NOT NULL AND `client_supply`.`date_goods_arrived` != '' ";
      $data_filter .= "Status: Completed  ";
      $supply_status_selected = 'Completed';
      break;
      
      default:
      $custom .= ' ';
      $data_filter .= "Status: All Status  ";
      $supply_status_selected = 'All Status';
    }

    if($warehouse_delivery_a != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_expected` , '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$warehouse_delivery_a', '%d/%m/%Y') ) ";    
    }

    if($warehouse_delivery_b != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_expected` , '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$warehouse_delivery_b', '%d/%m/%Y') ) ";    
    }

    if($warehouse_delivery_a != '' || $warehouse_delivery_b != ''){
      $data_filter .= "Delivery Date To Warehouse: $warehouse_delivery_a - $warehouse_delivery_b  ";
    }

    if($goods_arrived_a != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_arrived` , '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$goods_arrived_a', '%d/%m/%Y') ) ";    
    }

    if($goods_arrived_b != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_arrived` , '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$goods_arrived_b', '%d/%m/%Y') ) ";    
    }

    if($goods_arrived_a != '' || $goods_arrived_b != ''){
      $data_filter .= "Date Goods Arrived: $goods_arrived_a - $goods_arrived_b  ";
    }

    if($delivery_to_site_a != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$delivery_to_site_a', '%d/%m/%Y') ) ";    
    }

    if($delivery_to_site_b != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$delivery_to_site_b', '%d/%m/%Y') ) ";    
    }

    if($delivery_to_site_a != '' || $delivery_to_site_b != ''){
      $data_filter .= "Delivery To Site Date: $delivery_to_site_a - $delivery_to_site_b  ";
    }

    if($completed_delivery_a != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$completed_delivery_a', '%d/%m/%Y') ) ";    
    }

    if($completed_delivery_b != ''){
      $custom .= " AND UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$completed_delivery_b', '%d/%m/%Y') ) ";    
    }

    if($completed_delivery_a != '' || $completed_delivery_b != ''){
      $data_filter .= "Completed Date: $completed_delivery_a - $completed_delivery_b  ";
    }

    switch ($supply_report_sort) {
      case "wdas":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_expected` , '%d/%m/%Y') ) ASC ";
      $data_filter .= 'Sort: Warehouse Delivery Asc  ';
      break;
      case "wdds":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_expected` , '%d/%m/%Y') ) DESC ";
      $data_filter .= 'Sort: Warehouse Delivery Desc  ';
      break;
      case "awas":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_arrived` , '%d/%m/%Y') ) ASC ";
      $data_filter .= 'Sort: Arrived to Warehouse Asc  ';
      break;
      case "awds":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`date_goods_arrived` , '%d/%m/%Y') ) DESC ";
      $data_filter .= 'Sort: Arrived to Warehouse Desc  ';
      break;
      case "dsas":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) ASC ";
      $data_filter .= 'Sort: Delivery To Site Asc  ';
      break;
      case "dsds":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`delivery_date` , '%d/%m/%Y') ) DESC ";
      $data_filter .= 'Sort: Delivery To Site Desc  ';
      break;
      case "cdas":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`is_delivered_date` , '%d/%m/%Y') ) ASC ";
      $data_filter .= 'Sort: Completed Delivery Asc  ';
      break;
      case "cdds":
      $custom .= " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE( `client_supply`.`is_delivered_date` , '%d/%m/%Y') ) DESC ";
      $data_filter .= 'Sort: Completed Delivery Desc  ';
      break;
      default:
      $custom .= ' ORDER BY `client_supply`.`client_supply_id` ASC ';
      $data_filter .= 'Sort: Oldest Supply First  ';
    }

    $table_q = $this->reports_m->client_supply_report_q($custom);
    $records_num = 0;

    $content .= '<div class="def_page"><div class="clearfix header"><img src="'.base_url().'/img/focus-logo-print.png" align="left" class="block" style="margin-top:-30px; " /><h1 class="text-right block"  style="margin-top:-10px; margin-bottom:10px;" ><br />'.$supply_status_selected.' Supply Report</h1></div><br />';
    $content .= '<table id="" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr>';
    $content .= '<th width="5%">Project</th><th width="25%">Client</th><th width="24%">Supply Name</th><th width="25%">Warehouse</th>';

    if($supply_status == 2 || $supply_status == 3){
      $content .= '<th width="8%">Outbound</th><th width="8%">Completed</th>';
    }elseif($supply_status == 4){
      $content .= '<th width="8%">Inbound</th><th width="8%">Outbound</th>';
    }else{
      $content .= '<th width="8%">Inbound</th><th width="8%">Arrived</th>';
    }

    $content .= '</tr></thead><tbody>';

    foreach ($table_q->getResult() as $row){

      if($row->is_delivered_date != ''){
        $content .= '<tr style="background-color:#BEF5BE;">';
      }elseif($row->date_goods_arrived != ''){

        if( $row->unix_dlvy_dt < strtotime(date('Y-m-d')) ){
          $content .= '<tr style="background-color:#FFC6C6;">';
        } else{
          $content .= "<tr>";
        }

      }else{
        if($row->date_goods_arrived == ''){

          if( $row->unix_dt_gds_expt < strtotime(date('Y-m-d')) ){
            $content .= '<tr style="background-color:#FFC6C6;">';
          } else {
            $content .= "<tr>";
          }

        }else{
          $content .= "<tr>";
        }
      }


      $content .= "<td>$row->project_id</td>
      <td>$row->company_name</td>
      <td>$row->supply_name</td>
      <td>$row->warehouse</td>";
   

      if($supply_status == 2 || $supply_status == 3){
        $content .= '<td width="8%">'.$row->delivery_date.'</td><td width="8%">'.$row->is_delivered_date.'</td>';
      }elseif($supply_status == 4){
        $content .= '<td width="8%">'.$row->date_goods_expected.'</td><td width="8%">'.$row->delivery_date.'</td>';
      }else{
        $content .= "<td>$row->date_goods_expected</td><td>$row->date_goods_arrived</td>";
      }


      $content .= "</tr>";
      $records_num++;
    }

    $content .= '</tbody></table>';
    $content .= '<hr /><p style="margin-top:10px;">';
    $content .= '<strong> Legend :</strong>  &nbsp;  &nbsp; <span style="background:#BEF5BE; color:#000; padding:3px;">&nbsp; Delivered &nbsp;</span>  &nbsp; &nbsp; &nbsp; <span style="background:#FFC6C6; color:#000; padding:3px;">&nbsp; Overdue Delivery &nbsp;</span>';
    $content .= '</div>';


    $footer_text = '';

    if($project_manager_csply != ''){
      $footer_text .= 'Project Manager: '.$pm_selected_arr[1].'   ';
    }

    $footer_text .= $data_filter.'   ';
    $footer_text .= 'Records Found: '.$records_num;

    $my_pdf = $this->html_form($content,'landscape','A4','invoices','temp',$footer_text);
    echo $my_pdf;


  }















}