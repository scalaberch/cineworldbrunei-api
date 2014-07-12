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

	/**
	*	Method Name: hasAlreadyVoted()
	*	Description: Checks if a user has already voted a certain movie
	*	  
	*	@param user: the id of the user that will vote on the movie
	*	@param movie: the id of the movie that will be voted
	*
	*	@return boolean: true if user has already voted, otherwise false
	**/

	public function hasAlreadyVoted($movie, $user){
		$sql = "SELECT idmovieVotes FROM movieVotes WHERE movie_idmovie=? AND users_idusers=?";
		$query = $this->db->query($sql, array($movie, $user));
		if ($query->num_rows() > 0){
			return true;
		} else { return false; }
	}


}

?>