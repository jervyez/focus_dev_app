<?php
namespace App\Modules\Dev_notes\Models;

class Dev_notes_m{

	protected $db;

    public function __construct(){
        $db = \Config\Database::connect();
        $this->db = $db;
    }

    public function insert_post($dn_user_post,$dn_title, $dn_post_details, $dn_category, $dn_date_posted ,  $dn_date_commence, $dn_prgm_user_id,$dn_section_id, $dn_bugs  ){
        $this->db->query("INSERT INTO `dev_notes_tread` ( `dn_user_post`, `dn_title`, `dn_post_details`, `dn_category`, `dn_date_posted`, `dn_date_commence`, `dn_prgm_user_id`, `dn_section_id`, `is_bug_report` ) 
        VALUES ( '$dn_user_post,', '$dn_title','$dn_post_details', '$dn_category','$dn_date_posted' ,'$dn_date_commence', '$dn_prgm_user_id' , '$dn_section_id' , '$dn_bugs' ) ");
        return $this->db->insertID();  
    }

  
    public function list_post($is_bugs=''){
        $query = $this->db->query("SELECT  *,  UNIX_TIMESTAMP( STR_TO_DATE(`dev_notes_tread`.`dn_date_posted`, '%d/%m/%Y') ) AS `unix_posted` , CASE `dev_notes_tread`.`dn_category`
            WHEN 'Urgent' THEN 1
            WHEN 'Important' THEN 2
            WHEN 'When Time Permits' THEN 3
            WHEN 'Maybe' THEN 4
            END AS `priority_sort` 
            FROM `dev_notes_tread` 
            LEFT JOIN `dn_section` ON `dn_section`.`dn_section_id` = `dev_notes_tread`.`dn_section_id` 
            WHERE `dev_notes_tread`.`is_active` = '1'
            ".($is_bugs != '' ? " AND `dev_notes_tread`.`is_bug_report`  = '1' " : " AND  `dev_notes_tread`.`is_bug_report`  = '0' ")."
            ORDER BY `priority_sort` ASC , `dev_notes_tread`.`dn_id` ASC");
        return $query;      
    }

    public function view_post_detail($post_id){
        $query = $this->db->query("SELECT  `dev_notes_tread`.*,  CONCAT(  `users`.`user_first_name` ,' ', `users`.`user_last_name`  ) AS `posted_by` 
            FROM `dev_notes_tread` LEFT JOIN `users` ON `users`.`user_id` = `dev_notes_tread`.`dn_user_post` WHERE `dev_notes_tread`.`dn_id` = '$post_id' AND `dev_notes_tread`.`is_active` = '1' ");
        return $query;
    }

    public function delete_post($post_id){
        $query = $this->db->query("UPDATE `dev_notes_tread` SET `is_active` = '0' WHERE `dev_notes_tread`.`dn_id` = '$post_id'");
        return $query;
    }

    public function delete_section($id){
        $query = $this->db->query("UPDATE`dn_section` SET `is_active` = '0' WHERE `dn_section`.`dn_section_id` = '$id'  ");
        return $query;
    }

    public function update_section($id,$section_label){
        $query = $this->db->query("UPDATE `dn_section` SET `dn_section_label` = '$section_label' WHERE `dn_section`.`dn_section_id` = '$id'  ");
        return $query;
    }


    public function post_comment($dn_post_date,$dn_post_user_id,$dn_tread_id,$dn_post_details){
        $query = $this->db->query("INSERT INTO `dev_notes` (`dn_post_date`, `dn_post_user_id`, `dn_tread_id`, `dn_post_details`) VALUES ('$dn_post_date','$dn_post_user_id','$dn_tread_id','$dn_post_details'  ) ");
        return $this->db->insertID();  
        return $query;
    }

    public function list_comments($post_id){
        $query = $this->db->query(" SELECT * FROM `dev_notes` WHERE `dev_notes`.`is_active`  = '1' AND `dev_notes`.`dn_tread_id` = '$post_id' ORDER BY `dev_notes`.`dn_post_id` ASC ");
        return $query;
    }


    public function update_post($dn_title,$dn_post_details,$dn_category,$dn_date_commence,$dn_date_complete,$dn_prgm_user_id,$dn_id,$dn_section_id ){
        $query = $this->db->query("UPDATE `dev_notes_tread` SET `dn_title` = '$dn_title', `dn_post_details` = '$dn_post_details', `dn_category` = '$dn_category',`dn_section_id` = '$dn_section_id', `dn_date_commence` = '$dn_date_commence', `dn_date_complete` = '$dn_date_complete', `dn_prgm_user_id` = '$dn_prgm_user_id' WHERE `dev_notes_tread`.`dn_id` = '$dn_id' ");
        return $query;
    }

    public function fetch_sections(){
        $query = $this->db->query("SELECT * FROM `dn_section` WHERE `dn_section`.`is_active` = '1' ORDER BY `dn_section`.`dn_section_label` ASC ");
        return $query;
    }

    public function insert_new_section($section){
        $query = $this->db->query("INSERT INTO `soso1713_astute`.`dn_section` (  `dn_section_label` ) VALUES ( '$section' )");
        return $query;
    }


}

