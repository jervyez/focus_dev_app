<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Invoice\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Invoice\Models\Invoice_m;

use App\Modules\Projects\Controllers\Projects;
use App\Modules\Projects\Models\Projects_m;

use App\Modules\Company\Models\Company_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Purchase_order\Controllers\Purchase_order;



use App\Modules\Reports\Controllers\Reports;

class Invoice extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();
    $this->invoice_m = new Invoice_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {
    $this->users = new Users();
    $this->user_model = new Users_m();

    $data = array();

    $this->users->_check_user_access('invoice',1);
    $data['main_content'] = 'App\Modules\Invoice\Views\invoice_home';
    $data['screen'] = 'Invoice';
    $data['users'] = $this->user_model->fetch_user();
    $data['page_title'] = 'Invoice List';

    return view('App\Views\page',$data);
  }

  public function sum_total_wip_cost(){
    $data = array();
    $data['main_content'] = 'App\Modules\Invoice\Views\landing';
    echo '<p class="">USERS SCREEN LIST HERE ******</p>';

  }

  public function if_has_vr($project_id){
    $invoice_vr = $this->invoice_m->fetch_invoice_vr($project_id);
    return $invoice_vr->getNumRows();
  }

  public function get_total_amount_paid_project($project_id){
    $payment_q = $this->invoice_m->get_total_amount_paid_project($project_id);
    $getResultArray = $payment_q->getResultArray();
    $total_paid_amount = array_shift($getResultArray);
    return $total_paid_amount['total_paid'] ?? 0.00;
  }

  public function project_invoice($project_id){


    $this->projects = new Projects();

    if($this->session->get('invoice') >= 1){
      $data = array();

      $q_get_project_costs = $this->invoice_m->get_project_costs($project_id);

      $getResultArr_a = $q_get_project_costs->getResultArray();
      $data = array_shift($getResultArr_a);

      $applied_admin_settings_raw = $this->projects->display_project_applied_defaults($project_id);
      $data = array_merge($data, $applied_admin_settings_raw);

      $data['final_total_quoted'] = $data['work_quoted_total'];

      return view('App\Modules\Invoice\Views\invoice_project',$data);
    }
  }

  public function if_project_invoiced_full($project_id,$with_vr=0){
    $invoices_q = $this->invoice_m->get_invoices($project_id);
    $not_completed = 0;

    foreach ($invoices_q->getResult() as $invoice){
      if($invoice->is_invoiced == 0){
        $not_completed = 1;
      }

      if($with_vr == 1 && $invoice->label == 'VR' ){
        if($invoice->is_invoiced == 0){
          $not_completed = 1;
        }
      }
    }

    if($not_completed == 0){
      return 1;
    }else{
      return 0;
    }
  }

  public function get_paid_result(){
    $claim_id = $_POST['ajax_var'];
    $this->paid_table($claim_id);
  }



  public function paid_table($claim_id=''){

    $this->projects = new Projects();
    $this->projects_m = new Projects_m();

    $this->company_m = new Company_m();

    $this->purchase_order = new Purchase_order();

    $q_invoice_project = $this->invoice_m->list_invoice_project_paid($claim_id);
    $total_invoice = 0;
    $total_outstanding = 0;

    foreach ($q_invoice_project->getResult() as $invoice_project) {
      $q_unpaid_invoiced = $this->invoice_m->list_unpaid_invoiced($invoice_project->project_id,'1');

      foreach ($q_unpaid_invoiced->getResult() as $invoice) { 

        $project_totals = $this->projects->fetch_project_totals($invoice->project_id);
        $project_defaults = $this->projects->display_project_applied_defaults($invoice->project_id);

        $q_project_details = $this->projects_m->fetch_project_details($invoice->project_id);
        $getResultArray = $q_project_details->getResultArray();
        $project_details = array_shift($getResultArray);

        //$amount = $project_details['project_total']*($invoice->progress_percent/100);
        $amount = $invoice->invoiced_amount;

        $client_details_raw = $this->company_m->fetch_company_details($project_details['client_id']);
        $getResultArray = $client_details_raw->getResultArray();
        $client_details = array_shift($getResultArray);

        if($invoice->label == 'VR'){
          $invoice_progress = $invoice->project_id.$invoice->label;
          $amount = $project_totals['variation_total'];

          $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
          $total_outstanding = $total_outstanding + round($outstanding,2);
          $outstanding = number_format($outstanding,2);

          $progress_percent = '100.00';
        }elseif($invoice->label != ''){
          $invoice_progress = $invoice->label;
        }else{
          $invoice_progress = $invoice->project_id.'P'.$invoice->order_invoice;
        }

        $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
        $total_outstanding = $total_outstanding + round($outstanding,2);
        $outstanding = number_format($outstanding,2);

        if($invoice->label == 'VR'){
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_vr_'.$invoice->order_invoice;
        }elseif($invoice->label != ''){
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_f_'.$invoice->order_invoice;
        }else{
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_p_'.$invoice->order_invoice;
        }

        $total_invoice = $total_invoice + $amount;


        echo '<tr id="'.$project_defaults['admin_gst_rate'].'"><td><a href="'.site_url().'projects/view/'.$invoice->project_id.'?submit_invoice='.$invoice->project_id.'">'.$invoice->project_id.'</a></td><td>'.$project_details['project_name'].'</td><td><a onclick="invoice_paid_modal(this)" href="#" data-toggle="modal" data-target="#invoice_paid_modal" data-backdrop="static" id="'.$progress_id.'">'.$invoice_progress.'</a></td><td>'.$client_details['company_name'].'</td><td>'.$invoice->set_invoice_date.'</td><td><span class="invocie_amount_total ex-gst">'.number_format($amount,2).'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($amount,$project_defaults['admin_gst_rate']),2).'</span></td><td><span class="ex-gst invocie_outstanding">'.$outstanding.'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($outstanding,$project_defaults['admin_gst_rate']),2).'</span></td></tr>';
      }
    }
    
    echo '<tr class="hidden hide"><td><input type="hidden" class="total-paid-row" id="total-paid-row" value="'.number_format($total_invoice,2).'" /></td><td><input type="hidden" class="total-paid-outstanding-row" id="total-paid-outstanding-row" value="'.number_format($total_outstanding,2).'" /></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
  }

  public function list_payment_history(){
    $payment_history = explode('*',$_POST['ajax_var']);

    $project_id = $payment_history['0'];
    $invoice_id = $payment_history['1'];

    $fetch_payment_history_raw = $this->invoice_m->fetch_payment_history($project_id,$invoice_id);
    $has_payments = $fetch_payment_history_raw->getNumRows();

    if($has_payments > 0):

      foreach ($fetch_payment_history_raw->getResult() as $row) {
        echo '<tr><td>'.$row->payment_date.'</td><td><strong>$'.number_format($row->amount_exgst,2).'</strong> <strong class="pull-right">ex-gst</strong></td><td>'.$row->reference_number.'</td></tr>';
        echo '<tr><td colspan="3"><strong>Notes: </strong>'.$row->comments.'</td></tr>';
      }

    endif;
    
  }


  public function remove_recent_payment(){
    $payment_history = explode('*',$_POST['ajax_var']);

    $project_id = $payment_history['0'];
    $invoice_id = $payment_history['1'];

    $q_fetch_to_remove = $this->invoice_m->fetch_list_to_remove($project_id,$invoice_id);

    $getResultArray = $q_fetch_to_remove->getResultArray();
    $fetch_to_remove = array_shift($getResultArray);
    $this->invoice_m->delete_payments($fetch_to_remove['payment_id'],$fetch_to_remove['notes_id'],$invoice_id);

  }



  public function progress_payment(){
    $this->company_m = new Company_m();

    $post_ajax_arr = explode('*',$_POST['ajax_var']);
    $payment_date = $post_ajax_arr['0'];

    $progress_id_raw = explode('_',$post_ajax_arr['1']);
    $project_id = $progress_id_raw['0'];
    $order_invoice = $progress_id_raw['1'];


    $notes = $post_ajax_arr['2'];
    $reference_number = $post_ajax_arr['3'];

    $amount_exgst = $post_ajax_arr['4'];
    $amount_exgst = preg_replace("/,/", "", $amount_exgst);



    $outstanding = $post_ajax_arr['6']; 
    $outstanding =  substr($outstanding,1);

    $outstanding = preg_replace("/,/", "", $outstanding);


    $is_paid = $post_ajax_arr['5'];
    $invoice_id = $post_ajax_arr['7'];

    $notes_id = $this->company_m->insert_notes($notes);
    $this->invoice_m->insert_payment($project_id,$notes_id,$amount_exgst,$invoice_id,$payment_date,$reference_number);
    $this->invoice_m->set_payment_invoice($invoice_id,$is_paid);

    $invoice_vr = $this->fetch_vr($project_id);

    if( $this->if_invoiced_all($project_id) && $this->is_all_paid($project_id) ){
      $this->invoice_m->set_project_as_paid($project_id);
    } 

  }



  public function invoice_table($order=''){

    $this->projects = new Projects();
    $this->projects_m = new Projects_m();
    $this->company_m = new Company_m();
    $this->purchase_order = new Purchase_order();

    $q_invoice_project = $this->invoice_m->list_invoice_project();
    $total_invoice = 0;
    $total_outstanding = 0;

    foreach ($q_invoice_project->getResult() as $invoice_project) {
      $q_unpaid_invoiced = $this->invoice_m->list_unpaid_invoiced($invoice_project->project_id,0,$order);

      foreach ($q_unpaid_invoiced->getResult() as $invoice) {

        $project_totals = $this->projects->fetch_project_totals($invoice->project_id);
        $project_defaults = $this->projects->display_project_applied_defaults($invoice->project_id);

        $q_project_details = $this->projects_m->fetch_project_details($invoice->project_id);
        $getResultArray = $q_project_details->getResultArray();
        $project_details = array_shift($getResultArray);

        //$amount = $project_details['project_total']*($invoice->progress_percent/100);
        $amount = $invoice->invoiced_amount;

        $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
        $total_outstanding = $total_outstanding + round($outstanding,2);
        $outstanding = number_format($outstanding,2);

        $progress_percent = number_format($invoice->progress_percent,2);

        $client_details_raw = $this->company_m->fetch_company_details($project_details['client_id']);
        $getResultArray = $client_details_raw->getResultArray();
        $client_details = array_shift($getResultArray);


        if($invoice->label == 'VR'){
          $invoice_progress = $invoice->project_id.$invoice->label;
          $amount = $project_totals['variation_total'];

          $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
          $total_outstanding = $total_outstanding + round($outstanding,2);
          $outstanding = number_format($outstanding,2);

          $progress_percent = '100.00';
        }elseif($invoice->label != ''){
          $invoice_progress = $invoice->label;
        }else{
          $invoice_progress = $invoice->project_id.'P'.$invoice->order_invoice;
        }

        if($invoice->label == 'VR'){
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_vr_'.$invoice->order_invoice;
        }elseif($invoice->label != ''){
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_f_'.$invoice->order_invoice;
        }else{
          $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_p_'.$invoice->order_invoice;
        }
        $total_invoice = $total_invoice + round($amount,2); 

        echo '<tr id="'.$project_defaults['admin_gst_rate'].'"><td><a href="'.site_url().'projects/view/'.$invoice->project_id.'?submit_inv';
        echo 'oice='.$invoice->project_id.'">'.$invoice->project_id.'</a></td><td>'.$project_details['project_name'].'</td><td><a onclick="invoice_payment_modal(this)"';
        echo ' href="#" data-toggle="modal" data-target="#invoice_payment_modal" data-backdrop="static" id="'.$progress_id.'">'.$invoice_progress.'</a></td>';
        echo '<td>'.$client_details['company_name'].'</td><td>'.$progress_percent.'</td><td>'.$invoice->set_invoice_date.'</td><td><span class="ex-gst invocie_amount_total';
        echo ' outsdng">'.number_format($amount,2).'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($amount,$project_defaults['admin_gst_rate']),2).'</span>';
        echo '</td><td><span class="invocie_outstanding ex-gst outsding-b">'.$outstanding.'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($outstanding,$project_defaults['admin_gst_rate']),2).'</span></td></tr>';
      }
    }
    echo '<tr class="hidden hide"><td><input type="hidden" class="total-invoiced-row" id="total-invoiced-row" value="'.number_format($total_invoice,2).'" /></td><td><input type="hidden" class="total-invoiced-outstanding-row" id="total-invoiced-outstanding-row" value="'.number_format($total_outstanding,2).'" /></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
  

    }


  public function get_focus_company_select(){
 
    $this->company_m = new Company_m();

    $q_focus_comp = $this->company_m->display_company_by_type(5);
    foreach ($q_focus_comp->getResult() as $focus_comp):

      if($focus_comp->company_id != 4):
        echo '<option value="'.$focus_comp->company_id.'">'.$focus_comp->company_name.'</option>';
      endif;
    endforeach;
  }


  public function forecast_invoice_claim(){

    $date_a = date('d/m/Y'); 

    $fetch_progress_claim_q = $this->invoice_m->fetch_progress_claim($date_a);
    $claims = array();


    foreach ($fetch_progress_claim_q->getResult() as $invoice) {

      //  $project_totals = $this->projects->fetch_project_totals($invoice->project_id);
      $project_defaults = $this->projects->display_project_applied_defaults($invoice->project_id);

      $q_project_details = $this->projects_m->fetch_project_details($invoice->project_id);
        //$project_details = array_shift($q_project_details->result_array());

        //$amount = $project_details['project_total']*($invoice->progress_percent/100);
      $amount = $invoice->claim_amount;

      $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
      //  $total_outstanding = $total_outstanding + round($outstanding,2);
      $outstanding = number_format($outstanding,2);

      $progress_percent = $invoice->progress_percent;

      if($invoice->label == 'VR'){
        $invoice_progress = $invoice->project_id.$invoice->label;
          //$amount = $project_totals['variation_total'];

        $outstanding = $this->get_current_balance($invoice->project_id,$invoice->invoice_id,$amount);
        //  $total_outstanding = $total_outstanding + round($outstanding,2);
        $outstanding = number_format($outstanding,2);

        $progress_percent = '100.00';
      }elseif($invoice->label != ''){
        $invoice_progress = $invoice->label;
      }else{
        $invoice_progress = $invoice->project_id.'P'.$invoice->order_invoice;
      }

      if($invoice->label == 'VR'){
        $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_vr_'.$invoice->order_invoice;
      }elseif($invoice->label != ''){
        $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_f_'.$invoice->order_invoice;
      }else{
        $progress_id = $invoice->invoice_id.'_'.$invoice->project_id.'_p_'.$invoice->order_invoice;
      }
      //$total_invoice = $total_invoice + round($amount,2); 

      echo '<tr><td><a href="'.site_url().'projects/view/'.$invoice->project_id.'?submit_invoice='.$invoice->project_id.'">'.$invoice->project_id.'</a></td><td>'.$invoice->project_name.'</td><td>'.$invoice_progress.'</td><td>'.$invoice->company_name.'</td><td>'.$progress_percent.'</td><td>'.$invoice->invoice_date_req.'</td><td><span class="ex-gst invocie_amount_total outsdng">'.number_format($amount,2).'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($amount,$project_defaults['admin_gst_rate']),2).'</span></td><td><span class="invocie_outstanding ex-gst outsding-b">'.$outstanding.'</span><br /><span class="inc-gst">'.number_format($this->purchase_order->ext_to_inc_gst($outstanding,$project_defaults['admin_gst_rate']),2).'</span></td></tr>';
    }
  }


  public function job_book($project_id){

    $this->company_m = new Company_m();
    $this->projects = new Projects();
    $this->projects_m = new Projects_m();
    $this->user_model = new Users_m();

    $q_proj = $this->projects_m->fetch_complete_project_details($project_id);

    if($q_proj->getNumRows() > 0){
      $getResultArray = $q_proj->getResultArray();
      $data = array_shift($getResultArray);

      $q_focus_company = $this->company_m->display_company_detail_by_id($data['focus_company_id']);
      $getResultArray = $q_focus_company->getResultArray();
      $focus_company = array_shift($getResultArray);
      $data['focus_company_id'] = $focus_company['company_id'];
      $data['focus_company_name'] = $focus_company['company_name'];

      $q_client_company = $this->company_m->display_company_detail_by_id($data['client_id']);
      $getResultArray = $q_client_company->getResultArray();
      $client_company = array_shift($getResultArray);
      $data['client_company_id'] = $client_company['company_id'];
      $data['client_company_name'] = $client_company['company_name'];

      $q_contact_person = $this->company_m->fetch_all_contact_persons($data['primary_contact_person_id']);
      $getResultArray = $q_contact_person->getResultArray();
      $contact_person = array_shift($getResultArray);
      $data['contact_person_id'] = $contact_person['contact_person_id'];
      $data['contact_person_fname'] = $contact_person['first_name'];
      $data['contact_person_lname'] = $contact_person['last_name'];

      $q_fetch_phone = $this->company_m->fetch_phone($contact_person['contact_number_id']);
      $getResultArray = $q_fetch_phone->getResultArray();
      $contact_person_phone = array_shift($getResultArray);
      

      if($contact_person_phone['office_number'] != ''): 
        $data['contact_person_phone_office'] = $contact_person_phone['area_code'].' '.$contact_person_phone['office_number'];
      else: $data['contact_person_phone_office'] = '';
      endif;


      if($contact_person_phone['direct_number'] != ''): 
        $data['contact_person_phone_direct'] = $contact_person_phone['area_code'].' '.$contact_person_phone['direct_number'];
      else: $data['contact_person_phone_direct'] = '';
      endif;

      if($contact_person_phone['mobile_number'] != ''):
        $data['contact_person_phone_mobile'] = $contact_person_phone['mobile_number'];
      else: $data['contact_person_phone_mobile'] = '';
      endif;

      if($contact_person_phone['after_hours'] != ''):
        $data['contact_person_phone_afterhours'] = $contact_person_phone['area_code'].' '.$contact_person_phone['after_hours'];
      else: $data['contact_person_phone_afterhours'] = '';
      endif;

      $query_client_address = $this->company_m->fetch_complete_detail_address($client_company['address_id']);
      $getResultArray = $query_client_address->getResultArray();
      $temp_data = array_shift($getResultArray);
      $data['query_client_address_postcode'] = $temp_data['postcode'];
      $data['query_client_address_suburb'] = ucwords(strtolower($temp_data['suburb']));
      $data['query_client_address_po_box'] = $temp_data['po_box'];
      $data['query_client_address_street'] = ucwords(strtolower($temp_data['street']));
      $data['query_client_address_unit_level'] = ucwords(strtolower($temp_data['unit_level']));
      $data['query_client_address_unit_number'] = $temp_data['unit_number'];
      $data['query_client_address_state'] = $temp_data['name'];

      $q_fetch_contact_details_primary = $this->company_m->fetch_contact_details_primary($client_company['company_id']);
      $getResultArray = $q_fetch_contact_details_primary->getResultArray();
      $company_contact_details_primary_detail = array_shift($getResultArray);

      $data['company_contact_details_area_code'] = $company_contact_details_primary_detail['area_code'] ?? null;
      $data['company_contact_details_office_number'] = $company_contact_details_primary_detail['office_number'] ?? null;
      $data['company_contact_details_direct_number'] = $company_contact_details_primary_detail['direct_number'] ?? null;
      $data['company_contact_details_mobile_number'] = $company_contact_details_primary_detail['mobile_number'] ?? null;
      $data['company_contact_details_after_hours'] = $company_contact_details_primary_detail['after_hours'] ?? null;
      $data['company_contact_details_general_email'] = $company_contact_details_primary_detail['general_email'] ?? null;
      $data['company_contact_details_direct'] = $company_contact_details_primary_detail['direct'] ?? null;
      $data['company_contact_details_accounts'] = $company_contact_details_primary_detail['accounts'] ?? null;
      $data['company_contact_details_maintenance'] = $company_contact_details_primary_detail['maintenance'] ?? null;

      $shopping_center_q = $this->projects_m->select_shopping_center($data['address_id']);
      $getResultArray = $shopping_center_q->getResultArray();
      $shopping_center = array_shift($getResultArray);

      $data['shopping_center_id'] = $shopping_center['shopping_center_id'] ?? 0;
    //  $data['shopping_center_brand_name'] = $shopping_center['shopping_center_brand_name'];
    //  $data['shopping_common_name'] = $shopping_center['common_name'];




      $data['shopping_center_brand_name'] = $data['shop_name'];
      $data['shopping_common_name'] = $data['shop_name'];






      $query_address= $this->company_m->fetch_complete_detail_address($data['address_id']);
      $getResultArray = $query_address->getResultArray();
      $temp_data = array_shift($getResultArray);
      $data['postcode'] = $temp_data['postcode'];
      $data['suburb'] = ucwords(strtolower($temp_data['suburb']));
      $data['po_box'] = $temp_data['po_box'];
      $data['street'] = ucwords(strtolower($temp_data['street']));
      $data['unit_level'] = ucwords(strtolower($temp_data['unit_level']));
      $data['unit_number'] = $temp_data['unit_number'];
      $data['state'] = $temp_data['name'];
    // $data['address_id'] = $data['address_id'];

      $data['shortname'] = $temp_data['shortname'];
      $data['state_id'] =  $temp_data['state_id'];
      $data['phone_area_code'] = $temp_data['phone_area_code']; 

      $p_query_address = $this->company_m->fetch_complete_detail_address($data['invoice_address_id']);
      $getResultArray = $p_query_address->getResultArray();
      $p_temp_data = array_shift($getResultArray);
      $data['i_po_box'] = $p_temp_data['po_box'];
      $data['i_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
      $data['i_unit_number'] = $p_temp_data['unit_number'];
      $data['i_street'] = ucwords(strtolower($p_temp_data['street']));
      $data['i_suburb'] = ucwords(strtolower($p_temp_data['suburb']));
      $data['i_state'] = $p_temp_data['name'];
      $data['i_postcode'] = $p_temp_data['postcode'];


      $applied_admin_settings_raw = $this->projects->display_project_applied_defaults($project_id);
      $project_totals_arr = $this->projects->fetch_project_totals($project_id);
      $data = array_merge($data, $project_totals_arr);
      $data = array_merge($data, $applied_admin_settings_raw);

      $q_project_manager = $this->user_model->fetch_user($data['project_manager_id']);
      $getResultArray = $q_project_manager->getResultArray();
      $project_manager = array_shift($getResultArray);
      $data['pm_user_id'] = $project_manager['user_id'];
      $data['pm_user_first_name'] = $project_manager['user_first_name'];
      $data['pm_user_last_name'] = $project_manager['user_last_name'];

      $q_project_admin = $this->user_model->fetch_user($data['project_admin_id']);
      $getResultArray = $q_project_admin->getResultArray();
      $project_admin = array_shift($getResultArray);
      $data['pa_user_id'] = $project_admin['user_id'];
      $data['pa_user_first_name'] = $project_admin['user_first_name'];
      $data['pa_user_last_name'] = $project_admin['user_last_name'];

      $q_project_estiamator_id = $this->user_model->fetch_user($data['project_estiamator_id']);
      $getResultArray = $q_project_estiamator_id->getResultArray();
      $project_estiamator = array_shift($getResultArray);
      if (!empty($project_estiamator)):
        $data['pe_user_id'] = $project_estiamator['user_id'];
        $data['pe_user_first_name'] = $project_estiamator['user_first_name'];
        $data['pe_user_last_name'] = $project_estiamator['user_last_name'];
      else:
        $data['pe_user_id'] = "";
        $data['pe_user_first_name'] = "";
        $data['pe_user_last_name'] = "";
      endif;



    }


    return view('App\Modules\Invoice\Views\invoice_job_book_canvas',$data);
  }


  public function check_invoice_progress($project_id,$project_total){
    $project_id_old = $this->session->get('project_id_old');
    $project_total_old = $this->session->get('project_total_old');
    $same_prj_id = 0;
    $same_total = 0;


    if(isset($project_id_old) && trim($project_id_old!='')){
      if($project_id == $project_id_old){
        $same_prj_id = 1;
      }else{
        $this->session->get('project_id_old',$project_id);
      }
    }else{
      $this->session->get('project_id_old',$project_id);
    }


    if(isset($project_total_old) && trim($project_total_old!='')){
      if( $project_total != 0 && $project_total_old != 0){
        if($project_total == $project_total_old){
          $same_total = 1;
        }else{
          $same_total = 2;
          $this->session->get('project_total_old',$project_total);
        }
        $this->session->get('project_total_old',$project_total);
      }
    }else{
      $this->session->get('project_total_old',$project_total);
    }

    $counter = 0;
    $un_invoiced_count = 0;
    $invoiced  = array();
    $invoiced_ammount  = array();
    $old_percents = 0;

    $invoiced_raw = 0;
    $invoiced_total_percent = 0;

    //$project_total = round($project_total,2);

    $progress_new = array();


    if($same_prj_id == 1 && $same_total == 2){


      $query_list_invoice = $this->invoice_m->list_invoice($project_id);

      foreach ($query_list_invoice->getResult() as $row) {
        //echo '<p>'.$row->invoice_id.' '.$row->is_invoiced.' '.$row->invoiced_amount.' '.$row->progress_percent.' '.$row->order_invoice.' '.$row->label.'</p>';

        if($row->label != 'VR'){

          if($row->is_invoiced != 1){
            $un_invoiced_count++;
          }else{
            //$invoiced_raw = $invoiced_raw + (100 / ($project_total / $row->invoiced_amount  ) );

            $invoiced_raw = round((100 / ($project_total / $row->invoiced_amount  ) ),2);
            $progress_new[$row->invoice_id] = $invoiced_raw;
            $invoiced_total_percent = $invoiced_total_percent + $invoiced_raw;

            $old_percents = $old_percents + $row->progress_percent;
          }    

          $counter++;

        }
      }

      if($un_invoiced_count == 0){
        $un_invoiced_count = 1;
      }

      $added_percent = ($old_percents - $invoiced_total_percent) / $un_invoiced_count;


      $query_list_invoice = $this->invoice_m->list_invoice($project_id);

      foreach ($query_list_invoice->getResult() as $row) {

        if($row->label != 'VR'){

          if($row->is_invoiced == 1){
            $this->invoice_m->update_progress_percent($row->invoice_id,$progress_new[$row->invoice_id]);
          }else{
            $this->invoice_m->update_progress_percent($row->invoice_id,$row->progress_percent+$added_percent);
          }
        }
      }
    }
  }

  public function show_job_book($project_id){
    $fetch_project_jobbook_raw = $this->invoice_m->fetch_project_jobbook($project_id);
    $getResultArray = $fetch_project_jobbook_raw->getResultArray();
    $project_jobbook = array_shift($getResultArray);
    return $project_jobbook;
  }

  public function list_invoiced_items($project_id,$project_total,$variation_total){
    $total = 0;
    $set_var_total = 0;
    $fetch_project_jobbook_raw = $this->invoice_m->list_invoiced_items($project_id);
    foreach ($fetch_project_jobbook_raw->getResult() as $row) {

      echo '<div class="invoices_list">';

      //$progress_percent = $row->progress_percent/100;
      $invoiced_amount = $row->invoiced_amount;

      echo '<p class="mgn-b-10 clearfix">';

      if($row->label == ''){
        echo '<span class="pull-left text-left">'.$row->set_invoice_date.' &nbsp; &nbsp; <strong>'.$row->project_id.'P'.$row->order_invoice.' &nbsp; &nbsp; ';

        echo ($row->is_paid == 1 ? '&#9745; Paid' : '');

        echo '</strong></span> <strong class="pull-right text-right"> EX-GST : $'.number_format($invoiced_amount,2).'</strong><br />';
      }else{

        if($row->label == 'VR'){
          echo '<br />Variation';
          echo '<span class="pull-left text-left">'.$row->set_invoice_date.' &nbsp; &nbsp; <strong>'.$row->project_id.'VR &nbsp; &nbsp; ';
          echo ($row->is_paid == 1 ? '&#9745; Paid' : '');
          echo '</strong></span> <strong class="pull-right text-right"> EX-GST : $'.number_format($variation_total,2).'</strong><br />';
          $set_var_total = 1;

        }else{
          echo '<br />For Final Payment';
          echo '<span class="pull-left text-left">'.$row->set_invoice_date.' &nbsp; &nbsp; <strong>'.$row->project_id.'F &nbsp; &nbsp; ';
          echo ($row->is_paid == 1 ? '&nbsp; &#9745; Paid' : '');
          echo '</strong></span> <strong class="pull-right text-right"> EX-GST : $'.number_format($invoiced_amount,2).'</strong><br />';
        }
        
      }

      echo '</p>';
      echo '</div>';

      $total = $total + $invoiced_amount;

    }

    if($set_var_total == 0){
      $variation_total = 0;
    }

    echo '<p>&nbsp;</p><p>&nbsp;</p><hr /><p class="m_edit"><strong>Total EX-GST  &nbsp; &nbsp; : $'.number_format($total+$variation_total,2).'</strong></p>';
  }

  public function if_invoiced($project_id){ 
    $query_list_invoice = $this->invoice_m->list_invoiced_items($project_id);
    return $query_list_invoice->getNumRows();   
  }

  public function get_project_invoiced($project_id,$project_total,$vr_total){
    $total = 0;
    $fetch_project_jobbook_raw = $this->invoice_m->list_invoiced_items($project_id);

    foreach ($fetch_project_jobbook_raw->getResult() as $row) {

      if($row->is_invoiced == '1' && $row->label != 'VR'){
        $progress_percent = $row->progress_percent/100;
        $total = $total + ($progress_percent*$project_total);
      }

      if($row->label == 'VR' && $row->is_invoiced == '1'){
        //$progress_percent = $row->progress_percent/100;
        $total = $total + $vr_total;
      }
    }

    return $total;
  }

  public function set_project_as_fully_invoiced($project_id){
    $this->invoice_m->set_project_as_fully_invoiced($project_id);
  }

  public function insert_invoice_progress(){
    $invoice_arr = explode('*',$_POST['ajax_var']);
    $invoice_date_req = $invoice_arr[4];
    $project_id = $invoice_arr[5];
    $progress_percent = $invoice_arr[0];
    $label =  ($invoice_arr[3] != 'undefined' ? $invoice_arr[3] : '');
    $order_invoice = $invoice_arr[1];
    $this->invoice_m->insert_new_invoice($invoice_date_req, $project_id, $progress_percent,$label,$order_invoice);
  }

  public function delete_all_invoices(){
    $project_id = $_POST['ajax_var'];
    $this->invoice_m->delete_invoice($project_id);
  }

  public function delete_some_invoices(){
    $project_id = $_POST['ajax_var'];
    $this->invoice_m->delete_some_invoice($project_id);   
  }


  public function if_has_invoice($project_id){    
    $query_list_invoice = $this->invoice_m->list_invoice($project_id);
    return $query_list_invoice->getNumRows();
  }

  public function if_completed_invoice($project_id){
    $query_list_invoice = $this->invoice_m->list_invoice($project_id);
    $error = 0;

    foreach ($query_list_invoice->getResult() as $row) {
      if($row->invoice_date_req == ''){
        $error = 1;
      }
    }

    if($error == 1){
      return 0;
    }else{
      return 1; 
    }
  }

  public function is_vr_invoiced($project_id){
    $num_vr = 0;
    $invoice_vr_q = $this->invoice_m->select_vr_invoice($project_id);
    $num_vr = $invoice_vr_q->getNumRows();

    if($num_vr > 0){
      return true;
    }else{
      return  false;
    }
  }

  public function if_invoiced_all($project_id){   
    $query_list_invoice = $this->invoice_m->list_uninvoiced_items($project_id);
    if($query_list_invoice->getNumRows() > 0){

      if($query_list_invoice->getNumRows() === 1){
        $getResultArr_a = $query_list_invoice->getResultArray();
        $list_invoice = array_shift($getResultArr_a);

        $query_list_invoiced = $this->invoice_m->list_invoiced($project_id);
        $getResultArr_b = $query_list_invoiced->getResultArray();
        $list_invoiced = array_shift($getResultArr_b);
 

        if( 
          $list_invoice['label'] != '' && 
          $list_invoice['label'] != 'VR' && 
          $list_invoice['is_invoiced'] == '0' && 
          $list_invoice['invoiced_amount'] == '0.00' && 
          ( isset($list_invoiced['label']) && $list_invoiced['label'] == 'VR' )
          
        ){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }

    }else{
      return true;
    }
  }



  public function get_amount_total_paid_invoice($project_id,$invoice_id){
    $raw_total_paid = $this->invoice_m->get_total_amount_paid($project_id,$invoice_id);
    $getResultArray = $raw_total_paid->getResultArray();
    $total_paid_q = array_shift($getResultArray);
    $has_paid = $raw_total_paid->getNumRows();
    $total_paid = ($has_paid > 0 ? $total_paid_q['total_paid'] : 0);
    return $total_paid;
  }

  public function fetch_vr($project_id){
    $invoice_vr_q = $this->invoice_m->fetch_invoice_vr($project_id);
    $invoice_vr = array();

    if($invoice_vr_q->getNumRows() === 1){
      $getResultArray = $invoice_vr_q->getResultArray();
      $invoice_vr = array_shift($getResultArray);
    }else{

      $invoice_vr['invoice_id'] = 0;
      $invoice_vr['invoice_date_req'] = null;
      $invoice_vr['set_invoice_date'] = null;
      $invoice_vr['project_id'] = 0;
      $invoice_vr['is_paid'] = 0;
      $invoice_vr['progress_percent'] = null;
      $invoice_vr['label'] = null;
      $invoice_vr['is_invoiced'] = 0;
      $invoice_vr['invoiced_amount'] = null;
      $invoice_vr['order_invoice'] = 0;
    }

    return $invoice_vr;
  }






  public function set_invoice_progress(){
    $this->user_model = new Users_m();
    $this->projects_m = new Projects_m();
    $this->reports = new Reports();

    $static_defaults_q = $this->user_model->select_static_defaults();
    $getResultArray = $static_defaults_q->getResultArray();
    $static_defaults = array_shift($getResultArray);

    $invoice_notes_set = '';

    $send_to = $static_defaults['invoice_to']; // change this for the admin sender
    $email_cc_to = $static_defaults['invoice_cc'];

    $project_id = $this->request->getPost('project_number');
    $id_bttn_raw = $this->request->getPost('progress_invoice_id');
    $cc_emails = $this->request->getPost('cc_emails');
    $invoice_notes = $this->request->getPost('invoice_notes'); 
    $raw_invoice_notes = $this->request->getPost('raw_invoice_notes'); 
    $email_list = $this->request->getPost('email_list');
    $job_book_details_id = $this->request->getPost('job_book_details_id');
    $invoice_item_amount = $this->request->getPost('invoice_item_amount');
    $invoice_percent_value = $this->request->getPost('invoice_percent_value');
    $invoice_percent_value = $this->request->getPost('invoice_percent_value');
    $project_total_raw = $this->request->getPost('project_total_raw');
    $date_set_invoice_data = $this->request->getPost('date_set_invoice_data');
    $pdf_content = $this->request->getPost('pdf_content');
    $invoice_id_db = $this->request->getPost('invoice_id_db');
 

    $invoice_data_q = $this->invoice_m->get_invoice_data($invoice_id_db);
    $getResultArray = $invoice_data_q->getResultArray();
    $invoice_data = array_shift($getResultArray);

    $invoice_label = $invoice_data['label'];
    $order_invoice = $invoice_data['order_invoice'];
    $inv_progg_percent = number_format($invoice_data['progress_percent'],2);

    $emails_arr = explode(',',$email_list);
    array_push($emails_arr, $cc_emails);
    array_push($emails_arr, $email_cc_to);


    $user_id = $this->session->get('user_id');
    $user_details_q = $this->user_model->fetch_user($user_id);
    $getResultArray = $user_details_q->getResultArray();
    $user_details = array_shift($getResultArray);
    $user_email = $user_details['general_email'];

    array_push($emails_arr, $user_email);

    $q_proj = $this->projects_m->fetch_complete_project_details($project_id);
    $getResultArray = $q_proj->getResultArray();
    $project_details = array_shift($getResultArray);

    $pm_user_details_q = $this->user_model->fetch_user($project_details['project_manager_id']);
    $getResultArray = $pm_user_details_q->getResultArray();
    $pm_user_details = array_shift($getResultArray);
    $pm_user_email = $pm_user_details['general_email'];


    $pa_user_details_q = $this->user_model->fetch_user($project_details['project_admin_id']);
    $getResultArray = $pa_user_details_q->getResultArray();
    $pa_user_details = array_shift($getResultArray);
    $pa_user_email = $pa_user_details['general_email'];


    $project_notes_current = $project_details['notes'];

    if($project_details['client_contact_person_id'] > 0){

      $cc_pm_user_details_q = $this->user_model->fetch_user($project_details['client_contact_person_id']);
      $getResultArray = $cc_pm_user_details_q->getResultArray();
      $cc_pm_user_details = array_shift($getResultArray);
      $cc_pm_user_email = $cc_pm_user_details['general_email'];

      if (stripos($cc_emails,$cc_pm_user_email) !== false || stripos($email_list,$cc_pm_user_email) !== false) {
        //echo 'true';
      }else{
        $cc_emails .= ','.$cc_pm_user_email;
      }
    }

    array_push($emails_arr, $pm_user_email);

    $updated_emails = array_unique($emails_arr);

    $email_to_remove = array($send_to);
    $result_emails_arr = array_diff($updated_emails, $email_to_remove);
    $result_emails_arr = array_diff( $result_emails_arr, array( '' ) );
    $final_cc_emails = implode (", ", $result_emails_arr);

    $id_bttn = explode('_', $id_bttn_raw);
    $invoice_item_amount = str_replace(',', '', $invoice_item_amount);

    if($invoice_label == 'VR'){ ///// FOR VR
      $inv_curr_set = 'VR';
      $date_req_vr = $project_details['date_site_finish'];
      $date_for_vr = date('d/m/Y'); 
      $prj_inv_total = $invoice_item_amount;

    }else{

      if($invoice_label != ''){   //// FOR Final
        $inv_curr_set = 'F';
        $date_for_vr = $date_set_invoice_data;
        $prj_inv_total = $project_total_raw;
        
      }else{ ////// FOR Progress Claims

        $inv_curr_set = 'P_'.$order_invoice;
        $prj_inv_total = $project_total_raw;
      }

    }




    $invoice_notes_set = '<div class="notes_line"><p class="clearfix"><strong><span class="pull-left block text-left">'.$project_id.''.$inv_curr_set.' - '.$inv_progg_percent.'% of $'.number_format($prj_inv_total,2).'</span> <span class="pull-right block text-right"><strong>$'.number_format($invoice_item_amount,2).' EX-GST</strong></span></strong></p><p class="clearfix"><br />'.$invoice_notes.'</p></div>';
 
    $this->invoice_m->update_job_notes($job_book_details_id,$invoice_notes_set); //!!!!!!!!!!!!!!!!!!!!!!
    $type = 'New Invoice';



    if($invoice_label == 'VR'){ ///// FOR VR
      $this->invoice_m->insert_invoiced_variation($date_req_vr,$date_for_vr,$project_id,$order_invoice);
    }else{ ///// FOR Final
      $this->invoice_m->set_invoiced_progress($date_set_invoice_data,$order_invoice,$project_id,$invoice_item_amount);
    }

    // user log invoices
    $actions = 'New job book for Project No.'.$project_id.' Invoice No.'.$project_id.''.$inv_curr_set;
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type,'3');
    // user log invoices

      

    //echo "ok invocied";

    $pdf_file = $this->reports->pdf($pdf_content,$project_id.''.$inv_curr_set);
    $path_file =  site_url()."docs/inv_jbs/".$pdf_file.'.pdf';



    //review_code
/*

    // PHPMailer class
    $mail = new PHPMailer;
    $mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';          //sojourn.focusshopfit.com.au // Specify main and backup SMTP servers
    $mail->Port = 587;  


    $mail->setFrom('donot-reply-sojourn@focusshopfit.com.au', 'Sojourn - Job Book');
    $mail->addAddress($send_to);    // Add a recipient
    $mail->addAddress($user_email); // Name is optional

    $mail->addReplyTo($user_email);
    
    $mail->addCC($pm_user_email);
    $mail->addBCC('jervyezaballa@gmail.com');

    if($user_email != $pa_user_email){
      $mail->addCC($pa_user_email);
    }

    $cc_em_lst = explode(',',$email_list);
    foreach ($cc_em_lst as $key => $value) {
      $mail->addCC($value);
    }

    if($cc_emails != '')
    {
      $cc_em_lst_cc = explode(',',$cc_emails);
      foreach ($cc_em_lst_cc as $key => $value) {
        $mail->addCC($value);
      }
    }


    $mail->isHTML(true);                                  
    $year = date('Y');

    $mail->Subject = 'Job Book Processing Request - '.$project_id.''.$inv_curr_set;
    $mail->Body    = 'Sent via Sojourn auto-email service, You have an invoice needing to process. Please visit the link provided below to view the job book.<br /><br /><strong>'.$path_file.'</strong><br />.<br /><br />&copy; FSF Group '.$year;

    if(  (isset($raw_invoice_notes) && $raw_invoice_notes != '') ||   (isset($pdf_content) && $pdf_content != '')    ){

      if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;

      } else {
        redirect('projects/view/'.$project_id.'?submit_invoice='.$project_id, 'refresh');
      }
    }else{
      redirect('projects/view/'.$project_id.'?submit_invoice='.$project_id.'&error=1', 'refresh');
    }
*/

    //

//review_code
    return redirect()->to('projects/view/'.$project_id.'?submit_invoice='.$project_id);


  }

  public function is_all_paid($project_id){
    $query_list_invoice = $this->invoice_m->list_invoice($project_id);
    $is_not_paid = 0;

    foreach ($query_list_invoice->getResult() as $row) {
      if($row->is_paid == 0){
        $is_not_paid = 1;
      }
    }

    if($is_not_paid == 0){
      return true;
    }else{
      return false;
    }
  }

  public function un_invoice_item(){
    $invoice_item = explode('*',$_POST['ajax_var']);
    $invoice_id = $invoice_item['0'];
    $project_id = $invoice_item['1'];
    $job_book_notes = $invoice_item['2'];
    $this->invoice_m->un_invoice($invoice_id);

    $project_notes_q = $this->invoice_m->select_project_notes($project_id);
    $getResultArray = $project_notes_q->getResultArray();
    $project_notes = array_shift($getResultArray);

    $this->invoice_m->update_invoice_notes($project_notes["notes_id"],$job_book_notes);

  }


  public function get_current_balance($project_id,$invoice_id,$amount_to_pay){
    $raw_total_paid = $this->invoice_m->get_total_amount_paid($project_id,$invoice_id);
    $getResultArray = $raw_total_paid->getResultArray();
    $total_paid_q = array_shift($getResultArray);

    $has_paid = $raw_total_paid->getNumRows();


    $total_paid = ($has_paid > 0 ? $total_paid_q['total_paid'] : 0);

    $outstanding = ($amount_to_pay - $total_paid);

    $outstanding = round($outstanding, 2); 

    return $outstanding;
  }


public function list_project_invoice($project_id){

  $this->projects = new Projects();


    $file_name = '';


    $project_costs = $this->projects->fetch_project_totals($project_id);


    $query_list_invoice = $this->invoice_m->list_invoice($project_id);

    $counter = 0;
    $un_invoiced = 0;

    foreach ($query_list_invoice->getResult() as $row) {
      $counter++;


      if($row->is_invoiced == 1){

        if($project_id == '35055' && $row->label == ''){
          $progress_percent = $row->progress_percent + 0.001168936757;

          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }elseif($row->label != 'VR' && $row->label != '' && $project_id == '35055'){
          $progress_percent = $row->progress_percent + 0.007662126486;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }elseif($row->label != 'VR' && $row->label != '' && $project_id == '35385'){
          $progress_percent = $row->progress_percent - 0.00313;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }


        elseif($row->label != 'VR' && $row->label != '' && $project_id == '35099'){
          $progress_percent = $row->progress_percent + 0.0034373969295;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }
        
        
        

        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '1'){
          $progress_percent = $row->progress_percent + 0.00422369569;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }

        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '2'){
          $progress_percent = $row->progress_percent + 0.004030212478;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }
        
        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '3'){
          $progress_percent = $row->progress_percent + 0.00170031394;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }

        elseif($row->label != 'VR' && $row->label != '' && $project_id == '36814'){
          $progress_percent = $row->progress_percent + 0.000840465656;
          $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
          $progress_cost = round($progress_cost,2);
        }







else{
          $progress_percent = $row->progress_percent;
          $progress_cost = $row->invoiced_amount;
        }


      }else{

        if($project_id == '35055' && $row->label == ''){
          $progress_percent = $row->progress_percent + 0.001168936757;
        }elseif($row->label != 'VR' && $row->label != '' && $project_id == '35055'){
          $progress_percent = $row->progress_percent + 0.007662126486;
        }elseif($row->label != 'VR' && $row->label != '' && $project_id == '35385'){
          $progress_percent = $row->progress_percent - 0.00313;
        }


        elseif($row->label != 'VR' && $row->label != '' && $project_id == '35099'){
          $progress_percent = $row->progress_percent + 0.0034373969295;
        }




        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '1'){
          $progress_percent = $row->progress_percent + 0.00422369569;
        }

        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '2'){
          $progress_percent = $row->progress_percent + 0.004030212478;
        }
        
        elseif($row->label != 'VR' && $row->label == '' && $project_id == '36814' && $row->order_invoice == '3'){
          $progress_percent = $row->progress_percent + 0.00170031394;
        }

        elseif($row->label != 'VR' && $row->label != '' && $project_id == '36814'){
          $progress_percent = $row->progress_percent + 0.000840465656;
        }




