<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movievote extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    /**
	*	Method Name: voteMovie
	*	Description: Gives a certain movie a vote out of 5 stars.
	*
	*	@param user: the id of the user that will vote on the movie
	*	@param movie: the id of the movie that will be voted
	*	@param vote: the vote of the user from 0-5.
	*	
	*	@return boolean:
    **/

    public function voteMovie($user, $movie, $vote){
    	if ($this->hasAlreadyVoted($movie, $user)){

    	} else {

    	}
    }

    


}

?>