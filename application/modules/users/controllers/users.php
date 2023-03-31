<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Users\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Users\Models\Users_m;


require 'PHPMailer/PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer/PHPMailer.php';
require 'PHPMailer/PHPMailer/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Dompdf\Dompdf;
require 'Dompdf/autoload.inc.php';

class Users extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();

    $this->company = new Company();
    $this->company_m = new Company_m();


    $this->user_model = new Users_m();

    $this->request = \Config\Services::request();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $data = array();

  
    if(!$this->_is_logged_in() ):
      $this->logout();
      return redirect()->to('/signin');
    endif;


    $this->_check_user_access('users',1);
 
  //  $this->calc_leave_points();

    $overall_total_users = $this->user_model->overall_total_users();
    $data['overall_total_users'] = $overall_total_users->getRow();
    
    $local_total_users = $this->user_model->local_total_users();
    $data['local_total_users'] = $local_total_users->getRow();
    
    $offshore_total_users = $this->user_model->offshore_total_users();
    $data['offshore_total_users'] = $offshore_total_users->getRow();

    $total_fsf_group_users = $this->user_model->total_fsf_group_users();
    $data['total_fsf_group_users'] = $total_fsf_group_users->getRow();

    $total_fsf_group_users_local = $this->user_model->total_fsf_group_users_local();
    $data['total_fsf_group_users_local'] = $total_fsf_group_users_local->getRow();

    $total_fsf_group_users_offshore = $this->user_model->total_fsf_group_users_offshore();
    $data['total_fsf_group_users_offshore'] = $total_fsf_group_users_offshore->getRow();
    
    $total_focus_wa_users = $this->user_model->total_focus_wa_users();
    $data['total_focus_wa_users'] = $total_focus_wa_users->getRow();

    $total_focus_wa_users_local = $this->user_model->total_focus_wa_users_local();
    $data['total_focus_wa_users_local'] = $total_focus_wa_users_local->getRow();

    $total_focus_wa_users_offshore = $this->user_model->total_focus_wa_users_offshore();
    $data['total_focus_wa_users_offshore'] = $total_focus_wa_users_offshore->getRow();
    
    $total_focus_nsw_users = $this->user_model->total_focus_nsw_users();
    $data['total_focus_nsw_users'] = $total_focus_nsw_users->getRow();

    $total_focus_nsw_users_local = $this->user_model->total_focus_nsw_users_local();
    $data['total_focus_nsw_users_local'] = $total_focus_nsw_users_local->getRow();

    $total_focus_nsw_users_offshore = $this->user_model->total_focus_nsw_users_offshore();
    $data['total_focus_nsw_users_offshore'] = $total_focus_nsw_users_offshore->getRow();   

    $total_focus_maintenance_users = $this->user_model->total_focus_maintenance_users();
    $data['total_focus_maintenance_users'] = $total_focus_maintenance_users->getRow();

    $total_focus_maintenance_users_local = $this->user_model->total_focus_maintenance_users_local();
    $data['total_focus_maintenance_users_local'] = $total_focus_maintenance_users_local->getRow();

    $total_focus_maintenance_users_offshore = $this->user_model->total_focus_maintenance_users_offshore();
    $data['total_focus_maintenance_users_offshore'] = $total_focus_maintenance_users_offshore->getRow();

    $fetch_user= $this->user_model->fetch_user();
    $data['users'] = $fetch_user->getResult();

    $data['screen'] = 'FSF Group Sojourn Users';

    $data['page_title'] = 'FSF Group Sojourn Users';
 


    $data['main_content'] = 'App\Modules\Users\Views\users_list';
    return view('App\Views\page',$data);
  }

  public function check_closing(){

    $static_defaults_q = $this->user_model->select_static_defaults();
    $static_defaults = array_shift($static_defaults_q->getResultArray());
    $switch_date = intval( $static_defaults['switch_date'] );
    $day_today = intval( date('d') );

    if($switch_date != $day_today){
    $this->user_model->closing_logout();
    $this->user_model->set_date_closing($day_today);
    }

  } /// check_closing() /// checks for closing time

  public function user_logs(){
    if($this->session->get('is_admin') != 1 ):   
      return redirect()->to('/users');
    endif;

    $order = 'ORDER BY `users`.`user_first_name` ASC';
    $data['users_q'] = $this->user_model->fetch_login_user($order);

    $user_logs = $this->user_model->fetch_user_logs();
    $data['user_logs'] = $user_logs;

    $data['screen'] = 'Users Logs';
    $data['page_title'] = 'Users Logs';
 
    $data['main_content'] = 'App\Modules\Users\Views\user_logs';
    return view('App\Views\page',$data);

  }



  public function check_closing_time(){
    $this->session = \Config\Services::session();

    if( $this->_is_logged_in() ): //if user is logged in

      $static_defaults_q = $this->user_model->select_static_defaults();
      $static_defaults = array_shift($static_defaults_q->getResultArray());

      $swtich_time = $static_defaults['swtich_time'];
      $swtich_msg = $static_defaults['swtich_msg'];
      $swtich_alert =   intval( $static_defaults['swtich_alert'] );
      $switch_marker = $static_defaults['switch_marker'];

      $swtich_msg = str_replace("'","&apos;",$swtich_msg);
      $swtich_msg = str_replace('"' ,"&quot;",$swtich_msg);

      $date_time_alert_b = date('Y-m-d').' '.$swtich_time;
      $date_time_stamp_b = strtotime($date_time_alert_b);
      $date_time_stamp_a = strtotime($date_time_alert_b. ' -'.$swtich_alert.' minutes');

      $time_now = now();
      $day_today = intval(date('d'));

      if($switch_marker != $day_today){

        if($date_time_stamp_a < $time_now && $time_now < $date_time_stamp_b && !$this->session->get('set_alert')){
          $this->session->set('set_alert', 1 ); 
          echo '<script type="text/javascript">alert("'.$swtich_msg.'");</script>';

        }

        if($date_time_stamp_a < $time_now && $time_now < $date_time_stamp_b && $this->session->get('set_alert')){
          // error !!! must occur only once
          $this->user_model->closing_logout();
          $this->session->destroy(); 
          $this->user_model->set_switch_marker($day_today);
        }

      }
    
    endif; //if user is logged in
  }


  public function get_user_access($user_id){
    $user_access_q = $this->user_model->fetch_all_access($user_id);
    $user_q_resuet_array = $user_access_q->getResultArray();
    $user_access_arr = array_shift($user_q_resuet_array);
    return implode(',', $user_access_arr);
  }


  function fetch_user_future_availability($user_id){
    $current_date_time = strtotime(date("Y-m-d h:i A"));
    $user_ave_q = $this->user_model->fetch_future_availability($user_id,$current_date_time);
    return $user_ave_q;
  }

  function fetch_user_future_reocc_ava($user_id){
    $user_ave_q = $this->user_model->get_upcomming_reoccuring_ave($user_id);
    return $user_ave_q;
  }


  public function loop_compamy_group($company_id = ''){
    $this->session = \Config\Services::session();

    $admin_company_details = $this->user_model->fetch_company_group($company_id,1);
    $q_company_details_result = $admin_company_details->getResultArray();
    $data = array_shift( $q_company_details_result );

    echo '<div class="col-lg-offset-4 col-lg-4 col-md-6 col-md-offset-3 col-xs-12 box-widget" >
    <div class="box wid-type-'.$data['admin_company_details_id'].'_comp_group" style="    border-radius: 22px !important;">
    <div class="widg-head box-widg-head pad-5">'.$data['state_name'].'<span class="sub-h pull-right"></span></div>              
    <div class=" pad-5 text-center m-bottom-10">';

    if($this->session->get('is_admin') ==  1){
      echo '<a href="'.base_url().'admin/admin_company/'.$data['admin_company_details_id'].'" class="" id=""><h3>'.$data['company_name'].'</h3></a>';
    }else{
      echo '<h3>'.$data['company_name'].'</h3>';
    }

    echo'</div>
    </div>
    </div>';

    echo '<div class="clearfix"></div>';
    echo '<style type="text/css">.wid-type-'.$data['admin_company_details_id'].'_comp_group .widg-head{background: #3EC4F7;color: #fff;}.wid-type-'.$data['admin_company_details_id'].'_comp_group{background: #00ADEF;}</style>';

    $admin_company_group_q = $this->user_model->fetch_company_group($company_id);

    if($admin_company_group_q->getNumRows()):
      $admin_company_group = $admin_company_group_q->getResult();
      foreach($admin_company_group as $key => $comp):

        echo '<div class=" col-lg-4 col-md-4 col-xs-12 box-widget" >
        <div class="box wid-type-'.$comp->admin_company_details_id.'_comp_group" style="    border-radius: 22px !important;">
        <div class="widg-head box-widg-head pad-5">'.$comp->state_name.'<span class="sub-h pull-right"></span></div>              
        <div class=" pad-5 text-center m-bottom-10">';

        if($this->session->get('is_admin') ==  1){
          echo '<a href="'.base_url().'admin/admin_company/'.$comp->admin_company_details_id.'" class="" id=""><h3>'.$comp->company_name.'</h3></a>';
        }else{
          echo '<h3>'.$comp->company_name.'</h3>';
        }

        echo'</div></div></div>'; 
      endforeach;
    endif;
  }

  //review_code
  public function add(){

    $this->validation = \Config\Services::validation();

    if(!$this->_is_logged_in() ):
      return redirect()->to('/signin');
    endif;


    $data = array();

    $data['form_error'] = null;


    $departments = $this->user_model->fetch_all_departments();
    $data['departments'] = $departments->getResult();

    $roles = $this->user_model->fetch_all_roles();
    $data['roles'] = $roles->getResult();

    $focus = $this->admin_m->fetch_all_company_focus();
    $data['focus'] = $focus->getResult();

    $fetch_user= $this->user_model->list_user_short();
    $data['users'] = $fetch_user->getResult();

    $project_manager = $this->user_model->fetch_user_by_role(3);
    $data['project_manager'] = $project_manager->getResult();
    
    $static_defaults = $this->user_model->select_static_defaults();
    $data['static_defaults'] = $static_defaults->getResult();



    $rules = [
      'first_name' =>             ['label' => 'First Name'              , 'rules' => ['required', 'trim']   ],
      'last_name' =>              ['label' => 'Last Name'               , 'rules' => ['required', 'trim']   ],
      'gender' =>                 ['label' => 'Gender'                  , 'rules' => ['required', 'trim']   ],
      'login_name' =>             ['label' => 'Login Name'              , 'rules' => ['required', 'trim']   ],
      'password' =>               ['label' => 'Password'                , 'rules' => ['required', 'trim']   ],
      'department' =>             ['label' => 'Department'              , 'rules' => ['required', 'trim']   ],
      'confirm_password' =>       ['label' => 'Confirm Password'        , 'rules' => ['required', 'trim']   ],
      'role' =>                   ['label' => 'Role'                    , 'rules' => ['required', 'trim']   ],
      'focus' =>                  ['label' => 'Focus'                   , 'rules' => ['required', 'trim']   ],
      'direct_landline' =>        ['label' => 'Direct Landline'         , 'rules' => ['required', 'trim']   ],
      'email' =>                  ['label' => 'Email'                   , 'rules' => ['required', 'trim', 'valid_email']   ],
      'skype_id' =>               ['label' => 'Skype ID'                , 'rules' => ['required', 'trim']   ],
      'super_visor' =>            ['label' => 'Direct Reports'          , 'rules' => ['required', 'trim']   ],
      'is_offshore' =>            ['label' => 'Offshore Employee'       , 'rules' => ['required', 'trim']   ],
      'is_dummy' =>               ['label' => 'Dummy Account'           , 'rules' => ['required', 'trim']   ],     
      'dob' =>                    ['label' => 'Date of Birth'           , 'rules' => ['trim']   ],
      'after_hours' =>            ['label' => 'After Hours'             , 'rules' => ['trim']   ],
      'pm_for_pa' =>              ['label' => 'Primary PM'              , 'rules' => ['permit_empty','trim']   ],
      'mobile_number' =>          ['label' => 'Mobile Number'           , 'rules' => ['trim']   ],
      'personal_mobile_number'=>  ['label' => 'Personal Mobile Number'  , 'rules' => ['trim']   ],
      'personal_email' =>         ['label' => 'Personal Email'          , 'rules' => ['trim']   ],
      'comments' =>               ['label' => 'Comments'                , 'rules' => ['trim']   ],
      'contractor_employee' =>    ['label' => 'Contractor Employee'     , 'rules' => ['required', 'trim']   ],
    ];



    if( isset($_POST['role'])  && $_POST['role'] == '2|Project Administrator'){
       $rules[ 'pm_for_pa' ] = ['label' => 'Primary PM' , 'rules' => ['required', 'trim']   ]; // individual rules set
    }else{
      $_POST['pm_for_pa'] = null;
    }



 


    if($_SERVER['REQUEST_METHOD'] === 'POST'):

      if($this->validate($rules)): // form is valid and process form
        //PROCESSUBG NOW!

        $data['validation'] = $this->validator;
        $data['form_error'] = null;


        $role_raw = $this->request->getPost('role');
        $role_arr = explode('|',$role_raw);
        $role_id = $role_arr[0];


        $file_upload_arr = array('');
        if(isset($_FILES['profile_photo'])){
          if($_FILES['profile_photo']['name'] != ''){
            $file_upload_raw = $this->_upload_primary_photo('profile_photo','users');
            $file_upload_arr = explode('|',$file_upload_raw);
          }
        }

        if($file_upload_arr[0] == 'error'):
          $profile_photo = '';
          $data['form_error'] = '<p class="">Invalid file upload for profile photo.</p>';
        endif;


        if($file_upload_arr[0] == 'success'){
          $profile_photo = $file_upload_arr[1];
        }else{
          $profile_photo = '';
        }



$site_select = $this->request->getPost('site_select');
$first_name = $this->cap_first_word($this->if_set($this->request->getPost('first_name')));
$last_name = $this->cap_first_word($this->if_set($this->request->getPost('last_name')));
$gender = $this->cap_first_word($this->if_set($this->request->getPost('gender')));
$dob = $this->if_set($this->request->getPost('dob'));
$login_name = $this->if_set($this->request->getPost('login_name'));
$confirm_password = $this->if_set($this->request->getPost('confirm_password'));

$supervisor_id = $this->if_set($this->request->getPost('super_visor'));
$is_offshore = $this->if_set($this->request->getPost('is_offshore'));

$password = $this->if_set($this->request->getPost('password'));
$password = md5($password);

$department_raw = $this->request->getPost('department');
$department_arr = explode('|',$department_raw);
$department_id = $department_arr[0];

$focus_raw = $this->request->getPost('focus');
$focus_arr = explode('|',$focus_raw);
$focus_id = $focus_arr[0];

$skype_id = $this->if_set($this->request->getPost('skype_id'));
$skype_password = $this->request->getPost('skype_password');

$direct_landline = $this->if_set($this->request->getPost('direct_landline'));
$after_hours = $this->if_set($this->request->getPost('after_hours'));
$mobile_number = $this->if_set($this->request->getPost('mobile_number'));
$personal_mobile_number = $this->if_set($this->request->getPost('personal_mobile_number'));
$email = $this->if_set($this->request->getPost('email'));
$personal_email = $this->if_set($this->request->getPost('personal_email'));

$days_exp = $this->if_set($this->request->getPost('days_exp'));
$comments = $this->cap_first_word_sentence($this->if_set($this->request->getPost('comments')));

if($comments){
  $user_notes_id = $this->company_m->insert_notes($comments);
}else{
  $user_notes_id = 0;
}

$contact_number_id = $this->company_m->insert_contact_number('','',$direct_landline,$mobile_number,$after_hours,$personal_mobile_number);
$email_id = $this->company_m->insert_email($email,$personal_email);

 
$dashboard_access = $this->request->getPost('dashboard_access');
$company_access = $this->request->getPost('company_access');
$projects_access = $this->request->getPost('projects_access');
$wip_access = $this->request->getPost('wip_access');
$purchase_orders_access = $this->request->getPost('purchase_orders_access');
$invoice_access = $this->request->getPost('invoice_access');
$users_access = $this->request->getPost('users_access');
$bulletin_board = $this->request->getPost('bulletin_board');
$project_schedule = $this->request->getPost('project_schedule');
$labour_schedule = $this->request->getPost('labour_schedule');
$leave_requests = $this->request->getPost('leave_requests');
$job_date_access = $this->request->getPost('job_date_access');
$progress_report_set = $this->request->getPost('progress_report_set');
$contractor_employee = $this->request->getPost('contractor_employee');
$is_dummy = $this->if_set($this->request->getPost('is_dummy'));


date_default_timezone_set("Australia/Perth");
$user_timestamp_registered = time();




if($this->session->get('is_admin') ==  1){
  $admin = (isset($_POST['chk_is_peon']) && $_POST['chk_is_peon'] == 1 ? 1 : 0);
}else{
  $admin = 0;
}   







      $add_new_user_id = $this->user_model->add_new_user($login_name,$password,$first_name,$last_name,$gender,$department_id,$profile_photo,$user_timestamp_registered,$role_id,$email_id,$skype_id,$skype_password,$contact_number_id,$focus_id,$dob,$user_notes_id,$admin,$site_select,$contractor_employee,$is_offshore,$is_dummy);

      $this->user_model->insert_user_access($add_new_user_id,$dashboard_access,$company_access,$projects_access,$wip_access,$purchase_orders_access,$invoice_access,$users_access,$bulletin_board,$project_schedule,$labour_schedule,$leave_requests,$job_date_access,$progress_report_set);
      $this->user_model->insert_user_password($confirm_password,$add_new_user_id);

      if($role_id == 2){
        $pm_id = $this->if_set( $this->request->getPost('pm_for_pa'));
        $this->admin_m->pm_pa_assignment($add_new_user_id, $pm_id, $pm_id);
      }

      $this->user_model->update_user_supervisor($add_new_user_id,$supervisor_id);

      $new_user_success = 'The user is now added.';
      $this->session->setFlashdata('new_user_success', $new_user_success);

      $send_to = $email;


//review_code
      return redirect()->to('/users');

/*
    $user_mail = new PHPMailer;
    $user_mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
    $user_mail->Port = 587;    

    $user_mail->setFrom('donot-reply-sojourn@focusshopfit.com.au', 'Sojourn - Accounts');
    $user_mail->addAddress($send_to);    
    $user_mail->addReplyTo('donot-reply-sojourn@focusshopfit.com.au', 'Sojourn - Accounts');
    $user_mail->isHTML(true);
    $year = date('Y');
    $user_mail->Subject = 'Account Details';
    $user_mail->Body    = 'Do not reply in this email.<br /><br />Welcome '.$first_name.' to Sojourn, an FSF Group Project Management Application.<br /><br />You have been added as a new user and provided with a temporary password.  Please sign-in right away where you will be required to change your password, then you will need to sign in again using your username & changed password.  Use the link below.<br /><br /><a href="'.base_url().'" target="_blank">'.base_url().'</a><br /><br />Your User Name is : '.$login_name.' and Password is : '.$confirm_password.'<br /><br />If you go to the USER section of the site you can personalise your settings and complete your set up.<br /><br />&copy; FSF Group '.$year;

    if(!$user_mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $user_mail->ErrorInfo;
    } else {
      return redirect()->to('/users');
    }
*/
//review_code




     



      else: // has errors here

        $data['validation'] = $this->validator;
        $data['form_error'] = '<p class="">Fields have errors, kindly check and comply.</p>';

      endif;
    endif;



    




    $data['main_content'] = 'new_user';
    $data['screen'] = 'New User';
    $data['main_content'] = 'App\Modules\Users\Views\new_user';
    return view('App\Views\page',$data);

  }
  //review_code

  //review_code
  public function availability(){

    if(!$this->_is_logged_in() ):
      return redirect()->to('/signin');
    endif;

    $this->_check_user_access('users',1); //review_code

    $fetch_user= $this->user_model->list_user_short();
    $data['users'] = $fetch_user->getResult();

    $data['main_content'] = 'App\Modules\Users\Views\availability';
    $data['screen'] = 'User Availability';
    $data['page_title'] = 'User Availability';

    return view('App\Views\page',$data);
  }
  //review_code


  function if_user_is_available($user_id){
    $current_date_time = strtotime(date("Y-m-d h:i A"));
    $user_ave_q = $this->user_model->get_user_availability($user_id,$current_date_time);
    if($user_ave_q->getNumRows() === 1){
      return false;
    }else{
      return true;
    }
  }

  public function get_user_reoccur_ave($user_id){
    $time_extended = date("Hi");
    $day_like = strtolower(date("D") );
    $current_date_time =  strtotime(date("Y-m-d h:i A"));
    $current_timestamp = strtotime(date("Y-m-d"));  

    $reoccur_q = $this->user_model->get_reoccur_ave_year_month($current_date_time,$time_extended,$user_id);
      if($reoccur_q->getNumRows() === 1){
        $q_recur_res_arr = $reoccur_q->getResultArray();
        $reoccur_ave = array_shift($q_recur_res_arr);

      }else{
        $current_date_time = strtotime(date("Y-m-d h:i A"));
        $user_ave_roc_q = $this->user_model->get_reoccur_availability($current_date_time, $time_extended, $day_like,$user_id);
        $ouser_ave_q_get_rest_arr = $user_ave_roc_q->getResultArray();
        $reoccur_ave = array_shift($ouser_ave_q_get_rest_arr);

      }

    return $reoccur_ave;
  }

  public function fetch_user_ave_data($user_id){
    $current_date_time = strtotime(date("Y-m-d h:i A"));
    $user_ave_q = $this->user_model->get_user_availability($user_id,$current_date_time);
    $get_result_data = $user_ave_q->getResultArray();
    $user_ave = array_shift($get_result_data);

    if($user_ave_q->getNumRows() === 1){
      return $user_ave;
    }else{
      return $this->get_user_reoccur_ave($user_id);
    }
  }


  public function showAvailableLeave($user_id){

    $total_annual = 0;
    $total_personal = 0;

    $q_user_leave = $this->user_model->get_available_user_leave_credits($user_id);

    if($q_user_leave->getNumRows() == 1){
      $res_arr_q_userleave = $q_user_leave->getResultArray();
      $leave_credits = array_shift($res_arr_q_userleave);
      $total_annual = $leave_credits['total_annual'];
      $total_personal = $leave_credits['total_personal'];
    }


    if($this->session->get('is_admin') != 1 &&  $this->session->get('user_id') == $user_id){
      echo '<span class="fa fa-info-circle fa-lg pull-right tooltip-enabled" title="" data-html="true" data-placement="left" data-original-title="Annual Leave: '.$total_annual.'<br />Personal Leave: '.$total_personal.'" style="padding: 5px 10px;"></span>';
    }

    if($this->session->get('is_admin') == 1){
      echo '<span class="fa fa-info-circle fa-lg pull-right tooltip-enabled" title="" data-html="true" data-placement="left" data-original-title="Annual Leave: '.$total_annual.'<br />Personal Leave: '.$total_personal.'" style="padding: 5px 10px;"></span>';
    }

  }



  public function leave_type(){
    $leave_type = $this->user_model->fetch_leave_type();
    $leave_type = $leave_type->getResult();
    return $leave_type;
  }

  public function for_approval_count(){
    $this->session = \Config\Services::session();

    $user_id = $this->session->get('user_id');
    $fetch_pending_by_superv = $this->user_model->fetch_pending_leaves_by_supervisor_id($user_id);
    return $fetch_pending_by_superv->getNumRows();
  }

  public function leave_alloc($user_id_page){
    $leave_alloc = $this->user_model->fetch_leave_alloc($user_id_page);
    $leave_alloc = $leave_alloc->getRow();

    return $leave_alloc;
  }

  public function get_sched($user_id_page){
    $get_sched_of_work = $this->user_model->get_sched($user_id_page);
    $get_sched_of_work = $get_sched_of_work->getRow();

    return $get_sched_of_work;
  }

  public function leave_remaining($user_id){

    if ($user_id == ''){
      $user_id = $this->session->get('user_id'); 
    }
    
    $leave_remaining = $this->user_model->fetch_leave_alloc($user_id, date('Y'));
    $leave_remaining = $leave_remaining->getRow();
    
    return $leave_remaining;
  }

  public function user_state($user_id){
    $user_state = $this->user_model->fetch_user_state($user_id);
    $user_state = $user_state->getRow();
    return $user_state;
  }




  public function company_matrix(){

    if(!$this->_is_logged_in() ):
      return redirect()->to('/signin');
    endif;

    $this->_check_user_access('users',1); //review_code

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $data['all_focus_company'] = $all_focus_company->getResult();

    $data['main_content'] = 'matrix';
    $data['screen'] = 'FSF Group Sojourn';
    $data['page_title'] = 'Organizational Chart';


    $data['main_content'] = 'App\Modules\Users\Views\matrix';
    return view('App\Views\page',$data);


  }



  function _check_user_access($area,$access){
    if($this->session->get($area) >= $access ){

    }else{
      //review_code
      $this->logout(); 
      return redirect()->to('/signin'); // temporary // may change to projects or users list
      //review_code
    }

  }




  function _is_logged_in(){

    $this->session = \Config\Services::session();
    if($this->session->get('user_id')){
      return true;     
    }else{
      return false;
    }
  } // _is_logged_in()

    public function signin($map=''){
      // this is the default controller checks if logged in or not
      $request = \Config\Services::request();


      // $this->check_closing(); //review_code

      if($map==1){
        return redirect()->to('maps');
      }


      if($this->session->get('user_id') ){
        return redirect()->to('dashboard');
      }

      $data = array();
      $signin_error = null;



      $has_archvie_access = 0;
      $archive_exp = '';

      $data['page_title'] = 'Sign In';

      //review_code
            /*
          $bg_photo = $this->display_login_bg();
          $data['bg_file'] = $bg_photo;  
      */
      //review_code

      $userid = $this->session->get('user_id');


      $mail = new PHPMailer;                                    
      $mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
      $mail->Port = 587;

      $mail->setFrom('noreply@focusshopfit.com.au', 'Sojourn Reminder - PO Review');
      $mail->addReplyTo('noreply@focusshopfit.com.au', 'Do Not Reply');
      $mail->isHTML(true);  

      //review_code
      //Redirect
      if($this->_is_logged_in()){
        $user_role_id = $this->session->get('user_role_id');

        if($this->session->get('dashboard') >= 1 ){  
          redirect('dashboard');
        }elseif($user_role_id == 21){
          redirect('users'); 
          //dashboard
        }else{
          redirect('projects');
          //dashboard
        }
      }
      //Redirect
      //review_code


//review_code

/*
      $static_defaults_q = $this->user_model->select_static_defaults();
      $static_defaults = array_shift($static_defaults_q->result_array());
*/

//review_code





        $data['bg_file'] = base_url().'/img/sojourn_bg_signin.png';
        $data['main_content'] = 'App\Modules\Users\Views\signin';

        $rules = [
          'user_name' => ['label' => 'User name', 'rules' => ['required', 'trim']   ],
          'password' => ['label' => '', 'rules' => ['required', 'trim', 'min_length[8]']   ]
        ];


        if($_SERVER['REQUEST_METHOD'] === 'POST'): // if login form is submitted, processs here


          if($this->validate($rules)): // checks validation user login // login field is complete
            // $request->getPost('user_name') // getting individual post in Ci4
            // validation process continues here

         

            $user_name = $_POST['user_name'];
            $password = $_POST['password'];
            $remember = $_POST['remember'] ?? null;

            $data['remember'] = $remember ?? 0;

            $ip_add = $_SERVER['REMOTE_ADDR'];
            $data['ip_add'] = $ip_add;

            $userdata = $this->user_model->validate($user_name, md5($password), $ip_add);

            switch($userdata):

              case "0":
                $signin_error = '<p class="">Invalid sign-in credentials.</p>';
                $data['validation'] = $this->validator;
                $data['main_content'] = 'App\Modules\Users\Views\signin';
                
              break;

              case "1";
                $userdata_session = $this->user_model->get_user_id($user_name, md5($password), $ip_add);
                $data['user_id'] = $userdata_session->user_id;
                $this->session->set($data);
                $data['validation'] = $this->validator;

                date_default_timezone_set("Australia/Perth"); 
                $time_stamp = time(); 
                $this->user_model->set_user_logged_time($userdata_session->user_id,$time_stamp);

                $signin_error = "<button type = 'button' class = 'btn btn-success pull-right' id = 'log_out_prev_user'>Yes</button><p>User is already logged in on another pc. <br />Do you want to log-off Previous logged-in Account?</p>";
                $data['main_content'] = 'App\Modules\Users\Views\signin';

               
              break;
              default:
              // login successful // redirect to dashboard or projects

              $signin_error = null;
              $data['validation'] = null;

              $data = array();

              echo "// login successful // redirect to dashboard or projects<br /><br />";





      

              // login successful // redirect to dashboard or projects



//review_code
// from original code starts

/*
        echo '<div class="" style="background: url(\'https://sojourn.focusshopfit.com.au/uploads/login_bg/loading_sojourn_balls.gif\');height: 400px;width: 700px;margin: 150px auto 0;background-position: center center;">
        <img src="https://sojourn.focusshopfit.com.au/uploads/login_bg/focus_logo_login_screen.jpg" style="text-align: center; margin: 0 auto; width: 493px; display: block; height: 165px;">
        <p id="loading_text" class="" style="font-family: sans-serif;font-size: 18px; margin-top: 70px; text-align: center;">Sojourn is loading your dashboard.<br> Please wait.</p></div>';


          $this->_confirm_active_password($userdata->user_id,$password);


*/


// from original code ends
//review_code




              $time_stamp=time();
              $this->user_model->set_user_logged_time($userdata->user_id,$time_stamp);

              $raw_user_access = $this->user_model->fetch_all_access($userdata->user_id);
              $user_access_arr = $raw_user_access->getResultArray();
              $user_access = array_shift($user_access_arr);
              $this->session->set($user_access);

              $data['user_id'] = $userdata->user_id;
              $data['user_role_id'] = $userdata->user_role_id;
              //$data['user_access_group_id'] = $userdata->user_access_group_id;
              $data['user_first_name'] = $userdata->user_first_name;
              $data['user_last_name'] = $userdata->user_last_name;
              $data['user_profile_photo'] = $userdata->user_profile_photo;
              $data['user_department_id'] = $userdata->user_department_id;
              $data['user_focus_company_id'] = $userdata->user_focus_company_id;
              $data['set_view_company_project'] = $userdata->company_project_view;

              $data['is_active'] = $userdata->is_active;
              $data['logged_in'] = true;
              $data['logged_in_user'] = 1;
              $data['is_admin'] = $userdata->if_admin;
              $data['user_name'] = $user_name;
              $data['password'] = $password;
              $data['role_types'] = $userdata->role_types;
              $data['user_site'] = $userdata->site_access;
              $data['user_supervisor_id'] = $userdata->supervisor_id;



             $this->session->set($data);


          //   echo '<pre>';var_dump( $this->session->get() );echo '</pre>'; 






              // add redirect here

            //  return redirect()->to('/users/account/'.$userdata->user_id);
              return redirect()->to('/dashboard/');

             
            endswitch;
          
     
        

         // sets return for else

        // has errors??.

          else:



            // returns to login screen with errorsx
            $signin_error = '<p class="">Invalid sign-in credentials.</p>';
            $data['validation'] = $this->validator;
            // returns to login screen with errors



          
          endif; // checks validation user login


          $data['signin_error'] = $signin_error;
          $data['main_content'] = 'App\Modules\Users\Views\signin';
          return view('App\Views\page',$data);





        else: // if login form is submitted, processs here else

          // loads the regular login form
          $data['signin_error'] = $signin_error;
          $data['main_content'] = 'App\Modules\Users\Views\signin';
          return view('App\Views\page',$data);
          // loads the regular login form

        endif; // if login form is submitted, processs here closing



      


    }


  public function get_user_nav_order($user_id){

      if(!$user_id){
        return redirect()->to('/signin');
      }


      $q_nav_menu = $this->user_model->get_nav_order($user_id);
      $q_result = $q_nav_menu->getResult(); 
      $nav_order = array_shift( $q_result );
//      echo '<pre>';var_dump( $nav_order );echo '</pre>'; exit; 
     return $nav_order->left_nav_order;
  }



  public function fetch_user($user_id){
    $fetch_user = $this->user_model->fetch_user($user_id);
    $fetch_user = $fetch_user->getResult();
    return $fetch_user;
  }


  function reset_reoccur_avaialbility(){

    $person_did = $this->session->get('user_id');
    $fetch_user_loc = $this->admin_m->fetch_user_location($person_did);
    $q_result_fetch_user = $fetch_user_loc->getResultArray();
    $user_location = array_shift($q_result_fetch_user);

    $user_state_raw = explode(',', $user_location['location']);
    $user_site = trim($user_state_raw[1]);
    switch ($user_site) {
      case "NSW": 
      date_default_timezone_set('Australia/Sydney');
      break;

      case "QLD":
      date_default_timezone_set('Australia/Perth');
      break;

      case "WA":
      date_default_timezone_set('Australia/Perth');
      break;
    }


    $current_timestamp = strtotime(date("Y-m-d"));
    //$current_timestamp = strtotime("2016-12-05");
    $reoccur_q = $this->user_model->list_active_reoccur_availability($current_timestamp);

    foreach ($reoccur_q->getResult() as $reoccur){

      switch ($reoccur->pattern_type) {
        case "weekly": 
        $date_future = strtotime(date("Y-m-d").' + '.$reoccur->limits.' week');
        break;

        case "monthly":
        $date_future = strtotime(date("Y-m").'-'.$reoccur->range_reoccur.' + '.$reoccur->limits.' month');
        break;

        case "yearly":
        $date_future = strtotime(date("Y").'-'.$reoccur->limits.'-'.$reoccur->range_reoccur.' + 1 year');
        break;
      }

      $this->user_model->update_future_reoccur_present_date($current_timestamp,$date_future,$reoccur->reoccur_id);
    }
  }




  public function get_return_date($user_id){   // return text availability

    if(!$user_id){
      return;
    }

    $nowTimeStamp = strtotime('now');
    $returnMsg = '';

    if($user_id == 27){
      return $returnMsg;
    }


    $db = \Config\Database::connect();
    $this->db = $db;

    $sql_a = "SELECT from_unixtime(`user_availability`.`date_time_stamp_a`, '%H%i')  AS `startDayHrs`,  from_unixtime(`user_availability`.`date_time_stamp_b`, '%H%i')  AS `endDayHrs`,  from_unixtime(`user_availability`.`date_time_stamp_b`, '%j')  AS `endDayNum`
     FROM `user_availability`  WHERE `user_availability`.`user_id` = '$user_id' AND `user_availability`.`date_time_stamp_b` >= '$nowTimeStamp'  AND `user_availability`.`is_active` = '1'  ORDER BY `endDayNum`  DESC LIMIT 1 ";

    $my_result_a = $this->db->query($sql_a);
    $result_a_res_arr = $my_result_a->getResult();
    $data = array_shift($result_a_res_arr);
 

    $current_day_number = date("z")+1;

    if(!isset($data->endDayNum) ){
      $endDayNum = 365;
    }else{
      $endDayNum = $data->endDayNum;
    }

    unset($data);
 
    $table_arr_cell = array();

    $dateYear = 0;


   
    $day_number = 0;
    $sql_b = "SELECT *, 
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_a`, '%H%i') AS `startTime`, 
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_b`, '%H%i') AS `endTime`, 
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_b`, '%j') AS `dDay`, 
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_b`, '%Y') AS `dYear`,
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_a`, '%d/%m/%Y %h:%i %p') AS `dateTimeStart`,
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_b`, '%d/%m/%Y %h:%i %p') AS `dateTimeEnd`,
    FROM_UNIXTIME(`user_availability`.`date_time_stamp_b`, '%W') AS `nameDay`
    FROM `user_availability` WHERE `user_availability`.`user_id` = '$user_id' AND `user_availability`.`reoccur_id` = '0'
    AND ( `user_availability`.`date_time_stamp_b` >= '$nowTimeStamp' OR `user_availability`.`date_time_stamp_a` >= '$nowTimeStamp' ) 
    AND `user_availability`.`is_active` = '1'   ORDER BY `user_availability`.`date_time_stamp_a` ASC ";

    $my_result = $this->db->query($sql_b);
    
