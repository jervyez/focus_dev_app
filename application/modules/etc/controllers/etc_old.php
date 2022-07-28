<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

class Etc extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->model('etc_m');
		date_default_timezone_set("Australia/Perth");
	}

	public function submit_quote($work_con_sup_id=''){
		date_default_timezone_set("Australia/Perth");


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );


		$current_tmsptp = strtotime('today -'.$admin_defaults->grace_period_mins.' minutes');
		$current_hrstmp = date("Hi") - $admin_defaults->grace_period_mins;

		$work_contractor_supplier_id = $work_con_sup_id;

	//	var_dump($_GET);


		if( isset($_GET['cqr_file']) && $_GET['cqr_file'] != '' ){
			$cqr_file_pdf = $_GET['cqr_file'];
			$this->session->set_userdata('cqr_file_pdf', $cqr_file_pdf);
		}

		 
			
		 


		if(isset($work_contractor_supplier_id) && $work_contractor_supplier_id != ''){
//		}else{
//			
		


			$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_supplier_id);
			$work_contractor = array_shift($work_contractor_q->result() );


			$tender_time_due = date("Hi", strtotime($work_contractor->work_replyby_time));

			if(isset($work_contractor->ex_gst) && $work_contractor->ex_gst > 0 ){
				// go to completed
				//echo 'go to completed';
	 			redirect('/contractor_success_form');
			}else{
				// go to conditions // got to CQR FORM

				if($work_contractor->unix_reply_date > $current_tmsptp){
					$this->session->set_userdata('work_contractor_supplier_id', $work_contractor->works_contrator_id);

					/// go to CQR FORM
					//echo 'go to CQR Screen'; 
					//$this->contractor_quote_form($work_contractor->works_contrator_id);
					redirect('/contractor_quote_form');

				}elseif($work_contractor->unix_reply_date == $current_tmsptp){

					if($tender_time_due >= $current_hrstmp){
						$this->session->set_userdata('work_contractor_supplier_id', $work_contractor->works_contrator_id);
						/// go to CQR FORM
						//echo 'go to CQR Screen'; 
						//$this->contractor_quote_form($work_contractor->works_contrator_id);
						redirect('/contractor_quote_form');

					}else{
						/// go to expire screen
 					redirect('/contractor_expired_form');
					}
				}else{
					/// go to expire screen
 				redirect('/contractor_expired_form');
				}
			}

		}
	}


	public function submit_cqr_form(){   ////




		$gst_rate_q = $this->etc_m->get_current_gstRate();
		$rate = array_shift($gst_rate_q->result() );

		$gst = $rate->gst_rate/100;
		$has_error = 0;

 //	echo '<p id="" class="">'.$gst.'</p>';

//		var_dump($_POST);
//		var_dump($_FILES);

		$time = time();
		$cc_email_arr = array();
		$bcc_email_arr = array();
		

		$works_contrator_id = $this->input->post('works_contrator_id', true);
		$ex_gst = str_replace(',','',  $this->input->post('quote_amnt_exgst', true) );
		$conditions_exclusions = $this->input->post('conditions_exclusions', true);


		$project_id = $this->input->post('project_id', true);
		$work_id = $this->input->post('work_id', true);
		$contractor_id = $this->input->post('contractor_id', true);
		$company_id_pending = $this->input->post('pending_contractor_id', true);
		$focus_company_id = $this->input->post('focus_company_id', true);

		$estimator_full_name = $this->input->post('estimator_full_name', true);
		$estimator_email = $this->input->post('estimator_email', true);
		$project_name = $this->input->post('project_name', true);
		$pa_email = $this->input->post('pa_email', true);





		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$has_attachment = 1;
		}else{
			$has_attachment = 0;
		}



		$q_client_company = $this->etc_m->display_company_detail_by_id($contractor_id,$company_id_pending);
		$contractor = array_shift($q_client_company->result());

		$company_name = strtolower( str_replace(' ', '_', $contractor->company_name )  );


		$user_signature_q = $this->etc_m->get_user_email_signature($focus_company_id);
		$user_signature = array_shift($user_signature_q->result() );



		$cqr_contractor_conditions = str_replace("'",'`',$conditions_exclusions);

		$percent_mnt = $ex_gst * $gst;
		$inc_gst = round( $percent_mnt + $ex_gst, 2);


		$this->etc_m->insert_job_cost_conditions($works_contrator_id,$ex_gst,$inc_gst,$cqr_contractor_conditions);   //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!





		//public function generate_CQR($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0'){
 

// echo '<pre>';var_dump($_POST);echo '<pre>';exit;


		$file_company_name = strtolower( str_replace(' ', '_', $company_name )  );


		$files = $_FILES;

		$cpt = count($_FILES['contractor_attachment']['name']);


		$path =  "./docs/stored_docs";
   		$config = array();
	    $config['upload_path'] = $path."/";
        $config['allowed_types'] = '*';
 
        
	//	echo var_dump(is_dir($path));

        $config['max_size']  = '0';
        $config['max_width']  = '0';
        $config['max_height']  = '0';
        $config['$min_width'] = '0';
        $config['min_height'] = '0';


        $config['overwrite']     = FALSE;
        $date_upload = date('d/m/Y');


        $this->load->library('upload');
     $this->upload->initialize($config);

        $from = $estimator_email;
        

 //       echo '<p id="" class="">'.$cpt.'</p>';

        $file_name_set = $_FILES['contractor_attachment']['name'][0];


/*
        echo '<p class=""> xxxxxxxxxxxxx </p>'; 
        var_dump($contractor_id);
        echo '<p class=""> xxxxxxxxxxxxx </p>';  
*/

        $work_contractor_q = $this->etc_m->get_work_contractor_details($works_contrator_id);
        $contractor_supplier = array_shift($work_contractor_q->result() );


/*
        echo '<p class=""> `````````````````````` </p>'; 
        var_dump($contractor_supplier);
        echo '<p class=""> `````````````````````` </p>'; 
 */
 
        if($company_id_pending == 1){
        	$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_id,1);
        	$pending_contractor = array_shift($q_pending_contractor->result());

        	$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
        	$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

        	$contractor_supplier->company_name = $pending_contractor->company_name;
        	$contractor_supplier->office_number = $pending_contractor->contact_number;
        	$contractor_supplier->general_email = $pending_contractor->email;

        	$contractor_supplier->abn = '';
        	$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
        }
/*
        echo '<p class="">---------------------------</p>'; 
        var_dump($contractor_supplier);
        echo '<p class="">---------------------------</p>'; 

 */

$time = time();



		$file_name = $project_id.'_cqr_reply_'.$work_id.'_'.$file_company_name.'_'.$time.'.pdf';
		
$file_name =  str_replace( array( "'",  '"', ',', '"' ,'%' , '`'  , '&apos',  ' ' , '&',  ';', '<', '>' ), '', $file_name);

		$this->etc_m->insert_doc_cqr($file_name,$project_id,$date_upload);   // reply


//

		$this->generate_CQR($project_id,$work_id,$works_contrator_id,FALSE,'stored_docs',1,$file_name);  // ganerate from contractor reply

		//generate_CQR($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0',$forced_file_name='')

 

        $cpo_reply_email = '<p class="">Greetings,<br /></p>

        <p class="">Your quote submission has been received, we will verify and evaluate your data.</p><p class="">If you have any questions or there is any other information required, please contact me through '.$from.'. <br />';
        $cpo_reply_email .= 'We hope for a long business relationship with your company.</p><p>';

        if($has_attachment == 1){
        	$cpo_reply_email .= '<br />&bull; This job has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
        }


//$file_name =  str_replace( array( "'",  '"', ',', '"' ,'%' , '`'  , '&apos',  ' ' , '&',  ';', '<', '>' ), '', $file_name);

        $cpo_reply_email .= '<br />&bull; Download the Completed CQR (Contractor Quote Request) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$file_name.'" class="" id="" target="_blank" tile="Download Contractor Purchase Order Form"><strong id="" class="">this link</strong></a> to view.</p>';
 
// file of the accomplished CQR here!!!!


        $cpo_reply_email .= '<p style="color: red;"><br /><strong>Covid 19</strong> - please note that it is the responsibility of the contractor to ensure that all personnel attending <strong>ANY</strong> worksites for and on behalf of <strong>Focus Shopfit</strong> comply with all government direction, mandates and requirements for working on site.  This includes any contractors you may engage and the supply of any certification of vaccinations etc <strong>PRIOR</strong> to attending any site.  Should you have any questions relating to this, please ensure you contact a Focus Shopfit staff member before you commence any works.</p>';

		$cpo_reply_email .= '<p><br />Regards,<br /><br />'.$estimator_full_name.'<br /><strong id="" class="">'.$estimator_email.'</strong><br />Estimator</p>';
		$cpo_reply_email .= '<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';





        $email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$cpo_reply_email.'</div>';

        $to = $contractor_supplier->general_email;


	//	$to = 'jervy@focusshopfit.com.au'; /////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! remove_this





  //   echo $email_msg; 

 //   set_send_email($from,$to,$subject,$msg,$arr_cc=array(),$arr_bcc=array())




	//	$set_email_arr = explode(',',$set_cc_emails);
		$cc_email_arr = $this->push_cc_emails('rpl');
/*
		if(count($cc_cqr_emails) > 0){
			$cc_email_arr = array_merge($set_email_arr,$cc_cqr_emails);
		}else{
			$cc_email_arr = $set_email_arr;
		}
*/




		$cc_email_arr = array_filter($cc_email_arr);
		$bcc_email_arr = array_filter($bcc_email_arr);

		array_push($bcc_email_arr, $estimator_email);
	//	array_push($cc_email_arr, 'ian@focusshopfit.com.au');

		if(isset($pa_email) && $pa_email != ''){
			array_push($bcc_email_arr, $pa_email);
		}


	//	$subject = 'Tender Received - '.$project_id.' '.$project_name.' - '.$contractor->company_name;

		 //  [purpose]: [client_name] - [project_number] [project_name] - [contractor_name]



		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		$project_data_arr = array_shift($work_contractor_q->result_array());

		$q_client_company = $this->etc_m->display_company_detail_by_id($project_data_arr['client_id'],$project_data_arr['is_pending_client']);
		$client_company = array_shift($q_client_company->result());


	 $subject = 'Tender Received: '.$client_company->company_name.' - '.$project_id.' '.$project_name.' - '.$contractor->company_name;

