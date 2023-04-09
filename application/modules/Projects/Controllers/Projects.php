<?php
// module created by Jervy 23-9-2022
namespace App\Modules\Projects\Controllers;

use App\Controllers\BaseController;

use App\Modules\Projects\Models\Projects_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Etc\Controllers\Etc;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Invoice\Controllers\Invoice;
use App\Modules\Invoice\Models\Invoice_m;

use App\Modules\Reports\Models\Reports_m;

use App\Modules\Schedule\Models\Schedule_m;

use App\Modules\Project_schedule\Models\Project_schedule_m;

use App\Modules\Induction_health_safety\Models\Induction_health_safety_m;

use App\Modules\Works\Models\Works_m;

class Projects extends BaseController {

  private $projects_m;

  function __construct(){
    $this->projects_m = new Projects_m();
    $this->session = \Config\Services::session();
  }


  public function index(){
    $this->users = new Users();
    $this->user_model = new Users_m();
    $this->company_m = new Company_m();
    $this->admin_m = new Admin_m();
    $this->etc = new Etc();

    $this->projects_m->auto_log_out_site_login();
    $this->users->_check_user_access('projects',1);


    $data['clients_list'] = $this->company_m->display_company_by_type(1);
    $data['users'] = $this->user_model->fetch_user();
    $data['pms'] = $this->user_model->fetch_pms_year(date("Y"));





    if(isset($_GET['fompj'])){

      $user_id = $this->session->get('user_id');
      $company_id = $_GET['fompj'];
      $this->user_model->update_set_project_view($user_id,$company_id);

      $fompj['set_view_company_project'] = $company_id;
      $this->session->set($fompj);
    }else{
      $fompj['set_view_company_project'] = $this->session->get('set_view_company_project');
      $this->session->set($fompj);
    }




    $focus = $this->admin_m->fetch_all_company_focus(" AND `company_details`.`company_id` != '4' ");
    $data['focus'] = $focus->getResult();

    $comp_id = $this->session->get("set_view_company_project");


    if($comp_id != ''){
      $custom_fcom_query = " AND `project`.`focus_company_id` = '".$comp_id."' ";
    }else{
      $custom_fcom_query = "   ";
    }

    $q_comp_f = $this->projects_m->list_focus_company_main($custom_fcom_query);
    $getResultArray = $q_comp_f->getResultArray();
    $focus_comp_main = array_shift($getResultArray);


    if(isset($_GET['fompj'])){
      $data['focus_id_main_display'] = $_GET['fompj'];
    }else{
      $data['focus_id_main_display'] = $focus_comp_main['focus_company_id'] ?? null;
    }


    $user_id = $this->session->get('user_id');

    if($user_id == '72'){
      return redirect()->to('/dashboard');
    }

    $this->etc->remind_hr_left();

    $data['page_title'] = 'Project List';
    $data['screen'] = 'Projects';

    $data['main_content'] = 'App\Modules\Projects\Views\projects_v';
    return view('App\Views\page',$data);
  }

  public function process_feedback_unaccepted($project_id){
    $projects_q = $this->projects_m->list_works_for_feedback($project_id);

    foreach ($projects_q->getResult() as $project) {
      $this->etc = new Etc();
      $this->etc->set_email_notif($project->works_id);
    }

  }

  public function list_invoiced_items($project_id,$project_total,$variation_total){
    $this->invoice = new Invoice();
    $this->invoice->list_invoiced_items($project_id,$project_total,$variation_total);
  }

  public function list_users_pr_images(){
    $this->user_model = new Users_m();
    $fetch_project_manager_q = $this->user_model->fetch_user_by_role_combine(3, 20);
    foreach ($fetch_project_manager_q->getResult() as $row){

      if ($row->user_id != '29'){
        echo '<option value="'.strtolower(str_replace(' ','_', $row->user_first_name.' '.$row->user_last_name)).'" >'.$row->user_first_name.'</option>';
      }
    }
  }

  public function list_recent_pr_images($limit=''){
    $fetch_recent_pr_images_q = $this->projects_m->fetch_recent_pr_images($limit);
    foreach ($fetch_recent_pr_images_q->getResult() as $row){
      echo '<li class="list_pr_img_'.strtolower(str_replace(' ','_', $row->pm_name)).'" ><div><a href="'.site_url().'projects/progress_reports/'.$row->project_id.'" class="news-item-title"><strong>'.$row->project_id.'</strong></a> '.$row->project_name.'  <br /><i class="fa fa-user-circle"></i> '.$row->pm_name.'<br /> <i class="fa fa-users"></i>  '.$row->pa_name.'  </div><br/ ></li>';
    }
  }

  public function list_users_removed_jobdate(){
    $fetch_removed_jobdates_prj_q = $this->projects_m->fetch_users_remove_job_date();
    foreach ($fetch_removed_jobdates_prj_q->getResult() as $prj_log){
      echo '<option value="'.strtolower(str_replace(' ','_', $prj_log->user_name_log)).'" >'.$prj_log->user_first_name.'</option>';
    }
  }

  public function list_removed_jobdate_prj($limit=''){
    $fetch_removed_jobdates_prj_q = $this->projects_m->fetch_removed_jobdates_prj($limit);
    foreach ($fetch_removed_jobdates_prj_q->getResult() as $prj_log){
      echo '<li class="list_rem_user_'.strtolower(str_replace(' ','_', $prj_log->user_name_log)).'" ><div><a href="'.site_url().'projects/view/'.$prj_log->project_id.'" class="news-item-title"><strong>'.$prj_log->project_id.'</strong></a> '.$prj_log->project_name.'  <p class="news-item-preview  tooltip-enabled" title="" data-html="true" data-placement="left" data-original-title="'.$prj_log->company_name.'<br />'.$prj_log->pm_name.'<br />$'.number_format($prj_log->project_total+$prj_log->variation_total,2).'<br />'.$prj_log->date_site_commencement.' - '.$prj_log->date_site_finish.'">  <i class="fa fa-calendar"></i>  '.$prj_log->user_name_log.'  </p></div></li>';
    }
  }

  public function save_invoice_comments(){

    if(isset($_POST['invoice_comments'])){

      $invoice_comments = $_POST['invoice_comments'];
      $prj_id = $_POST['prj_id'];
      $include_invoice_comments = $_POST['include_invoice_comments'];

      $current_url = $_POST['current_tab'];
      $current_tab = substr($current_url, strrpos($current_url, '#' )+1);

      $this->projects_m->add_invoice_comment($prj_id,$invoice_comments,$include_invoice_comments);
      // redirect('/projects/view/'.$prj_id.'?submit_invoice='.$prj_id);
      
      return redirect()->to('/projects/view/'.$prj_id.'?tab=invoice');

    }else{
      return redirect()->to('/projects');
    }
  }

  public function list_all_brands($form='table'){
    $brands_list = $this->projects_m->fetch_brands();
    
    if($form=='table'){
      foreach ($brands_list->getResult() as $row){
        echo '<tr><td><span id="brnd_name_'.$row->brand_id.'">'.$row->brand_name.'</span> <input type="text" id="edt_brnd_inpt_'.$row->brand_id.'"  style="width: 80%; display:none; float: left;" class="brand_name_edit form-control" value="'.$row->brand_name.'" />';
        echo '<button id="edt_'.$row->brand_id.'" class="btn pull-right btn-info btn-sm pad-5 m-left-5" style="padding-right: 1px;" onclick="update_brand(this)"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></button>';
                echo '<button id="del_'.$row->brand_id.'" class="btn pull-right btn-danger btn-sm pad-5" onclick="delete_brand(this)"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>';
                if($row->has_brand_logo == 1){
                  echo '<button id="view_'.$row->brand_id.'" class="btn pull-right btn-success btn-sm pad-5 m-right-5" onclick="view_brand(this)" title = "View Brand Logo"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></button>';
                }else{
                  echo '<button id="view_'.$row->brand_id.'" class="btn pull-right btn-success btn-sm pad-5 m-right-5" onclick="view_brand(this)"  title = "Upload Brand Logo"><i class="fa fa-upload fa-lg" aria-hidden="true"></i></button>';
                }
                echo '<button id="save_'.$row->brand_id.'" class="btn pull-right btn-success btn-sm pad-5" onclick="edit_save(this)"  style=" display:none; margin: 0px 5px;"><i class="fa fa-save fa-lg" aria-hidden="true"></i></button></td></tr>';
      }
    }


    if($form=='select'){
      foreach ($brands_list->getResult() as $row){
        if( $row->brand_name != 'Other'){
          echo '<option value="'.$row->brand_id.'" >'.$row->brand_name.'</option>';
        }
      }
      echo '<option value="71" >Other</option>';
    }   
  }
/*
  public function read_csv_logs(){
    $time_Stamp = time();
    $filename = 'projects_removed_job_date_'.$time_Stamp.'.csv';
    $fetch_removed_jobdates_prj_q = $this->projects_m->fetch_removed_jobdates_prj_csv_report();

    $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($fetch_removed_jobdates_prj_q, $delimiter, $newline);
        force_download($filename, $data);
    
  }
*/

  public function rem_commnt($comm_id){
    if(isset($comm_id) && $comm_id != '' ){
      $this->projects_m->erase_comment($comm_id);
    }
    echo '<script>window.close();</script>';
  }

