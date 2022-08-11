<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Works extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');	
		$this->load->module('users');
		$this->load->module('company');
		$this->load->model('works_m');
		$this->load->model('admin_m');
		$this->load->module('attachments');
		$this->load->module('send_emails');
		$this->load->model('send_emails_m');
		if($this->session->userdata('is_admin') == 1 ):		
			$this->load->module('admin');
		endif;
		$this->load->module('projects');

		$this->load->helper(array('form', 'url','html'));
		//$this->load->model('project_m');
	}
	function index(){
	}

	function works_view(){
		$project_id = $this->uri->segment(3);

		$q_proj = $this->projects_m->fetch_project_details($project_id);
		if($q_proj->num_rows > 0){
			$data = array_shift($q_proj->result_array());
			$projid = $data['project_id'];
		}else{
			$data['error'] = 'Unable to locate record';
		}

		$proj_q = $this->projects_m->select_particular_project($project_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['projid'] = $projid;
		$data['screen'] = "Works";
		$this->load->view('works_v',$data);
	}

	function variations_view(){		
		$project_id = $this->uri->segment(3);

		$q_proj = $this->projects_m->fetch_project_details($project_id);
		if($q_proj->num_rows > 0){
			$data = array_shift($q_proj->result_array());
			$projid = $data['project_id'];
		}else{
			$data['error'] = 'Unable to locate record';
		}
		$data['projid'] = $projid;
		$data['screen'] = "Variations";
		$this->load->view('variations_v',$data);
	}

	public function list_supplier_category(){
		$supplier_category = $this->projects_m->display_supplier_category();
		foreach ($supplier_category->result_array() as $row){
			if($row['supplier_cat_name'] !== 'Other'){
				echo '<option value="3_'.$row['supplier_cat_id'].'">'.$row['supplier_cat_name'].'</option>';
			}
		}
	}

	function work_details(){
		$project_id = $this->uri->segment(3);
		$url_req = $this->uri->segment(4);

		$q_proj = $this->projects_m->fetch_project_details($project_id);
		if($q_proj->num_rows > 0){
			$data = array_shift($q_proj->result_array());
			$projid = $data['project_id'];
			$data['projmarkup'] = $data['markup'];
			$job_cat = $data['job_category'];
			$data['min_markup'] = $this->get_minimum_markup($job_cat);
		}else{
			$data['error'] = 'Unable to locate record';
		}

		$data['job_cat'] = $this->works_m->display_job_category();
		$data['main_content'] = 'work_details_v';
		$data['workdescription'] = 'Full Fitout';
		$data['markup']= 0;
		$data['operation'] = 0;

		$exist = 0;
		$work_q = $this->works_m->display_all_works($project_id);
		foreach ($work_q->result_array() as $row){
			$work_con_sup_id = $row['work_con_sup_id'];
			if($work_con_sup_id == 53){
				$exist = 1;
				$work_id = $row['works_id'];
			}
		}
		$data['exist'] = $exist;

		$is_joinery = $this->input->post('is_joinery');
		if($is_joinery !== 'on'){
			$this->form_validation->set_rules('worktype', 'Work Type','trim|required|xss_clean');
			if($this->input->post('worktype') == '2_82'){
				$this->form_validation->set_rules('other_work_description', 'Other Work Description','trim|required|xss_clean');
			}
		}else{
			$this->form_validation->set_rules('work_joinery_name', 'Work Joinery','trim|required|xss_clean');
		}
		//
		//$this->form_validation->set_rules('work_estimate', 'Work Estimate','trim|required|xss_clean');
		//$this->form_validation->set_rules('work_markup', 'Work Markup','trim|required|xss_clean');
		//$this->form_validation->set_rules('work_quote_val', 'Quote','trim|required|xss_clean');

		if($url_req == 'variations' ){ 
			$data['variations'] = 1;
			$data['screen'] = "Variation";

		}else{
			$data['variations'] = 0;
			$data['screen'] = "Work";
		}

		if($this->form_validation->run() === false){
			$data['error' ] = validation_errors();
			$this->load->view('page', $data);
			//valid_input_simple
		}else{
			$is_joinery = $this->input->post('is_joinery');
			if($is_joinery !== 'on'){
				$attachment_type =  $this->input->post('chck_attachment_type');
				$work_raw_type = $this->input->post('worktype');

				$w_type = explode('_', $work_raw_type);
				$contractor_type = $w_type[0];
				$work_con_sup_id = $w_type[1];
				if($work_raw_type == '2_82'){
					$other_work_desc = $this->input->post('other_work_description');
				}else{
					$other_work_desc = "";
				}
			}else{
				$contractor_type = 0;
				$work_con_sup_id = 0;
				$joinery_name = $this->input->post('work_joinery_name');
				$joinery_name_q = $this->works_m->verify_joinery_name($joinery_name);
				if($joinery_name_q->num_rows == 0){
					$joinery_id = $this->works_m->insert_joinery_name($joinery_name);
				}else{
					foreach ($joinery_name_q->result_array() as $row){
						$joinery_id = $row['joinery_id'];
					}
				}
			}

			//$work_estimate = $this->input->post('work_estimate');
			//$work_estimate = str_replace( ',', '', $work_estimate );
			$markup = $this->input->post('work_markup');
			$markup = str_replace( ',', '', $markup );
			//$total_work_quote = $this->input->post('work_quote_val');
			//$total_work_quote = str_replace( ',', '', $total_work_quote );

			$is_deliver_office = $this->input->post('chkdeltooffice');

			$work_cpo_date = $this->input->post('work_cpo_date');
			$work_reply_date = $this->input->post('work_replyby_date');

			$comments = $this->input->post('replyby_desc');
			$notes = $this->input->post('work_notes');

			$site_inspection_req = $this->input->post('chkcons_site_inspect');
			$special_conditions = $this->input->post('chckcons_spcl_condition');
			$additional_visit_req = $this->input->post('chckcons_addnl_visit');
			$operate_during_install = $this->input->post('chckcons_oprte_duringinstall');
			$week_work = $this->input->post('chckcons_week_work');
			$weekend_work = $this->input->post('chckcons_weekend_work');
			$after_hours_work = $this->input->post('chckcons_afterhrs_work');
			$new_premises = $this->input->post('chckcons_new_premises');
			$free_access = $this->input->post('chckcons_free_access');
			$other = $this->input->post('chckcons_others');
			$otherdesc = $this->input->post('other_consideration');

			$add_work = array(
							'work_reply_date' => $work_reply_date,
							'site_inspection_req' => $site_inspection_req,
							'special_conditions' => $special_conditions,
							'additional_visit_req' => $additional_visit_req,
							'operate_during_install' => $operate_during_install,
							'week_work' => $week_work,
							'weekend_work' => $weekend_work,
							'after_hours_work' => $after_hours_work,
							'new_premises' => $new_premises,
							'free_access' => $free_access,
							'other' => $other,
							'otherdesc' => $otherdesc,
							'comments' => $comments
						);
			$this->session->set_userdata($add_work);

			$is_variation = $this->input->post('is_variation');
			$note_id = $this->works_m->insert_work_notes($comments,$notes);

			if($is_joinery !== 'on'){
				$work_id = $this->works_m->insert_new_works($contractor_type,$work_con_sup_id,$other_work_desc,$markup,$note_id,$is_deliver_office,$work_cpo_date,$work_reply_date,$project_id,$is_variation,"","");
				if(isset($_POST['chck_attachment_type'])){
					$attachment_type =  $this->input->post('chck_attachment_type');
					$this->session->set_userdata($attachment_type);
					$x = 0;
					foreach($attachment_type as $chkval){
						$this->works_m->insert_work_attachment_type($work_id,$chkval);
						$x++;
					}
					$attachment_num = array(
							'attachment_num' => $x
						);
					$this->session->set_userdata($attachment_num);
				}
				$this->works_m->insert_considerations($work_id, $site_inspection_req, $special_conditions, $additional_visit_req, $operate_during_install, $week_work, $weekend_work, $after_hours_work, $new_premises, $free_access, $other, $otherdesc);
				
			}else{
				//$works_joinery_q = $this->works_m->verify_works_joinery($project_id);
				//if($works_joinery_q->num_rows == 0){
					//$work_id = $this->works_m->insert_new_works($contractor_type,$work_con_sup_id,$markup,$note_id,$is_deliver_office,$work_cpo_date,$work_reply_date,$project_id,$is_variation);
				//}
				$this->works_m->insert_works_joinery($work_id,$joinery_id,$markup,$note_id,$is_deliver_office,$work_cpo_date,$work_reply_date);
			}
			if($url_req == 'variations' ){
				$this->session->set_flashdata('curr_tab', 'variations');
			}else{
				$this->session->set_flashdata('curr_tab', 'works');
			}		
			/* NOTE there is a contractor that has no work cat!! */

			redirect('/projects/view/'.$project_id);

		}
		
	}
	function update_work_details(){

		$project_id = $this->uri->segment(3);
		$work_id = $this->uri->segment(4);
		$work_joinery_id = $this->uri->segment(5);

		$q_proj = $this->projects_m->fetch_project_details($project_id);
		if($q_proj->num_rows > 0){
			$data = array_shift($q_proj->result_array());
			$projid = $data['project_id'];
			$data['projmarkup'] = $data['markup'];
			$job_cat = $data['job_category'];
			$data['min_markup'] = $this->get_minimum_markup($job_cat);
		}else{
			$data['error'] = 'Unable to locate record';
		}

		$proj_q = $this->projects_m->select_particular_project($project_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['job_cat'] = $this->works_m->display_job_category();
		$data['main_content'] = 'work_detail_update_v';
		$works_q = $this->works_m->display_works_selected($work_id);
		if($work_joinery_id == ""){
			foreach ($works_q->result_array() as $row){
				$contractor_type = $row['contractor_type'];
				$work_con_sup_id = $row['work_con_sup_id'];
				if($contractor_type == 3){
					$work_desc = $row['supplier_cat_name'];
				}else{
					if($work_con_sup_id == 82){
						$work_desc = $row['other_work_desc'];
					}else{
						$work_desc = $row['job_sub_cat'];
					}
					
				}
			}
			$data['work_joinery_id'] = "";
		}else{
			$works_joinery_q = $this->works_m->display_selected_works_joinery($work_joinery_id);
			foreach ($works_joinery_q->result_array() as $row){
				$work_desc = $row['joinery_name'];
			}
			$work_con_sup_id = "";
			$data['work_joinery_id'] = $work_joinery_id;
		}
		
		$data['work_id'] = $work_id;
		$data['project_id'] = $project_id;
		$data['work_q'] = $works_q;
		$data['work_desc'] = $work_desc;
		//$data['markup']= 0;
		$data['operation'] = 0;
	
		$data['variations'] = 0;
		$data['screen'] = "Work";
		$data['work_con_sup_id'] = $work_con_sup_id;
		$work_attachment_type = $this->works_m->fetch_work_attachment_type($work_id);
		$data['attachment_type'] = $this->attachments_m->display_all_attachment_type();

		$data['work_attachment_type'] = $work_attachment_type;
		
		$this->load->view('page', $data);
		/*
		$project_id = $this->uri->segment(3);

		$q_proj = $this->projects_m->fetch_project_details($project_id);
		if($q_proj->num_rows > 0){
			$data = array_shift($q_proj->result_array());
			$projid = $data['project_id'];
			$data['projmarkup'] = $data['markup'];
			$job_cat = $data['job_category'];
			$data['min_markup'] = $this->get_minimum_markup($job_cat);
		}else{
			$data['error'] = 'Unable to locate record';
		}
		$data['job_cat'] = $this->works_m->display_job_category();
		$data['main_content'] = 'work_details_v';
		$data['workdescription'] = 'Full Fitout';
		$data['markup']= 0;
		$data['operation'] = 1;
		//print_r($data);
		$this->load->view('page', $data);
		*/
	}

	function update_other_work_desc(){
		$other_work_description = $_POST['other_work_description'];
		$work_id = $_POST['work_id'];
		$this->works_m->update_other_work_desc($work_id, $other_work_description);
	}

	function get_minimum_markup($job_cat){
		$proj_id = $this->uri->segment(3);
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$defaults_id = $row['defaults_id'];
		}
		$defaults_q = $this->admin_m->latest_system_default($defaults_id);
		foreach ($defaults_q->result_array() as $row){
			$markup_id = $row['markup_id'];
		}
		$markup_q = $this->admin_m->fetch_markup($markup_id);
		//$q_citecost = $this->works_m->get_site_costs();
		$data = array_shift($markup_q->result_array());
		switch($job_cat){
			case "Kiosk":
				$min_markup = $data['min_kiosk'];
				break;
			case "Full Fitout":
				$min_markup = $data['min_full_fitout'];
				break;
			case "Refurbishment":
				$min_markup = $data['min_refurbishment'];
				break;
			case "Strip out":
				$min_markup = $data['min_stripout'];
				break;
			case "Minor Works":
				$min_markup = $data['min_minor_works'];
				break;
			case "Maintenance":
				$min_markup = $data['min_maintenance'];
				break;
		}
		return $min_markup;
	}

	function get_work_markup(){
		$work_id = $_POST['work_id'];
		$work_q = $this->works_m->display_works_selected($work_id);
		foreach ($work_q->result_array() as $row){
			$markup = $row['work_markup'];
		}
		echo $markup;
	}

	function get_work_joinery_markup(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$work_q = $this->works_m->display_selected_works_joinery($work_joinery_id);
		foreach ($work_q->result_array() as $row){
			$markup = $row['work_markup'];
		}
		echo $markup;
	}

	function display_job_sub_cat(){
		$job_cat_id = $_POST['job_cat'];
		$data['job_cat'] = $this->works_m->display_job_subcategory($job_cat_id);
		$data['type'] = 0;
		$this->load->view('list_of_jobsubcat_v', $data);
	}
	function diplay_sup_cat(){
		$data['sup_cat'] = $this->works_m->display_supplier_category();
		$data['type'] = 1;
		$this->load->view('list_of_jobsubcat_v', $data);
	}

	function display_all_works_query($proj_id){
		$data['proj_id'] = $proj_id;
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}
		$data['works_t'] = $this->works_m->display_all_works($proj_id);
		$this->load->view('table_works', $data);
	}

	function display_all_works_joinery_query($work_id){
		$data['work_id'] = $work_id;
		$proj_id = $this->uri->segment(3);
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}
		$data['works_joinery_t'] = $this->works_m->display_all_works_joinery($work_id);
		$this->load->view('table_works_joinery', $data);
	}
	
	function display_all_variations_query($proj_id){
		$data['works_t'] = $this->works_m->display_all_works($proj_id,'1');
		$this->load->view('table_variations', $data);
	}

	function display_work_form(){
		/*$job_sub_cat = $this->works_m->display_job_subcategory();
		foreach ($job_sub_cat->result() as $row)
		{
		    echo $row->job_sub_cat;
		}*/
		if(isset($_POST['work_id'])){
			$work_id = $_POST['work_id'];
			$data['works'] = $this->works_m->display_works_selected($work_id);
			$data['job_sub_cat'] = $this->works_m->display_job_subcategory();
			$data['stat'] = 1;
			$this->load->view('add_work_form', $data);
		}else{
			$data['job_sub_cat'] = $this->works_m->display_job_subcategory();
			$data['stat'] = 0;
			$this->load->view('add_work_form', $data);
		}
	}
	function proj_summary_w_cost(){
		$proj_id = $this->uri->segment(3);

		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = html_entity_decode(str_replace("&apos;","'",$row['project_name'])); 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}
			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		   	
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($client_id);
		foreach ($comp_det_q->result_array() as $row){
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}

			$email_id = $row['email_id'];
			$email_q = $this->company_m->fetch_email($email_id);
			foreach ($email_q->result_array() as $email_row){
				$data['client_email'] = $email_row['general_email'];
			}

		}

		$proj_cost_total_t = $this->projects_m->get_project_cost_total($proj_id);
		foreach ($proj_cost_total_t->result_array() as $row){
			$data['install_cost_total'] = $row['install_cost_total'];
			$works_quoted_total = $row['work_quoted_total'];
		}

		$project_totals_arr = $this->projects->fetch_project_totals($proj_id);
		$data = array_merge($data, $project_totals_arr);

		$data['variation'] = 0;
		$data['contract_management'] = $works_quoted_total + $project_totals_arr['final_labor_cost'];

		$data['works_t'] = $this->works_m->display_all_works($proj_id);

		$proj_summary_exist = 0;
		$proj_form_q = $this->send_emails_m->display_project_forms($proj_id);
		foreach ($proj_form_q->result_array() as $row){
			if($row['description'] == 'project_summary_w_cost.pdf'){
				$proj_summary_exist++;
			}
		}
		if($proj_summary_exist == 0){
			$this->send_emails_m->insert_project_forms($proj_id,'project_summary_w_cost.pdf');
		}else{
			$this->send_emails_m->update_project_forms($proj_id,'project_summary_w_cost.pdf');
		}

		$data['proj_id'] = $proj_id;
		$this->load->view('proj_summary_w_cost', $data);
	}

	function proj_summary_wo_cost(){
		$proj_id = $this->uri->segment(3);

		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = html_entity_decode(str_replace("&apos;","'",$row['project_name'])); 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}
			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		   	
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($client_id);
		foreach ($comp_det_q->result_array() as $row){
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}

			$email_id = $row['email_id'];
			$email_q = $this->company_m->fetch_email($email_id);
			foreach ($email_q->result_array() as $email_row){
				$data['client_email'] = $email_row['general_email'];
			}

		}

		$proj_cost_total_t = $this->projects_m->get_project_cost_total($proj_id);
		foreach ($proj_cost_total_t->result_array() as $row){
			$data['install_cost_total'] = $row['install_cost_total'];
			$works_quoted_total = $row['work_quoted_total'];
		}

		$project_totals_arr = $this->projects->fetch_project_totals($proj_id);
		$data = array_merge($data, $project_totals_arr);

		$data['variation'] = 0;
		$data['contract_management'] = $works_quoted_total + $project_totals_arr['final_labor_cost'];

		$data['works_t'] = $this->works_m->display_all_works($proj_id);

		$proj_summary_exist = 0;
		$proj_form_q = $this->send_emails_m->display_project_forms($proj_id);
		foreach ($proj_form_q->result_array() as $row){
			if($row['description'] == 'project_summary_wo_cost.pdf'){
				$proj_summary_exist++;
			}
		}
		if($proj_summary_exist == 0){
			$this->send_emails_m->insert_project_forms($proj_id,'project_summary_wo_cost.pdf');
		}else{
			$this->send_emails_m->update_project_forms($proj_id,'project_summary_wo_cost.pdf');
		}

		$data['proj_id'] = $proj_id;
		$this->load->view('proj_summary_wo_cost', $data);
	}

	function proj_joinery_summary_w_cost(){
		$proj_id = $this->uri->segment(3);
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = html_entity_decode(str_replace("&apos;","'",$row['project_name'])); 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}
			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		   	
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($client_id);
		foreach ($comp_det_q->result_array() as $row){
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}

			$email_id = $row['email_id'];
			$email_q = $this->company_m->fetch_email($email_id);
			foreach ($email_q->result_array() as $email_row){
				$data['client_email'] = $email_row['general_email'];
			}
		}

		$works_t = $this->works_m->display_all_works($proj_id);
		foreach ($works_t->result_array() as $row){
			$work_con_sup_id = $row['work_con_sup_id'];
			if($work_con_sup_id == 53){
				$work_id = $row['works_id'];
			}
		}
		$data['joinery_t'] = $this->works_m->display_all_works_joinery($work_id);

		$form_exist = 0;
		$proj_form_q = $this->send_emails_m->display_project_forms($proj_id);
		foreach ($proj_form_q->result_array() as $row){
			if($row['description'] == 'joinery_summary_w_cost.pdf'){
				$form_exist++;
			}
		}
		if($form_exist == 0){
			$this->send_emails_m->insert_project_forms($proj_id,'joinery_summary_w_cost.pdf');
		}else{
			$this->send_emails_m->update_project_forms($proj_id,'joinery_summary_w_cost.pdf');
		}

		$data['proj_id'] = $proj_id;
		$this->load->view('joinery_summary', $data);
	}

	function proj_joinery_summary_wo_cost(){
		$proj_id = $this->uri->segment(3);

		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = html_entity_decode(str_replace("&apos;","'",$row['project_name'])); 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}
			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($client_id);
		foreach ($comp_det_q->result_array() as $row){
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}

			$email_id = $row['email_id'];
			$email_q = $this->company_m->fetch_email($email_id);
			foreach ($email_q->result_array() as $email_row){
				$data['client_email'] = $email_row['general_email'];
			}
		}

		$works_t = $this->works_m->display_all_works($proj_id);
		foreach ($works_t->result_array() as $row){
			$work_con_sup_id = $row['work_con_sup_id'];
			if($work_con_sup_id == 53){
				$work_id = $row['works_id'];
			}
		}

		$data['joinery_t'] = $this->works_m->display_all_works_joinery($work_id);

		$form_exist = 0;
		$proj_form_q = $this->send_emails_m->display_project_forms($proj_id);
		foreach ($proj_form_q->result_array() as $row){
			if($row['description'] == 'joinery_summary_wo_cost.pdf'){
				$form_exist++;
			}
		}
		if($form_exist == 0){
			$this->send_emails_m->insert_project_forms($proj_id,'joinery_summary_wo_cost.pdf');
		}else{
			$this->send_emails_m->update_project_forms($proj_id,'joinery_summary_wo_cost.pdf');
		}

		$data['proj_id'] = $proj_id;
		$this->load->view('joinery_summery_wo_cost', $data);
	}
	function contract_tot_rfntf(){
		$proj_id = $this->uri->segment(3);

		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$client_id = $row['client_id'];
			$compname = $row['company_name'];
			$data['project_name'] = $row['project_name'];
			$data['project_date'] = $row['project_date'];
			$data['project_total'] = $row['project_total'];
			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
			}

			$project_manager_id = $row['project_manager_id'];
			$users_q = $this->user_model->fetch_user($project_manager_id);
			foreach ($users_q->result_array() as $users_row){
				$data['proj_manager'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				/*$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
					$data['email'] = $email_row['general_email'];
				}*/
			}
			$primary_contact_person_id = $row['primary_contact_person_id'];
			$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
			foreach ($comp_q->result_array() as $row){
				$data['client_contact_person'] = $row['first_name']." ".$row['last_name'];
				//$email_id = $row['email_id'];
				$comp_contact_number_id = $row['contact_number_id'];
				$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
				foreach ($comp_phone_q->result_array() as $phone_row){
					$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}

				$email_id = $row['email_id'];
				$email_q = $this->company_m->fetch_email($email_id);
				foreach ($email_q->result_array() as $email_row){
					$data['client_email'] = $email_row['general_email'];
				}
			}

		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($client_id);
		foreach ($comp_det_q->result_array() as $row){
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		}

		$applied_admin_settings_raw = $this->projects->display_project_applied_defaults($proj_id);
		$data = array_merge($data, $applied_admin_settings_raw);


		$data['project_t'] = $proj_q;
		$data['proj_id'] = $proj_id;
		$this->load->view('contract_tot_rntf', $data);
		//$this->load->view('sample_fpdm', $data);
	}
	function works_contractors(){
		$work_id = $_POST['work_id'];
		$work_sel_cont_list_q = $this->works_m->display_work_contructor($work_id);
		foreach ($work_sel_cont_list_q->result_array() as $work_sel_cont_list_row){
			$contractor_id = $work_sel_cont_list_row['company_id'];
			echo $contractor_id."|";
		}
	}

	function fetch_works_contractors(){
		$work_contractor_id = $_POST['work_contractor_id'];
		$work_sel_cont_list_q = $this->works_m->display_selected_contractor($work_contractor_id);
		foreach ($work_sel_cont_list_q->result_array() as $work_sel_cont_list_row){
			$contractor_id = $work_sel_cont_list_row['company_id'];
			$work_id = $work_sel_cont_list_row['works_id'];
			$is_selected = $work_sel_cont_list_row['is_selected'];
			$cqr_created = $work_sel_cont_list_row['cqr_created'];
			$cpo_created = $work_sel_cont_list_row['cpo_created'];
		}
		echo $contractor_id."|".$work_id."|".$is_selected."|".$cqr_created."|".$cpo_created;
	}

	function get_contractor_email(){
		$work_id = $_POST['work_id'];
		$comp_id = $_POST['comp_id'];
		$work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_id,$comp_id);
		foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
			foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
			    $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
				   
				$contact_number_id = $work_cont_person_row['contact_number_id'];
				$phon_q = $this->company_m->fetch_phone($contact_number_id);
				foreach ($phon_q->result_array() as $phone_row){
					$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}

				$contact_email_id = $work_cont_person_row['email_id'];
				$email_q = $this->company_m->fetch_email($contact_email_id);
				foreach ($email_q->result_array() as $email_row){
					$cont_email = $email_row['general_email'];
				}
			}
		}
		echo $cont_email;
	}

	function contractor_quote_request(){
		$proj_id = $this->uri->segment(3);
		$work_id = $this->uri->segment(4);
		$contractor_id = $this->uri->segment(5);
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = $row['project_name']; 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}

			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		   	
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}
		}
		//$email_q = $this->company_m->fetch_email($email_id);
		//foreach ($email_q->result_array() as $row){
		 //  $data['email'] = $row['general_email'];
		//}
		$work_q = $this->works_m->display_works_selected($work_id);
		foreach ($work_q->result_array() as $row){
		   $company_client_id = $row['company_client_id'];
		   $contractor_type = $row['contractor_type'];
		   $goods_deliver_by_date = $row['goods_deliver_by_date'];

		   $site_inspection_req = $row['site_inspection_req'];
		   if($site_inspection_req == 1){
		   		$data['site_inspection_req'] = "checkbox_checked.jpg";
		   }else{
		   		$data['site_inspection_req'] = "checkbox.jpg";
		   }
		   $special_conditions = $row['special_conditions'];
		   if($special_conditions == 1){
		   		$data['special_conditions'] = "checkbox_checked.jpg";
		   }else{
		   		$data['special_conditions'] = "checkbox.jpg";
		   }
		   $additional_visit_req = $row['additional_visit_req'];
		   if($additional_visit_req == 1){
		   		$data['additional_visit_req'] = "checkbox_checked.jpg";
		   }else{
		   		$data['additional_visit_req'] = "checkbox.jpg";
		   }
		   $operate_during_install = $row['operate_during_install'];
		   if($operate_during_install == 1){
		   		$data['operate_during_install'] = "checkbox_checked.jpg";
		   }else{
		   		$data['operate_during_install'] = "checkbox.jpg";
		   }
		   $week_work = $row['week_work'];
		   if($week_work == 1){
		   		$data['week_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['week_work'] = "checkbox.jpg";
		   }
		   $weekend_work = $row['weekend_work'];
		   if($weekend_work == 1){
		   		$data['weekend_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['weekend_work'] = "checkbox.jpg";
		   }
		   $after_hours_work = $row['after_hours_work'];
		   if($after_hours_work == 1){
		   		$data['after_hours_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['after_hours_work'] = "checkbox.jpg";
		   }
		   $new_premises = $row['new_premises'];
		   if($new_premises == 1){
		   		$data['new_premises'] = "checkbox_checked.jpg";
		   }else{
		   		$data['new_premises'] = "checkbox.jpg";
		   }
		   $free_access = $row['free_access'];
		   if($free_access == 1){
		   		$data['free_access'] = "checkbox_checked.jpg";
		   }else{
		   		$data['free_access'] = "checkbox.jpg";
		   }
		   $other = $row['other'];
		   if($other == 1){
		   		$data['other'] = "checkbox_checked.jpg";
		   		$data['otherdesc'] = $row['otherdesc'];
		   }else{
		   		$data['other'] = "checkbox.jpg";
		   		$data['otherdesc'] = "";
		   }
		   

		   if($goods_deliver_by_date == "" ){
		   		$data['goods_deliver_by_date'] = "";	
		   }else{
		   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
		   }

		   $note_id = $row['note_id'];
		   $note_q = $this->company_m->fetch_notes($note_id);
		   foreach ($note_q->result_array() as $note_row){
		   		$data['notes'] = $note_row['notes'];
		   }
		   if($contractor_type == 2){
		   		$data['work_desc'] = $row['job_sub_cat'];
		   }else{
		   		$data['work_desc'] = $row['supplier_cat_name'];
		   }
		}
		
			
		$work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_id,$contractor_id);
		foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
			foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
			    $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
				   
				$contact_number_id = $work_cont_person_row['contact_number_id'];
				$phon_q = $this->company_m->fetch_phone($contact_number_id);
				foreach ($phon_q->result_array() as $phone_row){
					$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}

				$contact_email_id = $work_cont_person_row['email_id'];
				$email_q = $this->company_m->fetch_email($contact_email_id);
				foreach ($email_q->result_array() as $email_row){
					$data['cont_email'] = $email_row['general_email'];
				}
			}
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($contractor_id);
		foreach ($comp_det_q->result_array() as $row){
		   $data['work_company_name'] = $row['company_name'];
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		   $data['abn'] = $row['abn'];
		}
		
		$work_attachement_q = $this->works_m->fetch_work_attachment_type($work_id);

		$this->works_m->update_works_contractor_cqr($work_id,$contractor_id);
		$data['work_id'] = $work_id;
		$data['work_attachement_t'] = $work_attachement_q;
		$data['proj_id'] = $proj_id;
		$this->load->view('contractor_quote_request',$data);
	}
	function contractor_quote_request_nodisplay(){
		$proj_id = $_GET['project_id'];//$this->uri->segment(3);
		$work_id = $_GET['work_id'];//$this->uri->segment(4);
		$contractor_id = $_GET['comp_id'];//$this->uri->segment(5);
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		$data['project_t'] = $proj_q;
		foreach ($proj_q->result_array() as $row){
			$data['proj_name'] = $row['project_name']; 
		   	$primary_contact_person_id = $row['primary_contact_person_id'];
		   	$client_id = $row['client_id'];
			$job_type = $row['job_type'];
			$data['start_date'] = $row['date_site_commencement'];
			$data['end_date'] = $row['date_site_finish'];
			$site_address_id= $row['site_add'];
			$data['project_total'] = $row['project_total'];

			$project_estiamator_id = $row['project_estiamator_id'];
			$users_q = $this->user_model->fetch_user($project_estiamator_id);
			foreach ($users_q->result_array() as $users_row){
				$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
				$user_email_id = $users_row['user_email_id'];
				$email_q = $this->company_m->fetch_email($user_email_id);
				foreach ($email_q->result_array() as $email_row){
				   $data['email'] = $email_row['general_email'];
				}

			}

			$focus_company_id = $row['focus_company_id'];
			$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
			foreach ($focus_comp_q->result_array() as $focus_comp_row){
				$data['focus_logo'] = $focus_comp_row['logo'];
				$data['focus_comp'] = $focus_comp_row['company_name'];
				$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
				$data['acn'] = $focus_comp_row['acn'];
				$data['focus_abn'] = $focus_comp_row['abn'];
				$data['focus_email'] = $focus_comp_row['general_email'];
				$address_id = $focus_comp_row['address_id'];
				$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
				foreach ($focus_comp_q->result_array() as $comp_address_row){
					$po_box = $comp_address_row['po_box'];
					if($po_box == ""){
						$data['po_box'] = "";
					}else{
						$data['po_box'] = "PO".$comp_address_row['po_box'];
					}
					$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
				}
			}

			if($job_type == "Shopping Center"){
		   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}	
		   	}else{
				$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
				foreach ($site_address_q->result_array() as $client_add_row){
					if($client_add_row['unit_level'] == ""){
						$data['site_address_1st'] = $client_add_row['unit_number'];
					}else{
						$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
					}
					$data['site_address_2nd'] = $client_add_row['street'];
					$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
				}
		   	}
		   	
		}

		$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
		foreach ($comp_q->result_array() as $row){
			//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
			//$email_id = $row['email_id'];
			$comp_contact_number_id = $row['contact_number_id'];
			$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
			foreach ($comp_phone_q->result_array() as $phone_row){
				$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
			}
		}
		//$email_q = $this->company_m->fetch_email($email_id);
		//foreach ($email_q->result_array() as $row){
		 //  $data['email'] = $row['general_email'];
		//}
		$work_q = $this->works_m->display_works_selected($work_id);
		foreach ($work_q->result_array() as $row){
		   $company_client_id = $row['company_client_id'];
		   $contractor_type = $row['contractor_type'];
		   $goods_deliver_by_date = $row['goods_deliver_by_date'];

		   $site_inspection_req = $row['site_inspection_req'];
		   if($site_inspection_req == 1){
		   		$data['site_inspection_req'] = "checkbox_checked.jpg";
		   }else{
		   		$data['site_inspection_req'] = "checkbox.jpg";
		   }
		   $special_conditions = $row['special_conditions'];
		   if($special_conditions == 1){
		   		$data['special_conditions'] = "checkbox_checked.jpg";
		   }else{
		   		$data['special_conditions'] = "checkbox.jpg";
		   }
		   $additional_visit_req = $row['additional_visit_req'];
		   if($additional_visit_req == 1){
		   		$data['additional_visit_req'] = "checkbox_checked.jpg";
		   }else{
		   		$data['additional_visit_req'] = "checkbox.jpg";
		   }
		   $operate_during_install = $row['operate_during_install'];
		   if($operate_during_install == 1){
		   		$data['operate_during_install'] = "checkbox_checked.jpg";
		   }else{
		   		$data['operate_during_install'] = "checkbox.jpg";
		   }
		   $week_work = $row['week_work'];
		   if($week_work == 1){
		   		$data['week_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['week_work'] = "checkbox.jpg";
		   }
		   $weekend_work = $row['weekend_work'];
		   if($weekend_work == 1){
		   		$data['weekend_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['weekend_work'] = "checkbox.jpg";
		   }
		   $after_hours_work = $row['after_hours_work'];
		   if($after_hours_work == 1){
		   		$data['after_hours_work'] = "checkbox_checked.jpg";
		   }else{
		   		$data['after_hours_work'] = "checkbox.jpg";
		   }
		   $new_premises = $row['new_premises'];
		   if($new_premises == 1){
		   		$data['new_premises'] = "checkbox_checked.jpg";
		   }else{
		   		$data['new_premises'] = "checkbox.jpg";
		   }
		   $free_access = $row['free_access'];
		   if($free_access == 1){
		   		$data['free_access'] = "checkbox_checked.jpg";
		   }else{
		   		$data['free_access'] = "checkbox.jpg";
		   }
		   $other = $row['other'];
		   if($other == 1){
		   		$data['other'] = "checkbox_checked.jpg";
		   		$data['otherdesc'] = $row['otherdesc'];
		   }else{
		   		$data['other'] = "checkbox.jpg";
		   		$data['otherdesc'] = "";
		   }
		   

		   if($goods_deliver_by_date == "" ){
		   		$data['goods_deliver_by_date'] = "";	
		   }else{
		   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
		   }

		   $note_id = $row['note_id'];
		   $note_q = $this->company_m->fetch_notes($note_id);
		   foreach ($note_q->result_array() as $note_row){
		   		$data['notes'] = $note_row['notes'];
		   }
		   if($contractor_type == 2){
		   		$data['work_desc'] = $row['job_sub_cat'];
		   }else{
		   		$data['work_desc'] = $row['supplier_cat_name'];
		   }
		}
		
			
		$work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_id,$contractor_id);
		foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
			foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
			    $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
				   
				$contact_number_id = $work_cont_person_row['contact_number_id'];
				$phon_q = $this->company_m->fetch_phone($contact_number_id);
				foreach ($phon_q->result_array() as $phone_row){
					$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}

				$contact_email_id = $work_cont_person_row['email_id'];
				$email_q = $this->company_m->fetch_email($contact_email_id);
				foreach ($email_q->result_array() as $email_row){
					$data['cont_email'] = $email_row['general_email'];
				}
			}
		}

		$comp_det_q = $this->company_m->display_company_detail_by_id($contractor_id);
		foreach ($comp_det_q->result_array() as $row){
		   $data['work_company_name'] = $row['company_name'];
		   $address_id = $row['address_id'];
		   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
		   foreach ($comp_add_q->result_array() as $comp_add_row){
		   		if($comp_add_row['unit_level'] == ""){
		   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
		   		}else{
		   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
		   		}
				$data['comp_address_2nd'] = $comp_add_row['street'];
				$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
		   }
		   $data['abn'] = $row['abn'];
		}
		
		$work_attachement_q = $this->works_m->fetch_work_attachment_type($work_id);

		$this->works_m->update_works_contractor_cqr($work_id,$contractor_id);
		
		$data['work_id'] = $work_id;
		$data['work_attachement_t'] = $work_attachement_q;
		$data['proj_id'] = $proj_id;
		$this->load->view('contractor_quote_request_nodisplay',$data);
	}

	function work_contractor_po(){
		$proj_id = $this->uri->segment(3);
		$work_id = $this->uri->segment(4);
		$work_joinery_id = $this->uri->segment(5);
		if($work_joinery_id == ""){
			//$this->works_m->create_cpo_date($work_id);
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
			   	$primary_contact_person_id = $row['primary_contact_person_id'];
			   	$client_id = $row['client_id'];
				$job_type = $row['job_type'];
				$data['start_date'] = $row['date_site_commencement'];
				$data['end_date'] = $row['date_site_finish'];
				$site_address_id= $row['site_add'];
				$data['project_total'] = $row['project_total'];

				$project_estiamator_id = $row['project_estiamator_id'];
				$users_q = $this->user_model->fetch_user($project_estiamator_id);
				foreach ($users_q->result_array() as $users_row){
					$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
					$user_email_id = $users_row['user_email_id'];
					$email_q = $this->company_m->fetch_email($user_email_id);
					foreach ($email_q->result_array() as $email_row){
					   $data['email'] = $email_row['general_email'];
					}

				}

				$focus_company_id = $row['focus_company_id'];
				$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
				foreach ($focus_comp_q->result_array() as $focus_comp_row){
					$data['focus_logo'] = $focus_comp_row['logo'];
					$data['focus_comp'] = $focus_comp_row['company_name'];
					$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
					$data['acn'] = $focus_comp_row['acn'];
					$data['abn'] = $focus_comp_row['abn'];
					$data['focus_email'] = $focus_comp_row['general_email'];
					$address_id = $focus_comp_row['address_id'];
					$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
					foreach ($focus_comp_q->result_array() as $comp_address_row){
						$po_box = $comp_address_row['po_box'];
						if($po_box == ""){
							$data['po_box'] = "";
						}else{
							$data['po_box'] = "PO".$comp_address_row['po_box'];
						}
						$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
					}
				}

				if($job_type == "Shopping Center"){
			   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}	
			   	}else{
					$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}
			   	}
			   	
			}


			$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
			foreach ($comp_q->result_array() as $row){
				//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
				//$email_id = $row['email_id'];
				$comp_contact_number_id = $row['contact_number_id'];
				$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
				foreach ($comp_phone_q->result_array() as $phone_row){
					$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}
			}
			//$email_q = $this->company_m->fetch_email($email_id);
			//foreach ($email_q->result_array() as $row){
			 //  $data['email'] = $row['general_email'];
			//}
			$work_q = $this->works_m->display_works_selected($work_id);
			foreach ($work_q->result_array() as $row){
			   $company_client_id = $row['company_client_id'];
			   $price = $row['price'];
			   $data['price'] = $price;
			   $contractor_type = $row['contractor_type'];
			   $goods_deliver_by_date = $row['goods_deliver_by_date'];
			   if($goods_deliver_by_date == "" ){
			   		$data['goods_deliver_by_date'] = "";	
			   }else{
			   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
			   }

			   $note_id = $row['note_id'];
			   $note_q = $this->company_m->fetch_notes($note_id);
			   foreach ($note_q->result_array() as $note_row){
			   		$data['notes'] = $note_row['notes'];
			   }
			   if($contractor_type == 2){
			   		$data['work_desc'] = $row['job_sub_cat'];
			   }else{
			   		$data['work_desc'] = $row['supplier_cat_name'];
			   }
			   $work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_id,$company_client_id);
			   foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			   		$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			   		$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
					foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
					   $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
					   $contact_number_id = $work_cont_person_row['contact_number_id'];
					   $phon_q = $this->company_m->fetch_phone($contact_number_id);
					   foreach ($phon_q->result_array() as $phone_row){
					   		$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
					   }
					}
			   }
			}

			$comp_det_q = $this->company_m->display_company_detail_by_id($company_client_id);
			foreach ($comp_det_q->result_array() as $row){
			   $data['work_company_name'] = $row['company_name'];
			   $address_id = $row['address_id'];
			   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
			   foreach ($comp_add_q->result_array() as $comp_add_row){
			   		if($comp_add_row['unit_level'] == ""){
			   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
			   		}else{
			   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
			   		}
					$data['comp_address_2nd'] = $comp_add_row['street'];
					$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
			   }
			}


			$data['project_t'] = $proj_q;
			$data['works_t'] = $work_q;
			$data['proj_id'] = $proj_id;
			$data['work_id'] = $work_id;

			$this->works_m->update_works_contractor_cpo($work_id,$company_client_id);

			$q_admin_defaults = $this->admin_m->fetch_admin_defaults();
			foreach ($q_admin_defaults->result_array() as $row){
				$gst_rate = $row['gst_rate'];
			}
			$data['gst_rate'] = $gst_rate;
			$percent_gst_rate = $price * ($gst_rate/100);
	    	$data['inc_gst'] = $price + $percent_gst_rate;
			$this->load->view('contractor_purchase_order', $data);
		}else{
			//$this->works_m->create_cpo_date($work_id);
			$array = explode("-",$work_joinery_id);
			$joinery_id = $array[1];
			$this->works_m->create_joinery_cpo_date($joinery_id);
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
			   	$primary_contact_person_id = $row['primary_contact_person_id'];
			   	$client_id = $row['client_id'];
				$job_type = $row['job_type'];
				$data['start_date'] = $row['date_site_commencement'];
				$data['end_date'] = $row['date_site_finish'];
				$site_address_id= $row['site_add'];
				$data['project_total'] = $row['project_total'];

				$project_estiamator_id = $row['project_estiamator_id'];
				$users_q = $this->user_model->fetch_user($project_estiamator_id);
				foreach ($users_q->result_array() as $users_row){
					$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
					$user_email_id = $users_row['user_email_id'];
					$email_q = $this->company_m->fetch_email($user_email_id);
					foreach ($email_q->result_array() as $email_row){
					   $data['email'] = $email_row['general_email'];
					}

				}

				$focus_company_id = $row['focus_company_id'];
				$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
				foreach ($focus_comp_q->result_array() as $focus_comp_row){
					$data['focus_logo'] = $focus_comp_row['logo'];
					$data['focus_comp'] = $focus_comp_row['company_name'];
					$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
					$data['acn'] = $focus_comp_row['acn'];
					$data['abn'] = $focus_comp_row['abn'];
					$data['focus_email'] = $focus_comp_row['general_email'];
					$address_id = $focus_comp_row['address_id'];
					$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
					foreach ($focus_comp_q->result_array() as $comp_address_row){
						$po_box = $comp_address_row['po_box'];
						if($po_box == ""){
							$data['po_box'] = "";
						}else{
							$data['po_box'] = "PO".$comp_address_row['po_box'];
						}
						$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
					}
				}

				if($job_type == "Shopping Center"){
			   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}	
			   	}else{
					$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}
			   	}
			   	
			}


			$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
			foreach ($comp_q->result_array() as $row){
				//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
				//$email_id = $row['email_id'];
				$comp_contact_number_id = $row['contact_number_id'];
				$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
				foreach ($comp_phone_q->result_array() as $phone_row){
					$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}
			}
			//$email_q = $this->company_m->fetch_email($email_id);
			//foreach ($email_q->result_array() as $row){
			 //  $data['email'] = $row['general_email'];
			//}
			$work_q = $this->works_m->display_selected_works_joinery($joinery_id);
			foreach ($work_q->result_array() as $row){
			   $company_client_id = $row['work_joinery_contractor_id'];
			   $price = $row['price'];
			   $data['price'] = $price;
			   //$contractor_type = $row['contractor_type'];
			   $goods_deliver_by_date = $row['goods_deliver_by_date'];
			   if($goods_deliver_by_date == "" ){
			   		$data['goods_deliver_by_date'] = "";	
			   }else{
			   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
			   }

			   $note_id = $row['note_id'];
			   $note_q = $this->company_m->fetch_notes($note_id);
			   foreach ($note_q->result_array() as $note_row){
			   		$data['notes'] = $note_row['notes'];
			   }
			   
			   $data['work_desc'] = $row['joinery_name'];
			   
			   $work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_joinery_id,$company_client_id);
			   foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			   		$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			   		$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
					foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
					   $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
					   $contact_number_id = $work_cont_person_row['contact_number_id'];
					   $phon_q = $this->company_m->fetch_phone($contact_number_id);
					   foreach ($phon_q->result_array() as $phone_row){
					   		$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
					   }
					}
			   }
			}
			
			$comp_det_q = $this->company_m->display_company_detail_by_id($company_client_id);
			foreach ($comp_det_q->result_array() as $row){
			   $data['work_company_name'] = $row['company_name'];
			   $address_id = $row['address_id'];
			   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
			   foreach ($comp_add_q->result_array() as $comp_add_row){
			   		if($comp_add_row['unit_level'] == ""){
			   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
			   		}else{
			   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
			   		}
					$data['comp_address_2nd'] = $comp_add_row['street'];
					$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
			   }
			}


			$data['project_t'] = $proj_q;
			$data['works_t'] = $work_q;
			$data['proj_id'] = $proj_id;
			$data['work_id'] = $work_joinery_id;

			$this->works_m->update_works_contractor_cpo($work_joinery_id,$company_client_id);

			$q_admin_defaults = $this->admin_m->fetch_admin_defaults();
			foreach ($q_admin_defaults->result_array() as $row){
				$gst_rate = $row['gst_rate'];
			}
			$data['gst_rate'] = $gst_rate;
			$percent_gst_rate = $price * ($gst_rate/100);
	    	$data['inc_gst'] = $price + $percent_gst_rate;
			$this->load->view('contractor_purchase_order', $data);
		}
	
	}
	function work_contractor_po_nodisplay(){
		$proj_id = $_GET['project_id'];
		$work_id_joinery_id = $_GET['work_id'];
		$array = explode("-",$work_id_joinery_id);
		$work_id = $array[0];
		$work_joinery_id = $array[1];

		if($work_joinery_id == ""){
			$this->works_m->create_cpo_date($work_id);
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
			   	$primary_contact_person_id = $row['primary_contact_person_id'];
			   	$client_id = $row['client_id'];
				$job_type = $row['job_type'];
				$data['start_date'] = $row['date_site_commencement'];
				$data['end_date'] = $row['date_site_finish'];
				$site_address_id= $row['site_add'];
				$data['project_total'] = $row['project_total'];

				$project_estiamator_id = $row['project_estiamator_id'];
				$users_q = $this->user_model->fetch_user($project_estiamator_id);
				foreach ($users_q->result_array() as $users_row){
					$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
					$user_email_id = $users_row['user_email_id'];
					$email_q = $this->company_m->fetch_email($user_email_id);
					foreach ($email_q->result_array() as $email_row){
					   $data['email'] = $email_row['general_email'];
					}

				}

				$focus_company_id = $row['focus_company_id'];
				$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
				foreach ($focus_comp_q->result_array() as $focus_comp_row){
					$data['focus_logo'] = $focus_comp_row['logo'];
					$data['focus_comp'] = $focus_comp_row['company_name'];
					$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
					$data['acn'] = $focus_comp_row['acn'];
					$data['abn'] = $focus_comp_row['abn'];
					$data['focus_email'] = $focus_comp_row['general_email'];
					$address_id = $focus_comp_row['address_id'];
					$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
					foreach ($focus_comp_q->result_array() as $comp_address_row){
						$po_box = $comp_address_row['po_box'];
						if($po_box == ""){
							$data['po_box'] = "";
						}else{
							$data['po_box'] = "PO".$comp_address_row['po_box'];
						}
						$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
					}
				}

				if($job_type == "Shopping Center"){
			   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}	
			   	}else{
					$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}
			   	}
			   	
			}


			$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
			foreach ($comp_q->result_array() as $row){
				//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
				//$email_id = $row['email_id'];
				$comp_contact_number_id = $row['contact_number_id'];
				$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
				foreach ($comp_phone_q->result_array() as $phone_row){
					$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}
			}
			//$email_q = $this->company_m->fetch_email($email_id);
			//foreach ($email_q->result_array() as $row){
			 //  $data['email'] = $row['general_email'];
			//}
			$work_q = $this->works_m->display_works_selected($work_id);
			foreach ($work_q->result_array() as $row){
			   $company_client_id = $row['company_client_id'];
			   $price = $row['price'];
			   $data['price'] = $price;
			   $contractor_type = $row['contractor_type'];
			   $goods_deliver_by_date = $row['goods_deliver_by_date'];
			   if($goods_deliver_by_date == "" ){
			   		$data['goods_deliver_by_date'] = "";	
			   }else{
			   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
			   }

			   $note_id = $row['note_id'];
			   $note_q = $this->company_m->fetch_notes($note_id);
			   foreach ($note_q->result_array() as $note_row){
			   		$data['notes'] = $note_row['notes'];
			   }
			   if($contractor_type == 2){
			   		$data['work_desc'] = $row['job_sub_cat'];
			   }else{
			   		$data['work_desc'] = $row['supplier_cat_name'];
			   }
			   $work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_id,$company_client_id);
			   foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			   		$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			   		$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
					foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
					   $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
					   $contact_number_id = $work_cont_person_row['contact_number_id'];
					   $phon_q = $this->company_m->fetch_phone($contact_number_id);
					   foreach ($phon_q->result_array() as $phone_row){
					   		$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
					   }
					}
			   }
			}

			$comp_det_q = $this->company_m->display_company_detail_by_id($company_client_id);
			foreach ($comp_det_q->result_array() as $row){
			   $data['work_company_name'] = $row['company_name'];
			   $address_id = $row['address_id'];
			   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
			   foreach ($comp_add_q->result_array() as $comp_add_row){
			   		if($comp_add_row['unit_level'] == ""){
			   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
			   		}else{
			   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
			   		}
					$data['comp_address_2nd'] = $comp_add_row['street'];
					$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
			   }
			}


			$data['project_t'] = $proj_q;
			$data['works_t'] = $work_q;
			$data['proj_id'] = $proj_id;
			$data['work_id'] = $work_id;

			$this->works_m->update_works_contractor_cpo($work_id,$company_client_id);

			$q_admin_defaults = $this->admin_m->fetch_admin_defaults();
			foreach ($q_admin_defaults->result_array() as $row){
				$gst_rate = $row['gst_rate'];
			}
			$data['gst_rate'] = $gst_rate;
			$percent_gst_rate = $price * ($gst_rate/100);
	    	$data['inc_gst'] = $price + $percent_gst_rate;
			$this->load->view('contractor_purchase_order_nodisplay', $data);
		}else{
			$this->works_m->create_cpo_date($work_id);
			//$array = explode("-",$work_joinery_id);
			$joinery_id = $work_joinery_id;
			$this->works_m->create_joinery_cpo_date($joinery_id);
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
			   	$primary_contact_person_id = $row['primary_contact_person_id'];
			   	$client_id = $row['client_id'];
				$job_type = $row['job_type'];
				$data['start_date'] = $row['date_site_commencement'];
				$data['end_date'] = $row['date_site_finish'];
				$site_address_id= $row['site_add'];
				$data['project_total'] = $row['project_total'];

				$project_estiamator_id = $row['project_estiamator_id'];
				$users_q = $this->user_model->fetch_user($project_estiamator_id);
				foreach ($users_q->result_array() as $users_row){
					$data['contact_person'] = $users_row['user_first_name']." ".$users_row['user_last_name'];
					$user_email_id = $users_row['user_email_id'];
					$email_q = $this->company_m->fetch_email($user_email_id);
					foreach ($email_q->result_array() as $email_row){
					   $data['email'] = $email_row['general_email'];
					}

				}

				$focus_company_id = $row['focus_company_id'];
				$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
				foreach ($focus_comp_q->result_array() as $focus_comp_row){
					$data['focus_logo'] = $focus_comp_row['logo'];
					$data['focus_comp'] = $focus_comp_row['company_name'];
					$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
					$data['acn'] = $focus_comp_row['acn'];
					$data['abn'] = $focus_comp_row['abn'];
					$data['focus_email'] = $focus_comp_row['general_email'];
					$address_id = $focus_comp_row['address_id'];
					$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
					foreach ($focus_comp_q->result_array() as $comp_address_row){
						$po_box = $comp_address_row['po_box'];
						if($po_box == ""){
							$data['po_box'] = "";
						}else{
							$data['po_box'] = "PO".$comp_address_row['po_box'];
						}
						$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
					}
				}

				if($job_type == "Shopping Center"){
			   		$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}	
			   	}else{
					$site_address_q = $this->company_m->fetch_complete_detail_address($site_address_id);
					foreach ($site_address_q->result_array() as $client_add_row){
						if($client_add_row['unit_level'] == ""){
							$data['site_address_1st'] = $client_add_row['unit_number'];
						}else{
							$data['site_address_1st'] = $client_add_row['unit_level']." / ".$client_add_row['unit_number'];
						}
						$data['site_address_2nd'] = $client_add_row['street'];
						$data['site_address_3rd'] = ucfirst(strtolower($client_add_row['suburb']))." ".$client_add_row['shortname']." ".$client_add_row['postcode'];
					}
			   	}
			   	
			}


			$comp_q = $this->company_m->fetch_all_contact_persons($primary_contact_person_id);
			foreach ($comp_q->result_array() as $row){
				//$data['contact_person'] = $row['first_name']." ".$row['last_name'];
				//$email_id = $row['email_id'];
				$comp_contact_number_id = $row['contact_number_id'];
				$comp_phone_q = $this->company_m->fetch_phone($comp_contact_number_id);
				foreach ($comp_phone_q->result_array() as $phone_row){
					$data['comp_office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
				}
			}
			//$email_q = $this->company_m->fetch_email($email_id);
			//foreach ($email_q->result_array() as $row){
			 //  $data['email'] = $row['general_email'];
			//}
			$work_q = $this->works_m->display_selected_works_joinery($joinery_id);
			foreach ($work_q->result_array() as $row){
			   $company_client_id = $row['work_joinery_contractor_id'];
			   $price = $row['price'];
			   $data['price'] = $price;
			   //$contractor_type = $row['contractor_type'];
			   $goods_deliver_by_date = $row['goods_deliver_by_date'];
			   if($goods_deliver_by_date == "" ){
			   		$data['goods_deliver_by_date'] = "";	
			   }else{
			   		$data['goods_deliver_by_date'] = $row['goods_deliver_by_date'];
			   }

			   $note_id = $row['note_id'];
			   $note_q = $this->company_m->fetch_notes($note_id);
			   foreach ($note_q->result_array() as $note_row){
			   		$data['notes'] = $note_row['notes'];
			   }
			   
			   $data['work_desc'] = $row['joinery_name'];
			   
			   $work_sel_cont_q = $this->works_m->display_works_selected_contractor($work_joinery_id,$company_client_id);
			   foreach ($work_sel_cont_q->result_array() as $work_cont_row){
			   		$work_cont_contact_person_id = $work_cont_row['contact_person_id'];
			   		$work_cont_person_q = $this->company_m->fetch_all_contact_persons($work_cont_contact_person_id);
					foreach ($work_cont_person_q->result_array() as $work_cont_person_row){
					   $data['attention'] = $work_cont_person_row['first_name']." ".$work_cont_person_row['last_name'];
					   $contact_number_id = $work_cont_person_row['contact_number_id'];
					   $phon_q = $this->company_m->fetch_phone($contact_number_id);
					   foreach ($phon_q->result_array() as $phone_row){
					   		$data['office_number'] = "(".$phone_row['area_code'].") ".$phone_row['office_number'];
					   }
					}
			   }
			}
			
			$comp_det_q = $this->company_m->display_company_detail_by_id($company_client_id);
			foreach ($comp_det_q->result_array() as $row){
			   $data['work_company_name'] = $row['company_name'];
			   $address_id = $row['address_id'];
			   $comp_add_q = $this->company_m->fetch_complete_detail_address($address_id);
			   foreach ($comp_add_q->result_array() as $comp_add_row){
			   		if($comp_add_row['unit_level'] == ""){
			   			$data['comp_address_1st'] = $comp_add_row['unit_number'];
			   		}else{
			   			$data['comp_address_1st'] = $comp_add_row['unit_level']." / ".$comp_add_row['unit_number'];
			   		}
					$data['comp_address_2nd'] = $comp_add_row['street'];
					$data['comp_address_3rd'] = ucfirst(strtolower($comp_add_row['suburb']))." ".$comp_add_row['shortname']." ".$comp_add_row['postcode'];
			   }
			}


			$data['project_t'] = $proj_q;
			$data['works_t'] = $work_q;
			$data['proj_id'] = $proj_id;
			$data['work_id'] = $work_id_joinery_id;

			$this->works_m->update_works_contractor_cpo($work_id_joinery_id,$company_client_id);

			$q_admin_defaults = $this->admin_m->fetch_admin_defaults();
			foreach ($q_admin_defaults->result_array() as $row){
				$gst_rate = $row['gst_rate'];
			}
			$data['gst_rate'] = $gst_rate;
			$percent_gst_rate = $price * ($gst_rate/100);
	    	$data['inc_gst'] = $price + $percent_gst_rate;
			$this->load->view('contractor_purchase_order_nodisplay', $data);
		}
	}
	function select_work_contractor(){
		$works_contrator_id = $_POST['work_contractor_id'];
		$q_selected_contractor = $this->works_m->display_selected_contractor($works_contrator_id);
		foreach ($q_selected_contractor->result_array() as $row){
			$date_added = $row['date_added'];
			$company_id = $row['company_id'];
			$q_selected_company = $this->company_m->fetch_all_company($company_id);	
			foreach ($q_selected_company->result_array() as $comp_row){
				$company_name = $comp_row['company_name'];
			}
			$contact_person_id = $row['contact_person_id'];
			$ex_gst = $row['ex_gst'];
			$inc_gst = $row['inc_gst'];
			$is_selected = $row['is_selected'];

			echo $date_added."|".ucwords(strtolower($company_name))."|".$company_id."|".$ex_gst."|".$inc_gst."|".$is_selected;
		}
	}
	function job_date_entered(){
		$proj_id = $_POST['proj_id'];
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$job_date = $row['job_date'];
		}
		echo $job_date;
	}
	function display_work_contractor(){
		$work_id = $_POST['work_id'];
		$works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}
		if($cont_type == "Contractor"){
			$cont_type = 2;
		}else{
			$cont_type = 3;
		}

		$proj_id = $_POST['proj_id'];
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function insert_contractor(){
		$work_id = $_POST['work_id'];
	    $date_added = $_POST['date_added'];
	    $comp_id = $_POST['comp_id'];
	    $contact_person_id = $_POST['contact_person_id'];
		$works_cont_t = $this->works_m->insert_works_contractor($work_id,$date_added,$comp_id,$contact_person_id);
	    $works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}
		if($cont_type == "Contractor"){
			$cont_type = 2;
		}else{
			$cont_type = 3;
		}


		$proj_id = $_POST['proj_id'];
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function update_contractor(){
		$work_contractor_id = $_POST['work_contractor_id'];
		$work_id = $_POST['work_id'];
	    $date_added = $_POST['date_added'];
	    $comp_id = $_POST['comp_id'];
	    $contact_person_id = $_POST['contact_person_id'];
	    $inc_gst = $_POST['inc_gst'];
	    $ex_gst = $_POST['ex_gst'];
		$works_cont_t = $this->works_m->update_works_contractor($work_contractor_id,$work_id,$date_added,$comp_id,$contact_person_id,$ex_gst,$inc_gst);

	    $works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}
		if($cont_type == "Contractor"){
			$cont_type = 2;
		}else{
			$cont_type = 3;
		}

		$proj_id = $_POST['proj_id'];
		$proj_q = $this->projects_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function update_contractor_gst(){
		$works_contrator_id = $_POST['works_contrator_id'];
	    $inc_gst = $_POST['inc_gst'];
	    $ex_gst = $_POST['ex_gst'];
	    $q_selected_contractor = $this->works_m->display_selected_contractor($works_contrator_id);
		foreach ($q_selected_contractor->result_array() as $row){
			$date_added = $row['date_added'];
			$comp_id = $row['company_id'];
			$work_id = $row['works_id'];
			$date_added = $row['date_added']; 
			$contact_person_id = $row['contact_person_id'];
			$is_selected = $row['is_selected'];
		}
		$works_cont_t = $this->works_m->update_works_contractor($works_contrator_id,$work_id,$date_added,$comp_id,$contact_person_id,$ex_gst,$inc_gst);
		echo $is_selected;
	}

	function delete_contractor(){
		$work_contractor_id = $_POST['work_contractor_id'];
		$work_id = $_POST['work_id'];
		$work_id = $_POST['work_id'];
		
		$works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$data['work_estimate'] = $row['work_estimate'];
		}

		$works_cont_t = $this->works_m->display_selected_contractor($work_contractor_id);
		foreach ($works_cont_t->result_array() as $row){
			$is_selected = $row['is_selected'];
		}
		if($is_selected == 1){
?>
		<script>
			alert("Cannot delete Contractor! Contracotr is selected for a work!");
		</script>
<?php
			$works_t = $this->works_m->display_works_selected($work_id);
			foreach ($works_t->result_array() as $row){
				$cont_type = $row['contractor_type'];
			}
			if($cont_type == "Contractor"){
				$cont_type = 2;
			}else{
				$cont_type = 3;
			}

			$proj_id = $_POST['proj_id'];
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
				$data['job_date'] = $row['job_date'];
			}

			$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
			$this->load->view("work_contractor_t",$data);
		}else{
			$this->works_m->delete_works_contractor($work_contractor_id);
		    $works_t = $this->works_m->display_works_selected($work_id);
			foreach ($works_t->result_array() as $row){
				$cont_type = $row['contractor_type'];
			}
			if($cont_type == "Contractor"){
				$cont_type = 2;
			}else{
				$cont_type = 3;
			}

			$proj_id = $_POST['proj_id'];
			$proj_q = $this->projects_m->select_particular_project($proj_id);
			foreach ($proj_q->result_array() as $row){
				$data['job_date'] = $row['job_date'];
			}

			$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
			$this->load->view("work_contractor_t",$data);
		}
	}

	function fetch_gst_rate(){
		$q_admin_defaults = $this->admin_m->fetch_admin_defaults();
		foreach ($q_admin_defaults->result_array() as $row){
			$gst_rate = $row['gst_rate'];
		}
		echo $gst_rate;
	}

	function update_work(){
		$update_stat = $_POST['update_stat'];
		$work_id = $_POST['work_id'];
		$proj_id = $_POST['proj_id'];
		switch($update_stat){
			case 1:
			    $work_markup = $_POST['work_markup'];
			    $query = $this->works_m->update_works($update_stat,$work_id,"",$work_markup,"","","","","","","","","","","","","","","","","","","","","");
				echo $query;
				break;
			case 2:
			    $work_replyby_date = $_POST['work_replyby_date'];
			    $update_replyby_desc = $_POST['update_replyby_desc'];
			    $chkdeltooffice = $_POST['chkdeltooffice'];
			    $goods_deliver_by_date = $_POST['goods_deliver_by_date'];
			    $this->works_m->update_works($update_stat,$work_id,"","","",$work_replyby_date,$update_replyby_desc,$chkdeltooffice,$goods_deliver_by_date);
				break;
			case 3:
			    $update_work_notes = $_POST['update_work_notes'];
			    $this->works_m->update_works($update_stat,$work_id,"","","","","","","",$update_work_notes);
				break;
			case 4:
				$chkcons_site_inspect = $_POST['chkcons_site_inspect'];
			    $chckcons_week_work = $_POST['chckcons_week_work'];
			    $chckcons_spcl_condition = $_POST['chckcons_spcl_condition'];
			    $chckcons_weekend_work = $_POST['chckcons_weekend_work'];
			    $chckcons_addnl_visit = $_POST['chckcons_addnl_visit'];
			    $chckcons_afterhrs_work = $_POST['chckcons_afterhrs_work'];
			    $chckcons_oprte_duringinstall = $_POST['chckcons_oprte_duringinstall'];
			    $chckcons_new_premises = $_POST['chckcons_new_premises'];
			    $chckcons_free_access = $_POST['chckcons_free_access'];
			    $chckcons_others = $_POST['chckcons_others'];
			    $other_consideration = $_POST['other_consideration'];
			    $this->works_m->update_works($update_stat,$work_id,"","","","","","","","",$chkcons_site_inspect,$chckcons_week_work,$chckcons_spcl_condition,$chckcons_weekend_work,$chckcons_addnl_visit,$chckcons_afterhrs_work,$chckcons_oprte_duringinstall,$chckcons_new_premises,$chckcons_free_access,$chckcons_others,$other_consideration);
				break;
			case 5:
				$work_type = $_POST['work_type'];
      			$work_con_sup_id = $_POST['work_con_sup_id'];
      			$this->works_m->update_works($update_stat,$work_id,"","","","","","","","","","","","","","","","","","","",$work_type,$work_con_sup_id);
				break;
			case 6:
				$this->works_m->update_works($update_stat,$work_id);
				$this->works_m->remove_all_works_joinery($work_id);
				break;
			case 7:
				$price = $_POST['price'];
				$this->works_m->update_works($update_stat,$work_id,"","","","","","","","","","","","","","","","","","","","","",$price);
			case 8:
				$price = $_POST['price'];
				$quoted = $_POST['quoted'];
				$this->works_m->update_works($update_stat,$work_id,"","","","","","","","","","","","","","","","","","","","","",$price,$quoted);
		}
	}
	function select_contractor(){
		$work_id = $_POST['work_id'];
		$selected_work_contractor_id = $_POST['selected_work_contractor_id'];
		$this->works_m->set_selected_contractor($selected_work_contractor_id,$work_id );

		$works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}
		if($cont_type == "Contractor"){
			$cont_type = 2;
		}else{
			$cont_type = 3;
		}
		if(isset($_POST['all'])){
			$this->works_m->set_all_joinery_subitem_contractor($work_id,$selected_work_contractor_id);
		}

		$project_id = $_POST['proj_id'];
		$proj_q = $this->projects_m->select_particular_project($project_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_date'] = $row['job_date'];
		}

		$data['works_contructors_t'] = $this->works_m->display_work_contructor($work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function view_works_list(){
		$this->session->set_flashdata('curr_tab', 'works');
	}

	function works_total(){
		$proj_id = $_POST['proj_id'];
		$works_q = $this->works_m->display_all_works($proj_id);
		$t_price = 0;
		$t_estimate = 0;
		$t_quoted = 0;
		foreach ($works_q->result_array() as $row){
			$t_price = $t_price + $row['price'];
			$t_estimate = $t_estimate + $row['work_estimate'];
			$t_quoted = $t_quoted + $row['total_work_quote'];
		}

		$this->projects_m->update_project_cost_total($proj_id,$t_price,$t_estimate,$t_quoted);
		
		echo number_format($t_price)."/".number_format($t_estimate)."/".number_format($t_quoted);
	}

	function display_work_attachments(){
		$proj_id = $this->uri->segment(3);
		$work_id = $this->uri->segment(4);
		$works_attachment_t = $this->works_m->display_work_attachments($work_id);
		$data['works_attachment_t'] = $this->works_m->display_work_attachments($work_id);
		$data['proj_id'] = $proj_id;
		$data['work_id'] = $work_id;
		$this->load->view('table_work_attachment', $data);
	}

	function display_attachment_type_list(){

	}

	function do_upload()
	{
		$project_id = $this->uri->segment(3);
		$work_id = $this->uri->segment(4);
		$attachment_type = $_POST['attachment_type'];

	    $this->load->library('upload');

	    $files = $_FILES;
	    $cpt = count($_FILES['userfile']['name']);
	    for($i=0; $i<$cpt; $i++)
	    {
	    	$file_name =  $files['userfile']['name'][$i];
	    	$file_name = str_replace(' ', '_', $file_name);
	    	$work_attach_q = $this->works_m->display_work_attachments($work_id);
	    	$file = explode('.', $file_name);
	    	$filename = $file[0];
	    	$extension = $file[1];
	    	$file_exist = 0;
	    	foreach ($work_attach_q->result_array() as $row){
	    		$db_file_name = $row['work_attachments_url'];
	    		$db_file = explode('.', $db_file_name);
		    	$db_filename = $db_file[0];
		    	$db_extension = $db_file[1];
		    	if (strpos($db_filename, $filename) !== false) {
		    		if($extension == $db_extension){
		    			$file_exist = $file_exist + 1;
		    		}	
		    	}
	    	}

	    	if($file_exist > 0){
	    		$filename = $filename.$file_exist;
	    		$file_name = $filename.".".$extension;
	    	}

		    $_FILES['userfile']['name']= $file_name;
		    $_FILES['userfile']['type']= $files['userfile']['type'][$i];
		    $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
		    $_FILES['userfile']['error']= $files['userfile']['error'][$i];
		    $_FILES['userfile']['size']= $files['userfile']['size'][$i];    

		    $this->upload->initialize($this->set_upload_options($project_id,$work_id));
		    $this->upload->do_upload();
	    	$this->works_m->insert_work_attachments($work_id,$attachment_type,$_FILES['userfile']['name']);
	    }
	    $this->session->set_flashdata('works_curr_tab', 'attachments');
	    redirect('works/update_work_details/'.$project_id."/".$work_id);
	}
	private function set_upload_options($proj_id,$work_id)
	{   
	//  upload an image options
		$path = "./uploads/project_attachments/".$proj_id."/".$work_id;
		mkdir($path, 0755, true);
	    $config = array();
	    $config['upload_path'] = $path."/";
	    $config['allowed_types'] = '*';
	    $config['max_size']      = '0';
	    $config['overwrite']     = FALSE;


	    return $config;
	}
	function download_file(){
		$proj_id = $_POST['proj_id'];
		$work_id = $_POST['work_id'];
		$file_name = $_POST['file_name'];


		$this->load->library('ftp');

		$config['hostname'] = 'cp178.ezyreg.com';
		$config['username'] = 'soso1713';
		$config['password'] = 'f*0e^cr3';
		$config['debug']	= TRUE;

		$con = $this->ftp->connect($config);
	
		echo is_array(ftp_nlist($con, ".")) ? 'Connected!' : 'not Connected! :(';

		/*$server_path = '/public_html/uploads/project_attachments/'.$proj_id.'/'.$work_id.'/'.$file_name;
		
		$this->ftp->download($server_path, 'C:\Users\MarkO\Documents', 'ascii');

		$this->ftp->close();
		*/
		



		/*$ftp_server = "cp178.ezyreg.com";
		$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		$login = ftp_login($ftp_conn, "soso1713", "f*0e^cr3");

		$local_file = "local.zip";
		$proj_id = $_POST['proj_id'];
		$work_id = $_POST['work_id'];
		$file_name = $_POST['file_name'];
		$server_file ='/public_html/uploads/project_attachments/'.$proj_id.'/'.$work_id.'/'.$file_name;

		
		if (ftp_get($ftp_conn, $local_file, $server_file, FTP_ASCII))
		  {
		  echo "Successfully written to $local_file.";
		  }
		else
		  {
		  echo "Error downloading $server_file.";
		  }

		
		ftp_close($ftp_conn);
		*/
	} 
	function delete_attachment(){
		$work_id = $_POST['work_id'];
		$file_name = $_POST['file_name'];
		$this->works_m->remove_work_attachments($work_id,$file_name);
		$this->session->set_flashdata('works_curr_tab', 'attachments');
	}

	function edit_attachment_type(){
		$work_attachment_id = $_POST['work_attachment_id'];
    	$attach_type = $_POST['attach_type'];
    	$this->works_m->update_work_attachments($work_attachment_id,$attach_type);
    	echo $work_attachment_id."/".$attach_type;
	}

	function update_work_attachment_type(){
		$work_id = $_POST['work_id'];

		$this->works_m->delete_work_attachment_type($work_id);
		$checkboxValues =  $_POST['checkboxValues'];
		foreach($checkboxValues as $chkval){
			$this->works_m->insert_work_attachment_type($work_id,$chkval);
		}
	}

	function fetch_joinery(){
		$joinery = array();
		$joinery_q = $this->works_m->fetch_joinery();
		foreach ($joinery_q->result_array() as $row)
		{
		   array_push($joinery, $row['joinery_name']);
		}
		echo json_encode($joinery);
	}


	function update_work_joinery(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$price = $_POST['price'];
		$quoted = $_POST['quoted'];        
        
        $this->works_m->update_works_joinery_estimate($work_joinery_id,$price,$quoted);
        $work_joinery_q = $this->works_m->display_selected_works_joinery($work_joinery_id);
        foreach ($work_joinery_q->result_array() as $row)
		{
		   $works_id = $row['works_id'];
		}
		$work_joinery_q = $this->works_m->display_all_works_joinery($works_id);
		$total_estimate = 0;
		$total_quote = 0;
		foreach ($work_joinery_q->result_array() as $row)
		{
		   $total_estimate = $total_estimate + $row['j_estimate'];
		   $total_quote = $total_quote + $row['j_quote'];
		}
		$update_stat = 8;
		$this->works_m->update_works($update_stat,$works_id,"","","","","","","","","","","","","","","","","","","","","",$total_estimate,$total_quote);

		echo $total_estimate."|".$total_quote."|".$works_id;
	}

	function update_joinery_name(){
		$joinery_name = $_POST['joinery_name'];
		$work_joinery_id = $_POST['work_joinery_id'];
		$joinery_name_q = $this->works_m->verify_joinery_name($joinery_name);
		if($joinery_name_q->num_rows == 0){
			$joinery_id = $this->works_m->insert_joinery_name($joinery_name);
		}else{
			foreach ($joinery_name_q->result_array() as $row){
				$joinery_id = $row['joinery_id'];
			}
		}
		$this->works_m->update_selected_joinery_name($work_joinery_id,$joinery_id);
	}

	function is_joinery(){
		$works_id = $_POST['work_id'];
		$work_q = $this->works_m->display_works_selected($works_id);
		foreach ($work_q->result_array() as $row)
		{
		   $is_joinery = $row['work_con_sup_id'];
		}
		echo $is_joinery;
	}

	function get_joinery_work_id(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$work_q = $this->works_m->display_selected_works_joinery($work_joinery_id);
		foreach ($work_q->result_array() as $row){
			$works_id = $row['works_id'];
		}
		echo $works_id;
	}

	function unset_work_contractor(){
		$work_id = $_POST['work_id'];
		$work_q = $this->works_m->display_works_selected($work_id);
		foreach ($work_q->result_array() as $row)
		{
		   $comp_id = $row['company_client_id'];
		}
		$this->works_m->unset_selected_contractor($work_id,$comp_id);
	}

	function have_sub_item(){
		$works_id = $_POST['work_id'];
		$joinery_q = $this->works_m->display_all_works_joinery($works_id); 
		if($joinery_q->num_rows > 0){
			echo 1;
		}else{
			echo 0;
		}
	
	}
	function update_joinery_selected_subitem_price(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$unit_price = $_POST['work_joinery_unit_price'];
		$price = $_POST['t_price'];
		$qty = $_POST['joinery_qty'];
		$this->works_m->update_work_joinery_item_price($work_joinery_id,$unit_price,$price,$qty);
	}

	function update_joinery_selected_subitem_estimate(){
		$work_joinery_id = $_POST['work_joinery_id'];
      	$work_id = $_POST['work_id'];
      	$work_joinery_unit_estimated = $_POST['work_joinery_unit_estimated'];
      	$t_estimate = $_POST['t_estimate'];
      	$quoted = $_POST['quoted'];
      	$this->works_m->update_work_joinery_item_estimate($work_joinery_id,$work_id,$work_joinery_unit_estimated,$t_estimate,$quoted);
	}

	function get_joinery_totals(){
		$work_id = $_POST['work_id'];
		$work_joinery = $this->works_m->display_all_works_joinery($work_id);
      	$t_price = 0;
      	$t_estimate = 0;
      	$t_quote = 0;
      	foreach ($work_joinery->result_array() as $row)
		{
		   $t_price = $t_price + $row['j_price'];
		   $t_estimate = $t_estimate + $row['j_estimate'];
		   $t_quote = $t_quote + $row['j_quote'];
		}

		$this->works_m->update_work_joinery($work_id,$t_price,$t_estimate,$t_quote);
		echo $t_price."|".$t_estimate."|".$t_quote;
	}

	function set_joinery_subitem_contractor(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$work_id = $_POST['work_id'];
		$qty = $_POST['qty'];
		$unit_price = $_POST['unit_price'];
		$t_price = $_POST['t_price'];
		$company_id = $_POST['company_id'];
		$joinery_work_id = $_POST['joinery_work_id'];

		$work_id = $_POST['work_id'];
		$works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}

		$this->works_m->update_work_joinery_item_price($work_joinery_id,$unit_price,$t_price,$qty);
		if($company_id !== ""){
			$this->works_m->set_joinery_contractor($work_joinery_id,$work_id,$company_id,$t_price,$joinery_work_id);
		}
		$data['works_contructors_t'] = $this->works_m->display_work_contructor($joinery_work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function unset_joinery_subitem_contractor(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$work_id = $_POST['work_id'];

		$work_id = $_POST['work_id'];
		$works_t = $this->works_m->display_works_selected($work_id);
		foreach ($works_t->result_array() as $row){
			$cont_type = $row['contractor_type'];
			$data['work_estimate'] = $row['work_estimate'];
		}

		$this->works_m->unset_joinery_contractor($work_joinery_id,$work_id);
		$joinery_work_id = $work_id."-".$work_joinery_id;
		$data['works_contructors_t'] = $this->works_m->display_work_contructor($joinery_work_id);
		$this->load->view("work_contractor_t",$data);
	}

	function delete_selected_joinery_subitem(){
		$work_joinery_id = $_POST['work_joinery_id'];
		$this->works_m->delete_selected_joinery_subitem($work_joinery_id);
	}
	/*function display_supplier_and_contractor($type){
		$query = $this->company_m->display_company_by_type($type);
		foreach ($query->result() as $row){
			echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
		   //echo $row->general_address_id;   //echo $row->suburb;   //echo $row->postcode;   //echo $row->state_id;
		}
	}*/
}