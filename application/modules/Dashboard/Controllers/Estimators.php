<?php
// module created by Jervy 26-9-2022
namespace App\Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use App\Modules\Dashboard\Models\Dashboard_m_es;
use App\Modules\Dashboard\Models\Dashboard_m;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;


class Estimators extends BaseController {

  private $dashboard_m;
  private $dashboard_m_es;

  function __construct(){
    $this->dashboard_m = new Dashboard_m();
    $this->dashboard_m_es = new Dashboard_m_es();



  }


  public function index() {
    $this->users = new Users();
    $this->user_model = new Users_m();

    $this->admin_m = new Admin_m();
    $data = array();

    $this->users->_check_user_access('dashboard',1);

    $user_role_id = $this->session->get('user_role_id');
	if($this->session->get('is_admin') == 1  || $user_role_id == 8 || $user_role_id == 16 || $this->session->get('user_id') == 9 || $this->session->get('user_id') == 15  || $this->session->get('user_id') == 6 /*|| $user_role_id == 16 || $user_role_id == 3 || $user_role_id == 2 || $user_role_id == 7 || $user_role_id == 6*/):
	else:		
		redirect('projects', 'refresh');
	endif;


	$estimator = $this->dashboard_m->fetch_project_estimators();
	$data['estimator_list'] = $estimator->getResult();

	$data['es_id'] = $this->session->get('user_id');

	if($this->session->get('is_admin') == 1 || $user_role_id == 8 || $user_role_id == 16 || $this->session->get('user_id') == 9 || $this->session->get('user_id') == 15  || $this->session->get('user_id') == 6 ):

		$dash_type = $this->request->getGet('dash_view');
		if( isset($dash_type) && $dash_type != ''){
			$dash_details = explode('-', $dash_type);
			$data['assign_id'] = $dash_details[0];

			if($dash_details[1] == 'pm'){
				return redirect()->to('/dashboard?dash_view='.$dash_details[0].'-pm');
			}elseif($dash_details[1] == 'mn'){
				return redirect()->to('/dashboard?dash_view='.$dash_details[0].'-mn');
			}elseif($dash_details[1] == 'ad'){
      			$data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_home';
			}elseif($dash_details[1] == 'es'){
				$data['es_id'] = $dash_details[0];

				$fetch_user = $this->user_model->fetch_user($data['es_id']);
				$getResultArray = $fetch_user->getResultArray();
				$user_details = array_shift($getResultArray);

				$data['es_setter_f_name'] = str_replace(' ','_',strtolower($user_details['user_first_name']));
				$data['es_setter'] = $user_details['user_first_name'].' '.$user_details['user_last_name']; 

			}else{
				return redirect()->to('/dashboard');
			}
		}

	endif;

	$all_focus_company = $this->admin_m->fetch_all_company_focus(" AND `company_details`.`company_id` != '4' ");
	$data['focus_company'] = $all_focus_company->getResult();

	$data['main_content'] = 'App\Modules\Dashboard\Views\dashboard_est';
    $data['page_title'] = 'Dashboard';

    return view('App\Views\page',$data);
  }






  function list_deadlines($estimator_id='',$custom=''){

		$this_year =  date("d/m/Y", strtotime("- 5 days"));
 
		$estimator_dlq = $this->dashboard_m_es->fetch_upcoming_deadline($this_year,$estimator_id,$custom);
		$num_result = $estimator_dlq->getNumRows();
		$estimator_dl = $estimator_dlq->getResult();
	 
		$current_day_line = date('Y/m/d');

		foreach ($estimator_dl as $est ) {

			$date_allowance = strtotime(date("Y-m-d", strtotime("- 5 days")) ) * 1000 ;

			if($date_allowance <= $est->project_date_unix){
				$use_date_stamp = $est->project_date_unix;
			}else{
				$use_date_stamp = $date_allowance;
			}

			$quote_deadline_date_replaced = str_replace('/', '-', $est->quote_deadline_date);
			$quote_deadline_date_reformated = date('Y/m/d', strtotime("$quote_deadline_date_replaced - 2 day"));


			$date_deadline_check_val = date('N',$est->deadline_unix);


			if($date_deadline_check_val == 3){
				$deadline_unix = $est->deadline_unix - 259200000;
				$quote_deadline_date_reformated = date('Y/m/d', strtotime("$quote_deadline_date_replaced - 4 day"));
			}else{
				//$deadline_unix = $est->deadline_unix;
				$deadline_unix = $est->deadline_unix - 86400000;
			}

			echo '{
	                name: "<a href=\"'.site_url().'projects/view/'.$est->project_id.'\" target=\"_blank\">'.$est->project_id.'</a>", 
	                dataObj: "'.$est->user_first_name.' : '.$est->project_name.'",
	                values: [
	                	{from: "/Date('.$use_date_stamp.')/", to: "/Date('.$quote_deadline_date_reformated.')/", "desc": "<strong>'.$est->user_first_name.'</strong> : '.$est->project_name.'<br /><strong>'.$est->client_name.'</strong> &nbsp;  &nbsp;  &nbsp;   &nbsp;  Brand : '.$est->brand_name.'",customClass: "'.$est->user_first_name.'"},
	                	{from: "/Date('.$deadline_unix.')/", to: "/Date('.$deadline_unix.')/", "desc": "<strong>'.$est->user_first_name.'</strong> : '.$est->project_name.'<br /><strong>'.$est->client_name.'</strong> &nbsp;  &nbsp;  &nbsp;   &nbsp;  Brand : '.$est->brand_name.'",customClass: "red_deadline"},
	                	{from: "'.$current_day_line.'", to: "'.$current_day_line.'", "desc": "<strong>'.$est->user_first_name.'</strong> : '.$est->project_name.'<br /><strong>'.$est->client_name.'</strong> &nbsp;  &nbsp;  &nbsp;   &nbsp;  Brand : '.$est->brand_name.'",customClass: "curr_date"},
						{from: "'.date('Y/m/d', strtotime('- 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 20 days')).'",  customClass: "hide"}
	                ]
	        },';
		}

