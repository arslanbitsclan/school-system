<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Backup_crm_model extends App_Model
{
    private $contact_columns;

    public function __construct()
    {
        parent::__construct();

        
    }

     public function delete($id)
    {

       $this->db->where('id', $id);
       $this->db->delete(db_prefix() . 'backups');

    }

}