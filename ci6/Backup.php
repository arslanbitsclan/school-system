<?php


class Backup extends CI_Controller{


public function __construct()
	{
		parent::__construct();
		$this->load->helper('file');
		$this->load->library('zip');
		$this->load->helper('email');
        $this->load->library('email');
		//$this->load->library('encryption');
	    //$this->load->model('register_model');
	}


 public	function index(){
			//print_r(FCPATH);exit;
			$this->build_backups();
		}


    public function build_backups()
		{
       
         $date = date("y-m-d");
         $this->database_backup($date);
         $this->project_backup($date);
         //$this->send_backup($date);

		}

    public function database_backup($date)
       {   

       	$this->load->helper('string');
		$key_name1 = md5(date("d_m_Y_H_i_s")) . '_';
		$key_name2 = '_db';
		$key_name3 = date("d_m_Y_H_i_s");
         //$this->load->helper('file'); 
         $this->load->dbutil();
         $backup= $this->dbutil->backup();
           $path = "backup_crm";

         if(!is_dir($path)) //create the folder if it's not already exists
         {
         mkdir($path,0755,TRUE);
         }
         write_file('backup_crm/database_'.$date.$key_name1 . $key_name3 . $key_name2 .'.zip',$backup); 



       } 
    public function project_backup($date)
       {
        //$this->load->library('zip');
        $this->zip->read_dir(FCPATH,FALSE);
        $this->zip->archive('backup_crm/project_'.$date.'.zip');
        echo "Backup Done";


       }

    // public function send_backup($date)
    //   {

    //    //$this->load->helper('email');
    //    //$this->load->library('email');
    //    $config['charset'] ='utf-8';
    //    $config['mailtype'] ='html';
    //    $config['newline'] ="\r\n";
    //    $config['crlf'] ="\r\n";
    //    $this->email->initialize($config);
    //    $this->email->from('arslanbitsclan@gmail.com',"Arslan");
    //    $this->email->to('usamabitsclan@gmail.com');
    //    $this->email->attach('backup_crm/project_'.$date.'.zip');
    //    $this->email->attach('backup_crm/database_'.$date.'.zip');
    //    $this->email->subject("Backup For $date");
    //    $this->email->message("Backup For $date");
    //    if($this->email->send()){
    //    	unlink(PUBPATH."backup_crm/".'database_'.$date.'.zip');
    //     unlink(PUBPATH."backup_crm/".'project_'.$date.'.zip');
    //     }
    //     else{
    //     	show_error($this->email->print_debugger());
    //     }

    //   } 

}