// regular availability
    foreach ($my_result->getResult() as $data){

      $data->dDay = ABS($data->dDay);

      if($data->nameDay == 'Saturday' || $data->nameDay == 'Sunday'){
        $isWeekend = 1;
      }else{
        $isWeekend = 0;
      }


      if($data->endTime >= 1700){
        $is_free = 0;
      }else{
        $is_free = 1;
      }


      if(isset($table_arr_cell[$data->dDay])){
        

        $time_end_diff = $data->startTime - $table_arr_cell[$data->dDay]['endTime'];
        if( $time_end_diff >= 0 ){
          
          $returnMsg = 'Return by: '.$table_arr_cell[$data->dDay]['nameDay'].' '.$table_arr_cell[$data->dDay]['dateTimeEnd'].'';
          break;
          // gets return text here...
          // same day return date
        }else{
          $table_arr_cell[$data->dDay]['endTime'] = $data->endTime;
          $table_arr_cell[$data->dDay]['dateTimeEnd'] = $data->dateTimeEnd; 
          $table_arr_cell[$data->dDay]['dateTimeEnd'] = $is_free; 
        }
      }else{

        $table_arr_cell[$data->dDay] = array(
          "dDay"      => ABS($data->dDay),  
          "nameDay"     => $data->nameDay,  
          "dateTimeStart" => $data->dateTimeStart,  
          "dateTimeEnd" => $data->dateTimeEnd,  
          "startTime"   => $data->startTime,  
          "endTime"   => $data->endTime,  
          "dYear"     => $data->dYear,
          "isWeekEnd"   => $isWeekend,
          "is_free"     => $is_free
        );
      }
      $dateYear = $data->dYear;
    }
