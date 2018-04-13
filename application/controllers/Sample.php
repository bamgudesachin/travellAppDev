<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Sample extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function sample()
	{	
		$data = array(
		'userId'=>'1',
		'itineraryId'=>'1',
		'notesTitle'=>'CRON',
		'notesData'=>'CRON'
		);
		$this->db->insert('notes',$data);
	}
}
?>