/*
        echo '<p class="">'.$from.' ---- '.$to.' ---- '.$subject.'</p>'; 
*/
	 	$this->set_send_email($from,$to,$subject,$email_msg,$cc_email_arr,$bcc_email_arr); // done



	 	if (isset($file_name_set) && $file_name_set != ''){

	 		for($i=0; $i<$cpt; $i++){

	 			$file_name = $files['contractor_attachment']['name'][$i];
	 			$path_parts = pathinfo($file_name);
	 			$extension = strtolower($path_parts['extension']);

	 			$data_file_name = $project_id.'_cqr_reply_'.$work_id.'_'.$file_company_name.'_'.$path_parts['filename'].'_'.$time.'.'.$extension;

	 			$file_name_set = str_replace(' ', '_', $data_file_name);
	 			$file_name_set_final = str_replace("'", '`', $file_name_set);
	 		//	$file_name_amp = str_replace('&', '_and_', $file_name_set_final);

	 			$file_name_amp =  str_replace( array( "'",  '"', ',', '"'  , '`' ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $file_name_set_final);



		 // 	echo '<p id="" class="">'.$file_name_amp.'</p>';

	 			$_FILES['contractor_attachment']['name']= $file_name_amp;
	 			$_FILES['contractor_attachment']['tmp_name']= $files['contractor_attachment']['tmp_name'][$i];
	 			$_FILES['contractor_attachment']['type']= $files['contractor_attachment']['type'][$i];
	 			$_FILES['contractor_attachment']['error']= $files['contractor_attachment']['error'][$i];
	 			$_FILES['contractor_attachment']['size']= $files['contractor_attachment']['size'][$i];  

	 			if ( !$this->upload->do_upload('contractor_attachment')) {
	 				echo $this->upload->display_errors();
	 				$has_error = 1;

	 			//	echo '<p>'.$config['upload_path'].'</p>'; 
	 			//	echo '<p>'.$has_error.'</p>'; 
	 				exit;
	 			}else{


$file_name_amp =  str_replace( array( "'",  '"', ',', '"' ,'%' , '`'  ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $file_name_amp);

        			$this->etc_m->insert_doc_cqr($file_name_amp,$project_id,$date_upload); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! // contractor file uploaded
	 			}
        	}

        } 


	 	



		/*
		array(6) { ["quote_amnt_exgst"]=> string(3) "100" ["conditions_exclusions"]=> string(4) "test" ["works_contrator_id"]=> string(5) "74840" 
		["project_id"]=> string(5) "45132" ["work_id"]=> string(6) "346843" 
		["client_id"]=> string(3) "726" }


		 array(1) { ["contractor_attachment"]=> array(5) { ["name"]=> array(1) { [0]=> string(27) "Footlocker_Rockingham_5.jpg" } ["type"]=> array(1) { [0]=> string(10) "image/jpeg" } ["tmp_name"]=> array(1) { [0]=> string(14) "/tmp/phpBjXPXn" } ["error"]=> array(1) { [0]=> int(0) } ["size"]=> array(1) { [0]=> int(493711) } } }

		*/
/*
 		echo '<p id="" class="">'.$ex_gst.' '.$gst.' '.$inc_gst.'  '.$works_contrator_id.'  </p>';
 		echo '<p class="">----------------------</p>'; 
 		
*/


$this->session->sess_destroy();


//echo '<p id="" class="">'.$email_msg.' '.$from.' '.$to.'  '.$subject.'  </p>';



 		if($has_error != 1){
	redirect('/contractor_success_form'); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! contractor_reply
 		}

	}

	public function contractor_quote_form(){

		$work_contractor_supplier_id = $this->session->userdata('work_contractor_supplier_id');
		$cqr_file_pdf = $this->session->userdata('cqr_file_pdf');


		if( isset($work_contractor_supplier_id) && $work_contractor_supplier_id != '' ){

		}else{
			redirect('/contractor_success_form');
		}








//	 	echo '<p id="" class="">'.$work_contractor_supplier_id.'</p>';

		$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_supplier_id);
		$work_contractor_data_arr = array_shift($work_contractor_q->result_array());

		$project_id = $work_contractor_data_arr['project_id'];
		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		$project_data_arr = array_shift($work_contractor_q->result_array());

		$site_addr_id = $project_data_arr['site_address_id'];
		$q_postal_company = $this->etc_m->fetch_complete_detail_address($site_addr_id);
		$site_addr_arr = array_shift($q_postal_company->result_array());




		$project_estiamator_id = $project_data_arr['project_estiamator_id'];
	//	$q_esti_contact = $this->etc_m->fetch_email_user($project_estiamator_id);
	//	$esti_contact_arr = array_shift($q_esti_contact->result_array());




 
		$pa_email = '';

		if($project_estiamator_id > 0){
				$q_sender_contact = $this->etc_m->fetch_email_user($project_estiamator_id);
				$esti_contact_arr = array_shift($q_sender_contact->result_array());
		
		}else{
			$q_sender_contact = $this->etc_m->fetch_email_user($project_data_arr['project_manager_id']);
			$esti_contact_arr = array_shift($q_sender_contact->result_array());

		}


		$q_pa_contact = $this->etc_m->fetch_user($project_data_arr['project_admin_id']);
		$pa_contact = array_shift($q_pa_contact->result());

		$pa_email = $pa_contact->general_email;

		$q_pending_contractor = $this->etc_m->display_company_detail_by_id($work_contractor_data_arr['company_id'],$work_contractor_data_arr['is_pending']);
		$pending_contractor = array_shift($q_pending_contractor->result());

		$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );





	//	var_dump($file_company_name);



 /*
		var_dump($work_contractor_data_arr);
		echo '<p id="" class="">----</p>';
		var_dump($project_data_arr);
		echo '<p id="" class="">----</p>';
		var_dump($site_addr_arr);
		echo '<p id="" class="">----</p>';
		var_dump($esti_contact_arr);
		echo '<p id="" class="">----</p>';
 */
		$data = array_merge($work_contractor_data_arr, $project_data_arr,  $site_addr_arr,  $esti_contact_arr);
		$data['company_file_name'] = $file_company_name;
		$data['pa_email'] = $pa_email;





		$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($data['works_contrator_id']);

		$data['joinery_work_name'] = '';

		if($work_contractor_joinery->num_rows > 0){

			$work_joinery_id = array_shift($work_contractor_joinery->result() );
			$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
			$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

			if($joinery_work_q->num_rows > 0){
				$joinery_work_details = array_shift($joinery_work_q->result() );
				$data['joinery_work_name'] = 'Joinery - '.$joinery_work_details->joinery_name;
			//	$contractor_supplier->notes = $joinery_work_details->notes;
			}

		}




		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$data['has_attachment'] = 1;
		}else{
			$data['has_attachment'] = 0;
		}

  // 	var_dump($data);



		if( isset($cqr_file_pdf) && $cqr_file_pdf != '' ){
			$data['cqr_file_pdf'] = $cqr_file_pdf;
		}else{
			$data['cqr_file_pdf'] = '';
		}

		$this->load->view("quote_reply_form_v",$data);

	}

	public function contractor_expired_form(){
		$this->load->view("expired_reply_form_v");
	}

	public function contractor_success_form(){
		$this->load->view("done_reply_form_v");
	}

	public function error_form(){
		$this->load->view("error_form");
	}

	public function has_inssurance($company_id){

		$date_today = date('d/m/Y');
		$q_check_insurance = $this->etc_m->check_insurance($company_id,$date_today);

		if($q_check_insurance->num_rows > 0){

			$insurance = array_shift($q_check_insurance->result() );
			$sum_inssurance = $insurance->insurance_a + $insurance->insurance_b + $insurance->insurance_c;

			if($sum_inssurance >= 2){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function push_cc_emails($area){

		$q_get_cc_emails = $this->etc_m->get_cc_emails();
		$static_emails = array_shift($q_get_cc_emails->result() );

		$cc_emails_cqr = explode(',',$static_emails->cc_emails_cqr);
		$cc_emails_cpo = explode(',',$static_emails->cc_emails_cpo);
		$cc_emails_cqr_reply = explode(',',$static_emails->cc_emails_cqr_reply);
		$cc_emails_induction = explode(',',$static_emails->cc_emails_induction);

		$cc_array = array();

		switch ($area) {
			case "cqr":
				if( strlen($static_emails->cc_emails_cqr) > 0 ):
					foreach ($cc_emails_cqr as $key => $user_id):
						$q_cc_emails = $this->etc_m->fetch_email_user($user_id);
						$cc_email = array_shift($q_cc_emails->result());
						array_push($cc_array,$cc_email->general_email);
					endforeach;
				endif;
			break;
			case "rpl":
				if( strlen($static_emails->cc_emails_cqr_reply) > 0 ):
					foreach ($cc_emails_cqr_reply as $key => $user_id):
						$q_cc_emails = $this->etc_m->fetch_email_user($user_id);
						$cc_email = array_shift($q_cc_emails->result());
						array_push($cc_array,$cc_email->general_email);
					endforeach;
				endif;
			break;
			case "cpo":
				if( strlen($static_emails->cc_emails_cpo) > 0 ):
					foreach ($cc_emails_cpo as $key => $user_id):
						$q_cc_emails = $this->etc_m->fetch_email_user($user_id);
						$cc_email = array_shift($q_cc_emails->result());
						array_push($cc_array,$cc_email->general_email);
					endforeach;
				endif;
			break;
			case "ind":
				if( strlen($static_emails->cc_emails_induction) > 0 ):
					foreach ($cc_emails_induction as $key => $user_id):
						$q_cc_emails = $this->etc_m->fetch_email_user($user_id);
						$cc_email = array_shift($q_cc_emails->result());
						array_push($cc_array,$cc_email->general_email);
					endforeach;
				endif;
			break;
		}

/*
		echo '<pre>';
		var_dump(count($cc_array));
		echo '<pre>';


		echo '<pre>';
		var_dump($cc_array);
		echo '<pre>';

*/

		return $cc_array;
	}


	public function process_cqr(){ //
 

		$project_id = $this->input->post('project_id', true);
		$sel_contractor = $this->input->post('sel_cotnractor', true);
		$set_attach_mss = $this->input->post('set_attach_mss', true);



	//	$user_id = $this->session->userdata('user_id');
	//	$user_focus_company_id = $this->session->userdata('user_focus_company_id');


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );


		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$has_attachment = 1;
		}else{
			$has_attachment = 0;
		}

		$set_cc_emails = $this->input->post('set_cc_emails', true);
		$set_bcc_emails = $this->input->post('set_bcc_emails', true);
		$extra_msg = $this->input->post('extra_msg', true);


		$set_email_arr = explode(',',$set_cc_emails);
		$cc_cqr_emails = $this->push_cc_emails('cqr');

		if(count($cc_cqr_emails) > 0){
			$cc_email_arr = array_merge($set_email_arr,$cc_cqr_emails);
		}else{
			$cc_email_arr = $set_email_arr;
		}


		$bcc_email_arr = explode(',',$set_bcc_emails);


		foreach ($sel_contractor as $key => $work_contractor_id){

			$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_id);
			$row = array_shift($work_contractor_q->result() );

			if($row->project_estiamator_id > 0){
				$q_sender_contact = $this->etc_m->fetch_user($row->project_estiamator_id);
				$sender_contact = array_shift($q_sender_contact->result());
			}else{
				$q_sender_contact = $this->etc_m->fetch_user($row->project_manager_id);
				$sender_contact = array_shift($q_sender_contact->result());

				$q_pa_contact = $this->etc_m->fetch_user($row->project_admin_id);
				$pa_contact = array_shift($q_pa_contact->result());
				/// if no estimator PA gets a copy
			}





			$user_signature_q = $this->etc_m->get_user_email_signature($row->focus_company_id);
			$user_signature = array_shift($user_signature_q->result() );




		//	foreach ($work_contractor_q->result() as $row){

			$company_contractor_supplier_id = $row->company_id;


//echo '<p id="" class="">'.$project_id.' --- '.$row->works_id.' --- '.$company_contractor_supplier_id.' --- '.$row->is_pending.'--- '.$work_contractor_id.'</p>'; 
/*
			if($set_attach_mss == 1){
				$this->generate_SiteSheet($project_id,$row->works_id,$work_contractor_id);
			}

*/



			$check_work_reminder = $this->etc_m->find_work_reminder($project_id, $row->project_estiamator_id, $work_contractor_id);

			if( !($check_work_reminder->num_rows > 0)  ){
				$this->etc_m->insert_work_reminder($project_id, $row->project_estiamator_id, $work_contractor_id);
			}


			$work_job_name = '';
			$site_add = '';

			$cqr_email_template = $admin_defaults->cqr_email_template;
			$cqr_email_template = str_replace('#ffff00;','style="background-color: #ffff00;"', $cqr_email_template); 

			$button_var = '<h2><a href="https://sojourn.focusshopfit.com.au/submit_quote/_work_con_id_?cqr_file=_pdf_cqr_file_" style="padding: 8px 10px; text-decoration: none; border-radius: 5px; border: 1px solid #c8c6c4; color: #4f6bed; background-color: #EFEFEF;" target="_blank" title="Online Quotation Form">Online Quotation Form</a></h2>';
			$cqr_email_template = str_replace('online_quotation_form',$button_var, $cqr_email_template); 


			if($row->contractor_type == 2){
				$work_job_name = $row->job_sub_cat;
			}else{
				$work_job_name = $row->supplier_cat_name;
			}

			if($work_job_name == 'Other'){
				$work_job_name = $row->other_work_desc;
			}



			$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($work_contractor_id);

			if($work_contractor_joinery->num_rows > 0){

				$work_joinery_id = array_shift($work_contractor_joinery->result() );
				$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
				$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

				if($joinery_work_q->num_rows > 0){
					$joinery_work_details = array_shift($joinery_work_q->result() );
					$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
					$row->notes = $joinery_work_details->notes;
				}

			}





	
			$q_client_company = $this->etc_m->display_company_detail_by_id($row->client_id,$row->is_pending_client);
			$client_company = array_shift($q_client_company->result());





			$q_pending_contractor = $this->etc_m->display_company_detail_by_id($row->company_id,$row->is_pending);
			$pending_contractor = array_shift($q_pending_contractor->result());


			$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );


			$date_upload = date('d/m/Y');
			$time = time();


			$file_name = $project_id.'_cqr_'.$row->works_id.'_'.$file_company_name.'_'.$time.'.pdf';

			$user_id = $this->session->userdata('user_id');


$file_name =  str_replace( array( "'",  '"', ',', '"' ,'%'  , '`' ,  '&apos', ' ' ,  '&', ';', '<', '>' ), '', $file_name);

			$this->etc_m->insert_doc_cqr($file_name,$project_id,$date_upload,7,$user_id);

			$cqr_pdf_file = $project_id.'_cqr_'.$row->works_id.'_'.$file_company_name.'_'.$work_contractor_id;

//$file_company_name =  str_replace( array( "'",  '"', ',', '"' ,'%' , '`'  ,  '&apos', ' ' ,  '&', ';', '<', '>' ), '', $file_company_name);


			$this->generate_CQR($project_id,$row->works_id,$work_contractor_id,FALSE,'stored_docs',0,$file_name);  // process_cqr
			//generate_CQR($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0',$forced_file_name='')


			$query_client_address = $this->etc_m->fetch_complete_detail_address($row->site_address_id);
			$site_address = array_shift($query_client_address->result());
/*
			$q_esti_contact = $this->etc_m->fetch_email_user($row->project_estiamator_id);
			$esti_contact = array_shift($q_esti_contact->result());
*/

			if($row->project_estiamator_id > 0){
			$q_est_contact = $this->etc_m->fetch_email_user($row->project_estiamator_id);
			$esti_contact = array_shift($q_est_contact->result());
		}else{

			$q_est_contact = $this->etc_m->fetch_email_user($row->project_manager_id);
			$esti_contact = array_shift($q_est_contact->result());
		}



			if($row->job_type == 'Shopping Center'){
				$site_add .= $row->shop_tenancy_number.': '.$row->shop_name.', ';
			}

			if( isset($site_address->unit_level) && $site_address->unit_level != '' ){
				$site_add .= 'Unit '.$site_address->unit_level.'/';
			}

			$site_add .= $site_address->unit_number.' '.$site_address->street.' '. ucwords(strtolower($site_address->suburb)).', '.$site_address->shortname.', '.$site_address->postcode;

			$reply_dateTime_due = $row->work_reply_date.' '.$row->work_replyby_time;

			$cqr_email_template = str_replace('work_name',$work_job_name, $cqr_email_template); 
			$cqr_email_template = str_replace('client_name',$client_company->company_name, $cqr_email_template);
			$cqr_email_template = str_replace('site_address',$site_add, $cqr_email_template);
			$cqr_email_template = str_replace('tender_due_datetime',$reply_dateTime_due, $cqr_email_template);
			$cqr_email_template = str_replace('estimator_name',$esti_contact->user_full_name, $cqr_email_template);
			$cqr_email_template = str_replace('_work_con_id_',$work_contractor_id, $cqr_email_template);
			$cqr_email_template = str_replace('_pdf_cqr_file_',$file_name, $cqr_email_template);

			$cqr_email_template .= '<p>';
			if( strlen($row->notes) > 5 ){
				$cqr_email_template .= '<pre style="font-family: verdana,sans-serif; font-size:12px; "><strong id="" class="">PLEASE NOTE</strong><br />'.$row->notes.'</pre>';
			}

			if($has_attachment == 1){
				$cqr_email_template .= '<br />&bull; This project has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
			}


			$cqr_email_template .= '<br />&bull; Download the CQR (Contractor Quote Request) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$file_name.'" class="" id="" target="_blank" tile="Download Contractor Quote Request Form"><strong id="" class="">this link</strong></a> to view.</p>';



			$cqr_email_template .= '<p>';
			if(!empty($extra_msg)){
				$cqr_email_template .= '<pre style="font-family: verdana,sans-serif; font-size:12px; "><br /><strong id="" class="">More Details:</strong><br />'.$extra_msg.'</pre>';
			}


			$cqr_email_template .= '<p style="color: red;"><br /><strong>Covid 19</strong> - please note that it is the responsibility of the contractor to ensure that all personnel attending <strong>ANY</strong> worksites for and on behalf of <strong>Focus Shopfit</strong> comply with all government direction, mandates and requirements for working on site.  This includes any contractors you may engage and the supply of any certification of vaccinations etc <strong>PRIOR</strong> to attending any site.  Should you have any questions relating to this, please ensure you contact a Focus Shopfit staff member before you commence any works.</p>';

			$cqr_email_template .= '<br />Regards,<br /><br />'.$sender_contact->user_first_name.' '.$sender_contact->user_last_name.'<br /><strong id="" class="">'.$sender_contact->general_email.'</strong><br />'.$sender_contact->role_types.'</p>';
			$cqr_email_template .= '<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';

			$email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$cqr_email_template.'</div>';



/*
			echo '<p id="" class="">'.$row->general_email.'</p>';
			echo '<p id="" class="">'.$sender_contact->general_email.'</p>';

			print_r($cc_email_arr);
			print_r($bcc_email_arr);

			echo '<p id="" class="">----------------------</p>';
*/
			$from = $sender_contact->general_email;
			$to = $row->general_email;

			if($row->is_pending == 1){
				$to = $pending_contractor->email;
			}


		//	'.$client_company->project_name.'

	//		$subject = 'Focus Shopfit Tender - '.$client_company->company_name.', '.ucwords(strtolower($site_address->suburb)).', '.$site_address->shortname.' - '.$project_id;

			$contractor_name = $pending_contractor->company_name;



			$subject = 'Contractor Quotation Request: '.$client_company->company_name.' - '.$project_id.' '.$row->project_name.' - '.$contractor_name;
/*

Contractor Quotation Request, Contractor Purchase Order, Tender Received, Tender Reminder, Tender not Received 	

[purpose]: [client_name] - [project_number] [project_name] - [contractor_name]


	   

*/

	//	 	$to = 'jervy@focusshopfit.com.au'; /////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! remove_this


			array_push($bcc_email_arr, $sender_contact->general_email);


			if(!($row->project_estiamator_id > 0)){
				array_push($cc_email_arr, $pa_contact->general_email);
			}

			$cc_email_arr = array_filter($cc_email_arr);
			$bcc_email_arr = array_filter($bcc_email_arr);

			$this->set_send_email($from,$to,$subject,$email_msg,$cc_email_arr,$bcc_email_arr); //done


		 //	echo '<p class="">'.$from.' ------ '.$to.' ------ '.$subject.'</p>'; 

			$cqr_send_date = date('d/m/Y');
			$this->etc_m->set_cqr_sent($work_contractor_id,$cqr_send_date);


//	echo '<p id="" class="">'.$email_msg.'</p>';


			/*
				set_cqr_data($works_contrator_id)
				set_cqr_sent($works_contrator_id,$cqr_send_date)
				$sender_contact->general_email
				$cc_email_arr
				$bcc_email_arr
			*/
		}

