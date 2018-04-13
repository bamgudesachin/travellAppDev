<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
require_once('./application/libraries/REST_Controller.php');
//require_once APPPATH.'libraries/new_stripe/vendor/autoload.php';
//require_once('./application/libraries/new_stripe/vendor/autoload.php');   
class MY_Controller1 extends REST_Controller
{
    public $data = array();
    
    function __construct ()
    {
        parent::__construct();
        $post_data = $this->post();
        $token = $post_data['token'];
        $_POST['token'] = $post_data['token'];
        $token_expiry = $this->token_expiry($token);
        if ($token_expiry){
            $status = $this->is_token_active($token_expiry);            
            if($status){      
                $this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Token expired','Result'=>''), 403);   
            }            
        }else{
            $this->response(array('ResponseCode' => 0, 'ResponseMessage' => 'FAILURE', 'Comments' => 'Token Mismatch','Result'=>''), 401);
        }
    }

    public function token_expiry($token)
    {
        $sql = "SELECT tokenExpiry FROM users WHERE token='".$token."'";
        $record = $this->db->query($sql);
        if ($record->num_rows()>0) {
            return $record->row('tokenExpiry');
        }
    }


    function is_token_active($ts)
    {
        if ($ts <= time()) {
            return true;
        } else {
            return false;
        }
    }

    public function send_notification_ios($deviceToken, $payload)
    {
        $passphrase = '12345'; // change this to your passphrase(password)

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert','UC_FINAL.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert','Satori_Dev_Final.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        //stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');

        // Open a connection to the APNS server
        // for 
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); 

        /*$fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
*/
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        if (!$result){
            return false;
            //echo '<br>Message not delivered' . PHP_EOL . print_r($result);
        }            
        else{
            return true;
            //echo '<br>Message successfully delivered' . PHP_EOL . print_r($result);
        }
    }

    /*send notification for multiple users*/
    public function send_multiple_user_notification_ios($deviceToken, $payload)
    {
        //print_r($deviceToken);exit();
        $passphrase = '12345'; // change this to your passphrase(password)

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert','UC_FINAL.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert','Satori_Dev_Final.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        //stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');

        // Open a connection to the APNS server
        // for 
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); 

      /*  $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); */

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        foreach($deviceToken as $token)
        {
            $msg = chr(0) . pack('n',32) . pack('H*', $token) . pack('n',strlen($payload)) . $payload;
            // Build the binary notification
            //$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
        }

        fclose($fp);
        if (!$result){
            return false;
            //echo '<br>Message not delivered' . PHP_EOL . print_r($result);
        }            
        else{
            return true;
            //echo '<br>Message successfully delivered' . PHP_EOL . print_r($result);
        }
    }
}?>