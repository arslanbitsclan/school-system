<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Backup_crm extends AdminController
{
     public function __construct()
    {
        parent::__construct();
        
    }
    public function index()
    {
        //$data['title'] = _l('Backup_CRM');
        $this->load->view('backup_crm');
    }

     public function table()
    {
    
        $this->app->get_table_data(module_views_path('Backup_crm', 'Tables/Backup_crm'));
        
    }

    public function backupsettings()
    {
         $this->load->view('backup_setting');
       // echo "Backup Setting";
        
    }
    
       public function restorebackup($id)

   {
       echo "Restore Backup";
   }


    public function deletebackup($id)

   {
       $this->load->model('Backup_crm_model');
       $this->Backup_crm_model->delete($id);
       //set_alert('warning', _l('Person_Deleted' ));
       redirect('Backup_crm/Backup_crm');
   }
}