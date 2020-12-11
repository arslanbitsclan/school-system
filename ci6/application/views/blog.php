<!DOCTYPE html>
<html>
<head>
	<title>CRUD</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.css'; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/toastr.min.css'; ?>">









    <!-- include libraries(jQuery, bootstrap) -->
<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->


<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">


	
</head>
<body>
	<div class="navbar navbar-dark bg-dark">
    <div class="container">
    <a href="#" class="navbar-brand">Create  BLOG</a>
    </div>
    </div>
<form name="createBlog" id="createBlog" method="post" action="<?php echo base_url().'User/addForm';?>" >
  
    <div class="container">
    <label ><b>BLog Name</b></label>
    <input type="text" placeholder="Enter Blog Name" name="name" value="<?php echo set_value('name'); ?>" >
    <?php echo  form_error('name');?>


    <label ><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="email" value="<?php echo set_value('email'); ?>">
    <?php echo  form_error('email');?>



    <div id="summernote" name="summernote" value="<?php echo set_value('summernote'); ?>"></div>
    <?php echo  form_error('summernote');?>  
  
    <button type="submit"   class="btn btn-primary " onclick="success_toast()">Submit</button>
    <a href="<?php echo base_url().'index.php/User/index'; ?>" class="btn btn-primary  "
        onclick="warning_toast()">Cancel </a>

    
    </div>


</form>
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-3.5.1.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/toastr.min.js'; ?>"></script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
 
  <script>
    // $(document).ready(function() {
    //     $('#summernote').summernote();
    // });


      $('#summernote').summernote({
      height: 200,
      toolbar: [    
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],       
        ['insert',['picture']]
      ],
            //set callback image tuk upload ke serverside
            callbacks: {
                    onImageUpload: function(files) {
                        uploadFile(files[0]);
                    }
                }

    });

    function uploadFile(file) {
            data = new FormData();
            data.append("file", file);

            $.ajax({
                data: data,
                type: "POST",
                url: "<?php echo base_url();?>User/saveGambar",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {                                 
                 console.log(url);                                        
                 $('#summernote').summernote("insertImage", url);
                }
            });
        }
  </script>

<script >
    function success_toast(){

        toastr.success("Success message");
    }
      function warning_toast(){

        toastr.warning("warning message");
    }
</script>
</body>
</html>