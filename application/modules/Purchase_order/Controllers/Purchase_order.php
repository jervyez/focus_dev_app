<?php
// module created by Jervy 13-1-2023
namespace App\Modules\Purchase_order\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Projects\Controllers\Projects;
use App\Modules\Projects\Models\Projects_m;

use App\Modules\Works\Models\Works_m;

use App\Modules\Purchase_order\Models\Purchase_order_m;


class Purchase_order extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();
    $this->purchase_order_m = new Purchase_order_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $this->users = new Users();
    $this->user_model = new Users_m();

    $this->users->_check_user_access('purchase_orders',1);

    $data = array();

    $data['po_list'] = $this->purchase_order_m->get_po_list();
    $data['work_joinery_list'] = $this->purchase_order_m->get_work_joinery_list();

  //  $data['reconciled_list'] = null; //$this->purchase_order_m->get_reconciled_list();  
  //  $data['reconciled_list_joinery'] = null; //$this->purchase_order_m->get_reconciled_work_joinery_list();


    $data['all_focus_company'] = $this->admin_m->fetch_all_company_focus(); 

    $data['users'] = $this->user_model->fetch_user();
    $data['prj_pm'] = $this->purchase_order_m->get_prj_pm();  

    $data['main_content'] = 'App\Modules\Purchase_order\Views\purchase_order_home';
    $data['screen'] = 'Outstanding Purchase Order';

    $data['page_title'] = 'Outstanding Purchase Order';

    return view('App\Views\page',$data);
  }

  

  public function check_contractor_insurance($company_id){
    $this->company_m = new Company_m();


    $company_detail_q = $this->company_m->fetch_all_company($company_id);
    $get_result_arr = $company_detail_q->getResultArray();   
    $row = array_shift($get_result_arr);

    if($row['company_type_id'] != 2){
      return null;
    }

//var_dump($row);


  if($row['public_liability_expiration'] !== ""){
    $ple_raw_data = $row['public_liability_expiration'];
    $ple_arr =  explode('/',$ple_raw_data);
    $ple_day = $ple_arr[0];
    $ple_month = $ple_arr[1];
    $ple_year = $ple_arr[2];
    $ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
  }

  if($row['workers_compensation_expiration'] !== ""){
    $wce_raw_data = $row['workers_compensation_expiration'];
    $wce_arr =  explode('/',$wce_raw_data);
    $wce_day = $wce_arr[0];
    $wce_month = $wce_arr[1];
    $wce_year = $wce_arr[2];
    $wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
  }

  if($row['income_protection_expiration'] !== ""){
    $ipe_raw_data = $row['income_protection_expiration'];
    $ipe_arr =  explode('/',$ipe_raw_data);
    $ipe_day = $ipe_arr[0];
    $ipe_month = $ipe_arr[1];
    $ipe_year = $ipe_arr[2];
    $ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
  }
  $today = date('Y-m-d');
  
  $complete = 0;
  $incomplete = 0;
  
  if($row['company_type_id'] == '2'){
    if($row['has_insurance_public_liability'] == 1){
      if($row['public_liability_expiration'] !== ""){
        if($ple_date <= $today){
          $incomplete = 1;
        }else{
          if($row['has_insurance_workers_compensation'] == 1){
            if($row['workers_compensation_expiration'] !== ""){
              if($wce_date <= $today){
                $incomplete = 1;
              }else{
                $complete = 1;
              }
            }else{
              $incomplete = 1;
            }
          }else{
            if($row['has_insurance_income_protection'] == 1){
              if($row['income_protection_expiration'] !== ""){
                if($ipe_date <= $today){
                  $incomplete = 1;
                }else{
                  $complete = 1;
                }
              }else{
                $incomplete = 1;
              }
            }else{
              $incomplete = 1;
            }
          }
        }
      }else{
        $incomplete = 1;
      }
      
    }else{
      $incomplete = 1;
    }
  }

  $font_color = "";
  if($row['company_type_id'] == '2'){
    if($complete == 1){
      $font_color = "blue_ok";
    }else{
      if($incomplete == 1){
        $font_color = "red_bad";
      }
    }
  }


  return $font_color;

  }


  public function containsDecimal( $value ) {
    if ( strpos( $value, "." ) !== false ) {
      return true;
    }
    return false;
  }
  public function ext_to_inc_gst($value,$gst){

    if($this->containsDecimal($gst)){
      $gst = $gst/100;
    }

    $gst = round($gst,2);
    $value = str_replace(',', '', $value);
    $converted = $value + ($value*$gst);
    $converted = round($converted,2);
    return $converted;
  }



  public function po_review_process(){

    $this->user_model = new Users_m();

    $po_data_arr = explode('_', $this->request->getPost('ajax_var') ); // $this->request->getPost('ajax_var')

    $po_number = $po_data_arr[0];
    $project_id = $po_data_arr[1];
    $pm_id = $po_data_arr[2];
    $pa_id = $po_data_arr[3];

    $estimate = str_replace(",", "", $po_data_arr[4]);
    $raw_estimate = $po_data_arr[4];


    $estimated = (float)$estimate;

    $action = $po_data_arr[5];
    $date_set = date('d/m/Y');

    $this->purchase_order_m->insert_po_review($po_number,$project_id,$date_set,$estimated,$action);

    $action_notice = '';

    if($action == 1){
      $action_notice = 'Request for Removal';
    }

    if($action == 2){
      $action_notice = 'Waiting for Invoice';
    }

    
 //   date_default_timezone_set("Australia/Perth");
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $type = "INSERT";
    $actions = "Purchase Order Review on PO Number: ".$po_number." set to: ".$action_notice;
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);



    $content = '<p>A Purchase Order has been reviewed and it needs you to update its details.</p><p>You can click <a href="'.site_url().'purchase_order?po_rev=1" target="_blank" title="Go to Sojourn - Purchase Orders"><strong>this link</strong></a> to go to the purchase order screen.<p>';
    $content .= '<br /><p>PO Number: <strong>'.$po_number.'</strong><br /> Project: <strong>'.$project_id.'</strong><br />Action: <strong>'.$action_notice.'</strong><br />Amount: <strong>$'.number_format($estimated,2).' EX-GST</strong></p>';
    $content .= '<br /><p>You can click <a href="'.site_url().'projects/view/'.$project_id.'" target="_blank" title="Go to Sojourn - Projects"><strong>this link</strong></a> to go to the project details.<br />Before making changes make sure the job date is removed.</p>';

    $q_pm_email = $this->purchase_order_m->get_contact_user($pm_id);
    $get_result = $q_pm_email->getResult();
    $pm_data_email = array_shift($get_result);
    $pm_email = $pm_data_email->general_email;  

    $q_pa_email = $this->purchase_order_m->get_contact_user($pa_id);
    $get_result = $q_pa_email->getResult();
    $pm_data_email = array_shift($get_result);
    $pa_email = $pm_data_email->general_email;  

    $send_to = $pm_email;
    $add_cc = $pa_email;

    if($action == 1){
      //review_code
      //  $this->po_review_mail($project_id,$po_number,$content,$send_to,$add_cc);
      //review_code
    }
  }


  public function po_history(){
    $this->company_m = new Company_m();

    $ajax_var_raw = $_POST['ajax_var'];

    if (strpos($ajax_var_raw,'-') !== false) {

      $work_id_arr = explode('-',$ajax_var_raw);
      $work_id = $work_id_arr[0];

      $joinery_id_arr = explode('/',$work_id_arr[1]);
      $joinery_id = $joinery_id_arr[0];

    }else{
      $work_id_arr = explode('/',$ajax_var_raw);
      $work_id = $work_id_arr[0];
      $joinery_id = 0;
    }


    $get_work_raw = $this->purchase_order_m->select_po_history($work_id,$joinery_id);


    if($get_work_raw->getNumRows() > 0){
      foreach ($get_work_raw->getResultArray() as $row){

        $query_notes = $this->company_m->fetch_notes($row['notes_id']);
        $getResultArray = $query_notes->getResultArray();
        $temp_data = array_shift($getResultArray);
        $comments = $temp_data['comments'];

        echo '<tr><td>'.$row['work_invoice_date'].'</td><td>$'.$row['amount'].'</td><td>'.$row['invoice_no'].'</td><td>'.$comments.'</td></tr>';
      }
    }else{
      echo '<tr><td colspan="4">No Transactions</td></tr>';
    }

    $last_history_trans_raw = $this->purchase_order_m->select_last_po_history($work_id,$joinery_id);
    $getResultArray = $last_history_trans_raw->getResultArray();
    $last_history_trans = array_shift($getResultArray);

    if($last_history_trans_raw->getNumRows() > 0){
      echo '<tr><td colspan="4" class="clearfix"><button type="button" class="btn btn-danger" style="float:right" onClick="remove_last('.$last_history_trans['work_purchase_order_id'].','.$last_history_trans['work_id_po'].','.$last_history_trans['joinery_id'].'); remove_trans_bttn();" ><i class="fa fa-exclamation-triangle"></i> Remove Last Transaction</button></td></tr>';
    }
  }


  public function remove_last_trans(){
    $this->user_model = new Users_m();

    $post_ajax_arr = explode('*',$_POST['ajax_var']);
    $work_purchase_order_id = $post_ajax_arr[0];    
    $work_id_po = $post_ajax_arr[1];
    $joinery_id = $post_ajax_arr[2];

    $this->purchase_order_m->remove_po($work_purchase_order_id);
    $this->purchase_order_m->po_set_outstanding($work_id_po,$joinery_id);

    // user log remove transaction
    $joinery = '';
    if($joinery_id>0){ 
      $joinery = '-'.$joinery_id;
    }

    $type = 'Remove Transaction';

    $project_id = '0';
    $actions = 'Removed a transaction at purchase order No.'.$work_id_po.''.$joinery;
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type,'4');
    // user log remove transaction
  }


  public function get_reconciled_result(){
    $this->projects = new Projects();

    if(isset($_POST['ajax_var'])){
      $po_number = $_POST['ajax_var'];


      $list_q = $this->purchase_order_m->get_reconciled_list(1000000,$po_number);

      foreach ($list_q->getResultArray() as $row){
        $val_rec_a = $this->check_balance_po($row['works_id']);
        $prj_defaults = $this->projects->display_project_applied_defaults($row['project_id']);

        echo '<tr><td><a href="#" data-toggle="modal" data-target="#reconciliated_po_modal" data-backdrop="static" id="'.$row['works_id'].'-'.$row['project_id'].'" onclick="return_outstanding_po_item(\''.$row['works_id'].'-'.$row['project_id'].'\');" class="return_outstanding_po_item">'.$row['works_id'].'</a></td><td><a href="'.site_url().'projects/view/'.$row['project_id'].'" >'.$row['project_id'].'</a></td><td>'.$row['work_cpo_date'].'</td><td><a href="'.site_url().'works/update_work_details/'.$row['project_id'].'/'.$row['works_id'].'">';

        if($row['contractor_type']==2){

          if($row['job_sub_cat']=='Other'){
            echo $row['other_work_desc'];
          }else{ 
            echo $row['job_sub_cat'];
          }

        }elseif($row['contractor_type']==3){
          if($row['supplier_cat_name']=='Other'){
            echo $row['other_work_desc'];
          }else{
            echo $row['supplier_cat_name'];
          }
        }

        $r_inc_gst_price = $this->ext_to_inc_gst($row['price'],$prj_defaults['admin_gst_rate']);
        echo '</a></td><td>'.$row['contractor_name'].'</td><td>'.$row['project_name'].'</td><td>'.$row['reconciled_date'].'</td><td>'.$row['client_name'].'</td><td>'.$row['user_first_name'].' '.$row['user_last_name'].'</td><td><span class="ex-gst">'.number_format($row['price'],2).'</span><br /><span class="hide">-</span><span class="inc-gst">'.number_format($r_inc_gst_price,2).'</span></td>';
        echo '<td><span class="ex-gst">'.number_format($val_rec_a,2).'</span><br /><span class="hide">-</span><span class="inc-gst">'.number_format($this->ext_to_inc_gst($val_rec_a,$prj_defaults['admin_gst_rate']),2).'</span></td>';
        echo '</tr>';
      }


    $list_join_q = $this->purchase_order_m->get_reconciled_work_joinery_list($po_number);
    foreach ($list_join_q->getResultArray() as $row_j){

      $r_total_price_exgst = $row_j['price'] + $r_total_price_exgst;
      $r_inc_gst_price_j = $this->ext_to_inc_gst($row_j['price'],$j_prj_defaults['admin_gst_rate']);
      $r_total_price_incgst = $r_inc_gst_price_j + $r_total_price_incgst;

      $prj_defaults = $this->projects->display_project_applied_defaults($row_j['project_id']);
      $val_item_rc = $this->check_balance_po($row_j['works_id'],$row_j['work_joinery_id']);
      echo '<tr><td><a href="#" data-toggle="modal" data-target="#reconciliated_po_modal" data-backdrop="static" id="'.$row_j['works_id'].'-'.$row_j['work_joinery_id'].'/'.$row_j['project_id'].'" onclick="return_outstanding_po_item(\''.$row_j['works_id'].'-'.$row_j['work_joinery_id'].'/'.$row_j['project_id'].'\');" class="select_po_item">'.$row_j['works_id'].'-'.$row_j['work_joinery_id'].'</a></td><td><a href="'.site_url().'projects/view/'.$row_j['project_id'].'" >'.$row_j['project_id'].'</a></td><td>'.$row_j['work_cpo_date'].'</td><td><a href="'.site_url().'works/update_work_details/'.$row_j['project_id'].'/'.$row_j['works_id'].'">';
      echo $row_j['joinery_name'];
      echo '</a></td><td>'.$row_j['contractor_name'].'</td><td>'.$row_j['project_name'].'</td><td>'.$row_j['reconciled_date'].'</td><td>'.$row_j['client_name'].'</td><td>'.$row_j['user_first_name'].' '.$row_j['user_last_name'].'</td><td><span class="ex-gst">'.number_format($row_j['price'],2).'</span><br /><span class="hide">-</span><span class="inc-gst">'.number_format($r_inc_gst_price_j,2).'</span></td>';
      echo '<td><span class="ex-gst">'.number_format($val_item_rc,2).'</span><br /><span class="hide">-</span><span class="inc-gst">'.number_format($this->ext_to_inc_gst($val_item_rc,$prj_defaults['admin_gst_rate']),2).'</span></td>';
       
      echo '</tr>';
    } 
 

   
      if($list_join_q->getNumRows() < 1 && $list_q->getNumRows() < 1 ){
        echo '<tr><td colspan="11">No Records Found, please try a different PO Number.</td></tr>';
      }
   
    }
  }



  public function purchase_order_filtered(){
    $this->admin_m = new Admin_m();
    $this->user_model = new Users_m();

    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    foreach ($q_admin_defaults->getResultArray() as $row){
      $data['gst_rate'] = $row['gst_rate'];
    }

    $start_date = $_POST['start_date'];// ?? '01/03/2023';
    $end_date = $_POST['end_date'];// ?? '015/03/2023';

    $data['po_list'] = $this->purchase_order_m->get_po_list();
    $data['po_list_ordered'] = $this->purchase_order_m->get_po_list_order_by_project($start_date,$end_date);
    $data['work_joinery_list'] = $this->purchase_order_m->get_work_joinery_list($start_date,$end_date);
    $data['reconciled_list'] = $this->purchase_order_m->get_reconciled_list();  
    $data['reconciled_list_joinery'] = $this->purchase_order_m->get_reconciled_work_joinery_list(); 
    $data['users'] = $this->user_model->fetch_user();
    $data['main_content'] = 'purchase_order_home';

    return view('App\Modules\Purchase_order\Views\po_filtered_v',$data);
  }

  public function no_insurance_send_email(){
    $this->company_m = new Company_m();
    $this->projects_m = new Projects_m();
    $this->works_m = new Works_m();
    $this->admin_m = new Admin_m();


    // changed 10/16/2015 jrv
    $data_var = explode('*', $_POST['ajax_var']);
    $po_number = $data_var[0];
    $data['po_number'] = $po_number;
    $data['po_reference_value'] = $data_var[1];
    // changed 10/16/2015 jrv
    

    $works_q = $this->works_m->display_works_selected($po_number);
    foreach ($works_q->getResultArray() as $row){
      $contractor_id = $row['company_client_id'];
      $proj_id = $row['project_id'];
    }

    $proj_q = $this->projects_m->select_particular_project($proj_id);
    foreach ($proj_q->getResultArray() as $row){
      $client_id = $row['company_id'];
      $compname = $row['company_name'];
//=== Focus Company Details =========
      $focus_company_id = $row['focus_company_id'];

      $project_manager_id = $row['project_manager_id'];

      $data['focus_company_id'] = $focus_company_id;
      $focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
      foreach ($focus_comp_q->getResultArray() as $focus_comp_row){
        $data['focus_logo'] = $focus_comp_row['logo'];
        $data['focus_comp'] = $focus_comp_row['company_name'];
        $data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
        $data['acn'] = $focus_comp_row['acn'];
        $data['focus_abn'] = $focus_comp_row['abn'];
        $data['focus_email'] = $focus_comp_row['general_email'];
        $address_id = $focus_comp_row['address_id'];
        $focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
        foreach ($focus_comp_q->getResultArray() as $comp_address_row){
          $po_box = $comp_address_row['po_box'];
          if($po_box == ""){
            $data['po_box'] = "";
          }else{
            $data['po_box'] = "PO".$comp_address_row['po_box'];
          }
          $data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
        }
      }
//=== Focuse Company Details =========
    }


    $contractor_q = $this->works_m->display_works_selected_contractor($po_number,$contractor_id);
    foreach ($contractor_q->getResultArray() as $row){
      $contact_person_id = $row['contact_person_id'];
      $comp_q = $this->company_m->fetch_all_contact_persons($contact_person_id);
      foreach ($comp_q->getResultArray() as $cont_row){
        $email_id = $cont_row['email_id'];
        $email_q = $this->company_m->fetch_email($email_id);
        foreach ($email_q->getResultArray() as $email_row){
          $e_mail = $email_row['general_email'];
          $data['email'] = $e_mail;
        }
      }
    }
    $company_q = $this->company_m->fetch_all_company($contractor_id);
    foreach ($company_q->getResultArray() as $comp_row){
      if($comp_row['public_liability_expiration'] !== ""){
        $ple_raw_data = $comp_row['public_liability_expiration'];
        $ple_arr =  explode('/',$ple_raw_data);
        $ple_day = $ple_arr[0];
        $ple_month = $ple_arr[1];
        $ple_year = $ple_arr[2];
        $ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
      }

      if($comp_row['workers_compensation_expiration'] !== ""){
        $wce_raw_data = $comp_row['workers_compensation_expiration'];
        $wce_arr =  explode('/',$wce_raw_data);
        $wce_day = $wce_arr[0];
        $wce_month = $wce_arr[1];
        $wce_year = $wce_arr[2];
        $wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
      }

      if($comp_row['income_protection_expiration'] !== ""){
        $ipe_raw_data = $comp_row['income_protection_expiration'];
        $ipe_arr =  explode('/',$ipe_raw_data);
        $ipe_day = $ipe_arr[0];
        $ipe_month = $ipe_arr[1];
        $ipe_year = $ipe_arr[2];
        $ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
      }
      $today = date('Y-m-d');
      
      $complete = 0;
      $incomplete = 0;
      
      if($comp_row['company_type_id'] == '2'){
        if($comp_row['has_insurance_public_liability'] == 1){
          if($comp_row['public_liability_expiration'] !== ""){
            if($ple_date <= $today){
              $incomplete = 1;
            }else{
              if($comp_row['has_insurance_workers_compensation'] == 1){
                if($comp_row['workers_compensation_expiration'] !== ""){
                  if($wce_date <= $today){
                    $incomplete = 1;
                  }else{
                    $complete = 1;
                  }
                }else{
                  $incomplete = 1;
                }
              }else{
                if($comp_row['has_insurance_income_protection'] == 1){
                  if($comp_row['income_protection_expiration'] !== ""){
                    if($ipe_date <= $today){
                      $incomplete = 1;
                    }else{
                      $complete = 1;
                    }
                  }else{
                    $incomplete = 1;
                  }
                }else{
                  $incomplete = 1;
                }
              }
            }
          }else{
            $incomplete = 1;
          }
          
        }else{
          $incomplete = 1;
        }
      }
    }

    //review_code
    /*
    if($incomplete == 1){
      $data['contractor_id'] = $contractor_id;
      $default_msg_q = $this->admin_m->fetch_admin_default_email_message();
      foreach ($default_msg_q->result_array() as $row){
        $message_content = $row['message_content'];
        $sender_name = $row['sender_name'];
        $sender_email = $row['sender_email'];
        $bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au,mark.obis2012@gmail.com";
        $subject = $row['subject'];
      }

      $data['message'] = $message_content;
      $data['sender'] = $sender_name;
      $data['send_email'] = $sender_email;
      $data['comp_phone'] = "Ph. 08 6305 0991";
      $data['comp_address_line1'] = "Unit 3 / 86 Inspiration Drive";
      $data['comp_address_line2'] = "Wangara WA 6065";
      $data['comp_address_line3'] = "PO Box 1326 Wangara DC WA 6947";

      $data['comp_name'] = "FSF Group Pty Ltd";
      $data['abn1'] = "ABN 61 167 776 678";
      $data['comp_name2'] = "Focus Shopfit Pty Ltd";
      $data['abn2'] = "ABN 16 159 087 984";
      $data['comp_name3'] = "Focus Shopfit NSW Pty Ltd";
      $data['abn3'] = "ABN 17 164 759 102";


      $message = $this->load->view('message_view',$data,TRUE);

      require_once('PHPMailer/class.phpmailer.php');
      require_once('PHPMailer/PHPMailerAutoload.php');

      $mail = new phpmailer(true);
      $mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
      $mail->port = 587;

      $mail->setFrom($sender_email, $sender_name);
      $mail->addAddress($e_mail);    // Add a recipient
      $mail->addReplyTo($sender_email);
      
      $email_bcc_arr =  explode(',', $bcc_email);
      $no_arr = count($email_bcc_arr);
      $x = 0;
      while($x < $no_arr){
        $email_bcc = $email_bcc_arr[$x];
        $mail->addBCC($email_bcc);
        $x++;
      }

      $mail->smtpdebug = 2;
      $mail->isHTML(true);                                  // Set email format to HTML

      $mail->Subject = $subject;
      $mail->Body    = $message;

      if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
      } else {
        echo 'Message has been sent';
      }
    }else{
      echo 0;
    }
    */
    //review_code




  }




  public function insert_work_invoice(){
    $this->company_m = new Company_m();
    $this->user_model = new Users_m();

    if (isset($_POST['is_reconciled'])) {
      $is_reconciled = 1;
    }else{
      $is_reconciled = 0;
    }


    $work_invoice_date = $_POST['po_date_value'];
    $work_id_po = $_POST['po_number_item'];
    $notes = $_POST['po_notes_value'];
    $invoice_no = $_POST['po_reference_value'];
    $amount = str_replace( ',', '',$_POST['po_amount_value']);
    $is_reconciled_value = $is_reconciled;
    $po_project_id = $_POST['po_project_id'];


    if (strpos($work_id_po,'-') !== false) {

      $work_id_arr = explode('-',$work_id_po);
      $work_id = $work_id_arr[0];

      $joinery_id_arr = explode('/',$work_id_arr[1]);
      $joinery_id = $joinery_id_arr[0];

    }else{
      $work_id_arr = explode('/',$work_id_po);
      $work_id = $work_id_arr[0];
      $joinery_id = 0;
    }

    $notes_id = $this->company_m->insert_notes($notes);

    $this->purchase_order_m->insert_work_invoice($work_invoice_date,$work_id_po,$joinery_id,$notes_id,$invoice_no,$amount);

    // user log reconcile
    $type = 'Transaction';
    // user log reconcile

    if($is_reconciled_value == 1){
      $this->purchase_order_m->po_set_reconciled($work_id_po,$work_invoice_date,$joinery_id);
      $type = 'Reconciliation';
    }

    // user log reconcile
    if($joinery_id > 0){
      $joinery = '-'.$joinery_id;
    }else{
      $joinery = '';
    }

    $project_id = $po_project_id;
    $actions = 'Made a '.$type.' to purchase order No.'.$work_id_po.''.$joinery;
    date_default_timezone_set("Australia/Perth");
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type,'4');
    // user log reconcile


    return redirect()->to('/purchase_order');

  }



