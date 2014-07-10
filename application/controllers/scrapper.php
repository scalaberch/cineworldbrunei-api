<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scrapper extends CI_Controller {

	function __construct(){
		parent::__construct();

		$this->load->model('mall_ticket_m');
		$this->load->model('qlapcineplex_m');
		$this->load->model('timescineplex_m');
		$this->load->model('psbdualplex_m');

		$this->load->model('secondary_scrapper');
		//$this->load->model('info');

		require_once(APPPATH.'models/info.php');
	}



	public function index($param="foo"){
		//$this->load->view('welcome_message');

		//$data = $this->psbdualplex_m->getDataFromUrl("https://m.facebook.com/PSBdualplex/?v=timeline");
		//$this->secondary_scrapper->test_secondary();

		//$this->mall_ticket_m->scrapeData("http://www.mall-ticket.com", "The Mall Cineplex", 1);
		//$this->qlapcineplex_m->scrapeData("http://www.qlapcineplex.com", "Qlap CinePlex", 2);
		//$this->timescineplex_m->scrapeData("http://www.timescineplex.com", "Times CinePlex", 3);
		//$this->psbdualplex_m->scrapeData("https://m.facebook.com/PSBdualplex?v=timeline&filter=1", "PSB DualPlex", 4);
		$this->psbdualplex_m->scrapeData("http://www.cinemaonline.asia/Brunei/Movies/Showtimes.aspx?cin=623", "PSB DualPlex", 4);

		//$this->timescineplex_m->getDataFromUrl("http://www.timescineplex.com");

	}

	// 
	// Final Scrapping Method/Function
	//
	public function execScrapper(){

		$webParam = false; 
		if (isset($_GET['web'])){
			$webParam = true;
		} else { 
			echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "=============================================\n";
			echo "CineWorld Brunei - Web Scrapping Engine v.1.0\n";
			echo "=============================================\n\n";
			sleep(5);
			echo "[scrapper-service] Starting scrapping service...\n";
			sleep(5);
		}

		
		$cinemaData = array(); $resultScrapping = array();
		$query = $this->db->query('SELECT * FROM cinema');

		foreach($query->result() as $cinema){
			if (!$webParam){ echo "[scrapper-service] Calling scrapping service for: ".$cinema->cinemaName."\n"; }
			$exec = $this->getDataFromSource( $cinema->idcinema ); $result = false;

			if (!$exec){
				if (!$webParam){ echo "[scrapper-service] Can't open link. Might be disabled.\n\n"; }
			} else  { 
				$result = $exec->scrapeData($cinema->sourceURL, $cinema->cinemaName, $cinema->idcinema); 
				$resultScrapping[$cinema->cinemaName] = $result;

			}

			if ($result != false and !$webParam){
				echo "\n==============================\n";
				echo "        SCRAPPING RESULTS       \n\n";
				echo "  Scrapped Movies: ".$result['sm']."\n";
				echo "  Scrapped Schedules: ".$result['ss']."\n";
				echo "  New Movies: ".$result['nm']."\n";
				echo "  New Schedules: ".$result['ns']."\n";
				echo "==============================\nPlease wait for five seconds to continue...\n\n";
			}

			sleep(5);
		}

		if (!$webParam){

			echo "\n\n\n";
			echo "================================================\n";
			echo "              FINAL SCRAPPING RESULTS       \n\n\n";

			$sm = 0; $ss = 0; $nm = 0; $ns = 0;
			foreach($resultScrapping as $name=>$r){
				$sm += $r['sm'];
				$ss += $r['ss'];
				$nm += $r['nm'];
				$ns += $r['ns'];


				echo "CINEMA NAME: ".$name." \n";
				echo "  Scrapped Movies: ".$r['sm']."\n";
				echo "  Scrapped Schedules: ".$r['ss']."\n";
				echo "  New Movies: ".$r['nm']."\n";
				echo "  New Schedules: ".$r['ns']."\n\n";

			}

			echo " -----------------------------------------------\n";
			echo "  Total Scrapped Movies: ".$sm."\n";
			echo "  Total Scrapped Schedules: ".$ss."\n";
			echo "  Total New Movies: ".$nm."\n";
			echo "  Total New Schedules: ".$ns."\n";
			echo "================================================\n";
		}
		
		
	}


	public function wipeMovieData(){
		//	$key = md5($_GET['key']);

		$this->db->query("TRUNCATE movie");
		$this->db->query("TRUNCATE showtime");
		$this->db->query("TRUNCATE merged_reference");
	}



	/**
	 * Method: getDataFromSource()
	 * @param: source_id - database ID of the source.
	 * @return: Mixed. Returns false or an object.
	 *
	 * This method executes the scrapper function given the source id.
	 */
	public function getDataFromSource($source_id){
		switch($source_id){
			case 1:
				return $this->mall_ticket_m;
			case 2:
				return $this->qlapcineplex_m;
			case 3: case 5:
				return $this->timescineplex_m;
			case 4:
				return $this->psbdualplex_m;
			default:
				return false;
		}


	}



	/**
	 * Method:
	 * @param:
	 * @return: Array of format: YYYY-MM-DD HH:MM:DD
	 */
	public function normalize_schedules($scheduleList, $source){

		//echo $scheduleList[0];
		$id = 0; switch($source){
			case "The Mall Cineplex": $id = 1; break;
			case "Qlap CinePlex": $id = 2; break;
			case "Times CinePlex": $id = 3; break;
			case "PSB DualPlex": $id = 4; break;
			case "Times CinePlex Empire": $id = 5; break;
		}

		$newSchedules = array();
		foreach($scheduleList as $schedule){

			if (is_array($schedule)){

				$date_w = explode(" ", $schedule[0]);
				$date = date("Y")."-".$this->normalizeMonth($date_w[2])."-";
				if ($date_w[1] < 10){
					$date .= "0";
				} $date .= $date_w[1]." ";

				foreach($schedule[1] as $times){
					$datetime = $date.date("H:i:00", strtotime( trim($times) ) );
					$newSchedules[] = $datetime;
				}

			} else {
				// Getting the time...
				$datetime = ""; $hour = null;

				// Getting the date...
				if (preg_match_all("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $schedule, $matches)) {
					$datetime = $matches[0][0];
				} else {

					if (preg_match_all("/ [A-Z]+. [0-9]{2}/", $schedule, $match)){
						$date = explode(".", $match[0][0]);

						$datetime = date("Y")."-".$this->normalizeMonth($date[0])."-".trim($date[1]);
					} else { $datetime = " "; }
				}


				if (preg_match_all("/[0-9]{2}\:[0-9]{2}(AM|PM|NN|MN|)/", $schedule, $matches)){
					$datetime .= " ".date("H:i:00", strtotime( trim($matches[0][0]) ) );
				}

				$newSchedules[] = $datetime;

			}

		}

		return array("id"=>$id, "schedules"=>$newSchedules);
	}

	public function normalizeMonth($monthString){
		$month = trim(strtolower($monthString));
		switch($month){
			case "jan": return "01"; break;
			case "feb": return "02"; break;
			case "mar": return "03"; break;

			case "apr": return "04"; break;
			case "may": return "05"; break;
			case "jun": return "06"; break;

			case "jul": return "07"; break;
			case "aug": return "08"; break;
			case "sep": return "09"; break;

			case "oct": return "10"; break;
			case "nov": return "11"; break;
			case "dec": return "12"; break;
		}
	}

	public function printData(){
		$query = $this->db->query("SELECT * FROM cinema");
		foreach($query->result() as $row){
			echo $row->cinemaName."<br>";
			echo "<table border=1>";
			echo "<tr><th>Movie Name</th><th>Schedule</th></tr>";

			$query0 = $this->db->query("SELECT * FROM showtime LEFT JOIN movie ON movie.idmovie=showtime.movie_idmovie
											WHERE showtime.cinema_idcinema=?", array($row->idcinema));
			foreach($query0->result() as $movie){
				echo "<tr><td>";
				echo $movie->movieName;
				echo "</td><td>";
				echo $movie->schedule;
				echo "</td></tr>";

			}

			echo "</table><br><br>";
		}

	}

	/**
	 * Method: executeScrapper()
	 * @param: None.
	 * @return: None.
	 *
	 * This method could be called using PHP CLI for CRON jobs.
	 * This is used to call the scrapper/s to get all the data from the websites.
	 *
	 */
	
	public function executeScrapper($type="CLI", $source=0){

		$flag = false; $showMessages = false;
		if ($type == "CLI"){
			$showMessages = true;
			echo "[scrapper-service] Starting scrapping service...\n";
		}
		

		// Fetch all data...
		//	All data must be normalized beforehand...
		//	ASSUMPTION: movie names are normalized...

		$cinemaData = array();
		$query = $this->db->query('SELECT * FROM cinema');

		foreach($query->result() as $cinema){
			echo "[scrapper-service] Calling scrapping service for: ".$cinema->cinemaName."\n";
			$exec = $this->getDataFromSource( $cinema->idcinema );
			if (!$exec){
				echo "[scrapper-service] Source Error: ".$cinema->cinemaName." is not yet executed.\n";
			} else {
				$cinemaData[ $cinema->cinemaName ] = $exec->getDataFromUrl( $cinema->sourceURL );
			}
		}

		//print_r($cinemaData);
		echo "\n\n\n";
		echo "[scrapper-service] Consolidating scrapped data... Please wait...\n";

		// Then, we will consolidate the data for every cinema captured...
		// Now, the data must be per movies...
		
		$movieList = array();
		foreach($cinemaData as $cinemaName=>$cinema){

			foreach($cinema as $key=>$movieData){

				$searchTag = trim($movieData['searchTag']);

				// [FIX]: For mall_ticket_m wherein "blockbuster" still exists.
				// TODO: try putting it on mall_ticket_m model
				if (preg_match_all("/[Bb]lockbuster$/", $searchTag, $matches)){
					$searchTag = trim(preg_replace("/[Bb]lockbuster$/", "", $searchTag));
				}

				//Append to
				if (!array_key_exists($searchTag, $movieList)){
					$movie = array();
					//echo "New guy! Search tag is: ".$searchTag;

					$movie['info'] = new Info($searchTag);
					$movie['cinemas'] = array();
					$movie['schedules'] = array();

					$movieList[ $searchTag ] = $movie;
				}

				//Input Cinemas
				if (!in_array($cinemaName, $movieList[$searchTag]['cinemas'] )){
					$movieList[$searchTag]['cinemas'][] = $cinemaName;
				}

				//Input 2/3Dness and schedules...
				$movieList[$searchTag]['schedules'][] = array("cinema"=>$cinemaName, "screen"=>$movieData['dimension'], "schedule"=>$movieData['schedules']);


				// Consolidate all information collected first before getting to the secondary source/s
				// Secondary source/s are just for verification and fill-the-gap purposes only...
				$movieList[$searchTag]['info']->consolidateData($movieData, $searchTag);

			}

		}

		echo "[scrapper-service] Done consolidating data...\n";
		echo "[scrapper-service] Verifying movie list\n";
		echo " Movie List has: ".count($movieList)." movies.\n";

		// Now, let's verify the sources 
		foreach($movieList as $searchQuery=>$movie){

			//echo "\nMovie Name: ".$movie['info']->getMovieName()."\n";
			$search = strtolower($movie['info']->getMovieName());

			$search0 = explode(":", $search);
			$search = trim(preg_replace("/([23][Dd])/", "", $search0[0]));

			// Search this on the first source...
			$firstSource = $this->secondary_scrapper->checkIfInOMDB( $search ); //$searchQuery
			if (!$firstSource){
				// Search it on the secondary source...
				//echo "<hr>";
				//$this->secondary_scrapper->scrapeSecondarySource( $search );
				

			} else {

				// If it is on the first source, then grab the information needed...
				$firstSourceInfo = $this->secondary_scrapper->scrapeOMDB($firstSource);
				if (!$firstSourceInfo){
					// This would be either on the database error on the OMDB... Do nothing...
				} else {

					$movieList[$searchQuery]['info']->setMovieName( $firstSourceInfo->Title );
					$movieList[$searchQuery]['info']->setMovieDesc( $firstSourceInfo->Plot );
					$movieList[$searchQuery]['info']->setDirector( $firstSourceInfo->Director );
					$movieList[$searchQuery]['info']->setRunningTime( trim(preg_replace("/min/", "", $firstSourceInfo->Runtime)) );

					$movieList[$searchQuery]['info']->setGenre( $firstSourceInfo->Genre );
					$movieList[$searchQuery]['info']->setLanguage( $firstSourceInfo->Language );
					$movieList[$searchQuery]['info']->setCast( $firstSourceInfo->Actors );
					$movieList[$searchQuery]['info']->setReleaseDate( date("Y-m-d", strtotime($firstSourceInfo->Released)) );
					$movieList[$searchQuery]['info']->setRating( $firstSourceInfo->Rated);
					$movieList[$searchQuery]['info']->setWebsite( $firstSourceInfo->Website );
					$movieList[$searchQuery]['info']->setStars( $firstSourceInfo->imdbRating );
					$movieList[$searchQuery]['info']->setTrailer( "trailer here" );
					$movieList[$searchQuery]['info']->setPoster($firstSourceInfo->Poster);
				}
			}


			// Then get on the websites and trailer links... IMPORTANT!
			// Also, use the images to upload it on the server...
			$trailerLink = "";

		}

		// Fine, for now, let's print on the movie names....
		//foreach($movieList as $tag=>$movie){
		//	print_r($movie); echo "\n";
		//}


		// Then we save it to the database if ever the data is not yet in there... :)
		foreach($movieList as $tag=>$movie){

			// Check first if the tag is already in the database...
			$movieID = null;
			$query = $this->db->query("SELECT * FROM movie WHERE movieName=?", array($movie['info']->getMovieName()));
			if ($query->num_rows() <= 0){
			
				$i = $movie['info'];
				// If first time, then get the image....
				$imageLink = $i->getPoster();//$firstSourceInfo->Poster;
				if ($imageLink == "" or $imageLink == "N/A"){
					$imgName = "img/movies/noposter.jpg";
				} else { $imgName = "img/movies/".strtolower( preg_replace("/[^A-Za-z0-9]/", "_", $i->getMovieName() ) ) .".jpg"; }

				$insertQuery = $this->db->query("INSERT INTO movie VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
									array($i->getMovieName(), $i->getMovieDesc(), $i->getDirector(), $i->getRunningTime(), 
											$i->getStars(), $i->getTrailer(), $i->getLanguage(), $i->getGenre(), 
											$i->getReleaseDate(), $imgName, $i->getCast(), $i->getRating() ));
				$movieID = $this->db->insert_id();

				// Save the file to the file... ;)
				if ($imageLink != "" or $imageLink == "N/A"){
					$ch = curl_init($imageLink); 
					$fp = fopen($imgName, 'wb');
					curl_setopt($ch, CURLOPT_FILE, $fp);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					
					curl_exec($ch);
					curl_close($ch);
					fclose($fp);
				}
				


			} else { 
				foreach($query->result() as $row){
					$movieID = $row->idmovie;
				}
			}

			// Then, paste the schedules...
			foreach($movie['schedules'] as $sched){

				// Get first the cinema ID of it :)
				$cinemaID = 0;
				$cinemaQuery = $this->db->query("SELECT idcinema FROM cinema WHERE cinemaName=?", array($sched['cinema']) );
				foreach($cinemaQuery->result() as $row){
					$cinemaID = $row->idcinema;
				}

				// Then check if a schedule is already in the database....
				foreach($sched['schedule'] as $datetime){

					$scheduleQuery = $this->db->query("SELECT idshowtime FROM showtime WHERE schedule=? AND screen=? AND movie_idmovie=? AND cinema_idcinema=?", array($datetime, $sched['screen'], $movieID, $cinemaID) );
					

					//$scheduleQuery = $this->db->query("SELECT * FROM showtime WHERE schedule=? ", array("2010-09-19"));
					if ($scheduleQuery->num_rows() <= 0){

						$insertQueryStr = "INSERT INTO showtime VALUES(NULL, ?, ?, ?, ?)";
						$insertParams = array($cinemaID, $movieID, $datetime, $sched['screen']);

						echo "Adding new schedule for '".$movie['info']->getSearchTag()."' as ".$sched['screen']." on $datetime...\n";
						$insertQuery = $this->db->query($insertQueryStr, $insertParams);
					}

				}


			} // END foreach ($movie['schedules'] as $sched)

		}
	
	
	} // End Method


}
