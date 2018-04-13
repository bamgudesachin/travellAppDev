<?php defined('BASEPATH') OR exit('No direct script access allowed');

//require_once('./application/libraries/REST_Controller.php');
//require_once('./application/libraries/new_stripe/vendor/autoload.php');
//require_once APPPATH.'';
//require_once('./application/libraries/new_stripe/vendor/autoload.php');  

class Common extends MY_Controller1 {

	public function __construct()
	{
		parent::__construct();
		$this->config->load('my_constants');
		$this->load->model('mobile_api/Common_model');
		$this->load->model('mobile_api/Auth_model');
		$this->load->helper('security');	
		include APPPATH . 'libraries/classes/class.phpmailer.php';
	}

	
	/*Check the user present or not*/
	public function user_check($userId)
	{	
		$status = $this->Common_model->user_check($userId);	
		if ($status){
			return true;            
		}else{
			$this->form_validation->set_message('user_check', '{field} is do not exists');
			return false;
		}
	}

	/* Student Profile details */
	public function profileDetails_post()
	{
		$post_data = $this->post();
		
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
    			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);    		
		}else{

			$profile_detail = $this->Common_model->profile_detail($post_data);
			if ($profile_detail) {
				$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Profile detail fetched successfully','Result'=>$profile_detail[0]), 200);
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'No Data Found','Result'=>'No Data Found'), 400);
			}	
		}
	}

	/*Function for update user profile*/
	public function updateProfile_post()
	{
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');
		$email = array_key_exists("email",$post_data);
		if($email){ 
			$this->form_validation->set_rules('email','Email','required|valid_email|callback_update_email_check');

		}
		
		// $this->form_validation->set_rules('password','Password','required');
		// $this->form_validation->set_rules('newPassword','New password','required');
		$profile_picture = array_key_exists("profile_picture",$post_data);
		if(!empty($post_data['profile_picture'])){			
			$this->form_validation->set_rules('profile_picture', 'User profile image', 'required|callback_handle_profile_image_upload');		
		}else{
			unset($post_data['profile_picture']);
		}

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);    		
		}else{

			$data = $this->Common_model->updateProfile($post_data);
			//print_r($data);exit();
			if ($data) {
				$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Profile updated successfully.','Result'=>$data[0]), 200);
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => $_POST['message'],'Result'=>false), 400);
			}		
		}
	}

	/*Function for upload profile picture */
	public function handle_profile_image_upload() {

		$image = $this->post('profile_picture');

		    $temp_file_path = tempnam(sys_get_temp_dir(), 'androidtempimage'); // might not work on some systems, specify your temp path if system temp dir is not writeable
		    file_put_contents($temp_file_path, base64_decode($image));

		    $image_info = getimagesize($temp_file_path); 
		    $_FILES['userfile'] = array(
		    	'name' => uniqid().'.'.preg_replace('!\w+/!', '', $image_info['mime']),
		    	'tmp_name' => $temp_file_path,
		    	'size'  => filesize($temp_file_path),
		    	'error' => UPLOAD_ERR_OK,
		    	'type'  => $image_info['mime'],
		    	);


		    $config['upload_path'] = './uploads/user';	    
		    $config['allowed_types'] = 'gif|jpg|jpeg|png';
		    $config['max_size'] = '2048';
		    $config['remove_spaces'] = TRUE;
		    $config['encrypt_name'] = TRUE;


		    $this->load->library('upload', $config);
		    $this->upload->initialize($config);
		    if($this->upload->do_upload_rest('userfile', true)) {
		    	$arr_image_info = $this->upload->data();
		        //$_POST['profile_picture1'] = '/uploads/user/'.$arr_image_info['file_name'];	
		    	$_POST['profile_picture1'] = $arr_image_info['file_name'];		    
		    	return true;
		    }else{
		    	$error = $this->upload->display_errors('', '');
		    	$this->form_validation->set_message('handle_profile_image_upload', $error);
		    	return false;
		    }
		}


		/* To check the email is exist */
		public function update_email_check($email)
		{	
			$post_data = $this->post();
			$userId = $post_data['userId'];
			$status = $this->Common_model->update_email_check($email,$userId);    	
			if ($status){
				$this->form_validation->set_message('update_email_check', 'An account already exists with this email ID');
				return false;
			}else{
				return true;
			}    	      
		}
	/*
		function to clean the token
	*/
		function clean($string)
		{
		    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

		    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		}

		function uIdtoken($post_data)
		{
			$this->db->select('userId');
			$this->db->from('users');
			$this->db->where('userId',$post_data['userId']);
			$this->db->where('token',$post_data['token']);
			$data = $this->db->get()->row();

			if(!empty($data))
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		/* CHECKING FOR DIFFERENT ID'S*/
		function itinerary_check_common($post_data)
		{

			$this->db->select('itineraryId');
			$this->db->from('itinerary');
			$this->db->where('userId',$post_data['userId']);
			$this->db->where('itineraryId',$post_data['itineraryId']);
			$data = $this->db->get()->result();
			return $data;
		}

		public function itinerary_check($itineraryId)
		{	
			$status = $this->Common_model->itinerary_check($itineraryId);	
			if ($status){
				return true;            
			}else{
				$this->form_validation->set_message('itinerary_check', '{field} is do not exists');
				return false;
			}
		}

		public function flight_check($flightId)
		{
			$status = $this->Common_model->flight_check($flightId);	
			if ($status){
				return true;            
			}else{
				$this->form_validation->set_message('flight_check', '{field} is do not exists');
				return false;
			}
		}

		public function car_rent_check($carRentId)
		{
			$status = $this->Common_model->car_rent_check($carRentId);	
			if ($status){
				return true;            
			}else{
				$this->form_validation->set_message('car_rent_check', '{field} is do not exists');
				return false;
			}
		}
		public function hotel_check($hotelId)
		{
			$status = $this->Common_model->hotel_check($hotelId);	
			if ($status){
				return true;            
			}else{
				$this->form_validation->set_message('hotel_check', '{field} is do not exists');
				return false;
			}
		}

		public function notes_check($notesId)
		{
			$status = $this->Common_model->notes_check($notesId);	
			if ($status){
				return true;            
			}else{
				$this->form_validation->set_message('notes_check', '{field} is do not exists');
				return false;
			}		
		}
		/************INSERT*******************/


		public function insertFlight_post()
		{
			$post_data = $this->post();		
			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');
			
			if (array_key_exists("departureTimeZone",$post_data))
			{
				$this->form_validation->set_rules('departureTimeZone','Departure Time Zone','required');
			}
			if (array_key_exists("arrivalTimeZone",$post_data))
			{
				$this->form_validation->set_rules('arrivalTimeZone','Arrival Time Zone','required');
			}
			
			if (array_key_exists("flightId",$post_data))
			{
				$this->form_validation->set_rules($this->config->item('editFlight'));
			}
			else
			{
				$this->form_validation->set_rules($this->config->item('add_flight'));
			}


			if ($this->form_validation->run() === false) {
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
			}
			else
			{

				$token = $this->uIdtoken($post_data);
				if($token)
				{
					unset($post_data['token']);
					$data = $this->Common_model->insert_flight($post_data);
					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['message'],'Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'We are experiencing some technical difficulties, please try again later.', 'Result' => $data), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
				}
			}
		}



		public function insertHotel_post(){
			$post_data = $this->post();
			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');
			
			if (array_key_exists("timeZone",$post_data))
			{
				$this->form_validation->set_rules('timeZone','Time Zone','required');
			}
			
			if (array_key_exists("hotelId",$post_data))
			{
				$this->form_validation->set_rules($this->config->item('editHotel'));
			}
			else
			{
				$this->form_validation->set_rules($this->config->item('add_hotel'));
			}

			if ($this->form_validation->run() === false) {
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
			}

			else
			{
				$token = $this->uIdtoken($post_data);
				if($token)
				{
					unset($post_data['token']);
					$data = $this->Common_model->insert_hotel($post_data);

					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['message'],'Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'We are experiencing some technical difficulties, please try again later.','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
				}

			}
		}

		public function insertCarRental_post(){
			$post_data = $this->post();

			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');

			if (array_key_exists("timeZone",$post_data))
			{
				$this->form_validation->set_rules('timeZone','Time Zone','required');
			}
			
			if (array_key_exists("carRentId",$post_data))
			{
				$this->form_validation->set_rules($this->config->item('editCarRental'));
			}
			else
			{
				$this->form_validation->set_rules($this->config->item('add_car_rental'));
			}

			if ($this->form_validation->run() === false) {
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
			}

			else
			{
				$token = $this->uIdtoken($post_data);
				if($token)
				{
					unset($post_data['token']);
					$data = $this->Common_model->insert_car_rental($post_data);

					if($data)
					{
	    			//$merged_array = array_merge($data,$post_data);
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['message'],'Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'We are experiencing some technical difficulties, please try again later.','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
				}
			}
		}

		public function insertNotes_post(){
			$post_data = $this->post();

			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');

			if (array_key_exists("notesId",$post_data))
			{
				$this->form_validation->set_rules($this->config->item('editNotes'));
			}
			else
			{
				$this->form_validation->set_rules($this->config->item('add_notes'));
			}

			if ($this->form_validation->run() === false) {
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
			}

			else
			{
				$token = $this->uIdtoken($post_data);
				if($token)
				{
					unset($post_data['token']);
					$data = $this->Common_model->insert_notes($post_data);

					if($data)
					{
	    			//$merged_array = array_merge($data,$post_data);
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['message'],'Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'We are experiencing some technical difficulties, please try again later.','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
				}
			}
		}

		public function insertItinerary_post()
		{
			$post_data = $this->post();

			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');

			if (array_key_exists("itineraryId",$post_data))
			{
				$this->form_validation->set_rules($this->config->item('editItinerary'));
			}
			else
			{
				$this->form_validation->set_rules($this->config->item('add_itinerary'));
			}

			if ($this->form_validation->run() === false) {
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
			}

			else
			{    
				$data = array();
				$token = $this->uIdtoken($post_data);
				if($token)
				{
					unset($post_data['token']);
					$data = $this->Common_model->insert_itinerary($post_data);

					if($data)
					{
						$data[0]['note_flag'] = "0";
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['message'],'Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'We are experiencing some technical difficulties, please try again later.','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
				}
			}
		}
		
		/*Function for upload  moment images */
		public function handle_momentImage_upload($momentImages) {
			$filePath = array();
			$image = array();
			$fileType = array();
			foreach ($momentImages as $key) {
				$image[] = $key['momentImage'];
			}

			for($i=0;$i<sizeof($image);$i++)
			{
				if($image[$i])
				{

	       $temp_file_path = tempnam(sys_get_temp_dir(), 'androidtempimage'); // might not work on some systems, specify your temp path if system temp dir is not writeable
	       file_put_contents($temp_file_path, base64_decode($image[$i]));

	     //if ($fileType[$i] == 'image') {   
	       $image_info = getimagesize($temp_file_path);
	       $ext = preg_replace('!\w+/!', '', $image_info['mime']);


		     //extension of file object
		    /*}else{    
		     $finfo = new finfo(FILEINFO_MIME);
		     //print_r($finfo);exit;
		     $ext = $finfo->buffer(base64_decode($image[$i])) . "\n";   
		     $ext = preg_replace('!\w+/!', '', $ext);//extension of file object
		     //echo $ext;exit;
		     $ext = substr($ext, 0, strpos($ext, ";"));//extension of file object
		 }*/

		 $_FILES['userfile'] = array(
		         //'name' => preg_replace('!\w+/!', '', $image_info['mime']),
		 	'name' =>  $this->clean(do_hash(rand() . time() . rand(). uniqid())).'.'.$ext,
		 	'tmp_name' => $temp_file_path,
		 	'size'  => filesize($temp_file_path),
		 	'error' => UPLOAD_ERR_OK,
		 	'type'  =>  $ext,
		 	);

	     //if($fileType[$i] == 'image'){
		 $config['upload_path'] = './uploads/moments';     
		 $config['allowed_types'] = 'gif|jpg|jpeg|png';
	        //$folder_path = "image"; 
	     //}
		 $config['max_size'] = '1000000';
		 $config['remove_spaces'] = TRUE;
		 $config['encrypt_name'] = TRUE;

		 $this->load->library('upload', $config);
		 $this->upload->initialize($config);
		 if($this->upload->do_upload_rest('userfile', true)) {
		 	$arr_image_info = $this->upload->data();

		 	if (!isset($_POST['momentImages1'.$i])) {
		 		$_POST['momentImages1'.$i] = 'uploads/moments/'.$arr_image_info['file_name'];
		 		/*for unlink the images if not uploaded*/
		 		array_push($filePath, $arr_image_info['file_name']);
		 	}       
	           //return true;
		 }else{
		 	foreach ($filePath as $key) {
		 		$deleteFile = $key;
		 		unlink("uploads/moments/".$deleteFile);
		 	}
		 	return false;
		 }
		}else{
			return true;
		}
	  }//for
	  return true;
	}

	/*Insert moments against to itinerary*/
	public function insertMoment_post(){
		$post_data = $this->post();
		$momentImages = array_key_exists("momentImages",$post_data);
		if($momentImages){
			$momentImages = $post_data['momentImages'];
		}
		$id = array_key_exists("id",$post_data);
		if($id){
			$id = $post_data['id'];
		}
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');
		$type = array_key_exists("type",$post_data);
		if($type){
			$type = $post_data['type'];
			if($type == 'moment'){
				$this->form_validation->set_rules('id','Itinerary Id','required|numeric|callback_itinerary_check');
			}else{				
				$this->form_validation->set_rules('id','Flight Id','required|numeric|callback_flight_check');
			}
		}
		

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{   
				unset($post_data['token']);
				if($momentImages){ 
					$check_upload = $this->handle_momentImage_upload($momentImages);  
					if(!$check_upload){
						$error = $this->upload->display_errors('', '');
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => $error, 'Result' => false), 400);           
					} 
				}else{
					unset($post_data['momentImages']);
				}

				$data = $this->Common_model->insert_moment($post_data);

				if($data)
				{        
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['msg'],'Result'=>$data), 200);
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Moments cannot be added','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	/************READ*******************/
	public function fetchFlight_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('fetch'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_flight($post_data);
				if($data['count'])
				{
					$data['list'] = $this->Common_model->fetch_flight($post_data);
					if($data['list'])
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'FLIGHT DATA','Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function fetchCarRental_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('fetch'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);

				$data['count'] = $this->Common_model->count_fetch_car_rental($post_data);
				if($data['count'])
				{
					$data['list'] = $this->Common_model->fetch_car_rental($post_data);
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'CAR RENTAL DATA','Result'=>$data), 200);
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function fetchHotel_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('fetch'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_hotel($post_data);
				if($data['count'])
				{
					$data['list'] = $this->Common_model->fetch_hotel($post_data);
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'HOTEL BOOKING DATA','Result'=>$data), 200);
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function fetchItineraryNotes_post()
	{
		$post_data = $this->post();
		print_r($post_data);
		exit();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('fetch_notes'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_itinerary_notes($post_data);	    		
				if($data['count'])
				{
					$data['list'] = $this->Common_model->fetch_itinerary_notes($post_data);

					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'ITINERARY DATA FOR NOTES','Result'=>$data), 200);
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function fetchNotes_post()
	{
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('fetch_notes1'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_notes($post_data);

				if($data['count'])
				{
					$data['list'] = $this->Common_model->fetch_notes($post_data);
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'NOTES FOR ITINERARY','Result'=>$data), 200);
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	/* Fetch itinerary list with momentImage*/
	public function fetchItinerary_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		$limit = array_key_exists("limit",$post_data);
		if($limit){
			$this->form_validation->set_rules('limit','Limit','required|numeric');
		}
		$offset = array_key_exists("offset",$post_data);
		if($offset){
			$this->form_validation->set_rules('offset','Offset','required|numeric');
		}

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_itinerary($post_data);
				if($data['count']){
					$data['list'] = $this->Common_model->fetch_itinerary($post_data);
					if($data['list']){
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'Itinerary fetched successfully','Result'=>$data), 200);
					}
					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
					}
				}else{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	/* Fetch the all itinerary moments*/
	public function fetchMoment_post()
	{
		$post_data = $this->post();

		$id = array_key_exists("id",$post_data);
		if($id){
			$id = $post_data['id'];
		}

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		$type = array_key_exists("type",$post_data);
		if($type){
			$type = $post_data['type'];
			if($type == 'moment'){
				$this->form_validation->set_rules('id','Itinerary Id','required|numeric|callback_itinerary_check');
			}else{				
				$this->form_validation->set_rules('id','Flight Id','required|numeric|callback_flight_check');
			}
		}

		$limit = array_key_exists("limit",$post_data);
		if($limit){
			$this->form_validation->set_rules('limit','Limit','required|numeric');
		}
		$offset = array_key_exists("offset",$post_data);
		if($offset){
			$this->form_validation->set_rules('offset','Offset','required|numeric');
		}

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data['count'] = $this->Common_model->count_fetch_moment($post_data);
				if($data['count']){
					$data['list'] = $this->Common_model->fetch_moment($post_data);
					if($data['list']){
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['fetch_msg'],'Result'=>$data), 200);
					}else{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
					}
				}else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'NO RECORDS EXISTS','Result'=>false), 404);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}
	
	/************DELETE*******************/
	public function deleteFlight_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteFlight'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('flightId','Flight Id','required|numeric|callback_flight_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data = $this->Common_model->delete_flight($post_data);

				if($data)
				{
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'FLIGHT DELETED SUCCESSFULLY','Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER FLIGHT ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function deleteCarRental_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteCarRental'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('carRentId','Car Rent Id','required|numeric|callback_car_rent_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data = $this->Common_model->delete_car_rental($post_data);

				if($data)
				{
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'CAR RENTAL DELETED SUCCESSFULLY','Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER CAR RENT ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}


	public function deleteHotel_post()
	{	
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteHotel'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('hotelId','Hotel Id','required|numeric|callback_hotel_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data = $this->Common_model->delete_hotel($post_data);

				if($data)
				{
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'HOTEL DELETED SUCCESSFULLY','Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER HOTEL ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function deleteMoments_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteMoments'));


		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('momentsId','Moments Id','required|numeric|callback_moments_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);

				$type = array_key_exists("type",$post_data);
				if($type){
					$type = $post_data['type'];
					if($type == 'moment'){
						$_POST['delete'] = "Moment deleted successfully";
					}else{				
						$_POST['delete'] = "Lugage deleted successfully";
					}
				}

				$data = $this->Common_model->delete_moments($post_data);
				if($data)
				{

	    			// $url =  base_url()."uploads/moments/" . $data->momentImage;
	    			// unlink($url);

					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> $_POST['delete'],'Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER MOMENT ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function deleteItinerary_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteItinerary'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('itineraryId','Itinerary Id','required|numeric|callback_itinerary_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);

				$data = $this->Common_model->delete_itinerary($post_data);

				if($data)
				{

					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'ITINERARY DELETED SUCCESSFULLY','Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & ITINERARY ID MISMATCH','Result'=>false), 400);
				}	
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function	deleteItineraryNotes_post()
	{
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteItineraryNotes'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('hotelId','Hotel Id','required|numeric|callback_hotel_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data = $this->Common_model->delete_itinerary_notes($post_data);

				if($data)
				{
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'ITINERARY FOR NOTES DELETED SUCCESSFULLY','Result'=>$data), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER ITINERARY ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function deleteNotes_post()
	{	
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('deleteNotes'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('hotelId','Hotel Id','required|numeric|callback_hotel_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				unset($post_data['token']);
				$data = $this->Common_model->delete_notes($post_data);

				if($data)
				{
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'NOTES DELETED SUCCESSFULLY','Result'=>""), 200);
				}

				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER NOTES ID TO DELETE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}
	/************UPDATE*******************/

	public function editFlight_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('editFlight'));

		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{

				$temp = $post_data;

				unset($temp['token']);
				unset($temp['userId']);
				unset($temp['flightId']);

				if(!empty($temp))
				{
					unset($post_data['token']);

					if(array_key_exists("itineraryId", $post_data))
					{
						$itinerary_id = $post_data['itineraryId'];
						$it_data = $this->Common_model->check_itinerary($post_data);

						if(empty($it_data))
						{
							$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'SUCCESS','Comments'=> 'USER ID & ITINERARY IT DO NOT MATCH','Result'=>false), 400);
							exit();
						}
					}

					$data = $this->Common_model->edit_flight($post_data);

					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'FLIGHT UPDATED SUCCESSFULLY','Result'=>$data), 200);
					}

					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & FLIGHT ID MISMATCH','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER PARAMETERS TO UPDATE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function editCarRental_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('editCarRental'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('carRentId','Car Rent Id','required|numeric|callback_car_rent_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				$temp = $post_data;

				unset($temp['token']);
				unset($temp['userId']);
				unset($temp['carRentId']);

				if(!empty($temp))
				{
					unset($post_data['token']);

					if(array_key_exists("itineraryId", $post_data))
					{
						$itinerary_id = $post_data['itineraryId'];
						$it_data = $this->Common_model->check_itinerary($post_data);

						if(empty($it_data))
						{
							$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'SUCCESS','Comments'=> 'USER ID & ITINERARY IT DO NOT MATCH','Result'=>false), 400);
							exit();
						}
					}

					$data = $this->Common_model->edit_car_rent($post_data);
					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'CAR RENTAL UPDATED SUCCESSFULLY','Result'=>$data), 200);
					}

					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & CAR RENTAL ID MISMATCH','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER PARAMETERS TO UPDATE','Result'=>false), 400);
				}

			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function editHotel_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('editHotel'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('hotelId','Hotel Id','required|numeric|callback_hotel_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				$temp = $post_data;

				unset($temp['token']);
				unset($temp['userId']);
				unset($temp['hotelId']);

				if(!empty($temp))
				{
					unset($post_data['token']);

					if(array_key_exists("itineraryId", $post_data))
					{
						$itinerary_id = $post_data['itineraryId'];
						$it_data = $this->Common_model->check_itinerary($post_data);

						if(empty($it_data))
						{
							$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'SUCCESS','Comments'=> 'USER ID & ITINERARY IT DO NOT MATCH','Result'=>false), 400);
							exit();
						}
					}

					$data = $this->Common_model->edit_hotel($post_data);
					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'HOTEL UPDATED SUCCESSFULLY','Result'=>$data), 200);
					}

					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & HOTEL ID MISMATCH','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER PARAMETERS TO UPDATE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function editItinerary_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('editItinerary'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('itineraryId','Itinerary Id','required|numeric|callback_itinerary_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				$temp = $post_data;

				unset($temp['token']);
				unset($temp['userId']);
				unset($temp['itineraryId']);

				if(!empty($temp))
				{
					unset($post_data['token']);
					$data = $this->Common_model->edit_itinerary($post_data);
					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'ITINERARY UPDATED SUCCESSFULLY','Result'=>$data), 200);
					}

					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & ITINERARY ID MISMATCH','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER PARAMETERS TO UPDATE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function editNotes_post()
	{
		$post_data = $this->post();

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($this->config->item('editNotes'));
		// $this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');

		// $this->form_validation->set_rules('itineraryId','Itinerary Id','required|numeric|callback_itinerary_check');
		if ($this->form_validation->run() === false) {
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);
		}

		else
		{
			$token = $this->uIdtoken($post_data);
			if($token)
			{
				$temp = $post_data;

				unset($temp['token']);
				unset($temp['userId']);
				unset($temp['notesId']);
				if(!empty($temp))
				{
					unset($post_data['token']);
					$data = $this->Common_model->edit_notes($post_data);
					if($data)
					{
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS','Comments'=> 'NOTES UPDATED SUCCESSFULLY','Result'=>$data), 200);
					}

					else
					{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & NOTES ID MISMATCH','Result'=>false), 400);
					}
				}
				else
				{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'PLEASE ENTER PARAMETERS TO UPDATE','Result'=>false), 400);
				}
			}
			else
			{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'USER ID & TOKEN MISMATCH','Result'=>false), 400);
			}
		}
	}

	public function sample_post()
	{
		$array = array(
			'userId'=>1,
			'itineraryId'=>1,
			'notesTitle'=>'CRON',
			'notesData'=>'CRON'
			);
		$this->db->insert('notes',$array);
	}
	
	
	/*
	* insertUsersCurrentLocation	
	* Store the users current latitude and longitude.
	* @param (integer) $userId store userId
	* @param (string) $latitude store latitude
	* @param (string) $longitude store longitude
	* @return (boolean)
	*/
	public function insertUsersCurrentLocation_post(){
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');
		$this->form_validation->set_rules('latitude','Latitude','required');
		$this->form_validation->set_rules('longitude','Longitude','required');

		if ($this->form_validation->run() === false) {
    			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);    		
		}else{

			$insert_locations = $this->Common_model->insert_locations($post_data);
			if ($insert_locations) {
				$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Current locations inserted successfully','Result'=>$insert_locations), 200);
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Locations can not stored','Result'=>false), 400);
			}	
		}
	}

	/*
	* nearbyUsersList	
	* Get the list of nearby users according to current location and users with the same country as login user.
	* @param (integer) $userId store userId
	* @param (string) $latitude store latitude
	* @param (string) $longitude store longitude
	* @param (integer) $userId store userId
	* @param (integer) $userId store userId
	* @return (array)
	*/
	public function nearbyUsersList_post(){
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');
		$this->form_validation->set_rules('latitude','Latitude','required');
		$this->form_validation->set_rules('longitude','Longitude','required');
		$this->form_validation->set_rules('limit','Limit','required|numeric');
		$this->form_validation->set_rules('offset','Offset','required|numeric');

		if ($this->form_validation->run() === false) {
    			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>''), 400);    		
		}else{

			$data['count'] = $this->Common_model->count_nearby_users_list($post_data);
			//print_r($data['count']) ;exit();   	
	    	if($data['count']){
				$data['list'] = $this->Common_model->nearby_users_list($post_data);			
				if ($data) {
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Nearby users fetched successfully','Result'=>$data), 200);
				}else{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'No Data Found','Result'=>'No Data Found'), 400);
				}
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'No Data Found','Result'=>'No Data Found'), 400);
			}	
		}
	}
	
	
}?>