<?php use App\Modules\Invoice\Controllers\Invoice; ?>
<?php $this->invoice = new Invoice(); ?>

<?php use App\Modules\Invoice\Models\Invoice_m; ?>
<?php $this->invoice_m = new Invoice_m(); ?>

<?php $this->session = \Config\Services::session(); ?>

<?php
/*
  $raw_date = explode('/',$date_site_finish);
  $raw_date_site_finish = $raw_date['1'].'/'.$raw_date['0'].'/'.$raw_date['2'];
  $date_end = strtotime($raw_date_site_finish);
  $date = strtotime("+14 day", $date_end);
  $invoice_date_final = date('d/m/Y',$date); 
*/

  $invoice_date_final = $date_site_finish;
  $has_invoice = $this->invoice->if_has_invoice($project_id);
  $if_been_invoiced = $this->invoice->if_invoiced($project_id);
  $show_job_book_details = $this->invoice->show_job_book($project_id);

  if($has_invoice > 0){
    $this->invoice->check_invoice_progress($project_id,$final_total_quoted);
  }

  $inv_data = $this->invoice_m->fetch_invoice_id_last($project_id);
  $getResultArray = $inv_data->getResultArray();
  $inv_val_arr = array_shift($getResultArray);

?>

<?php if($job_date != ''): ?>
  <style type="text/css"> input.progress_date,input.final_payment,input.progress-percent{pointer-events:none;} </style>
<?php endif; ?>

<?php if($job_date == ''): ?> 
  <style type="text/css">
  .progress_invoice_button{display: none; visibility: hidden;}

    <?php if($this->session->get('user_role_id') == 6 || $this->session->get('user_role_id') == 5 || $this->session->get('invoice') < 2): ?>
      .progress_date,.final_payment,.progress-percent{pointer-events:none;}
    <?php endif; ?>
  </style>
<?php else: ?>
  <style type="text/css">
    <?php if($this->session->get('user_role_id') == 6 ||$this->session->get('user_role_id') == 5 || $this->session->get('invoice') < 2): ?>
      .progress_date,.final_payment,.progress-percent{pointer-events:none;}
    <?php endif; ?>
    .update_progress_values{display: none; visibility: hidden;}
  </style>
<?php endif; ?>

<?php if($this->session->get('is_admin') != 1): ?>
  <style type="text/css">.remove_link{display: none; visibility: hidden;}</style>
<?php endif; ?>

<?php if($this->session->get('invoice') != 2): ?>
  <style type="text/css">.progress_invoice_group,.progress_invoice{display: none; visibility: hidden;}</style>
<?php endif; ?>

<?php if($job_date != ''): ?>
  <style type="text/css">.save_progress_values,.update_progress_values_b{display: none; visibility: hidden;}</style>
<?php endif; ?>

<?php if($is_paid != 0): ?>
  <style type="text/css">.remove_recent_payment_b, .remove_recent_payment_a{display: none; visibility: hidden;}</style>
<?php endif; ?>

<?php if($final_total_quoted <= 0): ?>
  <style type="text/css">.progress_invoice{display: none; visibility: hidden;}</style>
<?php endif; ?>

<?php
  if($variation_total != 0 && $has_invoice > 0 && $job_date != '' && $this->invoice->if_has_vr($project_id) == 0){
    $this->invoice->set_invoice_vr($project_id,$date_site_finish);
  }


  if($variation_total == 0  /*&& $has_invoice > 0 && $job_date != ''*/ && $this->invoice->if_has_vr($project_id) != 0){
    //$this->invoice->set_invoice_vr($project_id,$date_site_finish);
    echo "delete vr inv";
    $this->invoice_m->un_invoice_vr($project_id);
  }


  //    


?>
<div class="row pad-10">
  <div class="col-xs-12">

    <div class="box-head pad-bottom-10 clearfix">
      <div class="pull-right m-top-10 m-left-10">


      <?php if($if_been_invoiced == 0): ?>
        <?php  if($has_invoice > 0): ?>
          <button class="btn btn-warning update_progress_values"><i class="fa fa-floppy-o"></i> Update Progress Values</button>
        <?php else: ?>
          <button class="btn btn-success save_progress_values" ><i class="fa fa-floppy-o"></i> Save Progress Values</button>


