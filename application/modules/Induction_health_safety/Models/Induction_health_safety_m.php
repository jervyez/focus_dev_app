<?php
namespace App\Modules\Induction_health_safety\Models;

class Induction_health_safety_m{

	protected $db;

    public function __construct(){
        $db = \Config\Database::connect();
        $this->db = $db;
    }
	
	public function fetch_user_emergency_contacts(){
		$query = $this->db->query("SELECT * from sitestaff_emergency_contacts where is_contractors = 0");
		return $query;
	}

	public function fetch_cont_sitestaff_emergency_contacts(){
		$query = $this->db->query("SELECT * from sitestaff_emergency_contacts where is_contractors = 1");
		return $query;
	}

	public function fetch_user_licences_certificates(){
		$query = $this->db->query("SELECT a.*, b.image_path from user_license_certificates a
										LEFT JOIN (SELECT * FROM site_staff_certificate_file WHERE user_type = 0) b on b.contractor_site_staff_id = a.user_id
									where is_contractors = 0 order by expiration_date");
		return $query;
	}

	public function delete_archive($archive_documents_id){
		$query = $this->db->query("UPDATE `archive_documents` SET `is_active` = '0' WHERE `archive_documents`.`archive_documents_id` = '$archive_documents_id'");
		return $query;
	}

	public function fetch_cont_sitestaff_licences_certificates(){
		$query = $this->db->query("SELECT a.*, b.image_path from user_license_certificates a
										LEFT JOIN (SELECT * FROM site_staff_certificate_file WHERE user_type = 1) b on b.contractor_site_staff_id = a.user_id
									where is_contractors = 1");
		return $query;
	}

	public function fetch_user_training_records(){
		$query = $this->db->query("SELECT * from traning_records where is_contractors = 0");
		return $query;
	}

	public function fetch_cont_sitestaff_training_records(){
		$query = $this->db->query("SELECT * from traning_records where is_contractors = 1");
		return $query;
	}

	public function fetch_license_cert_type(){
		$query = $this->db->query("SELECT * from licences_certs_types");
		return $query;
	}

	public function add_emergency_contact($user_id,$contact_fname,$contact_sname,$relation,$contacts,$is_contractors){
		$this->db->query("INSERT INTO sitestaff_emergency_contacts (user_id,contact_fname,contact_sname,relation,contacts,is_contractors) values('$user_id','$contact_fname','$contact_sname','$relation','$contacts','$is_contractors') ");
	}

	public function insert_uploaded_file($type_id,$user_id,$date,$file_name){
		$this->db->query("INSERT INTO `archive_documents` ( `type_id`, `user_id`, `date`, `file_name`) VALUES ( '$type_id', '$user_id', '$date', '$file_name')");
	}
/*
	public function update_archive_expiry($expiry, $user_id, $registry_type_id){
		$this->db->query("UPDATE `archive_registry` SET `expiry` = '$expiry', `is_exp_notified` = '0' WHERE `archive_registry`.`user_id` = '$user_id' AND `archive_registry`.`registry_type_id` = '$registry_type_id' ");
	}
*/


	public function update_archive_expiry($user_id, $registry_type_id){


		$query_a = $this->db->query("SELECT * FROM `archive_registry` WHERE `archive_registry`.`is_active` = '1' AND `archive_registry`.`registry_type_id` = '$registry_type_id'");
		$getResultArray = $query_a->getResultArray();
		$achive_data = array_shift($getResultArray);

	//	var_dump($achive_data );


		$data_expiry_arr = explode('/', $achive_data['expiry']);

		$new_expiry = $data_expiry_arr[0].'/'.$data_expiry_arr[1].'/'.( intval(  date("Y") ) + 1 );



		$this->db->query("UPDATE `archive_registry` SET `expiry` = '$new_expiry', `is_exp_notified` = '0' WHERE `archive_registry`.`user_id` = '$user_id' AND `archive_registry`.`registry_type_id` = '$registry_type_id' ");
	}


	public function list_uploaded_files_arch($type_id,$year){

	//	if($view_old > 0){
			$query = $this->db->query("SELECT * FROM `archive_documents` `ad_main`
				WHERE  `ad_main`.`is_active` = '1'
				AND `ad_main`.`type_id` = '$type_id' 
				AND `ad_main`.`is_active` = '1'  
				AND `ad_main`.`date` LIKE '%/$year' 
				ORDER BY `ad_main`.`archive_documents_id` DESC  ");
/*
		}else{
			$query = $this->db->query("SELECT * FROM `archive_documents` `ad_main`
				WHERE `ad_main`.`date` = ( SELECT `archive_documents`.`date` FROM `archive_documents` WHERE `archive_documents`.`type_id` = '$type_id' GROUP BY `archive_documents`.`date` ORDER BY `archive_documents`.`archive_documents_id` DESC LIMIT 1 )
				AND `ad_main`.`type_id` = '$type_id' AND `ad_main`.`is_active` = '1' ORDER BY `ad_main`.`archive_documents_id` DESC  ");
		}*/

		//$query = $this->db->query("SELECT * FROM `archive_documents` WHERE `archive_documents`.`type_id` = '$type_id' ORDER BY `archive_documents`.`archive_documents_id` DESC");

		return $query;
	}

	public function update_emergency_contact($sitestaff_emergency_contacts_id,$contact_fname,$contact_sname,$relation,$contacts){
		$this->db->query("UPDATE sitestaff_emergency_contacts 
							set contact_fname = '$contact_fname',
								contact_sname = '$contact_sname',
								relation = '$relation',
								contacts = '$contacts'
							where sitestaff_emergency_contacts_id = '$sitestaff_emergency_contacts_id' ");
	}

	public function remove_emergency_contact($sitestaff_emergency_contacts_id){
		$this->db->query("DELETE from sitestaff_emergency_contacts where sitestaff_emergency_contacts_id = '$sitestaff_emergency_contacts_id' ");
	}

	public function remove_site_staff_emergency_contact($user_id){
		$this->db->query("DELETE from sitestaff_emergency_contacts where user_id = '$user_id' ");
	}

	public function add_licence_cert($user_id,$LCtype,$LCName,$lcNumber,$expirationDate,$is_contractors,$has_expiration,$first_dose,$second_dose){
		$today_date = date("Y-m-d");
		$this->db->query("INSERT INTO user_license_certificates (date_entered,user_id,is_license,type,number,expiration_date,has_expiration,is_contractors,first_dose_date,sec_dose_date) values('$today_date','$user_id','$LCtype','$LCName','$lcNumber','$expirationDate','$has_expiration','$is_contractors','$first_dose','$second_dose') ");
	}

	public function update_licence_cert($user_license_certificates_id,$LCtype,$LCName,$lcNumber,$expirationDate,$has_expiration,$first_dose,$second_dose){
		$this->db->query("UPDATE user_license_certificates set is_license = '$LCtype', type = '$LCName' , number = '$lcNumber', expiration_date = '$expirationDate', has_expiration = '$has_expiration', first_dose_date = '$first_dose', sec_dose_date = '$second_dose' where user_license_certificates_id = '$user_license_certificates_id'");
	}

	public function remove_licence_cert($user_license_certificates_id){
		$this->db->query("DELETE from user_license_certificates where user_license_certificates_id = '$user_license_certificates_id'");
	}

	public function add_training($user_id,$trainingName,$trainingDate,$trainingLoc,$is_contractors){
		$today_date = date("Y-m-d");
		$this->db->query("INSERT INTO traning_records (date_entered,user_id,training_type,date_undertaken,taken_with,is_contractors) values('$today_date','$user_id','$trainingName','$trainingDate','$trainingLoc','$is_contractors') ");
	}

	public function update_training($training_records_id,$trainingName,$trainingDate,$trainingLoc){
		$this->db->query("UPDATE traning_records set training_type = '$trainingName',date_undertaken = '$trainingDate',taken_with = '$trainingLoc' where training_records_id = '$training_records_id' ");
	}

	public function remove_training($training_records_id){
		$this->db->query("DELETE from traning_records where training_records_id = '$training_records_id' ");
	}
	
	public function add_lc_type($lctypename){
		$this->db->query("INSERT INTO licences_certs_types (lc_type_name) values('$lctypename') ");	
	}

	public function temp_cont_site_staff(){
		$query = $this->db->query("SELECT distinct(a.company_id), b.company_name from temp_contractors_staff a
										left join company_details b on b.company_id = a.company_id
										where is_approved = 0
								");	
		return $query;
	}

	public function temp_cont_site_staff_submitted(){
		$query = $this->db->query("SELECT distinct(a.company_id), b.company_name from temp_contractors_staff a
										left join company_details b on b.company_id = a.company_id
										where is_approved = 0
											and induction_date_sent != '0000-00-00'
								");	
		return $query;
	}
	

	public function fetch_temp_sitestaff(){
		$query = $this->db->query("SELECT a.*, a.mobile_number as ss_mobile_number,b.company_name, b.induction_date_updated, b.induction_date_sent, d.first_name, d.last_name, e.general_email, f.* 
									from temp_contractors_staff a 
										LEFT join company_details b on b.company_id = a.company_id
										LEFT join (SELECT * FROM contact_person_company WHERE is_primary = 1) c on c.company_id = b.company_id
										LEFT JOIN contact_person d ON  d.contact_person_id =  c.contact_person_id
										LEFT JOIN email e ON e.email_id =  d.email_id
										LEFT JOIN contact_number f ON  f.contact_number_id = d.contact_number_id
									WHERE a.is_updated = 1
									ORDER BY b.company_name,
											a.staff_fname
								");	
		return $query;
	}

	public function fetch_temp_lc(){
		$query = $this->db->query("SELECT a.*, b.image_path from temp_certificate_license  a
										LEFT JOIN (SELECT * FROM site_staff_certificate_file WHERE user_type = 1) b on b.temp_contractors_staff_id = a.temp_contractors_staff_id");	
		return $query;
	}

	public function fetch_temp_training(){
		$query = $this->db->query("SELECT * from temp_trainings");	
		return $query;
	}

	public function update_induction_email_sent($company_id){
		$query = $this->db->query("UPDATE company_details set induction_email_stat = '1' where company_id = '$company_id'");
	}

	public function fetch_temp_contractors(){
		$query = $this->db->query("SELECT distinct(a.company_id), b.company_name, b.induction_date_sent, d.first_name, d.last_name, e.general_email,f.*
									from temp_contractors_staff a
										left join company_details b on a.company_id = b.company_id
										LEFT join contact_person_company c on c.company_id = b.company_id
										LEFT JOIN contact_person d ON  d.contact_person_id =  c.contact_person_id
										LEFT JOIN email e ON e.email_id =  d.email_id
										LEFT JOIN contact_number f ON  f.contact_number_id = d.contact_number_id
									WHERE b.active = '1'
										AND is_approved = 0
										group by b.company_name
								");	
		return $query;
	}

	public function fetch_temp_cont_sitestaff($company_id){
		$query = $this->db->query("SELECT * from temp_contractors_staff where company_id = '$company_id' and is_approved = 0");	
		return $query;
	}

	public function fetch_selected_temp_cont_sitestaff($temp_contractors_staff_id){
		$query = $this->db->query("SELECT * from temp_contractors_staff where temp_contractors_staff_id = '$temp_contractors_staff_id'");	
		return $query;
	}

	public function fetch_temp_license_cert($temp_contractors_staff_id){
		$query = $this->db->query("SELECT * FROM `temp_certificate_license` WHERE `temp_contractors_staff_id` = '$temp_contractors_staff_id'");
		return $query;	
	}

	public function fetch_sitestaff_licences_certificates($user_id){
		$query = $this->db->query("SELECT * from user_license_certificates where user_id = '$user_id' and is_contractors = 1");
		return $query;
	}

	public function fetch_temp_trainings($temp_contractors_staff_id){
		$query = $this->db->query("SELECT * FROM `temp_trainings` WHERE `temp_contractors_staff_id` = '$temp_contractors_staff_id'");
		return $query;	
	}

	public function fetch_sitestaff_training($user_id){
		$query = $this->db->query("SELECT * from traning_records where user_id = '$user_id' and is_contractors = 1");
		return $query;
	}

	public function set_temp_data_approve($temp_contractors_staff_id){
		$query = $this->db->query("UPDATE temp_contractors_staff set is_approved = '1' where temp_contractors_staff_id = '$temp_contractors_staff_id'");
	}

	public function fetch_induction_projects_list($induction_categories,$induction_project_value){
		$query_text = "SELECT * from project where job_category in (".$induction_categories.") and budget_estimate_total >= '".$induction_project_value."' or project_total >= '".$induction_project_value."' and is_active = 1 order by project_id desc";
		$query = $this->db->query($query_text);
		return $query;
	}

	public function fetch_induction_projects_details($project_id){
		$query = $this->db->query("SELECT isd.*,
											a.project_name,
											a.project_date,
											a.date_site_commencement,
											a.date_site_finish,
											a.job_type, 
											a.shop_tenancy_number,
											a.shop_name,
											sc.common_name, 
											b.brand_id,
											b.has_brand_logo, 
											site_address.*, 
											concat(pm.user_first_name,' ',pm.user_last_name) as pm_name,
											pm_contacts.area_code as pm_area_code,
											pm_contacts.office_number as pm_office_number,
											pm_contacts.direct_number as pm_direct_number,
											pm_contacts.mobile_number as pm_mobile_number,
											pm_email.general_email as pm_email,
											concat(lh.user_first_name,' ',lh.user_last_name) as lh_name,
											lh_contacts.area_code as lh_area_code,
											lh_contacts.office_number as lh_office_number,
											lh_contacts.direct_number as lh_direct_number,
											lh_contacts.mobile_number as lh_mobile_number,
											lh_email.general_email as lh_email,
											d.lh_name as manual_lh,
											d.lh_contact as manual_lh_contact,
											d.lh_email as manual_lh_email
									from project a
									left join induction_slide_details isd on isd.project_id = a.project_id
									left join brand b on b.brand_id = a.brand_id
									left join (
													SELECT ad.address_detail_id, 
															ad.unit_number,
															ad.unit_level,
															ad.street,
															ad.po_box,
															ag.suburb,
															ag.postcode,
															s.name,
															s.shortname,
															s.country,
															s.phone_area_code
														from address_detail ad
															left join address_general ag on ag.general_address_id = ad.general_address_id
															left join states s on s.id = ag.state_id
												) site_address on site_address.address_detail_id = a.address_id
									left join (SELECT user_id, user_first_name, user_last_name, user_contact_number_id, user_email_id from users) pm on pm.user_id = a.project_manager_id
									left join (SELECT * from contact_number) pm_contacts on pm_contacts.contact_number_id = pm.user_contact_number_id
									left join (SELECT email_id, general_email from email) pm_email on pm_email.email_id = pm.user_email_id
									left join project_schedule c on c.project_id = a.project_id
									left join (SELECT user_id, user_first_name, user_last_name,user_contact_number_id, user_email_id from users) lh on lh.user_id = c.leading_hand_id
									left join (SELECT * from contact_number) lh_contacts on lh_contacts.contact_number_id = lh.user_contact_number_id
									left join (SELECT email_id, general_email from email) lh_email on lh_email.email_id = lh.user_email_id
									left join manual_entry_project_schedule d on d.project_schedule_id = c.project_schedule_id
									left join shopping_center sc on sc.detail_address_id = a.address_id 
									where a.project_id = '$project_id'
							");
		return $query;
	}

	public function fetch_induction_slide_detials($project_id){
		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		return $query;
	}

	public function update_induction_slide_project_outline($project_id,$project_outline){
		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details (project_id,project_ouline_text) values('$project_id','$project_outline')");
		}else{
			$this->db->query("UPDATE induction_slide_details set project_ouline_text = '$project_outline' where project_id = '$project_id'");
		}
		
	}

	public function update_induction_slide_access($project_id,$file_name){

		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details (project_id,acces_map_filename) values('$project_id','$file_name')");
		}else{
			$this->db->query("UPDATE induction_slide_details set acces_map_filename = '$file_name' where project_id = '$project_id'");
		}
	}

	public function update_induction_videos($project_id){

		$query = $this->db->query("SELECT * from induction_slides_videos where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slides_videos (project_id,video_uploaded) values('$project_id','1')");
		}else{
			$this->db->query("UPDATE induction_slides_videos set video_uploaded = '1' where project_id = '$project_id'");
		}

	}

	public function update_induction_slide_amenities($project_id,$file_name){

		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details (project_id,amenities_map_filename) values('$project_id','$file_name')");
		}else{
			$this->db->query("UPDATE induction_slide_details set amenities_map_filename = '$file_name' where project_id = '$project_id'");
		}
	}

	public function update_induction_slide_emergency($project_id,$epr_medical_name,$epr_medical_contact,$epr_medical_address,$epr_emergency_name,$epr_emergency_contacts,$epr_emergency_address,$med_add_unit_level,$med_add_number,$med_add_street,$med_state_name,$med_add_suburb,$med_add_postcode,$emer_add_unit_level,$emer_add_number,$emer_add_street,$emer_state_name,$emer_add_suburb,$emer_add_postcode
){
		
		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details 
								(project_id,epr_medical_name,epr_medical_contact,epr_medical_address,epr_emergency_name,epr_emergency_contacts,epr_emergency_address,medical_add_unitlevel,medical_add_number,medical_add_street,medical_add_state,medical_add_suburb,medical_add_postcode,emergency_add_unitlevel,emergency_add_number,emergency_add_street,emergency_add_state,emergency_add_suburb,emergency_add_postcode) 
						values('$project_id','$epr_medical_name','$epr_medical_contact','$epr_medical_address','$epr_emergency_name','$epr_emergency_contacts','$epr_emergency_address','$med_add_unit_level','$med_add_number','$med_add_street','$med_state_name','$med_add_suburb','$med_add_postcode','$emer_add_unit_level','$emer_add_number','$emer_add_street','$emer_state_name','$emer_add_suburb','$emer_add_postcode'
)");
		}else{    
			$this->db->query("UPDATE induction_slide_details 
								set epr_medical_name = '$epr_medical_name',
									epr_medical_contact = '$epr_medical_contact',
									epr_medical_address = '$epr_medical_address',
									epr_emergency_name = '$epr_emergency_name',
									epr_emergency_contacts = '$epr_emergency_contacts',
									epr_emergency_address = '$epr_emergency_address',
									medical_add_unitlevel = '$med_add_unit_level',
									medical_add_number = '$med_add_number',
									medical_add_street = '$med_add_street',
									medical_add_state = '$med_state_name',
									medical_add_suburb = '$med_add_suburb',
									medical_add_postcode = '$med_add_postcode',
									emergency_add_unitlevel = '$emer_add_unit_level',
									emergency_add_number = '$emer_add_number',
									emergency_add_street = '$emer_add_street',
									emergency_add_state = '$emer_state_name',
									emergency_add_suburb = '$emer_add_suburb',
									emergency_add_postcode = '$emer_add_postcode'
							where project_id = '$project_id'
						");
		}
	}
	
	public function update_induction_slide_ppe($project_id,$ppe_selected){

		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details (project_id,ppe_list) values('$project_id','$ppe_selected')");
		}else{
			$this->db->query("UPDATE induction_slide_details set ppe_list = '$ppe_selected' where project_id = '$project_id'");
		}
	}
	

	public function update_induction_slide_site_hours($project_id,$generalSiteHours,$noisySiteHours,$otherSiteHours){
		$this->db->query("UPDATE induction_slide_details set general_site_hours = '$generalSiteHours',noisy_site_hours = '$noisySiteHours',other_site_hours = '$otherSiteHours' where project_id = '$project_id'");
	}

	public function set_cleared_slides($slide_no,$project_id){
		$slide = '%'.$slide_no.'%';

		
		$query = $this->db->query("SELECT * from induction_slide_details where project_id = '$project_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_slide_details (project_id,cleared_slides) values('$project_id','$slide_no')");
		}else{
			$query1 = $this->db->query("SELECT * from induction_slide_details where cleared_slides like '$slide' and project_id = '$project_id'");
			if($query1->num_rows == 0){
				$cleared_slide = $slide_no;
				foreach ($query->result_array() as $row){
					$cleared_slide = $row['cleared_slides'].",".$slide_no;
				}

				$this->db->query("UPDATE induction_slide_details set cleared_slides = '$cleared_slide' where project_id = '$project_id'");
			}
		}

		
	}

	public function set_inductions_as_saved($project_id){
		$this->db->query("UPDATE induction_slide_details set is_saved = 1 where project_id = '$project_id'");
	}

	public function fetch_induction_videos(){

		$query = $this->db->query("SELECT a.*, b.video_uploaded, c.project_name, d.company_name
									from induction_slide_details a
										left join induction_slides_videos b on b.project_id = a.project_id
										left join project c on c.project_id = a.project_id
										left join company_details d on d.company_id = c.client_id
									where a.is_saved = 1
									order by b.video_uploaded asc
								");
		return $query;

	}

	public function fetch_induction_videos_generated($project_id){

		$query = $this->db->query("SELECT * from induction_slides_videos
									where project_id = '$project_id'
										and video_uploaded = 1
								");
		if($query->getNumRows() == 0){
			return 0;
		}else{
			return 1;
		}

	}

	public function fetch_state(){

		$query = $this->db->query("SELECT * from states order by name");
		return $query;

	}

	public function fetch_general_address_suburb($state_name){

		$query = $this->db->query("SELECT distinct(a.suburb) as suburb from address_general a
										left join states b on b.id = a.state_id
									where b.name = '$state_name'
									order by a.suburb
								");
		return $query;

	}

	public function fetch_general_address($suburb){

		$query = $this->db->query("SELECT postcode from address_general a
										left join states b on b.id = a.state_id
									where a.suburb = '$suburb'
									order by a.postcode
								");
		return $query;

	}

	public function fetch_induction_video_person_watch($project_id){
		$query = $this->db->query("SELECT a.*, 
										b.site_staff_fname, 
										b.site_staff_sname, 
										d.company_name, 
										c.project_name, 
										e.user_first_name, 
										e.user_last_name,
										f.site_staff_fname as o_site_staff_fname,
										f.site_staff_sname as o_site_staff_sname,
										g.company_name as other_company_name 
									from induction_video_person_watch a
										left join contractos_site_staff b on b.contractor_site_staff_id = a.contractor_site_staff_id
										left join induction_other_company_sitestaff f on f.induction_other_company_sitestaff_id = a.contractor_site_staff_id
										left join induction_other_company g on g.induction_other_company_id = f.induction_other_company_id				
										left join users e on e.user_id = a.contractor_site_staff_id
										left join project c on c.project_id = a.project_id
										left join company_details d on d.company_id = b.company_id
										where a.project_id = '$project_id'
								");
		return $query;
	}

	public function fetch_other_company(){

		$query = $this->db->query("SELECT * from induction_other_company");
		return $query;

	}

	public function view_other_company_site_staff(){

		$query = $this->db->query("SELECT 
										a.*, 
										b.company_name
									from induction_other_company_sitestaff a
									left join induction_other_company b on b.induction_other_company_id = a.induction_other_company_id
								");
		return $query;
	}

	public function view_project_other_company_site_staff($project_id){

		$query = $this->db->query("SELECT 
										a.*, 
										b.company_name, 
										ivpw.induction_video_person_watch_id,
										ies.induction_email_send_id from induction_other_company_sitestaff a
									left join induction_other_company b on b.induction_other_company_id = a.induction_other_company_id
									left join (SELECT * from `induction_video_person_watch` where project_id = '$projec_id' and site_staff_type = 3) as ivpw ON ivpw.contractor_site_staff_id = a.induction_other_company_sitestaff_id
									left join (SELECT * from `induction_email_send` where project_id = '$projec_id' and  site_staff_type = 3) as ies ON ies.site_staff_id = a.induction_other_company_sitestaff_id
								");
		return $query;
	}

	public function insert_other_company($company_name){
		$this->db->query("INSERT INTO induction_other_company (company_name) values('$company_name')");

		$last_insert_id = $this->db->insert_id();
		return $last_insert_id;
	}

	public function insert_other_company_site_staff($company_id,$site_staff_fname,$site_staff_sname,$mobile_number,$email){
		$this->db->query("INSERT INTO induction_other_company_sitestaff (induction_other_company_id,site_staff_fname,site_staff_sname,mobile_number,email) values('$company_id','$site_staff_fname','$site_staff_sname','$mobile_number','$email')");
	}

	public function update_other_company_site_staff($induction_other_company_sitestaff_id,$company_id,$site_staff_fname,$site_staff_sname,$mobile_number,$email){
		$this->db->query("UPDATE induction_other_company_sitestaff 
								set induction_other_company_id = '$company_id',
									site_staff_fname = '$site_staff_fname',
									site_staff_sname = '$site_staff_sname',
									mobile_number = '$mobile_number',
									email = '$email'
								where induction_other_company_sitestaff_id = '$induction_other_company_sitestaff_id'
						");
	}

	public function delete_other_company_site_staff($induction_other_company_sitestaff_id){
		$this->db->query("DELETE from induction_other_company_sitestaff where induction_other_company_sitestaff_id = '$induction_other_company_sitestaff_id'");
	}

	public function get_brand_logo($brand_id){
		$query = $this->db->query("SELECT * from brand where brand_id = '$brand_id' and has_brand_logo = 1 ");
		if($query->getNumRows() == 0){
			return 0;
		}else{
			return 1;
		}
	}

	public function update_brand($brand_id){
		$this->db->query("UPDATE brand set has_brand_logo = 1 where brand_id = '$brand_id' ");
	}
	
	public function get_project_contractors($project_id){
		$query = $this->db->query("SELECT a.company_client_id from works a
									left join contractos_site_staff b on b.company_id = a.company_client_id
									where a.project_id = '$project_id'
										and a.company_client_id != 0
										and b.contractor_site_staff_id is not null
									group by a.company_client_id
								");
		return $query;
	}

	public function save_induction_video_link_sent($project_id,$site_staff_id,$site_staff_type){
		$query = $this->db->query("SELECT * from induction_email_send where project_id = '$project_id' and site_staff_id = '$site_staff_id' and site_staff_type = '$site_staff_type' ");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_email_send (project_id,site_staff_id,site_staff_type) values('$project_id','$site_staff_id','$site_staff_type')");
		}
	}

	public function get_user_site_staff($projec_id){
		$query = $this->db->query("SELECT `users`.*,
									`email`.`general_email`,
									`contact_number`.*,
									`company_details`.`company_name`,
									`notes`.`comments`,
									`users`.`if_admin`,
									`users`.`direct_company`,
									`users`.`is_third_party`,
									`email`.`personal_email`,
									IF(`i_email_send`.induction_email_send_id is null, 0,1)	as induction_email_send_id,	
									IF(`i_video_watch`.induction_video_person_watch_id is null, 0,1) as induction_video_watch
											FROM `users` 
											LEFT JOIN (SELECT * from induction_email_send where project_id = '$projec_id' and site_staff_type = 1) i_email_send on i_email_send.site_staff_id = `users`.user_id
											LEFT JOIN (SELECT * from induction_video_person_watch where project_id = '$projec_id' and site_staff_type = 1)  i_video_watch on i_video_watch.contractor_site_staff_id = `users`.user_id
											LEFT JOIN `email` ON `email`.`email_id` = `users`.`user_email_id`
											LEFT JOIN `contact_number` ON `contact_number`.`contact_number_id` = `users`.`user_contact_number_id`
											LEFT JOIN `company_details` ON `company_details`.`company_id` = `users`.`user_focus_company_id`
											LEFT JOIN `notes` ON `notes`.`notes_id` = `users`.`user_comments_id`
											WHERE `users`.`is_active` = '1' 
												and `users`.`is_site_staff` = '1'
											ORDER BY `users`.`user_focus_company_id` ASC,`users`.`user_first_name` ASC
								");
		return $query;
	}

	function fetch_site_staff($project_id){
		$query = $this->db->query("SELECT 
										a.*, 
										REPLACE(b.company_name, '&apos;', '`') as company_name, 
										c.*, 
										d.*, 
										e.*, 
										ivpw.induction_video_person_watch_id as ss_watched, 
										ies.induction_email_send_id 
										from contractos_site_staff a 
											left join company_details b on b.company_id = a.company_id  
											left join contact_person_company c on c.company_id = b.company_id
											left join contact_person d on d.contact_person_id = c.contact_person_id
											left join email e on e.email_id = d.email_id
											LEFT JOIN (SELECT * from `induction_video_person_watch` where project_id = '$project_id' and site_staff_type = 2) as ivpw ON ivpw.contractor_site_staff_id = a.contractor_site_staff_id
											LEFT JOIN (SELECT * from `induction_email_send` where project_id = '$project_id' and site_staff_type = 2) as ies ON ies.site_staff_id = a.contractor_site_staff_id
											
											where c.is_primary = 1
											order by b.company_name
									");	

		return $query;

	}

	public function fetch_induction_projects(){

		$query = $this->db->query("SELECT a.project_id as project_number, 
									a.*, 
									b.*,
									c.video_uploaded,
									d.company_name,
									f.postcode,
									g.*
								from project a
									left join induction_slide_details b on b.project_id = a.project_id
									left join induction_slides_videos c on c.project_id = a.project_id
									left join company_details d on d.company_id = a.client_id
									left join address_detail e on e.address_detail_id = a.address_id
									left join address_general f on f.general_address_id = e.general_address_id
									LEFT JOIN company_logo g on g.company_id = d.company_id
								where a.job_category in ('Full Fitout', 'Kiosk', 'Refurbishment')
									and a.budget_estimate_total >= 50000
									and a.is_active = 1
									and STR_TO_DATE(a.project_date,'%d/%m/%Y') > '2019-02-18'
								order by a.project_id desc
								");
		return $query;

	}

	public function fetch_project_induction_other_sitestaff($project_id){
		$query = $this->db->query("SELECT *,
										ivpw.induction_video_person_watch_id as ss_watched, 
										ies.induction_email_send_id 
									from induction_project_other_site_staff a
										left join induction_other_company_sitestaff b on b.induction_other_company_sitestaff_id = a.induction_other_company_site_staff_id
										left join induction_other_company c on c.induction_other_company_id = b.induction_other_company_id
										LEFT JOIN (SELECT * from `induction_video_person_watch` where project_id = '$project_id' and site_staff_type = 3) as ivpw ON ivpw.contractor_site_staff_id = a.induction_other_company_site_staff_id
										LEFT JOIN (SELECT * from `induction_email_send` where project_id = '$project_id' and site_staff_type = 3) as ies ON ies.site_staff_id = a.induction_other_company_site_staff_id
									where a.project_id = '$project_id'
								");
		return $query;
		
	}

	public function insert_induction_project_other_site_staff($project_id,$induction_other_company_site_staff_id){
		$query = $this->db->query("SELECT * from induction_project_other_site_staff where project_id = '$project_id' and induction_other_company_site_staff_id = '$induction_other_company_site_staff_id'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO induction_project_other_site_staff (induction_other_company_site_staff_id,project_id) values('$induction_other_company_site_staff_id','$project_id')");
		}
	}

	public function remove_induction_project_other_site_staff($induction_project_other_site_staff_id){
		$this->db->query("DELETE from induction_project_other_site_staff where induction_project_other_site_staff_id = '$induction_project_other_site_staff_id'");
	}

	public function project_is_exempted_induction(){
		$query = $this->db->query("SELECT * from induction_exempted_projects a
										left join project b on b.project_id = a.project_id
								");
		return $query;
	}

	public function fetch_induction_postcode_filters(){
		$query = $this->db->query("SELECT * from induction_postcode_filters");
		return $query;
	}

	public function fetch_all_pa(){
		$query = $this->db->query("SELECT * from users where user_role_id = 2 and is_active = 1");
		return $query;
	}

	public function fetch_contractors_for_update(){
		$query = $this->db->query("SELECT a.company_id, a.induction_email_stat, a.induction_date_sent,
										a.induction_date_updated,a.company_name, e.general_email 
									FROM company_details a
										LEFT JOIN (SELECT DISTINCT(company_id) FROM contractos_site_staff ) b ON b.company_id = a.company_id
										LEFT JOIN (SELECT * FROM contact_person_company WHERE is_primary = 1 AND is_active = 1) c ON c.company_id = a.company_id
										LEFT JOIN contact_person d ON d.contact_person_id = c.contact_person_id
										LEFT JOIN email e ON e.email_id = d.email_id
									WHERE a.active = 1
										AND b.company_id IS NOT NULL
										AND a.induction_date_sent < DATE_SUB(NOW(),INTERVAL 1 YEAR)
									ORDER BY a.induction_date_sent
								");

		return $query;
	}

	public function update_induction_date_updated($company_id,$date_updated){
		$this->db->query("UPDATE company_details set induction_date_updated = '$date_updated' where company_id = '$company_id'");
		
		$query = $this->db->query("SELECT * FROM temp_contractors_staff 
										WHERE company_id = '$company_id'
											AND is_updated = 1
											AND is_completed = 1
								");

		if($query->getNumRows()() == 0){
			$this->db->query("UPDATE company_details set induction_date_sent = '0000-00-00' where company_id = '$company_id'");
		}
		
	}

	public function fetch_company_has_expired_license($company_id){
		$query = $this->db->query("SELECT * 
										FROM contractos_site_staff a
											LEFT JOIN (SELECT * FROM user_license_certificates WHERE is_contractors = 1 AND has_expiration = 1) b ON b.user_id = a.contractor_site_staff_id
										WHERE company_id = '$company_id'
											AND b.expiration_date < NOW()
								");

		return $query;
	}
	
	public function fetch_site_staff_login($project_id){
		$query = $this->db->query("SELECT a.*, 
										date(a.login_datetime) AS log_date,
										TIME(a.login_datetime) AS time_in,
										TIME(a.logout_datetime) AS time_out,
										b.project_name, 
										if(a.is_contractor = 1,CONCAT(c.user_first_name,' ',c.user_last_name), 
											if(a.is_contractor = 2, CONCAT(d.site_staff_fname,' ',d.site_staff_sname),
												if(a.is_contractor = 0, CONCAT(c.user_first_name,' ',c.user_last_name),CONCAT(e.site_staff_fname,' ',e.site_staff_sname))
											)
										) AS site_staff_name,
										f.company_name AS client_name,
										if(a.is_contractor = 1, 'Focus' , if(a.is_contractor = 2, g.company_name, if( a.is_contractor = 3, h.company_name,'Focus'))) AS company_name
									FROM site_staff_site_login a
										LEFT JOIN project b ON b.project_id = a.project_id
										LEFT JOIN users c ON c.user_id = a.user_id
										LEFT JOIN contractos_site_staff d ON d.contractor_site_staff_id = a.user_id
										LEFT JOIN induction_other_company_sitestaff e ON e.induction_other_company_sitestaff_id = a.user_id
										LEFT JOIN company_details f ON f.company_id = b.client_id
										LEFT JOIN company_details g ON g.company_id = d.company_id
										LEFT JOIN induction_other_company h ON h.induction_other_company_id = e.induction_other_company_id
									WHERE a.project_id = '$project_id'
									ORDER BY a.login_datetime
								");
		return $query;
	}

	public function fetch_projects(){
		$query = $this->db->query("SELECT a.*, b.company_name, c.* FROM project a 
											LEFT JOIN company_details b on b.company_id = a.client_id
											LEFT JOIN company_logo c on c.company_id = b.company_id
										WHERE proj_latlong != '' 
											AND is_active = 1
								");
		return $query;
	}

	public function fetch_project_site_login_details($project_id){
		$query = $this->db->query("SELECT a.*, 
										date(a.login_datetime) AS log_date,
										TIME(a.login_datetime) AS time_in,
										TIME(a.logout_datetime) AS time_out,
										b.project_name, 
										if(a.is_contractor = 1,CONCAT(c.user_first_name,' ',c.user_last_name), 
											if(a.is_contractor = 2, CONCAT(d.site_staff_fname,' ',d.site_staff_sname),
												if(a.is_contractor = 0, CONCAT(c.user_first_name,' ',c.user_last_name),CONCAT(e.site_staff_fname,' ',e.site_staff_sname))
											)
										) AS site_staff_name,
										f.company_name AS client_name,
										if(a.is_contractor = 1, 'Focus' , if(a.is_contractor = 2, g.company_name, if( a.is_contractor = 3, h.company_name,'Focus'))) AS company_name
									FROM site_staff_site_login a
										LEFT JOIN project b ON b.project_id = a.project_id
										LEFT JOIN users c ON c.user_id = a.user_id
										LEFT JOIN contractos_site_staff d ON d.contractor_site_staff_id = a.user_id
										LEFT JOIN induction_other_company_sitestaff e ON e.induction_other_company_sitestaff_id = a.user_id
										LEFT JOIN company_details f ON f.company_id = b.client_id
										LEFT JOIN company_details g ON g.company_id = d.company_id
										LEFT JOIN induction_other_company h ON h.induction_other_company_id = e.induction_other_company_id
									WHERE a.project_id = '$project_id'
									ORDER BY a.login_datetime
								");
		return $query;
	}

	public function fetch_logged_sitestaff($project_id){
		$query = $this->db->query("SELECT a.*, 
										if(a.is_contractor = 0,	CONCAT(b.user_first_name,' ',b.user_last_name),if(a.is_contractor = 1, CONCAT(c.site_staff_fname,' ',c.site_staff_sname), CONCAT(d.site_staff_fname,' ',d.site_staff_sname))) AS site_staff
									FROM site_staff_site_login a 
										LEFT JOIN users b ON b.user_id = a.user_id
										LEFT JOIN contractos_site_staff c ON c.contractor_site_staff_id = a.user_id
										LEFT JOIN induction_other_company_sitestaff d ON d.induction_other_company_sitestaff_id = a.user_id
									WHERE a.project_id = '$project_id'
									GROUP BY a.user_id
	
								");
		return $query;
	}

	public function filter_logged_sitestaff($project_id,$user_id,$login_date){
		$query = "SELECT a.*, 
						b.project_name, 
						c.user_first_name,
						c.user_last_name,  
						d.site_staff_fname AS cont_fname,
						d.site_staff_sname AS cont_sname,
						e.site_staff_fname AS other_fname,
						e.site_staff_sname AS other_sname,
						f.company_name
					FROM site_staff_site_login a
						LEFT JOIN project b ON b.project_id = a.project_id
						LEFT JOIN users c ON c.user_id = a.user_id
						LEFT JOIN contractos_site_staff d ON d.contractor_site_staff_id = a.user_id
						LEFT JOIN induction_other_company_sitestaff e ON e.induction_other_company_sitestaff_id = a.user_id
						LEFT JOIN company_details f ON f.company_id = b.client_id
					WHERE a.project_id = '$project_id'
						";
		if($user_id > 0){
			$query = $query ." and a.user_id = '$user_id'";
		}

		if($login_date !== ''){
			$query = $query ." and date(a.login_datetime) = '$login_date'";
		}

		$query = $this->db->query($query);

		return $query;
	}

	public function fetch_site_staff_contractors($project_id){
		$query = $this->db->query("SELECT b.company_id as comp_id, b.company_name as comp_name, c.site_staff_fname FROM works a
										LEFT JOIN company_details b ON b.company_id = a.company_client_id
										LEFT JOIN (SELECT * from contractos_site_staff GROUP BY company_id) c ON c.company_id = a.company_client_id
									WHERE project_id = '$project_id'
										AND a.is_active = 1
										AND b.company_type_id = 2
										AND c.site_staff_fname IS NOT NULL 
									GROUP BY a.company_client_id 
									ORDER BY b.company_name
									");
		// $query = $this->db->query("SELECT company_id AS comp_id, company_name AS comp_name FROM company_details WHERE company_type_id = 2 AND active = 1 order by company_name");
		return $query;
	}

	public function fetch_others_company(){
		$query = $this->db->query("SELECT induction_other_company_id AS comp_id, company_name AS comp_name FROM induction_other_company order by company_name");
		return $query;
	}

	public function fetch_focus_site_staff(){
		$query = $this->db->query("SELECT user_id AS site_staff_id, user_first_name AS ss_fname, user_last_name AS ss_sname  FROM users WHERE is_site_staff = 1 AND is_active = 1 order by user_first_name");
		return $query;
	}

	public function fetch_cont_site_staff($comp_id){
		$query = $this->db->query("SELECT 
										a.contractor_site_staff_id AS site_staff_id,
										a.site_staff_fname AS ss_fname,
										a.site_staff_sname AS ss_sname  
									FROM contractos_site_staff a
										LEFT JOIN induction_video_person_watch b ON a.contractor_site_staff_id = b.contractor_site_staff_id 
									WHERE a.company_id = '$comp_id' 
										AND b.site_staff_type = 2
									ORDER BY site_staff_fname");
		// $query = $this->db->query("SELECT contractor_site_staff_id AS site_staff_id, site_staff_fname AS ss_fname, site_staff_sname AS ss_sname  FROM contractos_site_staff WHERE company_id = '$comp_id' order by site_staff_fname"$query = $this->db->query(
		return $query;
	}

	public function fetch_other_site_staff($comp_id){
		$query = $this->db->query("SELECT induction_other_company_sitestaff_id AS site_staff_id, site_staff_fname AS ss_fname, site_staff_sname AS ss_sname  FROM induction_other_company_sitestaff WHERE induction_other_company_id = '$comp_id' order by site_staff_fname");
		return $query;
	}

	public function insert_site_staff_site_login($user_id,$project_id,$login_datetime,$logout_datetime,$is_contractor){
		$query = $this->db->query("INSERT INTO site_staff_site_login (user_id,project_id,login_datetime,logout_datetime,is_contractor,is_done) VALUES('$user_id','$project_id','$login_datetime','$logout_datetime','$is_contractor',1)");
		return $query;
	}

	public function delete_site_staff_site_login($site_staff_site_login_id){
		$this->db->query("DELETE FROM site_staff_site_login where site_staff_site_login_id = '$site_staff_site_login_id'");
	}

	public function fetch_site_staff_covid_cert_file(){
		$query = $this->db->query("SELECT a.*,
							b.company_id,
							b.site_staff_fname,
							b.site_staff_sname,
							b.mobile_number,
							c.first_dose_date,
							c.sec_dose_date
						FROM site_staff_certificate_file a
							LEFT JOIN contractos_site_staff b ON b.contractor_site_staff_id = a.contractor_site_staff_id
							LEFT JOIN (SELECT user_id, first_dose_date,sec_dose_date FROM user_license_certificates WHERE type = 'COVID-19 Digital Certificate' AND is_contractors = 1) c ON c.user_id = a.contractor_site_staff_id 
						");
		return $query;
	}

	public function fetch_company_with_covid_cert(){
		$query = $this->db->query("SELECT c.company_id, c.company_name FROM temp_certificate_license a 
										LEFT JOIN temp_contractors_staff b ON b.temp_contractors_staff_id = a.temp_contractors_staff_id
										LEFT JOIN company_details c ON c.company_id = b.company_id
									WHERE a.lc_type = 'COVID-19 Digital Certificate'
										GROUP BY c.company_id
								");
		return $query;
	}

	public function fetch_covid_file($temp_contractors_staff_id){
		$query = $this->db->query("SELECT * from site_staff_certificate_file where temp_contractors_staff_id = '$temp_contractors_staff_id'");
		return $query;
	}

	public function fetch_project_per_state($state_id){
		//state set temporary as WA(6)
		$query = $this->db->query("SELECT a.project_id, a.project_name, c.state_id FROM project a
										LEFT JOIN address_detail b ON b.address_detail_id = a.address_id
										LEFT JOIN address_general c ON c.general_address_id = b.general_address_id
									WHERE a.is_active = 1
										AND c.state_id = '$state_id'
								");
		return $query;
	}

	public function insert_covidfile_uploads($staff_id,$image_path,$user_type){
		$query = $this->db->query("SELECT * from site_staff_certificate_file where contractor_site_staff_id = '$staff_id' and user_type = '$user_type'");
		if($query->getNumRows() == 0){
			$this->db->query("INSERT INTO site_staff_certificate_file (contractor_site_staff_id,image_path,user_type) values('$staff_id','$image_path','$user_type')");
		}else{
			$this->db->query("UPDATE site_staff_certificate_file set image_path = '$image_path' where contractor_site_staff_id = '$staff_id' and user_type = '$user_type'");
		}
	}

	public function insert_other_site_staff_covidfile_uploads($staff_id,$first_dose,$second_dose,$attachment_link){
			$this->db->query("UPDATE induction_other_company_sitestaff SET has_covid_cert = 1, first_dose = '$first_dose', second_dose = '$second_dose', attachment_link = '$attachment_link' WHERE induction_other_company_sitestaff_id = '$staff_id'");
	}

	public function approve_certificate_file($temp_contractors_staff_id,$contractor_site_staff_id){
		$this->db->query("UPDATE site_staff_certificate_file set contractor_site_staff_id = '$contractor_site_staff_id' where temp_contractors_staff_id = '$temp_contractors_staff_id' and user_type = '1'");
	}

	public function project_list_for_covid(){
		$query = $this->db->query("SELECT a.project_id, a.project_name  FROM project a 
										LEFT JOIN address_detail b ON b.address_detail_id = a.address_id
										LEFT JOIN address_general c ON c.general_address_id = b.general_address_id
									WHERE a.unaccepted_date = '' 
										AND a.is_active = 1
										AND c.state_id = 6
									ORDER BY project_id desc
								");
		return $query;
	}

	public function project_contractors(){
		$query = $this->db->query("SELECT c.project_id, c.works_id, b.company_id, b.company_name, a.is_selected FROM work_contractors a
										LEFT JOIN company_details b ON a.company_id = b.company_id
										LEFT JOIN works c ON c.works_id = a.works_id
										LEFT JOIN project d ON d.project_id = c.project_id
									WHERE d.is_active = 1
										AND d.unaccepted_date = ''
										AND b.company_type_id = 2
								");
		return $query;
	}

	public function selected_project_contractors($project_id){
		$query = $this->db->query("SELECT c.project_id, c.works_id, b.company_id, b.company_name, a.is_selected FROM work_contractors a
										LEFT JOIN company_details b ON a.company_id = b.company_id
										LEFT JOIN works c ON c.works_id = a.works_id
										LEFT JOIN project d ON d.project_id = c.project_id
									WHERE d.is_active = 1
										AND d.unaccepted_date = ''
										AND c.project_id = '$project_id'
										AND a.is_selected = 1
										AND b.company_type_id = 2
									GROUP BY a.company_id
									ORDER BY b.company_name
								");
		return $query;
	}

	public function fetch_company_site_staff_covid_cert_file($company_id,$project_id){

		$query = $this->db->query("SELECT a.company_id, 
										a.site_staff_fname,
										a.site_staff_sname,
										a.mobile_number,
										b.*,
										c.first_dose_date,
										c.sec_dose_date,
										d.date_watched
										
									FROM contractos_site_staff a
										LEFT JOIN (SELECT * FROM site_staff_certificate_file WHERE user_type = 1) b ON b.contractor_site_staff_id = a.contractor_site_staff_id
										LEFT JOIN (SELECT user_id, first_dose_date,sec_dose_date FROM user_license_certificates WHERE type = 'COVID-19 Digital Certificate' AND is_contractors = 1) c ON c.user_id = a.contractor_site_staff_id 
										LEFT JOIN (SELECT contractor_site_staff_id, project_id, date_watched FROM induction_video_person_watch WHERE site_staff_type = 2 AND project_id = '$project_id') d ON d.contractor_site_staff_id = a.contractor_site_staff_id
									WHERE a.company_id = '$company_id'
									ORDER BY a.site_staff_fname
								");
		return $query;


	}

	public function fetch_focus_site_staff_covid_cert_file($project_id){

		$query = $this->db->query("SELECT 0 AS company_id, 
										a.user_first_name,
										a.user_last_name,
										e.mobile_number,
										b.*,
										c.first_dose_date,
										c.sec_dose_date,
										d.date_watched
										
									FROM users a
										LEFT JOIN (SELECT * FROM site_staff_certificate_file WHERE user_type = 0) b ON b.contractor_site_staff_id = a.user_id
										LEFT JOIN (SELECT user_id, first_dose_date,sec_dose_date FROM user_license_certificates WHERE type = 'COVID-19 Digital Certificate' AND is_contractors = 0) c ON c.user_id = a.user_id 
										LEFT JOIN (SELECT contractor_site_staff_id, project_id, date_watched FROM induction_video_person_watch WHERE site_staff_type = 1 AND project_id = '$project_id') d ON d.contractor_site_staff_id = a.user_id
										LEFT JOIN contact_number e ON e.contact_number_id = a.user_contact_number_id
									WHERE a.user_focus_company_id = 5
										AND a.is_active = 1
										AND a.user_role_id <> 2
									ORDER BY a.user_first_name

								");
		return $query;


	}

	public function fetch_project_site_staff_covid_cert_file($company_id,$project_id){

		$query = $this->db->query("SELECT a.company_id, 
										a.site_staff_fname,
										a.site_staff_sname,
										a.mobile_number,
										b.*,
										c.first_dose_date,
										c.sec_dose_date,
										d.date_watched,
										e.company_name
									FROM contractos_site_staff a
										LEFT JOIN site_staff_certificate_file b ON b.contractor_site_staff_id = a.contractor_site_staff_id
										LEFT JOIN (SELECT user_id, first_dose_date,sec_dose_date FROM user_license_certificates WHERE type = 'COVID-19 Digital Certificate' AND is_contractors = 1) c ON c.user_id = b.contractor_site_staff_id 
										LEFT JOIN (SELECT contractor_site_staff_id, project_id, date_watched FROM induction_video_person_watch WHERE site_staff_type = 2 AND project_id = '$project_id') d ON d.contractor_site_staff_id = a.contractor_site_staff_id
										LEFT JOIN company_details e ON e.company_id = a.company_id
									WHERE d.date_watched is not null
										AND a.company_id = '$company_id'
									ORDER BY e.company_name
								");
		return $query;

		
	}

	public function temp_site_staff_unupdated($temp_contractors_staff_id){
		$query = $this->db->query("UPDATE temp_contractors_staff SET is_updated = 0 where temp_contractors_staff_id = '$temp_contractors_staff_id'");
		return $query;
	}

	public function update_site_staff($contractor_site_staff_id,$ss_fname,$ss_sname,$ss_position,$mobile_number,$email,$company_id,$is_apprentice,$temp_contractors_staff_id = '0'){
			if($temp_contractors_staff_id == '0'){
				$this->db->query("UPDATE contractos_site_staff set company_id = '$company_id', site_staff_fname = '$ss_fname',site_staff_sname = '$ss_sname' ,position = '$ss_position', mobile_number= '$mobile_number', email = '$email', is_apprentice = '$is_apprentice' where contractor_site_staff_id = '$contractor_site_staff_id' ");		
			}else{
				$this->db->query("UPDATE contractos_site_staff set company_id = '$company_id', site_staff_fname = '$ss_fname',site_staff_sname = '$ss_sname' ,position = '$ss_position', mobile_number= '$mobile_number', email = '$email', temp_contractors_staff_id = '$temp_contractors_staff_id', is_apprentice = '$is_apprentice' where contractor_site_staff_id = '$contractor_site_staff_id' ");		
			}
			
	}

}