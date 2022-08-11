<?php
foreach ($project_t->result_array() as $row){
  $client_id = $row['company_id'];
  $compname = $row['company_name'];
  $compname = str_replace('&apos;', "'", $compname);

  $leading_hand = $row['lh_fname']." ".$row['lh_sname'];

  $project_name = $row['project_name'];
  $project_name = str_replace("&apos;","'", $project_name);
  
  $company_name = str_replace(' ', '', $compname);
  $company_name = str_replace('/', '', $company_name);
 

  $filepath = "reports/".$client_id."_".$company_name."/".$proj_id."/safety_site_observation/";

  $site_address = "Unit/Level:".$site_address_1st.", ".$site_address_2nd.", ".$site_address_3rd;

  $dt = new DateTime($row['stsc_date_time']);

  $date_send = $dt->format('d/m/Y');
  $time_send = $dt->format('H:i:s');



define('FPDF_FONTPATH','font/');
require('fpdf/fpdf.php');

class PDF extends FPDF
{
  // Simple table
function BasicTable($header, $data)
{
    // Header
    foreach($header as $col)
        $this->Cell(40,7,$col,1);
    	$this->Ln();
    // Data
    foreach($data as $row)
    {
        foreach($row as $col)
            $this->Cell(40,6,$col,1);
        $this->Ln();
    }
}

function SetLogo($logo,$comp_name,$tel_no,$po_box,$acn,$focus_suburb,$abn,$focus_email,$project_total,$user_first_name,$user_last_name,$project_name,$date_send,$role_types) 
{ 
  $this->logo = $logo;  
  $this->comp_name = $comp_name; 
  $this->tel_no = $tel_no; 
  $this->po_box = $po_box;
  $this->acn = $acn;
  $this->abn = $abn;
  $this->focus_suburb = $focus_suburb;
  $this->focus_email = $focus_email;
  $this->project_total = "$ ".number_format($project_total,2);
  $this->user_first_name = $user_first_name;
  $this->user_last_name = $user_last_name;
  $this->project_name = $project_name;
  $this->date_send = $date_send;
  $this->role_types = $role_types;
} 
//Page header
function Header()
{  
    $this->Image('./uploads/misc/'.$this->logo,170,9,30);
    //Arial bold 15
    $this->SetFont('Arial','',12);
    //Title
    $this->Cell(50,5,"Safe Construction Management Plan",0,0,'');
    $this->Ln(10);
	$this->SetFillColor(139,137,137);
	$this->Cell(0,1,'',1,0,'',true);
	$this->Ln(10);
}

//Page footer
function Footer()
{
    global $date; 
    $date = date('d M Y');
    $this->SetFont('Arial','I',8);
    $this->SetY(-25);
    $this->Cell(190,10,"",1);
    $this->Cell(-190);
    $this->MultiCell(185,5,"I declare that the on-site documentation and the site work practises are adequate. If this is not the case and that action is required, it is my responsibility to see to it that it is put in place and followed up.",0,'C');
    $this->ln(0);
    $this->Cell(25,6,"Name",1);
    $this->Cell(70,6,$this->user_first_name." ".$this->user_last_name,1,0,'L',true);
    $this->Cell(25,6,"Title",1);
    $this->Cell(70,6,$this->role_types,1,0,'L',true);
}
}
//Instanciation of inherited class
$pdf=new PDF();
$pdf->SetLogo($focus_logo,$focus_comp,$office_no,$po_box,$acn,$focus_suburb,$abn,$focus_email,$project_total,$row['user_first_name'],$row['user_last_name'],$project_name,$date_send,$row['role_types']);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,20);

$pdf->Ln(-8);
$pdf->SetFont('Arial','B',15);
$pdf->Cell(90,8,'Safe to Start Checklist',0,0,'L');