<?php if($job_date != ''): ?>

          <?php if($is_paid == 0): ?>
            <button class="btn btn-warning update_progress_values" style="display:none;"><i class="fa fa-floppy-o"></i> Update Progress Values</button>
          <?php endif; ?>
<?php endif; ?>

        <?php endif; ?>

        <?php else: ?>
          <?php if($is_paid == 0): ?>
            <button class="btn btn-warning update_progress_values_b" ><i class="fa fa-floppy-o"></i> Update Progress Values</button>
         <?php endif; ?>


      <?php endif; ?>


      </div>

      <div class="pull-right m-right-10">
        <h4 class="m-top-20">Project Total: (ex-gst) <?php echo number_format($final_total_quoted,2); ?> &nbsp; &nbsp;
         Total Paid: $<?php echo number_format($this->invoice->get_total_amount_paid_project($project_id),2); ?></h4>
      </div>

      <label>Invoice</label>
 
          <span class="fa fa-film pointer play_invoice_vids open_help_vids_mpd" data-toggle="modal" data-target="#help_video_group"> </span>
      <p>This is where the invoicing of the project happens. </p>
    </div>

    <input type="hidden" class="project_total_raw" value="<?php echo $final_total_quoted; ?>">
    <input type="hidden" class="project_number" value="<?php echo $project_id; ?>">
    <input type="hidden" class="num_progress" value="<?php  echo ($has_invoice > 0 ? $has_invoice : '1' ); ?>">
    <input type="hidden" class="progress_invoice_id" value="">
    <input type="hidden" class="date_set_invoice_data" value="<?php echo date("d/m/Y"); ?>">
    
    <input type="hidden" class="variation" value="<?php echo $variation_total; ?>">

    <table class="table table-striped table-hover invoice-table">
      <thead>
        <tr>
          <th width="20%">Progress No.</th>
          <th width="15%">Percent</th>
          <th width="15%">Date</th>
          <th width="15%">Amount</th>
          <th width="20%">Action</th>
          <th width="15%">Outstanding</th>
        </tr>
      </thead>
      <tbody class="progress-body progress-body-list">


        <?php if($has_invoice>0): ?>
          <?php $this->invoice->list_project_invoice($project_id); ?>

        <?php else: ?>
          <tr>
            <td scope="row" class="t-head" id="progress-1">
              <div><input type="text" class="form-control final_payment" value="Final Payment" placeholder="Final Payment"></div>
            </td>
            <td>
              <div class="input-group">
                <div class="input-group-addon">%</div>
                <input type="text" class="form-control progress-percent" onclick="getHighlight('progress-0-percent')" onchange="final_progress(this)" value="100" placeholder="Percent" id="progress-0-percent" name="progress-1-percent"/>
              </div>
            </td>
            <td><div><input type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" class="form-control date_daily text-left progress_date" id="progress-0-date" name="progress-0-date" value="<?php echo $invoice_date_final; ?>"></div></td>
            <td><strong><div class="m-top-5">$<span class="total_cost_progress"><?php echo number_format($final_total_quoted,2); ?></span> ex-gst</div></strong></td>
            <td></td>
            <td><strong><div class="m-top-5"><span class="progress_outstanding"></span></div></strong></td>
          </tr>          
        <?php endif; ?>



        
      </tbody>      
      <tbody>
        <tr>
          <th scope="row" class="t-head text-right" colspan="3">
            <div class="m-top-5">Variation : </div>
          </th>
          <td><strong><div class="m-top-5">$<span class="total_cost_progress variation_total_cost"><?php echo number_format($variation_total,2); ?></span> ex-gst</div></strong></td>
          <td>
            <?php $variation_data = $this->invoice->fetch_vr($project_id); ?>

            <?php $outstanding = $this->invoice->get_current_balance($project_id,$variation_data['invoice_id'],$variation_total); ?>
            
            <?php if($this->invoice->if_has_vr($project_id) > 0 && $has_invoice > 0 && $job_date !='' && !$this->invoice->is_vr_invoiced($project_id) ): ?> 
              <?php //if($this->invoice->is_vr_invoiced($project_id) && $if_been_invoiced): ?>
                <div class="progress_vr_invoice_button"><button class="btn btn-primary  m-right-5 progress_invoice_variation" onclick="progress_invoice_variation(this)" data-invoice-id="<?php echo $variation_data['invoice_id']; ?>" id="VR_" data-toggle="modal" data-target="#set_invoice_modal"><i class="fa fa-file-text-o"></i> Set Invoice</button></div>
              <?php // endif; ?>
            <?php else: ?>

              <?php if($this->invoice->is_vr_invoiced($project_id) && $job_date != '' && $variation_data['is_paid'] == 0): ?>


                <div class="hide hidden progress-item" id="<?php echo $variation_data['invoice_id']; ?>"><?php echo $variation_data['invoice_id']; ?></div>
                
                <div class="vr_bttn_group">
                  <div class="btn-group pull-left m-right-10 progress_invoice_group">
                    <button type="button" disabled="disabled" class="btn btn-primary progress_invoice"  style="display:block !important; visibility: visible !important;"><i class="fa fa-file-text-o"></i> Invoiced</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu" style="display: none;">
                      <li><a href="#" class="progress_invoice_resend"><i class="fa fa-files-o"></i> View Invoice</a></li>
                      <li class="remove_link"><a href="#" id="<?php echo $project_id; ?>" class="remove_vr"><i class="fa fa-exclamation-triangle"></i> Remove Invoice</a></li>
                    </ul>
                  </div>
                  <button class="btn btn-danger vr_paid" id="<?php echo $project_id; ?>_<?php echo $variation_data['invoice_id']; ?>" data-toggle="modal" data-target="#payment_modal" data-backdrop="static"><i class="fa fa-usd"></i> Payment</button>
                </div>
              <?php else: ?>
                    <?php if($job_date != '' && $this->invoice->is_vr_invoiced($project_id)): ?>
                      <button class="btn btn-success vr_paid" id="<?php echo $project_id; ?>_<?php echo $variation_data['invoice_id']; ?>" data-toggle="modal" data-target="#payment_history_modal" data-backdrop="static"><i class="fa fa-usd"></i> Paid</button>
                    <?php endif; ?>
              <?php endif; ?>

            <?php endif; ?>

            <?php
              if( $this->invoice->if_invoiced_all($project_id) ){
                $this->invoice->set_project_as_fully_invoiced($project_id); 
              }
            ?>

          </td>
          <td><strong>$<span class="vr_outstanding"><?php echo number_format($outstanding,2); ?></span></strong></td>
        </tr>
        <tr>
          <td scope="row" class="t-head"></td>
          <td></td>
          <th class="text-right pad-10"><div class="m-top-5">Grand Total :</div></th>
          <?php $project_grand_total_in = $final_total_quoted+$variation_total; ?>

          <?php $project_grand_total_in = ($project_id == 38580 ? $project_grand_total_in+6391.83 : $project_grand_total_in);  ?>
          <td colspan="2"><strong><div class="m-top-5">$<span class="total_cost_progress"><?php echo number_format($project_grand_total_in,2); ?></span> ex-gst &nbsp; &nbsp; $<span class="total_cost_progress"><?php echo number_format($project_grand_total_in+($project_grand_total_in*($admin_gst_rate/100)),2); ?></span> inc-gst</div></strong></td>
          <td>

          <?php //if($this->invoice->if_invoiced_all($project_id)){ $this->invoice->set_project_as_fully_invoiced($project_id); } ?>

          <?php if($if_been_invoiced > 0 && $has_invoice > 0): ?>
            <?php if($this->invoice->is_all_paid($project_id) && $this->invoice->if_invoiced_all($project_id)): ?>
              <?php if($is_paid == 0): ?>
                <button id="<?php echo $project_id; ?>" class="btn btn-danger set_project_as_paid"><i class="fa fa-usd"></i> Set this Project as Fully Paid</button>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>

          </td>
         
        </tr>        
      </tbody>
    </table>

  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="set_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"  style="padding: 10px 10px 5px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Set Job Book Notes</h4>
      </div>
      <div class="modal-body clearfix pad-10">

        <form method="post" action="<?php echo site_url(); ?>invoice/set_invoice_progress">


          <div class="input-group m-bottom-10 tooltip-test clearfix" data-original-title="Optional: The default recipients are Project Manager, You and Administrator. Press Semi-Colon, Space or Comma to set.">
            <span class="input-group-addon" id=""><i class="fa fa-user"></i> Optional</span>
            <div class="form-control clearfix" style="height: auto;">
              <div class="email-container pull-left clearfix">
                <input type="email" placeholder="CC Emails" class="pull-left" id="cc_emails" name="cc_emails" style="border:none;width: 300px;">
              </div>
            </div>
          </div>

          <div class="hidden job_book_notes">
            <?php echo nl2br($show_job_book_details['notes']); ?>
          </div>

          <input type="hidden" name="email_list" class="email-list">
          <input type="hidden" name="raw_invoice_notes" class="raw_invoice_notes">
          <input type="hidden" name="job_book_details_id" class="job_book_details_id" value="<?php echo $show_job_book_details['notes_id']; ?>">
          <input type="hidden" name="invoice_item_amount" class="invoice_item_amount">
          <input type="hidden" name="invoice_percent_value" class="invoice_percent_value">
          <input type="hidden" name="project_number" class="project_number" value="<?php echo $project_id; ?>">
          <input type="hidden" name="progress_invoice_id" class="progress_invoice_id" value="">
          <input type="hidden" name="project_total_raw" class="project_total_raw" value="<?php echo $final_total_quoted; ?>">
          <input type="hidden" name="date_set_invoice_data" class="date_set_invoice_data" value="<?php echo date("d/m/Y"); ?>">
          <input type="hidden" name="invoice_id_db" class="invoice_id_db" value="<?php echo $inv_val_arr['invoice_id'] ?? 0; ?>">
          <textarea class="hide" name="pdf_content" id="content"></textarea>

          <div class="m-bottom-10">
            <textarea class="form-control" id="invoice_notes" name="invoice_notes" placeholder="Job Book Notes"  rows="5"></textarea>
          </div>
          <div class="pull-right" style="width:100%;">
            <button type="button" class="btn btn-default pull-left pdf_editor_bttn" data-dismiss="modal" data-toggle="modal" data-target="#pdf_editor">PDF Editor</button>
            <div class="pull-left hide"  style="padding: 5px;    margin: 2px 6px;">
              <strong>Max Characters (350)</strong> <strong><span class="chars_left_nts">0</span></strong> &nbsp; 
              <strong>Max Lines (15)</strong> <strong><span class="lines_left_nts">0</span></strong>
            </div>
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary set_invoice_modal_submit pull-right m-right-10">Submit</button>

            <input type="submit" class="submit_invoice_set hide" name="submit_invoice_set">
          </div>

        </form>

      </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="payment_history_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Payments</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" name="po_number_item" id="po_number_item" class="po_number_item">
            <input type="hidden" name="po_actual_balance" id="po_actual_balance" class="po_actual_balance">
            <input type="hidden" name="invoice_id_progress" id="invoice_id_progress" class="invoice_id_progress">
            <input type="hidden" name="progress_id" id="progress_id" class="progress_id">
            <input type="hidden" name="invoice_outstanding" id="invoice_outstanding" class="invoice_outstanding">

            

            <div class="col-sm-12 m-top-15">
              <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Reference Number</th>
                </tr>
              </thead>
              <tbody class="payment_history history_b">

              </tbody>
            </table>
              <button id="<?php echo $project_id; ?>" class="btn btn-danger remove_recent_payment_b">Remove Recent Transaction</button>
            </div>
 
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default po_cancel_values" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




