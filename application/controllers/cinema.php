<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');
class Cinema extends REST_Controller {

	function __construct(){
		parent::__construct();
        date_default_timezone_set("Asia/Brunei");
        header('Access-Control-Allow-Origin: *');
	}


	/**
	*	This method returns the list of movies in for every cinema
	*	@param id: The id of the cinema
	*	@param schedules: If this is set, include
	*
	*	@return: json object
	**/
	public function getMovies_get(){
		$data = array('returned: '=>$this->get('id'));
        $this->response($data);
	}


	/**
	*	This method returns the cinema information
	* 	Assumption: Table `cinema` is already loaded with information
	*	@param id: The id of the cinema pertained. If this is set, returns the information for
	*		that specific cinema. Otherwise, returns the information of all the cinemas.
	*
	*	@return: json object
	**/
	public function getCinemaInfo_get(){
		$cinema_id = $this->get('id');
		$sql = "SELECT * FROM cinema";
		if ($cinema_id == false){
			$query = $this->db->query($sql);
		} else { 
			$sql .= " WHERE idcinema=?";
			$query = $this->db->query($sql, array($cinema_id));
		}

		$result = array();
		foreach($query->result() as $cinemas){
			$result[] = $cinemas;
		}

		$this->response($result);
	}

	/**
	*	This method updates the cinema information given.
	*	This must be only used for the `moviemanager`
	*	@param id: The id of the cinema pertained. If this is set, returns the information for
	*		that specific cinema. Otherwise, returns the information of all the cinemas.
	*
	*	@return: json object if success or failed
	**/
	public function updateCinemaInfo_put(){
		header('Access-Control-Allow-Origin: *');
		//$data = 

		 $this->response(null, 200);
	}




}