//		var_dump($this->session->userdata);

	redirect(base_url().'projects/view/'.$project_id.'?tab=works');  ////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!11 process_cqr

	}

	public function remind_hr_left(){


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );

	//	$today_time = date('h:i A');

		$today_time = date('H:i', strtotime('-'.$admin_defaults->hrs_before_due.' hour'));

	//	$today_time = '5:00 PM';

		$has_attachment = 0;

		$day_today = date('Y-m-d');
		$today_date = date('d/m/Y');

	//	echo '<p class="">'.$day_today.' --- '.$project_id.' --- '.$today_date.'</p>'; 


		$works_contractor_q = $this->etc_m->get_remind_hr_left($today_date);

		foreach ($works_contractor_q->result() as $works_contractor){


			$starttimestamp = strtotime($today_time);
			$endtimestamp = strtotime($works_contractor->work_replyby_time);
			$difference = abs($endtimestamp - $starttimestamp)/3600;
			$difference = number_format($difference,2);

			
			if($difference <= 0.17){ // 15 mins - 30 mins allowance

				// echo 'its time';
		//	echo '<p class="">'.$today_time.' ------ '.$works_contractor->work_replyby_time.' ------ '.$difference.'</p>'; 

				$attachment_q = $this->etc_m->check_attachments($works_contractor->project_id);
				if($attachment_q->num_rows > 0){
					$has_attachment = 1;
				}else{
					$has_attachment = 0;
				}

			$work_contractor_q = $this->etc_m->get_work_contractor_details($works_contractor->work_contractors_id);
			$contractor_supplier = array_shift($work_contractor_q->result() );

			if($contractor_supplier->contractor_type == 2){
				$work_job_name = $contractor_supplier->job_sub_cat;
			}else{
				$work_job_name = $contractor_supplier->supplier_cat_name;
			}

			$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($works_contractor->work_contractors_id);

			if($work_contractor_joinery->num_rows > 0){

				$work_joinery_id = array_shift($work_contractor_joinery->result() );
				$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
				$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

				if($joinery_work_q->num_rows > 0){
					$joinery_work_details = array_shift($joinery_work_q->result() );
					$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				}
			}

			if($work_job_name == 'Other'){
				$work_job_name = $contractor_supplier->other_work_desc;
			}

			$q_client_company = $this->etc_m->display_company_detail_by_id($contractor_supplier->client_id,$contractor_supplier->is_pending_client);
			$client_company = array_shift($q_client_company->result());

			if($contractor_supplier->project_estiamator_id > 0){
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_estiamator_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}else{
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_manager_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}



			$user_signature_q = $this->etc_m->get_user_email_signature($contractor_supplier->focus_company_id);
			$user_signature = array_shift($user_signature_q->result() );
 


			$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




			if($contractor_supplier->is_pending == 1){
				$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
				$pending_contractor = array_shift($q_pending_contractor->result());

				$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
				$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

				$contractor_supplier->company_name = $pending_contractor->company_name;
				$contractor_supplier_location = '';

				$contractor_supplier->office_number = $pending_contractor->contact_number;
				$contractor_supplier->general_email = $pending_contractor->email;

				$contractor_supplier->abn = '';
				$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
			}



			//echo '<p class="">-- '.$file_company_name.' --</p>'; 

			$message = '<p class="">Greetings,</p>
			<p class="">You are receiving this reminder that your tender submission for <strong style="background-color: #ffff00;">'.$work_job_name.'</strong> job for our client <strong style="background-color: #ffff00;">'.$client_company->company_name.'</strong> is about to end in '.$admin_defaults->hrs_before_due.' hour(s)
			<br />If for any reason you do not believe you will be able to meet this deadline, then please contact <strong style="background-color: #ffff00;">'.$esti_contact->user_full_name.'</strong> at <strong class=""><u>'.$esti_contact->general_email.'</u></strong>.</p>
			<p class=""><br />Tenders received after the deadline may not be accepted.</p><p class="">Your tender <strong class="">MUST BE SUBMITTED ONLINE</strong> by clicking on this link.</p>
			<h2><a href="https://sojourn.focusshopfit.com.au/submit_quote/'.$works_contractor->work_contractors_id.'" style="padding: 8px 10px; text-decoration: none; border-radius: 5px; border: 1px solid #c8c6c4; color: #4f6bed; background-color: #EFEFEF;" target="_blank" title="Online Quotation Form">Online Quotation Form</a></h2><p>';


			if($has_attachment == 1){
				$message .= '<br />&bull; This project has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$works_contractor->project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
				$has_attachment = 0;
			}

$file_company_name =  str_replace( array( "'",  '"', ',', '"'  , '`' ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $file_company_name);

			$message .= '<br />&bull; Download the CQR (Contractor Quote Request) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$works_contractor->project_id.'_cqr_'.$contractor_supplier->works_id.'_'.$file_company_name.'_'.$contractor_supplier->works_contrator_id.'.pdf" class="" id="" target="_blank" tile="Download Contractor Quote Request Form"><strong id="" class="">this link</strong></a> to view.</p>';

	



			$message .= '<p class=""><br />Regards,<br /><br />'.$esti_contact->user_full_name.'<br /><strong id="" class="">'.$esti_contact->general_email.'</strong><br />Estimator</p>
			<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';


			$email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$message.'</div>';

			$this->etc_m->set_remind_hr_before($works_contractor->cqr_reminder_id ,$today_date);   // trigger tagged



//	 echo '<p class="">*******************</p>'.$email_msg.'<p class="">*******************</p>'; 

	 $from = $esti_contact->general_email;

	 $to = $contractor_supplier->general_email;

	 $subject = 'Tender Reminder: '.$client_company->company_name.' - '.$works_contractor->project_id.' '.$works_contractor->project_name.' - '.$contractor_supplier->company_name;  //hr left reminder

	 // Contractor Quotation Request: Sunglass Hut - 45309 Chadstone VIC - Cleaning - CareKleen

	// echo '<p class="">'.$from.' --- '.$to.' --- '.$subject.'</p>'; 


	 //  [purpose]: [client_name] - [project_number] [project_name] - [contractor_name]
 
 



	 		$this->set_send_email($from,$to,$subject,$email_msg); //done



			}


	
		}




	}


	public function remind_day_left(){

		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );

		$has_attachment = 0;

		$day_today = date('Y-m-d');
		$today_date = date('d/m/Y');

	//	echo '<p class="">'.$day_today.' --- '.$project_id.' --- '.$today_date.'</p>'; 


		$works_contractor_q = $this->etc_m->get_remind_day_left($day_today,$today_date,$admin_defaults->days_before_due);

		foreach ($works_contractor_q->result() as $works_contractor){


			$attachment_q = $this->etc_m->check_attachments($works_contractor->project_id);
			if($attachment_q->num_rows > 0){
				$has_attachment = 1;
			}else{
				$has_attachment = 0;
			}

			$work_contractor_q = $this->etc_m->get_work_contractor_details($works_contractor->work_contractors_id);
			$contractor_supplier = array_shift($work_contractor_q->result() );

			if($contractor_supplier->contractor_type == 2){
				$work_job_name = $contractor_supplier->job_sub_cat;
			}else{
				$work_job_name = $contractor_supplier->supplier_cat_name;
			}

			$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($works_contractor->work_contractors_id);

			if($work_contractor_joinery->num_rows > 0){

				$work_joinery_id = array_shift($work_contractor_joinery->result() );
				$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
				$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

				if($joinery_work_q->num_rows > 0){
					$joinery_work_details = array_shift($joinery_work_q->result() );
					$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				}
			}

			if($work_job_name == 'Other'){
				$work_job_name = $contractor_supplier->other_work_desc;
			}

			$q_client_company = $this->etc_m->display_company_detail_by_id($contractor_supplier->client_id,$contractor_supplier->is_pending_client);
			$client_company = array_shift($q_client_company->result());

			if($contractor_supplier->project_estiamator_id > 0){
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_estiamator_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}else{
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_manager_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}



			$user_signature_q = $this->etc_m->get_user_email_signature($contractor_supplier->focus_company_id);
			$user_signature = array_shift($user_signature_q->result() );
 


			$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




			if($contractor_supplier->is_pending == 1){
				$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
				$pending_contractor = array_shift($q_pending_contractor->result());

				$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
				$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

				$contractor_supplier->company_name = $pending_contractor->company_name;
				$contractor_supplier_location = '';

				$contractor_supplier->office_number = $pending_contractor->contact_number;
				$contractor_supplier->general_email = $pending_contractor->email;

				$contractor_supplier->abn = '';
				$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
			}



			//echo '<p class="">-- '.$file_company_name.' --</p>'; 

			$message = '<p class="">Greetings,</p>

			<p class="">You are receiving this reminder that your tender submission for <strong style="background-color: #ffff00;">'.$work_job_name.'</strong> job for our client <strong style="background-color: #ffff00;">'.$client_company->company_name.'</strong> is about to end in '.$admin_defaults->days_before_due.' day(s)
			<br />If for any reason you do not believe you will be able to meet this deadline, then please contact <strong style="background-color: #ffff00;">'.$esti_contact->user_full_name.'</strong> at <strong class=""><u>'.$esti_contact->general_email.'</u></strong>.</p>
			<p class=""><br />Tenders received after the deadline may not be accepted.</p><p class="">Your tender <strong class="">MUST BE SUBMITTED ONLINE</strong> by clicking on this link.</p>
			<h2><a href="https://sojourn.focusshopfit.com.au/submit_quote/'.$works_contractor->work_contractors_id.'" style="padding: 8px 10px; text-decoration: none; border-radius: 5px; border: 1px solid #c8c6c4; color: #4f6bed; background-color: #EFEFEF;" target="_blank" title="Online Quotation Form">Online Quotation Form</a></h2><p>';


			if($has_attachment == 1){
				$message .= '<br />&bull; This project has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$works_contractor->project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
				$has_attachment = 0;
			}


$file_company_name =  str_replace( array( "'",  '"', ',', '"' , '`'  ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $file_company_name);

			$message .= '<br />&bull; Download the CQR (Contractor Quote Request) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$works_contractor->project_id.'_cqr_'.$contractor_supplier->works_id.'_'.$file_company_name.'_'.$contractor_supplier->works_contrator_id.'.pdf" class="" id="" target="_blank" tile="Download Contractor Quote Request Form"><strong id="" class="">this link</strong></a> to view.</p>';

			$message .= '<p class=""><br />Regards,<br /><br />'.$esti_contact->user_full_name.'<br /><strong id="" class="">'.$esti_contact->general_email.'</strong><br />Estimator</p>
			<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';


			$email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$message.'</div>';

			$this->etc_m->set_remind_day_before($works_contractor->cqr_reminder_id ,$today_date);   // trigger tagged



//	 echo '<p class="">*******************</p>'.$email_msg.'<p class="">*******************</p>'; 

	 $from = $esti_contact->general_email;

	 $to = $contractor_supplier->general_email;

//	 $subject = 'Focus Shopfit Tender Reminder - '.$client_company->company_name;

	// echo '<p class="">'.$from.' --- '.$to.' --- '.$subject.'</p>'; 
 
 
	 $subject = 'Tender Reminder: '.$client_company->company_name.' - '.$works_contractor->project_id.' '.$works_contractor->project_name.' - '.$contractor_supplier->company_name; // day left reminder
 //  [purpose]: [client_name] - [project_number] [project_name] - [contractor_name]





	 		$this->set_send_email($from,$to,$subject,$email_msg); //done


		}




	}



