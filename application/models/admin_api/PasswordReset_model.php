<?php
class PasswordReset_model extends CI_Model {
	function __construct()
	{
		parent::__construct();

	}

	public function update_password()
	{
		$post_data = $this->input->post();
		$userId = $post_data['user_id'];		
		$password = $post_data['password'];
		/* encrypt the password using sha1 algorithm */
		$password = do_hash($password);

		$sql = "SELECT userId from users where userId='".$userId."'";
		$record = $this->db->query($sql);
        if ($record->num_rows()>0) {
                $data  = array('password' =>$password);
                $this->db->where('userId', $userId);
                return $this->db->update('users',$data);                
        }else{
                return false;
            }
	}


}