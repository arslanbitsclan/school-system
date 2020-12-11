<?php
defined ('BASEPATH') OR exit('No direct script access allowed ');

/**
 * 
 */
class Register  extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('encryption');
	    $this->load->model('register_model');
	}

	function index(){

    $this->load->view('register');


	}

	function validation(){

    $this->form_validation->set_rules('user_name','Name','required|trim');
	$this->form_validation->set_rules('user_email','Email Address','required|trim|valid_email|is_unique[codeigniter_register.email]');
	$this->form_validation->set_rules('user_password','Password','required|trim');

	if ($this->form_validation->run()) {
       $verification_key = md5(rand());
       $encrypted_password =  $this->encrypt->encode($this->input->post('user_password'));

       $data = array(
       	'name'              =>   $this->input->post('user_name'),
       	'email'             =>   $this->input->post('user_email'),
       	'password'          =>   $encrypted_password,
        'verification_key'  =>   $verification_key 


        );
       $id = $this->Register_model->insert($data);
       if($id>0)
       {

          $subject ="Please verify email for login";
          $message = "
                    <p>Hi".$this->input->post('user_name')." </p>
                    <p> This is  email verificatio from codeigniter login register system . for complete registration process and login into system . first you want to verify your email by click on this <a href='".base_url()."register/'>Link</a>.</p>
                    <p> once you click on this link  your email will be verified  and you can login into sysytem. </p>
                    <p> Thanks! </p>";

                    $config = array(

                   'protocol'  =>   'smtp',
                   'smtp_host' =>   'smtpout.secureserver.net',
                   'smtp_port' =>    80,
                   'smtp_user' => 'arslanbitsclan@gmail.com',
                   'smtp_pass' => 'root',
                   'mailtype'  => 'html',
                   'charset'   => 'iso-8859-1',
                   'wordwrap'  =>TRUE


                    );
                    $this->load->library('email',$config);
                    $this->email->set_newline("\r\n");
                    $this->email->from('arslanbitsclan@gmail.com');
                    $this->email->to($this->input->post('user_email') );
                    $this->email->subject($subject);
                    $this->email->message($message);
                    if($this->email->send()){
                    $this->session->set_flashdata('message','Check in your email for email  verification mail' );
                     redirect('register');


                    }


       }
	
    } else {

       $this->index();

    }

	}

	function verify_email(){

    if($this->uri->segment(3)){

    	$verification_key = $this->uri->segment(3);
    	if($this->register_model->verify_email($verification_key))
    	{
        $data['message']='<h1 align="center">Your email has been successfully verified now you can login from <a href"'.base_url().'login">here</a></h1>';

    	}else
    	{

         $data['message']='<h1 align="center">Invalid link</h1>';
    	}

    	$this->load->view('email_verification',$data);
    }


	}
}