//*-


	public function remind_not_recv(){
		$has_attachment = 0;

 

		$day_today = date('Y-m-d');
		$today_date = date('d/m/Y');

	//	echo '<p class="">'.$day_today.' --- '.$project_id.' --- '.$today_date.'</p>'; 


		$works_contractor_q = $this->etc_m->get_remind_day_over($day_today,$today_date);

		foreach ($works_contractor_q->result() as $works_contractor){

 

			$attachment_q = $this->etc_m->check_attachments($works_contractor->project_id);
			if($attachment_q->num_rows > 0){
				$has_attachment = 1;
			}else{
				$has_attachment = 0;
			}

			$work_contractor_q = $this->etc_m->get_work_contractor_details($works_contractor->work_contractors_id);
			$contractor_supplier = array_shift($work_contractor_q->result() );

			if($contractor_supplier->contractor_type == 2){
				$work_job_name = $contractor_supplier->job_sub_cat;
			}else{
				$work_job_name = $contractor_supplier->supplier_cat_name;
			}

			$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($works_contractor->work_contractors_id);

			if($work_contractor_joinery->num_rows > 0){

				$work_joinery_id = array_shift($work_contractor_joinery->result() );
				$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
				$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

				if($joinery_work_q->num_rows > 0){
					$joinery_work_details = array_shift($joinery_work_q->result() );
					$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				}
			}

			if($work_job_name == 'Other'){
				$work_job_name = $contractor_supplier->other_work_desc;
			}

			$q_client_company = $this->etc_m->display_company_detail_by_id($contractor_supplier->client_id,$contractor_supplier->is_pending_client);
			$client_company = array_shift($q_client_company->result());

			if($contractor_supplier->project_estiamator_id > 0){
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_estiamator_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}else{
				$q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_manager_id);
				$esti_contact = array_shift($q_esti_contact->result());
			}



			$user_signature_q = $this->etc_m->get_user_email_signature($contractor_supplier->focus_company_id);
			$user_signature = array_shift($user_signature_q->result() );
 


			$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




			if($contractor_supplier->is_pending == 1){
				$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
				$pending_contractor = array_shift($q_pending_contractor->result());

				$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
				$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

				$contractor_supplier->company_name = $pending_contractor->company_name;
				$contractor_supplier_location = '';

				$contractor_supplier->office_number = $pending_contractor->contact_number;
				$contractor_supplier->general_email = $pending_contractor->email;

				$contractor_supplier->abn = '';
				$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
			}



		//	echo '<p class="">-- '.$works_contractor->work_contractors_id.' --</p>'; 

			$message = '<p class="">Greetings,</p>


<p>You are receiving this notification that your tender for <strong style="background-color: #ffff00;">'.$work_job_name.'</strong> job for our client <strong style="background-color: #ffff00;">'.$client_company->company_name.'</strong> has not been received by the tender deadline.
<br />
If there has been an issue that has caused this to occur and you have your tender for submission please contact <strong style="background-color: #ffff00;">'.$esti_contact->user_full_name.'</strong> at <strong class=""><u>'.$esti_contact->general_email.'</u></strong>. 
<br />
Please be aware however that tenders received after the tender deadline will be considered as non-conforming and may not be accepted.</p>';



			 
 
			$message .= '<p class=""><br />Regards,<br /><br />'.$esti_contact->user_full_name.'<br /><strong id="" class="">'.$esti_contact->general_email.'</strong><br />Estimator</p>
			<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';


			$email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$message.'</div>';




 	// echo '<p class="">*******************</p>'.$email_msg.'<p class="">*******************</p>'; 
	 		$this->etc_m->set_remind_over($works_contractor->cqr_reminder_id ,$today_date);   // trigger tagged

	 $from = $esti_contact->general_email;

	 $to = $contractor_supplier->general_email;

//	 $subject = 'Focus Shopfit Tender not Received - '.$client_company->company_name;

	// echo '<p class="">'.$from.' --- '.$to.' --- '.$subject.'</p>'; 
 
 


 
	 $subject = 'Tender not Received: '.$client_company->company_name.' - '.$works_contractor->project_id.' '.$works_contractor->project_name.' - '.$contractor_supplier->company_name;
 //  [purpose]: [client_name] - [project_number] [project_name] - [contractor_name]



	 		$this->set_send_email($from,$to,$subject,$email_msg); // done
 

		}




	}




	public function set_send_email($from,$to,$subject,$msg,$arr_cc=array(),$arr_bcc=array()){

		if ( !class_exists("PHPMailer") ){
			require('PHPMailer/class.phpmailer.php');
		}

		$mail = new PHPMailer;
		$mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';  	
		$mail->Port = 587;    
		$mail->ishtml(true);
		$mail->smtpdebug = 2;
		
		$mail->setFrom($from,'Focus Shopfit');
		$mail->addAddress(strtolower($to));
		$mail->addReplyTo($from,'Focus Shopfit');
		$mail->addBCC('jervyezaballa@gmail.com');

		if(!empty($arr_cc)){
			foreach ($arr_cc as $key => $cc_email) {
				$mail->addCC(strtolower($cc_email));
			}
		}

		if(!empty($arr_bcc)){
			foreach ($arr_bcc as $key => $bcc_email) {
				$mail->addBCC(strtolower($bcc_email));
			}
		}

		$mail->Subject = $subject; //'Focus Shopfit Tender - Flight Centre Ltd, Carillon City, Perth';
		$mail->Body = $msg;
		

		if(!$mail->send()) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		}
	}

	public function generate_SiteSheet($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='mss',$forced_file_name=''){


		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		$prj_details = array_shift($proj_q->result());
		$site_addr = '';



		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );



		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$has_attachment = 1;
		}else{
			$has_attachment = 0;
		}


		//	echo '<p id="" class="">-----</p>';
		// var_dump($prj_details);
		//	echo '<p id="" class="">-------</p>';

		if($prj_details->job_type == 'Shopping Center'){
			$site_addr .= $prj_details->shop_tenancy_number.': '.$prj_details->shop_name.'<br />';
		}


		$q_postal_company = $this->etc_m->fetch_complete_detail_address($prj_details->address_id);
		$prj_site_addr = array_shift($q_postal_company->result());


		if( isset($prj_site_addr->unit_level) && $prj_site_addr->unit_level != '' ){
			$site_addr .= 'Unit: '.$prj_site_addr->unit_level.'/';
		}

		$site_addr .= $prj_site_addr->unit_number.' '.$prj_site_addr->street.'<br />'. ucwords(strtolower($prj_site_addr->suburb)).', '.$prj_site_addr->shortname.', '.$prj_site_addr->postcode;


		

		$focus_add = '';



		$q_focus_details = $this->etc_m->fetch_single_company_focus($prj_details->focus_company_id);
		$focus_company = array_shift($q_focus_details->result());

		$postal_address_id =  $focus_company->postal_address_id;

		$q_postal_company = $this->etc_m->fetch_complete_detail_address($postal_address_id);
		$focus_postal_addr = array_shift($q_postal_company->result());


		// var_dump($focus_company);

		// echo '<p id="" class="">-----</p>';

		// var_dump($focus_postal_addr);

/*
		$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
		$estimator_contact = array_shift($q_est_contact->result());
*/

		if($prj_details->project_estiamator_id > 0){
			$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
			$estimator_contact = array_shift($q_est_contact->result());
		}else{

			$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_manager_id);
			$estimator_contact = array_shift($q_est_contact->result());
		}


		$q_client_details = $this->etc_m->display_company_detail_by_id($prj_details->client_id,$prj_details->is_pending_client);
		$client = array_shift($q_client_details->result());

		// 	$postal_address_id =  $client->postal_address_id;



		// echo '<p id="" class="">--- xxxxxxxxxx --</p>';

		// var_dump($estimator_contact);  
	 	//------------------------------------------------------



	$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_id);
	$contractor_supplier = array_shift($work_contractor_q->result() );





	if($contractor_supplier->contractor_type == 2){
		$work_job_name = $contractor_supplier->job_sub_cat;
	}else{
		$work_job_name = $contractor_supplier->supplier_cat_name;
	}

	if($work_job_name == 'Other'){
		$work_job_name = $contractor_supplier->other_work_desc;
	}



	//	echo '<p id="" class="">--- 4444444444 ----</p>';
	 //	var_dump($contractor_supplier);

	$q_company_address = $this->etc_m->fetch_complete_detail_address($contractor_supplier->address_id);
	$contractor_supplier_addr = array_shift($q_company_address->result());

	$contractor_supplier_location = '';



	//	echo '<p id="" class="">--- 4444444444 ----</p>';
	 //	var_dump($contractor_supplier_addr);



	if( isset($contractor_supplier_addr->unit_level) && $contractor_supplier_addr->unit_level != '' ){
		$contractor_supplier_location .= 'Unit '.$contractor_supplier_addr->unit_level.'/';
	}

	$contractor_supplier_location .= $contractor_supplier_addr->unit_number.' '.$contractor_supplier_addr->street.'<br />'. ucwords(strtolower($contractor_supplier_addr->suburb)).', '.$contractor_supplier_addr->shortname.', '.$contractor_supplier_addr->postcode;

