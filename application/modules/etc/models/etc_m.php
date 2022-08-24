<?php

class Etc_m extends CI_Model{	
	
	function __construct(){
		parent::__construct();
	}

	public function get_user_email_signature($company_id){
		$query = $this->db->query(" SELECT * FROM `email_employee_signature` WHERE `email_employee_signature`.`company_id`  = '$company_id' ");
		return $query;
	}

	public function get_work_contractor_details($work_contractor_id){
		$query = $this->db->query(" SELECT `works`.`work_reply_date`,`works`.`work_cpo_date`,`project`.`focus_company_id`,`work_contractors`.`works_contrator_id`, `work_contractors`.`works_id` ,`works`.`work_replyby_time`,`work_contractors`.`ex_gst`,`project`.`job_type`, `work_contractors`.`ex_gst`,
			`project`.`shop_tenancy_number`,`work_contractors`.`prj_quote_review`,`project`.`shop_name`,`work_contractors`.`contractor_notes`, `works`.`project_id`,`project`.`address_id` ,`project`.`address_id` AS `site_address_id`,`project`.`client_id`,`project`.`project_name`, `contact_number`.`office_number` ,`contact_number`.`mobile_number`,
			`contact_person`.`first_name`,`contact_person`.`last_name`, `project`.`project_estiamator_id`,`work_contractors`.`company_id`,`work_contractors`.`is_pending`,`company_details`.`company_name`, `company_details`.`address_id`,
			`email`.`general_email`,`supplier_cat`.`supplier_cat_name`,  `job_sub_category`.`job_sub_cat`,`works`.`contractor_type`,`company_details`.`abn`, `notes`.`comments`,`notes`.`notes`, `considerations`.*, `project`.`project_manager_id`,`project`.`job_category`,
			UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_reply_date`, '%d/%m/%Y') ) AS `unix_reply_date`, `works`.`price`,`work_contractors`.`set_send_feedback` ,`work_contractors`.`contractor_reply_conditions`,`work_contractors`.`inc_gst`, `project`.`is_pending_client`,`works`.`other_work_desc`,`project`.`project_admin_id`,
			`project`.`project_date` 
			FROM `work_contractors`
			LEFT JOIN `works` ON `works`.`works_id` = `work_contractors`.`works_id`
			LEFT JOIN `notes` ON `notes`.`notes_id` =  `works`.`note_id`
			LEFT JOIN `considerations` ON `considerations`.`work_id` = `works`.`works_id`
			LEFT JOIN `job_sub_category` ON `job_sub_category`.`job_sub_cat_id` = `works`.`work_con_sup_id`
			LEFT JOIN `supplier_cat` ON `supplier_cat`.`supplier_cat_id`  = `works`.`work_con_sup_id`
			LEFT JOIN `contact_person` ON `contact_person`.`contact_person_id` = `work_contractors`.`contact_person_id`
			LEFT JOIN `email` ON `email`.`email_id` = `contact_person`.`email_id`
			LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id` = `contact_person`.`contact_number_id`
            LEFT JOIN `project` ON `project`.`project_id` = `works`.`project_id`
            LEFT JOIN `company_details` ON `company_details`.`company_id` =  `work_contractors`.`company_id`
			WHERE `work_contractors`.`works_contrator_id` = '$work_contractor_id' ");
		return $query;
	}

	public function list_group_contractors_per_selected($works_contrator_id){
		$query = $this->db->query("SELECT  `work_contractors`.`works_contrator_id`,`work_contractors`.`ex_gst` FROM `work_contractors` WHERE`work_contractors`.`works_id` IN ( SELECT `wc_a`.`works_id` FROM `work_contractors` `wc_a` WHERE `wc_a`.`works_contrator_id` = '$works_contrator_id' ) AND  `work_contractors`.`feedback_date` IS NULL ORDER BY `work_contractors`.`is_selected` DESC");
		return $query;
	}

	public function get_best_quoted_price($works_contrator_id){
		$query = $this->db->query("SELECT `work_contractors`.`works_contrator_id`,`work_contractors`.`ex_gst` FROM `work_contractors` 
			WHERE`work_contractors`.`works_id` IN ( SELECT `wc_a`.`works_id` FROM `work_contractors` `wc_a` WHERE `wc_a`.`works_contrator_id` = '$works_contrator_id' ) 
			AND `work_contractors`.`ex_gst` > 0 ORDER BY `work_contractors`.`ex_gst` ASC LIMIT 1");
		return $query;
	}

	public function update_sent_feedback($date,$work_contractor_id){
		$this->db->query("UPDATE `work_contractors` SET `feedback_date` = '$date' WHERE `work_contractors`.`works_contrator_id` = '$work_contractor_id'");
	}


	public function get_joineryDetails($works_id,$distinct_character){
		$query = $this->db->query("SELECT   `work_joinery`.`work_joinery_id`,`work_joinery`.`work_cpo_date`,`work_joinery`.`works_id`, `work_joinery`.`note_id`, `work_joinery`.`joinery_id`, `work_joinery`.`distinct_character` ,`notes`.`comments`, `notes`.`notes` ,`joinery`.`joinery_name`
			FROM `work_joinery` 
			LEFT JOIN `notes` ON `notes`.`notes_id` =  `work_joinery`.`note_id`
			LEFT JOIN  `joinery` ON `joinery`.`joinery_id` = `work_joinery`.`joinery_id`
			WHERE `work_joinery`.`works_id`  = '$works_id' AND `work_joinery`.`distinct_character` = '$distinct_character' ");
		return $query;
	}

	public function get_maintenance_contact($project_id){
		$query = $this->db->query("SELECT * FROM `project_site_contacts` WHERE `project_site_contacts`.`project_id` = '$project_id' ");
		return $query;
	}

	function get_work_contractor_joinery($works_contrator_id){
		$query = $this->db->query("SELECT `work_contractors`.`works_contrator_id`, `work_contractors`.`works_id` FROM `work_contractors` WHERE `work_contractors`.`works_contrator_id` = '$works_contrator_id'");
		return $query;
	}

	public function insert_work_reminder($project_id, $estimator_id, $work_contractors_id ){
		 $this->db->query(" INSERT INTO `cqr_reminder` ( `project_id`, `estimator_id`, `work_contractors_id`) VALUES ( '$project_id', '$estimator_id', '$work_contractors_id') ");
	}

	public function get_prime_feedback(){
		$query = $this->db->query(" SELECT * FROM `contractor_feedback` WHERE `contractor_feedback`.`is_active` = '1' AND `contractor_feedback`.`is_prime` = '1'   ");
		return $query;
	}

	public function get_cont_feedbacks($value){
		$query = $this->db->query(" SELECT * FROM `contractor_feedback` 
			WHERE `contractor_feedback`.`is_active` = '1' 
			AND CAST(`contractor_feedback`.`feedback_start_range` AS DECIMAL(4,2)) <= '$value' 
			AND CAST(`contractor_feedback`.`feedback_end_range` AS DECIMAL(4,2)) >= '$value'
			AND CAST(`contractor_feedback`.`feedback_end_range` AS DECIMAL(4,2)) > 0
			AND `contractor_feedback`.`is_prime` != '1'  ");
		return $query;
	}

	public function get_zero_quote_feedback(){
		$query = $this->db->query(" SELECT * FROM `contractor_feedback` WHERE  `contractor_feedback`.`is_active` = '1'
			AND CAST(`contractor_feedback`.`feedback_start_range` AS DECIMAL(4,2)) = 0
			AND CAST(`contractor_feedback`.`feedback_end_range` AS DECIMAL(4,2)) = 0
			AND `contractor_feedback`.`is_prime` = '0' ");
		return $query;
	}

	public function get_email_feedbacks(){
		$query = $this->db->query(" SELECT `selected_contractor_email`,`unsuccessful_contractor_email` FROM `static_defaults` ");
		return $query;
	}



	public function set_remind_day_before($reminder_id,$date){
		$this->db->query(" UPDATE `cqr_reminder` SET `day_left_remind` = '$date' WHERE `cqr_reminder`.`cqr_reminder_id` = '$reminder_id' ");
	}

	public function set_remind_hr_before($reminder_id,$date){
		$this->db->query(" UPDATE `cqr_reminder` SET `hr_left_remind` = '$date' WHERE `cqr_reminder`.`cqr_reminder_id` = '$reminder_id' ");
	}

	public function set_remind_over($reminder_id,$date){
		$this->db->query(" UPDATE `cqr_reminder` SET `no_tender_date` = '$date' WHERE `cqr_reminder`.`cqr_reminder_id` = '$reminder_id' ");
	}

	public function fetch_admin_default_email_message($section = ''){
		if($section == ''){
			$query = $this->db->query("SELECT * FROM `default_email_messages` where section = 'insurance'");
		}else{
			$query = $this->db->query("SELECT * FROM `default_email_messages` where section = '$section'");
		}
		
		return $query;
	}

	public function remove_quote_rev($works_contrator_id){
		$this->db->query(" UPDATE `work_contractors` SET `prj_quote_review` = '0' WHERE `work_contractors`.`works_contrator_id` = '$works_contrator_id' ");
	}

	public function quote_review_ok($works_contrator_id){
		$this->db->query(" UPDATE `work_contractors` SET `is_quote_review_ok` = '1' WHERE `work_contractors`.`works_contrator_id` = '$works_contrator_id' ");
	}

	public function get_remind_day_over($day_today,$today_date){
		$query = $this->db->query("SELECT `cqr_reminder`.* , `work_contractors`.`ex_gst`, `works`.`work_reply_date`,`works`.`other_work_desc`,
			DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') AS `date_reply_formatted`,
			DATEDIFF( DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') , '$day_today') AS `diff`,`project`.`project_name`,
			`works`.`work_replyby_time`,`works`.`works_id`
			FROM `cqr_reminder`

			LEFT JOIN `work_contractors` ON `work_contractors`.`works_contrator_id` =  `cqr_reminder`.`work_contractors_id`
			LEFT JOIN `works` ON `works`.`works_id` = `work_contractors`.`works_id`
            LEFT JOIN `project` ON `project`.`project_id` = `cqr_reminder`.`project_id`

			WHERE  `cqr_reminder`.`no_tender_date` IS NULL
			AND DATEDIFF( DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') , '$day_today') < 0 
			AND `work_contractors`.`ex_gst` IS NULL
			AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_reply_date`, '%d/%m/%Y') ) < UNIX_TIMESTAMP( STR_TO_DATE('$today_date', '%d/%m/%Y') )");
		return $query;
	}

	public function get_remind_day_left($day_today,$today_date,$day_due){ //$day_today, -> YYYY-MM-DD  //$today_date dd/mm/yyyy
		$query = $this->db->query("SELECT `cqr_reminder`.* , `work_contractors`.`ex_gst`, `works`.`work_reply_date`,`works`.`other_work_desc`,
			DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') AS `date_reply_formatted`,
			DATEDIFF( DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') , '$day_today') AS `diff`,`project`.`project_name`,
			`works`.`work_replyby_time`,`works`.`works_id`
			FROM `cqr_reminder`

			LEFT JOIN `work_contractors` ON `work_contractors`.`works_contrator_id` =  `cqr_reminder`.`work_contractors_id`
			LEFT JOIN `works` ON `works`.`works_id` = `work_contractors`.`works_id`

            LEFT JOIN `project` ON `project`.`project_id` = `cqr_reminder`.`project_id`

			WHERE `cqr_reminder`.`day_left_remind` IS NULL
			AND DATEDIFF( DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') , '$day_today') = $day_due
			AND `work_contractors`.`ex_gst` IS NULL
			AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_reply_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$today_date', '%d/%m/%Y') )");
		return $query;
	}



	public function get_remind_hr_left($today_date){ //$day_today, -> YYYY-MM-DD  //$today_date dd/mm/yyyy
		$query = $this->db->query("SELECT `cqr_reminder`.* , `work_contractors`.`ex_gst`, `works`.`work_reply_date`,`works`.`other_work_desc`,
			DATE_FORMAT(STR_TO_DATE(`works`.`work_reply_date`,'%d/%m/%Y'), '%Y-%m-%d') AS `date_reply_formatted`,`project`.`project_name`,
			`works`.`work_replyby_time`,`works`.`works_id`
			FROM `cqr_reminder`

			LEFT JOIN `work_contractors` ON `work_contractors`.`works_contrator_id` =  `cqr_reminder`.`work_contractors_id`
			LEFT JOIN `works` ON `works`.`works_id` = `work_contractors`.`works_id`

            LEFT JOIN `project` ON `project`.`project_id` = `cqr_reminder`.`project_id`

			WHERE `cqr_reminder`.`day_left_remind` IS NOT NULL
			AND `cqr_reminder`.`hr_left_remind` IS NULL
			AND `work_contractors`.`ex_gst` IS NULL
			AND UNIX_TIMESTAMP( STR_TO_DATE(`works`.`work_reply_date`, '%d/%m/%Y') ) = UNIX_TIMESTAMP( STR_TO_DATE('$today_date', '%d/%m/%Y') )");
		return $query;
	}


	public function get_cc_emails(){
		$query = $this->db->query(" SELECT `static_defaults`.`cc_emails_cqr`,`static_defaults`.`cc_emails_cpo`,`static_defaults`.`cc_emails_cqr_reply`,`static_defaults`.`cc_emails_induction` FROM `static_defaults` ");
		return $query;
	}



	public function find_work_reminder($project_id, $estimator_id, $work_contractors_id){
		$query = $this->db->query("SELECT * FROM `cqr_reminder` WHERE `cqr_reminder`.`project_id` = '$project_id' AND `cqr_reminder`.`estimator_id` = '$estimator_id' AND `cqr_reminder`.`work_contractors_id` = '$work_contractors_id'");
		return $query;
	}

	public function check_attachments($project_id){
		$query = $this->db->query("SELECT `storage_files`.*, `storage_doc_type`.`doc_type_name` FROM `storage_files` 
			LEFT JOIN `storage_doc_type` ON `storage_doc_type`.`storage_doc_type_id` =  `storage_files`.`file_type` 
			WHERE `storage_files`.`project_id` = '$project_id'  AND `storage_files`.`is_project_attachment` = '1' AND `storage_files`.`is_active` = '1'");
		return $query;
	}

	public function set_cqr_data($works_contrator_id){
		$query = $this->db->query("UPDATE `work_contractors` SET `cqr_created` = '1' WHERE `work_contractors`.`works_contrator_id`='$works_contrator_id'");
	}

	public function set_cpo_data($works_contrator_id){
		$query = $this->db->query("UPDATE `work_contractors` SET `cpo_created` = '1' WHERE `work_contractors`.`works_contrator_id`='$works_contrator_id'");
	}

	public function set_cqr_sent($works_contrator_id,$cqr_send_date){
		$query = $this->db->query("UPDATE `work_contractors` SET  `cqr_send` = '1', `cqr_send_date` = '$cqr_send_date' WHERE `work_contractors`.`works_contrator_id`='$works_contrator_id'");
	}

	public function set_cpo_sent($works_contrator_id,$cpo_send_date){
		$query = $this->db->query("UPDATE `work_contractors` SET  `cpo_send` = '1', `cpo_send_date` = '$cpo_send_date' WHERE `work_contractors`.`works_contrator_id`='$works_contrator_id'");
	}

	public function insert_job_cost_conditions($works_contrator_id,$ex_gst,$inc_gst,$reply_conditions){
		$query = $this->db->query("UPDATE `work_contractors` SET `ex_gst` = '$ex_gst', `inc_gst` = '$inc_gst', `contractor_reply_conditions` = '$reply_conditions' WHERE `work_contractors`.`works_contrator_id` = '$works_contrator_id' ");
	}

	public function insert_doc_cqr($file_name,$project_id,$date_upload,$file_type=26,$user_id=0){


		$file_query = $this->db->query("SELECT * FROM `storage_files` WHERE `storage_files`.`file_name` = '$file_name' ");

		if(!($file_query->num_rows > 0) ) {
 

			$query = $this->db->query("INSERT INTO `storage_files` ( `file_name`, `file_type`, `project_id`,  `date_upload`, `user_id`, `is_active`, `is_project_attachment`, `is_authorized`, `is_updated`, `for_replacement`, `will_replace_existing`, `replace_by_storage_files_id`) 
				VALUES ( '$file_name', '$file_type', '$project_id', '$date_upload', '$user_id', '1', '0', '0', '0', '0', '0', '0')");
		}

	}

	public function check_insurance($company_id,$date_today){

		$query = $this->db->query("SELECT `company_details`.`company_id`,`company_details`.`company_name`, `company_details`.`public_liability_expiration`, `company_details`.`workers_compensation_expiration`, 
			`company_details`.`income_protection_expiration`, 

			IF( UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`public_liability_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') ), '1','0' )     AS `insurance_a` , 
			IF( UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`workers_compensation_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') ), '1','0' ) AS `insurance_b`, 
			IF( UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`income_protection_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') ), '1','0' )    AS `insurance_c`
			
			FROM `company_details`
			WHERE `company_details`.`company_id` = '$company_id'
			AND (UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`public_liability_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') )
			OR UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`workers_compensation_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') )
			OR UNIX_TIMESTAMP( STR_TO_DATE(`company_details`.`income_protection_expiration`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('$date_today', '%d/%m/%Y') ) ) ");

		return $query;
	}


	public function insert_quote_review($works_contrator_id, $works_id, $company_id, $date, $price, $project_id){
		$query = $this->db->query("INSERT INTO `quote_review` ( `works_contrator_id`, `works_id`, `company_id`, `date`, `price`, `project_id`) 
		VALUES ( '$works_contrator_id', '$works_id', '$company_id', '$date', '$price', '$project_id')");
		return $query;
	}












	public function insert_user_log($user_id,$date,$time,$actions,$project_id,$type){
		$query = $this->db->query("INSERT INTO `user_log` (`user_id`, `date`,`time`, `actions`, `project_id`, `type`) VALUES ('$user_id', '$date','$time', '$actions', '$project_id', '$type')");
		return $query;
	}


	public function select_particular_project($id){
		$query = $this->db->query("SELECT a.*, b.*, 
									c.company_name AS pending_comp_name,
									concat(c.contact_person_fname,' ',c.contact_person_sname) AS pending_cont_person,
									c.contact_number AS pending_cont_number,
									c.email AS pending_cont_email, a.address_id as site_add  
									FROM project a 
										LEFT JOIN company_details b on b.company_id  = a.client_id
										LEFT JOIN company_details_temp c ON c.company_details_temp_id = a.client_id
									WHERE project_id = '$id'
								");
		return $query;
	}

	public function project_is_exempted_induction($project_id){
		$query = $this->db->query("SELECT * from induction_exempted_projects a
										left join project b on b.project_id = a.project_id
										where a.project_id = '$project_id'
								");
		if($query->num_rows == 0){
			return 0;
		}else{
			return 1;
		}
	}

	public function fetch_complete_project_details($project_id){
		$query = $this->db->query("SELECT * from `project`
			LEFT JOIN `contact_person` ON `contact_person`.`contact_person_id` =  `project`.`primary_contact_person_id`
			LEFT JOIN `users` ON `users`.`user_id` =  `project`.`focus_user_id`
			LEFT JOIN `notes` ON `notes`.`notes_id` = `project`.`notes_id`
			LEFT JOIN `brand` ON `brand`.`brand_id` = `project`.`brand_id`
			LEFT JOIN `project_status` ON `project_status`.`project_status_id` = `project`.`project_status_id`
			LEFT JOIN `project_cost_total` ON `project_cost_total`.`project_id` = `project`.`project_id`
			LEFT JOIN `company_details_temp` ON `company_details_temp`.`company_details_temp_id` = `project`.`client_id`
		/*	LEFT JOIN `company_details` ON `company_details`.`company_id` = `project`.`client_id`   */

			WHERE `project`.`project_id` = '$project_id' AND `project`.is_active = '1'  ");
		return $query;
	}
/*
	public function display_company_detail_by_id($id,$is_pending=0){

		if($is_pending == 1){
			$comnd_query = "SELECT * FROM `company_details_temp` WHERE `company_details_temp`.`company_details_temp_id` = '$id' ";
		}else{
			$comnd_query = "SELECT `company_details`.`company_name` FROM  `company_details` WHERE `company_details`.`company_id` =  '$id' ";
		}

		$query = $this->db->query($comnd_query);
		return $query;
	}
*/
	public function display_company_detail_by_id($id,$is_pending=0){
		 

		if($is_pending == 1){
			$comnd_query = "SELECT * FROM `company_details_temp` WHERE `company_details_temp`.`company_details_temp_id` = '$id' ";
		}else{
			$comnd_query = " SELECT `company_details`.*, `company_logo`.logo_path

			FROM  `company_details`
			/*LEFT JOIN  `email` ON  `email`.`email_id` =  `company_details`.`email_id`*/
			/*LEFT JOIN  `contact_number` ON  `contact_number`.`contact_number_id` =  `company_details`.`contact_number_id`*/
			LEFT JOIN  `address_detail` ON  `address_detail`.`address_detail_id` =  `company_details`.`address_id`
			LEFT JOIN  `address_general` ON  `address_general`.`general_address_id` =  `address_detail`.`general_address_id`
			LEFT JOIN  `company_type` ON  `company_type`.`company_type_id` =  `company_details`.`company_type_id`
			LEFT JOIN `company_logo` ON `company_logo`.company_id = `company_details`.company_id
			WHERE   `company_details`.`company_id` =  '$id'  ";
		}

		$query = $this->db->query($comnd_query);
		return $query;



	}

	public function postcode_excempted($postcode){
		$query = $this->db->query("SELECT * from induction_postcode_filters 
										where start_postcode - 1 < '$postcode' 
											and end_postcode + 1 > '$postcode'
								");
		if($query->num_rows == 0){
			return 0;
		}else{
			return 1;
		}
	}

	public function fetch_complete_detail_address($address_detail_id){
		$query = $this->db->query("SELECT  `states`.*,   `address_general`.*,`address_detail`.*
									FROM `address_detail`
									LEFT JOIN `address_general` ON `address_general`.`general_address_id` =`address_detail`.`general_address_id`
									LEFT JOIN `states` ON `states`.`id` = `address_general`.`state_id`
									WHERE  `address_detail`.`address_detail_id`='$address_detail_id'");
		return $query;
	}

	public function get_person_contacts($contact_person_id){
		$query = $this->db->query("SELECT * FROM `contact_person`
			LEFT JOIN `email` ON `email`.`email_id` = `contact_person`.`email_id`
			LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id`  = `contact_person`.`contact_number_id`
			WHERE `contact_person`.`contact_person_id` = '$contact_person_id'");
		return $query;
	}

	public function fetch_email_user($user_id=''){
		$query = $this->db->query("SELECT CONCAT(`users`.`user_first_name`, ' ', `users`.`user_last_name`) AS `user_full_name`,`email`.`email_id`,`email`.`general_email`, `contact_number`.*
				FROM `users` 
				LEFT JOIN `email` ON `email`.`email_id` = `users`.`user_email_id`
				LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id` = `users`.`user_contact_number_id`
				WHERE  `users`.`user_id` = '$user_id' ");

		return $query;
	}

	public function fetch_admin_defaults(){
		$query = $this->db->query("SELECT * FROM `admin_defaults` ORDER BY `admin_defaults`.`admin_default_id` DESC LIMIT 1");
		return $query;
	}
 
	public function fetch_user($user_id=''){
		if($user_id != ''){			
			$query = $this->db->query("SELECT `users`.*,`department`.`department_id`,`department`.`department_name`,`role`.`role_id`,`role`.`role_types`,`email`.`email_id`,`email`.`general_email`,`contact_number`.*,`company_details`.`company_id`,`company_details`.`company_name`,`notes`.`comments`,`users`.`if_admin`, `users`.`direct_company`,`email`.`personal_email`, `users`.`user_id` AS `primary_user_id`
				FROM `users` 
				LEFT JOIN `department` ON `department`.`department_id` =`users`.`user_department_id`
				LEFT JOIN `role` ON `role`.`role_id` = `users`.`user_role_id`
				LEFT JOIN `email` ON `email`.`email_id` = `users`.`user_email_id`
				LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id` = `users`.`user_contact_number_id`
				LEFT JOIN `company_details` ON `company_details`.`company_id` = `users`.`user_focus_company_id`
				LEFT JOIN `notes` ON `notes`.`notes_id` = `users`.`user_comments_id`
				WHERE  `users`.`user_id` = '$user_id' ");
		}else{
			$query = $this->db->query("SELECT `users`.*,`department`.`department_id`,`department`.`department_name`,`role`.`role_types`,`email`.`general_email`,`contact_number`.*,`company_details`.`company_name`,`notes`.`comments`,`users`.`if_admin`, `users`.`direct_company` ,`users`.`is_third_party`,`email`.`personal_email`,if(app_access.app_access_type is null, '',app_access.app_access_type ) as app_access_type
				FROM `users` 
				LEFT JOIN `department` ON `department`.`department_id` =`users`.`user_department_id`
				LEFT JOIN `role` ON `role`.`role_id` = `users`.`user_role_id`
				LEFT JOIN `email` ON `email`.`email_id` = `users`.`user_email_id`
				LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id` = `users`.`user_contact_number_id`
				LEFT JOIN `company_details` ON `company_details`.`company_id` = `users`.`user_focus_company_id`
				LEFT JOIN `notes` ON `notes`.`notes_id` = `users`.`user_comments_id`
				LEFT JOIN (SELECT * FROM app_access GROUP BY user_id ORDER BY user_id) as app_access ON app_access.user_id = `users`.user_id
				WHERE `users`.`is_active` = '1'  ORDER BY `users`.`user_focus_company_id` ASC,`users`.`user_first_name` ASC ");
		}
		return $query;
	}


	public function fetch_induction_videos_generated($project_id){

		$query = $this->db->query("SELECT * from induction_slides_videos
									where project_id = '$project_id'
										and video_uploaded = 1
								");
		if($query->num_rows == 0){
			return 0;
		}else{
			return 1;
		}

	}

	public function fetch_single_company_focus($id){
		$query = $this->db->query("SELECT `admin_company`.`admin_contact_number_id`,`admin_company`.`admin_email_id`, `address_general`.*, `address_detail`.*,  `states`.`name` AS `state_name`,`company_details`.`company_id`,`company_details`.`company_name`,`company_details`.`abn`,`company_details`.`acn`,`company_details`.`bank_account_id`,`company_details`.`address_id`,`company_details`.`postal_address_id`,`admin_company`.`logo`,`admin_company`.`admin_jurisdiction_state_ids`,`bank_account`.*,`contact_number`.`contact_number_id`,`contact_number`.`area_code`,`contact_number`.`office_number`,`contact_number`.`mobile_number`,`email`.`general_email`
			FROM `company_details`			
			LEFT JOIN  `admin_company` ON  `admin_company`.`admin_company_details_id` =  `company_details`.`company_id`
			LEFT JOIN  `bank_account` ON  `bank_account`.`bank_account_id` =  `company_details`.`bank_account_id`
			LEFT JOIN  `contact_number` ON  `contact_number`.`contact_number_id` =  `admin_company`.`admin_contact_number_id`
			LEFT JOIN   `email` ON  `email`.`email_id` = `admin_company`.`admin_email_id`
			LEFT JOIN  `address_detail` ON  `address_detail`.`address_detail_id` =  `company_details`.`address_id` 
			LEFT JOIN  `address_general` ON  `address_general`.`general_address_id` =  `address_detail`.`general_address_id`
			LEFT JOIN  `states` ON  `states`.`id` =  `address_general`.`state_id`
			WHERE `company_details`.`company_id` = '$id'");
		return $query;
	}

	public function get_current_gstRate(){
		$query = $this->db->query("SELECT  `admin_defaults`.`gst_rate` FROM `admin_defaults` ORDER BY `admin_defaults`.`admin_default_id`  DESC LIMIT 1");
		return $query;
	}

}