<?php use App\Modules\Company\Controllers\Company; ?>
<?php $this->company = new Company(); ?>

<?php use App\Modules\Bulletin_board\Controllers\Bulletin_board; ?>
<?php $this->bulletin_board = new Bulletin_board(); ?>

<?php use App\Modules\Projects\Controllers\Projects; ?>
<?php $this->projects = new Projects(); ?>


<?php if($this->session->get('projects') == 1): ?>
<style type="text/css">
 .del_stored_file{
    display: none;
    visibility: hidden;
  }
</style>
<?php endif; ?>
 

<!-- title bar -->
<div class="container-fluid head-control">
  <div class="container-fluid">
    <div class="row">

      <div class="col-md-6 col-sm-4 col-xs-12 pull-left">
        <header class="page-header">
          <h3><?php $datestring = "l, F d, Y"; $time = time(); //use time() for timestamp  ?>
            <?php echo $screen; ?> Screen<br><small><?php echo date($datestring, $time); #echo date("l, F d, Y"); ?></small>
          </h3>
        </header>
      </div>

      <div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
        <ul class="nav nav-tabs navbar-right">
          <li>
            <a href="<?php echo site_url(); ?>"><i class="fa fa-home"></i> Home</a>
          </li> 
          <li>
            <a href="<?php echo site_url(); ?>projects"><i class="fa fa-map-marker"></i> Projects</a>
          </li> 
        
          <li <?php if($screen=='Supplier'){ echo 'class="active"';} ?> >
            <a href="<?php echo site_url(); ?>projects/document_storage" class="btn-small"><em class="fa fa-cloud-upload"></em> Doc Storage </a>        
          </li>

        </ul>
      </div>

    </div>
  </div>
</div>
<!-- title bar -->

<div class="container-fluid">

<div class="test"></div>
  <!-- Example row of columns -->
  <div class="row">       
    <?php echo view('assets/sidebar'); ?>
    <div class="section col-sm-12 col-md-11 col-lg-11">
        <div class="container-fluid">
          <div class="row"> 

                        <div class="section col-sm-12 ">
      <div class="container-fluid">



        <div class="row">








<div class="row clearfix border-1" style="border-bottom: 1px solid #DDDDDD;    padding: 0;     border-left: 1px solid #ddd;   margin: 15px 5px 0px;">


                <div class="col-lg-4 col-md-12 hidden-md hidden-sm hidden-xs">
                  <div class="pad-left-5 projects clearfix">                    
                    <label class="project_name"><?php echo $screen; ?><p>Upload and Manage files here. You can also add new Document Type on the same screen.</p></label>
                  </div>
                </div>

                <div class="col-lg-8 col-md-12">

                  <div class="pad-top-10 pad-left-15    clearfix box-tabs"> 
                    
         

                  </div>

                </div>


              </div>


 

           <div class="col-md-9">
 

            <div class="left-section-box clearfix no-m">


            <div class="clearfix"></div>




<div class="box-tabs m-bottom-15">
                  <div class="tab-content">
                    

               

                    <div class="tab-pane fade in  clearfix active " style="padding:0 !important; border:none;" id="works">





              <div class="box-head clearfix pad-bottom-10">
                 

                <div class="pad-left-10   pull-left">                    
                    <label class="m-top-5">Uploaded Files</label>
                    <p>Please click on the client name to view uploaded files.</p>
                  </div>


 

                <div class="input-group m-top-15 m-left-10  pull-right" style="width:94px;"> <span class=" btn search_files_btn btn-success" style="color:#fff;"  data-toggle="modal" data-target="#doc_storage" ><i class="fa fa-cloud-upload"></i> Upload</span></div>


                <div class="input-group m-top-15 m-left-10  pull-right" style="width:300px;">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i> Upload Year </span> 

                  <input type="hidden" name="client_supply_settings_id" value="1"> 


                  <select id="year_prj_cdt" name="year_prj_cdt" tabindex="-1" class="form-control">


                    <?php $this_year = date('Y'); for ($i= date('Y'); $i > 2020; $i--): $l_year = $i-1;?> 
                      <option value="<?php echo "$i"; ?>" ><?php echo "$i-$l_year"; ?></option> 
                    <?php endfor; ?>

                    <?php // echo $this->projects->list_years_uploaded($this_year); ?>
 
                  </select>
                </div>

                <div class="input-group pull-right search-work-desc  m-right-5 m-top-15">
                  <div class="input-group">
                    <input type="text" class="form-control input-wd search_files_up" placeholder="Looking for...">  
                    <span class="input-group-addon btn search_files_btn btn-primary" style="color:#fff;"><i class="fa fa-search"></i> Search</span>                  
                  </div>
                </div>




              </div>
 






              <div class="clearfix box-area pad-5 m-left-5  pad-bottom-10">
                <div id="" class="pad-top-5">


                  <table id="table" class="table table-striped table-bordered   no-m" cellspacing="0" width="100%">
                   <thead> <tr>



                     <th width="80%" style="border: none;">Clients List</th>

                     <th width="20%" style="border: none; text-align:right;">
                      <div id="" class="btn btn-xs btn-success start_download_files" style="display:none;">Download Selected</div>
                    </th>


                  </tr> </thead> 
                </table>

                <div id="" class="pad-5" style="overflow-y: auto;max-height: 781px;border: 1px solid #ccc;border-top: 0;">
                  <?php $control_year = $this->request->uri->getSegment(3);  ?>

                  <?php if( isset($control_year) && $control_year != '' ): ?>
                    <script type="text/javascript"> $('select#year_prj_cdt').val('<?php echo $control_year; ?>'); </script>

                  <?php endif; ?>

                  <?php echo $this->projects->list_projects_by_client($control_year); ?>

                </div>

                <style type="text/css">

                .prj_files_group{
                  border-bottom: 1px solid #ddd;
                  background-color: #f7f7f7;
                }

                .uploaded_files_row{
                  border-bottom: 1px solid #ddd;

                }

 
