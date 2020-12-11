<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summer extends CI_Controller {

 function __construct()
 {
 parent::__construct();
 $this->load->model('mdata', '', TRUE);
 $this->load->helper(array('form', 'url', 'file'));
 }

    public function index()
  {
    $data['title'] = "WYSIWYG text editor summernote";    
    $this->load->view('vsummer',$data);   
  }


  public function saveGambar()
  {
         if(isset($_FILES["file"]["name"]))  
     {  
          $config['upload_path'] = './asset/img/';  
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
                echo base_url().'asset/img/'.$_FILES['file']['name'];                                     
          }  
     } 

  }

    public function datasummernote() {

    $data = array ('success' => false, 'messages' => array());

    $this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[5]|is_unique[tbnote.title]');          
    $this->form_validation->set_rules('editordata', 'Tex', 'required|min_length[10]');    
    $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');


    if($this->form_validation->run()) {     


        $data1 = array (
            'id' => $this->input->post('id'),
            'title' => $this->input->post('title'),
            'editor' => $this->input->post('editordata')            
          );
        $this->mdata->insert_all('tbnote',$data1);
        
      $data['success'] = true;

    } else {
      foreach ($_POST as $key => $value) {
        $data['messages'][$key] = form_error($key);
      }     

    }
    echo json_encode($data);

}


  function datanote() {             
         $fetch_data = $this->mdata->make_datatables1();  
         $data = array();  
         foreach($fetch_data as $row)  
         {  
              $sub_array = array();                  
              $sub_array[] = $row->id;  
              $sub_array[] = $row->title;                
              $sub_array[] = '<a href="'.base_url().'summer/news/'.$row->id.'" class="btn btn-info btn-md">post</a>'; 
              $data[] = $sub_array;  
         }  
         $output = array(  
              "draw"                    =>     intval($_POST["draw"]),  
              "recordsTotal"          =>      $this->mdata->get_all_data('tbnote'),  
              "recordsFiltered"     =>     $this->mdata->get_filtered_data1(),  
              "data"                    =>     $data  
         );  
         echo json_encode($output);  
    }


      public function news($data)
      {
        
        $cek = $this->mdata->check_all('tbnote',array('id'=>$data),1);
        if($cek)
        {
          foreach ($cek as $row)
          {
            $isi['judul'] = $row->title;
            $isi['editor'] = $row->editor;
          }

              $isi['title'] = "Data News";              
              $this->load->view('vnews',$isi); 
        }
        else
        {
          show_404();
        }

      }

}