<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Company extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');		
		$this->load->module('users'); 	
		$this->load->model('company_m');	
		// if($this->session->userdata('is_admin') == 1 ):		
			$this->load->module('admin');
			$this->load->model('admin_m');
		// endif;	
		if(!$this->users->_is_logged_in() ): 		
			redirect('', 'refresh');
		endif; 

		// $config = Array(
		//    'protocol' => 'smtp',
		//    'smtp_host' => 'ssl://cp178.ezyreg.com',
		//    'smtp_port' => 465,
		//    'smtp_user' => 'accounts@sojourn.focusshopfit.com.au',
		//    'smtp_pass' => 'CyMOCrP6',
		//    'mailtype'  => 'html', 
		//    'charset'   => 'iso-8859-1',
		//    'newline' => '\r\n'
		// );
		// $this->load->library('email', $config);


		if( $this->router->fetch_class() == 'company'){
    		if(isset($_GET['reload'])){
    			$comp_id = $_GET['reload'];
    			redirect('company/view/'.$comp_id, 'refresh');		
    		}
		}
	}

	public function send_emails($sender_email,$sender_name,$e_mail,$bcc_email,$subject,$message){
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
		$mail->ishtml(true);
		
		$mail->Subject = $subject;
		$mail->Body    = $message;

		if(!$mail->send()) {
			return 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
		} else {
			return 'Message has been sent';
		}
	}
	
	public function index(){
		$this->users->_check_user_access('company',1);
		$data['main_content'] = 'company_v';
		$data['new_compamy_id'] = $this->session->userdata('item');
		$data['comp_type'] = 1;
		$data['screen'] = 'Client';

		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();

		$data['page_title'] = 'Clients';

		$q_admin_default_email_message_clients = $this->admin_m->fetch_admin_default_email_message('onboarding-clients');
		$onboarding_default_clients = $q_admin_default_email_message_clients->row();

		$data['onboarding_subject_clients'] = $onboarding_default_clients->subject;
		$data['onboarding_default_message_clients'] = $onboarding_default_clients->message_content;

		$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding');
		$onboarding_default = $q_admin_default_email_message->row();

		$data['onboarding_subject'] = $onboarding_default->subject;
		$data['onboarding_default_message'] = $onboarding_default->message_content;

		$this->load->view('page', $data);
	}

	public function remove_company_onboard($comp_id){
		$declined_message = 'Removed from sojourn';
		$this->company_m->onboard_declined($comp_id, $declined_message);
		$this->company_m->delete_company($comp_id);



		// user logs
		$user_id = $this->session->userdata('user_id');
		$date = date("d/m/Y");
		$time = date("H:i:s");
		$type = "UPDATE";
		$actions = "Removed company ID:$comp_id from onboarding list";
		$this->user_model->insert_user_log($user_id,$date,$time,$actions,$project_id_review,$type);
		// user logs



		redirect('/company/onboarding');

	}
	
	public function contractor(){	
		$this->users->_check_user_access('company',1);			
		$data['main_content'] = 'company_v';
		$data['new_compamy_id'] = $this->session->userdata('item');
		$data['comp_type'] = 2;
		$data['screen'] = 'Contractor';

		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();

		$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding');
		$onboarding_default = $q_admin_default_email_message->row();

		$data['onboarding_subject'] = $onboarding_default->subject;
		$data['onboarding_default_message'] = $onboarding_default->message_content;

		$complete = 0;
		$incomplete = 0;
		$company_q = $this->company_m->display_company_by_type(2);

		foreach ($company_q->result_array() as $row){
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
			if($row['has_insurance_public_liability'] == 1){
				if($row['public_liability_expiration'] !== ""){
					if($ple_date < $today){
						$incomplete++;
					}else{
						if($row['has_insurance_workers_compensation'] == 1){
							if($row['workers_compensation_expiration'] !== ""){
								if($wce_date <= $today){
									$complete++;
								}else{
									$complete++;
								}
							}else{
								$incomplete++;
							}
						}else{
							if($row['has_insurance_income_protection'] == 1){
								if($row['income_protection_expiration'] !== ""){
									if($ipe_date <= $today){
										$incomplete++;
									}else{
										$complete++;
									}
								}else{
									$incomplete++;
								}
							}else{
								$incomplete++;
							}
						}
					}
				}else{
					$incomplete++;
				}
				
			}else{
				$incomplete++;
			}
		}
		
		$data['complete'] = $complete;
		$data['incomplete'] = $incomplete;

		$data['page_title'] = 'Contractor';

		$this->load->view('page', $data);
	}
	
	public function supplier(){	
		$this->users->_check_user_access('company',1);			
		$data['main_content'] = 'company_v';
		$data['new_compamy_id'] = $this->session->userdata('item');
		$data['comp_type'] = 3;
		$data['screen'] = 'Supplier';

		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();

		$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding');
		$onboarding_default = $q_admin_default_email_message->row();

		$data['onboarding_subject'] = $onboarding_default->subject;
		$data['onboarding_default_message'] = $onboarding_default->message_content;
		
		$data['page_title'] = 'Supplier';

		$this->load->view('page', $data);
	}

	public function update_myob_name(){
$this->clear_apost();
		$myob_name = $this->input->post('myob_name', true);
		$company_id = $this->input->post('company_id', true);




		if(isset($myob_name) && $myob_name != ''){

			$this->company_m->update_myob_name_details($myob_name,$company_id);
		} 


		redirect('/company/view/'.$company_id); 
	}
	
	public function view(){

		$curr_company_id = $this->uri->segment(3);


		$comp_type_list = $this->company_m->fetch_all_company_types();
		$data['comp_type_list'] = $comp_type_list->result();

		$suburb_list = $this->company_m->fetch_all_suburb();
		$data['suburb_list'] = $suburb_list->result();

		$all_company_list = $this->company_m->fetch_all_company();
		if($all_company_list->num_rows > 0){
			$data['all_company_list'] = $all_company_list->result();
		}else{
			$data['all_company_list'] = '';
		}

		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();


		$company_detail_q = $this->company_m->fetch_all_company($curr_company_id);			
		$company_detail = array_shift($company_detail_q->result_array());

		$data['company_name'] = $company_detail['company_name'];

		$data['abn'] = $company_detail['abn'];
		$data['acn'] = $company_detail['acn'];


$format_abn = $data['abn'];
$format_abn = trim(str_replace(' ', '', $format_abn)); 

$data['abn'] = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);
$data['acn'] = substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);

		$data['company_id'] = $company_detail['company_id'];


		$data['myob_name'] = $company_detail['myob_name'];

		//INSURANCE
		$public_liability = $company_detail['has_insurance_public_liability'];
		$data['public_liability'] = $public_liability;
		if($public_liability == 1){
			$data['pl_start_date'] = $company_detail['public_liability_start_date'];
			$data['pl_expiration'] = $company_detail['public_liability_expiration'];
		}else{
			$data['pl_start_date'] = "";
			$data['pl_expiration'] = "";
		}

		$workers_compensation = $company_detail['has_insurance_workers_compensation'];
		$data['workers_compensation'] = $workers_compensation;
		if($workers_compensation == 1){
			$data['wc_start_date'] = $company_detail['workers_compensation_start_date'];
			$data['wc_expiration'] = $company_detail['workers_compensation_expiration'];
		}else{
			$data['wc_start_date'] = "";
			$data['wc_expiration'] = "";
		}

		$income_protection = $company_detail['has_insurance_income_protection'];
		$data['income_protection'] = $income_protection;
		if($income_protection == 1){
			$data['ip_start_date'] = $company_detail['income_protection_start_date'];
			$data['ip_expiration'] = $company_detail['income_protection_expiration'];
		}else{
			$data['ip_start_date'] = "";
			$data['ip_expiration'] = "";
		}
		//INSURANCE

		$data['main_content'] = 'company_view';
		$data['screen'] = 'Company Detail';


		$bank_account_details_q = $this->company_m->fetch_bank_account_details($company_detail['bank_account_id']);			
		$bank_account_details = array_shift($bank_account_details_q->result_array());
		
		$q_client_project = $this->company_m->select_client($curr_company_id); 
		if($q_client_project->num_rows > 0){
			$data['has_project'] = 1;
		}else{
			$data['has_project'] = 0;
		}

		$data['bank_account_id'] = $bank_account_details['bank_account_id'];
		$data['bank_account_name'] = $bank_account_details['bank_account_name'];
		$data['bank_name'] = $bank_account_details['bank_name'];
		$data['bank_account_number'] = $bank_account_details['bank_account_number'];
		$data['bank_bsb_number'] = $bank_account_details['bank_bsb_number'];


		$query_address= $this->company_m->fetch_complete_detail_address($company_detail['address_id']);
		$temp_data = array_shift($query_address->result_array());
		$data['postcode'] = $temp_data['postcode'];
		$data['suburb'] = $temp_data['suburb'];
		$data['po_box'] = $temp_data['po_box'];
		$data['street'] = ucwords(strtolower($temp_data['street']));
		$data['unit_level'] = ucwords(strtolower($temp_data['unit_level']));
		$data['unit_number'] = $temp_data['unit_number'];
		$data['state'] = $temp_data['name'];
		$data['address_id'] = $company_detail['address_id'];

		$data['shortname'] = $temp_data['shortname'];
		$data['state_id'] =  $temp_data['state_id'];
		$data['phone_area_code'] = $temp_data['phone_area_code'];	

		$p_query_address = $this->company_m->fetch_complete_detail_address($company_detail['postal_address_id']);
		$p_temp_data = array_shift($p_query_address->result_array());
		$data['p_po_box'] = $p_temp_data['po_box'];
		$data['p_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
		$data['p_unit_number'] = $p_temp_data['unit_number'];
		$data['p_street'] = ucwords(strtolower($p_temp_data['street']));
		$data['p_suburb'] = $p_temp_data['suburb'];
		$data['p_state'] = $p_temp_data['name'];
		$data['p_postcode'] = $p_temp_data['postcode'];
		$data['postal_address_id'] = $company_detail['postal_address_id'];

		$data['p_shortname'] = $p_temp_data['shortname'];
		$data['p_state_id'] =  $p_temp_data['state_id'];
		$data['p_phone_area_code'] = $p_temp_data['phone_area_code'];


		$parent_company_detail_q = $this->company_m->fetch_all_company($company_detail['parent_company_id']); //parent company		
		$parent_company_detail = array_shift($parent_company_detail_q->result_array());
		$data['parent_company_name'] = $parent_company_detail['company_name'];
		$data['parent_company_id'] = $company_detail['company_id'];


		$sub_client_detail_q = $this->company_m->fetch_all_company($company_detail['sub_client_id']); //parent company		
		$sub_client_detail = array_shift($sub_client_detail_q->result_array());
		$data['sub_client_company_name'] = $sub_client_detail['company_name'];
		$data['sub_client_id'] = $sub_client_detail['company_id'];



		if($company_detail['company_type_id'] == 1){
			$company_type = 'Client';
		}else if($company_detail['company_type_id'] == 2){
			$company_type = 'Contractor';
		}else if($company_detail['company_type_id'] == 3){
			$company_type = 'Supplier';
		}else{}		

		$data['company_type'] = $company_type;
		$data['company_type_id'] = $company_detail['company_type_id'];

		$company_activity = $this->company_m->fetch_company_activity_name_by_type($company_type,$company_detail['activity_id']);
		$data['company_activity'] = ucwords($company_activity);
		$data['company_activity_id'] = $company_detail['activity_id'];

		$contact_person_company_q = $this->company_m->fetch_contact_person_company($company_detail['company_id']); //parent company		
		$contact_person_company = $contact_person_company_q->result_array();
		$data['contact_person_company'] = $contact_person_company;

		$data['notes_id'] = $company_detail['notes_id'];
		$query_notes = $this->company_m->fetch_notes($data['notes_id']);
		$temp_data = array_shift($query_notes->result_array());
		$data['comments'] = $temp_data['comments'];

		$q_company_ohs_count = $this->company_m->fetch_ohs_count($company_detail['company_id']);
		$data['company_ohs_count'] = $q_company_ohs_count;

		$q_company_ohs = $this->company_m->fetch_company_ohs($company_detail['company_id']);
		$company_ohs = array_shift($q_company_ohs->result_array());

		$data['workplace_health_safety'] = $company_ohs['workplace_health_safety'];
		$data['workplace_health_safety_notes'] = $company_ohs['workplace_health_safety_notes'];
		$data['swms'] = $company_ohs['swms'];
		$data['swms_notes'] = $company_ohs['swms_notes'];
		$data['jsa'] = $company_ohs['jsa'];
		$data['jsa_notes'] = $company_ohs['jsa_notes'];
		$data['reviewed_swms'] = $company_ohs['reviewed_swms'];
		$data['reviewed_swms_notes'] = $company_ohs['reviewed_swms_notes'];
		$data['safety_related_convictions'] = $company_ohs['safety_related_convictions'];
		$data['safety_related_convictions_details'] = $company_ohs['safety_related_convictions_details'];
		$data['safety_related_convictions_notes'] = $company_ohs['safety_related_convictions_notes'];
		$data['confirm_licences_certifications'] = $company_ohs['confirm_licences_certifications'];
		$data['confirm_licences_certifications_notes'] = $company_ohs['confirm_licences_certifications_notes'];

		$data['contact_person_company'] = $contact_person_company;

		$data['page_title'] = $data['company_name'];

		$this->load->view('page', $data);

		
	}

	public function clear_apost(){
		foreach ($_POST as $key => $value) {
			$_POST[$key] = str_replace("'","&apos;",$value);
		}
	}
	
	public function add(){

		$data['page_title'] = 'Add New Company';

		$this->users->_check_user_access('company',2);
		$data['main_content'] = 'company_add';
		$data['add'] = 1;
		$data['addIsSet'] = false;

		$this->form_validation->set_rules('company_name', 'Company Name','trim|required|xss_clean');
		$this->form_validation->set_rules('unit_level', 'Physical Unit/Level', 'trim|xss_clean');
		$this->form_validation->set_rules('unit_number', 'Physical Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street', 'Physical Street', 'trim|required|xss_clean');
		$this->form_validation->set_rules('suburb_a', 'Physical Suburb', 'trim|required|xss_clean');
		$this->form_validation->set_rules('state_a', 'Physical State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('postcode_a', 'Physical Postcode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pobox', 'PO Box', 'trim|xss_clean');
		$this->form_validation->set_rules('unit_level_b', 'Postal Unit/Level', 'trim|xss_clean');
		$this->form_validation->set_rules('number_b', 'Postal Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street_b', 'Postal Street', 'trim|xss_clean');
		$this->form_validation->set_rules('suburb_b', 'Postal Suburb', 'trim|required|xss_clean');
		$this->form_validation->set_rules('state_b', 'Postal State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('postcode_b', 'Postal Postcode', 'trim|required|xss_clean');
		$business_reg = 0;
		if(isset($_POST['business_not_registered'])):
			$business_reg = $_POST['business_not_registered'];
		endif;
		if($business_reg !== 'on'):
			$this->form_validation->set_rules('abn', 'ABN', 'trim|required|xss_clean');
			$this->form_validation->set_rules('acn', 'ACN', 'trim|required|xss_clean');
		else:
			$company_name = $_POST['company_name'];
			$street = $_POST['street'];
			$suburb_arr = explode('|', $_POST['suburb_a']);
			$phy_suburb = $suburb_arr[0];
			$state_arr = explode('|', $_POST['state_a']);
			$phy_state = $state_arr[3];
			$phy_postcode = $_POST['postcode_a']; 
			$comp_exist = $this->company_m->check_companyname_exist($company_name,$street,$phy_suburb,$phy_state,$phy_postcode);
			if($comp_exist == 1){
				$this->session->set_flashdata('duplicate_company_msg', 'Company already Exist!');
				redirect('/company');
			}

		endif;

		$this->form_validation->set_rules('activity', 'Activity', 'trim|required|xss_clean');
		$this->form_validation->set_rules('parent', 'Parent Company', 'trim|xss_clean');
		$this->form_validation->set_rules('comments', 'Comments', 'trim|xss_clean');
		$this->form_validation->set_rules('type', 'Company Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('areacode', 'Phone Areacode', 'trim|required|xss_clean');


		if(isset($_POST["type"]) && $_POST["type"]!=''){
			$this->form_validation->set_rules('account-name', 'Account Name', 'trim|xss_clean');
			$this->form_validation->set_rules('bank-name', 'Bank Name', 'trim|xss_clean');
			$this->form_validation->set_rules('account-number', 'Account Number', 'trim|xss_clean');
			$this->form_validation->set_rules('bsb-number', 'BSB Number', 'trim|xss_clean');
		}else{
			$this->form_validation->set_rules('account-name', 'Account Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('bank-name', 'Bank Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('account-number', 'Account Number', 'trim|required|xss_clean');
			$this->form_validation->set_rules('bsb-number', 'BSB Number', 'trim|required|xss_clean');
		}


		if(isset($_POST["add-contact-impt"]) && $_POST["add-contact-impt"]!=''){
			$add_contact_impt_raw = $_POST["add-contact-impt"];
			$add_contact_impt = explode(',', $add_contact_impt_raw);
			$add_contact_impt = array_filter($add_contact_impt);


			foreach ($add_contact_impt as $key => $value) {
				$this->form_validation->set_rules('contact_f_name_'.$value, 'Contact First Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('contact_l_name_'.$value, 'Contact Last Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('contact_gender_'.$value, 'Contact Gender', 'trim|required|xss_clean');
				$this->form_validation->set_rules('contact_email_'.$value, 'Contact Email', 'trim|required|xss_clean');


				if($_POST['contact_number_'.$value] == ''){
					if($_POST['mobile_number_'.$value] == '' ){
						$this->form_validation->set_rules('mobile_number_'.$value,'Mobile Number', 'trim|required|xss_clean');
					}else{
						$this->form_validation->set_rules('mobile_number_'.$value,'Mobile Number', 'trim|xss_clean');
					}
				}

				if($_POST['mobile_number_'.$value] == ''){
					if($_POST['contact_number_'.$value] == '' ){
						$this->form_validation->set_rules('contact_number_'.$value,'Office Contact Number', 'trim|required|xss_clean');
					}else{
						$this->form_validation->set_rules('contact_number_'.$value,'Office Contact Number', 'trim|xss_clean');
					}
				}

				$this->form_validation->set_rules('after_hours_'.$value,'After Hours Contact', 'trim|xss_clean');
				$this->form_validation->set_rules('contact_type_'.$value,'Contact Type', 'trim|required|xss_clean');

			}
		}



		
		//echo validation_errors();
		if($this->form_validation->run() === false){
			$this->clear_apost();
			$data['error' ] = validation_errors();
			
			$suburb_list = $this->company_m->fetch_all_suburb();		
			$comp_type_list = $this->company_m->fetch_all_company_types();
			$all_company_list = $this->company_m->fetch_all_company(NULL);
			$all_aud_states = $this->company_m->fetch_all_states();
			
			if($all_company_list->num_rows > 0){
				$data['all_company_list'] = $all_company_list->result();
			}else{
				$data['all_company_list'] = '';
			}
			
			$data['suburb_list'] = $suburb_list->result();
			$data['comp_type_list'] = $comp_type_list->result();
			$data['all_aud_states'] = $all_aud_states->result();




			$this->load->view('page', $data);

			

			//print_r($_POST);
			


			
		}else{
			$this->clear_apost();
			$data['company_name'] = $this->cap_first_word($this->if_set($this->input->post('company_name', true)));

			$data['unit_level'] = $this->if_set($this->input->post('unit_level', true));
			$data['unit_number'] = $this->if_set($this->input->post('unit_number', true));
			$data['street'] = $this->cap_first_word($this->if_set($this->input->post('street', true)));
			$data['postcode_a'] = $this->if_set($this->input->post('postcode_a', true));

			$data['pobox'] = $this->if_set($this->input->post('pobox', true));
			$data['unit_level_b'] = $this->if_set($this->input->post('unit_level_b', true));
			$data['number_b'] = $this->if_set($this->input->post('number_b', true));			
			$data['street_b'] = $this->cap_first_word($this->if_set($this->input->post('street_b', true)));
			$data['postcode_b'] = $this->if_set($this->input->post('postcode_b', true));

			$data['abn'] = $this->if_set($this->input->post('abn', true));
			$data['acn'] = $this->if_set($this->input->post('acn', true));
			$data['activity'] = $this->cap_first_word($this->if_set($this->input->post('activity', true)));

			$data['parent'] = $this->if_set($this->input->post('parent', true));
			$data['sub_client'] = $this->if_set($this->input->post('sub_client', true)); 

			$data['account-name'] = $this->cap_first_word($this->if_set($this->input->post('account-name',true)));
			$data['bank-name'] = $this->cap_first_word($this->if_set($this->input->post('bank-name',true)));
			$data['account-number'] = $this->cap_first_word($this->if_set($this->input->post('account-number',true)));
			$data['bsb-number'] = $this->cap_first_word($this->if_set($this->input->post('bsb-number',true)));


			$data['areacode'] = $this->if_set($this->input->post('areacode', true));

			$data['comments'] = $this->cap_first_word_sentence($this->if_set($this->input->post('comments', true)));
			
			$state_a_arr = explode('|', $this->input->post('state_a', true));
			$data['state_a'] = $state_a_arr[3];

			$suburb_a_ar = explode('|',$this->if_set($this->input->post('suburb_a', true)));
			$data['suburb_a'] = strtoupper($suburb_a_ar[0]);

			$state_b_arr = explode('|', $this->input->post('state_b', true));
			$data['state_b'] = $state_b_arr[3];

			$suburb_b_ar = explode('|',$this->if_set($this->input->post('suburb_b', true)));
			$data['suburb_b'] = strtoupper($suburb_b_ar[0]);



			$type_arr = explode('|', $this->input->post('type', true));

			if($type_arr[1] != ''){
				$data['company_type'] = $type_arr[1];
			}else{
				$data['company_type'] = 0;
			}


			//print_r($_POST);
			//var_dump($data);



			//if($data['comments']!=''){
				$company_notes_id = $this->company_m->insert_notes($data['comments']);
			//}else{
				//$company_notes_id = 0;
			//}
			


			$activity_id_arr = explode('|',$data['activity']);




			$general_address_id_result_a = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_a'],$data['suburb_a']);
			foreach ($general_address_id_result_a->result() as $general_address_id_a){
				$general_address_a = $general_address_id_a->general_address_id;
			}


			$general_address_id_result_b = $this->company_m->fetch_address_general_by('postcode-suburb',$data['postcode_b'],$data['suburb_b']);
			foreach ($general_address_id_result_b->result() as $general_address_id_b){
				$general_address_b = $general_address_id_b->general_address_id;
			}


			if($data['company_type']>1 ){
				$bank_account_id = $this->company_m->insert_bank_account($data['account-name'],$data['account-number'],$data['bank-name'],$data['bsb-number']);
			}else{
				$bank_account_id = 0;
			}


			$address_id = $this->company_m->insert_address_detail($data['street'],$general_address_a,$data['unit_level'],$data['unit_number']);

			$postal_address_id = $this->company_m->insert_address_detail($data['street_b'],$general_address_b,$data['unit_level_b'],$data['number_b'],$data['pobox']);


			$parent_arr_id = explode('|',$data['parent']);
			if($parent_arr_id[1]!=''){
				$parent_set_id = $parent_arr_id[1];
			}else{
				$parent_set_id = 0;
			}

			$sub_client_arr_id = explode('|',$data['sub_client']);
			if($sub_client_arr_id[1]!=''){
				$sub_client_id = $sub_client_arr_id[1];
			}else{
				$sub_client_id = 0;
			}


$format_abn = $data['abn'];
$format_abn = trim(str_replace(' ', '', $format_abn)); 

$data['abn'] = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);
$data['acn'] = substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);


			$new_company_id = $this->company_m->insert_company_details($data['company_name'],$data['abn'],$data['acn'] ,$activity_id_arr[1],$address_id,$postal_address_id,$data['company_type'],$bank_account_id,$company_notes_id,$parent_set_id,$sub_client_id);


			if(isset($_POST["add-contact-impt"]) && $_POST["add-contact-impt"]!=''){
				$add_contact_impt_raw = $_POST["add-contact-impt"];
				$add_contact_impt = explode(',', $add_contact_impt_raw);
				$add_contact_impt = array_filter($add_contact_impt);

				$data['add-contact-impt'] = $add_contact_impt;

				foreach ($add_contact_impt as $key => $value) {

					

					$assigned_contact_f_name = $this->cap_first_word($this->if_set($this->input->post('contact_f_name_'.$value,true)));   
					$assigned_contact_l_name = $this->cap_first_word($this->if_set($this->input->post('contact_l_name_'.$value,true)));   
					$assigned_contact_gender = $this->cap_first_word($this->if_set($this->input->post('contact_gender_'.$value,true)));   
					$assigned_contact_email = $this->if_set($this->input->post('contact_email_'.$value,true));   
					$assigned_contact_number = $this->cap_first_word($this->if_set($this->input->post('contact_number_'.$value,true))); 

					$assigned_contact_mobile_number = $this->cap_first_word($this->if_set($this->input->post('mobile_number_'.$value,true))); 
					$assigned_contact_after_hours = $this->cap_first_word($this->if_set($this->input->post('after_hours_'.$value,true))); 

					$assigned_contact_type = $this->cap_first_word($this->if_set($this->input->post('contact_type_'.$value,true)));

					if($this->input->post('set_as_primary_'.$value,true) == 'on'){
						$is_primary = 1;
					}else{
						$is_primary = 0;
					}

					$assigned_contact_number = str_replace(' ','',  trim($assigned_contact_number)) ;

					$assigned_contact =  substr($assigned_contact_number, 0,4).' '.substr($assigned_contact_number, 4,4).''.substr($assigned_contact_number, 8,4);

					$contact_number_id = $this->company_m->insert_contact_number($data['areacode'],$assigned_contact,'',$assigned_contact_mobile_number,$assigned_contact_after_hours);

					if($assigned_contact_email!=''){
						$email_id = $this->company_m->insert_email($assigned_contact_email);
					}else{
						$email_id = 0;
					}

					$contact_person_id = $this->company_m->insert_contact_person($assigned_contact_f_name,$assigned_contact_l_name,$assigned_contact_gender,$email_id,$contact_number_id);
					$this->company_m->insert_contact_person_company($new_company_id,$contact_person_id,$assigned_contact_type,$is_primary);

				}
			}

			// user log creation company
			$type = 'Insert';
			$actions = 'Added new '.$type_arr[0].' company, named '.$data['company_name'].'';
			date_default_timezone_set("Australia/Perth");
			$user_id = $this->session->userdata('user_id');
			$date = date("d/m/Y");
			$time = date("H:i:s");
			$this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');
			// user log creation company

			//Send E-mail================
			//$chk_send_insurance_link = "";
			//if(isset($_POST['chk_send_insurance_link'])):
				//$chk_send_insurance_link = $_POST['chk_send_insurance_link'];
			//endif;
			//if($chk_send_insurance_link == 'on'):
			if($data['company_type'] == 2):
		// //=== Focus Company Details =========
					//$focus_company_id = 5;

					//$data['focus_company_id'] = $focus_company_id;
					//$focus_comp_q = $this->admin_m->fetch_single_company_focus($focus_company_id);
					//foreach ($focus_comp_q->result_array() as $focus_comp_row){
					//	$data['focus_logo'] = $focus_comp_row['logo'];
					//	$data['focus_comp'] = $focus_comp_row['company_name'];
					//	$data['office_no'] = $focus_comp_row['area_code']." ".$focus_comp_row['office_number'];
					//	$data['acn'] = $focus_comp_row['acn'];
					//	$data['focus_abn'] = $focus_comp_row['abn'];
					//	$data['focus_email'] = $focus_comp_row['general_email'];
					//	$address_id = $focus_comp_row['address_id'];
					//	$focus_comp_q = $this->company_m->fetch_complete_detail_address($address_id);
					//	foreach ($focus_comp_q->result_array() as $comp_address_row){
					//		$po_box = $comp_address_row['po_box'];
					//		if($po_box == ""){
					//			$data['po_box'] = "";
					//		}else{
					//			$data['po_box'] = "PO".$comp_address_row['po_box'];
					//		}
					//		$data['focus_suburb'] = ucfirst(strtolower($comp_address_row['suburb']))." ".$comp_address_row['shortname']." ".$comp_address_row['postcode'];
					//	}
					//}
		//=== Focuse Company Details =========
				
						$e_mail = $assigned_contact_email;
						$default_msg_q = $this->company_m->fetch_admin_default_email_message();
						foreach ($default_msg_q->result_array() as $row){
							//$message_content = $row['message_content'];
							$sender_name = $row['sender_name'];
							$sender_email = $row['sender_email'];
							$bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au,marko@focusshopfit.com.au";
							$subject = $row['subject'];
						}
						$data['email'] = $assigned_contact_email;
						$data['contractor_id'] = $new_company_id;
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

				    	//$this->load->library('email');

				    	//$this->email->set_mailtype("html");
				    	$subject = "Insurances Required";
						$message = $this->load->view('message_view',$data,TRUE);
//++++++++++++++++++++++++++++++++++++++++++++++++++
				  //   	$this->load->library('email');

						// $this->email->from($sender_email, $sender_name);
						// $this->email->to($e_mail); 
						// //$this->email->cc($cc); 
						// $this->email->bcc($bcc_email); 

						// $this->email->subject("Insurances Required");
						// $this->email->message($message);

						// if ( ! $this->email->send())
						// {
						//     echo "E-mail Not Sent";
						// }else{
						// 	echo "E-mail Successfully Sent";
						// }
					
//+++++++++++++++++++++++++++++++++++++++++++++++++++
				$send_msg = $this->send_emails($sender_email,$sender_name,$e_mail,$bcc_email,$subject,$message);
				echo $send_msg;
			endif;
			//Send E-mail =====================


			$this->session->set_flashdata('new_company_id', $new_company_id);
			$this->session->set_flashdata('new_company_msg', 'New company is now added');
			redirect('/company');

			//redirect('/company/view/'.$new_company_insert_id);
		}
	}

	public function add_contact(){
		//var_dump($_POST);
		
		$data['contact_first_name'] = $this->input->post('first_name');
		$data['contact_last_name'] = $this->input->post('last_name');
		$data['contact_gender'] = $this->input->post('gender');
		$data['contact_email'] = $this->input->post('contact_email');
		$data['contact_contact_number'] = $this->input->post('contact_number');
		$data['contact_company'] = $this->input->post('company');
		//echo $data['contact_email'];
		$this->company_m->insert_new_contact_person($data);
	}

	public function cap_first_word($str){
		return ucwords(strtolower($str));
	}
	
	public function cap_first_word_sentence($str){
		//first we make everything lowercase, and 
		//then make the first letter if the entire string capitalized
		$str = ucfirst(strtolower($str));		
		//now capitalize every letter after a . ? and ! followed by space
		$str = preg_replace_callback('/[.!?] .*?\w/', create_function('$matches', 'return strtoupper($matches[0]);'), $str);		
		//print the result
		return $str;
	}

	public function if_set($val){
		//echo $val.'<br />';
		if(isset($val)){
			return ascii_to_entities($val);
		}else{
			return NULL;
		}
	}

	public function suburb_list($wrap=''){
		$suburb_list = $this->company_m->fetch_all_suburb();
		
		if($wrap == 'dropdown'){
			foreach ($suburb_list->result() as $row){
				echo '<option value="'.$row->suburb.'|'.$row->name.'|'.$row->phone_area_code.'">'.ucwords(strtolower($row->suburb)).'</option>';
			   //echo $row->general_address_id;   //echo $row->suburb;   //echo $row->postcode;   //echo $row->state_id;
			}
		}else if($wrap == 'list'){
			foreach ($suburb_list->result() as $row){
				echo '<li>'.$row->suburb.'</li>';
			}
		}else{
			foreach ($suburb_list->result() as $row){
				echo '<div>'.$row->suburb.'</div>';
			}		
		}
	}
	
	public function contact_person_list(){
		$contact_person_list = $this->company_m->fetch_all_contact_persons('');
		
		foreach ($contact_person_list->result() as $row){
			echo '<option value="'.$row->first_name.'|'.$row->last_name.'|'.$row->contact_person_id.'">'.$row->first_name.' '.$row->last_name.'</option>';
		}		
	}
	
	public function company_list($wrap=''){
		$comp_list = $this->company_m->fetch_all_company_type_id('1');
		
		if($wrap == 'dropdown'){
			foreach ($comp_list->result() as $row){
				echo '<option value="'.$row->company_name.'|'.$row->company_id.'">'.ucwords(strtolower($row->company_name)).'</option>';
			   //echo $row->general_address_id;   //echo $row->suburb;   //echo $row->postcode;   //echo $row->state_id;
			}
		}else if($wrap == 'list'){
			foreach ($comp_list->result() as $row){
				echo '<li>'.$row->company_name.'</li>';
			}
		}else{
			foreach ($comp_list->result() as $row){
				echo '<div>'.$row->company_name.'</div>';
			}		
		}
	}

      public function add_new_contact_dynamic(){
      	$data_val = explode('|',$_POST['ajax_var']);


      	$assigned_contact_number = $data_val[4];

      	$assigned_contact_number = str_replace(' ','',  trim($assigned_contact_number)) ;

      	$assigned_contact =  substr($assigned_contact_number, 0,4).' '.substr($assigned_contact_number, 4,4).''.substr($assigned_contact_number, 8,4);



      	$contact_number_id = $this->company_m->insert_contact_number($data_val[8],$assigned_contact,'',$data_val[6],$data_val[5]);
      	$email_id = $this->company_m->insert_email($data_val[7]);
		$contact_person_id = $this->company_m->insert_contact_person($data_val[0],$data_val[1],$data_val[2],$email_id,$contact_number_id);
		$this->company_m->insert_contact_person_company($data_val[9],$contact_person_id,$data_val[3],'0');


      }
 



	// this was for the admin company ajax edit func
	public function update_abn_acn_jurisdiction(){
		$this->clear_apost();
		$data_val = explode('_',$_POST['ajax_var']);

		$jurisdiction_raw = explode(',',$data_val[2]);
		$jurisdiction = '';
		foreach ($jurisdiction_raw as $key => $value) {
			$jur_arr = explode('|', $value);
			$jurisdiction .= $jur_arr[3].',';
		}
		$jurisdiction = substr($jurisdiction, 0, -1);

		$format_abn = $data_val[0];
		$format_abn = trim(str_replace(' ', '', $format_abn)); 

		$data_val[0] = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);
		$data_val[1] = substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);

		$this->admin_m->update_abn_acn_jurisdiction($data_val[0],$data_val[1],$jurisdiction,$data_val[3]);		
	}


	public function updat_admin_contact_email(){
		$data_val = explode('|',$_POST['ajax_var']);
		$this->admin_m->updat_admin_contact_email($data_val[0],$data_val[1],$data_val[2],$data_val[3],$data_val[4]);	
	}

	// this was for the admin company ajax edit func


	

	public function delete_person_contact(){
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->delete_contact_person($data_val[0]);
	}

	public function delete_company(){
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->delete_company($data_val[0]);

		// user log creation company
		$type = 'Delete';
		$actions = 'Delete company, named '.$data['company_name'].'';
		date_default_timezone_set("Australia/Perth");
		$user_id = $this->session->userdata('user_id');
		$date = date("d/m/Y");
		$time = date("H:i:s");
		$this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');
		// user log creation company

		redirect('/company');
	}

	public function update_name_company(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_company_name($data_val[0],$data_val[1]);
	}
/*
	public function update_details_address(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_address_details($data_val[0],$data_val[1],$data_val[2],$data_val[3],$data_val[4],$data_val[5],$data_val[6]);
	}
*/

	public function update_p_address(){
		$this->clear_apost();
		$poBox = '';
		$data_val = explode('|',$_POST['ajax_var']);

//  var data = phys_address_id+'|'+number+'|'+unit_level+'|'+street+'|'+suburb_a+'|'+postcode_a+'|'+state_id;


		$suburb = strtoupper($data_val[4]);
		$postcode = $data_val[5];
		$state_id = $data_val[6];
		$poBox = $data_val[7];


		$q_general_address_id = $this->company_m->get_gen_add_id($suburb,$postcode,$state_id);
		$address_gnerl = array_shift($q_general_address_id->result_array());
		$general_address_id = $address_gnerl['general_address_id'];


	 // update_address_details($address_detail_id,$unit_number,$unit_level,$street,$general_address_id,$pobox='')
		// var data = postal_address_id_data+'|'+p_number+'|'+p_unit_level+'|'+p_street+'|'+suburb_b+'|'+postcode_b+'|'+state_id+'|'+po_box;

 	$this->company_m->update_address_details($data_val[0],$data_val[1],$data_val[2],$data_val[3],$general_address_id,$poBox);


	}



	public function update_bank_details_account(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_bank_account_details($data_val[0],$data_val[1],$data_val[2],$data_val[3],$data_val[4]);
	}

	public function update_details_other(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_other_details($data_val[0],$data_val[1],$data_val[2],$data_val[3],$data_val[4],$data_val[5],$data_val[6]);

		// user log update company other detail
		$type = 'Update';
		$actions = 'Changed company other details Company Type:'.$data_val[2].' Company ID:'.$data_val[5].'';
		date_default_timezone_set("Australia/Perth");
		$user_id = $this->session->userdata('user_id');
		$date = date("d/m/Y");
		$time = date("H:i:s");
		$this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');
		// user log update company other detail
	}

	public function update_comments_notes(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		//== Added Function if Company Notes ID is 0 -- Edited Mark
		if($data_val[0] == 0 || $data_val[0] == '0'){
			$notes_id = $this->company_m->insert_notes($data_val[1]);
			$this->company_m->update_company_notes_id($data_val[2],$notes_id);
		}else{
		//== Added Function if Company Notes ID is 0 -- Edited Mark
			$this->company_m->update_notes_comments($data_val[0],$data_val[1]);
		}
	}


	public function update_person_contact(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);

		$format_a = str_replace(' ', '', $data_val[4]);
		$data_val[4] = substr($format_a,0,4)." ".substr($format_a,4,4)." ".substr($format_a,8,4)." ".substr($format_a,12,4);

		$format_b = str_replace(' ', '', $data_val[6]);
		$data_val[6] = substr($format_b,0,4)." ".substr($format_b,4,4)." ".substr($format_b,8,4)." ".substr($format_b,12,4);
		
		$this->company_m->update_contact_person($data_val[0],$data_val[1],$data_val[2],$data_val[3],$data_val[4],$data_val[5],$data_val[6],$data_val[7],$data_val[8],$data_val[9],$data_val[10],$data_val[11]);
	}

	public function update_contact_primary(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_primary_contact($data_val[0],$data_val[1],$data_val[2]);
	}

	public function update_workplace_health_safety(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_workplace_health_safety($data_val[0],$data_val[1],$data_val[2]);
	}
	
	public function update_swms(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_swms($data_val[0],$data_val[1],$data_val[2]);
	}

	public function update_jsa(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_jsa($data_val[0],$data_val[1],$data_val[2]);
	}

	public function update_reviewed_swms(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_reviewed_swms($data_val[0],$data_val[1],$data_val[2]);
	}

	public function update_safety_related_convictions(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_safety_related_convictions($data_val[0],$data_val[1],$data_val[2]);
	}

	public function update_confirm_licences_certifications(){
		$this->clear_apost();
		$data_val = explode('|',$_POST['ajax_var']);
		$this->company_m->update_confirm_licences_certifications($data_val[0],$data_val[1],$data_val[2]);
	}

	public function state_list($wrap=''){
		$states_list = $this->company_m->fetch_all_states();
		
		if($wrap == 'dropdown'){
			foreach ($states_list->result() as $row){
				echo '<option value="'.$row->id.'">'.ucwords(strtolower($row->name)).'</option>';
			   //echo $row->general_address_id;   //echo $row->suburb;   //echo $row->postcode;   //echo $row->state_id;
			}
		}else if($wrap == 'list'){
			foreach ($states_list->result() as $row){
				echo '<li>'.$row->name.'</li>';
			}
		}else{
			foreach ($states_list->result() as $row){
				echo '<div>'.$row->name.'</div>';
			}		
		}
	}

	public function get_suburb_list($data_post=''){
		if(isset( $_POST['ajax_var'] ) && $_POST['ajax_var']!=''){
			$data_raw = explode('|',$this->security->xss_clean($this->input->post('ajax_var')));
			$state_id = $data_raw[0];
			$wrap = $data_raw[1];
			$selector = $data_raw[2];
			$state = $data_raw[3];
			$phone_area_code = $data_raw[4];
		}else{
			$data_raw = explode('|',$this->security->xss_clean($data_post));
			$state_id = $data_raw[5];
			$wrap = $data_raw[0];
			$selector = $data_raw[1];
			$state = $data_raw[3];
			$phone_area_code = $data_raw[4];
		}
		
		$suburb_list = $this->company_m->fetch_address_general_by($selector,$state_id);
		
		if($wrap == 'dropdown'){

				echo '<option value="">Choose a Suburb...</option>';
			foreach ($suburb_list->result() as $row){

				echo '<option value="'.$row->suburb.'|'.$state.'|'.$phone_area_code.'">'.ucwords(strtolower($row->suburb)).'</option>';
			   //echo $row->general_address_id;   //echo $row->suburb;   //echo $row->postcode;   //echo $row->state_id;
			}
		}else if($wrap == 'list'){
			foreach ($suburb_list->result() as $row){
				echo '<li>'.$row->suburb.'</li>';
			}
		}else{
			foreach ($suburb_list->result() as $row){
				echo '<div>'.$row->suburb.'</div>';
			}		
		}
	}

	public function get_post_code_list($data_post=''){

		if(isset( $_POST['ajax_var'] ) && $_POST['ajax_var']!=''){
			$data_raw = $this->security->xss_clean($this->input->post('ajax_var'));
			$suburb_value =  $data_raw;
		}else{
			$suburb_value =  $data_post;
		}

		$post_code_list = $this->company_m->fetch_postcode($suburb_value);

		echo '<option value="">Choose a Postcode...</option>';
		

		foreach ($post_code_list->result() as $row){
			echo '<option value="'.$row->postcode.'">'.ucwords(strtolower($row->postcode)).'</option>';
		}
	}
	
	public function get_list_view(){
		$suburb_value =  $this->input->post('suburb');
		$state_value =  $this->input->post('state');
		$phonecode_value =  $this->input->post('phonecode');
		$post_code_list = $this->company_m->fetch_postcode($suburb_value);
		$counter=0;

		if($post_code_list->num_rows()>1){
			$post_code_items = '<option value="">Choose a Postcode...</option>';
		}
		foreach ($post_code_list->result() as $row){
			$post_code_items .= '<option value="'.$row->postcode.'">'.ucwords(strtolower($row->postcode)).'</option>';
			$counter++;
		}
		
		
		echo $post_code_items.'|'.$state_value.'|'.$phonecode_value;
	}
	
	public function set_suburb($value){		
		$suburb = explode('|',$value);
		return ucwords(strtolower($suburb[0]));
	}
	
	public function display_company_by_type($type){
		$data['com_c'] = $this->company_m->display_company_by_type($type);
		$this->load->view('tables_client',$data);
	}

	public function fetch_contacts($company_id){
		$q_contact_details = $this->company_m->fetch_contact_details_primary($company_id);


		$contact = array_shift($q_contact_details->result_array());
		echo '<td>';
		echo ($contact['office_number'] != ''? $contact['area_code'].' '.$contact['office_number'] : '');
		echo '</td><td><a href="mailto:'.strtolower($contact['general_email']).'?Subject=Inquiry" >'.strtolower($contact['general_email']).'</a></td>';


//echo $company_id.'xxxx';



/*
function_get_contact_details($company_id)

*/

	}
	
	public function donut_cart_companies(){
		$data['com_q'] = $this->company_m->count_company_by_type();
		$this->load->view('chart',$data);
	}
	
	public function activity($parent_type = ''){

		if($parent_type!=''){
			$activity_value = $parent_type;
		}else{
			$activity_value = $this->security->xss_clean($this->input->post('ajax_var'));
		}

		

		if($activity_value=='Client'){
			$query = $this->company_m->fetch_all_client_types();
			//var_dump($query);	
			//$all_client_list = '';	
			//$counter = 0;
			//echo '<option value="">Choose a Activity...</option>';
			foreach ($query->result() as $row){
				echo '<option value="'.ucwords(strtolower($row->client_category_name)).'|'.$row->client_category_id.'">'.ucwords(strtolower($row->client_category_name)).'</option>';				
			}
		}else if($activity_value=='Contractor'){
			$query = $this->company_m->fetch_all_contractor_types();
			//echo '<option value="">Choose a Activity...</option>';
			foreach ($query->result() as $row){
				echo '<option value="'.ucwords(strtolower($row->job_category)).'|'.$row->job_category_id.'">'.ucwords(strtolower($row->job_category)).'</option>';				
			}
		}else if($activity_value=='Supplier'){
			$query = $this->company_m->fetch_all_supplier_types();
			//echo '<option value="">Choose a Activity...</option>';
			foreach ($query->result() as $row){
				echo '<option value="'.ucwords(strtolower($row->supplier_cat_name)).'|'.$row->supplier_cat_id.'">'.ucwords(strtolower($row->supplier_cat_name)).'</option>';				
			}
		}else{
			//echo '<option value="">Choose a Activity...</option>';
		}
	}




	public function company_by_type($type_id=''){
		if($type_id!=''){
			$type_value = $type_id;
		}else{
			$type_value = $this->security->xss_clean($this->input->post('ajax_var'));
		}

		$query = $this->company_m->fetch_all_company_type_id($type_value);
		//var_dump($query);	
		//$all_client_list = '';	
		//$counter = 0;
		echo '<option value="">Choose a Parent...</option>';
		foreach ($query->result() as $row){
			echo '<option value="'.ucwords(strtolower($row->company_name)).'|'.$row->company_id.'">'.ucwords(strtolower($row->company_name)).'</option>';				
		}
		

	}
	public function works_company_by_type($type_id=''){
		if($type_id!=''){
			$type_value = $type_id;
		}else{
			$type_value = $this->security->xss_clean($this->input->post('ajax_var'));
		}

		$query = $this->company_m->fetch_all_company_type_id($type_value);
		//var_dump($query);	
		//$all_client_list = '';	
		//$counter = 0;
		foreach ($query->result() as $row){
			echo '<option value="'.ucwords(strtolower($row->company_name)).'|'.$row->company_id.'">'.ucwords(strtolower($row->company_name)).'</option>';				
		}
		

	}

	// public function upload_insurance(){
	// 	$comp_id = $_POST['company_id'];
	// 	$insurance_type = $_POST['insurance_type'];
	// 	$expiration_date = $_POST['attach_expiration'];



	//     if($insurance_type == 1){
	//     	$filename = $comp_id."_Public_Liability" ;
	//     }else{
	//     	if($insurance_type == 2){
	//     		$filename = $comp_id."_Workers_Compesation" ;
	//     	}else{
	//     		$filename = $comp_id."_Income Protection" ;
	//     	}
	//     }


	// 	$config['upload_path'] = './uploads/company/insurance';
	// 	$config['allowed_types'] = 'gif|jpg|png';
	// 	$config['max_size']	= '1024';
	// 	$config['max_width']  = '1024';
	// 	$config['max_height']  = '768';

	// 	$config['file_name']  = $filename;

	// 	$this->upload->initialize($config);

	// 	$this->load->library('upload', $config);
	// 	$this->upload->initialize($config);	



	
	// 		if ( ! $this->upload->do_upload()){
	// 			$upload_error = array('error' => $this->upload->display_errors());
	// 			$upload_has_error = 1;
	// 		}else{
	// 			
	// 			redirect('/company/view/'.$comp_id);
	// 			// $up_data = array('upload_data' => $this->upload->data());
	// 			// $logo = $up_data['upload_data']['file_name'];
	// 		}
	 
	// }

	function upload_insurance()
	{
		$comp_id = $_POST['company_id'];
		$insurance_type = $_POST['insurance_type'];
		$expiration_date = $_POST['attach_expiration'];

	    $this->load->library('upload');

	    $files = $_FILES;
	    $cpt = count($_FILES['userfile']['name']);
	    for($i=0; $i<$cpt; $i++)
	    {
	    	$file_name =  $files['userfile']['name'][$i];
	    	//$file_name = str_replace(' ', '_', $file_name);
	    	//$proj_attach_q = $this->attachments_m->display_selected_project_attachments($project_id);
	    	// $file = explode('.', $file_name);
	    	// $filename = $file[0];
	    	// $extension = $file[1];
	    	// $file_exist = 0;
	    	
		    // if (strpos($filename) !== false) {
		    // 		$file_exist = $file_exist + 1;
		    // }
	    	// //}

	    	// if($file_exist > 0){
	    	// 	$filename = $filename.$file_exist;
	    	// 	$file_name = $filename.".".$extension;
	    	// }

	    	if($insurance_type == 1){
		    	$filename = $comp_id."_Public_Liability" ;
		    }else{
		    	if($insurance_type == 2){
		    		$filename = $comp_id."_Workers_Compensation" ;
		    	}else{
		    		$filename = $comp_id."_Income Protection" ;
		    	}
		    }

		    $_FILES['userfile']['name']= $filename.'.'.'pdf';
		    $_FILES['userfile']['type']= $files['userfile']['type'][$i];
		    $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
		    $_FILES['userfile']['error']= $files['userfile']['error'][$i];
		    $_FILES['userfile']['size']= $files['userfile']['size'][$i];    

		    $this->upload->initialize($this->set_upload_options($comp_id));
		    $this->upload->do_upload();
	    	$this->company_m->update_company_details_insurance($comp_id,$insurance_type,$expiration_date);
	    }
	   	redirect('/company/view/'.$comp_id);
	}
	private function set_upload_options($comp_id)
	{   
	//  upload an image options
		$path = "./uploads/company/insurance/".$comp_id;
		mkdir($path, 0755, true);
	    $config = array();
	    $config['upload_path'] = $path."/";
	    $config['allowed_types'] = 'pdf';
	    $config['max_size']      = '0';
	    $config['overwrite']     = TRUE;


	    return $config;
	}

	public function update_insurance_exp_date(){
		$comp_id = $_POST['comp_id'];
		$insurance_type = $_POST['insurance_type'];
		$expiration_date = $_POST['expiration'];
		$this->company_m->update_company_details_insurance($comp_id,$insurance_type,$expiration_date);
	}

	public function filter_contractor_list(){
		$filter = $_POST['filter'];
		$company_q = $this->company_m->display_company_by_type(2);
		
		switch($filter){
			case 1:	
				echo '<table class = "table table-striped table-bordered" width = 100%><th>Contractor Name</th><th>Contact Numbers</th><th>E-mail</th><th>Public Liability</th><th>Workers Compensation</th><th>Income Protection</th>';
				foreach ($company_q->result_array() as $row){

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


					$public_liability_expiration = "";
					$workers_compensation_expiration = "";
					$income_protection_expiration = "";
					if($row['has_insurance_public_liability'] == 1){
						$public_liability_expiration = $row['public_liability_expiration'];
					}else{
						$public_liability_expiration = "No file";
					}
					if($row['has_insurance_workers_compensation'] == 1){
						$workers_compensation_expiration = $row['workers_compensation_expiration'];
					}else{
						$workers_compensation_expiration = "No file";
					}
					if($row['has_insurance_income_protection'] == 1){
						$income_protection_expiration = $row['income_protection_expiration'];
					}else{
						$income_protection_expiration = "No file";
					}

					//---------------
					if($row['has_insurance_public_liability'] == 1){
						if($row['public_liability_expiration'] !== ""){
							if($ple_date > $today){
								if($row['has_insurance_workers_compensation'] == 1){
									if($row['workers_compensation_expiration'] !== ""){
										if($wce_date > $today){
											echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
										}
									}
								}else{
									if($row['has_insurance_income_protection'] == 1){
										if($row['income_protection_expiration'] !== ""){
											if($ipe_date > $today){
												echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
											}
										}
									}
								}
							}
						}
						
					}
				}
				echo '</table>';
				break;
			case 2:

				echo '<table class = "table table-striped table-bordered" width = 100%><th>Contractor Name</th><th>Contact Numbers</th><th>E-mail</th><th>Public Liability</th><th>Workers Compensation</th><th>Income Protection</th>';
				foreach ($company_q->result_array() as $row){

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

					$public_liability_expiration = "";
					$workers_compensation_expiration = "";
					$income_protection_expiration = "";
					if($row['has_insurance_public_liability'] == 1){
						$public_liability_expiration = $row['public_liability_expiration'];
					}else{
						$public_liability_expiration = "No file";
					}
					if($row['has_insurance_workers_compensation'] == 1){
						$workers_compensation_expiration = $row['workers_compensation_expiration'];
					}else{
						$workers_compensation_expiration = "No file";
					}
					if($row['has_insurance_income_protection'] == 1){
						$income_protection_expiration = $row['income_protection_expiration'];
					}else{
						$income_protection_expiration = "No file";
					}

					//---------------
					if($row['has_insurance_public_liability'] == 1){
						if($row['public_liability_expiration'] !== ""){
							if($ple_date <= $today){
								echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
							}else{
								if($row['has_insurance_workers_compensation'] == 1){
									if($row['workers_compensation_expiration'] !== ""){
										if($wce_date <= $today){
											echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
										}
									}else{
										echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
									}
								}else{
									if($row['has_insurance_income_protection'] == 1){
										if($row['income_protection_expiration'] !== ""){
											if($ipe_date <= $today){
												echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
											}
										}else{
											echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
										}
									}else{
										echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
									}
								}
							}
						}else{
							echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
						}
						
					}else{
						echo '<tr><td colspan =>'.$row['company_name'].'</td><td>('.$row['area_code'].') '.$row['office_number'].'</td><td>'.strtolower($row['general_email']).'</td><td>'.$public_liability_expiration.'</td><td>'.$workers_compensation_expiration.'</td><td>'.$income_protection_expiration.'</td></tr>';
					}
				}
				echo '</table>';
				break;
		
		}
	}

	public function if_insurance_not_exist(){
		$comp_id = $_POST['comp_id'];
		$insurance_type = $_POST['ins_stat'];
		$this->company_m->remove_company_insurance($comp_id,$insurance_type);
	}

	public function check_company_exist(){

		$abn = $_POST['abn'];
		$type = $_POST['type'];
		$is_admin = $this->session->userdata('is_admin');

		$format_abn = $abn;
		$format_abn = trim(str_replace(' ', '', $format_abn)); 

		$abn = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);

		$exist = $this->company_m->check_company_exist($abn,$type);

		echo $exist;
	}

	public function check_company_exist_edit(){

		$company_id = $_POST['company_id'];
		$abn = $_POST['abn'];
		$type = $_POST['type'];
		$is_admin = $this->session->userdata('is_admin');
		
		$format_abn = $abn;
		$format_abn = trim(str_replace(' ', '', $format_abn)); 

		$abn = substr($format_abn,0,2)." ".substr($format_abn,2,3)." ".substr($format_abn,5,3)." ".substr($format_abn,8,3);

		$exist = $this->company_m->check_company_exist_edit($abn,$type,$company_id);

		echo $exist;
	}

	public function check_company_type(){
		$comp_id = $_POST['comp_id'];
		$comp_type = "";
		$comp_q = $this->company_m->fetch_company_details($comp_id);
		foreach ($comp_q->result_array() as $row){
			$comp_type = $row['company_type_id'];
		}
		echo $comp_type;
	}

	public function check_for_expired_insurance(){
		$comp_q = $this->company_m->fetch_all_company_details_active();
		$date = date('Y-m-d');
		$expired = 0;
		$all_insurance = 0;
		foreach ($comp_q->result_array() as $row){
			//$send_status = $row['email_send_status'];
			$insurance_expire = 0;
			if($row['pl_email_stat'] == 0){
				$has_insurance_public_liability = $row['has_insurance_public_liability'];
				if($has_insurance_public_liability == 1){
					$public_liability_expiration = $row['public_liability_expiration'];
					$ple_arr =  explode('/',$public_liability_expiration);
					$ple_day = $ple_arr[0];
					$ple_month = $ple_arr[1];
					$ple_year = $ple_arr[2];
					$ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
					$ple_month_before = date('Y-m-d', strtotime($ple_date. ' - 28 days'));
					if($date >= $ple_month_before){
						$insurance_expire = 1;
					}
				}
			}

			if($row['wc_email_stat'] == 0){
				$has_insurance_workers_compensation = $row['has_insurance_workers_compensation'];
				if($has_insurance_workers_compensation == 1){
					$workers_compensation_expiration = $row['workers_compensation_expiration'];
					$wce_arr =  explode('/',$workers_compensation_expiration);
					$wce_day = $wce_arr[0];
					$wce_month = $wce_arr[1];
					$wce_year = $wce_arr[2];
					$wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
					$wce_month_before = date('Y-m-d', strtotime($wce_date. ' - 28 days'));
					if($date >= $wce_month_before){
						$insurance_expire = 1;
					}
				}
			}

			if($row['email_send_status'] == 0){
				$has_insurance_income_protection = $row['has_insurance_income_protection'];
				if($has_insurance_income_protection == 1){

					if(isset($ipe_arr[1])){

						
						$income_protection_expiration = $row['income_protection_expiration'];
						$ipe_arr =  explode('/',$income_protection_expiration);
						$ipe_day = $ipe_arr[0];

						$ipe_month = $ipe_arr[1];
						$ipe_year = $ipe_arr[2];

						$ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
						$ipe_month_before = date('Y-m-d', strtotime($ipe_date. ' - 28 days'));
						if($date >= $ipe_month_before){
							$insurance_expire = 1;
						}	



					}


				}
	
			}

			if($insurance_expire == 1){
				$expired++;
			}
			
		}

		return $expired;
	}

	public function autosend_email_expired_insurance(){
		$this->load->module('admin_m');

		$comp_q = $this->company_m->fetch_all_company_details();
		$date = date('Y-m-d');
		
		foreach ($comp_q->result_array() as $row){
			$insurances = array();
			$expired = 0;
			//$send_status = $row['email_send_status'];
			$pl_insurance = 0;
			$wc_insurance = 0;
			$ip_insurance = 0; 

			$company_id = $row['company_id'];
			if($row['pl_email_stat'] == 0){
				$has_insurance_public_liability = $row['has_insurance_public_liability'];
				if($has_insurance_public_liability == 1){
					$public_liability_expiration = $row['public_liability_expiration'];
					$ple_arr =  explode('/',$public_liability_expiration);
					$ple_day = $ple_arr[0];
					$ple_month = $ple_arr[1];
					$ple_year = $ple_arr[2];
					$ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
					$ple_month_before = date('Y-m-d', strtotime($ple_date. ' - 28 days'));
					if($date >= $ple_month_before){
						$expired++;
						array_push($insurances,'public_liability');
						$pl_insurance = 1;
					}
				}
			}

			if($row['wc_email_stat'] == 0){	
				$has_insurance_workers_compensation = $row['has_insurance_workers_compensation'];
				if($has_insurance_workers_compensation == 1){
					$workers_compensation_expiration = $row['workers_compensation_expiration'];
					$wce_arr =  explode('/',$workers_compensation_expiration);
					$wce_day = $wce_arr[0];
					$wce_month = $wce_arr[1];
					$wce_year = $wce_arr[2];
					$wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
					$wce_month_before = date('Y-m-d', strtotime($wce_date. ' - 28 days'));
					if($date >= $wce_month_before){
						$expired++;
						array_push($insurances,'workers_compensation');
						$wc_insurance = 1;
					}
				}
			}

			if($row['email_send_status'] == 0){	
				$has_insurance_income_protection = $row['has_insurance_income_protection'];
				if($has_insurance_income_protection == 1){
					$income_protection_expiration = $row['income_protection_expiration'];
					$ipe_arr =  explode('/',$income_protection_expiration);
					$ipe_day = $ipe_arr[0];
					$ipe_month = $ipe_arr[1];
					$ipe_year = $ipe_arr[2];
					$ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
					$ipe_month_before = date('Y-m-d', strtotime($ipe_date. ' - 28 days'));
					if($date >= $ipe_month_before){
						$expired++;
						array_push($insurances,'income_protection');
						$ip_insurance = 1;
					}	
				}
			}

			if($expired > 0){

				$data['contractor_id'] = $company_id;
				$contractor_q = $this->company_m->fetch_contact_details_primary($company_id);
				foreach ($contractor_q->result_array() as $row){
					$e_mail = $row['general_email'];
					$data['email'] = $e_mail;
					//$contact_person_id = $row['contact_person_id'];
					// $comp_q = $this->company_m->fetch_all_contact_persons($contact_person_id);
					// foreach ($comp_q->result_array() as $cont_row){
					// 	$email_id = $cont_row['email_id'];
					// 	$email_q = $this->company_m->fetch_email($email_id);
					// 	foreach ($email_q->result_array() as $email_row){
					// 		$e_mail = $email_row['general_email'];
					// 		$data['email'] = $e_mail;
					// 	}
					// }
				}

				$user_query = $this->admin_m->fetch_admin_default_email_message();
				foreach ($user_query->result_array() as $row){
				    $user_id = $row['user_id'];
				    $message_content = $row['message_content'];
				    $sender_name = $row['sender_name'];
				    $sender_email = $row['sender_email'];
				    $bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au";

				}
				
				// $message_content = $this->session->userdata('message_content');
	   //          $sender_name = $this->session->userdata('sender_name');
	   //          $sender_email = $this->session->userdata('sender_email');
	   //          $bcc_email = $this->session->userdata('bcc_email');

				// $default_msg_q = $this->admin_m->fetch_admin_default_email_message();
				// foreach ($default_msg_q->result_array() as $row){
				// 	$message_content = $row['message_content'];
				// 	$sender_name = $row['sender_name'];
				// 	$sender_email = $row['sender_email'];
				// 	$bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au";
				// 	//$subject = $row['subject'];
				// }

				//$data['message'] = $message_content;

	            $data['insurances'] = $insurances;

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

		    	// $this->load->library('email');

		    	// $this->email->set_mailtype("html");
		    	$subject = 'Insurance is About to Expire';
				$message = $this->load->view('expired_insurance_message_view',$data,TRUE);

		    	//$this->load->library('email');
//+++++++++++++++++++++++++++++++++++++
				// $this->email->from($sender_email, $sender_name);
				// $this->email->to($e_mail); 
				// //$this->email->cc($cc); 
				// $this->email->bcc($bcc_email); 

				// $this->email->subject('Insurance is About to Expire');
				// $this->email->message($message);

				// if ( ! $this->email->send())
				// {
				//     echo "E-mail Not Sent";
				// }else{
				// 	$this->company_m->update_contractor_send_status($company_id);
				// 	echo "E-mail Successfully Sent";
				// }
//+++++++++++++++++++++++++++++++++++++++
				$send_msg = $this->send_emails($sender_email,$sender_name,$e_mail,$bcc_email,$subject,$message);
				if( $send_msg == 'Message has been sent'){
					$pl_insurance = 0;
					$wc_insurance = 0;
					$ip_insurance = 0;

					if($pl_insurance == 1){
						$this->company_m->update_contractor_send_status($company_id, 1);
					}
					if($wc_insurance == 1){
						$this->company_m->update_contractor_send_status($company_id, 2);
					}
					if($ip_insurance == 1){
						$this->company_m->update_contractor_send_status($company_id, 3);
					}
				}
				echo $send_msg;
			}
		}
	}

	public function contractors_list(){
		$comp_q = $this->company_m->fetch_all_company_details_active();
		$date = date('Y-m-d');
		echo '<table width = "100%" class ="table table-striped" table-bordered><th width = 20px><input type="checkbox" class = "checkall_contractor" onclick = "checkall_contractor()"></th><th>Company Name</th>';
		$no_insurance = 0;
		foreach ($comp_q->result_array() as $row){
			$insurances = array();
			$expired = 0;
			//$send_status = $row['email_send_status'];
			$company_id = $row['company_id'];
			$company_name = $row['company_name'];
			if($row['pl_email_stat'] == 0){
				$has_insurance_public_liability = $row['has_insurance_public_liability'];
				if($has_insurance_public_liability == 1){
					$public_liability_expiration = $row['public_liability_expiration'];
					$ple_arr =  explode('/',$public_liability_expiration);
					$ple_day = $ple_arr[0];
					$ple_month = $ple_arr[1];
					$ple_year = $ple_arr[2];
					$ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
					$ple_month_before = date('Y-m-d', strtotime($ple_date. ' - 28 days'));
					if($date >= $ple_month_before){
						$expired++;
						array_push($insurances,'public_liability');
					}
				}
			}

			if($row['wc_email_stat'] == 0){				
				$has_insurance_workers_compensation = $row['has_insurance_workers_compensation'];
				if($has_insurance_workers_compensation == 1){
					$workers_compensation_expiration = $row['workers_compensation_expiration'];
					$wce_arr =  explode('/',$workers_compensation_expiration);
					$wce_day = $wce_arr[0];
					$wce_month = $wce_arr[1];
					$wce_year = $wce_arr[2];
					$wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
					$wce_month_before = date('Y-m-d', strtotime($wce_date. ' - 28 days'));
					if($date >= $wce_month_before){
						$expired++;
						array_push($insurances,'workers_compensation');
					}
				}
			}

			if($row['email_send_status'] == 0){
				$has_insurance_income_protection = $row['has_insurance_income_protection'];
				if($has_insurance_income_protection == 1){
					$income_protection_expiration = $row['income_protection_expiration'];
					$ipe_arr =  explode('/',$income_protection_expiration);
					$ipe_day = $ipe_arr[0];
					$ipe_month = $ipe_arr[1];
					$ipe_year = $ipe_arr[2];
					$ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
					$ipe_month_before = date('Y-m-d', strtotime($ipe_date. ' - 28 days'));
					if($date >= $ipe_month_before){
						$expired++;
						array_push($insurances,'income_protection');
					}	
				}

				// if($has_insurance_public_liability == 0 && $has_insurance_workers_compensation == 0 && $has_insurance_income_protection == 0){
				// 	$expired++;
				// 	$no_insurance = 1;
				// }
			}

			if($expired > 0){
				echo '<tr><td><input type="checkbox" name = "chk_contractor_list" class = "chk_contractor_list" value = "'.$company_id.'"></td><td style = "color:Blue">'.$company_name.'</td></tr>';
			}
		}
		// $comp_q = $this->company_m->fetch_all_company_details_active();
		// foreach ($comp_q->result_array() as $row){
		// 	$has_insurance_public_liability = $row['has_insurance_public_liability'];
		// 	$has_insurance_workers_compensation = $row['has_insurance_workers_compensation'];
		// 	$has_insurance_income_protection = $row['has_insurance_income_protection'];
			
		// 	$company_id = $row['company_id'];
		// 	$company_name = $row['company_name'];
		// 	if($has_insurance_public_liability == 0 && $has_insurance_workers_compensation == 0 && $has_insurance_income_protection == 0){
		// 		echo '<tr><td><input type="checkbox" name = "chk_contractor_list" class = "chk_contractor_list" value = "'.$company_id.'"></td><td>'.$company_name.'</td></tr>';
		// 	}
		// }
		echo '</table>';
	}

	public function selected_contractor_send_email(){
		$checkboxValues =  $_POST['checkboxValues'];

		$email_sent = 0;
		$email_not_sent = 0;
		foreach($checkboxValues as $chkval){
			$company_id = $chkval;

			$comp_q = $this->company_m->fetch_company_details($company_id);
			$date = date('Y-m-d');
			
			foreach ($comp_q->result_array() as $row){
				$insurances = array();
				$expired = 0;

				$pl_insurance = 0;
				$wc_insurance = 0;
				$ip_insurance = 0; 
				//$send_status = $row['email_send_status'];
				$company_id = $row['company_id'];
				if($row['pl_email_stat'] == 0){
					$has_insurance_public_liability = $row['has_insurance_public_liability'];
					if($has_insurance_public_liability == 1){
						$public_liability_expiration = $row['public_liability_expiration'];
						$ple_arr =  explode('/',$public_liability_expiration);
						$ple_day = $ple_arr[0];
						$ple_month = $ple_arr[1];
						$ple_year = $ple_arr[2];
						$ple_date = $ple_year.'-'.$ple_month.'-'.$ple_day;
						$ple_month_before = date('Y-m-d', strtotime($ple_date. ' - 28 days'));
						if($date >= $ple_month_before){
							$expired++;
							array_push($insurances,'public_liability');
							$pl_insurance = 1;
						}
					}

				}
				
				if($row['wc_email_stat'] == 0){	
					$has_insurance_workers_compensation = $row['has_insurance_workers_compensation'];
					if($has_insurance_workers_compensation == 1){
						$workers_compensation_expiration = $row['workers_compensation_expiration'];
						$wce_arr =  explode('/',$workers_compensation_expiration);
						$wce_day = $wce_arr[0];
						$wce_month = $wce_arr[1];
						$wce_year = $wce_arr[2];
						$wce_date = $wce_year.'-'.$wce_month.'-'.$wce_day;
						$wce_month_before = date('Y-m-d', strtotime($wce_date. ' - 28 days'));
						if($date >= $wce_month_before){
							$expired++;
							array_push($insurances,'workers_compensation');
							$wc_insurance = 1;
						}
					}
				}

				if($row['email_send_status'] == 0){
					$has_insurance_income_protection = $row['has_insurance_income_protection'];
					if($has_insurance_income_protection == 1){
						$income_protection_expiration = $row['income_protection_expiration'];
						$ipe_arr =  explode('/',$income_protection_expiration);
						$ipe_day = $ipe_arr[0];
						$ipe_month = $ipe_arr[1];
						$ipe_year = $ipe_arr[2];
						$ipe_date = $ipe_year.'-'.$ipe_month.'-'.$ipe_day;
						$ipe_month_before = date('Y-m-d', strtotime($ipe_date. ' - 28 days'));
						if($date >= $ipe_month_before){
							$expired++;
							array_push($insurances,'income_protection');
							$ip_insurance = 1; 
						}	
					}
				}
			}

			$contractor_q = $this->company_m->fetch_contact_details_primary($company_id);
			foreach ($contractor_q->result_array() as $row){
				$e_mail = $row['general_email'];
				$data['email'] = $e_mail;
			}

			$user_query = $this->admin_m->fetch_admin_default_email_message();
			foreach ($user_query->result_array() as $row){
			    $user_id = $row['user_id'];
			    $message_content = $row['message_content'];
			    $sender_name = $row['sender_name'];
			    $sender_email = $row['sender_email'];
			    $bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au";

			}

			// $message_content = $this->session->userdata('message_content');
   //          $sender_name = $this->session->userdata('sender_name');
   //          $sender_email = $this->session->userdata('sender_email');
   //          $bcc_email = $this->session->userdata('bcc_email');


            $data['insurances'] = $insurances;
            $data['contractor_id'] = $company_id;

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

		    $subject = 'Insurance is About to Expire';
			$message = $this->load->view('expired_insurance_message_view',$data,TRUE);

//+++++++++++++++++++++++++++++++++++++++++++++++++
			// $this->email->from($sender_email, $sender_name);
			// $this->email->to($e_mail); 
			// 	//$this->email->cc($cc); 
			// $this->email->bcc($bcc_email); 

			// $this->email->subject('Insurance is About to Expire');
			// $this->email->message($message);

			// if ( ! $this->email->send())
			// {
			//     //echo "E-mail Not Sent";
			// 	$email_not_sent++;
			// }else{
			// 	$this->company_m->update_contractor_send_status($company_id);
			// 	//echo "E-mail Successfully Sent";
			// 	$email_sent++;
			// }
//++++++++++++++++++++++++++++++++++++++++++++++++++
			$send_msg = $this->send_emails($sender_email,$sender_name,$e_mail,$bcc_email,$subject,$message);
			if( $send_msg == 'Message has been sent'){
				$email_sent++;
				
				if($pl_insurance == 1){
					$this->company_m->update_contractor_send_status($company_id, 1);
				}
				if($wc_insurance == 1){
					$this->company_m->update_contractor_send_status($company_id, 2);
				}
				if($ip_insurance == 1){
					$this->company_m->update_contractor_send_status($company_id, 3);
				}
				
			}else{
				$email_not_sent++;
			}
			
		}
		echo $email_sent." Email Successfully Sent, ".$email_not_sent." Failed";
	}

	//Site staff functions start ==================================

	public function get_cont_site_staff(){
		$company_id = $_POST['company_id'];
		$query = $this->company_m->fetch_site_staff($company_id);
		
		echo json_encode($query->result());
	}

	public function get_cont_site_staff_all(){
		$query = $this->company_m->fetch_site_staff();
		
		echo json_encode($query->result());
	}

	public function filter_contractors_with_sitestaff(){
		$company_id = $_POST['company_id'];
		$query = $this->company_m->filter_contractors_with_sitestaff($company_id);
		
		echo json_encode($query->result());
	}

	public function add_cont_site_staff(){
		$data = json_decode(file_get_contents("php://input"), true);

		$ss_fname = $data['ss_fname'];
		$ss_sname = $data['ss_sname'];
		$ss_position = $data['ss_position'];
		$company_id = $data['company_id'];
		$mobile_number = $data['ss_mobile_no'];
		$ss_email = $data['ss_email'];
		$is_apprentice = $data['is_apprentice'];
		$query = $this->company_m->add_site_staff($company_id,$ss_fname,$ss_sname,$mobile_number,$ss_position,$ss_email,$is_apprentice);

		// if(isset($_POST['is_company'])){
		// 	$query = $this->company_m->fetch_site_staff($company_id);
		// }else{
		// 	$query = $this->company_m->fetch_site_staff();
		// }

		// echo json_encode($query->result());
	}

	public function update_cont_site_staff(){
		$data = json_decode(file_get_contents("php://input"), true);

		$ss_fname = $data['ss_fname'];
		$ss_sname = $data['ss_sname'];
		$ss_position = $data['ss_position'];
		$contractor_site_staff_id = $data['contractor_site_staff_id'];
		$mobile_number = $data['ss_mobile_no'];
		$ss_email = $data['ss_email'];
		$company_id = $data['company_id'];
		$gi_date = $data['gi_date'];
		$is_apprentice = $data['is_apprentice'];
		$query = $this->company_m->update_site_staff($contractor_site_staff_id,$ss_fname,$ss_sname,$ss_position,$mobile_number,$ss_email,$company_id,$is_apprentice,0,$gi_date);
	
		// if(isset($_POST['is_company'])){
		// 	$query = $this->company_m->fetch_site_staff($company_id);
		// }else{
		// 	$query = $this->company_m->fetch_site_staff();
		// }

		// echo json_encode($query->result());
	}

	public function remove_cont_site_staff(){
		$data = json_decode(file_get_contents("php://input"), true);
		
		$contractor_site_staff_id = $data['contractor_site_staff_id'];
		$query = $this->company_m->delete_site_staff($contractor_site_staff_id);

		// if(isset($_POST['is_company'])){
		// 	$company_id = $_POST['company_id'];
		// 	$query = $this->company_m->fetch_site_staff($company_id);
		// }else{
		// 	$query = $this->company_m->fetch_site_staff();
		// }

		// echo json_encode($query->result());
	}


