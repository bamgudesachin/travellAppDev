<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public $table1 = 'users';    
  
    public function register($post_data)
    {  
        //print_r($post_data);exit();
        $password = array_key_exists("password",$post_data);
        if($password){
           //$post_data['password'] = do_hash($password); 
           $post_data['password'] = $post_data['password'];
        }     

        $profile_picture = array_key_exists("profile_picture",$post_data);
        if($profile_picture){
            unset($post_data['profile_picture']);
            $post_data['profile_picture'] = $_POST['profile_picture1'];   
        }  

        //print_r($post_data);exit();
        /* Update the facebookId if user try to login with fb with same email which is register by normal login */
        $facebookId = array_key_exists("facebookId",$post_data);
        if($facebookId){
            $post_data['facebookId'] = $post_data['facebookId'];   
        } 
        $email = $post_data['email'];
        $email_exists = $this->email_check($email);
        if($email_exists){
            /* update facebookId according to email in user table */
            $_POST['success'] = "Facebook account attached successfully";
            $data = array('facebookId' => $post_data['facebookId']);
            $this->db->where('email',$email);
            return $this->db->update('users',$data);
        } else{
            $is_register = $this->db->insert($this->table1, $post_data);
            if($is_register){
                $_POST['success'] = "Register successfully";
                return true;
            }
        } //else

    }    
        
    public function get_facebook_user($facebookId)
    {
        $sql1 = "SELECT userId,firstName,lastName,email,profile_picture,age,gender,city,cityTravelledTo,country,countryTravelledTo,'password' loggedin_with,latitude,longitude,deviceToken,facebookId,deleteFlag,createdAt FROM users WHERE facebookId = '".$facebookId."'";
        $record1 = $this->db->query($sql1);
        if ($record1->num_rows()>0) {                
            return $record1->result_array();
        }        
    }
    
     public function email_check($email)
    {
        $sql1 = "SELECT userId FROM users WHERE email='".$email."'";
        $record1 = $this->db->query($sql1);
        if ($record1->num_rows()>0) {
            return true;
        }
    }

    public function login($post_data)
    {    
        
        $email = $post_data['email'];
        $password = $post_data['password'];
        //$password = do_hash($password);
        //$device_token = $post_data['deviceToken'];
        /* check the email is register by facebbok or not, if yes the return error */
        $check_user_exist = "SELECT * FROM users WHERE email = '".$email."'";
        $result = $this->db->query($check_user_exist);
        if ($result->num_rows()>0) {
            $data = $result->result_array();
           // print_r($data);exit();
            $user_password = $data[0]['password'];
            $facebookId = $data[0]['facebookId'];
            if($user_password == ''  && $facebookId){
                $_POST['login_error'] = "This email was used to register with facebook,Please use facebook login";  
                return false;  
            }else{
                if($password == $user_password) {
                    return $data;          
                }else{
                    $_POST['login_error'] = "Password not valid"; 
                    return false;   
                }
            }
        }else{
            $_POST['login_error'] = "Please enter a valid email and password"; 
            return false;
        }        
    }

    public function logout($post_data)
    {
        $userId = $post_data['userId'];
        $sql1 = "SELECT userId FROM users WHERE userId = '".$userId."'";
        $record1 = $this->db->query($sql1);
        if ($record1->num_rows()>0) {
                $device_token_update = "UPDATE users SET deviceToken ='' WHERE userId='".$userId."'";
                $result = $this->db->query($device_token_update);
            return true;
        }

    }

    /*function for make sure the  student id exists*/
    public function user_check($userId)
    {
        $sql1 = "SELECT userId FROM users WHERE userId ='".$userId."'";
        $record = $this->db->query($sql1);
        if ($record->num_rows()>0) {
            return true;
        }   
    }  
  
    /* Save tokens */
    public function save_token_with_expiry($post_data,$email)
    {
        if (empty($post_data['latitude'])) {
            unset($post_data['latitude']);
        }
        if (empty($post_data['longitude'])) {
            unset($post_data['longitude']);
        }
        unset($post_data['email']);
        unset($post_data['password']);
        unset($post_data['profile_picture']);
        return $this->db->where('email',$email)->update($this->table1, $post_data);
    }

    public function refresh_token_check($refresh_token)
    {
        $sql = "SELECT refreshTokenExpiry FROM users WHERE refreshToken='".$refresh_token."'";
        $record = $this->db->query($sql);        
        if ($record->num_rows()>0) {
            return $record->row('refreshTokenExpiry');            
        }
    }

    public function access_token($post_data)
    {
        if (empty($post_data['latitude'])) {
            unset($post_data['latitude']);
        }
        if (empty($post_data['longitude'])) {
            unset($post_data['longitude']);
        }

        return $this->db->where('refreshToken',$post_data['refreshToken'])->update($this->table1, $post_data);
    }
	
	
    /*****************************************************************************************/

    /*function for check email exists or not */
     public function email_exists($email)
    {
        $sql1 = "SELECT userId FROM users WHERE email='".$email."'";
        $record1 = $this->db->query($sql1);        
        if ($record1->num_rows()>0) {
            return true;
        }else
        {
           return false;
        }
        
    }

    /* forgot password */
    public function fogotPassword($post_data)
    {
        $email = $post_data['email'];
        $sql = "SELECT userId,firstName,lastName,email FROM users WHERE email='".$email."'";
        $record = $this->db->query($sql);
        if ($record->num_rows()>0) {
                //return $record->row('student_id');
                return $record->result_array();
        }else{
                return false;
            }
    }    
    
    

    
}?>