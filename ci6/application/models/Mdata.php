<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Mdata extends CI_Model
{     

      public $table1 = "tbnote";  
      public $select_column1 = array("id", "title",);  
      public $order_column1 = array("id", "title",);  

      function make_datatables1(){  
           
            $this->db->select($this->select_column1);  
           $this->db->from($this->table1);  
           if(isset($_POST["search"]["value"]))  
           {  
                $this->db->like("id", $_POST["search"]["value"]);  
                $this->db->or_like("title", $_POST["search"]["value"]);  
           }  
           if(isset($_POST["order"]))  
           {  
                $this->db->order_by($this->order_column1[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
           }  
           else  
           {  
                $this->db->order_by('id', 'DESC');  
           }  

           if($_POST["length"] != -1)  
           {  
                $this->db->limit($_POST['length'], $_POST['start']);  
           }  
           $query = $this->db->get();  
           return $query->result();  
      }  

      function get_filtered_data1()
      {  
           
            $this->db->select($this->select_column1);  
           $this->db->from($this->table1);  
           if(isset($_POST["search"]["value"]))  
           {  
                $this->db->like("id", $_POST["search"]["value"]);  
                $this->db->or_like("title", $_POST["search"]["value"]);  
           }  
           if(isset($_POST["order"]))  
           {  
                $this->db->order_by($this->order_column1[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
           }  
           else  
           {  
                $this->db->order_by('id', 'DESC');  
           }  

           $query = $this->db->get();  
           return $query->num_rows();  
      }       

      function get_all_data($table)  
      {  
           $this->db->select("*");  
           $this->db->from($table);  
           return $this->db->count_all_results();  
      }


      //query insert data
      function insert_all($table,$data) 
      {
        $this->db->insert($table,$data);
      }

      
 }

?>