else{
          $progress_percent = $row->progress_percent;
        }

        $progress_cost = ($project_costs['final_total_quoted']*$progress_percent)/100;
        $progress_cost = round($progress_cost,2);

      }


//$progress_cost = ($project_costs['final_total_quoted']*$row->progress_percent)/100;


$outstanding = $this->get_current_balance($row->project_id,$row->invoice_id,$progress_cost);
$outstanding = number_format($outstanding,2);

$total_paid = $this->get_amount_total_paid_invoice($row->project_id,$row->invoice_id);

      echo '<tr><td scope="row" class="t-head" id="">';

      if($row->label != ''){
        echo '<div class=""><input type="text" class="form-control final_payment" value="'.$row->label.'" placeholder="Final Payment"></div>';
      }else{
        echo '<div class="m-top-10 progress-item" id="'.$row->invoice_id.'">Progress '.($row->label != '' ? '0' : $counter).'<span class="progress_counter"></span></div>';
      }


      echo '</th>
<td><div class="input-group"><div class="input-group-addon">%</div><input type="text" '.($row->is_invoiced > 0 ? 'disabled="disabled"' : '').' class="form-control progress-percent" onclick="getHighlight(\'progress-'.($row->label != '' ? '0' : $counter).'-percent\')" onchange="'.($row->label != '' ? 'final_progress' : 'progressPercent').'(this)" value="'.number_format($progress_percent,2).'" placeholder="Percent" id="progress-'.($row->label != '' ? '0' : $counter).'-percent" name="progress-'.$counter.'-percent"/></div></td>
<td>';


      echo '<div class="progress-item" id="'.$row->invoice_id.'"><input type="hidden" name="outstanding" id="progress_outstanding" value="" /><div>';

