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
    		return $this->addMovieVote($user, $movie, round($vote));
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


	/**
	*	Method Name: addMovieVote()
	*	Description: adds a vote to the database.
	*	 
	*	@param user: the id of the user that will vote on the movie
	*	@param movie: the id of the movie that will be voted
	*	@param vote: the vote of the user from 0-5.
	*	
	*	@return boolean:
    **/

	public function addMovieVote($user, $movie, $vote){
		$data = array('users_idusers'=>$user, 'movie_idmovie'=>$movie, 'votes'=>$vote);
		$sql = $this->db->insert_string('movieVotes', $data);

		$query = $this->db->query($sql);
		if ($query->db->affected_rows() > 0){
			return true;
		} else { return false; }
	}


	/**
	*	Method Name: updateMovieVote()
	*	Description: updates a vote to the database.
	*	 
	*	@param user: the id of the user that will vote on the movie
	*	@param movie: the id of the movie that will be voted
	*	@param vote: the vote of the user from 0-5.
	*	
	*	@return boolean:
    **/

	public function addMovieVote($user, $movie, $vote){
		$data = array('votes'=>$vote); $where = "movie_idmovie=".$movie." AND users_idusers=".$user;
		$sql = $this->db->update_string('movieVotes', $data, $where);

		$query = $this->db->query($sql);
		if ($query->db->affected_rows() > 0){
			return true;
		} else { return false; }
	}

	/**
	*	Method name: getMovieVotes()
	*	Description: gets all the votes for that certain movie.
	*
	*	@param movie: the movie id of the movie
	*	@return float/double: the average of the movie votes
	**/

	public function getMovieVotes($movie){
		$total = 0, $count = 0;

		// Get first all the possible 'references' of the movie
		$query = $this->db->query("SELECT referenced_movie FROM merged_reference WHERE movie_idmovie=?", array($movie));
		foreach($query->result() as $movies){

			// Then for every reference
			//	Get first the scrapped vote
			$subQuery = $this->db->query("SELECT movieFeedbackStars FROM movie WHERE idmovie=?", array($movies->referenced_movie));
			if ($subQuery->num_rows > 0){ foreach($subQuery->result as $sub){
					$total += $sub->movieFeedbackStars;
					$count++;
				}
			}

			// After that, get all the user votes given that referenced_movie, if there are any
			$subQuery = $this->db->query("SELECT votes FROM movieVotes WHERE movie_idmovie=?", array($movies->referenced_movie));
			if ($subQuery->num_rows > 0){ foreach($subQuery->result as $sub){
					$total += $sub->votes;
					$count++;
				}
			}
		}

		// Get the average
		$average = $total / $count;
		return $average;

	}




}

?>