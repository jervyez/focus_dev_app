<?php
// module created by Jervy 23-9-2022
namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Projects\Controllers\Projects;
use App\Modules\Projects\Models\Projects_m;

use App\Modules\Induction_health_safety\Controllers\Induction_health_safety;
use App\Modules\Induction_health_safety\Models\Induction_health_safety_m;

class Admin extends BaseController {

  function __construct(){
    $this->admin_m = new Admin_m();
  }


  public function index($dataPass = array()){ 

    $this->users      = new Users();
    $this->user_model = new Users_m();
 
    if(!$this->users->_is_logged_in() ):
      $this->users->logout();
      return redirect()->to('/signin');
    endif;



    if(isset($_GET['rem_loc_id'])){
      $user_id = $_GET['rem_loc_id'];
      $this->admin_m->remove_location_assignment($user_id);
      $update_success = 'Employee Location Removed';
      $this->session->setFlashdata('delete_location_assign', $update_success);
      return redirect()->to('/admin?scroll=employee_location');
    }


    if(isset($_GET['delcs'])){
      $client_storage_id = $_GET['delcs'];
      $this->admin_m->remove_warehouse_assignment($client_storage_id);
      return redirect()->to('/admin#client_supply_settings');
    }
    
    $data = array();
    $data_a = array();
    $data_b = array();
    $data_c = array();
    $data_d = array();
    $data_e = array();

    $data_c['screen'] = 'Admin Defaults';

    $q_admin_defaults_notes = $this->admin_m->fetch_default_notes();
    $q_grs_arr_admin_defaults_notes = $q_admin_defaults_notes->getResultArray();
    $default_notes = array_shift($q_grs_arr_admin_defaults_notes);

    //fetch site cost
    $q_site_cost = $this->admin_m->fetch_site_costs();
    $q_get_resArr_site_cost = $q_site_cost->getResultArray();
    $data_a = array_shift($q_get_resArr_site_cost);
    //fetch site cost

    //admin_defaulst
    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    $q_get_res_arr_admin_defaults = $q_admin_defaults->getResultArray();
    $data_b = array_shift($q_get_res_arr_admin_defaults);

    $static_defaults = $this->user_model->select_static_defaults();
    $q_get_res_arr_static_defaults = $static_defaults->getResultArray();
    $data_sd = array_shift($q_get_res_arr_static_defaults);
    //admin_defaulst


    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $data_c['all_focus_company'] = $all_focus_company->getResult();

    $all_email_banners = $this->admin_m->list_email_banners();
    $data_c['all_focus_company_banners'] = $all_email_banners->getResult();

    $data['warranty_months'] = $data_b['warranty_months'];
    $data['warranty_years'] = $data_b['warranty_years'];
    $data['prj_review_day'] = $data_sd['prj_review_day'];

    //default email message
    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message();
    $q_get_res_arr_admin_default_email_message = $q_admin_default_email_message->getResultArray();
    $data_e = array_shift($q_get_res_arr_admin_default_email_message);  

    $data['emai_message'] = $this->admin_m->fetch_admin_default_email_message();
    //default email message

    //default Site Staff email message
    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-new');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_sender_name = $row['sender_name'];
      $induction_sender_email = $row['sender_email'];
      $induction_bcc_email = $row['bcc_email'];
      $induction_subject = $row['subject'];
      $induction_message_content = $row['message_content'];
      $induction_assigned_user = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-update');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_message_content_update = $row['message_content'];

    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-video-fss');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_message_content_video_fss = $row['message_content'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-video-oss');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_message_content_video_oss = $row['message_content'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-video');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_message_content_video = $row['message_content'];
    }
    //default Site Staff email message