  public function add(){
    $this->users = new Users();
    $this->user_model = new Users_m();

    $this->admin_m = new Admin_m();

    $this->company = new Company();
    $this->company_m = new Company_m();

    $this->project_schedule_m = new Project_schedule_m();
    $this->schedule_m = new Schedule_m();

    $this->works_m = new Works_m();
    $this->induction_health_safety_m = new Induction_health_safety_m();

    $this->users->_check_user_access('projects',2);

    if($this->session->get('company_project') == 1){
      return redirect()->to('/projects/add_company_project/');
    }

    $rules = array();
    $data = array();

    $client_type = $this->request->getPost('client_type');

    $defaults_raw = $this->admin_m->latest_system_default();
    $defaults = $defaults_raw->getResult();
    $defaults_id = $defaults[0]->defaults_id;
    $admin_default_id = $defaults[0]->admin_default_id;

    $admin_defaults_q = $this->admin_m->fetch_admin_defaults($admin_default_id);
    $getResultArray = $admin_defaults_q->getResultArray();
    $admin_defaults = array_shift($getResultArray);
    $days_quote_deadline = $admin_defaults['days_quote_deadline'];

    $all_company_list = $this->company_m->fetch_all_company_type_id('1');

    if($all_company_list->getNumRows() > 0){
      $data['all_company_list'] = $all_company_list->getResult();
    }

    $data['main_content'] = 'App\Modules\Projects\Views\projects_add';
    $data['page_title'] = 'Add New Project';
    $data['screen'] = 'Add New Project';

    $all_aud_states = $this->company_m->fetch_all_states();
    $data['all_aud_states'] = $all_aud_states->getResult();

    $focus = $this->admin_m->fetch_all_company_focus();
    $data['focus'] = $focus->getResult();

    $project_manager = $this->user_model->fetch_user_by_role(3);
    $data['project_manager'] = $project_manager->getResult();

    $account_manager = $this->user_model->fetch_user_by_role(20);
    $data['account_manager'] = $account_manager->getResult();

    $maintenance_administrator = $this->user_model->fetch_user_by_role(7);
    $data['maintenance_administrator'] = $maintenance_administrator->getResult();

    $project_administrator = $this->user_model->fetch_user_by_role(2);
    $data['project_administrator'] = $project_administrator->getResult();


    $shopping_center = $this->projects_m->fetch_shopping_center();
    $data['shopping_center'] = $shopping_center->getResult();

    $estimator = $this->user_model->fetch_user_by_role(8);
    $data['estimator'] = $estimator->getResult();

    $lead_hand = $this->user_model->fetch_user_by_role(15);
    $data['lead_hand'] = $lead_hand->getResult();

    $data['all_projects'] = $this->projects_m->display_all_projects();

    $company_project_item = array();
    foreach ($data['all_projects']->getResultArray() as $row){
      $company_project_item[$row['company_id']] = $row['company_name'];
    }
    asort($company_project_item);
    $data['all_company_project'] = $company_project_item;

    $q_warranty_categories = $this->projects_m->fetch_warranty_categories();
    $getResultArray = $q_warranty_categories->getResultArray();
    $data['warranty_categories'] = array_shift($getResultArray);


//|max_length[255]',

    $rules[ 'project_name' ] = ['project_name' => 'Project Name', 'rules' => ['required','trim','max_length[35]'] ];
    $rules[ 'site_start' ] = ['site_start' => 'Site Start', 'rules' => ['required','trim'] ];
    $rules[ 'site_finish' ] = ['site_finish' => 'Site Finish', 'rules' => ['required','trim'] ];
    $rules[ 'job_type' ] = ['job_type' => 'Job Type', 'rules' => ['required','trim'] ];
    $rules[ 'brand_name' ] = ['brand_name' => 'Brand', 'rules' => ['required','trim'] ];
    $rules[ 'job_category' ] = ['job_category' => 'Job Category', 'rules' => ['required','trim'] ];
    $rules[ 'project_date' ] = ['project_date' => 'Project Date', 'rules' => ['required','trim'] ];


    if( $this->request->getPost('is_shopping_center') != 1){
      $rules[ 'street' ] = ['street' => 'Site Street', 'rules' => ['required','trim'] ];
      $rules[ 'suburb_a' ] = ['suburb_a' => 'Site Project Address Suburb', 'rules' => ['required','trim'] ];
      $rules[ 'state_a' ] = ['state_a' => 'Site State', 'rules' => ['required','trim'] ];
      $rules[ 'postcode_a' ] = ['postcode_a' => 'Site Postcode', 'rules' => ['required','trim'] ];
    }else{
      $rules[ 'shop_tenancy_number' ] = ['shop_tenancy_number' => 'Site Shop/Tenancy Number', 'rules' => ['required','trim'] ];
      $rules[ 'brand_shopping_center' ] = ['brand_shopping_center' => 'Site Brand/Shopping Center', 'rules' => ['required','trim'] ];
    }

    if($client_type == 0){
      $rules[ 'street_b' ] = ['street_b' => 'Invoice Street', 'rules' => ['required','trim'] ];
      $rules[ 'suburb_b' ] = ['suburb_b' => 'Invoice Address Suburb', 'rules' => ['required','trim'] ];
      $rules[ 'state_b' ] = ['state_b' => 'Invoice State', 'rules' => ['required','trim'] ];
      $rules[ 'postcode_b' ] = ['postcode_b' => 'Invoice Postcode', 'rules' => ['required','trim'] ];
    }

    $rules[ 'project_manager' ] = ['project_manager' => 'Project Manager', 'rules' => ['required','trim'] ];
    $rules[ 'project_admin' ] = ['project_admin' => 'Project Admin', 'rules' => ['required','trim'] ];
    $rules[ 'estimator' ] = ['estimator' => 'Estimator', 'rules' => ['required','trim'] ];
    
    if($client_type == 0){
      $rules[ 'company_prg' ] = ['company_prg' => 'Company Client', 'rules' => ['required','trim'] ];
      $rules[ 'contact_person' ] = ['contact_person' => 'Contact Person', 'rules' => ['required','trim'] ];
    }

    $rules[ 'install_hrs' ] = ['install_hrs' => 'Site Hours', 'rules' => ['required','trim'] ];
    $rules[ 'project_total' ] = ['project_total' => 'Project Estimate', 'rules' => ['required','trim'] ];
    $rules[ 'labour_hrs_estimate' ] = ['labour_hrs_estimate' => 'Site Labour Estimate', 'rules' => ['required','trim'] ];
    $rules[ 'project_markup' ] = ['project_markup' => 'Project Markup', 'rules' => ['required','trim'] ];
    $rules[ 'leading_hand' ] = ['leading_hand' => 'Leading Hand', 'rules' => ['trim'] ];
    $rules[ 'proj_joinery_user' ] = ['proj_joinery_user' => 'Joinery Personel', 'rules' => ['required','trim'] ];

    if( $this->request->getPost('job_category') != 'Maintenance' && $this->request->getPost('job_category') != 'Minor Works' && $this->request->getPost('job_category') != 'Strip Out' && $this->request->getPost('job_category') != 'Design Works' ){
      $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['required','trim','greater_than[0]'] ];
    }else{
      $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['required','trim'] ];
    }

    if ($this->request->getPost('leading_hand') == 0 && $this->request->getPost('leading_hand') != ''){
      $rules[ 'lh_name' ] = ['lh_name' => 'Leading Hand Full Name', 'rules' => ['required','trim'] ];
      $rules[ 'lh_mobile_no' ] = ['lh_mobile_no' => 'Leading Hand Mobile No.', 'rules' => ['required','trim'] ];
    }


    $project_area = $this->request->getPost('project_area') ?? 0.00;
    $rounded_project_area = round(floatval($project_area),2);


    // validation checks here
    if($_SERVER['REQUEST_METHOD'] === 'POST'): // on load if form is usbmitted

      if($this->validate($rules)): // form is valid and process form
      // proceed insert and redirect here

        $focus_id = $this->request->getPost('focus');
        $project_name = $this->request->getPost('project_name');
        $client_po = $this->request->getPost('client_po');
        $job_type = $this->request->getPost('job_type');
        $job_category = $this->request->getPost('job_category');
        $project_date = $this->request->getPost('project_date');
        $job_date = $this->request->getPost('job_date');
        $site_start = $this->request->getPost('site_start');
        $site_finish = $this->request->getPost('site_finish');
        $brand_name = $this->request->getPost('brand_name');

        if($job_date != ''){
          $is_wip = 1;
        }else{
          $is_wip = 0;
        }

        $data['unit_level'] = $this->company->if_set($this->request->getPost('unit_level'));
        $data['unit_number'] = $this->company->if_set($this->request->getPost('unit_number'));
        $data['street'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street')));
        $data['postcode_a'] = $this->company->if_set($this->request->getPost('postcode_a'));

        $state_a_arr = explode('|', $this->request->getPost('state_a'));
        $num = count($state_a_arr);
        if($num > 1){
          $data['state_a'] = $state_a_arr[3];
        }

        $suburb_a_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
        $data['suburb_a'] = strtoupper($suburb_a_ar[0]);

        if($client_type == 0){
          $data['pobox'] = $this->company->if_set($this->request->getPost('pobox'));
          $data['unit_level_b'] = $this->company->if_set($this->request->getPost('unit_level_b'));
          $data['number_b'] = $this->company->if_set($this->request->getPost('number_b'));     
          $data['street_b'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street_b')));
          $data['postcode_b'] = $this->company->if_set($this->request->getPost('postcode_b')); 

          $state_b_arr = explode('|', $this->request->getPost('state_b'));
          $data['state_b'] = $state_b_arr[3];

          $suburb_b_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_b')));
          $data['suburb_b'] = strtoupper($suburb_b_ar[0]);
        }else{
          $data['pobox'] = "";
          $data['unit_level_b'] = $this->company->if_set($this->request->getPost('unit_level'));
          $data['number_b'] = $this->company->if_set($this->request->getPost('unit_number'));      
          $data['street_b'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street')));
          $data['postcode_b'] = $this->company->if_set($this->request->getPost('postcode_a'));

          ///$state_b_arr = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
          $data['state_b'] = $data['state_a'];

          $suburb_b_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
          $data['suburb_b'] = strtoupper($suburb_b_ar[0]);  
        }

        $project_manager_id = $this->request->getPost('project_manager');
        $project_admin_id = $this->request->getPost('project_admin');
        $project_estiamator_id = $this->request->getPost('estimator');
        $project_leading_hand_id = $this->request->getPost('leading_hand');
        $proj_joinery_user = $this->request->getPost('proj_joinery_user');

        if ($project_leading_hand_id == 0){
          $project_leading_hand_full_name = $this->request->getPost('lh_name');
          $project_leading_hand_mobile_no = $this->request->getPost('lh_mobile_no');
        }

        if($client_type == 0){
          $company_prg_arr =  explode('|',$this->request->getPost('company_prg'));
          $client_id = $company_prg_arr[1];
          $company_name = $company_prg_arr[0];
          $contact_person_id = $this->request->getPost('contact_person');

        }else{

          $pending_comp_id = $this->request->getPost('pending_comp_id');
          $company_prg_arr =  explode('/',$pending_comp_id);
          $client_id = $company_prg_arr[0];
          $company_name = $company_prg_arr[1];
          $contact_person_id = 0;
        }


        $project_total = str_replace (',','', $this->request->getPost('project_total') );

        $install_hrs = $this->request->getPost('install_hrs');
        $project_markup = $this->request->getPost('project_markup');
        $project_area = $this->request->getPost('project_area');
        $comments = $this->request->getPost('comments');
        $project_status_id = 1;

        $copy_work_project_id = $this->request->getPost('copy_work_project_id');
        $include_work_estimate = $this->request->getPost('include_work_estimate');
        $shop_tenancy_number = $this->request->getPost('shop_tenancy_number'); 
        $focus_user_id = $this->session->get('user_id');

        $labour_hrs_estimate = $this->request->getPost('labour_hrs_estimate');
        $is_shopping_center = $this->request->getPost('is_shopping_center');
        $project_notes_id = $this->company_m->insert_notes($comments);

        $is_double_time = $this->request->getPost('is_double_time');
        $cc_pm_raw = $this->request->getPost('client_contact_project_manager');

        $cc_pm = ($cc_pm_raw == 0 ? $project_manager_id : $cc_pm_raw);

        if($is_shopping_center == 1){
          $sc_address_id = $this->request->getPost('brand_shopping_center');
          $site_address_id = $this->projects_m->duplicate_address_row($sc_address_id);

        }else{

          $general_address_id_result_a = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_a'],$data['suburb_a']);
          foreach ($general_address_id_result_a->getResult() as $general_address_id_a){
            $general_address_a = $general_address_id_a->general_address_id;
          }
          $site_address_id = $this->company_m->insert_address_detail($data['street'],$general_address_a,$data['unit_level'],$data['unit_number']);
        }

        $general_address_id_result_b = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_b'],$data['suburb_b']);
        foreach ($general_address_id_result_b->getResult() as $general_address_id_b){
          $general_address_b = $general_address_id_b->general_address_id;
        }

        $invoice_address_id = $this->company_m->insert_address_detail($data['street_b'],$general_address_b,$data['unit_level_b'],$data['number_b'],$data['pobox']);


        $formated_start_date = str_replace("/","-",$site_start);
        $date_quote_deadline =  date('d/m/Y', strtotime("$formated_start_date -$days_quote_deadline days"));

        $check_date_quote_deadline = date("w", strtotime( str_replace("/","-",$date_quote_deadline) ));

        if($check_date_quote_deadline == 0){
          $new_days_quote_deadline = $days_quote_deadline + 2;
          $date_quote_deadline =  date('d/m/Y', strtotime("$formated_start_date -$new_days_quote_deadline days"));
        }

        if($check_date_quote_deadline == 6){
          $new_days_quote_deadline = $days_quote_deadline + 1;
          $date_quote_deadline =  date('d/m/Y', strtotime("$formated_start_date -$new_days_quote_deadline days"));
        }

        $inserted_project_id = $this->projects_m->insert_new_project($project_name, $project_date, $contact_person_id, $project_total, $job_date,$brand_name, $is_wip, $client_po, $site_start, $site_finish, $job_category, $job_type, $focus_user_id ,$focus_id, $project_manager_id, $project_admin_id, $project_estiamator_id,$site_address_id, $invoice_address_id, $project_notes_id, $project_markup,$project_status_id, $client_id, $install_hrs, $project_area, $is_double_time, $labour_hrs_estimate, $shop_tenancy_number,$defaults_id,$cc_pm,$date_quote_deadline,$proj_joinery_user,$client_type);


  //      echo '<pre>';var_dump( $inserted_project_id );echo '</pre>'; 



//=============== Labour Schedule =============

        $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
        foreach ($admin_defaults->getResult() as $row){
          $labour_sched_categories = $row->labour_sched_categories;
        }

        $category_exist = 0;
        $labour_sched_categories = explode(',',$labour_sched_categories);
        foreach ($labour_sched_categories as &$value) {
          if($job_category == $value){
            $category_exist = 1;
          }
        }


        if($category_exist == 0){
          $labour_hours = 0;
          if($install_hrs == 0){
            $labour_hours = $labour_hrs_estimate;
          }else{
            $labour_hours = $install_hrs;
          }

          $date = str_replace('/', '-', $site_start);
          $labour_start_date = date('Y-m-d', strtotime($date));

          $date = str_replace('/', '-', $site_finish);
          $labour_end_date = date('Y-m-d', strtotime($date));

          $date1_ts = strtotime($labour_start_date);
          $date2_ts = strtotime($labour_end_date);
          $diff = $date2_ts - $date1_ts;
          $total_days = round($diff / 86400) + 1;

          $hrs_per_day = $labour_hours / $total_days;

          $loop_date = $labour_start_date;
          while (strtotime($loop_date) <= strtotime($labour_end_date)) {
            $this->schedule_m->insert_labour_schedule($inserted_project_id,$loop_date,$hrs_per_day);
            $loop_date = date ("Y-m-d", strtotime("+1 days", strtotime($loop_date)));
            
          }
        }

//=============== Labour Schedule =============



        if($is_shopping_center == 1){
          $selected_shopping_center_raw = $this->request->getPost('selected_shopping_center_detail'); 
          $selected_shopping_center_arr = explode(',', $selected_shopping_center_raw);

          $shop_name = $selected_shopping_center_arr[0];
          $this->projects_m->set_shop_name($inserted_project_id,$shop_name);
        }

        $static_defaults_q = $this->user_model->select_static_defaults();
        $getResult = $static_defaults_q->getResult();
        $static_defaults = array_shift($getResult);
        $day_revew_req = $static_defaults->prj_review_day;
        $current_dead_line = date('d/m/Y', strtotime("$day_revew_req this week") );

        $this->projects_m->insert_wip_rvw($inserted_project_id, $current_dead_line, $project_date);

        if( strpos(implode(",",$data['warranty_categories']), $job_category) !== false ):
          $this->set_warranty_date_after_paid($inserted_project_id);
        endif;

        $new_project_sched_id = $this->projects_m->insert_new_project_sched_for_pr($inserted_project_id, $project_leading_hand_id);

        if ($project_leading_hand_id == 0){
          $this->projects_m->insert_manual_lead($new_project_sched_id, ucwords($project_leading_hand_full_name), $project_leading_hand_mobile_no);
        }



//================= For Maintenance Site sheet ===============
      if($job_category == 'Maintenance'):
        $site_cont_person = $this->request->getPost('site_cont_person');
        $site_cont_number = $this->request->getPost('site_cont_number');
        $site_cont_mobile = $this->request->getPost('site_cont_mobile');
        $site_cont_email = $this->request->getPost('site_cont_email');

        $this->projects_m->insert_project_site_contact($inserted_project_id, $site_cont_person, $site_cont_number, $site_cont_mobile, $site_cont_email);
      
        $user_id = $this->session->get('user_id');
        $date = date("d/m/Y");
        $time = date("H:i:s");
        $type = "INSERT";
        $actions = "Insert project #".$inserted_project_id." site contacts with Contact Person: ".$site_cont_person.", Contact Number: ".$site_cont_number.", Mobile Number: ".$site_cont_mobile.", Email: ".$site_cont_email;
        $this->user_model->insert_user_log($user_id,$date,$time,$actions,$inserted_project_id,$type);
        

      endif;
//================= For Maintenance Site sheet ===============
  




          
      if($job_category == 'Maintenance' || $job_category == 'Minor Works' || $job_category == 'Design Works' ):
        $this->projects_m->update_feedback(0,$inserted_project_id);
      endif;


      if($install_hrs != '' && $install_hrs > 0){
        $prj_install_hrs = $install_hrs;
      }else{
        $prj_install_hrs = $labour_hrs_estimate;
      }

      $this->insert_cost_total($inserted_project_id,$prj_install_hrs,$is_double_time);

      $this->session->setFlashdata('curr_tab', 'project-details');     

      $works_from_selected_project = $this->works_m->display_all_works($copy_work_project_id);

      foreach ($works_from_selected_project->getResult() as $row) {
        $work_notes_raw = $this->projects_m->fetch_project_notes($row->note_id);
        $getResultArray = $work_notes_raw->getResultArray();
        $work_notes = array_shift($getResultArray);

        $note_id = $this->works_m->insert_work_notes($work_notes['comments'],$work_notes['notes']);

        if($include_work_estimate == 1){
          $work_estimate = $row->work_estimate;
          $total_work_quote = $work_estimate + ($work_estimate*($project_markup/100));
        }else{
          $work_estimate = 0;
          $total_work_quote = $work_estimate + ($work_estimate*($project_markup/100));
        }       

        $work_id = $this->works_m->insert_new_works($row->contractor_type,$row->work_con_sup_id,$row->other_work_desc,$project_markup,$note_id,$row->is_deliver_office,'',$row->work_reply_date,$inserted_project_id,'0',$work_estimate,$total_work_quote);

        $works_joinery_raw = $this->works_m->display_all_works_joinery($row->works_id);
        foreach ($works_joinery_raw->getResult() as $joinery_row) {

          $joinery_notes_raw = $this->projects_m->fetch_project_notes($joinery_row->note_id);
          $joinery_notes = array_shift($joinery_notes_raw->result_array());

          $joinery_note_id = $this->works_m->insert_work_notes($joinery_notes['comments'],$joinery_notes['notes']);
          $added_new_joinery_id = $this->works_m->insert_works_joinery($work_id,$joinery_row->joinery_id,$project_markup,$joinery_note_id,$joinery_row->is_deliver_office,'',$joinery_row->work_reply_date);

        }

        $considerations_raw = $this->works_m->fetch_considerations($row->works_id);
        $getResultArray = $considerations_raw->getResultArray();
        $consdr = array_shift($getResultArray);
        $this->works_m->insert_considerations($work_id,0, $consdr['site_inspection_req'], $consdr['special_conditions'], $consdr['additional_visit_req'], $consdr['operate_during_install'], $consdr['week_work'], $consdr['weekend_work'], $consdr['after_hours_work'], $consdr['new_premises'], $consdr['free_access'], $consdr['other'], $consdr['otherdesc']);

      //  $this->session->set_flashdata('curr_tab', 'works');
      }
// ========================= EMAIL Notification for PA for INDUCTION ==================
      $is_exempted = $this->induction_project_exempted($inserted_project_id);
      
      if($is_exempted == 0):

        $proj_q = $this->projects_m->select_particular_project($inserted_project_id);
        foreach ($proj_q->getResultArray() as $row){
          $project_admin_id = $row['project_admin_id'];
        }

        $users_q = $this->user_model->fetch_user($project_admin_id);
        foreach ($users_q->getResultArray() as $users_row){
          $pm_name = $users_row['user_first_name']." ".$users_row['user_last_name'];
          $pm_email_id = $users_row['user_email_id'];
          $email_q = $this->company_m->fetch_email($pm_email_id);
          foreach ($email_q->getResultArray() as $email_row){
            $pm_email = $email_row['general_email'];
          }
        }

        $user_id = $this->session->get('user_id');
        $users_q = $this->user_model->fetch_user($user_id);
        foreach ($users_q->getResultArray() as $users_row){
          $user_name = $users_row['user_first_name']." ".$users_row['user_last_name'];
          $user_email_id = $users_row['user_email_id'];
          $email_q = $this->company_m->fetch_email($user_email_id);
          foreach ($email_q->getResultArray() as $email_row){
            $sender_user_email = $email_row['general_email'];
          }
        }


        $this->induction_health_safety_m->set_inductions_as_saved($inserted_project_id);
        $sender_name = $user_name;
        $email_from = $sender_user_email;
        $email_to = $pm_email;
        $subject = "Project: ".$inserted_project_id." ".$project_name.",".$company_name."is required for Induction";
        $message = "The new project created number: ".$inserted_project_id.", needs to have induction slides created. Please see: https://sojourn.focusshopfit.com.au/induction_health_safety/induction_slide_editor_view?project_id=".$inserted_project_id;
        //review_code
        /*
        require_once('PHPMailer/class.phpmailer.php');
        require_once('PHPMailer/PHPMailerAutoload.php');


        $mail = new phpmailer(true);
        $mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
        $mail->port = 587;
        $mail->setFrom($email_from, $sender_name);
    
        //$mail->setfrom('userconf@sojourn.focusshopfit.com.au', 'name');
        $mail->setFrom($email_from, $sender_name);

        //$mail->addreplyto('userconf@sojourn.focusshopfit.com.au', 'name');
        $mail->addReplyTo($email_from);
    
        $mail->addAddress($email_to);
        //$mail->addCC($email_cc);
        $mail->addBCC('mark.obis2012@gmail.com');
        $mail->addBCC('ian@focusshopfit.com.au');

        $mail->smtpdebug = 2;
        $mail->ishtml(true);

        $mail->Subject = $subject;
        $mail->Body    = $message;

        if(!$mail->send()) {
          //return 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
        } else {
          //return "Email Sent Successfully";
        }
        */
        //review_code
      endif;
// ========================= EMAIL Notification for PA for INDUCTION ==================


      return redirect()->to('/projects/view/'.$inserted_project_id);


      elseif( $this->request->getPost('job_category') != 'Maintenance' && $this->request->getPost('job_category') != 'Minor Works' && $this->request->getPost('job_category') != 'Strip Out' && $this->request->getPost('job_category') != 'Design Works' && $project_area < 10  ):

        $this->session->setFlashdata('error', 'Invalid form details, please try again.');
        $data['validation'] = $this->validator;
        $data['error' ] = 'Invalid value for project area.';
        $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';
        return view('App\Views\page',$data);

      else: // form has errors
        // return with errors

        $this->session->setFlashdata('error', 'Invalid form details, please try again.');
        $data['validation'] = $this->validator;
        $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';
        return view('App\Views\page',$data);

      // return with errors
      endif;
    endif;
    // validation checks here

    return view('App\Views\page',$data);

  }

  public function add_company_project(){
    $this->admin_m = new Admin_m();
    $this->company = new Company();
    $this->company_m = new Company_m();
    $this->user_model = new Users_m();

    $rules = array();
    $data = array();

    $data['page_title'] = 'Add New Company Project';
    $data['screen'] = 'Add New Company Project';

    $data['main_content'] = 'App\Modules\Projects\Views\projects_add_for_company';

    if(  $this->session->get('is_admin') == 1 || $this->session->get('company_project') == 1):

      $defaults_raw = $this->admin_m->latest_system_default();
      $defaults = $defaults_raw->getResult();
      $defaults_id = $defaults[0]->defaults_id;
      $admin_default_id = $defaults[0]->admin_default_id;

      //$comp_list = $this->company_m->fetch_all_company_type_id('1');

      $all_company_list = $this->company_m->fetch_all_company_type_id('1');
      if($all_company_list->getNumRows() > 0){
        $data['all_company_list'] = $all_company_list->getResult();
      }

      $admin_defaults_q = $this->admin_m->fetch_admin_defaults($admin_default_id);
      $getResultArray = $admin_defaults_q->getResultArray();
      $admin_defaults = array_shift($getResultArray);
      $days_quote_deadline = $admin_defaults['days_quote_deadline'];

      $all_aud_states = $this->company_m->fetch_all_states();
      $data['all_aud_states'] = $all_aud_states->getResult();

      $focus = $this->admin_m->fetch_all_company_focus();
      $data['focus'] = $focus->getResult();

      $project_manager = $this->user_model->fetch_user_by_role(3);
      $data['project_manager'] = $project_manager->getResult();

      $maintenance_administrator = $this->user_model->fetch_user_by_role(7);
      $data['maintenance_administrator'] = $maintenance_administrator->getResult();

      $project_administrator = $this->user_model->fetch_user_by_role(2);
      $data['project_administrator'] = $project_administrator->getResult();


      $shopping_center = $this->projects_m->fetch_shopping_center();
      $data['shopping_center'] = $shopping_center->getResult();

      $estimator = $this->user_model->fetch_user_by_role(8);
      $data['estimator'] = $estimator->getResult();


      $data['all_projects'] = $this->projects_m->display_all_projects();

      $company_project_item = array();
      foreach ($data['all_projects']->getResultArray() as $row){
        $company_project_item[$row['company_id']] = $row['company_name'];
      }
      asort($company_project_item);
      $data['all_company_project'] = $company_project_item;

      $rules[ 'project_name' ] = ['project_name' => 'Project Name', 'rules' => ['required','trim','max_length[35]'] ];
      $rules[ 'site_start' ] = ['site_start' => 'Site Start', 'rules' => ['required','trim'] ];
      $rules[ 'site_finish' ] = ['site_finish' => 'Site Finish', 'rules' => ['required','trim'] ];
      $rules[ 'job_type' ] = ['job_type' => 'Job Type', 'rules' => ['required','trim'] ];
      $rules[ 'job_category' ] = ['job_category' => 'Job Category', 'rules' => ['required','trim'] ];
      $rules[ 'project_date' ] = ['project_date' => 'Project Date', 'rules' => ['required','trim'] ];
      $rules[ 'company_prg' ] = ['company_prg' => 'Company Client', 'rules' => ['required','trim'] ];
      $rules[ 'contact_person' ] = ['contact_person' => 'Contact Person', 'rules' => ['required','trim'] ];
      $rules[ 'project_total' ] = ['project_total' => 'Project Estimate', 'rules' => ['required','trim'] ];
      $rules[ 'labour_hrs_estimate' ] = ['labour_hrs_estimate' => 'Site Labour Estimate', 'rules' => ['required','trim'] ];
      $rules[ 'project_markup' ] = ['project_markup' => 'Project Markup', 'rules' => ['required','trim'] ];
      $rules[ 'install_hrs' ] = ['install_hrs' => 'Site Hours', 'rules' => ['trim'] ];

      if( $this->request->getPost('job_category') == 'Maintenance' ||  $this->request->getPost('job_category') == 'Kiosk' || $this->request->getPost('job_category') == 'Minor Works' || $this->request->getPost('job_category') == 'Strip Out'|| $this->request->getPost('job_category') == 'Design Works' ){
        $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['trim'] ];
      }else{
        $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['required','trim'] ];
      }

      // validation checks here
    if($_SERVER['REQUEST_METHOD'] === 'POST'): // on load if form is usbmitted

      if($this->validate($rules)): // form is valid and process form
      // proceed insert and redirect here


        $focus_id = $this->request->getPost('focus');
        $project_name = $this->request->getPost('project_name');
        $client_po = $this->request->getPost('client_po');
        $job_type = $this->request->getPost('job_type');
        $job_category = $this->request->getPost('job_category');
        $project_date = $this->request->getPost('project_date');
        $job_date = $this->request->getPost('job_date') ?? '';
        $site_start = $this->request->getPost('site_start');
        $site_finish = $this->request->getPost('site_finish');

        if($job_date != ''){
          $is_wip = 1;
        }else{
          $is_wip = 0;
        }

        $data['unit_level'] = $this->company->if_set($this->request->getPost('unit_level'));
        $data['unit_number'] = $this->company->if_set($this->request->getPost('unit_number'));
        $data['street'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street')));
        $data['postcode_a'] = $this->company->if_set($this->request->getPost('postcode_a'));

        $data['pobox'] = $this->company->if_set($this->request->getPost('pobox'));
        $data['unit_level_b'] = $this->company->if_set($this->request->getPost('unit_level_b'));
        $data['number_b'] = $this->company->if_set($this->request->getPost('number_b'));     
        $data['street_b'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street_b')));
        $data['postcode_b'] = $this->company->if_set($this->request->getPost('postcode_b'));

        $project_manager_id = 20;
        $project_admin_id = 20;
        $project_estiamator_id = 20;

        $contact_person_id = $this->request->getPost('contact_person');
        $project_total = str_replace (',','', $this->request->getPost('project_total') );

        $install_hrs = $this->request->getPost('install_hrs');
        $project_markup = $this->request->getPost('project_markup');

        $project_area = $this->request->getPost('project_area');
        $comments = $this->request->getPost('comments');
        $project_status_id = 1;

        $copy_work_project_id = $this->request->getPost('copy_work_project_id');
        $include_work_estimate = $this->request->getPost('include_work_estimate');
        $shop_tenancy_number = $this->request->getPost('shop_tenancy_number') ?? '';
        $focus_user_id = $this->session->get('user_id');


        $labour_hrs_estimate = $this->request->getPost('labour_hrs_estimate');
        $is_shopping_center = $this->request->getPost('is_shopping_center');
        $project_notes_id = $this->company_m->insert_notes($comments);
        $is_double_time = $this->request->getPost('is_double_time');

        $company_prg_arr =  explode('|',$this->request->getPost('company_prg'));
        $client_id = $company_prg_arr[1];

        $client_details = $this->company_m->fetch_company_details($client_id);
        foreach ($client_details->getResult() as $data){
          $site_address_id = $data->address_id;
          $invoice_address_id = $data->address_id;
        }

        $formated_start_date = str_replace("/","-",$site_start);
        $date_quote_deadline =  date('d/m/Y', strtotime("$formated_start_date -$days_quote_deadline days"));

        $inserted_project_id = $this->projects_m->insert_new_project($project_name, $project_date, $contact_person_id, $project_total, $job_date, '71',$is_wip, $client_po, $site_start, $site_finish, $job_category,$job_type, $focus_user_id ,$focus_id, $project_manager_id, $project_admin_id, $project_estiamator_id,$site_address_id, $invoice_address_id, $project_notes_id, $project_markup,$project_status_id,$client_id, $install_hrs,$project_area, $is_double_time, $labour_hrs_estimate, $shop_tenancy_number,$defaults_id,$project_manager_id, $date_quote_deadline,0,0);

        $project_leading_hand_id = 0;
        $this->projects_m->insert_new_project_sched_for_pr($inserted_project_id, $project_leading_hand_id);
        
        if($install_hrs != '' && $install_hrs > 0){
          $prj_install_hrs = $install_hrs;
        }else{
          $prj_install_hrs = $labour_hrs_estimate;
        }

        $this->insert_cost_total($inserted_project_id,$prj_install_hrs,$is_double_time);

        $this->session->setFlashdata('curr_tab', 'project-details');     
        return redirect()->to('/projects/view/'.$inserted_project_id);

      else: // form has errors
        // return with errors

        $this->session->setFlashdata('error', 'Invalid form details, please try again.');
        $data['validation'] = $this->validator;
        $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';
        return view('App\Views\page',$data);

      // return with errors
      endif;
    endif;
    // validation checks here

    return view('App\Views\page',$data);

    else:
      return redirect()->to('/projects');
    endif;


   
  }


  public function insert_cost_total($inserted_project_id,$prj_install_hrs,$is_double_time){
    $this->admin_m = new Admin_m();

    $project_details_raw = $this->projects_m->fetch_project_details($inserted_project_id);
    $getResultArray = $project_details_raw->getResultArray();  
    $project_details = array_shift($getResultArray);

    $system_default_raw = $this->admin_m->latest_system_default($project_details['defaults_id']);
    $getResultArray = $system_default_raw->getResultArray(); 
    $system_default = array_shift($getResultArray);


    $site_costs_raw = $this->admin_m->fetch_site_costs($system_default['site_cost_id']);
    $getResultArray = $site_costs_raw->getResultArray(); 
    $site_costs = array_shift($getResultArray);

    if($is_double_time > 0){
      $install_cost_total = $site_costs['total_double_time']*$prj_install_hrs;
    }else{
      $install_cost_total = $site_costs['total_amalgamated_rate']*$prj_install_hrs;
    }

    $this->projects_m->insert_cost_total($inserted_project_id,$install_cost_total);
  }

  public function list_uploaded_files(){
    $this->admin_m = new Admin_m();

    $proj_id = $_POST['proj_id'];// ?? 47057;
    $job_date = $_POST['job_date'];// ?? '08/12/2022';

    $q_list_doc_type = $this->projects_m->list_uploaded_files($proj_id);

    $user_role_id = $this->session->get('user_role_id');
    $is_admin = $this->session->get('is_admin');

    $rows = $q_list_doc_type->getNumRows();

    $list_doc_type = $q_list_doc_type->getResult();
    $doc_type = '';

    $authorize_role_id = 0;
    $doc_storage_defaults = $this->admin_m->fetch_default_doc_storage();
    $q_doc_storage_defaults = $doc_storage_defaults->getResult();
    foreach ($q_doc_storage_defaults as $default_doc_storage){
      $authorize_role_id = $default_doc_storage->authorize_role_id; 
    }

    $proj_q = $this->projects_m->fetch_project_details($proj_id);
    $proj_q = $proj_q->getResult();
    foreach ($proj_q as $row){
      $job_category = $row->job_category; 
    }


    $default_doc_types = $this->admin_m->fetch_doc_storage_required_notification();
    $q_default_doc_types = $default_doc_types->getResult();

    if($rows < 1){
      echo '<p class="m-top-10"><em class="fa fa-exclamation-circle"></em> No Files Uploaded</p>';
    }else{
      foreach ($list_doc_type as $stored_files){

        $font_color = "";
        if($stored_files->for_replacement == 1):
          $font_color = "blue";
        endif;

        $will_replace_existing = $stored_files->will_replace_existing;

        if($will_replace_existing == 1):
          $font_color = "red";
        endif;

        if($stored_files->is_project_attachment == 1){
          $check = 'checked';
        }else{
          $check = '';
        }

        if($doc_type == ''){
          $doc_type = $stored_files->doc_type_name;
          echo "<p class=\"m-top-15 pointer cat_type_head\" id=\"doc_st_id_".$stored_files->storage_doc_type_id."\" ><strong>$doc_type</strong></p>";
        }else{
          if($doc_type != $stored_files->doc_type_name){
            $doc_type = $stored_files->doc_type_name;
            echo "<p class=\"m-top-15 pointer cat_type_head\" id=\"doc_st_id_".$stored_files->storage_doc_type_id."\" ><strong>$doc_type</strong></p>";
          }else{
            $doc_type = $stored_files->doc_type_name;
          }
        }

        $need_authorization = 0;
        foreach ($q_default_doc_types as $required_doc_type){
          if($doc_type == $required_doc_type->doc_type_name){
            $need_authorization = 1;
          }
        }
        
        echo '<p class="row_file_list clearfix pad-3 pad-left-5 pad-right-5 file_cat_'.$stored_files->storage_doc_type_id.'" style = "padding-left: 5px;">';

        echo '<em class="fa fa-level-up fa-lg fa-rotate-90 pull-left " style="font-size: 25px;    color: #000;    margin-right: 14px;"></em> &nbsp; ';

        if($need_authorization == 0):
          echo '<input type = "checkbox" name = "proj_attach" id = "attach_'.$stored_files->storage_files_id.'" class = "pull-left input_checkbox_attach_file"  title = "Project attachments" onclick = "attach_to_project('.$stored_files->storage_files_id.')" '.$check.'>';
        else:
          if($will_replace_existing == 0):
            echo '<input type = "checkbox" name = "proj_attach" id = "attach_'.$stored_files->storage_files_id.'" class = "pull-left input_checkbox_attach_file"  title = "Project attachments" onclick = "attach_to_project('.$stored_files->storage_files_id.')" '.$check.'>';
          else:
            if($is_admin == 1 || $user_role_id == $authorize_role_id  || $job_category == 'Maintenance'):
              echo '<input type = "checkbox" name = "proj_attach" id = "attach_'.$stored_files->storage_files_id.'" class = "pull-left input_checkbox_attach_file"  title = "Project attachments" onclick = "attach_to_project('.$stored_files->storage_files_id.')" '.$check.'>';
            else:
              if($job_date == ""):
                echo '<input type = "checkbox" name = "proj_attach" id = "attach_'.$stored_files->storage_files_id.'" class = "pull-left input_checkbox_attach_file"  title = "Project attachments" onclick = "attach_to_project('.$stored_files->storage_files_id.')" '.$check.'>';
              else:
                echo '<input type = "checkbox" name = "proj_attach" id = "attach_'.$stored_files->storage_files_id.'" class = "pull-left input_checkbox_attach_file"  title = "Project attachments" onclick = "attach_to_project('.$stored_files->storage_files_id.')" '.$check.' disabled>';
              endif;
            endif;
          endif;
        endif;
        
        echo '<span class="pull-left file_link_download pointer" id="" style = "color: '.$font_color.'; margin-left:20px;">'.$stored_files->file_name.'</span>';

        if(strlen($stored_files->user_first_name) > 0){
          echo '<span style=" background:#F7901E; font-size: 12px; padding: 1px 8px; float: right; border: 1px solid #864e11;  color: #fff;  height: 20px;    margin: 0px 5px;     border-radius: 10px;    display: block;"><em class="fa fa-calendar-o"></em> '.$stored_files->date_upload.' &nbsp; '.$stored_files->user_first_name.'</span>';
        }else{
          echo '<span style=" background:#F7901E; font-size: 12px; padding: 1px 8px; float: right; border: 1px solid #864e11;  color: #fff;  height: 20px;    margin: 0px 5px;     border-radius: 10px;    display: block;"><em class="fa fa-calendar-o"></em> '.$stored_files->date_upload.' &nbsp; Contractor</span>';
        }

        echo '<a class="btn btn-xs btn-success pull-right m-left-15 m-right-10" href="'.site_url().'docs/stored_docs/'.urlencode($stored_files->file_name).'" target="_blank" title="Download File">Download</a>';
        echo '<em id="'.$stored_files->storage_files_id.'" class="pointer fa fa-trash fa-lg pull-right del_stored_file" style="color: red; display:none; margin-top: 3px;" onclick = "del_stored_file('.$stored_files->storage_files_id.')"></em>';
        echo '</p>';

      }
    }
  }

  public function fetch_project_required_doc_type_file(){
    $data = json_decode(file_get_contents("php://input"));
    $project_id = $data['project_id'];// ?? 47057;
    $doc_type = $data['doc_type'];// ?? 12;

    $query = $this->projects_m->fetch_project_required_doc_type_file($project_id,$doc_type);
    echo json_encode($query->getResult());
  }

  public function find_contact_person($comp_id=''){
    $post_ajax_arr = array();

    if(isset($_POST['ajax_var'])){
      $post_ajax_arr = explode('|',$_POST['ajax_var']);
    }elseif($comp_id!=''){
      $post_ajax_arr[1] = $comp_id;
    }else{
      return false;
    }   

    $contact_persons_q = $this->projects_m->fectch_contact_person_by_company_id($post_ajax_arr['1']);

    foreach ($contact_persons_q->getResult() as $row) {
      echo '<option value="'.$row->contact_person_id.'" '.($row->is_primary == 1 ? 'selected="selected"' : '').'  >'.$row->first_name.' '.$row->last_name.'</option>';
    }
  }

  public function set_warranty_date_after_paid($project_id){
    $this->admin_m = new Admin_m();
    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    $getResultArray = $q_admin_defaults->getResultArray();
    $admin_defaults = array_shift($getResultArray);

    $warranty_months = $admin_defaults['warranty_months'];
    $warranty_years = $admin_defaults['warranty_years'];
    
    $q_fetch_project = $this->projects_m->select_particular_project($project_id);
    $fetch_project = $q_fetch_project->getResultArray();

    foreach ($fetch_project as $row) {

      $date = $row['date_site_finish'];
      $date = str_replace('/', '-', $date);
      $date_finish = date('Y-m-d', strtotime($date));

      $warranty_date_year = date('Y-m-d', strtotime("+".$warranty_years." months", strtotime($date_finish)));
      $warranty_date = date('d/m/Y', strtotime("+".$warranty_months." months", strtotime($warranty_date_year)));

      $this->projects_m->update_warranty_date($warranty_date, $row['project_id']);
    }
  }

  public function fetch_address_company_invoice(){
    $this->company_m = new Company_m();

    $post_ajax_arr = explode('|',$_POST['ajax_var']);

    $query_company_details = $this->company_m->fetch_all_company($post_ajax_arr[1]);
    $getResultArray = $query_company_details->getResultArray();
    $temp_company_details = array_shift($getResultArray);

    $query_complete_detail_address = $this->company_m->fetch_complete_detail_address($temp_company_details['address_id']);
    $getResultArray = $query_complete_detail_address->getResultArray();
    $temp_complete_detail_address = array_shift($getResultArray);

    echo ucwords(strtolower($temp_complete_detail_address['unit_level'])).'|';
    echo $temp_complete_detail_address['unit_number'].'|';
    echo ucwords(strtolower($temp_complete_detail_address['street'])).'|';
    echo $temp_complete_detail_address['po_box'].'|';

    echo $temp_complete_detail_address['shortname'].'|';
    echo $temp_complete_detail_address['name'].'|';
    echo $temp_complete_detail_address['phone_area_code'].'|';
    echo $temp_complete_detail_address['id'].'|';

    echo ucfirst(strtolower($temp_complete_detail_address['suburb'])).'|';
    echo $temp_complete_detail_address['postcode'];

  }

  public function set_jurisdiction_shoping_center($focus_id_set=''){
    $this->admin_m = new Admin_m();
    $shopping_center_arr = array();

    if($focus_id_set==''){
      $focus_id = $_POST['ajax_var'];
    }else{
      $focus_id = $focus_id_set;
    }

    $focus_raw = $this->admin_m->fetch_single_company_focus($focus_id);
    $getResultArray = $focus_raw->getResultArray();
    $focus = array_shift($getResultArray);

    $focus_jurisdiction =  explode(',', $focus['admin_jurisdiction_state_ids']);

    foreach ($focus_jurisdiction  as $jur_key => $state):
      $shopping_center = $this->projects_m->fetch_shopping_center_by_state($state);
      foreach ($shopping_center->getResultArray() as $row):
        if(!in_array($row['shopping_center_brand_name'], $shopping_center_arr)){
          array_push($shopping_center_arr,$row['shopping_center_brand_name']);
        }       
      endforeach;
    endforeach;

    foreach ($shopping_center_arr as $row => $value){
      echo '<option value="'.$value.'">'.$value.'</option>';
    }
  }

  public function set_pa(){
    $selected_pm = $this->request->getPost('selected_pm');
    $q_fetch_pa = $this->projects_m->fetch_pa($selected_pm);
    $getResultArray = $q_fetch_pa->getResultArray();
    $fetch_pa = array_shift($getResultArray);
    $set_proj_admin = $fetch_pa['project_administrator_id'];

    echo $set_proj_admin;
  }

  public function set_jurisdiction($focus_id=''){
    $this->company_m = new Company_m();
    $this->admin_m = new Admin_m();

    $all_aud_states = $this->company_m->fetch_all_states();
    $data['all_aud_states'] = $all_aud_states->getResult();

    echo "<option value=''>Choose a State</option>";


    if($focus_id == ''){
    $ajax_var = $this->request->getPost('ajax_var');
      $post_ajax_arr = explode('|',$ajax_var);

      $focus_id = $post_ajax_arr[0];
      $admin_company_details = $this->admin_m->fetch_single_company_focus($focus_id);
      $getResultArray = $admin_company_details->getResultArray();
      $focus_detail_data = array_shift($getResultArray);

      $jurisdiction = explode(',', $focus_detail_data['admin_jurisdiction_state_ids']);


      foreach ($jurisdiction  as $jur_key => $jur_value): 
        foreach ($data['all_aud_states']  as $key => $value):
          if( $jur_value == $value->id ){ echo '<option value="'.$value->shortname.'|'.$value->name.'|'.$value->phone_area_code.'|'.$value->id.'">'.$value->name.'</option>'; }
        endforeach;
      endforeach;

    }elseif ($focus_id!='') {

      $focus_id = $focus_id;
      $admin_company_details = $this->admin_m->fetch_single_company_focus($focus_id);
      $getResultArray = $admin_company_details->getResultArray();
      $focus_detail_data = array_shift($getResultArray);

      $jurisdiction = explode(',', $focus_detail_data['admin_jurisdiction_state_ids']);


      foreach ($jurisdiction  as $jur_key => $jur_value): 
        foreach ($data['all_aud_states']  as $key => $value):
          if( $jur_value == $value->id ){ echo '<option value="'.$value->shortname.'|'.$value->name.'|'.$value->phone_area_code.'|'.$value->id.'">'.$value->name.'</option>'; }
        endforeach;
      endforeach;
    }else{
      foreach ($data['all_aud_states']  as $key => $value):
        echo '<option value="'.$value->shortname.'|'.$value->name.'|'.$value->phone_area_code.'|'.$value->id.'">'.$value->name.'</option>';
      endforeach;
    }
  }

  public function check_doc_type_is_required(){
    $this->admin_m = new Admin_m();

    $doc_type_id = $_POST['doc_type_id'];// ?? 12;
    $project_id = $_POST['project_id'];// ?? 47057;

    $required = 0;
    $default_doc_types = $this->admin_m->fetch_doc_storage_required_notification();
    foreach ($default_doc_types->getResultArray() as $row){
      $storage_doc_type_id = $row['storage_doc_type_id'];
      if($doc_type_id == $storage_doc_type_id){
        $required = 1;
      }
    }

    if($required == 1){
      $project_files = $this->projects_m->fetch_project_required_doc_type_file($project_id,$doc_type_id);
      if($project_files->num_rows == 0){
        $required = 0;
      }
    }
    
    echo $required;
  }

  public function fetch_mark_up_by($job_cat='',$markup_id=''){
    $this->admin_m = new Admin_m();
   
    if($job_cat == ''){
      $job_cat = $_POST['job_cat']  ?? 'Full Fitout';

      $defaults_raw = $this->admin_m->latest_system_default();
      $defaults = $defaults_raw->getResult();
      $markup_id = $defaults[0]->markup_id;

      if($job_cat == 'Company'){
        echo '0|0|0';
      }else{
        $mark_up_q = $this->projects_m->fetch_mark_up_by_type($job_cat,$markup_id);
        $getResult = $mark_up_q->getResultArray();
        $mark_up = array_shift( $getResult);
        echo implode("|",$mark_up);
      }

    }else{
      if($job_cat == 'Company'){
        return '0|0|0';
      }else{ 
        $mark_up_q = $this->projects_m->fetch_mark_up_by_type($job_cat,$markup_id);
        $getResult = $mark_up_q->getResultArray();
        $mark_up = array_shift( $getResult);
        return implode("|",$mark_up);
      }
    }
 


  }


  public function fetch_project_totals($project_id){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();

    $project_details_raw = $this->projects_m->fetch_project_details($project_id);
    $get_result_a = $project_details_raw->getResultArray();
    $project_details = array_shift($get_result_a);

    $system_default_raw = $this->admin_m->latest_system_default($project_details['defaults_id']);
    $get_result_b = $system_default_raw->getResultArray();
    $system_default = array_shift($get_result_b);

    $markup_raw = $this->admin_m->fetch_markup($system_default['markup_id']);
    $get_result_c = $markup_raw->getResultArray();
    $markup = array_shift($get_result_c);

    $site_costs_raw = $this->admin_m->fetch_site_costs($system_default['site_cost_id']);
    $get_result_d = $site_costs_raw->getResultArray();
    $site_costs = array_shift($get_result_d);

    $labour_cost_raw = $this->admin_m->fetch_labour_cost($system_default['labour_cost_id']);
    $get_result_e = $labour_cost_raw->getResultArray();
    $labour_cost = array_shift($get_result_e);

    $admin_defaults_raw = $this->admin_m->fetch_admin_defaults($system_default['admin_default_id']);
    $get_result_f = $admin_defaults_raw->getResultArray();
    $admin_defaults = array_shift($get_result_f);

    $project_cost_total_raw = $this->projects_m->get_project_cost_total($project_id);  
    $get_result_g = $project_cost_total_raw->getResultArray(); 
    $project_cost_total = array_shift($get_result_g);

    $leave_percentage = ($labour_cost['total_leave_days']/$labour_cost['total_work_days'])*100;
    $labour_cost['leave_percentage'] = round($leave_percentage, 2);
    $labour_cost_grand_total = $labour_cost['leave_percentage']+$labour_cost['superannuation']+$labour_cost['workers_compensation']+$labour_cost['payroll_tax']+$labour_cost['leave_loading']+$labour_cost['other']; 

    $payroll_tax = $labour_cost['payroll_tax'];
    $rate = $site_costs['rate'];

    $gp_on_cost_total_hr = $rate+(($rate*$labour_cost_grand_total)/100);
    $gp_on_cost_time_half_hr = $gp_on_cost_total_hr + ((0.5*$rate) + (((0.5*$rate)*$payroll_tax )/100));
    $gp_on_cost_time_double_hr = $gp_on_cost_total_hr + ($rate+(($rate*$payroll_tax)/100));


    $gp_data_arr = $this->admin->get_double_amalgated_rate($site_costs,$labour_cost,$admin_defaults);

    if($project_details['install_time_hrs'] == 0){
      $total_install_rate = 0;
    }else{
      $total_install_rate = $project_cost_total['install_cost_total'];
    }



    $total_estimated_cost = $total_install_rate + ($total_install_rate * ($admin_defaults['installation_labour_mark_up']/100));
    $total_quoted_cost = $total_estimated_cost + ($total_estimated_cost * ($project_details['markup']/100));

    if($project_id == '35951'){
      $total_quoted_cost = $total_quoted_cost - 0.87;
    }


    if($project_details['is_double_time']>0){
      $actual_cost_total = ($site_costs['rate']*2);
      //$gp_cost_actual = $gp_on_cost_time_half_hr;
      $gp_cost_actual = $gp_data_arr['gp_on_cost_time_double_hr'];
    }else{
      $actual_cost_total = $gp_data_arr['gp_amalgamated_rate'];
      $gp_cost_actual = $gp_data_arr['gp_amalgamated_rate'];
    }

    $prj_install_hrs = $project_details['install_time_hrs'];


    $actual_project_cost = ($actual_cost_total * $prj_install_hrs) + $project_cost_total['work_estimated_total'];

    /* gP variables*/
    //$gp_actual_cost_total = $gp_cost_actual + (($gp_cost_actual*$labour_cost_grand_total) / 100);
    $gp_actual_project_cost = ($gp_cost_actual * $prj_install_hrs) + $project_cost_total['work_estimated_total'];
    /* gP variables*/

    $final_total_cost = $project_cost_total['work_price_total'] + $total_install_rate;
    $final_total_estimated = $project_cost_total['work_estimated_total'] + $total_estimated_cost;
    $final_total_quoted = $project_cost_total['work_quoted_total'] + $total_quoted_cost;



if($final_total_quoted== 0 && $gp_actual_project_cost == 0){
$gp = 0;
}else{
    $gp = round(($final_total_quoted - $gp_actual_project_cost) / $final_total_quoted,4);

}


    if($project_id == '36207'){
      $final_total_quoted = $final_total_quoted + 0.22;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }

    if($project_id == '36813'){
      $final_total_quoted = $final_total_quoted - 0.33;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }

    if($project_id == 36561){
      $final_total_quoted = $final_total_quoted - 0.12;
    }

    if($project_id == 37258){
      $final_total_quoted = $final_total_quoted + 0.32;
    }

    if($project_id == 36364){
      $final_total_quoted = $final_total_quoted - 0.2;
    }

    if($project_id == 39826){
      $final_total_quoted = $final_total_quoted + 0.24;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }

    if($project_id == 38921){
      $final_total_quoted = $final_total_quoted - 0.23;
    }

    if($project_id == 37625){
      $final_total_quoted = $final_total_quoted - 45.02;
    }

    if($project_id == 38201){
      $final_total_quoted = $final_total_quoted + 5948.78;
    }

    if($project_id == 37770){
      $final_total_quoted = $final_total_quoted - 0.41;
    }

    if($project_id == 38609){
      $final_total_quoted = $final_total_quoted + 0.04;
    }

    if($project_details['project_total'] != $final_total_quoted){ //////////////// !!!!!!!!!!!!!!!!!! here update total when changed
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
      $this->re_allocate_invoice($project_id,$final_total_quoted);
      // put change invoice here
    }



    if($project_id == 36410){
      $final_total_quoted = $final_total_quoted - 1.86;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }



    if($project_id == 43561){
      $final_total_quoted = $final_total_quoted - 0.37;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }

if($project_id == 46470){
      $final_total_quoted = $final_total_quoted - 0.5;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
    }


    if($project_id == 44338){
      $final_total_quoted = $final_total_quoted + 0.69;
      $this->projects_m->update_project_total($project_id,$final_total_quoted);
      $this->re_allocate_invoice($project_id,$final_total_quoted);
    }
 

    $result = array(
      "gp" => $gp,
      "final_total_cost" => $actual_project_cost,
      "final_total_estimated" => $final_total_estimated,
      "final_total_quoted" => $final_total_quoted,
      "final_labor_cost" => $total_quoted_cost,
      "labour_cost_grand_total" => $labour_cost_grand_total,
      "install_cost_total" => $project_cost_total['install_cost_total'],
      "variation_total" =>$project_cost_total['variation_total']
      );

    //var_dump($result);
    return $result;
  }


  public function re_allocate_invoice($project_id,$new_total){

    $this->invoice_m = new Invoice_m();


    $q_invoice_list = $this->invoice_m->get_invoices($project_id);
    $extra_percent  = 0;
    $added_percent = 0;
    $curr_percent = 0;

    $inv_curr_id = 0;
 
    foreach ($q_invoice_list->getResult() as $invoice) {


      if($invoice->is_invoiced == '1' && $invoice->set_invoice_date != '' && $invoice->progress_percent != '100'){

        $new_percent = 100 / ( $new_total / $invoice->invoiced_amount );
        $this->invoice_m->update_progress_percent($invoice->invoice_id,$new_percent);
        $extra_percent = $extra_percent  + $new_percent;
    
      }else{

        if($invoice->progress_percent != '100'){


          $inv_remains = $this->projects_m->count_remain_un_nvoice($project_id);

          if($extra_percent > 0){


            if($inv_curr_id == 0){
              $inv_curr_id =  $invoice->invoice_id;
              $curr_percent = $invoice->progress_percent;
            }else{
              $extra_percent = $extra_percent + $invoice->progress_percent;
            }


          }

        }
      }
    }


      $added_percent = ABS(100-$extra_percent);
    $this->invoice_m->update_progress_percent($inv_curr_id,$added_percent);



  }

  public function projects_wip_review(){
    if(
      $this->session->get('user_role_id') != 3  &&
      $this->session->get('user_role_id') != 20 && 
      $this->session->get('user_role_id') != 2  && 
      $this->session->get('user_role_id') != 16 && 
      $this->session->get('user_role_id') != 7 && 
      $this->session->get('is_admin') != 1 && 
      $this->session->get('user_id') != 6 ){

      return redirect()->to('/projects');
    }     


    $this->user_model = new Users_m();
  
    $data['users'] = $this->user_model->fetch_user();
    $data['pm_list'] = $this->projects_m->list_pm_wiprp();

    $data['page_title'] = 'Projects WIP Review';
    $data['screen'] = 'Projects WIP Review';

    $data['main_content'] = 'App\Modules\Projects\Views\projects_wip_review';
    return view('App\Views\page',$data);
  }


  public function getProject_PR_images(){

    $user_id = $this->session->get('user_id');
    $this->user_model = new Users_m();

    $q_user_if_admin = $this->user_model->fetch_user($user_id);
    $ra_user_if_admin = $q_user_if_admin->getResultArray();
    $user_if_admin = array_shift($ra_user_if_admin);

    if ($user_if_admin['if_admin'] == '1'){

      $q_project_id_pr_images_admin = $this->projects_m->get_project_id_admin($user_id);
      $project_id_pr_images_admin = $q_project_id_pr_images_admin->getResult();

      if (!empty($project_id_pr_images_admin)){

        echo '<a href="#" id="drop3" role="button" class="pr-notif dropdown-toggle ave_status_text" data-toggle="dropdown" style="display: block;"><span style="color:white; background: red;"><i class="fa fa-line-chart"></i></span> <b class="caret"></b></a>';
        echo '<ul class="pr-notif-ul dropdown-menu" role="menu" aria-labelledby="drop3">';
              
        foreach ($project_id_pr_images_admin as $row){

          echo '<li role="">';
          echo  '<a href="'.site_url().'projects/progress_reports/'.$row->project_id.'" role="menuitem" tabindex="-1"  style="color: red;"><i class="fa fa-exclamation-circle"></i> '.$row->project_id.' </a>';
          echo '</li>';
        }

        echo '</ul>';

      }
    } else {


      
      if ($user_if_admin['user_role_id'] == '2'){ // PA
        
        $project_id_pr_images = $this->projects_m->get_project_id_pa($user_id);
        $project_id_pr_images = $project_id_pr_images->getResult();

        if (!empty($project_id_pr_images)){

          echo '<a href="#" id="drop3" role="button" class="pr-notif dropdown-toggle ave_status_text" data-toggle="dropdown" style="display: block;"><span style="color:white; background: red;"><i class="fa fa-line-chart"></i></span> <b class="caret"></b></a>';
          echo '<ul class="pr-notif-ul dropdown-menu" role="menu" aria-labelledby="drop3">';
                
          foreach ($project_id_pr_images as $row){

            echo '<li role="">';
            echo  '<a href="'.site_url().'projects/progress_reports/'.$row->project_id.'" role="menuitem" tabindex="-1"  style="color: red;"><i class="fa fa-exclamation-circle"></i> '.$row->project_id.' </a>';
            echo '</li>';
          }

          echo '</ul>';

        }

      } elseif ($user_if_admin['user_role_id'] == '3' || $user_if_admin['user_role_id'] == '20') { // PM and AM

        $project_id_pr_images = $this->projects_m->get_project_id_pm($user_id);
        $project_id_pr_images = $project_id_pr_images->getResult();

        if (!empty($project_id_pr_images)){

          echo '<a href="#" id="drop3" role="button" class="pr-notif dropdown-toggle ave_status_text" data-toggle="dropdown" style="display: block;"><span style="color:white; background: red;"><i class="fa fa-line-chart"></i></span> <b class="caret"></b></a>';
          echo '<ul class="pr-notif-ul dropdown-menu" role="menu" aria-labelledby="drop3">';
                
          foreach ($project_id_pr_images as $row){

            echo '<li role="">';
            echo  '<a href="'.site_url().'projects/progress_reports/'.$row->project_id.'" role="menuitem" tabindex="-1"  style="color: red;"><i class="fa fa-exclamation-circle"></i> '.$row->project_id.' </a>';
            echo '</li>';
          }

          echo '</ul>';

        }

      } else {

        echo '';

      }

      

    }
  }

  public function list_project_comments($project_id=''){
    $this->user_model = new Users_m();


    if(isset($_POST['project_id'])){
      $project_id_set = $_POST['project_id'];
    }elseif($project_id!=''){
      $project_id_set = $project_id;
    }else{
      return false;
    }

    $is_prj_rvw = 1;

    if(isset($_POST['is_prj_rvw']) && $_POST['is_prj_rvw'] != ''){
      $is_prj_rvw = 0;
    }

    
    if(isset($_POST['is_prj_rvw']) && $_POST['is_prj_rvw'] != '' && $_POST['is_prj_rvw'] == 2 ){
      $is_prj_rvw = 2;
    }



    $project_comments = $this->projects_m->list_project_comments($project_id_set,$is_prj_rvw);

    $project_details = $this->projects_m->fetch_project_details($project_id_set);

    if($project_details->getNumRows() > 0){

      if($project_comments->getNumRows() > 0){

        foreach ($project_comments->getResultArray() as $row){

          $fetch_user= $this->user_model->fetch_user($row['user_id']);
          $getResultArray_a = $fetch_user->getResultArray();
          $user = array_shift($getResultArray_a);

          echo '<div class=" '.($row['is_active'] == 1 ? 'active' : 'deleted' ).' '.$row['project_comments_id'].'  notes_line user_postby_'.strtolower( str_replace(' ', '',  $user['user_first_name']) ).' comment_type_'.$row['is_project_comments'].'">';

          if($row['is_active'] == 1 && $row['is_project_comments'] == 2){
            echo '<div class="pull-right btn btn-danger view_delete btn-xs fa fa-trash" id="'.$row['project_comments_id'].'"></div>';
          }

          if($row['is_active'] == 0 && $row['is_project_comments'] == 2){
            echo '<div class="pull-right btn btn-warning view_deleted btn-xs fa fa-eye-slash"> </div>';
          }
          

          if( $this->session->get('user_id') == 2 ){
            echo '<a href="'.site_url().'projects/rem_commnt/'.$row['project_comments_id'].'" target="_blank" class="btn btn-xs btn-danger pull-right err_commnt">Remove</a>';
          }
          

          echo '<p class="">'.ucfirst (nl2br($row['project_comments'])).'</p><small><i class="fa fa-user"></i> '.$user['user_first_name'].' '.$user['user_last_name'].'<br /><i class="fa fa-calendar"></i> '.$row['date_posted'].'</small></div>';
        


        } 
      }else{
        echo '<div class="notes_line no_posted_comment"><p>No posts made!</p></div>';
      }

    }else{
      echo 'Error';
    }
  }

  public function add_brand(){
    $brand_name = $_POST['ajax_var'];
    $brands_list = $this->projects_m->add_brand($brand_name);
    echo $this->list_all_brands();
  }

  public function delete_brand(){
    $brand_raw = explode('_',$_POST['ajax_var']);
    $brand_id = $brand_raw[1];
    $brands_list = $this->projects_m->delete_brand($brand_id);
  }

  public function update_brand(){
    $brand_raw = explode('|',$_POST['ajax_var']);

    $brand_name = $brand_raw[0];
    $id = $brand_raw[1];

    $brands_list = $this->projects_m->update_brand($brand_name,$id);
  }


  public function add_project_comment(){


    $raw_project_comment = $this->request->getPost('ajax_var', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $project_comment = explode('`', $raw_project_comment);
 //   $datestring = "%l, %d%S, %F %Y %g:%i:%s %A";
    $datestring = "l, F d, Y"; $time = time();   

    $prj_rev = 1;

    if(isset($project_comment[3]) && $project_comment[3] != ''){
      $prj_rev = 0;
    }

    if(isset($project_comment[4]) && $project_comment[4] != ''){
      $prj_rev = 2;
    }

    $date_posted = date($datestring, $time);

    $supplier_category = $this->projects_m->add_project_comment($project_comment[1],$date_posted,$project_comment[2],$project_comment[0],$prj_rev);
    
    echo $date_posted;
  }

  public function project_comments_deleted(){
    $this->user_model = new Users_m();

    $comment_id = $_POST['comments_id'];
    $project_id = $_POST['project_id'];
    $user_id = $_POST['user_id'];

    $type = 'Update';
    $actions = 'Deleted an amendment from Project:'.$project_id.' - '.$comment_id;


    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
    $project_comments = $this->projects_m->delete_comment($comment_id);
  }


  public function display_all_projects($stat,$is_wpev = 0,$custom_q = ''){
    $this->admin_m = new Admin_m();



    $admin_defaults = $this->admin_m->fetch_admin_defaults(); //1
    foreach ($admin_defaults->getResult() as $row){
      $data['unaccepted_date_categories'] = $row->unaccepted_date_categories;
      $data['unaccepted_no_days'] = $row->unaccepted_no_days;
    }

    $q_warranty_categories = $this->projects_m->fetch_warranty_categories();
    $getResultArray = $q_warranty_categories->getResultArray();
    $data['warranty_categories'] = array_shift($getResultArray);

    $raw_warranty_cat = implode(",",$data['warranty_categories']);
    $replaced_warranty_cat = str_replace(",", "','", $raw_warranty_cat)."'";
    $format_warranty_cat = substr_replace($replaced_warranty_cat, "", 0, 2);

    if($stat == 'unset' || $stat == 'quote'){
      $extra_query = ' AND `project`.`job_date` = ""  ';

    }elseif($stat == 'invoiced'){
      $extra_query = 'AND `project`.`job_date` != ""  AND `project`.`is_paid` = "0"   '; 

    }elseif($stat == 'paid'){
      $extra_query = ' AND `project`.`is_paid` = "1"  ';
    }elseif($stat == 'all'){
      $extra_query = '   ';
    }elseif($stat == 'warranty'){
      $extra_query = 'AND `project`.`is_paid` = "1" AND `project`.`job_category` IN ('.$format_warranty_cat.')';
    }else{
      $extra_query = 'AND `project`.`job_date` != ""  AND `project`.`is_paid` = "0"   '; 
    }


    $data['is_wpev'] = $is_wpev;
      $extra_order = '';

    if($is_wpev > 0){
      $extra_order = ' ORDER BY `unix_date_site_finish`  ASC ';
      $extra_query .= " AND `project`.`job_type` != 'Company' ";

      if( $this->session->get('user_role_id') == 3 || $this->session->get('user_role_id') == 20   ){
        $user_pm_id = $this->session->get('user_id');
        $extra_query .= " AND `project`.`project_manager_id` = '$user_pm_id' ";
      }

      if( $this->session->get('user_role_id') == 2 || $this->session->get('user_role_id') == 7   ){



        $user_pa_id = $this->session->get('user_id');

        $pa_data = $this->projects_m->fetch_pa_pm_assignment($user_pa_id);
        $assignment = array_shift($pa_data->result_array());

        $prime_pm = $assignment['project_manager_primary_id'];
        $group_pm = explode(',', $assignment['project_manager_ids']);


        $extra_query .= " AND (  `project`.`project_manager_id` = '$prime_pm'  ";

          foreach ($group_pm as $key => $value) {
            $extra_query .= " OR `project`.`project_manager_id` = '$value' ";
          }
          
        $extra_query .= " OR `project`.`project_admin_id` = '$user_pa_id' ";

        $extra_query .= " ) ";

      }
    
    }

    if($is_wpev > 0 && $stat == 'quote'){
      $extra_order = " ORDER BY    UNIX_TIMESTAMP( STR_TO_DATE(`project`.`quote_deadline_date`, '%d/%m/%Y') ) ASC ";
    }


if($is_wpev == 0):
      $comp_id = $this->session->get("set_view_company_project");
      $custom_fcom_query = " AND `project`.`focus_company_id` = '".$comp_id."' ";
      $q_comp_f = $this->projects_m->list_focus_company_main($custom_fcom_query);
      $getResultArray = $q_comp_f->getResultArray();
      $focus_comp_main = array_shift($getResultArray);


      $base_f_comp_id = $focus_comp_main['focus_company_id'] ?? null;


      if(isset($base_f_comp_id) && $base_f_comp_id != ''){
        $extra_query .= " AND `project`.`focus_company_id` = '".$base_f_comp_id."' ";
      }
endif;

      if(isset($custom_q) && $custom_q != ''){
        $extra_query .= $custom_q;
      }



    $data['proj_t'] = $this->projects_m->display_all_projects($extra_query,$extra_order);

    return view('App\Modules\Projects\Views\tables_projects',$data);



  }

  public function document_storage(){
    $data['page_title'] = 'Document Storage';
    $data['screen'] = 'Document Storage';
    $data['main_content'] = 'App\Modules\Projects\Views\doc_storage';
    return view('App\Views\page',$data);
  }

  public function client_file_storage(){
    $data['screen'] = 'Client File Storage';
    $data['page_title'] = 'Client File Storage';
    $data['main_content'] = 'App\Modules\Projects\Views\cf_storage';
    return view('App\Views\page',$data);
  }







  public function list_projects_by_client($this_year=''){   /// client file storage

    if($this_year == ''){
      $this_year = date('Y');
    }
 
    $last_year = $this_year - 1;
    $q_list_projects_by_job_date = $this->projects_m->list_projects_by_client($this_year,$last_year);

    $has_data = $q_list_projects_by_job_date->getNumRows();

    $prj_line = '';
    $doc_type = '';

    if($has_data > 0){

      $projects_by_job_date = $q_list_projects_by_job_date->getResult();
      foreach ($projects_by_job_date as $data){
        
        if($prj_line != $data->client_id){
          $doc_type = '';
          echo '<div class="pad-5 prj_files_group">
            <div class="btn btn-info btn-xs fa fa-code-fork prj_files_head" id="'.$data->client_id.'"  style="margin: -4px 10px 0 0;" id=""></div>'.$data->company_name.' 
            <div class="pull-right btn btn-success btn-xs set_doc_storage_c" id="'.$data->company_name.'|'.$data->client_id.'_project_set"  data-toggle="modal" data-target="#doc_storage" style="margin: -1px 0 0 0;">Upload</div> </div>';
          $prj_line = $data->client_id;
        }


        if($doc_type == ''){
          $doc_type = $data->doc_type_name;
          echo '<p class="uploaded_files_row no-m pad-5 '.$data->client_id.'_files" style="display:none;"><strong>'.$doc_type.'</strong></p>';
        }else{
          if($doc_type != $data->doc_type_name){
            $doc_type = $data->doc_type_name;
            echo '<p class="uploaded_files_row no-m pad-5 '.$data->client_id.'_files" style="display:none;"><strong>'.$doc_type.'</strong></p>';
          }
        }

        echo '<div class="pad-5 '.$data->client_id.'_files uploaded_files_row" style="display:none;"> &nbsp; 
        <span><em class="fa fa-level-up fa-lg fa-rotate-90" style="color: #269ABC;"></em> &nbsp; 
        <span class="file_link_download pointer">'.$data->file_name.'</span>
        </span> 
        <span style=" background:#F7901E; font-size: 12px; padding: 1px 8px;    float: right;     border: 1px solid #864e11;  color: #fff;  height: 20px;    margin: 0px 5px;     border-radius: 10px;    display: block;"><em class="fa fa-calendar-o"></em> '.$data->date_upload.' &nbsp; '.$data->user_first_name.'</span>';


        echo '<a class="btn btn-xs btn-success pull-right m-left-15 m-right-10"  href="'.base_url().'docs/stored_docs/'.urlencode($data->file_name).'" target="_blank" title="Download File">Download</a>';
        echo '<em id="'.$data->storage_files_id.'" class="pointer fa fa-trash fa-lg pull-right del_stored_file" style="color: red; display:none; margin-top: 3px; "></em>';
        echo '</div>';
      }

    }else{
      echo '<div id="" class=""><p>No Uploaded Files</p></div>';
    }

  }












  public function set_date_review(){
    $this->user_model = new Users_m();
    $project_id = $this->request->getPost('ajax_var', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $rev_date  = date("d/m/Y");
    $this->projects_m->set_project_date_review($project_id,$rev_date);  

    $static_defaults_q = $this->user_model->select_static_defaults();
    $get_result_a = $static_defaults_q->getResult();
    $static_defaults = array_shift($get_result_a);

    $day_revew_req = $static_defaults->prj_review_day;
    $timestamp_day_revuew_req = (int)strtotime("$day_revew_req this week");
    $date_day_revuew_req = date('d/m/Y',$timestamp_day_revuew_req);
    $this->projects_m->update_set_wip_rvw($project_id,$date_day_revuew_req,$rev_date);

    $monday_revuew_req = (int)strtotime("Monday this week");
    $friday_revuew_req = (int)strtotime("Friday this week");


    $timestamp_lwk_revuew_req = (int)strtotime("$day_revew_req last week");
    $timestamp_nxt_revuew_req = (int)strtotime("$day_revew_req next week");

    $today_rvw_mrkr = (int)strtotime("Today");


    if($today_rvw_mrkr > $timestamp_day_revuew_req && $today_rvw_mrkr < $timestamp_nxt_revuew_req ){  // baddd
      if( $timestamp_day_revuew_req <  $today_rvw_mrkr && $today_rvw_mrkr <= $friday_revuew_req  ){
        $this->projects_m->prj_rvw_late($project_id,$date_day_revuew_req );
      }
    }

  }

  public function list_un_invoiced_rvw(){
    $this->user_model = new Users_m();
    $this->reports_m = new Reports_m();

    $static_defaults_q = $this->user_model->select_static_defaults();
    $getResult_a = $static_defaults_q->getResult();
    $static_defaults = array_shift($getResult_a) ;
    $day_revew_req = $static_defaults->prj_review_day;  
    $row_stat = ''; 
    //$timestamp_day_revuew_req =  strtotime("$day_revew_req this week");

    $timestamp_day_revuew_req = (int)strtotime("$day_revew_req this week");
    $monday_revuew_req = (int)strtotime("Monday this week");
    $friday_revuew_req = (int)strtotime("Friday this week");
    $today_rvw_mrkr = (int)strtotime("Today");


    $timestamp_lwk_revuew_req = (int)strtotime("$day_revew_req last week");
    $timestamp_nxt_revuew_req = (int)strtotime("$day_revew_req next week");

    $total_project_value = 0;
    $total_outstading_value = 0;
    $content = '';
    $inv_date_type = '';
    $records_num = '';  
    $current_day = date('d/m/Y');

    $extra_query = '';
    if( $this->session->get('user_role_id') == 3 || $this->session->get('user_role_id') == 20   ){
      $user_pm_id = $this->session->get('user_id');
      $extra_query .= " AND `project`.`project_manager_id` = '$user_pm_id' ";
    }

    if( $this->session->get('user_role_id') == 2 || $this->session->get('user_role_id') == 7   ){
      $user_pa_id = $this->session->get('user_id');
      $extra_query .= " AND `project`.`project_admin_id` = '$user_pa_id' ";
    }

    $extra_query .= " AND `project`.`job_type` != 'Company' ";


    $order_q = " ORDER BY UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`invoice_date_req`, '%d/%m/%Y') ) ASC ";
    $has_where = " WHERE  `invoice`.`is_invoiced` = '0' AND `invoice`.`is_paid` = '0'     AND UNIX_TIMESTAMP( STR_TO_DATE(`invoice`.`invoice_date_req`, '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('$current_day', '%d/%m/%Y') )    $extra_query    ";
    $table_q = $this->reports_m->select_list_invoice($has_where,'','','','','',$order_q);

    foreach ($table_q->getResult() as $row){

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

      $project_total_percent = number_format($project_total_percent,2);


      if($timestamp_lwk_revuew_req < $today_rvw_mrkr &&   $today_rvw_mrkr <= $timestamp_day_revuew_req ){

        if( $timestamp_lwk_revuew_req < $row->unix_review_date && $row->unix_review_date <= $timestamp_day_revuew_req  ){
          $row_stat = 'posted_rev';
        } else{
          $row_stat = 'needed_rev';
        }

      }else{

        if( $timestamp_day_revuew_req <  $row->unix_review_date && $row->unix_review_date <= $timestamp_nxt_revuew_req  ){
          $row_stat = 'posted_rev';
        } else{
          $row_stat = 'needed_rev';
        }

      }

      echo '<tr class="prj_rvw_rw '.$row_stat.'" id="'.$row->invoice_project_id.'-uninvoiced_prj_view" >';

$date_site_finish_tmspt = strtotime(date_format(date_create_from_format('d/m/Y', $row->date_site_finish), 'Y-m-d' ));


      echo '<td><strong><span class="hide">'.$date_site_finish_tmspt.'</span>  '.$row->date_site_finish.'</strong></td>';
      echo '<td><strong>'.$row->company_name.'</strong></td>';

      //echo '<td><strong class="prj_id_rvw">'.$row->invoice_project_id.'</strong> - '.$row->project_name.'</td>';

      echo '<td>
      <i class="fa fa-book  btn btn-sm btn-success view_notes_prjrvw" style="padding: 4px;"></i>
        <a href="'.site_url().'projects/update_project_details/'.$row->invoice_project_id.'?status_rvwprj=uninvoiced&pmr='.$row->project_manager_id.'" ><strong class="prj_id_rvw">'.$row->invoice_project_id.'</strong> - '.$row->project_name.'</a>
      </td>';


$invoice_date_req_tmspt = strtotime(date_format(date_create_from_format('d/m/Y', $row->invoice_date_req), 'Y-m-d' ));

      echo '<td><span class="hide">'.$invoice_date_req_tmspt.'</span><strong class="unset">'.$row->invoice_date_req.'</strong></td>';
      echo '<td>'.$progress_order.'</td><td>'.number_format($row->progress_percent,2).'</td><td>'.$project_total_percent.'</td><td class="rw_pm_slct hide">pm_'.$row->project_manager_id.'</td></tr>';
    } 
  }

  public function list_projects_by_job_date($this_year=''){
    $this->admin_m = new Admin_m();

    if($this_year == ''){
      $this_year = date('Y');
    }


    $user_role_id = $this->session->get('user_role_id');
    $is_admin = $this->session->get('is_admin');

    $authorize_role_id = 0;
    $doc_storage_defaults = $this->admin_m->fetch_default_doc_storage();
    $q_doc_storage_defaults = $doc_storage_defaults->getResult();
    foreach ($q_doc_storage_defaults as $default_doc_storage){
      $authorize_role_id = $default_doc_storage->authorize_role_id;
    }


    $default_doc_types = $this->admin_m->fetch_doc_storage_required_notification();
    $q_default_doc_types = $default_doc_types->getResult();
 
    $last_year = $this_year - 1;

    $q_list_projects_by_job_date = $this->projects_m->list_projects_by_job_date($this_year,$last_year);


    $has_data = $q_list_projects_by_job_date->getNumRows();

    $prj_line = 0;
    $doc_type = '';

    if($has_data > 0){

      $projects_by_job_date = $q_list_projects_by_job_date->getResult();
      foreach ($projects_by_job_date as $data){

        
        if($prj_line != $data->project_id){
    $doc_type = '';
        //  echo "<tr><td>";
          echo '<div class="pad-5 prj_files_group">
            <div class="btn btn-info btn-xs fa fa-code-fork prj_files_head" id="'.$data->project_id.'"  style="margin: -4px 0 0 0;" id=""></div> 
            &nbsp; '.$data->project_id.' - '.$data->project_name.'';

if($this->session->get('user_role_id') != 15  ):
            echo '<div class="pull-right btn btn-success btn-xs set_doc_storage" id="'.$data->project_id.'_project_set"  data-toggle="modal" data-target="#doc_storage" style="margin: -1px 0 0 0;">Upload</div>';
          
endif;

echo ' </div>';

          $prj_line = $data->project_id;
        }


        if($doc_type == ''){
          $doc_type = $data->doc_type_name;
          echo '<p class="uploaded_files_row no-m pad-5 cat_head_row pointer '.$data->project_id.'_files" id="catHead_'.$data->storage_doc_type_id.'_prjId_'.$data->project_id.'" style="display:none;"><strong>'.$doc_type.'</strong></p>';
        }else{
          if($doc_type != $data->doc_type_name){
            $doc_type = $data->doc_type_name;
            echo '<p class="uploaded_files_row no-m pad-5 cat_head_row pointer '.$data->project_id.'_files" id="catHead_'.$data->storage_doc_type_id.'_prjId_'.$data->project_id.'" style="display:none;"><strong>'.$doc_type.'</strong></p>';
          }
        }

        if($data->is_project_attachment == 1){
          $check = 'checked';
        }else{
          $check = '';
        }

        $need_authorization = 0;
        foreach ($q_default_doc_types as $required_doc_type){
          if($doc_type == $required_doc_type->doc_type_name){
            $need_authorization = 1;
          }
        }

 

        echo '<div class="pad-5 '.$data->project_id.'_files uploaded_files_row  data_files_row '.$data->project_id.'_prJCat_'.$data->storage_doc_type_id.' " style="display:none;"> &nbsp; 

          <span><em class="fa fa-level-up fa-lg fa-rotate-90" style="color: #269ABC;"></em> &nbsp;';

        if($need_authorization == 0):
          echo '<input type = "checkbox" name = "proj_attach" class="input_checkbox_attach_file" id = "attach_'.$data->storage_files_id.'" title = "Project attachments" onclick = "attach_to_project('.$data->storage_files_id.')" '.$check.'> &nbsp;';
        else:
          if($is_admin == 1 || $user_role_id == $authorize_role_id):
            if($data->is_authorized == 1):
              echo '<input type = "checkbox" name = "proj_attach" class="input_checkbox_attach_file" id = "attach_'.$data->storage_files_id.'" title = "Project attachments" onclick = "attach_to_project('.$data->storage_files_id.')" '.$check.'> &nbsp;';
            endif;
          else:
            if($data->is_authorized == 1){
              echo '<input type = "checkbox" name = "proj_attach" class="input_checkbox_attach_file" id = "attach_'.$data->storage_files_id.'" title = "Project attachments" onclick = "attach_to_project('.$data->storage_files_id.')" '.$check.' disabled> &nbsp;';
            }
          endif;
        endif;

        echo '<span class="file_link_download pointer">'.$data->file_name.'</span></span> 

          <span style=" background:#F7901E; font-size: 12px; padding: 1px 8px;    float: right;     border: 1px solid #864e11;  color: #fff;  height: 20px;    margin: 0px 5px;     border-radius: 10px;    display: block;"><em class="fa fa-calendar-o"></em> '.$data->date_upload.' &nbsp; '.$data->user_first_name.'</span>';



        echo '<a class="btn btn-xs btn-success pull-right m-left-15 m-right-10" href="'.site_url().'docs/stored_docs/'.urlencode($data->file_name).'" target="_blank" title="Download File">Download</a>';


        if($need_authorization == 0):
          if($this->session->get('user_role_id') != 15  ):

            echo '<em id="'.$data->storage_files_id.'" class="pointer fa fa-trash fa-lg pull-right del_stored_file" style="color: red; display:none; margin-top: 3px;"></em>';
              
          endif;
        else:
          if($is_admin == 1 || $user_role_id == $authorize_role_id):
            if($data->is_authorized == 0):
              echo '<button type = "button" class = "btn btn-success btn-xs pull-right" style = "font-size: 12px" onclick="approve_doc_type('.$data->storage_files_id.')">Approve</button>';
            endif;
            if($this->session->get('user_role_id') != 15  ):

              echo '<em id="'.$data->storage_files_id.'" class="pointer fa fa-trash fa-lg pull-right del_stored_file" style="color: red; display:none; margin-top: 3px;"></em>';
                
            endif;
          endif;
        endif;


          echo '</div>';

 




      //  echo "</td></tr>";  

      }


    }else{
      echo '<div id="" class=""><p>No Uploaded Files</p></div>';
    }

  }

  public function clear_apost(){
    foreach ($_POST as $key => $value) {
      $_POST[$key] = str_replace("'","&apos;",$value);
    }
  }

  public function add_doc_type(){
    $doc_type = 0;
    $this->clear_apost();
    $type_name = $this->request->getPost('type_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $doc_type = $this->request->getPost('doc_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($this->request->getPost('doc_type')  !==  null && $this->request->getPost('doc_type') > 0 ){
      $doc_type = $this->request->getPost('doc_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if(isset($type_name) && $type_name != ''){
      $this->projects_m->insert_doc_type($type_name,$doc_type);
    }

    if($this->request->getPost('doc_type')  !==  null && $this->request->getPost('doc_type') > 0 ){
      return redirect()->to('/projects/client_file_storage');
    }else{
      return redirect()->to('/projects/document_storage');
    }
  }



//review_code
  public function process_upload_file_storage(){
    $this->user_model = new Users_m();
    $this->company_m = new Company_m();


    $will_replace_existing = $_POST['will_replace_existing']; 
    $file_type = $_POST['doc_type_name'];
    $project_id = $_POST['doc_proj_id'] ?? null;
    $is_prj_scrn = $_POST['is_prj_scrn'] ?? 0;

    $time = time();
    $user_id = $this->session->get('user_id');
    $users_q = $this->user_model->fetch_user($user_id);

    $user_name = "";
    $user_email = "";


    $files = $_FILES;

    $client_id = 'NULL';

    if( isset($_POST['client']) && $_POST['client'] != '' ){
      $client_data_arr = explode('|', $_POST['client']);
      $client_id = $client_data_arr[1];
    }

    foreach ($users_q->getResultArray() as $users_row){
      $user_name = $users_row['user_first_name']." ".$users_row['user_last_name'];
      $user_email_id = $users_row['user_email_id'];

      $email_q = $this->company_m->fetch_email($user_email_id);
      foreach ($email_q->getResultArray() as $email_row){
        $user_email = $email_row['general_email'];
      }

    }

    $date_upload = date("d/m/Y");
    $path = "./docs/stored_docs";

    if(!is_dir($path)){
      mkdir($path, 0777);
    }

    $proj_q = $this->projects_m->select_particular_project($project_id);
    foreach ($proj_q->getResultArray() as $row){
      $project_manager_id = $row['project_manager_id'];
    }

    $pm_name = "";
    $pm_email = "";

    $pm_q = $this->user_model->fetch_user($project_manager_id);
    foreach ($pm_q->getResultArray() as $pm_row){
      $pm_name = $pm_row['user_first_name']." ".$pm_row['user_last_name'];
      $user_email_id = $pm_row['user_email_id'];

      $email_q = $this->company_m->fetch_email($user_email_id);
      foreach ($email_q->getResultArray() as $email_row){
        $pm_email = $email_row['general_email'];
      }
    }

   

    if ($this->request->getFileMultiple('doc_files')){
     foreach($this->request->getFileMultiple('doc_files') as $file){ // loop through files

      $file_name      = $file->getName();
      $file_name_arr  = explode('.',$file_name);
      $file_name_raw  = $file_name_arr[0];
      $file_ext       = $file_name_arr[1];

      if( isset($_POST['doc_proj_id']) && $_POST['doc_proj_id'] != '' ){
        $data_file_name = $project_id.'_'.$file_name_raw.'_'.$time.'.'.$file_ext;
      }else{
        $data_file_name = $file_name_raw.'_'.$time.'.'.$file_ext;
      }

      $file_name_set = str_replace(' ', '_', $data_file_name);
      $file_name_set_final = str_replace("'", '`', $file_name_set);
      $file_name_amp = str_replace('&', '_and_', $file_name_set_final);



      if ($file->isValid() && !$file->hasMoved()) {
        $file->move(ROOTPATH . $path.'/', $file_name_amp);
        $this->projects_m->insert_uploaded_file($file_name_amp,$file_type,$project_id,$client_id,$date_upload,$user_id,$will_replace_existing);

          //review_code
          // SEND NOTIFICATION
          if($will_replace_existing == 1){
            /*
            require_once('PHPMailer/class.phpmailer.php');
            require_once('PHPMailer/PHPMailerAutoload.php');

            $mail = new phpmailer(true);
            $mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
            $mail->port = 587;

            $mail->setFrom($user_email, $user_name);
            $mail->addReplyTo($user_email);
            $mail->addaddress($pm_email, $pm_name);
            $mail->addBCC('mark.obis2012@gmail.com');

            $mail->smtpdebug = 2;
            $mail->ishtml(true);

            $mail->Subject = "File upload for approval";
            $mail->Body    = "A file was uploaded to the doc storage of Project number: ".$project_id." and awaiting approval. Please visit this link to and go to doc storage to check: https://sojourn.focusshopfit.com.au/projects/view/".$project_id."/ds";
            sleep(0.5);

            if(!$mail->send()) {
              echo 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
            } else {
              echo "Email Sent Successfully";
            }
            */
          }
          // SEND NOTIFICATION
          //review_code


        }else{
          $upload_error = $file->getError();
          exit;
        }

      } // loop through files
    }


    if($is_prj_scrn == 1){
      return redirect()->to('/projects/view/'.$project_id);

    }else{

      if( isset($_POST['client']) && $_POST['client'] != '' ){
        return redirect()->to('/projects/client_file_storage');

      }else{
        return redirect()->to('/projects/document_storage');
      }

    }


  }
//review_code





  public function update_doc_type(){
    $this->clear_apost();
    $type_name = trim($_POST['type_name']);
    $type_id = trim($_POST['type_id']);
    
    $this->projects_m->update_type_name($type_name,$type_id);
  //  redirect('/projects/document_storage'); 

    echo '<script> window.history.back(); </script>';
  }

  public function delete_doc_type($type_id){
    $this->user_model = new Users_m();
    $this->projects_m->remove_doc_type($type_id); 

    $user_id =  $this->session->get('user_id');

    $type = 'Update';
    $actions = 'Deleted a doc type ID:'.$type_id;
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,'',$type);

  //  redirect('/projects/document_storage');

    echo '<script> window.history.back(); </script>';
  }



public function list_doc_type_storage( $view='select' , $doc_type_id=''){


    $q_list_doc_type = $this->projects_m->list_doc_type($doc_type_id);
    $doc_type = $q_list_doc_type->getResult();


    if($view == 'select'){
      foreach ($doc_type as $data){
        echo "<option value=\"$data->storage_doc_type_id\">".$data->doc_type_name."</option>";
      }
    }

    if($view == 'list_view'){
      foreach ($doc_type as $data){
        echo "<p id=\"".$data->storage_doc_type_id."\"><em class='fa fa-arrow-circle-right'></em> ".$data->doc_type_name;



 if($this->session->get('is_admin') == 1 || $this->session->get('user_id') == 6  ):
        echo '<em id="'.$data->storage_doc_type_id.'" class="pointer fa fa-pencil-square fa-lg pull-right edt_doctype" style="color: orange;   margin-top: 3px;"></em>';
endif;
        echo "</p>";

      }
    }

  }


  public function view($project_id,$curr_tab='',$variation_id=''){
    $this->admin_m = new Admin_m();
    $this->company_m = new Company_m();
    $this->user_model = new Users_m();
    $this->project_schedule_m = new Project_schedule_m();
    $this->induction_health_safety_m = new Induction_health_safety_m();

    $user_id = $this->session->get('user_id');

    if($user_id == '72'){
      return redirect()->to('/dashboard');
    }

    $user_role_id = $this->session->get('user_role_id');

    if($user_role_id == '15'){
      return redirect()->to('/projects');
    }


    $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
    foreach ($admin_defaults->getResult() as $row){
      $unaccepted_no_days = $row->unaccepted_no_days;
      $induction_work_value = $row->induction_work_value;
      $induction_commencement_date = $row->induction_commencement_date;
      // $induction_project_value = $row->induction_project_value;
      // $induction_categories = $row->induction_categories;
    }

    $restricted_cat = 0;
    $admin_cat = 0;
    $proj_q = $this->projects_m->fetch_complete_project_details($project_id);

    if( !($proj_q->getNumRows() > 0) ) { // if the opened project number is not an actual project, redurect to projects page
      return redirect()->to('/projects');
    }

    foreach ($proj_q->getResult() as $row) {

      $start_date = $row->date_site_commencement;
      if($row->job_date !== '' && $row->is_paid !== 0){
        $restricted_cat = 1;
        $has_unaccepted_date = $this->projects_m->has_unaccepted_date($project_id);
        if($has_unaccepted_date == 1){
          $this->projects_m->remove_unaccepted_date($project_id);
        }
      }else{
        $job_category = $row->job_category;
        $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
        foreach ($admin_defaults->getResult() as $row){
          $unaccepted_date_categories= $row->unaccepted_date_categories;
          $job_category_arr = explode(",",$unaccepted_date_categories);
          foreach ($job_category_arr as &$value) {
            if($value == $job_category){
              $admin_cat = 1;
            }
          }
        }

        if($admin_cat !== 1){
          $has_unaccepted_date = $this->projects_m->has_unaccepted_date($project_id);
          if($has_unaccepted_date == 0){
            
            if($start_date !== ""){
              $start_date_arr = explode('/',$start_date);
              $s_date_day = $start_date_arr[0];
              $s_date_month = $start_date_arr[1];
              $s_date_year = $start_date_arr[2];
              $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;

              $today = date('Y-m-d');
              $unaccepteddate =strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
              $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

              $unaccepteddate_arr = explode('-',$unaccepteddate);
              $u_date_day = $unaccepteddate_arr[2];
              $u_date_month = $unaccepteddate_arr[1];
              $u_date_year = $unaccepteddate_arr[0];
              $unaccepted_ddate = $u_date_day.'/'.$u_date_month.'/'.$u_date_year;



              
              if($unaccepteddate < $today){

                $logs_for_jobdate = $this->projects_m->get_recent_log_jobdate($project_id);

                if($logs_for_jobdate->getNumRows() > 0){
                  $data_result = $logs_for_jobdate->getResult();

                  $logs = array_shift($data_result);
                  //echo '<pre>';var_dump($logs );echo '</pre>'; 

                  $date_formatted = date_format(date_create_from_format('d/m/Y', $logs->date), 'Y-m-d');
                  $logged_date_time = $date_formatted.' '.$logs->time;


                  // Declare two dates
                  $start_date = strtotime($logged_date_time); // Start must be lower OLD DATE
                  $end_date = time();
                  $days_diff = (((($end_date - $start_date) / 60) / 60) / 24);



                  if($days_diff > 1){
                    // add email send-out here for feedback

                    if($job_category != 'Maintenance' && $job_category != 'Minor Works' && $job_category != 'Design Works'){
                      $this->process_feedback_unaccepted($project_id);
                    }


                    $this->projects_m->insert_unaccepted_date($project_id,$unaccepted_ddate);
                  }

                }else{

                    // add email send-out here for feedback

                    if($job_category != 'Maintenance' && $job_category != 'Minor Works' && $job_category != 'Design Works'){
                      $this->process_feedback_unaccepted($project_id);
                    }

                    $this->projects_m->insert_unaccepted_date($project_id,$unaccepted_ddate);
                }
              }
            }
          }
        }
      }
    }




    $q_proj = $this->projects_m->fetch_complete_project_details($project_id);
    if($q_proj->getNumRows() > 0){
      $resultArray_a = $q_proj->getResultArray();
      $data = array_shift($resultArray_a);

      $data['job_date_history'] = '';

      if($data['job_date'] == ''){
        $histry_jb_raw = $this->projects_m->fetch_job_date_history($project_id);
        $resultArray_b = $histry_jb_raw->getResultArray();
        $job_date_history = array_shift($resultArray_b);
        $data['job_date_history'] = $job_date_history['actions'] ?? null;
      }

      $admin_defaults_raw = $this->admin_m->latest_system_default($data['defaults_id']);
      $resultArray_c = $admin_defaults_raw->getResultArray();
      $admin_defaults = array_shift($resultArray_c);

      $markup_raw = $this->admin_m->fetch_markup($admin_defaults['markup_id']);
      $resultArray_d = $markup_raw->getResultArray();
      $markup_defaults = array_shift($resultArray_d);

      $q_project_notes = $this->projects_m->fetch_project_notes($data['notes_id']);
      $resultArray_e = $q_project_notes->getResultArray();
      $project_notes = array_shift($resultArray_e);
      $data['project_comments'] = $project_notes['comments'];

      $q_focus_company = $this->company_m->display_company_detail_by_id($data['focus_company_id']);
      $resultArray_f = $q_focus_company->getResultArray();
      $focus_company = array_shift($resultArray_f);
      $data['focus_company_id'] = $focus_company['company_id'];
      $data['focus_company_name'] = $focus_company['company_name'];


      if($data['is_pending_client'] == 0):
        $q_client_company = $this->company_m->display_company_detail_by_id($data['client_id']);
        $resultArray_g = $q_client_company->getResultArray();
        $client_company = array_shift($resultArray_g);
        $data['client_company_id'] = $client_company['company_id'];
        $data['client_company_name'] = $client_company['company_name'];

        $q_fetch_contact_details_primary = $this->company_m->fetch_contact_details_primary($client_company['company_id']);
        $resultArray_h = $q_fetch_contact_details_primary->getResultArray();
        $company_contact_details_primary_detail = array_shift($resultArray_h);

        $data['company_contact_details_area_code'] = $company_contact_details_primary_detail['area_code'] ?? null;
        $data['company_contact_details_office_number'] = $company_contact_details_primary_detail['office_number'] ?? null;
        $data['company_contact_details_direct_number'] = $company_contact_details_primary_detail['direct_number'] ?? null;
        $data['company_contact_details_mobile_number'] = $company_contact_details_primary_detail['mobile_number'] ?? null;
        $data['company_contact_details_after_hours'] = $company_contact_details_primary_detail['after_hours'] ?? null;
        $data['company_contact_details_general_email'] = $company_contact_details_primary_detail['general_email'] ?? null;
        $data['company_contact_details_direct'] = $company_contact_details_primary_detail['direct'] ?? null;
        $data['company_contact_details_accounts'] = $company_contact_details_primary_detail['accounts'] ?? null;
        $data['company_contact_details_maintenance'] = $company_contact_details_primary_detail['maintenance'] ?? null;

        $query_client_address = $this->company_m->fetch_complete_detail_address($client_company['address_id']);
        $resultArray_h = $query_client_address->getResultArray();
        $temp_data = array_shift($resultArray_h);
        $data['query_client_address_postcode'] = $temp_data['postcode'];
        $data['query_client_address_suburb'] = ucwords(strtolower($temp_data['suburb']));
        $data['query_client_address_po_box'] = $temp_data['po_box'];
        $data['query_client_address_street'] = ucwords(strtolower($temp_data['street']));
        $data['query_client_address_unit_level'] = ucwords(strtolower($temp_data['unit_level']));
        $data['query_client_address_unit_number'] = $temp_data['unit_number'];
        $data['query_client_address_state'] = $temp_data['name'];

        $q_contact_person = $this->company_m->fetch_all_contact_persons($data['primary_contact_person_id']);
        $resultArray_i = $q_contact_person->getResultArray();
        $contact_person = array_shift($resultArray_i);

        $data['contact_person_id'] = $contact_person['contact_person_id'];
        $data['contact_person_fname'] = $contact_person['first_name'];
        $data['contact_person_lname'] = $contact_person['last_name'];
        
        $q_fetch_phone = $this->company_m->fetch_phone($contact_person['contact_number_id']);
        $resultArray_j = $q_fetch_phone->getResultArray();
        $contact_person_phone = array_shift($resultArray_j);

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
      else:
        $data['client_company_id'] = $data['company_details_temp_id'];
        $data['client_company_name'] = $data['company_name'];

        $data['company_contact_details_area_code'] = "";
        $data['company_contact_details_office_number'] = "";
        $data['company_contact_details_direct_number'] = "";
        $data['company_contact_details_mobile_number'] = "";
        $data['company_contact_details_after_hours'] = "";
        $data['company_contact_details_general_email'] = "";
        $data['company_contact_details_direct'] = "";
        $data['company_contact_details_accounts'] = "";
        $data['company_contact_details_maintenance'] = "";

        $data['query_client_address_postcode'] = "";
        $data['query_client_address_suburb'] = "";
        $data['query_client_address_po_box'] = "";
        $data['query_client_address_street'] = "";
        $data['query_client_address_unit_level'] = "";
        $data['query_client_address_unit_number'] = "";
        $data['query_client_address_state'] = "";

        $data['contact_person_id'] = "";
        $data['contact_person_fname'] = $data['contact_person_fname'];
        $data['contact_person_lname'] = $data['contact_person_sname'];;

        $data['contact_person_phone_office'] = $data['contact_number'];
      endif;

      $query_address= $this->company_m->fetch_complete_detail_address($data['address_id']);
      $resultArray_k = $query_address->getResultArray();
      $temp_data = array_shift($resultArray_k);

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
      $resultArray_l = $p_query_address->getResultArray();
      $p_temp_data = array_shift($resultArray_l);
      $data['i_po_box'] = $p_temp_data['po_box'];
      $data['i_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
      $data['i_unit_number'] = $p_temp_data['unit_number'];
      $data['i_street'] = ucwords(strtolower($p_temp_data['street']));
      $data['i_suburb'] = ucwords(strtolower($p_temp_data['suburb']));
      $data['i_state'] = $p_temp_data['name'];
      $data['i_postcode'] = $p_temp_data['postcode'];
//    $data['postal_address_id'] = $company_detail['postal_address_id'];

      $data['i_shortname'] = $p_temp_data['shortname'];
      $data['i_state_id'] =  $p_temp_data['state_id'];
      $data['i_phone_area_code'] = $p_temp_data['phone_area_code'];

      $q_project_manager = $this->user_model->fetch_user($data['project_manager_id']);
      $resultArray_m = $q_project_manager->getResultArray();
      $project_manager = array_shift($resultArray_m);
      $data['pm_user_id'] = $project_manager['user_id'];
      $data['pm_user_first_name'] = $project_manager['user_first_name'];
      $data['pm_user_last_name'] = $project_manager['user_last_name'];

      $q_project_admin = $this->user_model->fetch_user($data['project_admin_id']);
      $resultArray_n = $q_project_admin->getResultArray();
      $project_admin = array_shift($resultArray_n);
      $data['pa_user_id'] = $project_admin['user_id'];
      $data['pa_user_first_name'] = $project_admin['user_first_name'];
      $data['pa_user_last_name'] = $project_admin['user_last_name'];

      $q_project_estiamator_id = $this->user_model->fetch_user($data['project_estiamator_id']);
      $resultArray_o = $q_project_estiamator_id->getResultArray();
      $project_estiamator = array_shift($resultArray_o);
      $data['pe_user_id'] = $project_estiamator['user_id'] ?? 0;
      $data['pe_user_first_name'] = $project_estiamator['user_first_name'] ?? null;
      $data['pe_user_last_name'] = $project_estiamator['user_last_name'] ?? null;


      if(isset($data['client_contact_person_id']) && $data['client_contact_person_id'] > 0){

        $q_project_cc_pm = $this->user_model->fetch_user($data['client_contact_person_id']);
        $resultArray_p = $q_project_cc_pm->getResultArray();
        $project_cc_pm = array_shift($resultArray_p);
        $data['cc_pm_user_id'] =  $project_cc_pm['user_id'];
        $data['cc_pm_user_first_name'] = $project_cc_pm['user_first_name'];
        $data['cc_pm_user_last_name'] = $project_cc_pm['user_last_name'];

      }else{

        $data['cc_pm_user_id'] =  0;
        $data['cc_pm_user_first_name'] = '';
        $data['cc_pm_user_last_name'] = '';
      }


      $lead_hand_id = $this->projects_m->get_project_sched_values($project_id);
      $ps_row1 = $lead_hand_id->getRow();

      if (!empty($ps_row1)){

        if ($ps_row1->leading_hand_id == 0){

          $q_proj_sched_details = $this->project_schedule_m->fetch_project_schedule($project_id);
          $data['proj_sched_details'] = $q_proj_sched_details->getRow();

          $q_proj_sched_details2 = $this->project_schedule_m->fetch_project_schedule($project_id);
          $proj_sched_details2 = $q_proj_sched_details2->getRow();

          $manual_const_details = $this->projects_m->get_manual_const($proj_sched_details2->project_schedule_id);
          $resultArray_q = $manual_const_details->getResultArray();
          $project_lead_hand = array_shift($resultArray_q);

          $data['lead_hand_user_id'] =  '0';
          $data['lead_hand_user_first_name'] = $project_lead_hand['lh_name'] ?? '';

        } else {
          $q_project_lead_hand = $this->user_model->fetch_user($ps_row1->leading_hand_id);
          $resultArray_r = $q_project_lead_hand->getResultArray();
          $project_lead_hand = array_shift($resultArray_r);
          $data['lead_hand_user_id'] =  $project_lead_hand['user_id'];
          $data['lead_hand_user_first_name'] = $project_lead_hand['user_first_name'];
          $data['lead_hand_user_last_name'] = $project_lead_hand['user_last_name'];
        }
      }

      if($data['job_category'] == 'Company'){
        $pg_markup = array(0,0,0,0);
        $data['min_markup'] = $pg_markup[1];
      }else{
        $pg_markup_raw = $this->fetch_mark_up_by($data['job_category'] , $markup_defaults['markup_id']);
        $pg_markup = explode('|',$pg_markup_raw);
        $data['min_markup'] = $pg_markup[1];
      }

// Joinery  
      $joinery_user_id = $data['joinery_selected_sender'];  
      $q_project_joinery= $this->user_model->fetch_user($joinery_user_id);  
      $resultArray_s = $q_project_joinery->getResultArray();
      $project_joinery = array_shift($resultArray_s);   
      //$data['lead_hand_user_id'] = $project_lead_hand['user_id'];   
      $data['joinery_user_first_name'] = $project_joinery['user_first_name'] ?? null;   
      $data['joinery_user_last_name'] = $project_joinery['user_last_name'] ?? null;   
// Joinery

      $shopping_center_q = $this->projects_m->select_shopping_center($data['address_id']);
      $resultArray_t = $shopping_center_q->getResultArray();
      $shopping_center = array_shift($resultArray_t);

      $data['shopping_center_id'] = $shopping_center['shopping_center_id'] ?? 0;
      /*
      $data['shopping_center_brand_name'] = $shopping_center['shopping_center_brand_name'];
      $data['shopping_common_name'] = $shopping_center['common_name'];
      */


      $data['shopping_center_brand_name'] = $data['shop_name'];
      $data['shopping_common_name'] = $data['shop_name'];


      $applied_admin_settings_raw = $this->display_project_applied_defaults($project_id);

      $project_totals_arr = $this->fetch_project_totals($project_id);

      $data = array_merge($data, $project_totals_arr);

      $data = array_merge($data, $applied_admin_settings_raw);

      $progress_report_defaults = $this->projects_m->fetch_progress_report_defaults($data['address_id']);
      $resultArray_u = $progress_report_defaults->getResultArray();
      $data['progress_report_defaults'] = array_shift($resultArray_u);

      $data['page_title'] = $data['project_id'].' - '.$data['project_name'];

      $data['restricted_cat'] = $restricted_cat;

      $data['screen'] = 'Project Details';

      $data['induction_exempted'] = $this->induction_project_exempted($data['project_id']);
      $data['induction_work_value'] = $induction_work_value;
      $video_generated = $this->induction_health_safety_m->fetch_induction_videos_generated($project_id);

      $data['induction_commencement_date'] = $induction_commencement_date;
      $data['video_generated'] = $video_generated;

      $static_defaults_q = $this->user_model->select_static_defaults();
      $resultArray_v = $static_defaults_q->getResultArray();
      $static_defaults = array_shift($resultArray_v);

      $data['works_restriction_categories'] = explode(",", $static_defaults['works_restriction_categories']);
      $data['variation_id']= $variation_id;
      $data['curr_tab'] = $curr_tab;
      $data['main_content'] = 'App\Modules\Projects\Views\projects_view';
      return view('App\Views\page',$data);
      
    }else{
      $data['variation_id']= $variation_id;
      $data['curr_tab'] = $curr_tab;
      return redirect()->to('/projects');
    }

  }


  public function quick_update(){

    $this->admin_m = new Admin_m();
    $this->users = new Users();
    $this->user_model = new Users_m();
    //

    $project_id = $_POST['project_id'];


    $project_details_raw = $this->projects_m->fetch_project_details($project_id);
    $getResultArray = $project_details_raw->getResultArray();  
    $project_details = array_shift($getResultArray);

    $system_default_raw = $this->admin_m->latest_system_default($project_details['defaults_id']);
    $getResultArray = $system_default_raw->getResultArray(); 
    $system_default = array_shift($getResultArray);

    $admin_defaults_q = $this->admin_m->fetch_admin_defaults($system_default['admin_default_id']); 
    $getResultArray = $admin_defaults_q->getResultArray(); 
    $admin_defaults = array_shift($getResultArray);
    $days_quote_deadline = $admin_defaults['days_quote_deadline'];

    $project_name = $this->request->getPost('project_name');

    $project_name = str_replace("'","&apos;",$project_name);



    $budget_estimate_total = str_replace (',','', $this->request->getPost('budget_estimate_total') );

    $client_po = $this->request->getPost('client_po');
    $site_labour_estimate = $this->request->getPost('site_labour_estimate');
    $site_start = $this->request->getPost('site_start');
    $site_finish = $this->request->getPost('site_finish');
    
    $is_double_time = $this->request->getPost('is_double_time');
    $type = 'Update';
    $attempt = 0;

    $job_date = '';

    $q_proj = $this->projects_m->fetch_complete_project_details($project_id);
    $getResultArray = $q_proj->getResultArray();
    $prev_project_details = array_shift($getResultArray);

    //if($this->invoice->if_project_invoiced_full($project_id) == 1){
      //$project_markup = $prev_project_details['markup'];
      //$install_time_hrs = $prev_project_details['install_time_hrs'];
    //}else{
      $project_markup = $this->request->getPost('project_markup');
      $install_time_hrs = $this->request->getPost('install_time_hrs');
    //}



    if($this->session->get('is_admin') == 1 || $this->session->get('job_date') == 1 || ( $this->session->get('user_role_id') == 7 && $prev_project_details['job_category'] == 'Maintenance' ) || ( $this->session->get('company_project') == 1 &&      $prev_project_details['job_category'] == 'Company'          ) ){
      
      if(isset( $_POST['job_date']  ) &&  $_POST['job_date'] != ''){
        $job_date = $_POST['job_date'];
      }else{
        $job_date = '';
      }

    }else{
      if($prev_project_details['job_date'] == ''){        

        if(isset( $_POST['job_date']  ) &&  $_POST['job_date'] != ''){
          $job_date = $_POST['job_date'];
        }else{
          $job_date = '';
        }


      }else{
        $job_date = $prev_project_details['job_date'];
        $attempt = 1;
      }
    }

    $person_did = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");


    if($_POST['project_markup'] != $prev_project_details['markup']){
      $type = 'Project Markup';
      $actions = 'Changed the project markup from '.$prev_project_details['markup'].'% to '.$_POST['project_markup'].'%';
      $this->user_model->insert_user_log($person_did,$date,$time,$actions,$project_id,$type);
    }


    if( $_POST['install_time_hrs'] != $prev_project_details['install_time_hrs']){
      $type = 'Site Hours';
      $actions = 'Changed the site hours from '.$prev_project_details['install_time_hrs'].' to '.$_POST['install_time_hrs'].'';
      $this->user_model->insert_user_log($person_did,$date,$time,$actions,$project_id,$type);
    }

    $unaccepted_date = "";      
    if(isset($_POST['unaccepted_date']) ){
      $unaccepted_date = $_POST['unaccepted_date'] ?? null;

      if($unaccepted_date){

        $date_parts_arr = explode('/',$unaccepted_date);
        if( checkdate($date_parts_arr[1], $date_parts_arr[0],$date_parts_arr[2]) ) {
          if($prev_project_details['job_category'] != 'Maintenance' && $prev_project_details['job_category'] != 'Minor Works' && $prev_project_details['job_category'] != 'Design Works'){
            $this->process_feedback_unaccepted($project_id);
          }
        }else{
          $unaccepted_date = '';
        }

      }

      


    }

    if(strlen($job_date) > 0 && $job_date != ''){
      $is_wip = 1;
    }else{
      $is_wip = 0;
    }

    if($site_finish != ''){
    //  $this->projects_m->update_vr_inv_date($site_finish,$site_finish,$project_id );
    }

    if(strlen($job_date) == 0 && $prev_project_details['job_date'] == ''){
      $actions = 'Updated project details, with no job date';
    }elseif($job_date == $prev_project_details['job_date']){
      $actions = 'Updated project details';
    }elseif($job_date != $prev_project_details['job_date'] && strlen($job_date) > 0 && strlen($prev_project_details['job_date']) > 0 ){
      $actions = 'Replaced job date from '.$prev_project_details['job_date'].' to '.$job_date;
    }elseif($job_date != $prev_project_details['job_date'] && strlen($job_date) == 0){
      $actions = 'Removed job date: '.$prev_project_details['job_date'];
    }else{
    //  $actions = 'Added job new date: '.$job_date;

      $q_get_log_prjId = $this->projects_m->check_log_jobdate($project_id);
      $getResultArray = $q_get_log_prjId->getResultArray();
      $prj_log = array_shift($getResultArray);

// === Edited has error if null Mark ======
      if(is_null($prj_log)):
         $log_job_date = "01/01/2014";
      else:
        $prj_log_arr = explode(':', $prj_log['actions'] );
        if(count($prj_log_arr) == 1){
          $log_job_date = "01/01/2014";
        }else{
          $log_job_date = str_replace(' ','', $prj_log_arr[1]);
        }
      endif;
      //$log_job_date = str_replace(' ','', $prj_log_arr[1]);
// === Edited has error if null Mark======
      if( strtotime($job_date) == strtotime($log_job_date) ){
        $actions = 'Restored old job date: '.$job_date;
      }else{
        $actions = 'Added job new date: '.$job_date;
        $this->projects_m->enter_job_date($project_id);
      }
    }

    if($attempt == 1){
      $actions = 'Updated project details';
    }


    $has_updated_wip = 0;

    if($install_time_hrs != '' && $install_time_hrs > 0){
      $prj_install_hrs = $install_time_hrs;
    }else{
      $prj_install_hrs = $site_labour_estimate;
    }

    $proj_q = $this->projects_m->select_particular_project($project_id);
    foreach ($proj_q->getResultArray() as $row){
      $proj_markup = $row['markup'];
    }



    $days_quote_deadline = $admin_defaults['days_quote_deadline'];

    $formated_start_date = str_replace("/","-",$site_start);
    $date_quote_deadline =  date('d/m/Y', strtotime("$formated_start_date -$days_quote_deadline days"));

    if(isset($_POST['quote_deadline']) && $_POST['quote_deadline'] != ''){
      $date_quote_deadline = $_POST['quote_deadline'];
    }
    
    $has_updated_wip = $this->projects_m->set_wip_project($project_id,$is_wip);


    $rev_date  = date("d/m/Y");


    $this->projects_m->project_details_quick_update($project_id,$project_name,$budget_estimate_total,$job_date,$date_quote_deadline,$client_po,$install_time_hrs,$project_markup,$site_start,$site_finish,$unaccepted_date,$rev_date);
    
    $this->update_install_cost_total($project_id,$prj_install_hrs,$is_double_time);





//$prev_project_details['job_date']


    $static_defaults_q = $this->user_model->select_static_defaults();
    $getResult = $static_defaults_q->getResult();
    $static_defaults = array_shift($getResult );

    $day_revew_req = $static_defaults->prj_review_day;

    $timestamp_day_revuew_req = (int)strtotime("$day_revew_req this week");
    $monday_revuew_req = (int)strtotime("Monday this week");
    $friday_revuew_req = (int)strtotime("Friday this week");

    $date_day_revuew_req = date('d/m/Y',$timestamp_day_revuew_req);
    $today_rvw_mrkr = (int)strtotime("Today");

    $q_prj_rvw = $this->projects_m->get_prj_rvw($date_day_revuew_req,$project_id);


    if($q_prj_rvw->getNumRows() === 1){ // if found data
      $this->projects_m->update_set_wip_rvw($project_id,$date_day_revuew_req,$rev_date);
    }else{
      $this->projects_m->insert_wip_rvw($project_id, $date_day_revuew_req, $rev_date);
    }

    // if($today_rvw_mrkr > $timestamp_day_revuew_req){  // baddd
    //  if( $timestamp_day_revuew_req <  $today_rvw_mrkr && $today_rvw_mrkr <= $friday_revuew_req  ){
    //    $this->projects_m->prj_rvw_late($project_id,$date_day_revuew_req );
    //  }
    // }



    $timestamp_lwk_revuew_req = (int)strtotime("$day_revew_req last week");
    $timestamp_nxt_revuew_req = (int)strtotime("$day_revew_req next week");

if($today_rvw_mrkr > $timestamp_day_revuew_req && $today_rvw_mrkr < $timestamp_nxt_revuew_req ){  // baddd
  if( $timestamp_day_revuew_req <  $today_rvw_mrkr && $today_rvw_mrkr <= $friday_revuew_req  ){
    $this->projects_m->prj_rvw_late($project_id,$date_day_revuew_req );
  }
}





    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
    

    if($proj_markup !== $project_markup):
      $work_q = $this->works_m->display_all_works($project_id);
      $joinery_works_id = "";
      foreach ($work_q->getResultArray() as $row){
        $works_id = $row['works_id'];
        if($row['work_con_sup_id'] == '53'){
          $joinery_works_id = $works_id;
        }
        $this->works_m->update_works(1,$works_id,"",$project_markup,"","","","","","","","","","","","","","","","","","","","","","");
        //$this->works_m->update_works(1,$works_id,"",$project_markup,"","","","","","","","","","","","","","","","","","","","","");
        $work_estimate = $row['work_estimate'];
        $quote = $work_estimate + ($work_estimate * ($project_markup/100));
        $this->works_m->update_works(8,$works_id,"","","","","","","","","","","","","","","","","","","","","",$work_estimate,$quote,"");
        //$this->works_m->update_works(8,$works_id,"","","","","","","","","","","","","","","","","","","","","",$work_estimate,$quote);
      }

      if($joinery_works_id !== "" ){
        $work_joinery_q = $this->works_m->display_all_works_joinery($joinery_works_id);
        foreach ($work_joinery_q->getResultArray() as $row){
          $joinery_id = $row['work_joinery_id'];
          $joinery_estimate = $row['j_estimate'];
            $joiner_quoted = $joinery_estimate +  ($joinery_estimate * ($project_markup/100));
            $this->works_m->update_joinery_markup($joinery_id,$project_markup,$joiner_quoted);
        }

        $work_joinery = $this->works_m->display_all_works_joinery($joinery_works_id);
            $t_price = 0;
            $t_estimate = 0;
            $t_quote = 0;
            foreach ($work_joinery->getResultArray() as $row)
        {
           $t_price = $t_price + $row['j_price'];
           $t_estimate = $t_estimate + $row['j_estimate'];
           $t_quote = $t_quote + $row['j_quote'];
        }

        $this->works_m->update_work_joinery($joinery_works_id,$t_price,$t_estimate,$t_quote);
      }
      
//== Total Works in project
      $works_q = $this->works_m->display_all_works($project_id);
      $t_price = 0;
      $t_estimate = 0;
      $t_quoted = 0;
      foreach ($works_q->getResultArray() as $row){
        $t_price = $t_price + $row['price'];
        $t_estimate = $t_estimate + $row['work_estimate'];
        $t_quoted = $t_quoted + $row['total_work_quote'];
      }

      $this->projects_m->update_project_cost_total($project_id,$t_price,$t_estimate,$t_quoted);

    endif;




    $this->session->setFlashdata('curr_tab', 'project-details'); 
    $this->session->setFlashdata('quick_update', 'Values are now updated!');


    if($prev_project_details['date_site_finish'] != $site_finish){
      $this->users->notify_changed_completion_date($project_id,$project_name);
    }


    if($has_updated_wip == 1){
      return redirect()->to('/projects/view/'.$project_id);
    }

  }

  public function update_project_details($project_id){
    $this->users = new Users();

    $this->company = new Company();
    $this->company_m = new Company_m();

    $this->admin_m = new Admin_m();
    $this->user_model = new Users_m();

    $this->invoice = new Invoice();

    $this->users->_check_user_access('projects',2);

    $rules = array();
    $data = array();

    $data['project_id'] = $project_id;

    $client_type = $this->request->getPost('client_type');

    $q_proj = $this->projects_m->fetch_complete_project_details($project_id);
    if($q_proj->getNumRows() > 0){
      $getResultArray = $q_proj->getResultArray();
      $data = array_shift($getResultArray);

      $data['job_date_history'] = '';

      if($data['job_date'] == ''){
        $histry_jb_raw = $this->projects_m->fetch_job_date_history($project_id);
        $getResultArray = $histry_jb_raw->getResultArray();
        $job_date_history = array_shift($getResultArray);
        $data['job_date_history'] = $job_date_history['actions'] ?? null;
      }

      $contact_person_name = "";
      $contact_person_number = "";
      $contact_person_mobile = "";
      $contact_person_email = "";

      $q_proj_site_cont = $this->projects_m->fetch_project_site_contact($project_id);
      foreach ($q_proj_site_cont->getResultArray() as $row){
        $contact_person_name = $row['contact_person_name'];
        $contact_person_number = $row['contact_person_number'];
        $contact_person_mobile = $row['contact_person_mobile'];
        $contact_person_email = $row['contact_person_email'];
      }

      $data['contact_person_name'] = $contact_person_name;
      $data['contact_person_number'] = $contact_person_number;
      $data['contact_person_mobile'] = $contact_person_mobile;
      $data['contact_person_email'] = $contact_person_email;

      $system_default_raw = $this->admin_m->latest_system_default($data['defaults_id']);
      $getResultArray = $system_default_raw->getResultArray();
      $system_default = array_shift($getResultArray);

      $admin_defaults_q = $this->admin_m->fetch_admin_defaults($system_default['admin_default_id']); 
      $getResultArray = $admin_defaults_q->getResultArray();
      $admin_defaults = array_shift($getResultArray);

      $days_quote_deadline = $admin_defaults['days_quote_deadline'];


      $markup_raw = $this->admin_m->fetch_markup($system_default['markup_id']);
      $getResultArray = $markup_raw->getResultArray();
      $markup_defaults = array_shift($getResultArray);

      $q_project_notes = $this->projects_m->fetch_project_notes($data['notes_id']);
      $getResultArray = $q_project_notes->getResultArray();
      $project_notes = array_shift($getResultArray);
      $data['project_comments'] = $project_notes['comments'];

      $q_focus_company = $this->company_m->display_company_detail_by_id($data['focus_company_id']);
      $getResultArray = $q_focus_company->getResultArray();
      $focus_company = array_shift($getResultArray);
      $data['focus_company_id'] = $focus_company['company_id'];
      $data['focus_company_name'] = $focus_company['company_name'];

      
      $maintenance_administrator = $this->user_model->fetch_user_by_role(7);
      $data['maintenance_administrator'] = $maintenance_administrator->getResult();

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

      $query_address= $this->company_m->fetch_complete_detail_address($data['address_id']);
      $getResultArray = $query_address->getResultArray();
      $temp_data = array_shift($getResultArray);
      $data['postcode'] = $temp_data['postcode'];
      $data['suburb'] = $temp_data['suburb'];
      $data['po_box'] = $temp_data['po_box'];
      $data['street'] = ucwords(strtolower($temp_data['street']));
      $data['unit_level'] = ucwords(strtolower($temp_data['unit_level']));
      $data['unit_number'] = $temp_data['unit_number'];
      $data['state'] = $temp_data['name'];

      $data['shortname'] = $temp_data['shortname'];
      $data['state_id'] =  $temp_data['state_id'];
      $data['phone_area_code'] = $temp_data['phone_area_code']; 

      $invoice_address_id = $data['invoice_address_id'];

      $p_query_address = $this->company_m->fetch_complete_detail_address($data['invoice_address_id']);
      $getResultArray = $p_query_address->getResultArray();
      $p_temp_data = array_shift($getResultArray);
      $data['i_po_box'] = $p_temp_data['po_box'];
      $data['i_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
      $data['i_unit_number'] = $p_temp_data['unit_number'];
      $data['i_street'] = ucwords(strtolower($p_temp_data['street']));
      $data['i_suburb'] = $p_temp_data['suburb'];
      $data['i_state'] = $p_temp_data['name'];
      $data['i_postcode'] = $p_temp_data['postcode'];
      //$data['i_address_id'] = $p_temp_data['postal_address_id'];

      $data['i_shortname'] = $p_temp_data['shortname'];
      $data['i_state_id'] =  $p_temp_data['state_id'];
      $data['i_phone_area_code'] = $p_temp_data['phone_area_code'];

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
      $data['pe_user_id'] = $project_estiamator['user_id'];
      $data['pe_user_first_name'] = $project_estiamator['user_first_name'];
      $data['pe_user_last_name'] = $project_estiamator['user_last_name'];

      $q_project_cc_pm = $this->user_model->fetch_user($data['client_contact_person_id']);
      $getResultArray = $q_project_cc_pm->getResultArray();
      $project_cc_pm = array_shift($getResultArray);
      $data['cc_pm_user_id'] =  $project_cc_pm['user_id'];
      $data['cc_pm_user_first_name'] = $project_cc_pm['user_first_name'];
      $data['cc_pm_user_last_name'] = $project_cc_pm['user_last_name'];

      $q_proj_sched_pr = $this->projects_m->get_project_sched_for_pr($project_id);

      if($q_proj_sched_pr->getNumRows() > 0){

        $proj_sched_pr = $q_proj_sched_pr->getRow();

        $q_project_lead_hand = $this->user_model->fetch_user($proj_sched_pr->leading_hand_id);
        $getResultArray = $q_project_lead_hand->getResultArray();
        $project_lead_hand = array_shift($getResultArray);
        $data['lead_hand_user_id'] =  $project_lead_hand['user_id'] ?? 0;
        $data['lead_hand_user_first_name'] = $project_lead_hand['user_first_name'] ?? '';
        $data['lead_hand_user_last_name'] = $project_lead_hand['user_last_name'] ?? '';

        if ($this->request->getPost('leading_hand') == 0){
        
          $q_manual_lh = $this->projects_m->get_manual_const($proj_sched_pr->project_schedule_id);
          $getResultArray = $q_manual_lh->getResultArray();
          $manual_lh = array_shift($getResultArray);
          $data['manual_lh_name'] = $manual_lh['lh_name'] ?? null;
          $data['manual_lh_contact'] = $manual_lh['lh_contact'] ?? null;
        }
      } else {
        $new_project_sched_id = $this->projects_m->insert_new_project_sched_for_pr($project_id, 0);
        $q_proj_sched_pr = $this->projects_m->get_project_sched_for_pr($project_id);

        $proj_sched_pr = $q_proj_sched_pr->row();

        $q_project_lead_hand = $this->user_model->fetch_user($proj_sched_pr->leading_hand_id);
        $getResultArray = $q_project_lead_hand->getResultArray();
        $project_lead_hand = array_shift($getResultArray);
        $data['lead_hand_user_id'] =  $project_lead_hand['user_id'];
        $data['lead_hand_user_first_name'] = $project_lead_hand['user_first_name'];
        $data['lead_hand_user_last_name'] = $project_lead_hand['user_last_name'];

        if ($this->request->getPost('leading_hand') == 0){
        
          $q_manual_lh = $this->projects_m->get_manual_const($proj_sched_pr->project_schedule_id);
          $getResultArray = $q_manual_lh->getResultArray();
          $manual_lh = array_shift($getResultArray);
          $data['manual_lh_name'] = $manual_lh['lh_name'] ?? null;
          $data['manual_lh_contact'] = $manual_lh['lh_contact'] ?? null;
        }

      }

      if($data['job_category'] == 'Company'){
        $pg_markup = array(0,0,0,0);
        $data['min_markup'] = $pg_markup[1];
      }else{
        $pg_markup_raw = $this->fetch_mark_up_by($data['job_category'] , $markup_defaults['markup_id']);
        $pg_markup = explode('|',$pg_markup_raw);
        $data['min_markup'] = $pg_markup[1];
      }

      $shopping_center_q = $this->projects_m->select_shopping_center($data['address_id']);
      $getResultArray = $shopping_center_q->getResultArray();
      $shopping_center = array_shift($getResultArray);

      $data['shopping_center_id'] = $shopping_center['shopping_center_id'] ?? 0;
      $data['shopping_center_brand_name'] = $shopping_center['shopping_center_brand_name'] ?? '';
      $data['shopping_common_name'] = $shopping_center['common_name'] ?? '';

      $all_company_list = $this->company_m->fetch_all_company(NULL);
      if($all_company_list->getNumRows() > 0){
        $data['all_company_list'] = $all_company_list->getResult();
      }

      $all_aud_states = $this->company_m->fetch_all_states();
      $data['all_aud_states'] = $all_aud_states->getResult();

      $focus = $this->admin_m->fetch_all_company_focus();
      $data['focus'] = $focus->getResult();

      $project_manager = $this->user_model->fetch_user_by_role(3);
      $data['project_manager'] = $project_manager->getResult();

      $account_manager = $this->user_model->fetch_user_by_role(20);
      $data['account_manager'] = $account_manager->getResult();

      $project_administrator = $this->user_model->fetch_user_by_role(2);
      $data['project_administrator'] = $project_administrator->getResult();

      $estimator = $this->user_model->fetch_user_by_role(8);
      $data['estimator'] = $estimator->getResult();    

      $leading_hand = $this->user_model->fetch_user_by_role(15);
      $data['leading_hand'] = $leading_hand->getResult();

      $shopping_center = $this->projects_m->fetch_shopping_center();
      $data['shopping_center'] = $shopping_center->getResult();

      $q_warranty_categories = $this->projects_m->fetch_warranty_categories();
      $getResultArray = $q_warranty_categories->getResultArray();
      $data['warranty_categories'] = array_shift($getResultArray);


      $rules[ 'project_name' ] = ['project_name' => 'Project Name', 'rules' => ['required','trim','max_length[35]'] ];
      $rules[ 'site_start' ] = ['site_start' => 'Site Start', 'rules' => ['required','trim'] ];


      $rules[ 'site_finish' ] = ['site_finish' => 'Site Finish', 'rules' => ['required','trim'] ];
      $rules[ 'job_category' ] = ['job_category' => 'Job Category', 'rules' => ['required','trim'] ];
      $rules[ 'brand_name' ] = ['brand_name' => 'Brand', 'rules' => ['required','trim'] ];
      $rules[ 'project_date' ] = ['project_date' => 'Project Date', 'rules' => ['required','trim'] ];

      if( $this->request->getPost('job_category') != 'Maintenance' && 
          $this->request->getPost('job_category') != 'Minor Works' && 
          $this->request->getPost('job_category') != 'Strip Out' && 
          $this->request->getPost('job_category') != 'Design Works' && $this->request->getPost('project_area') < 10){
        $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['required','trim','greater_than[0]'] ];
      }else{
        $rules[ 'project_area' ] = ['project_area' => 'Project Area', 'rules' => ['trim'] ];
      }

      $project_area = $this->request->getPost('project_area');

      $rules[ 'job_date' ] = ['job_date' => 'Job Date', 'rules' => ['trim'] ];


      if( $this->request->getPost('is_shopping_center') != 1){
        $rules[ 'street' ] = ['street' => 'Site Street', 'rules' => ['required','trim'] ];
        $rules[ 'suburb_a' ] = ['suburb_a' => 'Site Project Address Suburb', 'rules' => ['required','trim'] ];
        $rules[ 'state_a' ] = ['state_a' => 'Site State', 'rules' => ['required','trim'] ];
        $rules[ 'postcode_a' ] = ['postcode_a' => 'Site Postcode', 'rules' => ['required','trim'] ];

      }else{

        $rules[ 'shop_tenancy_number' ] = ['shop_tenancy_number' => 'Site Shop/Tenancy Number', 'rules' => ['required','trim'] ];
        $rules[ 'brand_shopping_center' ] = ['brand_shopping_center' => 'Site Brand/Shopping Center', 'rules' => ['required','trim'] ];
      }


      if($client_type == 0):
        $rules[ 'street_b' ] = ['street_b' => 'Invoice Street', 'rules' => ['required','trim'] ];
        $rules[ 'suburb_b' ] = ['suburb_b' => 'Invoice Address Suburb', 'rules' => ['required','trim'] ];
        $rules[ 'state_b' ] = ['state_b' => 'Invoice State', 'rules' => ['required','trim'] ];
        $rules[ 'postcode_b' ] = ['postcode_b' => 'Invoice Postcode', 'rules' => ['required','trim'] ];

      endif;

      $rules[ 'project_manager' ] = ['project_manager' => 'Project Manager', 'rules' => ['required','trim'] ];
      $rules[ 'project_manager' ] = ['project_manager' => 'Project Admin', 'rules' => ['required','trim'] ];
      $rules[ 'project_manager' ] = ['project_manager' => 'Estimator', 'rules' => ['required','trim'] ];


      if($client_type == 0):
        $rules[ 'company_prg' ] = ['company_prg' => 'Company Client', 'rules' => ['required','trim'] ];
        $rules[ 'contact_person' ] = ['contact_person' => 'Contact Person', 'rules' => ['required','trim'] ];
      endif;

      $rules[ 'install_hrs' ] = ['install_hrs' => 'Site Hours', 'rules' => ['trim'] ];
      $rules[ 'proj_joinery_user' ] = ['proj_joinery_user' => 'Joinery Personel', 'rules' => ['trim'] ];


      $data['page_title'] = 'Update Project: '.$data['project_name'].' - '.$data['project_id'];
      $data['screen'] = 'Update Project Details';
      $data['main_content'] = 'App\Modules\Projects\Views\project_detail_update';

 


      // validation checks here
      if($_SERVER['REQUEST_METHOD'] === 'POST'): // on load if form is usbmitted

        if($this->validate($rules)): // form is valid and process form
        // proceed insert and redirect here


      



        $focus_id = $this->request->getPost('focus');
        $project_name = $this->request->getPost('project_name');
        $client_po = $this->request->getPost('client_po');
        $job_type = $this->request->getPost('job_type');
        $job_category = $this->request->getPost('job_category');
        $project_date = $this->request->getPost('project_date');
        $site_start = $this->request->getPost('site_start');
        $site_finish = $this->request->getPost('site_finish');
        $proj_joinery_user = $this->request->getPost('proj_joinery_user');

        //================= For Maintenance Site sheet ===============
        if($job_category == 'Maintenance'):
          $site_cont_person = $this->request->getPost('site_cont_person');
          $site_cont_number = $this->request->getPost('site_cont_number');
          $site_cont_mobile = $this->request->getPost('site_cont_mobile');
          $site_cont_email = $this->request->getPost('site_cont_email');

          $proj_site_cont = $this->projects_m->fetch_project_site_contact($project_id);
          if($proj_site_cont->getNumRows() == 0){
            $this->projects_m->insert_project_site_contact($project_id, $site_cont_person, $site_cont_number, $site_cont_mobile, $site_cont_email);
            date_default_timezone_set("Australia/Perth");
            $user_id = $this->session->get('user_id');
            $date = date("d/m/Y");
            $time = date("H:i:s");
            $type = "INSERT";
            $actions = "Insert project #".$project_id." site contacts with Contact Person: ".$site_cont_person.", Contact Number: ".$site_cont_number.", Mobile Number: ".$site_cont_mobile.", Email: ".$site_cont_email;
            $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
          }else{
            $this->projects_m->update_project_site_contact($project_id, $site_cont_person, $site_cont_number, $site_cont_mobile, $site_cont_email);
            date_default_timezone_set("Australia/Perth");
            $user_id = $this->session->get('user_id');
            $date = date("d/m/Y");
            $time = date("H:i:s");
            $type = "Update";
            $actions = "Update project #".$project_id." site contacts with Contact Person: ".$site_cont_person.", Contact Number: ".$site_cont_number.", Mobile Number: ".$site_cont_mobile.", Email: ".$site_cont_email;
            $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
          }

          
        endif;
        //================= For Maintenance Site sheet =============== 

        $q_proj_prev = $this->projects_m->fetch_complete_project_details($project_id);
        $getResultArray = $q_proj_prev->getResultArray();
        $prev_project_details = array_shift($getResultArray);

        $attempt = 0;
        if($this->session->get('is_admin') == 1 || $this->session->get('job_date') == 1 || ( $this->session->get('user_role_id') == 7 && $prev_project_details['job_category'] == 'Maintenance' ) || ( $this->session->get('company_project') == 1 && $prev_project_details['job_category'] == 'Company' ) ){
          $job_date = $this->request->getPost('job_date');
        }else{
          if($prev_project_details['job_date'] == ''){
            $job_date = $this->request->getPost('job_date');
          }else{
            $job_date = $prev_project_details['job_date'];
            $attempt = 1;
          }
        }

        $is_wip = ($job_date != '' ? 1 : 0);

        if(strlen($job_date) == 0 && $prev_project_details['job_date'] == ''){
          $actions = 'Updated project details, with no job date';
        }elseif($job_date == $prev_project_details['job_date']){
          $actions = 'Updated project details';
        }elseif($job_date != $prev_project_details['job_date'] && strlen($job_date) > 0 && strlen($prev_project_details['job_date']) > 0 ){
          $actions = 'Replaced job date from '.$prev_project_details['job_date'].' to '.$job_date;
        }elseif($job_date != $prev_project_details['job_date'] && strlen($job_date) == 0){
          $actions = 'Removed job date: '.$prev_project_details['job_date'];
        }else{

          $q_get_log_prjId = $this->projects_m->check_log_jobdate($project_id);
          $getResultArray = $q_get_log_prjId->getResultArray();
          $prj_log = array_shift($getResultArray);

          $prj_log_arr = explode(':', $prj_log['actions'] );
          if(count($prj_log_arr) == 1){
            $log_job_date = "01/01/2014";
          }else{
            $log_job_date = str_replace(' ','', $prj_log_arr[1]);
          }

          if( strtotime($job_date) == strtotime($log_job_date) ){
            $actions = 'Restored old job date: '.$job_date;
          }else{
            $actions = 'Added job new date: '.$job_date;
            $this->projects_m->enter_job_date($project_id);
          }
        }

        if($attempt == 1){
          $actions = 'Updated project details';
        }

        $data['unit_level'] = $this->company->if_set ($this->request->getPost('unit_level'));
        $data['unit_number'] = $this->company->if_set($this->request->getPost('unit_number'));
        $data['street'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street')));
        $data['postcode_a'] = $this->company->if_set($this->request->getPost('postcode_a'));

        $state_a_arr = explode('|', $this->request->getPost('state_a')); 
        $data['state_a'] = $state_a_arr[3];


        $suburb_a_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
        $data['suburb_a'] = strtoupper($suburb_a_ar[0]);

        if($client_type == 0):
          $data['pobox'] = $this->company->if_set($this->request->getPost('pobox'));
          $data['unit_level_b'] = $this->company->if_set($this->request->getPost('unit_level_b'));
          $data['number_b'] = $this->company->if_set($this->request->getPost('number_b'));     
          $data['street_b'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street_b')));
          $data['postcode_b'] = $this->company->if_set($this->request->getPost('postcode_b'));
  

          $state_b_arr = explode('|', $this->request->getPost('state_b'));
          $data['state_b'] = $state_b_arr[3];

          $suburb_b_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_b')));
          $data['suburb_b'] = strtoupper($suburb_b_ar[0]);
        else:
          $data['pobox'] = "";
          $data['unit_level_b'] = $this->company->if_set($this->request->getPost('unit_level'));
          $data['number_b'] = $this->company->if_set($this->request->getPost('unit_number'));      
          $data['street_b'] = $this->company->cap_first_word($this->company->if_set($this->request->getPost('street')));
          $data['postcode_b'] = $this->company->if_set($this->request->getPost('postcode_a'));

          ///$state_b_arr = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
          $data['state_b'] = $data['state_a'];

          $suburb_b_ar = explode('|',$this->company->if_set($this->request->getPost('suburb_a')));
          $data['suburb_b'] = strtoupper($suburb_b_ar[0]);
        endif;
        

        $project_manager_id = $this->request->getPost('project_manager');
        $project_admin_id = $this->request->getPost('project_admin');
        $project_estiamator_id = $this->request->getPost('estimator');
        $project_leading_hand_id = $this->request->getPost('leading_hand');

        if ($project_leading_hand_id == 0){
          $project_leading_hand_full_name = $this->request->getPost('lh_name');
          $project_leading_hand_mobile_no = $this->request->getPost('lh_mobile_no');
        }

        if($client_type == 0):
          $company_prg_arr =  explode('|',$this->request->getPost('company_prg'));
          $client_id = $company_prg_arr[1];
          $contact_person_id = $this->request->getPost('contact_person');
        else:
          $pending_comp_id = $this->request->getPost('pending_comp_id');
          $company_prg_arr =  explode('/',$pending_comp_id);
          $client_id = $company_prg_arr[0];
          $company_name = $company_prg_arr[1];
          $contact_person_id = 0;
        endif;
        
        $brand_name = $this->request->getPost('brand_name');

        $est_amt = $this->request->getPost('project_total');
        $project_total = str_replace (',','', $est_amt);

        if($this->invoice->if_project_invoiced_full($project_id) == 1){
          $project_markup = $prev_project_details['markup'];
          $install_hrs = $prev_project_details['install_time_hrs'];
        }else{          
          $install_hrs = $this->request->getPost('install_hrs');
          $project_markup = $this->request->getPost('project_markup');
        }

        $project_area = $this->request->getPost('project_area');
        $comments = $this->request->getPost('comments');
        $project_status_id = 1;

        $shop_tenancy_number = $this->request->getPost('shop_tenancy_number'); 

        $focus_user_id = $this->session->get('user_id');

        $labour_hrs_estimate = $this->request->getPost('labour_hrs_estimate');

        $is_shopping_center = $this->request->getPost('is_shopping_center');
        $is_double_time = $this->request->getPost('is_double_time');






//======= fetch database Mark-up ===
        $proj_q = $this->projects_m->select_particular_project($project_id);
        foreach ($proj_q->getResultArray() as $row){
          $proj_markup = $row['markup'];
        }


        if($is_shopping_center == 1){


        $sc_address_id =  $this->request->getPost('brand_shopping_center');
        $site_address_id =  $this->projects_m->duplicate_address_row($sc_address_id);

        $selected_shopping_center_raw = $this->request->getPost('selected_shopping_center_detail'); 
        $selected_shopping_center_arr = explode(',', $selected_shopping_center_raw);

        $shop_name = $selected_shopping_center_arr[0];
        $this->projects_m->set_shop_name($project_id,$shop_name);


        }else{
          $site_address_id = $data['address_id'];
          $shop_tenancy_number = '';

          $general_address_id_result_a = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_a'],$data['suburb_a']);
          foreach ($general_address_id_result_a->getResult() as $general_address_id_a){
            $general_address_a = $general_address_id_a->general_address_id;
          }
          $site_address_id = $this->company_m->insert_address_detail($data['street'],$general_address_a,$data['unit_level'],$data['unit_number']);

          
        }


        $cc_pm_raw = $this->request->getPost('client_contact_project_manager');
        $cc_pm = ($cc_pm_raw == 0 ? $project_manager_id : $cc_pm_raw);







      $general_address_id_result_b = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_b'],$data['suburb_b']);
      foreach ($general_address_id_result_b->getResult() as $general_address_id_b){
        $general_address_b = $general_address_id_b->general_address_id;
      }
      $invoice_address_id = $this->company_m->insert_address_detail($data['street_b'],$general_address_b,$data['unit_level_b'],$data['number_b']);




        $rev_date  = date("d/m/Y");
        $this->projects_m->update_full_project_details($project_id,$project_name,$client_id,$contact_person_id,$client_po,$job_type,$brand_name,$job_category,$job_date,$site_start,$site_finish,$is_wip,$install_hrs,$is_double_time,$project_total,$labour_hrs_estimate,$project_markup,$project_area,$project_manager_id,$project_admin_id,$project_estiamator_id,$shop_tenancy_number,$site_address_id,$shop_tenancy_number,$site_address_id,$invoice_address_id,$focus_id,$cc_pm,$proj_joinery_user,$rev_date,$client_type);

        if( strpos(implode(",",$data['warranty_categories']), $job_category) !== false ):
          $this->projects->set_warranty_date_after_paid($project_id);
        endif;

        

    $static_defaults_q = $this->user_model->select_static_defaults();
    $getResult = $static_defaults_q->getResult();
    $static_defaults = array_shift($getResult);

    $day_revew_req = $static_defaults->prj_review_day;

    $timestamp_day_revuew_req = (int)strtotime("$day_revew_req this week");
    $monday_revuew_req = (int)strtotime("Monday this week");
    $friday_revuew_req = (int)strtotime("Friday this week");
    $timestamp_lwk_revuew_req = (int)strtotime("$day_revew_req last week");
    $timestamp_nxt_revuew_req = (int)strtotime("$day_revew_req next week");

    $date_day_revuew_req = date('d/m/Y',$timestamp_day_revuew_req);
    $today_rvw_mrkr = (int)strtotime("Today");

    $q_prj_rvw = $this->projects_m->get_prj_rvw($date_day_revuew_req,$project_id);


    if($q_prj_rvw->getNumRows() === 1){ // if found data
      $this->projects_m->update_set_wip_rvw($project_id,$date_day_revuew_req,$rev_date);
    }else{
      $this->projects_m->insert_wip_rvw($project_id, $date_day_revuew_req, $rev_date);
    }

    if($today_rvw_mrkr > $timestamp_day_revuew_req && $today_rvw_mrkr < $timestamp_nxt_revuew_req ){  // baddd
      if( $timestamp_day_revuew_req <  $today_rvw_mrkr && $today_rvw_mrkr <= $friday_revuew_req  ){
        $this->projects_m->prj_rvw_late($project_id,$date_day_revuew_req );
      }
    }



        $this->projects_m->update_project_sched_for_pr_lead($project_id, $project_leading_hand_id);

        if ($project_leading_hand_id == 0){
          $q_proj_sched_id = $this->projects_m->get_project_sched_for_pr($project_id);
          $getResultArray = $q_proj_sched_id->getResultArray();
          $proj_sched_id = array_shift($getResultArray);

          $q_manual_lh_id = $this->projects_m->get_manual_const($proj_sched_id['project_schedule_id']);
          $getResultArray = $q_manual_lh_id->getResultArray();
          $manual_lh_id = array_shift($getResultArray);

          if($q_manual_lh_id->getNumRows() > 0){
            $this->projects_m->update_manual_lead($proj_sched_id['project_schedule_id'], ucwords($project_leading_hand_full_name), $project_leading_hand_mobile_no);
          } else {
            $this->projects_m->insert_manual_lead($proj_sched_id['project_schedule_id'], ucwords($project_leading_hand_full_name), $project_leading_hand_mobile_no);
          }
        }

        $this->company_m->update_notes_comments($data['notes_id'],$comments);

        if($site_finish != ''){
        //  $this->projects_m->update_vr_inv_date($site_finish,$site_finish,$project_id );
        }


        if($install_hrs != '' && $install_hrs > 0){
          $prj_install_hrs = $install_hrs;
        }else{
          $prj_install_hrs = $labour_hrs_estimate;
        }

        $this->update_install_cost_total($project_id,$prj_install_hrs,$is_double_time);
//===== Change markup for all the works of the selected project ===
        if($proj_markup !== $project_markup):
          $work_q = $this->works_m->display_all_works($project_id);
          foreach ($work_q->getResultArray() as $row){
            $works_id = $row['works_id'];
            if($row['work_con_sup_id'] == '53'){
              $joinery_works_id = $works_id;
            }
            $this->works_m->update_works(1,$works_id,"",$project_markup,"","","","","","","","","","","","","","","","","","","","","","");
            //$this->works_m->update_works(1,$works_id,"",$project_markup,"","","","","","","","","","","","","","","","","","","","","");
            $work_estimate = $row['work_estimate'];
            $quote = $work_estimate + ($work_estimate * ($project_markup/100));
            $this->works_m->update_works(8,$works_id,"","","","","","","","","","","","","","","","","","","","","",$work_estimate,$quote,"");
            //$this->works_m->update_works(8,$works_id,"","","","","","","","","","","","","","","","","","","","","",$work_estimate,$quote);
          }

          if($joinery_works_id !== "" ){
            $work_joinery_q = $this->works_m->display_all_works_joinery($joinery_works_id);
            foreach ($work_joinery_q->result_array() as $row){
              $joinery_id = $row['work_joinery_id'];
              $joinery_estimate = $row['j_estimate'];
                $joiner_quoted = $joinery_estimate +  ($joinery_estimate * ($project_markup/100));
                $this->works_m->update_joinery_markup($joinery_id,$project_markup,$joiner_quoted);
            }

            $work_joinery = $this->works_m->display_all_works_joinery($joinery_works_id);
                $t_price = 0;
                $t_estimate = 0;
                $t_quote = 0;
                foreach ($work_joinery->getResultArray() as $row)
            {
               $t_price = $t_price + $row['j_price'];
               $t_estimate = $t_estimate + $row['j_estimate'];
               $t_quote = $t_quote + $row['j_quote'];
            }

            $this->works_m->update_work_joinery($joinery_works_id,$t_price,$t_estimate,$t_quote);
          }
          
    //== Total Works in project
          $works_q = $this->works_m->display_all_works($project_id);
          $t_price = 0;
          $t_estimate = 0;
          $t_quoted = 0;
          foreach ($works_q->getResultArray() as $row){
            $t_price = $t_price + $row['price'];
            $t_estimate = $t_estimate + $row['work_estimate'];
            $t_quoted = $t_quoted + $row['total_work_quote'];
          }

          $this->projects_m->update_project_cost_total($project_id,$t_price,$t_estimate,$t_quoted);
        endif;

        $this->session->setFlashdata('full_update', 'Project Details is now updated!');


        $type = 'Update';

        date_default_timezone_set("Australia/Perth");
        $user_id = $this->session->get('user_id');
        $date = date("d/m/Y");
        $time = date("H:i:s");
        $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);






    if( $install_hrs != $prev_project_details['install_time_hrs']){
      $type = 'Site Hours';
      $actions = 'Changed the site hours from '.$prev_project_details['install_time_hrs'].' to '.$install_hrs.'';
      $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
    }





        if( isset($_GET['status_rvwprj']) && $_GET['status_rvwprj'] != '' ){
          return redirect()->to('projects/projects_wip_review?prj_ret_rev='.$project_id.'-'.$_GET['status_rvwprj'].'_prj_view&pmr='.$project_manager_id);
        }



           return redirect()->to('/projects/view/'.$project_id);


        elseif ( 

          $this->request->getPost('job_category') != 'Maintenance' && 
          $this->request->getPost('job_category') != 'Minor Works' && 
          $this->request->getPost('job_category') != 'Strip Out' && 
          $this->request->getPost('job_category') != 'Design Works' && 
          $this->request->getPost('project_area') < 10 ):

          $this->session->setFlashdata('error', 'Invalid form details, please try again.');
          $data['validation'] = $this->validator;
          $data['error' ] = 'Invalid value for project area.';
          $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';
          return view('App\Views\page',$data);

        else: // form has errors
        // return with errors

          $this->session->setFlashdata('error', 'Invalid form details, please try again.');
          $data['validation'] = $this->validator;
          $data['form_error'] = '<p class="">&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Fields have errors, kindly check and comply.</p>';
          return view('App\Views\page',$data);

        // return with errors
        endif;
      else:

        return view('App\Views\page',$data);
      endif;
      // validation checks here







//    $rules[ 'brand_name' ] = ['brand_name' => 'Brand', 'rules' => ['required','trim'] ];


    }





    


  }


  public function update_feedback(){
    $project_id = $this->request->getPost('project_id');
    $feedback = $this->request->getPost('select_receive_feedback');
    $this->projects_m->update_feedback($feedback,$project_id);
    $type = 'Update';
    return redirect()->to('/projects/view/'.$project_id);
  }

  public function delete_project($project_id){
    $this->user_model = new Users_m();

    $this->session->setFlashdata('project_deleted', 'A project is been deleted. Project No.'.$project_id);
    $this->projects_m->delete_project_details($project_id);

    $type = 'Delete';
    $actions = 'Deleted project details';
    date_default_timezone_set("Australia/Perth");
    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id,$type);
    return redirect()->to('/projects');
  }

  public function update_install_cost_total($inserted_project_id,$prj_install_hrs,$is_double_time){
    $this->admin_m = new Admin_m();

    $project_details_raw = $this->projects_m->fetch_project_details($inserted_project_id);
    $getResultArray = $project_details_raw->getResultArray();
    $project_details = array_shift($getResultArray);

    $system_default_raw = $this->admin_m->latest_system_default($project_details['defaults_id']);
    $getResultArray = $system_default_raw->getResultArray();
    $system_default = array_shift($getResultArray);

    $site_costs_raw = $this->admin_m->fetch_site_costs($system_default['site_cost_id']);
    $getResultArray = $site_costs_raw->getResultArray();
    $site_costs = array_shift($getResultArray);

    if($is_double_time > 0){
      $install_cost_total = $site_costs['total_double_time']*$prj_install_hrs;
    }else{
      $install_cost_total = $site_costs['total_amalgamated_rate']*$prj_install_hrs;
    }
    $this->projects_m->update_install_cost_total($inserted_project_id,$install_cost_total);
  }

  public function show_project_invoice($project_id){
    $this->invoice = new Invoice();
    return $this->invoice->project_invoice($project_id);    
  }

  public function induction_project_exempted($project_id){
    $this->admin_m = new Admin_m();
    $this->company_m = new Company_m();

    $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
    foreach ($admin_defaults->getResult() as $row){
      $induction_work_value = $row->induction_work_value;
      $induction_project_value = $row->induction_project_value;
      $induction_categories = $row->induction_categories;
    }

    $proj_q = $this->projects_m->fetch_complete_project_details($project_id);
    foreach ($proj_q->getResult() as $row) { 
      $job_category = $row->job_category;
      $project_value = $row->project_total;
      $project_estimate = $row->budget_estimate_total;
      $client_id = $row->client_id;
      $address_id = $row->address_id;
    }

    $q_client_company = $this->company_m->display_company_detail_by_id($client_id);
    $getResultArray = $q_client_company->getResultArray();
    $client_company = array_shift($getResultArray);

    $query_client_address = $this->company_m->fetch_complete_detail_address($address_id);
    $getResultArray = $query_client_address->getResultArray();
    $temp_data = array_shift($getResultArray);

    $project_is_exempted = $this->admin_m->project_is_exempted_induction($project_id);

    if($project_is_exempted == 0){
      $induction_categories_arr = explode (",", $induction_categories);
      $arr_num = count($induction_categories_arr);
      $x = 0;
      $exist = 0;
      while($x < $arr_num){
        if($job_category == $induction_categories_arr[$x]){
          $exist = 1;
        }
        $x++;
      }

      if($exist == 1){
        $post_code_is_exempted = $this->admin_m->postcode_excempted($temp_data['postcode']);
        if($post_code_is_exempted == 1){
          $induction_exempted = 1;
        }else{
          if($project_estimate >= $induction_project_value){
            $induction_exempted = 0;
          }else{
            if($project_estimate >= $induction_project_value){
              $induction_exempted = 0;
            }else{
              $induction_exempted = 1;
            }
          }
        }
      }else{
        $induction_exempted = 1;
      }
    }else{
      $induction_exempted = $project_is_exempted;
    }
    
    return $induction_exempted;
  }



  public function display_project_applied_defaults($project_id){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();

    $project_details_raw = $this->projects_m->fetch_project_details($project_id);
    $getResultArray = $project_details_raw->getResultArray();   
    $project_details = array_shift($getResultArray);

    $system_default_raw = $this->admin_m->latest_system_default($project_details['defaults_id']);
    $getResultArray = $system_default_raw->getResultArray();
    $system_default = array_shift($getResultArray);

    $markup_raw = $this->admin_m->fetch_markup($system_default['markup_id']);
    $getResultArray = $markup_raw->getResultArray();
    $markup = array_shift($getResultArray);

    $site_costs_raw = $this->admin_m->fetch_site_costs($system_default['site_cost_id']);
    $getResultArray = $site_costs_raw->getResultArray();
    $site_costs = array_shift($getResultArray);

    $admin_defaults_raw = $this->admin_m->fetch_admin_defaults($system_default['admin_default_id']);
    $getResultArray = $admin_defaults_raw->getResultArray();
    $admin_defaults = array_shift($getResultArray);

    $labour_cost_raw = $this->admin_m->fetch_labour_cost($system_default['labour_cost_id']);
    $getResultArray = $labour_cost_raw->getResultArray();
    $labour_cost = array_shift($getResultArray);

    $gp_data_arr = $this->admin->get_double_amalgated_rate($site_costs,$labour_cost,$admin_defaults);

    $result = array(
      "admin_total_rate_amalgated" => $site_costs['total_amalgamated_rate'],
      "admin_total_rate_double" => $site_costs['total_double_time'],
      "admin_actual_rate_amalgate" => $gp_data_arr['gp_amalgamated_rate'],
      "admin_actual_rate_double" => $gp_data_arr['gp_on_cost_time_double_hr'],
      "admin_install_markup" => $admin_defaults['installation_labour_mark_up'],
      "admin_gst_rate" => $admin_defaults['gst_rate'],
      "admin_hourly_rate" => $site_costs['rate']
    );

    //var_dump($result);
    return $result;
  }


  public function set_warranty(){


    $this->admin_m = new Admin_m();

    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    $get_res_arr_admm_def = $q_admin_defaults->getResultArray();
    $admin_defaults = array_shift($get_res_arr_admm_def);

    $warranty_months = $admin_defaults['warranty_months'];
    $warranty_years = $admin_defaults['warranty_years'];

    $q_warranty_categories = $this->projects_m->fetch_warranty_categories();
    $get_res_arr_wrrnty_cat = $q_warranty_categories->getResultArray();
    $data['warranty_categories'] = array_shift($get_res_arr_wrrnty_cat);

    $q_warranty_categories = $this->projects_m->fetch_warranty_categories();
    $get_res_arr_wrrnty_cats = $q_warranty_categories->getResultArray();
    $data['warranty_categories'] = array_shift($get_res_arr_wrrnty_cats);

    $raw_warranty_cat = implode(",",$data['warranty_categories']);
    $replaced_warranty_cat = str_replace(",", "','", $raw_warranty_cat)."'";
    $format_warranty_cat = substr_replace($replaced_warranty_cat, "", 0, 2);
    
    $q_fetch_projects = $this->projects_m->display_all_projects('AND `project`.`job_category` IN ('.$format_warranty_cat.')');
    $fetch_projects = $q_fetch_projects->getResultArray();

    foreach ($fetch_projects as $row) {

      $date = $row['date_site_finish'];
      $date = str_replace('/', '-', $date);
      $date_finish = date('Y-m-d', strtotime($date));

      $warranty_date_year = date('Y-m-d', strtotime("+".$warranty_years." months", strtotime($date_finish)));
      $warranty_date = date('d/m/Y', strtotime("+".$warranty_months." months", strtotime($warranty_date_year)));

      $this->projects_m->update_warranty_date($warranty_date, $row['project_id']);
    }
  }


  public function fetch_project_total_values(){
    $project_id = $_POST['proj_id'];
    $project_totals_arr = $this->fetch_project_totals($project_id);
    $proj_default_arr = $this->display_project_applied_defaults($project_id);
    $admin_gst_rate = $proj_default_arr['admin_gst_rate'];
    $proj_ex_gst = round($project_totals_arr['final_total_quoted'],2);
    $proj_ex_gst_formatted = number_format($project_totals_arr['final_total_quoted'],2);
    $proj_inc_gst = number_format($proj_ex_gst+($proj_ex_gst*($admin_gst_rate/100)),2);
    //echo $project_id;
    echo $proj_ex_gst_formatted."|".$proj_inc_gst;
  }


  // Project Graphical Dashboard ====================
  public function get_work_list(){
    $data = json_decode(file_get_contents("php://input"), true);
    $project_id = $data['project_id'];
    $proj_q = $this->projects_m->fetch_project_works_sl_dashboard($project_id);
    echo json_encode($proj_q->getResult());
  }

  public function get_project_site_labour_cost(){
    $data = json_decode(file_get_contents("php://input"), true);
        $project_id = $data['project_id'];
    $work_q = $this->projects_m->fetch_project_site_labour_logs($project_id);
    echo json_encode($work_q->getResult());
  }
  
  public function list_job_subcategory(){
    $job_subcategory = $this->projects_m->display_job_subcategory();
    foreach ($job_subcategory->getResultArray() as $row){
      if($row['job_sub_cat'] !== 'Flooring'){
        echo '<option value="2_'.$row['job_sub_cat_id'].'">'.$row['job_sub_cat'].'</option>';
      }
    }
  }

  public function list_job_subcategory_no_other(){
    $job_subcategory = $this->projects_m->display_job_subcategory();
    foreach ($job_subcategory->getResultArray() as $row){
      if($row['job_sub_cat'] !== 'Other'){
        echo '<option value="2_'.$row['job_sub_cat_id'].'">'.$row['job_sub_cat'].'</option>';
      }
    }
  }

  public function job_cat_list_no_other(){
    $jobs_cat_type = $this->projects_m->display_all_job_category_type('');    
    foreach ($jobs_cat_type->getResult() as $row){
      //if($row->job_category !== 'Other'){
        echo '<option value="2_'.$row->job_category_id.'">'.$row->job_category.'</option>';
      //}
    }   
  }

  public function copy_report_to_docstroge(){
    $report_type = $_POST['report_type'];
    
    $proj_id = $_POST['project_id'];

    $user_id = $this->session->get('user_id');

    $time = time();

      $date_upload = date("d/m/Y");

    $proj_q = $this->projects_m->select_particular_project($proj_id);
    foreach ($proj_q->result_array() as $row){
      $client_id = $row['client_id'];
      $compname = $row['company_name'];
      $company_name = str_replace(' ', '', $compname);
    }

    switch($report_type){
      case "pswc":
        $file_type = 27;
        $data_file_name = $proj_id.'_project_summary_w_cost'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/project_summary_w_cost.pdf';
        break;
      case "pswoc":
        $file_type = 27;
        $data_file_name = $proj_id.'_project_summary_wo_cost'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/project_summary_w_cost.pdf';
        break;
      case "jswc":
        $file_type = 33;
        $data_file_name = $proj_id.'_joinery_summary_w_cost'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/joinery_summary_w_cost.pdf';
        break;
      case "jswoc":
        $file_type = 33;
        $data_file_name = $proj_id.'_joinery_summary_wo_cost'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/joinery_summary_wo_cost.pdf';
        break;
      case "var_sum":
        $file_type = 28;
        $data_file_name = $proj_id.'_variation_summary'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/variation_summary.pdf';
        break;
      case "proj_details":
        $file_type = 34;
        $data_file_name = $proj_id.'_project_details'.$time.'.pdf';
        $file_name_set = str_replace(' ', '_', $data_file_name);
        $src_file = './reports/'.$client_id.'_'.$company_name.'/'.$proj_id.'/project_details.pdf';
        break;
    }

    $destination_file = './docs/stored_docs/'.$data_file_name;

    $this->projects_m->insert_uploaded_file($file_name_set,$file_type,$proj_id,0,$date_upload,$user_id,0);
    copy($src_file, $destination_file);
  }

}