.uploaded_files_row:hover em.del_stored_file{ display: block !important;}
.uploaded_files_row:hover{  background-color: #efefef; }
 

                </style>

                <script type="text/javascript">
                  $('.prj_files_head').click(function(){

                    $('.uploaded_files_row').hide();
                    var group_id = $(this).attr('id');

                    setTimeout(function(){
                     $('.'+group_id+'_files').fadeIn();
                   }, 100);

                  });


                  $('.set_doc_storage').click(function(){
                    var prj_set = $(this).attr('id');

                    var prj_details_arr = prj_set.split("_");

                    $('input#doc_proj_id').val(prj_details_arr[0]);

                  });

                  $('.set_doc_storage_c').click(function(){
                    var client_set = $(this).attr('id');

                    var client_data_arr = client_set.split("_");

                    $('select.client').val(client_data_arr[0]);

                  });




                  $('.del_stored_file').click(function(){
                    $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );


                    setTimeout(function(){
                      $('#loading_modal').modal('hide');
                    },1000);


                    var file_id = $(this).attr('id');

                    $.post("<?php echo site_url(); ?>projects/remove_uploaded_file",{file_id: file_id});
                    $(this).parent().remove();

                  });





                  /*pad-5 41849_files uploaded_files_row*/

                  $('.search_files_btn').click(function(){
                    $('.uploaded_files_row').hide();
                    var search_files_up = $('input.search_files_up').val().toLowerCase();


                    var listItems = $(".uploaded_files_row");
                    listItems.each(function(idx,obj) {
                      var text_link = $(obj).find( "a" ).text().toLowerCase();

                      if(text_link.includes(search_files_up) === true ) {
 




                       setTimeout(function(){
                        $(obj).show().focus();
                      }, 100);



                     }


                    });

                  });


                  $('select#year_prj_cdt').on("change", function(e) {
                    var data = $(this).val();

                    $('#loading_modal').modal({"backdrop": "static", "show" : true} );

                    setTimeout(function(){
                      window.location.replace('<?php echo site_url(); ?>projects/client_file_storage/'+data);
                    },1000);
                  });



                </script>


                  <table id="table" class="table table-striped table-bordered   no-m" cellspacing="0" width="100%" style="display:none">
                    <tfoot class="tfoot_work_totals_footer"> <tr> <th>Work Categories</th> <th class="text-right">Price</th> <th class="text-right">Quoted</th>  </tr> </tfoot> 
                  </table>

                </div>
              </div>
            </div>
          </div>
</div>
</div>
</div>



 
       <div class="col-md-3 m-top-15 ">

       <div id="" class="" style="display:none;">

       <form method="post" action="<?php echo site_url(); ?>projects/process_zip_download" class="process_zip_form">

       <textarea name="files_list" class="files_list_download form-control" ></textarea>
         

       </form>
 
</div>
 

      
<?php  if($this->session->get('is_admin') == 1 || $this->session->get('user_id') == 6  ): ?>
            <div class="">
              <div class="box m-bottom-15  no-m">
                <div class="box-head pad-5">
                  <label><i class="fa fa-indent" aria-hidden="true"></i> Add New Type</label>
                </div>
                <div id="" class="box-area clearfix pad-5">
                  <div class="clearfix m-5">

                    <form method="post" action="<?php echo site_url(); ?>projects/add_doc_type" class="m-bottom-0">
                      <div class="input-group">
                        <input type="text" class="form-control" value="" name="type_name" placeholder="Type Name">
                        <input type="hidden" name="doc_type" value="1">
                        <span class="input-group-btn">
                          <input type="submit" value="Save" class="btn btn-primary">
                        </span>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
