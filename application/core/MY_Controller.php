<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $data = array();
    
    function __construct ()
    {
        parent::__construct();
       // date_default_timezone_set('Asia/Kolkata');
        if($this->session->userdata('logged_in')!=TRUE)
        {redirect('login/index');}
    	

    	
    }
}?>