// regular availability
    unset($data);
 



    $sql_c = "SELECT *,
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_a`, '%H%i') AS `startTime`, 
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_b`, '%H%i') AS `endTime`, 
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_b`, '%j') AS `dDay`, 
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_b`, '%Y') AS `dYear`,
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_a`, '%d/%m/%Y %h:%i %p') AS `dateTimeStart`,
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_b`, '%d/%m/%Y %h:%i %p') AS `dateTimeEnd`,
    FROM_UNIXTIME(`user_reoccur_availability`.`date_range_b`, '%W') AS `nameDay`

    FROM `user_reoccur_availability` LEFT JOIN `user_availability` ON `user_availability`.`reoccur_id` = `user_reoccur_availability`.`reoccur_id` 
    WHERE `user_reoccur_availability`.`is_active` = '1' AND `user_reoccur_availability`.`date_range_b` >= '$nowTimeStamp'
    AND `user_availability`.`user_id` = '$user_id' AND `user_availability`.`is_active` = '1' ORDER BY `user_reoccur_availability`.`date_range_a` ASC";

    $my_result = $this->db->query($sql_c);

    // set re-occur logs on the calendar
    foreach ($my_result->getResult() as $data):

      $days_range = array();
      $days_range = explode(',', strtoupper( $data->range_reoccur) );


      $dateYear = $data->dYear;
      $endDayNum = $data->dDay;

      for ($i=$current_day_number; $i <= $endDayNum; $i++) {

        $i = ABS($i);

        $year = $dateYear;
        $dayOfYear = $i-1;

        $dayDate =  date('l', strtotime('January 1st '.$year.' +'.$dayOfYear.' days'));
        $dayCalendar =  date('d/m/Y', strtotime('January 1st '.$year.' +'.$dayOfYear.' days'));
        $dayNameInit = date('D', strtotime('January 1st '.$year.' +'.$dayOfYear.' days'));


        

        if($dayDate == 'Saturday' || $dayDate == 'Sunday'){
          $isWeekend = 1;
          $isFree = 0;
        }else{
          $isWeekend = 0;

          if( isset($data->dateTimeEnd) && $data->dateTimeEnd != '' ){
            $isFree = 1;

          }else{
 
            if( intval($data->endTime) >= 1700 &&  intval($data->endTime) > 0   ){
              $isFree = 0;
            }else{
              $isFree = 1;
            }
          }
          

        }

        $dayNameInit = strtoupper($dayNameInit);


        if( !isset($table_arr_cell[$i] ) ){

          if( $data->range_reoccur != '' &&  in_array( $dayNameInit , $days_range)    ){ // Sets day as un available

            $table_arr_cell[$i] = array(
              "dDay"      => ABS($i),  
              "nameDay"     => $dayDate,  
              "dateTimeStart" => $dayCalendar,  
              "dateTimeEnd" => $data->dateTimeEnd,
              "startTime"   => $data->startTime,  
              "endTime"   => $data->endTime,  
              "dYear"     => $data->dYear,
              "isWeekEnd"   => $isWeekend,
              "is_free"     => $isFree
            );

          //  echo '<p class=""><strong class="">'.$isWeekend.'</strong></p>'; 


          }else{


      //  echo '<p class="">++++  </p>'; 

            $table_arr_cell[$i] = array(
              "dDay"      => ABS($i),  
              "nameDay"     => $dayDate,  
              "dateTimeStart" => $dayCalendar,  
              "dateTimeEnd" => '',
              "startTime"   => '',
              "endTime"   => '0',
              "dYear"     => $dateYear,
              "isWeekEnd"   => $isWeekend,
              "is_free"     => $isFree
            );


          //  echo '<p class=""><strong class="">'.$isWeekend.'</strong></p>'; 

          }

        }

      }

    endforeach;
    // set re-occur logs on the calendar


    unset($data);
    $dateYear = date('Y');


    for ($i=$current_day_number; $i <= $endDayNum+5; $i++):
      if(!isset($table_arr_cell[$i])){

        $i = ABS($i);

        $year = $dateYear;
        $dayOfYear = $i-1;

        $dayDate =  date('l', strtotime('January 1st '.$year.' +'.$dayOfYear.' days'));
        $dayCalendar =  date('d/m/Y', strtotime('January 1st '.$year.' +'.$dayOfYear.' days'));

      //  echo '<p id="" class="">888 '.$dayDate.'*</p>';


        if($dayDate == 'Saturday' || $dayDate == 'Sunday'){
          $isWeekend = 1;
          $isFree = 0;
        }else{
          $isWeekend = 0;
          $isFree = 1;
        }

        $table_arr_cell[$i] = array(
          "dDay"      => ABS($i),  
          "nameDay"     => $dayDate,  
          "dateTimeStart" => $dayCalendar,  
          "dateTimeEnd" => '',
          "startTime"   => '',
          "endTime"   => '0',
          "dYear"     => $year,
          "isWeekEnd"   => $isWeekend,
          "is_free"     => $isFree
        );
      }
    endfor;


    ksort($table_arr_cell);

    foreach ($table_arr_cell as $key => $value):

      if(  $table_arr_cell[$key]['is_free'] == 1 &&  $table_arr_cell[$key]['isWeekEnd'] == 0   && $table_arr_cell[$key]['endTime'] == 0 ){ // get next

        $returnMsg = 'Return by: '.$table_arr_cell[$key]['nameDay'].' '.$table_arr_cell[$key]['dateTimeStart'].' 8:00 AM'.''; // steve here error
        break;

      }elseif( $table_arr_cell[$key]['is_free'] == 1 && $table_arr_cell[$key]['endTime'] < 1700 && $table_arr_cell[$key]['endTime'] > 0 && $table_arr_cell[$key]['isWeekEnd'] == 0  ){ // within the day
        $returnMsg = 'Return by: '.$table_arr_cell[$key]['nameDay'].' '.$table_arr_cell[$key]['dateTimeEnd'].'';
        //echo $returnMsg;
        break;
      }



    endforeach;

    return $returnMsg;

  }

  public function get_user_ave_comments($user_id,$return_text=''){


    $fetch_user_loc = $this->admin_m->fetch_user_location($user_id);
    $get_rest_user_loc = $fetch_user_loc->getResultArray();
    $user_location = array_shift($get_rest_user_loc);


  //  echo '<pre>';var_dump($user_location['state_code'] );echo '</pre>'; exit;

    $currentTimeQld = (new \CodeIgniter\I18n\Time("now", $user_location['state_code'] ));
   // print $datetime->format('Y-m-d H:m:s');

   // exit;
 
  //  $currentTimeQld = new Time('now', $user_location['state_code'] );
 
  // echo $time->humanize(); // 1 year ago
  //  echo $time->getTimestamp();   // 1471018523 - UNIX timestamp


 //   $currentTimeQld = new DateTime(null, new DateTimeZone( $user_location['state_code'] ));

    $current_date_time = $currentTimeQld->getTimestamp();// strtotime(date("Y-m-d h:i A"));
    $this->reset_reoccur_avaialbility();


    $aus_timezone = ''; 

    $user_ave_q = $this->user_model->get_user_availability($user_id,$current_date_time);
    $res_arr_get_user_ave = $user_ave_q->getResultArray();
    $user_ave = array_shift($res_arr_get_user_ave);

    $emp_set_userId = $user_ave['user_id'] ?? null;
    $return_mgs = $this->get_return_date($emp_set_userId);


    if($user_ave_q->getNumRows() === 1){

      $state_code_loc = $user_location['state_code'];
      $new_set_end_date = date('l jS \of F Y h:ia',$user_ave['date_time_stamp_b'] );

      if($return_text != ''){
        return $user_ave['notes'].' '.$return_mgs;
      }else{

        if( strlen($user_ave['notes']) > 0 ){
          echo '<span style="color:#1F3A4D;" class=" tooltip-enabled" data-placement="left" title="" data-original-title="'.$user_ave['notes'].' '.$return_mgs.'"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
        }else{
          if($user_ave['status']!= ''){
            echo '<span style="color:#1F3A4D;" class=" tooltip-enabled" data-placement="left" title="" data-original-title="'.$return_mgs.'"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
          }
        }
      }

    }else{


      $reoccur_ave = $this->get_user_reoccur_ave($user_id);
      $hr = substr($reoccur_ave['end_time'] ,0,2);

      if($hr > 12){
        $end_time = $hr-12;
        $min = substr($reoccur_ave['end_time'] ,-2).'pm';
      }else{
        $end_time = $hr;
        $min = substr($reoccur_ave['end_time'] ,-2).'am';
      }


      $dis_time = $end_time.':'.$min;

      if( $reoccur_ave['is_no_end'] == 1){
        $date_end = date("h:ia",$reoccur_ave['end_time']);//.' '.$reoccur_ave['user_availability_id'];
      }else{
        $date_end = date("l jS \of F h:ia",$reoccur_ave['date_range_b']);//.' '.$reoccur_ave['user_availability_id'];
      }

      $emp_set_userId = $reoccur_ave['user_id'];
      $return_mgs = $this->get_return_date($emp_set_userId);

      if($return_text != ''){

        $return_text_msg = ( $user_ave['notes'] ?? $reoccur_ave['notes'] ).' '.$return_mgs;
        return $return_text_msg;

      }else{

        $user_ave_notes = $user_ave['notes'] ?? null;
        if( $user_ave_notes && isset($user_ave_notes) &&  strlen($user_ave_notes) > 0){
          echo '<span style="color:#1F3A4D;" class=" tooltip-enabled" data-placement="left" title="" data-original-title="'.( strlen($user_ave['notes']) > 0 ? $user_ave['notes'] : $reoccur_ave['status'] ).' '.$return_mgs.'"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
        }else{
          echo '<span style="color:#1F3A4D;" class=" tooltip-enabled" data-placement="left" title="" data-original-title="'.( strlen($reoccur_ave['notes']) > 0 ? $reoccur_ave['notes'] : $reoccur_ave['status'] ).' '.$return_mgs.'"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
        }

      }
    }
  }

  function delete_user_ava(){
    $this->clear_apost();
    $ava_id = $_POST['ajax_var'];
    $this->user_model->delete_ava($ava_id);
  }

  function delete_user_ava_rec(){
    $this->clear_apost();
    $ava_id = $_POST['ajax_var'];
    $this->user_model->delete_ava_rec($ava_id);
  }



  public function approve_leave($supervisor_id){
    
    $this->clear_apost();
    $current_date = strtotime("now");
    
    $ajax_var = $_POST['ajax_var'];// ?? '1753|Leave request is approved.|2';
    $approved_data = explode('|', $ajax_var);

    $leave_req_id = $approved_data[0];
    $action_comments = $approved_data[1];
    $leave_user_id = $approved_data[2];

    $fetch_user = $this->user_model->fetch_user($supervisor_id);
    $data['user'] = $fetch_user->getResult();

    $user_supervisor_id = $data['user'][0]->supervisor_id;


    if ($supervisor_id != '3'){
      $result = $this->user_model->approved_by_supervisor($leave_req_id, '3');

      if ($result == 1) {
        $this->user_model->insert_leave_action($leave_req_id, $supervisor_id, '1', $current_date, $action_comments);
        // redirect(base_url().'users/leave_approvals/'.$supervisor_id);

        echo 'You successfully approved the leave request!';
      } else {
        echo 'The leave request is already approved!';
      }
    } else {
      $result_gm = $this->user_model->approved_by_gm($leave_req_id, '1', $supervisor_id);

      if ($result_gm == 1) {
      
        $this->user_model->insert_leave_action($leave_req_id, $supervisor_id, '1', $current_date, $action_comments);


        $fetch_leave_defaults = $this->user_model->fetch_leave_defaults();
        $data['leave_defaults'] = $fetch_leave_defaults->getResult();
        $this->set_availalbility_on_leave($leave_req_id);

        $this->generate_leave_form($leave_req_id, $leave_user_id);

//review_code   
        // PHPMailer class
        $user_mail = new PHPMailer;
        $user_mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
        $user_mail->Port = 587;
        $user_mail->SMTPDebug = 2;
        // PHPMailer class 

        $user_mail->setFrom('ian@focusshopfit.com.au', 'Ian Gamble');
        $user_mail->addAddress($data['leave_defaults'][0]->recipient_email);  // Add a recipient
        //$user_mail->addAddress($user_email);                                // Name is optional

        $user_mail->addReplyTo('ian@focusshopfit.com.au', 'Ian Gamble');
        
        $cc_emails = array();
        $cc_emails = explode(",", $data['leave_defaults'][0]->cc_email);
        $cc_emails_count = count($cc_emails);

        for ($i=0; $i <= $cc_emails_count; $i++) { 
          $user_mail->addCC($cc_emails[$i]);  
        }

        $bcc_emails = array();
        $bcc_emails = explode(",", $data['leave_defaults'][0]->bcc_email);
        $bcc_emails_count = count($bcc_emails);

        for ($i=0; $i <= $bcc_emails_count; $i++) { 
          $user_mail->addBCC($bcc_emails[$i]);
        }

        $user_mail->addBCC('jervy@focusshopfit.com.au');


        $path_file = './docs/leave_form/leave_form_'.$leave_req_id.'.pdf';
        $user_mail->addAttachment($path_file);                // Add attachments
        //$user_mail->addAttachment('/tmp/image.jpg', 'new.jpg');     // Optional name
        


        $user_mail->isHTML(true);                                   // Set email format to HTML

        $body_content = $data['leave_defaults'][0]->message;

    #    if ($action_comments != ''):
    #      $body_content .= '<br><br>Notes:<br>'.$action_comments;
     #   endif;

        $body_content .= '<p style="font-family: "Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif !important"><br><br><br><br>Regards,<br><br><strong>'.$this->session->get('user_first_name').' '.$this->session->get('user_last_name').'</strong><br><br>'.$this->session->get('role_types').'<br>0413 053 500<br><span style="color: "#FF3399 !important";>ian@focusshopfit.com.au</span></p>';

        // for live'
        $body_content .= '<br><img src="'.base_url().'img/signatures/FSFGroup.png" />';

        // for local
        // $body_content .= '<br><img src="https://sojourn.focusshopfit.com.au/img/signatures/FSFGroup.png" />';

        $user_mail->Subject = 'RE: Application of Leave';
        $user_mail->Body    = '<span style="font-family: "Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif">'.$body_content."</span>";

//review_code        
        $user_mail->send();
/*
        if(!$user_mail->send()) {
          // echo 'Message could not be sent.';
          // echo 'Mailer Error: ' . $user_mail->ErrorInfo;
        } else {
          // echo 'Message could not be sent.';
        }
*/
//review_code



      } else {
        echo '0';
      }

    }
  }

