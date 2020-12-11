
<?php
class User_model extends CI_model{


        function create($formArray){

            $this->db->insert("users",$formArray);// Insert into users (name,email) Values(?,?);

            }


       function all(){

           return $users = $this->db->get('users')->result_array();

	        }


	         function getUser($Id){
           $this->db->where('Id',$Id);
           return $user = $this->db->get('users')->row_array();

	       }

	         function updateUser($Id,$formArray){
           $this->db->where('id',$Id);
		       $this->db->update('users',$formArray);

	      }
	         function deleteUser($Id){
           $this->db->where('Id',$Id);
		       $this->db->delete('users');

	      }
      }