    //default email message for onboarding - MC 08-24-18
    // edited 03-27-19
    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-clients');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_sender_name_clients = $row['sender_name'];
      $onboarding_sender_email_clients = $row['sender_email'];
      $onboarding_bcc_email_clients = $row['bcc_email'];
      $onboarding_subject_clients = $row['subject'];
      $onboarding_message_content_clients = $row['message_content'];
      $onboarding_assigned_user_clients = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_sender_name = $row['sender_name'];
      $onboarding_sender_email = $row['sender_email'];
      $onboarding_bcc_email = $row['bcc_email'];
      $onboarding_subject = $row['subject'];
      $onboarding_message_content = $row['message_content'];
      $onboarding_assigned_user = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-bank-details-form');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_sender_name_bank = $row['sender_name'];
      $onboarding_sender_email_bank = $row['sender_email'];
      $onboarding_bcc_email_bank = $row['bcc_email'];
      $onboarding_subject_bank = $row['subject'];
      $onboarding_message_content_bank = $row['message_content'];
      $onboarding_assigned_user_bank = $row['user_id'];
    }

    //default email message for onboarding end
    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-notif');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_notif_sender_name = $row['sender_name'];
      $onboarding_notif_sender_email = $row['sender_email'];
      $onboarding_notif_bcc_email = $row['bcc_email'];
      $onboarding_notif_subject = $row['subject'];
      $onboarding_notif_message_content = $row['message_content'];
      $onboarding_notif_assigned_user = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-approved-clients');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_approved_sender_name_clients = $row['sender_name'];
      $onboarding_approved_sender_email_clients = $row['sender_email'];
      $onboarding_approved_bcc_email_clients = $row['bcc_email'];
      $onboarding_approved_subject_clients = $row['subject'];
      $onboarding_approved_message_content_clients = $row['message_content'];
      $onboarding_approved_assigned_user_clients = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-approved');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_approved_sender_name = $row['sender_name'];
      $onboarding_approved_sender_email = $row['sender_email'];
      $onboarding_approved_bcc_email = $row['bcc_email'];
      $onboarding_approved_subject = $row['subject'];
      $onboarding_approved_message_content = $row['message_content'];
      $onboarding_approved_assigned_user = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-declined-clients');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_declined_sender_name_clients = $row['sender_name'];
      $onboarding_declined_sender_email_clients = $row['sender_email'];
      $onboarding_declined_bcc_email_clients = $row['bcc_email'];
      $onboarding_declined_subject_clients = $row['subject'];
      $onboarding_declined_message_content_clients = $row['message_content'];
      $onboarding_declined_assigned_user_clients = $row['user_id'];
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-declined');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $onboarding_declined_sender_name = $row['sender_name'];
      $onboarding_declined_sender_email = $row['sender_email'];
      $onboarding_declined_bcc_email = $row['bcc_email'];
      $onboarding_declined_subject = $row['subject'];
      $onboarding_declined_message_content = $row['message_content'];
      $onboarding_declined_assigned_user = $row['user_id'];
    }

    //markups
    $q_admin_markup = $this->admin_m->fetch_markup();
    $q_getArr_admin_markup = $q_admin_markup->getResultArray();
    $data_d = array_shift($q_getArr_admin_markup);   
    //markups

    //labour cost matrix
    $q_labour_cost = $this->admin_m->fetch_labour_cost();
    $q_getArr_admin_labour_cost = $q_labour_cost->getResultArray();
    $labour_cost = array_shift($q_getArr_admin_labour_cost);
    //labour cost matrix






    $data = array_merge($data_a,$data_b,$data_c,$data_d,$labour_cost,$dataPass,$data_e,$default_notes);
    $gp_data_arr = $this->get_double_amalgated_rate($data_a,$labour_cost,$data_b);


    $data['gp_on_cost_total_hr'] = $gp_data_arr['gp_on_cost_total_hr'];
    $data['gp_on_cost_time_half_hr'] = $gp_data_arr['gp_on_cost_time_half_hr'];
    $data['gp_on_cost_time_double_hr'] = $gp_data_arr['gp_on_cost_time_double_hr'];
    $data['gp_amalgamated_rate'] = $gp_data_arr['gp_amalgamated_rate'];
    $data['grand_total'] = $gp_data_arr['gp_grand_total'];
    $data['leave_percentage'] = $gp_data_arr['gp_leave_percentage'];


    $static_defaults = $this->user_model->select_static_defaults();
    $data['static_defaults'] = $static_defaults->getResult();

    $select_leave_rates_salaried = $this->admin_m->select_leave_rates('1');
    $data['select_leave_rates_salaried'] = $select_leave_rates_salaried->getResult();

    $select_leave_rates_wages = $this->admin_m->select_leave_rates('2');
    $data['select_leave_rates_wages'] = $select_leave_rates_wages->getResult();

    $select_leave_rates_manila = $this->admin_m->select_leave_rates('3');
    $data['select_leave_rates_manila'] = $select_leave_rates_manila->getResult();

    $fetch_user= $this->user_model->fetch_user();
    $data['users'] = $fetch_user->getResult();


    $contractor_feedbacks = $this->admin_m->get_contractor_feedbacks();
    $data['contractor_feedbacks'] = $contractor_feedbacks->getResult();


    if(isset($_GET['edit_cfb']) && $_GET['edit_cfb'] != "" ){
      $feedback_id = $_GET['edit_cfb'];
      $feedback_view_q = $this->admin_m->get_feedback_details($feedback_id);
      $q_getArr_feedback_view = $feedback_view_q->getResultArray();
      $data['feedback_details'] = array_shift($q_getArr_feedback_view);
    }


    $fetch_archive_types = $this->admin_m->get_archive_types();
    $data['archive_types'] = $fetch_archive_types->getResult();


    $fetch_user_location= $this->admin_m->fetch_user_location();
    $data['user_location'] = $fetch_user_location->getResult();

    $fetch_set_user_location= $this->admin_m->fetch_set_user_location();
    $data['set_user_location'] = $fetch_set_user_location->getResult();

    $fetch_users_set_location= $this->admin_m->fetch_users_set_location();
    $data['users_set_location'] = $fetch_users_set_location->getResult();

    $fetch_leave_email_defaults= $this->admin_m->fetch_leave_email_defaults();
    $data['leave_email_defaults'] = $fetch_leave_email_defaults->getResult();

    $year = date("Y");
    $q_seasons = $this->admin_m->list_seasons($year);
    $data['seasons'] = $q_seasons->getResult();

    $data['induction_sender_name'] = $induction_sender_name;
    $data['induction_sender_email'] = $induction_sender_email;
    $data['induction_bcc_email'] = $induction_bcc_email;
    $data['induction_subject'] = $induction_subject;
    $data['induction_message_content'] = $induction_message_content;
    $data['induction_assigned_user'] = $induction_assigned_user;
    $data['induction_message_content_update'] = $induction_message_content_update;
    $data['induction_message_content_video'] = $induction_message_content_video;
    $data['induction_message_content_video_fss'] = $induction_message_content_video_fss;
    $data['induction_message_content_video_oss'] = $induction_message_content_video_oss;

    $data['onboarding_sender_name_clients'] = $onboarding_sender_name_clients;
    $data['onboarding_sender_email_clients'] = $onboarding_sender_email_clients;
    $data['onboarding_bcc_email_clients'] = $onboarding_bcc_email_clients;
    $data['onboarding_subject_clients'] = $onboarding_subject_clients;
    $data['onboarding_message_content_clients'] = $onboarding_message_content_clients;
    $data['onboarding_assigned_user_clients'] = $onboarding_assigned_user_clients;

    $data['onboarding_sender_name'] = $onboarding_sender_name;
    $data['onboarding_sender_email'] = $onboarding_sender_email;
    $data['onboarding_bcc_email'] = $onboarding_bcc_email;
    $data['onboarding_subject'] = $onboarding_subject;
    $data['onboarding_message_content'] = $onboarding_message_content;
    $data['onboarding_assigned_user'] = $onboarding_assigned_user;

    $data['onboarding_sender_name_bank'] = $onboarding_sender_name_bank;
    $data['onboarding_sender_email_bank'] = $onboarding_sender_email_bank;
    $data['onboarding_bcc_email_bank'] = $onboarding_bcc_email_bank;
    $data['onboarding_subject_bank'] = $onboarding_subject_bank;
    $data['onboarding_message_content_bank'] = $onboarding_message_content_bank;
    $data['onboarding_assigned_user_bank'] = $onboarding_assigned_user_bank;

    $data['onboarding_notif_sender_name'] = $onboarding_notif_sender_name;
    $data['onboarding_notif_sender_email'] = $onboarding_notif_sender_email;
    $data['onboarding_notif_bcc_email'] = $onboarding_notif_bcc_email;
    $data['onboarding_notif_subject'] = $onboarding_notif_subject;
    $data['onboarding_notif_message_content'] = $onboarding_notif_message_content;
    $data['onboarding_notif_assigned_user'] = $onboarding_notif_assigned_user;

    $data['onboarding_approved_sender_name_clients'] = $onboarding_approved_sender_name_clients;
    $data['onboarding_approved_sender_email_clients'] = $onboarding_approved_sender_email_clients;
    $data['onboarding_approved_bcc_email_clients'] = $onboarding_approved_bcc_email_clients;
    $data['onboarding_approved_subject_clients'] = $onboarding_approved_subject_clients;
    $data['onboarding_approved_message_content_clients'] = $onboarding_approved_message_content_clients;
    $data['onboarding_approved_assigned_user_clients'] = $onboarding_approved_assigned_user_clients;

    $data['onboarding_approved_sender_name'] = $onboarding_approved_sender_name;
    $data['onboarding_approved_sender_email'] = $onboarding_approved_sender_email;
    $data['onboarding_approved_bcc_email'] = $onboarding_approved_bcc_email;
    $data['onboarding_approved_subject'] = $onboarding_approved_subject;
    $data['onboarding_approved_message_content'] = $onboarding_approved_message_content;
    $data['onboarding_approved_assigned_user'] = $onboarding_approved_assigned_user;

    $data['onboarding_declined_sender_name_clients'] = $onboarding_declined_sender_name_clients;
    $data['onboarding_declined_sender_email_clients'] = $onboarding_declined_sender_email_clients;
    $data['onboarding_declined_bcc_email_clients'] = $onboarding_declined_bcc_email_clients;
    $data['onboarding_declined_subject_clients'] = $onboarding_declined_subject_clients;
    $data['onboarding_declined_message_content_clients'] = $onboarding_declined_message_content_clients;
    $data['onboarding_declined_assigned_user_clients'] = $onboarding_declined_assigned_user_clients;

    $data['onboarding_declined_sender_name'] = $onboarding_declined_sender_name;
    $data['onboarding_declined_sender_email'] = $onboarding_declined_sender_email;
    $data['onboarding_declined_bcc_email'] = $onboarding_declined_bcc_email;
    $data['onboarding_declined_subject'] = $onboarding_declined_subject;
    $data['onboarding_declined_message_content'] = $onboarding_declined_message_content;
    $data['onboarding_declined_assigned_user'] = $onboarding_declined_assigned_user;

    $data['page_title'] = 'Admin Defaults';
    $data['main_content'] = 'App\Modules\Admin\Views\admin_v';
    return view('App\Views\page',$data);
  }


  public function location_assignments(){
    $location_id = $this->request->getPost('location');
    $employee_location = $this->request->getPost('employee_location');

    foreach ($employee_location as $key => $employee_id) {
      $this->admin_m->set_user_location($location_id,$employee_id);
    }

    $update_success = 'Employee Location Assigned';
    $this->session->setFlashdata('user_location_assign', $update_success);
    
    return redirect()->to('/admin?scroll=employee_location');
  }

  public function new_season(){
    $season_name = $this->request->getPost('season_name');
    $bg_start = $this->request->getPost('bg_start');
    $bg_finish = $this->request->getPost('bg_finish');

    $this->admin_m->add_season($season_name,$bg_start,$bg_finish);
    return redirect()->to('/admin?scroll=season_setup');
  }

  public function set_background($id){
    $bg_set_id = explode('_', $id);

    $img_id = $bg_set_id[0];
    $season_id = $bg_set_id[1];

    $this->admin_m->set_bg_background($img_id,$season_id);
    return redirect()->to('/admin?scroll=season_setup');
  }

  public function delete_bg($bg_id){
    $this->admin_m->delete_login_bg($bg_id);
    return redirect()->to('/admin?scroll=season_setup');
  }

  public function joinery_selected_user_select(){
    $user_list = $this->admin_m->joinery_selected_user();
    echo '<option value="0">None</option>';
    foreach ($user_list->getResultArray() as $row){
      if($row['is_primary'] == 1){
        echo '<option value = "'.$row['user_id'].'" selected = selected>'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      }else{
        echo '<option value = "'.$row['user_id'].'">'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      }
    }
  }


  public function joinery_selected_user_update($selected_user){
    
    $user_list = $this->admin_m->joinery_selected_user();
    echo '<option value="0">None</option>';
    foreach ($user_list->getResultArray() as $row){
      if($row['user_id'] == $selected_user){
        echo '<option value = "'.$row['user_id'].'" selected = selected>'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      }else{
        echo '<option value = "'.$row['user_id'].'">'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      }
      
      
    }
  }

  public function update_bg_login(){
    $season_name = $this->request->getPost('season_name');
    $bg_start = $this->request->getPost('bg_start');
    $bg_finish = $this->request->getPost('bg_finish');
    $bg_id = $this->request->getPost('bg_id');

    $this->admin_m->update_bg_login($season_name,$bg_start,$bg_finish,$bg_id);
    return redirect()->to('/admin?scroll=season_setup');
  }


  public function upload_signin_bg(){

    $this->users = new Users();
    $season_id = $this->request->getPost('season_id');

    $file_upload_raw = $this->users->_upload_primary_photo('bg_img_signin','login_bg','bg_login_');
    $file_upload_arr = explode('|',$file_upload_raw);

    if($file_upload_arr[0] == 'success'){
      $this->admin_m->insert_login_bg($file_upload_arr[1],$season_id);
      return redirect()->to('/admin?scroll=season_setup');
    }
     
    $this->admin_m->update_email_banner($banner_focus_id,$banner_file_name_arr);
  }



  public function update_closing_settings(){

      $alert_time_hr = $this->request->getPost('alert_time_hr');
      $alert_time_mins = $this->request->getPost('alert_time_mins');
      $alert_msg = $this->request->getPost('alert_msg');

      $alert_msg = str_replace("'","&apos;",$alert_msg);
      $alert_msg = str_replace('"' ,"&quot;",$alert_msg);

      $this->admin_m->update_closing_settings($alert_time_hr, $alert_time_mins, $alert_msg);
    return redirect()->to('/admin?scroll=closing_settings');
    }

  public function user_location(){
    $location = $this->request->getPost('location');
    $x_coordinate = $this->request->getPost('xcoordinate');
    $y_coordinate = $this->request->getPost('ycoordinate');

    $this->admin_m->add_user_location($location,$x_coordinate,$y_coordinate);

    $update_success = 'Location added.';
    $this->session->setFlashdata('update_user_location', $update_success);
    return redirect()->to('/admin?scroll=employee_location');
  }

  public function matrix(){

    $admin_defaults_raw = $this->admin_m->fetch_admin_defaults();
    $get_res_arr_def_raw = $admin_defaults_raw->getResultArray();
    $admin_defaults = array_shift($get_res_arr_def_raw);

    $markup_raw = $this->admin_m->fetch_markup();
    $get_ress_arr_markup_raw = $markup_raw->getResultArray();
    $markup = array_shift($get_ress_arr_markup_raw);

    $labour_cost_raw = $this->admin_m->fetch_labour_cost();
    $get_res_arr_coat_raw = $labour_cost_raw->getResultArray();
    $labour_cost = array_shift($get_res_arr_coat_raw);

    $hour_side = array();
    $time_half_side = array();
    $double_time = array();

    $data['total_days'] = $this->request->getPost('total_days');

    $data['hour_rate'] = $this->request->getPost('rate');
    $data['time_half_rate'] = $data['hour_rate'] + ($data['hour_rate'] * 0.5);
    $data['double_time_rate'] = $data['hour_rate'] + ($data['hour_rate'] * 1);

    $data['hours'] = $this->request->getPost('hours');
    $data['superannuation'] = $this->request->getPost('superannuation');
    $data['workers-comp'] = $this->request->getPost('workers-comp');

    $data['public-holidays_raw'] = $this->request->getPost('public-holidays');     
    $data['rdos_raw'] = $this->request->getPost('rdos');     
    $data['sick-leave_raw'] = $this->request->getPost('sick-leave');     
    $data['carers-leave_raw'] = $this->request->getPost('carers-leave');     
    $data['annual-leave_raw'] = $this->request->getPost('annual-leave');

    $data['public-holidays'] = round(($this->request->getPost('public-holidays')/$data['total_days'])*100, 2 );      
    $data['rdos'] = round(($this->request->getPost('rdos')/$data['total_days'])*100, 2 );      
    $data['sick-leave'] = round(($this->request->getPost('sick-leave')/$data['total_days'])*100, 2 );      
    $data['carers-leave'] = round(($this->request->getPost('carers-leave')/$data['total_days'])*100, 2 );      
    $data['annual-leave'] = round(($this->request->getPost('annual-leave')/$data['total_days'])*100, 2 );

    $data['downtime'] = $this->request->getPost('downtime');
    $data['leave-loading'] = $this->request->getPost('leave-loading');

    array_push($hour_side, $data['hour_rate']);
    array_push($hour_side, round(($data['hour_rate']*$data['superannuation'] )/100, 2 ) );
    array_push($hour_side, round(($data['hour_rate']*$data['workers-comp'] )/100, 2));

    array_push($hour_side, round(($data['hour_rate']* $data['public-holidays'] )/100, 2));
    array_push($hour_side, round(($data['hour_rate']*$data['rdos'] )/100, 2));
    array_push($hour_side, round(($data['hour_rate']*$data['sick-leave'] )/100, 2));
    array_push($hour_side, round(($data['hour_rate']*$data['carers-leave'] )/100, 2));
    array_push($hour_side, round((($data['hour_rate']*$data['annual-leave'])+0.17 *($data['hour_rate']*$data['annual-leave'])  ) /100, 2) );      
    array_push($hour_side, round(($data['hour_rate']*$data['downtime'] )/100, 2));
    array_push($hour_side, round(($data['hour_rate']*$data['leave-loading'] )/100, 2));

    array_push($time_half_side, $data['time_half_rate']);
    array_push($time_half_side, round(($data['time_half_rate']*$data['superannuation'] )/100, 2 ) );
    array_push($time_half_side, round(($data['time_half_rate']*$data['workers-comp'] )/100, 2));

    array_push($time_half_side, $hour_side[3]);
    array_push($time_half_side, $hour_side[4]);
    array_push($time_half_side, $hour_side[5]);
    array_push($time_half_side, $hour_side[6]);
    array_push($time_half_side, $hour_side[7]);
    array_push($time_half_side, $hour_side[8]);
    array_push($time_half_side, $hour_side[9]);

    array_push($double_time, $data['double_time_rate']);
    array_push($double_time, round(($data['double_time_rate']*$data['superannuation'] )/100, 2 ) );
    array_push($double_time, round(($data['double_time_rate']*$data['workers-comp'] )/100, 2));

    array_push($double_time, $hour_side[3]);
    array_push($double_time, $hour_side[4]);
    array_push($double_time, $hour_side[5]);
    array_push($double_time, $hour_side[6]);
    array_push($double_time, $hour_side[7]);
    array_push($double_time, $hour_side[8]);
    array_push($double_time, $hour_side[9]);

    $data['hour_rate_comp'] = array_sum($hour_side);
    $data['time_half_rate_comp'] = array_sum($time_half_side);
    $data['double_time_rate_comp'] = array_sum($double_time);

    $standard = $data['hour_rate_comp'] * ($admin_defaults['labor_split_standard']/100);
    $time_half = $data['time_half_rate_comp'] * ($admin_defaults['labor_split_time_and_half']/100);
    $double_time_comp = $data['double_time_rate_comp'] * ($admin_defaults['labor_split_double_time']/100);

    $data['total_amalgamated_rate'] = $standard + $time_half + $double_time_comp;

    $new_site_cost_id = $this->admin_m->update_site_costs($data);

    $this->admin_m->insert_latest_system_default($new_site_cost_id,$admin_defaults['admin_default_id'],$markup['markup_id'],$labour_cost['labour_cost_id']);

    $update_success = 'The record is now updated.';
    $this->session->setFlashdata('update_matrix', $update_success);

    return redirect()->to('/admin');
  }

  public function labour_cost_matrix(){
    $passArr = array();

    $admin_defaults_raw = $this->admin_m->fetch_admin_defaults();
    $get_res_arr_def_raw = $admin_defaults_raw->getResultArray();
    $admin_defaults = array_shift($get_res_arr_def_raw);

    $site_costs_raw = $this->admin_m->fetch_site_costs();
    $get_res_arr_cost_raw = $site_costs_raw->getResultArray();
    $site_costs = array_shift($get_res_arr_cost_raw);

    $markup_raw = $this->admin_m->fetch_markup();
    $get_res_arr_markup = $markup_raw->getResultArray();
    $markup = array_shift($get_res_arr_markup);



    $superannuation = $this->request->getPost('superannuation');
    $workers_compensation = $this->request->getPost('workers_compensation');
    $payroll_tax = $this->request->getPost('payroll_tax');
    $leave_loading = $this->request->getPost('leave_loading');
    $other = $this->request->getPost('other');
    $total_leave_days = $this->request->getPost('total_leave_days');
    $total_work_days = $this->request->getPost('total_work_days');

    

    $new_labour_cost_matrix = $this->admin_m->insert_labour_cost_matrix($superannuation,$workers_compensation,$payroll_tax,$leave_loading,$other,$total_leave_days,$total_work_days);

    $this->admin_m->insert_latest_system_default($site_costs['site_cost_id'],$admin_defaults['admin_default_id'],$markup['markup_id'],$new_labour_cost_matrix);

    $update_success = 'The Site Labour Cost Matrix is now updated.';
    $this->session->setFlashdata('update_labour_cost_matrix', $update_success);
    return redirect()->to('/admin');
    
  }



      public function project_mark_up(){


        $admin_defaults_raw = $this->admin_m->fetch_admin_defaults();
        $get_res_arr_defaults_raw = $admin_defaults_raw->getResultArray();
        $admin_defaults = array_shift($get_res_arr_defaults_raw);

        $site_costs_raw = $this->admin_m->fetch_site_costs();
        $get_res_arr_costs_raw = $site_costs_raw->getResultArray();
        $site_costs = array_shift($get_res_arr_costs_raw);

        $labour_cost_raw = $this->admin_m->fetch_labour_cost();
        $get_res_arr_labor_cost = $labour_cost_raw->getResultArray();
        $labour_cost = array_shift($get_res_arr_labor_cost);


        $passArr = array();

        $kiosk = $this->request->getPost('kiosk');
        $full_fitout = $this->request->getPost('full-fitout');
        $refurbishment = $this->request->getPost('refurbishment');
        $stripout = $this->request->getPost('stripout');
        $maintenance = $this->request->getPost('maintenance');
        $minor_works = $this->request->getPost('minor-works');
        $joinery_only = $this->request->getPost('joinery-only');

        $min_kiosk = $this->request->getPost('min_kiosk');
        $min_full_fitout = $this->request->getPost('min_full_fitout');
        $min_refurbishment = $this->request->getPost('min_refurbishment');
        $min_stripout = $this->request->getPost('min_stripout');
        $min_maintenance = $this->request->getPost('min_maintenance');
        $min_minor_works = $this->request->getPost('min_minor_works');
        $min_joinery_only = $this->request->getPost('min_joinery_only');

        $design_works = $this->request->getPost('design_works');
        $min_design_works = $this->request->getPost('min_design_works');


        $new_mark_up_id = $this->admin_m->updat_project_mark_up($kiosk,$full_fitout,$refurbishment,$stripout,$maintenance,$minor_works,$min_kiosk,$min_full_fitout,$min_refurbishment,$min_stripout,$min_maintenance,$min_minor_works,$design_works,$min_design_works,$joinery_only,$min_joinery_only);

        $this->admin_m->insert_latest_system_default($site_costs['site_cost_id'],$admin_defaults['admin_default_id'],$new_mark_up_id,$labour_cost['labour_cost_id']);

        $update_success = 'The project mark-up is now updated.';
        //$this->session->set_flashdata('update_company_id', $company_id);
        $this->session->setFlashdata('update_prj_mrk', $update_success);


        return redirect()->to('/admin#form_category_mark_up');
        
      }



  public function get_notice_days($leave_type_id){
    $get_notice_days= $this->admin_m->get_notice_days($leave_type_id);
    $get_notice_days = $get_notice_days->getResult();

    return $get_notice_days;
  }

  public function get_double_amalgated_rate($site_costs,$labour_cost,$admin_defaults){
    $rate = $site_costs['rate'];

    $leave_percentage = ($labour_cost['total_leave_days']/$labour_cost['total_work_days'])*100;
    $leave_percentage = round($leave_percentage, 2);
    $gp_grand_total = $leave_percentage+$labour_cost['superannuation']+$labour_cost['workers_compensation']+$labour_cost['payroll_tax']+$labour_cost['lc_leave_loading']+$labour_cost['other'];  

    $payroll_tax = $labour_cost['payroll_tax'];

    $gp_on_cost_total_hr = round($rate+(($rate*$gp_grand_total)/100),2);
    $gp_on_cost_time_half_hr = round($gp_on_cost_total_hr + ((0.5*$rate) + (((0.5*$rate)*$payroll_tax )/100)),2);
    $gp_on_cost_time_double_hr = round($gp_on_cost_total_hr + ($rate+(($rate*$payroll_tax)/100)),2);

    $amalgamated_rate = ($admin_defaults['labor_split_time_and_half']/100)*$gp_on_cost_time_half_hr + ($admin_defaults['labor_split_double_time']/100)*$gp_on_cost_time_double_hr + ($admin_defaults['labor_split_standard']/100)*$gp_on_cost_total_hr;

    $gp_amalgamated_rate = round($amalgamated_rate, 2);

    $result = array(
      "gp_on_cost_total_hr" => $gp_on_cost_total_hr,
      "gp_on_cost_time_half_hr" => $gp_on_cost_time_half_hr,
      "gp_on_cost_time_double_hr" => $gp_on_cost_time_double_hr,
      "gp_amalgamated_rate" => $gp_amalgamated_rate,
      "gp_grand_total" => $gp_grand_total,
      "gp_leave_percentage" => $leave_percentage
    );

    return $result;
  }


  public function display_rate_set_for_form(){
    $emp_rate_q = $this->admin_m->fetch_user_rate_set();
    foreach ($emp_rate_q->getResultArray() as $row){
      echo '<option value="'.$row['employee_rate_set_id'].'">'.$row['rate_set_name'].'</option>';
    }
  }


  public function fetch_users_list_table(){
    $search_user = $_POST['filter'];
    $user_list = $this->admin_m->search_user($search_user);
    echo '<table class = "table table-hover table-striped">';
    //echo '<option value="">'.$select_user.'</option>';
    foreach ($user_list->getResultArray() as $row){
      
      echo '<tr><td onclick = "joinery_select_user('.$row['user_id'].')">'.$row['user_first_name'].' '.$row['user_last_name'].'</td></tr>';
      
    }

    echo '</table>';
  }



  public function defaults(){
    $passArr = array();

    $admin_defaults_raw = $this->admin_m->fetch_admin_defaults();
    $get_res_arr_def_raw = $admin_defaults_raw->getResultArray();
    $admin_defaults = array_shift($get_res_arr_def_raw);

    $site_costs_raw = $this->admin_m->fetch_site_costs();
    $get_res_arr_costs_raw = $site_costs_raw->getResultArray();
    $site_costs = array_shift($get_res_arr_costs_raw);

    $markup_raw = $this->admin_m->fetch_markup();
    $get_res_arr_markup = $markup_raw->getResultArray();
    $markup = array_shift($get_res_arr_markup);

    $labour_cost_raw = $this->admin_m->fetch_labour_cost();
    $get_res_arr_cost_raw = $labour_cost_raw->getResultArray();
    $labour_cost = array_shift($get_res_arr_cost_raw);

    $data['gst-rate'] = $this->request->getPost('gst-rate');
    $data['installation-labour'] = $this->request->getPost('installation-labour');
    $data['time-half'] = $this->request->getPost('time-half');
    $data['double-time'] = $this->request->getPost('double-time');
    $data['standard-labour'] = $this->request->getPost('standard-labour');
    $data['labor_split_standard'] = 100 - ($data['time-half'] + $data['double-time']);
    $new_admin_defaults_id = $this->admin_m->update_admin_defaults($data);

    $standard = $site_costs['total_hour'] * ($data['labor_split_standard']/100);
    $time_half = $site_costs['total_time_half'] * ($data['time-half']/100);
    $double_time_comp = $site_costs['total_double_time'] * ($data['double-time']/100);

    $amalgamated_rate = $standard + $time_half + $double_time_comp;

    $this->admin_m->update_amalgamated_rate($amalgamated_rate);

    $update_success = 'The record is now updated.';
    $this->session->setFlashdata('update_default', $update_success); 

    $this->admin_m->insert_update_latest_system_defaults('admin_default',$new_admin_defaults_id);
    
    return redirect()->to('/admin#form_def_labour_split');
  }


  public function warranty_categories(){
    $checked_category = "";
    if(!empty($_POST['warranty_categories'])) {
      foreach($_POST['warranty_categories'] as $check) {
        $checked_category = $checked_category.",".$check;
      }
    }

    $this->admin_m->update_admin_warranty($checked_category);

    return redirect()->to('/admin');
  }


  public function default_unaccepted_projects(){

    $checked_category = "";
    if(!empty($_POST['unaccepted_proj_categories'])) {
      foreach($_POST['unaccepted_proj_categories'] as $check) {
        $checked_category = $checked_category.",".$check;
      }
    }

    $unaccepted_num_days = $_POST['unaccepted_num_days'];
    $days_quote_deadline = $this->request->getPost('days_quote_deadline');

    $this->admin_m->update_admin_default_unaccepted_proj($unaccepted_num_days,$checked_category);


    $admin_defaults_id = $this->admin_m->update_insert_quote_deadline($days_quote_deadline);
    $this->admin_m->insert_update_latest_system_defaults('admin_default',$admin_defaults_id);

    return redirect()->to('/admin');
  }




  public function default_induction_project(){
    $checked_category = "";
    $work_value = "";
    $project_total = "";
    if(!empty($_POST['induction_categories'])) {
      foreach($_POST['induction_categories'] as $check) {
        $checked_category = $checked_category.",".$check;
      }

      $work_value = str_replace(",","",$_POST['work_value']);
      $project_total = str_replace(",","",$_POST['project_total']);

    }

    $this->admin_m->update_default_induction_project($checked_category,$work_value,$project_total);

    return redirect()->to('/admin');
  }



  public function update_cat_pcc(){
    $cat_pcc_dashboard = implode(',', $_POST['cat_pcc_dashboard']);
    $this->admin_m->update_pcc_dashboard($cat_pcc_dashboard);
    return redirect()->to('/admin#update_cat_pcc');

  }


  public function default_labour_schedule(){
    $checked_category = "";
    if(!empty($_POST['labour_sched_categories'])) {
      foreach($_POST['labour_sched_categories'] as $check) {
        if($checked_category == ""){
          $checked_category = $check;
        }else{
          $checked_category = $checked_category.",".$check;
        }
      }
    }

    $this->admin_m->update_admin_default_labour_sched($checked_category);

    return redirect()->to('/admin');
  }

  public function add_feedback(){

    $start_range = $_POST['start_range'];
    $end_rage = $_POST['end_rage'];
    $feedback_statement = $_POST['feedback_statement'];

    $this->admin_m->add_contractor_feedback($start_range, $end_rage, $feedback_statement);

    return redirect()->to('/admin#contractor_feedback');
  }



  public function edit_feedback(){

    $feedback_id = $_POST['feedback_edt_id'];
    $start_range = $_POST['feedback_edt_start_range'];
    $end_range = $_POST['feedback_edt_end_range'];
    $is_prime = $_POST['feedback_edt_id_prime'];
    $statement = $_POST['feedback_edt_details'];

    if($is_prime == 1){
      $this->admin_m->contractor_feedback_prime();
    }

    $this->admin_m->edit_contractor_feedback($start_range,$end_range,$statement,$is_prime,$feedback_id);

    $this->session->setFlashdata('feeback_deleted', 1);
    return redirect()->to('/admin#contractor_feedback');
  }



  public function update_fbck_success_email(){
    $email = $this->request->getPost('fbck_success_email');
    $this->admin_m->update_feedback_emails($email,1); // type = 1 for success tender
    
    return redirect()->to('/admin#contractor_feedback');
  }

  public function update_fbck_unsuccessful_email(){
    $email = $this->request->getPost('fbck_unsuccessful_email');
    $this->admin_m->update_feedback_emails($email,2); // type = 1 for success tender
    
    return redirect()->to('/admin#contractor_feedback');
  }

  public function update_fbck_unaccepted_s_email(){
    $email = $this->request->getPost('unaccepted_selected_email');
    $this->admin_m->update_feedback_unaccepted_emails($email,1); // type = 1 for success tender
    
    return redirect()->to('/admin#contractor_feedback');

  }

  public function update_fbck_unaccepted_us_email(){
    $email = $this->request->getPost('unaccepted_unselected_email');
    $this->admin_m->update_feedback_unaccepted_emails($email,2); // type = 1 for success tender
    
    return redirect()->to('/admin#contractor_feedback');

  }


  public function del_feedback($feedback_id){
    $feedback_statement = $feedback_id;

    $this->admin_m->disable_feedback($feedback_statement);
    return redirect()->to('/admin#contractor_feedback');

  }


  public function default_progress_report(){
    $checked_category = "";
    if(!empty($_POST['progress_report_categories'])) {
      foreach($_POST['progress_report_categories'] as $check) {
        $checked_category = $checked_category.",".$check;
      }
    }

    $this->admin_m->update_admin_default_progress_report($checked_category);

    return redirect()->to('/admin');
  }



  public function warranty_setup(){

    $this->projects   = new Projects();

    $warranty_months = $this->request->getPost('warranty_months');
    $warranty_years = $this->request->getPost('warranty_years');

    $this->admin_m->update_warranty_setup($warranty_months, $warranty_years);

    $this->projects->set_warranty();

    $update_success = 'Warranty Date Setup is now updated.';
    $this->session->setFlashdata('warranty_setup', $update_success);

    return redirect()->to('/admin#form_warranty_extension');
  }
      
  public function new_arc_doc_type(){
    $registry_type = $_POST['archive_name'];
    $this->admin_m->add_archive_doc($registry_type);
    return redirect()->to('/admin#archive_documents_settings');
  }

  public function update_arc_doc_type(){
    $archive_name = $_POST['archive_name'];
    $archive_registry_types = $_POST['archive_registry_types'];

    $this->admin_m->update_doc_type($archive_name,$archive_registry_types);
    return redirect()->to('/admin#archive_documents_settings');
  }



  public function delete_archive_type($archive_type_id){
    $this->admin_m->remove_archive_name($archive_type_id);
    return redirect()->to('/admin#archive_documents_settings');
  }


  public function del_arch_det($arch_id){
    $this->admin_m->del_archive_details($arch_id);
    return redirect()->to('/admin#archive_documents_settings');
  }

  public function update_archive_details(){
    $emp_name = $_POST['emp_name'];
    $date_expiry_edt = $_POST['date_expiry_edt'];
    $archive_id_edt = $_POST['archive_id_edt'];

    $this->admin_m->update_archive_details($archive_id_edt,$emp_name,$date_expiry_edt);
    return redirect()->to('/admin#archive_documents_settings');
  }

  public function set_assignmnt_doc_type(){

    $doc_type_arr = explode('_', $_POST['doc_type_name']);

    $archive_name_edt = $doc_type_arr[1];
    $emp_name = $_POST['emp_name'];
    $date_expiry_edt = $_POST['date_expiry_arhdoc'];
    $archive_id_edt = $doc_type_arr[0];

    $this->admin_m->set_assignmnt_archive($emp_name,$date_expiry_edt,$archive_id_edt);

    return redirect()->to('/admin#archive_documents_settings');
  }

  public function archive_documents_settings(){

    $ad_remind_late_email = $_POST['ad_remind_late_email'];
    $remind_emp_on_expire = $_POST['remind_emp_on_expire'];
    $ad_no_of_weeks = $_POST['ad_no_of_weeks'];
    $ad_day_reminder = $_POST['ad_day_reminder'];

    $this->admin_m->update_static_archive_reminder($ad_remind_late_email,$remind_emp_on_expire,$ad_no_of_weeks,$ad_day_reminder);
    return redirect()->to('/admin#archive_documents_settings');
  }

  public function invoice_email(){
    $passArr = array();

    $recipient_email = $this->request->getPost('recipient_email');
    $optional_cc_email = $this->request->getPost('optional_cc_email') ?? '';

    $this->admin_m->update_static_settings_invoice_email($recipient_email,$optional_cc_email);

    $update_success = 'The User Accounts Setting is now updated.';
    $this->session->setFlashdata('invoice_default_email', $update_success);
    return redirect()->to('/admin#form_invoice_default_email');
  }

  public function update_cc_emails(){

    $q_static_emails = $this->admin_m->get_cc_emails_static();
    $get_res_stat_email = $q_static_emails->getResult();
    $static_emails = array_shift($get_res_stat_email);

    $cqr_cc_email = ( isset($_POST['cqr_cc_email'])  ? $_POST['cqr_cc_email'] : null);
    $cpo_cc_email = ( isset($_POST['cpo_cc_email'])  ? $_POST['cpo_cc_email'] : null);
    $reply_cc_email = ( isset($_POST['reply_cc_email'])  ? $_POST['reply_cc_email'] : null);
    $induction_cc_email = ( isset($_POST['induction_cc_email'])  ? $_POST['induction_cc_email'] : null);

    $cc_emails_cqr = explode(',',$static_emails->cc_emails_cqr);
    $cc_emails_cpo = explode(',',$static_emails->cc_emails_cpo);
    $cc_emails_cqr_reply = explode(',',$static_emails->cc_emails_cqr_reply);
    $cc_emails_induction = explode(',',$static_emails->cc_emails_induction);

    if($cqr_cc_email){
      array_push($cc_emails_cqr,$cqr_cc_email);
      $cc_emails_cqr =  array_filter(array_unique($cc_emails_cqr));
      $set_cc_emails_cqr = implode(',',$cc_emails_cqr);
    }else{
      $set_cc_emails_cqr = $static_emails->cc_emails_cqr;
    }

    if($cpo_cc_email){
      array_push($cc_emails_cpo,$cpo_cc_email);
      $cc_emails_cpo =  array_filter(array_unique($cc_emails_cpo));
      $set_cc_emails_cpo = implode(',',$cc_emails_cpo);
    }else{
      $set_cc_emails_cpo = $static_emails->cc_emails_cpo;
    }

    if($reply_cc_email){
      array_push($cc_emails_cqr_reply,$reply_cc_email);
      $cc_emails_cqr_reply =  array_filter(array_unique($cc_emails_cqr_reply));
      $set_cc_emails_cqr_reply = implode(',',$cc_emails_cqr_reply);
    }else{
      $set_cc_emails_cqr_reply = $static_emails->cc_emails_cqr_reply;
    }

    if($induction_cc_email){
      array_push($cc_emails_induction,$induction_cc_email);
      $cc_emails_induction =  array_filter(array_unique($cc_emails_induction));
      $set_cc_emails_induction = implode(',',$cc_emails_induction);
    }else{
      $set_cc_emails_induction = $static_emails->cc_emails_induction;
    }

    $this->admin_m->update_cc_emails_static($set_cc_emails_cqr,$set_cc_emails_cpo,$set_cc_emails_cqr_reply,$set_cc_emails_induction);



    return redirect()->to('/admin?scroll=cqr_email_template');
  }




  public function update_cqr_template(){
    $email_msg_cqr = $this->request->getPost('email_msg_cqr');
    $tender_due_time = $this->request->getPost('tender_due_time');
    $this->admin_m->update_cqrEmail($email_msg_cqr,$tender_due_time);
    return redirect()->to('/admin?scroll=cqr_email_template'); // use scroll instead !!!!
  }


  public function update_reminder_settings(){
    $grace_period_mins = $this->request->getPost('grace_period_mins');
    $days_before_due = $this->request->getPost('days_before_due');
    $hrs_before_due = $this->request->getPost('hrs_before_due');
    $this->admin_m->updateReminderTimings($grace_period_mins,$days_before_due,$hrs_before_due);

    return redirect()->to('/admin?scroll=cqr_email_template'); // use scroll instead !!!!
  }


  public function default_notes(){
    $cqr_notes_w_ins = $this->request->getPost('cqr_notes_w_ins');
    $cqr_notes_no_ins = $this->request->getPost('cqr_notes_no_ins');
    $cpo_notes_w_ins = $this->request->getPost('cpo_notes_w_ins');
    $cpo_notes_no_ins = $this->request->getPost('cpo_notes_no_ins');

    $new_admin_defaults_id = $this->admin_m->update_defaults_notes($cqr_notes_w_ins,$cqr_notes_no_ins,$cpo_notes_w_ins,$cpo_notes_no_ins);
    return redirect()->to('/admin#form_invoice_default_email');
  }
 


  public function update_emp_supply_remnd(){
    $remind_emp_csup_user = $_POST['remind_emp_csup_user'];
    $days_lead_reminder = $_POST['days_lead_reminder'];
    $client_supply_settings_id = $_POST['client_supply_settings_id'];
    $cc_email = $_POST['cc_email'];

    $this->admin_m->update_employee_supply_reminder($remind_emp_csup_user,$days_lead_reminder,$client_supply_settings_id,$cc_email);
    

    return redirect()->to('/admin#client_supply_settings');
  }

  public function update_lead_days(){
    $days = $_POST['days'];
    $this->admin_m->update_defailt_lead_days($days);
    return redirect()->to('/admin#client_supply_settings');
  }

  public function upload_email_banner(){

    $this->users = new Users();

    $file_upload_raw = $this->users->_upload_primary_photo('signature_banner','misc','email_signature_');
    $file_upload_arr = explode('|',$file_upload_raw);
    $banner_file_name_arr = $file_upload_arr[1]; // file name
    $banner_focus_id = $this->request->getPost('banner_focus_comp');

    $this->admin_m->update_email_banner($banner_focus_id,$banner_file_name_arr);

    return redirect()->to('/admin?scroll=employee_email_signature');
  }


  public function default_email_message_induction(){
    $sender_name_induction = $this->request->getPost('sender_name_induction');
    $sender_email_induction = $this->request->getPost('sender_email_induction');
    $subject_induction = $this->request->getPost('subject_induction');
    $email_msg_new_induction = $this->request->getPost('email_msg_induction');
    $email_msg_new_induction_update = $this->request->getPost('email_msg_induction_update');

    $email_msg_induction_video = $this->request->getPost('email_msg_induction_video');
    $email_msg_induction_video_fss = $this->request->getPost('email_msg_induction_video_fss');
    $email_msg_induction_video_oss = $this->request->getPost('email_msg_induction_video_oss');
    
    $bcc_email_induction = $this->request->getPost('bcc_email_induction');
    $user_id = $this->request->getPost('user_assigned_induction');

    $this->admin_m->update_admin_default_email_message_induction_new($sender_name_induction,$sender_email_induction,$subject_induction,$email_msg_new_induction,$bcc_email_induction,$user_id);

    $this->admin_m->update_admin_default_email_message_induction_update($sender_name_induction,$sender_email_induction,$subject_induction,$email_msg_new_induction_update,$bcc_email_induction,$user_id);

    $this->admin_m->update_admin_default_email_message_induction_video($sender_name_induction,$sender_email_induction,$subject_induction,$email_msg_induction_video,$bcc_email_induction,$user_id,5);
    $this->admin_m->update_admin_default_email_message_induction_video($sender_name_induction,$sender_email_induction,$subject_induction,$email_msg_induction_video_fss,$bcc_email_induction,$user_id,9);
    $this->admin_m->update_admin_default_email_message_induction_video($sender_name_induction,$sender_email_induction,$subject_induction,$email_msg_induction_video_oss,$bcc_email_induction,$user_id,10);
    


  }



  public function default_email_message(){

    $sender_name_no_insurance = $this->request->getPost('sender_name_no_insurance');
    $sender_email_no_insurnace = $this->request->getPost('sender_email_no_insurnace');
    $subject_no_insurnace = $this->request->getPost('subject_no_insurnace');
    $email_msg_no_insurance = $this->request->getPost('email_msg_no_insurance');
    $bcc_email_no_insurnace = $this->request->getPost('bcc_email_no_insurnace');
    $user_id = $this->request->getPost('user_assigned_forinsurance');

    $this->admin_m->update_admin_default_email_message($sender_name_no_insurance,$sender_email_no_insurnace,$subject_no_insurnace,$email_msg_no_insurance,$bcc_email_no_insurnace,$user_id);
    return redirect()->to('/admin?scroll=insurance_email_defaults');
  }

  public function pdf_do_upload() {

    $upload_path_pdf = $_POST['upload_path_pdf'];
    $state = $_POST['state'];

    $file = $this->request->getFile('userfile');
    $ext = $file->getClientExtension();

    if ($state == 'wa'){
      $name_pref = 'bank_details_form_wa';
    }

    if ($state == 'nsw'){
      $name_pref = 'bank_details_form_nsw';
    }

    $newName = $name_pref.'.'.$ext;

    if ($file->isValid() && !$file->hasMoved()) {
      $file->move(ROOTPATH . './docs/'.$upload_path_pdf.'/', $newName);
      return redirect()->to('/admin?scroll=onboarding_bank_details_form');
    }else{
      $upload_error = $file->getError();
      echo 'error|'.$upload_error;
    }

  }


  public function onboarding_contractor_msg(){

      $onboarding_contractor_msg = $_POST['onboarding_contractor_msg'];
      $this->admin_m->update_static_settings_onboarding_contractor_msg($onboarding_contractor_msg);
      $update_success = 'Onboarding Contractor Message is updated';
      $this->session->setFlashdata('onboarding_contractor_msg', $update_success);
      return redirect()->to('/admin?scroll=onboarding_message_for_contractors_only');
  }


  public function onboarding_email(){

    $recipient_email = $this->request->getPost('onboarding_recipient_email');
    $optional_cc_email = $this->request->getPost('onboarding_optional_cc_email');

    $this->admin_m->update_static_settings_onboarding_email($recipient_email,$optional_cc_email);

    $update_success = 'The User Accounts Setting is now updated.';
    $this->session->setFlashdata('onboarding_default_email', $update_success);

    return redirect()->to('/admin?scroll=onboarding_email_notification');

  }



  public function default_email_message_onboarding_notif(){

    $sender_name_onboarding_notif = $this->request->getPost('sender_name_onboarding_notif');
    $sender_email_onboarding_notif = $this->request->getPost('sender_email_onboarding_notif');
    $subject_onboarding_notif = $this->request->getPost('subject_onboarding_notif');
    $email_msg_onboarding_notif = $this->request->getPost('email_msg_onboarding_notif');
    $bcc_email_onboarding_notif = $this->request->getPost('bcc_email_onboarding_notif');
    $user_id = $this->request->getPost('user_assigned_onboarding_notif');

    $this->admin_m->update_admin_default_email_message_onboarding_notif($sender_name_onboarding_notif,$sender_email_onboarding_notif,$subject_onboarding_notif,$email_msg_onboarding_notif,$bcc_email_onboarding_notif,$user_id);


  }


  public function default_email_message_onboarding_approved_clients(){

    $sender_name_onboarding_approved = $this->request->getPost('sender_name_onboarding_approved_clients');
    $sender_email_onboarding_approved = $this->request->getPost('sender_email_onboarding_approved_clients');
    $subject_onboarding_approved = $this->request->getPost('subject_onboarding_approved_clients');
    $email_msg_onboarding_approved = $this->request->getPost('email_msg_onboarding_approved_clients');
    $bcc_email_onboarding_approved = $this->request->getPost('bcc_email_onboarding_approved_clients');
    $user_id = $this->request->getPost('user_assigned_onboarding_approved_client');

    $this->admin_m->update_admin_default_email_message_onboarding_approved_clients($sender_name_onboarding_approved,$sender_email_onboarding_approved,$subject_onboarding_approved,$email_msg_onboarding_approved,$bcc_email_onboarding_approved,$user_id);

    return redirect()->to('/admin?scroll=onboarding_approved_defaults');



  }

  public function default_email_message_onboarding_clients(){

    $sender_name_onboarding_clients = $this->request->getPost('sender_name_onboarding_clients');
    $sender_email_onboarding_clients = $this->request->getPost('sender_email_onboarding_clients');
    $subject_onboarding_clients = $this->request->getPost('subject_onboarding_clients');
    $email_msg_onboarding_clients = $this->request->getPost('email_msg_onboarding_clients');
    $bcc_email_onboarding_clients = $this->request->getPost('bcc_email_onboarding_clients');
    $user_id_clients = $this->request->getPost('user_assigned_onboarding_clients');
    
    $this->admin_m->update_admin_default_email_message_onboarding_clients($sender_name_onboarding_clients,$sender_email_onboarding_clients,$subject_onboarding_clients,$email_msg_onboarding_clients,$bcc_email_onboarding_clients,$user_id_clients);

    return redirect()->to('/admin?scroll=onboarding_sending_link_email_defaults');
  }


  public function default_email_message_onboarding_bank(){
    $sender_name_onboarding_bank = $this->request->getPost('sender_name_onboarding_bank');
    $sender_email_onboarding_bank = $this->request->getPost('sender_email_onboarding_bank');
    $subject_onboarding_bank = $this->request->getPost('subject_onboarding_bank');
    $email_msg_onboarding_bank = $this->request->getPost('email_msg_onboarding_bank');
    $bcc_email_onboarding_bank = $this->request->getPost('bcc_email_onboarding_bank');
    $user_id = $this->request->getPost('user_assigned_onboarding_bank');

    $this->admin_m->update_admin_default_email_message_onboarding_bank($sender_name_onboarding_bank,$sender_email_onboarding_bank,$subject_onboarding_bank,$email_msg_onboarding_bank,$bcc_email_onboarding_bank,$user_id);

    return redirect()->to('/admin?scroll=onboarding_bank_details_form');

  }








      public function default_email_message_onboarding_approved(){

    $sender_name_onboarding_approved = $this->request->getPost('sender_name_onboarding_approved');
    $sender_email_onboarding_approved = $this->request->getPost('sender_email_onboarding_approved');
    $subject_onboarding_approved = $this->request->getPost('subject_onboarding_approved');
    $email_msg_onboarding_approved = $this->request->getPost('email_msg_onboarding_approved');
    $bcc_email_onboarding_approved = $this->request->getPost('bcc_email_onboarding_approved');
    $user_id = $this->request->getPost('user_assigned_onboarding_approved');

    $this->admin_m->update_admin_default_email_message_onboarding_approved($sender_name_onboarding_approved,$sender_email_onboarding_approved,$subject_onboarding_approved,$email_msg_onboarding_approved,$bcc_email_onboarding_approved,$user_id);

    return redirect()->to('/admin?scroll=onboarding_approved_defaults');

  }

  public function default_email_message_onboarding_declined_clients(){

    $sender_name_onboarding_declined = $this->request->getPost('sender_name_onboarding_declined_clients');
    $sender_email_onboarding_declined = $this->request->getPost('sender_email_onboarding_declined_clients');
    $subject_onboarding_declined = $this->request->getPost('subject_onboarding_declined_clients');
    $email_msg_onboarding_declined = $this->request->getPost('email_msg_onboarding_declined_clients');
    $bcc_email_onboarding_declined = $this->request->getPost('bcc_email_onboarding_declined_clients');
    $user_id = $this->request->getPost('user_assigned_onboarding_declined_clients');

    $this->admin_m->update_admin_default_email_message_onboarding_declined_clients($sender_name_onboarding_declined,$sender_email_onboarding_declined,$subject_onboarding_declined,$email_msg_onboarding_declined,$bcc_email_onboarding_declined,$user_id);


return redirect()->to('/admin?scroll=onboarding_declined_defaults');

  }

  public function default_email_message_onboarding_declined(){

    $sender_name_onboarding_declined = $this->request->getPost('sender_name_onboarding_declined');
    $sender_email_onboarding_declined = $this->request->getPost('sender_email_onboarding_declined');
    $subject_onboarding_declined = $this->request->getPost('subject_onboarding_declined');


    $email_msg_onboarding_declined = $this->request->getPost('email_msg_onboarding_declined');
    $bcc_email_onboarding_declined = $this->request->getPost('bcc_email_onboarding_declined');
    $user_id = $this->request->getPost('user_assigned_onboarding_declined');

    $this->admin_m->update_admin_default_email_message_onboarding_declined($sender_name_onboarding_declined,$sender_email_onboarding_declined,$subject_onboarding_declined,$email_msg_onboarding_declined,$bcc_email_onboarding_declined,$user_id);

    
    return redirect()->to('/admin?scroll=onboarding_declined_defaults');

  }












  public function default_email_message_onboarding(){
    $sender_name_onboarding = $this->request->getPost('sender_name_onboarding');
    $sender_email_onboarding = $this->request->getPost('sender_email_onboarding');
    $subject_onboarding = $this->request->getPost('subject_onboarding');
    $email_msg_onboarding = $this->request->getPost('email_msg_onboarding');
    $bcc_email_onboarding = $this->request->getPost('bcc_email_onboarding');
    $user_id = $this->request->getPost('user_assigned_onboarding');

    $this->admin_m->update_admin_default_email_message_onboarding($sender_name_onboarding,$sender_email_onboarding,$subject_onboarding,$email_msg_onboarding,$bcc_email_onboarding,$user_id);

    return redirect()->to('/admin?scroll=onboarding_sending_link_email_defaults');

  }


  public function set_company_warehouse(){
    $client_brand_id = $_POST['company_brand_name'];
    $warehouse_id = $_POST['warehouse_location'];

    $this->admin_m->insert_company_warehouse($client_brand_id,$warehouse_id);
    return redirect()->to('/admin#client_supply_settings');
  }


  public function update_weeks_delivery(){
    $weeks = $_POST['weeks'];
    $this->admin_m->update_defailt_weeks_delivery($weeks);
    return redirect()->to('/admin#client_supply_settings');
  }


  public function insert_default_doc_storage_receiver(){
    $data = json_decode(file_get_contents("php://input"), true);
        $notif_receiver = $data['notif_receiver'];
        $cm_wa = $data['cm_wa'];
        $cm_nsw = $data['cm_nsw'];

        $pa = 0;
        $pm = 0;
        $set_out = 0;
        $joinery = 0;
    
    foreach ($notif_receiver as $value) {
        switch($value){
          case 'pa':
            $pa = 1;
            break;
          case 'pm':
            $pm = 1;
            break;
          case 'set_out':
            $set_out = 1;
            break;
          case 'joinery':
            $joinery = 1;
            break;
        }
    }
    
    $this->admin_m->save_default_doc_storage_receiver($pa,$pm,$set_out,$joinery,$cm_wa,$cm_nsw);


  }


  public function save_default_doc_storage(){
    $data = json_decode(file_get_contents("php://input"), true);
        $authorize_role_id = $data['authorize_role_id'];
        $email_subject = $data['email_subject'];
        $email_content = $data['email_content'];
    $this->admin_m->save_default_doc_storage($authorize_role_id,$email_subject,$email_content);
  }

  public function fetch_default_doc_storage(){
    $query = $this->admin_m->fetch_default_doc_storage();
    echo json_encode($query->getResult());
  }

  public function fetch_all_roles(){

    $this->user_model = new Users_m();


    $query = $this->user_model->fetch_all_roles();
    echo json_encode($query->getResult());
  }


  public function list_doc_type(){
    $this->projects_m = new Projects_m();

    $query = $this->projects_m->list_doc_type();
    echo json_encode($query->getResult());
  }

  public function delete_required_doc_type(){
    $data = json_decode(file_get_contents("php://input"), true);
        $doc_storage_required_notification_id = $data['doc_storage_required_notification_id'];
    $this->admin_m->delete_required_doc_type($doc_storage_required_notification_id);
  }


  public function insert_required_doc_type(){
    $data = json_decode(file_get_contents("php://input"), true);
        $doc_type = $data['doc_type'];
    $this->admin_m->insert_required_doc_type($doc_type);
  }


  public function fetch_doc_storage_required_notification(){
    $query = $this->admin_m->fetch_doc_storage_required_notification();
    echo json_encode($query->getResult());
  }

  public function get_seasons(){
    $year = date("Y");
    $q_seasons = $this->admin_m->list_seasons($year);


    foreach ($q_seasons->getResult() as $img_bg){

      echo '<div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" class="seasons_label_view" data-parent="#seasons_accordion" href="#'.strtolower(str_replace(' ','_',$img_bg->seasons_label)).'_'.$img_bg->seasons_id.'">'.$img_bg->seasons_label.'</a>  &nbsp; 
          <strong style="font-size: smaller;"><span class="start_date_bg">'.$img_bg->date_start.'</span>/'.$year.'</strong>
          - <strong style="font-size: smaller;"><span class="finish_date_bg">'.$img_bg->date_finish.'</span>/'.$year.'</strong>
          <div id="'.$img_bg->seasons_id.'_sbg" class="btn btn-xs pull-right btn-info edit_bg_btn"  data-toggle="modal" data-target="#bg_date_update" onclick="bgDatEdit(\''.$img_bg->seasons_id.'_sbg\')">Edit</div>
          <a id=""  href="'.base_url().'/admin/delete_bg/'.$img_bg->seasons_id.'"><div id="" class="btn btn-xs pull-right btn-danger m-right-15">Delete</div></a>
        </h4>
      </div>
      <div id="'.strtolower(str_replace(' ','_',$img_bg->seasons_label)).'_'.$img_bg->seasons_id.'" class="panel-collapse collapse">
        <div class="panel-body">';

          $q_pickup_season_bg = $this->admin_m->pickup_season_bg($img_bg->seasons_id);


          if($q_pickup_season_bg->getNumRows() > 0){
            foreach ($q_pickup_season_bg->getResult() as $img_list){
              echo '<div class="col-sm-2 text-center m-right-15"><span  >
              <img alt="'.$img_bg->seasons_label.'" src="'.base_url().'/uploads/login_bg/'.$img_list->file_name.'" class="img-thumbnail pointer" data-toggle="modal" data-target="#bg_design_view" onclick="setModalBG(\''.$img_list->seasons_background_id.'_'.$img_list->seasons_id.'\')" id="'.$img_list->seasons_background_id.'_'.$img_list->seasons_id.'">';
              
              if($img_list->is_selected == 1){
                echo '<span class="tooltip-enabled" data-original-title="Selected as background." style="position: absolute; right: 0; bottom: 0; background: #fff;"><i class="fa fa-check-square fa-lg"></i></span>';
              }

              echo '</span></div>';
            }
          }else{
            echo "<p>No Backgrounds Uploaded Yet.</p>";
          }


          echo '</div></div></div>';
        }
      }

  public function fetch_employee_rate(){
        $emp_rate_q = $this->admin_m->fetch_user_rate();

        foreach ($emp_rate_q->getResultArray() as $row){
          echo '<tr>';
          echo '<td><span class="badge alert-success pointer" title = "Edit" onclick = "edit_emp_rate('.$row['employee_rate_id'].')"><i class="fa fa-pencil-square-o"></i></span>'.$row['user_first_name'].' '.$row['user_last_name'].'</td>';
          echo '<td>'.$row['rate_set_name'].'</td>';

          echo '</tr>';
        }
      }

  public function fetch_emp_rate_set(){
        $emp_rate_q = $this->admin_m->fetch_user_rate_set();

        foreach ($emp_rate_q->getResultArray() as $row){
          echo '<tr>';
          echo '<td>'.$row['rate_set_name'].'<span class="badge alert-success pointer pull-right" title = "Edit" onclick = "edit_rate_set('.$row['employee_rate_set_id'].')"><i class="fa fa-pencil-square-o"></i></span></td>';
          echo '<td>'.$row['normal_rate'].'</td>';
          echo '<td>'.$row['time_half_rate'].'</td>';
          echo '<td>'.$row['double_time_rate'].'</td>';
          echo '<td>'.$row['double_time_half_rate'].'</td>';
          echo '<td>'.$row['travel'].'</td>';
          echo '<td>'.$row['meal'].'</td>';
          echo '<td>'.$row['living_away_from_home'].'</td>';
          echo '</tr>';
        }
      }



  public function list_cc_emails_static(){

    $q_static_emails = $this->admin_m->get_cc_emails_static();
    $q_res_arr_etatic_emails = $q_static_emails->getResult();
    $static_emails = array_shift($q_res_arr_etatic_emails);

    $cc_emails_cqr = explode(',',$static_emails->cc_emails_cqr);
    $cc_emails_cqr_reply = explode(',',$static_emails->cc_emails_cqr_reply);
    $cc_emails_cpo = explode(',',$static_emails->cc_emails_cpo);
    $cc_emails_induction = explode(',',$static_emails->cc_emails_induction);


    echo '<div class="col-sm-3 col-xs-12 clearfix "><strong><em class="fa fa-envelope"></em> Selected CC E-mails for CQR</strong>';
    if( strlen($static_emails->cc_emails_cqr) > 0 ){
      foreach ($cc_emails_cqr as $key => $user_id) {
        $q_cc_emails = $this->user_model->list_user_short(' AND `users`.`user_id` ='.$user_id);
        $get_res_cc_email = $q_cc_emails->getResult();
        $cc_email = array_shift($get_res_cc_email);
        echo '<p class="m-top-10"><a id="" href="'.base_url().'/admin/cc_static_delete_email/'.$user_id.'/cqr" class="btn btn-xs btn-danger m-right-5 pad-3"><em class="fa fa-trash-o"></em></a> '.$cc_email->user_first_name.' '.$cc_email->user_last_name.'  </p>';
      }
    }else{
      echo '<p class="m-top-10"><strong class="">No Employee Selected</strong></p>';
    }
    echo '</div>';

    echo '<div class="col-sm-3 col-xs-12 clearfix "><strong><em class="fa fa-envelope"></em> Selected CC E-mails for CQR Reply</strong>';
    if( strlen($static_emails->cc_emails_cqr_reply) > 0 ){
      foreach ($cc_emails_cqr_reply as $key => $user_id) {
        $q_cc_emails = $this->user_model->list_user_short(' AND `users`.`user_id` ='.$user_id);
        $get_res_cc_email = $q_cc_emails->getResult();
        $cc_email = array_shift($get_res_cc_email);
        echo '<p class="m-top-10"><a id="" href="'.base_url().'/admin/cc_static_delete_email/'.$user_id.'/rpl" class="btn btn-xs btn-danger m-right-5 pad-3"><em class="fa fa-trash-o"></em></a> '.$cc_email->user_first_name.' '.$cc_email->user_last_name.'  </p>';
      }
    }else{
      echo '<p class="m-top-10"><strong class="">No Employee Selected</strong></p>';
    }
    echo '</div>';

    echo '<div class="col-sm-3 col-xs-12 clearfix "><strong><em class="fa fa-envelope"></em> Selected CC E-mails for CPO</strong>'; 
    if( strlen($static_emails->cc_emails_cpo) > 0 ){
      foreach ($cc_emails_cpo as $key => $user_id) {
        $q_cc_emails = $this->user_model->list_user_short(' AND `users`.`user_id` ='.$user_id);
        $get_res_cc_email = $q_cc_emails->getResult();
        $cc_email = array_shift($get_res_cc_email);
        echo '<p class="m-top-10"><a id="" href="'.base_url().'/admin/cc_static_delete_email/'.$user_id.'/cpo" class="btn btn-xs btn-danger m-right-5 pad-3"><em class="fa fa-trash-o"></em></a> '.$cc_email->user_first_name.' '.$cc_email->user_last_name.'  </p>';
      }
    }else{
      echo '<p class="m-top-10"><strong class="">No Employee Selected</strong></p>';
    }
    echo '</div>';

    echo '<div class="col-sm-3 col-xs-12 clearfix "><strong><em class="fa fa-envelope"></em> Selected CC E-mails for CPO - Induction</strong>';


    if( strlen($static_emails->cc_emails_induction) > 0 ){
      foreach ($cc_emails_induction as $key => $user_id) {
        $q_cc_emails = $this->user_model->list_user_short(' AND `users`.`user_id` ='.$user_id);
        $get_res_cc_email = $q_cc_emails->getResult();
        $cc_email = array_shift($get_res_cc_email);
        echo '<p class="m-top-10"><a id="" href="'.base_url().'/admin/cc_static_delete_email/'.$user_id.'/ind" class="btn btn-xs btn-danger m-right-5 pad-3"><em class="fa fa-trash-o"></em></a> '.$cc_email->user_first_name.' '.$cc_email->user_last_name.'  </p>';
      }
    }else{
      echo '<p class="m-top-10"><strong class="">No Employee Selected</strong></p>';
    }
    echo '</div>';
  }

  public function cc_static_delete_email($user_id,$type){

 //   $updated_set_array = array_values(array_diff($array, array($user_id)));

    $q_static_emails = $this->admin_m->get_cc_emails_static();
    $get_res_stat_email = $q_static_emails->getResult();
    $static_emails = array_shift($get_res_stat_email);

    $cc_emails_cqr = explode(',',$static_emails->cc_emails_cqr);
    $cc_emails_cpo = explode(',',$static_emails->cc_emails_cpo);
    $cc_emails_cqr_reply = explode(',',$static_emails->cc_emails_cqr_reply);
    $cc_emails_induction = explode(',',$static_emails->cc_emails_induction);

    switch ($type) {
      case "cqr":
        $result = array_diff($cc_emails_cqr,array($user_id));
        $updated_values = implode(',',$result);
        $area = 'cc_emails_cqr';
      break;
      case "rpl":
        $result = array_diff($cc_emails_cqr_reply,array($user_id));
        $updated_values = implode(',',$result);
        $area = 'cc_emails_cqr_reply';
      break;
      case "cpo":
        $result = array_diff($cc_emails_cpo,array($user_id));
        $updated_values = implode(',',$result);
        $area = 'cc_emails_cpo';
      break;
      case "ind":
        $result = array_diff($cc_emails_induction,array($user_id));
        $updated_values = implode(',',$result);
        $area = 'cc_emails_induction';
      break;

    }

    $this->admin_m->update_static_cc_emails($updated_values,$area);
    return redirect()->to('/admin?scroll=cqr_email_template');


  }

  public function update_allowed_user_rempend_comp(){
    $user_id = $this->request->getPost('allowed_user_id');
    $this->admin_m->update_allowed_users_remove_comp($user_id);


    return redirect()->to('/admin?scroll=onboarding_pending_company_removal_link');
  }


    public function rem_allowed_id($id){
      $this->user_model = new Users_m();

      $static_defaults = $this->user_model->select_static_defaults();
      $get_res_arr_defs = $static_defaults->getResultArray();
      $data_sd = array_shift($get_res_arr_defs);
      $allowed_users = explode(',',$data_sd['remove_pending_onboarding']);
    
      $allowed_users = array_flip($allowed_users);
      unset($allowed_users[$id]);
      $allowed_users = array_flip($allowed_users);

      $updated_list = implode(',',$allowed_users);

      $this->admin_m->update_allowed_users($updated_list);
      return redirect()->to('/admin?scroll=onboarding_pending_company_removal_link');
    }



  public function onboarding_general_msg(){

    $onboarding_general_msg = $this->request->getPost('onboarding_general_msg');

    $this->admin_m->update_static_settings_onboarding_general_msg($onboarding_general_msg);

    $update_success = 'Onboarding General Message is updated';
    $this->session->setFlashdata('onboarding_general_msg', $update_success);

    return redirect()->to('/admin?scroll=onboarding_general_message_box');
    
  }











  public function onboarding_workplace_health_safety(){

      $workplace_health_safety_msg = $this->request->getPost('onboarding_workplace_health_safety');

      $this->admin_m->update_static_settings_workplace_health_safety_msg($workplace_health_safety_msg);

      $update_success = 'Workplace Health & Safety Default Message Message is updated';
      $this->session->setFlashdata('onboarding_workplace_health_safety', $update_success);
      return redirect()->to('/admin?scroll=workplace_health_safety');
 
  }

  public function onboarding_swms(){

      $swms_msg = $this->request->getPost('onboarding_swms');

      $this->admin_m->update_static_settings_swms_msg($swms_msg);

      $update_success = 'Safe Work Method Statements (SWMS) Default Message Message is updated';
      $this->session->setFlashdata('onboarding_swms', $update_success);
      return redirect()->to('/admin?scroll=safe_work_method_statements');
   
  }

  public function onboarding_jsa(){

      $jsa_msg = $this->request->getPost('onboarding_jsa');

      $this->admin_m->update_static_settings_jsa_msg($jsa_msg);

      $update_success = 'Job Safety Analysis (JSA) Default Message Message is updated';
      $this->session->setFlashdata('onboarding_jsa', $update_success);
      return redirect()->to('/admin?scroll=job_safety_analysis');
   
  }

  public function onboarding_reviewed_swms(){

      $reviewed_swms_msg = $this->request->getPost('onboarding_reviewed_swms');

      $this->admin_m->update_static_settings_reviewed_swms_msg($reviewed_swms_msg);

      $update_success = 'Reviewed SWMS is updated';
      $this->session->setFlashdata('onboarding_reviewed_swms', $update_success);
      return redirect()->to('/admin?scroll=reviewed_swms');
    
  }

  public function onboarding_safety_related_convictions(){

      $safety_related_convictions_msg = $this->request->getPost('onboarding_safety_related_convictions');

      $this->admin_m->update_static_settings_safety_related_convictions_msg($safety_related_convictions_msg);

      $update_success = 'Safety Related Convictions is updated';
      $this->session->setFlashdata('onboarding_safety_related_convictions', $update_success);
      return redirect()->to('/admin?scroll=safety_related_convictions');
    
  }

  public function onboarding_confirm_licences_certifications(){

      $confirm_licences_certifications_msg = $this->request->getPost('onboarding_confirm_licences_certifications');

      $this->admin_m->update_static_settings_confirm_licences_certifications_msg($confirm_licences_certifications_msg);

      $update_success = 'Confirm Licences Certifications is updated';
      $this->session->setFlashdata('onboarding_confirm_licences_certifications', $update_success);
      return redirect()->to('/admin?scroll=confirm_licences_certifications');
    
  }

  public function user_settings(){

      $days_exp = $this->request->getPost('days_exp');
      $temp_password = $this->request->getPost('temp_password');

      $this->admin_m->update_static_settings($days_exp,$temp_password);
      $update_success = 'The User Accounts Setting is now updated.';
      $this->session->setFlashdata('update_user_settings', $update_success);
     
      return redirect()->to('/admin?scroll=user_default_settings');
  }

  public function salaried_rates(){
    $salaried_total_annual_leave = $this->request->getPost('salaried_total_annual_leave');
    $salaried_total_personal_leave = $this->request->getPost('salaried_total_personal_leave');

    $this->admin_m->update_static_settings_leave_rates($salaried_total_annual_leave,$salaried_total_personal_leave,'1');

    $q_fetch_users_by_leave_rate = $this->admin_m->fetch_users_by_leave_rate('1');

    if ($q_fetch_users_by_leave_rate->getNumRows() > 0){
       foreach ($q_fetch_users_by_leave_rate->getResult() as $row){
        $this->admin_m->update_leave_request_to_disabled($row->user_id);
      }
    }

    $this->admin_m->update_leave_alloc_to_inactive('1');

    $update_success = 'The Salaried - AU Staffs Leave Rates Setting is now updated.';
    $this->session->setFlashdata('update_user_settings_leave_rates', $update_success);

    return redirect()->to('/admin?scroll=leave_rates');
  }




  public function wages_rates(){

    $wages_total_annual_leave = $this->request->getPost('wages_total_annual_leave');
    $wages_total_personal_leave = $this->request->getPost('wages_total_personal_leave');
    $wages_total_rdo = $this->request->getPost('wages_total_rdo');

    $this->admin_m->update_static_settings_leave_rates_rdo($wages_total_annual_leave,$wages_total_personal_leave,$wages_total_rdo,'2');

    $q_fetch_users_by_leave_rate = $this->admin_m->fetch_users_by_leave_rate('2');

    if ($q_fetch_users_by_leave_rate->getNumRows() > 0){
       foreach ($q_fetch_users_by_leave_rate->getResult() as $row){
        $this->admin_m->update_leave_request_to_disabled($row->user_id);
      }
    }

    $this->admin_m->update_leave_alloc_to_inactive('2');

    $update_success = 'The Wages - AU Staffs Leave Rates Setting is now updated.';
    $this->session->setFlashdata('update_user_settings_leave_rates', $update_success);

    return redirect()->to('/admin?scroll=leave_rates');


  }

  public function manila_rates(){

    $manila_total_vacation_leave = $this->request->getPost('manila_total_vacation_leave');
    $manila_total_sick_leave = $this->request->getPost('manila_total_sick_leave');

    $this->admin_m->update_static_settings_leave_rates($manila_total_vacation_leave,$manila_total_sick_leave,'3');

    $q_fetch_users_by_leave_rate = $this->admin_m->fetch_users_by_leave_rate('3');

    if ($q_fetch_users_by_leave_rate->getNumRows() > 0){
        foreach ($q_fetch_users_by_leave_rate->getResult() as $row){
        $this->admin_m->update_leave_request_to_disabled($row->user_id);
      }
    } 

    $this->admin_m->update_leave_alloc_to_inactive('3');

   $update_success = 'The Manila Leave Rates Setting is now updated.';
    $this->session->setFlashdata('update_user_settings_leave_rates', $update_success);

   return redirect()->to('/admin?scroll=leave_rates');


  }

  public function update_leave_notice(){
   
    $annual_notice1 = $this->request->getPost('annual_leave_notice1');
    $annual_notice2 = $this->request->getPost('annual_leave_notice2');
    $annual_notice3 = $this->request->getPost('annual_leave_notice3');

    $this->admin_m->update_leave_notice_defaults($annual_notice1, '1', '1');
    $this->admin_m->update_leave_notice_defaults($annual_notice2, '1', '2');
    $this->admin_m->update_leave_notice_defaults($annual_notice3, '1', '3');

    $update_success = 'The Leave Approval Days Notice Setting is now updated.';
    $this->session->setFlashdata('leave_approval_notice', $update_success);
      
    return redirect()->to('/admin?scroll=annual_leave_days_notice');
  }

  public function update_leave_emails(){
     
    $recipient_email = $this->request->getPost('leave_recipient_email');
    $cc_email = $this->request->getPost('leave_cc_email');
    $bcc_email = $this->request->getPost('leave_bcc_email');
    $message_content = $this->request->getPost('leave_message_content');
    
    $this->admin_m->update_leave_email_defaults($recipient_email, $cc_email, $bcc_email, $message_content);

    $update_success = 'The Default Leave Approval Email Setting is now updated.';
    $this->session->setFlashdata('leave_approval_email', $update_success);
    return redirect()->to('/admin?scroll=leave_email_defaults');
   
  }

  public function insert_employee_rate(){
    $user_id = $_POST['user_id'];
    $rate_id = $_POST['rate_id'];

    $this->admin_m->insert_employee_rate($user_id,$rate_id);
  }

  public function fetch_selected_rate_set($employee_val_id=''){
    $employee_rate_set_id = $_POST['employee_rate_set_id'] ?? $employee_val_id;

    $rate_set_q = $this->admin_m->fetch_rate_set_seleted($employee_rate_set_id);
    $get_res_arr = $rate_set_q->getResultArray();
    $qArr = array_shift($get_res_arr);
    $rate_set_name = $qArr['rate_set_name'];
    $normal_rate = $qArr['normal_rate'];
    $time_half_rate = $qArr['time_half_rate'];
    $double_time_rate = $qArr['double_time_rate']; 
    $double_time_half_rate = $qArr['double_time_half_rate'];
    $travel_allowance = $qArr['travel'];
    $meal_allowance = $qArr['meal'];
    $lafh_allowance = $qArr['living_away_from_home'];

    echo $rate_set_name."/".$normal_rate."/".$time_half_rate."/".$double_time_rate."/".$double_time_half_rate."/".$travel_allowance."/".$meal_allowance."/".$lafh_allowance;
  }

  public function update_selected_rate_set(){
    $employee_rate_set_id = $_POST['employee_rate_set_id'];
    $rate_set_name = $_POST['rate_set_name'];
    $normal_rate = $_POST['normal_rate'];
    $time_half_rate = $_POST['time_half_rate'];
    $double_time_rate = $_POST['double_time_rate']; 
    $double_time_half_rate = $_POST['double_time_half_rate'];
    $travel_allowance = $_POST['travel_allowance'];
    $meal_allowance = $_POST['meal_allowance'];
    $lafh_allowance = $_POST['lafh_allowance'];

    $this->admin_m->update_rate_set($employee_rate_set_id,$rate_set_name,$normal_rate,$time_half_rate,$double_time_rate,$double_time_half_rate,$travel_allowance,$meal_allowance,$lafh_allowance);
  }

  public function fetch_assigned_employee_rate(){
    $employee_rate_id = $_POST['employee_rate_id'];

    $emp_rate_q = $this->admin_m->fetch_emp_rate_seleted($employee_rate_id);
    $get_res_arr = $emp_rate_q->getResultArray();
    $qArr = array_shift($get_res_arr);
    $user_id = $qArr['user_id'];
    $employee_rate_set_id = $qArr['employee_rate_set_id'];

    echo $user_id."/".$employee_rate_set_id;
  }



  public function update_employee_rate(){
    $employee_rate_id = $_POST['employee_rate_id'];
    $user_id = $_POST['user_id'];
    $rate_id = $_POST['rate_id'];
    $this->admin_m->update_employee_rate($employee_rate_id,$user_id,$rate_id);
  }

  public function remove_employee_rate(){
    $employee_rate_id = $_POST['employee_rate_id'];
    $this->admin_m->remove_employee_rate($employee_rate_id);
  }

  public function remove_selected_rate_set(){
    $employee_rate_set_id = $_POST['employee_rate_set_id'];
    $this->admin_m->remove_rate_set($employee_rate_set_id);
  }


  public function po_rev_settings(){
    //var_dump($_POST);

    $weeks_old = $_POST['weeks_old'];
    $reminder_day_no = $_POST['reminder_day_no'];
    $days_wip_report = $_POST['days_wip_report'];
    $set_cc_porw = $_POST['set_cc_porw'];
    $send_wip_rrtime = $_POST['send_wip_rrtime'];

    $this->admin_m->update_po_rev_settings($weeks_old,$reminder_day_no,$days_wip_report,$set_cc_porw,$send_wip_rrtime);
    
    return redirect()->to('/admin?scroll=po_review');
  }



  public function insert_rate_set(){
    $rate_set_name = $_POST['rate_set_name'];
    $normal_rate = $_POST['normal_rate'];
    $time_half_rate = $_POST['time_half_rate'];
    $double_time_rate = $_POST['double_time_rate']; 
    $double_time_half_rate = $_POST['double_time_half_rate'];
    $travel_allowance = $_POST['travel_allowance'];
    $meal_allowance = $_POST['meal_allowance'];
    $lafh_allowance = $_POST['lafh_allowance'];

    $this->admin_m->insert_rate_set($rate_set_name,$normal_rate,$time_half_rate,$double_time_rate,$double_time_half_rate,$travel_allowance,$meal_allowance,$lafh_allowance);
  }




















  // induction start =========================

  public function display_all_job_category_type(){

    $this->projects_m = new Projects_m();

    $query = $this->projects_m->display_all_job_category_type();
    echo json_encode($query->getResult());
  }

  public function display_license_cert(){

    $this->induction_health_safety_m = new Induction_health_safety_m();

    $query = $this->induction_health_safety_m->fetch_license_cert_type();
    echo json_encode($query->getResult());
  }

  public function display_required_license_cert(){
    $query = $this->admin_m->display_required_license_cert();
    echo json_encode($query->getResult());
  }

  public function add_required_license_cert(){
    $activity_id = $_POST['activity_id'];
    $license_cert_id = $_POST['license_cert_id'];
    $state_id = $_POST['state_id'];

    $this->admin_m->add_required_license_cert($activity_id,$license_cert_id,$state_id);

    $query = $this->admin_m->display_required_license_cert();
    echo json_encode($query->getResult());
  }

  public function remove_required_license_cert(){
    $required_license_certificate_id = $_POST['required_license_certificate_id'];
    $this->admin_m->remove_required_license_cert($required_license_certificate_id);

    $query = $this->admin_m->display_required_license_cert();
    echo json_encode($query->getResult());
  }

  public function update_induction_slide_notes(){
    $induction_slide4_notes = $_POST['slide4_note'];
    $induction_slide6_notes = $_POST['slide6_note'];

    $this->admin_m->update_induction_slide_notes($induction_slide4_notes,$induction_slide6_notes);
  }

  public function fetch_exempted_project_list(){
    $query = $this->admin_m->fetch_exempted_project_list();
    echo json_encode($query->getResult());
  }


  public function company(){
    if( $this->session->get('is_admin') != 1):   
      return redirect()->to('/projects');
    endif;
 

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $data['all_focus_company'] = $all_focus_company->getResult();

    if($data['all_focus_company']==''){
      $data['all_focus_company'] = array();
    }

    $data['page_title'] = 'Focus Company';
    $data['screen'] = 'Focus Company';
    $data['main_content'] = 'App\Modules\Admin\Views\admin_company';

    return view('App\Views\page',$data);
  }

  public function delete_company($company_id){

    $this->user_model = new Users_m();
    $this->company_m  = new Company_m();

    $this->company_m->delete_company($company_id);

    // user log creation company
    $type = 'Delete';
    $actions = 'Delete company id: '.$company_id.'';
    // date_default_timezone_set("Australia/Perth");
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');

    return redirect()->to('/admin/company');
  }


  public function admin_company($company_id = ''){
    if( $this->session->get('is_admin') != 1):   
      return redirect()->to('/projects');
    endif;

    
    $this->company_m  = new Company_m();

    $curr_admin_id = $company_id ?? $this->uri->getSegment(3);
    $admin_company_details = $this->admin_m->fetch_single_company_focus($curr_admin_id);



// upload file ??
    /*
    $config['upload_path'] = './uploads/misc/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = '1024';
    $config['max_width']  = '1024';
    $config['max_height']  = '768';

    $time = date("hismdY", time());
    $config['file_name']  = 'focus_'.$time;

    $this->upload->initialize($config);

    $this->load->library('upload', $config);
    $this->upload->initialize($config);
    */
// upload file ??

// upload process
    if(isset($_POST['company_id_data'])){

      $this->users = new Users();

      $file_upload_raw = $this->users->_upload_primary_photo('focus_company_logo','misc','focus_company_logo_');
      $file_upload_arr = explode('|',$file_upload_raw);
      $logo = $file_upload_arr[1]; // file name
    
      $comp_id = $_POST['company_id_data'];
      $this->admin_m->updat_admin_comp_logo($comp_id,$logo);

      return redirect()->to('/admin/admin_company/'.$comp_id);
    }
// upload process


    $get_ress_arr_a = $admin_company_details->getResultArray();
    $data = array_shift( $get_ress_arr_a );
    


    $all_aud_states = $this->company_m->fetch_all_states();
    $data['all_aud_states'] = $all_aud_states->getResult();


    $query_address= $this->company_m->fetch_complete_detail_address($data['address_id']);
    $get_res_arr_b = $query_address->getResultArray();
    $temp_data = array_shift($get_res_arr_b);
    $data['postcode'] = $temp_data['postcode'];
    $data['suburb'] = $temp_data['suburb'];
    $data['po_box'] = $temp_data['po_box'];
    $data['street'] = ucwords(strtolower($temp_data['street']));
    $data['unit_level'] = ucwords(strtolower($temp_data['unit_level']));
    $data['unit_number'] = $temp_data['unit_number'];
    $data['state'] = $temp_data['name'];
// $data['address_id'] = $data['address_id'];

    $data['shortname'] = $temp_data['shortname'];
    $data['state_id'] =  $temp_data['state_id'];
    $data['phone_area_code'] = $temp_data['phone_area_code']; 

    $p_query_address = $this->company_m->fetch_complete_detail_address($data['postal_address_id']);
    $get_res_arr_c = $p_query_address->getResultArray();
    $p_temp_data = array_shift( $get_res_arr_c );
    $data['p_po_box'] = $p_temp_data['po_box'];
    $data['p_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
    $data['p_unit_number'] = $p_temp_data['unit_number'];
    $data['p_street'] = ucwords(strtolower($p_temp_data['street']));
    $data['p_suburb'] = $p_temp_data['suburb'];
    $data['p_state'] = $p_temp_data['name'];
    $data['p_postcode'] = $p_temp_data['postcode'];
//    $data['postal_address_id'] = $company_detail['postal_address_id'];

    $data['p_shortname'] = $p_temp_data['shortname'];
    $data['p_state_id'] =  $p_temp_data['state_id'];
    $data['p_phone_area_code'] = $p_temp_data['phone_area_code'];


    $data['screen'] = 'Focus Company';
    $data['page_title'] = 'Focus Company';
    $data['main_content'] = 'App\Modules\Admin\Views\admin_v_company';

    return view('App\Views\page',$data);
  }


  public function add(){
    $rules = array();

    $this->company_m  = new Company_m();
    $this->users      = new Users();

    $all_aud_states = $this->company_m->fetch_all_states();
    $data['all_aud_states'] = $all_aud_states->getResult();


    $rules[ 'company_name' ]    = ['label' => 'Company Name', 'rules'         => ['required', 'trim'] ];

    $rules[ 'street' ]          = ['label' => 'Physical Street', 'rules'      => ['required', 'trim']   ];
    $rules[ 'state_a' ]         = ['label' => 'State', 'rules'                => ['required', 'trim']   ];
    $rules[ 'suburb_a' ]        = ['label' => 'Suburb', 'rules'               => ['required', 'trim']   ];
    $rules[ 'postcode_a' ]      = ['label' => 'Postcode', 'rules'             => ['required', 'trim']   ];

    $rules[ 'street_b' ]        = ['label' => 'Postal Street', 'rules'        => ['required', 'trim']   ];
    $rules[ 'state_b' ]         = ['label' => 'Postal State', 'rules'         => ['required', 'trim']   ];
    $rules[ 'suburb_b' ]        = ['label' => 'Postal Suburb', 'rules'        => ['required', 'trim']   ];
    $rules[ 'postcode_b' ]      = ['label' => 'Postal Postcode', 'rules'      => ['required', 'trim']   ];

    $rules[ 'abn' ]             = ['label' => 'ABN', 'rules'                  => ['required', 'trim']   ];
    $rules[ 'acn' ]             = ['label' => 'ACN', 'rules'                  => ['required', 'trim']   ];
    $rules[ 'areacode' ]        = ['label' => 'Phone Areacode', 'rules'       => ['required', 'trim']   ];
    $rules[ 'contact_number' ]  = ['label' => 'Contact Number', 'rules'       => ['required', 'trim']   ];
    $rules[ 'email' ]           = ['label' => 'Email', 'rules'                => ['required', 'trim', 'valid_email']   ];

    $rules[ 'account-name' ]    = ['label' => 'Account Name', 'rules'         => ['required', 'trim']   ];
    $rules[ 'bank-name' ]       = ['label' => 'Bank Name', 'rules'            => ['required', 'trim']   ];
    $rules[ 'account-number' ]  = ['label' => 'Account Number', 'rules'       => ['required', 'trim']   ];
    $rules[ 'bsb-number' ]      = ['label' => 'BSB Number', 'rules'           => ['required', 'trim']   ]; 

    $rules[ 'unit_level' ]      = ['label' => 'Physical Unit/Level','rules'   => ['trim']   ];
    $rules[ 'unit_number' ]     = ['label' => 'Physical Number', 'rules'      => ['trim']   ];

    $rules[ 'pobox' ]           = ['label' => 'PO Box', 'rules'               => ['trim']   ];
    $rules[ 'unit_level_b' ]    = ['label' => 'Postal Unit/Level', 'rules'    => ['trim']   ];
    $rules[ 'number_b' ]        = ['label' => 'Postal Number', 'rules'        => ['trim']   ];


    if($_SERVER['REQUEST_METHOD'] === 'POST'): // on load if form is usbmitted

      if($this->validate($rules)): // form is valid and process form

        $data['validation'] = $this->validator;
        $data['form_error'] = null;

        $data['company_name']           = $this->cap_first_word($this->if_set($this->request->getPost('company_name')));
        $data['unit_level']             = $this->if_set($this->request->getPost('unit_level'));
        $data['unit_number']            = $this->if_set($this->request->getPost('unit_number'));
        $data['street']                 = $this->cap_first_word($this->if_set($this->request->getPost('street')));

        $state_a_arr                    = explode('|', $this->request->getPost('state_a'));
        $data['state_a']                = $state_a_arr[3];

        $suburb_a_ar                    = explode('|',$this->if_set($this->request->getPost('suburb_a')));
        $data['suburb_a']               = strtoupper($suburb_a_ar[0]);

        $data['postcode_a']             = $this->if_set($this->request->getPost('postcode_a'));
        $data['pobox']                  = $this->if_set($this->request->getPost('pobox'));

        $data['unit_level_b']           = $this->if_set($this->request->getPost('unit_level_b'));
        $data['number_b']               = $this->if_set($this->request->getPost('number_b'));
        $data['street_b']               = $this->cap_first_word($this->if_set($this->request->getPost('street_b')));

        $state_b_arr                    = explode('|', $this->request->getPost('state_b'));
        $data['state_b']                = $state_b_arr[3];

        $suburb_b_ar                    = explode('|',$this->if_set($this->request->getPost('suburb_b')));
        $data['suburb_b']               = strtoupper($suburb_b_ar[0]);
        $data['postcode_b']             = $this->if_set($this->request->getPost('postcode_b'));

        $data['email']                  = $this->if_set($this->request->getPost('email'));
        $data['contact_number']         = $this->if_set($this->request->getPost('contact_number'));
        $data['mobile_number']          = $this->if_set($this->request->getPost('mobile_number'));
        $data['areacode']               = $this->if_set($this->request->getPost('areacode'));

        $data['abn']                    = $this->if_set($this->request->getPost('abn'));
        $data['acn']                    = $this->if_set($this->request->getPost('acn'));
        
        $data['bank-account-name']      = $this->cap_first_word($this->if_set($this->request->getPost('account-name')));
        $data['bank-name']              = $this->cap_first_word($this->if_set($this->request->getPost('bank-name')));
        $data['bank-account-number']    = $this->cap_first_word($this->if_set($this->request->getPost('account-number')));
        $data['bsb-number']             = $this->cap_first_word($this->if_set($this->request->getPost('bsb-number')));


  


        $file_upload_raw = $this->users->_upload_primary_photo('company_logo','misc','focus_company_logo_');
        $file_upload_arr = explode('|',$file_upload_raw);
        $logo = $file_upload_arr[1]; // file name





        $general_address_id_result_a = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_a'],$data['suburb_a']);
        foreach ($general_address_id_result_a->getResult() as $general_address_id_a){
          $general_address_a = $general_address_id_a->general_address_id;
        }

        $general_address_id_result_b = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_b'],$data['suburb_b']);
        foreach ($general_address_id_result_b->getResult() as $general_address_id_b){
          $general_address_b = $general_address_id_b->general_address_id;
        }

        $jurisdiction_raw = $this->request->getPost('jurisdiction');
        $jurisdiction = '';
        foreach ($jurisdiction_raw as $key => $value) {
          $jur_arr = explode('|', $value);
          $jurisdiction .= $jur_arr[3].',';
        }
        $jurisdiction = substr($jurisdiction, 0, -1);


        $bank_account_id = $this->company_m->insert_bank_account($data['bank-account-name'],$data['bank-account-number'],$data['bank-name'],$data['bsb-number']);
        $address_id = $this->company_m->insert_address_detail($data['street'],$general_address_a,$data['unit_level'],$data['unit_number']);
        $postal_address_id = $this->company_m->insert_address_detail($data['street_b'],$general_address_b,$data['unit_level_b'],$data['number_b'],$data['pobox']);
        $new_company_id = $this->company_m->insert_company_details($data['company_name'],$data['abn'],$data['acn'] ,'0',$address_id,$postal_address_id,'5',$bank_account_id,'0','0');
        $contact_number_id = $this->company_m->insert_contact_number($data['areacode'],$data['contact_number'],'',$data['mobile_number'],'');
        $email_id = $this->company_m->insert_email($data['email']);

        $this->admin_m->insert_focus_company_details($new_company_id,$contact_number_id,$email_id,$jurisdiction,$logo);
        $this->session->setFlashdata('new_focus_company', 'New Focus Company is now added.');

        return redirect()->to('/admin/company');

        // proceed insert and redirect here

      else: // form has errors

        $data['validation'] = $this->validator;
        $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';

        // return with errors

        // return with errors

      endif;
    endif;



    $data['main_content'] = 'App\Modules\Admin\Views\new_company';
    $data['page_title'] = 'Add New Focus Company';
    $data['screen'] = 'New Focus Company';
    return view('App\Views\page',$data);


  }

  public function cap_first_word($str){
    return ucwords(strtolower($str));
  }

  public function if_set($val=''){
    if(isset($val)){
      return html_entity_decode($val);
    }else{
      return NULL;
    }
  }



  public function add_exempted_project(){
    $project_number = 46866;//$_POST['project_number'];

    $this->admin_m->add_exempted_project($project_number);
    
    $query = $this->admin_m->fetch_exempted_project_list();
    echo json_encode($query->getResult());
  }

  public function remove_exempted_project(){
    $induction_exempted_projects_id = $_POST['induction_exempted_projects_id'];

    $this->admin_m->remove_exempted_project($induction_exempted_projects_id);
    
    $query = $this->admin_m->fetch_exempted_project_list();
    echo json_encode($query->getResult());
  }

  public function fetch_exempted_postcode(){
    $query = $this->admin_m->fetch_exempted_postcode();
    echo json_encode($query->getResult());
  }

  public function add_exempted_postcode(){
    $induction_filter_state = $_POST['induction_filter_state'];
    $induction_filter_start_postcode = $_POST['induction_filter_start_postcode'];
    $induction_filter_ends_postcode = $_POST['induction_filter_ends_postcode'];

    $this->admin_m->add_exempted_postcode($induction_filter_state,$induction_filter_start_postcode,$induction_filter_ends_postcode);

    $query = $this->admin_m->fetch_exempted_postcode();
    echo json_encode($query->getResult());
  }

  public function remove_exempted_postcode(){
    $induction_postcode_filters_id = $_POST['induction_postcode_filters_id'];

    $this->admin_m->remove_exempted_postcode($induction_postcode_filters_id);

    $query = $this->admin_m->fetch_exempted_postcode();
    echo json_encode($query->getResult());
  }

// induction end =========================



  public function list_warehouse_location(){
    $q_get_warehouse_locations = $this->admin_m->get_warehouse_locations();
    return $q_get_warehouse_locations->getResult();    
  }

  public function list_assigned_warehouse(){
      $q_assigned_warehouse = $this->admin_m->get_assigned_warehouse();
      return $q_assigned_warehouse->getResult();
    }

  public function list_pa_assignment($pa_id){
    $assignment_q = $this->admin_m->fetch_pa_assignment($pa_id);
    $get_res_arr = $assignment_q->getResultArray();
    $assignment = array_shift($get_res_arr);
    return  $assignment;
  }


  public function fetch_users_list(){
    $this->user_model = new Users_m();
    $user_list = $this->user_model->fetch_user();
    
    //echo '<option value="">'.$select_user.'</option>';
    foreach ($user_list->getResultArray() as $row){
      
      echo '<option value="'.$row['user_id'].'">'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      
    }
  }


  public function get_client_companies($type,$display='select'){
    $this->company_m  = new Company_m();

    $company_q = $this->company_m->display_company_by_type($type);
     

    if($display == 'select'){ 

      foreach ($company_q->getResult() as $client_company){
        //var_dump($client_company);
        echo '<option value="'.$client_company->company_id.'">'.$client_company->company_name.'</option>';
      }
    }
  }


  public function joinery_selected_user(){
    $user_list = $this->admin_m->joinery_selected_user();
    echo '<table class = "table table-hover table-striped">';
    //echo '<option value="">'.$select_user.'</option>';
    foreach ($user_list->getResultArray() as $row){
      if($row['is_primary'] == 1){
        echo '<tr><td><input type = "radio" name = "joinery_user" checked onclick = "select_joinery_user('.$row['joinery_user_responsible_id'].')"></td><td>'.$row['user_first_name'].' '.$row['user_last_name'].'</td></tr>';
      }else{
        echo '<tr><td><input type = "radio" name = "joinery_user" onclick = "select_joinery_user('.$row['joinery_user_responsible_id'].')"></td><td>'.$row['user_first_name'].' '.$row['user_last_name'].'</td></tr>';
      }
    }
    echo '</table>';
  }

  public function get_employee_supply_rem($focus_company){
    $q_get_employee_supply_reminder = $this->admin_m->get_employee_supply_reminder($focus_company);
    $q_res_arr = $q_get_employee_supply_reminder->getResultArray();
    return array_shift($q_res_arr);
  }


  public function list_employee_per_archive($registry_type_id){

    $q_emp_archive = $this->admin_m->fetch_archive_assigned_emp($registry_type_id);

  //  $fetch_archive_emp = $this->admin_m->q_emp_archive();
    $archive_emp = $q_emp_archive->getResult();
    $emp_id = '';
    $archive_registry_id = '';
    $expiry = '';



    if($q_emp_archive->getNumRows() > 0){
      foreach($archive_emp as $key => $archive_data){
        $emp_id .= '_'.$archive_data->user_id;
        $archive_registry_id .= '_'.$archive_data->archive_registry_id;
        $expiry .= '_'.$archive_data->expiry;

        echo '<span class="badge btn-primary  " style="margin: 2px 5px 0px;"><i  class="fa fa-user"  style="margin: 0;"></i> &nbsp; '.$archive_data->user_first_name.' - '.$archive_data->expiry.'</span>';
      }

      $emp_id = substr($emp_id, 1);
      $archive_registry_id = substr($archive_registry_id, 1);
      $expiry = substr($expiry, 1);


      echo '<button class="btn btn-xs btn-info pull-right edit_archive_document_details_btn" data-toggle="modal" data-target="#archive_document_details" tabindex="-1" id="'.$emp_id.'-'.$registry_type_id.'-'.$archive_registry_id.'-'.$archive_data->registry_name.'-'.$archive_data->registry_types_id.'-'.$expiry.'" >Edit</button>';
    }else{

      echo '<span class="badge btn-warning  " style="margin: 2px 5px 0px;"><i  class="fa fa-exclamation-circle"  style="margin: 0;"></i> &nbsp; Un-assigned</span>';



    }
  }



}