echo '<input type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" value="'.$row->invoice_date_req.'" '.($row->is_invoiced > 0 ? 'disabled="disabled"' : '').' class="form-control date_daily text-left progress_date" id="progress-'.($row->label != '' ? '0' : $counter).'-date" name="progress-'.($row->label != '' ? '0' : $counter).'-date"></td>
<td><strong><div class="m-top-5">$<span class="total_cost_progress">'.number_format($progress_cost,2).'</span> ex-gst</div></strong></td><td>';

if($row->is_invoiced == 0 && $un_invoiced == 0 ){
  echo '<div class="progress_invoice_button"><button class="btn btn-primary-dark  m-right-5 progress_invoice" data-invoice-id="'.$row->invoice_id.'" id="'.($row->label != '' ? 'F' : 'P').'_'.$row->order_invoice.'" data-toggle="modal" data-target="#set_invoice_modal"><i class="fa fa-file-text-o"></i> Set Invoice</button></div>';
  $un_invoiced++;
}elseif($row->is_invoiced != 0 ){


$item_progss = ($row->label != '' ? 'F' : 'P_'.$row->order_invoice);

if($outstanding > 0 ):

echo '<div class="btn-group pull-left m-right-10 progress_invoice_group">
    <button type="button" disabled="disabled" class="btn btn-primary-gray progress_invoice"><i class="fa fa-file-text-o"></i> Invoiced</button>
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      <span class="caret"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
    <li><a href="#" class="progress_invoice_resend"><i class="fa fa-files-o"></i> View Invoice</a></li>';

if($total_paid == 0 && $this->session->get('is_admin') == 1 ):
    echo '<li class="remove_link"><a href="#" id="'.$row->invoice_id.'-'.$item_progss.'-'.$row->project_id.'" class="remove_invoice"><i class="fa fa-exclamation-triangle"></i> Remove Invoice</a></li>';
endif;

endif;    
    echo '</ul>
  </div>';



  if($row->is_paid == 1){
    $file_progress = $row->project_id.$item_progss;

  //  echo '<p id="" class="">'.$file_progress.'</p>';
    $dirUserPath = dirname(dirname(dirname(dirname(dirname(__FILE__))))). "/docs/inv_jbs";


    $dirAdmin = glob($dirUserPath ."/". $file_progress."*.pdf");


    foreach($dirAdmin as $filew){
    //  var_dump($dirAdmin);
    //  var_dump($filew);
      $file_name = basename($filew); 
    }

    echo '<a class="btn btn-primary  m-right-10" href="'.site_url().'docs/inv_jbs/'.$file_name.'" target="_blank"><i class="fa fa-files-o"></i> View Job Book</a>';
  }


 if($this->session->get('is_admin') == 1 || $this->session->get('user_role_id') == 5 || $this->session->get('user_role_id') == 6|| $this->session->get('user_role_id') == 16):

  if($row->is_paid == 1){
    echo '<button  class="btn btn-success progress_paid" id="'.$row->project_id.'"  data-toggle="modal" data-target="#payment_history_modal" data-backdrop="static" ><i class="fa fa-usd"></i> Paid</button>';
  }else{
    echo '<button  class="btn btn-danger progress_paid" id="'.$row->project_id.'_'.$row->order_invoice.'"  data-toggle="modal" data-target="#payment_modal" data-backdrop="static" ><i class="fa fa-usd"></i> Payment</button>';
  }
endif;


}else{

}


echo '</td><td><div class="m-top-5"><strong><span class="progress_outstanding">'.($row->is_invoiced == 1 ? '$'.$outstanding : '').'</span></strong></div></td></tr>';

      
      //echo '<option value="'.$row->contact_person_id.'" '.($row->is_primary == 1 ? 'selected="selected"' : '').'  >'.$row->first_name.' '.$row->last_name.'</option>';
    }


  }



}