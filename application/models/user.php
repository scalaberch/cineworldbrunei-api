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
    *   
    *   @param $userdata: An array that consists of the user's data grabbed from the social media
    *       it used. Array is of format {}
    **/
    



}


?>