//1759|Leave request is approved.|2




  function set_availability_reoccur(){
    $this->clear_apost();

    //11/10/2022 07:00 AM`11/10/2022 05:00 PM`55555555`2`/focus/users/account/2`Out of Office
    //07:00 AM`05:00 PM`daily``tue,wed,thu`11/10/2022`13/10/2022`0

    $ajax_var = $_POST['ajax_var'];// ?? '11/10/2022 07:00 AM`11/10/2022 05:00 PM`55555555`2`/focus/users/account/2`Out of Office';
    $reoccur = $_POST['reoccur'];// ?? '07:00 AM`05:00 PM`daily``tue,wed,thu`11/10/2022`13/10/2022`0';



    $ave = explode('`', $ajax_var);
    $user_id = $ave[3];

    $person_did = $user_id;


    $q_user_loc = $this->user_model->get_user_stateCode($person_did);
    $egt_res_arr_user_loc = $q_user_loc->getResultArray();
    $user_state = array_shift($egt_res_arr_user_loc);
    $user_site = $user_state['shortname'];


    switch ($user_site) {
      case "NSW": 
      date_default_timezone_set('Australia/Sydney');
      break;

      case "QLD":
      date_default_timezone_set('Australia/Perth');
      break;

      case "WA":
      date_default_timezone_set('Australia/Perth');
      break;
    }

    $date_a = $ave[0];
    $date_b = $ave[1];
    $notes = $ave[2];
    $user_id = $ave[3];
    $pathname = $ave[4];
    $status = $ave[5];
    $timezone = '';

    switch ($user_site) {
      case "NSW": 
      $timezone = 2;
      break;

      case "QLD":
      $timezone = 0;
      break;

      case "WA":
      $timezone = 1;
      break;
    }

    $this_year = date('Y');
    $occur = explode('`', $reoccur );

    $raw_start_time = explode(' ', $occur[0]);
    $start_time_raw = str_replace(':','',$raw_start_time[0]);
    $start_time = ($raw_start_time[1] == 'PM' ? $start_time_raw + 1200 : $start_time_raw);

    $raw_end_time = explode(' ', $occur[1]);
    $end_time_raw = str_replace(':','',$raw_end_time[0]);
    $end_time = ($raw_end_time[1] == 'PM' ? $end_time_raw + 1200 : $end_time_raw);

    $pattern_type = $occur[2];
    $limits = $occur[3];
    $range_reoccur = $occur[4];

    $data_rage_raw_a = explode('/', $occur[5]);
    $date_range_a =  strtotime($data_rage_raw_a[2].'-'.$data_rage_raw_a[1].'-'.$data_rage_raw_a[0].' 7:00 AM');


    $is_no_end = $occur[7];

    if($is_no_end == 1){
      $date_range_b = 4133926800; //3000-01-01
    }else{
      $data_rage_raw_b = explode('/', $occur[6]);
      $date_range_b = strtotime($data_rage_raw_b[2].'-'.$data_rage_raw_b[1].'-'.$data_rage_raw_b[0].' '.$end_time);
    }

    switch ($pattern_type) {
      case "daily":
      $date_future = '';
      break;

      case "weekly": 
      $date_future = strtotime($data_rage_raw_a[2].'-'.$data_rage_raw_a[1].'-'.$data_rage_raw_a[0].' + '.$limits.' week');
      //$date_future = strtotime(date('Y-m-d',$pDate)); 
      break;

      case "monthly":
      $date_future = strtotime($data_rage_raw_a[2].'-'.$data_rage_raw_a[1].'-'.$range_reoccur.' + '.$limits.' month');
      //$date_future = strtotime(date('Y-m-d',$pDate)); 
      break;

      case "yearly":
      $date_future = strtotime($data_rage_raw_a[2].'-'.$limits.'-'.$range_reoccur.' + 1 year');
      //$date_future = strtotime(date('Y-m-d',$pDate)); 
      break;
    }

    $reoccur_id = $this->user_model->insert_user_availability_reoccur($start_time,$end_time,$pattern_type,$limits,$range_reoccur,$date_range_a,$date_range_b,$is_no_end,$date_future);


    /* reoccur set */
    $user_q = $this->user_model->fetch_user($user_id);
    $ress_arr_user_q = $user_q->getResult();
    $user_detail = array_shift($ress_arr_user_q);
    $user_name = ucfirst($user_detail->user_first_name).' '.ucfirst($user_detail->user_last_name);

    $type = 'User Availability';
    $actions = 'Availability: '.$status.' is been set to.'.$user_name;

    //date_default_timezone_set("Australia/Perth");



    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($person_did,$date,$time,$actions,'',$type,'7');


    //var_dump($date_a);

    $time_stamp_a = $this->date_formater_to_timestamp($date_a,$user_id);
    $time_stamp_b = $this->date_formater_to_timestamp($date_b,$user_id);

 // echo "<p>$user_id,$status,$notes,$time_stamp_a,$time_stamp_b,$reoccur_id,$timezone<p>";


    $this->user_model->inset_availability($user_id,$status,$notes,$time_stamp_a,$time_stamp_b,$reoccur_id,$timezone);

    if (strpos($pathname, 'users') !== false) {
      echo '1';
    }else{
      echo $this->get_user_availability($user_id);
    }
    /* reoccur set */
  }
 






  public function disapproved_leave($supervisor_id){
    
    $this->clear_apost();
    $current_date = strtotime("now");

    $ajax_var = $_POST['ajax_var'];
    $disapproved_data = explode('|', $ajax_var);

    $leave_req_id = $disapproved_data[0];
    $action_comments = $disapproved_data[1];

//    var_dump($disapproved_data);

    if ($supervisor_id != '3'){
      $this->user_model->disapproved_by_supervisor($leave_req_id, '1');
    } else {
      $this->user_model->disapproved_by_gm($leave_req_id, '1', $supervisor_id);
    }

    $this->user_model->insert_leave_action($leave_req_id, $supervisor_id, '0', $current_date, $action_comments);
    return redirect()->to('/users/leave_approvals/'.$supervisor_id);
  }



  public function generate_leave_form($leave_req_id, $leave_user_id){


    $this->calc_leave_points();

    $fetch_user = $this->user_model->fetch_user($leave_user_id);
    $data['user'] = $fetch_user->getResult();

    $leave_alloc = $this->user_model->fetch_leave_alloc($leave_user_id);
    $data['leave_alloc'] = $leave_alloc->getResult();

    $user_supervisor_id = $data['user'][0]->supervisor_id;

    $for_pdf_content = $this->user_model->for_pdf_content($leave_req_id, $user_supervisor_id);
    $row = $for_pdf_content->getRow();

    $for_pdf_content_md = $this->user_model->for_pdf_content_md($leave_req_id);
    $row2 = $for_pdf_content_md->getRow();

    $leave_request_id = $row->leave_request_id;
    $date_applied = date('F j, Y', $row->date);
    $first_name = $row->user_first_name;
    $last_name = $row->user_last_name;
    $role = $row->role_types;
    $start_day = date('F j, Y', $row->start_day_of_leave);
    $end_day = date('F j, Y', $row->end_day_of_leave);
    $date_return = date('F j, Y', $row->date_return);
    $leave_type_id = $row->leave_type_id;
    $leave_type = $row->leave_type;
    $details = $row->details;
    $partial_day = $row->partial_day;
    $partial_part = $row->partial_part;

    // $partial_time = $row->partial_time;

    $superv_first_name = $row->superv_first_name;
    $superv_last_name = $row->superv_last_name;
    $total_annual = $row->total_annual;
    $total_personal = $row->total_personal;
    $no_hrs_of_work = $row->no_hrs_of_work;
    $total_days_away = $row->total_days_away;
    $holiday_leave = $row->holiday_leave;
    $direct_report_comments = $row->action_comments ?? 'Leave request is approved.';

    $md_comments = $row2->action_comments ?? 'Leave request is approved.';

    if ($leave_type_id == '1'){
      $total_leave = $data['leave_alloc'][0]->total_annual;
    } else {
      $total_leave = $data['leave_alloc'][0]->total_personal;
    }

    if ($partial_day == 1){
      if ($partial_part == 1){
        $part_of_day = 'Arrived Late';
      } else if ($partial_part == 2) {
        $part_of_day = 'Depart Early';
      }
    } else {
      $part_of_day = '';
    }

    if ($partial_day == 1){
      $total_leave_pdf = $total_days_away;

    } else {

      if ($leave_type_id >= 5){
        $total_leave_pdf = $total_days_away;
      } else {
        $total_leave_pdf = $total_days_away;
        
      }
    }

    $approved_by = ucfirst($this->session->get('user_first_name')).' '.ucfirst($this->session->get('user_last_name'));

    $content = '<h1 class="title">LEAVE REQUEST FORM</h1>';
    $content .= '<img src="./img/logo_leave_form.png" class="logo" />';
    $content .= '<div class="body1">';
      $content .= '<h1>APPLICATION INFORMATION:</h1><br><br>';
      $content .= '<span class="leave-info">Date Applied: </span><span class="leave-info-data" style="position: absolute; left: 150px;">'.$date_applied.'</span>';
      $content .= '<span class="leave-info" style="position: absolute; left: 370px;">Start Date of Leave: </span><span class="leave-info-data" style="position: absolute; left: 560px;">'.$start_day.'</span><br><br>';
      
      $content .= '<span class="leave-info">Name: </span><span class="leave-info-data" style="position: absolute; left: 150px;">'.$first_name.' '.$last_name.'</span>';
      $content .= '<span class="leave-info" style="position: absolute; left: 370px;">End Date of Leave: </span><span class="leave-info-data" style="position: absolute; left: 560px;">'.$end_day.'</span><br><br>';

      $content .= '<span class="leave-info">Position: </span><span class="leave-info-data" style="position: absolute; left: 150px;">'.$role.'</span>';
      $content .= '<span class="leave-info" style="position: absolute; left: 370px;">Date Return: </span><span class="leave-info-data" style="position: absolute; left: 560px;">'.$date_return.'</span><br><br>';

    $content .= '</div>';
    $content .= '<div class="body2">';
      $content .= '<h1>LEAVE INFORMATION:</h1><br><br>';
      $content .= '<span class="leave-info">Type of Leave: </span>';
      $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.$leave_type.'</span><br><br>';

      $content .= '<span class="leave-info">No. of Days Leave: </span>';
      $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.$total_leave_pdf.'</span><br><br>';

      $content .= '<span class="leave-info">No. of Public Holidays Included: </span>';
      $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.$holiday_leave.' day(s)</span><br><br>';

    if ($leave_type_id < '5'){

      if ($holiday_leave == 0){
        $content .= '<span class="leave-info">'.$leave_type.' Remaining: </span>';
        // $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.round($total_leave / $no_hrs_of_work, 2).' days ('.$total_leave.' hrs)</span><br><br>';

        $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.$total_leave.' days</span><br><br>';

      } else {

        // $total_leave_holiday_days = round($total_leave / $no_hrs_of_work, 2);
        // $holiday_hrs = $holiday_leave * $no_hrs_of_work;
        // $total_leave_holiday_hrs = $total_leave;

        $total_leave_holiday_days = $total_leave - $holiday_hrs;

        $content .= '<span class="leave-info">'.$leave_type.' Remaining: </span>';
        $content .= '<span class="leave-info-data" style="position: absolute; left: 400px;">'.$total_leave_holiday_days.' days</span><br><br>'; // ('.$total_leave_holiday_hrs.' hrs)</span><br><br>
      }
    }

      $content .= '<span class="leave-info">Purpose:</span><br>';
      $content .= '<span class="leave-info-data"> &nbsp;&nbsp;&nbsp;&nbsp; '.$details.'</span><br><br>';

      if ($user_supervisor_id != 3){
        $content .= '<span class="leave-info">Direct Report Comments:</span><br>';
        $content .= '<span class="leave-info-data"> &nbsp;&nbsp;&nbsp;&nbsp; '.$direct_report_comments.'</span><br><br>';
        $content .= '<span class="leave-info">Managing Director Comments:</span><br>';
        $content .= '<span class="leave-info-data"> &nbsp;&nbsp;&nbsp;&nbsp; '.$md_comments.'</span><br><br>';
      } else {
        $content .= '<span class="leave-info">Managing Director Comments:</span><br>';
        $content .= '<span class="leave-info-data"> &nbsp;&nbsp;&nbsp;&nbsp; '.$md_comments.'</span><br><br>';
      }

    $content .= '</div>';
    $content .= '<div class="body3">';
      $content .= '<span class="approval">Approved by (Direct Reporting): </span><span class="approval_name" style="position: absolute; left: 380px;">'.$superv_first_name.' '.$superv_last_name.'</span><br><br>';
      $content .= '<span class="approval">Managing Director Authorisation: </span><span class="approval_name" style="position: absolute; left: 380px;">'.$approved_by.'</span>';
    $content .= '</div>';

    $file_name = 'leave_form_'.$leave_request_id;
    $my_pdf = $this->html_form($content,'portrait','A4',$file_name,'leave_form');
    echo $my_pdf;
  }

  public function html_form($content,$orientation,$paper,$file_name,$folder,$auto_clear=TRUE){
    $document = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title></title>';
    $document .= '<link type="text/css" href="./css/pdf.css" rel="stylesheet" />';
    $document .= '</head><body>';
    $document .= $content;
    $document .= '</body></html>';
    return $this->pdf_create($document,$orientation,$paper,$file_name,$folder,$auto_clear);
  }



  public function pdf_create($html, $orientation='portrait', $paper='A4',$filename='generated_file',$folder_type='general' ,$auto_clear=TRUE,$stream=TRUE){
    
    $dompdf = new DOMPDF();
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

    $dompdf->setPaper($paper,$orientation);
    $dompdf->loadHtml($html);
    $dompdf->render();

    $canvas = $dompdf->getCanvas();
    $date_gen = date("jS F, Y");

    $user_prepared = ucfirst($this->session->get('user_first_name')).' '.ucfirst($this->session->get('user_last_name'));



/*
    $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
      $text = "Page $pageNumber of $pageCount";
      $font = $fontMetrics->getFont('monospace');
      $pageWidth = $canvas->get_width();
      $pageHeight = $canvas->get_height();
      $size = 12;
      $width = $fontMetrics->getTextWidth($text, $font, $size);
      $canvas->text($pageWidth - $width - 20, $pageHeight - 20, $text, $font, $size);
    });
*/



//    $font = $fontMetrics->getFont("helvetica", "bold");

    if($orientation == 'portrait'){
      $canvas->text(535,10, "Page: {PAGE_NUM} of {PAGE_COUNT} ", "helvetica", 8, array(0,0,0));
      $canvas->text(15, 810, "Page: {PAGE_NUM} of {PAGE_COUNT}                   Produced: $date_gen                   Prepared By: $user_prepared", "helvetica", 8, array(0,0,0));
      
    }else{
      $canvas->text(780,10, "Page: {PAGE_NUM} of {PAGE_COUNT} ", "helvetica", 8, array(0,0,0));
      $canvas->text(20, 800, "Page: {PAGE_NUM} of {PAGE_COUNT}                   Produced: $date_gen                   Prepared By: $user_prepared", "helvetica", 8, array(0,0,0));
    }

    $output = $dompdf->output();

    //$filename .= '-'.date("d-m-Y-His");

    if($auto_clear){
      $this->delete_dir('docs/'.$folder_type);  #remove folder and contents
    }

    //create the folder if it's not already exists
    if(!is_dir('docs/'.$folder_type)){
      mkdir('docs/'.$folder_type,0755,TRUE);
    }
    write_file('docs/'.$folder_type.'/'.$filename.'.pdf','');

    file_put_contents('docs/'.$folder_type.'/'.$filename.'.pdf', $output);

    //return $filename;
    //unlink('docs/'.$folder_type.'/'.$filename.'.pdf');



    $dompdf->stream($filename.'.pdf', array("Attachment" => false));
  }

  private function delete_dir($dir) {
    $handle=opendir($dir);
    while (($file = readdir($handle))!==false) {
      @unlink($dir.'/'.$file);
    }
    closedir($handle);
    rmdir($dir);
  }

  public function cancel_leave($leave_id,$user_id){

    $person_did = $this->session->get('user_id');

    $type = 'Leave';
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $actions = 'Canceled leave of user id: '.$user_id.' leave id:'.$leave_id;

    $this->user_model->insert_user_log($person_did,$date,$time,$actions,'',$type,'8');

    $this->user_model->cancel_applied_leave($leave_id);
    return redirect()->to('/users/leave_details/'.$user_id.'?view_approved=1');
  }

  public function set_availalbility_on_leave($leave_req_id=''){
     
    $fetch_leave_req_q = $this->user_model->fetch_leave_req($leave_req_id);
    $get_ress_leave_req = $fetch_leave_req_q->getResultArray();
    $fetch_leave_req = array_shift($get_ress_leave_req);

    $re_occur = 0;

    if($fetch_leave_req['partial_day'] == 1){

      if($fetch_leave_req['partial_part'] == 1){
        $date_a = date("Y-m-d", $fetch_leave_req['start_day_of_leave']   ).' 07:00am';
        $date_b = date("Y-m-d", $fetch_leave_req['end_day_of_leave']   ).' 12:00pm';
      }

      if($fetch_leave_req['partial_part'] == 2){
        $date_a = date("Y-m-d", $fetch_leave_req['start_day_of_leave']   ).' 12:00pm';
        $date_b = date("Y-m-d", $fetch_leave_req['end_day_of_leave']   ).' 05:00pm';
      }

    }else{
      $date_a = date("Y-m-d", $fetch_leave_req['start_day_of_leave']   ).' 07:00:00am';
      $date_b = date("Y-m-d", $fetch_leave_req['end_day_of_leave']   ).' 05:00:00pm';

      if($fetch_leave_req['total_days_away'] > 1){
        $re_occur = 1;
      }
    }

    $details = $fetch_leave_req['details'];
//echo "$date_a<br />$date_b";

    if( $fetch_leave_req['leave_type_id'] == 1 ||  $fetch_leave_req['leave_type_id'] == 4 ||  $fetch_leave_req['leave_type_id'] == 5  ||  $fetch_leave_req['leave_type_id'] == 6){
      $status = 'Leave';
    }

    if( $fetch_leave_req['leave_type_id'] == 2 || $fetch_leave_req['leave_type_id'] == 3){
      $status = 'Sick';
    }

//echo "<p></p>";
//var_dump($fetch_leave_req);

    $date_time_stamp_a = strtotime($date_a);
    $date_time_stamp_b = strtotime($date_b);


    $loc_id = 0;

    $person_did = $fetch_leave_req['user_id'];
    $fetch_user_loc = $this->admin_m->fetch_user_location($person_did);
    $get_ress_user_loc = $fetch_user_loc->getResultArray();
    $user_location = array_shift($get_ress_user_loc);

    $user_state_raw = explode(',', $user_location['location']);
    $user_site = trim($user_state_raw[1]);
    switch ($user_site) {
      case "NSW": 
      $loc_id = 2;
      break;

      case "QLD":
      $loc_id = 0;
      break;

      case "WA":
      $loc_id = 1;
      break;
    }

    if( $re_occur == 1 ){
      $reoccur_id = $this->user_model->insert_user_availability_reoccur('0700','1700','daily','','mon,tue,wed,thu,fri',$date_time_stamp_a,$date_time_stamp_b,'0','');
      $this->user_model->inset_availability($fetch_leave_req['user_id'],$status,  $fetch_leave_req['details'],$date_time_stamp_a,$date_time_stamp_b,$reoccur_id,$loc_id,$leave_req_id);
    }else{
      $this->user_model->inset_availability($fetch_leave_req['user_id'],$status,  $fetch_leave_req['details'],$date_time_stamp_a,$date_time_stamp_b,0,$loc_id,$leave_req_id);
    }
  }