<!-- Modal -->
<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Payments</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">

            <div class="col-sm-12 border-bottom">

              <div class="clearfix col-sm-6">
                <p>Total Ex-GST: $<strong class="po_total_mod">00.00</strong></p>
              </div>
              <div class="clearfix col-sm-6">
                <p>Total Inc-GST: $<strong class="po_total_mod_inc_gst">00.00</strong></p>
              </div>

              <div class="clearfix col-sm-6">
                <p>Description: <strong class="po_desc_mod">Progress</strong></p>
              </div>
              <div class="clearfix col-sm-6">
                <p>Outstanding Ex-GST: <strong class="po_balance_mod">$0.00</strong></p>
              </div>          
            </div>

            <div class="po_error"></div>


            <div class="col-sm-6">
              <div class="clearfix m-top-15">
                <label for="po_date_value" class="col-sm-3 control-label text-left m-top-10" style="font-weight: normal;">Date*</label>
                <div class="col-sm-9">
                  <div class="input-group">
                    <span class="input-group-addon" id=""><i class="fa fa-calendar"></i></span>
                    <input type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" class="form-control datepicker" id="po_date_value" tabindex="1" name="po_date_value" value="<?php echo date("d/m/Y"); ?>" >
                  </div>                
                  
                </div>
              </div>
            </div>

            <input type="hidden" name="po_number_item" id="po_number_item" class="po_number_item">
            <input type="hidden" name="po_actual_balance" id="po_actual_balance" class="po_actual_balance">
            <input type="hidden" name="invoice_id_progress" id="invoice_id_progress" class="invoice_id_progress">
            <input type="hidden" name="progress_id" id="progress_id" class="progress_id">
            <input type="hidden" name="invoice_outstanding" id="invoice_outstanding" class="invoice_outstanding">

            <div class="col-sm-6">
              <div class="clearfix m-top-15">
                <label for="po_amount_value" class="col-sm-3 control-label text-left m-top-10" style="font-weight: normal;">Amount*</label>
                <div class="col-sm-9">
                  <div class="input-group m-bottom-10">
                    <span class="input-group-addon" id="">$</span>
                    <input type="text" placeholder="Amount" class="form-control   tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Commas are not allowed."  id="progress_payment_amount_value" name="progress_payment_amount_value" value="" tabindex="2">
                    <span class="input-group-addon" id="">ex-gst</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="clearfix">
                <label for="invoice_payment_reference_no" class="col-sm-6 control-label text-left m-top-10" style="font-weight: normal;">Reference Name*</label>
                <div class="col-sm-6">
                  <input type="text" placeholder="Reference Name" class="form-control" id="invoice_payment_reference_no" name="invoice_payment_reference_no" value="" tabindex="3">
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="clearfix">
                <label for="po_amount_value" class="col-sm-3 control-label text-left m-top-10" style="font-weight: normal;"></label>
                <div class="col-sm-9">
                  <div class="input-group m-bottom-10">
                    <span class="input-group-addon" id="">$</span>
                    <input type="text" placeholder="Amount" class="form-control   tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="Commas are not allowed."  id="progress_payment_amount_value_inc_gst" name="progress_payment_amount_value_inc_gst" value="" tabindex="2">
                    <span class="input-group-addon" id="">inc-gst</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="clearfix  m-top-10">
                <label for="po_notes_value" class="col-sm-1 control-label text-left m-top-10" style="font-weight: normal;">Notes</label>
                <div class="col-sm-11">
                  <div class="input-group m-bottom-10">
                    <input type="text" placeholder="Notes" class="form-control" id="po_notes_value" name="po_notes_value" value="" tabindex="4">
                    <span class="input-group-addon" id=""><i class="fa fa-exclamation-triangle"></i> Set As Paid <input type="checkbox" name="is_paid_check" id="is_paid_check"></span>
                  </div>
                </div>


              </div>
            </div>

            <div class="col-sm-12 m-top-15">
              <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount Ext-GST</th>
                  <th>Reference Name</th>
                </tr>
              </thead>
              <tbody class="payment_history payment_history_a">

              </tbody>
            </table>
              <button id="<?php echo $project_id; ?>" class="btn btn-danger remove_recent_payment_a">Remove Recent Transaction</button>
           
