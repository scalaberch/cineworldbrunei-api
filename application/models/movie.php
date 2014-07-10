<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movie extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    /** 
    *   This returns the list of movies. Database speaking, the list of movies
    *   altogether with the referenced movie. If there is a reference, the reference will be 
    *   shown...
    *   @param: None
    *   @return: Array of movieID
    **/
    public function getAllMoviesID(){
        $movies = $this->db->query("SELECT idmovie FROM movie");
        $result = array();

        foreach($movies->result() as $movieResult){
            $reference = $this->db->query("SELECT * FROM merged_reference WHERE movie_idmovie=?", array($movieResult->idmovie));
            if ($reference->num_rows() > 0){
                foreach($reference->result() as $refResult){
                    
                    // Check if reference_id is not on the result, if not... put it on the result
                    if (!in_array($refResult->referenced_movie, $result)){
                        $result[] = $refResult->referenced_movie;
                    }

                }
            } else { $result[] = $movieResult->idmovie; }

        }

        return $result;
    }

    /**
    *   This returns the movie information given a movieid
    *   @param: movieid - The movie id
    *   @return: Array of mixed. Indeces are just the FIELD NAMES of the movie in DB.
    *           Boolean (FALSE) if no such movie on the database
    **/
    public function getMovieInformation($movieid){
        $movieQuery = $this->db->query("SELECT * FROM movie WHERE idmovie=?", array($movieid));
        if ($movieQuery->num_rows() > 0){
            foreach($movieQuery->result() as $m){
                return $m;
            }
        } else { return false; }
    }

    /**
    *   This updates the movie information given a movieid. Additionally, the parameters must come
    *   from the "moviemanager" interface
    *   @param: d - array of movieInformation
    *   @return: number of affected rows (if query is successful)
    *
    *   @todo: update also the controller...
    **/
    public function updateMovieInfo($d){
 
        $content = array($d[0],$d[10],$d[1],$d[3],$d[7],$d[9],$d[5],$d[4],$d[6],$d[2],$d[8], $d[11]);
        $queryString = "UPDATE movie SET movieName=?, movieDescription=?, movieDirector=?,
                            runningTime=?, movieFeedbackStars=?, trailerURL=?, language=?,
                            movieGenre=?, releaseDate=?, movieActors=?, movieRating=? WHERE idmovie=?";

        $query = $this->db->query($queryString, $content);
        return $this->db->affected_rows();
    }

    /**
    *   This "merges" a certain movie to another movie. Database speaking, we'll just add a reference
    *   to the "merged_reference" table of the movie.
    *   More likely, it would be @sourceID -> @mergerID
    *   @param: sourceID - The movie to be merged to.
    *   @param: mergerID - The movie that sourceID will be merged on.
    *   @return: 0
    *
    *   @todo: Reference also the movieVotes for it to be unified. 
    **/
    public function mergeMovie($sourceID, $mergerID){
        $this->db->query("INSERT INTO merged_reference VALUES(NULL, ?, ?)", array($sourceID, $mergerID));
        return 0;
    }

    /**
    *   This will vote for a movie. 
    *   @param: movieID - The movie to be voted
    *   @param: voteCount - Count on votes. Must be only 1,2,3,4 and 5 stars.
    *   @return: False if unsuccessful, otherwise the id of the vote.
    *
    *   @todo: Implement this. Empty.
    **/
    public function voteMovie($movieID, $voteCount){



    }

     /**
    *   This will get the votes for a specific movie.
    *   @param: movieID - The movie to be voted
    *   @return: False if unsuccessful, otherwise the number of votes (float)
    *
    *   @todo: Implement this. Empty.
    **/
    public function getMovieVotes($movieID){

    }


}

?>
