<?php
// module created by Jervy 26-9-2022
namespace App\Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use App\Modules\Dashboard\Models\Dashboard_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Projects\Controllers\Projects;
use App\Modules\Projects\Models\Projects_m;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Invoice\Controllers\Invoice;

use App\Modules\Purchase_order\Controllers\Purchase_order;
use App\Modules\Purchase_order\Models\Purchase_order_m;


class Dashboard extends BaseController {

  private $dashboard_m;

  function __construct(){
    $this->dashboard_m = new Dashboard_m();

    if(isset($_GET['delete_rfc'])){
      $revenue_forecast_id = $_GET['delete_rfc'];
      $this->dashboard_m->deactivate_stored_revenue_forecast($revenue_forecast_id);
      $this->session->setFlashdata('record_update','Record is now deleted.');
      return redirect()->to('/dashboard/sales_forecast/');
    }

    if(isset($_GET['primary_rfc'])){
      $forecast =  explode('_',$_GET['primary_rfc']);
      list($id,$year) = $forecast;
      $this->dashboard_m->set_primary_revenue_forecast($id,$year);
      $this->session->setFlashdata('record_update','Record is now set to primary forecast.');
      return redirect()->to('/dashboard/sales_forecast/view_'.$id);
    }

    $this->request = \Config\Services::request();
    $this->session = \Config\Services::session();

  }


  public function index() {
    $this->users = new Users();
    $this->user_model = new Users_m();

    $this->admin_m = new Admin_m();
    $data = array();

    if(!$this->users->_is_logged_in() ):
      return redirect()->to('/signin');
    endif;

    $this->users->_check_user_access('dashboard',1);
    $data['assign_id'] = 0;
    $data['screen'] = 'Dashboard';
    $user_role_id = $this->session->get('user_role_id');
    $user_department_id = $this->session->get('user_department_id');
    //Grant acess to Operations Manager
    $data['pm_setter'] = '';
    $data['pm_setter_focus_id'] = '';

    if($this->session->get('is_admin') == 1 || $user_role_id == 16 || $this->session->get('user_id') == 9 || $this->session->get('user_id') == 6 || $this->session->get('user_id') == 85):


    $dash_type = $this->request->getGet('dash_view');
    if( isset($dash_type) && $dash_type != ''){

      $dash_details = explode('-', $dash_type);
      $data['assign_id'] = $dash_details[0];

      $fetch_user = $this->user_model->fetch_user($dash_details[0]);
      $getResultArray = $fetch_user->getResultArray();
      $user_details = array_shift($getResultArray);
      $data['pm_setter'] = $user_details['user_first_name'].' '.$user_details['user_last_name']; 
      $data['pm_setter_focus_id'] = $user_details['user_focus_company_id']; 

      if($dash_details[1] == 'pm'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pm';
      }elseif($dash_details[1] == 'mn'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_mn';
      }elseif($dash_details[1] == 'es'){
        return redirect()->to('/dashboard/estimators?dash_view='.$dash_details[0].'-es');
      }elseif($dash_details[1] == 'ad'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_home';
      }elseif($dash_details[1] == 'ch'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_general_hammond';
      }elseif($dash_details[1] == 'jp'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_joinery_procurement';
      }elseif($dash_details[1] == 'acnt'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
      }elseif($dash_details[1] == 'const'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
      }elseif($dash_details[1] == 'set'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_set';
      }elseif($dash_details[1] == 'logis'){
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_logis';
      }else{
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_home';
      }

    }else{
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_home';
    }

    elseif($user_role_id == 3 || $user_role_id == 20):

      $dash_type = $this->request->getGet('dash_view');
    if( isset($dash_type) && $dash_type != ''){
      if($this->session->get('user_id') == 15 ){
        $dash_details = explode('-', $dash_type);
        $data['assign_id'] = $dash_details[0];


        $fetch_user = $this->user_model->fetch_user($dash_details[0]);
        $getResultArray = $fetch_user->getResultArray();
        $user_details = array_shift($getResultArray);
        $data['pm_setter'] = $user_details['user_first_name'].' '.$user_details['user_last_name']; 
        $data['pm_setter_focus_id'] = $user_details['user_focus_company_id']; 

        if($dash_details[1] == 'pm'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pm';
        }elseif($dash_details[1] == 'mn'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_mn';
        }elseif($dash_details[1] == 'ad'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_home';
        }elseif($dash_details[1] == 'ch'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_general_hammond';
        }elseif($dash_details[1] == 'jp'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_joinery_procurement';
        }elseif($dash_details[1] == 'acnt'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
        }elseif($dash_details[1] == 'const'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
        }elseif($dash_details[1] == 'set'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_set';
        }elseif($dash_details[1] == 'logis'){
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_logis';
        }elseif($dash_details[1] == 'es'){
          return redirect()->to('/dashboard/estimators?dash_view='.$dash_details[0].'-es');
        }else{
          $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pm';
        }
      }else{
        $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pm';
      }

    }else{
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pm';
    }




    elseif($this->session->get('user_id') == 32):
      $fetch_user= $this->user_model->list_user_short();
      $data['users'] = $fetch_user->getResult();
      $data['screen'] = 'User Availability';
      $data['main_content'] = 'App\Modules\Users\availability';
    elseif($user_role_id == 8):
      return redirect()->to('/dashboard/estimators');
    elseif($user_role_id == 2):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_pa';
    elseif($user_role_id == 7):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_mn';
    elseif($user_role_id == 9):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_joinery_procurement';
    elseif($user_department_id == 3):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
    elseif($user_role_id == 12):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_set';
    elseif($user_role_id == 11):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_accnt';
    elseif($user_role_id == 10):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_logis';
    elseif( $this->session->get('user_id') == 72 ):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_general_hammond';
    elseif( $this->session->get('dashboard') == 1 ):
      $data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_general';
    else:
      return redirect()->to('/projects');
    endif;

    $this_year = date("Y");

    $c_month = date("m");
    $c_year = date("Y");
    $my_year = $c_year;

    for ($month=1; $month < 13; $month++) { 
      if($month <= $c_month){
        $this->_check_sales($my_year,$month); // automatically updates sales of the current month
      }
    }



    $project_manager = $this->dashboard_m->fetch_pms_year($this_year); // ****--___--***
    
    $data['project_manager'] = $project_manager->getResult();

    $all_focus_company = $this->admin_m->fetch_all_company_focus(" AND `company_details`.`company_id` != '4' ");
    $data['focus_company'] = $all_focus_company->getResult();

    $post_months = array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
    $months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"); 


    $data['calendar_view'] = 2;

    if(isset($_GET['calendar_view'])){
      $calendar_view = $_GET['calendar_view'];
      $data['calendar_view'] = $calendar_view;
    }

    $old_year = date("Y")-1;
    $this_year = date("Y");


    $fetch_forecast = $this->dashboard_m->fetch_forecast($this_year,1);
    $getResultArray = $fetch_forecast->getResultArray();
    $data['fetch_forecast'] = array_shift($getResultArray);

    $sales_focus_company_q = $this->dashboard_m->get_sales_focus_company($old_year);
    $getResultArray = $sales_focus_company_q->getResultArray();
    $sales_focus_company = array_shift($getResultArray);
    $sales_focus_company['company_name'] = 'Last Year Sales';
    $data['fcsO'] = $sales_focus_company;

    $sales_focus_company_q = $this->dashboard_m->get_sales_focus_company($this_year);
    $getResultArray = $sales_focus_company_q->getResultArray();
    $sales_focus_company = array_shift($getResultArray);

    $outstanding_focus_company_q = $this->dashboard_m->get_focus_outstanding($this_year);
    $getResultArray = $outstanding_focus_company_q->getResultArray();
    $outstanding_focus_company = array_shift($getResultArray);

    $swout = array('');
    $focus_overall_indv = array();
    $focus_wip_overall = array();
    $focus_comp_wip = array();
    $focus_pm_wip = array();

    $wip_date_a = '';
    $wip_date_b = '';

    for ($i=1; $i<13; $i++){
      $month = $i;

      if($i == 12){
        $wip_date_a = "01/$month/$this_year";
        $wip_date_b = "01/01/".($this_year+1);

      }else{
        $wip_date_a = "01/$month/$this_year"; 
        $wip_date_b = "01/".($month+1)."/$this_year";
      }

      $key = $i - 1;
      $focus_wip_overall[$key] = $this->get_wip_value_permonth($wip_date_a,$wip_date_b);
    }

    $all_focus_company = $this->admin_m->fetch_all_company_focus(" AND `company_details`.`company_id` != '4' ");
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $forecast_per_comp[$company->company_id] = 0;

      for ($i=1; $i<13; $i++){

        $month = $i;

        if($i == 12){
          $wip_date_a = "01/$month/$this_year";
          $wip_date_b = "01/01/".($this_year+1);

        }else{
          $wip_date_a = "01/$month/$this_year"; 
          $wip_date_b = "01/".($month+1)."/$this_year";
        }

        $key = $i - 1;
        $focus_comp_wip[$company->company_name][$key] = $this->get_wip_value_permonth($wip_date_a,$wip_date_b,$company->company_id);
      }
    }

    $data['focus_comp_wip'] = $focus_comp_wip;

    $project_manager = $this->dashboard_m->fetch_pms_year($this_year); // ****--___--***
    $project_manager_list = $project_manager->getResult();


    foreach ($project_manager_list as $pm ) {
      $pm_name = $pm->user_first_name.' '.$pm->user_last_name;
      for ($i=1; $i<13; $i++){

        $month = $i;

        if($i == 12){
          $wip_date_a = "01/$month/$this_year";
          $wip_date_b = "01/01/".($this_year+1);

        }else{
          $wip_date_a = "01/$month/$this_year"; 
          $wip_date_b = "01/".($month+1)."/$this_year";
        }

        $key = $i - 1;
        $focus_pm_wip[$pm_name][$key] = $this->get_wip_value_permonth($wip_date_a,$wip_date_b,$pm->user_id,1);
      }

    }

    $data['focus_pm_wip'] = $focus_pm_wip;
    $data['focus_wip_overall'] = $focus_wip_overall;

    foreach ($post_months as $key => $value) {
      //echo "$key => $value<br />";

      $samount_val = $sales_focus_company['rev_'.$value];
      $samount_val = ($samount_val <= 0 ? 0 : $samount_val);
      $swout['sales_data_'.$value] = $samount_val;
    } //overall sales computation

    $sales_focus_company['company_name'] = "Focus Sales";
    $outstanding_focus_company['company_name'] = "Outstanding";

    $swout['company_name'] = "Overall Sales";
    $data['fcsC'] = $sales_focus_company;
    $data['fcsOT'] = $outstanding_focus_company;
    $data['swout'] = $swout;


    $data['focus_indv_comp_sales_old'] = $this->dashboard_m->get_sales_focus_company($old_year,1);
    $data['focus_indv_comp_sales'] = $this->dashboard_m->get_sales_focus_company($this_year,1);

    $data['focus_indv_comp_forecast'] = $this->dashboard_m->fetch_indv_comp_forecast($this_year);

    $rev_month = 'rev_'.strtolower(date('M'));
    $out_month = 'out_'.strtolower(date('M'));


    $this_year = date("Y");

    $data['pms_sales_c_year'] = $this->dashboard_m->fetch_pm_sales_year($this_year);

    $data['pms_sales_last_year'] = $this->dashboard_m->fetch_pm_sales_year($this_year-1);

    $data['pms_outstanding_c_year'] = $this->dashboard_m->fetch_pm_oustanding_year($this_year); // !!!!!!!!!!!!!!!!!!!!!!!!!!!! needs removing

    $data['focus_indv_pm_month_sales'] = $this->dashboard_m->fetch_pms_month_sales($rev_month,$this_year);
    $data['focus_indv_focu_month_sales'] = $this->dashboard_m->fetch_comp_month_sales($rev_month,$this_year);
    $data['focus_indv_focu_month_outs'] = $this->dashboard_m->fetch_comp_month_outs($out_month,$this_year);

    $q_focus_pm_forecast = $this->dashboard_m->get_pm_forecast($this_year);
    $data['focus_pm_forecast'] = $q_focus_pm_forecast->getResult();

    $q_current_forecast_q = $this->dashboard_m->get_current_forecast($this_year,'29','3197',29);
    $getResultArray = $q_current_forecast_q->getResultArray();
    $pm_forecast = array_shift($getResultArray);
    $total_forecast_maintenance = 0;


    $set_val_total = $pm_forecast['total'] ?? 0;
    $set_val_mns_fct_b = $pm_forecast['mns_fct_b'] ?? 0;

    $set_val_fct = $pm_forecast['wa_fct'] ?? 0;
    $set_val_forecast_percent = $pm_forecast['forecast_percent'] ?? 0;

    $set_val_nws_fct = $pm_forecast['nws_fct'] ?? 0;
    $set_val_nws_fct_b = $pm_forecast['nws_fct_b'] ?? 0;



    $total_forecast_maintenance = ( $set_val_total * (  $set_val_mns_fct_b  / 100  ) ) +   
    (( $set_val_total * (  $set_val_fct  / 100  ) ) * $set_val_forecast_percent/100)  +   
    (( $set_val_total * (  $set_val_nws_fct  / 100  ) ) * $set_val_nws_fct_b/100) ;

    $total_forecast_maintenance = $total_forecast_maintenance + ( $set_val_total * (  $set_val_nws_fct  /100  ) *  ($set_val_nws_fct_b/100) );
    $data['amount_for_maintenance'] = $total_forecast_maintenance;

    $focus_data_forecast_p = $this->dashboard_m->get_focus_comp_forecast($this_year);
    $q_focus_data_forecast_p = $focus_data_forecast_p->getResult();
    $forecast_per_comp = array();
    $forecast_per_comp[0] = 0;
    $focus_company = array();

    $q_curr_maintenance_sales = $this->dashboard_m->fetch_pm_sales_year($this_year,29);
    $getResultArray = $q_curr_maintenance_sales->getResultArray();
    $data['maintenance_current_sales'] = array_shift($getResultArray);

    foreach ($q_focus_data_forecast_p as $ffcp){
      if( intval($ffcp->pm_id) == 0 &&  intval($ffcp->comp_id) != 0){
        $forecast_per_comp[$ffcp->comp_id] = $ffcp->forecast_percent;
      }
    }

    foreach ($q_focus_data_forecast_p as $ffcp){
      if( intval($ffcp->pm_id) == 0 || intval($ffcp->pm_id) == 29){

      }else{ 
        $forecast_percent_val = $ffcp->total * ($forecast_per_comp[$ffcp->comp_id]/100) * ( $ffcp->forecast_percent / 100 );
        $forecast_per_comp[$ffcp->pm_id] = $forecast_percent_val;
      }
    }

    $data['focus_data_forecast_p'] = $forecast_per_comp;


    $pcc_selected_cats = $this->admin_m->fetch_admin_defaults();
    $getResultArray = $pcc_selected_cats->getResultArray();
    $pcc_cats = array_shift($getResultArray);
    $data['pcc_cats'] = $pcc_cats['cat_project_completion_calendar'];
    $data['page_title'] = 'Dashboard';