<?php if($this->session->get('is_admin') == 1): ?>
              <button id="" class="pull-right btn btn-warning zero_payment">Zero Payment</button>
            <?php endif; ?>
            </div>
 
          </div>
        </div>
      </div>



<script type="text/javascript">
  $('button.zero_payment').click(function(){
     $('input#progress_payment_amount_value').val('0.00');
     $('input#progress_payment_amount_value_inc_gst').val('0.00');
     $('#is_paid_check').prop('checked', true);

 
  });
</script>



<?php if($job_date == ''): ?>
  <script type="text/javascript"> $('button.progress_paid').hide(); </script>
<?php endif; ?>

       <div class="modal-footer">
        <button type="button" class="btn btn-default po_cancel_values" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success payment_set_values"><i class="fa fa-floppy-o"></i> Save Payment</button>
      </div>
    </div>
  </div>
</div>



<!-- MODAL -->
<div class="modal fade" id="pdf_editor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-pdf-editor">
    <div class="modal-content">
       
      <div class="modal-body clearfix pad-10">


        <iframe src="<?php echo site_url(); ?>invoice/job_book/<?php echo $project_id; ?>" style="" width="99.6%" height="90%" frameborder="0" class="frame_container"></iframe>
  
  

      </div>
    </div>
  </div>
</div>