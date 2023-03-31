<?php
namespace App\Modules\Contacts\Models;

class Contacts_m{

	protected $db;

    public function __construct(){
        $db = \Config\Database::connect();
        $this->db = $db;
    }

    public function fetch_contacts(){       
        $query = $this->db->query("select k.company_type_id,a.*,b.company_name,b.active,b.company_type_id, c.first_name, c.last_name, d.general_email, e.*, f.*, g.*,h.suburb,i.shortname
                            from contact_person_company a 
                            Left join company_details b on a.company_id = b.company_id 
                            left join contact_person c on a.contact_person_id = c.contact_person_id 
                            left join email d on c.email_id = d.email_id 
                            left join contact_number e on c.contact_number_id = e.contact_number_id 
                            left join job_category f on f.job_category_id = b.activity_id
                            left join supplier_cat g on g.supplier_cat_id = b.activity_id
                            left join address_detail j on j.address_detail_id = b.address_id
                            left join address_general h on h.general_address_id = j.general_address_id
                            left join states i on i.id = h.state_id
                            left join company_type k on k.company_type_id = b.company_type_id
                            where b.active = 1 AND a.is_active = 1
                            order by c.first_name");        
        return $query;
    }

    public function fetch_contacts_by_company_type($contact_type){      
        $query = $this->db->query("select a.*,b.company_name,b.company_type_id, c.first_name, c.last_name, d.general_email, e.*, f.*, g.*,h.suburb,i.shortname
                            from contact_person_company a 
                            Left join company_details b on a.company_id = b.company_id 
                            left join contact_person c on a.contact_person_id = c.contact_person_id 
                            left join email d on c.email_id = d.email_id 
                            left join contact_number e on c.contact_number_id = e.contact_number_id 
                            left join job_category f on f.job_category_id = b.activity_id
                            left join supplier_cat g on g.supplier_cat_id = b.activity_id
                            left join address_detail j on j.address_detail_id = b.address_id
                            left join address_general h on h.general_address_id = j.general_address_id
                            left join states i on i.id = h.state_id
                            where company_type_id = '$contact_type'
                            order by c.first_name");        
        return $query;
    }

    public function fetch_contacts_by_id($contact_id){      
        $query = $this->db->query("select a.*,b.company_name,b.company_type_id, c.first_name, c.last_name, d.general_email, e.*, f.*, g.*,h.suburb,i.name,i.shortname
                            from contact_person_company a 
                            Left join company_details b on a.company_id = b.company_id 
                            left join contact_person c on a.contact_person_id = c.contact_person_id 
                            left join email d on c.email_id = d.email_id 
                            left join contact_number e on c.contact_number_id = e.contact_number_id 
                            left join job_category f on f.job_category_id = b.activity_id
                            left join supplier_cat g on g.supplier_cat_id = b.activity_id
                            left join address_detail j on j.address_detail_id = b.address_id
                            left join address_general h on h.general_address_id = j.general_address_id
                            left join states i on i.id = h.state_id
                            where a.contact_person_id = '$contact_id'
                            order by c.first_name");        
        return $query;
    }

    public function fetch_contact_list_for_all(){
        $query = $this->db->query("SELECT CONCAT(t1.`user_first_name`, ' ', t1.`user_last_name`) AS staff_full_name, t2.`direct_number`, t2.`mobile_number`, t2.`personal_mobile_number` ,t1.`user_skype`, t3.`general_email`, t3.`personal_email`
                                    FROM `users` AS t1
                                    LEFT JOIN `contact_number` as t2 ON t1.`user_contact_number_id` = t2.`contact_number_id` 
                                    LEFT JOIN `email` as t3 ON t1.`user_email_id` = t3.`email_id` 
                                    WHERE t1.`is_active` = 1 
                                    ORDER BY t1.`user_first_name`");
        return $query;
    }

    public function fetch_focus_staff_all(){
        $query = $this->db->query("SELECT CONCAT(t1.`user_first_name`, ' ', t1.`user_last_name`) AS contact_name, t2.`company_name`, t3.`direct_number`, t3.`mobile_number`, t3.`personal_mobile_number`, t4.`general_email`, t4.`personal_email`, t1.`user_skype`, t1.`is_active`
                                    FROM `users` AS t1
                                    LEFT JOIN `company_details` AS t2 ON t1.`user_focus_company_id` = t2.`company_id`
                                    LEFT JOIN `contact_number` AS t3 ON t1.`user_contact_number_id` = t3.`contact_number_id`
                                    LEFT JOIN `email` AS t4 ON t1.`user_email_id` = t4.`email_id`
                                    WHERE t1.`is_active` = '1'
                                    ORDER BY t1.`user_first_name`");
        return $query;
    }


}

