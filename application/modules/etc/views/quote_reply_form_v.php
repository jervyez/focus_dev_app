<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Focus Shopfit PTY LTD">
    <meta name="description" content="Contractor Quote <?php echo ($prj_quote_review == 1 ? 'Review' : 'Request'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotation Form</title>
    <link href="<?php echo base_url(); ?>quote_form/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      .container { max-width: 960px; }
      .border-top { border-top: 1px solid #e5e5e5; }
      .border-bottom { border-bottom: 1px solid #e5e5e5; }
      .border-top-gray { border-top-color: #adb5bd; }
      .box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
      .lh-condensed { line-height: 1.25; }
    </style>
  </head>




  <body class="bg-light">

    <div class="container">

      

      <div class="row">
        <div class="col-md-5 col-lg-4 text-center">
          <img src="https://focusshopfit.com.au/wp-content/uploads/2020/07/focus-logo-288x143.png" alt="logo" width="288" height="143" class="img-responsive">
        </div>
        <div class="col-md-7 col-lg-8 mt-4">
          <h3>Contractor Quote <?php echo ($prj_quote_review == 1 ? 'Review' : 'Request'); ?></h3>
          
          <?php if($prj_quote_review == 1):?>
            <p class="text-muted">You are being requested to review your previously submitted quote, please enter your best price and conditions for the job.</p>
          <?php else: ?>
            <p class="text-muted">You are being requested to provide a detailed quotation for the job below, please enter your best price and conditions for the job.</p>
          <?php endif; ?>

        </div>
      </div>
      <hr class="mb-3">



           


      <div class="row">

        <?php if($prj_quote_review == 1):?>
          <div class="col-12">

            <div class="alert alert-warning border-dark" role="alert">
              <button class="btn btn-warning border-dark collapsed float-left" type="button" style="margin: 5px 20px 0 0;" data-toggle="collapse" data-target="#detailsList">
                <strong class="">Toggle Files</strong>
              </button>
              <h5 class="my-0 text-dark">Previous quote at $<?php echo number_format($ex_gst,2);  ?> EX-GST.</h5>
              <span class=" text-dark">Please review your previously submitted quote by clicking the toggle files button to view your details.</span>
            </div>

            <div class="collapse" id="detailsList">
              <div class="list-group mb-4">

                <?php foreach($pdf_file_list as $data => $file): ?>
                  <a href="<?php echo base_url(); ?>docs/stored_docs/<?php echo $file; ?>" class="list-group-item list-group-item-action col-form-label-sm" target="_blank"><span class="badge badge-primary badge-pill">&nbsp;</span>&nbsp;&nbsp; <?php echo $file; ?></a>
                <?php endforeach; ?>

              </div>
            </div>

          </div>
        <?php endif; ?>


        <div class="col-md-4 order-md-2 mb-4">

          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Job Details</span>
          </h4>


          <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Project</h6>
                <span class="text-muted"><?php echo $project_name; ?></span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Job</h6>

                  <?php if(isset($joinery_work_name) && $joinery_work_name != ''): ?>
                    <?php if($joinery_work_name != 'Other'): ?>
                      <span class="text-muted"><?php echo $joinery_work_name; ?></span>
                    <?php endif; ?>
                  <?php else: ?>

                    <?php if($contractor_type == 2): ?>
                        <?php if($job_sub_cat != 'Other'): ?>
                          <span class="text-muted"><?php echo $job_sub_cat; ?></span>
                        <?php endif; ?>
                      <?php else: ?>
                        <?php if($supplier_cat_name != 'Other'): ?>
                          <span class="text-muted"><?php echo $supplier_cat_name; ?></span>
                        <?php endif; ?>
                      <?php endif; ?>  
                  <?php endif; ?>


                  <?php if( $joinery_work_name == 'Other' || $supplier_cat_name == 'Other' || $job_sub_cat == 'Other'  ):  ?>
                    <span class="text-muted"><?php echo $other_work_desc; ?></span>
                  <?php endif; ?>  

 



              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Site Address</h6>
               <span class="text-muted">
                 <?php $site_add = '';  

                  if($job_type == 'Shopping Center'){
                    $site_add .= $shop_tenancy_number.': '.$shop_name.', ';
                  }

                  if( isset($unit_level) && $unit_level != '' ){
                    $site_add .= '<br />Unit '.$unit_level.'/';
                  }

                  $site_add .= $unit_number.' '.$street.' '. ucwords(strtolower($suburb)).', '.$shortname.', '.$postcode;
                ?>

                <?php echo $site_add; ?>

               </span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0 text-danger">Tender Deadline</h6>
                <span class="text-danger">Date: <?php echo $work_reply_date; ?><br />Time: <?php echo $work_replyby_time; ?></span>
              </div>
            </li>


            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0 ">Other Considerations</h6>

                  <?php echo ($site_inspection_req == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Site Inspection Req</span>' : ''); ?>
                  <?php echo ($special_conditions == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Special Conditions</span>' : ''); ?>
                  <?php echo ($additional_visit_req == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Additional Visits Req</span>' : ''); ?>
                  <?php echo ($operate_during_install == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Operate During Install</span>' : ''); ?>
                  <?php echo ($week_work == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Week Work </span>' : ''); ?>
                  <?php echo ($weekend_work == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Weekend Work</span>' : ''); ?>
                  <?php echo ($after_hours_work == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; After Hours Work</span>' : ''); ?>
                  <?php echo ($new_premises == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; New Premises</span>' : ''); ?>
                  <?php echo ($free_access == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; Free Access</span>' : ''); ?>
                  <?php echo ($other == 1 ? ' <span class="" style="padding:3px 0 0; display: block;" >&bull; '.$otherdesc.'</span>' : ''); ?>

              </div>
            </li>


            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Estimator</h6>
               <span class="text-muted"><?php echo $user_full_name; ?><br />
               &#9993; <a href="#"><?php echo $general_email; ?></a></span>
              </div>
            </li>


            <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
              <h6 class="my-0">Download Files</h6>
              <div class="btn-group" role="group" aria-label="Default button group" style="margin-top: 10px;">
                <?php if($has_attachment==1): ?>
                  <a type="button" href="<?php echo base_url(); ?>project_attachments/proj_attachment?project_id=<?php echo $project_id; ?>" target="_blank" class="btn btn-primary">Job Attachments</a>
                <?php endif; ?>

                <?php // $company_file_name =  str_replace( array( "'",  '"', ',', '"' ,'%' ,  '&apos', ' ' , ';', '<', '>' ), '', $company_file_name);   ?>


                <?php $company_file_name =  str_replace( array( "'",  '"', ',', '"'  , '`' ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>' ), '', $company_file_name); ?>

                <?php //if(isset($cqr_file_pdf) && $cqr_file_pdf != ''  ): ?>
                  <a type="button" href="<?php echo base_url(); ?>docs/stored_docs/<?php echo $cqr_file_pdf; ?>" target="_blank" class="btn btn-info">CQR Form</a>
                <?php /*else: ?>
                   <a type="button" href="./docs/stored_docs/<?php echo $project_id.'_cqr_'.$works_id.'_'.$company_file_name.'_'.$works_contrator_id; ?>.pdf" target="_blank" class="btn btn-info">CQR Form</a>
                <?php endif; */?>

              </div>
              </div>
            </li>

          </ul>

       

        </div>
        <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Tender Form</h4>
          <form class="needs-validation" novalidate method="post" action="<?php echo base_url(); ?>submit_cqr_form" enctype="multipart/form-data">





            <div class="mb-3">
              <label for="username">Amount*</label>
              <div class="input-group">
                <div class="input-group-prepend">

                  <?php if($prj_quote_review == 1):?>
                    <span class="input-group-text">Previous quote at&nbsp; <strong class="">$<?php echo number_format($ex_gst,2);  ?></strong>&nbsp; EX-GST</span>
                  <?php else: ?>
                    <span class="input-group-text">EX-GST</span>
                  <?php endif; ?>

                </div>
                <input type="text" class="form-control" id="quote_amnt_exgst" name="quote_amnt_exgst" autocomplete="off" placeholder="Quoted Amount 0.00 EX-GST" required>
                <div class="invalid-feedback" style="width: 100%;">
                  Amount is required.
                </div>
              </div>
            </div> 

            <div class="mb-3">
              <label for="username">Attachment</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">File Upload</span>
                </div>
                <input type="file" multiple="multiple" name="contractor_attachment[]" autocomplete="off" id="contractor_attachment" class=" form-control btn-default" >
              </div>
            </div> 

            <div class="mb-3">
              <label for="address">Conditions or Exclusions</label>
              <span class="text-muted text_remaining" style="float:right"></span>
              <textarea class="form-control" id="conditions_exclusions" maxlength="400"  name="conditions_exclusions" autocomplete="off" style="margin-top: 0px; margin-bottom: 0px; height:335px;" placeholder="Details"></textarea>
              <div class="invalid-feedback">
                Please enter your Conditions or Exclusions.
              </div>
            </div>

            <hr class="mb-4">
            <input type="hidden" name="works_contrator_id" value="<?php echo $works_contrator_id; ?>">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <input type="hidden" name="work_id" value="<?php echo $works_id; ?>">
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
            <input type="hidden" name="contractor_id" value="<?php echo $company_id; ?>">
            <input type="hidden" name="pending_contractor_id" value="<?php echo $is_pending; ?>">
            <input type="hidden" name="focus_company_id" value="<?php echo $focus_company_id; ?>">
            <input type="hidden" name="estimator_full_name" value="<?php echo $user_full_name; ?>">
            <input type="hidden" name="estimator_email" value="<?php echo $general_email; ?>">
            <input type="hidden" name="project_name" value="<?php echo $project_name; ?>">
            <input type="hidden" name="pa_email" value="<?php echo $pa_email; ?>">
            <input type="hidden" name="is_quote_review" value="<?php echo $prj_quote_review; ?>">
            <input type="hidden" name="old_quoted_amnt" value="<?php echo $ex_gst; ?>">

            

            <button class="btn btn-success btn-lg btn-block" type="submit">Submit quotation</button>
          </form>
        </div>
      </div>

      <footer class="my-2 pt-2 text-muted text-center text-small">
        <p class="mb-1">&copy; 2021 Focus Shopfit | Retail &amp; Commercial Projects | Shop Fitouts | Perth, WA | Auburn, NSW<br />All rights reserved.</p>
        <ul class="list-inline">
          <li class="list-inline-item"><a href="https://focusshopfit.com.au/why-focus/privacy-policy">Privacy</a></li>
          <li class="list-inline-item"><a href="#">1300 373 373</a></li>
          <li class="list-inline-item"><a href="#">info@focusshopfit.com.au</a></li>
        </ul>



      </footer>
    </div>


    <script src="<?php echo base_url(); ?>quote_form/jquery-3.2.1.slim.min.js"></script>
    <script src="<?php echo base_url(); ?>quote_form/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>quote_form/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>quote_form/holder.min.js"></script>
    <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      (function() {
        'use strict';

        window.addEventListener('load', function() {
          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');

          // Loop over them and prevent submission
          var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();
    </script> 
    <script src="<?php echo base_url(); ?>js/jquery.maxlength.min.js"></script>
    <script type="text/javascript">

     $(document).ready(function(){


 
    
    $('#conditions_exclusions').maxlength({
        counterContainer: $(".text_remaining"),
        text: '%left Characters left.'
      });



$('input#quote_amnt_exgst').keyup(function(event) {

  // skip for arrow keys
  if(event.which >= 37 && event.which <= 40){
   event.preventDefault();
 }

 $(this).val(function(index, value) {
      value = value.replace(/,/g,''); // remove commas from existing input
      value = value.replace(/[^0-9.]/g, '');
      return numberWithCommas(value); // add commas back in
    });
});

function numberWithCommas(x) {
  var parts = x.toString().split(".");
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return parts.join(".");
}



     });



    


</script>

<style type="text/css">

	@media only screen and (max-width: 600px) { 
		p{
			font-size:0.8em;
		} 

		p.lead{
			font-size:0.9em;
		} 


	}


</style>
  </body>
</html>
