<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.css'; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/toastr.min.css'; ?>">
     
	
</head>
<body>
	<div class="navbar navbar-dark bg-dark">
    <div class="container">
    <a href="#" class="navbar-brand">Login</a>
    </div>
    </div>
<div class="panel panel-default">
    <div  class="panel-heading"> Login</div>
    
    <?php 
    
    if($this->session->flashdata('message')){


    echo'

      <div class="alert alert-success">
          '.$this->session->flashdata("message").'
      </div>
          ';
       } 
       ?>
<form name="login" id="login" method="post" action="<?php echo base_url().'login/validation';?>" >
  
    <div class="container">
     
    <label ><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="user_email" class="form-control" value="<?php echo set_value('user_email'); ?>">
    <span class="text-danger"><?php echo  form_error('user_email');?></span> 
    <label ><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="user_password" class="form-control" value="<?php echo set_value('user_password'); ?>">
    <span class="text-danger"><?php echo  form_error('user_password');?></span> 

    <button type="submit"   class="btn btn-primary " onclick="success_toast()">Submit</button>
    <a href="<?php echo base_url().'index.php/Register/index'; ?>" class="btn btn-primary  "onclick="warning_toast()">Cancel </a>

    
    </div>


</form>
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-3.5.1.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/toastr.min.js'; ?>"></script>
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