/*
	 	if($prj_details->job_type == 'Shopping Center'){
	 		$site_add .= $prj_details->shop_tenancy_number.': '.$prj_details->shop_name.'<br />';
	 	}
 */



	 // company_m->fetch_complete_detail_address($address_detail_id)


	 $contractor_supplier->abn = '(ABN:'.$contractor_supplier->abn.')';


 
	$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




	if($contractor_supplier->is_pending == 1){
		$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
		$pending_contractor = array_shift($q_pending_contractor->result());

		$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
		$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

		$contractor_supplier->company_name = $pending_contractor->company_name;
		$contractor_supplier_location = '';
		$contractor_supplier_addr->phone_area_code = '';
		$contractor_supplier->office_number = $pending_contractor->contact_number;
		$contractor_supplier->mobile_number = '';
		$contractor_supplier->general_email = $pending_contractor->email;

		$contractor_supplier->abn = '';
		$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
	}




		$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($work_contractor_id);

		if($work_contractor_joinery->num_rows > 0){

			$work_joinery_id = array_shift($work_contractor_joinery->result() );
			$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
			$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

			if($joinery_work_q->num_rows > 0){
				$joinery_work_details = array_shift($joinery_work_q->result() );
				$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				$contractor_supplier->notes = $joinery_work_details->notes;
			}
		}


 

		$q_get_maintenance_contact = $this->etc_m->get_maintenance_contact($project_id);
		$maintenance_contact = array_shift($q_get_maintenance_contact->result());




	 	$this->load->module('reports');
	 	$html['contents'] = '<div style="padding: 0px 20px;">
	 	<table width="100%" class="mrg-bottom--10">
	 		<tr>
	 			<td rowspan="2" width="30%"><img class="mrg-left-10 mrg-top-25" src="https://sojourn.focusshopfit.com.au/img/focus_logo_cqr.jpg" width="250px" height="82.67px" /></td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					'.$focus_company->company_name.'<br />
	 					PO '.$focus_postal_addr->po_box.'<br />
	 					'.ucwords(strtolower($focus_postal_addr->suburb)).' '.$focus_postal_addr->shortname.' '.$focus_postal_addr->postcode.'
	 				</p>
	 			</td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					Tel: ('.$focus_company->area_code.') '.$focus_company->office_number.'<br />   
	 					ACN: '.$focus_company->acn.'<br /> 
	 					ABN: '.$focus_company->abn.' 
	 				</p>
	 			</td>
	 		</tr>
	 		<tr>
	 			<td colspan="2"><p id="" class="mrg-top-15 mrg-left-20">E-mail : '.$focus_company->general_email.'</p></td>
	 		</tr>
	 	</table>
	 	<div id="" class="content_area pad-5 block heigh-auto mrg-top--100">
	 		<table width="100%">
	 			<tr>
	 				<td>
	 					<p id="" class="">
	 						Client: '.$client->company_name.'<br />
	 						Subcontractor: '.$contractor_supplier->company_name.'
	 					</p>
	 				</td>
	 				<td>
	 					<p id="" class="text-right">
	 						<span style="font-weight: bold;    font-size: 18px;"> Maintenance Site Sheet</span><br />
	 						Project#: '.$project_id.'
	 					</p>
	 				</td>
	 			</tr>
	 		</table>
	 	</div>
	 	<div id="" class="divider"></div>

	 	<div id="" class="content_area pad-10 block heigh-auto">
		 	<p id="" class="content_title" style="width: 90px !important;">Site Location</p>
		 	<p class="">Contact Person: '.$maintenance_contact->contact_person_name.'</p>
		 	<p class="">Location: '.str_replace('<br />',' ', $site_addr).'</p>
		 	<p class="">
		 		Phone: '.$maintenance_contact->contact_person_number.' &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
		 		Mobile: '.$maintenance_contact->contact_person_mobile.' &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
		 		Email: '.$maintenance_contact->contact_person_email.'
		 	</p>
	 	</div>

	 	<div id="" class="content_area pad-10 block heigh-auto">
		 	<table width="100%">
			 	<tr>
			 		<td><strong class="">Prestart Evaluation</strong></td>
			 		<td><strong class=""> Y / N </strong></td>
			 		<td><strong class="">Comments</strong></td>
			 	</tr>

			 	<tr>
			 		<td><p class="">Have you discussed works with person on site</p></td>
			 		<td><img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /> &nbsp; <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></td>
			 		<td><span style="border: 1px solid #000;    padding: 2px;    height: 14px; display: inline-block; width:100%;">&nbsp;</span></td>
			 	</tr>

			 	<tr>
			 		<td><p class="">Have you checked work area to ensure all works can be achieved in a safe manner</p></td>
			 		<td><img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /> &nbsp; <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></td>
			 		<td><span style="border: 1px solid #000;    padding: 2px;     height: 14px; display: inline-block; width:100%;">&nbsp;</span></td>
			 	</tr>

			 	<tr>
			 		<td><p class="">Have you secured the area (if applicable) to prevent access by others</p></td>
			 		<td><img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /> &nbsp; <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></td>
			 		<td><span style="border: 1px solid #000;    padding: 2px;     height: 14px; display: inline-block; width:100%;">&nbsp;</span></td>
			 	</tr>

			 	<tr>
			 		<td><p class="">Have you reviewed the SWMS and added/amended if required</p></td>
			 		<td><img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /> &nbsp; <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></td>
			 		<td><span style="border: 1px solid #000;    padding: 2px;     height: 14px; display: inline-block; width:100%;">&nbsp;</span></td>
			 	</tr>

			 	<tr>
			 		<td><p class="">Do you have appropriate PPE (Personal Protection Equipment)</p></td>
			 		<td><img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /> &nbsp; <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></td>
			 		<td><span style="border: 1px solid #000;    padding: 2px;     height: 14px; display: inline-block; width:100%;">&nbsp;</span></td>
			 	</tr>

			 	<tr><td colspan="3"><p class="mrg-top-5"><strong class="">Please ensure everything on this list has been considered prior to the commencement of any works</strong></p></td></tr>



		 	</table>
	 	</div>


	 	<div id="" class="content_area pad-10 block heigh-auto">
	 		<p id="" class="content_title" style="width: 240px !important;">Description of works to be carried out</p>
	 		<p id="" class="">'.nl2br($contractor_supplier->notes).'</p>
	 	</div>

	 	<div id="" class="content_area pad-10 block" style="height: 7%; ">
	 		<p id="" class="content_title" style="width: 180px !important;">Comments & Material Used</p>
	 	</div>


	 	<div id="" class="content_area pad-10 block heigh-auto">
		 	<table width="100%">


			 	<tr>
			 		<td><p class=""><strong class="">Hours</strong> &nbsp; Start Time: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70px;">&nbsp;</span></p></td>
			 		<td><p class="">Finish Time: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70px;">&nbsp;</span></p></td>
			 		<td><p class="">Total Hours: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70px;">&nbsp;</span></p></td>
			 		<td><p class="">Travel Time: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70px;">&nbsp;</span></p></td>
			 	</tr>

			 	<tr>
			 		<td><p class=""><strong class="">Works Completed?</strong></p></td>
			 		<td><p class=""><strong class="">Yes</strong> <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></p></td>
			 		<td><p class=""><strong class="">No</strong> <img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" /></p></td>
			 		<td></td>
			 	</tr>

			 	<tr>
			 		<td colspan="4"><p class="mrg-top-10 mrg-bottom-5"><strong class="">Focus Shopfit Representative</strong></p></td>
			 	</tr>

			 	<tr>			 		
			 		<td><p class=""><strong class="">Date</strong>: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:60%;">&nbsp;</span></p></td>
			 		<td colspan="2"><p class=""><strong class="">Print Name</strong>: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70%;">&nbsp;</span></p></td>
			 		<td ><p class=""><strong class="">Signature:</strong></p></td>
			 	</tr>

			 	<tr>
			 		<td colspan="4"><p class="mrg-top-10 mrg-bottom-5"><strong class="">Acceptance of works completion by Client</strong></p></td>
			 	</tr>

			 	<tr>			 		
			 		<td><p class=""><strong class="">Date</strong>: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:60%;">&nbsp;</span></p></td>
			 		<td colspan="2"><p class=""><strong class="">Print Name</strong>: <span style="border: 1px solid #000;  margin-top:-3px;  padding: 2px;    height: 14px; display: inline-block; width:70%;">&nbsp;</span></p></td>
			 		<td ><p class=""><strong class="">Signature:</strong></p></td>
			 	</tr>



			 	 


		 	</table>
	 	</div>


	 

	 <p class="text-right">Issued by: '.$estimator_contact->user_full_name.'</p>
	 
	</div>';



		$file_name = $forced_file_name;//$project_id.'_maintenance_site_sheet_'.$work_contractor_id;
		$this->generate_cqr_pdf_etc($html['contents'],'portrait','A4',$file_name,$dir,$stream);
	

	//	$this->load->view('cqr_page',$html);



	}

	public function generate_CQR($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0',$forced_file_name=''){
		//

		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		$prj_details = array_shift($proj_q->result());
		$site_addr = '';
		$insurance_msg = '';


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );



		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$has_attachment = 1;
		}else{
			$has_attachment = 0;
		}


		//	echo '<p id="" class="">-----</p>';
		// var_dump($prj_details);
		//	echo '<p id="" class="">-------</p>';

		if($prj_details->job_type == 'Shopping Center'){
			$site_addr .= $prj_details->shop_tenancy_number.': '.$prj_details->shop_name.'<br />';
		}


		$q_postal_company = $this->etc_m->fetch_complete_detail_address($prj_details->address_id);
		$prj_site_addr = array_shift($q_postal_company->result());


		if( isset($prj_site_addr->unit_level) && $prj_site_addr->unit_level != '' ){
			$site_addr .= 'Unit: '.$prj_site_addr->unit_level.'/';
		}

		$site_addr .= $prj_site_addr->unit_number.' '.$prj_site_addr->street.'<br />'. ucwords(strtolower($prj_site_addr->suburb)).', '.$prj_site_addr->shortname.', '.$prj_site_addr->postcode;


		

		$focus_add = '';



		$q_focus_details = $this->etc_m->fetch_single_company_focus($prj_details->focus_company_id);
		$focus_company = array_shift($q_focus_details->result());

		$postal_address_id =  $focus_company->postal_address_id;

		$q_postal_company = $this->etc_m->fetch_complete_detail_address($postal_address_id);
		$focus_postal_addr = array_shift($q_postal_company->result());




		// var_dump($focus_company);

		// echo '<p id="" class="">-----</p>';

		// var_dump($focus_postal_addr);

/*
		$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
		$estimator_contact = array_shift($q_est_contact->result());
*/

		if($prj_details->project_estiamator_id > 0){
			$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
			$estimator_contact = array_shift($q_est_contact->result());
		}else{

			$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_manager_id);
			$estimator_contact = array_shift($q_est_contact->result());
		}




		$q_client_details = $this->etc_m->display_company_detail_by_id($prj_details->client_id,$prj_details->is_pending_client);
		$client = array_shift($q_client_details->result());

		// 	$postal_address_id =  $client->postal_address_id;



		// echo '<p id="" class="">--- xxxxxxxxxx --</p>';

		// var_dump($estimator_contact);  
	 	//------------------------------------------------------



	$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_id);
	$contractor_supplier = array_shift($work_contractor_q->result() );





	if($contractor_supplier->contractor_type == 2){
		$work_job_name = $contractor_supplier->job_sub_cat;
	}else{
		$work_job_name = $contractor_supplier->supplier_cat_name;
	}

	if($work_job_name == 'Other'){
		$work_job_name = $contractor_supplier->other_work_desc;
	}



	//	echo '<p id="" class="">--- 4444444444 ----</p>';
	 //	var_dump($contractor_supplier);

	$q_company_address = $this->etc_m->fetch_complete_detail_address($contractor_supplier->address_id);
	$contractor_supplier_addr = array_shift($q_company_address->result());

	$contractor_supplier_location = '';



	//	echo '<p id="" class="">--- 4444444444 ----</p>';
	 //	var_dump($contractor_supplier_addr);



	if( isset($contractor_supplier_addr->unit_level) && $contractor_supplier_addr->unit_level != '' ){
		$contractor_supplier_location .= 'Unit '.$contractor_supplier_addr->unit_level.'/';
	}

	$contractor_supplier_location .= $contractor_supplier_addr->unit_number.' '.$contractor_supplier_addr->street.'<br />'. ucwords(strtolower($contractor_supplier_addr->suburb)).', '.$contractor_supplier_addr->shortname.', '.$contractor_supplier_addr->postcode;

/*
	 	if($prj_details->job_type == 'Shopping Center'){
	 		$site_add .= $prj_details->shop_tenancy_number.': '.$prj_details->shop_name.'<br />';
	 	}
 */



	 // company_m->fetch_complete_detail_address($address_detail_id)


	 $contractor_supplier->abn = '(ABN:'.$contractor_supplier->abn.')';


 
	$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




	if($contractor_supplier->is_pending == 1){
		$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
		$pending_contractor = array_shift($q_pending_contractor->result());

		$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
		$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

		$contractor_supplier->company_name = $pending_contractor->company_name;
		$contractor_supplier_location = '';
		$contractor_supplier_addr->phone_area_code = '';
		$contractor_supplier->office_number = $pending_contractor->contact_number;
		$contractor_supplier->general_email = $pending_contractor->email;

		$contractor_supplier->abn = '';
		$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
	}




		$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($work_contractor_id);

		if($work_contractor_joinery->num_rows > 0){

			$work_joinery_id = array_shift($work_contractor_joinery->result() );
			$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
			$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

			if($joinery_work_q->num_rows > 0){
				$joinery_work_details = array_shift($joinery_work_q->result() );
				$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				$contractor_supplier->notes = $joinery_work_details->notes;
			}
		}

 

		if($this->has_inssurance($contractor_supplier->company_id)){
			$insurance_msg = $admin_defaults->cqr_notes_w_insurance;
		}else{
			$insurance_msg = $admin_defaults->cqr_notes_no_insurance;				
		}






	// 	$this->load->module('reports');
	 	$html['contents'] = '<div style="padding: 0px 20px;">
	 	<table width="100%" class="mrg-bottom--10">
	 		<tr>
	 			<td rowspan="2" width="30%"><img class="mrg-left-10 mrg-top-25" src="https://sojourn.focusshopfit.com.au/img/focus_logo_cqr.jpg" width="250px" height="82.67px" /></td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					'.$focus_company->company_name.'<br />
	 					PO '.$focus_postal_addr->po_box.'<br />
	 					'.ucwords(strtolower($focus_postal_addr->suburb)).' '.$focus_postal_addr->shortname.' '.$focus_postal_addr->postcode.'
	 				</p>
	 			</td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					Tel: ('.$focus_company->area_code.') '.$focus_company->office_number.'<br />   
	 					ACN: '.$focus_company->acn.'<br /> 
	 					ABN: '.$focus_company->abn.' 
	 				</p>
	 			</td>
	 		</tr>
	 		<tr>
	 			<td colspan="2"><p id="" class="mrg-top-15 mrg-left-20">E-mail : '.$focus_company->general_email.'</p></td>
	 		</tr>
	 	</table>
	 	<div id="" class="content_area pad-5 block heigh-auto mrg-top--100">
	 		<table width="100%">
	 			<tr>
	 				<td>
	 					<p id="" class="">
	 						Client: '.$client->company_name.'<br />
	 						Project: '.$prj_details->project_name.'
	 					</p>
	 				</td>
	 				<td>
	 					<p id="" class="text-right">
	 						<span style="font-weight: bold;    font-size: 18px;"> Contractor Quote Request</span><br />
	 						Project#: '.$project_id.'
	 					</p>
	 				</td>
	 			</tr>
	 		</table>
	 	</div>
	 	<div id="" class="divider"></div>

	 	<p id="" class="pad-top-5"><strong id="" class="">Reply Deadline</strong>: '.$contractor_supplier->work_reply_date.' '.$contractor_supplier->work_replyby_time.' '.$contractor_supplier->comments.'</p>

	 	<table width="100%">
	 		<tr>
	 			<td width="375px">
	 				<div id="" class="content_area pad-10 block heigh-auto  ">
	 					<p id="" class="content_title" style="width: 76px !important;">Contractor</p>
	 					<p id="" class="">
	 						'.$contractor_supplier->company_name.'<br />
	 						'.$contractor_supplier_location.'<br />
	 						Tel: '.$contractor_supplier_addr->phone_area_code.' '.$contractor_supplier->office_number.'<br />
	 						E-mail: '.$contractor_supplier->general_email.'
	 					</p>
	 				</div>
	 				<div id="" class="content_area pad-10 block heigh-auto  ">
	 					<p id="" class="">                       
	 						<strong id="" class="">Attention: '.$contractor_supplier->first_name.' '.$contractor_supplier->last_name.'</strong><br />
	 						<strong id="" class="">Description:</strong> '.$work_job_name.'<br />
	 						<strong id="" class="">Expected Start:</strong> '.$prj_details->date_site_commencement.' &nbsp; &nbsp;  &nbsp;  &nbsp; &nbsp; <strong id="" class="">End:</strong> '.$prj_details->date_site_finish.'

	 					</p>
	 					<hr class="mrg-top-5 mrg-bottom-5" />
	 					<p>
	 						<strong id="" class="">Estimator:</strong> '.$estimator_contact->user_full_name.'<br />  
	 						<strong id="" class="">E-mail:</strong> '.$estimator_contact->general_email.'                        
	 					</p>
	 				</div>
	 				<div id="" class="content_area pad-10 block heigh-auto  ">
	 					<p id="" class="content_title" style="width: 88px !important;">Site Address</p>
	 					<p id="" class="">
	 						'.$site_addr.'
	 					</p>
	 				</div>
	 				<div id="" class="content_area pad-10 block heigh-auto clearfix" style="height: 135px;">
	 					<p id="" class="content_title">Other Considerations</p>
	 					<div id="" class="pad-bottom-15">
	 						<div id="" class="clearfix block" style="height: 90px;">
	 							<div id="" class="pull-left width-50">
	 								<p id="" class="">
	 									'.($contractor_supplier->site_inspection_req == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Site Inspection Required<br />
	 									'.($contractor_supplier->special_conditions == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Special Condition<br />
	 									'.($contractor_supplier->additional_visit_req == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Additional Visit Required<br />
	 									'.($contractor_supplier->operate_during_install == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Operate During Install<br />
	 									'.($contractor_supplier->other == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Others
	 								</p>
	 							</div>
	 							<div id="" class="pull-left width-50">
	 								<p id="" class="mrg-left-20">
	 									'.($contractor_supplier->week_work == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Week Work<br />
	 									'.($contractor_supplier->weekend_work == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Weekend Work<br />
	 									'.($contractor_supplier->after_hours_work == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; After Hours Work<br />
	 									'.($contractor_supplier->new_premises == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; New Premises<br />
	 									'.($contractor_supplier->free_access == '0' ? '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_ballot.jpg" width="10px" height="10px" />' : '<img class="mrg-top-5" src="https://sojourn.focusshopfit.com.au/img/box_check.jpg" width="10px" height="10px" />').' &nbsp; Free Access
	 								</p>
	 							</div>
	 						</div>
	 						<div id="" class="block clearfix">
	 							'.($contractor_supplier->otherdesc == '' ? '' : '<p id="" class="" style="padding: 7px 0 10px;"><strong id="" class="">'.$contractor_supplier->otherdesc.'</strong></p>').'
	 						</div>
	 					</div>
	 				</div>



	 				<div id="" class="content_area pad-10 block heigh-auto  ">
	 					<p id="" class="content_title" style="width: 86px !important;">Attachments</p>


	 					'.($has_attachment == 1 ? '<p id="" class="">Please click link below to download attachments:</p>' : '<p id="" class=""><strong id="" class="">No Attachments</strong></p>').'

	 					<p id="" class="">'.($has_attachment == '1' ? '<a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$project_id.'" target="_blank" title="View Attachments">
	 						https://sojourn.focusshopfit.com.au/<br />
	 						project_attachments/proj_attachment?project_id='.$project_id.'
	 					</a>' : '').'

	 				</p>
	 			</div>


	 		</td>';