<?php endif; ?>




            <div class="edit_doc_type_box" style="display:none;">
              <div class="box m-bottom-15  no-m">
                <div class="box-head pad-5">
                  <label><i class="fa fa-indent" aria-hidden="true"></i> Edit Type</label>
                </div>
                <div id="" class="box-area clearfix pad-5">
                  <div class="clearfix m-5">

                    <form method="post" action="<?php // echo site_url(); ?>projects/update_doc_type" class="m-bottom-0">
                      <div class="input-group">
                        <span class="input-group-btn">
                          <input type="submit" value="Update" class="btn btn-success">
                        </span>
                        <input type="text" class="form-control type_name" value="" name="type_name" placeholder="Type Name">
                        <input type="hidden" class="form-control type_id" value="" name="type_id">
                        <span class="input-group-btn">
                          <a href="#" id="" class="btn btn-danger delete_dt_link"><em class="fa fa-trash "></em> Delete</a>
                        </span>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>

              

              <div class="box  no-m">
                <div class="box-head pad-5">
                  <label><i class="fa fa-list-ul " aria-hidden="true"></i> Document Types</label>
                </div>

                <div id="" class="box-area clearfix pad-5">
                  <div class="clearfix m-5 m-bottom-10">
                    <div class=" " id="">
                      
                      <?php  echo $this->projects->list_doc_type_storage('list_view',1); ?>
                    </div>
                  </div>
                </div>
              </div>


              <script type="text/javascript">
                $('.edt_doctype').click(function(){

                  var type_id = $(this).attr('id');
                  var type_name = $(this).parent().text();



                  $('.edit_doc_type_box input.type_name').val(type_name);
                  $('.edit_doc_type_box input.type_id').val(type_id);

                  $('.edit_doc_type_box a.delete_dt_link').attr("href", '<?php echo site_url(); ?>projects/delete_doc_type/'+type_id);

 
                  $('.edit_doc_type_box').show();
              //    $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );

       

                });
              </script>

            </div> 

          </div>
        </div>
      </div>


            <div class="col-md-12">
              
              </div>
            </div>
            
          </div>        
        </div>  
      
    </div>
  </div>



</div>

 
 
 

   
<?php /*
<div id="doc_storage" class="modal fade" tabindex="-1" data-width="760" >
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><em class="fa fa-cloud-upload"></em> Document File Storage: <?php //echo $project_id; ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">

            


            <form method="post" action="<?php echo site_url(); ?>projects/process_upload_file_storage" id="upload_doc_file_prj" enctype="multipart/form-data">

              <div class="input-group m-bottom-10"><span id="" class="input-group-addon">Document Type*</span>
                <select name="doc_type_name" class="form-control">
                  <option disabled="disabled" selected="selected" value="0" class="hide hidden">Select Document Type*</option>
                  <?php echo $this->projects->list_doc_type_storage(); ?>
                </select>
              </div>

              <div class="input-group">
                <input type="file" multiple="multiple" name="doc_files[]" requaired="" autocomplete="off" id="archive_name_edt" class="form-control btn-default pad-5" style="box-shadow: none;">
                <input type="hidden" name="doc_proj_id" id="doc_proj_id" value="">
                <input type="hidden" name="is_prj_scrn" id="is_prj_scrn" value="0">

                <span class="input-group-addon btn btn-success" data-dismiss="modal" onclick=" $('form#upload_doc_file_prj').submit(); $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );"  style="box-shadow: none; color:#fff;"><i class="fa fa-upload"></i> Upload</span>
              </div>
            </form>
          </div>
        </div>
      </div>


      <div class="modal-footer">
        
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
      </div>


    </div>
  </div>
</div>


*/ ?>


