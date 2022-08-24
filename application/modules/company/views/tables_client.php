<?php
foreach ($com_c->result_array() as $row){
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
	
	$complete = 0;
	$incomplete = 0;
	
	if($row['company_type_id'] == '2'){
		if($row['has_insurance_public_liability'] == 1){
			if($row['public_liability_expiration'] !== ""){
				if($ple_date <= $today){
					$incomplete = 1;
				}else{
					if($row['has_insurance_workers_compensation'] == 1){
						if($row['workers_compensation_expiration'] !== ""){
							if($wce_date <= $today){
								$incomplete = 1;
							}else{
								$complete = 1;
							}
						}else{
							$incomplete = 1;
						}
					}else{
						if($row['has_insurance_income_protection'] == 1){
							if($row['income_protection_expiration'] !== ""){
								if($ipe_date <= $today){
									$incomplete = 1;
								}else{
									$complete = 1;
								}
							}else{
								$incomplete = 1;
							}
						}else{
							$incomplete = 1;
						}
					}
				}
			}else{
				$incomplete = 1;
			}
			
		}else{
			$incomplete = 1;
		}
	}

	$font_color = "";
	if($row['company_type_id'] == '2'){
		if($complete == 1){
			$font_color = "Blue";
		}else{
			if($incomplete == 1){
				$font_color = "Red";
			}
		}
	}
	//echo '<tr><td><a href="'.base_url().'company/view/'.$row['company_id'].'" >'.$row['company_name'].'</a></td><td>'.ucwords(strtolower($row['suburb'])).' '.$row['shortname'].'</td><td>'.$row['area_code'].' '.$row['office_number'].'</td><td>'. strtolower($row['general_email']).'</td></tr>';
	echo '<tr><td><a href="'.base_url().'company/view/'.$row['company_id'].'" style = "color:'.$font_color.'">'.$row['company_name'].'</a></td><td>'.ucwords(strtolower($row['suburb'])).' '.$row['shortname'].'</td>';
	
	$company_id = $row['company_id'];
	$company_name = $row['company_name'];
	$logo_path = base_url().$row['logo_path'];

	echo $this->company->fetch_contacts($company_id);

	if($complete == 1){
		echo '<td>1</td>';
	}else{
		echo '<td>0</td>';
	}

	if ($row['company_type_id'] == 1){
		if ($row['logo_path'] === NULL){
			echo '<td><button class="btn btn-primary btn-xs" onclick="getCompanyData(\''.$company_id.'\', \''.$company_name.'\');">Upload</button></td>';	
		} else {
			echo '<td><button class="btn btn-success btn-xs" onclick="getCompanyLogoData(\''.$company_id.'\', \''.$company_name.'\', \''.$logo_path.'\');">View Logo</button></td>';
		}
	}

	echo '</tr>';
}

?>

<script type="text/javascript">
	
	function getCompanyData(id, name){
		$("#companyLogoUpload").modal('show');
		$("h4.company_logo_title").text('Company Upload Logo: '+name);
		$(".company_id").val(id);
	}

	function getCompanyLogoData(id, name, logo_path){
		$("#companyLogoDisplay").modal('show');
		$(".company_id").val(id);
		$("h4.company_logo_title").text(name);
		$("#logoPath").attr("src", logo_path);
	}

</script>