$total_chars = strlen($contractor_supplier->notes);

$loop_count =  ceil( $total_chars/1490 );

//$new_str = '';


      



		$arr_details_lines = preg_split('/\r\n|\r|\n/',$contractor_supplier->notes);

//1490
 

		if( $loop_count > 1 ){
			$html['contents'] .= '<td width="375px"><div id="" class="content_area pad-10 block mrg-left-5" style="height: 70%; ">';
		}else{
			$html['contents'] .= '<td width="375px"><div id="" class="content_area pad-10 block mrg-left-5" style="height: 50%; ">';
		}

 

	 				$html['contents'] .= '<p id="" class="content_title" style="width: 110px !important;">Scope of Works</p><div style="width: 340px !important; word-wrap: break-word;" >';


//$html['contents'] .= $contractor_supplier->notes;

/*
				 		 foreach ($arr_details_lines as $key => $value) {
				 		 	$html['contents'] .= $value.' <br />';

				 		 	if( $key % 30 == 0 && $key > 0){
				 		  		$html['contents'] .= '<p style="page-break-after: always;"></p><p class=""><br /></p><p><br /></p>';
				 		 	}	

				 		 }
*/

				 		 if( $loop_count > 1 ){

				 		 	for ($i=0; $i < $loop_count; $i++) { 

	//  $new_str .= substr_replace( $sentence, '<br />************<br />', (1490* ($i+1) ) , 0 );  2200

				 		 		$html['contents'] .=  nl2br( substr($contractor_supplier->notes,2036*$i,2036));
				 		 		$html['contents'] .=  ($i+1 == $loop_count ? '' : '<p style="page-break-after: always;"></p><p class=""><br /></p><p><br /></p>'); 

	// echo '<p class="">'.($i+1).'</p>'; 
				 		 	}



				 		 }else{

				 		 	$html['contents'] .=  nl2br($contractor_supplier->notes);
				 		 }



 /*
	 		if( count($arr_details_lines) > 30 ){
	 			$html['contents'] .= '<tr><td colspan="2"><p class="">&nbsp;<br />&nbsp;</p></td></tr>';
	 		}

*/
 


	 			$html['contents'] .= '</div></div>
	 		</td>
	 	</tr>

	 	<tr>
	 		<td colspan="2"><div id="" class="divider   "></div></td>
	 	</tr>
	 	<tr>
	 		<td colspan="2">
	 			<div id="" class="content_area pad-10 block heigh-auto">
	 				<p id="" class="content_title" style="width: 186px !important;">To be completed by tenderer</p>
	 				<p id="" class=""><strong id="" class="">We '.$contractor_supplier->company_name.' '.$contractor_supplier->abn.'</strong></p>';
	 		/*		
contractor_reply_conditions
inc_gst
*/
	 			

	 			if($contractor_reply == 1){
	 				$html['contents'] .='<p id="" class="">submit our quotation of <strong id="" class="">$<span style="text-decoration: underline;">'.number_format($contractor_supplier->inc_gst,2).'</span></strong> being for the abovementioned works including GST.</p>';
	 			}else{
	 				$html['contents'] .='<p id="" class="">submit our quotation of <strong id="" class="">$___________</strong> being for the abovementioned works including GST.</p>';
	 			}


	 		//	if( strlen($contractor_supplier->contractor_reply_conditions) > 0){

	 			if($contractor_reply == 1){
	 				$html['contents'] .='<p id="" class=""><br /><strong id="" class="">Comments</strong>: '.$contractor_supplier->contractor_reply_conditions.'</p>
	 				<p id="" class="mrg-top-20"><strong id="" class="">Signed by: <span style="text-decoration: underline;">'.$contractor_supplier->first_name.' '.$contractor_supplier->last_name.'</span></strong> for and on behalf of the tenderer</p>';
	 			} else{
	 				$html['contents'] .='<p id="" class=""><br /><strong id="" class="">Comments</strong>: ___________________________________________________________________________________</p>
	 				<p id="" class="">______________________________________________________________________________________________</p>
	 				<p id="" class="">______________________________________________________________________________________________</p>
	 				<p id="" class="">______________________________________________________________________________________________</p>
	 				<p id="" class="mrg-top-20"><strong id="" class="">Signed by</strong>: ___________________________ for and on behalf of the tenderer</p>';
	 			}

	 			$html['contents'] .= '</div>
	 		</td>
	 	</tr>


	 	<tr>
	 		<td colspan="2">
	 			<div id="" class="content_area pad-10 block heigh-auto">
	 				<p id="" class="text-center"><strong id="" class="">'.$insurance_msg.'</strong></p>
	 			</div>
	 		</td>
	 	</tr>

	 </table>
	</div>';

		$cqr_reply = '';

		if($contractor_reply == 1){
			$cqr_reply = 'cqr_reply';
		}else{
			$cqr_reply = 'cqr';
		}


		$file_name = $forced_file_name; //$project_id.'_'.$cqr_reply.'_'.$work_id.'_'.$file_company_name.'_'.$work_contractor_id;
		$this->generate_cqr_pdf_etc($html['contents'],'portrait','A4',$file_name,$dir,$stream);
		$date_today = date('d/m/Y');
		$this->etc_m->set_cqr_data($work_contractor_id);

		// $this->load->view('cqr_page',$html);


	}
 
	public function generate_cqr_pdf_etc($content, $orientation='portrait', $paper='A4',$filename='cqr',$folder_type='cqr' ,$stream=TRUE){



$filename =  str_replace( array( "'",  '"', ',', '"' ,'%' , '`'  ,  '&apos', ' ' ,  '&', ';', '<', '>' ), '', $filename);



		$this->load->helper('file');
		$this->load->helper('download');

		require_once('dompdf/dompdf_config.inc.php');
		spl_autoload_register('DOMPDF_autoload');


// gen cqr
//	function generate_cqr_pdf($content, $orientation='portrait', $paper='A4',$filename='cqr',$folder_type='cqr' ,$stream=TRUE){
		$dompdf = new DOMPDF();
		$document = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title></title>';
	//	$document .= '<link type="text/css" href="'.base_url().'css/pdf.css" rel="stylesheet" />';
	
		$document .= '<style type="text/css">
    *,
    body,
    html {
        margin: 0;
        padding: 0;
        vertical-align: top;
    }
    html {
        width: 99.9%;
    }
    * {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }
    .clearfix:after {
        content: " ";
        display: block;
        height: 0;
        clear: both;
        visibility: hidden;
    }

    .mrg-20 {
    	margin: 20px !important;
    }

    .pad-20 {
    	padding: 20px;
    }

    .content_area {
        border: 1px solid #000;
        margin: 8px 0;
    }
    .pad-5 {
        padding: 5px;
    }
    .pad-10 {
        padding: 10px;
    }
    .pad-bottom-15 {
        padding-bottom: 15px;
    }
    .pad-bottom-5 {
        padding-bottom: 5px;
    }
    .pad-top-5 {
        padding-top: 5px;
    }
    .pad-top-0{
    	padding-top: 0 !important;
    }
    .pull-left {
        float: left;
    }
    .width-100 {
        width: 100%;
    }
    .width-50 {
        width: 50%;
    }
    .block {
        display: block;
    }
    .heigh-auto {
        height: auto;
    }
    .content_title {
        margin-top: -15px;
    	background-color: #fff;
    	width: 40%;
    	padding-left:10px;
    	margin-bottom: 5px;
    }
    .mrg-bottom-5 {
        margin-bottom: 5px;
    }
    .mrg-bottom-10 {
        margin-bottom: 10px;
    }

    .mrg-bottom--10 {
        margin-bottom:-10px;
    }

    .mrg-right-10 {
        margin-right: 10px;
    }

    .mrg-right-15 {
        margin-right: 15px;
    }
    .mrg-right-20 {
        margin-right: 20px;
    }
    .mrg-right-40 {
        margin-right:40px;
    }
    .mrg-left-10 {
        margin-left: 10px;
    }
    .mrg-left-20 {
        margin-left: 20;
    }
    .mrg-left-5 {
        margin-left: 5px;
    }
    .mrg-top-5{
    	margin-top:5px;
    }
    .mrg-top-10 {
        margin-top: 10px;
    }
    .mrg-top-15 {
        margin-top: 15px;
    }
    .mrg-top-20 {
        margin-top:20px;
    }
    .mrg-top-25 {
        margin-top:25px;
    }
    .mrg-top--100 {
        margin-top:-100px;
    }

    .mrg-top--20 {
        margin-top:-20px;
    }
 

    .divider {
        display: block;
        width: 100%;
        border: 2px solid #656464;
        height: 2px;
        background: #8b8989;
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
</style>';
		$document .= '</head><body>';
		$document .= $content;
		$document .= '</body></html>';

		$html = mb_convert_encoding($document, 'HTML-ENTITIES', 'UTF-8');

		$dompdf->set_paper($paper,$orientation);
		$dompdf->load_html($html);
		$dompdf->render();
		$canvas = $dompdf->get_canvas();
		$w = $canvas->get_width();
		$h = $canvas->get_height();

		$date_gen = date("d/m/Y H:i A");

		$font = Font_Metrics::get_font("helvetica", "normal");
		$canvas->page_text(500,810, "$date_gen", $font, 8);

		$output = $dompdf->output();
 
		if(!is_dir('docs/'.$folder_type)){
			mkdir('docs/'.$folder_type,0777,TRUE);
		}
		//create the folder if it's not already exists

		write_file('docs/'.$folder_type.'/'.$filename,'');
		file_put_contents('docs/'.$folder_type.'/'.$filename, $output);
		//	return $filename;

		//unlink('docs/'.$folder_type.'/'.$filename.'.pdf');
		if($stream){
	 		$dompdf->stream($filename);
		}
//	}
// gen bb


	}
//////////////  for pdf contractor



	public function process_cpo(){

		$project_id = $this->input->post('project_id', true);
		$sel_contractor = $this->input->post('sel_cotnractor', true);
		$set_attach_mss = $this->input->post('set_attach_mss', true);
 

	//	$user_id = $this->session->userdata('user_id');
	//	$user_focus_company_id = $this->session->userdata('user_focus_company_id');

	//	$user_signature_q = $this->etc_m->get_user_email_signature($user_focus_company_id);
	//	$user_signature = array_shift($user_signature_q->result() );

		$cpo_email = '';
		$pa_contact = '';


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );

		$insurance_msg = '';


		$date_today = date('d/m/Y');

  
		$attachment_q = $this->etc_m->check_attachments($project_id);
		if($attachment_q->num_rows > 0){
			$has_attachment = 1;
		}else{
			$has_attachment = 0;
		}


	


		$set_cc_emails = $this->input->post('set_cc_emails', true);
		$set_bcc_emails = $this->input->post('set_bcc_emails', true);
		$extra_msg = $this->input->post('extra_msg', true);

	//	$cc_email_arr = explode(',',$set_cc_emails);


		$set_email_arr = explode(',',$set_cc_emails);
		$cc_cqr_emails = $this->push_cc_emails('cpo');

		if(count($cc_cqr_emails) > 0){
			$cc_email_arr = array_merge($set_email_arr,$cc_cqr_emails);
		}else{
			$cc_email_arr = $set_email_arr;
		}


		$cc_ind_emails = $this->push_cc_emails('ind');


 






		$bcc_email_arr = explode(',',$set_bcc_emails);

	//	$q_sender_contact = $this->etc_m->fetch_user($user_id);
	//	$sender_contact = array_shift($q_sender_contact->result());


		foreach ($sel_contractor as $key => $work_contractor_id){




			$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_id);
			$row = array_shift($work_contractor_q->result() );


			$company_contractor_supplier_id = $row->company_id;



			$file_company_name = strtolower( str_replace(' ', '_', $row->company_name )  );

 /*
			if($row->project_estiamator_id > 0){
				$q_sender_contact = $this->etc_m->fetch_user($row->project_estiamator_id);
				$sender_contact = array_shift($q_sender_contact->result());
			}else{
*/
				$q_sender_contact = $this->etc_m->fetch_user($row->project_manager_id);
				$sender_contact = array_shift($q_sender_contact->result());


	//		}

			



		$q_client_details = $this->etc_m->display_company_detail_by_id($row->client_id,$row->is_pending_client);
		$client = array_shift($q_client_details->result());
				





			$user_focus_company_id = $row->focus_company_id;
			$user_signature_q = $this->etc_m->get_user_email_signature($row->focus_company_id);
			$user_signature = array_shift($user_signature_q->result() );





	//		echo '<p id="" class="">'.$project_id.' --- '.$row->works_id.' --- '.$company_contractor_supplier_id.' --- '.$row->is_pending.'--- '.$work_contractor_id.'</p>'; 


if($row->job_category == 'Maintenance'){
			if($set_attach_mss == 1){

				$time = time();
				$mss_file = $project_id.'_maintenance_site_sheet_'.$work_contractor_id.'_'.$time.'.pdf';
				$this->generate_SiteSheet($project_id,$row->works_id,$work_contractor_id,FALSE,'mss',$mss_file);

				//generate_SiteSheet($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='mss',$forced_file_name='')
			}
		}

		//	$subject = $project_id.' '.$row->project_name.' CPO - '.$row->company_name;

			$from = $sender_contact->general_email;

			$to = $row->general_email;

			if($row->is_pending == 1){
				$to = $pending_contractor->email;
			}

			$cpo_email = '<p class="">Greetings,<br /></p>';


			$cpo_email .= '<p>Please see below the link for the Purchase Order Form as well as any other related documents, we would appreciate if you confirmed receipt. Please ensure you review the details of the linked documents for your reference and information.  Acceptance of this Contract Purchase Order (CPO) requires you to accept and comply with all of the site requirements associated with undertaking works for Focus Shopfit, including but not limited to:  provision of site specific SWMS prior to commencement, maintaining correct and appropriate insurances, provision and use of appropriate PPE, completion of the Focus company induction and Site Specific Induction prior to site attendance, signing in and out of site, supervision of your staff and/or contractors.<br /></p>';

			$cpo_email .= '<p>If you have any questions or there is any other information required, please contact me through '.$from.'. <br />Please esure that any documentation and paperwork is completed well before you are required on site to ensure there are no hold ups, thanks again in advance for your co-operation.</p><p>';

			if($has_attachment == 1){
				$cpo_email .= '<br />&bull; This job has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
			}


if($row->job_category == 'Maintenance'){
			if($set_attach_mss == 1){
				$cpo_email .= '<br />&bull; Download the MSS (Maintenance Site Sheet) form, click <a href="https://sojourn.focusshopfit.com.au/docs/mss/'.$mss_file.'" class="" id="" target="_blank" tile="Download Maintenance Site Sheet Form"><strong id="" class="">this link</strong></a> to view.';
			}
 
}
			$time = time();


$file_company_name =  str_replace( array( "'",  '"', ',', '"' , '`' ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $file_company_name);

			$cpo_file_name = $project_id.'_cpo_'.$row->works_id.'_'.$file_company_name.'_'.$time.'.pdf';

			$this->generate_CPO($project_id,$row->works_id,$work_contractor_id,FALSE,'stored_docs',0,$cpo_file_name);


//generate_CPO($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0',$forced_file_name='')



			$cpo_email .= '<br />&bull; Download the CPO (Contractor Purchase Order) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$cpo_file_name.'" class="" id="" target="_blank" tile="Download Contractor Purchase Order Form"><strong id="" class="">this link</strong></a> to view.</p>';



			if(!empty($extra_msg)){
				$cpo_email .= '<p>';
				$cpo_email .= '<pre style="font-family: verdana,sans-serif; font-size:12px; "><br /><strong id="" class="">More Details:</strong><br />'.$extra_msg.'</pre></p>';
			}


			$cpo_email .= '<p style="color: red;"><br /><strong>Covid 19</strong> - please note that it is the responsibility of the contractor to ensure that all personnel attending <strong>ANY</strong> worksites for and on behalf of <strong>Focus Shopfit</strong> comply with all government direction, mandates and requirements for working on site.  This includes any contractors you may engage and the supply of any certification of vaccinations etc <strong>PRIOR</strong> to attending any site.  Should you have any questions relating to this, please ensure you contact a Focus Shopfit staff member before you commence any works.</p>';

			$cpo_email .= '<p><br /><br />Regards,<br /><br />'.$sender_contact->user_first_name.' '.$sender_contact->user_last_name.'<br /><strong id="" class="">'.$sender_contact->general_email.'</strong><br />'.$sender_contact->role_types.'</p>';
			$cpo_email .= '<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';



			$user_id = $this->session->userdata('user_id');

$cpo_file_name =  str_replace( array( "'",  '"', ',', '"' , '`'  ,'%' ,  '&apos', ' ' , ';', '&', '&apos', '<', '>' ), '', $cpo_file_name);

			$this->etc_m->insert_doc_cqr($cpo_file_name,$project_id,$date_today,9,$user_id);

			array_push($cc_email_arr, $sender_contact->general_email);

 

			if($row->job_category != 'Maintenance'){
				
				$q_pa_contact = $this->etc_m->fetch_user($row->project_admin_id);
				$pa_contact = array_shift($q_pa_contact->result());
				array_push($cc_email_arr, $pa_contact->general_email);

			}




			$cc_email_arr = array_filter($cc_email_arr);
			$bcc_email_arr = array_filter($bcc_email_arr);

			$this->etc_m->set_cpo_data($work_contractor_id);
			$this->etc_m->set_cpo_sent($work_contractor_id,$date_today);
 

//echo '-------------';

			 
		// 	$to = 'jervy@focusshopfit.com.au'; /////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! remove_me

		//	echo "$from ---------   $to ---------   $subject ---------   ---------    ";
//echo '<p class="">--------------</p>';
		//	var_dump($cc_email_arr);
//echo '<p class="">--------------</p>';
		//	var_dump($bcc_email_arr);
//

			$email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$cpo_email.'</div>';



//echo '<p class="">--------------</p>';

	//		$subject = $project_id.' '.$row->project_name.' CPO - '.$row->company_name;

	 $subject = 'Contractor Purchase Order: '.$client->company_name.' - '.$project_id.' '.$row->project_name.' - '.$row->company_name;
	 $ind_vid_sub = $project_id.' '.$row->project_name.' - '.$row->company_name;
 //  [purpose]: [client_name] - [project_number] [project_name] - [contractor_name]

 
		 	$this->set_send_email($from,$to,$subject,$email_msg,$cc_email_arr,$bcc_email_arr); // done
 




//Induction Video Link Email ==========================


		 	$footer_email = '';
		 	$footer_email = '<p><br /><br />Regards,<br /><br />'.$sender_contact->user_first_name.' '.$sender_contact->user_last_name.'<br /><strong id="" class="">'.$sender_contact->general_email.'</strong><br />'.$sender_contact->role_types.'</p>';
			$footer_email .= '<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';



$cont_email = $to;
		 	$user_email = $sender_contact->general_email;

		 	$user_name = $sender_contact->user_first_name.' '.$sender_contact->user_last_name;

$project_date = $row->project_date;
$proj_id = $project_id;
$work_price = $row->price;
$focus_company_id = $row->focus_company_id;
$prj_name = $row->project_name;

$comp_det_q = $this->etc_m->display_company_detail_by_id($row->company_id);
foreach ($comp_det_q->result_array() as $row){
	$work_company_name = $row['company_name'];
	$company_type_id = $row['company_type_id'];
}

$admin_defaults = $this->etc_m->fetch_admin_defaults();
foreach ($admin_defaults->result() as $row){
	$induction_commencement_date = $row->induction_commencement_date;
	$induction_work_value = $row->induction_work_value;
}
			
			$date = str_replace('/', '-', $project_date);
			$project_date = date('Y-m-d', strtotime($date)); 
			$filtered_date = $induction_commencement_date;
			
			$video_generated = $this->etc_m->fetch_induction_videos_generated($proj_id);
			$induction_exempted = $this->induction_project_exempted($proj_id);
			if($project_date > $filtered_date):
				if($company_type_id == 2):
					if($induction_exempted == 0):
						$work_allowed = 0;
						if($work_price >= $induction_work_value){
							$work_allowed = 1;
						}else{
							if($work_price > 0 && $work_price < 2){
								$work_allowed = 1;
							}
						}
						if($work_allowed == 1){
							if($video_generated == 1):
								if($focus_company_id == 6){
									if($proj_id == 41078){
										require_once('PHPMailer/class.phpmailer.php');
										require_once('PHPMailer/PHPMailerAutoload.php');

										$q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message("induction-video");
										foreach ($q_admin_default_email_message->result_array() as $row){
											$induction_vl_sender_name = $row['sender_name'];
											$induction_vl_sender_email = $row['sender_email'];
											$induction_vl_bcc_email = $row['bcc_email'];
											$induction_vl_subject = $row['subject'];
											$induction_vl_message_content = $row['message_content'];
											$induction_vl_assigned_user = $row['user_id'];
										}
										
										$mail = new phpmailer(true);
										$mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
										$mail->port = 587;
										$mail->setFrom($user_email, $user_name);

										$mail->addReplyTo($user_email);

										$mail->addAddress($cont_email); //!!!!!!!!!!!!!!!!!!!!!!!!! 
									//	$mail->addAddress("jervy@focusshopfit.com.au");
										$mail->addBCC("mark.obis2012@gmail.com");
										$mail->addBCC("jervyezaballa@gmail.com");


										if(count($cc_ind_emails) > 0){
											foreach ($cc_ind_emails as $key => $value) {
												$mail->addCC($value);
											}
										}


									//	$mail->addBCC("ian@focusshopfit.com.au");
										$mail->addBCC($user_email);
									//	$mail->addBCC($sender_user_email);

										$mail->smtpdebug = 2;
										$mail->ishtml(true);

										$data['message'] = "<div style=\"font-family: verdana,sans-serif; font-size:12px;\"><p>Greetings,<br /></p><p>".$induction_vl_message_content."</p><p> Please See this Link: https://sojourn.focusshopfit.com.au/direct_contractor_upload/contractor_induction_video?project_id=".$proj_id.". </p><br />".$footer_email."</div>";
										$data['sender'] = $induction_vl_sender_name;
										$data['send_email'] = $induction_vl_sender_email;

										// $message = $this->load->view('message_view',$data,TRUE);

										$mail->Subject = "Induction Video Link: ".$ind_vid_sub;
										$mail->Body    = $data['message'];


									//	echo '<p class="">111111111111111</p>'; 

										if(!$mail->send()) {
											return 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
										} else {

										}


									}
								}else{
								// if($proj_id == 40634 || $proj_id == 40650 || $proj_id == 40901 || $proj_id == 41078){
									require_once('PHPMailer/class.phpmailer.php');
									require_once('PHPMailer/PHPMailerAutoload.php');

									$q_admin_default_email_message = $this->etc_m->fetch_admin_default_email_message("induction-video");
									foreach ($q_admin_default_email_message->result_array() as $row){
										$induction_vl_sender_name = $row['sender_name'];
										$induction_vl_sender_email = $row['sender_email'];
										$induction_vl_bcc_email = $row['bcc_email'];
										$induction_vl_subject = $row['subject'];
										$induction_vl_message_content = $row['message_content'];
										$induction_vl_assigned_user = $row['user_id'];
									}
									
									$mail = new phpmailer(true);
									$mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
									$mail->port = 587;
									$mail->setFrom($user_email, $user_name);

									$mail->addReplyTo($user_email);


									$mail->addAddress($cont_email); //!!!!!!!!!!!!!!!!!!!!!!!!! 
								//	$mail->addAddress("jervy@focusshopfit.com.au");


									$mail->addBCC("mark.obis2012@gmail.com");
								//	$mail->addBCC("ian@focusshopfit.com.au");
									$mail->addBCC("jervyezaballa@gmail.com");
									$mail->addBCC($user_email);
								//	$mail->addBCC($sender_user_email);


									if(count($cc_ind_emails) > 0){
										foreach ($cc_ind_emails as $key => $value) {
											$mail->addCC($value);
										}
									}

									$mail->smtpdebug = 2;
									$mail->ishtml(true);

									$data['message'] = "<div style=\"font-family: verdana,sans-serif; font-size:12px;\"><p>Greetings,<br /></p><p>".$induction_vl_message_content."</p><p> Please See this Link: https://sojourn.focusshopfit.com.au/direct_contractor_upload/contractor_induction_video?project_id=".$proj_id.". </p><br />".$footer_email."</div>";
									$data['sender'] = $induction_vl_sender_name;
									$data['send_email'] = $induction_vl_sender_email;

									// $message = $this->load->view('message_view',$data,TRUE);

									$mail->Subject = "Induction Video Link: ".$ind_vid_sub;
									$mail->Body    = $data['message'];


								//	echo '<p class="">222222222222222</p>'; 

									if(!$mail->send()) {
										return 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
									} else {

									}


								}
							endif;
						}
					endif;
				endif;
			endif;
//Induction Video Link Email ==========================








  // echo  $email_msg;



		}

  redirect(base_url().'projects/view/'.$project_id.'?tab=works');  ////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! process_cpo
	}




	public function generate_CPO($project_id='',$work_id='',$work_contractor_id='',$stream=FALSE, $dir='stored_docs',$contractor_reply='0',$forced_file_name=''){

		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		$prj_details = array_shift($proj_q->result());
		$site_addr = '';
		$insurance_msg = '';

		$date_today = date('d/m/Y');


		$fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
		$admin_defaults = array_shift($fetch_admin_defaults_q->result() );

		$q_company_contact_person = $this->etc_m->get_person_contacts($prj_details->contact_person_id);
		$company_contact = array_shift($q_company_contact_person->result());


		$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
		$estimator_contact = array_shift($q_est_contact->result());


		if($prj_details->job_type == 'Shopping Center'){
			$site_addr .= $prj_details->shop_tenancy_number.': '.$prj_details->shop_name.'<br />';
		}

	

		$q_postal_company = $this->etc_m->fetch_complete_detail_address($prj_details->address_id);
		$prj_site_addr = array_shift($q_postal_company->result());


		if( isset($prj_site_addr->unit_level) && $prj_site_addr->unit_level != '' ){
			$site_addr .= 'Unit: '.$prj_site_addr->unit_level.'/';
		}

		$site_addr .= $prj_site_addr->unit_number.' '.$prj_site_addr->street.'<br />'. ucwords(strtolower($prj_site_addr->suburb)).', '.$prj_site_addr->shortname.', '.$prj_site_addr->postcode;

		$focus_add = '';

		$q_focus_details = $this->etc_m->fetch_single_company_focus($prj_details->focus_company_id);
		$focus_company = array_shift($q_focus_details->result());

		$postal_address_id =  $focus_company->postal_address_id;

		$q_postal_company = $this->etc_m->fetch_complete_detail_address($postal_address_id);
		$focus_postal_addr = array_shift($q_postal_company->result());

		$q_est_contact = $this->etc_m->fetch_email_user($prj_details->project_estiamator_id);
		$estimator_contact = array_shift($q_est_contact->result());;

		$q_pm_contact = $this->etc_m->fetch_email_user($prj_details->project_manager_id);
		$pm_contact = array_shift($q_pm_contact->result());



		$q_client_details = $this->etc_m->display_company_detail_by_id($prj_details->client_id,$prj_details->is_pending_client);
		$client = array_shift($q_client_details->result());

 


		$work_contractor_q = $this->etc_m->get_work_contractor_details($work_contractor_id);
		$contractor_supplier = array_shift($work_contractor_q->result() );


		if($contractor_supplier->contractor_type == 2){
			$work_job_name = $contractor_supplier->job_sub_cat;
		}else{
			$work_job_name = $contractor_supplier->supplier_cat_name;
		}

		if($work_job_name == 'Other'){
			$work_job_name = $contractor_supplier->other_work_desc;
		}

	 

		$q_company_address = $this->etc_m->fetch_complete_detail_address($contractor_supplier->address_id);
		$contractor_supplier_addr = array_shift($q_company_address->result());

		$contractor_supplier_location = '';

 



		if( isset($contractor_supplier_addr->unit_level) && $contractor_supplier_addr->unit_level != '' ){
			$contractor_supplier_location .= 'Unit '.$contractor_supplier_addr->unit_level.'/';
		}

		$contractor_supplier_location .= $contractor_supplier_addr->unit_number.' '.$contractor_supplier_addr->street.'<br />'. ucwords(strtolower($contractor_supplier_addr->suburb)).', '.$contractor_supplier_addr->shortname.', '.$contractor_supplier_addr->postcode;

	 
		$contractor_supplier->abn = '(ABN:'.$contractor_supplier->abn.')';
		$file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );




		if($contractor_supplier->is_pending == 1){
			$q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
			$pending_contractor = array_shift($q_pending_contractor->result());

			$contractor_supplier->first_name = $pending_contractor->contact_person_fname;
			$contractor_supplier->last_name = $pending_contractor->contact_person_sname;

			$contractor_supplier->company_name = $pending_contractor->company_name;
			$contractor_supplier_location = '';
			$contractor_supplier_addr->phone_area_code = '';
			$contractor_supplier->office_number = $pending_contractor->contact_number;
			$contractor_supplier->general_email = $pending_contractor->email;

			$contractor_supplier->abn = '';
			$file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
		}




		$work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($work_contractor_id);
		


		if($work_contractor_joinery->num_rows > 0){

			$work_joinery_id = array_shift($work_contractor_joinery->result() );
			$distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
			$joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

			if($joinery_work_q->num_rows > 0){

				$joinery_work_details = array_shift($joinery_work_q->result() );
				

				$work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
				$contractor_supplier->notes = $joinery_work_details->notes;
			}

		}


		$gst_rate_q = $this->etc_m->get_current_gstRate();
		$rate = array_shift($gst_rate_q->result() );

		$gst = $rate->gst_rate/100;

		$inc_gst_rate = $contractor_supplier->ex_gst * $gst;
		$inc_gst = $contractor_supplier->ex_gst + $inc_gst_rate;

		if($this->has_inssurance($contractor_supplier->company_id)){
			$insurance_msg = $admin_defaults->cpo_notes_w_insurance;
		}else{
			$insurance_msg = $admin_defaults->cpo_notes_no_insurance;				
		}

	//	echo '<p class="">----------</p>';

	//	$long_details = nl2br($contractor_supplier->notes);

		$arr_details_lines = preg_split('/\r\n|\r|\n/',$contractor_supplier->notes);

