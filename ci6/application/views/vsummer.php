<!doctype html>
<html lang="en">
  <head>
    <title><?php echo $title;?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
      <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.css'; ?>">  
      <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
      <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>            
      <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
        <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js" defer></script>



     
     
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/summernote.min.css" />
    <noscript>
    <style>html{display:none;}</style>
    Sorry, your browser does not support JavaScript!
    </noscript>

  </head>
    <body>
         <div class="container">
          <div class="row">    
            <div class="col-sm-6">
          <h3><?php echo $title;?></h3><hr/>
          <?php echo form_open('Summer/datasummernote', 'id="mydata" '); ?>
          <input type="hidden" name="id" value="<?php echo (rand(1,1000));?>">
        <div class="form-group">
          <label for="usr">Ttitle :</label>
          <input type="text" class="form-control" id="title" name="title">
        </div>
        <div class="form-group">  
          <textarea class="form-control" rows="5" cols="9" id="editordata" name="editordata"></textarea>
        </div>
         <div id="summernote" name="summernote" value="<?php echo set_value('summernote'); ?>"> Summer Note</div>
        <button type="submit" class="btn btn-primary">Save</button>
          </form>  
            </div>
            <div class="col-sm-6">
               <h3>Data</h3><hr/>
            <div class="table-responsive">                                 
             <table id="datanote" class="table table-bordered table-striped">  
               <thead>  
                    <tr>  
                         <th>id</th>  
                         <th>Title</th>
                         <th>View</th>
                    </tr>  
               </thead>  
                </table>  
              </div> 
            </div>
          </div>
          <br><br><br>
        </div>

  <!-- Javascripts-->
       
    <script type="text/javascript">
     
      //datatable      
      var dataTable = $('#datanote').DataTable({             
           "serverSide":true,  
           "order":[],  
           "ajax":{  
                url:"<?php echo base_url();?>summer/datanote",  
                type:"POST"  
           },  
           "columnDefs":[  
                {  
                     "targets":[0, 1],  
                     "orderable":true,  
                },  
           ],  
      });  
      
    //edtor summernote
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
                url: "<?php echo base_url();?>summer/saveGambar",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {                                 
                 console.log(url);                                        
                 $('#editordata').summernote("insertImage", url);
                }
            });
        }

    //input form 
    $('#mydata').submit(function(e){

    e.preventDefault();
     var fa = $(this);

      $.ajax({
        url: fa.attr('action'),
        type: 'post' ,
        data: fa.serialize(),
        dataType: 'json',
        success: function(response) {
          if(response.success == true) {

            $('.form-group').removeClass('is-invalid')
                            .removeClass('is-valid');
            $('.text-danger').remove();
            fa[0].reset();         
            location.reload();

          } else {
            $.each(response.messages,function(key, value){
              var element = $('#' + key);
              element.closest('div.form-group')
              .removeClass('is-invalid')
              .addClass(value.length > 0 ? 'is-invalid' : 'is-valid')
              .find('.text-danger')
              .remove();
              element.after(value);
            });
          }
        }
     });

    });
    </script>

  </body>
</html>