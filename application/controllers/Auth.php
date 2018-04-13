<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once('./application/libraries/REST_Controller.php');

class Auth extends REST_Controller {

	public function __construct()
	{	parent::__construct();
		include APPPATH . 'libraries/classes/class.phpmailer.php';
        $this->config->load('my_constants');
		$this->load->model('mobile_api/Common_model');
        $this->load->model('mobile_api/Auth_model');
        $this->load->helper('security');
	}

	/*Register the User */
	public function register_post($fb_data = NULL)
	{   		
		//$last_login = date('Y-m-d H:i:s');
		if (!$fb_data) {
			$post_data = $this->post();
		}else{
			$post_data = $fb_data;
		}

		
		$profile_picture = $post_data['profile_picture'];
		$facebookId = array_key_exists("facebookId",$post_data);
        if($facebookId){           
           $facebookId = $post_data['facebookId'];   
        } 
		//$lastLogin = $last_login;

		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		
		if (!$fb_data) {
			$this->form_validation->set_rules('email','Email','required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password','Password','required');
		}
		
		
		if(!empty($profile_picture)){		
			$this->form_validation->set_rules('profile_picture', 'Profile image', 'required|callback_handle_profile_image_upload');		
		}else{
			unset($post_data['profile_picture']);
		}

		if ($this->form_validation->run() === false) {
    		$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);
		}else{	
			
			$response = $this->Auth_model->register($post_data);
			if ($response) {
				if (!$fb_data) {
					$data = $this->Auth_model->login($post_data);
				}else{
					$facebookId = $post_data['facebookId'];
					$data = $this->Auth_model->get_facebook_user($facebookId);
				}
				
				if ($data) {
					$email = $data[0]['email'];
					$post_data['token'] = $this->generate_token();
					$post_data['tokenExpiry'] = $this->generate_token_expiry();
					$post_data['refreshToken'] = $this->generate_token();
					$post_data['refreshTokenExpiry'] = $this->generate_refresh_token_expiry();
					
					$token_data = $this->Auth_model->save_token_with_expiry($post_data,$email);
					if ($token_data) {
						$data[0]['token'] = $post_data['token'];
						$data[0]['tokenExpiry'] = $post_data['tokenExpiry'];
						$data[0]['refreshToken'] = $post_data['refreshToken'];
						$data[0]['refreshTokenExpiry'] = $post_data['refreshTokenExpiry'];						
						$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => $_POST['success'],'Result'=>$data[0]), 200);
					}else{
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Server error','Result'=>''), 400);	
					}				
				}
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Unable to register user','Result'=>''), 400);
			}				
		}
	}


	/* Signup and login with facebook */
	public function signup_with_facebook_post()
    {
    	$this->load->helper('security');
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('facebookId','Facebook id','required');
		if ($this->form_validation->run() === false) {
    		$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);
		}else{
			$facebookId = $post_data['facebookId'];
			$data = $this->Auth_model->get_facebook_user($facebookId);
			if ($data) {
				$this->login_post($data);						
			}else{
				$this->register_post($post_data);
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
	public function email_check($email)
    {	
    	$status = $this->Auth_model->email_check($email);    	
    		if ($status){
	            $this->form_validation->set_message('email_check', 'An account already exists with this email ID');
	            return false;
	        }else{
	            return true;
	        }    	      
    }

    /*Check the user present or not*/
	public function user_check($userId)
    {	
    	$status = $this->Auth_model->user_check($userId);	
    	if ($status){
        	return true;            
        }else{
            $this->form_validation->set_message('user_check', '{field} is do not exists');
            return false;
        }
    }

    /* login the user
     parameters email,password
     */
    public function login_post($data = NULL)
    {
    	$this->load->helper('security');
    	if (!$data) {
    		$post_data = $this->post();
			$this->form_validation->set_data($post_data);
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('email','Email','required');
			$this->form_validation->set_rules('password','Password','required');
			if ($this->form_validation->run() === false) {
	    		$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);
			}else{
				$data = $this->Auth_model->login($post_data);
			}
    	}

		if ($data) {
			$email = $data[0]['email'];
			$post_data['token'] = $this->generate_token();
			$post_data['tokenExpiry'] = $this->generate_token_expiry();
			$post_data['refreshToken'] = $this->generate_token();
			$post_data['refreshTokenExpiry'] = $this->generate_refresh_token_expiry();
			
			$token_data = $this->Auth_model->save_token_with_expiry($post_data,$email);
			if ($token_data) {
				$data[0]['token'] = $post_data['token'];
				$data[0]['tokenExpiry'] = $post_data['tokenExpiry'];
				$data[0]['refreshToken'] = $post_data['refreshToken'];
				$data[0]['refreshTokenExpiry'] = $post_data['refreshTokenExpiry'];						
				$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Logged in successfully','Result'=>$data[0]), 200);
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Server error','Result'=>''), 400);	
			}			
		}else{
			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => $_POST['login_error'],'Result'=>''), 400);
		}				
	}

	public function logout_post()
	{
		$post_data = $this->post();
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('userId','User Id','required|numeric|callback_user_check');
		if ($this->form_validation->run() === false) {
    			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);    		
		}else{
			$data = $this->Auth_model->logout($post_data);			
			if ($data) {			
					$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Logout successfully','Result'=>$data), 200);
				}else{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Fail to logout','Result'=>$data), 400);	
				}	
		}
	}

	

    /* get New access token using refresh token */
	public function access_token_post()
	{
		$post_data = $this->post();
		$this->load->helper('security');
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		//$this->form_validation->set_rules('user_id','User Id','required|numeric');
		$this->form_validation->set_rules('refreshToken','Refresh token','required|callback_refresh_token_check');
		//$this->form_validation->set_rules('device_type','Device type','required');
		//$this->form_validation->set_rules('device_token','Device token','required');
		if ($this->form_validation->run() === false) {
    		$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);
		}else{
			$post_data['token'] = $this->generate_token();
			$post_data['tokenExpiry'] = $this->generate_token_expiry();			
			$access_token = $this->Auth_model->access_token($post_data);
			if ($access_token) {
				$this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'Refresh token verified successfully','Result'=> $post_data), 200);
			}else{
				$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Refresh token is invalid','Result'=>''), 400);
			}
		}
	}

	public function refresh_token_check($refresh_token)
	{
		$refresh_token_expiry = $this->Auth_model->refresh_token_check($refresh_token);
		if ($refresh_token_expiry){
        	$status = $this->is_token_active($refresh_token_expiry);        	
        	if($status){        		
        		$this->form_validation->set_message('refresh_token_check', 'Refresh token expired.');
            	return false;	
        	}else{
        		return true;
        	}
            
        }else{
        	$this->form_validation->set_message('refresh_token_check', 'Refresh token mismatch.');
            return false;
        }
	}

	function clean($string)
	{
	    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	function generate_token()
	{
	    return $this->clean(do_hash(rand() . time() . rand()));
	}

	function generate_token_expiry()
	{
	    return strtotime('+1 day', time());
	   // return strtotime('+2 minutes', time());

	}

	function generate_refresh_token_expiry()
	{
	    return strtotime('+30 day', time());
	}

	function is_token_active($ts)
	{
	    if ($ts <= time()) {
	        return true;
	    } else {
	        return false;
	    }
	}

   
		
	/*******************************************************************************/	/**
     * URL - /Auth/fogotPassword
     * TYPE - POST
     * PARAMETERS - email
     * @return mixed
     */
	public function fogotPassword_post()
	{
	    $post_data = $this->post();	
	    $email = $post_data['email'];	    
		$this->form_validation->set_data($post_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('email','Email','required|valid_email|callback_email_exists');
		if ($this->form_validation->run() === false) {
	    			$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => validation_errors(),'Result'=>false), 400);    		
			}else
			{

				$data = $this->Auth_model->fogotPassword($post_data);
				//print_r($data);exit();	
				if ($data){
					//print_r($data);exit();	
					$userId = $data[0]['userId'];
					$firstname = $data[0]['firstName'];					
					$link = site_url()."admin/PasswordReset?user_id=".$userId;				
					
					$send_email = $this->sendMail($email,$link,$firstname);
					
					
				}else{
					$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Unable to send email','Result'=>''), 400);
				}				
			}
	}		

    

	/* function for sending email for forgot password */
	function sendMail($email,$link,$firstname)
	{		 	    

		$msg= '<html>
		              <head></head>
		                <body style="color: black;">
		                	<p>Dear User <br><br></p>
		                	<p>Please click below to change your Travel App password<br><br></p>
		                	<strong><a href="'.$link.'" style="text-decoration: none !important;">Change Password</a><br><br></strong>
		                	<p>If you did not request a password change, please disregard this message.<br><br></p>
		                	<p>Sincerely,<br>Travel App customer service<br></p>

		                <body>
		       	</html>	';		    
		  //echo $msg;exit();
		  //include APPPATH . 'libraries/classes/class.phpmailer.php'; // include the class name				
					$mail = new PHPMailer(true); // create a new object
					$mail->IsSMTP(); 
			try{
	                $mail->IsHTML(true);	    
	                $mail->SMTPDebug = 1;                                                        
	                $mail->Host = "smtp.gmail.com"; //Hostname of the mail server  ssl://smtp.googlemail.com//smtpout.secureserver.net
	                $mail->Port = "587";//587 //Port of the SMTP like to be 25, 80, 465 or 587  ////465
	                $mail->SMTPAuth = true; //Whether to use SMTP authentication
	                $mail->Username = "bamgude.sachin@gmail.com"; //shreesaipratik1@gmail.com Username for SMTP authentication any valid email created in your domain  bamgude.sachin@gmail.com
	                $mail->Password = "sbam@1991"; //pr99AT99ik99 Password for SMTP authentication 
	                $mail->SMTPSecure  = 'tls'; 
				    $mail->SetFrom("bamgude.sachin@gmail.com",'Travel App');
	          //    $mail->SetFrom("bamgude.sachin@gmail.com");
					$mail->Subject = "Change your Travel App password";
					$mail->Body = $msg;
					$mail->AddAddress($email);//whom to send mail
	               // $mail->AddCC("");
					
					$send = $mail->Send(); //Send the mails

					//var_dump($send);exit();
					//echo $mail->ErrorInfo;exit();
					if($send){
						 $this->response(array('ResponseCode' => 1, 'ResponseMessage' => 'SUCCESS', 'Comments' => 'MAIL SENT SUCCESFULLY','Result'=>$send), 200);
					}
					else{

						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'MAIL SENDING FAIL','Result'=>$send), 400);
					}
				} catch (phpmailerException $e) {
					 // echo $e->errorMessage();exit(); //Pretty error messages from PHPMailer
						$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'MAIL SENDING FAIL','Result'=>$send), 400);
					
					} catch (Exception $e) {
					 // echo $e->getMessage();exit(); //Boring error messages from anything else!
					  	$this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'MAIL SENDING FAIL','Result'=>$send), 400);
					}

	}

	

	/* function for check the email id exists or not */
	public function email_exists($email)
	{	
		$status = $this->Auth_model->email_exists($email);	
	    if (!$status){
	        $this->form_validation->set_message('email_exists', 'This email is not registered with us');
	        return false;
	    }else{
	        return true;
	    }
	}

   
		
}?>