<!DOCTYPE html>
<html>
<head>
	<title>CRUD</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.css'; ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/toastr.min.css'; ?>">

  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
   <script type="text/javascript" src="<?php echo base_url().'assets/js/toastr.min.js'; ?>"></script>
	
</head>
<body>

    <div class="navbar navbar-dark bg-dark">
        <div class="container">
        <a href="#" class="navbar-brand">Update  Blog</a>
    </div>
</div>
	

<form name="createBlog" method="post" action="<?php echo base_url().'index.php/User/edit/'.$user['id'];?>" >
  



  <div class="container">
  <div class="row">
  <div class="col-md-12">
  <div class="form-group">
  <label ><b>Blog Name</b></label>
  <input type="text" placeholder="Enter Blog Name" name="name" value="<?php echo set_value('name',$user['name']); ?>" >
    <?php echo  form_error('name');?> 
   
  <label ><b>Email </b></label>
  <input type="text" placeholder="Enter Your Email" name="email" value="<?php echo set_value('email',$user['email']); ?>">
    <?php echo  form_error('email');?> 

  <div id="summernote" name="summernote" value="<?php echo set_value('summernote'); ?>"> Summer Note</div>
    <?php echo  form_error('summernote');?>  


  
  <button type="submit"onclick="success_toast()" >Update</button>
  <a href="<?php echo base_url().'index.php/User/index'; ?>" class="btn-secondary btn "onclick="warning_toast()">Cancel </a>
    
 </div>
 </div>
 </div>
 </div>

  
</form>
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-3.5.1.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/toastr.min.js'; ?>"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
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