		for($looper = $num_result; $looper < 21 ; $looper ++ ){
			echo '
			{ values: [
					{from: "'.date('Y/m/d', strtotime('- 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 5 days')).'",  customClass: "hide"},
					{from: "'.date('Y/m/d').'", to: "'.date('Y/m/d').'" ,customClass: "curr_date"},
					{from: "'.date('Y/m/d', strtotime('+ 5 days')).'", to: "'.date('Y/m/d', strtotime('+ 20 days')).'",  customClass: "hide"}
				]
			}, ';
		}
	}


	function completed_prjs($es_id=''){
		$total_string = '';
		
		$this_year = date("Y");
		$n_year = $this_year+1;
		$date_b = "01/01/$n_year";

		$this_year_a = "01/01/$this_year";
		$this_year_b = date("d/m/Y");

		$old_year = intval(date("Y"))-1;
		$old_month = date("m");
		$old_day = date("d");

		$date_a_last = "01/01/$old_year";
		$date_b_last = "$old_day/$old_month/$old_year";

		$display_cost = 0;
		$display_counter = 0;

		$total_string .= '<div class=\'row\'> &nbsp; ('.$this_year.')</div>';

		$estimator = $this->dashboard_m->fetch_project_estimators();
		$estimator_list = $estimator->getResult();
		foreach ($estimator_list as $est ) {
			$cost_total = 0;
			$counter = 0;
			$q_est_completed = $this->dashboard_m_es->fetch_completed($this_year_a,$this_year_b,$est->project_estiamator_id);
			$completed = $q_est_completed->getResult();

			foreach ($completed as $est_cmp ) {

				$cost_total = $cost_total  + $est_cmp->variation_total + $est_cmp->project_total;
				$counter++;
			}

			if($es_id != '' && $es_id == $est->project_estiamator_id){
				$display_cost = $cost_total;
				$display_counter = $counter;
			}

			if($es_id == ''){
				$display_cost = $display_cost + $cost_total;
				$display_counter = $display_counter + $counter;
			}

			$total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$est->user_first_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($cost_total,2).' <span class=\'pull-right\'>'.$counter.'</span></span></div>';
		}

		$total_string .= '<div class=\'row\'><span class=\'col-xs-12\'><hr style=\'margin:4px 0px;\' /></span> &nbsp; ('.$old_year.')</div>';

		foreach ($estimator_list as $est ) {
			$cost_total = 0;
			$counter = 0;
			$q_est_completed = $this->dashboard_m_es->fetch_completed($date_a_last,$date_b_last,$est->project_estiamator_id);
			$completed = $q_est_completed->getResult();

			foreach ($completed as $est_cmp ) {

				$cost_total = $cost_total  + $est_cmp->variation_total + $est_cmp->project_total;
				$counter++;
			}

			$total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$est->user_first_name.'</span> <span class=\'col-xs-6\'>$ '.number_format($cost_total,2).' <span class=\'pull-right\' style=\'font-weight: normal;    font-size: 14px;\'>'.$counter.'</span></span></div>';
		}

		echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><strong><i class="fa fa-usd"></i> '.number_format($display_cost,2).' <span class="pull-right" style=\'font-weight: normal; font-size: 14px;\'>'.number_format($display_counter).'</span></strong></p>';
	}


	public function up_coming_deadline($estimator_id = '',$custom=''){

		$estimator_name = array();
		$estimators_val = array();
		$total_string = '';

		$estimator = $this->dashboard_m->fetch_project_estimators();
		$estimator_list = $estimator->getResult();
		foreach ($estimator_list as $est ) {
			$estimator_name[$est->project_estiamator_id] = $est->user_first_name;
			$estimators_val[$est->project_estiamator_id] = 0;
		}

		if($estimator_id == 0 || $estimator_id == '' ){
			$estimator_id_set = '';
		}else{
			$estimator_id_set = $estimator_id;
		}

		$diff_a = 0;
		$diff_b = 0;

		$this_year = date("d/m/Y");

		if(isset($custom) && $custom!=''){
			$custom_q = " AND `project`.`focus_company_id` = '$custom' ";
		}else{
			$custom_q = " ";
		}

		$estimator_dlq = $this->dashboard_m_es->fetch_upcoming_deadline($this_year,'',$custom_q);
		$estimator_dl = $estimator_dlq->getResult();

		$current_day_line = date('Y/m/d');

		$no_selected = 0;



      	$dStart = (new \CodeIgniter\I18n\Time(date("Y-m-d")));


		foreach ($estimator_dl as $est ) {

			if( array_key_exists($est->project_estiamator_id, $estimators_val) ){
				if( $estimators_val[$est->project_estiamator_id] == 0  ){

					$quote_deadline_date_replaced = str_replace('/', '-', $est->quote_deadline_date);

					$quote_deadline_date_checker = date('N', strtotime("$quote_deadline_date_replaced"));

					if($quote_deadline_date_checker == 1){

						$set_time = date('Y-m-d', strtotime("$quote_deadline_date_replaced - 2 day")    );
      					$dEnd = (new \CodeIgniter\I18n\Time($set_time));

					}else{

      					$dEnd = (new \CodeIgniter\I18n\Time($quote_deadline_date_replaced));


					}

					$dDiff = $dStart->difference($dEnd);

					$project_date_date_replaced = str_replace('/', '-', $est->project_date);
				
					$p_set_date = date('Y-m-d', strtotime("$project_date_date_replaced"));

      				$pdEnd = (new \CodeIgniter\I18n\Time($p_set_date));

					$pdDiff = $dEnd->difference($pdEnd);

					if($est->project_estiamator_id == $estimator_id_set){
						$diff_a = intval($dDiff->days) - 1;
						$diff_b = $pdDiff->days;
					}

					$days_remains = intval($dDiff->days) - 1;

					if($days_remains > 0){

						if($estimator_id_set == '' && $no_selected == 0){
							$diff_a = intval($dDiff->days) - 1;
							$diff_b = $pdDiff->days;
							$no_selected = 1;
						}			

						$estimators_val[$est->project_estiamator_id] = 1;
						$total_string .= '<div class=\'row\'><span class=\'col-xs-4\'>'.$est->user_first_name.'</span><span class=\'col-xs-8\'>'.$days_remains.' day(s) before dealine.</span></div>';
					}
				}
			}
		}


		echo '<div id="" class="tooltip-enabled" title="" data-placement="bottom" data-html="true" data-original-title="'.$total_string.'">
		<input class="knob" data-width="100%" data-step=".1"  data-thickness=".15" value="'.$diff_a.'" readonly data-fgColor="#058EB4" data-angleOffset="-180"  data-max="'.$diff_b.'">
		<div id="" class="clearfix m-top-10 text-center"><strong><p><br />'.$diff_a.' day(s) before next deadline.</p></strong></div></div>';
	}


	function estimators_wip($es_id=''){
		$estimators_cost = array();
		$estimator_name = array();
		$estimator_counter = array();
		$total_string = '';

		$estimator = $this->dashboard_m->fetch_project_estimators();
		$estimator_list = $estimator->getResult();
		foreach ($estimator_list as $est ) {
			$estimators_cost[$est->project_estiamator_id] = 0;
			$estimator_counter[$est->project_estiamator_id] = 0;
			$estimator_name[$est->project_estiamator_id] = $est->user_first_name;
		}

		if($es_id != ''){
			$estimators_cost[$es_id] = 0;
			$estimator_counter[$es_id] = 0;
		}

		$estimators_cost[0] = 0;
		$estimator_counter[0] = 0;
		$estimator_name[0] = '';

		$all_estimators_wip_q = $this->dashboard_m_es->fetch_estimators_wip();
		$all_estimators_wip = $all_estimators_wip_q->getResult();

		//var_dump($all_estimators_wip);

		$loop_counter = 0;

		foreach ($all_estimators_wip as $es_wip){
			
				if ( array_key_exists($es_wip->project_estiamator_id, $estimators_cost) ) {		
					$total_cost = $es_wip->project_total + $es_wip->variation_total + $estimators_cost[$es_wip->project_estiamator_id];
					$estimators_cost[$es_wip->project_estiamator_id] = $total_cost;	
				}

				if ( array_key_exists($es_wip->project_estiamator_id, $estimator_counter) ) {		
					$estimator_counter[$es_wip->project_estiamator_id]++;
				}
		}

		if($es_id != ''){
			$display_cost = $estimators_cost[$es_id];
			$display_counter = $estimator_counter[$es_id];
		}else{
			$display_cost = array_sum($estimators_cost);
			$display_counter = array_sum($estimator_counter);
		}

		foreach ($estimator_list as $est ) {
			$total_string .= '<div class=\'row\'><span class=\'col-xs-6\'>'.$estimator_name[$est->project_estiamator_id].'</span> <span class=\'col-xs-6\'>$ '.number_format($estimators_cost[$est->project_estiamator_id],2).' <span class=\'pull-right\'>'.$estimator_counter[$est->project_estiamator_id].'</span></span></div>';
		}

		echo '<p class="value tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="'.$total_string.'"><strong><i class="fa fa-usd"></i> '.number_format($display_cost,2).' <span class="pull-right" style=\'font-weight: normal;    font-size: 14px;\'>'.number_format($display_counter).'</span></strong></p>';
	}


	public function estimators_quotes_completed($es_id = ''){ 
    $this->admin_m = new Admin_m();
		$total_string = '';

		$all_focus_company = $this->admin_m->fetch_all_company_focus();
		$focus_company = $all_focus_company->getResult();
		$focus_arr = array();
		$quoted_focus_company = array();
		$cost_focus = array();
		$cost_focus_b = array();

		foreach ($focus_company as $company){
			$focus_arr[$company->company_id] = $company->company_name;
			$quoted_focus_company[$company->company_id] = 0;
			$cost_focus[$company->company_id] = 0;
			$cost_focus_b[$company->company_id] = 0;
		}

		$estimator = $this->dashboard_m->fetch_project_estimators();
		$estimator_list = $estimator->getResult();
		$quoted_estimator = array();
		$quoted_estimator_name = array();
		$cost_estimator = array();
		$cost_estimator_b = array();
		
		foreach ($estimator_list as $est ) {
			$quoted_estimator[$est->project_estiamator_id] = 0;
			$cost_estimator[$est->project_estiamator_id] = 0;
			$cost_estimator_b[$est->project_estiamator_id] = 0;
			$quoted_estimator_name[$est->project_estiamator_id] = $est->user_first_name;
		}

		$quoted_estimator_name[0] = '';
		$is_restricted = 0;

		$current_year = intval(date("Y"));
		$last_year = intval(date("Y"))-1; 

		$n_month = date("m");
		$n_day = date("d");
		$date_last_year_today = "$n_day/$n_month/$last_year";

		$m_month = $n_month+2;
		$year_odl_set = $last_year;

		if($m_month > 12){
			$m_month = $m_month - 12;
			$year_odl_set = $last_year + 1;
			$date_last_year_next = "01/$m_month/$year_odl_set";
		}else{
			$date_last_year_next = "01/$m_month/$last_year";
		}

		$date_last_year_today_exx = "01/01/$current_year";
		$date_last_year_today_err = "$n_day/$n_month/$current_year";

		$fetch_quoted_per_comp_q = $this->dashboard_m_es->fetch_quoted_per_comp();
		$quoted_prj_amnt = $fetch_quoted_per_comp_q->getResult();

		foreach ($quoted_prj_amnt as $es_qutd){

			if( isset( $cost_estimator[$es_qutd->project_estiamator_id] )  ){
				$cost_estimator[$es_qutd->project_estiamator_id] = $cost_estimator[$es_qutd->project_estiamator_id] + $es_qutd->quote_total;
			}

			if( isset($cost_focus[$es_qutd->focus_company_id]) ){
				$cost_focus[$es_qutd->focus_company_id] = $cost_focus[$es_qutd->focus_company_id] + $es_qutd->quote_total;
			}
		}


		$all_projects_q = $this->dashboard_m->get_all_active_projects($date_last_year_today,$date_last_year_next);
		foreach ($all_projects_q->getResultArray() as $row){
			if($row['project_estiamator_id'] != '0'){
				if($row['project_estiamator_id'] != '8' && $row['project_estiamator_id'] != '92'){

					if($row['install_time_hrs'] > 0 || $row['work_estimated_total'] > 0.00 || $row['variation_total'] > 0.00 ){
						$project_cost = $row['project_total'] + $row['variation_total'];
					}else{
						$project_cost = $row['budget_estimate_total'];
					}

					if( isset($cost_focus_b[$row['focus_company_id']] )  ){
						$cost_focus_b[$row['focus_company_id']] = $cost_focus_b[$row['focus_company_id']] + $project_cost;
					}

					if( isset(  $cost_estimator_b[$row['project_estiamator_id']] )  ){
						$cost_estimator_b[$row['project_estiamator_id']] = $cost_estimator_b[$row['project_estiamator_id']] + $project_cost;
					}
				}
			}
		}



		$site_url = site_url();
		echo '<div style="overflow-y: auto; padding-right: 5px; height: 410px;">';

		foreach ($estimator_list as $est ) {

			if($est->project_estiamator_id != '0'):
				if($est->project_estiamator_id != '8' && $est->project_estiamator_id != '92'):

				echo '<div class="clearfix" style="border-bottom: 1px solid #ecf0f5;padding: 4px 0px;">
						<div class="pull-left m-right-5" style="height: 50px; width:50px; border-radius:50px; overflow:hidden; border: 1px solid #999999;">
							<img class="user_avatar img-responsive img-rounded" src="'.$site_url.'uploads/users/'.$est->user_profile_photo.'">
						</div>

						<div class="" id="">
							<p>
								<span class="pull-right">
									<span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$current_year.'" style="width: 150px;"><i class="fa fa-usd"></i> '.number_format($cost_estimator[$est->project_estiamator_id],2).'</span> <br> 
									<span class="label pull-right m-bottom-3 small_green_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$last_year.'" style="width: 150px; background-color: #aaa !important;"><i class="fa fa-usd"></i> '.number_format($cost_estimator_b[$est->project_estiamator_id],2).'</span>
								</span>	 
								<strong style="padding-top: 10px; display: block; color:#fff; "><span class="'.str_replace(' ','_',strtolower($quoted_estimator_name[$est->project_estiamator_id])).'" style="padding: 3px 6px; border-radius: 10px;" >'.$quoted_estimator_name[$est->project_estiamator_id].'</span></strong>
							</p>
						</div>
					</div>';			
				endif;
			endif;
		}

		$total_present = 0;
		$total_past = 0;

		foreach ($focus_company as $company){
			if($cost_focus[$company->company_id] > 0){

				echo '<div class="m-bottom-5 clearfix" style="border-bottom: 1px solid #ecf0f5;padding: 4px 0px;">		
						<i class="fa fa-user" style="font-size: 42px;float: left;margin-left: 7px;margin-right: 10px;"></i>
						<div class="" id="">
							<p>
								<span class="pull-right">
									<span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$current_year.'"  style="width: 150px;"><i class="fa fa-usd"></i> '.number_format($cost_focus[$company->company_id],2).'</span> <br> 
									<span class="label pull-right m-bottom-3 small_green_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$last_year.'"  style="width: 150px; background-color: #aaa !important;"><i class="fa fa-usd"></i> '.number_format($cost_focus_b[$company->company_id],2).'</span>
								</span> 
								<strong style="padding-top: 10px; display: block;">'.str_replace("Pty Ltd","",$focus_arr[$company->company_id]).'</strong>
							</p>
						</div>
					</div>';

				$total_present = $total_present + $cost_focus[$company->company_id];
				$total_past = $total_past + $cost_focus_b[$company->company_id];
			}
		}


		echo '</div>';



		echo '<div class="clearfix" style="padding-top: 5px;      padding-right: 5px;  border-top: 1px solid #eee;">
		<i class="fa fa-briefcase" style="font-size: 42px;float: left;margin-left: 7px;margin-right: 10px;"></i>
		<div class="" id="">
		<p>
			<span class="pull-right">
				<span class="label pull-right m-bottom-3 m-top-3 small_orange_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$current_year.'" style="width: 150px;"><i class="fa fa-usd"></i> '.number_format($total_present,2).'</span> <br> 
				<span class="label pull-right m-bottom-3 small_green_fixed tooltip-enabled" title="" data-placement="left" data-html="true" data-original-title="'.$last_year.'" style="width: 150px; background-color: #aaa !important;"><i class="fa fa-exclamation-triangle"></i> '.number_format($total_past,2).'</span>
			</span>
			<strong style="padding-top: 10px; display: block;">Overall Focus</strong>
		</p></div></div>';

	}









}