$pdf->Ln(10);
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(22,2,100);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,6,'Job Number',1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(40,6,$proj_id,1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(22,2,100);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,6,'Construction Mgr.',1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(100,6,$leading_hand ,1,1,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(22,2,100);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,6,'Client',1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(40,6,$compname,1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(22,2,100);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,6,'Site Address',1,0,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(100,6,$site_address,1,1,'L',true);

// $pdf->SetFont('Arial','B',8);
// $pdf->SetFillColor(22,2,100);
// $pdf->SetTextColor(255,255,255);
// $pdf->Cell(25,6,'Date',1,0,'L',true);

// $pdf->SetFont('Arial','B',8);
// $pdf->SetFillColor(255,255,255);
// $pdf->SetTextColor(0,0,0);
// $pdf->Cell(40,6,$date_send,1,0,'L',true);

// $pdf->SetFont('Arial','B',8);
// $pdf->SetFillColor(22,2,100);
// $pdf->SetTextColor(255,255,255);
// $pdf->Cell(25,6,'Time',1,0,'L',true);

// $pdf->SetFont('Arial','B',8);
// $pdf->SetFillColor(255,255,255);
// $pdf->SetTextColor(0,0,0);
// $pdf->Cell(100,6,$time_send,1,1,'L',true);

$pdf->Ln(3);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(22,2,100);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(75,6,'',1,0,'L',true);

$pdf->Cell(10,6,'Yes',1,0,'C',true);
$pdf->Cell(10,6,'No',1,0,'C',true);

$pdf->Cell(95,6,'Comments',1,1,'C',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(6,137,176);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(75,6,'Documentation',1,0,'L',true);

$pdf->Cell(10,6,'',1,0,'C',true);
$pdf->Cell(10,6,'',1,0,'C',true);

$pdf->Cell(95,6,'',1,1,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,8,'      Generic SWMS for tasks',1,0,'T',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['swms_comment']);
$pdf->MultiCell(95,5,$row['swms_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Focus site specific SWMS completed',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['swms_completed_comment']);
$pdf->MultiCell(95,5,$row['swms_completed_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      SWMS from all Contractors provided',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['contractors_swms_provided_comment']);
$pdf->MultiCell(95,5,$row['contractors_swms_provided_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Induction Toolbox Talks',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['induction_toolbox_talk_comment']);
$pdf->MultiCell(95,5,$row['induction_toolbox_talk_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Toolbox Talk Forms',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['toolbox_talk_forms_comment']);
$pdf->MultiCell(95,5,$row['toolbox_talk_forms_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Incident and Hazard Report Forms',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['incident_hazard_report_comment']);
$pdf->MultiCell(95,5,$row['incident_hazard_report_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Site Contact Poster Displayed',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['site_contact_displayed_comment']);
$pdf->MultiCell(95,5,$row['site_contact_displayed_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      MSDS for all hazardous chemicals or substances',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['msds_comment']);
$pdf->MultiCell(95,5,$row['msds_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Asbestos Report (if applicable)',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['asbestors_report_comment']);
$pdf->MultiCell(95,5,$row['asbestors_report_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}


$pdf->SetFillColor(6,137,176);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(75,6,'Emergency Response:',1,0,'L',true);

$pdf->Cell(10,6,'',1,0,'C',true);
$pdf->Cell(10,6,'',1,0,'C',true);

$pdf->Cell(95,6,'',1,1,'L',true);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,8,'      First Aid Kit available',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['first_aid_kit_commnent']);
$pdf->MultiCell(95,5,$row['first_aid_kit_commnent'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Nearest Medical Centre, Location & hours',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['nearest_medical_center_comment']);
$pdf->MultiCell(95,5,$row['nearest_medical_center_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Evacuation Area identified',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['evacuation_area_comment']);
$pdf->MultiCell(95,5,$row['evacuation_area_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->SetFillColor(6,137,176);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(75,6,'Site Safety:',1,0,'L',true);

$pdf->Cell(10,6,'',1,0,'C',true);
$pdf->Cell(10,6,'',1,0,'C',true);

$pdf->Cell(95,6,'',1,1,'L',true);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,8,'      Site Specific hazards identified',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['site_spcific_hazard_comments']);
$pdf->MultiCell(95,5,$row['site_spcific_hazard_comments'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Sufficient Lighting',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['sufficient_lighting_comment']);
$pdf->MultiCell(95,5,$row['sufficient_lighting_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Safe access and exit',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['safe_access_comment']);
$pdf->MultiCell(95,5,$row['safe_access_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Power tools - safe to use',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['power_tools_comment']);
$pdf->MultiCell(95,5,$row['power_tools_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Working at Heights (over 2 metres)',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['working_at_heights_comment']);
$pdf->MultiCell(95,5,$row['working_at_heights_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Ladders - safe to use',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['ladders_comment']);
$pdf->MultiCell(95,5,$row['ladders_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Asbestos',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['asbestos_comment']);
$pdf->MultiCell(95,5,$row['asbestos_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Electric leads tested & tagged',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['electric_leads_comment']);
$pdf->MultiCell(95,5,$row['electric_leads_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Hot works (if applicable)',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['hot_works_comment']);
$pdf->MultiCell(95,5,$row['hot_works_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->Cell(75,8,'      Local site rules',1,0,'L',true);
$pdf->Cell(10,8,'',1,0,'C',true);
$pdf->Cell(10,8,'',1,0,'C',true);

$pdf->Cell(95,8,'',1,0,'L',true);
$pdf->Cell(-95,8);
$str_len = strlen($row['local_site_rules_comment']);
$pdf->MultiCell(95,5,$row['local_site_rules_comment'],0,'L');
if($str_len < 55){
	$pdf->ln(3);
}

$pdf->SetFillColor(6,137,176);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(75,6,'Comments / Others:',1,0,'L',true);

$pdf->Cell(10,6,'',1,0,'C',true);
$pdf->Cell(10,6,'',1,0,'C',true);

$pdf->Cell(95,6,'',1,1,'L',true);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(190,12,'',1,0,'L',true);
$pdf->Cell(-185);
$pdf->MultiCell(185,5,$row['other_comments']);


//Checkboxes
switch($row['swms_for_task']){
	case 0:
		$pdf->Image('./img/check-red.png',98,61,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,61,4); // yes
		break;
}

switch($row['swms_completed']){
	case 0:
		$pdf->Image('./img/check-red.png',98,69,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,69,4); // yes
		break;
}

switch($row['contractors_swms_provided']){
	case 0:
		$pdf->Image('./img/check-red.png',98,77,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,77,4); // yes
		break;
}

switch($row['induction_toolbox_talk']){
	case 0:
		$pdf->Image('./img/check-red.png',98,85,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,85,4); // yes
		break;
}

switch($row['toolbox_talk_forms']){
	case 0:
		$pdf->Image('./img/check-red.png',98,93,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,93,4); // yes
		break;;
}

switch($row['incident_hazard_report']){
	case 0:
		$pdf->Image('./img/check-red.png',98,101,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,101,4); // yes
		break;
}

switch($row['site_contact_displayed']){
	case 0:
		$pdf->Image('./img/check-red.png',98,109,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,109,4); // yes
		break;
}

switch($row['msds']){
	case 0:
		$pdf->Image('./img/check-red.png',98,117,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,117,4); // yes
		break;
}

switch($row['asbestors_report']){
	case 0:
		$pdf->Image('./img/check-red.png',98,125,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,125,4); // yes
		break;
}

//Skip -------------

switch($row['first_aid_kit']){
	case 0:
		$pdf->Image('./img/check-red.png',98,139,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,139,4); // yes
		break;
}

switch($row['nearest_medical_center']){
	case 0:
		$pdf->Image('./img/check-red.png',98,147,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,147,4); // yes
		break;
}

switch($row['evacuation_area']){
	case 0:
		$pdf->Image('./img/check-red.png',98,155,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,155,4); // yes
		break;
}

//Skip -------------

switch($row['site_spcific_hazard']){
	case 0:
		$pdf->Image('./img/check-red.png',98,169,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,169,4); // yes
		break;
}

switch($row['sufficient_lighting']){
	case 0:
		$pdf->Image('./img/check-red.png',98,177,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,177,4); // yes
		break;
}

switch($row['safe_access']){
	case 0:
		$pdf->Image('./img/check-red.png',98,185,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,185,4); // yes
		break;
}

switch($row['power_tools']){
	case 0:
		$pdf->Image('./img/check-red.png',98,193,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,193,4); // yes
		break;
}

switch($row['working_at_heights']){
	case 0:
		$pdf->Image('./img/check-red.png',98,201,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,201,4); // yes
		break;
}

switch($row['ladders']){
	case 0:
		$pdf->Image('./img/check-red.png',98,209,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,209,4); // yes
		break;
}

switch($row['asbestos']){
	case 0:
		$pdf->Image('./img/check-red.png',98,217,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,217,4); // yes
		break;
}

switch($row['electric_leads']){
	case 0:
		$pdf->Image('./img/check-red.png',98,225,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,225,4); // yes
		break;
}

switch($row['hot_works']){
	case 0:
		$pdf->Image('./img/check-red.png',98,233,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,233,4); // yes
		break;
}

switch($row['local_site_rules']){
	case 0:
		$pdf->Image('./img/check-red.png',98,241,4); // No
		break;
	case 1:
		$pdf->Image('./img/check.png',88,241,4); // yes
		break;
}


if (!file_exists($filepath)) {
    mkdir($filepath, 0755, true);
}

$pdf->Output($filepath.$proj_id.'_safety_site_observation.pdf','F');
$pdf->Output($proj_id.'_safety_site_observation.pdf','I');
}
?>