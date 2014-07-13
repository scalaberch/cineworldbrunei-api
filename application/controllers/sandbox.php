<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sandbox extends CI_Controller {

	public function index(){
		$this->load->model("myqrcode");

		$this->myqrcode->generateTicketQRCode("chuchubaba");
	}

}

?>