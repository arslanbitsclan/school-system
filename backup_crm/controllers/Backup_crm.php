<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Backup_crm extends AdminController
{
     public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('zip');
        $this->load->helper('string');
        $this->load->model('Backup_crm_model');
    }
    /**
    * display the backup list page
    */
    public function index()
    {
        
        $this->load->view('backup_crm');
    }
     /**
    * To fetch data from table and show on datatables
    */

     public function table()
    {
    
        $this->app->get_table_data(module_views_path('Backup_crm', 'Tables/Backup_crm'));
        
    }
      /**
    * display backup setting page to select backup type and to select auto remove backup
    */

    public function backupsettings()
    {
         $this->load->view('backup_setting');
       
        
    }
     /**
    * For restoring the deleted backup 
    */
       public function restorebackup($id)

   {
       echo "Restore Backup";
   }

   /**
    * For deleting single backup field from DB 
    */
    public function deletebackup($id)

   {
       
       $this->Backup_crm_model->delete($id);
       //set_alert('warning', _l('Person_Deleted' ));
       redirect('Backup_crm/Backup_crm');
   }
   
    /**
    * For deleting bulk of backups from DB  
    */

    public function bulk_action()
    {
        
      $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->Backup_crm_model->delete($id)) {
                            $total_deleted++;
                        }
                      }

                    }
                }
            }


        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }




     /**
    * For  backup  
    */


      public function build_backups()
        {
       
         $date = date("y-m-d");
         $this->database_backup($date);
         $this->project_backup($date);
         //$this->send_backup($date);

        }

    public function database_backup($date)
       {
         //$this->load->helper('string');
        $key_name1 = md5(date("d_m_Y_H_i_s")) . '_';
        $key_name2 = '_db';
        $key_name3 = date("d_m_Y_H_i_s"); 
         $this->load->dbutil();
         $backup= $this->dbutil->backup();
           $path = "crm_backup";

         if(!is_dir($path)) //create the folder if it's not already exists
         {
         mkdir($path,0755,TRUE);
         }
         write_file(FCPATH.'crm_backup/database_'.$date.$key_name1 . $key_name3 . $key_name2 .'.zip',$backup); 



       } 
    public function project_backup($date)
       {
        $key_name1 = md5(date("d_m_Y_H_i_s")) . '_';
        $key_name2 = '_db';
        $key_name3 = date("d_m_Y_H_i_s");
         $path = "crm_backup";

         if(!is_dir($path)) //create the folder if it's not already exists
         {
         mkdir($path,0755,TRUE);
         }
        
        $this->zip->read_dir(FCPATH,FALSE);
        $this->zip->archive('crm_backup/project_'.$date.$key_name1 . $key_name3 . $key_name2 .'.zip');
        echo "Backup Done";


       }

}