<?php
defined ('BASEPATH') OR exit('No direct script access allowed ');

/**
 * 
 */
class dashboard  extends CI_Controller
{
public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('id'))
		{
         redirect('login');

		}
	}

	function index(){

		echo "Welcome";
		echo '<p align="center"><a href="'.base_url().'dashboard/logout">Logout</a></p>';

}

function logout()
{

$data= $this->session->all_userdata();
foreach ($data as $row => $rows_value) {
	$this->session->unset_userdata($row);

}
redirect('login');

}

}

?>
	