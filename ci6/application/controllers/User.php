<?php

class User extends CI_controller{



     function createBlog(){

     	$this->load->view('blog');


     }



      function addForm(){
          
        $this->load->model('User_model');
	    $this->form_validation->set_rules('name','Name','required');
	    $this->form_validation->set_rules('email','Email','required');
	    $this->form_validation->set_rules('summernote','SummerNote');



        if ($this->form_validation->run() == true) {

            //Save Data in DB
            
            $formArray = array();
            $formArray['name']=$this->input->post('name');
            $formArray['email']=$this->input->post('email');
            $formArray['summernote']=$this->input->post('summernote');
            $formArray['created_at'] = date('y-m-d');
            $this->User_model->create($formArray);



      
        $response['status']=1;
        $response['message']="<div class=\"alert alert-success\">Record has been added Successfully.</div>";
	
        } else { 

        	$response['status']=0;
        	$response['name']=strip_tags(form_error('name'));
        	$response['email']=strip_tags(form_error('email'));
        	$response['summernote']=strip_tags(form_error('summernote'));

            }
            echo json_encode($response);
        }




  public function saveGambar()
  {
         if(isset($_FILES["file"]["name"]))  
     {  
          $config['upload_path'] = './assets/img/';  
          $config['allowed_types'] = 'jpg|jpeg|png|gif';  
          $this->load->library('upload', $config);  
          if(!$this->upload->do_upload('file'))  
          {  
               $this->upload->display_errors();  
               return FALSE;
          }  
          else  
          {  
               $data = $this->upload->data();                 
                echo base_url().'assets/img/'.$_FILES['file']['name'];                                     
          }  
     } 

  }

          function showCreateForm(){

            $html = $this->load->view('blog','',true);
            $response['html'] = $html;
            echo json_encode($response);

            }


      function index(){


     $this->load->model('User_model');
     $users=$this->User_model->all();
     $data = array();
     $data['users']= $users;
	 $users = $this->load->view('list',$data);


    }



     function edit($Id){


    $this->load->model('User_model');
    $user= $this->User_model->getUser($Id);

    $data = array();
    $data['user']= $user;



    $this->form_validation->set_rules('name','Name','required');
	$this->form_validation->set_rules('email','email');
    $this->form_validation->set_rules('summernote','Summernote');




	if($this->form_validation->run()== false){

    $this->load->view('edit',$data);

	}else {

	$formArray = array();
	$formArray['name']=$this->input->post('name');
    $formArray['email']=$this->input->post('email');
    $formArray['summernote']=$this->input->post('summernote');
    $this->User_model->updateUser($Id,$formArray);
    $this->session->set_flashdata('success','Record Update Successfully');
    redirect(base_url().'index.php/User/index');
	}
    }

    function delete($Id){
    
    $this->load->model('User_model');
    $user= $this->User_model->getUser($Id);

    if(empty($user)){

    $this->session->set_flashdata('failure','Record not found in db');
    redirect(base_url().'index.php/User/index');
    }

    $this->User_model->deleteUser($Id);
    $this->session->set_flashdata('success','Record deleted Successfully');
    redirect(base_url().'index.php/User/index');
    }

    }


 