    return view('App\Views\page',$data);

  }

  public function pa_assignment(){
    $user_id = $this->session->get('user_id');
    $pa_data = $this->dashboard_m->fetch_pa_assignment($user_id);
    $getResultArray = $pa_data->getResultArray();
    return array_shift($getResultArray);
  }

  public function focus_get_map_locations_pa(){

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $current_date = date("d/m/Y");
    $year = date("Y");
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    $q_maps = $this->dashboard_m->get_map_projects($current_start_year,$current_date,$prime_pm);
    $map_details = $q_maps->getResult();
    $count = 0;

    echo "[";
    foreach ($map_details as $map) {

      if($map->y_coordinates != '' && $map->x_coordinates != ''){

        echo '{"longitude":'.$map->y_coordinates.', "latitude": '.$map->x_coordinates.'},';
        $count++;
      }
    }
    echo '],"count": '.$count.'';
  }

  public function focus_top_ten_clients_pm_donut($pm_data_id = '',$year_set=''){

    $user_id = ($pm_data_id == '' ? $this->session->get('user_id') : $pm_data_id);
    $fetch_user = $this->user_model->fetch_user($user_id);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);
    $comp_q = '';
    $comp_q .= 'AND (';
    $limit = 0;

    if($pm_data_id != ''){
      if($user_details['user_role_id'] == 3 && $user_details['user_department_id'] == 1):
        $pm_type = 1;
      endif; //for directors 

      if($user_details['user_role_id'] == 3 && $user_details['user_department_id'] == 4): //for PM 
      $pm_type = 2;
      endif; //for PM 

      if($user_details['user_role_id'] == 20 && $user_details['user_department_id'] == 4): //for PM 
      $pm_type = 2;
      endif; //for PM or AM
    }else{
      $pm_type = $this->pm_type();
    }

    if($pm_type == 1){ // for director/pm
      $direct_company = explode(',',$user_details['direct_company'] );
      $size = count($direct_company );

      foreach ($direct_company as $key => $value) {
        $comp_q .= '`project`.`focus_company_id` = '.$value.'';
        $limit++;
        if($size != $limit){
          $comp_q .= ' OR ';
        }
      }
    }

    if($pm_type == 2){ // for pm only
      $comp_q .= ' `project`.`project_manager_id` = '.$user_id.'';
    }

    $comp_q .= ')';

    if($year_set != ''){
      $current_date = date("d/m/").''.$year_set;
      $year = $year_set;
      $last_year = $year_set -1;
    }else{
      $current_date = date("d/m/Y");
      $year = date("Y");
      $last_year = intval(date("Y")) - 1;
    }

    $current_start_year = '01/01/'.$year; 

    $q_clients = $this->dashboard_m->get_top_ten_clients($current_start_year, $current_date,'','',$comp_q);
    $client_details  = $q_clients->getResult();
    $list_total = 0;

    foreach ($client_details as $company) {
      $q_vr_c_t = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c_t->getResultArray();
      $vr_val_t = array_shift($getResultArray);
      $list_total = $list_total + $company->grand_total + $vr_val_t['total_variation'];
    }

    $this_month = date("m");
    $this_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$this_day/$this_month/$last_year";

    $comp_total = array();
    $comp_name = array();

    foreach ($client_details as $company) {
      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);
      $cost_gtotl_amnt = round($company->grand_total+ $vr_val['total_variation']);
      $comp_total[$company->company_id] = $cost_gtotl_amnt;
        //  $comp_name[$company->company_id] = $company->company_name;
      $comp_name[$company->company_id] = $company->company_name_group;

    }

    arsort($comp_total);

    foreach ($comp_total as $raw_id => $compute_amount) {
      $percent = round(100/($list_total/$compute_amount),1);
      $company_name_group = $comp_name[$raw_id];
      echo "['". str_replace("'","&apos;",$company_name_group)."', ".$compute_amount."],";
    }
  }

  public function focus_top_ten_con_sup_pm_donut($type,$pm_data_id = ''){
    $this->user_model = new Users_m();

    if(isset($pm_data_id) && $pm_data_id != ''){
      $user_id = $pm_data_id;
    }else{
      $user_id = $this->session->get('user_id');
    }

    $direct_company = '';
    $fetch_user = $this->user_model->fetch_user($user_id);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);


    if($user_id != ''){
      if($user_details['user_role_id'] == 3 && $user_details['user_department_id'] == 1):
        $pm_type = 1;
        endif; //for directors 

        if($user_details['user_role_id'] == 3 && $user_details['user_department_id'] == 4): //for PM 
        $pm_type = 2;
        endif; //for PM 

        if($user_details['user_role_id'] == 20 && $user_details['user_department_id'] == 4): //for PM 
        $pm_type = 2;
        endif; //for PM or AM
      }else{

        $pm_type = $this->pm_type($user_id);
      }




    if($pm_type == 1){ // for director/pm
      $direct_company = explode(',',$user_details['direct_company'] );
    }

    if($pm_type == 2){ // for pm only
      $direct_company = explode(',',$user_details['user_focus_company_id'] );
    }


    $size = count($direct_company);
    $limit = 0;

    $comp_q = '';

    $comp_q .= 'AND (';
    foreach ($direct_company as $key => $value) {
      $comp_q .= '`project`.`focus_company_id` = '.$value.'';
      $limit++;

      if($size != $limit){
        $comp_q .= ' OR ';
      }

    }
    $comp_q .= ')';

    $current_date = date("d/m/Y");
    $year = date("Y");

    $last_year = intval(date("Y")) - 1;

    $base_year = '01/01/'.$year;

    $next_year_date = '01/01/'.$last_year;
    $current_start_year = date("d/m/Y");
    $last_start_year = '01/01/'.$last_year;

    $q_companies = $this->dashboard_m->get_company_sales($type,$base_year,$current_start_year,'',$comp_q);
    $company_details  = $q_companies->getResult();
    $counter = 0;


    $list_total = 0;

    foreach ($company_details as $company) {
      $list_total = $list_total + $company->total_price;
    }

    foreach ($company_details as $company) {
      $counter ++;
      $total = $company->total_price;
      $percent = round(100/($list_total/$company->total_price));

      $q_clients_overall = $this->dashboard_m->get_company_sales_overall($company->company_id);
      $getResultArray = $q_clients_overall->getResultArray();
      $overall_cost = array_shift($getResultArray);
      $grand_total = $overall_cost['total_price'];

          //echo '<div class="col-sm-8 col-md-8"><i class="fa fa-chevron-circle-right"></i>  &nbsp; ';

      $comp_name = $company->company_name; 



      echo "['". str_replace("'","&apos;",$comp_name)."', ".$company->total_price."],";



      $cmp_id = $company->company_id;

      $last_year_q = $this->dashboard_m->get_company_sales('',$base_year,$current_start_year,$cmp_id,$comp_q);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = $last_year_sale['total_price'];

      //echo ' </div><div class="col-md-1 col-sm-4"><strong>'.$percent.'%</strong></div>  <div class="col-md-3 col-sm-4 tooltip-test" title="" data-placement="left" data-original-title="Last Year : $ '.number_format($lst_year_total).'"><i class="fa fa-usd"></i> '.number_format($company->total_price).'</div><div class="col-sm-12"><hr class="block m-bottom-5 m-top-5"></div>';
    }
  }

  public function pm_type($user_id=''){
    $this->user_model = new Users_m();
    $pm_type = 0;

    if(isset($user_id) && $user_id != ''){

      $raw_user_dat = $this->user_model->fetch_user_role_dept($user_id);
      $getResultArray = $raw_user_dat->getResultArray();
      $user_data =  array_shift($getResultArray);

      if($user_data['user_role_id'] == 3 && $user_data['user_department_id'] == 1):
        $pm_type = 1;
      endif; //for directors 

      if($user_data['user_role_id'] == 3 && $user_data['user_department_id'] == 4): //for PM 
        $pm_type = 2;
      endif; //for PM 

      if($user_data['user_role_id'] == 20 && $user_data['user_department_id'] == 4): //for PM 
        $pm_type = 2;
      endif; //for PM 

    }else{
      if($this->session->get('user_role_id') == 3 && $this->session->get('user_department_id') == 1):
        $pm_type = 1;
      endif; //for directors 

      if($this->session->get('user_role_id') == 3 && $this->session->get('user_department_id') == 4): //for PM 
        $pm_type = 2;
      endif; //for PM 

      if($this->session->get('user_role_id') == 20 && $this->session->get('user_department_id') == 4): //for PM 
        $pm_type = 2;
      endif; //for PM 
    }

    return $pm_type;
  }


  public function focus_projects_by_type_widget_pm($assign_id='',$is_pie=''){
    $this->user_model = new Users_m();

    if(isset($assign_id) && $assign_id != ''){
      $user_id = $assign_id;
    }else{
      $user_id = $this->session->get('user_id');
    }


    $pm_type = $this->pm_type($user_id); 

    $fetch_user = $this->user_model->fetch_user($user_id);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);

    if($pm_type == 1){ // for director/pm
      $direct_company = explode(',',$user_details['direct_company'] );
    }

    if($pm_type == 2){ // for pm only
      $direct_company = explode(',',$user_details['user_focus_company_id'] );
    }

    $current_date = date("d/m/Y");
    $year = date("Y");
    $current_start_year = '01/01/'.$year;


    $comp_id = 0;

    $focus_arr = array();
    $focus_prjs = array();
    $focus_costs = array();


    $focus_catgy = array();
    $focus_catgy_name = array();
    $focus_catgy_costs = array();

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $focus_prjs[$company->company_id] = 0;
      $focus_costs[$company->company_id] = 0;
    }

    $q_work = $this->dashboard_m->get_work_types();
    foreach ($q_work->getResultArray() as $job_category) {
      $cat_id =  strtolower(str_replace(" ","_",$job_category['job_category']));
      $focus_catgy[$cat_id] = 0;
      $focus_catgy_costs[$cat_id] = 0;
      $focus_catgy_name[$cat_id] = $job_category['job_category'];
    }

    $cost = 0;
    $variation = 0; 
    $grand_prj_total = 0;

    $q_projects = $this->dashboard_m->get_projects_by_work_type($current_start_year, $current_date);
    foreach ($q_projects->getResultArray() as $project){

      if( in_array($project['focus_company_id'], $direct_company) ){
        $cost = $cost + $project['project_total'];
        $variation = $variation + $project['variation_total'];
        $comp_id = $project['focus_company_id'];

        $focus_prjs[$comp_id]++;
        $cat_id =  strtolower(str_replace(" ","_",$project['job_category']));
        $focus_catgy[$cat_id]++;

        if($pm_type == 1){// for director/pm
          $focus_catgy_costs[$cat_id] = $focus_catgy_costs[$cat_id] + $project['project_total'] + $project['variation_total'];
          $focus_costs[$comp_id] = $focus_costs[$comp_id] + $project['project_total'] + $project['variation_total'];
          $grand_prj_total = $grand_prj_total +  $project['project_total'];
        }

        if($pm_type == 2 && $project['project_manager_id'] == $user_id ){// for pm only
          $focus_catgy_costs[$cat_id] = $focus_catgy_costs[$cat_id] + $project['project_total'] + $project['variation_total'];
          $focus_costs[$comp_id] = $focus_costs[$comp_id] + $project['project_total'] + $project['variation_total'];
          $grand_prj_total = $grand_prj_total +  $project['project_total'];
        }


      }
    }

    $total_count_cat = array_sum($focus_catgy);

    foreach ($focus_catgy_name as $cat_id => $value){
      $cost = $focus_catgy_costs[$cat_id];
      $count = $focus_catgy[$cat_id];





      $grand_prj_total = ($grand_prj_total <= 1 ? 1 : $grand_prj_total);
      $cost = ($cost <= 1 ? 1 : $cost);


      if($cost>0){
        $percent = round(100/($grand_prj_total/$cost));
      }else{
        $percent = round(100/($grand_prj_total/1));
      }

      $grand_prj_total = ($grand_prj_total == 1 ? 0 : $grand_prj_total);
      $cost = ($cost == 1 ? 0 : $cost);

      if($grand_prj_total == 0 && $cost == 0){
        $percent = 0;
      }
      

      if($is_pie != ''){
        echo "['".str_replace("'","&apos;", $value)."',".$cost."],";
      }else{



        echo '<div id="" class="clearfix"><p><span class="col-sm-7"><i class="fa fa-chevron-circle-right"></i> &nbsp; '.$value.'</span><strong class="col-sm-5">$ '.number_format($cost).'</strong></p></div>';
        echo '<div class="col-md-12"><hr class="block m-bottom-5 m-top-5"></div>'; 
      }


    }
  }


  public function get_count_per_week($return_total = 0, $set_year = '', $set_emp_id = '', $custom_data = '' ){
    $this->user_model = new Users_m();

    $q_leave_types = $this->user_model->fetch_leave_type();
    $leave_types = $q_leave_types->getResult();
    $this_year = date("Y");

    if(isset($set_year) && $set_year != ''){
      $this_year = $set_year;
    }


    // if return total == 4, only return string

    $custom_q = '';
    $custom_r = '';

    $added_data = (object)[];
    $added_data->leave_type_id = '0';
    $added_data->leave_type = 'Public Holiday';
    $added_data->remarks = '';


    array_push($leave_types, $added_data);

    if( isset($set_emp_id) && $set_emp_id!='' ){
      $custom_q .= " AND `leave_request`.`user_id` = '$set_emp_id' ";
    }

    $leaves_arr = array();
    $this_year_date = '01/01/'.$this_year;
    $next_year_date = '01/01/'.($this_year+1);
    $enter_days = 0;

    $custom_q .= $custom_data;

    $get_weekly_leaves_q = $this->dashboard_m->get_weekly_leaves($this_year_date,$next_year_date,$custom_q);

    foreach ($get_weekly_leaves_q->getResult() as $leave_numbers){
      $actual_days_away = 0;

      $start_week_no = date("W", $leave_numbers->start_day_of_leave);
      $ending_week_no = date("W", $leave_numbers->end_day_of_leave);

      if($leave_numbers->partial_day == 1){
        if($leave_numbers->no_hrs_of_work == 0){
          $actual_days_away = round(  $leave_numbers->total_days_away / 1 ,2);
        }else{
          if($leave_numbers->no_hrs_of_work == ''){
            $no_hrs_wrk = 1;
          }else{
            $no_hrs_wrk = $leave_numbers->no_hrs_of_work;
          }

          $actual_days_away = round(  $leave_numbers->total_days_away / $no_hrs_wrk ,2);
        }
      }else{
        if($leave_numbers->no_hrs_of_work == 0){
          $actual_days_away = round( $leave_numbers->total_days_away,2) / 1;
        }else{


          if($leave_numbers->no_hrs_of_work == ''){
            $no_hrs_wrk = 1;
          }else{
            $no_hrs_wrk = $leave_numbers->no_hrs_of_work;
          }
 
          $actual_days_away = round( $leave_numbers->total_days_away,2) / $no_hrs_wrk;
        }
      }

      $is_diff = ($ending_week_no - $start_week_no) + 1;
      $starter = intval($start_week_no);


      if($actual_days_away > 1){
        if($start_week_no == $ending_week_no){ 
          if ( isset($leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] ) && $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] != ''  ){
            $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter]+$actual_days_away;
          }else{
            $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $actual_days_away;
          }

        }else{

          if( $start_week_no!= 52 ){
            $counter = 0; 
            $starter = intval($start_week_no);
            $date_set_val = 0;

            while($is_diff > 0){          

              $stamp_week_friday = strtotime($this_year.'W'.sprintf("%02d", $starter).' + 4Days');
              $stamp_week_monday = strtotime($this_year.'W'.sprintf("%02d", $starter));


              if($stamp_week_monday <= $leave_numbers->start_day_of_leave  && $leave_numbers->start_day_of_leave <= $stamp_week_friday ){

                $stamp_difference = abs($leave_numbers->start_day_of_leave - $stamp_week_friday);
                $diff_val = floor($stamp_difference / (60*60*24) );
                $actual_days_away = $diff_val+1;

                if ( isset($leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] ) && $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] != ''  ){
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] + $actual_days_away;
                }else{
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $actual_days_away;
                }

              }elseif($stamp_week_monday <= $leave_numbers->end_day_of_leave  && $leave_numbers->end_day_of_leave <= $stamp_week_friday){


                $stamp_difference = abs($leave_numbers->end_day_of_leave - $stamp_week_monday);
                $diff_val = floor($stamp_difference / (60*60*24) );

                $actual_days_away = $diff_val+1;

                if ( isset($leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] ) && $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] != ''  ){
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] + $actual_days_away;
                }else{
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $actual_days_away;
                }

              }else{

                $actual_days_away = 5;
                if ( isset($leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] ) && $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] != ''  ){
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] + $actual_days_away;
                }else{
                  $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $actual_days_away;
                }

              }

              $is_diff--;
              $starter++;

            }

          }
        }

      }else{

        if ( isset($leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] ) && $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] != ''  ){
          $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter]+$actual_days_away;

        }else{
          $leaves_arr[$leave_numbers->user_id][$leave_numbers->emp_leave_type_id][$starter] = $actual_days_away;          
        }
      }
    }


    if(isset($set_emp_id) && $set_emp_id != '' ){
      $custom_r .= " AND `users`.`user_id` = '$set_emp_id' ";
    }

    $custom_r .= $custom_data;

    $list_user_short_q = $this->user_model->list_user_short($custom_r);
    $user_list = $list_user_short_q->getResult();

    $overall_leave_data = array();

    foreach ($user_list as $users_data){
      $user_name = $users_data->user_first_name.' '.$users_data->user_last_name;

      foreach ($leave_types as $leave_data) {

        if($return_total == 1 || $return_total == 3){
          echo "['$user_name $leave_data->leave_type',";
        }

        for($x=3;$x<=52;$x++){

          if ( isset( $leaves_arr[$users_data->primary_user_id][$leave_data->leave_type_id][$x]) &&  $leaves_arr[$users_data->primary_user_id][$leave_data->leave_type_id][$x] != ''  ){
            if($return_total == 1 || $return_total == 3){
              echo $leaves_arr[$users_data->primary_user_id][$leave_data->leave_type_id][$x].',';
            }

            if ( isset( $overall_leave_data[$leave_data->leave_type_id][$x]) &&  $overall_leave_data[$leave_data->leave_type_id][$x] != ''  ){
              $overall_leave_data[$leave_data->leave_type_id][$x] = $overall_leave_data[$leave_data->leave_type_id][$x] + $leaves_arr[$users_data->primary_user_id][$leave_data->leave_type_id][$x];
            }else{
              $overall_leave_data[$leave_data->leave_type_id][$x] = $leaves_arr[$users_data->primary_user_id][$leave_data->leave_type_id][$x];
            }

          }else{
            if($return_total == 1 || $return_total == 3){
              echo "0,";
            }
            if ( isset( $overall_leave_data[$leave_data->leave_type_id][$x]) &&  $overall_leave_data[$leave_data->leave_type_id][$x] != ''  ){

              $overall_leave_data[$leave_data->leave_type_id][$x] = $overall_leave_data[$leave_data->leave_type_id][$x] + 0;
            }else{
              $overall_leave_data[$leave_data->leave_type_id][$x] = 0;
            }
          }
        }

        if($return_total == 1 || $return_total == 3){
          echo "],
          ";
        }
      }
    }

    $return_arr = array('');
    $iteam_leave_total = 0;
    $last_value = '';

    foreach ($leave_types as $leave_data ) {

      if($return_total == 1){
        echo "['Overall $leave_data->leave_type',"; 
        echo implode(',', $overall_leave_data[$leave_data->leave_type_id]); 
        echo "],";
      }

      if($return_total == 2){
        $return_arr[$leave_data->leave_type_id] = array_sum($overall_leave_data[$leave_data->leave_type_id]);
      }
    }

    if($return_total == 2){
      foreach ($leave_types as $leave_data) {
        $last_value .= $leave_data->leave_type_id.'-'.$return_arr[$leave_data->leave_type_id].'|';
      }
    }

    if( $return_total == 2  ){
      echo substr($last_value, 0, -1);
      return $return_arr;
    }
  }


  public function focus_top_ten_clients_mn_donut(){
    $focus_company_maintenence_q = $this->dashboard_m->get_focus_companies_mntnc();
    $focus_company_maintenence = $focus_company_maintenence_q->getResult();

    $maintenacne_data = array(); 
    foreach ($focus_company_maintenence as $maintenance_data){
      $maintenacne_data[$maintenance_data->focus_company_id] = 0;
      $pm_id = $maintenance_data->project_manager_id;
    }


    $limit = 0;
    $comp_q = " AND `project`.`job_category` = 'Maintenance' ";


    $current_date = date("d/m/Y");
    $year = date("Y");
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    $q_clients = $this->dashboard_m->get_top_ten_clients($current_start_year, $current_date,'','',$comp_q);
    $client_details  = $q_clients->getResult();
    
    $list_total = 0;

    //if( in_array($project['focus_company_id'], $direct_company) ){

    foreach ($client_details as $company) {
      $q_vr_c_t = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c_t->getResultArray();
      $vr_val_t = array_shift($getResultArray);
      $list_total = $list_total + $company->grand_total + $vr_val_t['total_variation'];
    }

    $last_year = intval(date("Y")) - 1;
    $this_month = date("m");
    $this_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$this_day/$this_month/$last_year";

    foreach ($client_details as $company) {
      //$percent = round(100/($list_total/$company->grand_total));

      
      if($company->grand_total == 0){
        $percent = round(100/($list_total/1));
      }elseif($list_total == 0){
        $percent = round(100/(1/$company->grand_total));
      }else{
        $percent = round(100/($list_total/$company->grand_total));
      }
 


      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);

      $comp_name = $company->company_name;


      $q_vr_c_u = $this->dashboard_m->client_vr_value($date_a_last,$date_b_last,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c_u->getResultArray();
      $vr_val_u = array_shift($getResultArray);

      $last_year_q = $this->dashboard_m->get_top_ten_clients($date_a_last, $date_b_last,$company->company_id);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = ($last_year_sale['grand_total'] ?? 0) + ($vr_val_u['total_variation'] ?? 0);

      $total_price = $company->grand_total+ $vr_val['total_variation'];


      echo "['". str_replace("'","&apos;",$comp_name)."', ".$total_price."],";


    }
  }


  function get_wip_value_permonth($date_a,$date_b,$comp_id='',$type=''){
    $q_wip_vales = $this->dashboard_m->get_wip_permonth($date_a,$date_b,$comp_id, $type);
    $wip_values = $q_wip_vales->getResult();

    $amount = 0;
    $total = 0;
    $count = 0;

    foreach ($wip_values as $prj_wip){
      if($prj_wip->label == 'VR' ){
        $amount = $prj_wip->variation_total;
      }else{
        if($prj_wip->install_time_hrs > 0 || $prj_wip->work_estimated_total > 0.00   ){
          $amount = $prj_wip->project_total * ($prj_wip->progress_percent/100);
        }else{
          $amount = $prj_wip->budget_estimate_total * ($prj_wip->progress_percent/100);
        }
      }

      $total = $total + $amount;
      $count++;
    }

    return $total;
  }

  public function labour_sched_dates_totals(){
    $this->admin_m = new Admin_m();

    $custom = " AND `company_details`.`company_id` !=  '3197' AND `company_details`.`company_id` !=  '4' ";
    $today = date('m/d/Y');
    $this_year = date('Y');

    $endyear_date = '31/12/'.$this_year;


    $curr_date_stamp = (new \CodeIgniter\I18n\Time(date("Y-m-d")));
    $curr_week_number = $curr_date_stamp->getWeekOfYear();




    $q_exluded_cats = $this->dashboard_m->get_current_exluded_prjCat();
    $getResultArray = $q_exluded_cats->getResultArray();
    $exc_cats = array_shift($getResultArray);
    $exc_cats_arr = explode(',', $exc_cats['labour_sched_categories'] );
    $exc_cats_arr =  array_filter($exc_cats_arr);
    $exlude_list = '';

    foreach ($exc_cats_arr as $key => $value) {
      $exlude_list .= "'$value',";
    }

    $exclude_list = rtrim($exlude_list, ", ");

    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();

    $focus_company_hrs_wip = array();
    $focus_company_hrs_qut = array();

    $ovral_company_hrs_wip = 0;
    $ovral_company_hrs_qut = 0;

    $weekStart = $curr_week_number;// - 2;
    $weekLimit = date('d/m/Y');
    $weekEnd = $weekStart+24;


    foreach ($focus_company as $company){

      $com_name_lbsl = trim($company->company_name);
      $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl);

      $focus_arr[$company->company_id] = $com_name_lbsl;
      $focus_company_hrs_wip[$company->company_id] = array();
      $focus_company_hrs_qut[$company->company_id] = array();

      $focus_company_hrs_wip[$company->company_id] = 0;
      $focus_company_hrs_qut[$company->company_id] = 0;

    }

    $q_get_labour_dates = $this->dashboard_m->get_dash_labour_hrs($weekLimit,$endyear_date,'',$exclude_list,1); // WIP
    $labour_dates = $q_get_labour_dates->getResult();
    foreach ($labour_dates as $data){
      $focus_company_hrs_wip[$data->focus_company_id] = $focus_company_hrs_wip[$data->focus_company_id] + $data->site_hours;
      $ovral_company_hrs_wip = $ovral_company_hrs_wip + $data->site_hours;
    
    }

    $q_get_labour_dates = $this->dashboard_m->get_dash_labour_hrs($weekLimit,$endyear_date,'',$exclude_list,0); // QUTOES
    $quote_labour_dates = $q_get_labour_dates->getResult();
    foreach ($quote_labour_dates as $data){
      $focus_company_hrs_qut[$data->focus_company_id] = $focus_company_hrs_qut[$data->focus_company_id] + $data->site_hours;
      $ovral_company_hrs_qut = $ovral_company_hrs_qut + $data->site_hours;
    }

    $return_text = '';


    foreach ($focus_company as $company){
      $return_text .= $focus_company_hrs_wip[$company->company_id].',';
      $return_text .= $focus_company_hrs_qut[$company->company_id].',';
    }

    $return_text .= $ovral_company_hrs_wip.','.$ovral_company_hrs_qut;
    return $return_text;
  }


  public function get_count_maint_per_week($this_year,$is_ave=0,$over=''){

      $counter_loop = 0;
      $ave_year = 0;

      if($is_ave == 1){
        $counter_loop = date("Y") - $this_year; 
        $ave_year = $counter_loop;
      }


      $list_data = array(0);

      $this_year_date = '01/01/'.$this_year;
      $next_year_date = '01/01/'.($this_year+1);

      if($is_ave == 1){
        $next_year_date = '01/01/'.(date("Y")+1);
      }

      $q_maintenance_projects = $this->dashboard_m->list_maintenance_projects_jb($this_year_date,$next_year_date);
      $maintenance_projects = $q_maintenance_projects->getResult();
      foreach ($maintenance_projects as $mn_prj){
        $job_date_formatted = date_format(date_create_from_format('d/m/Y', $mn_prj->job_date), 'Y-m-d');

        $week_number = date("W", strtotime($job_date_formatted));
        $week_index = $week_number - 1;
          //$list_data[$week_index]++;

        if(isset($list_data[$week_index]) && array_key_exists($week_index,$list_data)  ){
          $list_data[$week_index]++;
        }else{
          $list_data[$week_index] = 1;
        }
      }

      for($x=0;$x<52;$x++){
        if(isset($list_data[$x]) && array_key_exists($x,$list_data)  ){
          if($is_ave == 1){
            $list_data[$x] =    ( ($list_data[$x]  / $ave_year )   ) /5 ;
          } else{
            $list_data[$x] =     ($list_data[$x]  )  / 5;
          }
        }else{
          $list_data[$x] = 0;
        }
      }

      if($over != ''){
        var_dump($list_data);
      }

      return implode(',', $list_data);
    }




  public function labour_sched_dates(){
    $this->admin_m = new Admin_m();

    $custom = " AND `company_details`.`company_id` !=  '3197' AND `company_details`.`company_id` !=  '4' ";
    $today = date('m/d/Y');
    $this_year = date('Y');


    $curr_date_stamp = (new \CodeIgniter\I18n\Time(date("Y-m-d")));
    $curr_week_number = $curr_date_stamp->getWeekOfYear();



    $q_exluded_cats = $this->dashboard_m->get_current_exluded_prjCat();
    $getResultArray = $q_exluded_cats->getResultArray();
    $exc_cats = array_shift($getResultArray);
    $exc_cats_arr = explode(',', $exc_cats['labour_sched_categories'] );
    $exc_cats_arr =  array_filter($exc_cats_arr);
    $exlude_list = '';

    foreach ($exc_cats_arr as $key => $value) {
      $exlude_list .= "'$value',";
    }

    $exclude_list = rtrim($exlude_list, ", ");

    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();

    $focus_company_hrs_wip = array();
    $focus_company_hrs_qut = array();

    $ovral_company_hrs_wip = array();
    $ovral_company_hrs_qut = array();


    $ovral_company_hrs_old = array();
    $focus_company_hrs_old = array();


    $weekStart = $curr_week_number - 2;

    $weekLimit = date('d/m/Y',strtotime($this_year."W".$weekStart));

    $weekEnd = $weekStart+24;

    foreach ($focus_company as $company){

      $com_name_lbsl = trim($company->company_name);
      $com_name_lbsl = str_replace("Pty Ltd","",$com_name_lbsl);

      $focus_arr[$company->company_id] = $com_name_lbsl;
      $focus_company_hrs_wip[$company->company_id]  = array();
      $focus_company_hrs_qut[$company->company_id]  = array();

      $focus_company_hrs_wip[$company->company_id] = array_fill($weekStart,25,0);
      $focus_company_hrs_qut[$company->company_id] = array_fill($weekStart,25,0);

      $ovral_company_hrs_wip = array_fill($weekStart,25,0);
      $ovral_company_hrs_qut = array_fill($weekStart,25,0);
      $ovral_company_hrs_old = array_fill($weekStart,25,0);


      $focus_company_hrs_old[$company->company_id]  = array();
      $focus_company_hrs_old[$company->company_id] = array_fill($weekStart,25,0);
    }

    $q_get_labour_dates = $this->dashboard_m->get_labour_dates($exclude_list,$weekStart,$weekEnd,$weekLimit,$this_year);
    $labour_dates = $q_get_labour_dates->getResult();

    foreach ($labour_dates as $data){
      if($data->job_date != ''){
        $focus_company_hrs_wip[$data->focus_company_id][$data->week_number] = $focus_company_hrs_wip[$data->focus_company_id][$data->week_number] + $data->site_hours;
        $ovral_company_hrs_wip[$data->week_number] = $ovral_company_hrs_wip[$data->week_number] + $data->site_hours;
      }else{
        $focus_company_hrs_qut[$data->focus_company_id][$data->week_number] = $focus_company_hrs_qut[$data->focus_company_id][$data->week_number] + $data->site_hours;
        $ovral_company_hrs_qut[$data->week_number] = $ovral_company_hrs_qut[$data->week_number] + $data->site_hours;
      }
    }

    $ovral_company_wip = implode(',', $ovral_company_hrs_wip);
    $ovral_company_qut = implode(',', $ovral_company_hrs_qut);
 

    echo '[\'Overall WIP\','.$ovral_company_wip.'],';
    echo '[\'Overall Quote\','.$ovral_company_qut.'],';

    foreach ($focus_company as $company){
      $company_wip = implode(',', $focus_company_hrs_wip[$company->company_id]);
      $company_qut = implode(',', $focus_company_hrs_qut[$company->company_id]);

      echo '[\''.$focus_arr[$company->company_id].'WIP\','.$company_wip.'],';
      echo '[\''.$focus_arr[$company->company_id].'Quote\','.$company_qut.'],';
    }
 

    $last_year = $this_year-1;

//    $weekLimitOld = strftime('%d/%m/%Y',strtotime( $last_year."W".$weekStart));

    $weekLimitOld = date('d/m/Y',strtotime($last_year."W".$weekStart));




    $q_get_labour_dates = $this->dashboard_m->get_labour_dates($exclude_list,$weekStart,$weekEnd,$weekLimitOld,$last_year,1);
    $labour_dates = $q_get_labour_dates->getResult();

    foreach ($labour_dates as $data){
      $ovral_company_hrs_old[$data->week_number] = $ovral_company_hrs_old[$data->week_number] + $data->site_hours;
      $focus_company_hrs_old[$data->focus_company_id][$data->week_number] = $focus_company_hrs_old[$data->focus_company_id][$data->week_number] + $data->site_hours;
    }

    $ovral_company_old = implode(',', $ovral_company_hrs_old);
 
    echo '[\'Overall Last Year\','.$ovral_company_old.'],';

    foreach ($focus_company as $company){
      $company_hrs_old = implode(',', $focus_company_hrs_old[$company->company_id]);
      echo '[\''.$focus_arr[$company->company_id].'Last Year\','.$company_hrs_old.'],';
    }
  }

  public function get_count_maintenance(){
    $this_year = date('Y');

    $this_year_date = '01/01/'.$this_year;
    $this_year_end = date('d/m/Y');


    $last_year_date = '01/01/'.($this_year-1);
    $last_year_end = date('d/m/').($this_year-1);

    $count_m_prj_aq = $this->dashboard_m->count_maintenance_projects($this_year_date,$this_year_end);
    $getResult = $count_m_prj_aq->getResult();
    $count_m_prj_a = array_shift($getResult);
    $limit = 0;

    $count_m_prj_bq = $this->dashboard_m->count_maintenance_projects($last_year_date,$last_year_end);
    $getResult = $count_m_prj_bq->getResult();
    $count_m_prj_b = array_shift($getResult);

    if($count_m_prj_a->prj_numbers > $count_m_prj_b->prj_numbers ){
      $limit = $count_m_prj_a->prj_numbers + 50;
    }else{
      $limit = $count_m_prj_b->prj_numbers + 50;
    }


    echo '<div id=""  class="tooltip-enabled" title="" data-placement="top" data-html="true" data-original-title="Total Projects for '.$this_year.': '.number_format($count_m_prj_a->prj_numbers).'"><div class="clearfix center knob_box pad-10 small-widget"  id="" style="height:auto; width:100%;">                     
    <input class="knob hide" data-width="100%" data-step=".1" data-displayInput="false" data-linecap="round"   data-thickness=".15" value="'.$count_m_prj_a->prj_numbers.'" readonly data-fgColor="#FD5309" data-angleOffset="-180"  data-max="'.$limit.'">
    </div></div>';

    echo '<div id=""  class="tooltip-enabled" title="" data-placement="bottom" data-html="true" data-original-title="Total Projects for '.($this_year-1).': '.number_format($count_m_prj_b->prj_numbers).'" ><div class="clearfix center knob_box pad-10 small-widget" id="" style="height:65%; width:75%; position: absolute;top: 10.5%; left: 12%;">                        
    <input class="knob hide" data-width="100%" data-step=".1" data-displayInput="false" data-linecap="round"   data-thickness=".19" value="'.$count_m_prj_b->prj_numbers.'" readonly data-fgColor="#8600AF" data-angleOffset="-180"  data-max="'.$limit.'">
    </div></div>';


    echo '<div> <p style="font-size: 15px;">
    <span style="color:#FD5309;"><strong>'.$this_year.': '.number_format($count_m_prj_a->prj_numbers).'</strong></span>
    <span style="color:#FD5309;" class="pull-right"><strong>$ '.number_format($count_m_prj_a->total_proj_exgst).'</strong></span>
    <br>
    <span style="color:#8600AF;"><strong>'.($this_year-1).': '.number_format($count_m_prj_b->prj_numbers).'</strong></span>
    <span style="color:#8600AF;" class="pull-right"><strong>$ '.number_format($count_m_prj_b->total_proj_exgst).'</strong></span>
    </p></div>';

  }

  public function focus_top_ten_clients_donut($year_set='',$is_financial=''){

    if(isset($year_set) && $year_set!=''){

      $year =  $year_set;
    }else{
      $year = date("Y");

    }



    $current_date = date("d/m")."/".$year;
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    
    if(isset($is_financial) && $is_financial != ''){
      $current_start_year = '01/07/'.($year-1);
      $current_date = '30/06/'.$year;
    }



    $q_clients = $this->dashboard_m->get_top_ten_clients($current_start_year, $current_date);
    $client_details  = $q_clients->getResult();
    
    $list_total = 0;



    foreach ($client_details as $company) {
      $q_vr_c_t = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id);
      $getResultArray = $q_vr_c_t->getResultArray();
      $vr_val_t = array_shift($getResultArray);
      $list_total = $list_total + $company->grand_total + $vr_val_t['total_variation'];
    }

    $last_year = intval(date("Y")) - 1;
    $this_month = date("m");
    $this_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$this_day/$this_month/$last_year";

    foreach ($client_details as $company) {


      if($company->grand_total > 0){
        $percent = round(100/($list_total/$company->grand_total));
      }else{
        $percent = round(100/($list_total/1));
      }

      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);
      $comp_name = $company->company_name_group;

      $q_vr_c_u = $this->dashboard_m->client_vr_value($date_a_last,$date_b_last,$company->client_id);
      $getResultArray = $q_vr_c_u->getResultArray();
      $vr_val_u = array_shift($getResultArray);

      $last_year_q = $this->dashboard_m->get_top_ten_clients($date_a_last, $date_b_last,$company->company_id);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $grand_total = $last_year_sale['grand_total'] ?? 0;
      $total_variation = $vr_val_u['total_variation'] ?? 0;

      $lst_year_total = $grand_total + $total_variation;
      $client_cost = $company->grand_total + $total_variation;

      if($client_cost > 0){
        echo "['". str_replace("'","&apos;",$comp_name)."', ".$client_cost."],";
      }
    }
  }


  public function focus_top_ten_con_sup_donut($type){
    $current_date = date("d/m/Y");
    $year = date("Y");

    $last_year = intval(date("Y")) - 1;

    $base_year = '01/01/'.$year;

    $next_year_date = '01/01/'.$last_year;
    $current_start_year = date("d/m/Y");
    $last_start_year = '01/01/'.$last_year;

    $q_companies = $this->dashboard_m->get_company_sales($type,$base_year,$current_start_year);
    $company_details  = $q_companies->getResult();
    $counter = 0;


    $list_total = 0;

    foreach ($company_details as $company) {
      $list_total = $list_total + $company->total_price;
    }

    foreach ($company_details as $company) {
      $counter ++;
      $total = $company->total_price;
      $percent = round(100/($list_total/$company->total_price));

      $q_clients_overall = $this->dashboard_m->get_company_sales_overall($company->company_id);
      $getResultArray = $q_clients_overall->getResultArray();
      $overall_cost = array_shift($getResultArray);
      $grand_total = $overall_cost['total_price'];

      $comp_name = $company->company_name;

      $cmp_id = $company->company_id;

      $last_year_q = $this->dashboard_m->get_company_sales('',$base_year,$current_start_year,$current_start_year);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = $last_year_sale['total_price'] ?? 0;

      echo "['". str_replace("'","&apos;",$comp_name)."', ".$company->total_price."],";

    }
  }


  public function focus_top_ten_clients_mn($is_pie = '',$year_set='',$is_mr=''){
    $focus_company_maintenence_q = $this->dashboard_m->get_focus_companies_mntnc();
    $focus_company_maintenence = $focus_company_maintenence_q->getResult();

    $maintenacne_data = array(); 
    foreach ($focus_company_maintenence as $maintenance_data){
      $maintenacne_data[$maintenance_data->focus_company_id] = 0;
      $pm_id = $maintenance_data->project_manager_id;
    }

    $comp_q = " AND `project`.`job_category` = 'Maintenance' ";

    $current_date = date("d/m/Y");
    $current_year = date("Y");

    if($year_set != ''){
      if($current_year == $year_set){
        $current_date = date("d/m/Y");
        $year = date("Y");
        $last_year = intval(date("Y")) - 1;   
      }else{
        $current_date = '31/12/'.$year_set;
        $year = $year_set;
        $last_year = $year_set -1;
      }
    }else{
      $current_date = date("d/m/Y");
      $year = date("Y");
      $last_year = intval(date("Y")) - 1; 

    }


    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    $q_clients = $this->dashboard_m->get_top_ten_clients($current_start_year, $current_date,'','',$comp_q);
    $client_details  = $q_clients->getResult();

    $list_total = 0;
    foreach ($client_details as $company) {
      $q_vr_c_t = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c_t->getResultArray();
      $vr_val_t = array_shift($getResultArray);
      $list_total = $list_total + round($company->grand_total + $vr_val_t['total_variation']);
    }

    $this_month = date("m");
    $this_day = date("d");


    $comp_total = array();
    $comp_name = array();

    foreach ($client_details as $company) {
      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id,$comp_q);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);
      $cost_gtotl_amnt = round($company->grand_total+ $vr_val['total_variation']);
      $comp_total[$company->company_id] = $cost_gtotl_amnt;
      $comp_name[$company->company_id] = $company->company_name_group;
    }

    arsort($comp_total);

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$this_day/$this_month/$last_year";

    if(count($comp_total) == 0 && $is_pie != 'pie'){
      echo "<p><center><strong>No Records Yet.</strong></csnter></p>";
    }

    foreach ($comp_total as $raw_id => $compute_amount) {

      if($compute_amount < 1){
        $percent = round(100/($list_total/1),1);
      }else{
        $percent = round(100/($list_total/$compute_amount),1);
      }


      if($is_pie == ''){
        echo '<div class="mr_comp_name col-sm-6 col-md-7"><i class="fa fa-chevron-circle-right"></i>  &nbsp; ';
      }

      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$raw_id,$comp_q);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);

      $company_name = $comp_name[$raw_id];
      $company_name_group = $comp_name[$raw_id];

      if($is_pie == ''){
        if(strlen($company_name_group) > 30){
          echo '<span class="tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$company_name_group.'">'.substr($company_name_group,0,30).'...</span>';
        }else{
          echo $company_name_group;
        }
      }

      $q_vr_c_u = $this->dashboard_m->client_vr_value($date_a_last,$current_start_year,$raw_id,$comp_q);
      $getResultArray = $q_vr_c_u->getResultArray();
      $vr_val_u = array_shift($getResultArray);

      $last_year_q = $this->dashboard_m->get_top_ten_clients($date_a_last, $current_start_year,$raw_id);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $grand_total = $last_year_sale['grand_total'] ?? 0;
      $total_variation = $vr_val_u['total_variation'] ?? 0;

      $lst_year_total = $grand_total + $total_variation;


      if($is_pie != ''){
        echo "['". str_replace("'","&apos;",$company_name_group)."', ".$compute_amount."],";
      }else{
        echo ' </div><div class="mr_comp_val col-sm-4 col-md-2"><strong>'.number_format($percent,1).'%</strong></div>  <div class="mr_comp_val col-sm-4 col-md-3 tooltip-test" title="" data-placement="left" data-original-title="Last Year : $ '.number_format($lst_year_total).'"><i class="fa fa-usd"></i> '.number_format($compute_amount).'</div><div class="col-sm-12"><hr class="block m-bottom-5 m-top-5"></div>';
      }
    }
  }



  public function focus_top_ten_con_sup_mn($type,$pie=''){
    $focus_company_maintenence_q = $this->dashboard_m->get_focus_companies_mntnc();
    $focus_company_maintenence = $focus_company_maintenence_q->getResult();

    $maintenacne_data = array(); 
    foreach ($focus_company_maintenence as $maintenance_data){
      $maintenacne_data[$maintenance_data->focus_company_id] = 0;
      $pm_id = $maintenance_data->project_manager_id;
    }

    $comp_q = " AND `project`.`job_category` = 'Maintenance' ";

    $current_date = date("d/m/Y");
    $year = date("Y");

    $last_year = intval(date("Y")) - 1;
    $base_year = '01/01/'.$year;

    $next_year_date = '01/01/'.$last_year;
    $current_start_year = date("d/m/Y");

    $last_start_year = '01/01/'.$last_year;
    $last_year_current_date = date("d/m/").$last_year;

    $q_companies = $this->dashboard_m->get_company_sales($type,$base_year,$current_start_year,'',$comp_q);
    $company_details  = $q_companies->getResult();
    $counter = 0;

    $list_total = 0;

    foreach ($company_details as $company) {
      $list_total = $list_total + round($company->total_price);
    }

    foreach ($company_details as $company) {
      $counter ++;
      $total = $company->total_price;
      $percent = round(100/($list_total/$company->total_price),1);

      $q_clients_overall = $this->dashboard_m->get_company_sales_overall($company->company_id);
      $getResultArray = $q_clients_overall->getResultArray();
      $overall_cost = array_shift($getResultArray);
      $grand_total = $overall_cost['total_price'];

      $comp_name = $company->company_name;
      if($pie == ''){
        echo '<div class="col-sm-8 col-md-7"><i class="fa fa-chevron-circle-right"></i>  &nbsp; ';

        if(strlen($comp_name) > 30){
          echo '<span class="tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$comp_name.'">'.substr($comp_name,0,30).'...</span>';
        }else{
          echo $comp_name;
        }
      }

      $cmp_id = $company->company_id;

      $last_year_q = $this->dashboard_m->get_company_sales('',$last_start_year,$base_year,$cmp_id,$comp_q);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = $last_year_sale['total_price'] ?? 0;

      if($pie != ''){ 
        echo "['". str_replace("'","&apos;",$comp_name)."', ".$company->total_price."],";

      }else{
        echo ' </div><div class="col-md-2 col-sm-4"><strong>'.number_format($percent,1).'%</strong></div>  <div class="col-md-3 col-sm-4 tooltip-test" title="" data-placement="left" data-original-title="Last Year : $ '.number_format($lst_year_total).'"><i class="fa fa-usd"></i> '.number_format($company->total_price).'</div><div class="col-sm-12"><hr class="block m-bottom-5 m-top-5"></div>';
      }
    }
  }






  public function focus_top_ten_con_sup_mn_donut($type){

    $focus_company_maintenence_q = $this->dashboard_m->get_focus_companies_mntnc();
    $focus_company_maintenence = $focus_company_maintenence_q->getResult();

    $maintenacne_data = array(); 
    foreach ($focus_company_maintenence as $maintenance_data){
      $maintenacne_data[$maintenance_data->focus_company_id] = 0;
      $pm_id = $maintenance_data->project_manager_id;
    }

    $limit = 0;

    $comp_q = " AND `project`.`job_category` = 'Maintenance' ";
    $current_date = date("d/m/Y");
    $year = date("Y");

    $last_year = intval(date("Y")) - 1;

    $base_year = '01/01/'.$year;

    $next_year_date = '01/01/'.$last_year;
    $current_start_year = date("d/m/Y");
    $last_start_year = '01/01/'.$last_year;

    $q_companies = $this->dashboard_m->get_company_sales($type,$base_year,$current_start_year,'',$comp_q);
    $company_details  = $q_companies->getResult();
    $counter = 0;


    $list_total = 0;

    foreach ($company_details as $company) {
      $list_total = $list_total + $company->total_price;
    }

    foreach ($company_details as $company) {
      $counter ++;
      $total = $company->total_price;
      $percent = round(100/($list_total/$company->total_price));

      $q_clients_overall = $this->dashboard_m->get_company_sales_overall($company->company_id);
      $getResultArray = $q_clients_overall->getResultArray();
      $overall_cost = array_shift($getResultArray);
      $grand_total = $overall_cost['total_price'];

      $comp_name = $company->company_name;
      $cmp_id = $company->company_id;

      $last_year_q = $this->dashboard_m->get_company_sales('',$base_year,$current_start_year,$cmp_id,$comp_q);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = $last_year_sale['total_price'];
      $total_price = $company->total_price;
      echo "['". str_replace("'","&apos;",$comp_name)."', ".$total_price."],";

    }
  }

  public function focus_get_map_locations(){
    $current_date = date("d/m/Y");
    $year = date("Y");
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    $q_maps = $this->dashboard_m->get_map_projects($current_start_year,$current_date);
    $map_details = $q_maps->getResult();
    $count = 0;

    echo "[";
    foreach ($map_details as $map) {

      if($map->y_coordinates != '' && $map->x_coordinates != ''){

        echo '{"longitude":'.$map->y_coordinates.', "latitude": '.$map->x_coordinates.'},';
        $count++;
      }
    }
    echo '],"count": '.$count.'';
  }


  public function emp_get_locations_points(){

    $get_locations_points_q = $this->dashboard_m->get_locations_points();
    $focus_lcations = $get_locations_points_q->getResult();

    $count = 0;

    echo "[";

    foreach ($focus_lcations as $map){

      $get_employee_location_q = $this->dashboard_m->get_employee_location($map->location_address_id);
      $employee_location = $get_employee_location_q->getResult();

      echo '{"longitude":'.$map->y_coordinate.', "latitude": '.$map->x_coordinate.', "info_head": "<p><strong>'.$map->location.'</strong></p>", "info_text": "<p>';

      foreach ($employee_location as $employee){
        echo $employee->user_first_name.' '.$employee->user_last_name.'<br />';
      }

      echo '</p>" },';
      $count++;
    }

    echo '],"count": '.$count.'';
  }


  public function focus_company_sep_thermo($company_id='',$focus_company='',$initials=''){

    $c_year = date("Y");    
    $date_a = "01/01/$c_year";
    $date_b = date("d/m/Y");
    $total_sales = 0;

    $forecasted_amount = 0;

    $q_pm_sales = $this->dashboard_m->dash_total_pm_sales('',$c_year,'',$date_a,$date_b,$company_id);

    if($q_pm_sales->getNumRows() >= 1){
      $pm_sales = $q_pm_sales->getResultArray();
      foreach ($pm_sales as $sales => $value){
        if($value['label'] == 'VR'){
          $project_total_percent = $value['variation_total'];
        }else{
          $project_total_percent = $value['project_total'] * ($value['progress_percent']/100);
        }
        $total_sales = $total_sales + $project_total_percent;
      }
    }else{
      $total_sales = $total_sales + 0;
    }

    $n_year =  date("Y")+1;
    $set_new_date = '01/01/'.$n_year;

    $wip_amount = $this->get_wip_personal($date_a,$set_new_date,'',$company_id);

    $current_sales =  $wip_amount + $total_sales ;


    $q_get_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$company_id,1);
    $current_forecast_fc = $q_get_current_forecast->getResult();
    $current_forecast_fcur = array_shift($current_forecast_fc);


    if($q_get_current_forecast->getNumRows() >= 1){
      $forecasted_amount =  $current_forecast_fcur->total * ($current_forecast_fcur->forecast_percent/100) ;
    }

    if($forecasted_amount > 0){
      $percent_progess = ($current_sales/$forecasted_amount) * 100;
    }else{
      $percent_progess = ($current_sales/1) * 100;
    }

    $focus_company = str_replace('%20',' ', $focus_company);

    echo'
    <div class="progress-bar progress-bar-danger active progress-bar-striped tooltip-pb" style="background-color: rgb(251, 25, 38);width: '.$percent_progess.'%;border-radius: 0px 10px 10px 0px;"   data-original-title="'.$focus_company.' &nbsp;  $'.number_format($current_sales).' - Overall Progress" >'.number_format($percent_progess).'%</div> 
    ';

    if(isset($initials) && $initials!= ''){
      echo '<span style=" position: absolute;    left: 23px;   top:13px; font-size: 12px;   color: #fff;">'.$initials.'</span>';
    }

  }



  public function progressBar($assign_id='',$comp_id='',$initials=''){
    $user_id = '';
    $type = '';
    $init_f_total = 0;
    $init_c_total = 0;

    if(isset($assign_id) && $assign_id != '' && $assign_id != 0){
      $user_id = $assign_id;
      $type = 1;
    }else{
      $user_id = '';
      $type = '';
    }

    if(isset($comp_id) && $comp_id != '' && $comp_id != 0){
      $user_id = $assign_id;
      $type = '';
    }

    $c_year = date("Y");
    $c_month = date("m");

    $date_a = "01/01/$c_year";
    $date_b = date("d/m/Y");
    $date_c = "01/$c_month/$c_year";

    $months = array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");

    $focus_wip_overall = $this->get_wip_value_permonth($date_a,$date_b,$user_id,$type);   //--------------------


    if(isset($comp_id) && $comp_id != '' && $comp_id != 0){
      $q_curr_wip_rem = $this->dashboard_m->get_current_wip_remaining($assign_id,$date_a,$date_b);
      $q_current_invoiced_amount = $this->dashboard_m->get_current_invoiced($assign_id,$date_a,$date_b); 

    }else{

      $focus_wip_overall = $focus_wip_overall - $this->get_wip_value_permonth($date_a,$date_b,'3197',$type);
      $q_curr_wip_rem = $this->dashboard_m->get_current_wip_remaining('',$date_a,$date_b,$user_id);
      $q_current_invoiced_amount = $this->dashboard_m->get_current_invoiced('',$date_a,$date_b,$user_id); 

    }

    $getResultArray = $q_curr_wip_rem->getResultArray();
    $curr_wip_rem =  array_shift($getResultArray);


    $getResultArray = $q_current_invoiced_amount->getResultArray();
    $current_invoiced_amount =  array_shift($getResultArray);

    $q_forecast = $this->dashboard_m->fetch_forecast($c_year,'1');
    $getResultArray = $q_forecast->getResultArray();
    $forecast =  array_shift($getResultArray);

    $c_month_text = strtolower(date('M'));
    $percent_total = 0;


    for ($i=0; $i<12; $i++){

      $cost_forecast_amnt = $forecast['forecast_'.$months[$i]] ?? 0;

      $percent_total = $percent_total + $cost_forecast_amnt;

      if($c_month_text == $months[$i]){
        break;
      }
    }



    $current_standing = floatval($focus_wip_overall)+floatval($current_invoiced_amount['curr_invoiced']);  //!!!!!!!!!!!!!!!!!!!



    if(isset($assign_id) && $assign_id != '' && $assign_id != 0){

      if($assign_id == 29){
     
        $q_pm_forecast = $this->dashboard_m->get_current_forecast($c_year,$user_id,3197,1);
     
      }else{


        $rev_forecast = $forecast['revenue_forecast_id'] ?? 0;

        if(isset($comp_id) && $comp_id != '' && $comp_id != 0){
          $q_pm_forecast = $this->dashboard_m->fetch_pm_forecast_details($c_year,$rev_forecast,'',$user_id);
        }else{
          $q_pm_forecast = $this->dashboard_m->fetch_pm_forecast_details($c_year,$rev_forecast,$user_id);
        }


      }



      $pm_forecast = $q_pm_forecast->getResult();

      foreach ($pm_forecast as $pm_val_forecast){

          if($assign_id == 29){

            $init_temp = $pm_val_forecast->total * ($pm_val_forecast->forecast_percent /100);
            $init_temp = $pm_val_forecast->total  * ($pm_val_forecast->mns_fct_b/100);

          }else{


            if(isset($comp_id) && $comp_id != '' && $comp_id != 0){

              $init_temp = ($forecast['total'] * ($pm_val_forecast->f_comp_forecast_percent/100) ) ;
            }else{

              $init_temp = ($forecast['total'] * ($pm_val_forecast->f_comp_forecast_percent/100) ) * ($pm_val_forecast->forecast_percent / 100);
            }
          }


          $init_c_total = $init_c_total + $init_temp;

        }



        $forecasted_amount = $init_c_total * ($percent_total/100);

        if($current_standing > 0 && $forecasted_amount > 0){
          $current_progress = 100 / ( $forecasted_amount / $current_standing );
        }elseif($forecasted_amount > 0 && $current_standing <= 0 ){
          $current_progress = 100 / ( $forecasted_amount );
        }elseif($forecasted_amount <= 0 && $current_standing > 0 ){
          $current_progress = 100 / ( $current_standing );
        }else{
          $current_progress = 100 / 1;
        }

      }else{   ///////////////////// OVERALL FOCUS FORECAXST ACMOUNT  



        $raw_fwansq_q = $this->dashboard_m->get_focus_WANSW_forecast_percent($c_year);
        $getResultArray = $raw_fwansq_q->getResultArray();
        $wa_nsw_forecast =  array_shift($getResultArray);

        $set_forecast_amount = $forecast['total'] ?? 0;
        $set_focus_forecast_percent = $wa_nsw_forecast['focus_wansw_forecast_percent'] ?? 0;

        $forecasted_amount = ( $set_forecast_amount * ($set_focus_forecast_percent/100)   ) *     ($percent_total/100);

        $forecasted_amount = ($forecasted_amount <= 1 ? 1 : $forecasted_amount);
        $current_standing = ($current_standing <= 1 ? 1 : $current_standing);
        $current_progress = 100 / ( $forecasted_amount / $current_standing );
      }


      if($current_standing > 0 && $forecasted_amount > 0){
        $current_amnt_progress = 100 / ( $forecasted_amount / $current_standing );
      }elseif($forecasted_amount > 0 && $current_standing <= 0 ){
        $current_amnt_progress = 100 / ( $forecasted_amount );
      }elseif($forecasted_amount <= 0 && $current_standing > 0 ){
        $current_amnt_progress = 100 / ( $current_standing );
      }else{
        $current_amnt_progress = 100 / 1;
      }

      $final_width = abs($current_progress - $percent_total);

      if($current_standing > $forecasted_amount){

        if($forecasted_amount > 0){
          $less_amount_percent = 100 / ( $current_standing / $forecasted_amount );


          if(isset($comp_id) && $comp_id != '' && $comp_id != 0){
            $dyn_width_val=92;
            $dyn_wth_z = 92;
          }else{
            $dyn_width_val=96;
            $dyn_wth_z = 96;
          }

          echo '<div class="x progress-bar progress-bar-danger active progress-bar-striped tooltip-enabled" data-html="true" data-placement="bottom" data-original-title="Current Standing plus YTD WIP<br />$'.number_format($current_standing).'"  style="position: absolute; width: '.$dyn_width_val.'%; background-color: #1c61a7; border-radius: 20px; height: 20px; text-align: right; padding-right: 10px; ">'.number_format($current_amnt_progress).'%</div>
          <div class="progress-bar progress-bar-danger active progress-bar-striped tooltip-enabled" data-html="true" data-original-title="Cumulative Forecast YTD<br />$'.number_format($forecasted_amount).'<br /><br />Current Standing plus YTD WIP<br />$'.number_format($current_standing).' at '.number_format($current_amnt_progress).'%" style="z-index: 1; position: absolute; width: '.($less_amount_percent-6.5).'%; background-color: #002C8F; border-radius: 0px 10px 10px 0px; height: 20px;">Cumulative Forecast YTD</div> ';

        }else{
          $less_amount_percent = 100 / ( $current_standing / 1 );
          $forecasted_amount = 0;
          echo '<div class="y progress-bar progress-bar-danger active progress-bar-striped tooltip-enabled" data-html="true" data-placement="bottom" data-original-title="Current Standing plus YTD WIP<br />$'.number_format($current_standing).' at 100%"  style="left: 30px; position: absolute; width: 93.5%; background-color: #1c61a7; border-radius: 20px; height: 20px; text-align: right; padding-right: 10px; ">100%</div>';

        }



      }else{

        if(isset($comp_id) && $comp_id != '' && $comp_id != 0){
          $dyn_wth_z = 92;
        }else{
          $dyn_wth_z = 96;
        }

        if($current_standing > 0 && $forecasted_amount > 0){
          $less_amount_percent = 100 / ( $forecasted_amount / $current_standing );
        }elseif($forecasted_amount > 0 && $current_standing <= 0 ){
          $less_amount_percent = 100 / ( $forecasted_amount );
        }elseif($forecasted_amount <= 0 && $current_standing > 0 ){
          $less_amount_percent = 100 / ( $current_standing );
        }else{
          $less_amount_percent = 100 / 1;
        }


        echo '<div class="z progress-bar progress-bar-danger active progress-bar-striped tooltip-enabled" data-html="true" data-original-title="Current Standing plus YTD WIP<br />$'.number_format($current_standing).' at '.number_format($current_amnt_progress).'%<br /><br />Cumulative Forecast YTD<br />$'.number_format($forecasted_amount).'"  style="z-index: 1; position: absolute; width: '.($current_progress-5.5).'%; background-color: #1c61a7; border-radius: 0px 10px 10px 0px; ">'.number_format($current_amnt_progress).'%</div>
        <div class="progress-bar progress-bar-danger active progress-bar-striped tooltip-enabled" data-html="true" data-placement="bottom" data-original-title="Cumulative Forecast YTD<br />$'.number_format($forecasted_amount).'" style="position: absolute; width: '.$dyn_wth_z.'%; background-color: #002C8F; border-radius: 20px;  text-align: right; padding-right: 10px; "><span class="commltv_title_text">Cumulative Forecast YTD</span></div> ';
      }

      if(isset($initials) && $initials != ''){
        echo '<span style=" position: absolute;    left: 23px;   top:13px; font-size: 12px;   color: #fff;">'.$initials.'</span>';
      }
    }



  public function sales_widget(){
    $this->admin_m = new Admin_m();

    $pm_data = $this->dashboard_m->fetch_project_pm_nomore();
    $getResultArray = $pm_data->getResultArray();
    $pm_q = array_shift($getResultArray);
    $not_pm_arr = explode(',',$pm_q['user_id'] );

    $c_year = date("Y");
    $n_year = $c_year+1;
    $date_a = "01/01/$c_year";
    $date_b = "01/01/$n_year";

    $date_c = date("d/m/Y");
    
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    $grand_total_sales = 0;
    $sales_total = 0;

    $display_total = 0;
    $total_string = '';
    $total_string .= '<div class=\'row\'>&nbsp; ('.$c_year.')</div>';

    foreach ($focus_company as $company){
      $q_dash_sales = $this->dashboard_m->dash_sales($date_a,$date_c,$company->company_id,1);

      if($q_dash_sales->getNumRows() >= 1){

        $grand_total_sales = 0;
        $sales_total = 0;

        $dash_sales = $q_dash_sales->getResult();

        foreach ($dash_sales as $sales){
          if( !in_array($sales->project_manager_id, $not_pm_arr) ){

            if($sales->label == 'VR'){
              $sales_total = $sales->variation_total;
            }else{
              $sales_total = $sales->project_total*($sales->progress_percent/100);
            }
            $grand_total_sales = $grand_total_sales + $sales_total;

          }
        }
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$company->company_name).'</span> <span class=\'col-xs-6\'>$ '.number_format($grand_total_sales,2).'</span></div>';
        $display_total = $display_total + $grand_total_sales;
        
      }
    }

    $last_year = intval(date("Y"))-1;
    $n_month = date("m");
    $n_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$n_day/$n_month/$last_year";

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    foreach ($focus_company as $company){

      if($company->company_id != 4){
        $q_dash_sales = $this->dashboard_m->dash_sales($date_a_last,$date_b_last,$company->company_id,1);

        $grand_total_sales = 0;
        $sales_total = 0;

        $dash_sales = $q_dash_sales->getResult();

        foreach ($dash_sales as $sales){
          if($sales->label == 'VR'){
            $sales_total = $sales->variation_total;
          }else{
            $sales_total = $sales->project_total*($sales->progress_percent/100);
          }
          $grand_total_sales = $grand_total_sales + $sales_total;
        }
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$company->company_name).'</span> <span class=\'col-xs-6\'>$ '.number_format($grand_total_sales,2).'</span></div>';
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($display_total,2).'</strong></p>';
  }

  public function uninvoiced_widget(){
    $this->admin_m = new Admin_m();
    $this->invoice = new Invoice();

    $c_year = date("Y");
    $c_month = '01';

    $date_a = "01/01/$c_year";

    $n_year = date("Y");
    $n_month = date("m");
    $n_day = date("d");

    $date_b = "$n_day/$n_month/$n_year";

    $unvoiced_total_arr = array();
    $key_id = '';

    $total_string = '<div class=\'row\'> &nbsp; ('.$c_year.')</div>';


    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    $display_total = 0;

    foreach ($focus_company as $company){


      $q_dash_unvoiced = $this->dashboard_m->dash_unvoiced_per_date($date_a,$date_b,$company->company_id);
      $dash_unvoiced = $q_dash_unvoiced->getResult();

      $unvoiced_total = 0;
      $unvoiced_grand_total = 0;

      foreach ($dash_unvoiced as $unvoiced) {
        if($unvoiced->label == 'VR'){
          $unvoiced_total = $unvoiced->variation_total;
        }else{
          $unvoiced_total = $unvoiced->project_total*($unvoiced->progress_percent/100);
        }

        $unvoiced_grand_total = $unvoiced_grand_total + $unvoiced_total;
      }

      if($company->company_id != 4){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$company->company_name).'</span> <span class=\'col-xs-6\'>$ '.number_format($unvoiced_grand_total,2).'</span></div>';
        $display_total = $display_total + $unvoiced_grand_total;
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';
    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    foreach ($project_manager_list as $pm ) {
      $total_outstanding = 0;
      $q_pm_outstanding = $this->dashboard_m->dash_total_pm_sales($pm->user_id,$c_year,1,$date_a,$date_b);
      if($q_pm_outstanding->getNumRows() >= 1){
        $pm_outstanding = $q_pm_outstanding->getResultArray();

        foreach ($pm_outstanding as $sales => $value){

          if($value['label'] == 'VR'){
            $project_total_percent = $value['variation_total'];
          }else{
            $project_total_percent = $value['project_total'] * ($value['progress_percent']/100);
          }

          $outstanding = $this->invoice->get_current_balance($value['project_id'],$value['invoice_top_id'],$project_total_percent);
          $total_outstanding = $total_outstanding + $outstanding;
        }
      }else{
        $total_outstanding = $total_outstanding + 0;
      }

      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm->user_first_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($total_outstanding,2).'</span></div>';
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $date_a_last = "01/01/$last_year";
    $date_b_last = "31/12/$last_year";

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";

    foreach ($project_manager_list as $pm ) {
      $total_outstanding = 0;
      $q_pm_outstanding = $this->dashboard_m->dash_total_pm_sales($pm->user_id,$last_year,1,$date_a_last,$date_b_last);
      if($q_pm_outstanding->getNumRows() >= 1){
        $pm_outstanding = $q_pm_outstanding->getResultArray();

        foreach ($pm_outstanding as $sales => $value){

          if($value['label'] == 'VR'){
            $project_total_percent = $value['variation_total'];
          }else{
            $project_total_percent = $value['project_total'] * ($value['progress_percent']/100);
          }

          $outstanding = $this->invoice->get_current_balance($value['project_id'],$value['invoice_top_id'],$project_total_percent);
          $total_outstanding = $total_outstanding + $outstanding;
        }
      }else{
        $total_outstanding = $total_outstanding + 0;
      }

      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm->user_first_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($total_outstanding,2).'</span></div>';
    }


    $total_alltime_uninvoiced = 0;
    foreach ($focus_company as $company){
      $q_dash_unvoiced = $this->dashboard_m->dash_unvoiced_per_date('01/01/2011',$date_b,$company->company_id);
      $dash_unvoiced = $q_dash_unvoiced->getResult();

      $unvoiced_total = 0;
      $unvoiced_grand_total = 0;

      foreach ($dash_unvoiced as $unvoiced) {
        if($unvoiced->label == 'VR'){
          $unvoiced_total = $unvoiced->variation_total;
        }else{
          $unvoiced_total = $unvoiced->project_total*($unvoiced->progress_percent/100);
        }
        $unvoiced_grand_total = $unvoiced_grand_total + $unvoiced_total;
      }

      if($company->company_id != 4){
        $total_alltime_uninvoiced = $total_alltime_uninvoiced + $unvoiced_grand_total;
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';
    $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>All Time</span> <span class=\'col-xs-6\'>$ '.number_format($total_alltime_uninvoiced,2).'</span></div>';

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($total_alltime_uninvoiced,2).'</strong></p>';
  }

  public function outstanding_payments_widget(){
    $this->admin_m = new Admin_m();
    $this->invoice = new Invoice();

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    $total_string = '';
    $display_total = 0;
    $total_string .= '<div class=\'row\'>&nbsp; ('.date("Y").')</div>';

    $total_outstanding_all_time = 0;


    for ($i=date("Y"); $i>=2015 ; $i--) { 
      if($i == date("Y")){

        $c_year = $i;
        $n_month = date("m");
        $n_day = date("d");

        $date_a = "01/01/$c_year";
        $date_b = "$n_day/$n_month/$c_year";


        foreach ($focus_company as $company){
          if($company->company_id != 4){
            $invoice_amount= 0;
            $outstanding = 0;

            $q_dash_oustanding_payments = $this->dashboard_m->dash_oustanding_payments($date_a,$date_b,$company->company_id);
            $oustanding_payments = $q_dash_oustanding_payments->getResult();

            foreach ($oustanding_payments as $oustanding) {
              if($oustanding->label == 'VR'){
                $invoice_amount = $oustanding->variation_total;
              }else{
                $invoice_amount = $oustanding->project_total*($oustanding->progress_percent/100);
              }

              $outstanding = $outstanding + $this->invoice->get_current_balance($oustanding->project_id,$oustanding->invoice_id,$invoice_amount);
            }

            if($outstanding != 0){
              $total_outstanding_all_time =  $total_outstanding_all_time + $outstanding;
              $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$company->company_name).'</span> <span class=\'col-xs-6\'>$ '.number_format($outstanding,2).'</span></div><p></p>';
            }
          }
        }

      }else{
        $c_year = $i;
        $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$c_year.')</div>';

        $date_a = "01/01/$c_year";
        $date_b = "31/12/$c_year";


        foreach ($focus_company as $company){
          if($company->company_id != 4){
            $invoice_amount= 0;
            $outstanding = 0;

            $q_dash_oustanding_payments = $this->dashboard_m->dash_oustanding_payments($date_a,$date_b,$company->company_id);
            $oustanding_payments = $q_dash_oustanding_payments->getResult();

            foreach ($oustanding_payments as $oustanding) {
              if($oustanding->label == 'VR'){
                $invoice_amount = $oustanding->variation_total;
              }else{
                $invoice_amount = $oustanding->project_total*($oustanding->progress_percent/100);
              }

              $outstanding = $outstanding + $this->invoice->get_current_balance($oustanding->project_id,$oustanding->invoice_id,$invoice_amount);
            }


            if($outstanding != 0){
              $total_outstanding_all_time =  $total_outstanding_all_time + $outstanding;
              $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$company->company_name).'</span> <span class=\'col-xs-6\'>$ '.number_format($outstanding,2).'</span></div><p></p>';
            }
          }
        }
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';
    $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>All Time</span> <span class=\'col-xs-6\'>$ '.number_format($total_outstanding_all_time,2).'</span></div>';
    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($total_outstanding_all_time,2).'</strong></p>';
  }

  public function focus_top_ten_con_sup($type){
    $current_date = date("d/m/Y");
    $year = date("Y");

    $last_year = intval(date("Y")) - 1;
    $base_year = '01/01/'.$year;

    $next_year_date = '01/01/'.$last_year;
    $current_start_year = date("d/m/Y");
    $last_start_year = '01/01/'.$last_year;
    $last_year_current_date = date("d/m/").$last_year;

    $q_companies = $this->dashboard_m->get_company_sales($type,$base_year,$current_start_year);
    $company_details  = $q_companies->getResult();
    $counter = 0;

    $list_total = 0;

    foreach ($company_details as $company) {
      $list_total = $list_total + round($company->total_price);
    }

    foreach ($company_details as $company) {
      $counter ++;
      $total = $company->total_price;
      $percent = round(100/($list_total/ round($company->total_price) ),1);

      $q_clients_overall = $this->dashboard_m->get_company_sales_overall($company->company_id);
      $getResultArray = $q_clients_overall->getResultArray();
      $overall_cost = array_shift($getResultArray);
      $grand_total = $overall_cost['total_price'];

      echo '<div class="col-sm-8 col-md-7"><i class="fa fa-chevron-circle-right"></i>  &nbsp; ';

      $comp_name = $company->company_name;
      if(strlen($comp_name) > 30){
        echo '<span class="tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$comp_name.'">'.substr($comp_name,0,30).'...</span>';
      }else{
        echo $comp_name;
      }

      $cmp_id = $company->company_id;

      $last_year_q = $this->dashboard_m->get_company_sales('',$last_start_year,$base_year);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $lst_year_total = $last_year_sale['total_price'] ?? 0;

      echo ' </div><div class="col-md-2 col-sm-4"><strong>'.number_format($percent,1).'%</strong></div>  <div class="col-md-3 col-sm-4 tooltip-test" title="" data-placement="left" data-original-title="Last Year : $ '.number_format($lst_year_total).'"><i class="fa fa-usd"></i> '.number_format($company->total_price).'</div><div class="col-sm-12"><hr class="block m-bottom-5 m-top-5"></div>';
    }
  }


  public function wid_quoted($es_id = ''){
    $this->admin_m = new Admin_m();

    $total_string = '';
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    $focus_arr = array();
    $quoted_focus_company = array();
    $cost_focus = array();
    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $quoted_focus_company[$company->company_id] = 0;
      $cost_focus[$company->company_id] = 0;
    }

    $estimator = $this->dashboard_m->fetch_project_estimators();
    $estimator_list = $estimator->getResult();
    $quoted_estimator = array();
    $quoted_estimator_name = array();
    $cost_estimator = array();
    foreach ($estimator_list as $est ) {
      $quoted_estimator[$est->project_estiamator_id] = 0;
      $cost_estimator[$est->project_estiamator_id] = 0;
      $quoted_estimator_name[$est->project_estiamator_id] = $est->user_first_name;
    }


    if($es_id != ''){
      $quoted_estimator[$es_id] = 0;
      $cost_estimator[$es_id] = 0;
    }

    $quoted_estimator_name[0] = '';

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();
    $quoted_pm = array();
    $quoted_pm_name = array();
    $cost_pm = array();
    foreach ($project_manager_list as $pm ) {
      $quoted_pm[$pm->user_id] = 0;
      $cost_pm[$pm->user_id] = 0;
      $quoted_pm_name[$pm->user_id] = $pm->user_first_name;
    }

    $is_restricted = 0;

    $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
    foreach ($admin_defaults->getResult() as $row){
      $unaccepted_date_categories = $row->unaccepted_date_categories;
      $unaccepted_no_days = $row->unaccepted_no_days;
    }


    $all_projects_q = $this->dashboard_m->get_all_active_projects();
    foreach ($all_projects_q->getResultArray() as $row){


      $project_cost = 0;
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

      $status = '';
      if($row['job_date'] == '' && $row['is_paid'] == 0){
        $job_category_arr = explode(",",$unaccepted_date_categories);
        foreach ($job_category_arr as $value) {
          if($value ==  $row['job_category']){
            $is_restricted = 1;
          }
        }

        $today = date('Y-m-d');
        $unaccepteddate =strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
        $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

        if(strtotime($unaccepteddate) < strtotime($today)){
          if($is_restricted == 1 && $unaccepted_date == ""){
            $status = 'quote';
          }
        }elseif($unaccepted_date == ""){
          $status = 'quote';
        }else{

        }
      }

      if($status == 'quote'){


        if (array_key_exists($row['project_estiamator_id'], $quoted_estimator)) {
          $quoted_estimator[$row['project_estiamator_id']]++;
        }

        if (array_key_exists($row['project_manager_id'], $quoted_pm)) {
          $quoted_pm[$row['project_manager_id']]++;
        }

        $quoted_focus_company[$row['focus_company_id']]++;

        if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 || $row['variation_total'] > 0.00 ){
          $project_cost = $row['project_total'] + $row['variation_total'];
        }else{
          $project_cost = $row['budget_estimate_total'];
        }

        $cost_focus[$row['focus_company_id']] = $cost_focus[$row['focus_company_id']] + $project_cost;


        if (array_key_exists($row['project_estiamator_id'], $cost_estimator)) {
          $cost_estimator[$row['project_estiamator_id']] = $cost_estimator[$row['project_estiamator_id']] + $project_cost;
        }

        if (array_key_exists($row['project_manager_id'], $cost_pm)) {
          $cost_pm[$row['project_manager_id']] = $cost_pm[$row['project_manager_id']] + $project_cost;
        }

      }
    }

    $display_final = array_sum($quoted_focus_company);
    $display_cost = array_sum($cost_focus);

    $current_year = intval(date("Y"));
    $total_string .= '<div class=\'row\'>&nbsp; ('.$current_year.')</div>';

    foreach ($focus_company as $company){
      if($quoted_focus_company[$company->company_id] > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$company->company_id]).'</span> <span class=\'col-xs-6\'>$  '.number_format($cost_focus[$company->company_id],2).' <span class=\'pull-right\'>'.$quoted_focus_company[$company->company_id].'</span></span></div>';
      }
      $quoted_focus_company[$company->company_id] = 0;
      $cost_focus[$company->company_id] = 0;
    }
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($project_manager_list as $pm ) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$quoted_pm_name[$pm->user_id].'</span> <span class=\'col-xs-6\'>$  '.number_format($cost_pm[$pm->user_id],2).' <span class=\'pull-right\'>'.$quoted_pm[$pm->user_id].'</span></span></div>';
      $cost_pm[$pm->user_id] = 0;
      $quoted_pm[$pm->user_id] = 0;
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    if($es_id != ''){
      $display_cost = $cost_estimator[$es_id];
      $display_final = $quoted_estimator[$es_id];
    }

    foreach ($estimator_list as $est ) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$quoted_estimator_name[$est->project_estiamator_id].'</span> <span class=\'col-xs-6\'>$ '.number_format($cost_estimator[$est->project_estiamator_id],2).' <span class=\'pull-right\'>'.$quoted_estimator[$est->project_estiamator_id].'</span></span></div>';
      $cost_estimator[$est->project_estiamator_id] = 0;
      $quoted_estimator[$est->project_estiamator_id] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";

    $m_month = $n_month+2;
    $year_odl_set = $last_year;

    if($m_month > 12){
      $m_month - 12;
      $year_odl_set = $last_year + 1;
    }

    $date_last_year_next = "01/$m_month/$year_odl_set";

    $all_projects_q = $this->dashboard_m->get_all_active_projects($date_last_year_today,$date_last_year_next);
    foreach ($all_projects_q->getResultArray() as $row){
      $project_cost = 0;
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

      $status = '';
      if($row['job_date'] == '' && $row['is_paid'] == 0){
        $job_category_arr = explode(",",$unaccepted_date_categories);
        foreach ($job_category_arr as $value) {
          if($value ==  $row['job_category']){
            $is_restricted = 1;
          }
        }

        $today = date('Y-m-d');
        $unaccepteddate =strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
        $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

        if(strtotime($unaccepteddate) < strtotime($today)){
          if($is_restricted == 1 && $unaccepted_date == ""){
            $status = 'quote';
          }
        }elseif($unaccepted_date == ""){
          $status = 'quote';
        }else{

        }
      }

      if ( array_key_exists($row['project_estiamator_id'], $quoted_estimator) ) {
        $quoted_estimator[$row['project_estiamator_id']]++;
      }


      if (array_key_exists($row['project_manager_id'], $quoted_pm)) {
        $quoted_pm[$row['project_manager_id']]++;
      }


      if (array_key_exists($row['project_manager_id'], $quoted_pm)) {
        $quoted_pm[$row['project_manager_id']]++;
      }



      $quoted_focus_company[$row['focus_company_id']]++;

      if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 || $row['variation_total'] > 0.00 ){
        $project_cost = $row['project_total'] + $row['variation_total'];
      }else{
        $project_cost = $row['budget_estimate_total'];
      }

      $cost_focus[$row['focus_company_id']] = $cost_focus[$row['focus_company_id']] + $project_cost;


      if ( array_key_exists($row['project_estiamator_id'], $cost_estimator) ) {
        $cost_estimator[$row['project_estiamator_id']] = $cost_estimator[$row['project_estiamator_id']] + $project_cost;
      }


      if (array_key_exists($row['project_manager_id'], $cost_pm)) {
        $cost_pm[$row['project_manager_id']] = $cost_pm[$row['project_manager_id']] + $project_cost;
      }

    }


    foreach ($focus_company as $company){
      if($quoted_focus_company[$company->company_id] > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$company->company_id]).'</span> <span class=\'col-xs-6\'>$  '.number_format($cost_focus[$company->company_id],2).' <span class=\'pull-right\'>'.$quoted_focus_company[$company->company_id].'</span></span></div>';
      }
    }
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($project_manager_list as $pm ) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$quoted_pm_name[$pm->user_id].'</span> <span class=\'col-xs-6\'>$  '.number_format($cost_pm[$pm->user_id],2).' <span class=\'pull-right\'>'.$quoted_pm[$pm->user_id].'</span></span></div>';
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($estimator_list as $est ) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$quoted_estimator_name[$est->project_estiamator_id].'</span> <span class=\'col-xs-6\'>$ '.number_format($cost_estimator[$est->project_estiamator_id],2).' <span class=\'pull-right\'>'.$quoted_estimator[$est->project_estiamator_id].'</span></span></div>';
    }


    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><strong><i class="fa fa-usd"></i> '.number_format($display_cost,2).' <span class="pull-right" style=\'font-weight: normal;    font-size: 14px;\'>'.number_format($display_final).'</span></strong></p>';
  }

  public function focus_projects_by_type_widget($is_pie=''){
    $this->admin_m = new Admin_m();

    $current_date = date("d/m/Y");
    $year = date("Y");
    $current_start_year = '01/01/'.$year;

    $comp_id = 0;

    $focus_arr = array();
    $focus_prjs = array();
    $focus_costs = array();


    $focus_catgy = array();
    $focus_catgy_name = array();
    $focus_catgy_costs = array();

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $focus_prjs[$company->company_id] = 0;
      $focus_costs[$company->company_id] = 0;
    }

    $q_work = $this->dashboard_m->get_work_types();
    foreach ($q_work->getResultArray() as $job_category) {
      $cat_id =  strtolower(str_replace(" ","_",$job_category['job_category']));
      $focus_catgy[$cat_id] = 0;
      $focus_catgy_costs[$cat_id] = 0;
      $focus_catgy_name[$cat_id] = $job_category['job_category'];
    }

    $cost = 0;
    $variation = 0; 
    $grand_prj_total = 0;

    $q_projects = $this->dashboard_m->get_projects_by_work_type($current_start_year, $current_date);
    foreach ($q_projects->getResultArray() as $project){
      $cost = $cost + $project['project_total'];
      $variation = $variation + $project['variation_total'];
      $comp_id = $project['focus_company_id'];

      $focus_prjs[$comp_id]++;
      $cat_id =  strtolower(str_replace(" ","_",$project['job_category']));
      $focus_catgy[$cat_id]++;

      $focus_catgy_costs[$cat_id] = $focus_catgy_costs[$cat_id] + $project['project_total'] + $project['variation_total'];
      $focus_costs[$comp_id] = $focus_costs[$comp_id] + $project['project_total'] + $project['variation_total'];
      $grand_prj_total = $grand_prj_total +  $project['project_total'];
    }

    $total_count_cat = array_sum($focus_catgy);
    $total_cost_cat = array_sum($focus_catgy_costs);

    foreach ($focus_catgy_name as $cat_id => $value){
      $cost = $focus_catgy_costs[$cat_id];
      $count = $focus_catgy[$cat_id];

      $total_cost_cat = ($total_cost_cat <= 1 ? 1 : $total_cost_cat);
      $cost = ($cost <= 1 ? 1 : $cost);

      $percent = round(100/($total_cost_cat/$cost),1);


      $total_cost_cat = ($total_cost_cat == 1 ? 0 : $total_cost_cat);
      $cost = ($cost == 1 ? 0 : $cost);

      
      if($total_cost_cat == 0 && $cost == 0){
        $percent = 0;
      }

      if($is_pie != ''){
        echo "['".str_replace("'","&apos;",$value)."',".$cost."],";
      }else{
        echo '<div id="" class="clearfix"><p><span class="col-sm-7"><i class="fa fa-chevron-circle-right"></i> &nbsp; '.$value.'</span><strong class="col-sm-5">$ '.number_format($cost).'</strong></p></div>';
        echo '<div class="col-md-12"><hr class="block m-bottom-5 m-top-5"></div>';
      }

    }
  }


  public function pm_sales_widget($termo_val=''){
    $pm_data = $this->dashboard_m->fetch_project_pm_nomore();
    $getResultArray = $pm_data->getResultArray();
    $pm_q = array_shift($getResultArray);
    $not_pm_arr = explode(',',$pm_q['user_id'] );

    $grand_total_sales_cmp = 0;
    $grand_total_uninv_cmp = 0;
    $grand_total_over_cmp = 0;

    $c_year = date("Y");    
    $date_a = "01/01/$c_year";
    $date_b = date("d/m/Y");

    $wip_pm_total = array();

    $overall_total_sales = 0;
    $sales_result = array();
    $focus_pms = array();
    $focus_pm_pic = array();
    $focus_pm_comp = array();

    $set_invoiced_amount = array();

    $total_invoiced_init = 0;
    $total_string = '';

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y"));
    $project_manager_list = $project_manager->getResult();

    foreach ($project_manager_list as $pm ) {
      $set_invoiced_amount[$pm->user_id] = 0;
      $sales_result[$pm->user_id] = 0;
      $focus_pms[$pm->user_id] = $pm->user_first_name;
      $focus_pm_pic[$pm->user_id] = $pm->user_profile_photo;
      $focus_pm_comp[$pm->user_id] = $pm->user_focus_company_id;
    }

    $forecast_focus_total = 0;

    foreach ($project_manager_list as $pm ) {
      $total_sales = 0;
      $total_outstanding = 0;

      $q_pm_sales = $this->dashboard_m->dash_total_pm_sales($pm->user_id,$c_year,'',$date_a,$date_b);

      if($q_pm_sales->getNumRows() >= 1){

        $pm_sales = $q_pm_sales->getResultArray();

        foreach ($pm_sales as $sales => $value){
          if($value['label'] == 'VR'){
            $project_total_percent = $value['variation_total'];
          }else{
            $project_total_percent = $value['project_total'] * ($value['progress_percent']/100);
          }
          $total_sales = $total_sales + $project_total_percent;
        }

      }else{
        $total_sales = $total_sales + 0;
      }

      $set_invoiced_amount[$pm->user_id] = $set_invoiced_amount[$pm->user_id] + $total_sales;
      $overall_total_sales = $total_sales;// + $total_outstanding;
      $q_current_forecast_comp = $this->dashboard_m->get_current_forecast($c_year,$pm->user_focus_company_id,'1');
      $getResultArray = $q_current_forecast_comp->getResultArray();
      $comp_forecast = array_shift($getResultArray);

      if($pm->user_id == 29){

        $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$pm->user_id,'',1);
        $getResultArray = $q_current_forecast->getResultArray();
        $pm_forecast = array_shift($getResultArray);
        $set_total = $comp_forecast['total'] ?? 0;
        $set_forecast_percent = $pm_forecast['forecast_percent'] ?? 0;

        $total_forecast = $set_total * ($set_forecast_percent/100);
      }else{
        $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$pm->user_id);
        $getResultArray = $q_current_forecast->getResultArray();
        $pm_forecast = array_shift($getResultArray);
        $set_total = $pm_forecast['total'] ?? 0;
        $set_forecast_percent = $pm_forecast['forecast_percent'] ?? 0;
        $set_comp_pct = $pm_forecast['comp_pct'] ?? 0;

        $total_forecast = ( $set_total * (  $set_comp_pct /100  ) *  ($set_forecast_percent/100) );

      }

      $grand_total_sales_cmp = $grand_total_sales_cmp + $total_sales;
      $grand_total_uninv_cmp = $grand_total_uninv_cmp + $total_outstanding;
      $grand_total_over_cmp = $grand_total_over_cmp + $overall_total_sales;

    }

    $n_year =  date("Y")+1;
    $set_new_date = '01/01/'.$n_year;

    foreach ($project_manager_list as $pm ) {
      $wip_amount = $this->get_wip_value_permonth($date_a,$set_new_date,$pm->user_id,1);
      $wip_pm_total[$pm->user_id] = $wip_amount;
      $sales_result[$pm->user_id] = /*(( $set_estimates[$pm->user_id] + $set_quotes[$pm->user_id] ) - $set_invoiced[$pm->user_id] ) +*/ $wip_amount +  $set_invoiced_amount[$pm->user_id];
    }

    arsort($sales_result);
    $total_wip = array_sum($wip_pm_total);

    $total_invoiced = array_sum($set_invoiced_amount);
    
    if($termo_val == ''):
      echo "<div style=\"overflow-y: auto; padding-right: 5px; height: 400px;\">";
    endif;

    foreach ($sales_result as $pm_id => $sales){
      $comp_id_pm = $focus_pm_comp[$pm_id];
      if($pm_id == '29'){

        $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$pm_id,$comp_id_pm,$pm_id);
        $getResultArray = $q_current_forecast->getResultArray();
        $pm_forecast = array_shift($getResultArray);
        $total_forecast = $comp_forecast['total'] * ($pm_forecast['mns_fct_b']/100);

      }else{
        $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$pm_id);
        $getResultArray = $q_current_forecast->getResultArray();
        $pm_forecast = array_shift($getResultArray);
        $total_forecast = ( $pm_forecast['total'] * (  $pm_forecast['comp_pct']  /100  ) *  ($pm_forecast['forecast_percent']/100) );
      }

      if($sales > 0 && $total_forecast > 0 ){
        $status_forecast = round(100/($total_forecast/$sales));
      }else{
        $status_forecast = 0;
      }

      if($termo_val == ''):
        echo '<div class="m-bottom-15 clearfix"><div class="pull-left m-right-10"  style="height: 50px; width:50px; border-radius:50px; overflow:hidden; border: 1px solid #999999;"><img class="user_avatar img-responsive img-rounded" src="'.site_url().'/uploads/users/'.$focus_pm_pic[$pm_id].'"" /></div>';
        echo '<div class="" id=""><p><strong>'.$focus_pms[$pm_id].'</strong><span class="pull-right"><span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed"><i class="fa fa-usd"></i> '.number_format($set_invoiced_amount[$pm_id]).'</span> <br /> <span class="label pull-right m-bottom-3 small_green_fixed"><i class="fa fa-exclamation-triangle"></i> '.number_format($wip_pm_total[$pm_id]).'</span></span></p>';
        echo '<p><i class="fa fa-usd"></i> '.number_format($sales).'</p>';

        echo '<div class="progress no-m m-top-3 clearfix tooltip-enabled" title="" data-original-title="'.$status_forecast.'% - $'.number_format($sales).' / $'.number_format($total_forecast).'   " style="height: 7px;">
        <div class="progress-bar progress-bar-danger" style="width:'.$status_forecast.'%; background:red;"></div></div></div></div>';

        echo "<div class='clearfix'></div>";
      endif;

      $forecast_focus_total = $forecast_focus_total + $total_forecast;
    }

    if($termo_val == ''):
      echo "</div>";
    endif;

    $total_wip = ($total_wip <= 1 ? 1 : $total_wip);
    $forecast_focus_total = ($forecast_focus_total <= 1 ? 1 : $comp_forecast['total']);

    $forecast_focus_total = ($forecast_focus_total <= 1 ? 1 : $forecast_focus_total);
    $status_forecast = round(100/($forecast_focus_total/ ($total_wip + $total_invoiced) ));

    if($termo_val == ''):
      echo '<div class="clearfix" style="padding-top: 6px;    border-top: 1px solid #eee;"><i class="fa fa-briefcase" style="font-size: 42px;float: left;margin-left: 7px;margin-right: 10px;"></i>';
      echo '<div class="" id=""><p><strong>Overall Focus</strong><span class="pull-right"><span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed"><i class="fa fa-usd"></i> '.number_format($grand_total_sales_cmp).'</span> <br /> <span class="label pull-right m-bottom-3 small_green_fixed"><i class="fa fa-exclamation-triangle"></i> '.number_format($total_wip).'</span></span></p>';
      echo '<p><i class="fa fa-usd"></i> '.number_format( ($total_wip + $total_invoiced) ).' <strong class="pull-right m-right-10"></strong></p> </p>';
      echo '<div class="progress no-m m-top-3 clearfix tooltip-enabled" title="" data-original-title="'.$status_forecast.'% - $'.number_format( ($total_wip + $total_invoiced) ).' / $'.number_format($forecast_focus_total).'   " style="height: 7px;">
      <div class="progress-bar progress-bar-danger" style="width:'.$status_forecast.'%; background:red;"></div></div></div></div>';
      echo "<div class='clearfix'></div>";
    else:
      echo $status_forecast.'_'.number_format( ($total_wip + $total_invoiced) );
    endif;

    return $status_forecast.'_'.number_format( ($total_wip + $total_invoiced) );

  }


  public function focus_projects_count_widget($comp_id_set=''){
    $this->admin_m = new Admin_m();
    $this->invoice = new Invoice();

    $current_date = date("d/m/Y");
    $year = date("Y");
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);
    $custom = '';

    if(isset($comp_id_set) && $comp_id_set != ''){
      $custom = " AND `company_details`.`company_id` = '".$comp_id_set."' ";
    } 

    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();

    $focus_arr = array();
    $focus_invoiced = array();
    $focus_invoiced_old = array();
    $focus_comp_wip_count = array();
    $focus_comp_wip_count[0] = 0;


    $last_year = intval(date("Y"))-1;
    $n_month = date("m");
    $n_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$n_day/$n_month/$last_year";

    foreach ($focus_company as $company) {
      $focus_arr[$company->company_id] = $company->company_name;
      $focus_comp_wip_count[$company->company_id] = 0;

      $invoiced = 0;
      $invoiced_old = 0;

      $projects_qa = $this->dashboard_m->get_wip_invoiced_projects($current_start_year, $next_year_date, $company->company_id);
      $projects_ra = $projects_qa->getResultArray();

      foreach ($projects_ra as $result) {
        if($this->invoice->if_invoiced_all($result['project_id'])  && $this->invoice->if_has_invoice($result['project_id']) > 0 ){
          $invoiced++;
        }
      }

      $projects_qb = $this->dashboard_m->get_wip_invoiced_projects($date_a_last, $date_b_last, $company->company_id);
      $projects_rb = $projects_qb->getResultArray();

      foreach ($projects_rb as $result) {
        if($this->invoice->if_invoiced_all($result['project_id'])  && $this->invoice->if_has_invoice($result['project_id']) > 0 ){
          $invoiced_old++;
        }
      }

      $focus_invoiced[$company->company_id] = $invoiced;
      $focus_invoiced_old[$company->company_id] = $invoiced_old;
    }

    $display_inv = array_sum($focus_invoiced);

    $total_string_wip = '';
    $total_string_inv = '';

    $total_string_wip .= '('.$year.') WIP Count'; 
    $total_string_inv .= '('.$year.') Invoiced Count';

    foreach ($focus_arr as $comp_id => $value ){
      if($focus_invoiced[$comp_id] > 0){
        $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-7\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-5\'>'.number_format($focus_invoiced[$comp_id]).'</span></div>';
      }
    }

    $lat_old_year = $year-1;
    $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$lat_old_year.')</div>';

    $q_maps = $this->dashboard_m->get_wip_count();

    foreach ($q_maps->getResultArray() as $row){

      $comp_id = $row['focus_company_id'];

      if(isset($comp_id_set) && $comp_id_set != ''){
        if($comp_id_set == $comp_id  ){
          $focus_comp_wip_count[$comp_id_set]++;
        }
      }

      else{
        $focus_comp_wip_count[$comp_id]++;
      }
    }

    foreach ($focus_arr as $comp_id => $value ){
      if($focus_comp_wip_count[$comp_id] > 0){
        $total_string_wip .= '<div class=\'row\'><span class=\'col-xs-7\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-5\'>'.number_format($focus_comp_wip_count[$comp_id]).'</span></div>';
      }

      if($focus_invoiced[$comp_id] > 0){
        $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-7\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-5\'>'.number_format($focus_invoiced_old[$comp_id]).'</span></div>';
      }
    }

    $display_wip = array_sum($focus_comp_wip_count);

    echo '<div id="" class="clearfix row">        
    <strong class="text-center col-xs-6"><p class="h5x value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string_inv.'"><i class="fa fa-list-alt"></i> &nbsp;'.$display_inv.'</p></strong>
    <strong class="text-center col-xs-6"><p class="h5x value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string_wip.'"><i class="fa fa-tasks"></i> &nbsp;'.$display_wip.'</p></strong>
    </div>';
  }

  public function maintanance_average(){
    $this->admin_m = new Admin_m();

    $days_dif = array('');
    $days_dif_old = array('');

    $year = date("Y");
    $current_date = '01/01/'.intval($year+1);
    $current_start_year = '01/01/'.$year;

    $date_b = date("d/m/Y");

    $focus_arr = array();
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
    }

    $q_maintenance = $this->dashboard_m->get_maitenance_dates($current_start_year,$date_b);
    $maintenance_details  = $q_maintenance->getResult();

    foreach ($maintenance_details as $maintenance) {
      array_push($days_dif, $maintenance->total_days);

      if($maintenance->total_days == 58){
      //  var_dump($maintenance);
      }
    }

    $size = count($days_dif);
    $size = ($size <= 1 ? 1 : $size);
    $average = (array_sum($days_dif) / $size) + 0.0000;

    arsort($days_dif,1);
    $long_day =  max($days_dif);
    $short_day_day =  min($days_dif);

    $short_day_day = 1; // 1 actual

    $last_year = intval(date("Y"))-1;
    $n_month = date("m");
    $n_day = date("d");

    $date_a_last = "01/01/$last_year"; 
    $date_b_last = "$n_day/$n_month/$last_year";

    $q_maintenance = $this->dashboard_m->get_maitenance_dates($date_a_last,$date_b_last);
    $maintenance_details  = $q_maintenance->getResult();

    foreach ($maintenance_details as $maintenance) {
      array_push($days_dif_old, $maintenance->total_days);
    }

    $size_old = count($days_dif_old);
    if($size_old > 0){
      $average_old = ( array_sum($days_dif_old) / $size_old ) + 0.00000;
      arsort($days_dif_old,1);
      $long_day_old =  max($days_dif_old);
      $short_day_day_old =  min($days_dif_old);

    }else{
      $average_old = ' No Data Yet';
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title=" ('.$last_year.') &nbsp; '.number_format($average_old,2).'  &nbsp; ['.$short_day_day_old.' - '.$long_day_old.'] ">'.number_format($average,2).' Days';
    echo '<span class="pull-right">'.$short_day_day.'  <i class="fa fa-arrows-h" aria-hidden="true"></i> '.$long_day.'</span></p>';
  }

  public function focus_get_po_widget($comp_id_set=''){ 
    $this->admin_m = new Admin_m();
    $this->purchase_order_m = new Purchase_order_m();

    $year = date("Y");
    $current_date = date("d/m/Y");
    $current_start_year = '01/01/2014';
    $set_cpo = array();
    $custom = '';
    $custom_a = '';

    if(isset($comp_id_set) && $comp_id_set != ''){
      $custom = " AND `company_details`.`company_id` = '".$comp_id_set."' ";
      $custom_a = " AND `project`.`focus_company_id` = '".$comp_id_set."' ";
    }

    $set_date_a = '01/01/'.$year;

    $focus_arr = array();
    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $set_cpo[$company->company_id] = 0;
    }
    $total_string = "($year)";

    $po_list_ordered = $this->purchase_order_m->get_po_list_order_by_project($set_date_a,$current_date);
    foreach ($po_list_ordered->getResultArray() as $row){
      $work_id = $row['works_id'];

      $po_tot_inv_q = $this->purchase_order_m->get_po_total_paid($work_id);
      $invoiced = 0;
      foreach ($po_tot_inv_q->getResultArray() as $po_tot_row){
        $invoiced = $po_tot_row['total_paid'];
      }

      $out_standing = $row['price'] - $invoiced;

      $comp_id = $row['focus_company_id'];
      if(isset($set_cpo[$comp_id])){
        $set_cpo[$comp_id] = $set_cpo[$comp_id] + $out_standing;
      }
    }

    $display_total = array_sum($set_cpo);
    foreach ($focus_arr as $comp_id => $value ){
      $display_total_cpo = $set_cpo[$comp_id];
      if($display_total_cpo > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cpo,2).'</span></div>';
      }
      $set_cpo[$comp_id] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "31/12/$last_year";
    $set_date_b = '01/01/'.$last_year;

    $po_list_ordered = $this->purchase_order_m->get_po_list_order_by_project($set_date_b,$date_last_year_today,$custom_a);
    foreach ($po_list_ordered->getResultArray() as $row){
      $work_id = $row['works_id'];

      $po_tot_inv_q = $this->purchase_order_m->get_po_total_paid($work_id);
      $invoiced = 0;
      foreach ($po_tot_inv_q->getResultArray() as $po_tot_row){
        $invoiced = $po_tot_row['total_paid'];
      }

      $out_standing = $row['price'] - $invoiced;
      $comp_id = $row['focus_company_id'];

      if( in_array($comp_id, $set_cpo)  ){
        $set_cpo[$comp_id] = $set_cpo[$comp_id] + $out_standing;
      }
    }
    
    foreach ($focus_arr as $comp_id => $value ){
      if( in_array($comp_id, $set_cpo)  ){
        $display_total_cpo = $set_cpo[$comp_id];
        if($display_total_cpo > 0){
          $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cpo,2).'</span></div>';
        }
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($display_total,2).'</strong></p>';
  }



  public function average_date_invoice($company_id='',$year_set='',$financial_view=''){
    $this->admin_m = new Admin_m();

    if(isset($year_set) && $year_set != ''){
      $c_year = $year_set;
    }else{
      $c_year = date("Y");
    }

    $date_a = "01/01/$c_year";
    $date_b = date("d/m")."/".$c_year;

    if(isset($financial_view) && $financial_view!=''){
      $date_a = "01/07/".($c_year-1);
      $date_b = "30/06/".$c_year;
    }

    $custom = '';
    $custom_uq = '';


    if(isset($company_id) && $company_id !=''){
      $custom = " AND `company_details`.`company_id` = '$company_id' ";
      $custom_uq = " AND `users`.`user_focus_company_id` = '$company_id'  ";
    }


    // $date_a = "01/01/2016";
    // $date_b = "29/01/2016";
    $days_dif = array('');

    $q_ave = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$company_id); //1
    $days_result = $q_ave->getResult();

    foreach ($days_result as $result){
      $diff = ($result->days_diff < 0 ? 0 : $result->days_diff); 
      array_push($days_dif, $diff);
    }

    $size = count($days_dif);

    $size = ($size <= 1 ? 1 : $size);
    $average = array_sum($days_dif) / $size;

    arsort($days_dif,1);

    $long_day =  max($days_dif);
    $short_day_day =  min($days_dif);
    $short_day_day = 1;

    $total_string = '';

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y"),0,$custom_uq); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pms_averg = array();
    $pms_w_avg = array();
    foreach ($project_manager_list as $pm ) {
      $pms_averg[$pm->user_id] = array();
      $pms_w_avg[$pm->user_id] = $pm->user_id;
    }

    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){

      if($company->company_id != 4){

        $days_dif_comp = array('');

        $q_ave = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$company->company_id); //5
        $days_result = $q_ave->getResult();

        foreach ($days_result as $result){
          if($result->project_manager_id != 9){

            $diff = ($result->days_diff < 0 ? 0 : $result->days_diff); 
            array_push($days_dif_comp,$diff);
          }
        }

        $size_comp = count($days_dif_comp);
        $average_comp = array_sum($days_dif_comp) / $size_comp;

        arsort($days_dif_comp,1);

        $days_dif_comp = ($days_dif_comp <= 1 ? 1 : $days_dif_comp);
        $long_day_comp =  max($days_dif_comp);
        $short_day_day_comp =  min($days_dif_comp);
        $short_day_day_comp = 1;


        $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.str_replace("Pty Ltd","",$company->company_name).'</span><span class=\'col-xs-4\'>'.round($average_comp,1).'</span></div>';

        
        foreach ($project_manager_list as $pm ) {
          $q_ave_pm = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$pm->user_id,$company->company_id); //4
          $days_result_pm = $q_ave_pm->getResult();

          foreach ($days_result_pm as $result_pm){
            $diff = ($result_pm->days_diff < 0 ? 0 : $result_pm->days_diff); 
            array_push($pms_averg[$pm->user_id],$diff);
          }
        }
      }
    }


    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    foreach ($project_manager_list as $pm ){
      if( count($pms_averg[$pm->user_id]) > 0  ){
        $size = count($pms_averg[$pm->user_id]);

        $pm_average = array_sum($pms_averg[$pm->user_id]) / $size;
        arsort($pms_averg[$pm->user_id],1);
        $pm_long_day =  max($pms_averg[$pm->user_id]);
        $pm_short_day_day =  min($pms_averg[$pm->user_id]);
        $pm_short_day_day = 1;
        $pm_name = $pm->user_first_name;
        $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.$pm_name.'</span><span class=\'col-xs-4\'>'.round($pm_average,1).'</span></div>';
      }
    }


    $last_year = intval(date("Y")) - 1;
    $this_month = date("m");
    $this_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "31/12/$last_year";
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';


    $pms_averg_old = array();  
    foreach ($project_manager_list as $pm ) {
      $pms_averg_old[$pm->user_id] = array();      
    }

    foreach ($focus_company as $company){
      if($company->company_id != 4){
        $days_dif_comp = array('');
        $q_ave = $this->dashboard_m->get_maitenance_dates_pm($date_a_last,$date_b_last,$company->company_id); //5
        $days_result = $q_ave->getResult();

        foreach ($days_result as $result){
          if($result->project_manager_id != 9){
            $diff = ($result->days_diff < 0 ? 0 : $result->days_diff); 
            array_push($days_dif_comp,$diff);
          }
        }

        $size_comp = count($days_dif_comp);
        $average_comp = array_sum($days_dif_comp) / $size_comp;

        arsort($days_dif_comp,1);

        $days_dif_comp = ($days_dif_comp <= 1 ? 1 : $days_dif_comp);
        $long_day_comp =  max($days_dif_comp);
        $short_day_day_comp =  min($days_dif_comp);
        $short_day_day_comp = 1;

        $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.str_replace("Pty Ltd","",$company->company_name).'</span><span class=\'col-xs-4\'>'.round($average_comp,1).'</span></div>';

        foreach ($project_manager_list as $pm ) {
          $q_ave_pm = $this->dashboard_m->get_maitenance_dates_pm($date_a_last,$date_b_last,$pm->user_id,$company->company_id); //4
          $days_result_pm = $q_ave_pm->getResult();

          foreach ($days_result_pm as $result_pm){
            $diff = ($result_pm->days_diff < 0 ? 0 : $result_pm->days_diff); 
            array_push($pms_averg_old[$pm->user_id],$diff);
          }
        }
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    foreach ($project_manager_list as $pm ){
      if(array_key_exists($pm->user_id,$pms_averg_old)){
        $size = count($pms_averg_old[$pm->user_id]);
        if($size > 0){ 
          $pm_average = array_sum($pms_averg_old[$pm->user_id]) / $size;
          arsort($pms_averg_old[$pm->user_id],1);
          $pm_long_day =  max($pms_averg_old[$pm->user_id]);
          $pm_short_day_day =  min($pms_averg_old[$pm->user_id]);
          $pm_short_day_day = 1;
          $pm_name = $pm->user_first_name;

          $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.$pm_name.'</span><span class=\'col-xs-4\'>'.round($pm_average,1).'</span></div>';
        }
      }
    }

    echo '<div id="" class="pad-10"><input class="knob avg_fid" data-width="100%" data-step=".1"  data-thickness=".13" value="'.number_format($average,1).'" readonly data-fgColor="#964dd7" data-angleOffset="-180"  data-max="'.$long_day.'"></div>';
    echo '<div id="" style="position: absolute; width: 100%; bottom: 36px; left: 0px;" class="clearfix xxxx m-top-10"><div id="" style="width:50%; float:left;" class=" min_box_area"><strong><p>MIN: '.$short_day_day.' </p></strong></div><div id="" style="width:50%; float:left;" class=" max_box_area"><strong><p>MAX: '.$long_day.'</p></strong></div></div>';
    echo '<div class="yr_st_val" style="background-color: #c586fd; margin: -2px; padding: 5px 10px; font-size: 25px; border-top: 1px solid #8838d0; border-bottom: 1px solid #a678d0; font-weight: bold; margin-bottom: 10px; position: absolute; width: 100%; left: 2px; top: 0;"> <span class=""><i class="fa fa-calendar "></i> '.date('Y').' </span> <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'."></i></span></div>';

  }


  public function wid_site_labour_hrs($comp_id_set=''){
    $this->admin_m = new Admin_m();
    $current_date = date("d/m/Y");
    $total_string = '';
    $display_total = 0;
    $custom = '';
    $custom_a = '';

    $current_year = date("Y");
    $total_string .= '<div class=\'row\'>&nbsp; ('.$current_year.')</div>';

    if(isset($comp_id_set) && $comp_id_set != ''){
      $custom = " AND `company_details`.`company_id` = '".$comp_id_set."' ";
      $custom_a = " AND `project`.`focus_company_id` = '".$comp_id_set."' ";
    }

    $q_exluded_cats = $this->dashboard_m->get_current_exluded_prjCat();
    $getResultArray = $q_exluded_cats->getResultArray();
    $exc_cats = array_shift($getResultArray);
    $exc_cats_arr = explode(',', $exc_cats['labour_sched_categories'] );
    $exc_cats_arr =  array_filter($exc_cats_arr);
    $exlude_list = '';

    foreach ($exc_cats_arr as $key => $value) {
      $exlude_list .= "'$value',";
    }

    $exlude_list = rtrim($exlude_list, ", ");

    $endYearDate = '31/12/'.date('Y');
 



    $all_focus_company = $this->admin_m->fetch_all_company_focus($custom);
    $focus_company = $all_focus_company->getResult();
    $focus_company_hrs = array();
    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $focus_company_hrs[$company->company_id] = 0;
    }


    $focus_arr[$company->company_id] = $company->company_name;
    $focus_company_hrs['3'] = 0;


    $states = $this->dashboard_m->get_all_states();
    $states_list = $states->getResult();
    $states_name = array();
    $hrs_states = array();
    foreach ($states_list as $sts ) {
      $hrs_states[$sts->id] = 0;
      $states_name[$sts->id] = $sts->shortname;
    }

    $days_q = $this->dashboard_m->get_site_labour_hrs($current_date,$endYearDate,$custom_a,$exlude_list);
    foreach ($days_q->getResultArray() as $labor_hrs) {

      $comp_id = $labor_hrs['focus_company_id'];
      $state_id = $labor_hrs['state_id'];


      $focus_company_hrs[$comp_id] = $focus_company_hrs[$comp_id] + $labor_hrs['site_hours'];
 

      //$total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>'.number_format($labor_hrs['time'],2).'</span></div>';
      $display_total = $display_total + $labor_hrs['site_hours'];
      $hrs_states[$state_id] = $hrs_states[$state_id] + $labor_hrs['site_hours']; 
    }

    foreach ($focus_company as $company){
      if($focus_company_hrs[$company->company_id] > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$company->company_id]).'</span> <span class=\'col-xs-6 text-right\'>'.number_format($focus_company_hrs[$company->company_id],2).'</span></div>';
        $focus_company_hrs[$company->company_id] = 0;
      }
    }


    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($states_list as $sts){
      if($hrs_states[$sts->id] > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$states_name[$sts->id].'</span> <span class=\'col-xs-6 text-right\'>'.number_format($hrs_states[$sts->id],2).'</span></div>';
        $hrs_states[$sts->id] = 0;
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><strong>'.number_format($display_total,2).'</strong></p>';

  }



  public function wip_widget(){
    $this->admin_m = new Admin_m();

    $total_string = '';
    $focus_arr = array();
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
    }

    $start_date = "01/01/".date("Y");
    $n_year =  date("Y")+1;
    $set_new_date = '01/01/'.$n_year;

    $display_total = 0;


    foreach ($focus_arr as $comp_id => $value ){

      $wip_amount = $this->get_wip_value_permonth($start_date,$set_new_date,$comp_id);

      if($wip_amount > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($wip_amount,2).'</span></div>';
        $display_total = $display_total + $wip_amount;
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($display_total,2).'</strong></p>';

  }



  function get_wip_personal($date_a,$date_b,$pm_id,$comp){
    $q_wip_vales = $this->dashboard_m->get_personal_wip($date_a,$date_b,$pm_id, $comp);
    $wip_values = $q_wip_vales->getResult();

    $amount = 0;
    $total = 0;
    $count = 0;

    foreach ($wip_values as $prj_wip){

      if($prj_wip->label == 'VR' ){
        $amount = $prj_wip->variation_total;
      }else{
        if($prj_wip->install_time_hrs > 0 || $prj_wip->work_estimated_total > 0.00   ){
          $amount = $prj_wip->project_total * ($prj_wip->progress_percent/100);
        }else{
          $amount = $prj_wip->budget_estimate_total * ($prj_wip->progress_percent/100);
        }
      }

      $total = $total + $amount;
      $count++;

    }

    return $total;
  }






  public function ihs($type){


    if($type == 'get_recent_lost_time_days'){

      $q_get_recent_losttime_count = $this->dashboard_m->get_recent_losttime_count();
      $getResultArray = $q_get_recent_losttime_count->getResultArray();
      $ltd = array_shift($getResultArray);
      $date_reported_r = explode(' ', $ltd['date_reported']);
      $date_reported = $date_reported_r[0];

      $dStart = (new \CodeIgniter\I18n\Time($date_reported));
      $dEnd = (new \CodeIgniter\I18n\Time(date("Y-m-d")));
      $dDiff = $dStart->difference($dEnd);
      return $dDiff->days;
    }

    if($type == 'get_incident_count'){
      $q_get_incident_count = $this->dashboard_m->get_incident_count();
      $getResultArray = $q_get_incident_count->getResultArray();
      $incident_count = array_shift($getResultArray);
      return $incident_count['incident_count'];
    }

    if($type == 'get_count_inducted'){
      $q_get_count_inducted = $this->dashboard_m->get_count_inducted();
      $getResultArray = $q_get_count_inducted->getResultArray();
      $individuals_inducted = array_shift($getResultArray);
      return $individuals_inducted['indv_inducted'];
    }

    if($type == 'get_company_inducted_count'){
      $companies_inducted = $this->dashboard_m->get_company_inducted_count();
      return $companies_inducted;
    }
  }

  public function pm_estimates_widget($es_id = ''){
    $this->admin_m = new Admin_m();

    $year = date("Y");
    $current_date = date("d/m/Y");
    $current_start_year = '01/01/'.$year;

    $total_string = '';
    $is_restricted = 0;

    $estimator_value_counter = array();


    $total_string .= '<div class=\'row\'>&nbsp; ('.$year.')</div>';

    $admin_defaults = $this->admin_m->fetch_admin_defaults();
    foreach ($admin_defaults->getResult() as $row){
      $unaccepted_date_categories = $row->unaccepted_date_categories;
      $unaccepted_no_days = $row->unaccepted_no_days;
    }


    $pm_data = $this->dashboard_m->fetch_project_pm_nomore();
    $getResultArray = $pm_data->getResultArray();
    $pm_q = array_shift($getResultArray);
    $not_pm_arr = explode(',',$pm_q['user_id'] );


    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_split = array();

    foreach ($project_manager_list as $pm ) {
      $pm_split[$pm->user_id] = 0;
    }


    $amnt = 0;

    //lists estimators not PM sorry
    $unaccepted_amount = array();
    $estimator = $this->dashboard_m->fetch_project_estimators();
    $estimator_list = $estimator->getResult();

    foreach ($estimator_list as $est ) {
      $unaccepted_amount[$est->project_estiamator_id] = 0;
      $estimator_value_counter[$est->project_estiamator_id] = 0;
    }

    
    if($es_id != ''){
      $unaccepted_amount[$es_id] = 0;
      $estimator_value_counter[$es_id] = 0;
    }

    $exemp_cat = explode(',', $unaccepted_date_categories);


    $focus_arr = array();
    $project_cost = array();
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $project_cost[$company->company_id] = 0;
    }

    $q_projects = $this->dashboard_m->get_unaccepted_projects($current_start_year,$current_date);
    $projects = $q_projects->getResult();
    foreach ($projects as $un_accepted){

      if( !in_array($un_accepted->project_manager_id, $not_pm_arr) ){

        $unaccepted_date = $un_accepted->unaccepted_date;
        if($unaccepted_date !== ""){
          $unaccepted_date_arr = explode('/',$unaccepted_date);
          $u_date_day = $unaccepted_date_arr[0];
          $u_date_month = $unaccepted_date_arr[1];
          $u_date_year = $unaccepted_date_arr[2];
          $unaccepted_date = $u_date_year.'-'.$u_date_month.'-'.$u_date_day;
        }

        $start_date = $un_accepted->date_site_commencement;
        if($start_date !== ""){
          $start_date_arr = explode('/',$start_date);
          $s_date_day = $start_date_arr[0];
          $s_date_month = $start_date_arr[1];
          $s_date_year = $start_date_arr[2];
          $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;
        } 



        if( in_array($un_accepted->job_category, $exemp_cat)  ){
          $is_restricted = 1;
        }else{
          $is_restricted = 0;
        }

        $today = date('Y-m-d');
        $unaccepteddate = strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
        $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

        if(strtotime($unaccepteddate) < strtotime($today)){
          if($is_restricted == 1){
            if($unaccepted_date == ""){
              $status = 'quote';
            }else{
              $status = 'unset';
            }
          }else{
            $status = 'unset';
          }

        }else{
          if($unaccepted_date == ""){
            $status = 'quote';
          }else{
            $status = 'unset';
          }

        }

        if ($status == 'unset'){
          if($un_accepted->install_time_hrs > 0 || $un_accepted->work_estimated_total > 0.00 || $un_accepted->variation_total > 0.00 ){
            $amnt =  $un_accepted->project_total + $un_accepted->variation_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt;
          }else{
            $amnt = $un_accepted->budget_estimate_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt; 
          }


          if( isset($pm_split[$un_accepted->project_manager_id])) {
            $pm_split[$un_accepted->project_manager_id] = $pm_split[$un_accepted->project_manager_id] + $amnt;
          }
          
          if( isset($unaccepted_amount[$un_accepted->project_estiamator_id])) {
            $unaccepted_amount[$un_accepted->project_estiamator_id] = $unaccepted_amount[$un_accepted->project_estiamator_id] + $amnt;
            $estimator_value_counter[$un_accepted->project_estiamator_id]++;
          }




        }
      }
    }

    //var_dump($project_cost);

    $display_total = array_sum($project_cost); 
    foreach ($focus_arr as $comp_id => $value ){
      $display_total_cmp = $project_cost[$comp_id];
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
      $project_cost[$comp_id] = 0;
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';



    if($es_id != ''){
      $display_total = $unaccepted_amount[$es_id];
      $display_counter = $estimator_value_counter[$es_id];
    }else{
      $display_counter = '';
    }

    foreach ($project_manager_list as $pm ) {
      $display_total_cmp = $pm_split[$pm->user_id];
      $pm_name = $pm->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
      $pm_split[$pm->user_id] = 0;
    }


    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';






    foreach ($estimator_list as $est ) {
      $display_total_cmp = $unaccepted_amount[$est->project_estiamator_id];
      $pm_name = $est->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
      $unaccepted_amount[$est->project_estiamator_id] = 0;
    }


    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";


    $q_projects = $this->dashboard_m->get_unaccepted_projects("01/01/$last_year",$date_last_year_today);
    $projects = $q_projects->getResult();
    foreach ($projects as $un_accepted){

      if( !in_array($un_accepted->project_manager_id, $not_pm_arr) ){

        $unaccepted_date = $un_accepted->unaccepted_date;
        if($unaccepted_date !== ""){
          $unaccepted_date_arr = explode('/',$unaccepted_date);
          $u_date_day = $unaccepted_date_arr[0];
          $u_date_month = $unaccepted_date_arr[1];
          $u_date_year = $unaccepted_date_arr[2];
          $unaccepted_date = $u_date_year.'-'.$u_date_month.'-'.$u_date_day;
        }

        $start_date = $un_accepted->date_site_commencement;
        if($start_date !== ""){
          $start_date_arr = explode('/',$start_date);
          $s_date_day = $start_date_arr[0];
          $s_date_month = $start_date_arr[1];
          $s_date_year = $start_date_arr[2];
          $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;
        } 



        if( in_array($un_accepted->job_category, $exemp_cat)  ){
          $is_restricted = 1;
        }else{
          $is_restricted = 0;
        }

        $today = date('Y-m-d');
        $unaccepteddate = strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
        $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

        if(strtotime($unaccepteddate) < strtotime($today)){
          if($is_restricted == 1){
            if($unaccepted_date == ""){
              $status = 'quote';
            }else{
              $status = 'unset';
            }
          }else{
            $status = 'unset';
          }

        }else{
          if($unaccepted_date == ""){
            $status = 'quote';
          }else{
            $status = 'unset';
          }

        }

        if ($status == 'unset'){
          if($un_accepted->install_time_hrs > 0 || $un_accepted->work_estimated_total > 0.00 || $un_accepted->variation_total > 0.00 ){
            $amnt =  $un_accepted->project_total + $un_accepted->variation_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt;
          }else{
            $amnt = $un_accepted->budget_estimate_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt; 
          }

          if( isset($unaccepted_amount[$un_accepted->project_estiamator_id])) {
            $unaccepted_amount[$un_accepted->project_estiamator_id] = $unaccepted_amount[$un_accepted->project_estiamator_id] + $amnt;
          }


          if( isset($pm_split[$un_accepted->project_manager_id])) {
            $pm_split[$un_accepted->project_manager_id] = $pm_split[$un_accepted->project_manager_id] + $amnt;
          }



        }
      }
    }



    foreach ($focus_arr as $comp_id => $value ){
      $display_total_cmp = $project_cost[$comp_id];
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      } 
    }


    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    foreach ($project_manager_list as $pm ) {
      $display_total_cmp = $pm_split[$pm->user_id];
      $pm_name = $pm->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      } 
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';





    foreach ($estimator_list as $est ) {
      $display_total_cmp = $unaccepted_amount[$est->project_estiamator_id];
      $pm_name = $est->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      } 
    }


    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($display_total,2).' <span class="pull-right">'.$display_counter.'</span></strong></p>';
  }


  public function focus_top_ten_clients($set_year='',$is_financial=''){

    if(isset($set_year) && $set_year!=''){
      $year = $set_year;
    }else{
      $year = date("Y");
    }

    $current_date = date("d/m")."/".$year;
    $current_start_year = '01/01/'.$year; 

    if(isset($is_financial) && $is_financial != ''){
      $current_start_year = '01/07/'.($year-1);
      $current_date = '30/06/'.$year;
    }

    $q_clients = $this->dashboard_m->get_top_ten_clients($current_start_year, $current_date);
    $client_details  = $q_clients->getResult();

    $list_total = 0;

    foreach ($client_details as $company){
      $q_vr_c_t = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id);
      $getResultArray = $q_vr_c_t->getResultArray();
      $vr_val_t = array_shift($getResultArray);
      $list_total = $list_total + round($company->grand_total + $vr_val_t['total_variation']);
    }

    $last_year = intval( $year ) - 1;
    $this_month = date("m");
    $this_day = date("d");
    $date_a_last = "01/01/$last_year";
    $date_b_last = "$this_day/$this_month/$last_year";
    $comp_total = array();
    $comp_name = array();

    foreach ($client_details as $company) {
      $q_vr_c = $this->dashboard_m->client_vr_value($current_start_year,$current_date,$company->client_id);
      $getResultArray = $q_vr_c->getResultArray();
      $vr_val = array_shift($getResultArray);
      $cost_gtotl_amnt = round($company->grand_total+ $vr_val['total_variation']);
      $comp_total[$company->company_id] = $cost_gtotl_amnt;
      $comp_name[$company->company_id] = $company->company_name_group;

    }

    arsort($comp_total);

    foreach ($comp_total as $raw_id => $compute_amount) {
      echo '<div class="col-sm-8 col-md-7"><i class="fa fa-chevron-circle-right"></i>  &nbsp; ';

      if($compute_amount > 0){
        $percent = round(100/($list_total/$compute_amount),1);
      }else{
        $percent = round(100/($list_total/1),1);
      }



      $company_name_group = $comp_name[$raw_id];

      if(strlen($company_name_group) > 30){
        echo '<span class="tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$company_name_group.'">'.substr($company_name_group,0,30).'...</span>';
      }else{
        echo $company_name_group;
      }

      $q_vr_c_u = $this->dashboard_m->client_vr_value($date_a_last,$date_b_last,$raw_id);
      $getResultArray = $q_vr_c_u->getResultArray();
      $vr_val_u = array_shift($getResultArray);

      $last_year_q = $this->dashboard_m->get_top_ten_clients($date_a_last, $date_b_last,$raw_id);
      $getResultArray = $last_year_q->getResultArray();
      $last_year_sale = array_shift($getResultArray);
      $set_grand_total = $last_year_sale['grand_total'] ?? 0;
      $set_total_variation = $vr_val_u['total_variation'] ?? 0;

      $lst_year_total = $set_grand_total + $set_total_variation;

      if($compute_amount > 0){
        echo ' </div><div class="col-md-2 col-sm-4"><strong>'.number_format($percent,1).'%</strong></div>  <div class="col-md-3 col-sm-4 tooltip-test" title="" data-placement="left" data-original-title="Last Year : $ '.number_format($lst_year_total).'"><i class="fa fa-usd"></i> '.number_format($compute_amount).'</div><div class="col-sm-12"><hr class="block m-bottom-5 m-top-5"></div>';
      }

    }
  }




  public function users_availability(){
    $this->users = new Users();
    $this->user_model = new Users_m();

    $user_head_count = 0;

    $user_un_ave = array(); 
    $time_diff = array(); 
    $current_date_time = strtotime(date("Y-m-d h:i A")); 
    $fetch_user= $this->user_model->list_user_short();
    $users = $fetch_user->getResult();

    echo '<div class="user_avaial_list_thumb">';
    foreach($users as $key => $user_init):

      if(!$this->users->if_user_is_available($user_init->primary_user_id)): 
        array_push($user_un_ave, $user_init->primary_user_id); 

        $ave_data = $this->users->fetch_user_ave_data($user_init->primary_user_id);  

        $diff_time = $ave_data['date_time_stamp_b'] - $current_date_time;
        $time_diff[$user_init->primary_user_id] = $diff_time;

      else: 

        $reoccur_ave = $this->users->get_user_reoccur_ave($user_init->primary_user_id);
        $date_range_b = $reoccur_ave['date_range_b'] ?? 0;
        $diff_time = $date_range_b - $current_date_time;

        if($diff_time > 0){
          $time_diff[$user_init->primary_user_id] = $diff_time;
        }

      endif; 
    endforeach; 

    arsort($time_diff); 

    foreach ($time_diff as $key => $value): 
      $user_head_count++;
      $user_un_ave_dta = $this->users->fetch_user_ave_data($key);
      $availability_set = $this->users->get_user_availability($key,1,1);
      $availability_cls = strtolower( str_replace(' ', '_', $availability_set) );
      $availability_notes = $this->users->get_user_ave_comments($key,1);
      echo '<div  class="tooltip-enabled pull-left m-5 avbty" title="" data-original-title="'.$availability_set.' - '.ucfirst($availability_notes).'   " data-placement="right"  ><div id="" class="av_unit av_'.$availability_cls.' "><img src="'.site_url().'uploads/users/'.$user_un_ave_dta['user_profile_photo'].'"  style="width: 100%;" class="av_img"></div>';
      $fnme = explode(' ',$user_un_ave_dta['user_first_name']);
      echo '<p>'.$fnme[0].'</p>';
      echo "</div>";

    endforeach;

    echo "</div>";
    $user_avaial_list_thumb = ( $user_head_count*60 ) + 200;

    echo '<style type="text/css">.user_avaial_list_thumb{width: '.$user_avaial_list_thumb.'px;} .tooltip{ z-index: 100000000; }</style>';

  }


  function list_projects_progress($pm_id='',$custom='',$is_joinery=0){
    $this->admin_m = new Admin_m();

    $this_year =  date("d/m/Y", strtotime("- 5 days"));

    $selected_cats = $this->admin_m->fetch_admin_defaults();
    $getResultArray = $selected_cats->getResultArray();
    $cats = array_shift($getResultArray);
    $exc_cats_arr = explode(',', $cats['cat_project_completion_calendar'] );


    $include_list = '';

    foreach ($exc_cats_arr as $key => $value) {
      $include_list .= "'$value',";
    }

    $included_list = rtrim($include_list, ",");


    if( $pm_id == '29' ){
      $included_list = '';
    }

    if($is_joinery == 1){
      $q_get_project_progress_list = $this->dashboard_m->get_joinery_list(1);
    }else{
      $q_get_project_progress_list = $this->dashboard_m->get_project_progress_list($this_year,$pm_id,$custom,$included_list);
    }


    $num_result = $q_get_project_progress_list->getNumRows();
    $progress_list = $q_get_project_progress_list->getResult();

    $use_date_stamp = 0;

    $current_day_line = date('Y/m/d');
    $date_allowance = strtotime(date("Y-m-d", strtotime("- 5 days")) ) * 1000 ;

    foreach ($progress_list as $prj ) {

      $quote_deadline_date_replaced = str_replace('/', '-', $prj->date_site_finish);
      $quote_deadline_date_reformated = date('Y/m/d', strtotime("$quote_deadline_date_replaced "));

      $quote_start_date_replaced = str_replace('/', '-', $prj->date_site_commencement);
      $quote_start_date_reformated = strtotime("$quote_start_date_replaced ");

      $quote_start_date_reformated = $quote_start_date_reformated."000";


      if($date_allowance <= $quote_start_date_reformated){
        $use_date_stamp = $quote_start_date_reformated;
      }else{
        $use_date_stamp = $date_allowance;
      }

      $date_deadline_check_val = date('N',$prj->project_unix_end_date);

      echo '{
        name: "<a href=\"'.base_url().'projects/view/'.$prj->project_id.'\" target=\"_blank\">'.$prj->project_id.'</a>", 
        dataObj: "'.$prj->pm_name.' : '.$prj->project_name.'",
        values: [
        {from: "/Date('.$use_date_stamp.')/", to: "'.$quote_deadline_date_reformated.'", "desc": "<strong>'.$prj->pm_name.'</strong> : '.$prj->project_name.'<br /><strong>'.$prj->client_name.'</strong>&nbsp;  &nbsp;  &nbsp;   &nbsp;  Brand : '.$prj->brand_name.'",customClass: "'.strtolower(str_replace(' ','',$prj->job_category)).'"},
        {from: "'.$current_day_line.'", to: "'.$current_day_line.'", "desc": "<strong>'.$prj->pm_name.'</strong> : '.$prj->project_name.'<br /><strong>'.$prj->client_name.'</strong>&nbsp;  &nbsp;  &nbsp;   &nbsp;  Brand : '.$prj->brand_name.'",customClass: "curr_date"},
        {from: "'.date('Y/m/d', strtotime('- 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 80 days')).'",  customClass: "hide"}
        ]
      },';
    }



    for($looper = $num_result; $looper < 14 ; $looper ++ ){


      echo '

      { values: [
        {from: "'.date('Y/m/d', strtotime('- 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 5 days')).'",  customClass: "hide"},
        {from: "'.date('Y/m/d').'", to: "'.date('Y/m/d').'" ,customClass: "curr_date"},
        {from: "'.date('Y/m/d', strtotime('+ 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 80 days')).'",  customClass: "hide"}
        ]
      }, 


      ';

    }


  }





  function _check_sales($c_year=2023,$c_month=2){
    $see_outstanding_mn = 0;
    $see_outstanding_pm = 0;
    $init_invoied_amount = 0;

    $currentYear = $c_year;

    $n_month = $c_month+1;
    $n_year = $c_year;

    if($c_month == 12){
      $n_month = 1;
      $n_year = $c_year+1;
    }

    $date_a = "01/$c_month/$c_year";
    $date_b = "01/$n_month/$n_year";

    $mons = array(1 => "jan", 2 => "feb", 3 => "mar", 4 => "apr", 5 => "may", 6 => "jun", 7 => "jul", 8 => "aug", 9 => "sep", 10 => "oct", 11 => "nov", 12 => "dec");

    $rev_month = 'rev_'.$mons[$c_month];
    $q_list_pms = $this->dashboard_m->list_pm_bysales($date_a,$date_b);

    $pm_data = $this->dashboard_m->fetch_project_pm_nomore();
    $getResultArray = $pm_data->getResultArray();
    $pm_q = array_shift($getResultArray);
    $not_pm_arr = explode(',',$pm_q['user_id'] );

    $this->dashboard_m->reset_revenue($rev_month,$c_year);
    $list_pms = $q_list_pms->getResult();

    foreach ($list_pms as $pm_data ){

      $pm_id = $pm_data->user_id;
      $comp_id = $pm_data->focus_company_id;
      if( !in_array($pm_id, $not_pm_arr) ){

        $q_get_sales = $this->dashboard_m->get_sales($date_a,$date_b,$pm_id,$comp_id);
        $pm_get_sales = $q_get_sales->getResult();
        $pm_sale_total= 0;

        foreach ($pm_get_sales as $pm_sales ){
          if($pm_sales->label == 'VR'){
            $sales_total = $pm_sales->variation_total;
          }else{
            $sales_total = $pm_sales->project_total*($pm_sales->progress_percent/100);
          }
          $pm_sale_total = $pm_sale_total + $sales_total;
        }

        $revenue_id = intval($this->_if_sales_changed($currentYear,$pm_data->user_id,$pm_data->focus_company_id,$rev_month,$pm_sale_total));

        if($revenue_id > 1){
          $this->dashboard_m->update_sales($revenue_id,$rev_month,$pm_sale_total);
          $pm_sale_total= 0;
        }elseif($revenue_id == 0){
          $sales_id = $this->dashboard_m->set_sales($pm_data->user_id, $rev_month, $pm_sale_total, $pm_data->focus_company_id, $currentYear);
          $pm_sale_total= 0;
        }else{

        }

      }

    }

  }

  function _if_sales_changed($year,$proj_mngr_id,$focus_comp_id,$rev_month,$checkAmount){
    $q_sales = $this->dashboard_m->look_for_sales($year,$proj_mngr_id,$focus_comp_id);
    $getResultArray = $q_sales->getResultArray();
    $sales = array_shift($getResultArray);
    $checkAmount = round($checkAmount,2);

    if($sales[$rev_month] != $checkAmount){
      return $sales['revenue_id'];
    }elseif($sales[$rev_month] == $checkAmount){
      return 1;
    }else{
      return 0;
    }
  }


  public function invoiced_pa(){
    $this->admin_m = new Admin_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $c_year = date("Y");
    $n_year = $c_year+1;
    $date_a = "01/01/$c_year";
    $date_b = "01/01/$n_year";
    $date_c = date("d/m/Y");

    $project_manager = $this->dashboard_m->fetch_pms_year($c_year); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $personal_data = 0;

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    $grand_total_sales = 0;
    $sales_total = 0;
    $total_string = '<div class=\'row\'><span class=\'col-xs-12\'> ('.$c_year.')</div>';

    foreach ($focus_company as $company){
      $q_dash_sales = $this->dashboard_m->dash_sales($date_a,$date_c,$company->company_id,1);

      if($q_dash_sales->getNumRows() >= 1){

        $grand_total_sales = 0;
        $sales_total = 0;

        $dash_sales = $q_dash_sales->getResult();

        foreach ($dash_sales as $sales){

          if($sales->label == 'VR'){
            $sales_total = $sales->variation_total;
          }else{
            $sales_total = $sales->project_total*($sales->progress_percent/100);
          }

          $grand_total_sales = $grand_total_sales + $sales_total;

          if($prime_pm == $sales->project_manager_id){
            $personal_data = $personal_data + $sales_total;
          }

          if( in_array($sales->project_manager_id, $group_pm) ){
          //  echo $sales->project_manager_id.'<br />';
            if(isset($pm_data[$sales->project_manager_id])){

            $pm_data[$sales->project_manager_id] = $pm_data[$sales->project_manager_id] + $sales_total;
            }
          }

        }
      } 
    }

    foreach ($pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      $pm_data[$key] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $n_month = date("m");
    $n_day = date("d");

    $date_a_last = "01/01/$last_year";
    $date_b_last = "$n_day/$n_month/$last_year";

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';


    foreach ($focus_company as $company){
      $q_dash_sales = $this->dashboard_m->dash_sales($date_a_last,$date_b_last,$company->company_id,1);

      if($q_dash_sales->getNumRows() >= 1){

        $grand_total_sales = 0;
        $sales_total = 0;

        $dash_sales = $q_dash_sales->getResult();

        foreach ($dash_sales as $sales){

          if($sales->label == 'VR'){
            $sales_total = $sales->variation_total;
          }else{
            $sales_total = $sales->project_total*($sales->progress_percent/100);
          }

          $grand_total_sales = $grand_total_sales + $sales_total;

          if( in_array($sales->project_manager_id, $pm_data) ){
            $pm_data[$sales->project_manager_id] = $pm_data[$sales->project_manager_id] + $sales_total;
          }

        }
      } 
    }

    foreach ($pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($personal_data,2).'</strong></p>';
  }


  public function outstanding_payments_widget_pa(){
    $this->admin_m = new Admin_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $c_year = date("Y");
    $project_manager = $this->dashboard_m->fetch_pms_year($c_year); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $date_a = "01/01/$c_year";
    $n_year = date("Y");
    $n_month = date("m");
    $n_day = date("d");

    $date_b = "$n_day/$n_month/$n_year";

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    $total_string = '<div class=\'row\'><span class=\'col-xs-12\'>('.$c_year.')</div>';
    $personal_data = 0;

    $pm_outstanding = array();
    $display_each_value = 0;

    $project_manager = $this->dashboard_m->fetch_pms_year($c_year); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    foreach ($project_manager_list as $pm ) {
      $pm_outstanding[$pm->user_id] = 0;
    }

    $each_comp_total = array();

    foreach ($focus_company as $company){
      $each_comp_total[$company->company_id] = 0;

      $invoice_amount= 0;
      $total_invoice= 0;
      $total_paid = 0;
      $total_outstanding = 0;

      $key_id = '';

      $q_dash_oustanding_payments = $this->dashboard_m->dash_oustanding_payments($date_a,$date_b,$company->company_id);
      $oustanding_payments = $q_dash_oustanding_payments->getResult();

      foreach ($oustanding_payments as $oustanding) {

        if($oustanding->label == 'VR'){
          $invoice_amount = $oustanding->variation_total;
        }else{
          $invoice_amount = $oustanding->project_total*($oustanding->progress_percent/100);
        }

        $total_paid =  $oustanding->amount_exgst;
        $display_each_value = $invoice_amount - $total_paid;

        if ( array_key_exists($oustanding->project_manager_id, $pm_outstanding) ) {
          $pm_outstanding[$oustanding->project_manager_id] = $pm_outstanding[$oustanding->project_manager_id] + $display_each_value;
        }

        if ( array_key_exists($prime_pm, $pm_outstanding) ) {
          $personal_data_latest = $pm_outstanding[$prime_pm];
        }
      }
    }

    foreach ($pm_data as $key => $value) {
      $amount = $pm_outstanding[$key];
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($amount,2).'</span></div>';
      $pm_outstanding[$key] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $date_a_last = "01/01/$last_year";
    $date_b_last = "31/12/$last_year";

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";
    $date_b_full_end = "31/12/$last_year";


    foreach ($focus_company as $company){
      $each_comp_total[$company->company_id] = 0;

      $invoice_amount= 0;
      $total_invoice= 0;
      $total_paid = 0;
      $total_outstanding = 0;

      $key_id = '';

      $q_dash_oustanding_payments = $this->dashboard_m->dash_oustanding_payments($date_a_last,$date_b_full_end,$company->company_id);
      $oustanding_payments = $q_dash_oustanding_payments->getResult();

      foreach ($oustanding_payments as $oustanding) {

        if($oustanding->label == 'VR'){
          $invoice_amount = $oustanding->variation_total;
        }else{
          $invoice_amount = $oustanding->project_total*($oustanding->progress_percent/100);
        }

        $total_paid =  $oustanding->amount_exgst;
        $display_each_value = $invoice_amount - $total_paid;

        if( in_array($oustanding->project_manager_id, $pm_outstanding) ){
          $pm_outstanding[$oustanding->project_manager_id] = $pm_outstanding[$oustanding->project_manager_id] + $display_each_value;
        }

        if (in_array($prime_pm,$pm_outstanding))  {
          $personal_data =  $pm_outstanding[$prime_pm] + $personal_data_latest;
        }
      }
    }

    foreach ($pm_data as $key => $value) {
      $amount = $pm_outstanding[$key];
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($amount,2).'</span></div>';
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($personal_data,2).'</strong></p>';
  }


  public function uninvoiced_widget_pa(){
    $this->admin_m = new Admin_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $c_year = date("Y");
    $c_month = '01';

    $date_a = "01/01/$c_year";

    $n_year = date("Y");
    $n_month = date("m");
    $n_day = date("d");

    $date_b = "$n_day/$n_month/$n_year";

    $unvoiced_total_arr = array();
    $key_id = '';


    $total_string = '<div class=\'row\'><span class=\'col-xs-12\'> ('.$c_year.')</div>';

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();
    $personal_data = 0;

    $project_manager = $this->dashboard_m->fetch_pms_year($n_year); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    foreach ($focus_company as $company){
      $q_dash_unvoiced = $this->dashboard_m->dash_unvoiced_per_date($date_a,$date_b,$company->company_id);
      $dash_unvoiced = $q_dash_unvoiced->getResult();

      $unvoiced_total = 0;
      $unvoiced_grand_total = 0;

      foreach ($dash_unvoiced as $unvoiced) {
        if($unvoiced->label == 'VR'){
          $unvoiced_total = $unvoiced->variation_total;
        }else{
          $unvoiced_total = $unvoiced->project_total*($unvoiced->progress_percent/100);
        }

        $unvoiced_grand_total = $unvoiced_grand_total + $unvoiced_total;

        if($prime_pm == $unvoiced->project_manager_id){
          $personal_data = $personal_data + $unvoiced_total;
        }

        if( in_array($unvoiced->project_manager_id, $pm_data) ){
          $pm_data[$unvoiced->project_manager_id] = $pm_data[$unvoiced->project_manager_id] + $unvoiced_total;
        }
      }
    }

    foreach ($pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      $pm_data[$key] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $date_a_last = "01/01/$last_year";
    $date_b_last = "31/12/$last_year";

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";
    $date_b_full_end = "31/12/$last_year";


    foreach ($focus_company as $company){
      $q_dash_unvoiced = $this->dashboard_m->dash_unvoiced_per_date($date_a_last,$date_b_full_end,$company->company_id);
      $dash_unvoiced = $q_dash_unvoiced->getResult();

      $unvoiced_total = 0;
      $unvoiced_grand_total = 0;

      foreach ($dash_unvoiced as $unvoiced) {
        if($unvoiced->label == 'VR'){
          $unvoiced_total = $unvoiced->variation_total;
        }else{
          $unvoiced_total = $unvoiced->project_total*($unvoiced->progress_percent/100);
        }

        $unvoiced_grand_total = $unvoiced_grand_total + $unvoiced_total;

        if($prime_pm == $unvoiced->project_manager_id){
          $personal_data = $personal_data + $unvoiced_total;
        }

        if( in_array($unvoiced->project_manager_id, $pm_data) ){
          $pm_data[$unvoiced->project_manager_id] = $pm_data[$unvoiced->project_manager_id] + $unvoiced_total;
        }
      }
    }

    foreach ($pm_data as $key => $value) {
      if (array_key_exists($pm_name[$key],$pm_name))  {
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($personal_data,2).'</strong></p>';
  }

  public function pm_estimates_widget_pa(){
    $this->user_model = new Users_m();
    $this->admin_m = new Admin_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $year = date("Y");
    $current_date = date("d/m/Y");
    $current_start_year = '01/01/'.$year;
    
    $total_string = '<div class=\'row\'><span class=\'col-xs-12\'> ('.$year.')</div>';
    $is_restricted = 0;

    $fetch_user = $this->user_model->fetch_user($prime_pm);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);

  //  $pm_data = $this->dashboard_m->fetch_project_pm_nomore();
  //  $pm_q = array_shift($pm_data->result_array());
  //  $not_pm_arr = explode(',',$pm_q['user_id'] );


    $admin_defaults = $this->admin_m->fetch_admin_defaults(1);
    foreach ($admin_defaults->getResult() as $row){
      $unaccepted_date_categories = $row->unaccepted_date_categories;
      $unaccepted_no_days = $row->unaccepted_no_days;
    }

    $amnt = 0;

    //lists estimators not PM sorry
    $unaccepted_amount = array();
    $estimator = $this->dashboard_m->fetch_project_estimators();
    $estimator_list = $estimator->getResult();

    foreach ($estimator_list as $est ) {
      $unaccepted_amount[$est->project_estiamator_id] = 0;
    }

    $exemp_cat = explode(',', $unaccepted_date_categories);

    $focus_arr = array();
    $project_cost = array();
    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){
      $focus_arr[$company->company_id] = $company->company_name;
      $project_cost[$company->company_id] = 0;
    }
    $personal_data = 0;

    $q_projects = $this->dashboard_m->get_unaccepted_projects($current_start_year,$current_date);
    $projects = $q_projects->getResult();


    foreach ($projects as $un_accepted){
    //  if( !in_array($un_accepted->project_manager_id, $not_pm_arr) ){

      $unaccepted_date = $un_accepted->unaccepted_date;
      if($unaccepted_date !== ""){
        $unaccepted_date_arr = explode('/',$unaccepted_date);
        $u_date_day = $unaccepted_date_arr[0];
        $u_date_month = $unaccepted_date_arr[1];
        $u_date_year = $unaccepted_date_arr[2];
        $unaccepted_date = $u_date_year.'-'.$u_date_month.'-'.$u_date_day;
      }

      $start_date = $un_accepted->date_site_commencement;
      if($start_date !== ""){
        $start_date_arr = explode('/',$start_date);
        $s_date_day = $start_date_arr[0];
        $s_date_month = $start_date_arr[1];
        $s_date_year = $start_date_arr[2];
        $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;
      } 

      if( in_array($un_accepted->job_category, $exemp_cat)  ){
        $is_restricted = 1;
      }else{
        $is_restricted = 0;
      }

      $today = date('Y-m-d');
      $unaccepteddate = strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
      $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

      if(strtotime($unaccepteddate) < strtotime($today)){
        if($is_restricted == 1){
          if($unaccepted_date == ""){
            $status = 'quote';
          }else{
            $status = 'unset';
          }
        }else{
          $status = 'unset';
        }

      }else{
        if($unaccepted_date == ""){
          $status = 'quote';
        }else{
          $status = 'unset';
        }

      }

      if ($status == 'unset'){

        if( $un_accepted->focus_company_id == $user_details['user_focus_company_id'] ){
          if($un_accepted->install_time_hrs > 0 || $un_accepted->work_estimated_total > 0.00 || $un_accepted->variation_total > 0.00 ){
            $amnt =  $un_accepted->project_total + $un_accepted->variation_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt;
          }else{
            $amnt = $un_accepted->budget_estimate_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt; 
          }

          if( isset($unaccepted_amount[$un_accepted->project_estiamator_id])) {


            if($prime_pm == $un_accepted->project_manager_id){
              $unaccepted_amount[$un_accepted->project_estiamator_id] = $unaccepted_amount[$un_accepted->project_estiamator_id] + $amnt;
            }




          }
        }

        if( in_array($un_accepted->project_manager_id, $group_pm) ){


          if( in_array($un_accepted->project_manager_id, $pm_data) ){


            $pm_data[$un_accepted->project_manager_id] = $pm_data[$un_accepted->project_manager_id] + $amnt;
          }
        }

        if($prime_pm == $un_accepted->project_manager_id){
          $personal_data = $personal_data + $amnt;
        }

      }
      //}
    }

    //var_dump($project_cost);

    foreach ($focus_arr as $comp_id => $value ){
      $display_total_cmp = $project_cost[$comp_id];
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
      $project_cost[$comp_id] = 0;
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      $pm_data[$key] = 0;
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    foreach ($estimator_list as $est ) {
      $display_total_cmp = $unaccepted_amount[$est->project_estiamator_id];
      $est_name = $est->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$est_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }

      $unaccepted_amount[$est->project_estiamator_id] = 0;
    }











    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";
    $set_date_b = '01/01/'.$last_year;


    $q_projects = $this->dashboard_m->get_unaccepted_projects($set_date_b,$date_last_year_today);
    $projects = $q_projects->getResult();


    foreach ($projects as $un_accepted){
    //  if( !in_array($un_accepted->project_manager_id, $not_pm_arr) ){

      $unaccepted_date = $un_accepted->unaccepted_date;
      if($unaccepted_date !== ""){
        $unaccepted_date_arr = explode('/',$unaccepted_date);
        $u_date_day = $unaccepted_date_arr[0];
        $u_date_month = $unaccepted_date_arr[1];
        $u_date_year = $unaccepted_date_arr[2];
        $unaccepted_date = $u_date_year.'-'.$u_date_month.'-'.$u_date_day;
      }

      $start_date = $un_accepted->date_site_commencement;
      if($start_date !== ""){
        $start_date_arr = explode('/',$start_date);
        $s_date_day = $start_date_arr[0];
        $s_date_month = $start_date_arr[1];
        $s_date_year = $start_date_arr[2];
        $start_date = $s_date_year.'-'.$s_date_month.'-'.$s_date_day;
      } 

      if( in_array($un_accepted->job_category, $exemp_cat)  ){
        $is_restricted = 1;
      }else{
        $is_restricted = 0;
      }

      $today = date('Y-m-d');
      $unaccepteddate = strtotime ( '-'.$unaccepted_no_days.' day' , strtotime ( $start_date ) ) ;
      $unaccepteddate = date ( 'Y-m-d' , $unaccepteddate );

      if(strtotime($unaccepteddate) < strtotime($today)){
        if($is_restricted == 1){
          if($unaccepted_date == ""){
            $status = 'quote';
          }else{
            $status = 'unset';
          }
        }else{
          $status = 'unset';
        }

      }else{
        if($unaccepted_date == ""){
          $status = 'quote';
        }else{
          $status = 'unset';
        }

      }

      if ($status == 'unset'){

        if( $un_accepted->focus_company_id == $user_details['user_focus_company_id'] ){
          if($un_accepted->install_time_hrs > 0 || $un_accepted->work_estimated_total > 0.00 || $un_accepted->variation_total > 0.00 ){
            $amnt =  $un_accepted->project_total + $un_accepted->variation_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt;
          }else{
            $amnt = $un_accepted->budget_estimate_total;
            $project_cost[$un_accepted->focus_company_id] =  $project_cost[$un_accepted->focus_company_id] + $amnt; 
          }

          if( isset($unaccepted_amount[$un_accepted->project_estiamator_id])) {


            if($prime_pm == $un_accepted->project_manager_id){
              $unaccepted_amount[$un_accepted->project_estiamator_id] = $unaccepted_amount[$un_accepted->project_estiamator_id] + $amnt;
            }

          }
        }



        if( in_array($un_accepted->project_manager_id, $pm_data) ){
          $pm_data[$un_accepted->project_manager_id] = $pm_data[$un_accepted->project_manager_id] + $amnt;
        }

        if($prime_pm == $un_accepted->project_manager_id){
          $personal_data = $personal_data + $amnt;
        }

      }
    }

    foreach ($focus_arr as $comp_id => $value ){
      $display_total_cmp = $project_cost[$comp_id];
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.str_replace("Pty Ltd","",$focus_arr[$comp_id]).'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($pm_data as $key => $value) {
      if($value > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      }
    }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';


    foreach ($estimator_list as $est ) {
      $display_total_cmp = $unaccepted_amount[$est->project_estiamator_id];
      $pm_name = $est->user_first_name;
      if($display_total_cmp > 0){
        $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($display_total_cmp,2).'</span></div>';
      }
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($personal_data,2).'</strong></p>';
  }

  public function average_date_invoice_pa(){
    $this->user_model = new Users_m();
    $this->admin_m = new Admin_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    $pms_averg = array();
    $pms_w_avg = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;

        $pms_averg[$pm->user_id] = array();
        $pms_w_avg[$pm->user_id] = $pm->user_id;
      }
    }


    $c_year = date("Y");    
    $date_a = "01/01/$c_year";
    $date_b = date("d/m/Y");
    $days_dif = array();

    $long_day = 0;
    $size = 0;
    $short_day_day = 0;

    $fetch_user = $this->user_model->fetch_user($prime_pm);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);
    
    $q_ave = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$prime_pm,$user_details['user_focus_company_id']); //4
    $days_result = $q_ave->getResult();

    foreach ($days_result as $result){
      $diff = ($result->days_diff < 0 ? 0 : $result->days_diff);
      array_push($days_dif, $diff);
    }

    foreach ($project_manager_list as $pm ) {
      if(in_array($pm->user_id, $group_pm) ){

        $q_ave_pm = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$pm->user_id,$user_details['user_focus_company_id']); //4
        $days_result_pm = $q_ave_pm->getResult();

        foreach ($days_result_pm as $result_pm){
          $diff = ($result_pm->days_diff < 0 ? 0 : $result_pm->days_diff);
          array_push($pms_averg[$pm->user_id],$diff);
        }
      }
    }

      $size = count($days_dif);

      if($size > 0){
        $average = array_sum($days_dif) / $size;
        arsort($days_dif,1);
        $long_day =  max($days_dif);
        $short_day_day =  min($days_dif);
        $short_day_day = 1;
      }else{
        $average = 0;
        $long_day = 0;
        $short_day_day = 0;
      }

      $total_string = '';

      $all_focus_company = $this->admin_m->fetch_all_company_focus();
      $focus_company = $all_focus_company->getResult();

      foreach ($focus_company as $company){

        if( $company->company_id == $user_details['user_focus_company_id'] ){

          $days_dif_comp = array('');
          $q_ave = $this->dashboard_m->get_maitenance_dates_pm($date_a,$date_b,$company->company_id); //5
          $days_result = $q_ave->getResult();

          foreach ($days_result as $result){
            if($result->project_manager_id != 9){
              $diff = ($result->days_diff < 0 ? 0 : $result->days_diff);
              array_push($days_dif_comp, $diff);
            }
          }

          $size = count($days_dif_comp);
          $average_comp = array_sum($days_dif_comp) / $size;

          arsort($days_dif_comp,1);

          $long_day_comp =  max($days_dif_comp);
          $short_day_day_comp =  min($days_dif_comp);
          $short_day_day_comp = 1;
          $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.str_replace("Pty Ltd","",$company->company_name).'</span><span class=\'col-xs-4\'>'.round($average_comp,1).'</span></div>';

        }
      }

    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($project_manager_list as $pm ){
      if(in_array($pm->user_id, $group_pm) ){
        if( count($pms_averg[$pm->user_id]) > 0  ){
          $size = count($pms_averg[$pm->user_id]);
          $pm_average = array_sum($pms_averg[$pm->user_id]) / $size;
          arsort($pms_averg[$pm->user_id],1);
          $pm_long_day =  max($pms_averg[$pm->user_id]);
          $pm_short_day_day =  min($pms_averg[$pm->user_id]);
          $pm_short_day_day = 1;
          $pm_name = $pm->user_first_name;
          $total_string .= '<div class=\'row\'><span class=\'col-xs-8\'>'.$pm_name.'</span><span class=\'col-xs-4\'>'.round($pm_average,1).'</span></div>';
        }
      }
    }

    echo '<div id="" class="pad-10"><input class="knob avg_fid" data-width="100%" data-step=".1"  data-thickness=".13" value="'.number_format($average,1).'" readonly data-fgColor="#964dd7" data-angleOffset="-180"  data-max="'.$long_day.'"></div>';
    echo '<div id="" style="position: absolute; width: 100%; bottom: 36px; left: 0px;" class="clearfix xxxx m-top-10"><div id="" style="width:50%; float:left;" class=" min_box_area"><strong><p>MIN: '.$short_day_day.' </p></strong></div><div id="" style="width:50%; float:left;" class=" max_box_area"><strong><p>MAX: '.$long_day.'</p></strong></div></div>';
    echo '<div class="" style="background-color: #c586fd; margin: -2px; padding: 5px 10px; font-size: 25px; border-top: 1px solid #8838d0; border-bottom: 1px solid #a678d0; font-weight: bold; margin-bottom: 10px; position: absolute; width: 100%; left: 2px; top: 0;"> <span class=""><i class="fa fa-calendar "></i> '.date('Y').' </span> <span class="pointer" ><i class="fa fa-info-circle tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'."></i></span></div>';
  }

  public function focus_projects_count_widget_pa(){
    $this->admin_m = new Admin_m();
    $this->invoice = new Invoice();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data_a = array();
    $pm_data_b = array();

    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data_a[$pm->user_id] = 0;
        $pm_data_b[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $personal_data_a = 0;
    $personal_data_b = 0;

    $current_date = date("d/m/Y");
    $year = date("Y");
    $next_year_date = '01/01/'.($year+1);
    $current_start_year = '01/01/'.$year;
    $last_start_year = '01/01/'.($year-1);

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    $total_string_inv  = '';
    $total_string_wip  = '';

    foreach ($focus_company as $company) {
      $invoiced = 0;
      $invoiced_old = 0;

      $projects_qa = $this->dashboard_m->get_wip_invoiced_projects($current_start_year, $next_year_date, $company->company_id);
      $projects_ra = $projects_qa->getResultArray();

      foreach ($projects_ra as $result) {
        if($this->invoice->if_invoiced_all($result['project_id'])  && $this->invoice->if_has_invoice($result['project_id']) > 0 ){
          $invoiced++;

          if( in_array($result['project_manager_id'], $group_pm) ){
            $pm_data_a[ $result['project_manager_id'] ]++;
          }

          if($prime_pm == $result['project_manager_id']){
            $personal_data_a++;
          }

        }
      }
    }


    $q_maps = $this->dashboard_m->get_map_projects($current_start_year,$current_date);

    //$proj_t = $this->wip_m->display_all_wip_projects();
    foreach ($q_maps->getResultArray() as $row){
      $comp_id = $row['focus_company_id'];

      if($prime_pm == $row['project_manager_id']){
        $personal_data_b++;
      }

      $pm_id = $row['project_manager_id'];

      if( in_array($pm_id, $pm_data_b) ){
        $pm_data_b[$pm_id]++;
      }
    }

    $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-12\'> ('.$year.') Invoiced</div>';
    $total_string_wip .= '<div class=\'row\'><span class=\'col-xs-12\'> ('.$year.') WIP</div>';

    $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';
    $total_string_wip .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span></div>';

    foreach ($group_pm as $key => $value) {
      if ( array_key_exists($value, $pm_name)   &&    array_key_exists($value, $pm_data_a)   &&    array_key_exists($value, $pm_data_b) ) {
        $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-7\'>'.$pm_name[$value].'</span> <span class=\'col-xs-5\'>'.$pm_data_a[$value].'</span></div>'; $pm_data_a[$value] = 0;
        $total_string_wip .= '<div class=\'row\'><span class=\'col-xs-7\'>'.$pm_name[$value].'</span> <span class=\'col-xs-5\'>'.$pm_data_b[$value].'</span></div>';
      }
    }


    $last_year = intval(date("Y"))-1; 
    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";
    $set_date_b = '01/01/'.$last_year;


    $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    foreach ($focus_company as $company) {
      $invoiced = 0;
      $invoiced_old = 0;

      $projects_qa = $this->dashboard_m->get_wip_invoiced_projects($set_date_b, $date_last_year_today, $company->company_id);
      $projects_ra = $projects_qa->getResultArray();

      foreach ($projects_ra as $result) {
        if($this->invoice->if_invoiced_all($result['project_id'])  && $this->invoice->if_has_invoice($result['project_id']) > 0 ){
          $invoiced++;



          if( in_array($result['project_manager_id'], $pm_data_a) ){
            $pm_data_a[ $result['project_manager_id'] ]++;
          }
        }
      }
    }

    foreach ($group_pm as $key => $value) {
      if ( array_key_exists($value, $pm_name)   &&    array_key_exists($value, $pm_data_a)   ) {
        $total_string_inv .= '<div class=\'row\'><span class=\'col-xs-7\'>'.$pm_name[$value].'</span> <span class=\'col-xs-5\'>'.$pm_data_a[$value].'</span></div>'; $pm_data_a[$value] = 0;
      }
    }


    echo '<div id="" class="clearfix row">        
    <strong class="text-center col-xs-4"><p class="h5x value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string_inv.'"><i class="fa fa-list-alt"></i> &nbsp;'.$personal_data_a.'</p></strong>
    <strong class="text-center col-xs-4"><p class="h5x value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string_wip.'"><i class="fa fa-tasks"></i> &nbsp;'.$personal_data_b.'</p></strong>
    <strong class="text-center col-xs-4"></strong></div>';

  }


  public function focus_get_po_widget_pa(){
    $this->purchase_order_m = new Purchase_order_m();

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array(); 

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $year = date("Y");
    $current_date = '01/01/2019';
    $current_start_year = '01/01/2000';

    $personal_data = 0;

    $po_list_ordered = $this->purchase_order_m->get_po_list_order_by_project($current_start_year,$current_date);

    foreach ($po_list_ordered->getResultArray() as $row){
      $work_id = $row['works_id'];

      $po_tot_inv_q = $this->purchase_order_m->get_po_total_paid($work_id);
      $invoiced = 0;
      foreach ($po_tot_inv_q->getResultArray() as $po_tot_row){
        $invoiced = $po_tot_row['total_paid'];
      }

      $out_standing = $row['price'] - $invoiced;
      
      if( in_array($row['project_manager_id'], $group_pm) ){
        if( in_array($row['project_manager_id'], $pm_data) ){
          $pm_data[$row['project_manager_id']] = $pm_data[$row['project_manager_id']] + $out_standing;
        }
      }

      if($prime_pm == $row['project_manager_id']){
        $personal_data = $personal_data + $out_standing;
      }
    }

    $total_string = '<div class=\'row\'><span class=\'col-xs-12\'> ('.$year.')</div>';

    foreach ( $pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
      $pm_data[$key] = 0;
    }

    $last_year = intval(date("Y"))-1;
    $total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$last_year.')</div>';

    $n_month = date("m");
    $n_day = date("d");
    $date_last_year_today = "$n_day/$n_month/$last_year";
    $set_date_b = '01/01/'.$last_year;

    $po_list_ordered = $this->purchase_order_m->get_po_list_order_by_project($set_date_b,$date_last_year_today);

    foreach ($po_list_ordered->getResultArray() as $row){
      $work_id = $row['works_id'];

      $po_tot_inv_q = $this->purchase_order_m->get_po_total_paid($work_id);
      $invoiced = 0;
      foreach ($po_tot_inv_q->getResultArray() as $po_tot_row){
        $invoiced = $po_tot_row['total_paid'];
      }

      $out_standing = $row['price'] - $invoiced;
      
      if( in_array($row['project_manager_id'], $group_pm) ){
        $pm_data[$row['project_manager_id']] = $pm_data[$row['project_manager_id']] + $out_standing;
      }

      if($prime_pm == $row['project_manager_id']){
        $personal_data = $personal_data + $out_standing;
      }
    }

    foreach ( $pm_data as $key => $value) {
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($value,2).'</span></div>';
    }

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($personal_data,2).'</strong></p>';
  }

  public function wip_widget_pa(){

    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $total_string = '';
    $start_date = "01/01/".date("Y");
    $n_year =  date("Y")+1;
    $set_new_date = '01/01/'.$n_year;


    $display_total = 0;
    $fYear = intval(date("Y"))+2;

    $date_f = "01/01/$fYear";
    $date_ts = '01/01/1990';


    foreach ($pm_data as $key => $value) {
      $amount = $this->get_wip_value_permonth($start_date,$set_new_date,$key,'1'); 
      $total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$pm_name[$key].'</span> <span class=\'col-xs-6\'>$ '.number_format($amount,2).'</span></div>';
    }
    
    $display_total = $this->get_wip_value_permonth($start_date,$set_new_date,$prime_pm,'1');

    echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><i class="fa fa-usd"></i> <strong>'.number_format($display_total,2).'</strong></p>';

  }


  public function pm_sales_widget_pa($is_term=''){
    $this->admin_m = new Admin_m();
    $this->user_model = new Users_m();


    $assignment = $this->pa_assignment();
    $prime_pm = $assignment['project_manager_primary_id'];
    $group_pm = explode(',', $assignment['project_manager_ids']);

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    $pm_data = array();
    $pm_name = array();

    foreach ($project_manager_list as $pm ){
      if( in_array($pm->user_id, $group_pm) ){
        $pm_data[$pm->user_id] = 0;
        $pm_name[$pm->user_id] = $pm->user_first_name;
      }
    }

    $grand_total_sales_cmp = 0;
    $grand_total_uninv_cmp = 0;
    $grand_total_over_cmp = 0;

    $c_year = date("Y");    
    $date_a = "01/01/$c_year";
    $date_b = date("d/m/Y");
    $n_year =  date("Y")+1;
    $set_new_date = '01/01/'.$n_year;
    $date_c = date("d/m/Y");


    $pm_set_data = array();
    $wip_pm_total = array();

    $overall_total_sales = 0;
    $sales_result = array();
    $focus_pms = array();
    $focus_pm_pic = array();
    $focus_pm_comp = array();

    $set_invoiced_amount = array();

    $total_invoiced_init = 0;
    $total_string = '';

    $return_total = 0;

    $project_manager = $this->dashboard_m->fetch_pms_year(date("Y")); // ****--___--***
    $project_manager_list = $project_manager->getResult();

    foreach ($project_manager_list as $pm ) {
      $set_invoiced_amount[$pm->user_id] = 0;
      $sales_result[$pm->user_id] = 0;
      $focus_pms[$pm->user_id] = $pm->user_first_name;
      $focus_pm_pic[$pm->user_id] = $pm->user_profile_photo;
      $focus_pm_comp[$pm->user_id] = $pm->user_focus_company_id;

      $wip_pm_total[$pm->user_id] = 0;
      $sales_result[$pm->user_id] = 0;

      if( in_array($pm->user_id, $group_pm) ){
        $pm_set_data[$pm->user_id] = $pm->user_id;
      }
    }

    $all_focus_company = $this->admin_m->fetch_all_company_focus();
    $focus_company = $all_focus_company->getResult();

    foreach ($focus_company as $company){

      $q_dash_sales = $this->dashboard_m->dash_sales($date_a,$date_c,$company->company_id,1);

      if($q_dash_sales->getNumRows() >= 1){

        $grand_total_sales = 0;
        $sales_total = 0;

        $dash_sales = $q_dash_sales->getResult();

        foreach ($dash_sales as $sales){
          if( in_array($sales->project_manager_id, $group_pm) ){

            if($sales->label == 'VR'){
              $sales_total = $sales->variation_total;
            }else{
              $sales_total = $sales->project_total*($sales->progress_percent/100);
            }

            if(isset($set_invoiced_amount[$sales->project_manager_id])){
              $set_invoiced_amount[$sales->project_manager_id] = $set_invoiced_amount[$sales->project_manager_id] + $sales_total;
            }

            if(isset($pm_set_data[$sales->project_manager_id])){
              $pm_set_data[$sales->project_manager_id] = $sales->project_manager_id;
            }

            $grand_total_sales_cmp = $grand_total_sales_cmp + $sales_total;
          }
        }         
      } 
    }

    $forecast_focus_total = 0; 
    foreach ($project_manager_list as $pm ) {

      if( in_array($pm->user_id, $group_pm) ){
        $total_sales = 0;
        $total_outstanding = 0;

        $q_pm_sales = $this->dashboard_m->dash_total_pm_sales($pm->user_id,$c_year,'',$date_a,$date_b,$pm->user_focus_company_id);
        $pm_sales = $q_pm_sales->getResultArray();

        foreach ($pm_sales as $sales => $value){

          if($value['label'] == 'VR'){
            $project_total_percent = $value['variation_total'];
          }else{
            $project_total_percent = $value['project_total'] * ($value['progress_percent']/100);
          }
        }

      }

    }


//    foreach ($direct_company as $key => $comp_id) {
    foreach ($focus_company as $company){

      foreach ($pm_set_data as $pm_id => $value){
        //ion get_wip_perso

        if( in_array($pm_id, $group_pm) ){
          $wip_amount = $this->get_wip_personal($date_a,$set_new_date,$pm_id,$company->company_id);
          $wip_pm_total[$pm_id] = $wip_pm_total[$pm_id] + $wip_amount;
          $sales_result[$pm_id] = $sales_result[$pm_id] + $wip_amount + $set_invoiced_amount[$pm_id];

        //  echo $comp_id.'---'.$pm_id.'---'.$wip_amount.'<br />';
        }
      }

    }


    arsort($sales_result);
    $total_wip = array_sum($wip_pm_total);

    $total_invoiced = array_sum($set_invoiced_amount);
    
    if($is_term == ''){
      echo "<div style=\"overflow-y: auto; padding-right: 5px; height: 400px;\">";
    }

    foreach ($sales_result as $pm_id => $sales){

      if( in_array($pm_id, $group_pm) ){

      //  if( $sales > 0){
        $comp_id_pm = $focus_pm_comp[$pm_id];

          //$pm_wip = (( $set_estimates[$pm_id] + $set_quotes[$pm_id] ) - $set_invoiced[$pm_id] );

        $q_current_forecast_comp = $this->dashboard_m->get_current_forecast($c_year,$comp_id_pm,'1');
        $getResultArray = $q_current_forecast_comp->getResultArray();
        $comp_forecast = array_shift($getResultArray);

        $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$pm_id);
        $getResultArray = $q_current_forecast->getResultArray();
        $pm_forecast = array_shift($getResultArray);

        $total_forecast = ( $comp_forecast['total'] * (  $comp_forecast['forecast_percent']  /100  ) *  ($pm_forecast['forecast_percent']/100) );


        $pm_sales_value = $set_invoiced_amount[$pm_id] + $wip_pm_total[$pm_id];

        if( $pm_sales_value > 0 ){


          $pm_sales_value = ($pm_sales_value <= 1 ? 1 : $pm_sales_value);
          $total_forecast = ($total_forecast <= 1 ? 1 : $total_forecast);


          $status_forecast = round(100/($total_forecast/$pm_sales_value));
        }else{
          $status_forecast = 0;
        }


        if($is_term == ''){
          echo '<div class="m-bottom-15 clearfix"><div class="pull-left m-right-10"  style="height: 50px; width:50px; border-radius:50px; overflow:hidden; border: 1px solid #999999;"><img class="user_avatar img-responsive img-rounded" src="'.base_url().'/uploads/users/'.$focus_pm_pic[$pm_id].'"" /></div>';
          echo '<div class="" id=""><p><strong>'.$focus_pms[$pm_id].'</strong><span class="pull-right"><span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed"><i class="fa fa-usd"></i> '.number_format($set_invoiced_amount[$pm_id]).'</span> <br /> <span class="label pull-right m-bottom-3 small_green_fixed"><i class="fa fa-exclamation-triangle"></i> '.number_format($wip_pm_total[$pm_id]).'</span></span></p>';
          echo '<p><i class="fa fa-usd"></i> '.number_format($pm_sales_value).'</p>';

          echo '<div class="progress no-m m-top-3 clearfix tooltip-enabled" title="" data-original-title="'.$status_forecast.'% - $'.number_format($pm_sales_value).' / $'.number_format($total_forecast).'   " style="height: 7px;">
          <div class="progress-bar progress-bar-danger" style="width:'.$status_forecast.'%; background:red;"></div></div></div></div>';

          echo "<div class='clearfix'></div>";
        }
        $forecast_focus_total = $forecast_focus_total + $total_forecast;


        if($prime_pm == $pm_id){
          $return_total = $status_forecast;
        }
        //}
      }
    }
    if($is_term == ''){
      echo "</div>";
    }

    $fetch_user = $this->user_model->fetch_user($prime_pm);
    $getResultArray = $fetch_user->getResultArray();
    $user_details = array_shift($getResultArray);

    $q_current_forecast_comp = $this->dashboard_m->get_current_forecast($c_year,$user_details['user_focus_company_id'],'1');
    $getResultArray = $q_current_forecast_comp->getResultArray();
    $comp_forecast = array_shift($getResultArray);

    $q_current_forecast = $this->dashboard_m->get_current_forecast($c_year,$prime_pm);
    $getResultArray = $q_current_forecast->getResultArray();
    $pm_forecast = array_shift($getResultArray);
    $total_forecast = ( $comp_forecast['total'] * (  $comp_forecast['forecast_percent']  /100  ) *  ($pm_forecast['forecast_percent']/100) );


    if ( array_key_exists($prime_pm, $set_invoiced_amount)   &&    array_key_exists($prime_pm, $wip_pm_total) ) {
      $pm_sales_value = $set_invoiced_amount[$prime_pm] + $wip_pm_total[$prime_pm];
    }else{
      $pm_sales_value = 0;
    }

    $pm_sales_value = ($pm_sales_value < 1 ? 0 : $pm_sales_value);
    $total_forecast = ($total_forecast < 1 ? 0 : $total_forecast);

    if($total_forecast > 0 && $pm_sales_value > 0){
      $status_forecast = round(100/($total_forecast/$pm_sales_value));

    }elseif($total_forecast > 0  && $pm_sales_value <= 0 ){
      $status_forecast = round(100/($total_forecast));
    }else{
      $status_forecast = 0;
    }

    //$status_forecast = round(100/($forecast_focus_total/ ($total_wip + $total_invoiced) ));

    if($is_term == ''){
      echo '<div class="clearfix" style="padding-top: 6px;    border-top: 1px solid #eee;"><i class="fa fa-briefcase" style="font-size: 42px;float: left;margin-left: 7px;margin-right: 10px;"></i>';

      if ( array_key_exists($prime_pm, $set_invoiced_amount)   &&    array_key_exists($prime_pm, $wip_pm_total) ) {
        echo '<div class="" id=""><p><strong>Primary Overall</strong><span class="pull-right"><span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed"><i class="fa fa-usd"></i> '.number_format($set_invoiced_amount[$prime_pm]).'</span> <br /> <span class="label pull-right m-bottom-3 small_green_fixed"><i class="fa fa-exclamation-triangle"></i> '.number_format( $wip_pm_total[$prime_pm]).'</span></span></p>';
      }else{
        echo '<div class="" id=""><p><strong>Primary Overall</strong><span class="pull-right"><span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed"><i class="fa fa-usd"></i> 0.00</span> <br /> <span class="label pull-right m-bottom-3 small_green_fixed"><i class="fa fa-exclamation-triangle"></i> 0.00</span></span></p>';
      }

      echo '<p><i class="fa fa-usd"></i> '.number_format( $pm_sales_value ).' <strong class="pull-right m-right-10"></strong></p> </p>';
      echo '<div class="progress no-m m-top-3 clearfix tooltip-enabled" title="" data-original-title="'.$status_forecast.'% - $'.number_format( $pm_sales_value ).' / $'.number_format($total_forecast).'   " style="height: 7px;">';
      echo '<div class="progress-bar progress-bar-danger" style="width:'.$status_forecast.'%; background:red;"></div></div></div></div>';
      echo "<div class='clearfix'></div>";

    }
    //return $return_total;




    if ( array_key_exists($prime_pm, $set_invoiced_amount)   &&    array_key_exists($prime_pm, $wip_pm_total) ) {
      $pm_overall_display = $pm_sales_value = $set_invoiced_amount[$prime_pm] + $wip_pm_total[$prime_pm];  
    }else{
      $pm_overall_display = $pm_sales_value;
    }

    if($is_term == ''){

      return $return_total.'_'.number_format( $pm_overall_display );
    }else{

      echo $return_total.'_'.number_format( $pm_overall_display );
    }
  }











}