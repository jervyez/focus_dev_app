<!DOCTYPE>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<!-- main site ip -->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo (isset($page_title) ? $page_title : 'Sojourn');  ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="<?php echo base_url(); ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">        
        <link href="<?php echo base_url(); ?>/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">

        <link href="<?php echo base_url(); ?>/css/segment.css" rel="stylesheet"/>

        <link href="<?php echo base_url(); ?>/css/select2.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>/css/main.css?ver=50" rel="stylesheet" type="text/css">

        <script src="<?php echo base_url(); ?>/js/moment.js" ></script>  

        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/jquery-1.12.0.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/dataTables.fixedColumns.min.js"></script>

        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/jquery-2.1.3.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/tableHeadFixer.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/jquery.tablednd.0.7.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>/js/tablefilter.js"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        
		<link href="<?php echo base_url(); ?>/css/font-awesome.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
 
 
        <script src="<?php echo base_url(); ?>/js/pathseg.js"></script>
 
		<link href="<?php echo base_url(); ?>/css/c3.css" rel="stylesheet" type="text/css">
		<script src="<?php echo base_url(); ?>/js/c3/d3.js" charset="utf-8"></script>
		<script src="<?php echo base_url(); ?>/js/c3/c3.js"></script>

        <link href="<?php echo base_url(); ?>/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo base_url(); ?>/js/bootstrap-datetimepicker.min.js" ></script>

	    <script src="<?php echo base_url(); ?>/js/segment.js"></script>

        <!-- added by: MC 08-03-17 -->
        <link href="<?php echo base_url(); ?>/css/bsPhotoGallery/jquery.bsPhotoGallery.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>/css/Jcrop/Jcrop.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>/js/tinymce/js/tinymce/tinymce.min.js" charset="utf-8"></script>

        <script>
            tinymce.init({ 
                selector:'#sendpdf_body',
                height: '300px',
                toolbar: false,
                menubar: false,
                plugins: false,
                forced_root_block : ''
            });
        </script>

        <script>
            tinymce.init({ 
                selector:'#declinedCommentsBox',
                height: '300px',
                toolbar: false,
                menubar: false,
                plugins: false,
                forced_root_block : ''
            });
        </script>

        <script>
            tinymce.init({ 
                selector:'#email_msg_onboarding_bank',
                height: '300px',
                toolbar: 'bold italic',
                menubar: false,
                plugins: false,
                forced_root_block : ''
            });
        </script>

        <script>
            tinymce.init({ 
                selector:'#email_msg_cqr',
                height: '300px',
                toolbar: 'bold italic backcolor code',
                menubar: false,
                plugins: "textcolor code",
                forced_root_block : '',
                content_style: 'body { font-family:verdana,sans-serif; font-size:12px }'
            });
        </script>

    </head>
    <body>

    <div id="main" class="main-content">
    	<!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->