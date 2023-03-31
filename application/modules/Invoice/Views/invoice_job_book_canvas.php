<?php use App\Modules\Company\Controllers\Company; ?>
<?php $this->company = new Company(); ?>

<?php use App\Modules\Invoice\Controllers\Invoice; ?>
<?php $this->invoice = new Invoice(); ?>

<?php use App\Modules\Projects\Controllers\Projects; ?>
<?php $this->projects = new Projects(); ?>


<?php // $datestring = "%l, %F %d, %Y"; $time = time(); //use time() for timestamp  ?>
<?php //$this->invoice->reload_invoiced_amount(); ?>
<?php $show_job_book_details = $this->invoice->show_job_book($project_id); ?>
<!DOCTYPE>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script type="text/javascript" language="javascript" src="<?php echo site_url(); ?>js/vendor/jquery-1.11.0.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link href="<?php echo site_url(); ?>css/font-awesome.min.css" rel="stylesheet">
  <script src="<?php echo site_url(); ?>js/vendor/bootstrap.min.js"></script> 
  <script src="<?php echo site_url(); ?>js/pdf.js"></script>

</head>
<body class="canvas_body">

<div style="width: 130px; float:right; margin-right:10px; margin-top: 0px;margin-bottom: -20px;"><a href="#" class="highlight"><strong><i class="fa fa-pencil-square"></i></strong></a> &nbsp;  &nbsp;  &nbsp; <a href="#" class="add_text"><strong><i class="fa fa-text-width"></i></strong></a> &nbsp;  &nbsp;  &nbsp;  <a href="#" class="remove_elm_pdf"><strong><i class="fa fa-trash"></i></strong></a>&nbsp;  &nbsp;  &nbsp;  <a href="#" class="produce_pdf"><strong><i class="fa fa-file-image-o"></i></strong></a></div>

<form method="post" action="../../reports/pdf" style="display:none;">
  <textarea name="content" id="content"></textarea>
  <input type="submit" id="submit_pdf">
</form>

<div class="canvas_area editor_body">
 <table width="100%">
  <tr>
    <td width="50%"><img src="<?php echo site_url(); ?>img/logo.png" width="192" height="95" /></td>
    <td width="50%"><p class="text-right" style="font-size: 16px !important;"><strong><?php echo $focus_company_name; ?></strong></p></td>
  </tr>
</table>

  <div class="header pad-10 clearfix border-1">
  <table width="100%">
    <tr>
      <td width="70%"><p class="text-left">Client: <strong><?php echo $client_company_name; ?></strong> - <strong><?php echo $job_category; ?></strong><br />Project: <strong><?php echo $project_name; ?> <?php echo $client_po; ?></strong></p></td>
      <td width="30%"><p class="text-right"><strong>Job Book</strong><br />Project No. <strong><?php echo $project_id; ?></strong></p></td>
    </tr>
  </table>    
  </div>
  <hr class="full clearfix mgn-top-15" />

  <div class="full clearfix mgn-top-15">
    <table width="100%" class="d_edit">
      <tr>
        <td width="25%"><p>Contact: <strong><?php echo $contact_person_fname.' '.$contact_person_lname; ?></strong></p></td>
        <td width="25%"><p><?php if($contact_person_phone_office != ''): echo 'Office No: <strong>'.$contact_person_phone_office.'</strong>'; endif; ?></p></td>
        <td width="25%"><p><?php if($contact_person_phone_mobile != ''): echo 'Mobile No: <strong>'.$contact_person_phone_mobile.'</strong>'; endif; ?></p></td>
        <td width="25%"><p><?php if($contact_person_phone_direct != ''): echo 'Direct No: <strong>'.$contact_person_phone_direct.'</strong>'; endif; ?></p></td>
      </tr>
    </table>
  </div>


  <div class="clearfix c_edit">

  <fieldset class="pad-5 border-1 mgn-top-15 pad-10">
    <legend class="pad-l-10 pad-r-10"><strong>Client / Company Address</strong></legend>

    <table width="100%" class="c_edit">
      <tr>
        <td width="33%" valign="top">
          <p class=""><strong><?php echo $client_company_name; ?></strong></p>
          <p><?php echo $query_client_address_unit_number.' '.$query_client_address_unit_level.' '.$query_client_address_street; ?></p>
          <p class=""><?php echo $query_client_address_suburb.' '.$query_client_address_state.' '.$query_client_address_postcode; ?></p>
        </td>
        <td width="33%" valign="top">
          <p class=""><?php if($company_contact_details_office_number != ''): echo 'Office No: <strong>'.$company_contact_details_area_code.' '.$company_contact_details_office_number.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_direct_number != ''): echo 'Direct No: <strong>'.$company_contact_details_area_code.' '.$company_contact_details_direct_number.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_mobile_number != ''): echo 'Mobile No: <strong>'.$company_contact_details_mobile_number.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_after_hours != ''): echo 'After Hours: <strong>'.$company_contact_details_area_code.' '.$company_contact_details_after_hours.'</strong>'; endif; ?></p>
        </td>
        <td width="33%" valign="top">
          <p class=""><?php if($company_contact_details_general_email != ''): echo 'General Email: <strong>'.$company_contact_details_general_email.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_direct != ''): echo 'Direct Email: <strong>'.$company_contact_details_direct.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_accounts != ''): echo 'Accounts Email: <strong>'.$company_contact_details_accounts.'</strong>'; endif; ?></p>
          <p class=""><?php if($company_contact_details_maintenance != ''): echo 'Maintenance Email: <strong>'.$company_contact_details_maintenance.'</strong>'; endif; ?></p>
        </td>
      </tr>
    </table>

