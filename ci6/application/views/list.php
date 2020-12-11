<!DOCTYPE html>
<html>
<head>
	<title>CRUD</title>
	
	
</head>
     <body>
	   <div class="navbar navbar-dark bg-dark">
     <div class="container">
     <a href="#" class="navbar-brand">BlogList</a>
     </div>
     </div>
	 <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.css'; ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/toastr.min.css'; ?>">


	 <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-3.5.1.min.js'; ?>"></script>
	 <script type="text/javascript" src="<?php echo base_url().'assets/js/bootstrap.min.js'; ?>"></script>

	 
     <div class="row">
     <div class="col-md-6">
	
	 <a href="javascript:void(0);" onclick= "showModel();"class="btn btn-primary">Create </a>
   
     </div>
     </div>
  
     <div class="row">
	 <div class="col-md-12 pt-3">

     <table class="table table-info table-striped" id="table">
     <thead>
     <tr>
        <th class="code colr"><strong>ID</th>
        <th class="coursename colr"><strong> Name</th>
      	<th class="coursename colr"><strong> Email</th>
          <th class="coursename colr"><strong> Summer Note</th>
      	<th class="coursename colr"><strong width="60"> Edit</th>
      	<th class="coursename colr"><strong width="100"> Delete</th>
      
      
     </tr>
     </thead>
     <tbody  id="myTable">
     
               
           <?php if(!empty($users)) {foreach($users as $user) {?>
                 <tr>
                  <td><?php echo $user['id']?></td>
                  <td><?php echo $user['name']?></td>
                  <td><?php echo $user['email']?></td>
                   <!-- <td><?php echo $user['summernote']?></td> -->
                  <td>
                  <a href="<?php echo base_url().'index.php/User/edit/'.$user['id']?>" class="btn btn-primary">Edit </a></td>
                  <td>
                   <a href="<?php echo base_url().'index.php/User/delete/'.$user['id']?>" class="btn btn-danger">Delete </a>
                  </td>
                 </tr>
                  <?php  } } else {?>
           	      <tr>
           		    <td colspan="5">Record Not Found</td>
           	      </tr>
                  <?php }?>
                  
                   
                  </tr>
   
</tbody>
</table>


</div>
</div>
</div>




<!-- Modal -->
<div class="modal fade" id="addBlog" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ADD Blog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="response">
      	
      </div>
      <div class="modal-body">
       
      </div>
      
    </div>
  </div>
</div>
<div class="modal fade" id="ajaxResponseModel" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alert</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="ajaxResponse">
        
      </div>
      <div class="modal-body">
       
      </div>
      <div class="model-footer">
        <button type="button" class="bt btn-secondary" data-dismiss="model">Close</button>
      </div>
      
    </div>
  </div>
</div>
<script type="text/javascript">
    	function showModel(){
          
        $("#addBlog").modal("show");
        $.ajax({
           url:'<?php echo base_url().'index.php/User/showCreateForm';?>',
           type:'POST',
           data:{},
           dataType:'json',
           success:function(response){
           
           	$("#response").html(response["html"]);

           }


        })

    	}
        $("body").on("submit","#createBlog",function(e){

        e.preventDefault();
          

        $.ajax({
           url:'<?php echo base_url().'index.php/User/addForm';?>',
           type:'POST',
           data:$(this).serializeArray(),
           dataType:'json',
           success:function(response){
           
           // alert(response['status'] );
           	if(response['status'] == 0){


           		if(response["name"] != ""){
           			$(".nameError").html(response["name"]).addClass('invalid-feedback d-block');
           			$("#name").addClass('is-invalid');
           		}else{
                   $(".nameError").html("").removeClass('invalid-feedback d-block');
                $("#name").removeClass('is-invalid');


              }
           		if(response["email"] != ""){
           			$(".emailError").html(response["email"]).addClass('invalid-feedback d-block');
           			$("#email").addClass('is-invalid');
           		}else{
                   $(".emailError").html("").removeClass('invalid-feedback d-block');
                $("#email").removeClass('is-invalid');


              }
              if(response["summernote"] != ""){
                $(".summernoteError").html(response["summernote"]).addClass('invalid-feedback d-block');
                $("#summernote").addClass('is-invalid');
              }else{
                   $(".summernoteError").html("").removeClass('invalid-feedback d-block');
                $("#summernote").removeClass('is-invalid');


              }


           	}else{

              $(".nameError").html("").removeClass('invalid-feedback d-block');
                $("#name").removeClass('is-invalid');

                $(".emailError").html("").removeClass('invalid-feedback d-block');
                $("#email").removeClass('is-invalid');
                $(".summernoteError").html("").removeClass('invalid-feedback d-block');
                $("#summernote").removeClass('is-invalid');



            }

           }


        });

        });
    	
    </script>
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