public function add_leave_alloc($user_id_page){
    $this->clear_apost();


    $current_date = date("d-m-Y");
    $timestamp_current = strtotime($current_date);
    $yesterday_date = strtotime($current_date."-1 days");
    $annual_manual_entry = $this->request->getPost('annual_manual_entry');
    $personal_manual_entry = $this->request->getPost('personal_manual_entry');
    $checked_sched = $this->request->getPost('sched');
    $no_hrs_of_work = $this->request->getPost('no_hrs_of_work');
    $leave_rate_type = $this->request->getPost('leave_rate_type');

    if (isset($checked_sched)){
      $checked_sched = implode(',', $checked_sched);
    } else {
      $checked_sched = '';
    }

    $static_defaults = $this->user_model->select_static_defaults();
    $static_defaults = $static_defaults->getRow();

    $annual_leave_daily_rate = $static_defaults->annual_leave_daily_rate;
    $personal_leave_daily_rate = $static_defaults->personal_leave_daily_rate;

    $leave_alloc = $this->user_model->fetch_leave_alloc($user_id_page);
    $num_rows = $leave_alloc->getNumRows();

    if ($num_rows == 0){
      $result = $this->user_model->add_leave_alloc($timestamp_current, $user_id_page, $annual_manual_entry, $personal_manual_entry, $checked_sched, $no_hrs_of_work, $timestamp_current, $leave_rate_type); 

      $update_success = 'User account is now updated.';
      $this->session->setFlashdata('total_leave', $update_success);


      return redirect()->to('/users/account/'.$user_id_page);
    } else {
      $update_success = 'User account is now updated.';
      $this->session->setFlashdata('total_leave', $update_success);


      return redirect()->to('/users/account/'.$user_id_page);
    }

    // }
    
  }


  public function apply_leave($user_id){

    $this->clear_apost();

    $leave_type_count = 0;
    $current_date = strtotime("now");
    //$current_year = date('Y');

    $ajax_var = $_POST['ajax_var'];// ?? '2|11/10/2022|11/10/2022|12/10/2022|asdasd|1|0|0||0|';
    $insert_data = explode('|', $ajax_var);

    $leave_type = $insert_data[0];

    $date_start = str_replace('/', '-', $insert_data[1]);
    $timestamp_start = strtotime($date_start);

    $date_end = str_replace('/', '-', $insert_data[2]);
    $timestamp_end = strtotime($date_end);

    $date_return = str_replace('/', '-', $insert_data[3]);
    $timestamp_return = strtotime($date_return);

    $leave_details = $insert_data[4];
    $total_hrs_leave = $insert_data[5];
    $partial_day = $insert_data[6];
    $partial_part = $insert_data[7];
    $partial_time = $insert_data[8];
    $ph_holidays = $insert_data[9];
    $applied_by = $insert_data[10];

    $fetch_user = $this->user_model->fetch_user($user_id);
    $res_arr_user = $fetch_user->getResultArray();
    $qArr = array_shift($res_arr_user);
    $user_supervisor_id = $qArr['supervisor_id'];

    $this->user_model->insert_leave_req($current_date, $user_id, $leave_type, $timestamp_start, $timestamp_end, $timestamp_return, $leave_details, $total_hrs_leave, $user_supervisor_id, $partial_day, $partial_part, $partial_time, $ph_holidays, $applied_by);
  }

  function set_availability($innit_ave = ''){
    $this->clear_apost();

    if($innit_ave != ''){

      $ave = explode('`',$innit_ave);

    }else{

      $ajax_var = $_POST['ajax_var'] ?? null;
      $ave = explode('`', $ajax_var);

    }
/*
    $ave[0] = '06/10/2022 07:00 AM';
    $ave[1] = '06/10/2022 05:00 PM';
    $ave[2] = 'test';
    $ave[3] = '2';
    $ave[4] = '/focus/users/account/2';
    $ave[5] = 'Out of Office';
*/




    $date_a     = $ave[0];
    $date_b     = $ave[1];
    $notes      = $ave[2];
    $user_id    = $ave[3];
    $pathname   = $ave[4];
    $status     = $ave[5];
    $timezone   = 0;

    $user_q = $this->user_model->fetch_user($user_id);
    $get_ress_arr_user = $user_q->getResult();
    $user_detail = array_shift($get_ress_arr_user);
    $user_name = ucfirst($user_detail->user_first_name).' '.ucfirst($user_detail->user_last_name);

    $person_did = $this->session->get('user_id');

    $type = 'User Availability';
    $actions = 'Availability: '.$status.' is been set to.'.$user_name;
    //

    //date_default_timezone_set("Australia/Perth");


    $fetch_user_loc = $this->admin_m->fetch_user_location($user_id);
    $get_ress_arr_user_loc = $fetch_user_loc->getResultArray();
    $user_location = array_shift($get_ress_arr_user_loc);
//    $currentTimeQld = new DateTime();


/*
    $state_code_loc = $user_location['state_code'];
    $currentTimeQld->setTimezone(new DateTimeZone($state_code_loc));
    $current_date_time = $currentTimeQld->getTimestamp();// strtotime(date("Y-m-d h:i A"));
*/


    $currentTimeQld = (new \CodeIgniter\I18n\Time("now", $user_location['state_code'] ));
    $current_date_time = $currentTimeQld->getTimestamp();// strtotime(date("Y-m-d h:i A"));


    $date = $currentTimeQld->format("d/m/Y");
    $time = $currentTimeQld->format("H:i:s");

    $this->user_model->insert_user_log($person_did,$date,$time,$actions,'',$type,'7');


    //var_dump($date_a);

    $time_stamp_a = $this->date_formater_to_timestamp($date_a,$user_id);
    $time_stamp_b = $this->date_formater_to_timestamp($date_b,$user_id);

    $this->user_model->inset_availability($user_id,$status,$notes,$time_stamp_a,$time_stamp_b,0,$timezone);

    if (strpos($pathname, 'users') !== false) {
      echo '1';
    }else{
      echo $this->get_user_availability($user_id);
    }

  }






  function reset_availability(){

    if(isset($_POST['ajax_var'])){
      $data_reset = explode('`', $_POST['ajax_var']);
      $pathname = $data_reset[0];
      $user_id = $data_reset[1];
    }else{
      return null;
    }





    $availability_id = 0;
    $type = 0;

    if (strpos($pathname, 'users') !== false) {
      echo '1';
    }


    $current_date_time = strtotime(date("Y-m-d h:i A"));

    $current_date = date('Y/m/d');
    $tomorrow = date('Y-m-d',strtotime($current_date . "+1 days"));


    $this->reset_reoccur_avaialbility();

    $availability_id = 0;
    $type = 0;

    $is_available = 0;
    $stage_b = 1;

    $user_ave_q = $this->user_model->get_user_availability($user_id,$current_date_time);
    $user_ave_get_rest_arr = $user_ave_q->getResultArray();
    $user_ave = array_shift($user_ave_get_rest_arr);

    if($user_ave_q->getNumRows() === 1){
      $availability_id = $user_ave['user_availability_id']; 
      $stage_b = 0;
      $type = 1;

      $this->user_model->remove_availability($availability_id);
    }else{


    }
 
    $current_timestamp = strtotime(date("Y-m-d h:i A"));
  //  $current_timestamp = strtotime(date("2017-11-01"));
    $time_extended = date("Hi");
    $day_like = strtolower(date("D") );

    if($stage_b == 1){

      $reoccur_q = $this->user_model->get_reoccur_ave_year_month($current_timestamp,$time_extended,$user_id);
      if($reoccur_q->getNumRows() === 1){

        $get_ress_arr_recurr = $reoccur_q->getResultArray();

        $reoccur = array_shift($get_ress_arr_recurr);
        $availability_id = $reoccur['reoccur_id'];
        $pattern_type = $reoccur['pattern_type']; 
        $type = 2;


        switch ($pattern_type) {

          case "weekly": 
            $date_future = strtotime(date("Y-m-d").' + '.$reoccur['limits'].' week');
          break;


          case "monthly":
          $date_future = strtotime(date("Y-m").'-'.$reoccur['range_reoccur'].' + '.$reoccur['limits'].' month');
          //$date_future_more = strtotime(date("Y-m", $date_future).'-'.$reoccur['range_reoccur'].' + '.$reoccur['limits'].' month' );
          break;

          case "yearly":
          $date_future = strtotime(date("Y").'-'.$reoccur['limits'].'-'.$reoccur['range_reoccur'].' + 1 year');
          //$date_future_more = strtotime(date("Y", $date_future).'-'.$reoccur['limits'].'-'.$reoccur['range_reoccur'].' + 1 year');
          break;
        }

        $this->user_model->update_future_reoccur_present_date($date_future,$reoccur['date_range_b'],$availability_id);


      }else{
        $is_available = 1;
      }
    }


    if($is_available == 1){

      $current_date_time = strtotime(date("Y-m-d h:i A"));
      $user_ave_roc_q = $this->user_model->get_reoccur_availability($current_date_time, $time_extended, $day_like,$user_id);


      if($user_ave_roc_q->getNumRows() === 1){
        $user_roc_ress_arr = $user_ave_roc_q->getResultArray();
        $reoccur = array_shift($user_roc_ress_arr);
        $availability_id = $reoccur['reoccur_id']; 
        $type = 2;
        $pattern_type = $reoccur['pattern_type']; 



        switch ($pattern_type) {

          case "weekly": 
            $date_future = strtotime(date("Y-m-d").' + '.$reoccur['limits'].' week');
            $this->user_model->update_future_reoccur_present_date($date_future,$reoccur['date_range_b'],$availability_id);
          break;


          case "daily": 
            $date_future = strtotime(date("Y-m-d").' + 1 day');
            $this->user_model->update_future_reoccur_present_date($date_future,$reoccur['date_range_b'],$availability_id);
          break;


        }


      }
    }


  }


  function date_formater_to_timestamp($input_datetime,$user_id=''){

    $set = explode(' ',$input_datetime);
    $date = explode('/', $set[0]);
    $time = explode(':', $set[1]);

    if(isset(  $user_id ) && $user_id != ''){

      $userid = $user_id;
      $fetch_user_loc = $this->admin_m->fetch_user_location($userid);
      $res_arr_user_loc = $fetch_user_loc->getResultArray();
      $user_location = array_shift($res_arr_user_loc);
      $state_code_loc = $user_location['state_code'];

    }else{

      $userid = $this->session->get('user_id');

      $res_arr_user_loc = $fetch_user_loc->getResultArray();
      $user_location = array_shift($res_arr_user_loc);


      $state_code_loc = $user_location['state_code'];

    }


    date_default_timezone_set($state_code_loc);

    $date_formatted = $date[2].'-'.$date[1].'-'.$date[0].' '.$time[0].':'.$time[1].' '.$set[2];
    $timestamp = strtotime("$date_formatted");
    return $timestamp;
  }



  public function get_user_availability($user_id,$mod='',$return_text=''){

/*
    $fetch_user_loc = $this->admin_m->fetch_user_location($user_id);
    $user_location = array_shift($fetch_user_loc->result_array());
    $currentTimeQld = new DateTime(null, new DateTimeZone( $user_location['state_code'] ));
*/
    $current_date_time =  strtotime(date("Y-m-d h:i A"));


  //  echo '<p id="" class="">*** '.$user_location['state_code'].' ***</p>';

  //  echo '<p id="" class="">'.$currentTimeQld->format('Y-m-d h:i A').'</p>';


  //  $current_date_time = $currentTimeQld->getTimestamp();// strtotime(date("Y-m-d h:i A"));
    $this->reset_reoccur_avaialbility();

  //  $current_date_time = strtotime(date("2017-11-01"));
    $current_date_time =  strtotime(date("Y-m-d h:i A"));
    $is_available = 0;
    $stage_b = 1;

    $user_ave_q = $this->user_model->get_user_availability($user_id,$current_date_time);
    $r_arr_user_ave = $user_ave_q->getResultArray();
    $user_ave = array_shift($r_arr_user_ave);

    if($mod != ''){

      if($user_ave_q->getNumRows() === 1){

        if($return_text == ''){


          if($user_ave['status'] == 'Busy'){
            echo '<span style="color:white; background: red;"><i class="fa fa-exclamation-circle"></i>';
          }elseif($user_ave['status'] == 'Sick'){
            echo '<span style="color:white; background: purple;"><i class="fa fa-times-circle"></i>';
          }elseif($user_ave['status'] == 'Leave'){
            echo '<span style="color:white; background: gray;"><i class="fa fa-minus-circle"></i>';
          }elseif($user_ave['status'] == 'Out of Office'){
            echo '<span style="color:white; background: orange;"><i class="fa fa-arrow-circle-left"></i>';
          }else{ }

          echo ' '.$user_ave['status'].'</span>';
        }else{
          return $user_ave['status']; 
        }

        $stage_b = 0;

      }

    }else{

      if($user_ave_q->getNumRows() === 1){

        if($user_ave['status'] == 'Busy'){
          echo '<span style="color: red;"><i class="fa fa-exclamation-circle"></i>';
        }elseif($user_ave['status'] == 'Sick'){
          echo '<span style="color: purple;"><i class="fa fa-times-circle"></i>';
        }elseif($user_ave['status'] == 'Leave'){
          echo '<span style="color: gray;"><i class="fa fa-minus-circle"></i>';
        }elseif($user_ave['status'] == 'Out of Office'){
          echo '<span style="color: orange;"><i class="fa fa-arrow-circle-left"></i>';
        }else{ }

        echo ' '.$user_ave['status'].'</span>';
        $stage_b = 0;
      }
    }

 
    $current_timestamp = $current_date_time; //strtotime(date("Y-m-d h:i A"));
  //  $current_timestamp = strtotime(date("2017-11-01"));
    $time_extended = date("Hi");
    $day_like = strtolower(date("D") );


  //  echo "$current_timestamp $time_extended $day_like";

    if($stage_b == 1){

      $reoccur_q = $this->user_model->get_reoccur_ave_year_month($current_timestamp,$time_extended,$user_id);
      if($reoccur_q->getNumRows() === 1){

        $reoccur = array_shift($reoccur_q->getResultArray());



        if($return_text == ''){
          if($reoccur['status'] == 'Busy'){
            echo '<span style="color: red;"><i class="fa fa-exclamation-circle"></i>';
          }elseif($reoccur['status'] == 'Sick'){
            echo '<span style="color: purple;"><i class="fa fa-times-circle"></i>';
          }elseif($reoccur['status'] == 'Leave'){
            echo '<span style="color: gray;"><i class="fa fa-minus-circle"></i>';
          }elseif($reoccur['status'] == 'Out of Office'){
            echo '<span style="color: orange;"><i class="fa fa-arrow-circle-left"></i>';
          }else{ }

          echo ' '.$reoccur['status'].'</span>';
        }else{

          return $reoccur['status'];
        }




      }else{
        $is_available = 1;
      }
    }




    if($is_available == 1){

      $current_date_time = strtotime(date("Y-m-d h:i A"));
      $user_ave_roc_q = $this->user_model->get_reoccur_availability($current_date_time, $time_extended, $day_like,$user_id);
      $r_arr_user_ave_roc = $user_ave_roc_q->getResultArray();
      $reoccur_ave = array_shift($r_arr_user_ave_roc);


      if($user_ave_roc_q->getNumRows() === 1){


        if($return_text == ''){
        if($reoccur_ave['status'] == 'Busy'){
          echo '<span style="color: red;"><i class="fa fa-exclamation-circle"></i>';
        }elseif($reoccur_ave['status'] == 'Sick'){
          echo '<span style="color: purple;"><i class="fa fa-times-circle"></i>';
        }elseif($reoccur_ave['status'] == 'Leave'){
          echo '<span style="color: gray;"><i class="fa fa-minus-circle"></i>';
        }elseif($reoccur_ave['status'] == 'Out of Office'){
          echo '<span style="color: orange;"><i class="fa fa-arrow-circle-left"></i>';
        }else{ }

        echo ' '.$reoccur_ave['status'].'</span>';

      }else{
        return $reoccur_ave['status'];
      }

      }else{
        echo '<span style="color: green;"><i class="fa fa-check-circle"></i>';
        echo ' Available </span>';
      }
      
    }

  }




 
  public function loop_user_supervisor($supervisor_id = '',$ext='',$pm_ids=''){

    $is_gray = '';

    if($supervisor_id == ''){
      $supervisor_id = 3;
      //$pm_ids = 3;
      $ext = '<div id="" class="default_blank blanks box-area  pad-10 m-10 pull-left" style=""></div>';

      $user_access_q = $this->user_model->fetch_user($supervisor_id);
      $q_user_access_r = $user_access_q->getResultArray();
      $user = array_shift( $q_user_access_r );



      echo '<div class="box-area photo_frame pad-5 text-left"><div id="" class="user_'.$user['user_focus_company_id'].'_comp_group" style="border-radius: 60px !important;     margin: 0px 10px 0px 0px;    width: 60px;    height: 60px;    float: left;    overflow: hidden; ">
      <div style="float: left; overflow: hidden; height: 60px; "><a class="text-center" href="'.base_url().'/users/account/'.$user['user_id'].'">';

        if($user['user_profile_photo'] == ''):

    echo '<img src="'.base_url().'/uploads/users/no_photo.jpg" style="width: 60px;   ">';
                              

   else: 

    echo '<img src="'.base_url().'/uploads/users/'.$user['user_profile_photo'].'" style="width: 60px;   ">';
      endif;

      echo'</a></div></div>
                                              
                                <p><strong>'.$user['role_types'].'</strong></p>
                                <p><a href="'.base_url().'/users/account/'.$user['user_id'].'">'.$user['user_first_name'].' '.$user['user_last_name'].'</a></p>
                                  <hr style="margin: 20px 0 0;">
                              </div>';
  //  echo $ext;
    } 


    $fetch_user_sups_q= $this->user_model->fetch_users_under_supervisor($supervisor_id);
    $fetch_user_sups = $fetch_user_sups_q->getResult();



    foreach($fetch_user_sups as $key => $value){
 
  $div_tag = $pm_ids.'_'.$supervisor_id.'_'.$value->user_id;
 
// var_dump($pm_ids);
// var_dump($supervisor_id);


//echo $value->user_id.$ext.$value->user_first_name.'**'.$div_tag.'<br />';

$pm_group_arr = explode('_', $div_tag);
//unset($pm_group_arr[0]);

//array_filter($pm_group_arr);
$group_pms = implode(' direp_', $pm_group_arr);





      if($value->is_third_party == 1){
        $is_gray = 'gray_color';
      }else{
        $is_gray = '';
      }
 
//'.base_url('users/account/'.$value->user_id).'




      echo $ext.'
                              <div class="box-area pad-5 text-left direp_'.$group_pms.' diruser_'.$value->user_id.'">
                                <div id="" class="user_'.$value->user_focus_company_id.'_comp_group '.$is_gray.'" style="border-radius: 60px !important;     margin: 0px 10px 0px 0px;    width: 60px;    height: 60px;    float: left;    overflow: hidden; "><div style="float: left; overflow: hidden; height: 60px; ">
                                                              <div class="text-center user_dir pointer" id="user_'.$value->user_id.'"  >    ';


if($value->user_profile_photo == ''):
    echo '<img src="'.base_url().'/uploads/users/no_photo.jpg" style="width: 60px;   ">';              
else: 
    echo '<img src="'.base_url().'/uploads/users/'.$value->user_profile_photo.'" style="width: 60px;  ">';
endif;
                                                                  
                                                              

                                                                echo '</div></div></div>
                                              
                                <p><strong>'.$value->role_types.'</strong></p>
                                <p> <a href="'.base_url().'users/account/'.$value->user_id.'" class="m-left-5">'.$value->user_first_name.' '.$value->user_last_name.'</a></p>
                                <hr style="margin: 20px 0 0;">';





                              echo '</div>';


      $this->loop_user_supervisor($value->user_id,$ext.'<div id="" class="blanks box-area  pad-10 m-10 pull-left direp_'.$group_pms.'" style=""></div>',$supervisor_id.'_'.$pm_ids);







    }

  }
  



    //review_code
    function logout(){
      $this->session = \Config\Services::session();
      $userid = $this->session->get('user_id');
      $request = \Config\Services::request();

      $person_did = $userid;
      $type = 'User Logout';

      $date = date("d/m/Y");
      $time = date("H:i:s");

      $user_ip = $request->getIPAddress();
      $actions = 'Logged in IP '.$user_ip;

      $this->user_model->insert_user_log($person_did,$date,$time,$actions,'',$type);
      delete_cookie("user_id");

      $this->user_model->log_out($userid);
      $this->session->destroy();

      $link = base_url().'/users/signin';


    //  echo '<pre>';var_dump($link );echo '</pre>'; 
 

      return redirect()->to('/signin');


  //  return redirect($link);
    }
    //review_code






    public function test_partial(){
      echo '<pre>';var_dump( 'TEST PARTIAL HERE' );echo '</pre>'; 
    }


  

    //review_code
    public function login_users(){
      $data['log_users_q'] = $this->user_model->fetch_login_user();
   //   $this->load->view("login_users_t",$data);
      return view('App\Modules\Users\Views\login_users_t',$data);
    }
    //review_code


  function set_user_log(){
    $this->session = \Config\Services::session();
    $userid = $this->session->get('user_id');

    $date_log = date('Y-m-d');
    $time_log = date('H:i:s');
    $this->user_model->insert_user_min_log($userid,$date_log,$time_log);

    $sess_ipadd = $this->session->get('ip_add');

    $log_in_users = $this->user_model->fetch_user($userid);
    foreach ($log_in_users->getResultArray() as $row)
    {
      $user_ipadd = $row['ip_address'];
      if($user_ipadd !== $sess_ipadd){
         if($this->session->get('user_id')){

          //review_code
          $output = 0;          
          # $this->session->destroy();
          # return redirect()->to('/signin');
          //review_code

         }
        $output = 0;
      }else{
        //review_code
        #$output = 0;
        $output = 0; 
        //review_code
      }
    }

    echo $output;
  }
  




  public function fetch_all_leave_dates(){

    $exists = '';

 //   $check_leave_date = explode('|',$_POST['ajax_var']);


    $check_leave_date[0] = 2;
    $check_leave_date[1] = '10/10/2022';
   
    $apply_date =  date('d/m/Y', strtotime($check_leave_date['1']));

  //  $format_apply_date = DateTime::createFromFormat('d/m/Y', $apply_date);
  //  $apply_date = $format_apply_date->format('Y-m-d');


    $apply_date = date_format(date_create_from_format('d/m/Y', $apply_date), 'Y-m-d');



  //  $get_apply_date = new DateTime($apply_date);

    $user_id = $check_leave_date['0'];
    $all_leave_dates_by_user = $this->user_model->fetch_all_leave_dates($user_id);
    $data['all_leave_dates_by_user'] = $all_leave_dates_by_user->getResult();

    foreach ($data['all_leave_dates_by_user'] as $row) {

      $f_start_date = date('Y-m-d', $row->start_day_of_leave);
      $f_end_date = date('Y-m-d', $row->end_day_of_leave);

    //  $format_start_date = DateTime::createFromFormat('d/m/Y', $f_start_date);
    //  $format_end_date = DateTime::createFromFormat('d/m/Y', $f_end_date);
      
  //    $start_date = $format_start_date->format('Y-m-d');
  //    $end_date = $format_end_date->format('Y-m-d');




$strtime_get_start_date = strtotime($f_start_date);
$strtime_get_end_date = strtotime($f_end_date);
$strtime_apply_date = strtotime($apply_date);

  //    $get_start_date = new DateTime($start_date);
   //   $get_end_date = new DateTime($end_date);

      if(($strtime_apply_date >= $strtime_get_start_date ) && ($strtime_apply_date <= $strtime_get_end_date) ){
        $exists = 1;
      }
    }


 

  }


  function set_user_log_min(){
    $data['user_log_min'] = 1;
    $this->session->set($data);

    $remember = $this->session->get('remember');
    if($remember == 1){
      $username = $this->session->get('user_name');
      $password = $this->session->get('password');
    }else{
      $username = "";
      $password = "";
    }

    echo $username."|".$password."|".$remember;
  }

  public function check_pending_leave_count(){

    $check_pending = explode('|',$_POST['ajax_var']);
    
    $user_id = $check_pending['0'];
    $type = $check_pending['1'];
    $no_hrs_of_work = $check_pending['2'];



    if ($type == 1){

      $pending_total_annual = $this->user_model->check_pending_total_annual($user_id);
      $data['pending_total_annual'] = $pending_total_annual->getResult();
      
      $pending_total_annual = $data['pending_total_annual'][0]->pending_total_annual;

      $pending_total_holiday_annual = $this->user_model->check_pending_total_annual_holiday($user_id);
      $data['pending_total_holiday_annual'] = $pending_total_holiday_annual->getResult();
      $pending_total_holiday_annual = $data['pending_total_holiday_annual'][0]->holiday_annual;

      $total_annual_holiday = $no_hrs_of_work * $pending_total_holiday_annual;
      $total_pending_annual = $pending_total_annual - $total_annual_holiday;

      echo $total_pending_annual;

    } else if ($type == 2 || $type == 3 || $type == 4) {

      $pending_total_personal = $this->user_model->check_pending_total_personal($user_id);
      $data['pending_total_personal'] = $pending_total_personal->getResult();

      $pending_total_personal = $data['pending_total_personal'][0]->pending_total_personal;

      $pending_total_holiday_personal = $this->user_model->check_pending_total_personal_holiday($user_id);
      $data['pending_total_holiday_personal'] = $pending_total_holiday_personal->getResult();
      $pending_total_holiday_personal = $data['pending_total_holiday_personal'][0]->holiday_personal;

      $total_personal_holiday = $no_hrs_of_work * $pending_total_holiday_personal;
      $total_pending_personal = $pending_total_personal - $total_personal_holiday;

      echo $total_pending_personal;

    } else {
      echo 'others to, walang gagawin';
    }

    
  }



    public  function _upload_primary_photo($fileToUpload,$dir,$name_pref='user_'){

      $time = date("hismdY", time());
      $file = $this->request->getFile($fileToUpload);
      $ext = $file->getClientExtension();

      $newName = $name_pref.$time.'.'.$ext;

      if ($file->isValid() && !$file->hasMoved()) {
        $file->move(ROOTPATH . './uploads/'.$dir.'/', $newName);
        return 'success|'.$newName;
      }else{
        $upload_error = $file->getError();
        return 'error|'.$upload_error;
      }

    }


    public function account($user_id = null){
      $data = array();

      if(!$this->_is_logged_in() ):
        return redirect()->to('/signin');
      endif;

      if(!$user_id){
        return redirect()->to('/users');
      }else{
        $data['user_id_page'] = $user_id;
      }


      $this->_check_user_access('users',1);


    $data['main_content'] = 'account';
    $data['screen'] = 'Account Details';

    $departments = $this->user_model->fetch_all_departments();
    $data['departments'] = $departments->getResult();

    $roles = $this->user_model->fetch_all_roles();
    $data['roles'] = $roles->getResult();

    $focus = $this->admin_m->fetch_all_company_focus();
    $data['focus'] = $focus->getResult();
    $error_password = 0;

    $static_defaults = $this->user_model->select_static_defaults();
    $data['static_defaults'] = $static_defaults->getResult();

    $data['error'] = '';
    $data['upload_error'] = '';

    
/*
    $access = $this->user_model->fetch_all_access();
    $data['all_access'] = $access->result();
*/
    $user_list = $this->user_model->list_user_short();
    $data['user_list'] = $user_list->getResult();

    $user_id = $this->request->uri->getSegment(3);

    $access = "";
    $access_query = $this->user_model->fetch_app_access($user_id);
    foreach ($access_query->getResultArray() as $row){
      $access = $access."/".$row['app_access_type'];
    }

    $travel_access = 0;
    $plate_no = "";
    $taccess_query = $this->user_model->fetch_app_travel_access($user_id);
    if($taccess_query->getNumRows() == 1){
      $travel_access = 1;
      foreach ($taccess_query->getResultArray() as $row){
        $plate_no = $row['plate_no'];
      }
    }


    $logged_user_id = $this->session->get('user_id');
    $user_direct_reportee = $this->user_model->get_direct_reports($logged_user_id);
    $arr_reportee = array();

    if($user_direct_reportee->getNumRows() > 0){
      foreach ($user_direct_reportee->getResultArray() as $row){
        array_push($arr_reportee,$row['user_id']);
      }
    }

    $data['direct_reportee'] = $arr_reportee;

    $data['access'] = $access;

    $data['travel_access'] = $travel_access;
    $data['plate_no'] = $plate_no;

    $used_annual_total = $this->user_model->get_total_leave_annual($user_id);
    $data['used_annual_total'] = $used_annual_total->getRow();

    $used_personal_total = $this->user_model->get_total_leave_personal($user_id);
    $data['used_personal_total'] = $used_personal_total->getRow();

    $q_annual_holidays = $this->user_model->get_annual_holidays($user_id);
    $data['annual_holidays'] = $q_annual_holidays->getRow();

    $q_sick_holidays = $this->user_model->get_sick_holidays($user_id);
    $data['sick_holidays'] = $q_sick_holidays->getRow();

    $fetch_user = $this->user_model->fetch_user($user_id);
    $data['user'] = $fetch_user->getResult();

    $this->calc_leave_points();

    if( $data['user'][0]->is_active == 0){
      redirect('users', 'refresh');
    }

    $data['direct_company'] = $data['user'][0]->direct_company;
    
    if($data['user'][0]->user_date_of_birth != ''){

      $dob_arr = explode('/',$data['user'][0]->user_date_of_birth);
      $curr_year = date('Y');
      $curr_month = date('m');
      $curr_day = date('d');

      $data['age'] = ($curr_year - $dob_arr[2]) - 1;

      if($curr_month.$curr_day >= $dob_arr[1].$dob_arr[0]){
        $data['age']++;     
      }

    }else{
      $data['age'] = '';
    }

    $re_password = $this->user_model->get_latest_user_password($user_id);
    $graerr_re_psswrd = $re_password->getResultArray();
    $re_password_arr = array_shift($graerr_re_psswrd);

    $data['current_password'] = $re_password_arr['password'];

















if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])):



  $rules = [
    'new_password' => ['label' => 'New Password', 'rules' => ['required', 'trim', 'min_length[8]']   ],
    'confirm_password' => ['label' => 'Confirm Password', 'rules' => ['required', 'trim', 'min_length[8]','matches[new_password]']   ]
  ];




  if($this->validate($rules)){

     $data['validation'] = $this->validator;

      $new_password = $_POST['new_password'] ?? null;
      $confirm_password = $_POST['confirm_password'] ?? null;






      $old_passwords_q = $this->user_model->fetch_user_passwords($user_id);

      foreach ($old_passwords_q->getResultArray() as $row){
        if($row['password'] == $new_password){
          $error_password = 1;
        }
      }




        //$current_password_raw = $_POST['current_password'];
        //$current_password = md5($current_password_raw);

        $static_defaults_q = $this->user_model->select_static_defaults();
        $stat_def_res_arr = $static_defaults_q->getResultArray();
        $static_defaults = array_shift($stat_def_res_arr);

        if($new_password == $confirm_password){         

          $this->user_model->change_user_password($new_password,$user_id,$static_defaults['days_psswrd_exp']);
          //$data['user_password_updated'] = 'Your password is now changed';
          $this->session->setFlashdata('new_pass_msg', 'An email was sent for confirmation. Please sign-in with your new password.');

          $send_to = $data['user'][0]->general_email;
          

            $user_mail = new PHPMailer;
            $user_mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
            $user_mail->Port = 587;    


            $user_mail->setFrom('donot-reply-sojourn@focusshopfit.com.au', 'Sojourn - Accounts');
            $user_mail->addAddress($send_to);    // Add a recipient

            $user_mail->addReplyTo('donot-reply-sojourn@focusshopfit.com.au', 'Sojourn - Accounts');

            $user_mail->isHTML(true);                                  // Set email format to HTML

            $year = date('Y');

            $user_mail->Subject = 'Password Change';
            $user_mail->Body    = "Do not reply in this email.<br /><br />Congratulations!<br /><br />Your New Password is : ****".substr($new_password,4)."<br /><br />&copy; FSF Group ".$year;

            if(!$user_mail->send()) {
              echo 'Message could not be sent.';
              echo 'Mailer Error: ' . $user_mail->ErrorInfo;
            } else {
              redirect('users/account/'.$user_id, 'refresh');
            }
      }







  }else{


    if($error_password == 1){
          $data['error'] = '<p><strong>New Password Error:</strong> This password is already used. Please Try again.</p>';
        }


    $signin_error = '<p class="">Invalid sign-in credentials.</p>';
    $data['validation'] = $this->validator;
}









  endif;




























    if(isset($_POST['is_form_submit'])){

      $res_arr_users = $fetch_user->getResultArray();

      $user_info = array_shift($res_arr_users);


      $first_name = $this->cap_first_word($this->if_set($_POST['first_name'] ));
      $last_name = $this->cap_first_word($this->if_set($_POST['last_name'] ));
      $gender = $this->cap_first_word($this->if_set($_POST['gender']));
      $dob = $this->if_set($_POST['dob']);
      $login_name = $this->if_set($_POST['login_name']);
      $is_offshore = $_POST['is_offshore'];
      $contractor_employee = $_POST['contractor_employee'] ?? 0;
      $site_staff = $_POST['site_staff'];
      $gi_date = $_POST['gi_date'] ?? null;

      $department_raw = $_POST['department'];
      $department_arr = explode('|',$department_raw);

      $department_id = $department_arr[0];

      $focus_raw = $_POST['focus'];
      $focus_arr = explode('|',$focus_raw);
      $focus_id = $focus_arr[0];

      if($this->session->get('is_admin') != 1 ){
        $department_id = $user_info['user_department_id'];
        $focus_id = $user_info['user_focus_company_id'];
      }

      $skype_id = $this->if_set($_POST['skype_id']);
      $skype_password = $_POST['skype_password'];

      $direct_landline = $this->if_set($_POST['direct_landline']);
      $after_hours = $this->if_set($_POST['after_hours']);
      $mobile_number = $this->if_set($_POST['mobile_number']);
      $personal_mobile_number = $this->if_set($_POST['personal_mobile_number']);
      $email = $this->if_set($_POST['email']);
      $personal_email = $this->if_set($_POST['personal_email']);
      $comments = $this->cap_first_word_sentence($this->if_set($_POST['comments']));
      $contact_number_id = $_POST['contact_number_id'];
      $email_id = $_POST['email_id'];
      $user_comments_id = $_POST['user_comments_id'];
 
      if($user_comments_id > 0 && $user_comments_id!=''){
        $this->user_model->update_comments($user_comments_id,$comments);
      }else if($user_comments_id!='' && $comments){
        $user_comments_id = $this->company_m->insert_notes($comments);
      }else{

      }


      $profile_raw = $_POST['profile_raw'];

      $file_upload_arr = array('');
      if(isset($_FILES['profile_photo'])){
        if($_FILES['profile_photo']['name'] != ''){
          $file_upload_raw = $this->_upload_primary_photo('profile_photo','users');
          $file_upload_arr = explode('|',$file_upload_raw);
        }
      }
 

      if($file_upload_arr[0] == 'error'){
        $profile = $profile_raw;
        $data['upload_error'] = $file_upload_arr[1];
      }

      if($file_upload_arr[0] == 'success'){
        $profile = $file_upload_arr[1];
      }else{
        $profile = $profile_raw;
        if(array_key_exists('1',$file_upload_arr)){
          $data['upload_error'] = $file_upload_arr[1];
        }
      }

      if($department_id != 1){
        $this->user_model->update_company_director($user_id,'NULL');
      }

      $supervisor_id = $_POST['supervisor'];
      $is_dummy = $_POST['is_dummy'] ?? 0;

      $this->session->setFlashdata('account_update_msg', 'User account is now updated.');

      $this->user_model->update_user_details($user_id,$login_name,$first_name,$last_name,$skype_id,$skype_password,$gender,$dob,$department_id,$is_offshore,$focus_id,$user_comments_id,$profile,$supervisor_id,$contractor_employee,$site_staff,$gi_date,$is_dummy);

      $this->user_model->update_contact_email($email_id,$email,$contact_number_id,$direct_landline,$mobile_number,$after_hours,$personal_mobile_number,$personal_email);

   //   redirect($this->uri->uri_string(),'refresh');



      return redirect()->to('/users/account/'.$user_id);

      

    }


      $data['page_title'] = 'Account Details: '.$data['user'][0]->user_first_name.' '.$data['user'][0]->user_last_name;


      $data['main_content'] = 'App\Modules\Users\Views\account';
      return $user_id ? view('App\Views\page',$data) : redirect()->to('/users');
    }

  public function leave_details($user_set_id) {


    if(!$this->_is_logged_in() ):
      $this->logout();
      return redirect()->to('/signin');
    endif;

    $user_id = $user_set_id ?? $this->uri->getSegment(3);



    $fetch_user = $this->user_model->fetch_user($user_id);
    $data['user'] = $fetch_user->getResult();

    $fetch_leave_alloc = $this->user_model->fetch_leave_alloc($user_id, '2017');


    if($fetch_leave_alloc->getNumRows() > 0){
      $data['leave_alloc'] = $fetch_leave_alloc->getResult();  
    }else{
      $data['leave_alloc'] = null;
    }

   

    $logged_user_id = $this->session->get('user_id');
    $user_direct_reportee = $this->user_model->get_direct_reports($logged_user_id);
    $arr_reportee = array();

    if($user_direct_reportee->getNumRows() > 0){
      foreach ($user_direct_reportee->getResultArray() as $row){
        array_push($arr_reportee,$row['user_id']);
      }
    }

    $data['direct_reportee'] = $arr_reportee;
    
    $fetch_pending_leaves = $this->user_model->fetch_pending_leaves($user_id);
    $data['pending_leaves'] = $fetch_pending_leaves->getResult();

    $fetch_approved_leaves = $this->user_model->fetch_approved_leaves($user_id);
    $data['approved_leaves'] = $fetch_approved_leaves->getResult();

    $fetch_approved_leaves_all = $this->user_model->fetch_approved_leaves_all($user_id);
    $data['approved_leaves_all'] = $fetch_approved_leaves_all->getResult();

    $fetch_approved_leaves_by_md = $this->user_model->fetch_approved_leaves_by_md($user_id);
    $data['approved_leaves_by_md'] = $fetch_approved_leaves_by_md->getResult();

    $fetch_approved_leaves_by_md_all = $this->user_model->fetch_approved_leaves_by_md_all($user_id);
    $data['approved_leaves_by_md_all'] = $fetch_approved_leaves_by_md_all->getResult();

    $fetch_unapproved_leaves = $this->user_model->fetch_unapproved_leaves($user_id);
    $data['unapproved_leaves'] = $fetch_unapproved_leaves->getResult();

    //$fetch_leave_request = $this->user_model->fetch_leave_request($user_id);
    //$data['leave_req'] = $fetch_leave_request->result();
 
    $data['screen'] = 'Employee Leave Details';
    $data['comp_type'] = 1;

    $data['page_title'] = 'Leave Details: '. $data['user'][0]->user_first_name.' '.$data['user'][0]->user_last_name;
 
  
    $data['main_content'] = 'App\Modules\Users\Views\emp_leave_v';
    return view('App\Views\page',$data);
  }

  public function notify_changed_completion_date($project_id,$project_name=''){

    $q_added_supply = $this->user_model->check_supply_added_user($project_id);
    $supply_list_r = '';


    $mail = new PHPMailer;                                    
    $mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
    $mail->Port = 587;

    $mail->addReplyTo('noreply@focusshopfit.com.au', 'Do Not Reply');
    $mail->isHTML(true);  


    $mail->setFrom('noreply@focusshopfit.com.au', 'Sojourn Client Supply');
    $content = '';



    if($q_added_supply->getNumRows() > 0){
      foreach ($q_added_supply->result() as $supply){


                    $q_expired_notify = $this->user_model->fetch_email_user($supply->added_by_user);
                    $expired_notify = array_shift($q_expired_notify->result_array());

                    $mail->addAddress($expired_notify['general_email']);


    $mail->Subject = 'Project Completion Date Change';


    $content = '<p>Please be advised that the completion date of project <strong>'.$project_id.': '.$project_name.'</strong> has been changed.</p>';
    $content .= '<p>Kindly check and update the "Delivery To Site Date" of your client supply, <strong>'.$supply->supply_name.'</strong></p>';
    $mail->Body = $content.'<br /><br />Sent via Sojourn auto-email service.<br />Please log-in to Sojourn and check the client supply as per details above.<br /><br />&copy; FSF Group '.date('Y').'</p>';
    $mail->send();
        sleep(0.5);

      }
    }

  }


  public function leave_approvals($user_id){
    $fetch_pending_by_superv = $this->user_model->fetch_pending_leaves_by_supervisor_id($user_id);
    $data['pending_by_superv'] = $fetch_pending_by_superv->getResult();
    $data['screen'] = 'For Approval Leaves';
    $data['comp_type'] = 1;
    $data['page_title'] = 'Leave Approvals';
    $data['main_content'] = 'App\Modules\Users\Views\leave_approvals';
    return view('App\Views\page',$data);
  }


  public function fetch_user_access($preset_name = ''){
    $this->clear_apost();

    if($preset_name == ''){
      $ajax_var = $_POST['ajax_var'] ?? null;
      $role_arr = explode('|',$ajax_var);
      $preset_name = $role_arr['1'] ?? null;
    }


    $user_access_q = $this->user_model->fetch_role_access($preset_name);
    $get_res_arr_access = $user_access_q->getResultArray();
    $user_access = array_shift($get_res_arr_access) ?? array();

    echo implode(',', $user_access);

  }


  public function delete_user($user_id){
    $del_user_id = $user_id ?? $this->request->uri->getSegment(3);
    $this->user_model->delete_user($del_user_id);
    return redirect()->to('/users');
  }


  public function update_user_access(){
    $this->clear_apost();
    $user_id = $_POST['user_id_access'];

    if($this->session->get('is_admin') ==  1){
      $is_admin = $_POST['chk_is_peon'];
    }else{
      $fetch_user= $this->user_model->fetch_user($user_id);
      $get_ress_arr_fetch_user = $fetch_user->getResultArray();
      $user_details = array_shift($get_ress_arr_fetch_user);
      $is_admin = $user_details['if_admin'];
    }

    $dashboard = $_POST['dashboard_access'];
    $company = $_POST['company_access'];
    $projects = $_POST['projects_access'];
    $wip = $_POST['wip_access'];
    $purchase_orders = $_POST['purchase_orders_access'];
    $invoice = $_POST['invoice_access'];
    $users = $_POST['users_access'];
    $bulletin_board = $_POST['bulletin_board'];
    $project_schedule = $_POST['project_schedule'];
    $labour_schedule = $_POST['labour_schedule'];
    $company_project = $_POST['company_project'];
    $shopping_center = $_POST['shopping_center'];
    $client_supply = $_POST['client_supply'];

    $site_labour = $_POST['site_labour'];
    $site_labour_app = $_POST['site_labour_app'];
    $quick_quote = $_POST['quick_quote'];
    $quote_deadline = $_POST['quote_deadline'];
    $leave_requests = $_POST['leave_requests'];
    $job_date_access = $_POST['job_date_access'];
    $purchase_order_access = $_POST['purchase_order_access'];
    $progress_report_set = $_POST['progress_report_set'];
    $onboarding_set = $_POST['onboarding_set'];

    $role_raw = $_POST['role'];
    $role_arr = explode('|',$role_raw);
    $role_id = $role_arr[0];
    $incident_report = $_POST['incident_report_access'];

    //echo "$user_id,$is_admin,$dashboard,$company,$projects,$wip,$purchase_orders,$invoice,$users";

    $this->user_model->update_user_access($user_id,$is_admin,$dashboard,$client_supply,$company,$projects,$wip,$purchase_orders,$invoice,$users,$role_id,$bulletin_board,$project_schedule,$labour_schedule,$company_project,$shopping_center,$site_labour,$site_labour_app,$quick_quote,$quote_deadline,$leave_requests,$job_date_access,$purchase_order_access, $progress_report_set, $onboarding_set,$incident_report);
    $this->session->setFlashdata('user_access', 'User Access is now updated.');


      return redirect()->to('/users/account/'.$user_id);
  }


  public function update_projects_default_view(){
    $this->clear_apost();
    $user_id = $_POST['user_id'];
    $projects_load_view = $_POST['projects_load_view'];

    $this->user_model->update_projects_dv($user_id,$projects_load_view);

    //var_dump($companies);
    $this->session->setFlashdata('user_access', 'Default projects landing page is updated.');
    return redirect()->to('/users/account/'.$user_id);
  }


  public function update_menu_order(){
    $user_id = $_POST['user_id'];
    $menu_list_order = $_POST['menu_list_order'];
    $this->session->setFlashdata('user_access', 'Left Navigation Menu is updated.');

    $this->user_model->set_user_navMenu($menu_list_order,$user_id);

    return redirect()->to('/users/account/'.$user_id);
  }

  public function update_projects_pv(){
    $this->clear_apost();
    $user_id = $_POST['user_id'];
    $projects_load_view_personal = $_POST['projects_load_view_personal'];

    $this->user_model->update_projects_pv($user_id,$projects_load_view_personal);

    //var_dump($companies);
    $this->session->setFlashdata('user_access', 'Default projects view personal projects is updated.');
    return redirect()->to('/users/account/'.$user_id);
  }


    public function clear_apost(){
      if($_SERVER['REQUEST_METHOD'] === 'POST'):
        foreach ($_POST as $key => $value) {
          $_POST[$key] = str_replace("'","&apos;",$value);
        }
      endif;
    }


      public function update_leave_alloc($user_id_page){
    $this->clear_apost();

    $annual_manual_entry = $_POST['annual_manual_entry'];
    $personal_manual_entry = $_POST['personal_manual_entry'];
    $checked_sched = $_POST['sched'];
    $no_hrs_of_work = $_POST['no_hrs_of_work'] ?? null;
    $leave_rate_type = $_POST['leave_rate_type'];

    $is_offshore_update = $_POST['is_offshore_update'];

    $leave_alloc = $this->user_model->fetch_leave_alloc($user_id_page);
    $row = $leave_alloc->getRow();

    $current_date = date("d-m-Y");
    $timestamp_current = strtotime($current_date);
    //$timestamp_current = strtotime("2017-06-15");
    $annual_accumulated = $row->annual_accumulated;
    $personal_accumulated = $row->personal_accumulated;
    $last_annual_accumulated = $row->last_annual_accumulated;
    $last_personal_accumulated = $row->last_personal_accumulated;
    $sched_of_work = $row->sched_of_work;

    if (isset($checked_sched)){
      $checked_sched = implode(',', $checked_sched);
    } else {
      $checked_sched = '';
    }

    if ($checked_sched <> $sched_of_work){
      $last_annual_accumulated = $annual_accumulated + $last_annual_accumulated;
      $last_personal_accumulated = $personal_accumulated + $last_personal_accumulated;

      $this->user_model->update_approved_leave_to_inactive($user_id_page);
      $result = $this->user_model->update_leave_alloc_sched($user_id_page, $annual_manual_entry, $personal_manual_entry, $checked_sched, $no_hrs_of_work, $timestamp_current, $last_annual_accumulated, $last_personal_accumulated, $leave_rate_type);
    } else {

      if ($is_offshore_update == '1'){
        $this->user_model->update_earned_offshore('0', '0', date('n'), $user_id_page);
      }

      $this->user_model->update_approved_leave_to_inactive($user_id_page);
      $result = $this->user_model->update_leave_alloc($user_id_page, $annual_manual_entry, $personal_manual_entry, $no_hrs_of_work, $timestamp_current, $leave_rate_type);
    }

    $update_success = 'User account is now updated.';
    $this->session->setFlashdata('total_leave', $update_success);
  //  redirect('/users/account/'.$user_id_page);


      return redirect()->to('/users/account/'.$user_id_page,);


  }





    public function check_user_if_remembered(){
      $remember = $this->session->get('remember');
      echo $remember;
    }

 
    
 


    public function cap_first_word_sentence($str){
    //first we make everything lowercase, and 
    //then make the first letter if the entire string capitalized
      $str = ucfirst(strtolower($str));   
    


      return preg_replace_callback('#^((<(.+?)>)*)(.*?)$#', function ($c) {
            return $c[1].ucfirst(array_pop($c));
    }, $str);



    }


  public function if_set($val){
    //echo $val.'<br />';
    if(isset($val)){
      return ascii_to_entities($val);
    }else{
      return NULL;
    }
  }



  public function cap_first_word($str){
    return ucwords(strtolower($str));
  }



  public function calc_leave_points(){

    $leave_alloc_all = $this->user_model->fetch_leave_alloc_all();
    $leave_alloc_all = $leave_alloc_all->getResult();

    foreach ($leave_alloc_all as $row) {

      $user_id = $row->user_id;     
      $annual_manual_entry = $row->annual_manual_entry;     
      $personal_manual_entry = $row->personal_manual_entry;
      $last_annual_accumulated = $row->annual_accumulated;      
      $last_personal_accumulated = $row->personal_accumulated;
      $sched_work = $row->sched_of_work;
      $no_hrs_of_work = $row->no_hrs_of_work;
      $leave_rate_type = $row->leave_rate_type;
      $date_log = date('Y-m-d', $row->date_log);
      $today = date('Y-m-d');
      $day_yesterday = date('Y-m-d',strtotime("-1 days"));
      //$day_yesterday = date('Y-m-d',strtotime("2017-06-14"));

      $last_week_update_local = $row->last_week_update_local;
      
      $annual_consumed = $this->user_model->get_total_leave_annual($user_id);
      $annual_consumed = $annual_consumed->getRow();

      $personal_consumed = $this->user_model->get_total_leave_personal($user_id);
      $personal_consumed = $personal_consumed->getRow();

      $q_annual_holidays = $this->user_model->get_annual_holidays($user_id);
      $annual_holidays = $q_annual_holidays->getRow();

      $q_sick_holidays = $this->user_model->get_sick_holidays($user_id);
      $sick_holidays = $q_sick_holidays->getRow(); 

      $q_fetch_leave_total_rates = $this->user_model->fetch_leave_total_rates($leave_rate_type);
      $fetch_leave_total_rates = $q_fetch_leave_total_rates->getResult();  

      $q_count_sched_of_work = $this->user_model->count_sched_of_work($user_id);
      $count_sched_of_work = $q_count_sched_of_work->getResult();  

      $fetch_user = $this->user_model->fetch_user($user_id);
      $data['user'] = $fetch_user->getResult();

      if( $data['user'][0]->is_offshore == 0){  

        $date_today = date('Y-m-d');
        $date = date_create($date_today);
        date_modify($date, '-1 week');
        $last_week = date_format($date,"W");
        $current_week = date('W');
        $work_sched_count = $count_sched_of_work[0]->total;

        // $current_week = '1';

        if ($leave_rate_type == 1){

          if ($last_week_update_local == 0){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual = $annual_leave_rate_percentage + $annual_manual_entry;
            $total_personal = $personal_leave_rate_percentage + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            // $total_annual_converted = $total_annual_final * $no_hrs_of_work;
            // $total_personal_converted = $total_personal_final * $no_hrs_of_work;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($annual_leave_rate_percentage, $personal_leave_rate_percentage, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          } 

          if ($last_week_update_local != 0 && $last_week_update_local != 52 && $last_week_update_local < $current_week) {

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual_earnings = $annual_leave_rate_percentage + $last_annual_accumulated;
            $total_personal_earnings = $personal_leave_rate_percentage + $last_personal_accumulated;

            $total_annual = $total_annual_earnings + $annual_manual_entry;
            $total_personal = $total_personal_earnings + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($total_annual_earnings, $total_personal_earnings, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }

          if ($current_week == 1 && $last_week_update_local == 52){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual_earnings = $annual_leave_rate_percentage + $last_annual_accumulated;
            $total_personal_earnings = $personal_leave_rate_percentage + $last_personal_accumulated;

            $total_annual = $total_annual_earnings + $annual_manual_entry;
            $total_personal = $total_personal_earnings + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($total_annual_earnings, $total_personal_earnings, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }

          
          if ($last_week_update_local == $current_week){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_plus_rate = $annual_manual_entry + $last_annual_accumulated;
            $personal_leave_plus_rate = $personal_manual_entry  + $last_personal_accumulated;

            $total_annual_final = $annual_leave_plus_rate - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $personal_leave_plus_rate - $personal_consumed->used_personal + $sick_holidays_count;

            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }
        }

        if ($leave_rate_type == 2){

          if ($last_week_update_local == 0){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual = $annual_leave_rate_percentage + $annual_manual_entry;
            $total_personal = $personal_leave_rate_percentage + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            // $total_annual_converted = $total_annual_final * $no_hrs_of_work;
            // $total_personal_converted = $total_personal_final * $no_hrs_of_work;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($annual_leave_rate_percentage, $personal_leave_rate_percentage, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          } 

          if ($last_week_update_local != 0 && $last_week_update_local != 52 && $last_week_update_local < $current_week) {

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual_earnings = $annual_leave_rate_percentage + $last_annual_accumulated;
            $total_personal_earnings = $personal_leave_rate_percentage + $last_personal_accumulated;

            $total_annual = $total_annual_earnings + $annual_manual_entry;
            $total_personal = $total_personal_earnings + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($total_annual_earnings, $total_personal_earnings, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }

          if ($current_week == 1 && $last_week_update_local == 52){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_rate = $fetch_leave_total_rates[0]->annual_leave / 52;
            $personal_leave_rate = $fetch_leave_total_rates[0]->personal_leave / 52;

            $annual_leave_rate_rounded = round($annual_leave_rate, 6);
            $personal_leave_rate_rounded = round($personal_leave_rate, 6);

            switch ($work_sched_count) {
              case '1':
                
                $percentage = 20;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '2':
                
                $percentage = 40;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '3':
                
                $percentage = 60;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '4':
                
                $percentage = 80;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              case '5':
                
                $percentage = 100;
                $annual_leave_rate_percentage = ($percentage / 100) * $annual_leave_rate_rounded;
                $personal_leave_rate_percentage = ($percentage / 100) * $personal_leave_rate_rounded;

                break;
              default:

                $annual_leave_rate_percentage = 0;
                $personal_leave_rate_percentage = 0;

                break;
            }

            $total_annual_earnings = $annual_leave_rate_percentage + $last_annual_accumulated;
            $total_personal_earnings = $personal_leave_rate_percentage + $last_personal_accumulated;

            $total_annual = $total_annual_earnings + $annual_manual_entry;
            $total_personal = $total_personal_earnings + $personal_manual_entry;

            $total_annual_final = $total_annual - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $total_personal - $personal_consumed->used_personal + $sick_holidays_count;

            $this->user_model->update_current_week($current_week, $user_id);
            $this->user_model->update_earned_points($total_annual_earnings, $total_personal_earnings, $user_id);
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }

          if ($last_week_update_local == $current_week){

            $annual_holidays_count = $annual_holidays->holidays;
            $sick_holidays_count = $sick_holidays->holidays;

            $annual_leave_plus_rate = $annual_manual_entry + $last_annual_accumulated;
            $personal_leave_plus_rate = $personal_manual_entry  + $last_personal_accumulated;
            
            $total_annual_final = $annual_leave_plus_rate - $annual_consumed->used_annual + $annual_holidays_count;
            $total_personal_final = $personal_leave_plus_rate - $personal_consumed->used_personal + $sick_holidays_count;
            $this->user_model->update_total_leave(round($total_annual_final, 2), round($total_personal_final, 2), $user_id);

          }

        }

      } else {
        // echo '<script>alert("offshore");</script>';

        if ($leave_rate_type == 3){

          $last_month_update_offshore = $row->last_month_update_offshore;
          $annual_earned_offshore = $row->annual_earned_offshore;     
          $personal_earned_offshore = $row->personal_earned_offshore;

          $annual_holidays_count = $annual_holidays->holidays;
          $sick_holidays_count = $sick_holidays->holidays;

          $annual_leave_rate = round($fetch_leave_total_rates[0]->annual_leave / 12, '2');
          $personal_leave_rate = round($fetch_leave_total_rates[0]->personal_leave / 12, '2');

          // print_r($user_id.'|'.$annual_holidays->ph_holidays.'<br><br>');

          // $date_today = date('2020-03-01');
          $date_today = date('Y-m-d');
          $date = date_create($date_today);
          date_modify($date, '-1 month');
          $last_month = date_format($date,"m");

          $current_month = date('m');
          // $current_month = '1';

          if ($last_month_update_offshore == 0){
            $total_annual_points = $annual_manual_entry + $annual_earned_offshore;
            $total_annual = $total_annual_points - $annual_consumed->used_annual + $annual_holidays_count;

            $total_personal_points = $personal_manual_entry + $personal_earned_offshore;
            $total_personal = $total_personal_points - $personal_consumed->used_personal + $sick_holidays_count;

            if (!empty($sched_work)){
              $this->user_model->update_total_leave($total_annual, $total_personal, $user_id);
              $this->user_model->update_earned_offshore(0, 0, $current_month, $user_id);
            }
          } else {

            if ($current_month == '01'){
              if (!empty($sched_work)){
              //  $this->user_model->update_starting_leave(0, 0, $user_id);  // edited by Jervy 1/28/2021 not sure if bug but this keeps running and reseting values
                $this->user_model->update_total_leave($annual_leave_rate, $personal_leave_rate, $user_id);
                $this->user_model->update_earned_offshore($annual_leave_rate, $personal_leave_rate, $current_month, $user_id);
              }
            }

            if (ltrim($current_month, '0') != $last_month_update_offshore){
              if ($last_month < $current_month){
                $annual_earned_offshore = $annual_earned_offshore + $annual_leave_rate;
                $personal_earned_offshore = $personal_earned_offshore + $personal_leave_rate;

                $total_annual_points = $annual_manual_entry + $annual_earned_offshore;
                $total_annual = $total_annual_points - $annual_consumed->used_annual + $annual_holidays_count;

                $total_personal_points = $personal_manual_entry + $personal_earned_offshore;
                $total_personal = $total_personal_points - $personal_consumed->used_personal + $sick_holidays_count;

                if (!empty($sched_work)){
                  $this->user_model->update_total_leave($total_annual, $total_personal, $user_id);
                  $this->user_model->update_earned_offshore($annual_earned_offshore, $personal_earned_offshore, $current_month, $user_id);
                }
              } else {
                $total_annual_points = $annual_manual_entry + $annual_earned_offshore;
                $total_annual = $total_annual_points - $annual_consumed->used_annual + $annual_holidays_count;

                $total_personal_points = $personal_manual_entry + $personal_earned_offshore;
                $total_personal = $total_personal_points - $personal_consumed->used_personal + $sick_holidays_count;

                if (!empty($sched_work)){
                  $this->user_model->update_total_leave($total_annual, $total_personal, $user_id);
                }
              }
            } else {
  
              $total_annual_points = $annual_manual_entry + $annual_earned_offshore;
              $total_annual = $total_annual_points - $annual_consumed->used_annual + $annual_holidays_count;

              $total_personal_points = $personal_manual_entry + $personal_earned_offshore;
              $total_personal = $total_personal_points - $personal_consumed->used_personal + $sick_holidays_count;

              if (!empty($sched_work)){
                $this->user_model->update_total_leave($total_annual, $total_personal, $user_id);
              }
            }
          }
        }
      }
    }
    
  }








}