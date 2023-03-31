<?php

// module created by Jervy 20-9-2022
namespace App\Modules\Reports\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

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










  public function test(){

  }











}