// 25 max length of new lines  //<p style="page-break-after: always;"></p>

 

	 	$this->load->module('reports');
	 	$html['contents'] = '<div style="padding: 0px 20px;">
	 	<table width="100%" class="mrg-bottom--10">
	 		<tr>
	 			<td rowspan="2" width="30%"><img class="mrg-left-10 mrg-top-25" src="https://sojourn.focusshopfit.com.au/img/focus_logo_cqr.jpg" width="250px" height="82.67px" /></td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					'.$focus_company->company_name.'<br />
	 					PO '.$focus_postal_addr->po_box.'<br />
	 					'.ucwords(strtolower($focus_postal_addr->suburb)).' '.$focus_postal_addr->shortname.' '.$focus_postal_addr->postcode.'
	 				</p>
	 			</td>
	 			<td width="35%">
	 				<p id="" class="mrg-top-20 mrg-left-20">
	 					Tel: ('.$focus_company->area_code.') '.$focus_company->office_number.'<br />   
	 					ACN: '.$focus_company->acn.'<br /> 
	 					ABN: '.$focus_company->abn.' 
	 				</p>
	 			</td>
	 		</tr>
	 		<tr>
	 			<td colspan="2"><p id="" class="mrg-top-15 mrg-left-20">E-mail : '.$focus_company->general_email.'</p></td>
	 		</tr>
	 	</table>

	 	<div id="" class="divider"></div>

	 	
 		<table width="100%">
 			<tr>
 				<td width="50%">
 					<div id="" class="content_area pad-10 block"  style="height: 50px;">
	 					<p id="" class="text-center">
	 						<span style="font-weight: bold;    font-size: 18px;">Contractor Purchase Order</span><br />
	 						<strong class="">Date:</strong> '.$date_today.' &nbsp;  &nbsp;  &nbsp;  <strong class="">'.$work_id.' / '.$project_id.'</strong>
	 					</p>
 					</div>
 				</td>
 				<td width="50%">	
 				<div class="pad-10 block">
	 				<p id="" class="">
	 					<strong class="">Client</strong>: '.$client->company_name.'<br />
	 					<strong class="">Project</strong>: '.$prj_details->project_name.'<br />
	 					<strong class="">Our Contact</strong>: '.$pm_contact->user_full_name.'<br />
	 					<strong class="">Email</strong>: '.$pm_contact->general_email.'


	 				</p>	
 				</div> 				
 				</td>
 			</tr>
 		</table>

	 	<table width="100%">
	 		<tr>
	 			<td width="375px">
	 				<div id="" class="content_area pad-10 block heigh-auto  ">
	 					<p id="" class="content_title" style="width: 76px !important;">Contractor</p>
	 					<p id="" class="">
	 						'.$contractor_supplier->company_name.'<br />
	 						'.$contractor_supplier_location.'<br />	 					
	 						<strong id="" class="">Attention: '.$contractor_supplier->first_name.' '.$contractor_supplier->last_name.'</strong><br />
	 						Contact: '.$contractor_supplier_addr->phone_area_code.' '.$contractor_supplier->office_number.'
	 					</p>
	 				</div>
	 	 


	 			</td>
	 			<td width="375px">
		 			<div id="" class="content_area pad-10 block mrg-left-5" style="height: 93px;">
		 					<p id="" class="content_title" style="width: 88px !important;">Site Address</p>
		 					<p id="" class="">
		 						'.$site_addr.'<br />
		 						<strong class="">Office Number:</strong> '.$company_contact->area_code.' '.$company_contact->office_number.'
		 					</p>
		 			</div>  
		 		</td>
		 	</tr>

	 		<tr>

			 	<td colspan="2">

	 				<p id="" class="pad-top-5">
	 					<strong id="" class="">Description</strong>: '.$work_job_name.'<br />
	 					<strong id="" class="">Estimated Project Start</strong>: '.$prj_details->date_site_commencement.'  &nbsp;  &nbsp;  &nbsp;   &nbsp;  &nbsp;  &nbsp;   &nbsp;  &nbsp;  &nbsp; 
	 					<strong id="" class="">Finish</strong>: '.$prj_details->date_site_finish.'

	 				</p>

			 		<div id="" class="divider  mrg-top-5"></div>
				 	<div id="" class="pad-10 block heigh-auto" style="height:490px;">
				 		 <p id="" class="">';

				 		 foreach ($arr_details_lines as $key => $value) {
				 		 	$html['contents'] .= $value.'<br />';
				 		 	if($key % 30 == 0 && $key > 0){
				 		 		$html['contents'] .= '<p style="page-break-after: always;"></p><p><br /></p><p><br /></p>';
				 		 	}	

				 		 }





				 	$html['contents'] .= '</div>
			 	</td>

	 		</tr>';



	 		if( count($arr_details_lines) > 30 ){
	 			$html['contents'] .= '<tr><td colspan="2"><p class="">&nbsp;<br />&nbsp;</p></td></tr>';
	 		}

		 	$html['contents'] .= '<tr>
		 		<td colspan="2">
		 			<div id="" class="divider   "></div>
		 			<p class="text-center">'.$insurance_msg.'</p>
		 			<div id="" class="divider   "></div>
		 		</td>
		 	</tr>



		 	<tr>
	 			<td width="375px">
	 				<div id="" class="content_area pad-10 block heigh-auto  " style="height: 90px;">
	 					<p id="" class="content_title" style="width: 58px !important;">General</p>	 					
	 				</div>
	 	 


	 			</td>
	 			<td width="375px">
		 			<div id="" class="content_area pad-10 block mrg-left-5" style="height: 20px; font-weight: bold;">
		 				<strong style="font-size:18px;">Price</strong>
		 				<p class="text-right mrg-top--20"  style="font-size: 18px;"> $'.number_format($contractor_supplier->ex_gst,2).' EX-GST</p>
		 			</div>
		 			<p class="text-right  style=" font-size:8px;"> $'.number_format($inc_gst,2).' <sub style="font-weight: bold; font-size: 10px;">INC-GST &nbsp;</sub></p>
		 			<p id="" class="pad-5">
		 				<strong  style=" font-size: 13px;">Purchase Order number MUST be shown on all Invoices</strong><br />
		 				Standard terms 45 days EOM unless otherwise agreed
		 			</p>
		 		</td>
		 	</tr>





	 </table>
	</div>';

		
		$cqr_reply = 'cpo';

		$file_name = $forced_file_name; //$project_id.'_'.$cqr_reply.'_'.$work_id.'_'.$file_company_name.'_'.$work_contractor_id;




