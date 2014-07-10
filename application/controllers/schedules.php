<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');
class Schedules extends REST_Controller {

	function __construct(){
		parent::__construct();
        date_default_timezone_set("Asia/Brunei");

        header('Access-Control-Allow-Origin: *');

		// Loading the database manually...
		//$dsn = "mysql://root:root@localhost/cinemaWebService";
		//$this->load->database($dsn);
	}

    function test_get(){
		$this->response(array());
    }


	function user_get(){
        // respond with information about a user
        $data = array("information"=>'returned: '. $this->get('id'));
        $this->response($data);
    }



    function cinemas_get(){

    	$cinemaList = array();

    	$query = $this->db->query("SELECT * FROM cinema");
    	foreach($query->result() as $cinema){
    		$data = array("cinemaID"=>$cinema->idcinema, "cinemaName"=>$cinema->cinemaName, "image"=>"image/location.jpg" );
    		$cinemaList[] = $data;
    	}
    	
        $this->response($cinemaList);
    }

    function movies_get(){
        $data = array(); $movies = array();
        $startDateTime = date("Y-m-d 00:00:00"); $endDateTime = date("Y-m-d 23:59:59");

        // Get movieList with the schedule of TODAY...
        //$queryStr = "SELECT * FROM movie AS m LEFT JOIN showtime AS s ON m.idmovie=s.movie_idmovie
        //               WHERE s.schedule BETWEEN '$startDateTime' AND '$endDateTime' ORDER BY m.searchTag ASC";

        $movieListQuery = $this->db->query("SELECT * FROM movie");
        foreach($movieListQuery->result() as $movieResult){

            $cinemaInfo = array();
            $cinemaInfo['movieName'] = $movieResult->movieName;
            $cinemaInfo['description'] = $movieResult->movieDescription;
            
            $cinemaInfo['actors'] = $movieResult->movieActors; //$movieResult->moveActors;
            
            $cinemaInfo['rating'] = $movieResult->movieRating;
            $cinemaInfo['director'] = $movieResult->movieDirector;
            $cinemaInfo['runningTime'] = $movieResult->runningTime;
            $cinemaInfo['stars'] = $movieResult->movieFeedbackStars/2;
            $cinemaInfo['language'] = $movieResult->language;
            $cinemaInfo['genre'] = $movieResult->movieGenre;
            $cinemaInfo['releaseDate'] = $movieResult->releaseDate;
            $cinemaInfo['trailerURL'] = $movieResult->trailerURL;
            $cinemaInfo['imageURL'] = $movieResult->imageURL;

            //
            // New Query right here...
            //

            $cineScheds = array();
            $cinemaQueryStr = "SELECT DISTINCT cinema_idcinema FROM showtime WHERE movie_idmovie=?";
            $cinemaQuery = $this->db->query($cinemaQueryStr, array($movieResult->idmovie));
            foreach($cinemaQuery->result() as $c){
                //echo $c->cinema_idcinema."<br>";
                $cineSched = array("idcinema"=>$c->cinema_idcinema, "2d"=>array(), "3d"=>array());

                $schedQueryStr = "SELECT * FROM showtime WHERE cinema_idcinema=? AND movie_idmovie=? AND schedule BETWEEN ? AND ?";
                $params = array($c->cinema_idcinema, $movieResult->idmovie, $startDateTime, $endDateTime);
                $schedQuery = $this->db->query($schedQueryStr, $params);

                if ($schedQuery->num_rows() > 0){

                    $sched = array(); 

                    foreach($schedQuery->result() as $sc){

                        $timestamp = strtotime($sc->schedule);
                        if ($sc->screen == "2D"){
                            $cineSched['2d'][] = $timestamp;
                        } else if ($sc->screen == "3D"){
                            $cineSched['3d'][] = $timestamp;
                        }

                    } 


                    $cineScheds[] = $cineSched;

                }

                //break;
            }

            $movie = array("movieID"=>$movieResult->idmovie, 
                            "movieInformation"=>$cinemaInfo, 
                            "cinemaSchedules"=>$cineScheds
                            );

            //$movie = null;
            $movies[] = $movie;
        } // end movie

        $data['data'] = $movies;
        $data["timestamp"] = time();
        $this->response($data);
    }