</div>



<div class="clearfix g_edit">
  <fieldset class="pad-5 border-1 mgn-top-15 pad-10">
    <legend class="pad-l-10 pad-r-10"><strong>Address</strong></legend>
    <table width="100%" class="j_edit">
      <tr>
        <td width="50%" valign="top">
          <p class=""><strong>Site</strong></p>
          <p><?php $shop_tenancy_numb = ($job_type != 'Shopping Center' ? '' : ''.$shop_tenancy_number.', '.$shopping_common_name.'<br />' ); ?>
          <p><?php echo "$shop_tenancy_numb $unit_level $unit_number $street<br />$suburb, $state, $postcode"; ?></p>
        </td>
        <td width="50%" valign="top">
          <p class=""><strong>Invoice</strong></p>
          <p><?php echo "$i_po_box $i_unit_level $i_unit_number $i_street<br />$i_suburb, $i_state,  $i_postcode"; ?></p>
        </td>
      </tr>
    </table>
  </fieldset>
</div>

<div class="clearfix">
  <fieldset class="pad-5 border-1 mgn-top-15 pad-10">
    <legend class="pad-l-10 pad-r-10"><strong>Project Totals</strong></legend>
    <table width="100%" class="c_edit">
      <tr>
        <td width="50%">
        <div class="block clearfix full">
            <p class=""><span class="">Quotes Total :</span> <strong class="">$<?php echo number_format($final_total_quoted,2); ?></strong></p>
            <p class=""><span class=""><?php echo $admin_gst_rate; ?>% GST :</span> <strong class="">$<?php echo number_format($final_total_quoted*($admin_gst_rate/100),2); ?></strong></p>
            <p class="">Total (inc GST) : <strong class="">$<?php echo number_format($final_total_quoted+($final_total_quoted*($admin_gst_rate/100)),2); ?></strong></p>
          </div>
        </td>
        <td width="50%">
          <p class="">Variations Total : <strong class="">$<?php echo number_format($variation_total,2); ?></strong></p>
          <p class=""><?php echo $admin_gst_rate; ?>% GST : <strong class="">$<?php echo number_format($variation_total*($admin_gst_rate/100),2); ?></strong></p>
          <p class=""><span class="">Total (inc GST) :</span> <strong class="">$<?php echo number_format($variation_total+($variation_total*($admin_gst_rate/100)),2); ?></strong></p>   
        </td>
      </tr>
    </table>
  </fieldset> 
</div>


  <div style="float:left !important; width:50% !important;">
    <div class="pad-r-5">
      <fieldset class="pad-10 border-1 mgn-top-10">
        <legend class="pad-l-10 pad-r-10"><strong>Details</strong></legend>
        <div class="clearfix">
          <p class="clearfix">
            <span class="pull-left text-left">Representative :</span>         <strong class="pull-right text-right"><?php echo $pm_user_first_name.' '.$pm_user_last_name; ?></strong><br />
            <span class="pull-left text-left">Job Date : </span>              <strong class="pull-right text-right"><?php echo $job_date; ?></strong><br />
            <span class="pull-left text-left">Start Date : </span>            <strong class="pull-right text-right"><?php echo $date_site_commencement; ?></strong><br />
            <span class="pull-left text-left">Expected Finish Date : </span>  <strong class="pull-right text-right"><?php echo $date_site_finish; ?></strong><br />
            <span class="pull-left text-left">PO Number :</span>              <strong class="pull-right text-right"><?php echo $client_po; ?></strong>
          </p>
          <p>&nbsp;</p>
        </div>
      </fieldset>
    </div>
 </div>




 <div style="float:right !important; width:50% !important;">
  <div class="pad-l-10">
    <fieldset class="pad-10 border-1 mgn-top-10">
      <legend class="pad-l-10 pad-r-10"><strong>Invoices</strong></legend>
      <div class="clearfix invoices_list_item">
        <?php $this->projects->list_invoiced_items($project_id,$final_total_quoted,$variation_total); ?>
      </div>
    </fieldset>
  </div>
</div> 






</div>
<link href="<?php echo site_url(); ?>css/bootstrap.min.css" rel="stylesheet" type="text/css"> 
<link href="<?php echo site_url(); ?>css/main.css" rel="stylesheet" type="text/css">
</body>
</html>