<?php


class Test_Api extends CI_Controller{


public function __construct()
	{
		parent::__construct();
		// $this->load->helper('file');
		// $this->load->library('zip');
		// $this->load->helper('email');
  //       $this->load->library('email');
		//$this->load->library('encryption');
	    //$this->load->model('register_model');
	}


 public	function index()
    {
     $this->load->view('api_view');

 	

 	}
 	public function action()
 	{

 		if($this->input->post('data_action'))
 		{

 			$data_action = $this->input->post('data_action');
 			if($data_action == "Insert")
 			{
              $api_url="http://localhost/ci6/Api/insert";

 			}
             $form_data = array(
              'first_name'   => $this->input->post('first_name'),
              'last_name'   => $this->input->post('last_name')
               );
             $client = curl_init($api_url);
             curl_setopt($client, CURLOPT_POST, true);
             curl_setopt($client, CURLOPT_POSTFIELDS, $form_data);
             curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
             $response = curl_exec($client);    // Use to execute 
 			 curl_close($client);    //  Use to close
 			 echo $response; 

 			if($data_action=="fetch_all")
 			{

 				$api_url="http://localhost/ci6/Api";
 				$client = curl_init($api_url);   //initialize CURL
 				curl_setopt($client, CURLOPT_RETURNTRANSFER, true);   //use to  send data 
 				$response = curl_exec($client);    // Use to execute 
 				curl_close($client);    //  Use to close 
 				$result=json_decode($response);
 				$output='';
 				if(count($result)>0)
 				{

                  foreach ($result as $row)
                   {
                     $output .= '
                     <tr> 
                     <td>'.$row->first_name.'</td>
                     <td>'.$row->last_name.'</td>
                     <td><button type ="button" name="edit" class="btn btn-warning btn-xs edit" id="'.$row->id.'">Edit</button></td>
                     <td><button type ="button" name="delete" class="btn btn-danger btn-xs delete" id="'.$row->id.'">Delete</button></td>
                     </tr>
                     ';
                  }
 				}
 				else
 				{
                 $output .='
                          <tr>
                          <td colspan="4" align="center">No Data Found</td>
                          </tr>
                           ';
 				}
 				echo $output;
 			}
 		}
 	}
 }
 ?>