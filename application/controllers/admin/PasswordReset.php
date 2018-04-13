<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class PasswordReset Extends CI_Controller{

	function __construct()
	{
		parent::__construct();	
		$this->load->model('admin_api/passwordReset_model');
        //date_default_timezone_set("Asia/Kolkata");
        //date_default_timezone_get();	
        $this->load->helper('security');	
	}

	public function index($user_id=NULL){
		$user_id =  $_GET['user_id'];
		$data['message'] = '';

		if(!isset($_GET['message'])){
			if (!empty($user_id)) {
				$user_id= $user_id;
			}else{
				$user_id= "";
				$data['message'] = 'User not valid!';
			}
		}else{
			$data['message'] = $_GET['message'];
		}	

		
		$data['title'] = 'Password reset';		
		$data['user_id'] = $user_id;
		
	    $this->load->view('admin/passwordReset_view',$data);
	}

	/* update the new password */
	public function updatePassword(){
			
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password','New password','required|min_length[6]');
		$this->form_validation->set_rules('cpassword','Confirm password','required|min_length[6]|matches[password]');

		if ($this->form_validation->run() == FALSE)
		{	
			$user_id = $this->input->post('user_id');	
			$data['user_id'] = $user_id;	
			$data['message'] = "";	
			$this->load->view('admin/passwordReset_view',$data);		
		}else{				
			$query=$this->passwordReset_model->update_password();
			 if($query){
				redirect('admin/PasswordReset?user_id=&message=Password updated successfully!');
			}else{
				redirect('admin/PasswordReset?user_id=&message=Password can not be updated');
			}
		}
	}
}

?>