    function moviestest_get(){

        $startDateTime = date("Y-m-d 00:00:00"); $endDateTime = date("Y-m-d 23:59:59");
        $sql = "SELECT * FROM showtime AS s WHERE s.schedule BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($startDateTime, $endDateTime));


        $movieList = array("test"=>1, 2);

        foreach($query->result() as $schedResult){


        }


        $this->response($movieList);

    }

    function cinemamovie_get(){
        $id = $this->get('id'); $movieList = array();

        $queryString = "SELECT DISTINCT movie_idmovie FROM showtime WHERE cinema_idcinema=?";
        $query = $this->db->query($queryString, array($id));
        foreach($query->result() as $movie){


            $movieList[] = $movie->movie_idmovie;
        }


        $this->response($movieList);
    }

    function moviesLegacy_get(){

        $data = array(); $startDateTime = date("Y-m-d 00:00:00"); $endDateTime = date("Y-m-d 23:59:59");


        // Get movieList with the schedule of TODAY...
        //$queryStr = "SELECT * FROM movie AS m LEFT JOIN showtime AS s ON m.idmovie=s.movie_idmovie
        //               WHERE s.schedule BETWEEN '$startDateTime' AND '$endDateTime' ORDER BY m.searchTag ASC";

        $movieListQuery = $this->db->query("SELECT * FROM movie");
        $counter = 0;
        foreach($movieListQuery->result() as $movieResult){

            $cinemaInfo = array();
            $cinemaInfo['description'] = $movieResult->movieDescription;
            $cinemaInfo['actors'] = $movieResult->moveActors;
            $cinemaInfo['rating'] = $movieResult->movieRating;
            $cinemaInfo['director'] = $movieResult->movieDirector;
            $cinemaInfo['runningTime'] = $movieResult->runningTime." minutes";
            $cinemaInfo['stars'] = $movieResult->movieFeedbackStars/5;
            $cinemaInfo['language'] = $movieResult->language;
            $cinemaInfo['genre'] = $movieResult->movieGenre;
            $cinemaInfo['releaseDate'] = $movieResult->releaseDate;
            $cinemaInfo['trailerURL'] = $movieResult->trailerURL;
            $cinemaInfo['imageURL'] = $movieResult->imageURL;



            $cinemaScheds = array(); $prevCinema = null; $item = null;
            $cinemaSchedQueryStr = "SELECT * FROM showtime LEFT JOIN cinema ON cinema.idcinema=showtime.cinema_idcinema 
                                        WHERE showtime.schedule BETWEEN ? AND ? ORDER BY cinema.cinemaName ASC";
            $cinemaSchedQuery = $this->db->query($cinemaSchedQueryStr, array($startDateTime, $endDateTime));
            foreach($cinemaSchedQuery->result() as $schedResult){

                if ($prevCinema != $schedResult->idcinema){
                    $prevCinema = $schedResult->idcinema;
                    $item = array("cinemaID"=>$schedResult->idcinema, "schedules"=>array());
                    $cinemaScheds[$schedResult->cinemaName] = $item;
                }

                $cinemaScheds[$schedResult->cinemaName]["schedules"][] = array("screen"=>$schedResult->screen, "sched"=>$schedResult->schedule);

            }

            $movie = array("movieID"=>$movieResult->idmovie, "movieName"=>$movieResult->movieName, 
                                "movieInformation"=>$cinemaInfo, "cinemaSchedules"=>$cinemaScheds );
            $data[] = $movie;
        }

        $this->response(array("movies"=>$data));
    }

}

?>
