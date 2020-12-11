<!doctype html>
<html lang="en">
  <head>
    <title><?php echo $title;?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo base_url();?>asset/bootstrap.min.css">            
    <link rel="stylesheet" href="<?php echo base_url();?>asset/datatable/dataTables.bootstrap.min.css" /> 
    <link rel="stylesheet" href="<?php echo base_url();?>asset/summernote/summernote.css" />
    <noscript>
    <style>html{display:none;}</style>
    Sorry, your browser does not support JavaScript!
    </noscript>

  </head>
    <body>

       <div class="container">
        <div class="page-header"><strong><?php echo $judul;?></strong></div>
        <div class="card card-body">
          <?php echo $editor;?>
          </div>
        </div>
         
      <!-- Javascripts-->
        <script src="<?php echo base_url();?>asset/jquery.min.js"></script>
        <script src="<?php echo base_url();?>asset/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>asset/datatable/jquery.dataTables.min.js"></script>  
        <script src="<?php echo base_url();?>asset/datatable/dataTables.bootstrap.min.js"></script>          
        <script src="<?php echo base_url();?>asset/summernote/summernote.js"></script>     

  </body>
</html>