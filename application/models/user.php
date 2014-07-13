<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    

    /**
    *   Method Name; getUserID()
    *   Description: Gets the user id of a certain user given credentials
    *
    *   @param socialid: The social media id grabbed from login
    *   @param handler: The social media handler grabbed from login
    *
    *   @return mixed: type(int) if user id found otherwise false
    **/

    public function getUserID($socialid, $handler){
        $sql = "SELECT idusers FROM users WHERE social_acct_id=? AND social_acct_handler=?";
        $query = $this->db->query($sql, array($socialid, $handler));
        if ($query->num_rows() > 0){
            foreach($query->result() as $result){
                return $result->idusers;
            }
        } else { return false;  }
    }



    /**
    *   Method Name: register_user()
    *   Description: Registers a user to the database.
    *   Assumption: The user is not yet registered to the database
    *               Also, all inputs are already in correct form such as email (<id>@<domain>.<tag>) and 
    *                   contact number ( +<country_code><area/network_code><number> )
    *   
    *   @param ln:  User's Last Name
    *   @param fn:  User's First Name
    *   @param mn:  User's Middle Name
    *   @param sai: User's social account id (from login action)
    *   @param sah: User's social account handler (Facebook or Twitter)
    *   @param a:   User's address
    *   @param e:   User's email address
    *   @param c:   User's contact number
    *
    *   @return
    **/
    
    public function registerUser($ln, $fn, $mn, $sai, $sah, $a, $e, $c){
        $data = array('user_last_name'=>$ln, 'user_first_name'=>$fn, 'user_middle_name'=>$mn,
                        'social_acct_id'=>$sai, 'social_acct_handler'=>$sah, 'user_address'=>$a,
                        'user_email'=>$e, 'user_contact_number'=>$c);
        $sql = $this->db->insert_string('users', $data);
        $query = $this->db->query($sql);

        if ($this->db->insert_id() != null){
            return true;
        } else { return false; }
    }


}


?>