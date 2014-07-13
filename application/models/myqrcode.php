<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH."libraries/phpqrcode/qrlib.php");
class MyQRCode extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    /**
	*	Method Name: generateTicketQRCode
	*	Description: generates the QR Code of the ticket...
	*
	*	@param ticketid: the id of the ticket
	*	
	*	@return 
    **/

    public function generateTicketQRCode($ticketid){
    	QRcode::png($ticketid);
    }

    

}

?>