$file_name =  str_replace( array( "'",  '"', ',', '"' , '`'  ,'%' ,  '&apos', ' ' , ';', '&', '&apos', '<', '>' ), '', $file_name);

		$this->generate_cqr_pdf_etc($html['contents'],'portrait','A4',$file_name,$dir,$stream);
		$date_today = date('d/m/Y');
	//	$this->etc_m->set_cqr_data($work_contractor_id);

	//	$this->load->view('cqr_page',$html);


	}


		public function induction_project_exempted($project_id){
		$admin_defaults = $this->etc_m->fetch_admin_defaults(1);
		foreach ($admin_defaults->result() as $row){
			$induction_work_value = $row->induction_work_value;
			$induction_project_value = $row->induction_project_value;
			$induction_categories = $row->induction_categories;
		}

		$proj_q = $this->etc_m->fetch_complete_project_details($project_id);
		foreach ($proj_q->result() as $row) {	
			$job_category = $row->job_category;
			$project_value = $row->project_total;
			$project_estimate = $row->budget_estimate_total;
			$client_id = $row->client_id;
			$address_id = $row->address_id;
		}

		$q_client_company = $this->etc_m->display_company_detail_by_id($client_id);
		$client_company = array_shift($q_client_company->result_array());

		$query_client_address = $this->etc_m->fetch_complete_detail_address($address_id);
		$temp_data = array_shift($query_client_address->result_array());

		$project_is_exempted = $this->etc_m->project_is_exempted_induction($project_id);


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
				$post_code_is_exempted = $this->etc_m->postcode_excempted($temp_data['postcode']);
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



	public function send_pdf(){
		$this->load->view('send_pdf_view');
	}

	public function send_to_contractor_view(){
		$proj_id = $this->uri->segment(3);
		$proj_q = $this->etc_m->select_particular_project($proj_id);
		foreach ($proj_q->result_array() as $row){
			$data['job_category'] = $row['job_category'];
		}
		$project_schedule = $this->project_schedule_m->fetch_project_schedule($proj_id);
		if($project_schedule->num_rows > 0){
			foreach ($project_schedule->result_array() as $row){
				$data['is_printed'] = $row['is_printed'];
			}
		}else{
			$data['is_printed'] = 0;
		}

		$video_generated = $this->induction_health_safety_m->fetch_induction_videos_generated($proj_id);
		$data['video_generated'] = $video_generated;

		$data['induction_exempted'] = $this->projects->induction_project_exempted($proj_id);
		$this->load->view('send_to_contractor_view',$data);
	}





} 