<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Booking extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
	* 	Defining some variable constants here.
	*	This is for the booking transaction state.
    **/

	public $_ONGOING = "ONGOING";
    public $_CONFIRMED = "CONFIRMED";
    public $_SEAT_EXPIRED = "SEAT_EXPIRED";
    public $_EXPIRED = "EXPIRED";

    



}


?>