//Site staff functions end ====================================

	public function upload_logo() {

		$this->load->library('upload');
	    $this->load->library('image_lib');
	    $this->load->library('session');

		$company_id = $this->input->post('company_id');

		$config['upload_path'] = './uploads/company/logo/';
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_size']	= '';
		$config['max_width']  = '';
		$config['max_height']  = '';

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->session->set_flashdata('error_logo', $error);

			redirect('/company', 'refresh');
		}
		else
		{
			$result = $this->company_m->get_company_logo($company_id);

			$upload_path = str_replace(".","",$config['upload_path']);
			$upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
			$file_name = $upload_data['file_name'];
			$company_logo_path = $upload_path.$file_name;

			if ($result->num_rows() != 0){
				$this->company_m->edit_company_logo_path($company_id, $company_logo_path);
				$this->session->set_flashdata('upload_logo_success', 'Successfully changed the logo of ');
				redirect('company');
			} else {
				$this->company_m->insert_company_logo_path($company_id, $company_logo_path);
				$this->session->set_flashdata('upload_logo_success', 'Successfully uploaded the logo of ');
				redirect('company');
			}
		}
	}

	public function delete_logo($company_id){

		$this->company_m->delete_company_logo($company_id);
		redirect('company', 'refresh');
	}

	public function onboarding_send(){

		$this->clear_apost();

		require_once('PHPMailer/class.phpmailer.php');
		require_once('PHPMailer/PHPMailerAutoload.php');

		$ajax_var = $this->input->post('ajax_var');
		$onboardingData = explode('|', $ajax_var);

		$onboarding_email_address = $onboardingData[0];
		$onboarding_first_name = ucfirst($onboardingData[1]);
		$onboarding_last_name = ucfirst($onboardingData[2]);
		$onboarding_subject = $onboardingData[3];
		$onboarding_message = $onboardingData[4];
		$onboarding_type = $onboardingData[5];
		$company_details_temp_id = $onboardingData[6];

		// $check_if_email_exist = $this->company_m->check_email_exist($onboarding_email_address);

		// if ($check_if_email_exist == 1){
		// 	$response['status'] = 'Email Address is already exist! Please try another.';
		// } else {

		// 	$check_if_email_exist_in_onboarding = $this->company_m->check_email_exist_in_onboarding($onboarding_email_address);			

		// 	if ($check_if_email_exist_in_onboarding == 1){
		// 		$response['status'] = 'Email Address is already exist! Please try another.';
		// 	} else {

				$onboarding_insert_id = $this->company_m->insert_onboarding_data($onboarding_email_address, $onboarding_first_name, $onboarding_last_name, $onboarding_message,$company_details_temp_id);

				$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding');
				$onboarding_default_admin = $q_admin_default_email_message->row();

				$data['onboarding_default_admin_sender_name'] = $onboarding_default_admin->sender_name;
				$data['onboarding_default_admin_sender_email'] = $onboarding_default_admin->sender_email;
				$data['onboarding_default_admin_bcc_email'] = $onboarding_default_admin->bcc_email;
				$data['onboarding_default_admin_subject'] = $onboarding_default_admin->subject;
				$data['onboarding_default_admin_default_message'] = $onboarding_default_admin->message_content;

				$onboard_mailer = new phpmailer(true);		
				$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
				$onboard_mailer->Port = 587;
				
				$onboard_mailer->setFrom($data['onboarding_default_admin_sender_email'], $data['onboarding_default_admin_sender_name']);
				$onboard_mailer->addReplyTo($data['onboarding_default_admin_sender_email'], $data['onboarding_default_admin_sender_name']);

				$onboard_mailer->addAddress($onboarding_email_address);
				$onboard_mailer->addBCC($data['onboarding_default_admin_sender_email']);
				$onboard_mailer->addBCC($data['onboarding_default_admin_bcc_email']);
				$onboard_mailer->addBCC('mark.obis2012@gmail.com');

				$onboard_mailer->isHTML(true);
				
				$onboard_mailer->Subject = $onboarding_subject;

				$email_body_content = 'Hi '.$onboarding_first_name.', <br><br>';
				$email_body_content .= $onboarding_message.'<br/><br/>';
				$email_body_content .= '<a href="'.base_url().'onboarding?id='.$onboarding_insert_id.'&type='.$onboarding_type.'" target="_blank">Click Here!</a><br><br><br><br>';
				$email_body_content .= 'Regards,<br><br>';
				$email_body_content .= $data['onboarding_default_admin_sender_name'].'<br><br>';
				$email_body_content .= 'Group Administrator<br>';
				$email_body_content .= $data['onboarding_default_admin_sender_email'].'<br/><br>';
				$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

				$onboard_mailer->Body = $email_body_content;
				// $onboard_mailer->Body    = '<span style="font-family: "Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif">'.$body_content."</span>";

				if(!$onboard_mailer->send()) {
					// $response['status'] = 'Message could not be sent.';
					$response['status'] =  'Mailer Error: ' . $onboard_mailer->ErrorInfo;
					// $response = array('send_status'=>false);
				} else {
					$response['status'] = 'Email Link Sent';
				}
			// }
		// }

		echo json_encode($response);
	}

	public function onboarding() {

		$this->users->_check_user_access('company',1);
		$data['main_content'] = 'onboard_v';
		$data['screen'] = 'Onboarding';

		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();

		$q_pending_onboard = $this->company_m->fetch_pending_onboard();
		$data['pending_onboard'] = $q_pending_onboard->result();

		$q_declined_onboard = $this->company_m->fetch_declined_onboard();
		$data['declined_onboard'] = $q_declined_onboard->result();

		$data['page_title'] = 'Onboarding';

		$q_static_defaults = $this->company_m->select_static_defaults();
		$static_defaults_data =  array_shift($q_static_defaults->result_array());

		$data['workplace_health_safety_msg'] = $static_defaults_data['workplace_health_safety_msg'];
		$data['swms_msg'] = $static_defaults_data['swms_msg'];
		$data['jsa_msg'] = $static_defaults_data['jsa_msg'];
		$data['reviewed_swms_msg'] = $static_defaults_data['reviewed_swms_msg'];
		$data['safety_related_convictions_msg'] = $static_defaults_data['safety_related_convictions_msg'];
		$data['confirm_licences_certifications_msg'] = $static_defaults_data['confirm_licences_certifications_msg'];
		$data['remove_pending_onboarding'] = $static_defaults_data['remove_pending_onboarding'];

		$this->load->view('page', $data);
	}

	public function view_onboard(){

		$company_id = $this->uri->segment(3);

		$data['screen'] = 'Company Detail';
		$data['main_content'] = 'onboard_view';
		
		$all_aud_states = $this->company_m->fetch_all_states();
		$data['all_aud_states'] = $all_aud_states->result();

		$comp_type_list = $this->company_m->fetch_all_company_types();
		$data['comp_type_list'] = $comp_type_list->result();

		$q_pending_onboard = $this->company_m->fetch_pending_onboard_id($company_id);

		if ($q_pending_onboard == '0'){
			redirect('company/onboarding');
		} else {
			$view_pending_onboard = array_shift($q_pending_onboard->result_array());

			$data['page_title'] = '(Pending) '.$view_pending_onboard['company_name'];

			$data['company_id'] = $view_pending_onboard['company_id'];
			$data['company_name'] = $view_pending_onboard['company_name'];
			$data['company_type_id'] = $view_pending_onboard['company_type_id'];
			$data['company_type'] = $view_pending_onboard['company_type'];

			$company_activity = $this->company_m->fetch_company_activity_name_by_type($data['company_type'], $view_pending_onboard['activity_id']);
			$data['company_activity'] = ucwords($company_activity);
			$data['company_activity_id'] = $view_pending_onboard['activity_id'];

			// $data['company_activity'] = $view_pending_onboard['job_category'];
			// $data['company_activity_id'] = $view_pending_onboard['activity_id'];
			$data['parent_company_id'] = $view_pending_onboard['parent_company_id'];
			// $data['parent_company_id'] = $view_pending_onboard['parent_company_id'];

			$data['abn'] = $view_pending_onboard['abn'];

			$q_check_abn = $this->company_m->check_abn_onboarding($data['abn'], $data['company_id']);

			$data['ABNexist_count'] = $q_check_abn->num_rows();
			$ABNexist = array_shift($q_check_abn->result_array());
			$data['ABNexistcompany_name'] = $ABNexist['company_name'];
			
			$data['acn'] = $view_pending_onboard['acn'];
			$data['notes_id'] = $view_pending_onboard['notes_id'];
			$data['comments'] = $view_pending_onboard['comments'];

			$query_address= $this->company_m->fetch_complete_detail_address($view_pending_onboard['address_id']);
			$temp_data = array_shift($query_address->result_array());
			$data['postcode'] = $temp_data['postcode'];
			$data['suburb'] = $temp_data['suburb'];
			$data['po_box'] = $temp_data['po_box'];
			$data['street'] = ucwords(strtolower($temp_data['street']));
			$data['unit_level'] = ucwords(strtolower($temp_data['unit_level']));
			$data['unit_number'] = $temp_data['unit_number'];
			$data['state'] = $temp_data['name'];
			$data['address_id'] = $view_pending_onboard['address_id'];

			$data['shortname'] = $temp_data['shortname'];
			$data['state_id'] =  $temp_data['state_id'];
			$data['phone_area_code'] = $temp_data['phone_area_code'];	

			$p_query_address = $this->company_m->fetch_complete_detail_address($view_pending_onboard['postal_address_id']);
			$p_temp_data = array_shift($p_query_address->result_array());
			$data['p_po_box'] = $p_temp_data['po_box'];
			$data['p_unit_level'] = ucwords(strtolower($p_temp_data['unit_level']));
			$data['p_unit_number'] = $p_temp_data['unit_number'];
			$data['p_street'] = ucwords(strtolower($p_temp_data['street']));
			$data['p_suburb'] = $p_temp_data['suburb'];
			$data['p_state'] = $p_temp_data['name'];
			$data['p_postcode'] = $p_temp_data['postcode'];
			$data['postal_address_id'] = $view_pending_onboard['postal_address_id'];

			$data['p_shortname'] = $p_temp_data['shortname'];
			$data['p_state_id'] =  $p_temp_data['state_id'];
			$data['p_phone_area_code'] = $p_temp_data['phone_area_code'];

			$bank_account_details_q = $this->company_m->fetch_bank_account_details($view_pending_onboard['bank_account_id']);
			$bank_account_details = array_shift($bank_account_details_q->result_array());
			$data['bank_account_id'] = $bank_account_details['bank_account_id'];
			$data['bank_name'] = $bank_account_details['bank_name'];
			$data['bank_account_name'] = $bank_account_details['bank_account_name'];
			$data['bank_account_number'] = $bank_account_details['bank_account_number'];
			$data['bank_bsb_number'] = $bank_account_details['bank_bsb_number'];
		
			$parent_company_detail_q = $this->company_m->fetch_all_company($view_pending_onboard['parent_company_id']); //parent company		
			$parent_company_detail = array_shift($parent_company_detail_q->result_array());
			$data['parent_company_name'] = $parent_company_detail['company_name'];
			$data['parent_company_id'] = $parent_company_detail['company_id'];

			$sub_client_detail_q = $this->company_m->fetch_all_company($view_pending_onboard['sub_client_id']); //parent company		
			$sub_client_detail = array_shift($sub_client_detail_q->result_array());
			$data['sub_client_company_name'] = $sub_client_detail['company_name'];
			$data['sub_client_id'] = $sub_client_detail['company_id'];

			$contact_person_company_q = $this->company_m->fetch_contact_person_company($view_pending_onboard['company_id']);
			$contact_person_company = $contact_person_company_q->result_array();
			$data['contact_person_company'] = $contact_person_company;

			$data['workplace_health_safety'] = $view_pending_onboard['workplace_health_safety'];
			$data['workplace_health_safety_notes'] = $view_pending_onboard['workplace_health_safety_notes'];
			$data['swms'] = $view_pending_onboard['swms'];
			$data['swms_notes'] = $view_pending_onboard['swms_notes'];
			$data['jsa'] = $view_pending_onboard['jsa'];
			$data['jsa_notes'] = $view_pending_onboard['jsa_notes'];
			$data['reviewed_swms'] = $view_pending_onboard['reviewed_swms'];
			$data['reviewed_swms_notes'] = $view_pending_onboard['reviewed_swms_notes'];
			$data['safety_related_convictions'] = $view_pending_onboard['safety_related_convictions'];
			$data['safety_related_convictions_details'] = $view_pending_onboard['safety_related_convictions_details'];
			$data['safety_related_convictions_notes'] = $view_pending_onboard['safety_related_convictions_notes'];
			$data['confirm_licences_certifications'] = $view_pending_onboard['confirm_licences_certifications'];
			$data['confirm_licences_certifications_notes'] = $view_pending_onboard['confirm_licences_certifications_notes'];

			$q_static_defaults = $this->company_m->select_static_defaults();
			$static_defaults_data =  array_shift($q_static_defaults->result_array());

			$data['workplace_health_safety_msg'] = $static_defaults_data['workplace_health_safety_msg'];
			$data['swms_msg'] = $static_defaults_data['swms_msg'];
			$data['jsa_msg'] = $static_defaults_data['jsa_msg'];
			$data['reviewed_swms_msg'] = $static_defaults_data['reviewed_swms_msg'];
			$data['safety_related_convictions_msg'] = $static_defaults_data['safety_related_convictions_msg'];
			$data['confirm_licences_certifications_msg'] = $static_defaults_data['confirm_licences_certifications_msg'];
		}

		$this->load->view('page', $data);

	}

	public function onboard_approved(){

		$ajax_var = $this->input->post('ajax_var');
		$onboard_approved = explode('|', $ajax_var);

		$company_id = $onboard_approved[0];
		$primary_email = $onboard_approved[1];
		$company_type_id = $onboard_approved[2];
		$bank_details_form_state = $onboard_approved[3];

		$result = $this->company_m->onboard_approved($company_id);


		$comp_query = $this->company_m->fetch_all_company($company_id);
		foreach ($comp_query->result_array() as $row){
			$company_details_temp_id = $row['company_details_temp_id'];
			if($company_details_temp_id > 0){
				$this->company_m->remove_temporary_comp($company_details_temp_id);
				
			}
		}

		$q_admin_default_email_message_clients = $this->admin_m->fetch_admin_default_email_message('onboarding-approved-clients');
		$onboarding_approved_default_clients = $q_admin_default_email_message_clients->row();

		$data['onboarding_approved_sender_name_clients'] = $onboarding_approved_default_clients->sender_name;
		$data['onboarding_approved_sender_email_clients'] = $onboarding_approved_default_clients->sender_email;
		$data['onboarding_approved_bcc_email_clients'] = $onboarding_approved_default_clients->bcc_email;
		$data['onboarding_approved_subject_clients'] = $onboarding_approved_default_clients->subject;
		$data['onboarding_approved_default_message_clients'] = $onboarding_approved_default_clients->message_content;

		$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-approved');
		$onboarding_approved_default = $q_admin_default_email_message->row();

		$data['onboarding_approved_sender_name'] = $onboarding_approved_default->sender_name;
		$data['onboarding_approved_sender_email'] = $onboarding_approved_default->sender_email;
		$data['onboarding_approved_bcc_email'] = $onboarding_approved_default->bcc_email;
		$data['onboarding_approved_subject'] = $onboarding_approved_default->subject;
		$data['onboarding_approved_default_message'] = $onboarding_approved_default->message_content;

		$q_admin_default_email_message_bank = $this->admin_m->fetch_admin_default_email_message('onboarding-bank-details-form');
		$onboarding_approved_default_bank = $q_admin_default_email_message_bank->row();

		$data['onboarding_approved_sender_name_bank'] = $onboarding_approved_default_bank->sender_name;
		$data['onboarding_approved_sender_email_bank'] = $onboarding_approved_default_bank->sender_email;
		$data['onboarding_approved_bcc_email_bank'] = $onboarding_approved_default_bank->bcc_email;
		$data['onboarding_approved_subject_bank'] = $onboarding_approved_default_bank->subject;
		$data['onboarding_approved_default_message_bank'] = $onboarding_approved_default_bank->message_content;

		$q_static_defaults = $this->company_m->select_static_defaults();
		$static_defaults_data =  array_shift($q_static_defaults->result_array());

		$data['onboarding_general_msg'] = $static_defaults_data['onboarding_general_msg'];

		$new_comp_q = $this->company_m->fetch_contact_details_primary($company_id);
		foreach ($new_comp_q->result_array() as $new_comp_row){
			$contact_person_id = $new_comp_row['contact_person_id'];
		}

		if($company_type_id == 2):

			$this->company_m->update_pending_work_contractor($company_details_temp_id,$company_id,$contact_person_id);

			$e_mail = $primary_email;
			$default_msg_q = $this->company_m->fetch_admin_default_email_message();

			foreach ($default_msg_q->result_array() as $row){
				$message_content = $row['message_content'];
				$sender_name = $row['sender_name'];
				$sender_email = $row['sender_email'];
				$bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au,marko@focusshopfit.com.au";
				// $bcc_email = "michael@focusshopfit.com.au";				
				$subject = $row['subject'];
			}

			$data['email'] = $primary_email;
			$data['contractor_id'] = $company_id;
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

	    	$subject = "Insurances Required";
			$message = $this->load->view('message_view',$data,TRUE);

			$send_msg = $this->send_emails($sender_email,$sender_name,$e_mail,$bcc_email,$subject,$message);
			// echo $send_msg;
		endif;

		if($company_type_id == 1):
			$this->company_m->update_pending_project_client($company_details_temp_id,$company_id,$contact_person_id);

			$onboard_mailer = new phpmailer(true);		
			$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
			$onboard_mailer->Port = 587;
			
			$onboard_mailer->setFrom($data['onboarding_approved_sender_email_clients'], $data['onboarding_approved_sender_name_clients']);
			$onboard_mailer->addReplyTo($data['onboarding_approved_sender_email_clients'], $data['onboarding_approved_sender_name_clients']);

			$onboard_mailer->addAddress($primary_email);
			$onboard_mailer->addBCC($data['onboarding_approved_bcc_email_clients']);
			$onboard_mailer->addBCC('marko@focusshopfit.com.au');
			$onboard_mailer->isHTML(true);
			
			$onboard_mailer->Subject = $data['onboarding_approved_subject_clients'];

			$email_body_content = 'Good Day! <br><br>';
			$email_body_content .= $data['onboarding_approved_default_message_clients'];
			// $email_body_content .= '<br><br>';
			// $email_body_content .= $data['onboarding_general_msg'];
			$email_body_content .= '<br><br><br><br>Regards,<br><br>';
			$email_body_content .= $data['onboarding_approved_sender_name_clients'].'<br><br>';					
			$email_body_content .= 'Group Administrator<br>';
			$email_body_content .= $data['onboarding_approved_sender_email_clients'].'<br><br>';
			$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

			$onboard_mailer->Body = $email_body_content;
			if(!$onboard_mailer->send()) {
				// $response['status'] = 'Message could not be sent.';
				// $response['status'] =  'Mailer Error: ' . $onboard_mailer->ErrorInfo;
				// $response = array('send_status'=>false);
			} else {
				// $response['status'] = 'Email Link Sent';
				// user log creation company
				$type = 'Approved';
				$actions = 'Approved new onboard company, with the company ID of '.$company_id;
				date_default_timezone_set("Australia/Perth");
				$user_id = $this->session->userdata('user_id');
				$date = date("d/m/Y");
				$time = date("H:i:s");
				$this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');
				// user log creation company
			}

		else:
			$this->company_m->update_pending_work_contractor($company_details_temp_id,$company_id,$contact_person_id);
			
			$onboard_mailer = new phpmailer(true);		
			$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
			$onboard_mailer->Port = 587;
			
			$onboard_mailer->setFrom($data['onboarding_approved_sender_email'], $data['onboarding_approved_sender_name']);
			$onboard_mailer->addReplyTo($data['onboarding_approved_sender_email'], $data['onboarding_approved_sender_name']);

			$onboard_mailer->addAddress($primary_email);
			$onboard_mailer->addBCC($data['onboarding_approved_bcc_email']);
			$onboard_mailer->addBCC('marko@focusshopfit.com.au');
			$onboard_mailer->isHTML(true);
			
			$onboard_mailer->Subject = $data['onboarding_approved_subject'];

			$email_body_content = 'Good Day! <br><br>';
			$email_body_content .= $data['onboarding_approved_default_message'];
			$email_body_content .= '<br><br>';
			$email_body_content .= $data['onboarding_general_msg'];
			$email_body_content .= '<br><br><br><br>Regards,<br><br>';
			$email_body_content .= $data['onboarding_approved_sender_name'].'<br><br>';					
			$email_body_content .= 'Group Administrator<br>';
			$email_body_content .= $data['onboarding_approved_sender_email'].'<br><br>';
			$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

			$onboard_mailer->Body = $email_body_content;
			if(!$onboard_mailer->send()) {
				// $response['status'] = 'Message could not be sent.';
				// $response['status'] =  'Mailer Error: ' . $onboard_mailer->ErrorInfo;
				// $response = array('send_status'=>false);
			} else {
				// $response['status'] = 'Email Link Sent';
				// user log creation company
				$type = 'Approved';
				$actions = 'Approved new onboard company, with the company ID of '.$company_id;
				date_default_timezone_set("Australia/Perth");
				$user_id = $this->session->userdata('user_id');
				$date = date("d/m/Y");
				$time = date("H:i:s");
				$this->user_model->insert_user_log($user_id,$date,$time,$actions,'000000',$type,'2');
				// user log creation company

				$onboard_mailer = new phpmailer(true);		
				$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
				$onboard_mailer->Port = 587;
				
				$onboard_mailer->setFrom($data['onboarding_approved_sender_email_bank'], $data['onboarding_approved_sender_name_bank']);
				$onboard_mailer->addReplyTo($data['onboarding_approved_sender_email_bank'], $data['onboarding_approved_sender_name_bank']);

				$onboard_mailer->addAddress($primary_email);
				
				if (!empty($data['onboarding_approved_bcc_email_bank'])){

				$findthis = ',';
				$pos = strpos($data['onboarding_approved_bcc_email_bank'], $findthis);

				if ($pos === false){
					$onboard_mailer->addBCC($data['onboarding_approved_bcc_email_bank']);
				} else {
					$sendpdf_bcc_arr = array();
					$sendpdf_bcc_arr = explode(",", $data['onboarding_approved_bcc_email_bank']);
					$sendpdf_bcc_count = count($sendpdf_bcc_arr);

					for ($i=0; $i < $sendpdf_bcc_count; $i++) { 
						$onboard_mailer->addBCC($sendpdf_bcc_arr[$i]);	
					}
				}
			}
				
				$onboard_mailer->addBCC('marko@focusshopfit.com.au');

				if ($bank_details_form_state == 'wa'){
					$onboard_mailer->addAttachment('./docs/bank_details_form/wa/bank_details_form_wa.PDF');	
				}

				if ($bank_details_form_state == 'nsw'){
					$onboard_mailer->addAttachment('./docs/bank_details_form/nsw/bank_details_form_nsw.PDF');
				}

				$onboard_mailer->isHTML(true);
				
				$onboard_mailer->Subject = $data['onboarding_approved_subject_bank'];

				$email_body_content = 'Good Day! <br><br>';
				$email_body_content .= $data['onboarding_approved_default_message_bank'];
				// $email_body_content .= '<br><br>';
				// $email_body_content .= $data['onboarding_general_msg'];
				$email_body_content .= '<br><br><br><br>Regards,<br><br>';
				$email_body_content .= $data['onboarding_approved_sender_name_bank'].'<br><br>';					
				$email_body_content .= 'Group Administrator<br>';
				$email_body_content .= $data['onboarding_approved_sender_email_bank'].'<br><br>';
				$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

				$onboard_mailer->Body = $email_body_content;
				if(!$onboard_mailer->send()) {
					// $response['status'] = 'Message could not be sent.';
					// $response['status'] =  'Mailer Error: ' . $onboard_mailer->ErrorInfo;
					// $response = array('send_status'=>false);
				} else {

				}
			}

		endif;

		echo $result;
	}

	public function onboard_declined(){

		$this->clear_apost();

		$ajax_var = $this->input->post('ajax_var');

		$onboard_declined = explode('|', $ajax_var);

		$company_id = $onboard_declined[0];
		$primary_email = $onboard_declined[1];
		$company_type_id = $onboard_declined[2];
		$declined_message = $onboard_declined[3];
		
		$q_admin_default_email_message_clients = $this->admin_m->fetch_admin_default_email_message('onboarding-declined-clients');
		$onboarding_declined_default_clients = $q_admin_default_email_message_clients->row();

		$data['onboarding_declined_sender_name_clients'] = $onboarding_declined_default_clients->sender_name;
		$data['onboarding_declined_sender_email_clients'] = $onboarding_declined_default_clients->sender_email;
		$data['onboarding_declined_bcc_email_clients'] = $onboarding_declined_default_clients->bcc_email;
		$data['onboarding_declined_subject_clients'] = $onboarding_declined_default_clients->subject;
		$data['onboarding_declined_default_message_clients'] = $onboarding_declined_default_clients->message_content;

		$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('onboarding-declined');
		$onboarding_declined_default = $q_admin_default_email_message->row();

		$data['onboarding_declined_sender_name'] = $onboarding_declined_default->sender_name;
		$data['onboarding_declined_sender_email'] = $onboarding_declined_default->sender_email;
		$data['onboarding_declined_bcc_email'] = $onboarding_declined_default->bcc_email;
		$data['onboarding_declined_subject'] = $onboarding_declined_default->subject;
		$data['onboarding_declined_default_message'] = $onboarding_declined_default->message_content;

		$q_static_defaults = $this->company_m->select_static_defaults();
		$static_defaults_data =  array_shift($q_static_defaults->result_array());

		$data['onboarding_general_msg'] = $static_defaults_data['onboarding_general_msg'];

		if($company_type_id == 1):

			$onboard_mailer = new phpmailer(true);		
			$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
			$onboard_mailer->Port = 587;
			
			$onboard_mailer->setFrom($data['onboarding_declined_sender_email_clients'], $data['onboarding_declined_sender_name_clients']);
			$onboard_mailer->addReplyTo($data['onboarding_declined_sender_email_clients'], $data['onboarding_declined_sender_name_clients']);

			$onboard_mailer->addAddress($primary_email);
			$onboard_mailer->addBCC($data['onboarding_declined_bcc_email_clients']);
			$onboard_mailer->addBCC('marko@focusshopfit.com.au');
			$onboard_mailer->isHTML(true);
			
			$onboard_mailer->Subject = $data['onboarding_declined_subject_clients'];

			$email_body_content = 'Good Day! <br><br>';
			$email_body_content .= $data['onboarding_declined_default_message_clients'];
			// $email_body_content .= '<br><br>';
			// $email_body_content .= $data['onboarding_general_msg'];
			$email_body_content .= '<br><br><strong><span style="color: #d9534f;">Reason for Declining:</span></strong><br>';
			$email_body_content .= $declined_message;

			$email_body_content .= '<br><br><br><br>Regards,<br><br>';
			$email_body_content .= $data['onboarding_declined_sender_name_clients'].'<br>';
			$email_body_content .= 'Group Administrator<br>';					
			$email_body_content .= $data['onboarding_declined_sender_email_clients'].'<br><br>';
			$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

			$onboard_mailer->Body = $email_body_content;
			if(!$onboard_mailer->send()) {
				// $status = 'Message could not be sent.';
				// $status =  $onboard_mailer->ErrorInfo;
				// $status = array('send_status'=>false);
				$status = 0;
			} else {
				$status = $this->company_m->onboard_declined($company_id, $declined_message);
			}

			echo $status;

		else:

			$onboard_mailer = new phpmailer(true);		
			$onboard_mailer->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';
			$onboard_mailer->Port = 587;
			
			$onboard_mailer->setFrom($data['onboarding_declined_sender_email'], $data['onboarding_declined_sender_name_clients']);
			$onboard_mailer->addReplyTo($data['onboarding_declined_sender_email'], $data['onboarding_declined_sender_name_clients']);

			$onboard_mailer->addAddress($primary_email);
			$onboard_mailer->addBCC($data['onboarding_declined_bcc_email']);
			$onboard_mailer->addBCC('marko@focusshopfit.com.au');
			$onboard_mailer->isHTML(true);
			
			$onboard_mailer->Subject = $data['onboarding_declined_subject'];

			$email_body_content = 'Good Day! <br><br>';
			$email_body_content .= $data['onboarding_declined_default_message'];
			$email_body_content .= '<br><br>';
			$email_body_content .= $data['onboarding_general_msg'];
			$email_body_content .= '<br><br><strong><span style="color: #d9534f;">Reason for Declining:</span></strong><br>';
			$email_body_content .= $declined_message;

			$email_body_content .= '<br><br><br><br>Regards,<br><br>';
			$email_body_content .= $data['onboarding_declined_sender_name'].'<br>';
			$email_body_content .= 'Group Administrator<br>';					
			$email_body_content .= $data['onboarding_declined_sender_email'].'<br><br>';
			$email_body_content .= '<img src="'.base_url().'img/signatures/FSFGroup.png">';

			$onboard_mailer->Body = $email_body_content;
			if(!$onboard_mailer->send()) {
				// $status = 'Message could not be sent.';
				// $status =  $onboard_mailer->ErrorInfo;
				// $status = array('send_status'=>false);
				$status = 0;
			} else {
				$status = $this->company_m->onboard_declined($company_id, $declined_message);
			}

			echo $status;

		endif;
	}

	public function onboard_removed(){

		$ajax_var = $this->input->post('ajax_var');

		$onboard_removed = explode('|', $ajax_var);

		$company_id = $onboard_removed[0];
		$primary_email = $onboard_removed[1];

		$status = $this->company_m->onboard_removed($company_id);

		echo $status;
	}

	public function fetch_ohs_updated(){

		$ajax_var = $this->input->post('ajax_var');
		$ohs_updated_var = explode('|', $ajax_var);

		$company_id = $ohs_updated_var[0];

		$ohs_updated = $this->company_m->fetch_ohs_updated($company_id);			
		$ohs_updated = array_shift($ohs_updated->result_array());

		$data['workplace_health_safety'] = $ohs_updated['workplace_health_safety'];
		$data['swms'] = $ohs_updated['swms'];
		$data['jsa'] = $ohs_updated['jsa'];
		$data['reviewed_swms'] = $ohs_updated['reviewed_swms'];
		$data['safety_related_convictions'] = $ohs_updated['safety_related_convictions'];
		$data['confirm_licences_certifications'] = $ohs_updated['confirm_licences_certifications'];

		echo $data['workplace_health_safety'].'|'.$data['swms'].'|'.$data['jsa'].'|'.$data['reviewed_swms'].'|'.$data['safety_related_convictions'].'|'.$data['confirm_licences_certifications'];
	}

	public function enable_ohs_form(){

		$ajax_var = $this->input->post('ajax_var');
		$enable_ohs_form_data = explode('|', $ajax_var);

		$company_id = $enable_ohs_form_data[0];

		$q_enable_ohs_form = $this->company_m->enable_ohs_form($company_id);			

		echo $q_enable_ohs_form;
	}

	//================= Temporary Company Start==================
	public function fetch_temporary_comp(){
		$data = json_decode(file_get_contents("php://input"), true);
		$company_type = $data['company_type'];
		
		$query = $this->company_m->fetch_temporary_comptype($company_type);
		echo json_encode($query->result());
	}	

	public function insert_temporary_comp(){
		$data = json_decode(file_get_contents("php://input"), true);
		$comp_type = $data['comp_type'];
		$comp_name = $data['comp_name'];
		$cont_person_fname = $data['cont_person_fname'];
		$cont_person_sname = $data['cont_person_sname'];
		$cont_number = $data['cont_number'];
		$email = $data['email'];

		$this->company_m->insert_temporary_comp($comp_type,$comp_name,$cont_person_fname,$cont_person_sname,$cont_number,$email);
	}

	public function remove_temporary_comp(){
		$data = json_decode(file_get_contents("php://input"), true);
		$company_details_temp_id = $data['company_details_temp_id'];
		$this->company_m->remove_temporary_comp($company_details_temp_id);
	}

	public function fetch_temporary_cont_sup(){
		$query = $this->company_m->fetch_temporary_cont_sup();
		echo json_encode($query->result());
	}	

	public function update_temporary_comp(){
		$data = json_decode(file_get_contents("php://input"), true);
		$company_details_temp_id = $data['company_details_temp_id'];
		$comp_type = $data['comp_type'];
		$comp_name = $data['comp_name'];
		$cont_person_fname = $data['cont_person_fname'];
		$cont_person_sname = $data['cont_person_sname'];
		$cont_number = $data['cont_number'];
		$email = $data['email'];

		$this->company_m->update_temporary_comp($company_details_temp_id,$comp_type,$comp_name,$cont_person_fname,$cont_person_sname,$cont_number,$email);
	}

	//================= Temporary Company Start==================
}