//review_code
  public function po_review_mail($project_id,$po_number,$content,$send_to,$add_cc){
    $this->user_model = new Users_m();

    $static_defaults_q = $this->user_model->select_static_defaults();
    $get_result_arr = $static_defaults_q->getResultArray();
    $static_defaults = array_shift($get_result_arr);

    $po_email_cc = $static_defaults['po_email_cc'];


    $mail = new PHPMailer;                                    
    $mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
    $mail->Port = 587;

    $mail->setFrom('noreply@focusshopfit.com.au', 'Sojourn - PO Review');
    $mail->addReplyTo('noreply@focusshopfit.com.au', 'Do Not Reply');

    $mail->addAddress($send_to);
    $mail->addCC($add_cc);

    if($po_email_cc != ''){ 
      $po_cc_arr = explode(',', $po_email_cc);
      foreach ($po_cc_arr as $key => $value) {
        $value_cc = str_replace(' ', '', $value);
        $mail->addCC($value_cc);
      }
    }


    $mail->isHTML(true);
    $year = date('Y');

    $mail->Subject = 'Purchase Order Review: '.$project_id.' - '.$po_number;
    $mail->Body    = $content.'<br /><br />Sent via Sojourn auto-email service, you have a purchase order that needs action.<br />Please log-in to Sojourn and apply the necessary changes as per details above.<br />If you are already logge-in, you can click this <a title="View Purchase Order: '.$po_number.'" target="_blank" href="'.site_url().'purchase_order?vpo='.$po_number.'" >link</a> to go to the purchase order screen and review.<br /><br />&copy; FSF Group '.$year;

    if(!$mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      echo 'Message has been sent';
    }


  }
//review_code




  public function check_balance_po($work_id_po,$joinery_id=0){
    $get_work_raw = $this->purchase_order_m->get_work($work_id_po);
    $get_result_arr = $get_work_raw->getResultArray();
    $work_details = array_shift($get_result_arr);

    $total_paid_raw = $this->purchase_order_m->get_po_total_paid($work_id_po,$joinery_id);
    $get_result_arr = $total_paid_raw->getResultArray();
    $total_paid = array_shift($get_result_arr);

    if($joinery_id!=0){

      $get_joinery_raw = $this->purchase_order_m->get_joinery($joinery_id,$work_id_po);
      $get_result_arr = $get_joinery_raw->getResultArray();  
      $joinery_details = array_shift($get_result_arr);

      return ($joinery_details['unit_price']*$joinery_details['qty']) - $total_paid["total_paid"];

    }else{
      return $work_details['price'] - $total_paid["total_paid"];
    }

  }






}