<div id="doc_storage" class="modal fade" tabindex="-1" data-width="760" >
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><em class="fa fa-cloud-upload"></em> Document File Storage </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">

            


            <form method="post" action="<?php echo site_url(); ?>projects/process_upload_file_storage" id="upload_doc_file_prj" enctype="multipart/form-data">

              <div class="input-group m-bottom-10" >
                <span id="" class="input-group-addon"><strong>Document Type*</strong></span>
                <select name="doc_type_name" class="form-control doc_type_name" required="">
                  <option disabled="disabled" selected="selected" value="0" class="hide hidden">Select Document Type*</option>
                  <?php echo $this->projects->list_doc_type_storage('select',1); ?>
                </select>
              </div>


              <div class="input-group m-bottom-10" >
                <span id="" class="input-group-addon"><strong>Client*</strong></span>
                <select name="client" class="form-control client" required="">
                  <option disabled="disabled" selected="selected" value="0" class="hide hidden">Select Client Name*</option>
                  <?php echo $this->company->company_list('dropdown'); ?>
                </select>
              </div>




              <div class="clearfix file_area_box" style="position: relative; border: 1px solid #CCCCCC; border-radius: 5px; overflow: hidden; height: 100px;">        
                <input type="file" multiple="multiple" name="doc_files[]" required="" autocomplete="off" id="doc_files"  onchange="javascript:docUploadedList()" class=" form-control btn-default" style="width: 100%; position: absolute; z-index: 220; box-shadow: none;height: 100px;background: none !important;font-weight: bold;border: none !important;">
                <div id="doc_fileList" style=" border-top: 0;" class=""></div>
              </div>
              
              <div class=" m-bottom-10 m-top-10">
                <input type="hidden" name="doc_proj_id" id="doc_proj_id" value="">
                <input type="hidden" name="is_prj_scrn" id="is_prj_scrn" value="0">
                
                <span class="btn btn-success doc_upload_submit"  style=" color:#fff; box-shadow: none;"><i class="fa fa-upload"></i> Upload</span>
              </div>

            </form>


          </div>
        </div>
      </div>
    
    
    </div>
  </div>
</div>
<style type="text/css">


 
#doc_fileList{
  background-color: #add6ad;
  border-color: #3e8f3e;
  position: absolute;
  top: 35px;
  width: 100%;
  z-index: 180;
}


.file_link_download{
  cursor: pointer !important;
}

.file_link_download.active {
    background-color: #54ad54;
    color: #fff;
    padding: 2px 8px;
    font-weight: bold;
    border-radius: 6px;
    text-decoration: none;
}


</style>

<?php  //if($this->session->get('is_admin') == 1 || $this->session->get('user_id') == 6  ):?>

<script type="text/javascript">

$('input#doc_files').text("Your Text to Choose a File Here!");

$('input#doc_files').on('dragover', function(e) {
  $(this).css('border-color','#3e8f3e');
  $(this).css('color','#449a44');
});

  docUploadedList = function() {
    $('#doc_fileList ul').remove();


    var input = document.getElementById('doc_files');
    var output = document.getElementById('doc_fileList');
    var children = "";
    for (var i = 0; i < input.files.length; ++i) {
      children += '<li>' + input.files.item(i).name + '</li>';
    }
    $('#doc_fileList').append('<ul class="" style="    padding: 10px 30px;">'+children+'</ul>');
    var children = '';


    var list_hieght = $('#doc_fileList').outerHeight();

    var added_hight_inputBox = list_hieght+30;

    $('input#doc_files').css('height',added_hight_inputBox+'px');

    $('.file_area_box').css('height',added_hight_inputBox+'px');
  }

  $('.doc_upload_submit').click(function(){

    var doc_type = $('select.doc_type_name').val();

    if(doc_type > 0){

      $('#doc_storage').modal('hide');
      $('form#upload_doc_file_prj').submit();
      $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );

    }else{
      alert('Please select Document Type.')
    }

  });

  $('.doc_upload_submit').click(function(){

    var doc_type = $('select.doc_type_name').val();

    if(doc_type > 0){

      $('#doc_storage').modal('hide');
      $('form#upload_doc_file_prj').submit();
      $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );

    }else{
      alert('Please select Document Type.')
    }

  });





  $('.start_download_files').click(function(){
    $('#loading_modal').modal({'backdrop': 'static', 'show' : true} );
    setTimeout(function(){
      $('#loading_modal').modal('hide');
    },2000);

    $(this).hide();

  //  $('.toggle_download_links').show();
    $('input.input_checkbox_attach_file').show(); 
/*
    $('a.file_link_download').each(function(){
      $(this).attr("href", $(this).data("href"));
    });
*/
    $('.file_link_download').removeClass('active');
 


 $('form.process_zip_form').submit();
    $('.files_list_download').val('');

  });


  $('.file_link_download').click(function(){

    $('.start_download_files').show();

    var add_file = $(this).text();
    var file_list = $('.files_list_download').val();


    if ($(this).hasClass("active")) {
      $(this).removeClass('active');

      var new_files_set = file_list.replace(add_file, "");


    $('.files_list_download').val(new_files_set);

    }else{
      $(this).addClass('active');


    $('.files_list_download').val(file_list+','+add_file);

    }
  });




</script>


<?php $this->bulletin_board->list_latest_post(); ?>
<?php echo view('assets/logout-modal'); ?>