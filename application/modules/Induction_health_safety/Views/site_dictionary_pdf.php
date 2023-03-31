<?php


// $pdf=new PDF_TextBox();
// $pdf->AddPage();
// $pdf->SetFont('Arial','',15);
// $pdf->SetXY(0,0);
// $pdf->drawTextBox('This sentence is centered in the middle of the box.', 100, 50, 'C', 'M');
// $pdf->Output();

// drawTextBox(string strText, float w, float h [, string align [, string valign [, boolean border]]])
// strText: the string to print
// w: width of the box
// h: height of the box
// align: horizontal alignment (L, C, R or J). Default value: L
// valign: vertical alignment (T, M or B). Default value: T
// border: whether to draw the border of the box. Default value: true

?>
<?php 

require_once('fpdf/fpdf.php');
require_once('fpdf/fpdi/fpdi.php');

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

function SetVariable($project_id,$client_name){
	$this->project_id = $project_id;
	$this->client_name = $client_name;
}


// function SetLogo($logo,$comp_name,$tel_no,$po_box,$acn,$focus_suburb,$abn,$focus_email,$inc_gst,$project_total,$price,$cpo_notes_insurance,$insurance_stat) 
// { 
//   $this->logo = $logo;  
//   $this->comp_name = $comp_name; 
//   $this->tel_no = $tel_no; 
//   $this->po_box = $po_box;
//   $this->acn = $acn;
//   $this->abn = $abn;
//   $this->focus_suburb = $focus_suburb;
//   $this->focus_email = $focus_email;
//   $this->inc_gst = $inc_gst;
//   $this->project_total = "$ ".number_format($project_total,2);
//   $this->price = "$ ".number_format($price,2);
//   $this->cpo_notes_insurance = $cpo_notes_insurance;
//   $this->insurance_stat = $insurance_stat;
// } 
//Page header
function Header()
{  
	$this->SetFont('Arial','B',11);
	$this->ln(-5);
	$this->Cell(5,0,'');
	$this->Cell(80,20,'Safe Construction Management Plan');
    $this->Image('./uploads/misc/flogo.png',165,7,35);
    $this->Line(10, 20, 200, 20);
}

//Page footer
function Footer()
{
    global $date; 
    $date = date('d M Y');
    //Position at 1.5 cm from bottom
    $this->SetY(-55);
    //Arial italic 8
	$this->Line(10, 238, 200, 238);
    $this->Image('./docs/tempqrcode/site_diary/'.$this->project_id.'/qrcode.png',15,240,30);
    $this->SetXY(46, 240);
  	$this->MultiCell(150,5,"If you have completed the Site Specific Induction for ".$this->client_name." #".$this->project_id." then you can sign in AND out by scanning this QR code with you camera, following the link and entering your mobile number.  PLEASE NOTE: you must do this at the end of the day or when you leave the site also.",0,"C");
  	$this->SetXY(46, 256);
  	$this->MultiCell(150,5,"This will not work UNLESS you have completed the Site Specific Induction.",0,"C");
  	$this->SetXY(46, 263);
  	$this->MultiCell(150,5,"If you have any questions please call the office on 1300 373 373 and ask to speak with Katrina Isidro.",0,"C");
    $this->Line(10, 270, 200, 270);
    $this->SetFillColor(139,137,137);
    $this->SetFont('Arial','B',10);
    $this->Ln(25);
    $this->SetXY(10, 275);
  	$this->MultiCell(190,5,"Anyone coming on to this site is required to have completed the Site Specific Induction",0,"C");
    // $this->Cell(0,1,'All Focus Employee and Contractors MUST Read and Sign the Induction Tool Box Talk Prior to Starting Work');
    $this->Ln(1);
 

  
}
}
//require('fpdf/textbox/textbox.php');
$pdf=new PDF();
$pdf->SetVariable($project_id,$client_name);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,50);

$pdf->ln(10);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(5,10,'');
$pdf->Cell(30,20,'Site Diary');
$pdf->SetFont('Arial','',10);
$pdf->Cell(15,20,'Client: ');
$pdf->Cell(80,20,$client_name);
$pdf->Cell(15,20,'Job No: ');
$pdf->Cell(20,20,$project_id);

$pdf->ln(15);
$pdf->Cell(5,10,'');
$pdf->SetFillColor(8,3,154);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(15,8,'Date',1,0,'C',1);
$pdf->Cell(60,8,'Full Name',1,0,'C',1);
$pdf->Cell(70,8,'Company',1,0,'C',1);
$pdf->Cell(20,8,'Time-in',1,0,'C',1);
$pdf->Cell(20,8,'Time-out',1,0,'C',1);
foreach ($project_site_login_q->result_array() as $row){
	$project_id = $row['project_id'];
	$log_date = $row['log_date'];
	$log_date=date_create($log_date);
	$log_date = date_format($log_date,"d/m");


	$site_staff_name = $row['site_staff_name'];
	$company_name = $row['company_name'];
	$dateObject = new DateTime($row['login_datetime']);
	$time_in = $dateObject->format('h:i A');
	$dateObject = new DateTime($row['logout_datetime']);
	$time_out = $dateObject->format('h:i A');
	if($company_name == ""){
		$company_name = "Focus";
	}
	$pdf->ln(8);
	$pdf->Cell(5,10,'');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(15,8,$log_date,1);
	$pdf->Cell(60,8,$site_staff_name,1);
	$pdf->Cell(70,8,$company_name,1);
	$pdf->Cell(20,8,$time_in,1);
	$pdf->Cell(20,8,$time_out,1);
	
}



$filepath = "uploads/project_site_diary/".$project_id."/";

if (!file_exists($filepath)) {
    mkdir($filepath, 0755, true);
}


$pdf->Output($filepath.'site_diary.pdf','F');
$pdf->Output($filepath.'site_diary.pdf','I');

$pdf->Output();
?>